<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorBadge extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'description',
        'requirements',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'requirements' => 'array',
        'is_active' => 'boolean',
    ];

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'vendor_badge_assignments', 'badge_id', 'vendor_id')
            ->withPivot(['awarded_at', 'expires_at'])
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if vendor qualifies for this badge
     */
    public function vendorQualifies(Vendor $vendor)
    {
        if (!$this->requirements) return false;

        $reqs = $this->requirements;

        // Check minimum sales
        if (isset($reqs['min_sales']) && $vendor->total_sales < $reqs['min_sales']) {
            return false;
        }

        // Check minimum orders
        if (isset($reqs['min_orders']) && $vendor->total_orders < $reqs['min_orders']) {
            return false;
        }

        // Check minimum rating
        if (isset($reqs['min_rating']) && ($vendor->avg_rating ?? 0) < $reqs['min_rating']) {
            return false;
        }

        // Check account age
        if (isset($reqs['min_days_active'])) {
            $daysActive = $vendor->created_at->diffInDays(now());
            if ($daysActive < $reqs['min_days_active']) {
                return false;
            }
        }

        // Check verification
        if (isset($reqs['is_verified']) && $reqs['is_verified'] && !$vendor->is_verified) {
            return false;
        }

        return true;
    }

    /**
     * Get default badges to seed
     */
    public static function getDefaultBadges()
    {
        return [
            [
                'name' => 'Vendeur Vérifié',
                'slug' => 'verified',
                'icon' => 'bi-patch-check-fill',
                'color' => '#0d6efd',
                'description' => 'Identité vérifiée par la plateforme',
                'requirements' => ['is_verified' => true],
                'priority' => 100,
            ],
            [
                'name' => 'Top Vendeur',
                'slug' => 'top-seller',
                'icon' => 'bi-award-fill',
                'color' => '#ffc107',
                'description' => 'Plus de 100 ventes avec une note moyenne de 4.5+',
                'requirements' => ['min_orders' => 100, 'min_rating' => 4.5],
                'priority' => 90,
            ],
            [
                'name' => 'Vendeur Pro',
                'slug' => 'pro-seller',
                'icon' => 'bi-star-fill',
                'color' => '#6f42c1',
                'description' => 'Plus de 50 ventes avec une excellente réputation',
                'requirements' => ['min_orders' => 50, 'min_rating' => 4.0],
                'priority' => 80,
            ],
            [
                'name' => 'Nouveau Vendeur',
                'slug' => 'new-seller',
                'icon' => 'bi-lightning-fill',
                'color' => '#20c997',
                'description' => 'Vendeur récemment inscrit',
                'requirements' => [],
                'priority' => 10,
            ],
            [
                'name' => 'Réponse Rapide',
                'slug' => 'fast-response',
                'icon' => 'bi-chat-dots-fill',
                'color' => '#198754',
                'description' => 'Répond généralement en moins de 2 heures',
                'requirements' => [],
                'priority' => 60,
            ],
        ];
    }
}
