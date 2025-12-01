<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GiftCard;
use Illuminate\Http\Request;

class GiftCardController extends Controller
{
    public function index(Request $request)
    {
        $query = GiftCard::with(['purchaser', 'redeemedByCustomer']);

        if ($request->status === 'active') {
            $query->where('is_active', true)->where('current_balance', '>', 0);
        } elseif ($request->status === 'used') {
            $query->where('current_balance', 0);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        $giftCards = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total' => GiftCard::count(),
            'active' => GiftCard::where('is_active', true)->where('current_balance', '>', 0)->count(),
            'total_value' => GiftCard::sum('current_balance'),
        ];

        return view('admin.gift-cards.index', compact('giftCards', 'stats'));
    }

    public function create()
    {
        return view('admin.gift-cards.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:5|max:1000',
            'quantity' => 'required|integer|min:1|max:100',
            'recipient_email' => 'nullable|email',
            'recipient_name' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:500',
        ]);

        $cards = [];
        for ($i = 0; $i < $request->quantity; $i++) {
            $cards[] = GiftCard::createCard(
                $request->amount,
                null,
                $request->recipient_email,
                $request->recipient_name,
                $request->message
            );
        }

        if ($request->quantity === 1) {
            return redirect()->route('admin.gift-cards.show', $cards[0]->id)
                ->with('success', 'Carte cadeau créée. Code: ' . $cards[0]->code);
        }

        return redirect()->route('admin.gift-cards.index')
            ->with('success', $request->quantity . ' cartes cadeaux créées.');
    }

    public function show($id)
    {
        $giftCard = GiftCard::with(['purchaser', 'redeemedByCustomer', 'transactions.order'])
            ->findOrFail($id);

        return view('admin.gift-cards.show', compact('giftCard'));
    }

    public function toggleStatus($id)
    {
        $giftCard = GiftCard::findOrFail($id);
        $giftCard->update(['is_active' => !$giftCard->is_active]);

        return redirect()->back()->with('success', 'Statut mis à jour.');
    }

    public function adjustBalance(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'reason' => 'required|string|max:255',
        ]);

        $giftCard = GiftCard::findOrFail($id);
        $newBalance = max(0, $giftCard->current_balance + $request->amount);
        
        $giftCard->update(['current_balance' => $newBalance]);

        // Log the adjustment
        \App\Models\GiftCardTransaction::create([
            'gift_card_id' => $giftCard->id,
            'amount' => $request->amount,
            'type' => $request->amount > 0 ? 'purchase' : 'redemption',
            'balance_after' => $newBalance,
        ]);

        return redirect()->back()->with('success', 'Solde ajusté.');
    }
}
