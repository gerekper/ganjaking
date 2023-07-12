@extends('layouts/layoutMaster')

@section('title', 'Cards Statistics- UI elements')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/swiper/swiper.css')}}" />
@endsection

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/cards-statistics.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
<script src="{{asset('assets/vendor/libs/swiper/swiper.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/cards-statistics.js')}}"></script>
@endsection

@section('content')

<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">UI Elements /</span> Cards Statistics
</h4>
<div class="row gy-4">
  <!-- Cards with few info -->
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center flex-wrap gap-2">
          <div class="avatar me-3">
            <div class="avatar-initial bg-label-primary rounded">
              <i class="mdi mdi-account-outline mdi-24px">
              </i>
            </div>
          </div>
          <div class="card-info">
            <div class="d-flex align-items-center">
              <h4 class="mb-0">8,458</h4>
              <i class="mdi mdi-chevron-down text-danger mdi-24px"></i>
              <small class="text-danger">8.10%</small>
            </div>
            <small class="text-muted">New Customers</small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center flex-wrap gap-2">
          <div class="avatar me-3">
            <div class="avatar-initial bg-label-success rounded">
              <i class="mdi mdi-currency-usd mdi-24px">
              </i>
            </div>
          </div>
          <div class="card-info">
            <div class="d-flex align-items-center">
              <h4 class="mb-0">28.6k</h4>
              <i class="mdi mdi-chevron-up text-success mdi-24px"></i>
              <small class="text-success">25.8%</small>
            </div>
            <small class="text-muted">Total Revenue</small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center flex-wrap gap-2">
          <div class="avatar me-3">
            <div class="avatar-initial bg-label-info rounded">
              <i class="mdi mdi-trending-up mdi-24px">
              </i>
            </div>
          </div>
          <div class="card-info">
            <div class="d-flex align-items-center">
              <h4 class="mb-0">13.6k</h4>
              <i class="mdi mdi-chevron-down text-danger mdi-24px"></i>
              <small class="text-danger">12.1%</small>
            </div>
            <small class="text-muted">New Transactions</small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center flex-wrap gap-2">
          <div class="avatar me-3">
            <div class="avatar-initial bg-label-warning rounded">
              <i class="mdi mdi-poll mdi-24px">
              </i>
            </div>
          </div>
          <div class="card-info">
            <div class="d-flex align-items-center">
              <h4 class="mb-0">2,856</h4>
              <i class="mdi mdi-chevron-up text-success mdi-24px"></i>
              <small class="text-success">54.6%</small>
            </div>
            <small class="text-muted">Total Profit</small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Cards with few info -->

  <!-- Ratings -->
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="row">
        <div class="col-6">
          <div class="card-body">
            <div class="card-info mb-3 pb-2">
              <h5 class="mb-3 text-nowrap">Ratings</h5>
              <div class="badge bg-label-primary rounded-pill lh-xs">Year of 2021</div>
            </div>
            <div class="d-flex align-items-end">
              <h4 class="mb-0 me-2">8.14k</h4>
              <small class="text-success">+15.6%</small>
            </div>
          </div>
        </div>
        <div class="col-6 text-end d-flex align-items-end">
          <div class="card-body pb-0 pt-3">
            <img src="{{asset('assets/img/illustrations/card-ratings-illustration.png')}}" alt="Ratings" class="img-fluid" width="95">
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Ratings -->

  <!-- Sessions -->
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="row">
        <div class="col-6">
          <div class="card-body">
            <div class="card-info mb-3 pb-2">
              <h5 class="mb-3 text-nowrap">Sessions</h5>
              <div class="badge bg-label-success rounded-pill lh-xs">Last Month</div>
            </div>
            <div class="d-flex align-items-end">
              <h4 class="mb-0 me-2">12.2k</h4>
              <small class="text-danger ">-25.5%</small>
            </div>
          </div>
        </div>
        <div class="col-6 text-end d-flex align-items-end">
          <div class="card-body pb-0 pt-3">
            <img src="{{asset('assets/img/illustrations/card-session-illustration.png')}}" alt="Ratings" class="img-fluid" width="81">
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Sessions -->

  <!-- Customers -->
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="row">
        <div class="col-6">
          <div class="card-body">
            <div class="card-info mb-3 pb-2">
              <h5 class="mb-3 text-nowrap">Customers</h5>
              <div class="badge bg-label-warning rounded-pill lh-xs">Daily Customers</div>
            </div>
            <div class="d-flex align-items-end d-flex align-items-end">
              <h4 class="mb-0 me-2">42.4k</h4>
              <small class="text-success">+9.2%</small>
            </div>
          </div>
        </div>
        <div class="col-6 text-end d-flex align-items-end">
          <div class="card-body pb-0 pt-3">
            <img src="{{asset('assets/img/illustrations/card-customers-illustration.png')}}" alt="Ratings" class="img-fluid" width="84">
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Customers -->

  <!-- Total Orders -->
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="row">
        <div class="col-6">
          <div class="card-body">
            <div class="card-info mb-3 pb-2">
              <h5 class="mb-3 text-nowrap">Total Orders</h5>
              <div class="badge bg-label-secondary rounded-pill lh-xs">Last Week</div>
            </div>
            <div class="d-flex align-items-end">
              <h4 class="mb-0 me-2">42.5k</h4>
              <small class="text-success">+10.8%</small>
            </div>
          </div>
        </div>
        <div class="col-6 text-end d-flex align-items-end">
          <div class="card-body pb-0 pt-3">
            <img src="{{asset('assets/img/illustrations/card-orders-illustration.png')}}" alt="Ratings" class="img-fluid" width="78">
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Total Orders -->

  <!-- Total statistics -->
  <div class="col-xl-2 col-lg-3 col-sm-4 col-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
          <div class="avatar">
            <div class="avatar-initial bg-label-primary rounded">
              <i class="mdi mdi-cart-plus mdi-24px"></i>
            </div>
          </div>
          <div class="d-flex align-items-center">
            <p class="mb-0 text-success me-1">+22%</p>
            <i class="mdi mdi-chevron-up text-success"></i>
          </div>
        </div>
        <div class="card-info mt-4 pt-1">
          <h5 class="mb-2">155k</h5>
          <p class="text-muted">Total Orders</p>
          <div class="badge bg-label-secondary rounded-pill">Last 4 Month</div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-2 col-lg-3 col-sm-4 col-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
          <div class="avatar">
            <div class="avatar-initial bg-label-warning rounded">
              <i class="mdi mdi-wallet-giftcard mdi-24px"></i>
            </div>
          </div>
          <div class="d-flex align-items-center">
            <p class="mb-0 text-danger me-1">-18%</p>
            <i class="mdi mdi-chevron-up text-danger"></i>
          </div>
        </div>
        <div class="card-info mt-4 pt-1">
          <h5 class="mb-2">$89.34k</h5>
          <p class="text-muted">Total Profit</p>
          <div class="badge bg-label-secondary rounded-pill">Last One Year</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-2 col-lg-3 col-sm-4 col-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
          <div class="avatar">
            <div class="avatar-initial bg-label-info rounded">
              <i class="mdi mdi-link mdi-24px"></i>
            </div>
          </div>
          <div class="d-flex align-items-center">
            <p class="mb-0 text-success me-1">+62%</p>
            <i class="mdi mdi-chevron-up text-success"></i>
          </div>
        </div>
        <div class="card-info mt-4 pt-1">
          <h5 class="mb-2">142.8k</h5>
          <p class="text-muted">Total Impression</p>
          <div class="badge bg-label-secondary rounded-pill">Last One Year</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-2 col-lg-3 col-sm-4 col-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
          <div class="avatar">
            <div class="avatar-initial bg-label-success rounded">
              <i class="mdi mdi-currency-usd mdi-24px"></i>
            </div>
          </div>
          <div class="d-flex align-items-center">
            <p class="mb-0 text-success me-1">+38%</p>
            <i class="mdi mdi-chevron-up text-success"></i>
          </div>
        </div>
        <div class="card-info mt-4 pt-1">
          <h5 class="mb-2">$13.4k</h5>
          <p class="text-muted">Total Sales</p>
          <div class="badge bg-label-secondary rounded-pill">Last Sales </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-2 col-lg-3 col-sm-4 col-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
          <div class="avatar">
            <div class="avatar-initial bg-label-danger rounded">
              <i class="mdi mdi-briefcase-variant-outline mdi-24px"></i>
            </div>
          </div>
          <div class="d-flex align-items-center">
            <p class="mb-0 text-danger me-1">-16%</p>
            <i class="mdi mdi-chevron-up text-danger"></i>
          </div>
        </div>
        <div class="card-info mt-4 pt-1">
          <h5 class="mb-2">$8.16k</h5>
          <p class="text-muted">Total Expenses</p>
          <div class="badge bg-label-secondary rounded-pill">Last One Month</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-2 col-lg-3 col-sm-4 col-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
          <div class="avatar">
            <div class="avatar-initial bg-label-secondary rounded">
              <i class="mdi mdi-trending-up mdi-24px"></i>
            </div>
          </div>
          <div class="d-flex align-items-center">
            <p class="mb-0 text-success me-1">+46%</p>
            <i class="mdi mdi-chevron-up text-success"></i>
          </div>
        </div>
        <div class="card-info mt-4 pt-1">
          <h5 class="mb-2">$2.55k</h5>
          <p class="text-muted">Transactions</p>
          <div class="badge bg-label-secondary rounded-pill">Last One Year</div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Total statistics -->

  <!-- Total Revenue chart -->
  <div class="col-xl-2 col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-header pb-0">
        <div class="d-flex align-items-end mb-1 flex-wrap gap-2">
          <h4 class="mb-0 me-2">$42.5k</h4>
          <p class="mb-0 text-danger">-22%</p>
        </div>
        <span class="d-block mb-2 text-muted">Total Revenue</span>
      </div>
      <div class="card-body">
        <div id="totalRevenue"></div>
      </div>
    </div>
  </div>
  <!--/ Total Revenue chart -->

  <!-- Sessions line chart -->
  <div class="col-xl-2 col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-header pb-0">
        <div class="d-flex align-items-end mb-1 flex-wrap gap-2">
          <h4 class="mb-0 me-2">$38.5k</h4>
          <p class="mb-0 text-success">+62%</p>
        </div>
        <span class="d-block mb-2 text-muted">Sessions</span>
      </div>
      <div class="card-body">
        <div id="sessions"></div>
      </div>
    </div>
  </div>
  <!--/ Sessions line chart -->

  <!-- overview Radial chart -->
  <div class="col-xl-2 col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-header pb-0">
        <div class="d-flex align-items-end mb-1 flex-wrap gap-2">
          <h4 class="mb-0 me-2">$67.1k</h4>
          <p class="mb-0 text-success">+49%</p>
        </div>
        <span class="d-block mb-2 text-muted">Overview</span>
      </div>
      <div class="card-body">
        <div id="overviewChart" class="d-flex align-items-center"></div>
      </div>
    </div>
  </div>
  <!--/ overview Radial chart -->

  <!-- Total Profit chart -->
  <div class="col-xl-2 col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-header pb-0">
        <div class="d-flex align-items-end mb-1 flex-wrap gap-2">
          <h4 class="mb-0 me-2">$88.5k</h4>
          <p class="mb-0 text-danger">-18%</p>
        </div>
        <span class="d-block mb-2 text-muted">Total Profit</span>
      </div>
      <div class="card-body">
        <div id="totalProfitChart"></div>
      </div>
    </div>
  </div>
  <!--/ Total Profit chart -->

  <!-- Total Sales chart -->
  <div class="col-xl-2 col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-header pb-0">
        <div class="d-flex align-items-end mb-1 flex-wrap gap-2">
          <h4 class="mb-0 me-2">$22.6k</h4>
          <p class="mb-0 text-success">+38%</p>
        </div>
        <span class="d-block mb-2 text-muted">Total Sales</span>
      </div>
      <div class="card-body">
        <div id="totalSalesChart"></div>
      </div>
    </div>
  </div>
  <!--/ Total Sales chart -->

  <!-- Total Growth chart -->
  <div class="col-xl-2 col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-header pb-0">
        <div class="d-flex align-items-end mb-1 flex-wrap gap-2">
          <h4 class="mb-0 me-2">$27.9k</h4>
          <p class="mb-0 text-success">+16%</p>
        </div>
        <span class="d-block mb-2 text-muted">Total Growth</span>
      </div>
      <div class="card-body">
        <div id="totalGrowthChart"></div>
      </div>
    </div>
  </div>
  <!--/ Total Sales chart -->

  <!-- Sales & Profit chart -->
  <div class="col-xl-3 col-sm-6">
    <div class="card">
      <div class="card-body pb-0">
        <div class="row">
          <div class="col-6">
            <div id="salesChart"></div>
          </div>
          <div class="col-6 px-0">
            <div class="card-info pt-2 ps-2">
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <h5 class="mb-0">152k</h5>
                <div class="d-flex  text-success">
                  <p class="mb-0">+12%</p>
                  <div class="mdi mdi-chevron-up"></div>
                </div>
              </div>
              <p class="mb-0 mt-1">Total Sales</p>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="card-body pt-0">
        <div class="row">
          <div class="col-6">
            <div id="profitChart"></div>
          </div>
          <div class="col-6 px-0">
            <div class="card-info pt-2 ps-2">
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <h5 class="mb-0">89.5k</h5>
                <div class="d-flex text-danger">
                  <p class="mb-0">-8%</p>
                  <div class="mdi mdi-chevron-down"></div>
                </div>
              </div>
              <p class="mb-0 mt-1">Total Profit</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Sales & Profit chart -->

  <!-- Total Visits -->
  <div class="col-xl-3 col-sm-6">
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
            <h4 class="mb-0 pt-1">23.5%</h4>
            <small class="text-muted">2,890</small>
          </div>
          <div class="col-4">
            <div class="divider divider-vertical">
              <div class="divider-text">
                <span class="badge-divider-bg bg-label-secondary">VS</span>
              </div>
            </div>
          </div>
          <div class="col-4 text-end">
            <div class="d-flex gap-2 justify-content-end align-items-center mb-2">
              <p class="mb-0 text-muted">Desktop</p>
              <div class="avatar avatar-xs flex-shrink-0">
                <div class="avatar-initial rounded bg-label-primary">
                  <i class="mdi mdi-monitor mdi-14px"></i>
                </div>
              </div>
            </div>
            <h4 class="mb-0 pt-1">76.5%</h4>
            <small class="text-muted">22,465</small>
          </div>
        </div>
        <div class="d-flex align-items-center mt-3">
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
  <div class="col-xl-3 col-sm-6">
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

  <!-- Total Impression & Order Chart -->
  <div class="col-xl-3 col-sm-6">
    <div class="card">
      <div class="card-body pb-0 pt-3">
        <div class="row d-flex align-items-center">
          <div class="col-5">
            <div class="chart-progress" data-color="primary" data-series="70" data-icon="../../assets/img/icons/misc/card-icon-laptop.png"></div>
          </div>
          <div class="col-7">
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
          <div class="col-5">
            <div class="chart-progress" data-color="warning" data-series="40" data-icon="../../assets/img/icons/misc/card-icon-bag.png"></div>
          </div>
          <div class="col-7">
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

  <!-- Weekly Sales-->
  <div class="col-lg-6">
    <div class="swiper-container swiper-container-horizontal swiper swiper-sales" id="swiper-weekly-sales">
      <div class="swiper-wrapper">
        <div class="swiper-slide pb-3">
          <h5 class="mb-2">Weekly Sales</h5>
          <div class="d-flex align-items-center gap-2">
            <small>Total $23.5k Earning</small>
            <div class="d-flex text-success">
              <small class="fw-medium">+62%</small>
              <i class="mdi mdi-chevron-up"></i>
            </div>
          </div>
          <div class="d-flex align-items-center mt-3">
            <img src="{{ asset('assets/img/products/card-apple-iphone-x.png')}}" alt="Weekly sales" width="84" class="rounded">
            <div class="d-flex flex-column w-100 ms-4">
              <h6 class="mb-3">Mobiles & Computers</h6>
              <div class="row d-flex flex-wrap justify-content-between">
                <div class="col-6">
                  <ul class="list-unstyled mb-0">
                    <li class="d-flex mb-2 pb-1 align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">24</small>
                      <small class="mb-0 text-truncate">Mobiles</small>
                    </li>
                    <li class="d-flex align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">12</small>
                      <small class="mb-0 text-truncate">Tablets</small>
                    </li>
                  </ul>
                </div>
                <div class="col-6">
                  <ul class="list-unstyled mb-0">
                    <li class="d-flex mb-2 pb-1 align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">50</small>
                      <small class="mb-0 text-truncate">Accessories</small>
                    </li>
                    <li class="d-flex align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">38</small>
                      <small class="mb-0 text-truncate">Computers</small>
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
          <h5 class="mb-2">Weekly Sales</h5>
          <div class="d-flex align-items-center gap-2">
            <small>Total $23.5k Earning</small>
            <div class="d-flex text-success">
              <small class="fw-medium">+62%</small>
              <i class="mdi mdi-chevron-up"></i>
            </div>
          </div>
          <div class="d-flex align-items-center">
            <img src="{{ asset('assets/img/products/card-apple-iphone-x.png')}}" alt="Weekly sales" width="84" class="rounded">
            <div class="d-flex flex-column w-100 ms-4">
              <h6 class="mt-0 mt-md-3 mb-3">Appliances & Electronics</h6>
              <div class="row d-flex flex-wrap justify-content-between">
                <div class="col-6">
                  <ul class="list-unstyled mb-0">
                    <li class="d-flex mb-2 pb-1 align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">16</small>
                      <small class="mb-0 text-truncate">TV's</small>
                    </li>
                    <li class="d-flex align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">40</small>
                      <small class="mb-0 text-truncate">Speakers</small>
                    </li>
                  </ul>
                </div>
                <div class="col-6">
                  <ul class="list-unstyled mb-0">
                    <li class="d-flex mb-2 pb-1 align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">9</small>
                      <small class="mb-0 text-truncate">Cameras</small>
                    </li>
                    <li class="d-flex align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">18</small>
                      <small class="mb-0 text-truncate">Consoles</small>
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
          <h5 class="mb-2">Weekly Sales</h5>
          <div class="d-flex align-items-center gap-2">
            <small>Total $23.5k Earning</small>
            <div class="d-flex text-success">
              <small class="fw-medium">+62%</small>
              <i class="mdi mdi-chevron-up"></i>
            </div>
          </div>
          <div class="d-flex align-items-center">
            <img src="{{ asset('assets/img/products/card-apple-iphone-x.png')}}" alt="Weekly sales" width="84" class="rounded">
            <div class="d-flex flex-column w-100 ms-4">
              <h6 class="mt-0 mt-md-3 mb-3">Fashion</h6>
              <div class="row d-flex flex-wrap justify-content-between">
                <div class="col-6">
                  <ul class="list-unstyled mb-0">
                    <li class="d-flex mb-2 pb-1 align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">16</small>
                      <small class="mb-0 text-truncate">T-shirts</small>
                    </li>
                    <li class="d-flex align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">29</small>
                      <small class="mb-0 text-truncate">Watches</small>
                    </li>
                  </ul>
                </div>
                <div class="col-6">
                  <ul class="list-unstyled mb-0">
                    <li class="d-flex mb-2 pb-1 align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">43</small>
                      <small class="mb-0 text-truncate">Shoes</small>
                    </li>
                    <li class="d-flex align-items-center">
                      <small class="mb-0 me-2 sales-text-bg bg-label-secondary">7</small>
                      <small class="mb-0 text-truncate">Sun Glasses</small>
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
  <!--/ Weekly Sales-->

  <!-- Marketing & Sales-->
  <div class="col-lg-6">
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
            <img src="{{ asset('assets/img/products/card-marketing-expense-logo.png')}}" alt="Marketing and sales" width="84" class="rounded">
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
            <img src="{{ asset('assets/img/products/card-accounting-logo.png')}}" alt="Marketing and sales" width="84" class="rounded">
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
            <img src="{{ asset('assets/img/products/card-sales-overview-logo.png')}}" alt="Marketing and sales" width="84" class="rounded">
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

  <!-- Sales Overview-->
  <div class="col-lg-6">
    <div class="card">
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
            <small class="text-muted">New Customers</small>
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
            <small class="text-muted">Total Profit</small>
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
            <small class="text-muted">New Transactions</small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Sales Overview-->

  <!-- Live Visitors-->
  <div class="col-lg-6">
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
@endsection
