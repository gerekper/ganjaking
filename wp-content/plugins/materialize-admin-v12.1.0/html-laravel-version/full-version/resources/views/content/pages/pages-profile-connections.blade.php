@extends('layouts/layoutMaster')

@section('title', 'User Profile - Connections')

<!-- Page -->
@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">User Profile /</span> Connections
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
                  <i class='mdi mdi-map-marker-outline me-1 mdi-20px'></i> <span class="fw-semibold">Vatican City</span>
                </li>
                <li class="list-inline-item">
                  <i class='mdi mdi-calendar-blank-outline me-1 mdi-20px'></i> <span class="fw-semibold"> Joined April 2021</span></li>
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
      <li class="nav-item"><a class="nav-link" href="{{url('pages/profile-user')}}"><i class='mdi mdi-account-outline me-1 mdi-20px'></i> Profile</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('pages/profile-teams')}}"><i class='mdi mdi-account-multiple-outline me-1 mdi-20px'></i> Teams</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('pages/profile-projects')}}"><i class='mdi mdi-view-grid-outline me-1 mdi-20px'></i> Projects</a></li>
      <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i class='mdi mdi-link me-1 mdi-20px'></i> Connections</a></li>
    </ul>
  </div>
</div>
<!--/ Navbar pills -->

<!-- Connection Cards -->
<div class="row g-4">
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-body text-center">
        <div class="dropdown btn-pinned">
          <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-24px text-muted"></i></button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="javascript:void(0);">Share connection</a></li>
            <li><a class="dropdown-item" href="javascript:void(0);">Block connection</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item text-danger" href="javascript:void(0);">Delete</a></li>
          </ul>
        </div>
        <div class="mx-auto mb-4">
          <img src="{{asset('assets/img/avatars/3.png')}}" alt="Avatar Image" class="rounded-circle w-px-100" />
        </div>
        <h5 class="mb-1 card-title">Mark Gilbert</h5>
        <span class="text-muted">UI Designer</span>
        <div class="d-flex align-items-center justify-content-center my-4 gap-2">
          <a href="javascript:;" class="me-1"><span class="badge bg-label-secondary rounded-pill">Figma</span></a>
          <a href="javascript:;"><span class="badge bg-label-warning rounded-pill">Sketch</span></a>
        </div>

        <div class="d-flex align-items-center justify-content-around mb-4">
          <div>
            <h4 class="mb-1">18</h4>
            <span class="text-muted">Projects</span>
          </div>
          <div>
            <h4 class="mb-1">834</h4>
            <span class="text-muted">Tasks</span>
          </div>
          <div>
            <h4 class="mb-1">129</h4>
            <span class="text-muted">Connections</span>
          </div>
        </div>
        <div class="d-flex align-items-center justify-content-center">
          <a href="javascript:;" class="btn btn-primary d-flex align-items-center me-3"><i class="mdi mdi-account-check-outline me-1"></i>Connected</a>
          <a href="javascript:;" class="btn btn-outline-secondary btn-icon"><i class="mdi mdi-email-outline"></i></a>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-body text-center">
        <div class="dropdown btn-pinned">
          <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-24px text-muted"></i></button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="javascript:void(0);">Share connection</a></li>
            <li><a class="dropdown-item" href="javascript:void(0);">Block connection</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item text-danger" href="javascript:void(0);">Delete</a></li>
          </ul>
        </div>
        <div class="mx-auto mb-4">
          <img src="{{asset('assets/img/avatars/12.png')}}" alt="Avatar Image" class="rounded-circle w-px-100" />
        </div>
        <h5 class="mb-1 card-title">Eugenia Parsons</h5>
        <span class="text-muted">Developer</span>
        <div class="d-flex align-items-center justify-content-center my-4 gap-2">
          <a href="javascript:;" class="me-1"><span class="badge bg-label-danger rounded-pill">Angular</span></a>
          <a href="javascript:;"><span class="badge bg-label-info rounded-pill">React</span></a>
        </div>

        <div class="d-flex align-items-center justify-content-around mb-4">
          <div>
            <h4 class="mb-1">112</h4>
            <span class="text-muted">Projects</span>
          </div>
          <div>
            <h4 class="mb-1">23.1k</h4>
            <span class="text-muted">Tasks</span>
          </div>
          <div>
            <h4 class="mb-1">1.28k</h4>
            <span class="text-muted">Connections</span>
          </div>
        </div>
        <div class="d-flex align-items-center justify-content-center">
          <a href="javascript:;" class="btn btn-outline-primary d-flex align-items-center me-3"><i class="mdi mdi-account-plus-outline me-1"></i>Connect</a>
          <a href="javascript:;" class="btn btn-outline-secondary btn-icon"><i class="mdi mdi-email-outline"></i></a>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-body text-center">
        <div class="dropdown btn-pinned">
          <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-24px text-muted"></i></button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="javascript:void(0);">Share connection</a></li>
            <li><a class="dropdown-item" href="javascript:void(0);">Block connection</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item text-danger" href="javascript:void(0);">Delete</a></li>
          </ul>
        </div>
        <div class="mx-auto mb-4">
          <img src="{{asset('assets/img/avatars/5.png')}}" alt="Avatar Image" class="rounded-circle w-px-100" />
        </div>
        <h5 class="mb-1 card-title">Francis Byrd</h5>
        <span class="text-muted">Developer</span>
        <div class="d-flex align-items-center justify-content-center my-4 gap-2">
          <a href="javascript:;" class="me-1"><span class="badge bg-label-info rounded-pill">React</span></a>
          <a href="javascript:;"><span class="badge bg-label-primary rounded-pill">HTML</span></a>
        </div>

        <div class="d-flex align-items-center justify-content-around mb-4">
          <div>
            <h4 class="mb-1">32</h4>
            <span class="text-muted">Projects</span>
          </div>
          <div>
            <h4 class="mb-1">1.25k</h4>
            <span class="text-muted">Tasks</span>
          </div>
          <div>
            <h4 class="mb-1">890</h4>
            <span class="text-muted">Connections</span>
          </div>
        </div>
        <div class="d-flex align-items-center justify-content-center">
          <a href="javascript:;" class="btn btn-outline-primary d-flex align-items-center me-3"><i class="mdi mdi-account-plus-outline me-1"></i>Connect</a>
          <a href="javascript:;" class="btn btn-outline-secondary btn-icon"><i class="mdi mdi-email-outline"></i></a>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-body text-center">
        <div class="dropdown btn-pinned">
          <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-24px text-muted"></i></button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="javascript:void(0);">Share connection</a></li>
            <li><a class="dropdown-item" href="javascript:void(0);">Block connection</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item text-danger" href="javascript:void(0);">Delete</a></li>
          </ul>
        </div>
        <div class="mx-auto mb-4">
          <img src="{{asset('assets/img/avatars/18.png')}}" alt="Avatar Image" class="rounded-circle w-px-100" />
        </div>
        <h5 class="mb-1 card-title">Leon Lucas</h5>
        <span class="text-muted">UI/UX Designer</span>
        <div class="d-flex align-items-center justify-content-center my-4 gap-2">
          <a href="javascript:;" class="me-1"><span class="badge bg-label-secondary rounded-pill">Figma</span></a>
          <a href="javascript:;" class="me-1"><span class="badge bg-label-warning rounded-pill">Sketch</span></a>
          <a href="javascript:;"><span class="badge bg-label-primary rounded-pill">Photoshop</span></a>
        </div>

        <div class="d-flex align-items-center justify-content-around mb-4">
          <div>
            <h4 class="mb-1">86</h4>
            <span class="text-muted">Projects</span>
          </div>
          <div>
            <h4 class="mb-1">12.4k</h4>
            <span class="text-muted">Tasks</span>
          </div>
          <div>
            <h4 class="mb-1">890</h4>
            <span class="text-muted">Connections</span>
          </div>
        </div>
        <div class="d-flex align-items-center justify-content-center">
          <a href="javascript:;" class="btn btn-outline-primary d-flex align-items-center me-3"><i class="mdi mdi-account-plus-outline me-1"></i>Connect</a>
          <a href="javascript:;" class="btn btn-outline-secondary btn-icon"><i class="mdi mdi-email-outline"></i></a>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-body text-center">
        <div class="dropdown btn-pinned">
          <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-24px text-muted"></i></button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="javascript:void(0);">Share connection</a></li>
            <li><a class="dropdown-item" href="javascript:void(0);">Block connection</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item text-danger" href="javascript:void(0);">Delete</a></li>
          </ul>
        </div>
        <div class="mx-auto mb-4">
          <img src="{{asset('assets/img/avatars/9.png')}}" alt="Avatar Image" class="rounded-circle w-px-100" />
        </div>
        <h5 class="mb-1 card-title">Jayden Rogers</h5>
        <span class="text-muted">Full Stack Developer</span>
        <div class="d-flex align-items-center justify-content-center my-4 gap-2">
          <a href="javascript:;" class="me-1"><span class="badge bg-label-info rounded-pill">React</span></a>
          <a href="javascript:;" class="me-1"><span class="badge bg-label-danger rounded-pill">Angular</span></a>
          <a href="javascript:;"><span class="badge bg-label-primary rounded-pill">HTML</span></a>
        </div>

        <div class="d-flex align-items-center justify-content-around mb-4">
          <div>
            <h4 class="mb-1">244</h4>
            <span class="text-muted">Projects</span>
          </div>
          <div>
            <h4 class="mb-1">23.8k</h4>
            <span class="text-muted">Tasks</span>
          </div>
          <div>
            <h4 class="mb-1">2.14k</h4>
            <span class="text-muted">Connections</span>
          </div>
        </div>
        <div class="d-flex align-items-center justify-content-center">
          <a href="javascript:;" class="btn btn-primary d-flex align-items-center me-3"><i class="mdi mdi-account-check-outline me-1"></i>Connected</a>
          <a href="javascript:;" class="btn btn-outline-secondary btn-icon"><i class="mdi mdi-email-outline"></i></a>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-body text-center">
        <div class="dropdown btn-pinned">
          <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-24px text-muted"></i></button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="javascript:void(0);">Share connection</a></li>
            <li><a class="dropdown-item" href="javascript:void(0);">Block connection</a></li>
            <li>
              <hr class="dropdown-divider" />
            </li>
            <li><a class="dropdown-item text-danger" href="javascript:void(0);">Delete</a></li>
          </ul>
        </div>
        <div class="mx-auto mb-4">
          <img src="{{asset('assets/img/avatars/10.png')}}" alt="Avatar Image" class="rounded-circle w-px-100" />
        </div>
        <h5 class="mb-1 card-title">Jeanette Powell</h5>
        <span class="text-muted">SEO</span>
        <div class="d-flex align-items-center justify-content-center my-4 gap-2">
          <a href="javascript:;" class="me-1"><span class="badge bg-label-success rounded-pill">Writing</span></a>
          <a href="javascript:;"><span class="badge bg-label-secondary rounded-pill">Analysis</span></a>
        </div>

        <div class="d-flex align-items-center justify-content-around mb-4">
          <div>
            <h4 class="mb-1">32</h4>
            <span class="text-muted">Projects</span>
          </div>
          <div>
            <h4 class="mb-1">1.28k</h4>
            <span class="text-muted">Tasks</span>
          </div>
          <div>
            <h4 class="mb-1">1.27k</h4>
            <span class="text-muted">Connections</span>
          </div>
        </div>
        <div class="d-flex align-items-center justify-content-center">
          <a href="javascript:;" class="btn btn-outline-primary d-flex align-items-center me-3"><i class="mdi mdi-account-plus-outline me-1"></i>Connect</a>
          <a href="javascript:;" class="btn btn-outline-secondary btn-icon"><i class="mdi mdi-email-outline"></i></a>
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Connection Cards -->
@endsection
