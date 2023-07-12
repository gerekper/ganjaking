@extends('layouts/layoutMaster')

@section('title', 'User Profile - Projects')

<!-- Page -->
@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">User Profile /</span> Projects
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
      <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i class='mdi mdi-view-grid-outline me-1 mdi-20px'></i> Projects</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('pages/profile-connections')}}"><i class='mdi mdi-link me-1 mdi-20px'></i> Connections</a></li>
    </ul>
  </div>
</div>
<!--/ Navbar pills -->

<!-- Project Cards -->
<div class="row g-4">
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-header">
        <div class="d-flex align-items-start">
          <div class="d-flex align-items-start">
            <div class="avatar me-3">
              <img src="{{ asset('assets/img/icons/brands/social-label.png')}}" alt="Avatar" class="rounded-circle" />
            </div>
            <div class="me-2">
              <h5 class="mb-1"><a href="javascript:;" class="stretched-link text-heading">Social Banners</a></h5>
              <div class="client-info"><span class="fw-semibold">Client: </span><span class="text-body">Christian Jimenez</span></div>
            </div>
          </div>
          <div class="ms-auto">
            <div class="dropdown zindex-2">
              <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-24px text-muted"></i></button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="javascript:void(0);">Rename project</a></li>
                <li><a class="dropdown-item" href="javascript:void(0);">View details</a></li>
                <li><a class="dropdown-item" href="javascript:void(0);">Add to favorites</a></li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-danger" href="javascript:void(0);">Leave Project</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="d-flex align-items-center flex-wrap">
          <div class="bg-lighter px-2 py-1 rounded-2 me-auto mb-3">
            <p class="mb-1 fw-semibold text-heading">$24.8k <span class="text-muted fw-normal">/ $18.2k</span></p>
            <span class="text-muted">Total Budget</span>
          </div>
          <div class="text-end mb-3">
            <p class="mb-1 text-heading fw-semibold">Start Date: <span class="text-muted fw-normal">14/2/21</span></p>
            <p class="mb-1 text-heading fw-semibold">Deadline: <span class="text-muted fw-normal">28/2/22</span></p>
          </div>
        </div>
        <p class="mb-0">We are Consulting, Software Development and Web Development Services.</p>
      </div>
      <div class="card-body border-top">
        <div class="d-flex align-items-center mb-3">
          <p class="mb-1 text-heading fw-semibold">All Hours: <span class="text-muted fw-normal">380/244</span></p>
          <span class="badge bg-label-success ms-auto rounded-pill">28 Days left</span>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-1">
          <small class="text-muted">Task: 290/344</small>
          <small class="text-muted">95% Completed</small>
        </div>
        <div class="progress mb-3 rounded rounded" style="height: 8px;">
          <div class="progress-bar rounded" role="progressbar" style="width: 95%;" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div class="d-flex align-items-center">
          <div class="d-flex align-items-center">
            <ul class="list-unstyled d-flex align-items-center avatar-group mb-0 zindex-2">
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Vinnie Mostowy" class="avatar avatar-sm pull-up">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/5.png')}}" alt="Avatar">
              </li>
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Allen Rieske" class="avatar avatar-sm pull-up">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/12.png')}}" alt="Avatar">
              </li>
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Julee Rossignol" class="avatar avatar-sm pull-up me-2">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/6.png')}}" alt="Avatar">
              </li>
              <li><small class="text-muted">280 Members</small></li>
            </ul>
          </div>
          <div class="ms-auto">
            <a href="javascript:void(0);" class="text-muted"><i class="mdi mdi-message-outline mdi-24px me-2"></i>15</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-header">
        <div class="d-flex align-items-start">
          <div class="d-flex align-items-start">
            <div class="avatar me-3">
              <img src="{{ asset('assets/img/icons/brands/react-label.png')}}" alt="Avatar" class="rounded-circle" />
            </div>
            <div class="me-2">
              <h5 class="mb-1"><a href="javascript:;" class="stretched-link text-heading">Admin Template</a></h5>
              <div class="client-info"><span class="fw-semibold">Client: </span><span class="text-body">Jeffrey Phillips</span></div>
            </div>
          </div>
          <div class="ms-auto">
            <div class="dropdown zindex-2">
              <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-24px text-muted"></i></button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="javascript:void(0);">Rename project</a></li>
                <li><a class="dropdown-item" href="javascript:void(0);">View details</a></li>
                <li><a class="dropdown-item" href="javascript:void(0);">Add to favorites</a></li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-danger" href="javascript:void(0);">Leave Project</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="d-flex align-items-center flex-wrap">
          <div class="bg-lighter px-2 py-1 rounded-2 me-auto mb-3">
            <p class="mb-1 fw-semibold text-heading">$2.4k <span class="text-muted fw-normal">/ 1.8k</span></p>
            <span class="text-muted">Total Budget</span>
          </div>
          <div class="text-end mb-3">
            <p class="mb-1 text-heading fw-semibold">Start Date: <span class="text-muted fw-normal">18/8/21</span></p>
            <p class="mb-1 text-heading fw-semibold">Deadline: <span class="text-muted fw-normal">21/6/22</span></p>
          </div>
        </div>
        <p class="mb-0">Time is our most valuable asset, that's why we want to help you save it by creating…</p>
      </div>
      <div class="card-body border-top">
        <div class="d-flex align-items-center mb-3">
          <p class="mb-1 text-heading fw-semibold">All Hours: <span class="text-muted fw-normal">98/135</span></p>
          <span class="badge bg-label-warning ms-auto rounded-pill">15 Days left</span>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-1">
          <small class="text-muted">Task: 12/90</small>
          <small class="text-muted">42% Completed</small>
        </div>
        <div class="progress mb-3 rounded rounded" style="height: 8px;">
          <div class="progress-bar rounded" role="progressbar" style="width: 42%;" aria-valuenow="42" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div class="d-flex align-items-center">
          <div class="d-flex align-items-center">
            <ul class="list-unstyled d-flex align-items-center avatar-group mb-0 zindex-2">
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Kaith D'souza" class="avatar avatar-sm pull-up">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/15.png')}}" alt="Avatar">
              </li>
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="John Doe" class="avatar avatar-sm pull-up">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/1.png')}}" alt="Avatar">
              </li>
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Alan Walker" class="avatar avatar-sm pull-up me-2">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/16.png')}}" alt="Avatar">
              </li>
              <li><small class="text-muted">1.1k Members</small></li>
            </ul>
          </div>
          <div class="ms-auto">
            <a href="javascript:void(0);" class="text-muted"><i class="mdi mdi-message-outline mdi-24px me-2"></i> 236</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-header">
        <div class="d-flex align-items-start">
          <div class="d-flex align-items-start">
            <div class="avatar me-3">
              <img src="{{ asset('assets/img/icons/brands/vue-label.png')}}" alt="Avatar" class="rounded-circle" />
            </div>
            <div class="me-2">
              <h5 class="mb-1"><a href="javascript:;" class="stretched-link text-heading">App Design</a></h5>
              <div class="client-info"><span class="fw-semibold">Client: </span><span class="text-body">Ricky McDonald</span></div>
            </div>
          </div>
          <div class="ms-auto">
            <div class="dropdown zindex-2">
              <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-24px text-muted"></i></button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="javascript:void(0);">Rename project</a></li>
                <li><a class="dropdown-item" href="javascript:void(0);">View details</a></li>
                <li><a class="dropdown-item" href="javascript:void(0);">Add to favorites</a></li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-danger" href="javascript:void(0);">Leave Project</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="d-flex align-items-center flex-wrap">
          <div class="bg-lighter px-2 py-1 rounded-2 me-auto mb-3">
            <p class="mb-1 fw-semibold text-heading">$980 <span class="text-muted fw-normal">/ $420</span></p>
            <span class="text-muted">Total Budget</span>
          </div>
          <div class="text-end mb-3">
            <p class="mb-1 text-heading fw-semibold">Start Date: <span class="text-muted fw-normal">24/7/21</span></p>
            <p class="mb-1 text-heading fw-semibold">Deadline: <span class="text-muted fw-normal">8/10/21</span></p>
          </div>
        </div>
        <p class="mb-0">App design combines the user interface (UI) and user experience (UX).</p>
      </div>
      <div class="card-body border-top">
        <div class="d-flex align-items-center mb-3">
          <p class="mb-1 text-heading fw-semibold">All Hours: <span class="text-muted fw-normal">880/421</span></p>
          <span class="badge bg-label-danger ms-auto rounded-pill">45 Days left</span>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-1">
          <small class="text-muted">Task: 22/140</small>
          <small class="text-muted">68% Completed</small>
        </div>
        <div class="progress mb-3 rounded rounded" style="height: 8px;">
          <div class="progress-bar rounded" role="progressbar" style="width: 68%;" aria-valuenow="68" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div class="d-flex align-items-center">
          <div class="d-flex align-items-center">
            <ul class="list-unstyled d-flex align-items-center avatar-group mb-0 zindex-2">
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Jimmy Ressula" class="avatar avatar-sm pull-up">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/4.png')}}" alt="Avatar">
              </li>
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Kristi Lawker" class="avatar avatar-sm pull-up">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/2.png')}}" alt="Avatar">
              </li>
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Danny Paul" class="avatar avatar-sm pull-up me-2">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/7.png')}}" alt="Avatar">
              </li>
              <li><small class="text-muted">458 Members</small></li>
            </ul>
          </div>
          <div class="ms-auto">
            <a href="javascript:void(0);" class="text-muted"><i class="mdi mdi-message-outline mdi-24px me-2"></i> 98</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-header">
        <div class="d-flex align-items-start">
          <div class="d-flex align-items-start">
            <div class="avatar me-3">
              <img src="{{ asset('assets/img/icons/brands/html-label.png')}}" alt="Avatar" class="rounded-circle" />
            </div>
            <div class="me-2">
              <h5 class="mb-1"><a href="javascript:;" class="stretched-link text-heading">Create Website</a></h5>
              <div class="client-info"><span class="fw-semibold">Client: </span><span class="text-body">Hulda Wright</span></div>
            </div>
          </div>
          <div class="ms-auto">
            <div class="dropdown zindex-2">
              <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-24px text-muted"></i></button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="javascript:void(0);">Rename project</a></li>
                <li><a class="dropdown-item" href="javascript:void(0);">View details</a></li>
                <li><a class="dropdown-item" href="javascript:void(0);">Add to favorites</a></li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-danger" href="javascript:void(0);">Leave Project</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="d-flex align-items-center flex-wrap">
          <div class="bg-lighter px-2 py-1 rounded-2 me-auto mb-3">
            <p class="mb-1 fw-semibold text-heading">$8.5k <span class="text-muted fw-normal">/ $2.43k</span></p>
            <span class="text-muted">Total Budget</span>
          </div>
          <div class="text-end mb-3">
            <p class="mb-1 text-heading fw-semibold">Start Date: <span class="text-muted fw-normal">10/2/19</span></p>
            <p class="mb-1 text-heading fw-semibold">Deadline: <span class="text-muted fw-normal">12/9/22</span></p>
          </div>
        </div>
        <p class="mb-0">Your domain name should reflect your products or services so that your...</p>
      </div>
      <div class="card-body border-top">
        <div class="d-flex align-items-center mb-3">
          <p class="mb-1 text-heading fw-semibold">All Hours: <span class="text-muted fw-normal">1.2k/820</span></p>
          <span class="badge bg-label-warning ms-auto rounded-pill">126 Days left</span>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-1">
          <small class="text-muted">Task: 237/420</small>
          <small class="text-muted">72% Completed</small>
        </div>
        <div class="progress mb-3 rounded" style="height: 8px;">
          <div class="progress-bar rounded" role="progressbar" style="width: 72%;" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div class="d-flex align-items-center">
          <div class="d-flex align-items-center">
            <ul class="list-unstyled d-flex align-items-center avatar-group mb-0 zindex-2">
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Andrew Tye" class="avatar avatar-sm pull-up">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/6.png')}}" alt="Avatar">
              </li>
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Rishi Swaat" class="avatar avatar-sm pull-up">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/9.png')}}" alt="Avatar">
              </li>
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Rossie Kim" class="avatar avatar-sm pull-up me-2">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/12.png')}}" alt="Avatar">
              </li>
              <li><small class="text-muted">137 Members</small></li>
            </ul>
          </div>
          <div class="ms-auto">
            <a href="javascript:void(0);" class="text-muted"><i class="mdi mdi-message-outline mdi-24px me-2"></i> 120</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-header">
        <div class="d-flex align-items-start">
          <div class="d-flex align-items-start">
            <div class="avatar me-3">
              <img src="{{ asset('assets/img/icons/brands/figma-label.png')}}" alt="Avatar" class="rounded-circle" />
            </div>
            <div class="me-2">
              <h5 class="mb-1"><a href="javascript:;" class="stretched-link text-heading">Figma Dashboard</a></h5>
              <div class="client-info"><span class="fw-semibold">Client: </span><span class="text-body">Jerry Greene</span></div>
            </div>
          </div>
          <div class="ms-auto">
            <div class="dropdown zindex-2">
              <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-24px text-muted"></i></button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="javascript:void(0);">Rename project</a></li>
                <li><a class="dropdown-item" href="javascript:void(0);">View details</a></li>
                <li><a class="dropdown-item" href="javascript:void(0);">Add to favorites</a></li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-danger" href="javascript:void(0);">Leave Project</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="d-flex align-items-center flex-wrap">
          <div class="bg-lighter px-2 py-1 rounded-2 me-auto mb-3">
            <p class="mb-1 fw-semibold text-heading">$52.7k <span class="text-muted fw-normal">/ $28.4k</span></p>
            <span class="text-muted">Total Budget</span>
          </div>
          <div class="text-end mb-3">
            <p class="mb-1 text-heading fw-semibold">Start Date: <span class="text-muted fw-normal">12/12/20</span></p>
            <p class="mb-1 text-heading fw-semibold">Deadline: <span class="text-muted fw-normal">25/12/21</span></p>
          </div>
        </div>
        <p class="mb-0">Use this template to organize your design project. Some of the key features are…</p>
      </div>
      <div class="card-body border-top">
        <div class="d-flex align-items-center mb-3">
          <p class="mb-1 text-heading fw-semibold">All Hours: <span class="text-muted fw-normal">142/420</span></p>
          <span class="badge bg-label-danger ms-auto rounded-pill">5 Days left</span>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-1">
          <small class="text-muted">Task: 29/285</small>
          <small class="text-muted">35% Completed</small>
        </div>
        <div class="progress mb-3 rounded" style="height: 8px;">
          <div class="progress-bar rounded" role="progressbar" style="width: 35%;" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div class="d-flex align-items-center">
          <div class="d-flex align-items-center">
            <ul class="list-unstyled d-flex align-items-center avatar-group mb-0 zindex-2">
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Kim Merchent" class="avatar avatar-sm pull-up">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/10.png')}}" alt="Avatar">
              </li>
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Sam D'souza" class="avatar avatar-sm pull-up">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/13.png')}}" alt="Avatar">
              </li>
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Nurvi Karlos" class="avatar avatar-sm pull-up me-2">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/15.png')}}" alt="Avatar">
              </li>
              <li><small class="text-muted">82 Members</small></li>
            </ul>
          </div>
          <div class="ms-auto">
            <a href="javascript:void(0);" class="text-muted"><i class="mdi mdi-message-outline mdi-24px me-2"></i> 20</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-header">
        <div class="d-flex align-items-start">
          <div class="d-flex align-items-start">
            <div class="avatar me-3">
              <img src="{{ asset('assets/img/icons/brands/xd-label.png')}}" alt="Avatar" class="rounded-circle" />
            </div>
            <div class="me-2">
              <h5 class="mb-1"><a href="javascript:;" class="stretched-link text-heading">Logo Design</a></h5>
              <div class="client-info"><span class="fw-semibold">Client: </span><span class="text-body">Olive Strickland</span></div>
            </div>
          </div>
          <div class="ms-auto">
            <div class="dropdown zindex-2">
              <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-24px text-muted"></i></button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="javascript:void(0);">Rename project</a></li>
                <li><a class="dropdown-item" href="javascript:void(0);">View details</a></li>
                <li><a class="dropdown-item" href="javascript:void(0);">Add to favorites</a></li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-danger" href="javascript:void(0);">Leave Project</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="d-flex align-items-center flex-wrap">
          <div class="bg-lighter px-2 py-1 rounded-2 me-auto mb-3">
            <p class="mb-1 fw-semibold text-heading">$1.3k <span class="text-muted fw-normal">/ $655</span></p>
            <span class="text-muted">Total Budget</span>
          </div>
          <div class="text-end mb-3">
            <p class="mb-1 text-heading fw-semibold">Start Date: <span class="text-muted fw-normal">17/8/21</span></p>
            <p class="mb-1 text-heading fw-semibold">Deadline: <span class="text-muted fw-normal">02/11/21</span></p>
          </div>
        </div>
        <p class="mb-0">Premium logo designs created by top logo designers. Create the branding of business.</p>
      </div>
      <div class="card-body border-top">
        <div class="d-flex align-items-center mb-3">
          <p class="mb-1 text-heading fw-semibold">All Hours: <span class="text-muted fw-normal">580/445</span></p>
          <span class="badge bg-label-success ms-auto rounded-pill">4 Days left</span>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-1">
          <small class="text-muted">Task: 290/290</small>
          <small class="text-muted">100% Completed</small>
        </div>
        <div class="progress mb-3 rounded" style="height: 8px;">
          <div class="progress-bar rounded" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div class="d-flex align-items-center">
          <div class="d-flex align-items-center">
            <ul class="list-unstyled d-flex align-items-center avatar-group mb-0 zindex-2">
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Kim Karlos" class="avatar avatar-sm pull-up">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/3.png')}}" alt="Avatar">
              </li>
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Katy Turner" class="avatar avatar-sm pull-up">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/9.png')}}" alt="Avatar">
              </li>
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Peter Adward" class="avatar avatar-sm pull-up me-2">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/15.png')}}" alt="Avatar">
              </li>
              <li><small class="text-muted">16 Members</small></li>
            </ul>
          </div>
          <div class="ms-auto">
            <a href="javascript:void(0);" class="text-muted"><i class="mdi mdi-message-outline mdi-24px me-2"></i> 37</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Project Cards -->
@endsection
