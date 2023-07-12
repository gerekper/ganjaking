@php
$pricingModal = true;
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Modals - UI elements')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/modal-add-new-cc.js')}}"></script>
<script src="{{asset('assets/js/modal-add-new-address.js')}}"></script>
<script src="{{asset('assets/js/modal-create-app.js')}}"></script>
<script src="{{asset('assets/js/modal-edit-user.js')}}"></script>
<script src="{{asset('assets/js/modal-enable-otp.js')}}"></script>
<script src="{{asset('assets/js/modal-share-project.js')}}"></script>
<script src="{{asset('assets/js/modal-two-factor-auth.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">Modal Examples</h4>

<div class="row">
  <!--  Pricing -->
  <div class="col-12 col-sm-6 col-lg-4 mb-4">
    <div class="card">
      <div class="card-body text-center">
        <i class='mdi mdi-36px mdi-currency-usd'></i>
        <h5 class="mt-3 fw-medium">Pricing</h5>
        <p> Elegant pricing options modal popup example, easy to use in any page.</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pricingModal"> Show </button>
      </div>
    </div>
  </div>
  <!--/  Pricing -->

  <!--  Add New Credit Card -->
  <div class="col-12 col-sm-6 col-lg-4 mb-4">
    <div class="card">
      <div class="card-body text-center">
        <i class='mdi mdi-36px mdi mdi-credit-card-outline'></i>
        <h5 class="mt-3 fw-medium">Add New Credit Card</h5>
        <p> Quickly collect the credit card details, built in input mask and form validation support.</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewCCModal"> Show </button>
      </div>
    </div>
  </div>
  <!--/  Add New Credit Card -->

  <!--  Add New Address -->
  <div class="col-12 col-sm-6 col-lg-4 mb-4">
    <div class="card">
      <div class="card-body text-center">
        <i class='mdi mdi-36px mdi-home-outline'></i>
        <h5 class="mt-3 fw-medium">Add New Address</h5>
        <p> Ready to use form to collect user address data with validation and custom input support.</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewAddress"> Show </button>
      </div>
    </div>
  </div>
  <!--/  Add New Address -->

  <!--  Refer & Earn -->
  <div class="col-12 col-sm-6 col-lg-4 mb-4">
    <div class="card">
      <div class="card-body text-center">
        <i class='mdi mdi-36px mdi-gift-outline'></i>
        <h5 class="mt-3 fw-medium">Refer & Earn</h5>
        <p>Use Refer & Earn modal to encourage your exiting customers refer their friends & colleague.</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#referAndEarn"> Show </button>
      </div>
    </div>
  </div>
  <!--/  Refer & Earn -->

  <!--  Edit User -->
  <div class="col-12 col-sm-6 col-lg-4 mb-4">
    <div class="card">
      <div class="card-body text-center">
        <i class='mdi mdi-36px mdi-account-outline'></i>
        <h5 class="mt-3 fw-medium">Edit User</h5>
        <p>Easily update the user data on the go, built in form validation and custom controls.</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editUser"> Show </button>
      </div>
    </div>
  </div>
  <!--/  Edit User -->

  <!--  Enable OTP -->
  <div class="col-12 col-sm-6 col-lg-4 mb-md-0 mb-4">
    <div class="card">
      <div class="card-body text-center">
        <i class='mdi mdi-36px mdi-cellphone'></i>
        <h5 class="mt-3 fw-medium">Enable OTP</h5>
        <p>Use this modal to enhance your application security by enabling authentication with OTP.</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#enableOTP"> Show </button>
      </div>
    </div>
  </div>
  <!--/  Enable OTP -->

  <!--  Share Project -->
  <div class="col-12 col-sm-6 col-lg-4 mb-4">
    <div class="card">
      <div class="card-body text-center">
        <i class='mdi mdi-36px mdi-file-document-outline'></i>
        <h5 class="mt-3 fw-medium">Share Project</h5>
        <p>Elegant Share Project options modal popup example, easy to use in any page.</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#shareProject"> Show </button>
      </div>
    </div>
  </div>
  <!--/  Share Project -->

  <!--  Create App -->
  <div class="col-12 col-sm-6 col-lg-4 mb-4">
    <div class="card">
      <div class="card-body text-center">
        <i class='mdi mdi-36px mdi-cube-outline'></i>
        <h5 class="mt-3 fw-medium">Create App</h5>
        <p>Provide application data with this form to create your app, easy to use in page.</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createApp"> Show </button>
      </div>
    </div>
  </div>
  <!--/  Create App -->

  <!--  Two Factor Auth -->
  <div class="col-12 col-sm-6 col-lg-4">
    <div class="card">
      <div class="card-body text-center">
        <i class="mdi mdi-lock-outline mdi-36px"></i>
        <h5 class="mt-3 fw-medium">Two Factor Auth</h5>
        <p>Enhance your application security by enabling two factor authentication.</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#twoFactorAuth"> Show </button>
      </div>
    </div>
  </div>
  <!--/  Two Factor Auth -->
</div>

<!-- All Modals -->
@include('_partials/_modals/modal-add-new-cc')
@include('_partials/_modals/modal-add-new-address')
@include('_partials/_modals/modal-refer-earn')
@include('_partials/_modals/modal-edit-user')
@include('_partials/_modals/modal-enable-otp')
@include('_partials/_modals/modal-share-project')
@include('_partials/_modals/modal-create-app')
@include('_partials/_modals/modal-two-factor-auth')
@endsection
