@extends('layouts/layoutMaster')

@section('title', 'Wizard Icons - Forms')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/form-wizard-icons.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Form Wizard /</span> Icons
</h4>
<p class="mb-4">Icons used on this page are made by
  <a href="https://www.flaticon.com/authors/itim2101" title="itim2101" target="_blank">itim2101</a> from
  <a href="https://www.flaticon.com/" title="Flaticon" target="_blank">www.flaticon.com</a>
</p>
<!-- Default -->
<div class="row">
  <div class="col-12">
    <h5>Default</h5>
  </div>

  <!-- Default Icons Wizard -->
  <div class="col-12 mb-4">
    <small class="text-light fw-semibold">Basic Icons</small>
    <div class="bs-stepper wizard-icons wizard-icons-example mt-2">

      <div class="bs-stepper-header">
        <div class="step" data-target="#account-details">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-icon">
              <svg viewBox="0 0 54 54">
                <use xlink:href='{{asset('assets/svg/icons/form-wizard-account.svg#wizardAccount')}}'></use>
              </svg>
            </span>
            <span class="bs-stepper-label">Account Details</span>
          </button>
        </div>
        <div class="line">
          <i class="mdi mdi-chevron-right"></i>
        </div>
        <div class="step" data-target="#personal-info">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-icon">
              <svg viewBox="0 0 58 54">
                <use xlink:href='{{asset('assets/svg/icons/form-wizard-personal.svg#wizardPersonal')}}'></use>
              </svg>
            </span>
            <span class="bs-stepper-label">Personal Info</span>
          </button>
        </div>
        <div class="line">
          <i class="mdi mdi-chevron-right"></i>
        </div>
        <div class="step" data-target="#address">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-icon">
              <svg viewBox="0 0 54 54">
                <use xlink:href='{{asset('assets/svg/icons/form-wizard-address.svg#wizardAddress')}}'></use>
              </svg>
            </span>
            <span class="bs-stepper-label">Address</span>
          </button>
        </div>
        <div class="line">
          <i class="mdi mdi-chevron-right"></i>
        </div>
        <div class="step" data-target="#social-links">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-icon">
              <svg viewBox="0 0 54 54">
                <use xlink:href='{{asset('assets/svg/icons/form-wizard-social-link.svg#wizardSocialLink')}}'></use>
              </svg>
            </span>
            <span class="bs-stepper-label">Social Links</span>
          </button>
        </div>
        <div class="line">
          <i class="mdi mdi-chevron-right"></i>
        </div>
        <div class="step" data-target="#review-submit">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-icon">
              <svg viewBox="0 0 54 54">
                <use xlink:href='{{asset('assets/svg/icons/form-wizard-submit.svg#wizardSubmit')}}'></use>
              </svg>
            </span>
            <span class="bs-stepper-label">Review & Submit</span>
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
                <button class="btn btn-outline-secondary btn-prev" disabled> <i class="mdi mdi-arrow-left me-sm-1"></i>
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
                <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="mdi mdi-arrow-right"></i></button>
              </div>
            </div>
          </div>
          <!-- Address -->
          <div id="address" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Address</h6>
              <small>Enter Your Address.</small>
            </div>
            <div class="row g-4">
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" class="form-control" id="address-input" placeholder="98  Borough bridge Road, Birmingham">
                  <label for="address-input">Address</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" class="form-control" id="landmark" placeholder="Borough bridge">
                  <label for="landmark">Landmark</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" class="form-control" id="pincode" placeholder="658921">
                  <label for="pincode">Pincode</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" class="form-control" id="city" placeholder="Birmingham">
                  <label for="city">City</label>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1"></i>
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
                  <label for="linkedin">Linkedin</label>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="mdi mdi-arrow-right"></i></button>
              </div>
            </div>
          </div>
          <!-- Review -->
          <div id="review-submit" class="content">

            <p class="fw-semibold mb-2">Account</p>
            <ul class="list-unstyled">
              <li>Username</li>
              <li>exampl@email.com</li>
            </ul>
            <hr>
            <p class="fw-semibold mb-2">Personal Info</p>
            <ul class="list-unstyled">
              <li>First Name</li>
              <li>Last Name</li>
              <li>Country</li>
              <li>Language</li>
            </ul>
            <hr>
            <p class="fw-semibold mb-2">Address</p>
            <ul class="list-unstyled">
              <li>Address</li>
              <li>Landmark</li>
              <li>Pincode</li>
              <li>City</li>
            </ul>
            <hr>
            <p class="fw-semibold mb-2">Social Links</p>
            <ul class="list-unstyled">
              <li>https://twitter.com/abc</li>
              <li>https://facebook.com/abc</li>
              <li>https://plus.google.com/abc</li>
              <li>https://linkedin.com/abc</li>
            </ul>
            <div class="col-12 d-flex justify-content-between">
              <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1"></i>
                <span class="align-middle d-sm-inline-block d-none">Previous</span>
              </button>
              <button class="btn btn-primary btn-submit">Submit</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- /Default Icons Wizard -->

  <!-- Vertical Wizard -->
  <div class="col-12 mb-4">
    <small class="text-light fw-semibold">Vertical</small>
    <div class="bs-stepper wizard-vertical vertical wizard-vertical-icons-example wizard-vertical-icons mt-2">
      <div class="bs-stepper-header gap-lg-2">
        <div class="step" data-target="#account-details-1">
          <button type="button" class="step-trigger">
            <span class="avatar">
              <span class="avatar-initial rounded-2">
                <i class="mdi mdi-card-account-details-outline mdi-24px"></i>
              </span>
            </span>
            <span class="bs-stepper-label flex-column align-items-start gap-1 ms-2">
              <span class="bs-stepper-title">Account Details</span>
              <span class="bs-stepper-subtitle">Setup Account Details</span>
            </span>
          </button>
        </div>
        <div class="step" data-target="#personal-info-1">
          <button type="button" class="step-trigger">
            <span class="avatar">
              <span class="avatar-initial rounded-2">
                <i class="mdi mdi-account-outline mdi-24px"></i>
              </span>
            </span>
            <span class="bs-stepper-label flex-column align-items-start gap-1 ms-2">
              <span class="bs-stepper-title">Personal Info</span>
              <span class="bs-stepper-subtitle">Add personal info</span>
            </span>
          </button>
        </div>
        <div class="step" data-target="#social-links-1">
          <button type="button" class="step-trigger">
            <span class="avatar">
              <span class="avatar-initial rounded-2">
                <i class="mdi mdi-instagram mdi-24px"></i>
              </span>
            </span>
            <span class="bs-stepper-label flex-column align-items-start gap-1 ms-2">
              <span class="bs-stepper-title">Social Links</span>
              <span class="bs-stepper-subtitle">Add social links</span>
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

  <!-- Modern Icons Wizard -->
  <div class="col-12 mb-4">
    <small class="text-light fw-semibold">Modern Style Wizard With Icons</small>
    <div class="bs-stepper wizard-icons wizard-modern wizard-modern-icons-example mt-2">
      <div class="bs-stepper-header">
        <div class="step" data-target="#account-details-modern">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-icon">
              <svg viewBox="0 0 54 54">
                <use xlink:href='{{asset('assets/svg/icons/form-wizard-account.svg#wizardAccount')}}'></use>
              </svg>
            </span>
            <span class="bs-stepper-label">Account Details</span>
          </button>
        </div>
        <div class="line">
          <i class="mdi mdi-chevron-right"></i>
        </div>
        <div class="step" data-target="#personal-info-modern">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-icon">
              <svg viewBox="0 0 58 54">
                <use xlink:href='{{asset('assets/svg/icons/form-wizard-personal.svg#wizardPersonal')}}'></use>
              </svg>
            </span>
            <span class="bs-stepper-label">Personal Info</span>
          </button>
        </div>
        <div class="line">
          <i class="mdi mdi-chevron-right"></i>
        </div>
        <div class="step" data-target="#address-modern">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-icon">
              <svg viewBox="0 0 54 54">
                <use xlink:href='{{asset('assets/svg/icons/form-wizard-address.svg#wizardAddress')}}'></use>
              </svg>
            </span>
            <span class="bs-stepper-label">Address</span>
          </button>
        </div>
        <div class="line">
          <i class="mdi mdi-chevron-right"></i>
        </div>
        <div class="step" data-target="#social-links-modern">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-icon">
              <svg viewBox="0 0 54 54">
                <use xlink:href='{{asset('assets/svg/icons/form-wizard-social-link.svg#wizardSocialLink')}}'></use>
              </svg>
            </span>
            <span class="bs-stepper-label">Social Links</span>
          </button>
        </div>
        <div class="line">
          <i class="mdi mdi-chevron-right"></i>
        </div>
        <div class="step" data-target="#review-submit-modern">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-icon">
              <svg viewBox="0 0 54 54">
                <use xlink:href='{{asset('assets/svg/icons/form-wizard-submit.svg#wizardSubmit')}}'></use>
              </svg>
            </span>
            <span class="bs-stepper-label">Review & Submit</span>
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
                    <input type="password" id="confirm-password-modern" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="confirm-password2-modern" />
                    <label for="confirm-password-modern">Confirm Password</label>
                  </div>
                  <span class="input-group-text cursor-pointer" id="confirm-password2-modern"><i class="mdi mdi-eye-off-outline"></i></span>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-outline-secondary btn-prev" disabled> <i class="mdi mdi-arrow-left me-sm-1"></i>
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
                <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="mdi mdi-arrow-right"></i></button>
              </div>
            </div>
          </div>
          <!-- Address -->
          <div id="address-modern" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Address</h6>
              <small>Enter Your Address.</small>
            </div>
            <div class="row g-4">
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" class="form-control" id="address-modern-input" placeholder="98  Borough bridge Road, Birmingham">
                  <label for="address-modern-input">Address</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" class="form-control" id="landmark-modern" placeholder="Borough bridge">
                  <label for="landmark-modern">Landmark</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" class="form-control" id="pincode-modern" placeholder="658921">
                  <label for="pincode-modern">Pincode</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" class="form-control" id="city-modern" placeholder="Birmingham">
                  <label for="city-modern">City</label>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1"></i>
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
                  <label for="linkedin-modern">Linkedin</label>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="mdi mdi-arrow-right"></i></button>
              </div>
            </div>
          </div>
          <!-- Review -->
          <div id="review-submit-modern" class="content">
            <p class="fw-semibold mb-2">Account</p>
            <ul class="list-unstyled">
              <li>Username</li>
              <li>exampl@email.com</li>
            </ul>
            <hr>
            <p class="fw-semibold mb-2">Personal Info</p>
            <ul class="list-unstyled">
              <li>First Name</li>
              <li>Last Name</li>
              <li>Country</li>
              <li>Language</li>
            </ul>
            <hr>
            <p class="fw-semibold mb-2">Address</p>
            <ul class="list-unstyled">
              <li>Address</li>
              <li>Landmark</li>
              <li>Pincode</li>
              <li>City</li>
            </ul>
            <hr>
            <p class="fw-semibold mb-2">Social Links</p>
            <ul class="list-unstyled">
              <li>https://twitter.com/abc</li>
              <li>https://facebook.com/abc</li>
              <li>https://plus.google.com/abc</li>
              <li>https://linkedin.com/abc</li>
            </ul>
            <div class="col-12 d-flex justify-content-between">
              <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1"></i>
                <span class="align-middle d-sm-inline-block d-none">Previous</span>
              </button>
              <button class="btn btn-primary btn-submit">Submit</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- /Modern Icons Wizard -->


  <!-- Modern Vertical Icons Wizard -->
  <div class="col-12">
    <small class="text-light fw-semibold">Vertical Icons</small>
    <div class="bs-stepper vertical wizard-modern wizard-vertical-icons wizard-modern-vertical-icons-example mt-2">
      <div class="bs-stepper-header">
        <div class="step" data-target="#account-details-vertical-modern">
          <button type="button" class="step-trigger">
            <span class="avatar">
              <span class="avatar-initial rounded-2">
                <i class="mdi mdi-card-account-details-outline mdi-24px"></i>
              </span>
            </span>
            <span class="bs-stepper-label flex-column align-items-start gap-1 ms-2">
              <span class="bs-stepper-title">Account Details</span>
              <span class="bs-stepper-subtitle">Setup Account Details</span>
            </span>
          </button>
        </div>
        <div class="step" data-target="#personal-info-vertical-modern">
          <button type="button" class="step-trigger">
            <span class="avatar">
              <span class="avatar-initial rounded-2">
                <i class="mdi mdi-account-outline mdi-24px"></i>
              </span>
            </span>
            <span class="bs-stepper-label flex-column align-items-start gap-1 ms-2">
              <span class="bs-stepper-title">Personal Info</span>
              <span class="bs-stepper-subtitle">Add personal info</span>
            </span>
          </button>
        </div>
        <div class="step" data-target="#social-links-vertical-modern">
          <button type="button" class="step-trigger">
            <span class="avatar">
              <span class="avatar-initial rounded-2">
                <i class="mdi mdi-instagram mdi-24px"></i>
              </span>
            </span>
            <span class="bs-stepper-label flex-column align-items-start gap-1 ms-2">
              <span class="bs-stepper-title">Social Links</span>
              <span class="bs-stepper-subtitle">Add social links</span>
            </span>
          </button>
        </div>
      </div>
      <div class="bs-stepper-content">
        <form onSubmit="return false">
          <!-- Account Details -->
          <div id="account-details-vertical-modern" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Account Details</h6>
              <small>Enter Your Account Details.</small>
            </div>
            <div class="row g-3">
              <div class="col-sm-6">
                <label class="form-label" for="username-modern-vertical">Username</label>
                <input type="text" id="username-modern-vertical" class="form-control" placeholder="john.doe" />
              </div>
              <div class="col-sm-6">
                <label class="form-label" for="email-modern-vertical">Email</label>
                <input type="text" id="email-modern-vertical" class="form-control" placeholder="john.doe" aria-label="john.doe" />
              </div>
              <div class="col-sm-6 form-password-toggle">
                <label class="form-label" for="password-modern-vertical">Password</label>
                <div class="input-group input-group-merge">
                  <input type="password" id="password-modern-vertical" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password-modern-vertical1" />
                  <span class="input-group-text cursor-pointer" id="password-modern-vertical1"><i class="mdi mdi-eye-off-outline"></i></span>
                </div>
              </div>
              <div class="col-sm-6 form-password-toggle">
                <label class="form-label" for="confirm-password-modern-vertical2">Confirm Password</label>
                <div class="input-group input-group-merge">
                  <input type="password" id="confirm-password-modern-vertical2" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="confirm-password-modern-vertical3" />
                  <span class="input-group-text cursor-pointer" id="confirm-password-modern-vertical3"><i class="mdi mdi-eye-off-outline"></i></span>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-outline-secondary btn-prev" disabled> <i class="mdi mdi-arrow-left me-sm-1"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="mdi mdi-arrow-right"></i></button>
              </div>
            </div>
          </div>
          <!-- Personal Info -->
          <div id="personal-info-vertical-modern" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Personal Info</h6>
              <small>Enter Your Personal Info.</small>
            </div>
            <div class="row g-3">
              <div class="col-sm-6">
                <label class="form-label" for="first-name-modern-vertical">First Name</label>
                <input type="text" id="first-name-modern-vertical" class="form-control" placeholder="John" />
              </div>
              <div class="col-sm-6">
                <label class="form-label" for="last-name-modern-vertical">Last Name</label>
                <input type="text" id="last-name-modern-vertical" class="form-control" placeholder="Doe" />
              </div>
              <div class="col-sm-6">
                <label class="form-label" for="country-vertical-modern">Country</label>
                <select class="select2" id="country-vertical-modern">
                  <option>UK</option>
                  <option>USA</option>
                  <option>Spain</option>
                  <option>France</option>
                  <option>Italy</option>
                  <option>Australia</option>
                </select>
              </div>
              <div class="col-sm-6">
                <label class="form-label" for="language-vertical-modern">Language</label>
                <select class="selectpicker w-auto" id="language-vertical-modern" data-style="btn-default" data-icon-base="mdi" data-tick-icon="mdi-check text-white" multiple>
                  <option>English</option>
                  <option>French</option>
                  <option>Spanish</option>
                </select>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="mdi mdi-arrow-right"></i></button>
              </div>
            </div>
          </div>
          <!-- Social Links -->
          <div id="social-links-vertical-modern" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Social Links</h6>
              <small>Enter Your Social Links.</small>
            </div>
            <div class="row g-3">
              <div class="col-sm-6">
                <label class="form-label" for="twitter-vertical-modern">Twitter</label>
                <input type="text" id="twitter-vertical-modern" class="form-control" placeholder="https://twitter.com/abc" />
              </div>
              <div class="col-sm-6">
                <label class="form-label" for="facebook-vertical-modern">Facebook</label>
                <input type="text" id="facebook-vertical-modern" class="form-control" placeholder="https://facebook.com/abc" />
              </div>
              <div class="col-sm-6">
                <label class="form-label" for="google-vertical-modern">Google+</label>
                <input type="text" id="google-vertical-modern" class="form-control" placeholder="https://plus.google.com/abc" />
              </div>
              <div class="col-sm-6">
                <label class="form-label" for="linkedin-vertical-modern">Linkedin</label>
                <input type="text" id="linkedin-vertical-modern" class="form-control" placeholder="https://linkedin.com/abc" />
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-sm-1"></i>
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
  <!-- /Modern Vertical Icons Wizard -->
</div>
@endsection
