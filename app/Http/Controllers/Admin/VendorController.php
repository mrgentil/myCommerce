<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Shop;
use App\Models\Vendor;
use App\Models\VendorPayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Yajra\DataTables\Facades\DataTables;

class VendorController extends Controller
{
    public function index()
    {
        $pendingCount = Vendor::where('status', 'pending')->count();
        return view('admin.vendors.index', compact('pendingCount'));
    }

    public function getVendorData(Request $request)
    {
        $vendors = Vendor::with('shop')->select(['id', 'name', 'email', 'phone', 'status', 'commission_rate', 'created_at']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $vendors->where('status', $request->status);
        }

        return DataTables::of($vendors)
            ->addColumn('shop_name', function ($vendor) {
                return $vendor->shop ? $vendor->shop->name : '-';
            })
            ->addColumn('status_badge', function ($vendor) {
                $badges = [
                    'pending' => '<span class="badge bg-warning">Pending</span>',
                    'approved' => '<span class="badge bg-success">Approved</span>',
                    'active' => '<span class="badge bg-success">Active</span>',
                    'rejected' => '<span class="badge bg-danger">Rejected</span>',
                    'inactive' => '<span class="badge bg-secondary">Inactive</span>',
                    'banned' => '<span class="badge bg-dark">Banned</span>',
                ];
                return $badges[$vendor->status] ?? '<span class="badge bg-secondary">'.$vendor->status.'</span>';
            })
            ->addColumn('action', function ($vendor) {
                $actions = '<div class="btn-group btn-group-sm">';
                
                // View button
                $actions .= '<a href="'.route('admin.vendors.show', $vendor->id).'" class="btn btn-info btn-sm"><i class="bi bi-eye"></i></a>';
                
                // Approve/Reject buttons for pending vendors
                if ($vendor->status === 'pending') {
                    $actions .= '<button class="btn btn-success btn-sm" onclick="approveVendor('.$vendor->id.')"><i class="bi bi-check"></i></button>';
                    $actions .= '<button class="btn btn-danger btn-sm" onclick="rejectVendor('.$vendor->id.')"><i class="bi bi-x"></i></button>';
                }
                
                // Delete button
                $actions .= '<button class="btn btn-danger btn-sm" onclick="deleteVendor('.$vendor->id.')"><i class="bi bi-trash"></i></button>';
                
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function show($id)
    {
        $vendor = Vendor::with(['shop', 'products'])->findOrFail($id);
        $pendingBalance = VendorPayout::getPendingBalance($vendor->id);
        $commissions = Commission::where('vendor_id', $id)->latest()->take(10)->get();
        
        return view('admin.vendors.show', compact('vendor', 'pendingBalance', 'commissions'));
    }

    public function create()
    {
        return view('admin.vendors.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:vendors,email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->symbols(),
            ],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9\s\-]+$/'],
            'status' => ['required', 'in:active,inactive,banned,pending,approved'],
            'commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $vendor = Vendor::create([
            'name' => trim($validatedData['name']),
            'email' => strtolower(trim($validatedData['email'])),
            'password' => $validatedData['password'],
            'phone' => $validatedData['phone'] ?? null,
            'status' => $validatedData['status'],
            'commission_rate' => $validatedData['commission_rate'] ?? 10.00,
        ]);

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vendor registered successfully!');
    }

    /**
     * Approve a pending vendor.
     */
    public function approve($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->update(['status' => 'approved']);
        
        // Also approve the shop
        if ($vendor->shop) {
            $vendor->shop->update(['status' => 'approved']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Vendor approved successfully!',
        ]);
    }

    /**
     * Reject a pending vendor.
     */
    public function reject(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->update(['status' => 'rejected']);
        
        // Also reject the shop
        if ($vendor->shop) {
            $vendor->shop->update(['status' => 'rejected']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Vendor rejected.',
        ]);
    }

    /**
     * Suspend a vendor.
     */
    public function suspend($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->update(['status' => 'inactive']);
        
        if ($vendor->shop) {
            $vendor->shop->update(['status' => 'inactive']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Vendeur suspendu avec succès.',
        ]);
    }

    /**
     * Ban a vendor.
     */
    public function ban($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->update(['status' => 'banned']);
        
        if ($vendor->shop) {
            $vendor->shop->update(['status' => 'inactive']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Vendeur banni avec succès.',
        ]);
    }

    /**
     * Update vendor commission rate.
     */
    public function updateCommission(Request $request, $id)
    {
        $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        $vendor = Vendor::findOrFail($id);
        $vendor->update(['commission_rate' => $request->commission_rate]);

        return response()->json([
            'success' => true,
            'message' => 'Commission rate updated successfully!',
        ]);
    }

    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->delete();

        return response()->json([
            'success' => true,
            'message' => __('cms.vendors.success_delete'),
        ]);
    }
}
