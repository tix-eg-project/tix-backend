@extends('Admin.layout.app')

@section('title', __('Dashboard'))

@section('content')

    <div class="analytics">
        <div class="row text-center">


            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card rounded-4 custom-card bg-light text-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center gap-3 mb-2">
                            <h5 class="mb-0">
                                <i class="bi bi-people-fill text-primary"></i>
                                {{ __('messages.Number Of Users') }}
                            </h5>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">

                            <h2 class="fw-bold mb-0">{{ $userscount }}</h2>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card rounded-4 custom-card bg-light text-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center gap-3 mb-2">
                            <h5 class="mb-0">
                                <i class="menu-icon tf-icons bx bx-briefcase text-primary"></i>
                                {{ __('messages.Number Of Vendor') }}
                            </h5>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('vendore.index') }}" class="btn btn-success">
                                {{ __('messages.Show') }}
                            </a>
                            <h2 class="fw-bold mb-0">{{ $vendorcount }}</h2>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card rounded-4 custom-card bg-light text-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center gap-3 mb-2">
                            <h5 class="mb-0">
                                <i class="menu-icon tf-icons bx bx-buildings text-primary"></i>
                                {{ __('messages.Number Of City') }}
                            </h5>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('cities.index') }}" class="btn btn-success">
                                {{ __('messages.Show') }}
                            </a>
                            <h2 class="fw-bold mb-0">{{ $citycount }}</h2>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card rounded-4 custom-card bg-light text-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center gap-3 mb-2">
                            <h5 class="mb-0">
                                <i class="menu-icon tf-icons bx bx-globe text-primary"></i>
                                {{ __('messages.Number Of Country') }}
                            </h5>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('country.index') }}" class="btn btn-success">
                                {{ __('messages.Show') }}
                            </a>
                            <h2 class="fw-bold mb-0">{{ $countrycount }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card rounded-4 custom-card bg-light text-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center gap-3 mb-2">
                            <h5 class="mb-0">
                                <i class="menu-icon tf-icons bx bx-receipt text-primary"></i>
                                {{ __('messages.Number_Of_Orders') }}
                            </h5>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-success">
                                {{ __('messages.Show') }}
                            </a>
                            <h2 class="fw-bold mb-0">{{ $ordercount }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card rounded-4 custom-card bg-light text-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center gap-3 mb-2">
                            <h5 class="mb-0">
                                <i class="menu-icon tf-icons bx bx-phone text-primary"></i>
                                {{ __('messages.Number_Of_Messages') }}
                            </h5>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('contact_us.index') }}" class="btn btn-success">
                                {{ __('messages.Show') }}
                            </a>
                            <h2 class="fw-bold mb-0">{{ $contactuscount }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card rounded-4 custom-card bg-light text-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center gap-3 mb-2">
                            <h5 class="mb-0">
                                <i class="menu-icon tf-icons bx bx-navigation text-primary"></i>
                                {{ __('messages.Number_Of_Shipping_Zones') }}
                            </h5>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('admin.shipping_zones.index') }}" class="btn btn-success">
                                {{ __('messages.Show') }}
                            </a>
                            <h2 class="fw-bold mb-0">{{ $shippingzone }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card rounded-4 custom-card bg-light text-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center gap-3 mb-2">
                            <h5 class="mb-0">
                                <i class="menu-icon tf-icons bx bx-box text-primary"></i>
                                {{ __('messages.Number_Of_Products') }}
                            </h5>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('products.index') }}" class="btn btn-success">
                                {{ __('messages.Show') }}
                            </a>
                            <h2 class="fw-bold mb-0">{{ $productcount }}</h2>
                        </div>
                    </div>
                </div>
            </div>






        </div>
    </div>


@endsection
