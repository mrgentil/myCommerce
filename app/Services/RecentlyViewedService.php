<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class RecentlyViewedService
{
    const MAX_ITEMS = 20;
    const SESSION_KEY = 'recently_viewed_products';

    /**
     * Add product to recently viewed
     */
    public static function add($productId)
    {
        $viewed = Session::get(self::SESSION_KEY, []);

        // Remove if already exists (to move to front)
        $viewed = array_diff($viewed, [$productId]);

        // Add to beginning
        array_unshift($viewed, $productId);

        // Keep only MAX_ITEMS
        $viewed = array_slice($viewed, 0, self::MAX_ITEMS);

        Session::put(self::SESSION_KEY, $viewed);
    }

    /**
     * Get recently viewed product IDs
     */
    public static function getIds($limit = 10, $excludeId = null)
    {
        $viewed = Session::get(self::SESSION_KEY, []);

        if ($excludeId) {
            $viewed = array_diff($viewed, [$excludeId]);
        }

        return array_slice($viewed, 0, $limit);
    }

    /**
     * Get recently viewed products
     */
    public static function get($limit = 10, $excludeId = null)
    {
        $ids = self::getIds($limit, $excludeId);

        if (empty($ids)) {
            return collect();
        }

        return Product::with(['translation', 'thumbnail', 'variants'])
            ->whereIn('id', $ids)
            ->where('status', true)
            ->orderByRaw('FIELD(id, ' . implode(',', $ids) . ')')
            ->get();
    }

    /**
     * Clear recently viewed
     */
    public static function clear()
    {
        Session::forget(self::SESSION_KEY);
    }
}
