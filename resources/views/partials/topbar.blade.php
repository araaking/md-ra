<div class="topbar-custom">
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <!-- Left Side Menu -->
            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                <!-- Sidebar Toggle Button -->
                <li>
                    <button class="button-toggle-menu nav-link">
                        <i data-feather="menu" class="noti-icon"></i>
                    </button>
                </li>
                
                <!-- Greeting -->
                <li class="d-none d-lg-block">
                    <h5 class="mb-0">Hi, Admin!</h5>
                </li>
            </ul>

            <!-- Right Side Menu -->
            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                
                <!-- Fullscreen Toggle -->
                <li class="d-none d-sm-flex">
                    <button type="button" 
                            class="btn nav-link" 
                            data-toggle="fullscreen">
                        <i data-feather="maximize" 
                           class="align-middle fullscreen noti-icon"></i>
                    </button>
                </li>

                <!-- Dark/Light Mode Toggle -->
                <li class="d-none d-sm-flex">
                    <button type="button" 
                            class="btn nav-link" 
                            id="light-dark-mode">
                        <i data-feather="moon" 
                           class="align-middle dark-mode"></i>
                        <i data-feather="sun" 
                           class="align-middle light-mode"></i>
                    </button>
                </li>
                
                <!-- User Profile Dropdown -->
                <li class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle nav-user me-0" 
                       data-bs-toggle="dropdown" 
                       href="#" 
                       role="button">
                        <img src="{{ asset('assets/images/users/user-13.jpg') }}" 
                             alt="user-image" 
                             class="rounded-circle" />
                        <span class="pro-user-name ms-1">
                            Alex <i class="mdi mdi-chevron-down"></i>
                        </span>
                    </a>
                    
                    <!-- Dropdown Menu -->
                    <div class="dropdown-menu dropdown-menu-end profile-dropdown">
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Welcome!</h6>
                        </div>
                        
                        <!-- Profile Link -->
                        <a href="pages-profile.html" 
                           class="dropdown-item notify-item">
                            <i class="mdi mdi-account-circle-outline fs-16 align-middle"></i>
                            <span>My Account</span>
                        </a>
                        
                        <!-- Divider -->
                        <div class="dropdown-divider"></div>
                        
                        <!-- Logout Link -->
                        <a href="{{ route('logout') }}" 
                           class="dropdown-item notify-item" 
                           onclick="event.preventDefault(); 
                                  document.getElementById('logout-form').submit();">
                            <i class="mdi mdi-location-exit fs-16 align-middle"></i>
                            <span>Logout</span>
                        </a>
                        
                        <!-- Hidden Logout Form -->
                        <form id="logout-form" 
                              action="{{ route('logout') }}" 
                              method="POST" 
                              class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
