@extends('Admin.layout.app')
@section('notification_active', 'active')

@section('title', __('messages.Notifications'))

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4 text-center">{{ __('messages.Notifications') }}</h4>

            <div class="card">
                <div class="card-body">

                    <!-- جدول الإشعارات -->
                    <div class="table-responsive text-nowrap">
                        <table class="table table-striped text-black text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.Message') }}</th>
                                    <th>{{ __('messages.Created_at') }}</th>
                                    <th>{{ __('messages.Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($notifications as $notification)
                                    @php
                                        $data = is_array($notification->data)
                                            ? $notification->data
                                            : json_decode($notification->data, true);
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $data['message'] ?? '—' }}</td>
                                        <td>{{ $notification->created_at->diffForHumans() }}</td>
                                        <td>
                                            <form action="{{ route('Admin.notifications.delete', $notification->id) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('هل أنت متأكد من حذف الإشعار؟')">
                                                    <i class="bx bx-trash"></i>
                                                    {{ __('messages.delete') }}
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

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $notifications->links('pagination::bootstrap-4') }}
                    </div>

                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">{{ __('messages.Back') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // بحث تلقائي
            const input = document.getElementById('searchInput');
            const form = document.getElementById('searchForm');
            let timer = null;

            input.addEventListener('input', function() {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    form.submit();
                }, 500);
            });

            // حذف الإشعار
            document.querySelectorAll('.delete-notification-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    Swal.fire({
                        title: '{{ __('Are you sure?') }}',
                        text: '{{ __('Do you want to delete this item') }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '{{ __('Yes, delete it!') }}',
                        cancelButtonText: '{{ __('Cancel') }}',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.action = '/Admin.notifications.delete/' + id; // تعديل هنا
                            form.method = 'POST';

                            form.innerHTML = `
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                `;

                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });

        });
    </script>
@endpush
