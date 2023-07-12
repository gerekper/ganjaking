@extends('layouts/layoutMaster')

@section('title', 'Extras - Forms')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/autosize/autosize.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.js')}}"></script>
<script src="{{asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/forms-extras.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Forms /</span> Extras
</h4>

<div class="row">
  <!-- Autosize -->
  <div class="col-12">
    <div class="card mb-4">
      <h5 class="card-header">Autosize</h5>
      <div class="card-body">
        <textarea id="autosize-demo" rows="3" class="form-control"></textarea>
      </div>
    </div>
  </div>
  <!-- /Autosize -->

  <!-- Input Mask -->
  <div class="col-12">
    <div class="card mb-4">
      <h5 class="card-header">CleaveJS Input Mask</h5>
      <div class="card-body">
        <div class="row">
          <!-- Credit Card -->
          <div class="col-xl-4 col-md-6 col-sm-12 mb-4">
            <div class="input-group input-group-merge">
              <div class="form-floating form-floating-outline">
                <input type="text" id="creditCardMask" name="creditCardMask" class="form-control credit-card-mask" placeholder="1356 3215 6548 7898" aria-describedby="creditCardMask2" />
                <label for="creditCardMask">Credit Card</label>
              </div>
              <span class="input-group-text cursor-pointer p-1" id="creditCardMask2"><span class="card-type"></span></span>
            </div>
          </div>
          <!-- Phone Number -->
          <div class="col-xl-4 col-md-6 col-sm-12 mb-4">
            <div class="input-group input-group-merge">
              <span class="input-group-text">US (+1)</span>
              <div class="form-floating form-floating-outline">
                <input type="text" id="phone-number-mask" class="form-control phone-number-mask" placeholder="202 555 0111" />
                <label for="phone-number-mask">Phone Number</label>
              </div>
            </div>
          </div>
          <!-- Date -->
          <div class="col-xl-4 col-md-6 col-sm-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" id="date-mask" class="form-control date-mask" placeholder="YYYY-MM-DD" />
              <label for="date-mask">Date</label>
            </div>
          </div>
          <!-- Time -->
          <div class="col-xl-4 col-md-6 col-sm-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" id="time-mask" class="form-control time-mask" placeholder="hh:mm:ss" />
              <label for="time-mask">Time</label>
            </div>
          </div>
          <!-- Numeral Formatting -->
          <div class="col-xl-4 col-md-6 col-sm-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" id="numeral-mask" class="form-control numeral-mask" placeholder="Enter Numeral" />
              <label for="numeral-mask">Numeral formatting</label>
            </div>
          </div>
          <!-- Blocks -->
          <div class="col-xl-4 col-md-6 col-sm-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" id="block-mask" class="form-control block-mask" placeholder="Blocks [4, 3, 3]" />
              <label for="block-mask">Blocks</label>
            </div>
          </div>
          <!-- Delimiters -->
          <div class="col-xl-4 col-md-6 col-sm-12 mb-4 mb-xl-0">
            <div class="form-floating form-floating-outline">
              <input type="text" id="delimiter-mask" class="form-control delimiter-mask" placeholder="Delimiter: '.'" />
              <label for="delimiter-mask">Delimiters</label>
            </div>
          </div>
          <!-- Custom Delimiters -->
          <div class="col-xl-4 col-md-6 col-sm-12 mb-4 mb-xl-0">
            <div class="form-floating form-floating-outline">
              <input type="text" id="custom-delimiter-mask" class="form-control custom-delimiter-mask" placeholder="Delimiter: ['.', '.', '-']" />
              <label for="custom-delimiter-mask">Custom Delimiters</label>
            </div>
          </div>
          <!-- Prefix -->
          <div class="col-xl-4 col-md-6 col-sm-12">
            <div class="form-floating form-floating-outline">
              <input type="text" id="prefix-mask" class="form-control prefix-mask" />
              <label for="prefix-mask">Prefix</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /Input Mask -->

  <!-- Bootstrap Maxlength -->
  <div class="col-12">
    <div class="card mb-4">
      <h5 class="card-header">Bootstrap Maxlength</h5>
      <div class="card-body">
        <div class="row">
          <div class="col-12 mb-4">
            <div class="form-floating form-floating-outline">
              <input type="text" id="bootstrap-maxlength-example1" class="form-control bootstrap-maxlength-example" placeholder="John Doe" maxlength="25" />
              <label for="bootstrap-maxlength-example1">Input</label>
            </div>
          </div>
          <div class="col-12">
            <div class="form-floating form-floating-outline">
              <textarea id="bootstrap-maxlength-example2" class="form-control h-px-100 bootstrap-maxlength-example" placeholder="Lorem ipsum..." rows="3" maxlength="255"></textarea>
              <label for="bootstrap-maxlength-example2">Textarea</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /Bootstrap Maxlength -->

  <!-- Form Repeater -->
  <div class="col-12">
    <div class="card">
      <h5 class="card-header">Form Repeater</h5>
      <div class="card-body">
        <form class="form-repeater">
          <div data-repeater-list="group-a">
            <div data-repeater-item>
              <div class="row">
                <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
                  <div class="form-floating form-floating-outline">
                    <input type="text" id="form-repeater-1-1" class="form-control" placeholder="john.doe" />
                    <label for="form-repeater-1-1">Username</label>
                  </div>
                </div>
                <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
                  <div class="form-floating form-floating-outline">
                    <input type="password" id="form-repeater-1-2" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                    <label for="form-repeater-1-2">Password</label>
                  </div>
                </div>
                <div class="mb-3 col-lg-6 col-xl-2 col-12 mb-0">
                  <div class="form-floating form-floating-outline">
                    <select id="form-repeater-1-3" class="form-select">
                      <option value="Male">Male</option>
                      <option value="Female">Female</option>
                    </select>
                    <label for="form-repeater-1-3">Gender</label>
                  </div>
                </div>
                <div class="mb-3 col-lg-6 col-xl-2 col-12 mb-0">
                  <div class="form-floating form-floating-outline">
                    <select id="form-repeater-1-4" class="form-select">
                      <option value="Designer">Designer</option>
                      <option value="Developer">Developer</option>
                      <option value="Tester">Tester</option>
                      <option value="Manager">Manager</option>
                    </select>
                    <label for="form-repeater-1-4">Profession</label>
                  </div>
                </div>
                <div class="mb-3 col-lg-12 col-xl-2 col-12 d-flex align-items-center mb-0">
                  <button class="btn btn-label-danger" data-repeater-delete>
                    <i class="mdi mdi-close me-1"></i>
                    <span class="align-middle">Delete</span>
                  </button>
                </div>
              </div>
              <hr>
            </div>
          </div>
          <div class="mb-0">
            <button class="btn btn-primary" data-repeater-create>
              <i class="mdi mdi-plus me-1"></i>
              <span class="align-middle">Add</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- /Form Repeater -->

</div>
@endsection
