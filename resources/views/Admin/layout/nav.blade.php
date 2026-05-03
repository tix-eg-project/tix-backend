<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

        <ul class="navbar-nav flex-row align-items-center ms-auto">

            {{-- الإشعارات --}}
            <li class="nav-item dropdown me-4">
                <a href="javascript:;"
                    class="dropdown-toggle position-relative d-flex align-items-center notificationsIcon"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell fs-5"></i>
                    @if (Auth::guard('admin')->user()->unreadNotifications()->count())
                        <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle"
                            id="notificationsIconCounter">
                            {{ Auth::user()->unreadNotifications()->count() }}
                        </span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-{{ in_array(App::getLocale(), ['en', 'fr']) ? 'end' : 'start' }} shadow-lg"
                    style="width: 380px; max-height: 450px; overflow-y: auto; border-radius: 8px;">
                    <div class="dropdown-header bg-primary text-white text-center py-2">
                        <strong>{{ __('Notifications') }}</strong>
                    </div>
                    <div class="list-group" id="notificationsModal">
                        @forelse(Auth::guard('admin')->user()->notifications()->orderBy('created_at', 'desc')->take(5)->get() as $notification)
                            @php
                                $url = $notification->data['url'] ?? '#';
                                $message = $notification->data['message'] ?? 'رسالة غير متوفرة';
                            @endphp

                            <a href="{{ $url . '?notification_id=' . $notification->id }}"
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center mark-as-read"
                                data-id="{{ $notification->id }}">

                                <div class="w-100 me-2">
                                    <div class="d-flex justify-content-between">
                                        <p
                                            class="mb-1 {{ !$notification->read_at ? 'text-dark fw-bold' : 'text-muted' }}">
                                            {{ $message }}
                                        </p>
                                        <small class="text-muted">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>

                                <i class="fas fa-user-circle text-primary fs-4"></i>
                            </a>
                        @empty
                            <div class="text-center text-muted py-3">
                                {{ __('No new notifications') }}
                            </div>
                        @endforelse

                    </div>
                    {{-- @if ($notifications->count()) --}}
                    @php
                        $notificationsCount = auth()->user()->notifications()->count();
                    @endphp

                    @if ($notificationsCount)
                        <div class="text-center p-2 border-top">
                            <a href="{{ route('Admin.notifications.markAllRead') }}"
                                class="btn btn-sm btn-outline-primary me-2">{{ __('Read All') }}</a>
                            <a href="{{ route('Admin.notifications') }}"
                                class="btn btn-sm btn-primary">{{ __('Index All') }}</a>
                        </div>
                    @endif
                    {{-- @endif --}}
                </div>
            </li>



            <!-- اللغة -->
            <li class="dropdown nav-item lh-1 me-3">
                <button class="dropdown-toggle bg-transparent border-0" data-bs-toggle="dropdown" aria-expanded="false"
                    aria-label="Language Menu">
                    <i class="fas fa-globe fs-5"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ LaravelLocalization::getLocalizedURL('en') }}">
                            <img src="{{ asset('assets/img/flags/us_flag.jpg') }}" alt="English" width="20"
                                class="me-2">
                            {{ __('messages.English') }}
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ LaravelLocalization::getLocalizedURL('ar') }}">
                            <img src="{{ asset('assets/img/flags/egypt_flag.jpg') }}" alt="Arabic" width="20"
                                class="me-2">
                            {{ __('messages.Arabic') }}
                        </a>
                    </li>
                </ul>
            </li>




            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                @php
                    $user = auth()->guard('admin')->check()
                        ? auth()->guard('admin')->user()
                        : (auth()->guard('vendore')->check()
                            ? auth()->guard('vendore')->user()
                            : (auth()->check()
                                ? auth()->user()
                                : null));
                @endphp

                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        @if ($user && $user->image)
                            <img src="{{ asset($user->image) }}" alt="User Avatar"
                                class="w-px-40 h-auto rounded-circle" style="object-fit: cover;" />
                        @else
                            <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Default Avatar"
                                class="w-px-40 h-auto rounded-circle" style="object-fit: cover;" />
                        @endif
                    </div>
                </a>




                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">

                                <div class="flex-grow-1">
                                    @if ($user)
                                        <span class="fw-semibold d-block">{{ $user->name }}</span>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.profile') }}">
                            <i class="bx bx-user me-2"></i>
                            <span class="align-middle">{{ __('messages.My Profile') }}</span>
                        </a>
                    </li>

                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item bg-transparent border-0">
                                <i class="bx bx-power-off me-2"></i>
                                {{ __('messages.LogOut') }}
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>
