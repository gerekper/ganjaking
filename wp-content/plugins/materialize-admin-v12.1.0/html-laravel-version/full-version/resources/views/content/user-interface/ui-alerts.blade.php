@extends('layouts/layoutMaster')

@section('title', 'Alerts - UI elements')

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">UI elements /</span> Alerts
</h4>
<div class="row mb-4">
  <!-- Basic Alerts -->
  <div class="col-md mb-4 mb-md-0">
    <div class="card">
      <h5 class="card-header">Basic Alerts</h5>
      <div class="card-body">
        <div class="alert alert-primary" role="alert">
          This is a primary alert — check it out!
        </div>

        <div class="alert alert-secondary" role="alert">
          This is a secondary alert — check it out!
        </div>

        <div class="alert alert-success" role="alert">
          This is a success alert — check it out!
        </div>

        <div class="alert alert-danger" role="alert">
          This is a danger alert — check it out!
        </div>

        <div class="alert alert-warning" role="alert">
          This is a warning alert — check it out!
        </div>

        <div class="alert alert-info" role="alert">
          This is an info alert — check it out!
        </div>

        <div class="alert alert-dark mb-0" role="alert">
          This is a dark alert — check it out!
        </div>
      </div>
    </div>
  </div>
  <!--/ Basic Alerts -->
  <!-- Outline Alerts -->
  <div class="col-md">
    <div class="card">
      <h5 class="card-header">Outline Alerts</h5>
      <div class="card-body">
        <div class="alert alert-outline-primary" role="alert">
          This is a primary outline alert — check it out!
        </div>

        <div class="alert alert-outline-secondary" role="alert">
          This is a secondary outline alert — check it out!
        </div>

        <div class="alert alert-outline-success" role="alert">
          This is a success outline alert — check it out!
        </div>

        <div class="alert alert-outline-danger" role="alert">
          This is a danger outline alert — check it out!
        </div>

        <div class="alert alert-outline-warning" role="alert">
          This is a warning outline alert — check it out!
        </div>

        <div class="alert alert-outline-info" role="alert">
          This is an info outline alert — check it out!
        </div>

        <div class="alert alert-outline-dark mb-0" role="alert">
          This is a dark outline alert — check it out!
        </div>
      </div>
    </div>
  </div>
  <!--/ Outline Alerts -->
</div>
<div class="row mb-4">
  <!-- Solid Alerts -->
  <div class="col-md mb-4 mb-md-0">
    <div class="card">
      <h5 class="card-header">Solid Alerts</h5>
      <div class="card-body">
        <div class="alert alert-solid-primary" role="alert">
          This is a primary solid alert — check it out!
        </div>

        <div class="alert alert-solid-secondary" role="alert">
          This is a secondary solid alert — check it out!
        </div>

        <div class="alert alert-solid-success" role="alert">
          This is a success solid alert — check it out!
        </div>

        <div class="alert alert-solid-danger" role="alert">
          This is a danger solid alert — check it out!
        </div>

        <div class="alert alert-solid-warning" role="alert">
          This is a warning solid alert — check it out!
        </div>

        <div class="alert alert-solid-info" role="alert">
          This is an info solid alert — check it out!
        </div>

        <div class="alert alert-solid-dark mb-0" role="alert">
          This is a dark solid alert — check it out!
        </div>
      </div>
    </div>
  </div>
  <!--/ Solid Alerts -->
  <!-- Dismissible Alerts -->
  <div class="col-md">
    <div class="card">
      <h5 class="card-header">Dismissible Alerts</h5>
      <div class="card-body">
        <div class="alert alert-primary alert-dismissible" role="alert">
          This is a primary dismissible alert — check it out!
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          </button>
        </div>

        <div class="alert alert-secondary alert-dismissible" role="alert">
          This is a secondary dismissible alert — check it out!
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          </button>
        </div>

        <div class="alert alert-success alert-dismissible" role="alert">
          This is a success dismissible alert — check it out!
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          </button>
        </div>

        <div class="alert alert-danger alert-dismissible" role="alert">
          This is a danger dismissible alert — check it out!
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          </button>
        </div>

        <div class="alert alert-warning alert-dismissible" role="alert">
          This is a warning dismissible alert — check it out!
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          </button>
        </div>

        <div class="alert alert-info alert-dismissible" role="alert">
          This is an info dismissible alert — check it out!
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          </button>
        </div>

        <div class="alert alert-dark alert-dismissible mb-0" role="alert">
          This is a dark dismissible alert — check it out!
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          </button>
        </div>
      </div>
    </div>
  </div>
  <!--/ Dismissible Alerts -->
</div>
<div class="row">
  <!-- Alerts with headings -->
  <div class="col-md mb-4 mb-md-0">
    <div class="card">
      <h5 class="card-header">Alerts with Heading</h5>
      <div class="card-body">
        <div class="alert alert-success alert-dismissible" role="alert">
          <h4 class="alert-heading d-flex align-items-center"><i class="mdi mdi-check-circle-outline mdi-24px me-2"></i>Well done :)</h4>
          <hr>
          <p class="mb-0">Halvah cheesecake toffee. Cupcake jelly cookie chocolate bar topping. Cupcake candy dessert
            biscuit
            chocolate halvah bear claw sweet liquorice. Gummies wafer candy canes chocolate.</p>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          </button>
        </div>
        <div class="alert alert-danger alert-dismissible" role="alert">
          <h4 class="alert-heading d-flex align-items-center"><i class="mdi mdi-alert-circle-outline mdi-24px me-2"></i>Error!!</h4>
          <p>Aww yeah, you successfully read this important alert message. Sweet muffin croissant oat cake marzipan
            powder jujubes.</p>
          <hr>
          <p class="mb-0">Whenever you need to, be sure to use margin utilities to keep things nice and tidy.Jujubes
            bonbon danish dragée oat cake cupcake macaroon. Sesame snaps pudding cotton candy.</p>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          </button>
        </div>
        <div class="alert alert-primary alert-dismissible mb-0" role="alert">
          <h4 class="alert-heading d-flex align-items-center"><i class="mdi mdi-chat-alert-outline mdi-24px me-2"></i>For a watch</h4>
          <p class="mb-0">Bonbon sweet roll dragée lemon drops tart gummi bears fruitcake. Jujubes bonbon danish dragée
            oat cake
            cupcake macaroon. Sesame snaps pudding cotton candy.</p>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          </button>
        </div>
      </div>
    </div>
  </div>
  <!--/ Alerts with headings -->
  <!-- Alerts with Icons -->
  <div class="col-md">
    <div class="card">
      <h5 class="card-header">Alerts with Icons</h5>
      <div class="card-body">
        <div class="alert alert-solid-primary d-flex align-items-center" role="alert">
          <i class="mdi mdi-alert-circle-check-outline me-2"></i>
          This is a primary solid alert — check it out!
        </div>

        <div class="alert alert-solid-secondary d-flex align-items-center" role="alert">
          <i class="mdi mdi-alert-remove-outline me-2"></i>
          This is a secondary solid alert — check it out!
        </div>

        <div class="alert alert-solid-success d-flex align-items-center" role="alert">
          <i class="mdi mdi-check-circle-outline me-2"></i>
          This is a success solid alert — check it out!
        </div>

        <div class="alert alert-solid-danger d-flex align-items-center" role="alert">
          <i class="mdi mdi-alert-circle-outline me-2"></i>
          This is a danger solid alert — check it out!
        </div>

        <div class="alert alert-solid-warning d-flex align-items-center" role="alert">
          <i class="mdi mdi-alert-outline me-2"></i>
          This is a warning solid alert — check it out!
        </div>

        <div class="alert alert-solid-info d-flex align-items-center" role="alert">
          <i class="mdi mdi-chat-alert-outline me-2"></i>
          This is an info solid alert — check it out!
        </div>

        <div class="alert alert-solid-dark d-flex align-items-center mb-0" role="alert">
          <i class="mdi mdi-alert-rhombus-outline me-2"></i>
          This is a dark solid alert — check it out!
        </div>
      </div>
    </div>
  </div>
  <!--/ Alerts with Icons -->
</div>
@endsection
