<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\LoyaltyTransaction;
use App\Models\LoyaltyReward;
use Illuminate\Support\Facades\DB;

class LoyaltyService
{
    // Points per euro spent
    const POINTS_PER_EURO = 1;
    
    // Points for actions
    const POINTS_FOR_REVIEW = 50;
    const POINTS_FOR_REGISTRATION = 100;
    const POINTS_FOR_FIRST_ORDER = 200;

    /**
     * Award points for an order
     */
    public static function awardOrderPoints($customerId, $orderAmount, $orderId)
    {
        $points = floor($orderAmount * self::POINTS_PER_EURO);
        
        if ($points <= 0) return 0;

        return self::addPoints($customerId, $points, 'earned', 
            "Points pour commande #{$orderId}", 'Order', $orderId);
    }

    /**
     * Award points for a review
     */
    public static function awardReviewPoints($customerId, $reviewId)
    {
        return self::addPoints($customerId, self::POINTS_FOR_REVIEW, 'earned',
            "Points pour avis produit", 'ProductReview', $reviewId);
    }

    /**
     * Award registration bonus
     */
    public static function awardRegistrationBonus($customerId)
    {
        return self::addPoints($customerId, self::POINTS_FOR_REGISTRATION, 'bonus',
            "Bonus de bienvenue");
    }

    /**
     * Award first order bonus
     */
    public static function awardFirstOrderBonus($customerId, $orderId)
    {
        // Check if this is the first order
        $orderCount = \App\Models\Order::where('customer_id', $customerId)->count();
        
        if ($orderCount <= 1) {
            return self::addPoints($customerId, self::POINTS_FOR_FIRST_ORDER, 'bonus',
                "Bonus première commande", 'Order', $orderId);
        }
        
        return 0;
    }

    /**
     * Add points to customer
     */
    public static function addPoints($customerId, $points, $type, $description, $refType = null, $refId = null)
    {
        return DB::transaction(function () use ($customerId, $points, $type, $description, $refType, $refId) {
            $customer = Customer::lockForUpdate()->findOrFail($customerId);
            
            $customer->increment('loyalty_points', $points);
            $customer->increment('total_points_earned', $points);

            LoyaltyTransaction::create([
                'customer_id' => $customerId,
                'points' => $points,
                'type' => $type,
                'description' => $description,
                'reference_type' => $refType,
                'reference_id' => $refId,
                'balance_after' => $customer->fresh()->loyalty_points,
            ]);

            return $points;
        });
    }

    /**
     * Redeem points for a reward
     */
    public static function redeemReward($customerId, $rewardId)
    {
        return DB::transaction(function () use ($customerId, $rewardId) {
            $customer = Customer::lockForUpdate()->findOrFail($customerId);
            $reward = LoyaltyReward::active()->findOrFail($rewardId);

            if ($customer->loyalty_points < $reward->points_required) {
                throw new \Exception('Points insuffisants');
            }

            $customer->decrement('loyalty_points', $reward->points_required);

            LoyaltyTransaction::create([
                'customer_id' => $customerId,
                'points' => -$reward->points_required,
                'type' => 'redeemed',
                'description' => "Échange: {$reward->name}",
                'reference_type' => 'LoyaltyReward',
                'reference_id' => $rewardId,
                'balance_after' => $customer->fresh()->loyalty_points,
            ]);

            return $reward;
        });
    }

    /**
     * Refund points (e.g., for cancelled order)
     */
    public static function refundPoints($customerId, $points, $description, $refType = null, $refId = null)
    {
        return DB::transaction(function () use ($customerId, $points, $description, $refType, $refId) {
            $customer = Customer::lockForUpdate()->findOrFail($customerId);
            
            $customer->decrement('loyalty_points', $points);
            $customer->decrement('total_points_earned', $points);

            LoyaltyTransaction::create([
                'customer_id' => $customerId,
                'points' => -$points,
                'type' => 'refund',
                'description' => $description,
                'reference_type' => $refType,
                'reference_id' => $refId,
                'balance_after' => $customer->fresh()->loyalty_points,
            ]);

            return $points;
        });
    }

    /**
     * Get customer loyalty level
     */
    public static function getLevel($totalPointsEarned)
    {
        if ($totalPointsEarned >= 10000) return ['name' => 'Platine', 'color' => '#E5E4E2', 'multiplier' => 2.0];
        if ($totalPointsEarned >= 5000) return ['name' => 'Or', 'color' => '#FFD700', 'multiplier' => 1.5];
        if ($totalPointsEarned >= 2000) return ['name' => 'Argent', 'color' => '#C0C0C0', 'multiplier' => 1.25];
        if ($totalPointsEarned >= 500) return ['name' => 'Bronze', 'color' => '#CD7F32', 'multiplier' => 1.1];
        return ['name' => 'Membre', 'color' => '#6c757d', 'multiplier' => 1.0];
    }
}
