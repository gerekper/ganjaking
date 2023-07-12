@php
$configData = Helper::appClasses();
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Forgot Password Cover - Pages')

@section('vendor-style')
<!-- Vendor -->
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
@endsection

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-auth.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/pages-auth.js')}}"></script>
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

    <!-- /Left Section -->
    <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center justify-content-center p-5 pb-2">
      <img src="{{asset('assets/img/illustrations/auth-forgot-password-illustration-'.$configData['style'].'.png') }}" class="auth-cover-illustration w-100" alt="auth-illustration" data-app-light-img="illustrations/auth-forgot-password-illustration-light.png" data-app-dark-img="illustrations/auth-forgot-password-illustration-dark.png" />
      <img src="{{asset('assets/img/illustrations/auth-cover-forgot-password-mask-'.$configData['style'].'.png') }}" class="authentication-image" alt="mask" data-app-light-img="illustrations/auth-cover-forgot-password-mask-light.png" data-app-dark-img="illustrations/auth-cover-forgot-password-mask-dark.png" />
    </div>
    <!-- /Left Section -->

    <!-- Forgot Password -->
    <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-5 p-4">
      <div class="w-px-400 mx-auto">
        <h4 class="mb-2 fw-semibold">Forgot Password? 🔒</h4>
        <p class="mb-4">Enter your email and we'll send you instructions to reset your password</p>
        <form id="formAuthentication" class="mb-3" action="{{url('auth/reset-password-cover')}}" method="GET">
          <div class="form-floating form-floating-outline mb-3">
            <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email" autofocus>
            <label for="email">Email</label>
          </div>
          <button class="btn btn-primary d-grid w-100">Send Reset Link</button>
        </form>
        <div class="text-center">
          <a href="{{url('auth/login-cover')}}" class="d-flex align-items-center justify-content-center">
            <i class="mdi mdi-chevron-left scaleX-n1-rtl mdi-24px"></i>
            Back to login
          </a>
        </div>
      </div>
    </div>
    <!-- /Forgot Password -->
  </div>
</div>
@endsection
