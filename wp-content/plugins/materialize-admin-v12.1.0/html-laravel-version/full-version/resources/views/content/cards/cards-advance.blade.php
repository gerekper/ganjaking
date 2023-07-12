@extends('layouts/layoutMaster')

@section('title', 'Cards Advance- UI elements')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/modal-add-new-cc.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">UI Elements /</span> Cards Advance
</h4>

<div class="row">
  <!-- Transactions -->
  <div class="col-lg-6 col-xl-4 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Transactions</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="transactionID" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="mdi mdi-dots-vertical mdi-24px"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
            <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <ul class="p-0 m-0">
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <div class="avatar-initial bg-label-success rounded">
                <div>
                  <img src="{{asset('assets/img/icons/payments/credit-card.png')}}" alt="credit-card">
                </div>
              </div>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-1  fw-semibold">Credit Card</h6>
                <span class="text-muted">Digital Ocean</span>
              </div>
              <div class="user-progress d-flex align-items-center gap-1">
                <h6 class="mb-0 fw-semibold">-$850</h6>
                <i class='mdi mdi-chevron-down text-danger'></i>
              </div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <div class="avatar-initial bg-label-primary rounded">
                <div>
                  <img src="{{asset('assets/img/icons/payments/paypal-primary.png')}}" alt="paypal">
                </div>
              </div>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-1 fw-semibold">Paypal</h6>
                <span class="text-muted">Received Money</span>
              </div>
              <div class="user-progress d-flex align-items-center gap-1">
                <h6 class="mb-0 fw-semibold">+$34,456</h6>
                <i class='mdi mdi-chevron-up text-success'></i>
              </div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <div class="avatar-initial bg-label-info rounded">
                <div>
                  <img src="{{asset('assets/img/icons/payments/mastercard-info.png')}}" alt="mastercard">
                </div>
              </div>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-1 fw-semibold">Mastercard</h6>
                <span class="text-muted">Netflix</span>
              </div>
              <div class="user-progress d-flex align-items-center gap-1">
                <h6 class="mb-0 fw-semibold">-$199</h6>
                <i class='mdi mdi-chevron-down text-danger'></i>
              </div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <div class="avatar-initial bg-label-danger rounded">
                <div>
                  <img src="{{asset('assets/img/icons/payments/wallet.png')}}" alt="wallet">
                </div>
              </div>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-1 fw-semibold">Wallet</h6>
                <span class="text-muted">Mac'D</span>
              </div>
              <div class="user-progress d-flex align-items-center gap-1">
                <h6 class="mb-0 fw-semibold">-$156</h6>
                <i class='mdi mdi-chevron-down text-danger'></i>
              </div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <div class="avatar-initial bg-label-primary rounded">
                <div>
                  <img src="{{asset('assets/img/icons/payments/paypal-primary.png')}}" alt="paypal">
                </div>
              </div>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-1 fw-semibold">Paypal</h6>
                <span class="text-muted">Refund</span>
              </div>
              <div class="user-progress d-flex align-items-center gap-1">
                <h6 class="mb-0 fw-semibold">+$12,872</h6>
                <i class='mdi mdi-chevron-up text-success'></i>
              </div>
            </div>
          </li>
          <li class="d-flex">
            <div class="avatar flex-shrink-0 me-3">
              <div class="avatar-initial bg-label-warning rounded">
                <div>
                  <img src="{{asset('assets/img/icons/payments/Stripe.png')}}" alt="Stripe">
                </div>
              </div>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-1 fw-semibold">Stripe</h6>
                <span class="text-muted">Buy Apple Watch</span>
              </div>
              <div class="user-progress d-flex align-items-center gap-1">
                <h6 class="mb-0 fw-semibold">-$299</h6>
                <i class='mdi mdi-chevron-down text-danger'></i>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <!--/ Transactions -->

  <!-- Upgrade Plan -->
  <div class="col-lg-6 col-xl-4 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Upgrade Your Plan</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="upgradePlanCard" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="mdi mdi-dots-vertical mdi-24px"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="upgradePlanCard">
            <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <span class="text-muted">Please make the payment to start enjoying all the features of our premium plan as soon as possible.</span>
        <div class="d-flex bg-label-primary p-2 border rounded my-3">
          <div class="border border-2 border-primary rounded me-3 p-2 d-flex align-items-center justify-content-center w-px-40 h-px-40">
            <i class="mdi mdi-star-outline mdi-24px"></i>
          </div>
          <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
            <div class="me-2">
              <h6 class="mb-0 fw-semibold">Platinum</h6>
              <a href="javascript:void(0)" class="small" data-bs-target="#upgradePlanModal" data-bs-toggle="modal">Upgrade Plan</a>
            </div>
            <div class="user-progress">
              <div class="d-flex justify-content-center">
                <sup class="mt-3 mb-0 text-heading small">$</sup>
                <h3 class="fw-medium mb-0">2,124</h3>
                <sub class="mt-auto mb-2 text-heading small"> /Year</sub>
              </div>
            </div>
          </div>
        </div>
        <form id="paymentDetailsForm" onsubmit="return false">
          <h6 class="mb-3 pb-1 fw-semibold">Payment Details</h6>
          <div class="d-flex mb-2">
            <div class="me-3">
              <img src="{{asset('assets/img/icons/payments/logo-mastercard-small.png')}}" alt="credit card" height="30">
            </div>
            <div class="d-flex justify-content-between w-100 flex-wrap gap-2">
              <div class="d-flex flex-column">
                <h6 class="mb-0 fw-semibold">Credit Card</h6>
                <small class="text-muted">5688 xxxx xxxx 2356</small>
              </div>
              <div class="w-px-75">
                <input type="text" class="form-control cvv-code-payment" maxlength="3" placeholder="CVV" />
              </div>
            </div>
          </div>
          <div class="d-flex mb-2">
            <div class="me-3">
              <img src="{{asset('assets/img/icons/payments/logo-credit-card-2.png')}}" alt="credit card" height="30">
            </div>
            <div class="d-flex justify-content-between w-100 flex-wrap gap-2">
              <div class="d-flex flex-column">
                <h6 class="mb-0 fw-semibold">Credit Card</h6>
                <small class="text-muted">8562 xxxx xxxx 4563</small>
              </div>
              <div class="w-px-75">
                <input type="text" class="form-control cvv-code-payment" maxlength="3" placeholder="CVV" />
              </div>
            </div>
          </div>
          <a href="javascript:;" class="d-block my-2 small" data-bs-toggle="modal" data-bs-target="#addNewCCModal"> Add Payment Method </a>
          <div class="col-12 mb-2 pb-1">
            <input id="addEmail" name="addEmail" class="form-control" type="text" placeholder="Email Address" />
          </div>
          <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary w-100">
              <span class="me-1">Process to payment</span>
              <i class="mdi mdi-arrow-right scaleX-n1-rtl"></i> </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!--/ Upgrade Plan -->

  <!-- Meeting Schedule -->
  <div class="col-lg-6 col-xl-4 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Meeting Schedule</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="meetingSchedule" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="mdi mdi-dots-vertical mdi-24px"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="meetingSchedule">
            <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <ul class="p-0 m-0">
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <img src="{{asset('assets/img/avatars/4.png')}}" alt="avatar" class="rounded">
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0 fw-semibold">Call with Woods</h6>
                <small class="text-muted">
                  <i class="mdi mdi-calendar-blank-outline mdi-14px"></i>
                  <span>21 Jul | 08:20-10:30</span>
                </small>
              </div>
              <div class="badge bg-label-primary rounded-pill">Business</div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <img src="{{asset('assets/img/avatars/5.png')}}" alt="avatar" class="rounded">
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0 fw-semibold">Conference call</h6>
                <small class="text-muted">
                  <i class="mdi mdi-calendar-blank-outline mdi-14px"></i>
                  <span>21 Jul | 08:20-10:30</span>
                </small>
              </div>
              <div class="badge bg-label-warning rounded-pill">Dinner</div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <img src="{{asset('assets/img/avatars/3.png')}}" alt="avatar" class="rounded">
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0 fw-semibold">Meeting with Mark</h6>
                <small class="text-muted">
                  <i class="mdi mdi-calendar-blank-outline mdi-14px"></i>
                  <span>21 Jul | 08:20-10:30</span>
                </small>
              </div>
              <div class="badge bg-label-secondary rounded-pill">Meetup</div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <img src="{{asset('assets/img/avatars/14.png')}}" alt="avatar" class="rounded">
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0 fw-semibold">Meeting in Oakland</h6>
                <small class="text-muted">
                  <i class="mdi mdi-calendar-blank-outline mdi-14px"></i>
                  <span>21 Jul | 08:20-10:30</span>
                </small>
              </div>
              <div class="badge bg-label-danger rounded-pill">Dinner</div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <img src="{{asset('assets/img/avatars/8.png')}}" alt="avatar" class="rounded">
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0 fw-semibold">Call with hilda</h6>
                <small class="text-muted">
                  <i class="mdi mdi-calendar-blank-outline mdi-14px"></i>
                  <span>21 Jul | 08:20-10:30</span>
                </small>
              </div>
              <div class="badge bg-label-success rounded-pill">Meditation</div>
            </div>
          </li>
          <li class="d-flex">
            <div class="avatar flex-shrink-0 me-3">
              <img src="{{asset('assets/img/avatars/1.png')}}" alt="avatar" class="rounded">
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0 fw-semibold">Meeting with Carl</h6>
                <small class="text-muted">
                  <i class="mdi mdi-calendar-blank-outline mdi-14px"></i>
                  <span>21 Jul | 08:20-10:30</span>
                </small>
              </div>
              <div class="badge bg-label-primary rounded-pill">Business</div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <!--/ Meeting Schedule -->

  <!-- Project Statistics -->
  <div class="col-lg-6 col-xl-4 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Project Statistics</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="projectStatus" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="mdi mdi-dots-vertical mdi-24px"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="projectStatus">
            <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <ul class="p-0 m-0">
          <li class="d-flex mb-4 justify-content-between">
            <h6 class="mb-0 small">NAME</h6>
            <h6 class="mb-0 small">BUDGET</h6>
          </li>
          <li class="d-flex mb-4">
            <div class="avatar avatar-md flex-shrink-0 me-3">
              <div class="avatar-initial bg-lighter rounded">
                <div>
                  <img src="{{asset('assets/img/icons/misc/3d-illustration.png')}}" alt="User" class="h-25">
                </div>
              </div>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">3D Illustration</h6>
                <small class="text-muted">Blender Illustration</small>
              </div>
              <div class="badge bg-label-primary rounded-pill">$6,500</div>
            </div>
          </li>
          <li class="d-flex mb-4">
            <div class="avatar avatar-md flex-shrink-0 me-3">
              <div class="avatar-initial bg-lighter rounded">
                <div>
                  <img src="{{asset('assets/img/icons/misc/finance-app-design.png')}}" alt="User" class="h-25">
                </div>
              </div>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">Finance App Design</h6>
                <small class="text-muted">Figma UI Kit</small>
              </div>
              <div class="badge bg-label-primary rounded-pill">$4,290</div>
            </div>
          </li>
          <li class="d-flex mb-4">
            <div class="avatar avatar-md flex-shrink-0 me-3">
              <div class="avatar-initial bg-lighter rounded">
                <div>
                  <img src="{{asset('assets/img/icons/misc/4-square.png')}}" alt="User" class="h-25">
                </div>
              </div>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">4 Square</h6>
                <small class="text-muted">Android Application</small>
              </div>
              <div class="badge bg-label-primary rounded-pill">$44,500</div>
            </div>
          </li>
          <li class="d-flex mb-4">
            <div class="avatar avatar-md flex-shrink-0 me-3">
              <div class="avatar-initial bg-lighter rounded">
                <div>
                  <img src="{{asset('assets/img/icons/misc/delta-web-app.png')}}" alt="User" class="h-25">
                </div>
              </div>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">Delta Web App</h6>
                <small class="text-muted">React Dashboard</small>
              </div>
              <div class="badge bg-label-primary rounded-pill">$12,690</div>
            </div>
          </li>
          <li class="d-flex">
            <div class="avatar avatar-md flex-shrink-0 me-3">
              <div class="avatar-initial bg-lighter rounded">
                <div>
                  <img src="{{asset('assets/img/icons/misc/ecommerce-website.png')}}" alt="User" class="h-25">
                </div>
              </div>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">eCommerce Website</h6>
                <small class="text-muted">Vue + Laravel</small>
              </div>
              <div class="badge bg-label-primary rounded-pill">$10,850</div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <!--/ Project Statistics -->

  <!-- Top Referral Source  -->
  <div class="col-12 col-xl-8 mb-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <div class="card-title m-0">
          <h5 class="mb-0">Top Referral Sources</h5>
          <small class="text-muted">82% Activity Growth</small>
        </div>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="earningReportsTabsId" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="mdi mdi-dots-vertical mdi-24px"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="earningReportsTabsId">
            <a class="dropdown-item" href="javascript:void(0);">View More</a>
            <a class="dropdown-item" href="javascript:void(0);">Delete</a>
          </div>
        </div>
      </div>
      <div class="card-body pb-1">
        <ul class="nav nav-tabs nav-tabs-widget pb-3 gap-4 mx-1 d-flex flex-nowrap" role="tablist">
          <li class="nav-item">
            <a href="javascript:void(0);" class="nav-link btn active d-flex flex-column align-items-center justify-content-center" role="tab" data-bs-toggle="tab" data-bs-target="#navs-orders-id" aria-controls="navs-orders-id" aria-selected="true">
              <div class="avatar">
                <div class="avatar-initial bg-label-secondary rounded">
                  <i class="mdi mdi-cellphone"></i>
                </div>
              </div>
            </a>
          </li>
          <li class="nav-item">
            <a href="javascript:void(0);" class="nav-link btn d-flex flex-column align-items-center justify-content-center" role="tab" data-bs-toggle="tab" data-bs-target="#navs-sales-id" aria-controls="navs-sales-id" aria-selected="false">
              <div class="avatar">
                <div class="avatar-initial bg-label-secondary rounded">
                  <i class="mdi mdi-television"></i>
                </div>
              </div>
            </a>
          </li>
          <li class="nav-item">
            <a href="javascript:void(0);" class="nav-link btn d-flex flex-column align-items-center justify-content-center" role="tab" data-bs-toggle="tab" data-bs-target="#navs-profit-id" aria-controls="navs-profit-id" aria-selected="false">
              <div class="avatar">
                <div class="avatar-initial bg-label-secondary rounded">
                  <i class="mdi mdi-gamepad-circle-outline"></i>
                </div>
              </div>
            </a>
          </li>
          <li class="nav-item">
            <a href="javascript:void(0);" class="nav-link btn d-flex flex-column align-items-center justify-content-center" role="tab" data-bs-toggle="tab" data-bs-target="#navs-income-id" aria-controls="navs-income-id" aria-selected="false">
              <div class="avatar">
                <div class="avatar-initial bg-label-secondary rounded">
                  <i class="mdi mdi-watch"></i>
                </div>
              </div>
            </a>
          </li>
          <li class="nav-item">
            <a href="javascript:void(0);" class="nav-link btn d-flex align-items-center justify-content-center disabled" role="tab" data-bs-toggle="tab" aria-selected="false">
              <div class="avatar">
                <div class="avatar-initial bg-label-secondary rounded">
                  <i class="mdi mdi-plus"></i>
                </div>
              </div>
            </a>
          </li>
        </ul>
        <div class="tab-content p-0 ms-0 ms-sm-2">
          <div class="tab-pane fade show active" id="navs-orders-id" role="tabpanel">
            <div class="table-responsive text-nowrap">
              <table class="table table-borderless">
                <thead>
                  <tr>
                    <th class="ps-0 fw-medium">Image</th>
                    <th class="fw-medium ps-0">Product Name</th>
                    <th class="pe-0 fw-medium text-end">Qty</th>
                    <th class="pe-0 fw-medium text-end">Price</th>
                    <th class="pe-0 text-end fw-medium">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets/img/products/samsung-s22.png')}}" alt="samsung" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">Oneplus 9 Pro</td>
                    <td class="text-heading text-end pe-0">4</td>
                    <td class="text-heading text-end pe-0">$599</td>
                    <td class="pe-0 text-end fw-semibold h6">$2,396</td>
                  </tr>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets/img/products/apple-iPhone-13-pro.png')}}" alt="iphone" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">Apple iPhone 13 Pro</td>
                    <td class="text-heading text-end pe-0">1</td>
                    <td class="text-heading text-end pe-0">$399</td>
                    <td class="pe-0 text-end fw-semibold h6">$399</td>
                  </tr>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets/img/products/oneplus-9-pro.png')}}" alt="us-flag" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">Oneplus 9 Pro</td>
                    <td class="text-heading text-end pe-0">4</td>
                    <td class="text-heading text-end pe-0">$599</td>
                    <td class="pe-0 text-end fw-semibold h6">$2,396</td>
                  </tr>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets/img/products/google-pixel-6.png')}}" alt="us-flag" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">Google Pixel 6</td>
                    <td class="text-heading text-end pe-0">3</td>
                    <td class="text-heading text-end pe-0">$450</td>
                    <td class="pe-0 text-end fw-semibold h6">$1,350</td>
                  </tr>

                </tbody>

              </table>
            </div>
          </div>
          <div class="tab-pane fade" id="navs-sales-id" role="tabpanel">
            <div class="table-responsive text-nowrap">
              <table class="table table-borderless">
                <thead>
                  <tr>
                    <th class="ps-0 fw-medium">Image</th>
                    <th class="fw-medium ps-0">Product Name</th>
                    <th class="pe-0 fw-medium text-end">Qty</th>
                    <th class="pe-0 fw-medium text-end">Price</th>
                    <th class="pe-0 text-end fw-medium">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets/img/products/apple-mac-mini.png')}}" alt="mac-mini" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">Apple Mac Mini</td>
                    <td class="text-heading text-end pe-0">2</td>
                    <td class="text-heading text-end pe-0">$849</td>
                    <td class="pe-0 text-end fw-semibold h6">$1,698</td>
                  </tr>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets/img/products/hp-envy-x360.png')}}" alt="hp-envy" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">Newest HP Envy x360</td>
                    <td class="text-heading text-end pe-0">4</td>
                    <td class="text-heading text-end pe-0">$599</td>
                    <td class="pe-0 text-end fw-semibold h6">$2,399</td>
                  </tr>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets/img/products/dell-inspiron-3000.png')}}" alt="dell" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">Dell Inspiron 3000</td>
                    <td class="text-heading text-end pe-0">1</td>
                    <td class="text-heading text-end pe-0">$399</td>
                    <td class="pe-0 text-end fw-semibold h6">$399</td>
                  </tr>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets/img/products/apple-iMac-4k.png')}}" alt="apple-iMac" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">Apple iMac 4k</td>
                    <td class="text-heading text-end pe-0">3</td>
                    <td class="text-heading text-end pe-0">$450</td>
                    <td class="pe-0 text-end fw-semibold h6">$1,350</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="tab-pane fade" id="navs-profit-id" role="tabpanel">
            <div class="table-responsive text-nowrap">
              <table class="table table-borderless">
                <thead>
                  <tr>
                    <th class="ps-0 fw-medium">Image</th>
                    <th class="fw-medium ps-0">Product Name</th>
                    <th class="pe-0 fw-medium text-end">Qty</th>
                    <th class="pe-0 fw-medium text-end">Price</th>
                    <th class="pe-0 text-end fw-medium">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets/img/products/sony-play-station-5.png')}}" alt="sony-play-station" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">Sony Play Station 5</td>
                    <td class="text-heading text-end pe-0">1</td>
                    <td class="text-heading text-end pe-0">$599</td>
                    <td class="pe-0 text-end fw-semibold h6">$1,698</td>
                  </tr>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets/img/products/xbox-series-x.png')}}" alt="xbox" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">XBOX Series X</td>
                    <td class="text-heading text-end pe-0">3</td>
                    <td class="text-heading text-end pe-0">$489</td>
                    <td class="pe-0 text-end fw-semibold h6">$1,467</td>
                  </tr>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets/img/products/nintendo-switch.png')}}" alt="nintendo-switch" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">Nintendo Switch</td>
                    <td class="text-heading text-end pe-0">4</td>
                    <td class="text-heading text-end pe-0">$335</td>
                    <td class="pe-0 text-end fw-semibold h6">$1,340</td>
                  </tr>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets/img/products/sup-game-box-400.png')}}" alt="game" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">SUP Game Box 400</td>
                    <td class="text-heading text-end pe-0">8</td>
                    <td class="text-heading text-end pe-0">$14</td>
                    <td class="pe-0 text-end fw-semibold h6">$112</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="tab-pane fade" id="navs-income-id" role="tabpanel">
            <div class="table-responsive text-nowrap">
              <table class="table table-borderless">
                <thead>
                  <tr>
                    <th class="ps-0 fw-medium">Image</th>
                    <th class="fw-medium ps-0">Product Name</th>
                    <th class="pe-0 fw-medium text-end">Qty</th>
                    <th class="pe-0 fw-medium text-end">Price</th>
                    <th class="pe-0 text-end fw-medium">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets/img/products/samsung-watch-4.png')}}" alt="samsung-watch" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">Samsung Watch 4</td>
                    <td class="text-heading text-end pe-0">2</td>
                    <td class="text-heading text-end pe-0">$202</td>
                    <td class="pe-0 text-end fw-semibold h6">$404</td>
                  </tr>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets/img/products/apple-watch-7.png')}}" alt="apple-watch" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">Apple Watch 7</td>
                    <td class="text-heading text-end pe-0">1</td>
                    <td class="text-heading text-end pe-0">$399</td>
                    <td class="pe-0 text-end fw-semibold h6">$399</td>
                  </tr>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets/img/products/amazon-echo-dot.png')}}" alt="amazon-echo-dot" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">Amazon Echo Dot</td>
                    <td class="text-heading text-end pe-0">3</td>
                    <td class="text-heading text-end pe-0">$59</td>
                    <td class="pe-0 text-end fw-semibold h6">$177</td>
                  </tr>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets/img/products/gramin-verve.png')}}" alt="gramin-verve" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">Gramin Verve</td>
                    <td class="text-heading text-end pe-0">1</td>
                    <td class="text-heading text-end pe-0">$139</td>
                    <td class="pe-0 text-end fw-semibold h6">$139</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Top Referral Source  -->

  <!-- Total Earnings -->
  <div class="col-lg-6 col-xl-4 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Total Earnings</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="totalEarnings" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="mdi mdi-dots-vertical mdi-24px"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="totalEarnings">
            <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="mb-4 mt-3 pb-2">
          <div class="d-flex align-items-center">
            <h2 class="mb-0">$42,880</h2>
            <span class="text-success ms-2 fw-semibold">
              <i class="mdi mdi-menu-up"></i>
              <small>22%</small>
            </span>
          </div>
          <small class="text-muted mt-1">Compared to $84,725 last year</small>
        </div>
        <div class="mb-4 pb-1">
          <div class="d-flex justify-content-between gap-3">
            <h6 class="mb-2 fw-semibold">Amazon</h6>
            <span class="text-heading fw-semibold">$24,453</span>
          </div>
          <div class="progress w-100 rounded bg-label-primary" style="height: 6px;">
            <div class="progress-bar bg-primary rounded" style="width: 75%" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
        <div class="mb-4 pb-1">
          <div class="d-flex justify-content-between gap-3">
            <h6 class="mb-2 fw-semibold">Flipkart</h6>
            <span class="text-heading fw-semibold">$12,763</span>
          </div>
          <div class="progress w-100 rounded bg-label-success" style="height: 6px;">
            <div class="progress-bar bg-success rounded" style="width: 55%" role="progressbar" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
        <div>
          <div class="d-flex justify-content-between gap-3">
            <h6 class="mb-2 fw-semibold">eBay</h6>
            <span class="text-heading fw-semibold">$4,978</span>
          </div>
          <div class="progress w-100 rounded bg-label-danger" style="height: 6px;">
            <div class="progress-bar bg-danger rounded" style="width: 35%" role="progressbar" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Total Earnings -->

  <!-- Social Network Visits -->
  <div class="col-lg-6 col-xl-4 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Social Network Visits</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="socialNetworkList" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="mdi mdi-dots-vertical mdi-24px"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="socialNetworkList">
            <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <div class="d-flex align-items-center mb-1">
            <h4 class="mb-0">28,468</h4>
            <span class="text-success ms-2 fw-semibold">
              <i class="mdi mdi-menu-up"></i>
              <small>62%</small>
            </span>
          </div>
          <small class="text-muted">Last 1 Year Visits</small>
        </div>
        <ul class="p-0 m-0">
          <li class="d-flex mb-3">
            <div class="flex-shrink-0">
              <img src="{{asset('assets/img/icons/brands/facebook-rounded.png')}}" alt="facebook" class="me-3" height="34">
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">Facebook</h6>
                <small class="text-muted">Social Media</small>
              </div>
              <div class="d-flex align-items-center">
                <span class="fw-semibold text-heading">12,348</span>
                <div class="ms-3 badge bg-label-success rounded-pill">+12%</div>
              </div>
            </div>
          </li>
          <li class="d-flex mb-3">
            <div class="flex-shrink-0">
              <img src="{{asset('assets/img/icons/brands/dribbble-rounded.png')}}" alt="dribbble" class="me-3" height="34">
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">Dribbble</h6>
                <small class="text-muted">Community</small>
              </div>
              <div class="d-flex align-items-center">
                <span class="fw-semibold text-heading">8,450</span>
                <div class="ms-3 badge bg-label-success rounded-pill">+32%</div>
              </div>
            </div>
          </li>
          <li class="d-flex mb-3">
            <div class="flex-shrink-0">
              <img src="{{asset('assets/img/icons/brands/twitter-rounded.png')}}" alt="facebook" class="me-3" height="34">
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">Twitter</h6>
                <small class="text-muted">Social Media</small>
              </div>
              <div class="d-flex align-items-center">
                <span class="fw-semibold text-heading">350</span>
                <div class="ms-3 badge bg-label-danger rounded-pill">-18%</div>
              </div>
            </div>
          </li>
          <li class="d-flex">
            <div class="flex-shrink-0">
              <img src="{{asset('assets/img/icons/brands/instagram-rounded.png')}}" alt="instagram" class="me-3" height="34">
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">Instagram</h6>
                <small class="text-muted">Social Media</small>
              </div>
              <div class="d-flex align-items-center">
                <span class="fw-semibold text-heading">25,566</span>
                <div class="ms-3 badge bg-label-success rounded-pill">+42%</div>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <!--/ Social Network Visits -->

  <!-- General Statistics -->
  <div class="col-lg-6 col-xl-4 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">General Statistics</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="generalStatistics" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="mdi mdi-dots-vertical mdi-24px"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="generalStatistics">
            <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
          </div>
        </div>
      </div>
      <div class="card-body pb-3">
        <div class="mb-4 mt-2">
          <div class="d-flex align-items-center">
            <div class="avatar avatar-md">
              <div class="avatar-initial bg-label-primary rounded">
                <img src="{{ asset('assets/svg/icons/credit-card.svg')}}" alt="credit-card" class="w-px-30">
              </div>
            </div>
            <div class="ms-3">
              <h2 class="mb-0">$89,522</h2>
              <small class="text-muted">Last 6 Month Profit </small>
            </div>
          </div>
        </div>
        <div class="mb-4">
          <h6 class="mb-2 fw-semibold">Current Activity</h6>
          <div class="progress w-100 rounded bg-label-primary" style="height: 6px;">
            <div class="progress-bar bg-primary rounded" style="width: 85%" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
        <div class="table-responsive text-nowrap">
          <table class="table">
            <tbody class="table-border-bottom-0">
              <tr>
                <td class="ps-0 pe-5"><span class="badge badge-dot bg-primary me-2"></span> <span class="text-heading">Profit</span></td>
                <td class="ps-5 d-flex justify-content-end"><span class="text-heading fw-semibold">$54,234</span></td>
                <td class="pe-0">
                  <div class="d-flex align-items-center justify-content-end">
                    <span class="text-heading fw-semibold me-2">+85%</span>
                    <i class="mdi mdi-chevron-up mdi-20px text-success"></i>
                  </div>
                </td>
              </tr>
              <tr>
                <td class="ps-0 pe-5"><span class="badge badge-dot bg-warning me-2"></span><span class="text-heading">Sales</span></td>
                <td class="ps-5 d-flex justify-content-end"><span class="text-heading fw-semibold">8,657</span></td>
                <td class="pe-0">
                  <div class="d-flex align-items-center justify-content-end">
                    <span class="text-heading fw-semibold me-2">+42%</span>
                    <i class="mdi mdi-chevron-up mdi-20px text-success"></i>
                  </div>
                </td>
              </tr>
              <tr>
                <td class="ps-0 pe-5"><span class="badge badge-dot bg-info me-2"></span> <span class="text-heading">User</span></td>
                <td class="ps-5 d-flex justify-content-end"><span class="text-heading fw-semibold">16,456</span></td>
                <td class="pe-0">
                  <div class="d-flex align-items-center justify-content-end">
                    <span class="text-heading fw-semibold me-2">-12%</span>
                    <i class="mdi mdi-chevron-down mdi-20px text-danger"></i>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <!--/ General Statistics -->

  <!-- Most Sales in Countries -->
  <div class="col-lg-6 col-xl-4 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Most Sales in Countries</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="mostSales" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="mdi mdi-dots-vertical mdi-24px"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="mostSales">
            <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
          </div>
        </div>
      </div>
      <div class="card-body pb-1">
        <div class="mb-4 mt-1">
          <div class="d-flex align-items-center">
            <h1 class="mb-0 me-3 display-3">22,842</h1>
            <div class="badge bg-label-success rounded-pill">+42%</div>
          </div>
          <small class="text-muted mt-1">Sales Last 90 Days</small>
        </div>
        <div class="table-responsive text-nowrap border-top">
          <table class="table">
            <tbody class="table-border-bottom-0">
              <tr>
                <td class="ps-0 pe-5"><span class="text-heading">Australia</span></td>
                <td class="ps-5 d-flex justify-content-end"><span class="text-heading fw-semibold">18,879</span></td>
                <td class="pe-0">
                  <div class="d-flex align-items-center justify-content-end">
                    <span class="text-heading fw-semibold me-2">15%</span>
                    <i class="mdi mdi-chevron-down mdi-20px text-danger"></i>
                  </div>
                </td>
              </tr>
              <tr>
                <td class="ps-0 pe-5"><span class="text-heading">Canada</span></td>
                <td class="ps-5 d-flex justify-content-end"><span class="text-heading fw-semibold">10,357</span></td>
                <td class="pe-0">
                  <div class="d-flex align-items-center justify-content-end">
                    <span class="text-heading fw-semibold me-2">85%</span>
                    <i class="mdi mdi-chevron-up mdi-20px text-success"></i>
                  </div>
                </td>
              </tr>
              <tr>
                <td class="ps-0 pe-5"><span class="text-heading">India</span></td>
                <td class="ps-5 d-flex justify-content-end"><span class="text-heading fw-semibold">4,860</span></td>
                <td class="pe-0">
                  <div class="d-flex align-items-center justify-content-end">
                    <span class="text-heading fw-semibold me-2">48%</span>
                    <i class="mdi mdi-chevron-up mdi-20px text-success"></i>
                  </div>
                </td>
              </tr>
              <tr>
                <td class="ps-0 pe-5"><span class="text-heading">France</span></td>
                <td class="ps-5 d-flex justify-content-end"><span class="text-heading fw-semibold">2,560</span></td>
                <td class="pe-0">
                  <div class="d-flex align-items-center justify-content-end">
                    <span class="text-heading fw-semibold me-2">36%</span>
                    <i class="mdi mdi-chevron-up mdi-20px text-success"></i>
                  </div>
                </td>
              </tr>
              <tr>
                <td class="ps-0 pe-5"><span class="text-heading">United State</span></td>
                <td class="ps-5 d-flex justify-content-end"><span class="text-heading fw-semibold">899</span></td>
                <td class="pe-0">
                  <div class="d-flex align-items-center justify-content-end">
                    <span class="text-heading fw-semibold me-2">16%</span>
                    <i class="mdi mdi-chevron-down mdi-20px text-danger"></i>
                  </div>
                </td>
              </tr>
              <tr>
                <td class="ps-0 pe-5"><span class="text-heading">Japan</span></td>
                <td class="ps-5 d-flex justify-content-end"><span class="text-heading fw-semibold">43</span></td>
                <td class="pe-0">
                  <div class="d-flex align-items-center justify-content-end">
                    <span class="text-heading fw-semibold me-2">35%</span>
                    <i class="mdi mdi-chevron-up mdi-20px text-success"></i>
                  </div>
                </td>
              </tr>
              <tr>
                <td class="ps-0 pe-5"><span class="text-heading">Brazil</span></td>
                <td class="ps-5 d-flex justify-content-end"><span class="text-heading fw-semibold">18</span></td>
                <td class="pe-0">
                  <div class="d-flex align-items-center justify-content-end">
                    <span class="text-heading fw-semibold me-2">12%</span>
                    <i class="mdi mdi-chevron-up mdi-20px text-success"></i>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
  <!--/ Most Sales in Countries -->

  <!-- Payment History -->
  <div class="col-lg-6 col-xl-4 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Payment History</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="paymentHistory" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="mdi mdi-dots-vertical mdi-24px"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="paymentHistory">
            <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
          </div>
        </div>
      </div>
      <div class="table-responsive text-nowrap">
        <table class="table table-borderless">
          <thead class="border-bottom">
            <tr>
              <th class="text-capitalize text-body fw-medium fs-6">Card</th>
              <th class="text-capitalize text-body fw-medium fs-6">Date</th>
              <th class="text-end text-capitalize text-body fw-medium fs-6">Spend</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="d-flex">
                <div class="px-2 rounded bg-lighter d-flex align-items-center h-px-30">
                  <img src="{{ asset('assets/img/icons/payments/logo-visa.png')}}" alt="credit-card" width="30">
                </div>
                <div class="ms-2">
                  <h6 class="mb-0 fw-semibold">*4399</h6>
                  <small class="text-muted">Credit Card</small>
                </div>
              </td>
              <td class="text-muted small">05/Jan</td>

              <td class="text-end">
                <div class="ms-2">
                  <h6 class="mb-0 fw-semibold">-$2,820</h6>
                  <small class="text-muted">$10,450</small>
                </div>
              </td>
            </tr>
            <tr>
              <td class="d-flex">
                <div class="px-2 rounded bg-lighter d-flex align-items-center h-px-30">
                  <img src="{{ asset('assets/img/icons/payments/logo-mastercard.png')}}" alt="debit-card" width="30">
                </div>
                <div class="ms-2">
                  <h6 class="mb-0 fw-semibold">*5545</h6>
                  <small class="text-muted">Debit Card</small>
                </div>
              </td>
              <td class="text-muted small">12/Feb</td>

              <td class="text-end">
                <div class="ms-2">
                  <h6 class="mb-0 fw-semibold">-$345</h6>
                  <small class="text-muted">$8,709</small>
                </div>
              </td>
            </tr>
            <tr>
              <td class="d-flex">
                <div class="px-2 rounded bg-lighter d-flex align-items-center h-px-30">
                  <img src="{{ asset('assets/img/icons/payments/logo-american-express.png')}}" alt="atm-card" width="30">
                </div>
                <div class="ms-2">
                  <h6 class="mb-0 fw-semibold">*9860</h6>
                  <small class="text-muted">ATM Card</small>
                </div>
              </td>
              <td class="text-muted small">24/Feb</td>

              <td class="text-end">
                <div class="ms-2">
                  <h6 class="mb-0 fw-semibold">-$999</h6>
                  <small class="text-muted">$25,900</small>
                </div>
              </td>
            </tr>
            <tr>
              <td class="d-flex">
                <div class="px-2 rounded bg-lighter d-flex align-items-center h-px-30">
                  <img src="{{ asset('assets/img/icons/payments/logo-visa.png')}}" alt="debit-card" width="30">
                </div>
                <div class="ms-2">
                  <h6 class="mb-0 fw-semibold">*4300</h6>
                  <small class="text-muted">Credit Card</small>
                </div>
              </td>
              <td class="text-muted small">08/Mar</td>

              <td class="text-end">
                <div class="ms-2">
                  <h6 class="mb-0 fw-semibold">-$8,453</h6>
                  <small class="text-muted">$9,233</small>
                </div>
              </td>
            </tr>
            <tr>
              <td class="d-flex">
                <div class="px-2 rounded bg-lighter d-flex align-items-center h-px-30">
                  <img src="{{ asset('assets/img/icons/payments/logo-mastercard.png')}}" alt="credit-card" width="30">
                </div>
                <div class="ms-2">
                  <h6 class="mb-0 fw-semibold">*5545</h6>
                  <small class="text-muted">Debit Card</small>
                </div>
              </td>
              <td class="text-muted small">15/Apr</td>

              <td class="text-end">
                <div class="ms-2">
                  <h6 class="mb-0 fw-semibold">-$24</h6>
                  <small class="text-muted">$500</small>
                </div>
              </td>
            </tr>
            <tr>
              <td class="d-flex">
                <div class="px-2 rounded bg-lighter d-flex align-items-center h-px-30">
                  <img src="{{ asset('assets/img/icons/payments/logo-visa.png')}}" alt="credit-card" width="30">
                </div>
                <div class="ms-2">
                  <h6 class="mb-0 fw-semibold">*4399</h6>
                  <small class="text-muted">Credit Card</small>
                </div>
              </td>
              <td class="text-muted small">28/Apr</td>

              <td class="text-end">
                <div class="ms-2">
                  <h6 class="mb-0 fw-semibold">-$299</h6>
                  <small class="text-muted">$1,380</small>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!--/ Payment History -->

  <!-- Subscribers by Countries -->
  <div class="col-lg-6 col-xl-4 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Subscribers by Countries</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="subscribers" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="mdi mdi-dots-vertical mdi-24px"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="subscribers">
            <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
            <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
          </div>
        </div>
      </div>
      <div class="table-responsive text-nowrap">
        <table class="table table-borderless">
          <thead class="border-bottom">
            <tr>
              <th class="text-capitalize text-body fw-medium fs-6">Countries</th>
              <th class="text-capitalize text-body fw-medium fs-6">Subscriber</th>
              <th class="text-end text-capitalize text-body fw-medium fs-6">Percent</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="d-flex align-items-center">
                <div class="avatar avatar-sm flex-shrink-0 me-3">
                  <img src="{{ asset('assets/svg/flags/us.svg')}}" alt="us-flag" width="30">
                </div>
                <h6 class="mb-0 fw-semibold">USA</h6>
              </td>
              <td class="text-muted fw-medium">22,450</td>

              <td class="d-flex justify-content-end align-items-center">
                <h6 class="mb-0 fw-semibold text-success">+22.5%</h6>
                <i class="mdi mdi-chevron-up text-success ms-2"></i>
              </td>
            </tr>
            <tr>
              <td class="d-flex align-items-center">
                <div class="avatar avatar-sm flex-shrink-0 me-3">
                  <img src="{{ asset('assets/svg/flags/in.svg')}}" alt="indian-flag" width="30">
                </div>
                <h6 class="mb-0 fw-semibold">India</h6>
              </td>
              <td class="text-muted fw-medium">18,568</td>

              <td class="d-flex justify-content-end align-items-center">
                <h6 class="mb-0 fw-semibold text-success">+18.5%</h6>
                <i class="mdi mdi-chevron-up text-success ms-2"></i>
              </td>
            </tr>
            <tr>
              <td class="d-flex align-items-center">
                <div class="avatar avatar-sm flex-shrink-0 me-3">
                  <img src="{{ asset('assets/svg/flags/br.svg')}}" alt="Brazil-flag" width="30">
                </div>
                <h6 class="mb-0 fw-semibold">Brazil</h6>
              </td>
              <td class="text-muted fw-medium">8,457</td>

              <td class="d-flex justify-content-end align-items-center">
                <h6 class="mb-0 fw-semibold text-danger">-8.3%</h6>
                <i class="mdi mdi-chevron-down text-danger ms-2"></i>
              </td>
            </tr>
            <tr>
              <td class="d-flex align-items-center">
                <div class="avatar avatar-sm flex-shrink-0 me-3">
                  <img src="{{ asset('assets/svg/flags/au.svg')}}" alt="Australia-flag" width="30">
                </div>
                <h6 class="mb-0 fw-semibold">Australia</h6>
              </td>
              <td class="text-muted fw-medium">2,850</td>

              <td class="d-flex justify-content-end align-items-center">
                <h6 class="mb-0 fw-semibold text-success">+15.2%</h6>
                <i class="mdi mdi-chevron-up text-success ms-2"></i>
              </td>
            </tr>
            <tr>
              <td class="d-flex align-items-center">
                <div class="avatar avatar-sm flex-shrink-0 me-3">
                  <img src="{{ asset('assets/svg/flags/fr.svg')}}" alt="France-flag" width="30">
                </div>
                <h6 class="mb-0 fw-semibold">France</h6>
              </td>
              <td class="text-muted fw-medium">1,930</td>

              <td class="d-flex justify-content-end align-items-center">
                <h6 class="mb-0 fw-semibold text-danger">-12.6%</h6>
                <i class="mdi mdi-chevron-down text-danger ms-2"></i>
              </td>
            </tr>
            <tr>
              <td class="d-flex align-items-center">
                <div class="avatar avatar-sm flex-shrink-0 me-3">
                  <img src="{{ asset('assets/svg/flags/cn.svg')}}" alt="china-flag" width="30">
                </div>
                <h6 class="mb-0 fw-semibold">China</h6>
              </td>
              <td class="text-muted fw-medium">852</td>
              <td class="d-flex justify-content-end align-items-center">
                <h6 class="mb-0 fw-semibold text-danger">-2.4%</h6>
                <i class="mdi mdi-chevron-down text-danger ms-2"></i>
              </td>
            </tr>
            <tr>
              <td class="d-flex align-items-center">
                <div class="avatar avatar-sm flex-shrink-0 me-3">
                  <img src="{{ asset('assets/svg/flags/pt.svg')}}" alt="china-flag" width="30">
                </div>
                <h6 class="mb-0 fw-semibold">Portugal</h6>
              </td>
              <td class="text-muted fw-medium">633</td>
              <td class="d-flex justify-content-end align-items-center">
                <h6 class="mb-0 fw-semibold text-success">+12.4%</h6>
                <i class="mdi mdi-chevron-up text-success ms-2"></i>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!--/ Subscribers by Countries -->

  <!-- Timeline -->
  <div class="col-12 col-lg-6 col-xl-8 mb-4">
    <div class="card h-100">
      <img src="{{ asset('assets/img/elements/activity-timeline.png')}}" alt="timeline-image" class="card-img-top h-px-200" style="object-fit: cover;">
      <div class="card-body">
        <h4 class="mb-4">Activity Timeline</h4>
        <ul class="timeline card-timeline mb-0">
          <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point timeline-point-danger"></span>
            <div class="timeline-event">
              <div class="timeline-header mb-1">
                <h6 class="mb-0 fw-semibold">8 Invoices have been paid</h6>
                <small class="text-muted">Wednesday</small>
              </div>
              <p class="text-muted mb-2">Invoices have been paid to the company</p>
              <div class="d-flex">
                <a href="javascript:void(0)" class="me-3">
                  <img src="{{asset('assets/img/icons/misc/pdf.png')}}" alt="PDF image" width="15" class="me-2">
                  <span class="fw-semibold text-muted">invoices.pdf</span>
                </a>
              </div>
            </div>
          </li>
          <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point timeline-point-primary"></span>
            <div class="timeline-event">
              <div class="timeline-header mb-1">
                <h6 class="mb-0 fw-semibold">Create a new project for client 😎
                </h6>
                <small class="text-muted">April, 18</small>
              </div>
              <p class="text-muted mb-2">Invoices have been paid to the company.</p>
              <div class="d-flex flex-wrap align-items-center">
                <div class="avatar avatar-sm me-3">
                  <img src="{{ asset('assets/img/avatars/1.png')}}" alt="Avatar" class="rounded-circle" />
                </div>
                <h6 class="mb-0 fw-semibold text-muted">John Doe (Client)</h6>
              </div>
            </div>
          </li>
          <li class="timeline-item timeline-item-transparent border-0">
            <span class="timeline-point timeline-point-info"></span>
            <div class="timeline-event pb-1">
              <div class="timeline-header mb-1">
                <h6 class="mb-0 fw-semibold">Order #37745 from September</h6>
                <small class="text-muted">January, 10</small>
              </div>
              <p class="text-muted mb-0">Invoices have been paid to the company.</p>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <!--/ Timeline -->

  <!-- Finance App -->
  <div class="col-lg-6 col-xl-4 mb-4">
    <div class="card h-100">
      <img class="card-img-top" src="{{ asset('assets/img/elements/iPhone-bg.png')}}" alt="iPhone-bg">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="d-flex">
            <div class="badge bg-label-success rounded-pill me-3">UI Design</div>
            <div class="badge bg-label-danger rounded-pill">React</div>
          </div>
          <div class="dropdown">
            <button class="btn p-0" type="button" id="financeApp" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="mdi mdi-dots-vertical mdi-24px"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="financeApp">
              <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
            </div>
          </div>
        </div>
        <h4 class="mb-2">Finance ios App</h4>
        <p class="text-muted fw-semibold">Due Date: 20/Dec/2022</p>
        <p class="text-muted my-3 ">Managing your money isn’t the easiest thing to do. Now that many of us no longer balance a checkbook, tracking and expenses.</p>
        <div class="mb-3 mt-1">
          <div class="d-flex justify-content-between gap-3">
            <h6 class="mb-2 fw-semibold">Progress</h6>
            <span class="text-heading fw-semibold">68%</span>
          </div>
          <div class="progress w-100 rounded bg-label-success" style="height: 8px;">
            <div class="progress-bar bg-success rounded" style="width: 68%" role="progressbar" aria-valuenow="68" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <ul class="list-unstyled m-0 d-flex align-items-center avatar-group">
            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Vinnie Mostowy" class="avatar pull-up">
              <img class="rounded-circle" src="{{ asset('assets/img/avatars/5.png')}}" alt="Avatar">
            </li>
            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Allen Rieske" class="avatar pull-up">
              <img class="rounded-circle" src="{{ asset('assets/img/avatars/12.png')}}" alt="Avatar">
            </li>
            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Julee Rossignol" class="avatar pull-up">
              <img class="rounded-circle" src="{{ asset('assets/img/avatars/6.png')}}" alt="Avatar">
            </li>
            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Darcey Nooner" class="avatar pull-up">
              <img class="rounded-circle" src="{{ asset('assets/img/avatars/10.png')}}" alt="Avatar">
            </li>
          </ul>
          <div class="d-flex">
            <div class="me-2 text-muted">
              <i class="mdi mdi-paperclip mdi-20px"></i>
              <span class="fw-semibold">24</span>
            </div>
            <div class="me-2 text-muted">
              <i class="mdi mdi-check-circle-outline mdi-20px"></i>
              <span class="fw-semibold">74/180</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Finance App -->
</div>

@include('_partials/_modals/modal-add-new-cc')
@include('_partials/_modals/modal-upgrade-plan')
<!-- /Modal -->
<script>
// Check selected custom option
window.Helpers.initCustomOptionCheck();
</script>

@endsection
