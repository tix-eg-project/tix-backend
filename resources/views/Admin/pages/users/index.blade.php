@extends('Admin.layout.app')
@section('users_active', 'active')
@section('title', __('Users'))
@section('content')

    {{-- Flash Messages --}}
    @foreach (['Add' => 'success', 'Error' => 'danger', 'edit' => 'success', 'delete' => 'danger'] as $key => $type)
        @if (session()->has($key))
            <div class="alert alert-{{ $type }} alert-dismissible fade show text-center" role="alert">
                <strong>{{ session()->get($key) }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
    @endforeach

    <script>
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 3000);
    </script>

    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4 text-center"><span class="text-muted fw-light"></span>
                {{ __('messages.Users') }}</h4>

            <!-- Basic Bootstrap Table -->
            <div class="card">
                <div class="card-body">


                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <form method="GET" action="{{ route('admin.pages.users.index') }}" id="searchForm" class="d-flex"
                            style="gap: 10px;">
                            <input type="text" name="search" id="searchInput" class="form-control bg-light text-dark"
                                placeholder="{{ __('Search by User name') }}" value="{{ request('search') }}"
                                style="width: 250px;">

                        </form>
                        <div>
                            <a href="{{ route('admin.pages.users.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> {{ __('messages.Add User+') }}
                            </a>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="table-responsive text-nowrap">
                        <table class="table table-striped text-black text-center">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('messages.Name') }}</th>
                                    <th>{{ __('messages.Email') }}</th>
                                    <th>{{ __('messages.Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                             <a href="{{ route('admin.pages.users.edit', $user) }}" class="btn btn-info">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <button type="button" onclick="deleteId({{ $user->id }})"
                                                class="btn btn-danger">
                                                <i class="far fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">{{ __('messages.Nothing!') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="p-2 text-end">
                            {!! $users->withQueryString()->links('pagination::bootstrap-5') !!}
                        </div>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">{{ __('messages.Back') }}</a>

                </div>
            </div>
        </div>
    </div>
    </div>

@endsection

@push('scripts')
    <script>
        function deleteId(id) {
            Swal.fire({
                title: '{{ __('Are you sure?') }}',
                text: "{{ __('Do you want to delete this item') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC143C',
                cancelButtonColor: '#696969',
                cancelButtonText: "{{ __('Cancel') }}",
                confirmButtonText: '{{ __('Yes, delete it!') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.action = "{{ route('admin.pages.users.delete', '') }}/" + id;
                    form.method = 'POST';

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';

                    form.appendChild(csrfInput);
                    form.appendChild(methodInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const input = document.getElementById("UserSearch");
            const form = document.getElementById("searchForm");
            let timeout = null;

            input.addEventListener("input", function() {
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    form.submit();
                }, 500); // ينتظر نصف ثانية بعد آخر كتابة
            });
        });
    </script>
@endpush
