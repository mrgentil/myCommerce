<?php

use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PaymentGatewayConfigController;
use App\Http\Controllers\Admin\PaymentGatewayController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductReviewController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RefundController;
use App\Http\Controllers\Admin\SocialMediaLinkController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\DisputeController;
use App\Http\Controllers\Admin\ReturnController as AdminReturnController;
use App\Http\Controllers\Admin\VendorBadgeController;
use App\Http\Controllers\Admin\GiftCardController;
use App\Http\Controllers\Admin\LoyaltyController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\SiteSettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login', function () {
    return view('admin.auth.login');
});

Auth::routes();

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    /* Dashboard */
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    /* Categiries */
    Route::resource('categories', CategoryController::class);
    Route::post('/categories/data', [CategoryController::class, 'getCategories'])->name('categories.data');
    Route::post('/admin/categories/update-status', [CategoryController::class, 'updateCategoryStatus'])->name('categories.updateStatus');

    /* Products */
    Route::resource('products', ProductController::class);
    Route::post('products/data', [ProductController::class, 'getProducts'])->name('products.data');
    Route::post('admin/products/updateStatus', [ProductController::class, 'updateStatus'])->name('products.updateStatus');

    /* Brands */
    Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
    Route::get('admin/brands/getdata', [BrandController::class, 'getData'])->name('brands.getData');
    Route::get('brands/{id}/edit', [BrandController::class, 'edit'])->name('brands.edit');
    Route::put('brands/{brand}', [BrandController::class, 'update'])->name('brands.update');
    Route::get('brands/create', [BrandController::class, 'create'])->name('brands.create');
    Route::post('brands', [BrandController::class, 'store'])->name('brands.store');
    Route::delete('brands/{id}', [BrandController::class, 'destroy'])->name('brands.destroy');
    Route::post('brands/update-status', [BrandController::class, 'updateStatus'])->name('brands.updateStatus');

    /* change Language */
    Route::post('/change-language', [LanguageController::class, 'changeLanguage'])->name('change.language');

    /* Menus */
    Route::resource('menus', MenuController::class);
    Route::post('menus/data', [MenuController::class, 'getData'])->name('menus.data');
    Route::resource('menus.items', MenuItemController::class)->shallow();
    Route::get('menus-items', [MenuItemController::class, 'index'])->name('menus.item.index');
    Route::post('menus-items/getdata', [MenuItemController::class, 'getData'])->name('menus.item.getData');

    /* Banners */
    Route::resource('banners', BannerController::class);
    Route::post('banners/data', [BannerController::class, 'getData'])->name('banners.data');
    Route::put('/banners/toggle-status/{id}', [BannerController::class, 'toggleStatus'])->name('banners.toggleStatus');
    Route::post('/banners/update-status', [BannerController::class, 'updateStatus'])->name('banners.updateStatus');

    /* Hero Slides */
    Route::resource('hero-slides', HeroSlideController::class);
    Route::post('hero-slides/update-order', [HeroSlideController::class, 'updateOrder'])->name('hero-slides.update-order');
    Route::post('hero-slides/{heroSlide}/toggle-status', [HeroSlideController::class, 'toggleStatus'])->name('hero-slides.toggle-status');

    /* Social Media Links */
    Route::resource('social-media-links', SocialMediaLinkController::class);
    Route::post('social-media-links/data', [SocialMediaLinkController::class, 'getData'])->name('social-media-links.data');

    /* Orders */
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::delete('orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::post('orders/data', [OrderController::class, 'getData'])->name('orders.data');

    /* Product Variants */
    Route::resource('product_variants', ProductVariantController::class);
    Route::post('/product_variants/data', [ProductVariantController::class, 'getData'])->name('product_variants.data');

    /* Customers */
    Route::resource('customers', CustomerController::class);
    Route::get('admin/customers/data', [CustomerController::class, 'getCustomerData'])->name('customers.data');

    /* Reviews */
    Route::get('/reviews/data', [ProductReviewController::class, 'getData'])->name('reviews.data');
    Route::resource('reviews', ProductReviewController::class)->except(['create', 'store']);

    /* Attributes */
    Route::resource('attributes', AttributeController::class);

    /* Attribute Value Management */
    Route::post('attributes/{attribute}/values', [AttributeController::class, 'storeValue'])->name('attributes.values.store');
    Route::delete('values/{value}', [AttributeController::class, 'destroyValue'])->name('values.destroy');
    Route::post('attributes/data', [AttributeController::class, 'getAttributesData'])->name('attributes.data');

    /* Attribute Value Translations Management */
    Route::post('values/{value}/translations', [AttributeController::class, 'storeTranslation'])->name('values.translations.store');
    Route::delete('translations/{translation}', [AttributeController::class, 'destroyTranslation'])->name('translations.destroy');

    /* Vendors */
    Route::get('vendors', [VendorController::class, 'index'])->name('vendors.index');
    Route::get('vendors/data', [VendorController::class, 'getVendorData'])->name('vendors.data');
    Route::get('vendors/{id}', [VendorController::class, 'show'])->name('vendors.show');
    Route::delete('vendors/{id}', [VendorController::class, 'destroy'])->name('vendors.destroy');
    Route::get('vendors/create', [VendorController::class, 'create'])->name('vendors.create');
    Route::post('vendors', [VendorController::class, 'store'])->name('vendors.store');
    Route::post('vendors/{id}/approve', [VendorController::class, 'approve'])->name('vendors.approve');
    Route::post('vendors/{id}/reject', [VendorController::class, 'reject'])->name('vendors.reject');
    Route::post('vendors/{id}/suspend', [VendorController::class, 'suspend'])->name('vendors.suspend');
    Route::post('vendors/{id}/ban', [VendorController::class, 'ban'])->name('vendors.ban');
    Route::patch('vendors/{id}/commission', [VendorController::class, 'updateCommission'])->name('vendors.commission');

    /* Shops */
    Route::get('shops', [ShopController::class, 'index'])->name('shops.index');
    Route::get('shops/data', [ShopController::class, 'getData'])->name('shops.data');
    Route::get('shops/{id}', [ShopController::class, 'show'])->name('shops.show');
    Route::post('shops/{id}/approve', [ShopController::class, 'approve'])->name('shops.approve');
    Route::post('shops/{id}/reject', [ShopController::class, 'reject'])->name('shops.reject');
    Route::post('shops/{id}/suspend', [ShopController::class, 'suspend'])->name('shops.suspend');

    /* Pages */
    Route::resource('pages', PageController::class);
    Route::post('pages/update-status', [PageController::class, 'updatePageStatus'])->name('pages.updateStatus');
    Route::post('pages/data', [PageController::class, 'data'])->name('pages.data');

    /* payments */
    Route::get('payments/get-data', [PaymentController::class, 'getData'])->name('payments.getData');
    Route::resource('payments', PaymentController::class)->only(['index', 'destroy', 'show']);

    /* Refunds */
    Route::get('refunds', [RefundController::class, 'index'])->name('refunds.index');
    Route::get('refunds/data', [RefundController::class, 'getData'])->name('refunds.getData');
    Route::delete('refunds/{refund}', [RefundController::class, 'destroy'])->name('refunds.destroy');
    Route::get('refunds/{refund}', [RefundController::class, 'show'])->name('refunds.show');

    /* Payment Gateways */
    Route::get('payment-gateways', [PaymentGatewayController::class, 'index'])->name('payment-gateways.index');
    Route::get('payment-gateways/data', [PaymentGatewayController::class, 'getData'])->name('payment-gateways.getData');
    Route::get('payment-gateways/{paymentGateway}/edit', [PaymentGatewayController::class, 'edit'])->name('payment-gateways.edit');
    Route::put('payment-gateways/{paymentGateway}', [PaymentGatewayController::class, 'update'])->name('payment-gateways.update');
    Route::delete('payment-gateways/{paymentGateway}', [PaymentGatewayController::class, 'destroy'])->name('payment-gateways.destroy');

    /* Payment Gateways Configs */
    Route::get('payment_gateway_configs/getData', [PaymentGatewayConfigController::class, 'getData'])->name('payment_gateway_configs.getData');
    Route::resource('payment_gateway_configs', PaymentGatewayConfigController::class)->except(['show']);

    /* Profile */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    /* Site Settings */
    Route::get('site-settings', [SiteSettingsController::class, 'index'])->name('site-settings.index');
    Route::get('site-settings/edit', [SiteSettingsController::class, 'edit'])->name('site-settings.edit');
    Route::post('site-settings/update', [SiteSettingsController::class, 'update'])->name('site-settings.update');
    Route::post('site-settings/remove-logo', [SiteSettingsController::class, 'removeLogo'])->name('site-settings.remove-logo');

    /* ============ MARKETPLACE FEATURES ============ */

    /* Flash Sales */
    Route::get('flash-sales', [FlashSaleController::class, 'index'])->name('flash-sales.index');
    Route::get('flash-sales/create', [FlashSaleController::class, 'create'])->name('flash-sales.create');
    Route::post('flash-sales', [FlashSaleController::class, 'store'])->name('flash-sales.store');
    Route::get('flash-sales/{id}/edit', [FlashSaleController::class, 'edit'])->name('flash-sales.edit');
    Route::put('flash-sales/{id}', [FlashSaleController::class, 'update'])->name('flash-sales.update');
    Route::delete('flash-sales/{id}', [FlashSaleController::class, 'destroy'])->name('flash-sales.destroy');
    Route::post('flash-sales/{id}/products', [FlashSaleController::class, 'addProduct'])->name('flash-sales.add-product');
    Route::delete('flash-sales/{id}/products/{productId}', [FlashSaleController::class, 'removeProduct'])->name('flash-sales.remove-product');

    /* Disputes */
    Route::get('disputes', [DisputeController::class, 'index'])->name('disputes.index');
    Route::get('disputes/{id}', [DisputeController::class, 'show'])->name('disputes.show');
    Route::post('disputes/{id}/message', [DisputeController::class, 'addMessage'])->name('disputes.message');
    Route::post('disputes/{id}/status', [DisputeController::class, 'updateStatus'])->name('disputes.status');
    Route::post('disputes/{id}/resolve', [DisputeController::class, 'resolve'])->name('disputes.resolve');

    /* Returns Management */
    Route::get('returns', [AdminReturnController::class, 'index'])->name('returns.index');
    Route::get('returns/{id}', [AdminReturnController::class, 'show'])->name('returns.show');
    Route::post('returns/{id}/status', [AdminReturnController::class, 'updateStatus'])->name('returns.status');

    /* Vendor Badges */
    Route::get('badges', [VendorBadgeController::class, 'index'])->name('badges.index');
    Route::get('badges/create', [VendorBadgeController::class, 'create'])->name('badges.create');
    Route::post('badges', [VendorBadgeController::class, 'store'])->name('badges.store');
    Route::get('badges/{id}/edit', [VendorBadgeController::class, 'edit'])->name('badges.edit');
    Route::put('badges/{id}', [VendorBadgeController::class, 'update'])->name('badges.update');
    Route::delete('badges/{id}', [VendorBadgeController::class, 'destroy'])->name('badges.destroy');
    Route::get('badges/assignments', [VendorBadgeController::class, 'assignments'])->name('badges.assignments');
    Route::post('badges/assign', [VendorBadgeController::class, 'assignBadge'])->name('badges.assign');
    Route::post('badges/remove', [VendorBadgeController::class, 'removeBadge'])->name('badges.remove');
    Route::post('vendors/{id}/verify', [VendorBadgeController::class, 'verifyVendor'])->name('vendors.verify');

    /* Gift Cards */
    Route::get('gift-cards', [GiftCardController::class, 'index'])->name('gift-cards.index');
    Route::get('gift-cards/create', [GiftCardController::class, 'create'])->name('gift-cards.create');
    Route::post('gift-cards', [GiftCardController::class, 'store'])->name('gift-cards.store');
    Route::get('gift-cards/{id}', [GiftCardController::class, 'show'])->name('gift-cards.show');
    Route::post('gift-cards/{id}/toggle', [GiftCardController::class, 'toggleStatus'])->name('gift-cards.toggle');
    Route::post('gift-cards/{id}/adjust', [GiftCardController::class, 'adjustBalance'])->name('gift-cards.adjust');

    /* Loyalty Program */
    Route::get('loyalty', [LoyaltyController::class, 'index'])->name('loyalty.index');
    Route::get('loyalty/rewards/create', [LoyaltyController::class, 'createReward'])->name('loyalty.rewards.create');
    Route::post('loyalty/rewards', [LoyaltyController::class, 'storeReward'])->name('loyalty.rewards.store');
    Route::get('loyalty/rewards/{id}/edit', [LoyaltyController::class, 'editReward'])->name('loyalty.rewards.edit');
    Route::put('loyalty/rewards/{id}', [LoyaltyController::class, 'updateReward'])->name('loyalty.rewards.update');
    Route::delete('loyalty/rewards/{id}', [LoyaltyController::class, 'deleteReward'])->name('loyalty.rewards.delete');
    Route::post('loyalty/add-points', [LoyaltyController::class, 'addPoints'])->name('loyalty.add-points');
    Route::get('loyalty/customers/{id}', [LoyaltyController::class, 'customerHistory'])->name('loyalty.customer-history');

    /* Notifications */
    Route::get('notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/create', [AdminNotificationController::class, 'create'])->name('notifications.create');
    Route::post('notifications/send', [AdminNotificationController::class, 'send'])->name('notifications.send');

    /* Reviews Moderation */
    Route::get('reviews-moderation', [AdminReviewController::class, 'index'])->name('reviews-moderation.index');
    Route::get('reviews-moderation/{id}', [AdminReviewController::class, 'show'])->name('reviews-moderation.show');
    Route::post('reviews-moderation/{id}/approve', [AdminReviewController::class, 'approve'])->name('reviews-moderation.approve');
    Route::post('reviews-moderation/{id}/reject', [AdminReviewController::class, 'reject'])->name('reviews-moderation.reject');
    Route::delete('reviews-moderation/{id}', [AdminReviewController::class, 'destroy'])->name('reviews-moderation.destroy');
    Route::post('reviews-moderation/bulk', [AdminReviewController::class, 'bulkAction'])->name('reviews-moderation.bulk');
});

// Store routes MUST be loaded LAST (contains catch-all route for dynamic pages)
require base_path('routes/store.php');
