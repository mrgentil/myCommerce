<?php

namespace App\Services;

use App\Models\UserNotification;
use App\Models\NotificationPreference;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Notify about new order
     */
    public static function orderCreated($order)
    {
        // Notify customer
        if ($order->customer_id) {
            UserNotification::notifyCustomer(
                $order->customer_id,
                'order',
                'Commande confirmée',
                "Votre commande #{$order->id} a été reçue et sera traitée sous peu.",
                route('customer.orders.show', $order->id),
                ['order_id' => $order->id]
            );
        }

        // Notify vendor
        if ($order->vendor_id) {
            UserNotification::notifyVendor(
                $order->vendor_id,
                'order',
                'Nouvelle commande',
                "Vous avez reçu une nouvelle commande #{$order->id}.",
                route('vendor.orders.show', $order->id),
                ['order_id' => $order->id]
            );
        }
    }

    /**
     * Notify about order status change
     */
    public static function orderStatusChanged($order, $newStatus)
    {
        $statusMessages = [
            'processing' => 'est en cours de préparation',
            'shipped' => 'a été expédiée',
            'delivered' => 'a été livrée',
            'cancelled' => 'a été annulée',
        ];

        $message = $statusMessages[$newStatus] ?? 'a été mise à jour';

        if ($order->customer_id) {
            UserNotification::notifyCustomer(
                $order->customer_id,
                'order',
                'Mise à jour de commande',
                "Votre commande #{$order->id} {$message}.",
                route('customer.orders.show', $order->id),
                ['order_id' => $order->id, 'status' => $newStatus]
            );
        }
    }

    /**
     * Notify about new message
     */
    public static function newMessage($conversation, $message, $recipientType, $recipientId)
    {
        $type = $recipientType === 'vendor' ? 'Vendor' : 'Customer';
        $senderName = $message->sender_type === 'customer' 
            ? ($message->sender->name ?? 'Client')
            : ($message->sender->shop->name ?? $message->sender->name ?? 'Vendeur');

        if ($recipientType === 'vendor') {
            UserNotification::notifyVendor(
                $recipientId,
                'message',
                'Nouveau message',
                "Vous avez reçu un message de {$senderName}",
                route('vendor.messages.show', $conversation->id)
            );
        } else {
            UserNotification::notifyCustomer(
                $recipientId,
                'message',
                'Nouveau message',
                "Vous avez reçu un message de {$senderName}",
                route('customer.messages.show', $conversation->id)
            );
        }
    }

    /**
     * Notify about new review
     */
    public static function newReview($review)
    {
        $product = $review->product;
        
        if ($product && $product->vendor_id) {
            UserNotification::notifyVendor(
                $product->vendor_id,
                'review',
                'Nouvel avis',
                "Un client a laissé un avis {$review->rating}/5 sur {$product->name}",
                route('vendor.reviews.index'),
                ['review_id' => $review->id, 'rating' => $review->rating]
            );
        }
    }

    /**
     * Notify about return request
     */
    public static function returnRequested($return)
    {
        UserNotification::notifyVendor(
            $return->vendor_id,
            'return',
            'Demande de retour',
            "Un client demande un retour pour la commande #{$return->order_id}",
            route('vendor.returns.show', $return->id),
            ['return_id' => $return->id]
        );
    }

    /**
     * Notify about return status change
     */
    public static function returnStatusChanged($return)
    {
        $statusMessages = [
            'approved' => 'a été approuvée',
            'rejected' => 'a été refusée',
            'received' => 'L\'article a été reçu',
            'refunded' => 'Vous avez été remboursé',
        ];

        $message = $statusMessages[$return->status] ?? 'a été mise à jour';

        UserNotification::notifyCustomer(
            $return->customer_id,
            'return',
            'Mise à jour de votre retour',
            "Votre demande de retour #{$return->id} {$message}.",
            route('customer.returns.show', $return->id),
            ['return_id' => $return->id, 'status' => $return->status]
        );
    }

    /**
     * Send promotional notification
     */
    public static function sendPromotion($userType, $userId, $title, $message, $actionUrl = null)
    {
        if ($userType === 'vendor') {
            UserNotification::notifyVendor($userId, 'promotion', $title, $message, $actionUrl);
        } else {
            UserNotification::notifyCustomer($userId, 'promotion', $title, $message, $actionUrl);
        }
    }
}
