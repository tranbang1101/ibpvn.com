<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View|Response
    {
        $cart = $this->sessionCart($request);
        $productsById = collect();

        try {
            if ($cart !== []) {
                $productsById = Product::query()
                    ->where('hien_thi', true)
                    ->whereIn('id', array_keys($cart))
                    ->with(['detail', 'variants', 'addons' => static fn ($query) => $query->where('hien_thi', true)])
                    ->get()
                    ->keyBy('id');
            }
        } catch (QueryException $exception) {
            Log::error('Laravel cart data could not be loaded.', [
                'exception' => $exception->getMessage(),
            ]);

            return response()->view('storefront.cart-unavailable', [], 503);
        }

        $sessionOptions = $request->session()->get('cart_options', []);
        $lines = [];
        $subtotal = 0;
        $shipping = 0;
        foreach ($cart as $productId => $quantity) {
            $product = $productsById->get($productId);
            if (! $product) {
                unset($cart[$productId], $sessionOptions[$productId]);

                continue;
            }

            $options = is_array($sessionOptions[$productId] ?? null) ? $sessionOptions[$productId] : [];
            $variant = $product->variants->firstWhere('id', (int) ($options['variant_id'] ?? 0));
            $addonIds = array_map('intval', is_array($options['addon_ids'] ?? null) ? $options['addon_ids'] : []);
            $addons = $product->addons->whereIn('id', $addonIds)->values();
            $unitPrice = (float) ($variant?->gia ?: $product->gia_khuyen_mai ?: $product->gia);
            $addonTotal = (float) $addons->sum(static fn ($addon) => $addon->gia_khuyen_mai ?: $addon->gia_goc);
            $lineTotal = ($unitPrice + $addonTotal) * $quantity;
            $subtotal += $lineTotal;
            if (! $product->detail?->giao_hang_mien_phi) {
                $shipping = max($shipping, (float) ($product->detail?->phi_giao_hang ?? 0));
            }

            $lines[$productId] = [
                'product' => $product,
                'quantity' => $quantity,
                'variant' => $variant,
                'addons' => $addons,
                'unit_price' => $unitPrice,
                'unit_total' => $unitPrice + $addonTotal,
                'line_total' => $lineTotal,
            ];
        }

        $request->session()->put('cart', $cart);
        $request->session()->put('cart_options', $sessionOptions);

        return view('storefront.cart', [
            'lines' => $lines,
            'itemCount' => array_sum($cart),
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $subtotal + $shipping,
        ]);
    }

    public function add(Request $request): RedirectResponse|JsonResponse|Response
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'min:1'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
            'variant_id' => ['nullable', 'integer', 'min:0'],
            'addon_ids' => ['nullable', 'array', 'max:20'],
            'addon_ids.*' => ['integer', 'min:1'],
            'selection_present' => ['nullable', 'boolean'],
        ]);
        $quantity = (int) ($data['quantity'] ?? 1);
        $optionsByProduct = $request->session()->get('cart_options', []);
        $optionsByProduct = is_array($optionsByProduct) ? $optionsByProduct : [];
        $previousOptions = is_array($optionsByProduct[$data['product_id']] ?? null)
            ? $optionsByProduct[$data['product_id']]
            : [];
        $cart = $this->sessionCart($request);
        $currentQuantity = (int) ($cart[$data['product_id']] ?? 0);

        try {
            $product = Product::query()
                ->where('hien_thi', true)
                ->findOrFail($data['product_id']);
            $variantId = array_key_exists('variant_id', $data)
                ? (int) $data['variant_id']
                : (int) ($previousOptions['variant_id'] ?? 0);
            $addonIds = array_key_exists('selection_present', $data)
                ? array_map('intval', $data['addon_ids'] ?? [])
                : array_map('intval', $previousOptions['addon_ids'] ?? []);

            if ($variantId > 0 && ! $product->variants()->whereKey($variantId)->exists()) {
                return $this->failure($request, 'Phiên bản sản phẩm không hợp lệ. Vui lòng tải lại trang.', 422);
            }

            $addonIds = array_values(array_unique($addonIds));
            if ($addonIds !== []) {
                $validAddonIds = $product->addons()
                    ->where('hien_thi', true)
                    ->whereIn('id', $addonIds)
                    ->pluck('id')
                    ->map(static fn ($id) => (int) $id)
                    ->all();
                if (count($validAddonIds) !== count($addonIds)) {
                    return $this->failure($request, 'Phụ kiện đã chọn không hợp lệ. Vui lòng tải lại trang.', 422);
                }
                $addonIds = $validAddonIds;
            }

            $stock = (int) $product->so_luong_ton;
            $buyNow = $request->boolean('buy_now');
            if ($stock < 1 || $quantity + ($buyNow ? 0 : $currentQuantity) > min(99, $stock)) {
                return $this->failure($request, 'Số lượng yêu cầu vượt quá giới hạn hoặc tồn kho hiện có.', 409);
            }

            $existingAddonIds = array_map('intval', $previousOptions['addon_ids'] ?? []);
            sort($existingAddonIds);
            $requestedAddonIds = $addonIds;
            sort($requestedAddonIds);
            if (
                ! $buyNow
                && $currentQuantity > 0
                && (
                    (int) ($previousOptions['variant_id'] ?? 0) !== $variantId
                    || $existingAddonIds !== $requestedAddonIds
                )
            ) {
                return $this->failure(
                    $request,
                    'Giỏ hàng đã có sản phẩm với lựa chọn khác. Hãy xóa sản phẩm hiện tại trước khi thêm lựa chọn mới.',
                    409,
                );
            }
        } catch (QueryException $exception) {
            Log::error('Laravel cart product validation failed.', [
                'product_id' => $data['product_id'],
                'exception' => $exception->getMessage(),
            ]);

            return $this->failure($request, 'Chưa thể kiểm tra sản phẩm lúc này. Vui lòng thử lại sau.', 503);
        }

        $configuration = ['variant_id' => $variantId, 'addon_ids' => $addonIds];
        if ($buyNow) {
            $request->session()->put('buy_now', [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'created_at' => now()->timestamp,
            ] + $configuration);

            return redirect()->route('cart.checkout');
        }

        $request->session()->forget('buy_now');
        $cart[$product->id] = $currentQuantity + $quantity;
        $optionsByProduct[$product->id] = $configuration;
        $request->session()->put('cart', $cart);
        $request->session()->put('cart_options', $optionsByProduct);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'count' => array_sum($cart),
                'message' => 'Đã thêm vào giỏ hàng.',
            ]);
        }

        return redirect()->route('cart.index')->with('cart_added', true);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', 'in:update,remove'],
            'product_id' => ['required', 'integer', 'min:1'],
            'quantity' => ['required_if:action,update', 'integer', 'min:1', 'max:99'],
        ]);
        $cart = $this->sessionCart($request);
        $productId = (int) $data['product_id'];

        if ($data['action'] === 'remove') {
            unset($cart[$productId]);
            $this->forgetProductOptions($request, $productId);
            $request->session()->put('cart', $cart);

            return redirect()->route('cart.index')->with('cart_notice', 'Đã xóa sản phẩm khỏi giỏ hàng.');
        }

        if (! array_key_exists($productId, $cart)) {
            return redirect()->route('cart.index')->with('cart_error', 'Sản phẩm không có trong giỏ hàng.');
        }

        try {
            $product = Product::query()
                ->where('hien_thi', true)
                ->find($productId);
        } catch (QueryException $exception) {
            Log::error('Laravel cart stock could not be checked.', [
                'product_id' => $productId,
                'exception' => $exception->getMessage(),
            ]);

            return redirect()->route('cart.index')->with('cart_error', 'Chưa thể kiểm tra tồn kho. Vui lòng thử lại.');
        }

        if (! $product || (int) $data['quantity'] > (int) $product->so_luong_ton) {
            return redirect()->route('cart.index')->with('cart_error', 'Số lượng yêu cầu vượt quá tồn kho hiện có.');
        }

        $cart[$productId] = (int) $data['quantity'];
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('cart_notice', 'Đã cập nhật số lượng.');
    }

    private function sessionCart(Request $request): array
    {
        $storedCart = $request->session()->get('cart', []);
        if (! is_array($storedCart)) {
            return [];
        }

        $cart = [];
        foreach ($storedCart as $productId => $quantity) {
            $id = filter_var($productId, FILTER_VALIDATE_INT);
            $count = filter_var($quantity, FILTER_VALIDATE_INT);
            if ($id && $id > 0 && $count && $count > 0) {
                $cart[$id] = min(99, $count);
            }
        }

        return $cart;
    }

    private function forgetProductOptions(Request $request, int $productId): void
    {
        $options = $request->session()->get('cart_options', []);
        if (is_array($options)) {
            unset($options[$productId]);
            $request->session()->put('cart_options', $options);
        }
    }

    private function failure(Request $request, string $message, int $status): RedirectResponse|JsonResponse|Response
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], $status);
        }

        return back()->withInput()->with('cart_error', $message);
    }
}
