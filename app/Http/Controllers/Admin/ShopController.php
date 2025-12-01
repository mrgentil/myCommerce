<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ShopController extends Controller
{
    /**
     * Display a listing of shops.
     */
    public function index()
    {
        $totalShops = Shop::count();
        $pendingShops = Shop::where('status', 'pending')->count();
        $approvedShops = Shop::where('status', 'approved')->count();
        
        return view('admin.shops.index', compact('totalShops', 'pendingShops', 'approvedShops'));
    }

    /**
     * Get shops data for DataTables.
     */
    public function getData(Request $request)
    {
        $shops = Shop::with('vendor')->select(['id', 'vendor_id', 'name', 'slug', 'status', 'created_at']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $shops->where('status', $request->status);
        }

        return DataTables::of($shops)
            ->addColumn('vendor_name', function ($shop) {
                return $shop->vendor ? $shop->vendor->name : '-';
            })
            ->addColumn('vendor_email', function ($shop) {
                return $shop->vendor ? $shop->vendor->email : '-';
            })
            ->addColumn('status_badge', function ($shop) {
                $badges = [
                    'pending' => '<span class="badge bg-warning">En attente</span>',
                    'approved' => '<span class="badge bg-success">Approuvée</span>',
                    'active' => '<span class="badge bg-success">Active</span>',
                    'rejected' => '<span class="badge bg-danger">Rejetée</span>',
                    'inactive' => '<span class="badge bg-secondary">Inactive</span>',
                ];
                return $badges[$shop->status] ?? '<span class="badge bg-secondary">'.$shop->status.'</span>';
            })
            ->addColumn('action', function ($shop) {
                $actions = '<div class="btn-group btn-group-sm">';
                
                // View button
                $actions .= '<a href="'.route('admin.shops.show', $shop->id).'" class="btn btn-info btn-sm" title="Voir"><i class="bi bi-eye"></i></a>';
                
                // View shop page
                $actions .= '<a href="'.route('shop.view', $shop->slug).'" target="_blank" class="btn btn-primary btn-sm" title="Voir la boutique"><i class="bi bi-shop"></i></a>';
                
                // Approve/Reject buttons for pending shops
                if ($shop->status === 'pending') {
                    $actions .= '<button class="btn btn-success btn-sm" onclick="approveShop('.$shop->id.')" title="Approuver"><i class="bi bi-check"></i></button>';
                    $actions .= '<button class="btn btn-danger btn-sm" onclick="rejectShop('.$shop->id.')" title="Rejeter"><i class="bi bi-x"></i></button>';
                }
                
                // Suspend button for active/approved shops
                if ($shop->status === 'approved' || $shop->status === 'active') {
                    $actions .= '<button class="btn btn-warning btn-sm" onclick="suspendShop('.$shop->id.')" title="Suspendre"><i class="bi bi-pause-circle"></i></button>';
                }
                
                // Reactivate button for inactive/rejected shops
                if ($shop->status === 'inactive' || $shop->status === 'rejected') {
                    $actions .= '<button class="btn btn-success btn-sm" onclick="approveShop('.$shop->id.')" title="Réactiver"><i class="bi bi-check"></i></button>';
                }
                
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    /**
     * Display the specified shop.
     */
    public function show($id)
    {
        $shop = Shop::with(['vendor', 'products'])->findOrFail($id);
        $productsCount = $shop->products()->count();
        $activeProductsCount = $shop->products()->where('status', 'active')->count();
        
        return view('admin.shops.show', compact('shop', 'productsCount', 'activeProductsCount'));
    }

    /**
     * Approve a shop.
     */
    public function approve($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'message' => 'Boutique approuvée avec succès!',
        ]);
    }

    /**
     * Reject a shop.
     */
    public function reject($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'message' => 'Boutique rejetée.',
        ]);
    }

    /**
     * Suspend a shop.
     */
    public function suspend($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->update(['status' => 'inactive']);

        return response()->json([
            'success' => true,
            'message' => 'Boutique suspendue avec succès.',
        ]);
    }
}
