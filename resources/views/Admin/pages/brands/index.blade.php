@extends('Admin.layout.app')
@section('Brand_active', 'active')
@section('Brand_open', 'open')
@section('title', __('messages.Brands'))

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4>{{ __('messages.Brands') }}</h4>

    <div class="card">
        <div class="card-body">


            <div class="d-flex justify-content-between align-items-center mb-3">
                <!-- Left: Search -->
                <form method="GET" action="{{ route('brands.index') }}" id="searchForm" class="d-flex"
                    style="gap: 10px;">
                    <input type="text" name="search" id="searchInput" class="form-control bg-light text-dark"
                        placeholder="{{ __('messages.Search by Brand name') }}" value="{{ request('search') }}"
                        style="width: 250px;">

                </form>

                <!-- Right: Add Button -->
                <div>
                    <a href="{{ route('brands.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> {{ __('messages.Add Brand+') }}
                    </a>
                </div>
            </div>


            <div class="table-responsive text-nowrap">
                <table class="table table-striped text-black text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($brands as $brand)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $brand->name }}</td>



                            <td>
                                <a href="{{ route('brands.edit', $brand->id) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('brands.destroy', $brand->id) }}" method="POST"
                                    class="d-inline-block">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('{{ __('messages.confirm_delete') }}')"
                                        class="btn btn-sm btn-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">{{ __('messages.no_data') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


        </div>
    </div>
</div>


<div class="d-flex justify-content-center">
    {{ $brands->links('pagination::bootstrap-4') }}
</div>
@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('searchInput');
        const form = document.getElementById('searchForm');
        let timer = null;

        input.addEventListener('input', function() {
            clearTimeout(timer);
            timer = setTimeout(() => {
                form.submit();
            }, 500); // انتظر 0.5 ثانية بعد آخر حرف
        });
    });
</script>
@endpush