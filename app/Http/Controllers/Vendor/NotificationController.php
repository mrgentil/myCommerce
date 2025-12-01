<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use App\Models\NotificationPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get vendor notifications
     */
    public function index()
    {
        $vendorId = Auth::guard('vendor')->id();

        $notifications = UserNotification::where('notifiable_type', 'App\Models\Vendor')
            ->where('notifiable_id', $vendorId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('vendor.notifications.index', compact('notifications'));
    }

    /**
     * Get notifications as JSON
     */
    public function getNotifications()
    {
        $vendorId = Auth::guard('vendor')->id();

        $notifications = UserNotification::where('notifiable_type', 'App\Models\Vendor')
            ->where('notifiable_id', $vendorId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'notifications' => $notifications->map(function ($n) {
                return [
                    'id' => $n->id,
                    'type' => $n->type,
                    'title' => $n->title,
                    'message' => $n->message,
                    'icon' => $n->icon,
                    'color' => $n->color,
                    'action_url' => $n->action_url,
                    'read' => $n->isRead(),
                    'time' => $n->created_at->diffForHumans(),
                ];
            }),
            'unread_count' => UserNotification::where('notifiable_type', 'App\Models\Vendor')
                ->where('notifiable_id', $vendorId)
                ->unread()
                ->count(),
        ]);
    }

    /**
     * Mark as read
     */
    public function markAsRead($id)
    {
        $vendorId = Auth::guard('vendor')->id();

        $notification = UserNotification::where('notifiable_type', 'App\Models\Vendor')
            ->where('notifiable_id', $vendorId)
            ->findOrFail($id);

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead()
    {
        $vendorId = Auth::guard('vendor')->id();

        UserNotification::where('notifiable_type', 'App\Models\Vendor')
            ->where('notifiable_id', $vendorId)
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread count
     */
    public function unreadCount()
    {
        $vendorId = Auth::guard('vendor')->id();

        $count = UserNotification::where('notifiable_type', 'App\Models\Vendor')
            ->where('notifiable_id', $vendorId)
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Preferences page
     */
    public function preferences()
    {
        $vendorId = Auth::guard('vendor')->id();
        $preferences = NotificationPreference::getForUser('App\Models\Vendor', $vendorId);

        return view('vendor.notifications.preferences', compact('preferences'));
    }

    /**
     * Update preferences
     */
    public function updatePreferences(Request $request)
    {
        $vendorId = Auth::guard('vendor')->id();
        $preferences = NotificationPreference::getForUser('App\Models\Vendor', $vendorId);

        $preferences->update([
            'email_orders' => $request->boolean('email_orders'),
            'email_messages' => $request->boolean('email_messages'),
            'email_reviews' => $request->boolean('email_reviews'),
            'email_promotions' => $request->boolean('email_promotions'),
            'push_enabled' => $request->boolean('push_enabled'),
        ]);

        return redirect()->back()->with('success', 'Préférences mises à jour');
    }
}
