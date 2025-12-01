<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Shop;

class ShopViewController extends Controller
{
    /**
     * Display shop listing page.
     */
    public function index()
    {
        $shops = Shop::where(function($query) {
                $query->where('status', 'approved')
                      ->orWhere('status', 'active');
            })
            ->with('vendor')
            ->paginate(12);

        // Add product count including vendor_id fallback
        $shops->getCollection()->transform(function ($shop) {
            $shop->products_count = Product::where(function($query) use ($shop) {
                $query->where('shop_id', $shop->id)
                      ->orWhere('vendor_id', $shop->vendor_id);
            })->where('status', 1)->count();
            return $shop;
        });

        return view('themes.xylo.shops.index', compact('shops'));
    }

    /**
     * Display a specific shop page.
     */
    public function show($slug)
    {
        $shop = Shop::where('slug', $slug)
            ->where(function ($query) {
                $query->where('status', 'approved')
                    ->orWhere('status', 'active');
            })
            ->with('vendor')
            ->firstOrFail();

        // Get products by shop_id OR by vendor_id (fallback for older products)
        $products = Product::where(function($query) use ($shop) {
                $query->where('shop_id', $shop->id)
                      ->orWhere('vendor_id', $shop->vendor_id);
            })
            ->where('status', 1)
            ->with(['translations', 'thumbnail', 'category', 'variants'])
            ->paginate(12);

        return view('themes.xylo.shops.show', compact('shop', 'products'));
    }
}
