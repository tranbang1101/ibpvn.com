<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function home(): View
    {
        $categories = config('store.categories');
        $categoryProducts = [];
        $featuredReviews = collect();
        $loadError = false;

        try {
            foreach ($categories as $key => $name) {
                $products = Product::query()
                    ->where('hien_thi', true)
                    ->where('danh_muc', $name)
                    ->orderByDesc('da_ban')
                    ->orderByDesc('id')
                    ->limit(6)
                    ->get();

                if ($products->isEmpty()) {
                    continue;
                }

                while ($products->count() < 6) {
                    $products->push($products[$products->count() % $products->count()]);
                }

                $categoryProducts[$key] = [
                    'name' => $name,
                    'products' => $products,
                ];
            }

            $featuredReviews = Review::query()
                ->where('trang_thai', true)
                ->whereHas('product', static fn ($query) => $query->where('hien_thi', true))
                ->with('product:id,ten')
                ->latest('created_at')
                ->latest('id')
                ->limit(4)
                ->get();
        } catch (QueryException $exception) {
            Log::error('Laravel storefront data could not be loaded.', [
                'exception' => $exception->getMessage(),
            ]);
            $categoryProducts = [];
            $featuredReviews = collect();
            $loadError = true;
        }

        $discountedProducts = collect($categoryProducts)
            ->flatMap(static fn (array $category) => $category['products'])
            ->filter(static fn (Product $product) => $product->gia_khuyen_mai > 0
                && $product->gia_khuyen_mai < $product->gia)
            ->take(18)
            ->values();

        return view('storefront.home', [
            'categories' => $categories,
            'categoryProducts' => $categoryProducts,
            'discountedProducts' => $discountedProducts,
            'featuredReviews' => $featuredReviews,
            'loadError' => $loadError,
        ]);
    }

    public function catalog(Request $request): View
    {
        $categories = config('store.categories');
        $categoryKey = $request->query('category', '');
        $categoryKey = is_string($categoryKey) ? trim($categoryKey) : '';
        $categoryName = $categories[$categoryKey] ?? '';
        $keyword = $request->query('keyword', '');
        $keyword = is_string($keyword) ? mb_substr(trim($keyword), 0, 120) : '';
        $sortOptions = [
            'popular' => 'Bán chạy',
            'price_asc' => 'Giá thấp đến cao',
            'price_desc' => 'Giá cao đến thấp',
        ];
        $sort = $request->query('sort', 'popular');
        $sort = is_string($sort) && isset($sortOptions[$sort]) ? $sort : 'popular';

        $products = null;
        $loadError = false;

        try {
            $query = Product::query()->where('hien_thi', true);

            if ($categoryName !== '') {
                $query->where('danh_muc', $categoryName);
            }

            if ($keyword !== '') {
                $searchTerm = '%'.addcslashes($keyword, '\\%_').'%';
                $query->where(static function ($query) use ($searchTerm): void {
                    $query->where('ten', 'like', $searchTerm)
                        ->orWhere('thuong_hieu', 'like', $searchTerm)
                        ->orWhere('mo_ta_ngan', 'like', $searchTerm);
                });
            }

            match ($sort) {
                'price_asc' => $query->orderByRaw('COALESCE(NULLIF(gia_khuyen_mai, 0), gia) ASC')
                    ->orderByDesc('id'),
                'price_desc' => $query->orderByRaw('COALESCE(NULLIF(gia_khuyen_mai, 0), gia) DESC')
                    ->orderByDesc('id'),
                default => $query->orderByDesc('da_ban')->orderByDesc('id'),
            };

            $products = $query->paginate(12)->withQueryString();
        } catch (QueryException $exception) {
            Log::error('Laravel product catalog data could not be loaded.', [
                'exception' => $exception->getMessage(),
            ]);
            $loadError = true;
        }

        return view('storefront.catalog', [
            'categories' => $categories,
            'categoryKey' => $categoryKey,
            'categoryName' => $categoryName,
            'keyword' => $keyword,
            'sort' => $sort,
            'sortOptions' => $sortOptions,
            'products' => $products,
            'loadError' => $loadError,
        ]);
    }
}
