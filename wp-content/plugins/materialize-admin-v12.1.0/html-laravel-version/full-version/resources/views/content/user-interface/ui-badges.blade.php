@extends('layouts/layoutMaster')

@section('title', 'Badges - UI elements')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">UI elements /</span> Badges</h4>

<div class="row">
  <!-- Basic Badges -->
  <div class="col-lg">
    <div class="card mb-4">
      <h5 class="card-header">Basic Badges</h5>
      <div class="card-body">
        <div class="text-light small fw-semibold">Default</div>
        <div class="demo-inline-spacing">
          <span class="badge bg-primary">Primary</span>
          <span class="badge bg-secondary">Secondary</span>
          <span class="badge bg-success">Success</span>
          <span class="badge bg-danger">Danger</span>
          <span class="badge bg-warning">Warning</span>
          <span class="badge bg-info">Info</span>
          <span class="badge bg-dark">Dark</span>
        </div>
      </div>
      <hr class="m-0" />
      <div class="card-body">
        <div class="text-light small fw-semibold">Pills</div>

        <div class="demo-inline-spacing">
          <span class="badge rounded-pill bg-primary">Primary</span>
          <span class="badge rounded-pill bg-secondary">Secondary</span>
          <span class="badge rounded-pill bg-success">Success</span>
          <span class="badge rounded-pill bg-danger">Danger</span>
          <span class="badge rounded-pill bg-warning">Warning</span>
          <span class="badge rounded-pill bg-info">Info</span>
          <span class="badge rounded-pill bg-dark">Dark</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Label Badges -->
  <div class="col-lg">
    <div class="card mb-4">
      <h5 class="card-header">Label Badges</h5>
      <div class="card-body">
        <div class="text-light small fw-semibold">Label Default</div>

        <div class="demo-inline-spacing">
          <span class="badge bg-label-primary">Primary</span>
          <span class="badge bg-label-secondary">Secondary</span>
          <span class="badge bg-label-success">Success</span>
          <span class="badge bg-label-danger">Danger</span>
          <span class="badge bg-label-warning">Warning</span>
          <span class="badge bg-label-info">Info</span>
          <span class="badge bg-label-dark">Dark</span>
        </div>
      </div>
      <hr class="m-0" />
      <div class="card-body">
        <div class="text-light small fw-semibold">Label Pills</div>

        <div class="demo-inline-spacing">
          <span class="badge rounded-pill bg-label-primary">Primary</span>
          <span class="badge rounded-pill bg-label-secondary">Secondary</span>
          <span class="badge rounded-pill bg-label-success">Success</span>
          <span class="badge rounded-pill bg-label-danger">Danger</span>
          <span class="badge rounded-pill bg-label-warning">Warning</span>
          <span class="badge rounded-pill bg-label-info">Info</span>
          <span class="badge rounded-pill bg-label-dark">Dark</span>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">

  <!-- Button with Badges -->
  <div class="col-lg">
    <div class="card mb-4">
      <h5 class="card-header">Button with Badges</h5>
      <div class="row row-bordered g-0">
        <div class="col-xl-4 p-4">
          <small class="text-light fw-semibold">Default</small>
          <div class="demo-vertical-spacing">
            <button type="button" class="btn btn-primary">
              Text
              <span class="badge bg-white text-primary ms-1">4</span>
            </button>
            <button type="button" class="btn btn-primary">
              Text
              <span class="badge bg-secondary rounded-pill ms-1">4</span>
            </button>
          </div>
        </div>
        <div class="col-xl-4 p-4">
          <small class="text-light fw-semibold">Label</small>
          <div class="demo-vertical-spacing">
            <button type="button" class="btn btn-label-primary">
              Text
              <span class="badge bg-white text-primary ms-1">4</span>
            </button>
            <button type="button" class="btn btn-label-primary">
              Text
              <span class="badge bg-secondary rounded-pill ms-1">4</span>
            </button>
          </div>
        </div>

        <div class="col-xl-4 p-4">
          <small class="text-light fw-semibold">Outline</small>
          <div class="demo-vertical-spacing">
            <button type="button" class="btn btn-outline-primary">
              Text
              <span class="badge ms-1">4</span>
            </button>
            <button type="button" class="btn btn-outline-secondary">
              Text
              <span class="badge rounded-pill ms-1">4</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Badge Circle -->
  <div class="col-12">
    <div class="card mb-4">
      <h5 class="card-header">Badge Circle & Square Style</h5>
      <div class="row row-bordered g-0">
        <div class="col-lg-6 p-4">
          <h6>Basic</h6>
          <div class="text-light small fw-semibold mb-2">Default</div>
          <div class="demo-inline-spacing">
            <p>
              <span class="badge badge-center rounded-pill bg-primary">1</span>
              <span class="badge badge-center rounded-pill bg-secondary">2</span>
              <span class="badge badge-center rounded-pill bg-success">3</span>
              <span class="badge badge-center rounded-pill bg-danger">4</span>
              <span class="badge badge-center rounded-pill bg-warning">5</span>
              <span class="badge badge-center rounded-pill bg-info">6</span>
            </p>
            <p>
              <span class="badge badge-center bg-primary">1</span>
              <span class="badge badge-center bg-secondary">2</span>
              <span class="badge badge-center bg-success">3</span>
              <span class="badge badge-center bg-danger">4</span>
              <span class="badge badge-center bg-warning">5</span>
              <span class="badge badge-center bg-info">6</span>
            </p>
          </div>
        </div>
        <div class="col-lg-6 p-4">
          <h6>Label</h6>
          <div class="text-light small fw-semibold mb-2">Default</div>
          <div class="demo-inline-spacing">
            <p>
              <span class="badge badge-center rounded-pill bg-label-primary">1</span>
              <span class="badge badge-center rounded-pill bg-label-secondary">2</span>
              <span class="badge badge-center rounded-pill bg-label-success">3</span>
              <span class="badge badge-center rounded-pill bg-label-danger">4</span>
              <span class="badge badge-center rounded-pill bg-label-warning">5</span>
              <span class="badge badge-center rounded-pill bg-label-info">6</span>
            </p>
            <p>
              <span class="badge badge-center bg-label-primary">1</span>
              <span class="badge badge-center bg-label-secondary">2</span>
              <span class="badge badge-center bg-label-success">3</span>
              <span class="badge badge-center bg-label-danger">4</span>
              <span class="badge badge-center bg-label-warning">5</span>
              <span class="badge badge-center bg-label-info">6</span>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Badge Circle with Icons -->
  <div class="col-12">
    <div class="card mb-4">
      <h5 class="card-header">Badge Circle & Square With Icon</h5>
      <div class="row row-bordered g-0">
        <div class="col-lg-6 p-4">
          <h6>Basic</h6>
          <div class="text-light small fw-semibold mb-2">Default</div>
          <div class="demo-inline-spacing">
            <p>
              <span class="badge badge-center rounded-pill bg-primary"><i class='mdi mdi-star-outline'></i></span>
              <span class="badge badge-center rounded-pill bg-secondary"><i class='mdi mdi-bell-outline'></i></span>
              <span class="badge badge-center rounded-pill bg-success"><i class='mdi mdi-check'></i></span>
              <span class="badge badge-center rounded-pill bg-danger"><i class='mdi mdi-currency-usd'></i></span>
              <span class="badge badge-center rounded-pill bg-warning"><i class='mdi mdi-chart-pie-outline'></i></span>
              <span class="badge badge-center rounded-pill bg-info"><i class='mdi mdi-trending-up'></i></span>
            </p>
            <p>
              <span class="badge badge-center bg-primary"><i class='mdi mdi-star-outline'></i></span>
              <span class="badge badge-center bg-secondary"><i class='mdi mdi-bell-outline'></i></span>
              <span class="badge badge-center bg-success"><i class='mdi mdi-check'></i></span>
              <span class="badge badge-center bg-danger"><i class='mdi mdi-currency-usd'></i></span>
              <span class="badge badge-center bg-warning"><i class='mdi mdi-chart-pie-outline'></i></span>
              <span class="badge badge-center bg-info"><i class='mdi mdi-trending-up'></i></span>
            </p>
          </div>
        </div>
        <div class="col-lg-6 p-4">
          <h6>Label</h6>
          <div class="text-light small fw-semibold mb-2">Default</div>
          <div class="demo-inline-spacing">
            <p>
              <span class="badge badge-center rounded-pill bg-label-primary"><i class='mdi mdi-star-outline'></i></span>
              <span class="badge badge-center rounded-pill bg-label-secondary"><i class='mdi mdi-bell-outline'></i></span>
              <span class="badge badge-center rounded-pill bg-label-success"><i class='mdi mdi-check'></i></span>
              <span class="badge badge-center rounded-pill bg-label-danger"><i class='mdi mdi-currency-usd'></i></span>
              <span class="badge badge-center rounded-pill bg-label-warning"><i class='mdi mdi-chart-pie-outline'></i></span>
              <span class="badge badge-center rounded-pill bg-label-info"><i class='mdi mdi-trending-up'></i></span>
            </p>
            <p>
              <span class="badge badge-center bg-label-primary"><i class='mdi mdi-star-outline'></i></span>
              <span class="badge badge-center bg-label-secondary"><i class='mdi mdi-bell-outline'></i></span>
              <span class="badge badge-center bg-label-success"><i class='mdi mdi-check'></i></span>
              <span class="badge badge-center bg-label-danger"><i class='mdi mdi-currency-usd'></i></span>
              <span class="badge badge-center bg-label-warning"><i class='mdi mdi-chart-pie-outline'></i></span>
              <span class="badge badge-center bg-label-info"><i class='mdi mdi-trending-up'></i></span>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Dots -->
  <div class="col-12">
    <div class="card mb-4">
      <h5 class="card-header">Dots Style</h5>
      <div class="card-body d-sm-flex d-block">
        <div class="d-flex align-items-center lh-1 me-3 mb-3 mb-sm-0">
          <span class="badge badge-dot bg-primary me-1"></span> Primary
        </div>
        <div class="d-flex align-items-center lh-1 me-3 mb-3 mb-sm-0">
          <span class="badge badge-dot bg-secondary me-1"></span> Secondary
        </div>
        <div class="d-flex align-items-center lh-1 me-3 mb-3 mb-sm-0">
          <span class="badge badge-dot bg-success me-1"></span> Success
        </div>
        <div class="d-flex align-items-center lh-1 me-3 mb-3 mb-sm-0">
          <span class="badge badge-dot bg-danger me-1"></span> Danger
        </div>
        <div class="d-flex align-items-center lh-1 me-3 mb-3 mb-sm-0">
          <span class="badge badge-dot bg-warning me-1"></span> Warning
        </div>
        <div class="d-flex align-items-center lh-1 me-3 mb-3 mb-sm-0">
          <span class="badge badge-dot bg-info me-1"></span> Info
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <!-- Notifications -->
  <div class="col-lg">
    <div class="card mb-4">
      <h5 class="card-header">Button with Badges Notification</h5>
      <div class="card-body demo-inline-spacing gap-3">
        <button type="button" class="btn btn-label-primary text-nowrap d-inline-flex position-relative me-3">
          Badge
          <span class="position-absolute top-0 start-100 translate-middle badge bg-primary text-white">2</span>
        </button>
        <button type="button" class="btn btn-warning text-nowrap d-inline-flex position-relative me-3">
          Label Badge
          <span class="position-absolute top-0 start-100 translate-middle badge bg-label-warning border border-warning">2</span>
        </button>
        <button type="button" class="btn btn-label-info text-nowrap d-inline-flex position-relative me-3">
          Pill
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-info text-white">2</span>
        </button>
        <button type="button" class="btn btn-label-danger text-nowrap d-inline-flex position-relative">
          Dot
          <span class="position-absolute top-0 start-100 translate-middle badge badge-dot border border-2 p-2 bg-danger"></span>
        </button>
      </div>
    </div>
  </div>
  <div class="col-lg">
    <div class="card mb-4">
      <h5 class="card-header">Notifications With Icons</h5>
      <div class="card-body demo-inline-spacing gap-3">
        <div class="text-light small fw-semibold mt-0">Small badge notifications.</div>
        <div class="text-nowrap d-inline-flex position-relative me-3">
          <span class="tf-icons mdi mdi-email-outline"></span>
          <span class="position-absolute top-0 start-100 translate-middle badge bg-primary text-white badge-notifications">6</span>
        </div>
        <div class="text-nowrap d-inline-flex position-relative me-3">
          <span class="tf-icons mdi mdi-twitter"></span>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-label-info text-info badge-notifications">5</span>
        </div>
        <div class="text-nowrap d-inline-flex position-relative me-3">
          <span class="tf-icons mdi mdi-bell-outline"></span>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger text-white badge-notifications">4</span>
        </div>
        <div class="text-nowrap d-inline-flex position-relative me-3">
          <span class="tf-icons mdi mdi-facebook"></span>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger badge-dot"></span>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-lg">
    <div class="card mb-4">
      <h5 class="card-header">Badges Position</h5>
      <div class="card-body">
        <div class="text-light small fw-semibold mb-2">Position using utility classes like <code>top-*</code>, <code>start-*</code>, etc...</div>
        <div class="demo-inline-spacing">
          <div class="avatar d-inline-flex position-relative me-3">
            <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle">
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary text-white">4</span>
          </div>
          <div class="avatar d-inline-flex position-relative me-3">
            <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle">
            <span class="position-absolute top-100 start-0 translate-middle badge rounded-pill bg-primary text-white">4</span>
          </div>
          <div class="avatar d-inline-flex position-relative me-3">
            <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle">
            <span class="position-absolute top-100 start-100 translate-middle badge rounded-pill bg-primary text-white">4</span>
          </div>
          <div class="avatar d-inline-flex position-relative me-3">
            <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle">
            <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-primary text-white">4</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg">
    <div class="card mb-4">
      <h5 class="card-header">Badge Overlaps for Shapes</h5>
      <div class="card-body">
        <div class="text-light small fw-semibold mb-2">Using <code>rounded-*</code> utilities for avatar & <code>.badge-dot</code> class for notification</div>
        <div class="demo-inline-spacing">
          <div class="avatar d-inline-flex position-relative me-3">
            <img src="../../assets/img/avatars/1.png" alt="Avatar">
            <span class="position-absolute top-0 start-100 translate-middle badge badge-dot rounded-pill bg-primary"></span>
          </div>
          <div class="avatar d-inline-flex position-relative me-3">
            <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-1">
            <span class="position-absolute top-0 start-100 translate-middle badge badge-dot border rounded-pill bg-primary"></span>
          </div>
          <div class="avatar d-inline-flex position-relative me-3">
            <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-2">
            <span class="position-absolute top-0 start-100 translate-middle badge badge-dot p-2 rounded-pill bg-primary"></span>
          </div>
          <div class="avatar d-inline-flex position-relative me-3">
            <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded">
            <span class="position-absolute top-0 start-100 translate-middle badge badge-dot rounded-pill bg-primary"></span>
          </div>
          <div class="avatar d-inline-flex position-relative me-3">
            <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-3">
            <span class="position-absolute top-0 start-100 translate-middle badge badge-dot p-2 border border-2 rounded-pill bg-primary"></span>
          </div>
          <div class="avatar d-inline-flex position-relative me-3">
            <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle">
            <span class="position-absolute top-0 start-100 translate-middle badge border rounded-pill bg-primary">9</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-lg">
    <div class="card mb-lg-0 mb-4">
      <h5 class="card-header">Maximum Values</h5>
      <div class="card-body pt-3">
        <div class="avatar d-inline-flex position-relative me-3">
          <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle">
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary text-white">99</span>
        </div>
        <div class="avatar d-inline-flex position-relative me-3">
          <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle">
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary text-white">99+</span>
        </div>
        <div class="avatar d-inline-flex position-relative me-3">
          <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle">
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary text-white">999+</span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg">
    <div class="card">
      <h5 class="card-header">Custom label Badges</h5>
      <div class="card-body pt-3 d-flex flex-wrap gap-4">
        <div class="avatar d-inline-flex position-relative">
          <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle">
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-label-primary">4</span>
        </div>
        <div class="avatar d-inline-flex position-relative">
          <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle">
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-label-secondary">4</span>
        </div>
        <div class="avatar d-inline-flex position-relative">
          <span class="avatar-initial rounded-circle bg-success">pi</span>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-label-success">4</span>
        </div>
        <div class="avatar d-inline-flex position-relative">
          <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle">
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-label-danger">4</span>
        </div>
        <div class="avatar d-inline-flex position-relative">
          <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle">
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-label-warning">4</span>
        </div>
        <div class="avatar d-inline-flex position-relative">
          <span class="avatar-initial rounded-circle bg-info">pi</span>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-label-info">4</span>
        </div>
        <div class="avatar d-inline-flex position-relative">
          <img src="../../assets/img/avatars/1.png" alt="Avatar" class="rounded-circle">
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-label-dark">4</span>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
