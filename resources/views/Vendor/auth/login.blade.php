{{-- resources/views/vendor/auth/login.blade.php --}}
<!DOCTYPE html>
<html
    lang="ar"
    dir="rtl"
    class="light-style customizer-hide"
    data-theme="theme-default"
    data-assets-path="{{ asset('assets') }}/"
    data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>تسجيل الدخول للمتجر</title>

    <!-- Favicon (اختياري) -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- خطوط وأيقونات Sneat -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS (Sneat) -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- صفحة auth -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />

    <!-- Helpers & Config -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
</head>

<body>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Card -->
                <div class="card">
                    <div class="card-body">

                        <!-- لوجو/عنوان (اختياري: استبدل بصورة شعارك) -->
                        <div class="app-brand justify-content-center mb-2">
                            <a href="{{ url('/') }}" class="app-brand-link gap-2">
                                <span class="app-brand-text demo text-body fw-bolder">{{ config('app.name', 'Tix') }}</span>
                            </a>
                        </div>

                        <!-- عنوان -->
                        <h4 class="mb-2 text-center">تسجيل الدخول للتاجر</h4>

                        <!-- فورم تسجيل الدخول — نفس الداتا ونفس route -->
                        <form id="formAuthentication" class="mb-3" method="POST" action="{{ route('vendor.login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-user"></i></span>
                                    <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        placeholder="ادخل البريد الإلكتروني"
                                        value="{{ old('email') }}"
                                        required
                                        autofocus />
                                </div>
                            </div>

                            <div class="mb-3 form-password-toggle">
                                <label class="form-label" for="password">كلمة المرور</label>
                                <div class="input-group input-group-merge">
                                    <input
                                        type="password"
                                        id="password"
                                        class="form-control"
                                        name="password"
                                        placeholder="••••••••"
                                        required
                                        aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer" id="togglePassword">
                                        <i class="bx bx-hide" id="toggleIcon"></i>
                                    </span>
                                </div>
                            </div>

                            @if ($errors->any())
                            <div class="alert alert-danger text-center" role="alert">
                                {{ $errors->first() }}
                            </div>
                            @endif

                            <button class="btn btn-primary d-grid w-100" type="submit">تسجيل الدخول</button>
                        </form>
                        <!-- /فورم -->

                        <!-- جانب بصري (اختياري لو عايز جملة ترحيب أو صورة) -->
                        {{--
                        <div class="text-center mt-3">
                            <img src="{{ asset('template/images/MyTours.jpg') }}" alt="" width="120" class="rounded-2">
                        <div class="mt-2 text-muted">مرحباً بعودتك!</div>
                    </div>
                    --}}

                </div>
            </div>
            <!-- /Card -->
        </div>
    </div>
    </div>

    <!-- Core JS (Sneat) -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Toggle كلمة المرور (لو سكربت القالب مش مفعّل) -->
    <script>
        (function() {
            const toggleBtn = document.getElementById('togglePassword');
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (toggleBtn && input && icon) {
                toggleBtn.addEventListener('click', function() {
                    const isPassword = input.getAttribute('type') === 'password';
                    input.setAttribute('type', isPassword ? 'text' : 'password');
                    icon.classList.toggle('bx-hide', !isPassword);
                    icon.classList.toggle('bx-show', isPassword);
                });
            }
        })();
    </script>
</body>

</html>