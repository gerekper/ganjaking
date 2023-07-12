@extends('layouts/layoutMaster')

@section('title', 'User Profile - Profile')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
@endsection

<!-- Page -->
@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
@endsection


@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/pages-profile.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">User Profile /</span> Profile
</h4>


<!-- Header -->
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="user-profile-header-banner">
        <img src="{{asset('assets/img/pages/profile-banner.png')}}" alt="Banner image" class="rounded-top">
      </div>
      <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
          <img src="{{asset('assets/img/avatars/1.png')}}" alt="user image" class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img">
        </div>
        <div class="flex-grow-1 mt-3 mt-sm-5">
          <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
            <div class="user-profile-info">
              <h4>John Doe</h4>
              <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                <li class="list-inline-item">
                  <i class='mdi mdi-invert-colors me-1 mdi-20px'></i><span class="fw-semibold">UX Designer</span>
                </li>
                <li class="list-inline-item">
                  <i class='mdi mdi-map-marker-outline me-1 mdi-20px'></i><span class="fw-semibold">Vatican City</span>
                </li>
                <li class="list-inline-item">
                  <i class='mdi mdi-calendar-blank-outline me-1 mdi-20px'></i><span class="fw-semibold"> Joined April 2021</span></li>
              </ul>
            </div>
            <a href="javascript:void(0)" class="btn btn-primary">
              <i class='mdi mdi-account-check-outline me-1'></i>Connected
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Header -->

<!-- Navbar pills -->
<div class="row">
  <div class="col-md-12">
    <ul class="nav nav-pills flex-column flex-sm-row mb-4">
      <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i class='mdi mdi-account-outline me-1 mdi-20px'></i>Profile</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('pages/profile-teams')}}"><i class='mdi mdi-account-multiple-outline me-1 mdi-20px'></i>Teams</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('pages/profile-projects')}}"><i class='mdi mdi-view-grid-outline me-1 mdi-20px'></i>Projects</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('pages/profile-connections')}}"><i class='mdi mdi-link me-1 mdi-20px'></i>Connections</a></li>
    </ul>
  </div>
</div>
<!--/ Navbar pills -->

<!-- User Profile Content -->
<div class="row">
  <div class="col-xl-4 col-lg-5 col-md-5">
    <!-- About User -->
    <div class="card mb-4">
      <div class="card-body">
        <small class="card-text text-uppercase text-muted">About</small>
        <ul class="list-unstyled my-3 py-1">
          <li class="d-flex align-items-center mb-3"><i class="mdi mdi-account-outline mdi-24px"></i><span class="fw-semibold mx-2">Full Name:</span> <span>John Doe</span></li>
          <li class="d-flex align-items-center mb-3"><i class="mdi mdi-check mdi-24px"></i><span class="fw-semibold mx-2">Status:</span> <span>Active</span></li>
          <li class="d-flex align-items-center mb-3"><i class="mdi mdi-star-outline mdi-24px"></i><span class="fw-semibold mx-2">Role:</span> <span>Developer</span></li>
          <li class="d-flex align-items-center mb-3"><i class="mdi mdi-flag-outline mdi-24px"></i><span class="fw-semibold mx-2">Country:</span> <span>USA</span></li>
          <li class="d-flex align-items-center mb-3"><i class="mdi mdi-translate mdi-24px"></i><span class="fw-semibold mx-2">Languages:</span> <span>English</span></li>
        </ul>
        <small class="card-text text-uppercase text-muted">Contacts</small>
        <ul class="list-unstyled my-3 py-1">
          <li class="d-flex align-items-center mb-3"><i class="mdi mdi-phone-outline mdi-24px"></i><span class="fw-semibold mx-2">Contact:</span> <span>(123) 456-7890</span></li>
          <li class="d-flex align-items-center mb-3"><i class="mdi mdi-message-outline mdi-24px"></i><span class="fw-semibold mx-2">Skype:</span> <span>john.doe</span></li>
          <li class="d-flex align-items-center mb-3"><i class="mdi mdi-email-outline mdi-24px"></i><span class="fw-semibold mx-2">Email:</span> <span>john.doe@example.com</span></li>
        </ul>
        <small class="card-text text-uppercase text-muted">Teams</small>
        <ul class="list-unstyled mb-0 mt-3 pt-1">
          <li class="d-flex align-items-center mb-3"><i class="mdi mdi-github mdi-24px text-secondary me-2"></i>
            <div class="d-flex flex-wrap"><span class="fw-semibold me-2">Backend Developer</span><span>(126 Members)</span></div>
          </li>
          <li class="d-flex align-items-center"><i class="mdi mdi-react mdi-24px text-info me-2"></i>
            <div class="d-flex flex-wrap"><span class="fw-semibold me-2">React Developer</span><span>(98 Members)</span></div>
          </li>
        </ul>
      </div>
    </div>
    <!--/ About User -->
    <!-- Profile Overview -->
    <div class="card mb-4">
      <div class="card-body">
        <small class="card-text text-uppercase text-muted">Overview</small>
        <ul class="list-unstyled mb-0 mt-3 pt-1">
          <li class="d-flex align-items-center mb-3"><i class="mdi mdi-check mdi-24px"></i><span class="fw-semibold mx-2">Task Compiled:</span> <span>13.5k</span></li>
          <li class="d-flex align-items-center mb-3"><i class="mdi mdi-account-outline mdi-24px"></i><span class="fw-semibold mx-2">Projects Compiled:</span> <span>146</span></li>
          <li class="d-flex align-items-center"><i class="mdi mdi-view-grid-outline mdi-24px"></i><span class="fw-semibold mx-2">Connections:</span> <span>897</span></li>
        </ul>
      </div>
    </div>
    <!--/ Profile Overview -->
  </div>
  <div class="col-xl-8 col-lg-7 col-md-7">
    <!-- Activity Timeline -->
    <div class="card card-action mb-4">
      <div class="card-header align-items-center">
        <h5 class="card-action-title mb-0"><i class='mdi mdi-format-list-bulleted mdi-24px me-2'></i>Activity Timeline</h5>
        <div class="card-action-element">
          <div class="dropdown">
            <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-24px text-muted"></i></button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="javascript:void(0);">Share timeline</a></li>
              <li><a class="dropdown-item" href="javascript:void(0);">Suggest edits</a></li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item" href="javascript:void(0);">Report bug</a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="card-body pt-3 pb-0">
        <ul class="timeline mb-0">
          <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point timeline-point-danger"></span>
            <div class="timeline-event">
              <div class="timeline-header mb-1">
                <h6 class="mb-0">Client Meeting</h6>
                <span class="text-muted">Today</span>
              </div>
              <p class="text-muted mb-2">Project meeting with john @10:15am</p>
              <div class="d-flex flex-wrap">
                <div class="avatar me-3">
                  <img src="{{asset('assets/img/avatars/3.png')}}" alt="Avatar" class="rounded-circle" />
                </div>
                <div>
                  <h6 class="mb-0">Lester McCarthy (Client)</h6>
                  <span class="text-muted">CEO of Infibeam</span>
                </div>
              </div>
            </div>
          </li>
          <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point timeline-point-primary"></span>
            <div class="timeline-event">
              <div class="timeline-header mb-1">
                <h6 class="mb-0">Create a new project for client</h6>
                <span class="text-muted">2 Day Ago</span>
              </div>
              <p class="text-muted mb-0">Add files to new design folder</p>
            </div>
          </li>
          <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point timeline-point-warning"></span>
            <div class="timeline-event">
              <div class="timeline-header mb-1">
                <h6 class="mb-0">Shared 2 New Project Files</h6>
                <span class="text-muted">6 Day Ago</span>
              </div>
              <p class="text-muted mb-2">Sent by Mollie Dixon <img src="{{asset('assets/img/avatars/4.png')}}" class="rounded-circle me-3" alt="avatar" height="24" width="24"></p>
              <div class="d-flex flex-wrap gap-2">
                <a href="javascript:void(0)" class="me-3">
                  <img src="{{asset('assets/img/icons/misc/doc.png')}}" alt="Document image" width="15" class="me-2">
                  <span class="fw-medium text-body">App Guidelines</span>
                </a>
                <a href="javascript:void(0)">
                  <img src="{{asset('assets/img/icons/misc/xls.png')}}" alt="Excel image" width="15" class="me-2">
                  <span class="fw-medium text-body">Testing Results</span>
                </a>
              </div>
            </div>
          </li>
          <li class="timeline-item timeline-item-transparent border-0">
            <span class="timeline-point timeline-point-info"></span>
            <div class="timeline-event">
              <div class="timeline-header mb-1">
                <h6 class="mb-0">Project status updated</h6>
                <span class="text-muted">10 Day Ago</span>
              </div>
              <p class="text-muted mb-0">Woocommerce iOS App Completed</p>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <!--/ Activity Timeline -->
    <div class="row">
      <!-- Connections -->
      <div class="col-lg-12 col-xl-6">
        <div class="card card-action mb-4">
          <div class="card-header align-items-center">
            <h5 class="card-action-title mb-0">Connections</h5>
            <div class="card-action-element">
              <div class="dropdown">
                <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-24px text-muted"></i></button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><a class="dropdown-item" href="javascript:void(0);">Share connections</a></li>
                  <li><a class="dropdown-item" href="javascript:void(0);">Suggest edits</a></li>
                  <li>
                    <hr class="dropdown-divider">
                  </li>
                  <li><a class="dropdown-item" href="javascript:void(0);">Report bug</a></li>
                </ul>
              </div>
            </div>
          </div>
          <div class="card-body">
            <ul class="list-unstyled mb-0">
              <li class="mb-3">
                <div class="d-flex align-items-start">
                  <div class="d-flex align-items-start">
                    <div class="avatar me-3">
                      <img src="{{asset('assets/img/avatars/2.png')}}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div class="me-2">
                      <h6 class="mb-0">Cecilia Payne</h6>
                      <small class="text-muted">45 Connections</small>
                    </div>
                  </div>
                  <div class="ms-auto">
                    <button class="btn btn-outline-primary btn-icon"><i class="mdi mdi-account-outline mdi-24px"></i></button>
                  </div>
                </div>
              </li>
              <li class="mb-3">
                <div class="d-flex align-items-start">
                  <div class="d-flex align-items-start">
                    <div class="avatar me-3">
                      <img src="{{asset('assets/img/avatars/3.png')}}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div class="me-2">
                      <h6 class="mb-0">Curtis Fletcher</h6>
                      <small class="text-muted">1.32k Connections</small>
                    </div>
                  </div>
                  <div class="ms-auto">
                    <button class="btn btn-primary btn-icon"><i class="mdi mdi-account-outline mdi-24px"></i></button>
                  </div>
                </div>
              </li>
              <li class="mb-3">
                <div class="d-flex align-items-start">
                  <div class="d-flex align-items-start">
                    <div class="avatar me-3">
                      <img src="{{asset('assets/img/avatars/10.png')}}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div class="me-2">
                      <h6 class="mb-0">Alice Stone</h6>
                      <small class="text-muted">125 Connections</small>
                    </div>
                  </div>
                  <div class="ms-auto">
                    <button class="btn btn-primary btn-icon"><i class="mdi mdi-account-outline mdi-24px"></i></button>
                  </div>
                </div>
              </li>
              <li class="mb-3">
                <div class="d-flex align-items-start">
                  <div class="d-flex align-items-start">
                    <div class="avatar me-3">
                      <img src="{{asset('assets/img/avatars/7.png')}}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div class="me-2">
                      <h6 class="mb-0">Darrell Barnes</h6>
                      <small class="text-muted">456 Connections</small>
                    </div>
                  </div>
                  <div class="ms-auto">
                    <button class="btn btn-outline-primary btn-icon"><i class="mdi mdi-account-outline mdi-24px"></i></button>
                  </div>
                </div>
              <li class="mb-3">
                <div class="d-flex align-items-start">
                  <div class="d-flex align-items-start">
                    <div class="avatar me-3">
                      <img src="{{asset('assets/img/avatars/12.png')}}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div class="me-2">
                      <h6 class="mb-0">Eugenia Moore</h6>
                      <small class="text-muted">1.2k Connections</small>
                    </div>
                  </div>
                  <div class="ms-auto">
                    <button class="btn btn-outline-primary btn-icon"><i class="mdi mdi-account-outline mdi-24px"></i></button>
                  </div>
                </div>
              </li>
              <li class="text-center">
                <a href="javascript:;">View all connections</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <!--/ Connections -->
      <!-- Teams -->
      <div class="col-lg-12 col-xl-6">
        <div class="card card-action mb-4">
          <div class="card-header align-items-center">
            <h5 class="card-action-title mb-0">Teams</h5>
            <div class="card-action-element">
              <div class="dropdown">
                <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-24px text-muted"></i></button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><a class="dropdown-item" href="javascript:void(0);">Share teams</a></li>
                  <li><a class="dropdown-item" href="javascript:void(0);">Suggest edits</a></li>
                  <li>
                    <hr class="dropdown-divider">
                  </li>
                  <li><a class="dropdown-item" href="javascript:void(0);">Report bug</a></li>
                </ul>
              </div>
            </div>
          </div>
          <div class="card-body">
            <ul class="list-unstyled mb-0">
              <li class="mb-3">
                <div class="d-flex align-items-center">
                  <div class="d-flex align-items-start">
                    <div class="avatar me-3">
                      <img src="{{asset('assets/img/icons/brands/react-label.png')}}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div class="me-2">
                      <h6 class="mb-0">React Developers</h6>
                      <small class="text-muted">72 Members</small>
                    </div>
                  </div>
                  <div class="ms-auto">
                    <a href="javascript:;"><span class="badge bg-label-danger rounded-pill">Developer</span></a>
                  </div>
                </div>
              </li>
              <li class="mb-3">
                <div class="d-flex align-items-center">
                  <div class="d-flex align-items-start">
                    <div class="avatar me-3">
                      <img src="{{asset('assets/img/icons/brands/support-label.png')}}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div class="me-2">
                      <h6 class="mb-0">Support Team</h6>
                      <small class="text-muted">122 Members</small>
                    </div>
                  </div>
                  <div class="ms-auto">
                    <a href="javascript:;"><span class="badge bg-label-primary rounded-pill">Support</span></a>
                  </div>
                </div>
              </li>
              <li class="mb-3">
                <div class="d-flex align-items-center">
                  <div class="d-flex align-items-start">
                    <div class="avatar me-3">
                      <img src="{{asset('assets/img/icons/brands/figma-label.png')}}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div class="me-2">
                      <h6 class="mb-0">UI Designers</h6>
                      <small class="text-muted">7 Members</small>
                    </div>
                  </div>
                  <div class="ms-auto">
                    <a href="javascript:;"><span class="badge bg-label-info rounded-pill">Designer</span></a>
                  </div>
                </div>
              </li>
              <li class="mb-3">
                <div class="d-flex align-items-center">
                  <div class="d-flex align-items-start">
                    <div class="avatar me-3">
                      <img src="{{asset('assets/img/icons/brands/vue-label.png')}}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div class="me-2">
                      <h6 class="mb-0">Vue.js Developers</h6>
                      <small class="text-muted">289 Members</small>
                    </div>
                  </div>
                  <div class="ms-auto">
                    <a href="javascript:;"><span class="badge bg-label-danger rounded-pill">Developer</span></a>
                  </div>
                </div>
              </li>
              <li class="mb-3">
                <div class="d-flex align-items-center">
                  <div class="d-flex align-items-start">
                    <div class="avatar me-3">
                      <img src="{{asset('assets/img/icons/brands/twitter-label.png')}}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div class="me-w">
                      <h6 class="mb-0">Digital Marketing</h6>
                      <small class="text-muted">24 Members</small>
                    </div>
                  </div>
                  <div class="ms-auto">
                    <a href="javascript:;"><span class="badge bg-label-secondary rounded-pill">Marketing</span></a>
                  </div>
                </div>
              </li>
              <li class="text-center">
                <a href="javascript:;">View all teams</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <!--/ Teams -->
    </div>
    <!-- Projects table -->
    <div class="card mb-4">
      <div class="card-datatable table-responsive">
        <table class="datatables-projects table">
          <thead>
            <tr>
              <th></th>
              <th></th>
              <th>Name</th>
              <th>Leader</th>
              <th>Team</th>
              <th class="w-px-200">Status</th>
              <th>Action</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
    <!--/ Projects table -->
  </div>
</div>
<!--/ User Profile Content -->
@endsection
