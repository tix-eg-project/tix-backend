{{-- resources/views/Vendor/pages/profile/updateProfile.blade.php --}}
@extends('Vendor.layout.app')

@section('title', 'تعديل البروفايل')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">
        <div class="col-12 col-lg-8 mx-auto">
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">تعديل البروفايل</h5>
                    <a href="{{ route('vendor.dashboard') }}" class="btn btn-sm btn-outline-secondary">رجوع للداشبورد</a>
                </div>

                <div class="card-body">
                    <form action="{{ route('vendor.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- اسم صاحب الشركة --}}
                        <div class="mb-3">
                            <label class="form-label">اسم صاحب الشركة</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $vendor->name) }}" required>
                            @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        {{-- الوصف --}}
                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $vendor->description) }}</textarea>
                            @error('description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        {{-- ✳️ كلمة المرور الجديدة (اختياري) --}}
                        <div class="mb-3">
                            <label class="form-label">كلمة المرور الجديدة (اختياري)</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••">
                            <small class="text-muted d-block mt-1">اتركها فارغة إذا لا تريد تغيير كلمة المرور.</small>
                            @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        {{-- الصورة --}}
                        <div class="mb-3">
                            <label class="form-label d-block">الصورة الشخصية</label>

                            @if($vendor->image)
                            <div class="mb-2">
                                <img id="previewImage" src="{{ asset($vendor->image) }}" alt="vendor image" style="max-height:120px">
                            </div>
                            @else
                            <img id="previewImage" src="" alt="" style="display:none;max-height:120px">
                            @endif

                            <input type="file" id="imageInput" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            <small class="text-muted d-block mt-1">حد أقصى 4MB — الامتدادات: jpeg, png, jpg, gif, webp</small>
                            @error('image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('vendor.dashboard') }}" class="btn btn-outline-secondary">رجوع</a>
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // معاينة الصورة
    const input = document.getElementById('imageInput');
    const preview = document.getElementById('previewImage');
    if (input) {
        input.addEventListener('change', (e) => {
            const file = e.target.files && e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (ev) => {
                preview.src = ev.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    }
</script>
@endpush
