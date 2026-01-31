@extends('Admin.layout.app')

@section('country_active', 'active')
@section('country_open', 'open')


@section('title', __('messages.Country'))

@section('content')
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4 text-center"><span class="text-muted fw-light"></span> {{ __('messages.Countries') }}</h4>

            <!-- Basic Bootstrap Table -->
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <!-- Left: Search -->
                        {{-- <div> --}}
                        <form method="GET" action="{{ route('country.index') }}" id="searchForm" class="d-flex"
                            style="gap: 10px;">
                            <input type="text" name="search" id="searchInput" class="form-control bg-light text-dark"
                                placeholder="{{ __('Search by Country name') }}" value="{{ request('search') }}"
                                style="width: 250px;">

                        </form>
                        {{-- </div> --}}

                        <!-- Right: Add Button -->
                        <div>
                            <a href="{{ route('country.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> {{ __('messages.Add Country+') }}
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive text-nowrap">
                        <table class="table table-striped text-black text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.Name') }}</th>
                                    <th>{{ __('messages.Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($countries as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>
                                            <a href="{{ route('country.edit', $item->id) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                            <form action="{{ route('country.destroy', $item->id) }}" method="POST"
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
                                        <td colspan="4" class="text-center">{{ __('messages.no_data') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $countries->links('pagination::bootstrap-4') }}
                    </div>

                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">{{ __('messages.Back') }}</a>
                </div>
            </div>
        </div>
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
