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
<script src="{{asset('assets/js/modal-edit-cc.js')}}"></script>
<script src="{{asset('assets/js/modal-add-new-cc.js')}}"></script>
<script src="{{asset('assets/js/modal-add-new-address.js')}}"></script>
<script src="{{asset('assets/js/app-user-view.js')}}"></script>
<script src="{{asset('assets/js/app-user-view-billing.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">User / View /</span> Billing & Plans
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
        <div class="progress mb-1 rounded" style="height: 6px;">
          <div class="progress-bar rounded" role="progressbar" style="width: 80%;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <span>6 days remaining</span>
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
      <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i class="mdi mdi-bookmark-outline mdi-20px me-1"></i>Billing & Plans</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('app/user/view/notifications')}}"><i class="mdi mdi-bell-outline mdi-20px me-1"></i>Notifications</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('app/user/view/connections')}}"><i class="mdi mdi-link mdi-20px me-1"></i>Connections</a></li>
    </ul>
    <!--/ User Tabs -->

    <!-- Current Plan -->
    <div class="card mb-4">
      <h5 class="card-header">Current Plan</h5>
      <div class="card-body">
        <div class="row">
          <div class="col-xl-6 order-1 order-xl-0">
            <div class="mb-4">
              <p class="mb-1">Your Current Plan is <span class="fw-semibold text-heading">Basic</span></p>
              <p>A simple start for everyone</p>
            </div>
            <div class="mb-4">
              <h6 class="mb-1 fw-semibold">Active until Dec 09, 2021</h6>
              <p>We will send you a notification upon Subscription expiration</p>
            </div>
            <div class="mb-4">
              <h6 class="mb-1 fw-semibold"><span class="me-2">$199 Per Month</span> <span class="badge bg-label-primary">Popular</span></h6>
              <p>Standard plan for small to medium businesses</p>
            </div>
          </div>
          <div class="col-xl-6 order-0 order-xl-0">
            <div class="alert alert-warning mb-4" role="alert">
              <h6 class="alert-heading mb-1">We need your attention!</h6>
              <span>Your plan requires update</span>
            </div>
            <div class="plan-statistics">
              <div class="d-flex justify-content-between">
                <h6 class="mb-2 fw-semibold">Days</h6>
                <h6 class="mb-2 fw-semibold">24 of 30 Days</h6>
              </div>
              <div class="progress mb-1 rounded" style="height: 10px;">
                <div class="progress-bar w-75 rounded " role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <p>6 days remaining until your plan requires update</p>
            </div>
          </div>
          <div class="col-12 order-2 order-xl-0">
            <button class="btn btn-primary me-2 my-2" data-bs-toggle="modal" data-bs-target="#upgradePlanModal">Upgrade Plan</button>
            <button class="btn btn-outline-danger cancel-subscription">Cancel Subscription</button>
          </div>
        </div>
      </div>
    </div>
    <!-- /Current Plan -->

    <!-- Payment Methods -->
    <div class="card card-action mb-4">
      <div class="card-header align-items-center">
        <h5 class="card-action-title mb-0">Payment Methods</h5>
        <div class="card-action-element">
          <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#addNewCCModal"><i class="mdi mdi-plus me-1"></i>Add Card</button>
        </div>
      </div>
      <div class="card-body">
        <div class="added-cards">
          <div class="cardMaster border p-3 rounded mb-3">
            <div class="d-flex justify-content-between flex-sm-row flex-column">
              <div class="card-information">
                <img class="mb-3 img-fluid" src="{{asset('assets/img/icons/payments/mastercard.png')}}" alt="Master Card">
                <div class="d-flex align-items-center mb-1">
                  <h6 class="mb-0 me-3 fw-semibold">Kaith Morrison</h6>
                  <span class="badge bg-label-primary me-1">Popular</span>
                </div>
                <span class="card-number">&#8727;&#8727;&#8727;&#8727; &#8727;&#8727;&#8727;&#8727; &#8727;&#8727;&#8727;&#8727; 9856</span>
              </div>
              <div class="d-flex flex-column text-start text-lg-end">
                <div class="d-flex order-sm-0 order-1 mt-3">
                  <button class="btn btn-outline-primary me-3" data-bs-toggle="modal" data-bs-target="#editCCModal">Edit</button>
                  <button class="btn btn-outline-secondary">Delete</button>
                </div>
                <small class="mt-sm-auto mt-2 order-sm-1 order-0 text-end">Card expires at 12/26</small>
              </div>
            </div>
          </div>
          <div class="cardMaster border p-3 rounded mb-3">
            <div class="d-flex justify-content-between flex-sm-row flex-column">
              <div class="card-information">
                <img class="mb-3 img-fluid" src="{{asset('assets/img/icons/payments/visa.png')}}" alt="Master Card">
                <h6 class="mb-1 fw-semibold">Tom McBride</h6>
                <span class="card-number">&#8727;&#8727;&#8727;&#8727; &#8727;&#8727;&#8727;&#8727; &#8727;&#8727;&#8727;&#8727; 6542</span>
              </div>
              <div class="d-flex flex-column text-start text-lg-end">
                <div class="d-flex order-sm-0 order-1 my-3">
                  <button class="btn btn-outline-primary me-3" data-bs-toggle="modal" data-bs-target="#editCCModal">Edit</button>
                  <button class="btn btn-outline-secondary">Delete</button>
                </div>
                <small class="mt-sm-auto mt-2 order-sm-1 order-0 text-end">Card expires at 10/24</small>
              </div>
            </div>
          </div>
          <div class="cardMaster border p-3 rounded">
            <div class="d-flex justify-content-between flex-sm-row flex-column">
              <div class="card-information">
                <img class="mb-3 img-fluid" src="{{asset('assets/img/icons/payments/american-ex.png')}}" alt="Visa Card">
                <div class="d-flex align-items-center mb-1">
                  <h6 class="mb-0 me-3 fw-semibold">Mildred Wagner</h6>
                  <span class="badge bg-label-danger me-1">Expired</span>
                </div>
                <span class="card-number">&#8727;&#8727;&#8727;&#8727; &#8727;&#8727;&#8727;&#8727; &#8727;&#8727;&#8727;&#8727; 5896</span>
              </div>
              <div class="d-flex flex-column text-start text-lg-end">
                <div class="d-flex order-sm-0 order-1 mt-3">
                  <button class="btn btn-outline-primary me-3" data-bs-toggle="modal" data-bs-target="#editCCModal">Edit</button>
                  <button class="btn btn-outline-secondary">Delete</button>
                </div>
                <small class="mt-sm-auto mt-2 order-sm-1 order-0 text-end">Card expires at 10/27</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--/ Payment Methods -->

    <!-- Billing Address -->
    <div class="card card-action mb-4">
      <div class="card-header align-items-center">
        <h5 class="card-action-title mb-0">Billing Address</h5>
        <div class="card-action-element">
          <button class="btn btn-primary btn-sm edit-address" type="button" data-bs-toggle="modal" data-bs-target="#addNewAddress">Edit address</button>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-xl-7 col-12">
            <dl class="row mb-0">
              <dt class="col-sm-4 mb-3 text-nowrap fw-semibold text-heading">Company Name:</dt>
              <dd class="col-sm-8">{{ config('variables.templateName') }}</dd>

              <dt class="col-sm-4 mb-3 text-nowrap fw-semibold text-heading">Billing Email:</dt>
              <dd class="col-sm-8">user@ex.com</dd>

              <dt class="col-sm-4 mb-3 text-nowrap fw-semibold text-heading">Tax ID:</dt>
              <dd class="col-sm-8">TAX-357378</dd>

              <dt class="col-sm-4 mb-3 text-nowrap fw-semibold text-heading">VAT Number:</dt>
              <dd class="col-sm-8">SDF754K77</dd>

              <dt class="col-sm-4 mb-3 text-nowrap fw-semibold text-heading mb-0">Billing Address:</dt>
              <dd class="col-sm-8 mb-0">100 Water Plant <br>Avenue, Building 1303<br> Wake Island</dd>
            </dl>
          </div>
          <div class="col-xl-5 col-12">
            <dl class="row mb-0">
              <dt class="col-sm-4 mb-3 text-nowrap fw-semibold text-heading">Contact:</dt>
              <dd class="col-sm-8">+1 (605) 977-32-65</dd>

              <dt class="col-sm-4 mb-3 text-nowrap fw-semibold text-heading">Country:</dt>
              <dd class="col-sm-8">Wake Island</dd>

              <dt class="col-sm-4 mb-3 text-nowrap fw-semibold text-heading">State:</dt>
              <dd class="col-sm-8">Capholim</dd>

              <dt class="col-sm-4 mb-3 text-nowrap fw-semibold text-heading">Zipcode:</dt>
              <dd class="col-sm-8">403114</dd>
            </dl>
          </div>
        </div>
      </div>
    </div>
    <!--/ Billing Address -->

  </div>
  <!--/ User Content -->
</div>

<!-- Modal -->
@include('_partials/_modals/modal-edit-user')
@include('_partials/_modals/modal-edit-cc')
@include('_partials/_modals/modal-add-new-address')
@include('_partials/_modals/modal-add-new-cc')
@include('_partials/_modals/modal-upgrade-plan')
<!-- /Modal -->

@endsection
