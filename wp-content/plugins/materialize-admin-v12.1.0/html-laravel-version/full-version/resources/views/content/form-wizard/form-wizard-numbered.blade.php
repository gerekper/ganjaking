@extends('layouts/layoutMaster')

@section('title', 'Wizard Numbered - Forms')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/form-wizard-numbered.js')}}"></script>
<script src="{{asset('assets/js/form-wizard-validation.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Form Wizard/</span> Numbered
</h4>
<!-- Default -->
<div class="row">
  <div class="col-12">
    <h5>Default</h5>
  </div>

  <!-- Default Wizard -->
  <div class="col-12 mb-4">
    <small class="text-light fw-semibold">Basic</small>
    <div class="bs-stepper wizard-numbered mt-2">
      <div class="bs-stepper-header">
        <div class="step" data-target="#account-details">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
            <span class="bs-stepper-label">
              <span class="bs-stepper-number">01</span>
              <span class="d-flex flex-column gap-1 ms-2">
                <span class="bs-stepper-title">Account Details</span>
                <span class="bs-stepper-subtitle">Setup Account Details</span>
              </span>
            </span>
          </button>
        </div>
        <div class="line"></div>
        <div class="step" data-target="#personal-info">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
            <span class="bs-stepper-label">
              <span class="bs-stepper-number">02</span>
              <span class="d-flex flex-column gap-1 ms-2">
                <span class="bs-stepper-title">Personal Info</span>
                <span class="bs-stepper-subtitle">Add personal info</span>
              </span>
            </span>
          </button>
        </div>
        <div class="line"></div>
        <div class="step" data-target="#social-links">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
            <span class="bs-stepper-label">
              <span class="bs-stepper-number">03</span>
              <span class="d-flex flex-column gap-1 ms-2">
                <span class="bs-stepper-title">Social Links</span>
                <span class="bs-stepper-subtitle">Add social links</span>
              </span>
            </span>
          </button>
        </div>
      </div>
      <div class="bs-stepper-content">
        <form onSubmit="return false">
          <!-- Account Details -->
          <div id="account-details" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Account Details</h6>
              <small>Enter Your Account Details.</small>
            </div>
            <div class="row g-4">
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="username" class="form-control" placeholder="johndoe" />
                  <label for="username">Username</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="email" id="email" class="form-control" placeholder="john.doe@email.com" aria-label="john.doe" />
                  <label for="email">Email</label>
                </div>
              </div>
              <div class="col-sm-6 form-password-toggle">
                <div class="input-group input-group-merge">
                  <div class="form-floating form-floating-outline">
                    <input type="password" id="password" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password2" />
                    <label for="password">Password</label>
                  </div>
                  <span class="input-group-text cursor-pointer" id="password2"><i class="mdi mdi-eye-off-outline"></i></span>
                </div>
              </div>
              <div class="col-sm-6 form-password-toggle">
                <div class="input-group input-group-merge">
                  <div class="form-floating form-floating-outline">
                    <input type="password" id="confirm-password" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="confirm-password2" />
                    <label for="confirm-password">Confirm Password</label>
                  </div>
                  <span class="input-group-text cursor-pointer" id="confirm-password2"><i class="mdi mdi-eye-off-outline"></i></span>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-outline-secondary btn-prev" disabled> <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="mdi mdi-arrow-right"></i></button>
              </div>
            </div>
          </div>
          <!-- Personal Info -->
          <div id="personal-info" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Personal Info</h6>
              <small>Enter Your Personal Info.</small>
            </div>
            <div class="row g-4">
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="first-name" class="form-control" placeholder="John" />
                  <label for="first-name">First Name</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="last-name" class="form-control" placeholder="Doe" />
                  <label for="last-name">Last Name</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <select class="select2" id="country">
                    <option label=" "></option>
                    <option>UK</option>
                    <option>USA</option>
                    <option>Spain</option>
                    <option>France</option>
                    <option>Italy</option>
                    <option>Australia</option>
                  </select>
                  <label for="country">Country</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <select class="selectpicker w-auto" id="language" data-style="btn-transparent" data-icon-base="mdi" data-tick-icon="mdi-check text-white" multiple>
                    <option>English</option>
                    <option>French</option>
                    <option>Spanish</option>
                  </select>
                  <label for="language">Language</label>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="mdi mdi-arrow-right"></i></button>
              </div>
            </div>
          </div>
          <!-- Social Links -->
          <div id="social-links" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Social Links</h6>
              <small>Enter Your Social Links.</small>
            </div>
            <div class="row g-4">
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="twitter" class="form-control" placeholder="https://twitter.com/abc" />
                  <label for="twitter">Twitter</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="facebook" class="form-control" placeholder="https://facebook.com/abc" />
                  <label for="facebook">Facebook</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="google" class="form-control" placeholder="https://plus.google.com/abc" />
                  <label for="google">Google+</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="linkedin" class="form-control" placeholder="https://linkedin.com/abc" />
                  <label for="linkedin">LinkedIn</label>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-submit">Submit</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- /Default Wizard -->

  <!-- Validation Wizard -->
  <div class="col-12 mb-4">
    <small class="text-light fw-semibold">Validation</small>
    <div id="wizard-validation" class="bs-stepper mt-2">
      <div class="bs-stepper-header">
        <div class="step" data-target="#account-details-validation">
          <button type="button" class="step-trigger flex-lg-wrap gap-lg-2 px-lg-0">
            <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
            <span class="bs-stepper-label ms-lg-0">
              <span class="d-flex flex-column gap-1 text-lg-center">
                <span class="bs-stepper-title">Account Details</span>
                <span class="bs-stepper-subtitle">Setup Account Details</span>
              </span>
            </span>
          </button>
        </div>
        <div class="line mt-lg-n4 mb-lg-3"></div>
        <div class="step" data-target="#personal-info-validation">
          <button type="button" class="step-trigger flex-lg-wrap gap-lg-2 px-lg-0">
            <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
            <span class="bs-stepper-label ms-lg-0">
              <span class="d-flex flex-column gap-1 text-lg-center">
                <span class="bs-stepper-title">Personal Info</span>
                <span class="bs-stepper-subtitle">Add personal info</span>
              </span>
            </span>
          </button>
        </div>
        <div class="line mt-lg-n4 mb-lg-3"></div>
        <div class="step" data-target="#social-links-validation">
          <button type="button" class="step-trigger flex-lg-wrap gap-lg-2 px-lg-0">
            <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
            <span class="bs-stepper-label ms-lg-0">
              <span class="d-flex flex-column gap-1 text-lg-center">
                <span class="bs-stepper-title">Social Links</span>
                <span class="bs-stepper-subtitle">Add social links</span>
              </span>
            </span>
          </button>
        </div>
      </div>
      <div class="bs-stepper-content">
        <form id="wizard-validation-form" onSubmit="return false">
          <!-- Account Details -->
          <div id="account-details-validation" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Account Details</h6>
              <small>Enter Your Account Details.</small>
            </div>
            <div class="row g-4">
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" name="formValidationUsername" id="formValidationUsername" class="form-control" placeholder="johndoe" />
                  <label for="formValidationUsername">Username</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="email" name="formValidationEmail" id="formValidationEmail" class="form-control" placeholder="john.doe@email.com" aria-label="john.doe" />
                  <label for="formValidationEmail">Email</label>
                </div>
              </div>
              <div class="col-sm-6 form-password-toggle">
                <div class="input-group input-group-merge">
                  <div class="form-floating form-floating-outline">
                    <input type="password" id="formValidationPass" name="formValidationPass" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="formValidationPass2" />
                    <label for="formValidationPass">Password</label>
                  </div>
                  <span class="input-group-text cursor-pointer" id="formValidationPass2"><i class="mdi mdi-eye-off-outline"></i></span>
                </div>
              </div>
              <div class="col-sm-6 form-password-toggle">
                <div class="input-group input-group-merge">
                  <div class="form-floating form-floating-outline">
                    <input type="password" id="formValidationConfirmPass" name="formValidationConfirmPass" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="formValidationConfirmPass2" />
                    <label for="formValidationConfirmPass">Confirm Password</label>
                  </div>
                  <span class="input-group-text cursor-pointer" id="formValidationConfirmPass2"><i class="mdi mdi-eye-off-outline"></i></span>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-outline-secondary btn-prev" disabled> <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="mdi mdi-arrow-right"></i></button>
              </div>
            </div>
          </div>
          <!-- Personal Info -->
          <div id="personal-info-validation" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Personal Info</h6>
              <small>Enter Your Personal Info.</small>
            </div>
            <div class="row g-4">
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="formValidationFirstName" name="formValidationFirstName" class="form-control" placeholder="John" />
                  <label for="formValidationFirstName">First Name</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="formValidationLastName" name="formValidationLastName" class="form-control" placeholder="Doe" />
                  <label for="formValidationLastName">Last Name</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <select class="select2" id="formValidationCountry" name="formValidationCountry">
                    <option label=" "></option>
                    <option>UK</option>
                    <option>USA</option>
                    <option>Spain</option>
                    <option>France</option>
                    <option>Italy</option>
                    <option>Australia</option>
                  </select>
                  <label for="formValidationCountry">Country</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <select class="selectpicker w-auto" id="formValidationLanguage" data-style="btn-transparent" data-icon-base="mdi" data-tick-icon="mdi-check text-white" name="formValidationLanguage" multiple>
                    <option>English</option>
                    <option>French</option>
                    <option>Spanish</option>
                  </select>
                  <label for="formValidationLanguage">Language</label>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="mdi mdi-arrow-right"></i></button>
              </div>
            </div>
          </div>
          <!-- Social Links -->
          <div id="social-links-validation" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Social Links</h6>
              <small>Enter Your Social Links.</small>
            </div>
            <div class="row g-4">
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" name="formValidationTwitter" id="formValidationTwitter" class="form-control" placeholder="https://twitter.com/abc" />
                  <label for="formValidationTwitter">Twitter</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" name="formValidationFacebook" id="formValidationFacebook" class="form-control" placeholder="https://facebook.com/abc" />
                  <label for="formValidationFacebook">Facebook</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" name="formValidationGoogle" id="formValidationGoogle" class="form-control" placeholder="https://plus.google.com/abc" />
                  <label for="formValidationGoogle">Google+</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" name="formValidationLinkedIn" id="formValidationLinkedIn" class="form-control" placeholder="https://linkedin.com/abc" />
                  <label for="formValidationLinkedIn">LinkedIn</label>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-next btn-submit">Submit</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- /Validation Wizard -->

  <!-- Vertical Wizard -->
  <div class="col-12 mb-4">
    <small class="text-light fw-semibold">Vertical</small>
    <div class="bs-stepper wizard-vertical vertical mt-2">
      <div class="bs-stepper-header gap-lg-2">
        <div class="step" data-target="#account-details-1">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
            <span class="bs-stepper-label">
              <span class="bs-stepper-number">01</span>
              <span class="d-flex flex-column gap-1 ms-2">
                <span class="bs-stepper-title">Account Details</span>
                <span class="bs-stepper-subtitle">Setup Account Details</span>
              </span>
            </span>
          </button>
        </div>
        <div class="line"></div>
        <div class="step" data-target="#personal-info-1">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
            <span class="bs-stepper-label">
              <span class="bs-stepper-number">02</span>
              <span class="d-flex flex-column gap-1 ms-2">
                <span class="bs-stepper-title">Personal Info</span>
                <span class="bs-stepper-subtitle">Add personal info</span>
              </span>
            </span>
          </button>
        </div>
        <div class="line"></div>
        <div class="step" data-target="#social-links-1">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
            <span class="bs-stepper-label">
              <span class="bs-stepper-number">03</span>
              <span class="d-flex flex-column gap-1 ms-2">
                <span class="bs-stepper-title">Social Links</span>
                <span class="bs-stepper-subtitle">Add social links</span>
              </span>
            </span>
          </button>
        </div>
      </div>
      <div class="bs-stepper-content">
        <form onSubmit="return false">
          <!-- Account Details -->
          <div id="account-details-1" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Account Details</h6>
              <small>Enter Your Account Details.</small>
            </div>
            <div class="row g-4">
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="username-vertical" class="form-control" placeholder="johndoe" />
                  <label for="username-vertical">Username</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="email" id="email-vertical" class="form-control" placeholder="john.doe@email.com" aria-label="john.doe" />
                  <label for="email-vertical">Email</label>
                </div>
              </div>
              <div class="col-sm-6 form-password-toggle">
                <div class="input-group input-group-merge">
                  <div class="form-floating form-floating-outline">
                    <input type="password" id="password-vertical" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password2-vertical" />
                    <label for="password-vertical">Password</label>
                  </div>
                  <span class="input-group-text cursor-pointer" id="password2-vertical"><i class="mdi mdi-eye-off-outline"></i></span>
                </div>
              </div>
              <div class="col-sm-6 form-password-toggle">
                <div class="input-group input-group-merge">
                  <div class="form-floating form-floating-outline">
                    <input type="password" id="confirm-password-vertical" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="confirm-password-vertical2" />
                    <label for="confirm-password-vertical">Confirm Password</label>
                  </div>
                  <span class="input-group-text cursor-pointer" id="confirm-password-vertical2"><i class="mdi mdi-eye-off-outline"></i></span>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-outline-secondary btn-prev" disabled> <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="mdi mdi-arrow-right"></i></button>
              </div>
            </div>
          </div>
          <!-- Personal Info -->
          <div id="personal-info-1" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Personal Info</h6>
              <small>Enter Your Personal Info.</small>
            </div>
            <div class="row g-4">
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="first-name-vertical" class="form-control" placeholder="John" />
                  <label for="first-name-vertical">First Name</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="last-name-vertical" class="form-control" placeholder="Doe" />
                  <label for="last-name-vertical">Last Name</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <select class="select2" id="country-vertical">
                    <option label=" "></option>
                    <option>UK</option>
                    <option>USA</option>
                    <option>Spain</option>
                    <option>France</option>
                    <option>Italy</option>
                    <option>Australia</option>
                  </select>
                  <label for="country-vertical">Country</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <select class="selectpicker w-auto" id="language-vertical" data-style="btn-transparent" data-icon-base="mdi" data-tick-icon="mdi-check text-white" multiple>
                    <option>English</option>
                    <option>French</option>
                    <option>Spanish</option>
                  </select>
                  <label for="language-vertical">Language</label>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="mdi mdi-arrow-right"></i></button>
              </div>
            </div>
          </div>
          <!-- Social Links -->
          <div id="social-links-1" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Social Links</h6>
              <small>Enter Your Social Links.</small>
            </div>
            <div class="row g-4">
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="twitter-vertical" class="form-control" placeholder="https://twitter.com/abc" />
                  <label for="twitter-vertical">Twitter</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="facebook-vertical" class="form-control" placeholder="https://facebook.com/abc" />
                  <label for="facebook-vertical">Facebook</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="google-vertical" class="form-control" placeholder="https://plus.google.com/abc" />
                  <label for="google-vertical">Google+</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="linkedin-vertical" class="form-control" placeholder="https://linkedin.com/abc" />
                  <label for="linkedin-vertical">LinkedIn</label>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-submit">Submit</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- /Vertical Wizard -->
</div>
<hr class="container-m-nx mb-5">

<!-- Modern -->
<div class="row">
  <div class="col-12">
    <h5>Modern</h5>
  </div>

  <!-- Modern Wizard -->
  <div class="col-12 mb-4">
    <small class="text-light fw-semibold mt-2">Basic</small>
    <div class="bs-stepper wizard-modern wizard-modern-example mt-2">
      <div class="bs-stepper-header">
        <div class="step" data-target="#account-details-modern">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
            <span class="bs-stepper-label">
              <span class="bs-stepper-number">01</span>
              <span class="d-flex flex-column gap-1 ms-2">
                <span class="bs-stepper-title">Account Details</span>
                <span class="bs-stepper-subtitle">Setup Account Details</span>
              </span>
            </span>
          </button>
        </div>
        <div class="line"></div>
        <div class="step" data-target="#personal-info-modern">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
            <span class="bs-stepper-label">
              <span class="bs-stepper-number">02</span>
              <span class="d-flex flex-column gap-1 ms-2">
                <span class="bs-stepper-title">Personal Info</span>
                <span class="bs-stepper-subtitle">Add personal info</span>
              </span>
            </span>
          </button>
        </div>
        <div class="line"></div>
        <div class="step" data-target="#social-links-modern">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
            <span class="bs-stepper-label">
              <span class="bs-stepper-number">03</span>
              <span class="d-flex flex-column gap-1 ms-2">
                <span class="bs-stepper-title">Social Links</span>
                <span class="bs-stepper-subtitle">Add social links</span>
              </span>
            </span>
          </button>
        </div>
      </div>
      <div class="bs-stepper-content">
        <form onSubmit="return false">
          <!-- Account Details -->
          <div id="account-details-modern" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Account Details</h6>
              <small>Enter Your Account Details.</small>
            </div>
            <div class="row g-4">
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="username-modern" class="form-control" placeholder="johndoe" />
                  <label for="username-modern">Username</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="email" id="email-modern" class="form-control" placeholder="john.doe@email.com" aria-label="john.doe" />
                  <label for="email-modern">Email</label>
                </div>
              </div>
              <div class="col-sm-6 form-password-toggle">
                <div class="input-group input-group-merge">
                  <div class="form-floating form-floating-outline">
                    <input type="password" id="password-modern" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password2-modern" />
                    <label for="password-modern">Password</label>
                  </div>
                  <span class="input-group-text cursor-pointer" id="password2-modern"><i class="mdi mdi-eye-off-outline"></i></span>
                </div>
              </div>
              <div class="col-sm-6 form-password-toggle">
                <div class="input-group input-group-merge">
                  <div class="form-floating form-floating-outline">
                    <input type="password" id="confirm-password-modern" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="confirm-password-modern2" />
                    <label for="confirm-password-modern">Confirm Password</label>
                  </div>
                  <span class="input-group-text cursor-pointer" id="confirm-password-modern2"><i class="mdi mdi-eye-off-outline"></i></span>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-outline-secondary btn-prev" disabled> <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="mdi mdi-arrow-right"></i></button>
              </div>
            </div>
          </div>
          <!-- Personal Info -->
          <div id="personal-info-modern" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Personal Info</h6>
              <small>Enter Your Personal Info.</small>
            </div>
            <div class="row g-4">
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="first-name-modern" class="form-control" placeholder="John" />
                  <label for="first-name-modern">First Name</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="last-name-modern" class="form-control" placeholder="Doe" />
                  <label for="last-name-modern">Last Name</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <select class="select2" id="country-modern">
                    <option label=" "></option>
                    <option>UK</option>
                    <option>USA</option>
                    <option>Spain</option>
                    <option>France</option>
                    <option>Italy</option>
                    <option>Australia</option>
                  </select>
                  <label for="country-modern">Country</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <select class="selectpicker w-auto" id="language-modern" data-style="btn-transparent" data-icon-base="mdi" data-tick-icon="mdi-check text-white" multiple>
                    <option>English</option>
                    <option>French</option>
                    <option>Spanish</option>
                  </select>
                  <label for="language-modern">Language</label>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="mdi mdi-arrow-right"></i></button>
              </div>
            </div>
          </div>
          <!-- Social Links -->
          <div id="social-links-modern" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Social Links</h6>
              <small>Enter Your Social Links.</small>
            </div>
            <div class="row g-4">
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="twitter-modern" class="form-control" placeholder="https://twitter.com/abc" />
                  <label for="twitter-modern">Twitter</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="facebook-modern" class="form-control" placeholder="https://facebook.com/abc" />
                  <label for="facebook-modern">Facebook</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="google-modern" class="form-control" placeholder="https://plus.google.com/abc" />
                  <label for="google-modern">Google+</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="linkedin-modern" class="form-control" placeholder="https://linkedin.com/abc" />
                  <label for="linkedin-modern">LinkedIn</label>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-submit">Submit</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- /Modern Wizard -->

  <!-- Modern Vertical Wizard -->
  <div class="col-12">
    <small class="text-light fw-semibold">Vertical</small>
    <div class="bs-stepper vertical wizard-modern wizard-modern-vertical mt-2">
      <div class="bs-stepper-header gap-lg-2">
        <div class="step" data-target="#account-details-modern-vertical">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
            <span class="bs-stepper-label">
              <span class="bs-stepper-number">01</span>
              <span class="d-flex flex-column gap-1 ms-2">
                <span class="bs-stepper-title">Account Details</span>
                <span class="bs-stepper-subtitle">Setup Account Details</span>
              </span>
            </span>
          </button>
        </div>
        <div class="line"></div>
        <div class="step" data-target="#personal-info-modern-vertical">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
            <span class="bs-stepper-label">
              <span class="bs-stepper-number">02</span>
              <span class="d-flex flex-column gap-1 ms-2">
                <span class="bs-stepper-title">Personal Info</span>
                <span class="bs-stepper-subtitle">Add personal info</span>
              </span>
            </span>
          </button>
        </div>
        <div class="line"></div>
        <div class="step" data-target="#social-links-modern-vertical">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
            <span class="bs-stepper-label">
              <span class="bs-stepper-number">03</span>
              <span class="d-flex flex-column gap-1 ms-2">
                <span class="bs-stepper-title">Social Links</span>
                <span class="bs-stepper-subtitle">Add social links</span>
              </span>
            </span>
          </button>
        </div>
      </div>
      <div class="bs-stepper-content">
        <form onSubmit="return false">
          <!-- Account Details -->
          <div id="account-details-modern-vertical" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Account Details</h6>
              <small>Enter Your Account Details.</small>
            </div>
            <div class="row g-4">
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="username-modern-vertical" class="form-control" placeholder="johndoe" />
                  <label for="username-modern-vertical">Username</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="email" id="email-modern-vertical" class="form-control" placeholder="john.doe@email.com" aria-label="john.doe" />
                  <label for="email-modern-vertical">Email</label>
                </div>
              </div>
              <div class="col-sm-6 form-password-toggle">
                <div class="input-group input-group-merge">
                  <div class="form-floating form-floating-outline">
                    <input type="password" id="password-modern-vertical" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password2-modern-vertical" />
                    <label for="password-modern-vertical">Password</label>
                  </div>
                  <span class="input-group-text cursor-pointer" id="password2-modern-vertical"><i class="mdi mdi-eye-off-outline"></i></span>
                </div>
              </div>
              <div class="col-sm-6 form-password-toggle">
                <div class="input-group input-group-merge">
                  <div class="form-floating form-floating-outline">
                    <input type="password" id="confirm-password-modern-vertical" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="confirm-password-modern-vertical2" />
                    <label for="confirm-password-modern-vertical">Confirm Password</label>
                  </div>
                  <span class="input-group-text cursor-pointer" id="confirm-password-modern-vertical2"><i class="mdi mdi-eye-off-outline"></i></span>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-outline-secondary btn-prev" disabled> <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="mdi mdi-arrow-right"></i></button>
              </div>
            </div>
          </div>
          <!-- Personal Info -->
          <div id="personal-info-modern-vertical" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Personal Info</h6>
              <small>Enter Your Personal Info.</small>
            </div>
            <div class="row g-4">
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="first-name-modern-vertical" class="form-control" placeholder="John" />
                  <label for="first-name-modern-vertical">First Name</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="last-name-modern-vertical" class="form-control" placeholder="Doe" />
                  <label for="last-name-modern-vertical">Last Name</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <select class="select2" id="country-modern-vertical">
                    <option label=" "></option>
                    <option>UK</option>
                    <option>USA</option>
                    <option>Spain</option>
                    <option>France</option>
                    <option>Italy</option>
                    <option>Australia</option>
                  </select>
                  <label for="country-modern-vertical">Country</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <select class="selectpicker w-auto" id="language-modern-vertical" data-style="btn-transparent" data-icon-base="mdi" data-tick-icon="mdi-check text-white" multiple>
                    <option>English</option>
                    <option>French</option>
                    <option>Spanish</option>
                  </select>
                  <label for="language-modern-vertical">Language</label>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="mdi mdi-arrow-right"></i></button>
              </div>
            </div>
          </div>
          <!-- Social Links -->
          <div id="social-links-modern-vertical" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Social Links</h6>
              <small>Enter Your Social Links.</small>
            </div>
            <div class="row g-4">
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="twitter-modern-vertical" class="form-control" placeholder="https://twitter.com/abc" />
                  <label for="twitter-modern-vertical">Twitter</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="facebook-modern-vertical" class="form-control" placeholder="https://facebook.com/abc" />
                  <label for="facebook-modern-vertical">Facebook</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="google-modern-vertical" class="form-control" placeholder="https://plus.google.com/abc" />
                  <label for="google-modern-vertical">Google+</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="linkedin-modern-vertical" class="form-control" placeholder="https://linkedin.com/abc" />
                  <label for="linkedin-modern-vertical">LinkedIn</label>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-submit">Submit</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- /Modern Vertical Wizard -->
</div>
@endsection
