@php
$configData = Helper::appClasses();
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Multi Steps Sign-up - Pages')

@section('vendor-style')
<!-- Vendor -->
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
@endsection

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-auth.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/pages-auth-multisteps.js')}}"></script>
@endsection

@section('content')
<div class="authentication-wrapper authentication-cover">
  <!-- Logo -->
  <a href="{{url('/')}}" class="auth-cover-brand d-flex align-items-center gap-2">
    <span class="app-brand-logo demo">@include('_partials.macros',["width"=>25,"withbg"=>'#666cff'])</span>
    <span class="app-brand-text demo text-heading fw-bold">{{config('variables.templateName')}}</span>
  </a>
  <!-- /Logo -->
  <div class="authentication-inner row m-0">

    <!-- Left Text -->
    <div class="d-none d-lg-flex col-lg-4 align-items-center justify-content-center p-5">
      <img alt="register-multi-steps-illustration" src="{{asset('assets/img/illustrations/auth-register-multi-steps-illustration.png')}}" class="h-auto mh-100 w-px-200">
    </div>
    <!-- /Left Text -->

    <!--  Multi Steps Registration -->
    <div class="d-flex col-lg-8 align-items-center justify-content-center authentication-bg p-5">
      <div class="w-px-700">
        <div id="multiStepsValidation" class="bs-stepper wizard-numbered">
          <div class="bs-stepper-header border-bottom-0">
            <div class="step" data-target="#accountDetailsValidation">
              <button type="button" class="step-trigger">
                <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
                <span class="bs-stepper-label">
                  <span class="bs-stepper-number">01</span>
                  <span class="d-flex flex-column gap-1 ms-2">
                    <span class="bs-stepper-title">Account</span>
                    <span class="bs-stepper-subtitle">Account Details</span>
                  </span>
                </span>
              </button>
            </div>
            <div class="line"></div>
            <div class="step" data-target="#personalInfoValidation">
              <button type="button" class="step-trigger">
                <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
                <span class="bs-stepper-label">
                  <span class="bs-stepper-number">02</span>
                  <span class="d-flex flex-column gap-1 ms-2">
                    <span class="bs-stepper-title">Personal</span>
                    <span class="bs-stepper-subtitle">Enter Information</span>
                  </span>
                </span>
              </button>
            </div>
            <div class="line"></div>
            <div class="step" data-target="#billingLinksValidation">
              <button type="button" class="step-trigger">
                <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
                <span class="bs-stepper-label">
                  <span class="bs-stepper-number">03</span>
                  <span class="d-flex flex-column gap-1 ms-2">
                    <span class="bs-stepper-title">Billing</span>
                    <span class="bs-stepper-subtitle">Payment Details</span>
                  </span>
                </span>
              </button>
            </div>
          </div>
          <div class="bs-stepper-content">
            <form id="multiStepsForm" onSubmit="return false">
              <!-- Account Details -->
              <div id="accountDetailsValidation" class="content">
                <div class="content-header mb-3">
                  <h4 class="mb-0">Account Information</h4>
                  <small>Enter Your Account Details</small>
                </div>
                <div class="row g-3">
                  <div class="col-sm-6">
                    <div class="form-floating form-floating-outline">
                      <input type="text" name="multiStepsUsername" id="multiStepsUsername" class="form-control" placeholder="johndoe" />
                      <label for="multiStepsUsername">Username</label>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-floating form-floating-outline">
                      <input type="email" name="multiStepsEmail" id="multiStepsEmail" class="form-control" placeholder="john.doe@email.com" aria-label="john.doe" />
                      <label for="multiStepsEmail">Email</label>
                    </div>
                  </div>
                  <div class="col-sm-6 form-password-toggle">
                    <div class="input-group input-group-merge">
                      <div class="form-floating form-floating-outline">
                        <input type="password" id="multiStepsPass" name="multiStepsPass" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="multiStepsPass2" />
                        <label for="multiStepsPass">Password</label>
                      </div>
                      <span class="input-group-text cursor-pointer" id="multiStepsPass2"><i class="mdi mdi-eye-off-outline"></i></span>
                    </div>
                  </div>
                  <div class="col-sm-6 form-password-toggle">
                    <div class="input-group input-group-merge">
                      <div class="form-floating form-floating-outline">
                        <input type="password" id="multiStepsConfirmPass" name="multiStepsConfirmPass" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="multiStepsConfirmPass2" />
                        <label for="multiStepsConfirmPass">Confirm Password</label>
                      </div>
                      <span class="input-group-text cursor-pointer" id="multiStepsConfirmPass2"><i class="mdi mdi-eye-off-outline"></i></span>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-floating form-floating-outline">
                      <input type="text" name="multiStepsURL" id="multiStepsURL" class="form-control" placeholder="johndoe/profile" aria-label="johndoe" />
                      <label for="multiStepsURL">Profile Link</label>
                    </div>
                  </div>
                  <div class="col-12 d-flex justify-content-between">
                    <button class="btn btn-secondary btn-prev" disabled> <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                      <span class="align-middle d-sm-inline-block d-none">Previous</span>
                    </button>
                    <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1 me-0">Next</span> <i class="mdi mdi-arrow-right"></i></button>
                  </div>
                </div>
              </div>
              <!-- Personal Info -->
              <div id="personalInfoValidation" class="content">
                <div class="content-header mb-3">
                  <h4 class="mb-0">Personal Information</h4>
                  <small>Enter Your Personal Information</small>
                </div>
                <div class="row g-3">
                  <div class="col-sm-6">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="multiStepsFirstName" name="multiStepsFirstName" class="form-control" placeholder="John" />
                      <label for="multiStepsFirstName">First Name</label>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="multiStepsLastName" name="multiStepsLastName" class="form-control" placeholder="Doe" />
                      <label for="multiStepsLastName">Last Name</label>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="input-group input-group-merge">
                      <span class="input-group-text">US (+1)</span>
                      <div class="form-floating form-floating-outline">
                        <input type="text" id="multiStepsMobile" name="multiStepsMobile" class="form-control multi-steps-mobile" placeholder="202 555 0111" />
                        <label for="multiStepsMobile">Mobile</label>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="multiStepsPincode" name="multiStepsPincode" class="form-control multi-steps-pincode" placeholder="Postal Code" maxlength="6" />
                      <label for="multiStepsPincode">Pincode</label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="multiStepsAddress" name="multiStepsAddress" class="form-control" placeholder="Address" />
                      <label for="multiStepsAddress">Address</label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="multiStepsArea" name="multiStepsArea" class="form-control" placeholder="Area/Landmark" />
                      <label for="multiStepsArea">Landmark</label>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="multiStepsCity" class="form-control" placeholder="Jackson" />
                      <label for="multiStepsCity">City</label>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-floating form-floating-outline">
                      <select id="multiStepsState" class="select2 form-select" data-allow-clear="true">
                        <option value="">Select</option>
                        <option value="AL">Alabama</option>
                        <option value="AK">Alaska</option>
                        <option value="AZ">Arizona</option>
                        <option value="AR">Arkansas</option>
                        <option value="CA">California</option>
                        <option value="CO">Colorado</option>
                        <option value="CT">Connecticut</option>
                        <option value="DE">Delaware</option>
                        <option value="DC">District Of Columbia</option>
                        <option value="FL">Florida</option>
                        <option value="GA">Georgia</option>
                        <option value="HI">Hawaii</option>
                        <option value="ID">Idaho</option>
                        <option value="IL">Illinois</option>
                        <option value="IN">Indiana</option>
                        <option value="IA">Iowa</option>
                        <option value="KS">Kansas</option>
                        <option value="KY">Kentucky</option>
                        <option value="LA">Louisiana</option>
                        <option value="ME">Maine</option>
                        <option value="MD">Maryland</option>
                        <option value="MA">Massachusetts</option>
                        <option value="MI">Michigan</option>
                        <option value="MN">Minnesota</option>
                        <option value="MS">Mississippi</option>
                        <option value="MO">Missouri</option>
                        <option value="MT">Montana</option>
                        <option value="NE">Nebraska</option>
                        <option value="NV">Nevada</option>
                        <option value="NH">New Hampshire</option>
                        <option value="NJ">New Jersey</option>
                        <option value="NM">New Mexico</option>
                        <option value="NY">New York</option>
                        <option value="NC">North Carolina</option>
                        <option value="ND">North Dakota</option>
                        <option value="OH">Ohio</option>
                        <option value="OK">Oklahoma</option>
                        <option value="OR">Oregon</option>
                        <option value="PA">Pennsylvania</option>
                        <option value="RI">Rhode Island</option>
                        <option value="SC">South Carolina</option>
                        <option value="SD">South Dakota</option>
                        <option value="TN">Tennessee</option>
                        <option value="TX">Texas</option>
                        <option value="UT">Utah</option>
                        <option value="VT">Vermont</option>
                        <option value="VA">Virginia</option>
                        <option value="WA">Washington</option>
                        <option value="WV">West Virginia</option>
                        <option value="WI">Wisconsin</option>
                        <option value="WY">Wyoming</option>
                      </select>
                      <label for="multiStepsState">State</label>
                    </div>
                  </div>
                  <div class="col-12 d-flex justify-content-between">
                    <button class="btn btn-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                      <span class="align-middle d-sm-inline-block d-none">Previous</span>
                    </button>
                    <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1 me-0">Next</span> <i class="mdi mdi-arrow-right"></i></button>
                  </div>
                </div>
              </div>
              <!-- Billing Links -->
              <div id="billingLinksValidation" class="content">
                <div class="content-header mb-3">
                  <h4 class="mb-0">Select Plan</h4>
                  <small>Select plan as per your requirement</small>
                </div>
                <!-- Custom plan options -->
                <div class="row gap-md-0 gap-3 mb-4">
                  <div class="col-md">
                    <div class="form-check custom-option custom-option-icon">
                      <label class="form-check-label custom-option-content" for="basicOption">
                        <span class="custom-option-body">
                          <span class="fs-4 d-block fw-medium text-heading">Basic</span>
                          <small>A simple start for start ups & Students</small>
                          <span class="d-flex justify-content-center py-2">
                            <sup class="text-primary fs-6 lh-1 mt-2">$</sup>
                            <span class="fw-medium display-5 text-primary">0</span>
                            <sub class="lh-1 fs-big mt-auto mb-2 text-muted">/month</sub>
                          </span>
                        </span>
                        <input name="customRadioIcon" class="form-check-input" type="radio" value="" id="basicOption" />
                      </label>
                    </div>
                  </div>
                  <div class="col-md">
                    <div class="form-check custom-option custom-option-icon">
                      <label class="form-check-label custom-option-content" for="standardOption">
                        <span class="custom-option-body">
                          <span class="fs-4 d-block fw-medium text-heading">Standard</span>
                          <small>For small to medium businesses</small>
                          <span class="d-flex justify-content-center py-2">
                            <sup class="text-primary fs-6 lh-1 mt-2">$</sup>
                            <span class="fw-medium display-5 text-primary">99</span>
                            <sub class="lh-1 fs-big mt-auto mb-2 text-muted">/month</sub>
                          </span>
                        </span>
                        <input name="customRadioIcon" class="form-check-input" type="radio" value="" id="standardOption" checked />
                      </label>
                    </div>
                  </div>
                  <div class="col-md">
                    <div class="form-check custom-option custom-option-icon">
                      <label class="form-check-label custom-option-content" for="enterpriseOption">
                        <span class="custom-option-body">
                          <span class="fs-4 d-block fw-medium text-heading">Enterprise</span>
                          <small>Solution for enterprise & organizations</small>
                          <span class="d-flex justify-content-center py-2">
                            <sup class="text-primary fs-6 lh-1 mt-2">$</sup>
                            <span class="fw-medium display-5 text-primary">499</span>
                            <sub class="lh-1 fs-big mt-auto mb-2 text-muted">/year</sub>
                          </span>
                        </span>
                        <input name="customRadioIcon" class="form-check-input" type="radio" value="" id="enterpriseOption" />
                      </label>
                    </div>
                  </div>
                </div>
                <!--/ Custom plan options -->
                <div class="content-header mb-3">
                  <h4 class="mb-0">Payment Information</h4>
                  <small>Enter your card information</small>
                </div>
                <!-- Credit Card Details -->
                <div class="row g-3">
                  <div class="col-md-12">
                    <div class="input-group input-group-merge">
                      <div class="form-floating form-floating-outline">
                        <input id="multiStepsCard" class="form-control multi-steps-card" name="multiStepsCard" type="text" placeholder="1356 3215 6548 7898" aria-describedby="multiStepsCardImg" />
                        <label for="multiStepsCard">Card Number</label>
                      </div>
                      <span class="input-group-text cursor-pointer" id="multiStepsCardImg"><span class="card-type"></span></span>
                    </div>
                  </div>
                  <div class="col-md-5">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="multiStepsName" class="form-control" name="multiStepsName" placeholder="John Doe" />
                      <label for="multiStepsName">Name On Card</label>
                    </div>
                  </div>
                  <div class="col-6 col-md-4">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="multiStepsExDate" class="form-control multi-steps-exp-date" name="multiStepsExDate" placeholder="MM/YY" />
                      <label for="multiStepsExDate">Expiry Date</label>
                    </div>
                  </div>
                  <div class="col-6 col-md-3">
                    <div class="input-group input-group-merge">
                      <div class="form-floating form-floating-outline">
                        <input type="text" id="multiStepsCvv" class="form-control multi-steps-cvv" name="multiStepsCvv" maxlength="3" placeholder="654" />
                        <label for="multiStepsCvv">CVV Code</label>
                      </div>
                      <span class="input-group-text cursor-pointer" id="multiStepsCvvHelp"><i class="mdi mdi-help-circle-outline text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Card Verification Value"></i></span>
                    </div>
                  </div>
                  <div class="col-12 d-flex justify-content-between">
                    <button class="btn btn-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                      <span class="align-middle d-sm-inline-block d-none">Previous</span>
                    </button>
                    <button type="submit" class="btn btn-primary btn-next btn-submit">Submit</button>
                  </div>
                </div>
                <!--/ Credit Card Details -->
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- / Multi Steps Registration -->
  </div>
</div>

<script>
  // Check selected custom option
  window.Helpers.initCustomOptionCheck();

</script>
@endsection
