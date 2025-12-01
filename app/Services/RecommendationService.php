<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    /**
     * Get similar products based on category, brand, and attributes
     */
    public static function getSimilarProducts($product, $limit = 8)
    {
        return Product::with(['translation', 'thumbnail', 'variants'])
            ->where('id', '!=', $product->id)
            ->where('status', true)
            ->where(function ($query) use ($product) {
                // Same category
                $query->where('category_id', $product->category_id)
                    // Or same brand
                    ->orWhere('brand_id', $product->brand_id)
                    // Or same vendor
                    ->orWhere('vendor_id', $product->vendor_id);
            })
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->limit($limit)
            ->get();
    }

    /**
     * Get "Customers also bought" products
     */
    public static function getAlsoBought($product, $limit = 6)
    {
        // Get orders that contain this product
        $orderIds = DB::table('order_details')
            ->where('product_id', $product->id)
            ->pluck('order_id');

        if ($orderIds->isEmpty()) {
            return collect();
        }

        // Get other products from those orders
        $productIds = DB::table('order_details')
            ->whereIn('order_id', $orderIds)
            ->where('product_id', '!=', $product->id)
            ->select('product_id', DB::raw('COUNT(*) as frequency'))
            ->groupBy('product_id')
            ->orderByDesc('frequency')
            ->limit($limit)
            ->pluck('product_id');

        if ($productIds->isEmpty()) {
            return collect();
        }

        return Product::with(['translation', 'thumbnail', 'variants'])
            ->whereIn('id', $productIds)
            ->where('status', true)
            ->get();
    }

    /**
     * Get personalized recommendations for customer
     */
    public static function getPersonalized($customerId = null, $limit = 12)
    {
        if (!$customerId) {
            $customerId = Auth::guard('customer')->id();
        }

        if (!$customerId) {
            return self::getPopular($limit);
        }

        // Get customer's purchased categories and brands
        $purchasedData = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('orders.customer_id', $customerId)
            ->select('products.category_id', 'products.brand_id')
            ->get();

        $categoryIds = $purchasedData->pluck('category_id')->unique()->filter();
        $brandIds = $purchasedData->pluck('brand_id')->unique()->filter();

        // Get recently viewed categories
        $recentlyViewed = RecentlyViewedService::getIds(20);
        if (!empty($recentlyViewed)) {
            $viewedCategories = Product::whereIn('id', $recentlyViewed)->pluck('category_id');
            $categoryIds = $categoryIds->merge($viewedCategories)->unique();
        }

        if ($categoryIds->isEmpty()) {
            return self::getPopular($limit);
        }

        // Get products from those categories/brands not yet purchased
        $purchasedProductIds = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->where('orders.customer_id', $customerId)
            ->pluck('order_details.product_id');

        return Product::with(['translation', 'thumbnail', 'variants'])
            ->where('status', true)
            ->whereNotIn('id', $purchasedProductIds)
            ->where(function ($query) use ($categoryIds, $brandIds) {
                $query->whereIn('category_id', $categoryIds)
                    ->orWhereIn('brand_id', $brandIds);
            })
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->limit($limit)
            ->get();
    }

    /**
     * Get popular/trending products
     */
    public static function getPopular($limit = 12)
    {
        return Product::with(['translation', 'thumbnail', 'variants'])
            ->where('status', true)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->withCount('orderDetails')
            ->orderByDesc('order_details_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get new arrivals
     */
    public static function getNewArrivals($limit = 12)
    {
        return Product::with(['translation', 'thumbnail', 'variants'])
            ->where('status', true)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get deals/discounted products
     */
    public static function getDeals($limit = 12)
    {
        return Product::with(['translation', 'thumbnail', 'variants'])
            ->where('status', true)
            ->whereHas('variants', function ($q) {
                $q->whereNotNull('compare_price')
                  ->whereColumn('price', '<', 'compare_price');
            })
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->limit($limit)
            ->get();
    }
}
