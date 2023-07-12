@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Error - Pages')

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-misc.css')}}">
@endsection


@section('content')
<!-- Error -->
<div class="misc-wrapper">
  <h1 class="mb-2 mx-2" style="font-size: 6rem;">404</h1>
  <h4 class="mb-2 fw-semibold">Page Not Found ⚠️</h4>
  <p class="mb-4 mx-2">we couldn't find the page you are looking for</p>
  <div class="d-flex justify-content-center mt-5">
    <img src="{{ asset('assets/img/illustrations/misc-error-object.png') }}" alt="misc-error" class="img-fluid misc-object d-none d-lg-inline-block" width="160">
    <img src="{{ asset('assets/img/illustrations/misc-bg-'.$configData['style'].'.png') }}" alt="misc-error" class="misc-bg d-none d-lg-inline-block" data-app-light-img="illustrations/misc-bg-light.png" data-app-dark-img="illustrations/misc-bg-dark.png">
    <div class="d-flex flex-column align-items-center">
      <img src="{{ asset('assets/img/illustrations/misc-error-illustration.png' )}}" alt="misc-error" class="img-fluid zindex-1" width="190">
      <div>
        <a href="{{url('/')}}" class="btn btn-primary text-center my-4">Back to home</a>
      </div>
    </div>
  </div>
</div>
<!-- /Error -->
@endsection
