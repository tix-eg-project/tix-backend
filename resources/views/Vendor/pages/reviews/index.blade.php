@extends('Vendor.layout.app')
@section('reviews_active', 'active')
@section('title', __('messages.Reviews') ?? 'Reviews')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <h4 class="mb-0">
                {{ __('messages.Product Reviews') ?? 'Product Reviews' }}
                @if(request('product_id'))
                    - {{ $reviews->first()->product->name_text ?? 'Product #'.request('product_id') }}
                @endif
            </h4>
            <div class="d-flex gap-2">
                @if(request('product_id'))
                    <a href="{{ route('vendor.reviews.index') }}" class="btn btn-outline-secondary">
                        {{ __('messages.All Reviews') ?? 'All Reviews' }}
                    </a>
                @endif
                <a href="{{ route('vendor.products.index') }}" class="btn btn-secondary">
                    {{ __('messages.Products List') ?? 'Products List' }}
                </a>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover table-striped text-center align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:56px">#</th>
                            <th>{{ __('messages.Product') ?? 'Product' }}</th>
                            <th>{{ __('messages.User') ?? 'User' }}</th>
                            <th>{{ __('messages.Rating') ?? 'Rating' }}</th>
                            <th>{{ __('messages.Comment') ?? 'Comment' }}</th>
                            <th>{{ __('messages.Image') ?? 'Image' }}</th>
                            <th>{{ __('messages.Status') ?? 'Status' }}</th>
                            <th>{{ __('messages.Created At') ?? 'Created At' }}</th>
                            <th class="text-end">{{ __('messages.Actions') ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reviews as $review)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">
                                {{ $review->product->name_text ?? ($review->product->name['en'] ?? ($review->product->name['ar'] ?? 'Product #'.$review->product_id)) }}
                            </td>
                            <td>{{ $review->user->name ?? 'User #'.$review->user_id }}</td>
                            <td>
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bx {{ $i <= $review->rating ? 'bxs-star text-warning' : 'bx-star text-muted' }}"></i>
                                @endfor
                            </td>
                            <td class="text-muted" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $review->comment }}
                            </td>
                            <td>
                                @if($review->image)
                                    <img src="{{ asset('storage/' . $review->image) }}" width="50" height="50" style="object-fit:cover; border-radius:6px;" alt="Review Image">
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $review->is_visible ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $review->is_visible ? (__('messages.visible') ?? 'Visible') : (__('messages.hidden') ?? 'Hidden') }}
                                </span>
                            </td>
                            <td>{{ $review->created_at->format('Y-m-d') }}</td>
                            <td class="text-end">
                                <form action="{{ route('vendor.reviews.toggle-visibility', $review->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $review->is_visible ? 'btn-outline-secondary' : 'btn-outline-success' }}">
                                        @if($review->is_visible)
                                            <i class="bx bx-hide"></i> {{ __('messages.Hide') ?? 'Hide' }}
                                        @else
                                            <i class="bx bx-show"></i> {{ __('messages.Show') ?? 'Show' }}
                                        @endif
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">{{ __('messages.No reviews found') ?? 'No reviews found' }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
