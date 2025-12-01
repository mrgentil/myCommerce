<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use Illuminate\Http\Request;

class FlashSaleController extends Controller
{
    /**
     * Display current flash sale
     */
    public function index()
    {
        $currentSale = FlashSale::active()
            ->with(['products' => function ($q) {
                $q->with(['product.translation', 'product.thumbnail', 'product.primaryVariant', 'variant'])
                  ->orderBy('quantity_sold', 'desc');
            }])
            ->first();

        $upcomingSales = FlashSale::upcoming()
            ->orderBy('starts_at')
            ->take(3)
            ->get();

        return view('themes.xylo.flash-sale', compact('currentSale', 'upcomingSales'));
    }

    /**
     * Get flash sale data for AJAX refresh
     */
    public function getData($id)
    {
        $sale = FlashSale::with(['products.product.translation', 'products.product.thumbnail'])
            ->findOrFail($id);

        if (!$sale->isLive()) {
            return response()->json(['ended' => true]);
        }

        return response()->json([
            'ended' => false,
            'seconds_remaining' => $sale->seconds_remaining,
            'products' => $sale->products->map(function ($p) {
                return [
                    'id' => $p->id,
                    'quantity_sold' => $p->quantity_sold,
                    'remaining' => $p->remaining,
                    'sold_percentage' => $p->sold_percentage,
                    'is_available' => $p->isAvailable(),
                ];
            }),
        ]);
    }

    /**
     * Get deals page (all discounted products)
     */
    public function deals(Request $request)
    {
        $products = \App\Models\Product::with(['translation', 'thumbnail', 'primaryVariant', 'variants'])
            ->where('status', true)
            ->whereHas('variants', function ($q) {
                $q->whereNotNull('compare_price')
                  ->whereColumn('price', '<', 'compare_price');
            })
            ->withAvg('reviews', 'rating')
            ->orderByDesc('created_at')
            ->paginate(24);

        return view('themes.xylo.deals', compact('products'));
    }
}
