<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\VendorPayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FinanceController extends Controller
{
    public function index()
    {
        $vendorId = Auth::guard('vendor')->id();
        $vendor = Auth::guard('vendor')->user();

        // Calculate earnings
        $totalEarnings = Order::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->sum('total_amount');

        $monthEarnings = Order::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $weekEarnings = Order::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('total_amount');

        $todayEarnings = Order::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total_amount');

        // Commission rate (default 10%)
        $commissionRate = $vendor->commission_rate ?? 10;
        $totalCommission = $totalEarnings * ($commissionRate / 100);
        $netEarnings = $totalEarnings - $totalCommission;

        // Payouts
        $totalPaid = VendorPayout::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->sum('amount');

        $pendingPayouts = VendorPayout::where('vendor_id', $vendorId)
            ->where('status', 'pending')
            ->sum('amount');

        $availableBalance = $netEarnings - $totalPaid - $pendingPayouts;

        // Recent transactions (completed orders)
        $recentTransactions = Order::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Payout history
        $payouts = VendorPayout::where('vendor_id', $vendorId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Monthly earnings chart (last 6 months)
        $monthlyEarnings = $this->getMonthlyEarnings($vendorId, 6);

        return view('vendor.finance.index', compact(
            'totalEarnings',
            'monthEarnings',
            'weekEarnings',
            'todayEarnings',
            'commissionRate',
            'totalCommission',
            'netEarnings',
            'totalPaid',
            'pendingPayouts',
            'availableBalance',
            'recentTransactions',
            'payouts',
            'monthlyEarnings'
        ));
    }

    public function requestPayout(Request $request)
    {
        $vendorId = Auth::guard('vendor')->id();
        $vendor = Auth::guard('vendor')->user();

        $request->validate([
            'amount' => 'required|numeric|min:10'
        ]);

        // Calculate available balance
        $totalEarnings = Order::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->sum('total_amount');

        $commissionRate = $vendor->commission_rate ?? 10;
        $netEarnings = $totalEarnings * (1 - $commissionRate / 100);

        $totalPaid = VendorPayout::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->sum('amount');

        $pendingPayouts = VendorPayout::where('vendor_id', $vendorId)
            ->where('status', 'pending')
            ->sum('amount');

        $availableBalance = $netEarnings - $totalPaid - $pendingPayouts;

        if ($request->amount > $availableBalance) {
            return back()->with('error', 'Solde insuffisant pour cette demande.');
        }

        VendorPayout::create([
            'vendor_id' => $vendorId,
            'amount' => $request->amount,
            'status' => 'pending',
            'payment_method' => $request->payment_method ?? 'bank_transfer',
            'notes' => $request->notes
        ]);

        return back()->with('success', 'Demande de retrait envoyée avec succès.');
    }

    private function getMonthlyEarnings($vendorId, $months)
    {
        $labels = [];
        $data = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $earnings = Order::where('vendor_id', $vendorId)
                ->where('status', 'completed')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('total_amount');

            $data[] = round($earnings, 2);
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
