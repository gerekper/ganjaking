@php
$containerNav = $containerNav ?? 'container-xxl';
$navbarDetached = ($navbarDetached ?? '');
@endphp

<!-- Navbar -->
@if(isset($navbarDetached) && $navbarDetached == 'navbar-detached')
<nav class="layout-navbar {{$containerNav}} navbar navbar-expand-xl {{$navbarDetached}} align-items-center bg-navbar-theme" id="layout-navbar">
  @endif
  @if(isset($navbarDetached) && $navbarDetached == '')
<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="{{$containerNav}}">
    @endif

    <!--  Brand demo (display only for navbar-full and hide on below xl) -->
    @if(isset($navbarFull))
    <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
      <a href="{{url('/')}}" class="app-brand-link gap-2">
        <span class="app-brand-logo demo">
          @include('_partials.macros',["width"=>25,"withbg"=>'#666cff'])
        </span>
        <span class="app-brand-text demo menu-text fw-bold">{{config('variables.templateName')}}</span>
      </a>
    </div>
    @endif

    <!-- ! Not required for layout-without-menu -->
    @if(!isset($navbarHideToggle))
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ?' d-xl-none ' : '' }}">
      <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
        <i class="mdi mdi-menu mdi-24px"></i>
      </a>
    </div>
    @endif

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

      @if(!isset($menuHorizontal))
      <!-- Search -->
      <div class="navbar-nav align-items-center">
        <div class="nav-item navbar-search-wrapper mb-0">
          <a class="nav-item nav-link search-toggler fw-normal px-0" href="javascript:void(0);">
            <i class="mdi mdi-magnify mdi-24px scaleX-n1-rtl"></i>
            <span class="d-none d-md-inline-block text-muted">Search (Ctrl+/)</span>
          </a>
        </div>
      </div>
      <!-- /Search -->
      @endif

      <ul class="navbar-nav flex-row align-items-center ms-auto">
        @if(isset($menuHorizontal))
        <!-- Search -->
        <li class="nav-item navbar-search-wrapper me-2 me-xl-0">
          <a class="nav-link search-toggler" href="javascript:void(0);">
            <i class="mdi mdi-magnify mdi-24px scaleX-n1-rtl"></i>
          </a>
        </li>
        <!-- /Search -->
        @endif
        <!-- Language -->
        <li class="nav-item dropdown-language dropdown me-1 me-xl-0">
          <a class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
            <i class='mdi mdi-translate mdi-24px'></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item" href="{{url('lang/en')}}" data-language="en">
                <span class="align-middle">English</span>
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="{{url('lang/fr')}}" data-language="fr">
                <span class="align-middle">French</span>
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="{{url('lang/de')}}" data-language="de">
                <span class="align-middle">German</span>
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="{{url('lang/pt')}}" data-language="pt">
                <span class="align-middle">Portuguese</span>
              </a>
            </li>
          </ul>
        </li>
        <!--/ Language -->

        <!-- Style Switcher -->
        <li class="nav-item me-1 me-xl-0">
          <a class="nav-link btn btn-text-secondary rounded-pill btn-icon style-switcher-toggle hide-arrow" href="javascript:void(0);">
            <i class='mdi mdi-24px'></i>
          </a>
        </li>
        <!--/ Style Switcher -->

        <!-- Quick links  -->
        <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-1 me-xl-0">
          <a class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
            <i class='mdi mdi-view-grid-plus-outline mdi-24px'></i>
          </a>
          <div class="dropdown-menu dropdown-menu-end py-0">
            <div class="dropdown-menu-header border-bottom">
              <div class="dropdown-header d-flex align-items-center py-3">
                <h5 class="text-body mb-0 me-auto">Shortcuts</h5>
                <a href="javascript:void(0)" class="dropdown-shortcuts-add text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Add shortcuts"><i class="mdi mdi-view-grid-plus-outline mdi-24px"></i></a>
              </div>
            </div>
            <div class="dropdown-shortcuts-list scrollable-container">
              <div class="row row-bordered overflow-visible g-0">
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-calendar fs-4"></i>
                  </span>
                  <a href="{{url('app/calendar')}}" class="stretched-link">Calendar</a>
                  <small class="text-muted mb-0">Appointments</small>
                </div>
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-file-document-outline fs-4"></i>
                  </span>
                  <a href="{{url('app/invoice/list')}}" class="stretched-link">Invoice App</a>
                  <small class="text-muted mb-0">Manage Accounts</small>
                </div>
              </div>
              <div class="row row-bordered overflow-visible g-0">
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-account-outline fs-4"></i>
                  </span>
                  <a href="{{url('app/user/list')}}" class="stretched-link">User App</a>
                  <small class="text-muted mb-0">Manage Users</small>
                </div>
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-shield-check-outline fs-4"></i>
                  </span>
                  <a href="{{url('app/access-roles')}}" class="stretched-link">Role Management</a>
                  <small class="text-muted mb-0">Permission</small>
                </div>
              </div>
              <div class="row row-bordered overflow-visible g-0">
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-chart-pie-outline fs-4"></i>
                  </span>
                  <a href="{{url('/')}}" class="stretched-link">Dashboard</a>
                  <small class="text-muted mb-0">User Profile</small>
                </div>
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-cog-outline fs-4"></i>
                  </span>
                  <a href="{{url('pages/account-settings-account')}}" class="stretched-link">Setting</a>
                  <small class="text-muted mb-0">Account Settings</small>
                </div>
              </div>
              <div class="row row-bordered overflow-visible g-0">
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-help-circle-outline fs-4"></i>
                  </span>
                  <a href="{{url('pages/help-center-landing')}}" class="stretched-link">Help Center</a>
                  <small class="text-muted mb-0">FAQs & Articles</small>
                </div>
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-dock-window fs-4"></i>
                  </span>
                  <a href="{{url('modal-examples')}}" class="stretched-link">Modals</a>
                  <small class="text-muted mb-0">Useful Popups</small>
                </div>
              </div>
            </div>
          </div>
        </li>
        <!-- Quick links -->

        <!-- Notification -->
        <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-2 me-xl-1">
          <a class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
            <i class="mdi mdi-bell-outline mdi-24px"></i>
            <span class="position-absolute top-0 start-50 translate-middle-y badge badge-dot bg-danger mt-2 border"></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end py-0">
            <li class="dropdown-menu-header border-bottom">
              <div class="dropdown-header d-flex align-items-center py-3">
                <h6 class="mb-0 me-auto">Notification</h6>
                <span class="badge rounded-pill bg-label-primary">8 New</span>
              </div>
            </li>
            <li class="dropdown-notifications-list scrollable-container">
              <ul class="list-group list-group-flush">
                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                  <div class="d-flex gap-2">
                    <div class="flex-shrink-0">
                      <div class="avatar me-1">
                        <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle">
                      </div>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                      <h6 class="mb-1 text-truncate">Congratulation Lettie 🎉</h6>
                      <small class="text-truncate text-body">Won the monthly best seller gold badge</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                      <small class="text-muted">1h ago</small>
                    </div>
                  </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                  <div class="d-flex gap-2">
                    <div class="flex-shrink-0">
                      <div class="avatar me-1">
                        <span class="avatar-initial rounded-circle bg-label-danger">CF</span>
                      </div>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                      <h6 class="mb-1 text-truncate">Charles Franklin</h6>
                      <small class="text-truncate text-body">Accepted your connection</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                      <small class="text-muted">12hr ago</small>
                    </div>
                  </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                  <div class="d-flex gap-2">
                    <div class="flex-shrink-0">
                      <div class="avatar me-1">
                        <img src="{{ asset('assets/img/avatars/2.png') }}" alt class="h-auto rounded-circle">
                      </div>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                      <h6 class="mb-1 text-truncate">New Message ✉️</h6>
                      <small class="text-truncate text-body">You have new message from Natalie</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                      <small class="text-muted">1h ago</small>
                    </div>
                  </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                  <div class="d-flex gap-2">
                    <div class="flex-shrink-0">
                      <div class="avatar me-1">
                        <span class="avatar-initial rounded-circle bg-label-success"><i class="mdi mdi-cart-outline"></i></span>
                      </div>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                      <h6 class="mb-1 text-truncate">Whoo! You have new order 🛒 </h6>
                      <small class="text-truncate text-body">ACME Inc. made new order $1,154</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                      <small class="text-muted">1 day ago</small>
                    </div>
                  </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                  <div class="d-flex gap-2">
                    <div class="flex-shrink-0">
                      <div class="avatar me-1">
                        <img src="{{ asset('assets/img/avatars/9.png') }}" alt class="w-px-40 h-auto rounded-circle">
                      </div>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                      <h6 class="mb-1 text-truncate">Application has been approved 🚀 </h6>
                      <small class="text-truncate text-body">Your ABC project application has been approved.</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                      <small class="text-muted">2 days ago</small>
                    </div>
                  </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                  <div class="d-flex gap-2">
                    <div class="flex-shrink-0">
                      <div class="avatar me-1">
                        <span class="avatar-initial rounded-circle bg-label-success"><i class="mdi mdi-chart-pie-outline"></i></span>
                      </div>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                      <h6 class="mb-1 text-truncate">Monthly report is generated</h6>
                      <small class="text-truncate text-body">July monthly financial report is generated </small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                      <small class="text-muted">3 days ago</small>
                    </div>
                  </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                  <div class="d-flex gap-2">
                    <div class="flex-shrink-0">
                      <div class="avatar me-1">
                        <img src="{{ asset('assets/img/avatars/5.png') }}" alt class="h-auto rounded-circle">
                      </div>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                      <h6 class="mb-1 text-truncate">Send connection request</h6>
                      <small class="text-truncate text-body">Peter sent you connection request</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                      <small class="text-muted">4 days ago</small>
                    </div>
                  </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                  <div class="d-flex gap-2">
                    <div class="flex-shrink-0">
                      <div class="avatar me-1">
                        <img src="{{ asset('assets/img/avatars/6.png') }}" alt class="h-auto rounded-circle">
                      </div>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                      <h6 class="mb-1 text-truncate">New message from Jane</h6>
                      <small class="text-truncate text-body">Your have new message from Jane</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                      <small class="text-muted">5 days ago</small>
                    </div>
                  </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                  <div class="d-flex gap-2">
                    <div class="flex-shrink-0">
                      <div class="avatar me-1">
                        <span class="avatar-initial rounded-circle bg-label-warning"><i class="mdi mdi-alert-circle-outline"></i></span>
                      </div>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                      <h6 class="mb-1">CPU is running high</h6>
                      <small class="text-truncate text-body">CPU Utilization Percent is currently at 88.63%,</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                      <small class="text-muted">5 days ago</small>
                    </div>
                  </div>
                </li>
              </ul>
            </li>
            <li class="dropdown-menu-footer border-top p-2">
              <a href="javascript:void(0);" class="btn btn-primary d-flex justify-content-center">
                View all notifications
              </a>
            </li>
          </ul>
        </li>
        <!--/ Notification -->

        <!-- User -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
          <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
            <div class="avatar avatar-online">
              <img src="{{ Auth::user() ? Auth::user()->profile_photo_url : asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle">
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item" href="{{ Route::has('profile.show') ? route('profile.show') : url('pages/profile-user') }}">
                <div class="d-flex">
                  <div class="flex-shrink-0 me-3">
                    <div class="avatar avatar-online">
                      <img src="{{ Auth::user() ? Auth::user()->profile_photo_url : asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle">
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <span class="fw-semibold d-block">
                      @if (Auth::check())
                      {{ Auth::user()->name }}
                      @else
                      John Doe
                      @endif
                    </span>
                    <small class="text-muted">Admin</small>
                  </div>
                </div>
              </a>
            </li>
            <li>
              <div class="dropdown-divider"></div>
            </li>
            <li>
              <a class="dropdown-item" href="{{ Route::has('profile.show') ? route('profile.show') : url('pages/profile-user') }}">
                <i class="mdi mdi-account-outline me-2"></i>
                <span class="align-middle">My Profile</span>
              </a>
            </li>
            @if (Auth::check() && Laravel\Jetstream\Jetstream::hasApiFeatures())
            <li>
              <a class="dropdown-item" href="{{ route('api-tokens.index') }}">
                <i class='mdi mdi-key-outline me-2'></i>
                <span class="align-middle">API Tokens</span>
              </a>
            </li>
            @endif
            <li>
              <a class="dropdown-item" href="{{url('app/invoice/list')}}">
                <i class="mdi mdi-credit-card-outline me-2"></i>
                <span class="align-middle">Billing</span>
              </a>
            </li>
            @if (Auth::User() && Laravel\Jetstream\Jetstream::hasTeamFeatures())
            <li>
              <div class="dropdown-divider"></div>
            </li>
            <li>
              <h6 class="dropdown-header">Manage Team</h6>
            </li>
            <li>
              <div class="dropdown-divider"></div>
            </li>
            <li>
              <a class="dropdown-item" href="{{ Auth::user() ? route('teams.show', Auth::user()->currentTeam->id) : 'javascript:void(0)' }}">
                <i class='mdi mdi-cog-outline me-2'></i>
                <span class="align-middle">Team Settings</span>
              </a>
            </li>
            @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
            <li>
              <a class="dropdown-item" href="{{ route('teams.create') }}">
                <i class='mdi mdi-account-outline me-2'></i>
                <span class="align-middle">Create New Team</span>
              </a>
            </li>
            @endcan
            <li>
              <div class="dropdown-divider"></div>
            </li>
            <lI>
              <h6 class="dropdown-header">Switch Teams</h6>
            </lI>
            <li>
              <div class="dropdown-divider"></div>
            </li>
            @if (Auth::user())
            @foreach (Auth::user()->allTeams() as $team)
            {{-- Below commented code read by artisan command while installing jetstream. !! Do not remove if you want to use jetstream. --}}

            {{-- <x-switchable-team :team="$team" /> --}}
            @endforeach
            @endif
            @endif
            <li>
              <div class="dropdown-divider"></div>
            </li>
            @if (Auth::check())
            <li>
              <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class='mdi mdi-logout me-2'></i>
                <span class="align-middle">Logout</span>
              </a>
            </li>
            <form method="POST" id="logout-form" action="{{ route('logout') }}">
              @csrf
            </form>
            @else
            <li>
              <a class="dropdown-item" href="{{ Route::has('login') ? route('login') : url('auth/login-basic') }}">
                <i class='mdi mdi-login me-2'></i>
                <span class="align-middle">Login</span>
              </a>
            </li>
            @endif
          </ul>
        </li>
        <!--/ User -->
      </ul>
    </div>

    <!-- Search Small Screens -->
    <div class="navbar-search-wrapper search-input-wrapper {{ isset($menuHorizontal) ? $containerNav : '' }} d-none">
      <input type="text" class="form-control search-input {{ isset($menuHorizontal) ? '' : $containerNav }} border-0" placeholder="Search..." aria-label="Search...">
      <i class="mdi mdi-close search-toggler cursor-pointer"></i>
    </div>
    @if(!isset($navbarDetached))
  </div>
  @endif
</nav>
<!-- / Navbar -->
