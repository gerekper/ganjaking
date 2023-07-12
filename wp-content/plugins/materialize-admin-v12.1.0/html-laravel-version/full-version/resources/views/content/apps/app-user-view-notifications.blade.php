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
<script src="{{asset('assets/js/app-user-view.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">User / View /</span> Notifications
</h4>
<div class="row">
  <!-- User Sidebar -->
  <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
    <!-- User Card -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="user-avatar-section">
          <div class=" d-flex align-items-center flex-column">
            <img class="img-fluid rounded mt-4 mb-3" src="{{asset('assets/img/avatars/10.png')}}" height="120" width="120" alt="User avatar" />
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
          <span class="text-heading fw-semibold">80% Completed</span>
        </div>
        <div class="progress mb-1 rounded" style="height: 8px;">
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
      <li class="nav-item"><a class="nav-link" href="{{url('app/user/view/security')}}"><i class="mdi mdi-lock-open-outline mdi-20px me-1"></i>Security</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('app/user/view/billing')}}"><i class="mdi mdi-bookmark-outline mdi-20px me-1"></i>Billing & Plans</a></li>
      <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i class="mdi mdi-bell-outline mdi-20px me-1"></i>Notifications</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('app/user/view/connections')}}"><i class="mdi mdi-link mdi-20px me-1"></i>Connections</a></li>
    </ul>
    <!--/ User Tabs -->

    <!-- Project table -->
    <div class="card mb-4">
      <!-- Notifications -->
      <h5 class="card-header border-bottom">Recent Devices</h5>
      <div class="card-body py-3">
        <span class="text-heading fw-semibold">Change to notification settings, the user will get the update</span>
      </div>
      <div class="table-responsive">
        <table class="table border-top">
          <thead class="table-light">
            <tr>
              <th class="text-nowrap">Type</th>
              <th class="text-nowrap text-center">✉️ Email</th>
              <th class="text-nowrap text-center">🖥 Browser</th>
              <th class="text-nowrap text-center">👩🏻‍💻 App</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="text-nowrap">New for you</td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck1" checked />
                </div>
              </td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck2" checked />
                </div>
              </td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck3" checked />
                </div>
              </td>
            </tr>
            <tr>
              <td class="text-nowrap">Account activity</td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck4" checked />
                </div>
              </td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck5" checked />
                </div>
              </td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck6" checked />
                </div>
              </td>
            </tr>
            <tr>
              <td class="text-nowrap">A new browser used to sign in</td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck7" checked />
                </div>
              </td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck8" checked />
                </div>
              </td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck9" />
                </div>
              </td>
            </tr>
            <tr>
              <td class="text-nowrap">A new device is linked</td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck10" checked />
                </div>
              </td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck11" />
                </div>
              </td>
              <td>
                <div class="form-check d-flex justify-content-center">
                  <input class="form-check-input" type="checkbox" id="defaultCheck12" />
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="card-body">
        <button type="submit" class="btn btn-primary me-2">Save changes</button>
        <button type="reset" class="btn btn-outline-secondary">Discard</button>
      </div>
      <!-- /Notifications -->
    </div>
    <!-- /Project table -->
  </div>
  <!--/ User Content -->
</div>

<!-- Modals -->
@include('_partials/_modals/modal-edit-user')
@include('_partials/_modals/modal-upgrade-plan')
<!-- /Modals -->

@endsection
