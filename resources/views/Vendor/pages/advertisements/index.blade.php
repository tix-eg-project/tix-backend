@extends('Admin.layout.app')
@section('advertisement_active', 'active')
@section('advertisement_open', 'open')
@section('title', __('messages.advertisements'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>{{ __('messages.advertisements') }}</h4>

    <a href="{{ route('advertisements.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> {{ __('messages.add_advertisement') }}
    </a>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ __('messages.advertisements') }}</h5>

        <!-- Advertisement Images Grid -->
        <div class="row">
            @foreach($advertisements as $advertisement)
            <div class="col-md-3 mb-4">
                <!-- Advertisement Card -->
                <div class="card shadow-sm">
                    <img src="{{ asset($advertisement->image) }}" class="card-img-top" alt="Advertisement Image" style="height: 200px; object-fit: cover;">
                    <div class="card-body text-center">
                        <form action="{{ route('advertisements.destroy', $advertisement->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">{{ __('messages.delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection