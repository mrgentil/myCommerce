<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.customer');
    }

    /**
     * List all conversations for the customer
     */
    public function index()
    {
        $customerId = Auth::guard('customer')->id();

        $conversations = Conversation::where('customer_id', $customerId)
            ->with(['vendor.shop', 'product.translation', 'lastMessage'])
            ->withCount(['messages as unread_count' => function($q) {
                $q->where('sender_type', 'vendor')->where('is_read', false);
            }])
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('themes.xylo.customer.messages.index', compact('conversations'));
    }

    /**
     * Show a conversation
     */
    public function show($id)
    {
        $customerId = Auth::guard('customer')->id();

        $conversation = Conversation::where('customer_id', $customerId)
            ->with(['vendor.shop', 'product.translation', 'order', 'messages'])
            ->findOrFail($id);

        // Mark messages as read
        $conversation->markAsReadForCustomer();

        return view('themes.xylo.customer.messages.show', compact('conversation'));
    }

    /**
     * Start a new conversation with a vendor
     */
    public function create(Request $request)
    {
        $vendorId = $request->get('vendor_id');
        $productId = $request->get('product_id');
        $orderId = $request->get('order_id');

        $vendor = Vendor::with('shop')->findOrFail($vendorId);
        $product = $productId ? Product::with('translation')->find($productId) : null;
        $order = $orderId ? Order::find($orderId) : null;

        return view('themes.xylo.customer.messages.create', compact('vendor', 'product', 'order'));
    }

    /**
     * Store a new message
     */
    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'content' => 'required|string|max:2000',
            'product_id' => 'nullable|exists:products,id',
            'order_id' => 'nullable|exists:orders,id',
            'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        $customerId = Auth::guard('customer')->id();

        // Find or create conversation
        $conversation = Conversation::findOrCreateConversation(
            $customerId,
            $request->vendor_id,
            $request->product_id,
            $request->order_id
        );

        // Handle attachment
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('messages', 'public');
        }

        // Create message
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'customer',
            'sender_id' => $customerId,
            'content' => $request->content,
            'attachment' => $attachmentPath,
        ]);

        $conversation->touch();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'conversation_id' => $conversation->id]);
        }

        return redirect()->route('customer.messages.show', $conversation->id)
            ->with('success', 'Message envoyé !');
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

        $customerId = Auth::guard('customer')->id();

        $conversation = Conversation::where('customer_id', $customerId)->findOrFail($id);

        // Handle attachment
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('messages', 'public');
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'customer',
            'sender_id' => $customerId,
            'content' => $request->content,
            'attachment' => $attachmentPath,
        ]);

        $conversation->touch();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Message envoyé !');
    }

    /**
     * Get messages via AJAX (for real-time updates)
     */
    public function getMessages($id)
    {
        $customerId = Auth::guard('customer')->id();

        $conversation = Conversation::where('customer_id', $customerId)->findOrFail($id);
        $messages = $conversation->messages()->with('sender')->get();

        $conversation->markAsReadForCustomer();

        return response()->json([
            'messages' => $messages->map(function($msg) use ($customerId) {
                return [
                    'id' => $msg->id,
                    'content' => $msg->content,
                    'attachment' => $msg->attachment ? asset('storage/' . $msg->attachment) : null,
                    'sender_type' => $msg->sender_type,
                    'sender_name' => $msg->sender_name,
                    'is_mine' => $msg->sender_type === 'customer' && $msg->sender_id === $customerId,
                    'created_at' => $msg->created_at->format('d/m/Y H:i'),
                    'time_ago' => $msg->created_at->diffForHumans(),
                ];
            })
        ]);
    }

    /**
     * Get unread count
     */
    public function unreadCount()
    {
        $customerId = Auth::guard('customer')->id();

        $count = Message::whereHas('conversation', function($q) use ($customerId) {
            $q->where('customer_id', $customerId);
        })
        ->where('sender_type', 'vendor')
        ->where('is_read', false)
        ->count();

        return response()->json(['count' => $count]);
    }
}
