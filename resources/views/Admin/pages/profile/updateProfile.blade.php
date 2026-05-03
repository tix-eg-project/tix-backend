@extends('Admin.layout.app')

@section('myProfile_active', 'active')

@section('content')
    <style>
        .center-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 200px);
            padding: 2rem;
            width: 100%;
        }

        .profile-card {
            background: #fff;
            border-radius: 0.5rem;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 700px;
        }

        .form-text {
            font-size: 0.875em;
            color: #6c757d;
        }
    </style>

    <div class="center-wrapper">
        <div class="card profile-card">
            <div class="card-header text-center">
                <h5 class="mb-0">{{ Auth::user()->name }}</h5>
            </div>

            <div class="card-body">
                <form method="post" action="{{ route('admin.updateProfile') }}" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.Name') }}</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}"
                            placeholder="{{ __('Enter your name') }}">
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.Email') }}</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}"
                            placeholder="{{ __('Enter your email') }}">
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.Phone') }}</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}"
                            placeholder="{{ __('Enter your phone number') }}">
                        @error('phone')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- ✳️ حقل الباسورد الجديد (اختياري) --}}
                    <div class="mb-3">
                        <label class="form-label">كلمة المرور الجديدة (اختياري)</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••">
                        @error('password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                        <small class="form-text">اتركها فارغة لو مش عايز تغيّر الباسورد.</small>
                    </div>

                    {{-- Image --}}
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.image') }}</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                            accept="image/*">
                        @error('image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        {{-- Check if image exists and display it --}}
                        @if ($user->image)
                            <div class="mt-2">
                                <img src="{{ asset($user->image) }}" width="80" alt="current image">
                                <small class="text-white d-block mt-1">{{ basename($user->image) }}</small>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-warning">{{ __('messages.Back') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('messages.Update') }}</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // معاينة الصورة فور اختيارها (الكود كما هو)
        document.getElementById('imageInput')?.addEventListener('change', function(event) {
            const image = event.target.files[0];
            if (image) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('previewImage');
                    if (preview) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                };
                reader.readAsDataURL(image);
            }
        });
    </script>
@endpush
