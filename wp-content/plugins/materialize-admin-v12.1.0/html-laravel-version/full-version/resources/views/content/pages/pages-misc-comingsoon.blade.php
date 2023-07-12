@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Coming Soon - Pages')

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-misc.css')}}">
@endsection

@section('content')
<!-- We are Coming soon -->
<div class="misc-wrapper">
  <h3 class="mb-2 mx-2">We are launching soon 🚀</h3>
  <p class="mb-4 mx-2">We're creating something awesome. Please subscribe to get notified when it's ready!</p>
  <form onsubmit="return false">
    <div class="mb-0 d-flex gap-3">
      <input type="text" class="form-control" placeholder="Enter your email" autofocus>
      <button type="submit" class="btn btn-primary">Notify</button>
    </div>
  </form>
  <div class="d-flex justify-content-center mt-5">
    <img src="{{ asset('assets/img/illustrations/misc-coming-soon-object.png')}}" alt="misc-coming-soon" class="img-fluid misc-object d-none d-lg-inline-block" width="170">
    <img src="{{ asset('assets/img/illustrations/misc-bg-'.$configData['style'].'.png') }}" alt="misc-coming-soon" class="misc-bg d-none d-lg-inline-block" data-app-light-img="illustrations/misc-bg-light.png" data-app-dark-img="illustrations/misc-bg-dark.png">
    <img src="{{ asset('assets/img/illustrations/misc-coming-soon-illustration.png')}}" alt="misc-coming-soon" class="img-fluid zindex-1" width="190">
  </div>
</div>
<!-- /We are Coming soon -->
@endsection
