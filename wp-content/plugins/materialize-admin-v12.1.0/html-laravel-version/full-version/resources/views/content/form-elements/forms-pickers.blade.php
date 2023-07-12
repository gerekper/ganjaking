@extends('layouts/layoutMaster')

@section('title', 'Pickers - Forms')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/pickr/pickr-themes.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js')}}"></script>
<script src="{{asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.js')}}"></script>
<script src="{{asset('assets/vendor/libs/pickr/pickr.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/forms-pickers.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Forms /</span> Pickers
</h4>

<div class="row">
  <!-- Flat Picker -->
  <div class="col-12 mb-4">
    <div class="card">
      <h5 class="card-header">Flatpickr</h5>
      <div class="card-body">
        <div class="row">
          <!-- Date Picker-->
          <div class="col-md-6 col-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" class="form-control" placeholder="YYYY-MM-DD" id="flatpickr-date" />
              <label for="flatpickr-date">Date Picker</label>
            </div>
          </div>
          <!-- /Date Picker -->

          <!-- Time Picker-->
          <div class="col-md-6 col-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" class="form-control" placeholder="HH:MM" id="flatpickr-time" />
              <label for="flatpickr-time">Time Picker</label>
            </div>
          </div>
          <!-- /Time Picker -->

          <!-- Datetime Picker-->
          <div class="col-md-6 col-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" class="form-control" placeholder="YYYY-MM-DD HH:MM" id="flatpickr-datetime" />
              <label for="flatpickr-datetime">Datetime Picker</label>
            </div>
          </div>
          <!-- /Datetime Picker-->

          <!-- Multiple Dates Picker-->
          <div class="col-md-6 col-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" class="form-control" placeholder="YYYY-MM-DD HH:MM" id="flatpickr-multi" />
              <label for="flatpickr-multi">Multiple Dates Picker</label>
            </div>
          </div>
          <!-- /Multiple Dates Picker-->

          <!-- Range Picker-->
          <div class="col-md-6 col-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" class="form-control" placeholder="YYYY-MM-DD to YYYY-MM-DD" id="flatpickr-range" />
              <label for="flatpickr-range">Range Picker</label>
            </div>
          </div>
          <!-- /Range Picker-->

          <!-- Human Friendly Date Picker-->
          <div class="col-md-6 col-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" class="form-control" placeholder="Month DD, YYYY" id="flatpickr-human-friendly" />
              <label for="flatpickr-human-friendly">Human Friendly Date Picker</label>
            </div>
          </div>
          <!-- /Human Friendly Date Picker-->

          <!-- Disabled Range-->
          <div class="col-md-6 col-12 mb-md-0 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" class="form-control" placeholder="YYYY-MM-DD" id="flatpickr-disabled-range" />
              <label for="flatpickr-disabled-range">Disabled Range</label>
            </div>
          </div>
          <!-- /Disabled Range-->

          <!-- Inline Picker-->
          <div class="col-md-6 col-12">
            <div class="form-floating form-floating-outline">
              <input type="text" class="form-control mb-1" placeholder="YYYY-MM-DD" id="flatpickr-inline" />
              <label for="flatpickr-inline">Inline Picker</label>
            </div>
          </div>
          <!-- /Inline Picker-->
        </div>
      </div>
    </div>
  </div>
  <!-- /Flatpickr -->

  <!-- Bootstrap Datepicker -->
  <div class="col-12 mb-4">
    <div class="card">
      <h5 class="card-header">Bootstrap Datepicker</h5>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6 col-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" id="bs-datepicker-basic" placeholder="MM/DD/YYYY" class="form-control" />
              <label for="bs-datepicker-basic">Basic</label>
            </div>
          </div>
          <div class="col-md-6 col-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" id="bs-datepicker-format" placeholder="DD/MM/YYYY" class="form-control" />
              <label for="bs-datepicker-format">Format</label>
            </div>
          </div>
          <div class="col-md-6 col-12 mb-md-0 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" id="bs-datepicker-autoclose" placeholder="MM/DD/YYYY" class="form-control" />
              <label for="bs-datepicker-autoclose">Auto Close</label>
            </div>
          </div>
          <div class="col-md-6 col-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" id="bs-datepicker-disabled-days" placeholder="MM/DD/YYYY" class="form-control" />
              <label for="bs-datepicker-disabled-days">Disabled Days</label>
            </div>
          </div>
          <div class="col-md-6 col-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" id="bs-datepicker-multidate" placeholder="MM/DD/YYYY, MM/DD/YYYY" class="form-control" />
              <label for="bs-datepicker-multidate">Multidate</label>
            </div>
          </div>
          <div class="col-md-6 col-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" id="bs-datepicker-options" placeholder="MM/DD/YYYY" class="form-control" />
              <label for="bs-datepicker-options">Options</label>
            </div>
          </div>
          <div class="col-md-6 col-12 mb-4">
            <label for="dateRangePicker" class="form-label">Date Range</label>
            <div class="input-group input-daterange" id="bs-datepicker-daterange">
              <input type="text" id="dateRangePicker" placeholder="MM/DD/YYYY" class="form-control" />
              <span class="input-group-text">to</span>
              <input type="text" placeholder="MM/DD/YYYY" class="form-control" />
            </div>
          </div>
          <div class="col-md-6 col-12">
            <label class="form-label">Inline</label>
            <div id="bs-datepicker-inline"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /Bootstrap Datepicker -->

  <!-- Bootstrap Daterangepicker -->
  <div class="col-12 mb-4">
    <div class="card">
      <h5 class="card-header">Bootstrap Daterange Picker</h5>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6 col-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" id="bs-rangepicker-basic" class="form-control" />
              <label for="bs-rangepicker-basic">Basic</label>
            </div>
          </div>
          <div class="col-md-6 col-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" id="bs-rangepicker-single" class="form-control" />
              <label for="bs-rangepicker-single">Single Picker</label>
            </div>
          </div>
          <div class="col-md-6 col-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" id="bs-rangepicker-time" class="form-control" />
              <label for="bs-rangepicker-time">With Time Picker</label>
            </div>
          </div>
          <div class="col-md-6 col-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" id="bs-rangepicker-range" class="form-control" />
              <label for="bs-rangepicker-range">Ranges</label>
            </div>
          </div>
          <div class="col-md-6 col-12 mb-md-0 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" id="bs-rangepicker-week-num" class="form-control" />
              <label for="bs-rangepicker-week-num">Week Numbers</label>
            </div>
          </div>
          <div class="col-md-6 col-12">
            <div class="form-floating form-floating-outline">
              <input type="text" id="bs-rangepicker-dropdown" class="form-control" />
              <label for="bs-rangepicker-dropdown">Month & Year Dropdown</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /Bootstrap Daterangepicker -->

  <!-- jQuery Timepicker -->
  <div class="col-12 mb-4">
    <div class="card">
      <h5 class="card-header">jQuery Timepicker</h5>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6 col-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" id="timepicker-basic" placeholder="HH:MMam" class="form-control" />
              <label for="timepicker-basic">Basic</label>
            </div>
          </div>
          <div class="col-md-6 col-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" id="timepicker-min-max" placeholder="HH:MMam" class="form-control" />
              <label for="timepicker-min-max">Min-Max</label>
            </div>
          </div>
          <div class="col-md-6 col-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" id="timepicker-disabled-times" placeholder="HH:MMam" class="form-control" />
              <label for="timepicker-disabled-times">Disabled Times</label>
            </div>
          </div>
          <div class="col-md-6 col-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" id="timepicker-format" placeholder="HH:MM:SS" class="form-control" />
              <label for="timepicker-format">Format</label>
            </div>
          </div>
          <div class="col-md-6 col-12 mb-md-0 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" id="timepicker-step" placeholder="HH:MMam" class="form-control" />
              <label for="timepicker-step">Step</label>
            </div>
          </div>
          <div class="col-md-6 col-12">
            <div class="form-floating form-floating-outline">
              <input type="text" id="timepicker-24hours" placeholder="20:00:00" class="form-control" />
              <label for="timepicker-24hours">24 Hours Format</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /jQuery Timepicker -->

  <!-- Color Picker -->
  <div class="col-12">
    <div class="card">
      <h5 class="card-header">Color Picker</h5>
      <div class="card-body">
        <div class="row">
          <div class="classic col col-sm-3 col-lg-2">
            <p>Classic</p>
            <div id="color-picker-classic"></div>
          </div>
          <div class="monolith col col-sm-3 col-lg-2">
            <p>Monolith</p>
            <div id="color-picker-monolith"></div>
          </div>
          <div class="nano col col-sm-3 col-lg-2">
            <p>Nano</p>
            <div id="color-picker-nano"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /Color Picker-->
</div>

@endsection
