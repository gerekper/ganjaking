@extends('layouts/layoutMaster')

@section('title', 'Cards Analytics- UI elements'
)

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
@endsection

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/cards-analytics.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/cards-analytics.js')}}"></script>
@endsection

@section('content')

<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">UI Elements /</span> Cards Analytics
</h4>

<div class="row gy-4">
  <!-- Total Transactions & Report Chart -->
  <div class="col-12 col-xl-8">
    <div class="card">
      <div class="row">
        <div class="col-md-7 col-12 order-2 order-md-0">
          <div class="card-header">
            <h5 class="mb-0">Total Transactions</h5>
          </div>
          <div class="card-body">
            <div id="totalTransactionChart"></div>
          </div>
        </div>
        <div class="col-md-5 col-12 border-start">
          <div class="card-header">
            <div class="d-flex justify-content-between">
              <h5 class="mb-1">Report</h5>
              <div class="dropdown">
                <button class="btn p-0" type="button" id="totalTransaction" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="mdi mdi-dots-vertical mdi-24px"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="totalTransaction">
                  <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                  <a class="dropdown-item" href="javascript:void(0);">Share</a>
                  <a class="dropdown-item" href="javascript:void(0);">Update</a>
                </div>
              </div>
            </div>
            <p class="text-muted mb-0">Last month transactions $234.40k</p>
          </div>
          <div class="card-body pt-3">
            <div class="row">
              <div class="col-6 border-end">
                <div class="d-flex flex-column align-items-center">
                  <div class="avatar">
                    <div class="avatar-initial bg-label-success rounded">
                      <div class="mdi mdi-trending-up mdi-24px"></div>
                    </div>
                  </div>
                  <p class="text-muted my-2">This Week</p>
                  <h6 class="mb-0 fw-semibold">+82.45%</h6>
                </div>
              </div>
              <div class="col-6">
                <div class="d-flex flex-column align-items-center">
                  <div class="avatar">
                    <div class="avatar-initial bg-label-primary rounded">
                      <div class="mdi mdi-trending-down mdi-24px"></div>
                    </div>
                  </div>
                  <p class="text-muted my-2">This Week</p>
                  <h6 class="mb-0 fw-semibold">-24.86%</h6>
                </div>
              </div>
            </div>
            <hr class="my-4">
            <div class="d-flex justify-content-around">
              <div>
                <p class="text-muted mb-1">Performance</p>
                <h6 class="mb-0 fw-semibold">+94.15%</h6>
              </div>
              <button class="btn btn-primary" type="button">view report</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Total Transactions & Report Chart -->

  <!-- Performance Overview Chart-->
  <div class="col-12 col-xl-4 col-md-6">
    <div class="card">
      <div class="card-header pb-1">
        <div class="d-flex justify-content-between">
          <h5 class="mb-1">Performance Overview</h5>
          <div class="dropdown">
            <button class="btn p-0" type="button" id="performanceOverviewDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="mdi mdi-dots-vertical mdi-24px"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="performanceOverviewDropdown">
              <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div id="performanceOverviewChart"></div>
        <div class="d-flex align-items-center justify-content-center gap-1">
          <div class="badge badge-dot bg-warning"></div>
          <p class="text-muted mb-0">Average cost per interaction is $5.65</p>
        </div>
      </div>
    </div>
  </div>
  <!--/ Performance Overview Chart-->

  <!-- visits By Day Chart-->
  <div class="col-12 col-xl-4 col-md-6">
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
              <i class="mdi mdi-chevron-right mdi-24px"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ visits By Day Chart-->

  <!-- Organic Sessions Chart-->
  <div class="col-12 col-xl-4 col-md-6">
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

  <!-- Weekly Sales Chart-->
  <div class="col-12 col-xl-4 col-md-6">
    <div class="card">
      <div class="card-header">
        <div class="d-flex justify-content-between">
          <h5 class="mb-1">Weekly Sales</h5>
          <div class="dropdown">
            <button class="btn p-0" type="button" id="weeklySalesDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="mdi mdi-dots-vertical mdi-24px"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="weeklySalesDropdown">
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
              <a class="dropdown-item" href="javascript:void(0);">Update</a>
              <a class="dropdown-item" href="javascript:void(0);">Share</a>
            </div>
          </div>
        </div>
        <p class="text-muted mb-0">Total 85.4k Sales</p>
      </div>
      <div class="card-body">
        <div class="row mb-2">
          <div class="col-6 d-flex align-items-center">
            <div class="avatar">
              <div class="avatar-initial bg-label-primary rounded">
                <i class="mdi mdi-trending-up mdi-24px"></i>
              </div>
            </div>
            <div class="ms-3 d-flex flex-column">
              <small class="text-muted mb-1">Net Income</small>
              <h6 class="mb-0 fw-semibold">$438.5K</h6>
            </div>
          </div>
          <div class="col-6 d-flex align-items-center">
            <div class="avatar">
              <div class="avatar-initial bg-label-warning rounded">
                <i class="mdi mdi-currency-usd mdi-24px"></i>
              </div>
            </div>
            <div class="ms-3 d-flex flex-column">
              <small class="text-muted mb-1">Expense</small>
              <h6 class="mb-0 fw-semibold">$22.4K</h6>
            </div>
          </div>
        </div>
        <div id="weeklySalesChart"></div>
      </div>
    </div>
  </div>
  <!--/ Weekly Sales Chart-->

  <!-- Project Timeline Chart-->
  <div class="col-12 col-xl-8">
    <div class="card">
      <div class="row">
        <div class="col-md-8 col-12 order-2 order-md-0">
          <div class="card-header">
            <h5 class="mb-0">Project Timeline</h5>
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
            <p class="text-muted mb-0">4 Ongoing Project</p>
          </div>
          <div class="card-body pt-3">
            <div class="d-flex align-items-center mb-4 pb-1">
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
            <div class="d-flex align-items-center mb-4 pb-1">
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

  <!-- Monthly Budget Chart-->
  <div class="col-12 col-xl-4 col-md-6">
    <div class="card">
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

  <!-- Performance Chart -->
  <div class="col-12 col-xl-4 col-md-6">
    <div class="card">
      <div class="card-header pb-1">
        <div class="d-flex justify-content-between">
          <h5 class="mb-1">Performance</h5>
          <div class="dropdown">
            <button class="btn p-0" type="button" id="performanceDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="mdi mdi-dots-vertical mdi-24px"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="performanceDropdown">
              <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div id="performanceChart"></div>
      </div>
    </div>
  </div>
  <!--/ Performance Chart -->

  <!-- External Links Chart -->
  <div class="col-12 col-xl-4 col-md-6 order-md-1 order-lg-0">
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

  <!-- Sales Country Chart -->
  <div class="col-12 col-xl-4 col-lg-6">
    <div class="card">
      <div class="card-header">
        <div class="d-flex justify-content-between">
          <h5 class="mb-1">Sales Country</h5>
          <div class="dropdown">
            <button class="btn p-0" type="button" id="salesCountryDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="mdi mdi-dots-vertical mdi-24px"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="salesCountryDropdown">
              <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
            </div>
          </div>
        </div>
        <p class="mb-0 text-muted">Total $42,580 Sales</p>
      </div>
      <div class="card-body pb-1 px-0">
        <div id="salesCountryChart"></div>
      </div>
    </div>
  </div>
  <!--/ Sales Country Chart -->

  <!-- Activity Timeline -->
  <div class="col-12 col-xl-8 col-lg-6">
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
      <div class="card-body pt-4 pb-1">
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
  <!-- Activity Timeline -->

  <!-- Weekly Overview Chart -->
  <div class="col-12 col-xl-4 col-md-6 order-md-2 order-lg-0">
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
        <p class="mb-0 text-muted">Total $42,580 Sales</p>
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
</div>
@endsection
