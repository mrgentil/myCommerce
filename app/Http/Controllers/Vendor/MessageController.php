<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * List all conversations for the vendor
     */
    public function index()
    {
        $vendorId = Auth::guard('vendor')->id();

        $conversations = Conversation::where('vendor_id', $vendorId)
            ->with(['customer', 'product.translation', 'order', 'lastMessage'])
            ->withCount(['messages as unread_count' => function($q) {
                $q->where('sender_type', 'customer')->where('is_read', false);
            }])
            ->orderByDesc('updated_at')
            ->paginate(20);

        $totalUnread = Conversation::where('vendor_id', $vendorId)
            ->withCount(['messages as unread' => function($q) {
                $q->where('sender_type', 'customer')->where('is_read', false);
            }])
            ->get()
            ->sum('unread');

        return view('vendor.messages.index', compact('conversations', 'totalUnread'));
    }

    /**
     * Show a conversation
     */
    public function show($id)
    {
        $vendorId = Auth::guard('vendor')->id();

        $conversation = Conversation::where('vendor_id', $vendorId)
            ->with(['customer', 'product.translation', 'order', 'messages'])
            ->findOrFail($id);

        // Mark messages as read
        $conversation->markAsReadForVendor();

        // Get other conversations for sidebar
        $conversations = Conversation::where('vendor_id', $vendorId)
            ->with(['customer', 'lastMessage'])
            ->withCount(['messages as unread_count' => function($q) {
                $q->where('sender_type', 'customer')->where('is_read', false);
            }])
            ->orderByDesc('updated_at')
            ->take(20)
            ->get();

        return view('vendor.messages.show', compact('conversation', 'conversations'));
    }

    /**
     * Reply to a conversation
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
            'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        $vendorId = Auth::guard('vendor')->id();

        $conversation = Conversation::where('vendor_id', $vendorId)->findOrFail($id);

        // Handle attachment
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('messages', 'public');
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'vendor',
            'sender_id' => $vendorId,
            'content' => $request->content,
            'attachment' => $attachmentPath,
        ]);

        $conversation->touch();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'content' => $message->content,
                    'attachment' => $attachmentPath ? asset('storage/' . $attachmentPath) : null,
                    'created_at' => $message->created_at->format('d/m/Y H:i'),
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Message envoyé !');
    }

    /**
     * Get messages via AJAX
     */
    public function getMessages($id)
    {
        $vendorId = Auth::guard('vendor')->id();

        $conversation = Conversation::where('vendor_id', $vendorId)->findOrFail($id);
        $messages = $conversation->messages()->get();

        $conversation->markAsReadForVendor();

        return response()->json([
            'messages' => $messages->map(function($msg) use ($vendorId) {
                return [
                    'id' => $msg->id,
                    'content' => $msg->content,
                    'attachment' => $msg->attachment ? asset('storage/' . $msg->attachment) : null,
                    'sender_type' => $msg->sender_type,
                    'sender_name' => $msg->sender_name,
                    'is_mine' => $msg->sender_type === 'vendor' && $msg->sender_id === $vendorId,
                    'created_at' => $msg->created_at->format('d/m/Y H:i'),
                    'time_ago' => $msg->created_at->diffForHumans(),
                ];
            })
        ]);
    }

    /**
     * Close a conversation
     */
    public function close($id)
    {
        $vendorId = Auth::guard('vendor')->id();
        $conversation = Conversation::where('vendor_id', $vendorId)->findOrFail($id);
        $conversation->update(['status' => 'closed']);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread count
     */
    public function unreadCount()
    {
        $vendorId = Auth::guard('vendor')->id();

        $count = Message::whereHas('conversation', function($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId);
        })
        ->where('sender_type', 'customer')
        ->where('is_read', false)
        ->count();

        return response()->json(['count' => $count]);
    }
}
