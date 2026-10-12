<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class CheckoutController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if (! config('store.demo_mode') && ! Auth::check()) {
            return $this->loginRedirect();
        }

        try {
            $lines = $this->loadLines($request);
        } catch (RuntimeException $exception) {
            $request->session()->forget('buy_now');

            return redirect()->route('cart.index')->with('cart_error', $exception->getMessage());
        } catch (QueryException $exception) {
            Log::error('Laravel checkout items could not be loaded.', [
                'exception' => $exception->getMessage(),
            ]);

            return response()->view('storefront.checkout-unavailable', [], 503);
        }

        if ($lines === []) {
            return redirect()->route('cart.index')->with('cart_error', 'Giỏ hàng đang trống.');
        }

        $totals = $this->calculateTotals($lines);
        $user = Auth::user();

        return view('storefront.checkout', [
            'lines' => $lines,
            'subtotal' => $totals['subtotal'],
            'shipping' => $totals['shipping'],
            'total' => $totals['total'],
            'customer' => [
                'name' => old('name', $user?->ho_ten),
                'email' => old('email', $user?->email),
                'phone' => old('phone', $user?->so_dien_thoai),
            ],
            'demoMode' => config('store.demo_mode'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (config('store.demo_mode')) {
            try {
                $lines = $this->loadLines($request);
                if ($lines === []) {
                    return redirect()->route('cart.index')->with('cart_error', 'Giỏ hàng đang trống.');
                }
                $totals = $this->calculateTotals($lines);
            } catch (RuntimeException $exception) {
                return redirect()->route('cart.index')->with('cart_error', $exception->getMessage());
            } catch (QueryException $exception) {
                Log::error('Laravel demo checkout items could not be loaded.', [
                    'exception' => $exception->getMessage(),
                ]);

                return response()->view('storefront.checkout-unavailable', [], 503);
            }

            $request->session()->put('last_order', 'DEMO-'.strtoupper(Str::random(10)));
            $request->session()->put('last_order_total', $totals['total']);
            $request->session()->put('demo_order', true);
            $request->session()->put('cart', []);
            $request->session()->put('cart_options', []);
            $request->session()->forget('buy_now');

            return redirect()->route('orders.success');
        }

        if (! Auth::check()) {
            return $this->loginRedirect();
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['required', 'string', 'max:25', 'regex:/^[0-9+().\-\s]+$/'],
            'address' => ['required', 'string', 'max:2000'],
            'note' => ['nullable', 'string', 'max:5000'],
        ]);

        try {
            $order = DB::transaction(function () use ($request, $data): Order {
                $lines = $this->loadLines($request, true);
                if ($lines === []) {
                    throw new RuntimeException('Giỏ hàng đang trống.');
                }

                foreach ($lines as $line) {
                    if ($line['quantity'] > (int) $line['product']->so_luong_ton) {
                        throw new RuntimeException('Tồn kho vừa thay đổi. Vui lòng kiểm tra lại giỏ hàng.');
                    }
                }

                $totals = $this->calculateTotals($lines);
                $order = Order::create([
                    'ma_don' => 'IBP'.now()->format('ymd').strtoupper(Str::random(12)),
                    'user_id' => Auth::id(),
                    'ten_nguoi_nhan' => $data['name'],
                    'email' => strtolower($data['email']),
                    'so_dien_thoai' => trim($data['phone']),
                    'dia_chi' => trim($data['address']),
                    'tam_tinh' => $totals['subtotal'],
                    'phi_giao_hang' => $totals['shipping'],
                    'tong_tien' => $totals['total'],
                    'ghi_chu' => $data['note'] ?? null,
                ]);

                foreach ($lines as $line) {
                    $productName = $line['product']->ten;
                    if ($line['variant']) {
                        $productName .= ' — '.$line['variant']->ten_phien_ban;
                    }
                    $order->items()->create([
                        'product_id' => $line['product']->id,
                        'ten_san_pham' => $productName,
                        'so_luong' => $line['quantity'],
                        'don_gia' => $line['unit_price'],
                        'thanh_tien' => $line['unit_price'] * $line['quantity'],
                    ]);

                    foreach ($line['addons'] as $addon) {
                        $addonPrice = (float) ($addon->gia_khuyen_mai ?: $addon->gia_goc);
                        $order->items()->create([
                            'product_id' => null,
                            'ten_san_pham' => 'Phụ kiện: '.$addon->ten,
                            'so_luong' => $line['quantity'],
                            'don_gia' => $addonPrice,
                            'thanh_tien' => $addonPrice * $line['quantity'],
                        ]);
                    }

                    $line['product']->decrement('so_luong_ton', $line['quantity']);
                    $line['product']->increment('da_ban', $line['quantity']);
                }

                return $order;
            });
        } catch (RuntimeException $exception) {
            return back()
                ->withInput($request->except('password'))
                ->with('checkout_error', $exception->getMessage());
        } catch (QueryException $exception) {
            Log::error('Laravel checkout order could not be saved.', [
                'exception' => $exception->getMessage(),
            ]);

            return back()
                ->withInput($request->except('password'))
                ->with('checkout_error', 'Chưa thể tạo đơn hàng. Vui lòng thử lại hoặc liên hệ IBP.');
        }

        if ($request->session()->has('buy_now')) {
            $request->session()->forget('buy_now');
        } else {
            $request->session()->put('cart', []);
            $request->session()->put('cart_options', []);
        }

        $request->session()->put('last_order', $order->ma_don);
        $request->session()->put('last_order_total', (float) $order->tong_tien);

        return redirect()->route('orders.success');
    }

    private function loadLines(Request $request, bool $lock = false): array
    {
        $buyNow = $request->session()->get('buy_now');
        if (is_array($buyNow)) {
            if ((int) ($buyNow['created_at'] ?? 0) < now()->timestamp - 1800) {
                throw new RuntimeException('Phiên mua ngay đã hết hạn. Vui lòng thêm lại sản phẩm.');
            }
            $cart = [
                (int) ($buyNow['product_id'] ?? 0) => max(1, min(99, (int) ($buyNow['quantity'] ?? 1))),
            ];
        } else {
            $storedCart = $request->session()->get('cart', []);
            $cart = is_array($storedCart) ? $storedCart : [];
        }
        $cart = array_filter($cart, static fn ($quantity, $id): bool => (int) $id > 0 && (int) $quantity > 0, ARRAY_FILTER_USE_BOTH);
        if ($cart === []) {
            return [];
        }

        $query = Product::query()
            ->where('hien_thi', true)
            ->whereIn('id', array_keys($cart))
            ->with(['detail', 'variants', 'addons' => static fn ($query) => $query->where('hien_thi', true)]);
        if ($lock) {
            $query->lockForUpdate();
        }
        $products = $query->get()->keyBy('id');
        $optionsByProduct = $request->session()->get('cart_options', []);
        $lines = [];

        foreach ($cart as $productId => $quantity) {
            $product = $products->get($productId);
            if (! $product) {
                throw new RuntimeException('Một sản phẩm trong giỏ hàng không còn được kinh doanh. Vui lòng kiểm tra lại giỏ hàng.');
            }

            $options = is_array($buyNow) && (int) ($buyNow['product_id'] ?? 0) === (int) $productId
                ? $buyNow
                : (is_array($optionsByProduct[$productId] ?? null) ? $optionsByProduct[$productId] : []);
            $variantId = (int) ($options['variant_id'] ?? 0);
            $variant = $product->variants->firstWhere('id', $variantId);
            if ($variantId > 0 && ! $variant) {
                throw new RuntimeException('Phiên bản đã chọn không còn khả dụng. Vui lòng quay lại giỏ hàng.');
            }

            $addonIds = array_values(array_unique(array_map(
                'intval',
                is_array($options['addon_ids'] ?? null) ? $options['addon_ids'] : [],
            )));
            $addons = $product->addons->whereIn('id', $addonIds)->values();
            if ($addons->count() !== count($addonIds)) {
                throw new RuntimeException('Một hoặc nhiều phụ kiện đã chọn không còn khả dụng. Vui lòng quay lại giỏ hàng.');
            }

            $unitPrice = (float) ($variant?->gia ?: $product->gia_khuyen_mai ?: $product->gia);
            $quantity = max(1, min(99, (int) $quantity));
            $lines[(int) $productId] = [
                'product' => $product,
                'quantity' => $quantity,
                'variant' => $variant,
                'addons' => $addons,
                'unit_price' => $unitPrice,
            ];
        }

        return $lines;
    }

    private function calculateTotals(array $lines): array
    {
        $subtotal = 0.0;
        $shipping = 0.0;
        foreach ($lines as $line) {
            $subtotal += $line['unit_price'] * $line['quantity'];
            foreach ($line['addons'] as $addon) {
                $subtotal += (float) ($addon->gia_khuyen_mai ?: $addon->gia_goc) * $line['quantity'];
            }
            if (! $line['product']->detail?->giao_hang_mien_phi) {
                $shipping = max($shipping, (float) ($line['product']->detail?->phi_giao_hang ?? 0));
            }
        }

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $subtotal + $shipping,
        ];
    }

    private function loginRedirect(): RedirectResponse
    {
        return redirect()->route('login', [
            'redirect' => route('cart.checkout', [], false),
            'msg' => 'checkout_required',
        ]);
    }
}
