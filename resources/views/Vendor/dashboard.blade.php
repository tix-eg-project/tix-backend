@extends('Vendor.layout.app')

@section('title', __('Dashboard'))

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="analytics">
            <div class="row text-center">

                {{-- المنتجات --}}
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card rounded-4 custom-card bg-light text-dark">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center gap-3 mb-2">
                                <h5 class="mb-0">
                                    <i class="menu-icon tf-icons bx bx-box text-primary"></i>
                                    {{ __('messages.Number Of products') }}
                                </h5>
                                <!-- <span class="badge bg-label-primary text-primary">
                                    {{ number_format($revenue30d, 2) }} {{ __('messages.currency') }}
                                </span> -->
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="{{ route('vendor.products.index') }}" class="btn btn-success">
                                    {{ __('messages.Show') }}
                                </a>
                                <h2 class="fw-bold mb-0">{{ $productsCount }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- العروض --}}
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card rounded-4 custom-card bg-light text-dark">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center gap-3 mb-2">
                                <h5 class="mb-0">
                                    <i class="menu-icon tf-icons bx bx-purchase-tag text-primary"></i>
                                    {{ __('messages.Offers') }}
                                </h5>
                                <!-- <span class="badge bg-label-primary text-primary">
                                    {{ number_format($revenue30d, 2) }} {{ __('messages.currency') }}
                                </span> -->
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="{{ route('vendor.offers.index') }}" class="btn btn-success">
                                    {{ __('messages.Show') }}
                                </a>
                                <h2 class="fw-bold mb-0">{{ $offersCount }}</h2>
                            </div>
                        </div>
                    </div>
                </div>



                {{-- الطلبات --}}
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card rounded-4 custom-card bg-light text-dark">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center gap-3 mb-2">
                                <h5 class="mb-0">
                                    <i class="menu-icon tf-icons bx bx-receipt text-primary"></i>
                                    {{ __('messages.Number Of Orders') }}
                                </h5>
                                <!-- <span class="badge bg-label-primary text-primary">
                                    {{ number_format($revenue30d, 2) }} {{ __('messages.currency') }}
                                </span> -->
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="{{ route('vendor.orders.index') }}" class="btn btn-success">
                                    {{ __('messages.Show') }}
                                </a>
                                <h2 class="fw-bold mb-0">{{ $ordersCount }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection