@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Cards Gamifications- UI elements')

@section('content')

<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">UI Elements /</span> Cards Gamifications
</h4>

<div class="row gy-4">
  <div class="col-md-12 col-lg-4">
    <div class="card">
      <div class="card-body text-nowrap">
        <h4 class="card-title mb-1 d-flex gap-2 flex-wrap">Congratulations <strong>Norris!</strong> 🎉</h4>
        <p class="pb-0">Best seller of the month</p>
        <h4 class="text-primary mb-1">$42.8k</h4>
        <p class="mb-2 pb-1">78% of target 🤟🏻</p>
        <a href="javascript:;" class="btn btn-sm btn-primary">View Sales</a>
      </div>
      <img src="{{asset('assets/img/illustrations/trophy.png')}}" class="position-absolute bottom-0 end-0 me-3" height="140" alt="view sales">
    </div>
  </div>

  <div class="col-md-12 col-lg-8">
    <div class="card h-100">
      <div class="d-flex align-items-end row">
        <div class="col-md-6 order-2 order-md-1">
          <div class="card-body">
            <h4 class="card-title pb-xl-2">Congratulations <strong> John!</strong>🎉</h4>
            <p class="mb-0">You have done <span class="fw-semibold">68%</span>😎 more sales today.</p>
            <p>Check your new badge in your profile.</p>
            <a href="javascript:;" class="btn btn-primary">View Profile</a>
          </div>
        </div>
        <div class="col-md-6 text-center text-md-end order-1 order-md-2">
          <div class="card-body pb-0 px-0 px-md-4 ps-0">
            <img src="{{asset('assets/img/illustrations/illustration-john-'.$configData['style'].'.png')}}" height="186" alt="View Profile" data-app-light-img="illustrations/illustration-john-light.png" data-app-dark-img="illustrations/illustration-john-dark.png">
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-8 col-12">
    <div class="card h-100">
      <div class="d-flex align-items-end row">
        <div class="col-md-6">
          <div class="card-body">
            <h4 class="card-title pb-2">Congratulations <strong>Daisy!</strong>🎉</h4>
            <p class="mb-0">You have done 84% 😍 more task today.</p>
            <p class="pb-2">Check your new badge in your profile.</p>
            <a href="javascript:;" class="btn btn-primary">View Profile</a>
          </div>
        </div>
        <div class="col-md-6 text-center text-md-end">
          <div class="card-body pb-0 px-0 px-md-4 ps-0">
            <img src="{{asset('assets/img/illustrations/illustration-daisy-'.$configData['style'].'.png')}}" height="186" alt="View Profile" data-app-light-img="illustrations/illustration-daisy-light.png" data-app-dark-img="illustrations/illustration-daisy-dark.png">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-body">
        <h4 class="card-title mb-1 d-flex gap-2 flex-wrap">Upgrade Account 😀</h4>
        <p class="pb-1">Add 15 team members</p>
        <h4 class="text-primary mb-1">$199</h4>
        <p class="mb-2 pb-1">40% OFF 😍</p>
        <a href="javascript:;" class="btn btn-sm btn-primary">Upgrade Plan</a>
      </div>
      <img src="{{asset('assets/img/illustrations/illustration-upgrade-account.png')}}" class=" position-absolute bottom-0 end-0 me-3 mb-3" height="162" alt="Upgrade Account">
    </div>
  </div>
</div>
@endsection
