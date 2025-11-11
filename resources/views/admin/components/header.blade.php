@php
    $segment = Request::segment(2);
    $child_segment = Request::segment(3);
@endphp

<header class="top-header">
    <nav class="navbar navbar-expand gap-3">
        <div class="mobile-toggle-icon fs-3">
            <i class="bi bi-list"></i>
        </div>

        <div class="top-navbar-right ms-auto">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item search-toggle-icon">
                    <a class="nav-link" href="#">
                        <div class="">
                            <i class="bi bi-search"></i>
                        </div>
                    </a>
                </li>

                @if (admin()->user()->role_id == \App\Models\Role::SUPERADMIN)
                    <li class="nav-item">
                        <style scoped>
                            .form-check-input:checked {
                                background-color: #7daef6;
                                border-color: #7daef6;
                            }
                        </style>
                        <a class="nav-link" href="{{ route('admin.session.mode') }}">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" style="cursor: pointer;" onclick="{window.location = '{{ route('admin.session.mode') }}'}" id="flexSwitchCheckDefault" {{ Request::session()->get('is_test') ? 'checked' : '' }}>  
                                <label class="form-check-label" for="flexSwitchCheckDefaultdf" style="font-size: 14px;">Test Mode</label>
                            </div>
                        </a>
                    </li>
                @endif

                <li class="nav-item dropdown dropdown-user-setting">
                    <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                        <div class="user-setting d-flex align-items-center">
                            @if (!empty(admin()->user()->image))
                                <img src="{{ getImagePath(admin()->user()->image, 'profile') }}" class="user-img" alt="">
                            @else
                                <img src="{{ loadAssets('images/avatars/avatar-1.png') }}" class="user-img" alt="">
                            @endif
                            <h6 class="mb-0 dropdown-user-name" style="margin-left: 8px;color:#000;">{{ Str::ucfirst(admin()->user()->name) }}</h6>
                        </div>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">
                        {{-- <li>
                            <a class="dropdown-item" href="#">
                                <div class="d-flex align-items-center">
                                    @if (!empty(admin()->user()->image))
                                        <a href="{{ route('admin.profile') }}">
                                            <img src="{{ getImagePath(admin()->user()->image, 'profile') }}" alt="" class="rounded-circle" width="54" height="54">
                                        </a>
                                    @else
                                        <a href="{{ route('admin.profile') }}">
                                            <img src="{{ loadAssets('images/avatars/avatar-1.png') }}" alt="" class="rounded-circle" width="54" height="54">
                                        </a>
                                    @endif
                                    <div class="ms-3">
                                        <h6 class="mb-0 dropdown-user-name">{{ admin()->user()->name }}</h6>
                                    </div>
                                </div>
                            </a>
                        </li> --}}

                        {{-- <li>
                            <hr class="dropdown-divider">
                        </li> --}}
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.profile') }}">
                                <div class="d-flex align-items-center">
                                    <div class=""><i class="lni lni-user"></i></div>
                                    <div class="ms-3"><span>Profile</span></div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.profile') }}">
                                <div class="d-flex align-items-center">
                                    <div class=""><i class="lni lni-key"></i></div>
                                    <div class="ms-3"><span>Change password</span></div>
                                </div>
                            </a>
                        </li>

                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a class="dropdown-item" href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    <div class="d-flex align-items-center">
                                        <div class=""><i class="fa-solid fa-right-from-bracket"></i></div>
                                        <div class="ms-3"><span>Logout</span></div>
                                    </div>
                                </a>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>