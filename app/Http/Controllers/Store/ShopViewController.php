<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopFollower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            ->withCount('products')
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
            ->withAvg('reviews', 'rating')
            ->paginate(12);

        // Check if customer follows this shop
        $isFollowing = false;
        $customerId = Auth::guard('customer')->id();
        if ($customerId) {
            $isFollowing = ShopFollower::isFollowing($shop->id, $customerId);
        }

        return view('themes.xylo.shops.show', compact('shop', 'products', 'isFollowing'));
    }

    /**
     * Toggle follow shop
     */
    public function toggleFollow(Request $request, $shopId)
    {
        $customerId = Auth::guard('customer')->id();
        
        if (!$customerId) {
            return response()->json(['error' => 'Connexion requise'], 401);
        }

        $shop = Shop::findOrFail($shopId);
        $isFollowing = ShopFollower::toggle($shop->id, $customerId);

        // Notify vendor if new follower
        if ($isFollowing && $shop->vendor_id) {
            \App\Models\UserNotification::notifyVendor(
                $shop->vendor_id,
                'promotion',
                'Nouveau follower',
                "Un client suit maintenant votre boutique !",
                route('vendor.dashboard')
            );
        }

        return response()->json([
            'success' => true,
            'is_following' => $isFollowing,
            'followers_count' => $shop->fresh()->followers_count,
        ]);
    }

    /**
     * Get customer's followed shops
     */
    public function following()
    {
        $customerId = Auth::guard('customer')->id();
        
        if (!$customerId) {
            return redirect()->route('customer.login');
        }

        $followedShops = Shop::whereHas('followers', function ($q) use ($customerId) {
            $q->where('customer_id', $customerId);
        })
        ->with('vendor')
        ->withCount('products')
        ->paginate(12);

        return view('themes.xylo.customer.following', compact('followedShops'));
    }
}
