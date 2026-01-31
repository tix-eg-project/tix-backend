<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                {{-- Logo SVG --}}
                <svg width="25" viewBox="0 0 25 42" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <path id="path-1" d="M13.79.36L3.4 7.44..." />
                    </defs>
                    <g fill="none">
                        <g transform="translate(-27 -15)">
                            <g transform="translate(27 15)">
                                <mask id="mask-2" fill="#fff">
                                    <use xlink:href="#path-1" />
                                </mask>
                                <use fill="#696cff" xlink:href="#path-1" />
                            </g>
                        </g>
                    </g>
                </svg>
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2">Tix</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    @php
    // helper للـ active
    $is = fn(...$patterns) => request()->routeIs($patterns);

    // helper فحص الصلاحية
    function canItem(?string $perm): bool {
    if (!$perm) return true;
    $u = auth()->user();
    return $u && $u->can($perm);
    }

    // كل عنصر ومعاه صلاحية العرض المناسبة
    $aMenu = [
    ['label'=>__('messages.dashboard'), 'icon'=>'bx bx-home-circle', 'route'=>'admin.dashboard', 'active'=>$is('admin.dashboard'), 'permission'=>'dashboard_index'],

    ['label'=>__('messages.Countries'), 'icon'=>'bx bx-globe', 'route'=>'country.index', 'active'=>$is('country.*'), 'permission'=>'countries_index'], // مفقودة: أضفها
    ['label'=>__('messages.Cities'), 'icon'=>'bx bx-buildings', 'route'=>'cities.index', 'active'=>$is('cities.*'), 'permission'=>'cities_index'], // مفقودة: أضفها

    ['label'=>__('messages.banners'), 'icon'=>'bx bx-carousel', 'route'=>'banners.index', 'active'=>$is('banners.*'), 'permission'=>'banners_index'], // مفقودة: أضفها

    ['label'=>__('messages.categories'), 'icon'=>'bx bx-category', 'route'=>'categories.index', 'active'=>$is('categories.*'), 'permission'=>'categories_index'],
    ['label'=>__('messages.subcategories'), 'icon'=>'bx bx-image', 'route'=>'subcategories.index', 'active'=>$is('subcategories.*'), 'permission'=>'subcategories_index'],

    ['label'=>__('messages.Brands'), 'icon'=>'bx bx-purchase-tag', 'route'=>'brands.index', 'active'=>$is('brands.*'), 'permission'=>'brands_index'], // مفقودة: أضفها

    ['label'=>__('messages.Products'), 'icon'=>'bx bx-box', 'route'=>'products.index', 'active'=>$is('products.*'), 'permission'=>'products_index'],

    ['label'=>__('messages.Variants'), 'icon'=>'bx bx-shape-circle', 'route'=>'variants.index', 'active'=>$is('variants.*'), 'permission'=>'variants_index'], // مفقودة: أضفها
    ['label'=>__('messages.Variant Values'), 'icon'=>'bx bx-palette', 'route'=>'variant-values.index', 'active'=>$is('variant-values.*'), 'permission'=>'variant_values_index'],// مفقودة: أضفها (بـ underscore)

    ['label'=>__('messages.Offers'), 'icon'=>'bx bx-purchase-tag', 'route'=>'offer.index', 'active'=>$is('offer.*','offers.*'), 'permission'=>'offers_index'], // مفقودة: أضفها

    ['label'=>__('messages.Coupons'), 'icon'=>'bx bx-gift', 'route'=>'admin.coupons.index', 'active'=>$is('admin.coupons.*'), 'permission'=>'coupons_index'],

    // لديك حالياً shipping_index / shipping_update فقط؛ الأفضل إنشاء shipping_zones_index
    ['label'=>__('messages.Shipping Zones'), 'icon'=>'bx bx-navigation', 'route'=>'admin.shipping_zones.index', 'active'=>$is('admin.shipping_zones.*'), 'permission'=>'shipping_zones_index'],// مفقودة: أضفها (مؤقتاً ممكن تربطها بـ shipping_index)

    ['label'=>__('messages.Vsoft_cities'), 'icon'=>'bx bxs-error', 'route'=>'admin.vsoft-cities.index', 'active'=>$is('admin.damaged-stocks.*')],
    ['label'=>__('messages.Orders'), 'icon'=>'bx bx-receipt', 'route'=>'admin.orders.index', 'active'=>$is('admin.orders.*'), 'permission'=>'orders_index'],
    ['label' => __('messages.Returns'), 'icon' => 'bx bx-rotate-left', 'route' => 'admin.returns.index', 'active' => $is('admin.returns.*')],

    ['label'=>__('messages.damaged_stocks'), 'icon'=>'bx bxs-error', 'route'=>'admin.damaged-stocks.index', 'active'=>$is('admin.damaged-stocks.*')],

    ['label'=>__('messages.My Profile'), 'icon'=>'fa-solid fa-user', 'route'=>'admin.profile', 'active'=>$is('admin.profile'), 'permission'=>null],

    // انتبه: route اسمها vendore.index (بالـ e)
    ['label'=>__('messages.Vendors'), 'icon'=>'bx bx-briefcase', 'route'=>'vendore.index', 'active'=>$is('vendore.*','vendors.*'), 'permission'=>'vendors_index'], // مفقودة: أضفها

    // عندك في الجدول about_edit فقط، فهنستخدمه
    ['label'=>__('messages.About Us'), 'icon'=>'bx bx-info-circle', 'route'=>'about.index', 'active'=>$is('about.*'), 'permission'=>'about_edit'],

    ['label'=>__('messages.Social Links'), 'icon'=>'bx bx-share-alt', 'route'=>'social-links.index', 'active'=>$is('social-links.*'), 'permission'=>'social_links_index'],
    ['label'=>__('messages.stay-in-touch'), 'icon'=>'bx bx-chat', 'route'=>'stay-in-touch.index', 'active'=>$is('stay-in-touch.*'), 'permission'=>'stay_in_touch_index'],
    ['label'=>__('messages.contact-us'), 'icon'=>'bx bx-phone', 'route'=>'contact_us.index', 'active'=>$is('contact_us.*'), 'permission'=>'contact_us_index'],

    ['label'=>__('messages.Notifications'), 'icon'=>'bx bx-bell', 'route'=>'Admin.notifications', 'active'=>$is('Admin.notifications','Admin.notifications.*'), 'permission'=>'notifications_index'],

    // سياسات: مفيهاش صلاحيات عندك.. هنقترح أسماء edit
    ['label'=>__('messages.privacy-policy'), 'icon'=>'bx bx-lock-alt', 'route'=>'admin.privacy-policy.edit', 'active'=>$is('admin.privacy-policy.*'), 'permission'=>'privacy_policy_edit'], // مفقودة: أضفها
    ['label'=>__('messages.terms-conditions'), 'icon'=>'bx bx-file', 'route'=>'admin.terms-conditions.edit', 'active'=>$is('admin.terms-conditions.*'), 'permission'=>'terms_conditions_edit'], // مفقودة: أضفها
    ['label'=>__('messages.return-policy'), 'icon'=>'bx bx-undo', 'route'=>'admin.return-policy.edit', 'active'=>$is('admin.return-policy.*'), 'permission'=>'return_policy_edit'], // مفقودة: أضفها

    ['label'=>__('messages.roles'), 'icon'=>'bx bx-lock-alt', 'route'=>'admin.roles.index', 'active'=>$is('admin.roles.*'), 'permission'=>'roles_index'],
    ['label'=>__('messages.permissions'), 'icon'=>'bx bx-key', 'route'=>'admin.permissions.index', 'active'=>$is('admin.permissions.*'), 'permission'=>'permissions_index'],
    ['label'=>__('messages.admins'), 'icon'=>'fa-solid fa-user', 'route'=>'admin.admins.index', 'active'=>$is('admin.admins.*'), 'permission'=>'admins_index'],
    ];
    @endphp

    <ul class="menu-inner py-1">
        @foreach($aMenu as $it)
        @if (canItem($it['permission'] ?? null))
        <li class="menu-item {{ $it['active'] ? 'active open' : '' }}">
            <a href="{{ route($it['route']) }}" class="menu-link {{ $it['active'] ? 'aria-current' : '' }}">
                <i class="menu-icon tf-icons {{ $it['icon'] }}"></i>
                <div>{{ $it['label'] }}</div>
            </a>
        </li>
        @endif
        @endforeach
    </ul>
</aside>

@push('styles')
<style>
    .menu .menu-item.active>.menu-link {
        background: rgba(105, 108, 255, 0.08);
        color: #696cff !important;
        border-radius: 10px;
    }

    .menu .menu-item.active .menu-icon {
        color: #696cff !important;
    }

    .menu-inner {
        padding-inline: .6rem;
    }

    [dir="rtl"] .menu .menu-item.active>.menu-link {
        border-right: 3px solid #696cff;
    }

    [dir="ltr"] .menu .menu-item.active>.menu-link {
        border-left: 3px solid #696cff;
    }
</style>
@endpush