@extends('layouts/layoutMaster')

@section('title', 'Tooltips and popovers - UI elements')

@section('page-script')
<script src="{{asset('assets/js/ui-popover.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">UI elements /</span> Tooltips & popovers
</h4>

<!-- Tooltips -->
<div class="card mb-4">
  <h5 class="card-header">Tooltips</h5>
  <div class="card-body">
    <div class="text-light small fw-semibold">Directions</div>
    <div class="row demo-vertical-spacing">
      <div class="col">
        <button type="button" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="right" title="Tooltip on right">
          Right
        </button>
      </div>
      <div class="col">
        <button type="button" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Tooltip on top">
          Top
        </button>
      </div>
      <div class="col">
        <button type="button" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Tooltip on bottom">
          Bottom
        </button>
      </div>
      <div class="col">
        <button type="button" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="left" title="Tooltip on left">
          Left
        </button>
      </div>
    </div>
  </div>
  <hr class="m-0" />
  <div class="card-body">
    <div class="text-light small fw-semibold">Solid</div>

    <div class="demo-inline-spacing">
      <button type="button" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="Primary tooltip">
        Primary
      </button>
      <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-secondary" title="Secondary tooltip">
        Secondary
      </button>
      <button type="button" class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" title="Success tooltip">
        Success
      </button>
      <button type="button" class="btn btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-danger" title="Danger tooltip">
        Danger
      </button>
      <button type="button" class="btn btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-warning" title="Warning tooltip">
        Warning
      </button>
      <button type="button" class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-info" title="Info tooltip">
        Info
      </button>
      <button type="button" class="btn btn-dark" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-dark" title="Dark tooltip">
        Dark
      </button>
    </div>
  </div>
</div>
<!--/ Tooltips -->

<!-- Popovers -->
<div class="card">
  <h5 class="card-header">Popovers</h5>
  <div class="card-body">
    <div class="text-light small fw-semibold">Directions</div>
    <div class="row demo-vertical-spacing">
      <div class="col">
        <button type="button" class="btn btn-primary text-nowrap" data-bs-animation="true" data-bs-toggle="popover" data-bs-placement="right" data-bs-content="This is a very beautiful popover, show some love." title="Popover title">
          Popover on right
        </button>
      </div>
      <div class="col">
        <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="popover" data-bs-placement="top" data-bs-content="This is a very beautiful popover, show some love." title="Popover title">
          Popover on top
        </button>
      </div>
      <div class="col">
        <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-content="This is a very beautiful popover, show some love." title="Popover title">
          Popover on bottom
        </button>
      </div>
      <div class="col">
        <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="popover" data-bs-placement="left" data-bs-content="This is a very beautiful popover, show some love." title="Popover title">
          Popover on left
        </button>
      </div>
    </div>
  </div>
  <hr class="m-0" />
  <div class="card-body">
    <div class="text-light small fw-semibold">Solid</div>

    <div class="demo-inline-spacing">
      <button type="button" class="btn btn-primary" data-bs-toggle="popover" data-bs-placement="right" data-bs-custom-class="popover-primary" data-bs-content="This is a very beautiful popover, show some love." title="Popover title">
        Primary
      </button>
      <button type="button" class="btn btn-secondary" data-bs-toggle="popover" data-bs-placement="right" data-bs-custom-class="popover-secondary" data-bs-content="This is a very beautiful popover, show some love." title="Popover title">
        Secondary
      </button>
      <button type="button" class="btn btn-success" data-bs-toggle="popover" data-bs-placement="top" data-bs-custom-class="popover-success" data-bs-content="This is a very beautiful popover, show some love." title="Popover title">
        Success
      </button>
      <button type="button" class="btn btn-danger" data-bs-toggle="popover" data-bs-placement="top" data-bs-custom-class="popover-danger" data-bs-content="This is a very beautiful popover, show some love." title="Popover title">
        Danger
      </button>
      <button type="button" class="btn btn-warning" data-bs-toggle="popover" data-bs-placement="left" data-bs-custom-class="popover-warning" data-bs-content="This is a very beautiful popover, show some love." title="Popover title">
        Warning
      </button>
      <button type="button" class="btn btn-info" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-custom-class="popover-info" data-bs-content="This is a very beautiful popover, show some love." title="Popover title">
        Info
      </button>
      <button type="button" class="btn btn-dark" data-bs-toggle="popover" data-bs-placement="top" data-bs-custom-class="popover-dark" data-bs-content="This is a very beautiful popover, show some love." title="Popover title">
        Dark
      </button>
    </div>
  </div>
</div>
<!--/ Popovers -->
@endsection
