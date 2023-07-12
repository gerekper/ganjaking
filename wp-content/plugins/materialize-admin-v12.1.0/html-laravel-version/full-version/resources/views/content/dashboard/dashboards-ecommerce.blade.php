@extends('layouts/layoutMaster')

@section('title', 'E-commerce')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/swiper/swiper.css')}}" />
@endsection

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/cards-statistics.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
<script src="{{asset('assets/vendor/libs/swiper/swiper.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/dashboards-ecommerce.js')}}"></script>
@endsection

@section('content')
<div class="row gy-4 mb-4">
  <!-- Sales Overview-->
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header">
        <div class="d-flex justify-content-between">
          <h4 class="mb-2">Sales Overview</h4>
          <div class="dropdown">
            <button class="btn p-0" type="button" id="salesOverview" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="mdi mdi-dots-vertical mdi-24px"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="salesOverview">
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
              <a class="dropdown-item" href="javascript:void(0);">Share</a>
              <a class="dropdown-item" href="javascript:void(0);">Update</a>
            </div>
          </div>
        </div>
        <div class="d-flex align-items-center">
          <small class="me-2">Total 42.5k Sales</small>
          <div class="d-flex align-items-center text-success">
            <p class="mb-0">+18%</p>
            <i class="mdi mdi-chevron-up"></i>
          </div>
        </div>
      </div>
      <div class="card-body d-flex justify-content-between flex-wrap gap-3">
        <div class="d-flex gap-3">
          <div class="avatar">
            <div class="avatar-initial bg-label-primary rounded">
              <i class="mdi mdi-account-outline mdi-24px"></i>
            </div>
          </div>
          <div class="card-info">
            <h4 class="mb-0">8,458</h4>
            <small class="text-muted">Customers</small>
          </div>
        </div>
        <div class="d-flex gap-3">
          <div class="avatar">
            <div class="avatar-initial bg-label-warning rounded">
              <i class="mdi mdi-poll mdi-24px"></i>
            </div>
          </div>
          <div class="card-info">
            <h4 class="mb-0">$28.5k</h4>
            <small class="text-muted">Profit</small>
          </div>
        </div>
        <div class="d-flex gap-3">
          <div class="avatar">
            <div class="avatar-initial bg-label-info rounded">
              <i class="mdi mdi-trending-up mdi-24px"></i>
            </div>
          </div>
          <div class="card-info">
            <h4 class="mb-0">2,450k</h4>
            <small class="text-muted">Transactions</small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Sales Overview-->

  <!-- Ratings -->
  <div class="col-lg-3 col-sm-6">
    <div class="card h-100">
      <div class="row">
        <div class="col-6">
          <div class="card-body">
            <div class="card-info mb-3 py-2 mb-lg-1 mb-xl-3">
              <h5 class="mb-3 mb-lg-2 mb-xl-3 text-nowrap">Ratings</h5>
              <div class="badge bg-label-primary rounded-pill lh-xs">Year of 2021</div>
            </div>
            <div class="d-flex align-items-end flex-wrap gap-1">
              <h4 class="mb-0 me-2">8.14k</h4>
              <small class="text-success">+15.6%</small>
            </div>
          </div>
        </div>
        <div class="col-6 text-end d-flex align-items-end justify-content-center">
          <div class="card-body pb-0 pt-3 position-absolute bottom-0">
            <img src="{{asset('assets/img/illustrations/card-ratings-illustration.png')}}" alt="Ratings" width="95">
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Ratings -->

  <!-- Sessions -->
  <div class="col-lg-3 col-sm-6">
    <div class="card h-100">
      <div class="row">
        <div class="col-6">
          <div class="card-body">
            <div class="card-info mb-3 py-2 mb-lg-1 mb-xl-3">
              <h5 class="mb-3 mb-lg-2 mb-xl-3 text-nowrap">Sessions</h5>
              <div class="badge bg-label-success rounded-pill lh-xs">Last Month</div>
            </div>
            <div class="d-flex align-items-end flex-wrap gap-1">
              <h4 class="mb-0 me-2">12.2k</h4>
              <small class="text-danger ">-25.5%</small>
            </div>
          </div>
        </div>
        <div class="col-6 text-end d-flex align-items-end justify-content-center">
          <div class="card-body pb-0 pt-3 position-absolute bottom-0">
            <img src="{{asset('assets/img/illustrations/card-session-illustration.png')}}" alt="Ratings" width="81">
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Sessions -->

  <!-- Weekly Sales with bg-->
  <div class="col-lg-6">
    <div class="swiper-container swiper-container-horizontal swiper text-bg-primary" id="swiper-weekly-sales-with-bg">
      <div class="swiper-wrapper">
        <div class="swiper-slide">
          <div class="row">
            <div class="col-12">
              <h5 class="text-white mb-2">Weekly Sales</h5>
              <div class="d-flex align-items-center gap-2">
                <small>Total $23.5k Earning</small>
                <div class="d-flex text-success">
                  <small class="fw-medium">+62%</small>
                  <i class="mdi mdi-chevron-up"></i>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-7 col-md-9 col-12 order-2 order-md-1">
                <h6 class="text-white mt-0 mt-md-3 mb-3 py-1">Mobiles & Computers</h6>
                <div class="row">
                  <div class="col-6">
                    <ul class="list-unstyled mb-0">
                      <li class="d-flex mb-3 align-items-center">
                        <p class="mb-0 me-2 weekly-sales-text-bg-primary">24</p>
                        <p class="mb-0">Mobiles</p>
                      </li>
                      <li class="d-flex align-items-center">
                        <p class="mb-0 me-2 weekly-sales-text-bg-primary">12</p>
                        <p class="mb-0">Tablets</p>
                      </li>
                    </ul>
                  </div>
                  <div class="col-6">
                    <ul class="list-unstyled mb-0">
                      <li class="d-flex mb-3 align-items-center">
                        <p class="mb-0 me-2 weekly-sales-text-bg-primary">50</p>
                        <p class="mb-0">Accessories</p>
                      </li>
                      <li class="d-flex align-items-center">
                        <p class="mb-0 me-2 weekly-sales-text-bg-primary">38</p>
                        <p class="mb-0">Computers</p>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="col-lg-5 col-md-3 col-12 order-1 order-md-2 my-2 my-md-0 text-center">
                <img src="{{asset('assets/img/products/card-weekly-sales-phone.png')}}" alt="weekly sales" width="230" class="weekly-sales-img">
              </div>
            </div>
          </div>
        </div>
        <div class="swiper-slide">
          <div class="row">
            <div class="col-12">
              <h5 class="text-white mb-2">Weekly Sales</h5>
              <div class="d-flex align-items-center gap-2">
                <small>Total $23.5k Earning</small>
                <div class="d-flex text-success">
                  <small class="fw-medium">+62%</small>
                  <i class="mdi mdi-chevron-up"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-7 col-md-9 col-12 order-2 order-md-1">
              <h6 class="text-white mt-0 mt-md-3 mb-3 py-1">Appliances & Electronics</h6>
              <div class="row">
                <div class="col-6">
                  <ul class="list-unstyled mb-0">
                    <li class="d-flex mb-3 align-items-center">
                      <p class="mb-0 me-2 weekly-sales-text-bg-primary">16</p>
                      <p class="mb-0">TV's</p>
                    </li>
                    <li class="d-flex align-items-center">
                      <p class="mb-0 me-2 weekly-sales-text-bg-primary">40</p>
                      <p class="mb-0">Speakers</p>
                    </li>
                  </ul>
                </div>
                <div class="col-6">
                  <ul class="list-unstyled mb-0">
                    <li class="d-flex mb-3 align-items-center">
                      <p class="mb-0 me-2 weekly-sales-text-bg-primary">9</p>
                      <p class="mb-0">Cameras</p>
                    </li>
                    <li class="d-flex align-items-center">
                      <p class="mb-0 me-2 weekly-sales-text-bg-primary">18</p>
                      <p class="mb-0">Consoles</p>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-lg-5 col-md-3 col-12 order-1 order-md-2 my-2 my-md-0 text-center">
              <img src="{{asset('assets/img/products/card-weekly-sales-controller.png')}}" alt="weekly sales" width="230" class="weekly-sales-img">
            </div>
          </div>
        </div>
        <div class="swiper-slide">
          <div class="row">
            <div class="col-12">
              <h5 class="text-white mb-2">Weekly Sales</h5>
              <div class="d-flex align-items-center gap-2">
                <small>Total $23.5k Earning</small>
                <div class="d-flex text-success">
                  <small class="fw-medium">+62%</small>
                  <i class="mdi mdi-chevron-up"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-7 col-md-9 col-12 order-2 order-md-1">
              <h6 class="text-white mt-0 mt-md-3 mb-3 py-1">Fashion</h6>
              <div class="row">
                <div class="col-6">
                  <ul class="list-unstyled mb-0">
                    <li class="d-flex mb-3 align-items-center">
                      <p class="mb-0 me-2 weekly-sales-text-bg-primary">16</p>
                      <p class="mb-0">T-shirts</p>
                    </li>
                    <li class="d-flex align-items-center">
                      <p class="mb-0 me-2 weekly-sales-text-bg-primary">29</p>
                      <p class="mb-0">Watches</p>
                    </li>
                  </ul>
                </div>
                <div class="col-6">
                  <ul class="list-unstyled mb-0">
                    <li class="d-flex mb-3 align-items-center">
                      <p class="mb-0 me-2 weekly-sales-text-bg-primary">43</p>
                      <p class="mb-0">Shoes</p>
                    </li>
                    <li class="d-flex align-items-center">
                      <p class="mb-0 me-2 weekly-sales-text-bg-primary">7</p>
                      <p class="mb-0">Sun Glasses</p>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-lg-5 col-md-3 col-12 order-1 order-md-2 my-2 my-md-0 text-center">
              <img src="{{asset('assets/img/products/card-weekly-sales-watch.png')}}" alt="weekly sales" width="230" class="weekly-sales-img">
            </div>
          </div>
        </div>
      </div>
      <div class="swiper-pagination"></div>
    </div>
  </div>
  <!--/ Weekly Sales with bg-->

  <!-- Total Visits -->
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-header pb-0">
        <div class="d-flex justify-content-between flex-wrap gap-2">
          <p class="d-block mb-2 text-muted">Total Visits</p>
          <div class="d-flex text-success">
            <p class="me-1">+18.4%</p>
            <i class="mdi mdi-chevron-up"></i>
          </div>
        </div>
        <h4 class="mb-1">$42.5k</h4>
      </div>
      <div class="card-body">
        <div class="row mt-3">
          <div class="col-4">
            <div class="d-flex gap-2 align-items-center mb-2">
              <div class="avatar avatar-xs flex-shrink-0">
                <div class="avatar-initial rounded bg-label-warning">
                  <i class="mdi mdi-cellphone mdi-14px"></i>
                </div>
              </div>
              <p class="mb-0 text-muted">Mobile</p>
            </div>
            <h4 class="mb-0 pt-1 text-nowrap">23.5%</h4>
            <small class="text-muted">2,890</small>
          </div>
          <div class="col-4">
            <div class="divider divider-vertical">
              <div class="divider-text">
                <span class="badge-divider-bg bg-label-secondary">VS</span>
              </div>
            </div>
          </div>
          <div class="col-4 text-end pe-lg-0 pe-xl-2">
            <div class="d-flex gap-2 justify-content-end align-items-center mb-2">
              <p class="mb-0 text-muted">Desktop</p>
              <div class="avatar avatar-xs flex-shrink-0">
                <div class="avatar-initial rounded bg-label-primary">
                  <i class="mdi mdi-monitor mdi-14px"></i>
                </div>
              </div>
            </div>
            <h4 class="mb-0 pt-1 text-nowrap">76.5%</h4>
            <small class="text-muted">22,465</small>
          </div>
        </div>
        <div class="d-flex align-items-center mt-2 pt-1">
          <div class="progress w-100 rounded" style="height: 10px;">
            <div class="progress-bar bg-warning" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
            <div class="progress-bar bg-primary" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Total Visits -->

  <!-- Sales This Months -->
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Sales This Month</h5>
      </div>
      <div class="card-body">
        <div class="card-info">
          <p class="text-muted mb-2">Total Sales This Month</p>
          <h5 class="mb-0">$28,450</h5>
        </div>
        <div id="saleThisMonth"></div>
      </div>
    </div>
  </div>
  <!--/ Sales This Months -->

</div>
<div class="row gy-4 mb-4">
  <!-- Activity Timeline -->
  <div class="col-12 col-lg-6">
    <div class="card">
      <div class="card-header">
        <div class="d-flex justify-content-between">
          <h5 class="mb-1">Activity Timeline</h5>
          <div class="dropdown">
            <button class="btn p-0" type="button" id="timelineDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="mdi mdi-dots-vertical mdi-24px"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="timelineDropdown">
              <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body pt-4 pb-2 mt-2">
        <ul class="timeline card-timeline mb-0">
          <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point timeline-point-danger"></span>
            <div class="timeline-event">
              <div class="timeline-header mb-1">
                <h6 class="mb-2 fw-semibold">8 Invoices have been paid</h6>
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
                <h6 class="mb-2 fw-semibold">Create a new project for client 😎
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
                <h6 class="mb-2 fw-semibold">Order #37745 from September</h6>
                <small class="text-muted">January, 10</small>
              </div>
              <p class="text-muted mb-0">Invoices have been paid to the company.</p>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <!--/ Activity Timeline -->

  <!-- Top Referral Source  -->
  <div class="col-12 col-lg-6">
    <div class="card h-100">
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
                    <th class="ps-0 fw-medium text-heading">Image</th>
                    <th class="fw-medium ps-0 text-heading">Product Name</th>
                    <th class="pe-0 fw-medium text-end text-heading">Status</th>
                    <th class="pe-0 fw-medium text-end text-heading">Revenue</th>
                    <th class="pe-0 text-end fw-medium text-heading">Conversion</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets//img/products/samsung-s22.png')}}" alt="samsung" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">Oneplus 9 Pro</td>
                    <td class="text-heading text-end pe-0"><span class="badge rounded-pill bg-label-primary">Out of Stock</span></td>
                    <td class="text-heading text-end pe-0 fw-semibold">$12.5k</td>
                    <td class="pe-0 text-end fw-semibold h6 text-success">+24%</td>
                  </tr>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets/img/products/apple-iPhone-13-pro.png')}}" alt="iphone" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">Apple iPhone 13 Pro</td>
                    <td class="text-heading text-end pe-0"><span class="badge rounded-pill bg-label-success">In Stock</span></td>
                    <td class="text-heading text-end pe-0 fw-semibold">$45k</td>
                    <td class="pe-0 text-end fw-semibold h6 text-danger">-18%</td>
                  </tr>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets//img/products/oneplus-9-pro.png')}}" alt="us-flag" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">Oneplus 9 Pro</td>
                    <td class="text-heading text-end pe-0"><span class="badge rounded-pill bg-label-warning">Coming Soon</span></td>
                    <td class="text-heading text-end pe-0 fw-semibold text-heading">$98.2k</td>
                    <td class="pe-0 text-end fw-semibold h6 text-success">+55%</td>
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
                    <th class="ps-0 fw-medium text-heading">Image</th>
                    <th class="fw-medium ps-0 text-heading">Product Name</th>
                    <th class="pe-0 fw-medium text-end text-heading">Status</th>
                    <th class="pe-0 fw-medium text-end text-heading">Revenue</th>
                    <th class="pe-0 text-end fw-medium text-heading">Conversion</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets//img/products/apple-mac-mini.png')}}" alt="mac-mini" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">Apple Mac Mini</td>
                    <td class="text-heading text-end pe-0"><span class="badge rounded-pill bg-label-success">In Stock</span></td>
                    <td class="text-heading text-end pe-0 fw-semibold">$94.6k</td>
                    <td class="pe-0 text-end fw-semibold h6 text-success">+16%</td>
                  </tr>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets//img/products/hp-envy-x360.png')}}" alt="hp-envy" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">Newest HP Envy x360</td>
                    <td class="text-heading text-end pe-0"><span class="badge rounded-pill bg-label-warning">Coming Soon</span></td>
                    <td class="text-heading text-end pe-0 fw-semibold">$76.5k</td>
                    <td class="pe-0 text-end fw-semibold h6 text-success">+27%</td>
                  </tr>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets//img/products/dell-inspiron-3000.png')}}" alt="dell" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">Dell Inspiron 3000</td>
                    <td class="text-heading text-end pe-0"><span class="badge rounded-pill bg-label-primary">Out of Stock</span></td>
                    <td class="text-heading text-end pe-0 fw-semibold">$69.3k</td>
                    <td class="pe-0 text-end fw-semibold h6 text-danger">-9%</td>
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
                    <th class="ps-0 fw-medium text-heading">Image</th>
                    <th class="fw-medium ps-0 text-heading">Product Name</th>
                    <th class="pe-0 fw-medium text-end text-heading">Status</th>
                    <th class="pe-0 fw-medium text-end text-heading">Revenue</th>
                    <th class="pe-0 text-end fw-medium text-heading">Conversion</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets//img/products/sony-play-station-5.png')}}" alt="sony-play-station" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">Sony Play Station 5</td>
                    <td class="text-heading text-end pe-0"><span class="badge rounded-pill bg-label-warning">Coming Soon</span></td>
                    <td class="text-heading text-end pe-0 fw-semibold">$18.6k</td>
                    <td class="pe-0 text-end fw-semibold h6 text-success">+34%</td>
                  </tr>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets//img/products/xbox-series-x.png')}}" alt="xbox" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">XBOX Series X</td>
                    <td class="text-heading text-end pe-0"><span class="badge rounded-pill bg-label-primary">Out of Stock</span></td>
                    <td class="text-heading text-end pe-0 fw-semibold">$29.7k</td>
                    <td class="pe-0 text-end fw-semibold h6 text-danger">-21%</td>
                  </tr>
                  <tr>
                    <td class="ps-0">
                      <img src="{{ asset('assets//img/products/nintendo-switch.png')}}" alt="nintendo-switch" class="rounded" height="34">
                    </td>
                    <td class="text-heading fw-semibold ps-0">Nintendo Switch</td>
                    <td class="text-heading text-end pe-0"><span class="badge rounded-pill bg-label-success">In Stock</span></td>
                    <td class="text-heading text-end pe-0 fw-semibold">$10.4k</td>
                    <td class="pe-0 text-end fw-semibold h6 text-success">+38%</td>
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

  <!-- Total Impression & Order Chart -->
  <div class="col-lg-3 col-sm-6 order-sm-1 order-lg-0">
    <div class="card">
      <div class="card-body pb-0 pt-3">
        <div class="row d-flex align-items-center">
          <div class="col-5 col-lg-6 col-xl-5">
            <div class="chart-progress" data-color="primary" data-series="70" data-icon="../../assets/img/icons/misc/card-icon-laptop.png"></div>
          </div>
          <div class="col-7 col-lg-6 col-xl-7">
            <div class="card-info">
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <h5 class="mb-0">84k</h5>
                <div class="d-flex  text-danger">
                  <p class="mb-0">-24%</p>
                  <div class="mdi mdi-chevron-down"></div>
                </div>
              </div>
              <p class="mb-0 mt-1">Total Impression</p>
            </div>
          </div>
        </div>
      </div>
      <hr class="my-2">
      <div class="card-body pt-0 pb-3">
        <div class="row d-flex align-items-center">
          <div class="col-5 col-lg-6 col-xl-5">
            <div class="chart-progress" data-color="warning" data-series="40" data-icon="../../assets/img/icons/misc/card-icon-bag.png"></div>
          </div>
          <div class="col-7 col-lg-6 col-xl-7">
            <div class="card-info">
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <h5 class="mb-0">22k</h5>
                <div class="d-flex  text-success">
                  <p class="mb-0">+15%</p>
                  <div class="mdi mdi-chevron-up"></div>
                </div>
              </div>
              <p class="mb-0 mt-1">Total Order</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Total Impression & Order Chart -->

  <!-- Marketing & Sales-->
  <div class="col-lg-5">
    <div class="swiper-container swiper-container-horizontal swiper swiper-sales" id="swiper-marketing-sales">
      <div class="swiper-wrapper">
        <div class="swiper-slide pb-3">
          <h5 class="mb-2">Marketing & Sales</h5>
          <div class="d-flex align-items-center gap-2">
            <small>Total 245.8k Sales</small>
            <div class="d-flex text-success">
              <small class="fw-medium">+25%</small>
              <i class="mdi mdi-chevron-up"></i>
            </div>
          </div>
          <div class="d-flex align-items-center mt-3">
            <img src="{{ asset('assets//img/products/card-marketing-expense-logo.png')}}" alt="Marketing and sales" width="84" class="rounded">
            <div class="d-flex flex-column w-100 ms-4">
              <h6 class="mb-3">Marketing Expense</h6>
              <div class="row d-flex flex-wrap justify-content-between">
                <div class="col-6">
                  <ul class="list-unstyled mb-0">
                    <li class="d-flex mb-2 pb-1 align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">5k</small>
                      <small class="mb-0 text-truncate">Operating</small>
                    </li>
                    <li class="d-flex align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">6k</small>
                      <small class="mb-0 text-truncate">COGF</small>
                    </li>
                  </ul>
                </div>
                <div class="col-6">
                  <ul class="list-unstyled mb-0">
                    <li class="d-flex mb-2 pb-1 align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">2k</small>
                      <small class="mb-0 text-truncate">Financial</small>
                    </li>
                    <li class="d-flex align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">1k</small>
                      <small class="mb-0 text-truncate">Expense</small>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-3 pt-1">
            <button type="button" class="btn btn-sm btn-outline-primary me-3">Details</button>
            <button type="button" class="btn btn-sm btn-primary">Report</button>
          </div>
        </div>
        <div class="swiper-slide pb-3">
          <h5 class="mb-2">Marketing & Sales</h5>
          <div class="d-flex align-items-center gap-2">
            <small>Total 245.8k Sales</small>
            <div class="d-flex text-success">
              <small class="fw-medium">+25%</small>
              <i class="mdi mdi-chevron-up"></i>
            </div>
          </div>
          <div class="d-flex align-items-center mt-3">
            <img src="{{ asset('assets//img/products/card-accounting-logo.png')}}" alt="Marketing and sales" width="84" class="rounded">
            <div class="d-flex flex-column w-100 ms-4">
              <h6 class="mb-3">Accounting</h6>
              <div class="row d-flex flex-wrap justify-content-between">
                <div class="col-6">
                  <ul class="list-unstyled mb-0">
                    <li class="d-flex mb-2 pb-1 align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">18</small>
                      <small class="mb-0 text-truncate">Billing</small>
                    </li>
                    <li class="d-flex align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">30</small>
                      <small class="mb-0 text-truncate">Leads</small>
                    </li>
                  </ul>
                </div>
                <div class="col-6">
                  <ul class="list-unstyled mb-0">
                    <li class="d-flex mb-2 pb-1 align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">28</small>
                      <small class="mb-0 text-truncate">Sales</small>
                    </li>
                    <li class="d-flex align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">80</small>
                      <small class="mb-0 text-truncate">Impression</small>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-3 pt-1">
            <button type="button" class="btn btn-sm btn-outline-primary me-3">Details</button>
            <button type="button" class="btn btn-sm btn-primary">Report</button>
          </div>
        </div>
        <div class="swiper-slide pb-3">
          <h5 class="mb-2">Marketing & Sales</h5>
          <div class="d-flex align-items-center gap-2">
            <small>Total 245.8k Sales</small>
            <div class="d-flex text-success">
              <small class="fw-medium">+25%</small>
              <i class="mdi mdi-chevron-up"></i>
            </div>
          </div>
          <div class="d-flex align-items-center mt-3">
            <img src="{{ asset('assets//img/products/card-sales-overview-logo.png')}}" alt="Marketing and sales" width="84" class="rounded">
            <div class="d-flex flex-column w-100 ms-4">
              <h6 class="mb-3">Sales Overview</h6>
              <div class="row d-flex flex-wrap justify-content-between">
                <div class="col-6">
                  <ul class="list-unstyled mb-0">
                    <li class="d-flex mb-2 pb-1 align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">68</small>
                      <small class="mb-0 text-truncate">Open</small>
                    </li>
                    <li class="d-flex align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">04</small>
                      <small class="mb-0 text-truncate">Lost</small>
                    </li>
                  </ul>
                </div>
                <div class="col-6">
                  <ul class="list-unstyled mb-0">
                    <li class="d-flex mb-2 pb-1 align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">52</small>
                      <small class="mb-0 text-truncate">Converted</small>
                    </li>
                    <li class="d-flex align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">12</small>
                      <small class="mb-0 text-truncate">Quotations</small>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-3 pt-1">
            <button type="button" class="btn btn-sm btn-outline-primary me-3">Details</button>
            <button type="button" class="btn btn-sm btn-primary">Report</button>
          </div>
        </div>
      </div>
      <div class="swiper-pagination"></div>
    </div>
  </div>
  <!--/ Marketing & Sales-->

  <!-- Live Visitors-->
  <div class="col-lg-4 col-sm-6 order-sm-2 order-lg-0">
    <div class="card">
      <div class="card-header pb-0">
        <div class="d-flex justify-content-between mb-1">
          <h4 class="mb-0">Live Visitors</h4>
          <div class="d-flex text-success">
            <p class="mb-0 me-2">+78.2%</p>
            <i class="mdi mdi-chevron-up"></i>
          </div>
        </div>
        <small class="text-muted">Total 890 Visitors Are Live</small>
      </div>
      <div class="card-body">
        <div id="liveVisitors"></div>
      </div>
    </div>
  </div>
  <!--/ Live Visitors-->
</div>
<div class="row gy-4">

  <!-- Roles Datatables -->
  <div class="col-lg-8 col-12">
    <div class="card ">
      <div class="table-responsive rounded-3">
        <table class="datatables-ecommerce table table-sm">
          <thead class="table-light">
            <tr>
              <th class="py-3"></th>
              <th class="py-3">User</th>
              <th class="py-3">Email</th>
              <th class="py-3">Role</th>
              <th class="py-3">Status</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
  <!--/ Roles Datatables -->

  <!-- visits By Day Chart-->
  <div class="col-12 col-xl-4 col-lg-4">
    <div class="card">
      <div class="card-header pb-1">
        <div class="d-flex justify-content-between">
          <h5 class="mb-1">Visits by Day</h5>
          <div class="dropdown">
            <button class="btn p-0" type="button" id="visitsByDayDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="mdi mdi-dots-vertical mdi-24px"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="visitsByDayDropdown">
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
              <a class="dropdown-item" href="javascript:void(0);">Update</a>
              <a class="dropdown-item" href="javascript:void(0);">Share</a>
            </div>
          </div>
        </div>
        <p class="mb-0 text-muted">Total 248.5k Visits</p>
      </div>
      <div class="card-body">
        <div id="visitsByDayChart"></div>
        <div class="d-flex justify-content-between mt-3">
          <div>
            <h6 class="mb-1 fw-semibold">Most Visited Day</h6>
            <p class="mb-0 text-muted">Total 62.4k Visits on Thursday</p>
          </div>
          <div class="avatar">
            <div class="avatar-initial bg-label-primary rounded">
              <i class="mdi mdi-chevron-right mdi-24px scaleX-n1-rtl"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ visits By Day Chart-->
</div>
@endsection
