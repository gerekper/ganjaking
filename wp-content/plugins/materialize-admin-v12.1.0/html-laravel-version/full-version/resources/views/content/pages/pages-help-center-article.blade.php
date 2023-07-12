@extends('layouts/layoutMaster')

@section('title', 'Article - Pages')

<!-- Page -->
@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-help-center.css')}}" />
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Help Center /</span> Article
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

  <!-- Article -->
  <div class="col-xl-9 col-lg-8 col-md-8">
    <div class="card overflow-hidden">
      <div class="card-body">
        <a class="btn btn-outline-primary mb-4" href="{{url('pages/help-center-categories')}}">
          <i class="mdi mdi-arrow-left scaleX-n1-rtl me-1"></i>
          <span class="align-middle">Back to Category</span>
        </a>
        <div class="h5 d-flex align-items-center mt-2 mb-4">
          <div class="avatar">
            <div class="avatar-initial bg-label-secondary rounded w-100">
              <i class="mdi mdi-account-outline mdi-24px"></i>
            </div>
          </div>
          <span class="ms-3">Pricing Wizard</span>
        </div>
        <p>
          In a professional context it often happens that private or corporate clients corder a publication to be made
          and presented with the actual content still not being ready. Think of a news blog that's filled with content
          hourly on the day of going live. However, reviewers tend to be distracted by comprehensible content, say, a
          random text copied from a
          newspaper or the internet. The are likely to focus on the text, disregarding the layout and its elements.
        </p>
        <p>
          Most of its text is made up from sections 1.10.32–3 of Cicero's De finibus bonorum et malorum (On the
          Boundaries of Goods and Evils; finibus may also be translated as purposes). Neque porro quisquam est qui
          dolorem ipsum quia
          dolor sit amet, consectetur, adipisci velit is the first known version ("Neither is there anyone who loves
          grief itself since it
          is grief and thus wants to obtain it"). It was found by Richard McClintock, a philologist, director of
          publications at Hampden-Sydney College in Virginia; he
          searched for citings of consectetur in classical Latin literature, a term of remarkably low frequency in that
          literary corpus.
        </p>
        <p>
          Cicero famously orated against his political opponent Lucius Sergius Catilina. Occasionally the first Oration
          against Catiline is taken for type specimens: Quo usque tandem abutere, Catilina, patientia nostra? Quam diu
          etiam furor iste
          tuus nos eludet? (How long, O Catiline, will you abuse our patience? And for how long will that madness of
          yours mock us?)
        </p>
        <hr>
        <div class="d-flex justify-content-between flex-wrap gap-3 mb-2">
          <div class="article-info mb-2">
            <h6 class="mb-1 fw-semibold">Did you find this article helpful?</h6>
            <p class="card-text text-muted">55 People found this helpful</p>
          </div>
          <h6 class="fw-semibold">Still need help? <a href="javascript:void(0);">Contact us?</a></h6>
        </div>
        <div class="article-votes">
          <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-outline-primary me-2">
            <span class="mdi mdi-thumb-up-outline"></span>
          </a>
          <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-outline-primary">
            <span class="mdi mdi-thumb-down-outline"></span>
          </a>
        </div>
      </div>
    </div>
  </div>
  <!-- /Article -->
</div>
@endsection
