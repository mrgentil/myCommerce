<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyReward;
use App\Models\LoyaltyTransaction;
use App\Models\Customer;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    public function index()
    {
        $rewards = LoyaltyReward::orderBy('points_required')->get();
        
        $stats = [
            'total_points_issued' => LoyaltyTransaction::where('type', 'earned')->sum('points'),
            'total_points_redeemed' => abs(LoyaltyTransaction::where('type', 'redeemed')->sum('points')),
            'active_customers' => Customer::where('loyalty_points', '>', 0)->count(),
        ];

        $recentTransactions = LoyaltyTransaction::with('customer')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return view('admin.loyalty.index', compact('rewards', 'stats', 'recentTransactions'));
    }

    public function createReward()
    {
        return view('admin.loyalty.create-reward');
    }

    public function storeReward(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'points_required' => 'required|integer|min:1',
            'reward_type' => 'required|in:discount_percent,discount_fixed,free_shipping,product',
            'reward_value' => 'nullable|numeric|min:0',
        ]);

        LoyaltyReward::create($request->only([
            'name', 'description', 'points_required', 'reward_type', 'reward_value', 'product_id'
        ]));

        return redirect()->route('admin.loyalty.index')->with('success', 'Récompense créée.');
    }

    public function editReward($id)
    {
        $reward = LoyaltyReward::findOrFail($id);
        return view('admin.loyalty.edit-reward', compact('reward'));
    }

    public function updateReward(Request $request, $id)
    {
        $reward = LoyaltyReward::findOrFail($id);
        
        $reward->update([
            'name' => $request->name,
            'description' => $request->description,
            'points_required' => $request->points_required,
            'reward_type' => $request->reward_type,
            'reward_value' => $request->reward_value,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->back()->with('success', 'Récompense mise à jour.');
    }

    public function deleteReward($id)
    {
        LoyaltyReward::findOrFail($id)->delete();
        return redirect()->route('admin.loyalty.index')->with('success', 'Récompense supprimée.');
    }

    /**
     * Manually add points to a customer
     */
    public function addPoints(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'points' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        LoyaltyService::addPoints(
            $request->customer_id,
            $request->points,
            'bonus',
            $request->reason
        );

        return redirect()->back()->with('success', 'Points ajoutés.');
    }

    /**
     * View customer points history
     */
    public function customerHistory($customerId)
    {
        $customer = Customer::findOrFail($customerId);
        $transactions = LoyaltyTransaction::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.loyalty.customer-history', compact('customer', 'transactions'));
    }
}
