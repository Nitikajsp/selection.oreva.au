<aside class="sidebar">
    <div class="nav-logo-wrap">
        <a href="{{ route('home') }}" class="app-brand-link">
            <img class="sidebar-logo" src="{{ asset('/' . get_setting('logo')) }}" alt="Logo">
        </a>
    </div>
    <hr class="text-secondary my-0" />

    <ul class="menu-inner align-items-center">
        <!-- Home -->
        <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <a href="{{ route('home') }}" class="customnav-link d-flex flex-column align-items-center text-white">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                    <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                </svg>
                <span class="text-small" style="font-size: 12px; ">Home</span>
            </a>
        </li>

        <!-- Users -->
        <li class="nav-item {{ request()->routeIs('customers.index') ? 'active' : '' }}">
            <a href="{{ route('customers.index') }}" class="customnav-link d-flex flex-column align-items-center text-white">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                </svg>
                <span class="text-small" style="font-size: 12px; ">Customers</span>
            </a>
        </li>

        <!-- Builder -->
        <li class="nav-item {{ request()->routeIs('user_builders.index') ? 'active' : '' }}">
            <a href="{{ route('user_builders.index') }}" class="customnav-link d-flex flex-column align-items-center text-white">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                </svg>
                <span class="text-small" style="font-size: 12px; ">Builder</span>
            </a>
        </li>


        <!-- Products -->
        <li class="nav-item {{ request()->routeIs('showproduct') ? 'active' : '' }}">
            <a href="{{ route('showproduct') }}" class="customnav-link d-flex flex-column align-items-center text-white">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M3 9a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-9z" />
                    <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" />
                </svg>
                <span class="text-small" style="font-size: 12px; ">Products</span>
            </a>
        </li>

        <!-- Categories -->
        <li class="nav-item {{ request()->routeIs('showcategory') ? 'active' : '' }}">
            <a href="{{ route('showcategory') }}" class="customnav-link d-flex flex-column align-items-center text-white">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M11.5 21h-2.926a3 3 0 0 1 -2.965 -2.544l-1.255 -8.152a2 2 0 0 1 1.977 -2.304h11.339a2 2 0 0 1 1.977 2.304l-.5 3.248" />
                    <path d="M9 11v-5a3 3 0 0 1 6 0v5" />
                    <path d="M15 19l2 2l4 -4" />
                </svg>
                <span class="text-small" style="font-size: 12px; ">Categories</span>
            </a>
        </li>

        <!-- Settings -->
        <li class="nav-item {{ request()->routeIs('settings.index') ? 'active' : '' }}">
            <a href="{{ route('settings.index') }}" class="customnav-link d-flex flex-column align-items-center text-white">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                    <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                </svg>
                <span class="text-small" style="font-size: 12px; ">Settings</span>
            </a>
        </li>

        <!-- Logout -->
        {{-- <li class="nav-item">
      <a href="{{ route('logout') }}" class="customnav-link d-flex flex-column align-items-center text-white" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
            <path d="M7 12l5 5l5 -5" />
        </svg>
        <span class="text-small" style="font-size: 12px; ">Logout</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        </li> --}}
        <li class="nav-item">
            <a href="#" class="customnav-link d-flex flex-column align-items-center text-white" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                    <path d="M7 12l5 5l5 -5" />
                </svg>
                <span class="text-small" style="font-size: 12px; cursor: pointer;">
                    Logout
                </span>
            </a>
        </li>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>

        <!-- Logout Confirmation Modal -->
        <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to logout?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmLogoutBtn">Logout</button>
                    </div>
                </div>
            </div>
        </div>


    </ul>

    <script>
        $(document).ready(function() {
            $('#confirmLogoutBtn').on('click', function() {
                $('#logout-form').submit();
            });
        });

    </script>
</aside>
