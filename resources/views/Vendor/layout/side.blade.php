<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('vendor.dashboard') }}" class="app-brand-link">
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
    // Helper small function
    $is = fn(...$patterns) => request()->routeIs($patterns);

    $vMenu = [
    ['label'=>__('messages.dashboard'), 'icon'=>'bx bx-home-circle', 'route'=>'vendor.dashboard', 'active'=>$is('vendor.dashboard')],
    ['label'=>__('messages.Products'), 'icon'=>'bx bx-box', 'route'=>'vendor.products.index', 'active'=>$is('vendor.products.*')],
    ['label'=>__('messages.Variants'), 'icon'=>'bx bx-shape-circle', 'route'=>'vendor.variants.index', 'active'=>$is('vendor.variants.*')],
    ['label'=>__('messages.Variant Values'), 'icon'=>'bx bx-palette', 'route'=>'vendor.variant-values.index','active'=>$is('vendor.variant-values.*','vendor.variant-values.index','vendor.variant-values.create','vendor.variant-values.edit')],
    ['label'=>__('messages.Offers'), 'icon'=>'bx bx-purchase-tag', 'route'=>'vendor.offers.index', 'active'=>$is('vendor.offers.*')],
    ['label'=>__('messages.orders'), 'icon'=>'bx bx-receipt', 'route'=>'vendor.orders.index', 'active'=>$is('vendor.orders.*')],
     ['label' => __('messages.Returns'), 'icon' => 'bx bx-rotate-left', 'route' => 'vendor.returns.index', 'active' => $is('vendor.returns.*')],

    ['label'=>__('messages.damaged_stocks'), 'icon'=>'bx bxs-error', 'route'=>'vendor.damaged-stocks.index', 'active'=>$is('vendor.damaged-stocks.*')],

    ];
    @endphp

    <ul class="menu-inner py-1">
        @foreach($vMenu as $it)
        <li class="menu-item {{ $it['active'] ? 'active open' : '' }}">
            <a href="{{ route($it['route']) }}" class="menu-link {{ $it['active'] ? 'aria-current' : '' }}">
                <i class="menu-icon tf-icons {{ $it['icon'] }}"></i>
                <div>{{ $it['label'] }}</div>
            </a>
        </li>
        @endforeach
    </ul>
</aside>

@push('styles')
<style>
    /* Active styling */
    .menu .menu-item.active>.menu-link {
        background: rgba(105, 108, 255, 0.08);
        color: #696cff !important;
        border-radius: 10px;
    }

    .menu .menu-item.active .menu-icon {
        color: #696cff !important;
    }

    /* Thin separator shadow already exists; keep spacing pleasant */
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