@php
use Illuminate\Support\Facades\Route;
@endphp

@endphp

<!--APP-SIDEBAR-->
<div class="sticky">
    <div class="app-sidebar__overlay" data-bs-toggle="sidebar"></div>
    <div class="app-sidebar deep-dark-glass">
        <!-- Mac-style Control Dots -->


        <!-- <div class="side-header">
            <a class="header-brand1" href="{{ route('admin.dashboard') }}">
                <img src="{{ asset(settings('thumbnail') ?: 'backend/images/brand/logo.png') }}" id="sidebar-logo-img" alt="logo">
            </a>
        </div> -->

        <!-- Sidebar Search -->

        <div class="main-sidemenu">
            <ul id="customMenulist" class="side-menu"></ul>
            <ul class="side-menu mt-2">

                <li class="sidebar-category mt-4 mb-2">
                    <span class="text-muted fw-bold" style="font-size: 11px; letter-spacing: 1px; padding-left: 20px;">MAIN</span>
                </li>
                <li class="slide">
                    <a class="side-menu__item {{ request()->routeIs('dashboard') ? 'has-link active' : '' }}"
                        href="{{ route('admin.dashboard') }}">
                        <i class="fa-solid fa-house side-menu__icon"></i>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>








                <li class="slide">
                    <a class="side-menu__item {{ request()->routeIs('admin.property.*') ? 'has-link active' : '' }}"
                        href="{{ route('admin.property.index') }}">
                        <i class="fa-solid fa-building side-menu__icon"></i>
                        <span class="side-menu__label">Property List</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item {{ request()->routeIs('admin.page.*') ? 'has-link active' : '' }}"
                        href="{{ route('admin.page.index') }}">
                        <i class="fa-solid fa-file side-menu__icon"></i>
                        <span class="side-menu__label">Dynamic Page</span>
                    </a>
                </li>




                <li class="sidebar-category mt-4 mb-2">
                    <span class="text-muted fw-bold" style="font-size: 11px; letter-spacing: 1px; padding-left: 20px;">SYSTEM</span>
                </li>
                @role('admin')
                <li class="slide">
                    <a class="side-menu__item {{ request()->routeIs('admin.setting.*') ? 'has-link active' : '' }}"
                        data-bs-toggle="slide" href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 512 512">
                            <path
                                d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z" />
                        </svg>
                        <span class="side-menu__label">Settings</span><i class="angle fa fa-angle-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a href="{{ route('admin.setting.general.index') }}" class="slide-item"><i class="fa-solid fa-sliders-h me-2"></i> General Settings</a></li>

                        <li><a href="{{ route('admin.setting.logo.index') }}" class="slide-item"><i class="fa-solid fa-palette me-2"></i> Logo Settings</a></li>
                        <li><a href="{{ route('admin.setting.profile.index') }}" class="slide-item"><i class="fa-solid fa-user-gear me-2"></i> Profile Settings</a></li>
                        <li><a href="{{ route('admin.setting.mail.index') }}" class="slide-item"><i class="fa-solid fa-envelope me-2"></i> Mail Settings</a></li>


                        <li><a href="{{ route('admin.setting.social.index') }}" class="slide-item"><i class="fa-solid fa-share-nodes me-2"></i> Social Settings</a></li>
                        <li><a href="{{ route('admin.setting.google.map.index') }}" class="slide-item"><i class="fa-solid fa-map-location-dot me-2"></i> Google Map Settings</a></li>


                       
                    </ul>
                </li>
                @endrole





                <li class="slide">
                    <hr />
                </li>
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa-solid fa-power-off side-menu__icon"></i>
                        <span class="side-menu__label">Log out</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
                <li class="slide">
                    <hr />
                </li>
            </ul>
            <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191"
                    width="24" height="24" viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z" />
                </svg>
            </div>
        </div>
        <!-- Sidebar User Profile Card (Bottom) -->
        <div class="sidebar-user-header floating-profile-card bg-light border">
            <div class="user-avatar-initials bg-warning text-white">
                {{ substr(auth()->user()->name, 0, 1) }}{{ substr(strrchr(auth()->user()->name, " "), 1, 1) ?: '' }}
            </div>
            <div class="user-info">
                <h6 class="user-name text-dark fw-bold mb-0">{{ auth()->user()->name }}</h6>
                <p class="user-email text-muted mb-0" style="font-size: 11px;">{{ auth()->user()->email }}</p>
            </div>
            <i class="fa fa-chevron-up ms-auto text-muted" style="font-size: 10px;"></i>
        </div>
    </div>
</div>
<!--/APP-SIDEBAR-->

<script>
    const menuSearchInput = document.getElementById('menuSearching');
    const customMenuList = document.getElementById('customMenulist');

    function sideMenu() {
        menus.forEach(menu => {
            if (menu.name.toLowerCase().includes(menuSearchInput.value.toLowerCase())) {
                customMenuList.innerHTML += `
                    <li class="slide">
                        <a class="side-menu__item" href="#">
                            <i class="fa-solid fa-bars side-menu__icon"></i>
                            <span class="side-menu__label">${menu.name}</span>
                        </a>
                    </li>
                `;
            }
        });
    }

    menuSearchInput.addEventListener('input', function() {
        customMenuList.innerHTML = '';
        if (menuSearchInput.value.trim() === '') {
            customMenuList.innerHTML = '';
        } else {
            sideMenu();
        }
    });
</script>