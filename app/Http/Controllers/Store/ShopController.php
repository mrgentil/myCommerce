<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();

        $filters = [
            'category' => $request->input('category', []),
            'brand' => $request->input('brand', []),
            'shop' => $request->input('shop', []),
            'price_min' => $request->input('price_min', 0),
            'price_max' => $request->input('price_max', 10000),
            'color' => $request->input('color', []),
            'size' => $request->input('size', []),
            'sort' => $request->input('sort', 'newest'),
            'rating' => $request->input('rating', null),
            'in_stock' => $request->input('in_stock', false),
            'free_shipping' => $request->input('free_shipping', false),
            'on_sale' => $request->input('on_sale', false),
            'search' => $request->input('q', ''),
        ];

        $products = Product::with(['translation', 'variants.attributeValues', 'shop', 'vendor'])
            ->where('status', true)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            // Search filter
            ->when(!empty($filters['search']), function ($query) use ($filters) {
                $query->whereHas('translation', function ($q) use ($filters) {
                    $q->where('name', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('description', 'like', '%' . $filters['search'] . '%');
                });
            })
            // Category filter
            ->when(!empty($filters['category']), function ($query) use ($filters) {
                $query->whereIn('category_id', (array) $filters['category']);
            })
            // Brand filter
            ->when(!empty($filters['brand']), function ($query) use ($filters) {
                $query->whereIn('brand_id', (array) $filters['brand']);
            })
            // Shop filter
            ->when(!empty($filters['shop']), function ($query) use ($filters) {
                $query->whereIn('shop_id', (array) $filters['shop']);
            })
            // Rating filter
            ->when($filters['rating'], function ($query) use ($filters) {
                $query->having('reviews_avg_rating', '>=', $filters['rating']);
            })
            // In stock filter
            ->when($filters['in_stock'], function ($query) {
                $query->whereHas('variants', function ($q) {
                    $q->where('quantity', '>', 0);
                });
            })
            // On sale filter
            ->when($filters['on_sale'], function ($query) {
                $query->whereHas('variants', function ($q) {
                    $q->whereNotNull('compare_price')
                      ->whereColumn('price', '<', 'compare_price');
                });
            })
            // Price and attribute filters
            ->whereHas('variants', function ($variantQuery) use ($filters) {
                $variantQuery
                    ->when($filters['price_min'], function ($q) use ($filters) {
                        $q->where('price', '>=', $filters['price_min']);
                    })
                    ->when($filters['price_max'] && $filters['price_max'] < 10000, function ($q) use ($filters) {
                        $q->where('price', '<=', $filters['price_max']);
                    })
                    ->when(!empty($filters['color']), function ($q) use ($filters) {
                        $q->whereHas('attributeValues', function ($avQuery) use ($filters) {
                            $avQuery->whereIn('value', (array) $filters['color'])
                                ->whereHas('attribute', function ($aQuery) {
                                    $aQuery->where('name', 'Color');
                                });
                        });
                    })
                    ->when(!empty($filters['size']), function ($q) use ($filters) {
                        $q->whereHas('attributeValues', function ($avQuery) use ($filters) {
                            $avQuery->whereIn('value', (array) $filters['size'])
                                ->whereHas('attribute', function ($aQuery) {
                                    $aQuery->where('name', 'Size');
                                });
                        });
                    });
            });

        // Apply sorting
        switch ($filters['sort']) {
            case 'price_low':
                $products->orderByRaw('(SELECT MIN(price) FROM product_variants WHERE product_variants.product_id = products.id) ASC');
                break;
            case 'price_high':
                $products->orderByRaw('(SELECT MAX(price) FROM product_variants WHERE product_variants.product_id = products.id) DESC');
                break;
            case 'popular':
                $products->orderBy('reviews_count', 'desc');
                break;
            case 'rating':
                $products->orderByDesc('reviews_avg_rating');
                break;
            case 'bestselling':
                $products->withCount('orderDetails')->orderBy('order_details_count', 'desc');
                break;
            default:
                $products->orderBy('created_at', 'desc');
        }

        $products = $products->paginate(12)->appends($request->query());

        // Get filter options
        $brands = Brand::with('translation')->withCount('products')->having('products_count', '>', 0)->get();
        $categories = Category::with('translation')->withCount('products')->having('products_count', '>', 0)->get();
        $shops = Shop::where(function ($query) {
            $query->where('status', 'approved')
                ->orWhere('status', 'active');
        })->withCount('products')->having('products_count', '>', 0)->get();

        // Get price range
        $priceRange = [
            'min' => Product::whereHas('variants')->with('variants')->get()->flatMap->variants->min('price') ?? 0,
            'max' => Product::whereHas('variants')->with('variants')->get()->flatMap->variants->max('price') ?? 10000,
        ];

        if ($request->ajax()) {
            return response()->json([
                'html' => view('themes.xylo.partials.product-list', compact('products'))->render(),
                'pagination' => $products->links()->toHtml(),
                'total' => $products->total(),
            ]);
        }

        return view('themes.xylo.shop', compact('products', 'categories', 'brands', 'shops', 'filters', 'priceRange'));
    }
}
