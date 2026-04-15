<?php

use App\Http\Controllers\Api\User\Subcategory\SubcategoryController;
use App\Http\Controllers\Web\Admin\AdminAboutUsController;
use App\Http\Controllers\Web\Admin\AdminContactUsController;
use App\Http\Controllers\Web\Admin\AdminOrderController;
use App\Http\Controllers\Web\Admin\AdminSocialLinkController;
use App\Http\Controllers\Web\Admin\AdminStayInTouchController;
use App\Http\Controllers\Web\Admin\Advertisement\AdminAdvertisementController;
use App\Http\Controllers\Web\Admin\Banner\AdminBannerController;
use App\Http\Controllers\Web\Admin\Brand\AdminBrandController;
use App\Http\Controllers\Web\Admin\Category\AdminCategoryController;
use App\Http\Controllers\Web\Admin\City\CityController;
use App\Http\Controllers\Web\Admin\Country\CountryController;
use App\Http\Controllers\Web\Admin\Coupon\AdminCouponController;
use App\Http\Controllers\Web\Admin\Offer\OfferController;
use App\Http\Controllers\Web\Admin\PermissionController;
use App\Http\Controllers\Web\Admin\PrivacyPolicyController;
use App\Http\Controllers\Web\Admin\Product\AdminAttributeDefinitionController;
use App\Http\Controllers\Web\Admin\Product\AdminProductAttributeController;
use App\Http\Controllers\Web\Admin\Product\ProductController;
use App\Http\Controllers\Web\Admin\Product\ProductItemVariantController;
use App\Http\Controllers\Web\Admin\Product\ProductVariantController;
use App\Http\Controllers\Web\Admin\Product\VariantValueController;
use App\Http\Controllers\Web\Admin\ReturnPolicyController;
use App\Http\Controllers\Web\Admin\RoleController;
use App\Http\Controllers\Web\Admin\ShippingZone\ShippingZoneController;
use App\Http\Controllers\Web\Admin\Subcategory\AdminSubcategoryController;
use App\Http\Controllers\Web\Admin\TermsConditionsController;
use App\Http\Controllers\Web\Admin\User\UserController;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\AdminProfileController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\VendorProfileController;
use App\Http\Controllers\Web\NotificationController;
use App\Http\Controllers\Web\Vendor\Inventory\VendorDamagedStockController;
use App\Http\Controllers\Web\Admin\Inventory\AdminDamagedStockController;
use App\Http\Controllers\Web\Vendor\Returns\VendorReturnRequestController;
use App\Http\Controllers\Web\Admin\Returns\AdminReturnRequestController;
use App\Http\Controllers\Web\Admin\Returns\ReturnRequestController;
use App\Http\Controllers\Web\Admin\AdminVSoftCityController;




//vendor 

use App\Http\Controllers\Web\Vendor\LoginVendorController;
use App\Http\Controllers\Web\Vendor\Offer\VendorOfferController;
use App\Http\Controllers\Web\Vendor\Order\VendorOrderController;
use App\Http\Controllers\Web\Vendor\Product\VendorProductController;
use App\Http\Controllers\Web\Vendor\Product\VendorProductItemVariantController;
use App\Http\Controllers\Web\Vendor\Product\VendorProductVariantController;
use App\Http\Controllers\Web\Vendor\Product\VendorVariantValueController;
use App\Http\Controllers\Web\Vendor\VendorController;
use App\Http\Controllers\Web\Vendor\VendoreController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Http\Controllers\Web\Vendor\VendorNotificationController;



use App\Http\Controllers\Api\User\Payment\XPayCallbackController;
use App\Http\Controllers\Api\User\Payment\PaymentRollbackController;


Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function () {

    Route::get('/', function () {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('login');
    });
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard')->middleware('auth:admin');

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('admin/logout', [LoginController::class, 'logout'])->name('admin.logout');


    Route::get('/api/category/{category}/subcategories', [\App\Http\Controllers\Api\User\Subcategory\SubcategoryController::class, 'getsubcategorybycategory'])
        ->name('api.subcategories.byCategory');
    Route::middleware(['auth:admin'])->group(function () {

        Route::get('/tables', [AdminController::class, 'tables'])->name('admin.tables');
        Route::get('/billing', [AdminController::class, 'billing'])->name('admin.billing');
        Route::get('/virtual-reality', [AdminController::class, 'virtualReality'])->name('admin.virtual-reality');
        // Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');

        Route::prefix('banners')->name('banners.')->group(function () {
            Route::get('/', [AdminBannerController::class, 'index'])->name('index');
            Route::get('/create', [AdminBannerController::class, 'create'])->name('create');
            Route::post('/', [AdminBannerController::class, 'store'])->name('store');
            Route::get('/{banner}/edit', [AdminBannerController::class, 'edit'])->name('edit');
            Route::put('/{banner}', [AdminBannerController::class, 'update'])->name('update');
            Route::delete('/{banner}', [AdminBannerController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('advertisements')->name('advertisements.')->group(function () {
            Route::get('/', [AdminAdvertisementController::class, 'index'])->name('index');
            Route::get('/create', [AdminAdvertisementController::class, 'create'])->name('create');
            Route::post('/', [AdminAdvertisementController::class, 'store'])->name('store');
            Route::delete('/{advertisement}', [AdminAdvertisementController::class, 'destroy'])->name('destroy');
        });

        // Route::get('category/{category}/subcategories', [\App\Http\Controllers\Api\User\Subcategory\SubcategoryController::class, 'getsubcategorybycategory']);


        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [AdminCategoryController::class, 'index'])->name('index');
            Route::get('/create', [AdminCategoryController::class, 'create'])->name('create');
            Route::post('/', [AdminCategoryController::class, 'store'])->name('store');
            Route::get('/{category}/edit', [AdminCategoryController::class, 'edit'])->name('edit');
            Route::put('/{category}', [AdminCategoryController::class, 'update'])->name('update');
            Route::delete('/{category}', [AdminCategoryController::class, 'destroy'])->name('destroy');
        });
        Route::prefix('subcategories')->name('subcategories.')->group(function () {
            Route::get('/', [AdminSubcategoryController::class, 'index'])->name('index');
            Route::get('/create', [AdminSubcategoryController::class, 'create'])->name('create');
            Route::post('/', [AdminSubcategoryController::class, 'store'])->name('store');
            Route::get('/{subcategory}/edit', [AdminSubcategoryController::class, 'edit'])->name('edit');
            Route::put('/{subcategory}', [AdminSubcategoryController::class, 'update'])->name('update');
            Route::delete('/{subcategory}', [AdminSubcategoryController::class, 'destroy'])->name('destroy');
        });


    //    Route::match(['GET','POST'], 'xpay/callback', [XPayCallbackController::class, 'callback'])
    // ->name('xpay.callback');

// rollback الوحيد اللي هنستخدمه للاختبار/الإلغاء اليدوي ويرجع نفس صفحة الـ Blade
// Route::get('payment/xpay/rollback', \App\Http\Controllers\Api\User\Payment\PaymentRollbackController::class)
//     ->name('xpay.rollback');

        // يمكنك إضافة المزيد من الصفحات هنا حسب الحاجة

        Route::prefix('countries')->group(function () {
            Route::get('/', [CountryController::class, 'index'])->name('country.index');
            Route::get('/create', [CountryController::class, 'create'])->name('country.create');
            Route::post('/', [CountryController::class, 'store'])->name('country.store');
            Route::get('/{id}/edit', [CountryController::class, 'edit'])->name('country.edit');
            Route::put('/{id}', [CountryController::class, 'update'])->name('country.update');
            Route::delete('/{id}', [CountryController::class, 'destroy'])->name('country.destroy');
        });

        Route::prefix('vendors')->group(function () {
            Route::get('/', [VendoreController::class, 'index'])->name('vendore.index');
            Route::get('/create', [VendoreController::class, 'create'])->name('vendore.create');
            Route::post('/', [VendoreController::class, 'store'])->name('vendore.store');
            Route::get('/{id}/show', [VendoreController::class, 'show'])->name('vendore.show');
            Route::get('/{id}/edit', [VendoreController::class, 'edit'])->name('vendore.edit');
            Route::put('/{id}', [VendoreController::class, 'update'])->name('vendore.update');
            Route::delete('/{id}', [VendoreController::class, 'destroy'])->name('vendore.destroy');

            Route::post('/vendors/{vendor}/update-status', [VendoreController::class, 'updateStatus'])
                ->name('vendors.updateStatus');
        });

        Route::prefix('cities')->group(function () {
            Route::get('/', [CityController::class, 'index'])->name('cities.index');
            Route::get('/create', [CityController::class, 'create'])->name('cities.create');
            Route::post('/', [CityController::class, 'store'])->name('cities.store');
            Route::get('/{id}/edit', [CityController::class, 'edit'])->name('cities.edit');
            Route::put('/{id}', [CityController::class, 'update'])->name('cities.update');
            Route::delete('/{id}', [CityController::class, 'destroy'])->name('cities.destroy');
        });
        Route::prefix('subcategories')->name('subcategories.')->group(function () {
            Route::get('/', [AdminSubcategoryController::class, 'index'])->name('index');
            Route::get('/create', [AdminSubcategoryController::class, 'create'])->name('create');
            Route::post('/', [AdminSubcategoryController::class, 'store'])->name('store');
            Route::get('/{subcategory}/edit', [AdminSubcategoryController::class, 'edit'])->name('edit');
            Route::put('/{subcategory}', [AdminSubcategoryController::class, 'update'])->name('update');
            Route::delete('/{subcategory}', [AdminSubcategoryController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('brands')->name('brands.')->group(function () {
            Route::get('/', [AdminBrandController::class, 'index'])->name('index');
            Route::get('/create', [AdminBrandController::class, 'create'])->name('create');
            Route::post('/', [AdminBrandController::class, 'store'])->name('store');
            Route::get('/{brand}/edit', [AdminBrandController::class, 'edit'])->name('edit');
            Route::put('/{brand}', [AdminBrandController::class, 'update'])->name('update');
            Route::delete('/{brand}', [AdminBrandController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('admin')->group(function () {
            Route::get('profile', [AdminProfileController::class, 'profile'])->name('admin.profile');
            Route::post('/updateProfile', [AdminProfileController::class, 'updateProfile'])->name('admin.updateProfile');
        });
        // Notifications
        Route::prefix('notifications')->middleware(['auth', 'check.notification'])->group(function () {
            Route::post('/mark-as-read', [NotificationController::class, 'markAsRead'])->name('Admin.notifications.markAsRead');
            Route::get('/mark-all-read', [NotificationController::class, 'ReadAll'])->name('Admin.notifications.markAllRead');
            Route::get('/', [NotificationController::class, 'getNotifications'])->name('Admin.notifications');
            Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('Admin.notifications.delete');
            Route::delete('/', [NotificationController::class, 'delete'])->name('Admin.notifications.deleteAll');
            Route::get('notifications', [NotificationController::class, 'index'])->name('Admin.notifications.index');
        });

        // Route::prefix('users')->group(function () {
        //     Route::get('index', [UserController::class, 'index'])->name('admin.pages.users.index');
        //     Route::get('create', [UserController::class, 'create'])->name('admin.pages.users.create');
        //     Route::post('store', [UserController::class, 'store'])->name('admin.pages.users.store');
        //     Route::get('edit/{user}', [UserController::class, 'edit'])->name('admin.pages.users.edit');
        //     Route::put('update/{user}', [UserController::class, 'update'])->name('admin.pages.users.update');
        //     Route::delete('delete/{id}', [UserController::class, 'destroy'])->name('admin.pages.users.delete');
        // });

        Route::prefix('offers')->group(function () {
            Route::get('/', [OfferController::class, 'index'])->name('offer.index');
            Route::get('create', [OfferController::class, 'create'])->name('offer.create');
            Route::post('store', [OfferController::class, 'store'])->name('offer.store');
            Route::get('edit/{offer}', [OfferController::class, 'edit'])->name('offer.edit');
            Route::put('update/{offer}', [OfferController::class, 'update'])->name('offer.update');
            Route::delete('delete/{offer}', [OfferController::class, 'destroy'])->name('offer.delete');
            Route::patch('/admin/offer/{offer}/toggle-status', [OfferController::class, 'toggleStatus'])
                ->name('offer.toggleStatus');
        });

        Route::prefix('products')->group(function () {
            Route::get('/',               [ProductController::class, 'index'])->name('products.index');
            Route::get('/create',         [ProductController::class, 'create'])->name('products.create');
            Route::post('/store',         [ProductController::class, 'store'])->name('products.store');
            Route::get('/edit/{product}', [ProductController::class, 'edit'])->name('products.edit');
            Route::put('/update/{product}', [ProductController::class, 'update'])->name('products.update');
            Route::delete('/delete/{product}', [ProductController::class, 'destroy'])->name('products.delete');
        });

        Route::resource('variants', ProductVariantController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

        Route::resource('variant-values', VariantValueController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
            ->parameters(['variant-values' => 'value']);

        //  Route::delete('/delete/{variant-values}', [VariantValueController::class, 'destroy'])->name('variant-values.destroy');


        // routes/web.php

        Route::prefix('products/{product}')->group(function () {
            Route::resource('variant-items', ProductItemVariantController::class)
                ->parameters(['variant-items' => 'variantItem'])
                ->names('products-variant')
                ->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
        })->whereNumber('product');

        Route::prefix('social-links')->name('social-links.')->group(function () {
            Route::get('/', [AdminSocialLinkController::class, 'index'])->name('index');
            Route::get('/{link}/edit', [AdminSocialLinkController::class, 'edit'])->name('edit');
            Route::put('/{link}', [AdminSocialLinkController::class, 'update'])->name('update');
            Route::delete('/{link}', [AdminSocialLinkController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('about')->name('about.')->group(function () {
            Route::get('/', [AdminAboutUsController::class, 'index'])->name('index');
            Route::get('/create', [AdminAboutUsController::class, 'create'])->name('create');
            Route::post('/store', [AdminAboutUsController::class, 'store'])->name('store');
            Route::get('/edit', [AdminAboutUsController::class, 'edit'])->name('edit');
            Route::put('/update', [AdminAboutUsController::class, 'update'])->name('update');
        });

        Route::prefix('stay-in-touch')->name('stay-in-touch.')->group(function () {
            Route::get('/', [AdminStayInTouchController::class, 'index'])->name('index');
            Route::get('/edit', [AdminStayInTouchController::class, 'edit'])->name('edit');
            Route::put('/', [AdminStayInTouchController::class, 'update'])->name('update');
        });

        Route::get('contact-us', [AdminContactUsController::class, 'index'])->name('contact_us.index');
        Route::delete('contact-us/{contactUs}', [AdminContactUsController::class, 'destroy'])->name('contact_us.destroy');


        Route::get('orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
        Route::get('orders/{order}/edit', [AdminOrderController::class, 'edit'])->name('admin.orders.edit');
        Route::put('orders/{order}', [AdminOrderController::class, 'update'])->name('admin.orders.update');
        Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
        Route::delete('orders/{order}', [AdminOrderController::class, 'destroy'])->name('admin.orders.destroy');


        Route::prefix('coupons')->name('admin.coupons.')->group(function () {
            Route::get('/', [AdminCouponController::class, 'index'])->name('index');
            Route::get('/create', [AdminCouponController::class, 'create'])->name('create');
            Route::post('/', [AdminCouponController::class, 'store'])->name('store');
            Route::get('/{coupon}/edit', [AdminCouponController::class, 'edit'])->name('edit');
            Route::put('/{coupon}', [AdminCouponController::class, 'update'])->name('update');
            Route::delete('/{coupon}', [AdminCouponController::class, 'destroy'])->name('destroy');
        });
        Route::prefix('shipping-zones')->name('admin.shipping_zones.')->group(function () {
            Route::get('/', [ShippingZoneController::class, 'index'])->name('index');
            Route::get('/create', [ShippingZoneController::class, 'create'])->name('create');
            Route::post('/', [ShippingZoneController::class, 'store'])->name('store');
            Route::get('/{shippingZone}/edit', [ShippingZoneController::class, 'edit'])->name('edit');
            Route::put('/{shippingZone}', [ShippingZoneController::class, 'update'])->name('update');
            Route::delete('/{shippingZone}', [ShippingZoneController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('privacy-policy')->name('admin.privacy-policy.')->group(function () {
            Route::get('edit', [PrivacyPolicyController::class, 'edit'])->name('edit');
            Route::put('update', [PrivacyPolicyController::class, 'update'])->name('update');
        });

        Route::prefix('terms-conditions')->name('admin.terms-conditions.')->group(function () {
            Route::get('edit', [TermsConditionsController::class, 'edit'])->name('edit');
            Route::put('update', [TermsConditionsController::class, 'update'])->name('update');
        });

        Route::prefix('return-policy')->name('admin.return-policy.')->group(function () {
            Route::get('edit', [ReturnPolicyController::class, 'edit'])->name('edit');
            Route::put('update', [ReturnPolicyController::class, 'update'])->name('update');
        });



        Route::prefix('roles')->name('admin.roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('/create', [RoleController::class, 'create'])->name('create');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::put('/{role}', [RoleController::class, 'update'])->name('update');
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('permissions')->name('admin.permissions.')->group(function () {
            Route::get('/', [PermissionController::class, 'index'])->name('index');
            Route::get('/create', [PermissionController::class, 'create'])->name('create');
            Route::post('/', [PermissionController::class, 'store'])->name('store');
            Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('edit');
            Route::put('/{permission}', [PermissionController::class, 'update'])->name('update');
            Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('admins')->name('admin.admins.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('create', [UserController::class, 'create'])->name('create');
            Route::post('store', [UserController::class, 'store'])->name('store');
            Route::get('{admin}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('{admin}', [UserController::class, 'update'])->name('update');
            Route::delete('{admin}', [UserController::class, 'destroy'])->name('destroy');
        });
    
        Route::get('returns', [AdminReturnRequestController::class, 'index'])->name('admin.returns.index');
        Route::get('returns/{return_request}', [AdminReturnRequestController::class, 'show'])->name('admin.returns.show');
        Route::get('returns/{return_request}/edit', [AdminReturnRequestController::class, 'edit'])->name('admin.returns.edit');
        Route::put('returns/{return_request}', [AdminReturnRequestController::class, 'update'])->name('admin.returns.update');

        Route::resource('damaged-stocks', AdminDamagedStockController::class)
            ->only(['index', 'show']);


        Route::get('damaged-stocks', [AdminDamagedStockController::class, 'index'])->name('admin.damaged-stocks.index');
        Route::get('damaged-stocks/{}', [AdminDamagedStockController::class, 'show'])->name('admin.damaged-stocks.show');
        
               Route::get('vsoft-cities', [AdminVSoftCityController::class, 'index'])->name('admin.vsoft-cities.index');
        Route::get('vsoft-cities/{id}/edit', [AdminVSoftCityController::class, 'edit'])->name('admin.vsoft-cities.edit');
        Route::post('vsoft-cities/{id}', [AdminVSoftCityController::class, 'update'])->name('admin.vsoft-cities.update');
        // جديد: Bulk map مدن → زون
        Route::post('vsoft-cities/bulk-map', [AdminVSoftCityController::class, 'bulkMap'])->name('admin.vsoft-cities.bulk-map');


        // Route::resource('variants.values', VariantValueController::class)
        //     ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
        //     ->shallow()
        //     ->names('variant-values');



        // Route::prefix('attributes')->group(function () {
        //     Route::get('/',                 [AdminAttributeDefinitionController::class, 'index'])->name('attributes.index');
        //     Route::get('/create',           [AdminAttributeDefinitionController::class, 'create'])->name('attributes.create');
        //     Route::post('/store',           [AdminAttributeDefinitionController::class, 'store'])->name('attributes.store');
        //     Route::get('/edit/{id}',        [AdminAttributeDefinitionController::class, 'edit'])->name('attributes.edit');
        //     Route::put('/update/{id}',      [AdminAttributeDefinitionController::class, 'update'])->name('attributes.update');
        //     Route::delete('/delete/{id}',   [AdminAttributeDefinitionController::class, 'destroy'])->name('attributes.delete');
        //     Route::patch('/{id}/toggle-status', [AdminAttributeDefinitionController::class, 'toggleStatus'])->name('attributes.toggleStatus');
        // });
        // // أمثلة:
        // Route::get('product-attributes', [AdminProductAttributeController::class, 'index'])->name('product-attributes.index');
        // Route::get('product-attributes/create', [AdminProductAttributeController::class, 'create'])->name('product-attributes.create');
        // Route::post('product-attributes', [AdminProductAttributeController::class, 'store'])->name('product-attributes.store');

        // Route::get('product-attributes/{product_id}/{attribute_id}/{idx}/edit', [AdminProductAttributeController::class, 'edit'])->name('product-attributes.edit');
        // Route::put('product-attributes/{product_id}/{attribute_id}/{idx}', [AdminProductAttributeController::class, 'update'])->name('product-attributes.update');
        // Route::delete('product-attributes/{product_id}/{attribute_id}/{idx}', [AdminProductAttributeController::class, 'destroy'])->name('product-attributes.destroy');
    });



    Route::get('/vendor', function () {
        if (Auth::guard('vendor')->check()) {
            return redirect()->route('vendor.dashboard');
        }

        return redirect()->route('vendor.login');
    });

    Route::get('/vendor/dashboard', [VendorController::class, 'dashboard'])->name('vendor.dashboard')->middleware('auth:vendor');

    // بـ:
    Route::get('/vendor/login', [LoginVendorController::class, 'showLoginForm'])->name('vendor.login');
    Route::post('/vendor/login', [LoginVendorController::class, 'login']);
    Route::post('vendor/logout', [LoginVendorController::class, 'logout'])->name('vendor.logout');

    // ───────────────────────────────
    // Vendor routes (no conflicts)
    // ───────────────────────────────
    Route::prefix('vendor')
        ->middleware(['auth:vendor'])
        ->as('vendor.')
        ->group(function () {
            // صفحات عامة للفندور
            Route::get('/tables', [AdminController::class, 'tables'])->name('tables');
            Route::get('/billing', [AdminController::class, 'billing'])->name('billing');
            Route::get('/virtual-reality', [AdminController::class, 'virtualReality'])->name('virtual-reality');
            Route::get('/profile', [AdminController::class, 'profile'])->name('profile');

            Route::get('/profile', [VendorProfileController::class, 'edit'])->name('profile.edit');
            Route::post('/profile', [VendorProfileController::class, 'update'])->name('profile.update');

            // PRODUCTS (vendor/products/…)
            Route::prefix('products')->as('products.')->group(function () {
                Route::get('/',                   [VendorProductController::class, 'index'])->name('index');
                Route::get('/create',             [VendorProductController::class, 'create'])->name('create');
                Route::post('/store',             [VendorProductController::class, 'store'])->name('store');
                Route::get('/edit/{product}',     [VendorProductController::class, 'edit'])->name('edit');
                Route::put('/update/{product}',   [VendorProductController::class, 'update'])->name('update');
                Route::delete('/delete/{product}', [VendorProductController::class, 'destroy'])->name('delete');
            });

            // VARIANTS (vendor/variants/…)
            Route::resource('variants', VendorProductVariantController::class)
                ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
                ->names('variants'); // => vendor.variants.index ... الخ

            // VARIANT VALUES (vendor/variant-values/…)
            Route::resource('variant-values', VendorVariantValueController::class)
                ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
                ->parameters(['variant-values' => 'value'])
                ->names('variant-values'); // => vendor.variant-values.index ... الخ

            // PRODUCT VARIANT ITEMS (vendor/products/{product}/variant-items/…)
            Route::prefix('products/{product}')
                ->whereNumber('product')
                ->group(function () {
                    Route::resource('variant-items', VendorProductItemVariantController::class)
                        ->parameters(['variant-items' => 'variantItem'])
                        ->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy'])
                        ->names('products-variant'); // => vendor.products-variant.index ... الخ
                });
            Route::prefix('offers')->as('offers.')->group(function () {
                Route::get('/',            [VendorOfferController::class, 'index'])->name('index');
                Route::get('create',       [VendorOfferController::class, 'create'])->name('create');
                Route::post('store',       [VendorOfferController::class, 'store'])->name('store');
                Route::get('edit/{offer}', [VendorOfferController::class, 'edit'])->name('edit');
                Route::put('update/{offer}', [VendorOfferController::class, 'update'])->name('update');
                Route::delete('delete/{offer}', [VendorOfferController::class, 'destroy'])->name('delete');

                Route::patch('toggle-status/{offer}', [VendorOfferController::class, 'toggleStatus'])->name('toggleStatus');
            });

            // Route::prefix('orders')->name('orders.')->group(function () {
            //     Route::get('/',             [VendorOrderController::class, 'index'])->name('index');
            //     Route::get('/{order}',      [VendorOrderController::class, 'show'])->name('show');
            //     Route::get('/{order}/edit', [VendorOrderController::class, 'edit'])->name('edit');
            //     Route::put('/{order}',      [VendorOrderController::class, 'update'])->name('update');
            //     Route::put('/{order}',      [VendorOrderController::class, 'destroy'])->name('destroy');
            // });


            Route::get('orders', [VendorOrderController::class, 'index'])->name('orders.index');
            Route::get('orders/{order}/edit', [VendorOrderController::class, 'edit'])->name('orders.edit');
            Route::put('orders/{order}', [VendorOrderController::class, 'update'])->name('orders.update');
            Route::get('orders/{order}', [VendorOrderController::class, 'show'])->name('orders.show');
            Route::delete('orders/{order}', [VendorOrderController::class, 'destroy'])->name('orders.destroy');
            
            Route::get('returns', [VendorReturnRequestController::class, 'index'])->name('returns.index');
            Route::get('returns/{return_request}', [VendorReturnRequestController::class, 'show'])->name('returns.show');
            Route::get('returns/{return_request}/edit', [VendorReturnRequestController::class, 'edit'])->name('returns.edit');
            Route::put('returns/{return_request}', [VendorReturnRequestController::class, 'update'])->name('returns.update');

            Route::resource('damaged-stocks', VendorDamagedStockController::class)
                ->only(['index', 'show']);


            Route::get('damaged-stocks', [VendorDamagedStockController::class, 'index'])->name('damaged-stocks.index');
            Route::get('damaged-stocks/{}', [VendorDamagedStockController::class, 'show'])->name('damaged-stocks.show');
            
            Route::get('/notifications/open/{id}', [VendorNotificationController::class, 'open'])
                ->name('notifications.open');

            // تعليم الكل مقروء
            Route::post('/notifications/read-all', [VendorNotificationController::class, 'readAll'])
                ->name('notifications.readAll');
        });
});
