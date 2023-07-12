@extends('layouts/layoutMaster')

@section('title', 'User View - Pages')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/modal-edit-user.js')}}"></script>
<script src="{{asset('assets/js/modal-enable-otp.js')}}"></script>
<script src="{{asset('assets/js/app-user-view.js')}}"></script>
<script src="{{asset('assets/js/app-user-view-security.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">User / View /</span> Security
</h4>
<div class="row">
  <!-- User Sidebar -->
  <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
    <!-- User Card -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="user-avatar-section">
          <div class=" d-flex align-items-center flex-column">
            <img class="img-fluid rounded mb-3 mt-4" src="{{asset('assets/img/avatars/10.png')}}" height="120" width="120" alt="User avatar" />
            <div class="user-info text-center">
              <h4>Violet Mendoza</h4>
              <span class="badge bg-label-danger">Author</span>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-between flex-wrap my-2 py-3">
          <div class="d-flex align-items-center me-4 mt-3 gap-3">
            <div class="avatar">
              <div class="avatar-initial bg-label-primary rounded">
                <i class='mdi mdi-check mdi-24px'></i>
              </div>
            </div>
            <div>
              <h4 class="mb-0 fw-normal">1.23k</h4>
              <span>Tasks Done</span>
            </div>
          </div>
          <div class="d-flex align-items-center mt-3 gap-3">
            <div class="avatar">
              <div class="avatar-initial bg-label-primary rounded">
                <i class='mdi mdi-briefcase-variant-outline mdi-24px'></i>
              </div>
            </div>
            <div>
              <h4 class="mb-0 fw-normal">568</h4>
              <span>Projects Done</span>
            </div>
          </div>
        </div>
        <h5 class="pb-3 border-bottom mb-3">Details</h5>
        <div class="info-container">
          <ul class="list-unstyled mb-4">
            <li class="mb-3">
              <span class="fw-semibold text-heading">Username:</span>
              <span>violet.dev</span>
            </li>
            <li class="mb-3">
              <span class="fw-semibold text-heading">Email:</span>
              <span>vafgot@vultukir.org</span>
            </li>
            <li class="mb-3">
              <span class="fw-semibold text-heading">Status:</span>
              <span class="badge bg-label-success">Active</span>
            </li>
            <li class="mb-3">
              <span class="fw-semibold text-heading">Role:</span>
              <span>Author</span>
            </li>
            <li class="mb-3">
              <span class="fw-semibold text-heading">Tax id:</span>
              <span>Tax-8965</span>
            </li>
            <li class="mb-3">
              <span class="fw-semibold text-heading">Contact:</span>
              <span>(123) 456-7890</span>
            </li>
            <li class="mb-3">
              <span class="fw-semibold text-heading">Languages:</span>
              <span>French</span>
            </li>
            <li class="mb-3">
              <span class="fw-semibold text-heading">Country:</span>
              <span>England</span>
            </li>
          </ul>
          <div class="d-flex justify-content-center">
            <a href="javascript:;" class="btn btn-primary me-3" data-bs-target="#editUser" data-bs-toggle="modal">Edit</a>
            <a href="javascript:;" class="btn btn-outline-danger suspend-user">Suspended</a>
          </div>
        </div>
      </div>
    </div>
    <!-- /User Card -->
    <!-- Plan Card -->
    <div class="card mb-4 border-2 border-primary">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <span class="badge bg-label-primary">Standard</span>
          <div class="d-flex justify-content-center">
            <sup class="h5 pricing-currency mt-3 mb-0 me-1 text-primary">$</sup>
            <h1 class="fw-normal display-3 mb-0 text-primary">99</h1>
            <sub class="h5 pricing-duration mt-auto mb-2 fw-normal">/month</sub>
          </div>
        </div>
        <ul class="list-unstyled g-2 my-4">
          <li class="mb-2 d-flex align-items-center"><i class="mdi mdi-circle-medium text-lighter mdi-24px"></i><span>10 Users</span></li>
          <li class="mb-2 d-flex align-items-center"><i class="mdi mdi-circle-medium text-lighter mdi-24px"></i><span>Up to 10 GB storage</span></li>
          <li class="mb-2 d-flex align-items-center"><i class="mdi mdi-circle-medium text-lighter mdi-24px"></i><span>Basic Support</span></li>
        </ul>
        <div class="d-flex justify-content-between align-items-center mb-1">
          <span class="text-heading fw-semibold">Days</span>
          <span class="text-heading fw-semibold">65% Completed</span>
        </div>
        <div class="progress mb-1 rounded" style="height: 6px;">
          <div class="progress-bar rounded" role="progressbar" style="width: 65%;" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <span>4 days remaining</span>
        <div class="d-grid w-100 mt-4">
          <button class="btn btn-primary" data-bs-target="#upgradePlanModal" data-bs-toggle="modal">Upgrade Plan</button>
        </div>
      </div>
    </div>
    <!-- /Plan Card -->
  </div>
  <!--/ User Sidebar -->


  <!-- User Content -->
  <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
    <!-- User Tabs -->
    <ul class="nav nav-tabs mb-3">
      <li class="nav-item"><a class="nav-link" href="{{url('app/user/view/account')}}"><i class="mdi mdi-account-outline mdi-20px me-1"></i>Account</a></li>
      <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i class="mdi mdi-lock-open-outline mdi-20px me-1"></i>Security</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('app/user/view/billing')}}"><i class="mdi mdi-bookmark-outline mdi-20px me-1"></i>Billing & Plans</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('app/user/view/notifications')}}"><i class="mdi mdi-bell-outline mdi-20px me-1"></i>Notifications</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('app/user/view/connections')}}"><i class="mdi mdi-link mdi-20px me-1"></i>Connections</a></li>
    </ul>
    <!--/ User Tabs -->

    <!-- Change Password -->
    <div class="card mb-4">
      <h5 class="card-header">Change Password</h5>
      <div class="card-body">
        <form id="formChangePassword" method="POST" onsubmit="return false">
          <div class="alert alert-warning" role="alert">
            <h6 class="alert-heading mb-1">Ensure that these requirements are met</h6>
            <span>Minimum 8 characters long, uppercase & symbol</span>
          </div>
          <div class="row">
            <div class="mb-3 col-12 col-sm-6 form-password-toggle">
              <div class="input-group input-group-merge">
                <div class="form-floating form-floating-outline">
                  <input class="form-control" type="password" id="newPassword" name="newPassword" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                  <label for="newPassword">New Password</label>
                </div>
                <span class="input-group-text cursor-pointer"><i class="mdi mdi-eye-off-outline"></i></span>
              </div>
            </div>

            <div class="mb-3 col-12 col-sm-6 form-password-toggle">
              <div class="input-group input-group-merge">
                <div class="form-floating form-floating-outline">
                  <input class="form-control" type="password" name="confirmPassword" id="confirmPassword" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                  <label for="confirmPassword">Confirm New Password</label>
                </div>
                <span class="input-group-text cursor-pointer"><i class="mdi mdi-eye-off-outline"></i></span>
              </div>
            </div>
            <div>
              <button type="submit" class="btn btn-primary me-2">Change Password</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <!--/ Change Password -->

    <!-- Two-steps verification -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">Two-steps verification</h5>
        <span class="text-muted">Keep your account secure with authentication step.</span>
      </div>
      <div class="card-body">
        <h6 class="mt-1">SMS</h6>
        <div class="d-flex justify-content-between border-bottom mb-4">
          <span>+1(968) 945-8832</span>
          <div class="action-icons">
            <a href="javascript:;" class="text-body me-1" data-bs-target="#enableOTP" data-bs-toggle="modal"><i class="mdi mdi-pencil-outline"></i></a>
            <a href="javascript:;" class="text-body"><i class="mdi mdi-delete-outline"></i></a>
          </div>
        </div>
        <p class="mb-0">Two-factor authentication adds an additional layer of security to your account by requiring more than just a password to log in.
          <a href="javascript:void(0);">Learn more.</a>
        </p>
      </div>
    </div>
    <!--/ Two-steps verification -->

    <!-- Recent Devices -->
    <div class="card mb-4">
      <h5 class="card-header">Recent Devices</h5>
      <div class="table-responsive">
        <table class="table">
          <thead class="table-light">
            <tr>
              <th class="text-truncate">Browser</th>
              <th class="text-truncate">Device</th>
              <th class="text-truncate">Location</th>
              <th class="text-truncate">Recent Activities</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="text-truncate"><img src="{{asset('assets/img/icons/brands/chrome.png')}}" alt="Chrome" class="me-3" width="22" height="22"><span class="text-heading">Chrome on Windows</span></td>
              <td class="text-truncate">HP Spectre 360</td>
              <td class="text-truncate">Switzerland</td>
              <td class="text-truncate">10, July 2021 20:07</td>
            </tr>
            <tr>
              <td class="text-truncate"><img src="{{asset('assets/img/icons/brands/chrome.png')}}" alt="Chrome" class="me-3" width="22" height="22"><span class="text-heading">Chrome on iPhone</span></td>
              <td class="text-truncate">iPhone 12x</td>
              <td class="text-truncate">Australia</td>
              <td class="text-truncate">13, July 2021 10:10</td>
            </tr>
            <tr>
              <td class="text-truncate"><img src="{{asset('assets/img/icons/brands/chrome.png')}}" alt="Chrome" class="me-3" width="22" height="22"><span class="text-heading">Chrome on Android</span></td>
              <td class="text-truncate">Oneplus 9 Pro</td>
              <td class="text-truncate">Dubai</td>
              <td class="text-truncate">14, July 2021 15:15</td>
            </tr>
            <tr>
              <td class="text-truncate"><img src="{{asset('assets/img/icons/brands/chrome.png')}}" alt="Chrome" class="me-3" width="22" height="22"><span class="text-heading">Chrome on MacOS</span></td>
              <td class="text-truncate">Apple iMac</td>
              <td class="text-truncate">India</td>
              <td class="text-truncate">16, July 2021 16:17</td>
            </tr>
            <tr>
              <td class="text-truncate"><img src="{{asset('assets/img/icons/brands/chrome.png')}}" alt="Chrome" class="me-3" width="22" height="22"><span class="text-heading">Chrome on Windows</span></td>
              <td class="text-truncate">HP Spectre 360</td>
              <td class="text-truncate">Switzerland</td>
              <td class="text-truncate">20, July 2021 21:01</td>
            </tr>
            <tr>
              <td class="text-truncate border-bottom-0"><img src="{{asset('assets/img/icons/brands/chrome.png')}}" alt="Chrome" class="me-3" width="22" height="22"><span class="text-heading">Chrome on Android</span></td>
              <td class="text-truncate border-bottom-0">Oneplus 9 Pro</td>
              <td class="text-truncate border-bottom-0">Dubai</td>
              <td class="text-truncate border-bottom-0">21, July 2021 12:22</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <!--/ Recent Devices -->
  </div>
  <!--/ User Content -->
</div>

<!-- Modals -->
@include('_partials/_modals/modal-edit-user')
@include('_partials/_modals/modal-enable-otp')
@include('_partials/_modals/modal-upgrade-plan')
<!-- /Modals -->

@endsection
