<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use App\Models\Customer;
use App\Models\Vendor;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $stats = [
            'customers' => Customer::count(),
            'vendors' => Vendor::count(),
            'notifications_today' => UserNotification::whereDate('created_at', today())->count(),
        ];

        $recentNotifications = UserNotification::orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return view('admin.notifications.index', compact('stats', 'recentNotifications'));
    }

    public function create()
    {
        return view('admin.notifications.create');
    }

    public function send(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'target' => 'required|in:all_customers,all_vendors,all,specific_customer,specific_vendor',
            'target_id' => 'nullable|integer',
            'type' => 'required|in:system,promotion,order',
        ]);

        $count = 0;

        if ($request->target === 'all_customers' || $request->target === 'all') {
            $customers = Customer::pluck('id');
            foreach ($customers as $customerId) {
                UserNotification::notifyCustomer(
                    $customerId,
                    $request->type,
                    $request->title,
                    $request->message,
                    $request->action_url
                );
                $count++;
            }
        }

        if ($request->target === 'all_vendors' || $request->target === 'all') {
            $vendors = Vendor::pluck('id');
            foreach ($vendors as $vendorId) {
                UserNotification::notifyVendor(
                    $vendorId,
                    $request->type,
                    $request->title,
                    $request->message,
                    $request->action_url
                );
                $count++;
            }
        }

        if ($request->target === 'specific_customer' && $request->target_id) {
            UserNotification::notifyCustomer(
                $request->target_id,
                $request->type,
                $request->title,
                $request->message,
                $request->action_url
            );
            $count = 1;
        }

        if ($request->target === 'specific_vendor' && $request->target_id) {
            UserNotification::notifyVendor(
                $request->target_id,
                $request->type,
                $request->title,
                $request->message,
                $request->action_url
            );
            $count = 1;
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', "Notification envoyée à {$count} utilisateur(s).");
    }
}
