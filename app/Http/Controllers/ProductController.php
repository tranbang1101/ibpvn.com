<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(string $slug): View|Response
    {
        try {
            $product = Product::query()
                ->where('hien_thi', true)
                ->where('slug', $slug)
                ->with([
                    'detail',
                    'images' => static fn ($query) => $query->orderBy('thu_tu')->orderBy('id'),
                    'variants',
                    'addons' => static fn ($query) => $query->where('hien_thi', true)->orderBy('thu_tu')->orderBy('id'),
                    'faqs' => static fn ($query) => $query->where('hien_thi', true)->orderBy('thu_tu')->orderBy('id'),
                    'reviews' => static fn ($query) => $query->where('trang_thai', true)->latest('created_at')->latest('id')->limit(30),
                ])
                ->first();
        } catch (QueryException $exception) {
            Log::error('Laravel product detail data could not be loaded.', [
                'slug' => $slug,
                'exception' => $exception->getMessage(),
            ]);

            return response()->view('storefront.product-unavailable', [
                'loadError' => true,
            ], 503);
        }

        abort_unless($product, 404);
        if (! config('store.demo_mode')) {
            $product->increment('luot_xem');
        }

        $specifications = $product->detail?->thong_so_ky_thuat ?? [];
        if (! is_array($specifications)) {
            $specifications = [];
        }

        $origin = $specifications['Xuất xứ'] ?? ($product->thuong_hieu ?: 'Đang cập nhật');
        $defaultSpecifications = [
            'Thương hiệu' => $product->thuong_hieu ?: 'Đang cập nhật',
            'Danh mục' => $product->danh_muc ?: 'Đang cập nhật',
            'Bảo hành' => $product->detail?->bao_hanh ?: 'Đang cập nhật',
            'Điện áp' => 'AC 220V / 50Hz',
            'Công suất' => 'Theo cấu hình sản phẩm',
            'Kích thước' => 'Vui lòng liên hệ tư vấn',
            'Trọng lượng' => 'Vui lòng liên hệ tư vấn',
            'Xuất xứ' => $origin,
        ];
        $specifications = array_slice($specifications + $defaultSpecifications, 0, 8, true);
        $imageUrls = $product->images
            ->pluck('image_url')
            ->filter()
            ->values();
        if ($imageUrls->isEmpty()) {
            $imageUrls = collect([$product->anh_chinh ?: '/assets/images/sanpham.png']);
        }
        $imageFallbacks = [
            '/assets/images/a.o.smith-mini1.png',
            '/assets/images/a.o.smith-mini2.png',
            '/assets/images/a.o.smith-mini3.png',
        ];
        while ($imageUrls->count() < 4) {
            $imageUrls->push($imageFallbacks[($imageUrls->count() - 1) % count($imageFallbacks)]);
        }

        $similarProducts = Product::query()
            ->where('hien_thi', true)
            ->where('id', '<>', $product->id)
            ->when($product->danh_muc, static fn ($query, $category) => $query->where('danh_muc', $category))
            ->orderByDesc('da_ban')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return view('storefront.product', [
            'product' => $product,
            'origin' => $origin,
            'specifications' => $specifications,
            'imageUrls' => $imageUrls,
            'similarProducts' => $similarProducts,
        ]);
    }

    public function showLegacyId(): View|Response
    {
        $id = filter_var(request()->query('id'), FILTER_VALIDATE_INT);
        abort_unless($id && $id > 0, 404);

        try {
            $slug = Product::query()->whereKey($id)->where('hien_thi', true)->value('slug');
        } catch (QueryException $exception) {
            Log::error('Legacy product detail redirect could not be resolved.', [
                'product_id' => $id,
                'exception' => $exception->getMessage(),
            ]);

            return response()->view('storefront.product-unavailable', ['loadError' => true], 503);
        }

        abort_unless($slug, 404);

        return $this->show($slug);
    }
}
