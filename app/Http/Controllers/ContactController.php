<?php

namespace App\Http\Controllers;

use App\Models\CustomerConsultation;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContactController extends Controller
{
    private const TOPICS = [
        'warranty' => 'Chính sách bảo hành',
        'insurance' => 'Chính sách bảo hiểm',
        'refund' => 'Chính sách hoàn tiền',
        'promotion' => 'Chính sách khuyến mãi',
        'dealer' => 'Đăng ký làm đại lý',
    ];

    public function index(Request $request): View
    {
        $topic = $request->query('topic');

        return view('storefront.contact', [
            'categories' => config('store.categories'),
            'topic' => is_string($topic) ? (self::TOPICS[$topic] ?? '') : '',
            'sent' => $request->session()->get('contact_sent', false),
            'demoMode' => config('store.demo_mode'),
            'demoNotice' => $request->session()->get('contact_demo_notice', false),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->submit($request);
    }

    public function storeProductConsultation(Request $request, string $slug): RedirectResponse
    {
        try {
            $product = Product::query()
                ->where('hien_thi', true)
                ->where('slug', $slug)
                ->firstOrFail();
        } catch (QueryException $exception) {
            Log::error('Laravel product consultation could not load its product.', [
                'slug' => $slug,
                'exception' => $exception->getMessage(),
            ]);

            return response()->view('storefront.product-unavailable', [
                'loadError' => true,
            ], 503);
        }

        return $this->submit($request, $product);
    }

    private function submit(Request $request, ?Product $contextProduct = null): RedirectResponse
    {
        if (config('store.demo_mode')) {
            $redirect = $contextProduct
                ? redirect()->route('products.show', ['slug' => $contextProduct->slug])
                : redirect()->route('contact', ['topic' => $request->query('topic')]);

            return $redirect->with('contact_demo_notice', true);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:25', 'regex:/^[0-9+().\-\s]+$/'],
            'email' => [$contextProduct ? 'required' : 'nullable', 'email', 'max:190'],
            'product' => ['nullable', 'string', Rule::in(array_keys(config('store.categories')))],
            'product_id' => ['nullable', 'integer', 'min:1'],
            'message' => ['nullable', 'string', 'max:5000'],
            'topic' => ['nullable', 'string', Rule::in(array_keys(self::TOPICS))],
        ]);

        try {
            $productId = $contextProduct?->id;
            if (! empty($data['product_id'])) {
                $productId ??= Product::query()
                    ->whereKey($data['product_id'])
                    ->where('hien_thi', true)
                    ->value('id');
            }

            $details = [];
            if (! empty($data['product'])) {
                $details[] = 'Danh mục quan tâm: '.config('store.categories.'.$data['product']);
            }
            if (! empty($data['topic'])) {
                $details[] = 'Chủ đề: '.(self::TOPICS[$data['topic']] ?? '');
            }
            if ($contextProduct) {
                $details[] = 'Sản phẩm quan tâm: '.$contextProduct->ten;
            }
            if (! empty($data['message'])) {
                $details[] = $data['message'];
            }

            CustomerConsultation::create([
                'product_id' => $productId,
                'ho_ten' => $data['name'],
                'so_dien_thoai' => $data['phone'],
                'email' => $data['email'] ?? null,
                'noi_dung' => $details === [] ? null : implode("\n\n", $details),
            ]);
        } catch (QueryException $exception) {
            Log::error('Laravel customer consultation could not be saved.', [
                'exception' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('contact_error', 'Chưa thể lưu yêu cầu lúc này. Vui lòng gọi 0983 537 155 để được hỗ trợ.');
        }

        if ($contextProduct) {
            return redirect()
                ->route('products.show', ['slug' => $contextProduct->slug])
                ->with('contact_sent', true);
        }

        return redirect()
            ->route('contact', ['topic' => $request->query('topic')])
            ->with('contact_sent', true);
    }
}
