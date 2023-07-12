@extends('layouts/layoutMaster')

@section('title', 'Help Center Categories - Pages')

<!-- Page -->
@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-help-center.css')}}" />
@endsection

@section('content')

<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Help Center /</span> Categories
</h4>

<div class="row">
  <!-- Categories -->
  <div class="col-xl-3 col-lg-4 col-md-4 mb-lg-0 mb-4">
    <h4>Getting Started</h4>
    <div class="nav-align-left">
      <ul class="nav nav-pills border-0 w-100 gap-1">
        <li class="nav-item">
          <button class="nav-link" data-bs-target="javascript:void(0);">Setup</button>
        </li>
        <li class="nav-item">
          <button class="nav-link active" data-bs-target="javascript:void(0);">Items & Categories</button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-bs-target="javascript:void(0);">Payments & Checkout</button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-bs-target="javascript:void(0);">Fulfillment</button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-bs-target="javascript:void(0);">Manage Orders</button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-bs-target="javascript:void(0);">Coupons & Other Gifts</button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-bs-target="javascript:void(0);">Store Emails</button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-bs-target="javascript:void(0);">Taxes</button>
        </li>
      </ul>
    </div>
  </div>
  <!-- /Categories -->

  <!-- Article list -->
  <div class="col-xl-9 col-lg-8 col-md-8">
    <div class="card overflow-hidden">
      <div class="card-body">
        <div class="h5 d-flex align-items-center">
          <div class="avatar avatar-sm">
            <div class="avatar-initial bg-label-primary rounded w-100">
              <i class="mdi mdi-cube-outline mdi-24px"></i>
            </div>
          </div>
          <span class="ms-3">Items & Categories</span>
        </div>
        <ul class="list-unstyled my-4">
          <li class="mb-3">
            <a href="{{url('pages/help-center-article')}}" class="d-flex align-items-center">
              <i class="mdi mdi-chevron-right scaleX-n1-rtl text-muted me-1"></i>
              Pricing Wizard
            </a>
          </li>
          <li class="mb-3">
            <a href="{{url('pages/help-center-article')}}" class="d-flex align-items-center">
              <i class="mdi mdi-chevron-right scaleX-n1-rtl text-muted me-1"></i>
              Shopping on Instagram
            </a>
          </li>
          <li class="mb-3">
            <a href="{{url('pages/help-center-article')}}" class="d-flex align-items-center">
              <i class="mdi mdi-chevron-right scaleX-n1-rtl text-muted me-1"></i>
              Square sync
            </a>
          </li>
          <li class="mb-3">
            <a href="{{url('pages/help-center-article')}}" class="d-flex align-items-center">
              <i class="mdi mdi-chevron-right scaleX-n1-rtl text-muted me-1"></i>
              Change theme & fonts of the app
            </a>
          </li>
          <li class="mb-3">
            <a href="{{url('pages/help-center-article')}}" class="d-flex align-items-center">
              <i class="mdi mdi-chevron-right scaleX-n1-rtl text-muted me-1"></i>
              Calculate and track your material costs
            </a>
          </li>
          <li class="mb-3">
            <a href="{{url('pages/help-center-article')}}" class="d-flex align-items-center">
              <i class="mdi mdi-chevron-right scaleX-n1-rtl text-muted me-1"></i>
              eCommerce tracking with google analytics
            </a>
          </li>
          <li>
            <a href="{{url('pages/help-center-article')}}" class="d-flex align-items-center">
              <i class="mdi mdi-chevron-right scaleX-n1-rtl text-muted me-1"></i>
              How many sites can I have? How do I Delete one?
            </a>
          </li>
        </ul>

        <a class="btn btn-outline-primary" href="{{url('pages/help-center-landing')}}">
          <i class="mdi mdi-chevron-left scaleX-n1-rtl me-1"></i>
          <span class="align-middle">Back to help center</span>
        </a>
      </div>
    </div>
  </div>
  <!-- /Article list -->
</div>
@endsection
