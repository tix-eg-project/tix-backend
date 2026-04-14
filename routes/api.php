<?php

use App\Http\Controllers\Api\Admin\OrderAdminController;
use App\Http\Controllers\Api\Auth\User\ProfileController;
use App\Http\Controllers\Api\Auth\User\UserAuthController;
use App\Http\Controllers\Api\Auth\Vendor\RegisterVendorController;
use App\Http\Controllers\Api\Auth\Vendor\UserVendorProfileController;
use App\Http\Controllers\Api\Country\Cities\CityController;
use App\Http\Controllers\Api\Country\CountryController;
use App\Http\Controllers\Api\User\AboutUs\UserAboutUsController;
use App\Http\Controllers\Api\User\Advertisement\AdvertisementController;
use App\Http\Controllers\Api\User\Banner\BannerController;
use App\Http\Controllers\Api\User\Brand\UserBrandController;
use App\Http\Controllers\Api\User\Cart\CartController;
use App\Http\Controllers\Api\User\Cart\CartSummaryController;
use App\Http\Controllers\Api\User\Cart\PaymentMethodController;
use App\Http\Controllers\Api\User\Cart\UserContactController;
use App\Http\Controllers\Api\User\Category\CategoryController;
use App\Http\Controllers\Api\User\Checkout\CheckoutController;
use App\Http\Controllers\Api\User\Comments\CommentController;
use App\Http\Controllers\Api\User\ContactUs\ContactUsController;
use App\Http\Controllers\Api\User\Favorites\FavoriteController;
use App\Http\Controllers\Api\User\Offer\UserOfferController;
use App\Http\Controllers\Api\User\Order\OrderController;
use App\Http\Controllers\Api\User\Payment\XPayCallbackController;
use App\Http\Controllers\Api\User\Payment\XPayRedirectController;
use App\Http\Controllers\Api\User\Payment\XPayReturnApiController;
use App\Http\Controllers\Api\User\Payment\XPayStatusController;
use App\Http\Controllers\Api\User\Privacy\PrivacyPolicyController;
use App\Http\Controllers\Api\User\Privacy\ReturnPolicyController;
use App\Http\Controllers\Api\User\Privacy\TermsConditionsController;
use App\Http\Controllers\Api\User\Product\AttributeDefinitionController;
use App\Http\Controllers\Api\User\Product\ProductDetailsController;
use App\Http\Controllers\Api\User\Product\ProductFilterController;
use App\Http\Controllers\Api\User\Product\ProductReviewController;
use App\Http\Controllers\Api\User\Product\ProductListController;
use App\Http\Controllers\Api\User\Product\PublicProductController;
use App\Http\Controllers\Api\User\Product\SearchController;
use App\Http\Controllers\Api\User\Shopping\UserShippingZoneController;
use App\Http\Controllers\Api\User\Social\UserSocialLinkController;
use App\Http\Controllers\Api\User\StayInTouch\StayInTouchUserController;
use App\Http\Controllers\Api\User\Subcategory\SubcategoryController;
use App\Http\Controllers\Api\User\Returns\ReturnRequestController;
use App\Http\Controllers\Api\User\Returns\UserReturnRequestController;
use App\Http\Controllers\Api\OpayController;
use App\Http\Controllers\Api\PaymentCallbackController;
use App\Http\Controllers\Api\Shipping\VSoftController;
use App\Models\Advertisement;
use App\Models\StayInTouch;
use Illuminate\Support\Facades\Route;


























Route::get('test', function () {
    return 'reer';
});

Route::prefix('auth/user')->group(function () {
    Route::post('register',            [UserAuthController::class, 'register']);
    Route::post('verify',              [UserAuthController::class, 'verify']);
    Route::post('login',               [UserAuthController::class, 'login']);
    Route::post('send-reset-code',     [UserAuthController::class, 'sendResetCode']);
    Route::post('forget-password',     [UserAuthController::class, 'forgetPassword']);
    Route::post('resend-reset-code',   [UserAuthController::class, 'resendResetCode']);
    Route::post('verify-reset-code',   [UserAuthController::class, 'verifyResetCode']);
    Route::post('reset-password',      [UserAuthController::class, 'resetPassword']);
});

Route::middleware('locale')  // الآن بدون auth:sanctum
    ->group(function () {
        Route::get('banners',                       [BannerController::class, 'index']);
        Route::get('advertisements',                       [AdvertisementController::class, 'index']);
        Route::get('categories',                       [CategoryController::class, 'index']);
        Route::get('subcategories',                       [SubcategoryController::class, 'index']);
        Route::get('category/{category}/subcategories',                       [SubcategoryController::class, 'getsubcategorybycategory']);
        Route::get('brands',                       [UserBrandController::class, 'index']);
        Route::get('products',                       [ProductListController::class, 'index']);
        Route::get('/products/{id}', [ProductDetailsController::class, 'show']);
        Route::get('/products/{id}/reviews', [ProductReviewController::class, 'index']);
        Route::get('category/{category}/products',                       [ProductListController::class, 'productsByCategory']);
        Route::get('subcategory/{subcategory}/products',                       [ProductListController::class, 'productsBySubcategory']);
        Route::get('/product/discounted', [PublicProductController::class, 'discountedProducts']);
        Route::get('/offers/{offer}/products', [PublicProductController::class, 'productsByOffer']);
        Route::get('offers',     [UserOfferController::class, 'index']);
        Route::get('product/filter', [ProductFilterController::class, 'filter']);
        Route::get('search', [SearchController::class, 'index']);

        Route::get('/vendor/{id}/profile', [UserVendorProfileController::class, 'show']);

        Route::get('terms-policy', [TermsConditionsController::class, 'show']);
        Route::get('return-policy', [ReturnPolicyController::class, 'show']);
        Route::get('privacy-policy', [PrivacyPolicyController::class, 'show']);


        //Route::get('attribute-definitions', [AttributeDefinitionController::class, 'index']);

        Route::get('countries',                       [CountryController::class, 'index']);
        Route::get('cities/{country_id}', [CityController::class, 'index']);

        Route::post('vendor/register', [RegisterVendorController::class, 'register']);


        //Route::post('vendor/register', [RegisterVendorController::class, 'register']);


        Route::get('about-us', [UserAboutUsController::class, 'index']);
        Route::get('stay-in-touch', [StayInTouchUserController::class, 'index']);
        Route::get('social-links', [UserSocialLinkController::class, 'index']);
        Route::post('contact-us', [ContactUsController::class, 'store']);

        Route::get('xpay/redirect', [XPayRedirectController::class, 'redirect'])->name('xpay.redirect');
        Route::match(['post', 'get'], 'xpay/callback', [XPayCallbackController::class, 'callback'])->name('xpay.callback');
        Route::get('payments/xpay/status', [XPayStatusController::class, 'status'])->name('xpay.status');
        Route::get('payment/xpay/return', [XPayReturnApiController::class, 'return'])->name('xpay.return');
    });

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user',                       [UserAuthController::class, 'user']);
    Route::post('logout',                    [UserAuthController::class, 'logout']);
    Route::get('/profile',  [ProfileController::class, 'show']);
    Route::post('/profile',  [ProfileController::class, 'update']);


    Route::post('/products/{id}/reviews', [ProductReviewController::class, 'store']);

    Route::post('favorites/toggle', [FavoriteController::class, 'toggle']);
    Route::get('favorites', [FavoriteController::class, 'index']);
    Route::delete('favorites/{productId}', [FavoriteController::class, 'destroy']);
    Route::delete('favorites',             [FavoriteController::class, 'clear']);


    Route::post('cart/add',        [CartController::class, 'store'])->name('store');
    Route::get('cart',         [CartController::class, 'index'])->name('index');
    Route::put('cart/{id}',     [CartController::class, 'update'])->name('update');
    Route::delete('cart/{id}',  [CartController::class, 'destroy'])->name('destroy');
    Route::delete('cart',      [CartController::class, 'clear'])->name('clear');

    Route::get('shipping/zones', [UserShippingZoneController::class, 'index']);

    Route::match(['get', 'post'], '/summary', [CartSummaryController::class, 'summary'])->name('summary');
    Route::delete('/coupon', [CartSummaryController::class, 'removeCoupon'])->name('coupon.remove');
    Route::post('/checkout', [CheckoutController::class, 'processCheckout']);

    Route::get('/contact',  [UserContactController::class, 'show']);
    Route::post('/contact', [UserContactController::class, 'store']);
    Route::delete('/contact', [UserContactController::class, 'destroy']);

    Route::get('payment-methods', [PaymentMethodController::class, 'index']);

    //Route::post('process', [CheckoutController::class, 'processCheckout']);

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/',        [OrderController::class, 'index']);
        Route::get('/{id}',    [OrderController::class, 'show']);
        Route::get('/{id}/comments', [CommentController::class, 'show']);
        Route::delete('/{id}', [OrderController::class, 'destroy']);
        Route::delete('/',     [OrderController::class, 'clear']);
    });

    Route::post('/orders/{order}/returns', [ReturnRequestController::class, 'store']);
    Route::get('/returns', [UserReturnRequestController::class, 'index']);
    
    Route::prefix('admin/orders')->name('admin.orders.')->group(function () {
        Route::post('/{id}/status',        [OrderAdminController::class, 'updateStatus'])->name('status');
        Route::post('/{id}/delivered-at',  [OrderAdminController::class, 'setDeliveredAt'])->name('delivered_at');
    });

    Route::post('/comments', [CommentController::class, 'store']);
    
     Route::post('/opay/webhook', [OpayController::class, 'webhook'])
        ->name('opay.webhook');

    Route::match(['get', 'post'], '/payment/callback', [PaymentCallbackController::class, 'handleCallback'])
        ->name('payment.callback');

    Route::prefix('shipping')->group(function () {
        Route::get('/cities', [VSoftController::class, 'cities']);
        Route::get('/quote',  [VSoftController::class, 'quote']);
    });
});
