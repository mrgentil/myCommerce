<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $vendorId = Auth::guard('vendor')->id();
        $vendor = Auth::guard('vendor')->user();
        $shop = Shop::where('vendor_id', $vendorId)->first();

        // Basic stats
        $totalSales = Order::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->sum('total_amount');

        $todaySales = Order::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total_amount');

        $monthSales = Order::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $totalOrders = Order::where('vendor_id', $vendorId)->count();
        $pendingOrders = Order::where('vendor_id', $vendorId)->where('status', 'pending')->count();
        $processingOrders = Order::where('vendor_id', $vendorId)->where('status', 'processing')->count();
        $completedOrders = Order::where('vendor_id', $vendorId)->where('status', 'completed')->count();

        $totalProducts = Product::where('vendor_id', $vendorId)->count();
        $activeProducts = Product::where('vendor_id', $vendorId)->where('status', 1)->count();
        
        // Stock is in product_variants table
        $lowStockProducts = Product::where('vendor_id', $vendorId)
            ->whereHas('variants', function($q) {
                $q->where('stock', '<=', 5)->where('stock', '>', 0);
            })
            ->count();
        $outOfStockProducts = Product::where('vendor_id', $vendorId)
            ->whereHas('variants', function($q) {
                $q->where('stock', 0);
            })
            ->count();

        // Recent orders (last 10)
        $recentOrders = Order::where('vendor_id', $vendorId)
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Top products
        $topProducts = Product::where('vendor_id', $vendorId)
            ->with(['translation', 'primaryVariant'])
            ->latest()
            ->take(5)
            ->get();

        // Low stock alerts (products with variants having low stock)
        $lowStockAlerts = Product::where('vendor_id', $vendorId)
            ->whereHas('variants', function($q) {
                $q->where('stock', '<=', 5);
            })
            ->with(['translation', 'variants' => function($q) {
                $q->where('stock', '<=', 5)->orderBy('stock', 'asc');
            }])
            ->take(5)
            ->get();

        // Sales chart data (last 7 days)
        $salesChartData = $this->getSalesChartData($vendorId, 7);

        // Orders chart data (last 7 days)  
        $ordersChartData = $this->getOrdersChartData($vendorId, 7);

        // Monthly comparison
        $lastMonthSales = Order::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total_amount');

        $salesGrowth = $lastMonthSales > 0 
            ? round((($monthSales - $lastMonthSales) / $lastMonthSales) * 100, 1) 
            : 100;

        return view('vendor.dashboard.index', compact(
            'vendor',
            'shop',
            'totalSales',
            'todaySales',
            'monthSales',
            'totalOrders',
            'pendingOrders',
            'processingOrders',
            'completedOrders',
            'totalProducts',
            'activeProducts',
            'lowStockProducts',
            'outOfStockProducts',
            'recentOrders',
            'topProducts',
            'lowStockAlerts',
            'salesChartData',
            'ordersChartData',
            'salesGrowth'
        ));
    }

    private function getSalesChartData($vendorId, $days)
    {
        $labels = [];
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d/m');
            
            $daySales = Order::where('vendor_id', $vendorId)
                ->where('status', 'completed')
                ->whereDate('created_at', $date)
                ->sum('total_amount');
            
            $data[] = round($daySales, 2);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function getOrdersChartData($vendorId, $days)
    {
        $labels = [];
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d/m');
            
            $dayOrders = Order::where('vendor_id', $vendorId)
                ->whereDate('created_at', $date)
                ->count();
            
            $data[] = $dayOrders;
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
