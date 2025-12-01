<?php

use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Vendor\Auth\AuthController;
use App\Http\Controllers\Vendor\DashboardController;
use App\Http\Controllers\Vendor\OrderController;
use App\Http\Controllers\Vendor\ProductController;
use App\Http\Controllers\Vendor\ProductReviewController;
use App\Http\Controllers\Vendor\ProfileController;
use App\Http\Controllers\Vendor\ShopController;
use App\Http\Controllers\Vendor\FinanceController;
use App\Http\Controllers\Vendor\StatisticsController;
use App\Http\Controllers\Vendor\CouponController;
use App\Http\Controllers\Vendor\MessageController;
use App\Http\Controllers\Vendor\ReturnController;
use App\Http\Controllers\Vendor\NotificationController;
use App\Http\Controllers\Vendor\QuestionController;
use Illuminate\Support\Facades\Route;

Route::prefix('vendor')->group(function () {
    // Guest routes
    Route::middleware('guest:vendor')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('vendor.login');
        Route::post('/login', [AuthController::class, 'login'])->name('vendor.login.submit');
        Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('vendor.register');
        Route::post('/register', [AuthController::class, 'register'])->name('vendor.register.submit');
    });
    
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth.vendor')->name('vendor.logout');

    Route::middleware('auth.vendor')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('vendor.dashboard');
        Route::resource('products', ProductController::class)->names('vendor.products');
        Route::post('products/data', [ProductController::class, 'getProducts'])->name('vendor.products.data');
        Route::post('products/updateStatus', [ProductController::class, 'updateStatus'])->name('vendor.products.updateStatus');

        Route::get('reviews', [ProductReviewController::class, 'index'])->name('vendor.reviews.index');
        Route::get('reviews/data', [ProductReviewController::class, 'getData'])->name('vendor.reviews.data');
        Route::get('reviews/{review}', [ProductReviewController::class, 'show'])->name('vendor.reviews.show');
        Route::delete('reviews/{review}', [ProductReviewController::class, 'destroy'])->name('vendor.reviews.destroy');

        /** Orders */
        Route::get('orders', [OrderController::class, 'index'])->name('vendor.orders.index');
        Route::post('orders/data', [OrderController::class, 'getData'])->name('vendor.orders.data');
        Route::get('orders/{id}', [OrderController::class, 'show'])->name('vendor.orders.show');
        Route::post('orders/{id}/status', [OrderController::class, 'updateStatus'])->name('vendor.orders.updateStatus');
        Route::delete('orders/{id}', [OrderController::class, 'destroy'])->name('vendor.orders.destroy');

        /** Language Switch */
        Route::post('/change-language', [LanguageController::class, 'changeLanguage'])->name('vendor.change.language');

        /** Profile */
        Route::get('profile/edit', [ProfileController::class, 'edit'])->name('vendor.profile.edit');
        Route::patch('profile', [ProfileController::class, 'update'])->name('vendor.profile.update');

        /** Shop Configuration */
        Route::get('shop', [ShopController::class, 'edit'])->name('vendor.shop.edit');
        Route::put('shop', [ShopController::class, 'update'])->name('vendor.shop.update');
        Route::post('shop/remove-logo', [ShopController::class, 'removeLogo'])->name('vendor.shop.remove-logo');
        Route::post('shop/remove-banner', [ShopController::class, 'removeBanner'])->name('vendor.shop.remove-banner');

        /** Finance */
        Route::get('finance', [FinanceController::class, 'index'])->name('vendor.finance.index');
        Route::post('finance/payout', [FinanceController::class, 'requestPayout'])->name('vendor.finance.payout');

        /** Statistics */
        Route::get('statistics', [StatisticsController::class, 'index'])->name('vendor.statistics.index');

        /** Coupons */
        Route::get('coupons', [CouponController::class, 'index'])->name('vendor.coupons.index');
        Route::get('coupons/create', [CouponController::class, 'create'])->name('vendor.coupons.create');
        Route::post('coupons', [CouponController::class, 'store'])->name('vendor.coupons.store');
        Route::get('coupons/{id}/edit', [CouponController::class, 'edit'])->name('vendor.coupons.edit');
        Route::put('coupons/{id}', [CouponController::class, 'update'])->name('vendor.coupons.update');
        Route::delete('coupons/{id}', [CouponController::class, 'destroy'])->name('vendor.coupons.destroy');
        Route::post('coupons/{id}/toggle', [CouponController::class, 'toggle'])->name('vendor.coupons.toggle');

        /** Messages */
        Route::get('messages', [MessageController::class, 'index'])->name('vendor.messages.index');
        Route::get('messages/{id}', [MessageController::class, 'show'])->name('vendor.messages.show');
        Route::post('messages/{id}/reply', [MessageController::class, 'reply'])->name('vendor.messages.reply');
        Route::get('messages/{id}/get', [MessageController::class, 'getMessages'])->name('vendor.messages.get');
        Route::post('messages/{id}/close', [MessageController::class, 'close'])->name('vendor.messages.close');
        Route::get('messages/unread/count', [MessageController::class, 'unreadCount'])->name('vendor.messages.unread');

        /** Returns */
        Route::get('returns', [ReturnController::class, 'index'])->name('vendor.returns.index');
        Route::get('returns/{id}', [ReturnController::class, 'show'])->name('vendor.returns.show');
        Route::post('returns/{id}/approve', [ReturnController::class, 'approve'])->name('vendor.returns.approve');
        Route::post('returns/{id}/reject', [ReturnController::class, 'reject'])->name('vendor.returns.reject');
        Route::post('returns/{id}/received', [ReturnController::class, 'markReceived'])->name('vendor.returns.received');
        Route::post('returns/{id}/refund', [ReturnController::class, 'refund'])->name('vendor.returns.refund');

        /** Notifications */
        Route::get('notifications', [NotificationController::class, 'index'])->name('vendor.notifications.index');
        Route::get('notifications/get', [NotificationController::class, 'getNotifications'])->name('vendor.notifications.get');
        Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('vendor.notifications.mark-read');
        Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('vendor.notifications.mark-all-read');
        Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('vendor.notifications.unread-count');
        Route::get('notifications/preferences', [NotificationController::class, 'preferences'])->name('vendor.notifications.preferences');
        Route::post('notifications/preferences', [NotificationController::class, 'updatePreferences'])->name('vendor.notifications.update-preferences');

        /** Questions */
        Route::get('questions', [QuestionController::class, 'index'])->name('vendor.questions.index');
        Route::post('questions/{id}/answer', [QuestionController::class, 'answer'])->name('vendor.questions.answer');
        Route::delete('questions/{id}', [QuestionController::class, 'destroy'])->name('vendor.questions.destroy');
    });
});
