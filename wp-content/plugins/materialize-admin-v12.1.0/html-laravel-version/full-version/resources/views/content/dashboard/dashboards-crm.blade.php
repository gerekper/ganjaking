@extends('layouts/layoutMaster')

@section('title', 'crm')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/swiper/swiper.css')}}" />
@endsection

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/cards-statistics.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/cards-analytics.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
<script src="{{asset('assets/vendor/libs/swiper/swiper.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/dashboards-crm.js')}}"></script>
@endsection

@section('content')
<div class="row gy-4 mb-4">
  <!-- Congratulations card -->
  <div class="col-xl-4 col-lg-4 col-md-12 col-sm-8 col-12">
    <div class="card h-100">
      <div class="card-body text-nowrap">
        <h4 class="card-title mb-1 d-flex gap-2 flex-wrap">Congratulations <strong>Norris!</strong> 🎉</h4>
        <p class="pb-0">Best seller of the month</p>
        <h4 class="text-primary mb-1">$42.8k</h4>
        <p class="mb-2 pb-1">78% of target 🚀</p>
        <a href="javascript:;" class="btn btn-sm btn-primary">View Sales</a>
      </div>
      <img src="{{asset('assets/img/illustrations/trophy.png')}}" class="position-absolute bottom-0 end-0 me-3" height="140" alt="view sales">
    </div>
  </div>
  <!--/ Congratulations card -->

  <!-- Total Profit -->
  <div class="col-xl-2 col-lg-2 col-md-3 col-sm-4 col-6">
    <div class="card h-100">
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
          <p class="text-muted">Total Order</p>
          <div class="badge bg-label-secondary rounded-pill">Last 4 Month</div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Total Profit -->

  <!-- Total Expenses -->
  <div class="col-xl-2 col-lg-2 col-md-3 col-sm-4 col-6">
    <div class="card h-100">
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
          <div class="badge bg-label-secondary rounded-pill">Last Six Month</div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Total Expenses -->

  <!-- Total Profit chart -->
  <div class="col-xl-2 col-lg-2 col-md-3 col-sm-4 col-6">
    <div class="card h-100">
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

  <!-- Total Growth chart -->
  <div class="col-xl-2 col-lg-2 col-md-3 col-sm-4 col-6">
    <div class="card h-100">
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
</div>
<div class="row gy-4">
  <!-- Organic Sessions Chart-->
  <div class="col-lg-4 col-12">
    <div class="card">
      <div class="card-header pb-1">
        <div class="d-flex justify-content-between">
          <h5 class="mb-1">Organic Sessions</h5>
          <div class="dropdown">
            <button class="btn p-0" type="button" id="organicSessionsDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="mdi mdi-dots-vertical mdi-24px"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="organicSessionsDropdown">
              <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div id="organicSessionsChart"></div>
      </div>
    </div>
  </div>
  <!--/ Organic Sessions Chart-->

  <!-- Project Timeline Chart-->
  <div class="col-lg-8 col-12">
    <div class="card">
      <div class="row">
        <div class="col-md-8 col-12">
          <div class="card-header">
            <h5 class="mb-1">Project Timeline</h5>
            <small class="mb-0 text-body">Total 840 Task Completed</small>
          </div>
          <div class="card-body px-2">
            <div id="projectTimelineChart"></div>
          </div>
        </div>
        <div class="col-md-4 col-12 border-start">
          <div class="card-header">
            <div class="d-flex justify-content-between">
              <h5 class="mb-1">Project List</h5>
              <div class="dropdown">
                <button class="btn p-0" type="button" id="projectTimeline" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="mdi mdi-dots-vertical mdi-24px"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="projectTimeline">
                  <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                  <a class="dropdown-item" href="javascript:void(0);">Share</a>
                  <a class="dropdown-item" href="javascript:void(0);">Update</a>
                </div>
              </div>
            </div>
            <small class="text-body mb-0">4 Ongoing Project</small>
          </div>
          <div class="card-body">
            <div class="d-flex align-items-center mb-3 pb-1">
              <div class="avatar">
                <div class="avatar-initial bg-label-primary rounded">
                  <i class="mdi mdi-cellphone mdi-24px"></i>
                </div>
              </div>
              <div class="ms-3 d-flex flex-column">
                <h6 class="mb-1 fw-semibold">IOS Application</h6>
                <small class="text-muted">Task 840/2.5K</small>
              </div>
            </div>
            <div class="d-flex align-items-center mb-3 pb-1">
              <div class="avatar">
                <div class="avatar-initial bg-label-success rounded">
                  <i class="mdi mdi-creation mdi-24px"></i>
                </div>
              </div>
              <div class="ms-3 d-flex flex-column">
                <h6 class="mb-1 fw-semibold">Web Application</h6>
                <small class="text-muted">Task 99/1.42k</small>
              </div>
            </div>
            <div class="d-flex align-items-center mb-3 pb-1">
              <div class="avatar">
                <div class="avatar-initial bg-label-secondary rounded">
                  <i class="mdi mdi-credit-card-outline mdi-24px"></i>
                </div>
              </div>
              <div class="ms-3 d-flex flex-column">
                <h6 class="mb-1 fw-semibold">Bank Dashboard</h6>
                <small class="text-muted">Task 58/100</small>
              </div>
            </div>
            <div class="d-flex align-items-center">
              <div class="avatar">
                <div class="avatar-initial bg-label-info rounded">
                  <i class="mdi mdi-pencil-ruler-outline mdi-24px"></i>
                </div>
              </div>
              <div class="ms-3 d-flex flex-column">
                <h6 class="mb-1 fw-semibold">UI Kit Design</h6>
                <small class="text-muted">Task 120/350</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Project Timeline Chart-->

  <!-- Weekly Overview Chart -->
  <div class="col-lg-4 col-md-6 col-12">
    <div class="card">
      <div class="card-header">
        <div class="d-flex justify-content-between">
          <h5 class="mb-1">Weekly Overview</h5>
          <div class="dropdown">
            <button class="btn p-0" type="button" id="weeklyOverviewDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="mdi mdi-dots-vertical mdi-24px"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="weeklyOverviewDropdown">
              <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div id="weeklyOverviewChart"></div>
        <div class="mt-1">
          <div class="d-flex align-items-center gap-3">
            <h3 class="mb-0">62%</h3>
            <p class="mb-0 text-muted">Your sales performance is 35% 😎 better compared to last month</p>
          </div>
          <div class="d-grid mt-3">
            <button class="btn btn-primary" type="button">Details</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Weekly Overview Chart -->

  <!-- Social Network Visits -->
  <div class="col-lg-4 col-md-6 col-12">
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
          <li class="d-flex pb-1 mb-3">
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
          <li class="d-flex pb-1 mb-3">
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
          <li class="d-flex pb-1 mb-3">
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
          <li class="d-flex pb-1">
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

  <!-- Monthly Budget Chart-->
  <div class="col-lg-4 col-md-6 col-12">
    <div class="card h-100">
      <div class="card-header pb-1">
        <div class="d-flex justify-content-between">
          <h5 class="mb-1">Monthly Budget</h5>
          <div class="dropdown">
            <button class="btn p-0" type="button" id="monthlyBudgetDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="mdi mdi-dots-vertical mdi-24px"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="monthlyBudgetDropdown">
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
              <a class="dropdown-item" href="javascript:void(0);">Update</a>
              <a class="dropdown-item" href="javascript:void(0);">Share</a>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div id="monthlyBudgetChart"></div>
        <div class="mt-3">
          <p class="mb-0 text-muted">Last month you had $2.42 expense transactions, 12 savings entries and 4 bills.</p>
        </div>
      </div>
    </div>
  </div>
  <!--/ Monthly Budget Chart-->

  <!-- Meeting Schedule -->
  <div class="col-lg-4 col-md-6 col-12">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0 me-2">Meeting Schedule</h5>
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
      <div class="card-body pt-2">
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


  <!-- External Links Chart -->
  <div class="col-lg-4 col-md-6 col-12">
    <div class="card">
      <div class="card-header">
        <div class="d-flex justify-content-between">
          <h5 class="mb-1">External Links</h5>
          <div class="dropdown">
            <button class="btn p-0" type="button" id="externalLinksDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="mdi mdi-dots-vertical mdi-24px"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="externalLinksDropdown">
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
              <a class="dropdown-item" href="javascript:void(0);">Update</a>
              <a class="dropdown-item" href="javascript:void(0);">Share</a>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div id="externalLinksChart"></div>
        <div class="table-responsive text-nowrap">
          <table class="table table-borderless">
            <tbody>
              <tr>
                <td class="text-start pb-0 ps-0">
                  <div class="d-flex align-items-center">
                    <div class="badge badge-dot bg-primary me-2"></div>
                    <h6 class="mb-0 fw-semibold">Google Analytics</h6>
                  </div>
                </td>
                <td class="pb-0">
                  <p class="mb-0 text-muted">$845k</p>
                </td>
                <td class="pe-0 pb-0">
                  <div class="d-flex align-items-center justify-content-end">
                    <h6 class="mb-0 fw-semibold me-2">82%</h6>
                    <i class="mdi mdi-chevron-up text-success"></i>
                  </div>
                </td>
              </tr>
              <tr>
                <td class="text-start pb-0 ps-0">
                  <div class="d-flex align-items-center">
                    <div class="badge badge-dot bg-secondary me-2"></div>
                    <h6 class="mb-0 fw-semibold">Facebook Ads</h6>
                  </div>
                </td>
                <td class="pb-0">
                  <p class="mb-0 text-muted">$12.5k</p>
                </td>
                <td class="pe-0 pb-0">
                  <div class="d-flex align-items-center justify-content-end">
                    <h6 class="mb-0 fw-semibold me-2">52%</h6>
                    <i class="mdi mdi-chevron-down text-danger"></i>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <!--/ External Links Chart -->

  <!-- Payment History -->
  <div class="col-lg-4 col-md-6 col-12">
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
          <thead>
            <tr>
              <th class="text-capitalize text-body fw-medium fs-6">Card</th>
              <th class="text-capitalize text-body fw-medium fs-6">Date</th>
              <th class="text-end text-capitalize text-body fw-medium fs-6">Spend</th>
            </tr>
          </thead>
          <tbody class="border-top">
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


  <!-- Most Sales in Countries -->
  <div class="col-lg-4 col-12">
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
      <div class="card-body">
        <div class="mt-1">
          <div class="d-flex align-items-center">
            <h1 class="mb-0 me-3 display-3">22,842</h1>
            <div class="badge bg-label-success rounded-pill">+42%</div>
          </div>
          <small class="text-muted mt-1">Sales Last 90 Days</small>
        </div>
      </div>
      <div class="table-responsive text-nowrap border-top">
        <table class="table">
          <tbody class="table-border-bottom-0">
            <tr>
              <td class="pe-5"><span class="text-heading">Australia</span></td>
              <td class="ps-5 d-flex justify-content-end"><span class="text-heading fw-semibold">18,879</span></td>
              <td>
                <div class="d-flex align-items-center justify-content-end">
                  <span class="text-heading fw-semibold me-2">15%</span>
                  <i class="mdi mdi-chevron-down mdi-20px text-danger"></i>
                </div>
              </td>
            </tr>
            <tr>
              <td class="pe-5"><span class="text-heading">Canada</span></td>
              <td class="ps-5 d-flex justify-content-end"><span class="text-heading fw-semibold">10,357</span></td>
              <td>
                <div class="d-flex align-items-center justify-content-end">
                  <span class="text-heading fw-semibold me-2">85%</span>
                  <i class="mdi mdi-chevron-up mdi-20px text-success"></i>
                </div>
              </td>
            </tr>
            <tr>
              <td class="pe-5"><span class="text-heading">India</span></td>
              <td class="ps-5 d-flex justify-content-end"><span class="text-heading fw-semibold">4,860</span></td>
              <td>
                <div class="d-flex align-items-center justify-content-end">
                  <span class="text-heading fw-semibold me-2">48%</span>
                  <i class="mdi mdi-chevron-up mdi-20px text-success"></i>
                </div>
              </td>
            </tr>
            <tr>
              <td class="pe-5"><span class="text-heading">France</span></td>
              <td class="ps-5 d-flex justify-content-end"><span class="text-heading fw-semibold">2,560</span></td>
              <td>
                <div class="d-flex align-items-center justify-content-end">
                  <span class="text-heading fw-semibold me-2">36%</span>
                  <i class="mdi mdi-chevron-up mdi-20px text-success"></i>
                </div>
              </td>
            </tr>
            <tr>
              <td class="pe-5"><span class="text-heading">United State</span></td>
              <td class="ps-5 d-flex justify-content-end"><span class="text-heading fw-semibold">899</span></td>
              <td>
                <div class="d-flex align-items-center justify-content-end">
                  <span class="text-heading fw-semibold me-2">16%</span>
                  <i class="mdi mdi-chevron-down mdi-20px text-danger"></i>
                </div>
              </td>
            </tr>
            <tr>
              <td class="pe-5"><span class="text-heading">Japan</span></td>
              <td class="ps-5 d-flex justify-content-end"><span class="text-heading fw-semibold">43</span></td>
              <td>
                <div class="d-flex align-items-center justify-content-end">
                  <span class="text-heading fw-semibold me-2">35%</span>
                  <i class="mdi mdi-chevron-up mdi-20px text-success"></i>
                </div>
              </td>
            </tr>
            <tr>
              <td class="pe-5"><span class="text-heading">Brazil</span></td>
              <td class="ps-5 d-flex justify-content-end"><span class="text-heading fw-semibold">18</span></td>
              <td>
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
  <!--/ Most Sales in Countries -->

  <!-- Roles Datatables -->
  <div class="col-lg-8 col-12">
    <div class="card">
      <div class="table-responsive rounded-3">
        <table class="datatables-crm table table-sm">
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
</div>
@endsection
