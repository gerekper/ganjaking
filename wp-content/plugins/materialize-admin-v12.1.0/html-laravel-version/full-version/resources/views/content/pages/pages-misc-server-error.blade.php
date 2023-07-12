@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Server Error - Pages')

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-misc.css')}}">
@endsection


@section('content')
<!-- Server Error -->
<div class="misc-wrapper">
  <h1 class="mb-2 mx-2" style="font-size: 6rem;">500</h1>
  <h4 class="mb-2 fw-semibold">Internal server error 🔐</h4>
  <p class="mb-2 mx-2">Oops somthing went wrong.</p>
  <div class="d-flex justify-content-center mt-5">
    <img src="{{ asset('assets/img/illustrations/misc-error-object.png')}}" alt="misc-server-error" class="img-fluid misc-object d-none d-lg-inline-block" width="160">
    <img src="{{ asset('assets/img/illustrations/misc-bg-'.$configData['style'].'.png') }}" alt="misc-server-error" class="misc-bg d-none d-lg-inline-block" data-app-light-img="illustrations/misc-bg-light.png" data-app-dark-img="illustrations/misc-bg-dark.png">
    <div class="d-flex flex-column align-items-center">
      <img src="{{ asset('assets/img/illustrations/misc-server-error-illustration.png')}}" alt="misc-server-error" class="img-fluid zindex-1" width="230">
      <div>
        <a href="{{url('/')}}" class="btn btn-primary text-center my-5">Back to home</a>
      </div>
    </div>
  </div>
</div>
<!-- /Server Error -->
@endsection
