<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function index()
    {
        $vendorId = Auth::guard('vendor')->id();

        // Sales statistics
        $totalSales = Order::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->sum('total_amount');

        $totalOrders = Order::where('vendor_id', $vendorId)->count();
        $completedOrders = Order::where('vendor_id', $vendorId)->where('status', 'completed')->count();
        
        $conversionRate = $totalOrders > 0 
            ? round(($completedOrders / $totalOrders) * 100, 1) 
            : 0;

        $averageOrderValue = $completedOrders > 0 
            ? round($totalSales / $completedOrders, 2) 
            : 0;

        // Product statistics
        $totalProducts = Product::where('vendor_id', $vendorId)->count();
        $activeProducts = Product::where('vendor_id', $vendorId)->where('status', 1)->count();
        
        $totalViews = 0; // Views tracking not implemented
        $totalSalesCount = $completedOrders; // Use completed orders as sales count

        // Top products (recent products)
        $topProductsBySales = Product::where('vendor_id', $vendorId)
            ->with(['translation', 'primaryVariant'])
            ->latest()
            ->take(10)
            ->get();

        // Top products by views (same as above for now)
        $topProductsByViews = $topProductsBySales;

        // Sales by month (last 12 months)
        $salesByMonth = $this->getSalesByMonth($vendorId, 12);

        // Orders by status
        $ordersByStatus = [
            'pending' => Order::where('vendor_id', $vendorId)->where('status', 'pending')->count(),
            'processing' => Order::where('vendor_id', $vendorId)->where('status', 'processing')->count(),
            'shipped' => Order::where('vendor_id', $vendorId)->where('status', 'shipped')->count(),
            'completed' => Order::where('vendor_id', $vendorId)->where('status', 'completed')->count(),
            'cancelled' => Order::where('vendor_id', $vendorId)->where('status', 'cancelled')->count(),
        ];

        // Daily sales for current month
        $dailySales = $this->getDailySales($vendorId);

        // Comparison with previous period
        $thisMonthSales = Order::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $lastMonthSales = Order::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total_amount');

        $salesGrowth = $lastMonthSales > 0 
            ? round((($thisMonthSales - $lastMonthSales) / $lastMonthSales) * 100, 1) 
            : ($thisMonthSales > 0 ? 100 : 0);

        return view('vendor.statistics.index', compact(
            'totalSales',
            'totalOrders',
            'completedOrders',
            'conversionRate',
            'averageOrderValue',
            'totalProducts',
            'activeProducts',
            'totalViews',
            'totalSalesCount',
            'topProductsBySales',
            'topProductsByViews',
            'salesByMonth',
            'ordersByStatus',
            'dailySales',
            'thisMonthSales',
            'lastMonthSales',
            'salesGrowth'
        ));
    }

    private function getSalesByMonth($vendorId, $months)
    {
        $labels = [];
        $data = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M');

            $sales = Order::where('vendor_id', $vendorId)
                ->where('status', 'completed')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('total_amount');

            $data[] = round($sales, 2);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function getDailySales($vendorId)
    {
        $labels = [];
        $data = [];
        $daysInMonth = now()->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create(now()->year, now()->month, $day);
            $labels[] = $day;

            if ($date->isPast() || $date->isToday()) {
                $sales = Order::where('vendor_id', $vendorId)
                    ->where('status', 'completed')
                    ->whereDate('created_at', $date)
                    ->sum('total_amount');
                $data[] = round($sales, 2);
            } else {
                $data[] = null;
            }
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
