<?php

use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Store\Auth\ForgotPasswordController;
use App\Http\Controllers\Store\Auth\LoginController;
use App\Http\Controllers\Store\Auth\RegisterController;
use App\Http\Controllers\Store\Auth\ResetPasswordController;
use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Store\CategoryController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\Store\CurrencyController;
use App\Http\Controllers\Store\Customer\ProfileController;
use App\Http\Controllers\Store\PaymentGateway\StripeController;
use App\Http\Controllers\Store\ProductController;
use App\Http\Controllers\Store\ReviewController;
use App\Http\Controllers\Store\SearchController;
use App\Http\Controllers\Store\ShopController;
use App\Http\Controllers\Store\WishlistController;
use App\Http\Controllers\Store\PageController;
use App\Http\Controllers\Store\ShopViewController;
use App\Http\Controllers\Store\MessageController;
use App\Http\Controllers\Store\OrderController;
use App\Http\Controllers\Store\ReturnController;
use App\Http\Controllers\Store\NotificationController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StoreController::class, 'index'])->name('xylo.home');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::post('/change-currency', [CurrencyController::class, 'changeCurrency'])->name('change.currency');

Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
Route::get('/cart', [CartController::class, 'viewCart'])->name('cart.view');
Route::post('/cart/update', [CartController::class, 'updateCart'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'removeFromCart'])->name('cart.remove');

Route::post('/change-store-language', [LanguageController::class, 'changeLanguage'])->name('change.store.language');

Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.applyCoupon');
Route::post('/cart/remove-coupon', [CartController::class, 'removeCoupon'])->name('cart.removeCoupon');

Route::get('/products', [ShopController::class, 'index'])->name('shop.index');

Route::get('/search-suggestions', [SearchController::class, 'suggestions']);
Route::get('/search', [SearchController::class, 'searchResults']);

Route::get('/get-variant-price', [ProductController::class, 'getVariantPrice'])->name('product.variant.price');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

// PayPal callbacks
Route::get('/checkout/paypal/success', [CheckoutController::class, 'paypalSuccess'])->name('paypal.success');
Route::get('/checkout/paypal/cancel', [CheckoutController::class, 'paypalCancel'])->name('paypal.cancel');

// Category page
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');

Route::post('/product/review/store', [ReviewController::class, 'store'])->name('review.store');

Route::prefix('customer')->name('customer.')->group(function () {

    // Guest routes
    Route::middleware('guest:customer')->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login']);

        Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('register', [RegisterController::class, 'register']);

        Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

        Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
    });

    // Authenticated routes
    Route::middleware('auth.customer')->group(function () {
        Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        Route::post('/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');

        //  Customer Profile Routes
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // Customer Messages Routes
        Route::get('/messages', [MessageController::class, 'index'])->name('customer.messages.index');
        Route::get('/messages/create', [MessageController::class, 'create'])->name('customer.messages.create');
        Route::post('/messages', [MessageController::class, 'store'])->name('customer.messages.store');
        Route::get('/messages/{id}', [MessageController::class, 'show'])->name('customer.messages.show');
        Route::post('/messages/{id}/reply', [MessageController::class, 'reply'])->name('customer.messages.reply');
        Route::get('/messages/{id}/get', [MessageController::class, 'getMessages'])->name('customer.messages.get');
        Route::get('/messages/unread/count', [MessageController::class, 'unreadCount'])->name('customer.messages.unread');

        // Customer Orders Routes
        Route::get('/orders', [OrderController::class, 'index'])->name('customer.orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('customer.orders.show');

        // Customer Returns Routes
        Route::get('/returns', [ReturnController::class, 'index'])->name('customer.returns.index');
        Route::get('/returns/create/{order}', [ReturnController::class, 'create'])->name('customer.returns.create');
        Route::post('/returns', [ReturnController::class, 'store'])->name('customer.returns.store');
        Route::get('/returns/{id}', [ReturnController::class, 'show'])->name('customer.returns.show');
        Route::post('/returns/{id}/tracking', [ReturnController::class, 'updateTracking'])->name('customer.returns.update-tracking');
        Route::post('/returns/{id}/cancel', [ReturnController::class, 'cancel'])->name('customer.returns.cancel');

        // Customer Notifications Routes
        Route::get('/notifications', [NotificationController::class, 'index'])->name('customer.notifications.index');
        Route::get('/notifications/get', [NotificationController::class, 'getNotifications'])->name('customer.notifications.get');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('customer.notifications.mark-read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('customer.notifications.mark-all-read');
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('customer.notifications.unread-count');
        Route::get('/notifications/preferences', [NotificationController::class, 'preferences'])->name('customer.notifications.preferences');
        Route::post('/notifications/preferences', [NotificationController::class, 'updatePreferences'])->name('customer.notifications.update-preferences');
    });
});

// Public order tracking
Route::get('/track-order', [OrderController::class, 'track'])->name('track.order');

// Reviews
Route::post('/reviews/{id}/helpful', [ReviewController::class, 'markHelpful'])->name('reviews.helpful');
Route::get('/products/{id}/reviews', [ReviewController::class, 'getProductReviews'])->name('products.reviews');

// Product Q&A
Route::get('/products/{id}/questions', [\App\Http\Controllers\Store\QuestionController::class, 'index'])->name('products.questions');
Route::post('/questions', [\App\Http\Controllers\Store\QuestionController::class, 'store'])->name('questions.store')->middleware('auth.customer');
Route::post('/questions/{id}/answer', [\App\Http\Controllers\Store\QuestionController::class, 'answer'])->name('questions.answer')->middleware('auth.customer');
Route::post('/questions/helpful', [\App\Http\Controllers\Store\QuestionController::class, 'markHelpful'])->name('questions.helpful');

Route::get('/stripe/checkout', [StripeController::class, 'checkout'])->name('stripe.checkout.process');

// Flash Sales & Deals
Route::get('/flash-sale', [\App\Http\Controllers\Store\FlashSaleController::class, 'index'])->name('flash-sale');
Route::get('/flash-sale/{id}/data', [\App\Http\Controllers\Store\FlashSaleController::class, 'getData'])->name('flash-sale.data');
Route::get('/deals', [\App\Http\Controllers\Store\FlashSaleController::class, 'deals'])->name('deals');

// Shop pages
Route::get('/shops', [ShopViewController::class, 'index'])->name('shops.index');
Route::get('/shop/{slug}', [ShopViewController::class, 'show'])->name('shop.view');
Route::post('/shop/{id}/follow', [ShopViewController::class, 'toggleFollow'])->name('shop.follow')->middleware('auth.customer');
Route::get('/my-followed-shops', [ShopViewController::class, 'following'])->name('customer.following')->middleware('auth.customer');

// Dynamic pages (must be last to avoid conflicts)
// Exclude system routes: login, register, admin, vendor, customer, etc.
Route::get('/{slug}', [PageController::class, 'show'])
    ->name('page.show')
    ->where('slug', '^(?!login|register|logout|admin|vendor|customer|password|api|sanctum|site-settings)[a-zA-Z0-9\-_]+$');
