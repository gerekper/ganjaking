@extends('layouts/layoutMaster')

@section('title', 'Cards Actions- UI elements')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/spinkit/spinkit.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sortablejs/sortable.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/cards-actions.js')}}"></script>
@endsection

@section('content')

<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">UI Elements /</span> Cards Actions
</h4>

<!-- Cards Action -->
<div class="card card-action mb-5">
  <div class="card-alert"></div>
  <div class="card-header">
    <div class="card-action-title">Cards Action</div>
    <div class="card-action-element">
      <ul class="list-inline mb-0">
        <li class="list-inline-item">
          <a href="javascript:void(0);" class="card-collapsible"><i class="tf-icons mdi mdi-chevron-up"></i></a>
        </li>
        <li class="list-inline-item">
          <a href="javascript:void(0);" class="card-reload">
            <span class="d-inline-flex scaleX-n1-rtl align-middle">
              <i class="tf-icons mdi mdi-rotate-left"></i>
            </span>
          </a>
        </li>
        <li class="list-inline-item">
          <a href="javascript:void(0);" class="card-expand"><i class="tf-icons mdi mdi-fullscreen"></i></a>
        </li>
        <li class="list-inline-item">
          <a href="javascript:void(0);" class="card-close"><i class="tf-icons mdi mdi-close"></i></a>
        </li>
      </ul>
    </div>
  </div>
  <div class="collapse show">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>Action</th>
          <th class="text-center">Icon</th>
          <th>Details</th>
        </tr>
      </thead>
      <tbody class="table-border-bottom-0">
        <tr>
          <td>Collapse</td>
          <td class="text-center">
            <i class="tf-icons mdi mdi-chevron-up"></i>
          </td>
          <td>Collapse card content using collapse action.</td>
        </tr>
        <tr>
          <td>Refresh Content</td>
          <td class="text-center scaleX-n1-rtl">
            <i class="tf-icons mdi mdi-rotate-left"></i>
          </td>
          <td>Refresh your card content using refresh action.</td>
        </tr>
        <tr>
          <td>Expand Card</td>
          <td class="text-center">
            <i class="tf-icons mdi mdi-fullscreen"></i>
          </td>
          <td>Maximize your card using expand action</td>
        </tr>
        <tr>
          <td>Remove Card</td>
          <td class="text-center">
            <i class="tf-icons mdi mdi-close"></i>
          </td>
          <td>Remove card from page using remove card action</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<h6 class="pb-1 mb-4 text-muted">Examples</h6>
<p>Use <code>.card-action</code> class with <code>.card</code> class to create action card. Use <code>.card-action-title</code> for action card title and <code>.card-action-element</code> to warp the actions icons.</p>
<div class="row mb-5">
  <div class="col-md">
    <div class="card card-action mb-4">
      <div class="card-header">
        <div class="card-action-title">Collapsible Card</div>
        <div class="card-action-element">
          <ul class="list-inline mb-0">
            <li class="list-inline-item">
              <a href="javascript:void(0);" class="card-collapsible"><i class="tf-icons mdi mdi-chevron-up"></i></a>
            </li>
          </ul>
        </div>
      </div>
      <div class="collapse show">
        <div class="card-body">
          <p class="card-text">To create a collapsible card, use <code>.card-collapsible</code> class with action item. To show the collapsible content default use <code>.show</code> class with <code>.collapse</code>.</p>
          <p class="card-text">Click on <i class="tf-icons mdi mdi-chevron-up"></i> to see card collapse in action.</p>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md">
    <div class="card card-action mb-4">
      <div class="card-alert"></div>
      <div class="card-header">
        <div class="card-action-title">Refresh Content</div>
        <div class="card-action-element">
          <ul class="list-inline mb-0">
            <li class="list-inline-item">
              <a href="javascript:void(0);" class="card-reload d-flex scaleX-n1-rtl">
                <i class="tf-icons mdi mdi-rotate-left"></i>
              </a>
            </li>
          </ul>
        </div>
      </div>

      <div class="card-body">
        <p class="card-text">To create a card with refresh action, use <code>.card-reload</code> class with action item. Use <code>.card-alert</code> class to show custom response message.</p>
        <p class="card-text">Click on <i class="tf-icons mdi mdi-rotate-left scaleX-n1-rtl"></i> icon to see refresh card content in action.</p>
      </div>
    </div>
  </div>
  <div class="w-100"></div>
  <div class="col-md">
    <div class="card card-action mb-4">
      <div class="card-header">
        <div class="card-action-title">Expand Card</div>
        <div class="card-action-element">
          <ul class="list-inline mb-0">
            <li class="list-inline-item">
              <a href="javascript:void(0);" class="card-expand"><i class="tf-icons mdi mdi-fullscreen"></i></a>
            </li>
          </ul>
        </div>
      </div>

      <div class="card-body">
        <p class="card-text">To create a card with expand(fullscreen) action, use <code>.card-expand</code> class with action item. Use <kbd>ESC</kbd> key to exit from the fullscreen mode.</p>
        <p class="card-text">Click on <i class="tf-icons mdi mdi-fullscreen"></i> icon to see expand card in action.</p>
      </div>
    </div>
  </div>
  <div class="col-md">
    <div class="card card-action mb-4">
      <div class="card-alert"></div>
      <div class="card-header">
        <div class="card-action-title">Remove Card</div>
        <div class="card-action-element">
          <ul class="list-inline mb-0">
            <li class="list-inline-item">
              <a href="javascript:void(0);" class="card-close"><i class="tf-icons mdi mdi-close"></i></a>
            </li>
          </ul>
        </div>
      </div>
      <div class="card-body">
        <p class="card-text">Remove card action hide the card, use <code>.card-close</code> class with action item.</p>
        <br />
        <p class="card-text">Click on <i class="tf-icons mdi mdi-close"></i> icon to see remove card in action.</p>
      </div>
    </div>
  </div>
</div>
<!--/ Cards Action -->

<!-- Header elements -->
<h5 class="pb-1 mb-4">Header Elements</h5>
<div class="row mb-5">
  <div class="col-md">
    <div class="card mb-4">
      <div class="card-header header-elements">
        <span class=" me-2">Card Header</span>
        <div class="card-header-elements ms-auto">
          <span class="badge bg-primary rounded-pill">New</span>
        </div>
      </div>
      <div class="card-body">
        <p class="card-text">Sample card header with badge.</p>
      </div>
    </div>
  </div>
  <div class="col-md">
    <div class="card mb-4">
      <div class="card-body">
        <div class="card-title header-elements">
          <h5 class="m-0 me-2">Card Title</h5>
          <div class="card-title-elements ms-auto">
            <span class="badge badge-outline-primary rounded-pill">10</span>
          </div>
        </div>
        <p class="card-text">Sample card title with outline badge.</p>
      </div>
    </div>
  </div>
  <div class="w-100"></div>

  <div class="col-md">
    <div class="card mb-4">
      <div class="card-header header-elements">
        <span class=" me-2">Card Header</span>

        <div class="card-header-elements ms-auto">
          <button type="button" class="btn btn-xs btn-primary"><span class="tf-icon mdi mdi-plus me-1"></span>Button</button>
        </div>
      </div>
      <div class="card-body">
        <p class="card-text">Sample card header with extra small button.</p>
      </div>
    </div>
  </div>

  <div class="col-md">
    <div class="card mb-4">
      <div class="card-body">
        <div class="card-title header-elements">
          <h5 class="m-0 me-2">Card Title</h5>
          <div class="card-title-elements ms-auto">
            <button type="button" class="btn btn-icon btn-sm btn-primary">
              <span class="tf-icon mdi mdi-shopping-outline"></span>
            </button>
          </div>
        </div>
        <p class="card-text">Sample card title with small icon button.</p>
      </div>
    </div>
  </div>
  <div class="w-100"></div>

  <div class="col-md">
    <div class="card mb-4">
      <div class="card-header header-elements">
        <span class=" me-2">Card Header</span>

        <div class="card-header-elements ms-auto">
          <input type="text" class="form-control form-control-sm" placeholder="Search" />
        </div>
      </div>
      <div class="card-body">
        <p class="card-text">Sample card header with extra search input box.</p>
      </div>
    </div>
  </div>
  <div class="col-md">
    <div class="card mb-4">
      <div class="card-body">
        <div class="card-title header-elements">
          <h5 class="m-0 me-2">Card Title</h5>
          <div class="card-title-elements ms-auto">
            <label class="switch switch-primary switch-sm me-0">
              <input type="checkbox" class="switch-input" />
              <span class="switch-toggle-slider">
                <span class="switch-on"></span>
                <span class="switch-off"></span>
              </span>
              <span class="switch-label"></span>
            </label>
          </div>
        </div>
        <p class="card-text">Sample card title with switch.</p>
      </div>
    </div>
  </div>
  <div class="w-100"></div>
  <div class="col-md">
    <div class="card mb-4">
      <div class="card-header header-elements">
        <span class=" me-2">Card Header</span>
        <div class="card-header-elements ms-auto">
          <span class="tf-icon mdi mdi-bell-outline text-muted"></span>
          <span class="text text-muted d-flex">
            <small>Sample Text</small>
          </span>
        </div>
      </div>
      <div class="card-body">
        <p class="card-text">Sample card header with text.</p>
      </div>
    </div>
  </div>
  <div class="col-md">
    <div class="card mb-4">
      <div class="card-body">
        <div class="card-title header-elements">
          <h5 class="m-0 me-2">Card Title</h5>
          <div class="card-header-elements ms-auto">
            <span class="tf-icon mdi mdi-bell-outline text-muted"></span>
            <span class="text text-muted d-flex">
              <small>Sample Text</small>
            </span>
          </div>
        </div>
        <p class="card-text">Sample card title with text.</p>
      </div>
    </div>
  </div>
  <div class="w-100"></div>
  <div class="col-md">
    <div class="card mb-4">
      <div class="card-header header-elements">
        <span class=" me-2">Card Header</span>
        <div class="card-header-elements">
          <span class="badge bg-danger rounded-pill">Hello!</span>
        </div>
        <div class="card-header-elements ms-auto">
          <div class="btn-group">
            <button type="button" class="btn btn-primary">Primary</button>
            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" data-bs-reference="parent"></button>
            <div class="dropdown-menu">
              <a class="dropdown-item" href="javascript:void(0)">Action</a>
              <a class="dropdown-item" href="javascript:void(0)">Another action</a>
              <a class="dropdown-item" href="javascript:void(0)">Something else here</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="javascript:void(0)">Separated link</a>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body">
        <p class="card-text">Sample card header with badge and dropdown.</p>
      </div>
    </div>
  </div>
  <div class="col-md">
    <div class="card mb-4">
      <div class="card-body">
        <div class="card-title header-elements">
          <h5 class="m-0 me-2">Card Title</h5>
          <div class="card-title-elements">
            <label class="switch switch-primary switch-sm me-0">
              <input type="checkbox" class="switch-input" />
              <span class="switch-toggle-slider">
                <span class="switch-on"></span>
                <span class="switch-off"></span>
              </span>
            </label>
          </div>
          <div class="card-title-elements ms-auto">
            <select class="form-select form-select-sm w-px-100">
              <option selected="">Option 1</option>
              <option>Option 2</option>
              <option>Option 3</option>
            </select>
            <button type="button" class="btn btn-sm btn-primary">Go</button>
          </div>
        </div>
        <p class="card-text">Sample card title with switch, select box & button.</p>
      </div>
    </div>
  </div>
</div>
<!--/ Header elements -->

<!-- Draggable Cards -->
<h5 class="pb-1 mb-4">Draggable Cards</h5>
<div class="row" id="sortable-4">
  <div class="col-md-6 col-xl-4">
    <div class="card bg-primary text-white mb-3">
      <div class="card-header cursor-move text-white">Drag me!</div>
      <div class="card-body">
        <h4 class="card-title text-white">Primary card title</h4>
        <p class="card-text">
          Some quick example text to build on the card title and make up.
        </p>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-4">
    <div class="card bg-secondary text-white mb-3">
      <div class="card-header cursor-move text-white">Drag me!</div>
      <div class="card-body">
        <h4 class="card-title text-white">Secondary card title</h4>
        <p class="card-text">
          Some quick example text to build on the card title and make up.
        </p>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-4">
    <div class="card bg-success text-white mb-3">
      <div class="card-header cursor-move text-white">Drag me!</div>
      <div class="card-body">
        <h4 class="card-title text-white">Success card title</h4>
        <p class="card-text">
          Some quick example text to build on the card title and make up.
        </p>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-4">
    <div class="card bg-danger text-white mb-3">
      <div class="card-header cursor-move text-white">Drag me!</div>
      <div class="card-body">
        <h4 class="card-title text-white">Danger card title</h4>
        <p class="card-text">
          Some quick example text to build on the card title and make up.
        </p>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-4">
    <div class="card bg-warning text-white mb-3">
      <div class="card-header cursor-move text-white">Drag me!</div>
      <div class="card-body">
        <h4 class="card-title text-white">Warning card title</h4>
        <p class="card-text">
          Some quick example text to build on the card title and make up.
        </p>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-4">
    <div class="card bg-info text-white mb-3">
      <div class="card-header cursor-move text-white">Drag me!</div>
      <div class="card-body">
        <h4 class="card-title text-white">Info card title</h4>
        <p class="card-text">
          Some quick example text to build on the card title and make up.
        </p>
      </div>
    </div>
  </div>
</div>
<!--/ Draggable Cards -->

@endsection
