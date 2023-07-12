@extends('layouts/layoutMaster')

@section('title', 'Buttons - UI elements')

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">UI elements /</span>
  Buttons
</h4>

<div class="row">
  <!-- Basic Buttons -->

  <div class="col-12">
    <div class="card mb-4">
      <h5 class="card-header">Basic Buttons</h5>
      <div class="card-body">
        <small class="text-light fw-semibold">Default</small>
        <div class="demo-inline-spacing">
          <button type="button" class="btn btn-primary">Primary</button>
          <button type="button" class="btn btn-secondary">Secondary</button>
          <button type="button" class="btn btn-success">Success</button>
          <button type="button" class="btn btn-danger">Danger</button>
          <button type="button" class="btn btn-warning">Warning</button>
          <button type="button" class="btn btn-info">Info</button>
          <button type="button" class="btn btn-dark">Dark</button>
        </div>
      </div>
      <hr class="m-0" />
      <div class="card-body">
        <small class="text-light fw-semibold">Rounded</small>
        <div class="demo-inline-spacing">
          <button type="button" class="btn rounded-pill btn-primary">Primary</button>
          <button type="button" class="btn rounded-pill btn-secondary">Secondary</button>
          <button type="button" class="btn rounded-pill btn-success">Success</button>
          <button type="button" class="btn rounded-pill btn-danger">Danger</button>
          <button type="button" class="btn rounded-pill btn-warning">Warning</button>
          <button type="button" class="btn rounded-pill btn-info">Info</button>
          <button type="button" class="btn rounded-pill btn-dark">Dark</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Label Buttons -->

  <div class="col-12">
    <div class="card mb-4">
      <h5 class="card-header">Label(Tonal) Buttons</h5>
      <div class="card-body">
        <small class="text-light fw-semibold">Default</small>
        <div class="demo-inline-spacing">
          <button type="button" class="btn btn-label-primary">Primary</button>
          <button type="button" class="btn btn-label-secondary">Secondary</button>
          <button type="button" class="btn btn-label-success">Success</button>
          <button type="button" class="btn btn-label-danger">Danger</button>
          <button type="button" class="btn btn-label-warning">Warning</button>
          <button type="button" class="btn btn-label-info">Info</button>
          <button type="button" class="btn btn-label-dark">Dark</button>
        </div>
      </div>
      <hr class="m-0" />
      <div class="card-body">
        <small class="text-light fw-semibold">Rounded</small>
        <div class="demo-inline-spacing">
          <button type="button" class="btn rounded-pill btn-label-primary">Primary</button>
          <button type="button" class="btn rounded-pill btn-label-secondary">Secondary</button>
          <button type="button" class="btn rounded-pill btn-label-success">Success</button>
          <button type="button" class="btn rounded-pill btn-label-danger">Danger</button>
          <button type="button" class="btn rounded-pill btn-label-warning">Warning</button>
          <button type="button" class="btn rounded-pill btn-label-info">Info</button>
          <button type="button" class="btn rounded-pill btn-label-dark">Dark</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Outline Buttons -->

  <div class="col-12">
    <div class="card mb-4">
      <h5 class="card-header">Outline Buttons</h5>
      <div class="card-body">
        <small class="text-light fw-semibold">Default</small>
        <div class="demo-inline-spacing">
          <button type="button" class="btn btn-outline-primary">Primary</button>
          <button type="button" class="btn btn-outline-secondary">Secondary</button>
          <button type="button" class="btn btn-outline-success">Success</button>
          <button type="button" class="btn btn-outline-danger">Danger</button>
          <button type="button" class="btn btn-outline-warning">Warning</button>
          <button type="button" class="btn btn-outline-info">Info</button>
          <button type="button" class="btn btn-outline-dark">Dark</button>
        </div>
      </div>
      <hr class="m-0" />
      <div class="card-body">
        <small class="text-light fw-semibold">Rounded</small>
        <div class="demo-inline-spacing">
          <button type="button" class="btn rounded-pill btn-outline-primary">Primary</button>
          <button type="button" class="btn rounded-pill btn-outline-secondary">Secondary</button>
          <button type="button" class="btn rounded-pill btn-outline-success">Success</button>
          <button type="button" class="btn rounded-pill btn-outline-danger">Danger</button>
          <button type="button" class="btn rounded-pill btn-outline-warning">Warning</button>
          <button type="button" class="btn rounded-pill btn-outline-info">Info</button>
          <button type="button" class="btn rounded-pill btn-outline-dark">Dark</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Buttons with Icons -->


  <!-- Text Buttons -->

  <div class="col-12">
    <div class="card mb-4">
      <h5 class="card-header">Text Buttons</h5>
      <div class="card-body">
        <div class="demo-inline-spacing">
          <button type="button" class="btn btn-text-primary">Primary</button>
          <button type="button" class="btn btn-text-secondary">Secondary</button>
          <button type="button" class="btn btn-text-success">Success</button>
          <button type="button" class="btn btn-text-danger">Danger</button>
          <button type="button" class="btn btn-text-warning">Warning</button>
          <button type="button" class="btn btn-text-info">Info</button>
          <button type="button" class="btn btn-text-dark">Dark</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Buttons with Icons -->

  <div class="col-12">
    <div class="card mb-4">
      <h5 class="card-header">Buttons with Icons</h5>
      <div class="row row-bordered g-0">
        <div class="col-lg-4 p-4">
          <small class="text-light fw-semibold">Basic</small>
          <div class="demo-inline-spacing">
            <button type="button" class="btn btn-primary">
              <span class="tf-icons mdi mdi-checkbox-marked-circle-outline me-1"></span>Primary
            </button>
            <button type="button" class="btn btn-secondary">
              <span class="tf-icons mdi mdi-bell-outline me-1"></span>Secondary
            </button>
          </div>
          <div class="demo-inline-spacing">
            <button type="button" class="btn rounded-pill btn-primary">
              <span class="tf-icons mdi mdi-checkbox-marked-circle-outline me-1"></span>Primary
            </button>
            <button type="button" class="btn rounded-pill btn-secondary">
              <span class="tf-icons mdi mdi-bell-outline me-1"></span>Secondary
            </button>
          </div>
        </div>
        <div class="col-lg-4 p-4">
          <small class="text-light fw-semibold">Label(Tonal)</small>
          <div class="demo-inline-spacing">
            <button type="button" class="btn btn-label-primary">
              <span class="tf-icons mdi mdi-checkbox-marked-circle-outline me-1"></span>Primary
            </button>
            <button type="button" class="btn btn-label-secondary">
              <span class="tf-icons mdi mdi-bell-outline me-1"></span>Secondary
            </button>
          </div>
          <div class="demo-inline-spacing">
            <button type="button" class="btn rounded-pill btn-label-primary">
              <span class="tf-icons mdi mdi-checkbox-marked-circle-outline me-1"></span>Primary
            </button>
            <button type="button" class="btn rounded-pill btn-label-secondary">
              <span class="tf-icons mdi mdi-bell-outline me-1"></span>Secondary
            </button>
          </div>
        </div>
        <div class="col-lg-4 p-4">
          <small class="text-light fw-semibold">Outline</small>
          <div class="demo-inline-spacing">
            <button type="button" class="btn btn-outline-primary">
              <span class="tf-icons mdi mdi-checkbox-marked-circle-outline me-1"></span>Primary
            </button>
            <button type="button" class="btn btn-outline-secondary">
              <span class="tf-icons mdi mdi-bell-outline me-1"></span>Secondary
            </button>
          </div>
          <div class="demo-inline-spacing">
            <button type="button" class="btn rounded-pill btn-outline-primary">
              <span class="tf-icons mdi mdi-checkbox-marked-circle-outline me-1"></span>Primary
            </button>
            <button type="button" class="btn rounded-pill btn-outline-secondary">
              <span class="tf-icons mdi mdi-bell-outline me-1"></span>Secondary
            </button>
          </div>
        </div>
      </div>
      <hr class="m-0" />
      <div class="row row-bordered g-0">
        <div class="col-lg-4 p-4">
          <small class="text-light fw-semibold">Basic</small>
          <div class="demo-inline-spacing">
            <button type="button" class="btn btn-icon btn-primary">
              <span class="tf-icons mdi mdi-checkbox-marked-circle-outline"></span>
            </button>
            <button type="button" class="btn btn-icon btn-secondary">
              <span class="tf-icons mdi mdi-bell-outline"></span>
            </button>
            <button type="button" class="btn rounded-pill btn-icon btn-primary">
              <span class="tf-icons mdi mdi-checkbox-marked-circle-outline"></span>
            </button>
            <button type="button" class="btn rounded-pill btn-icon btn-secondary">
              <span class="tf-icons mdi mdi-bell-outline"></span>
            </button>
          </div>
        </div>
        <div class="col-lg-4 p-4">
          <small class="text-light fw-semibold">Label(Tonal)</small>
          <div class="demo-inline-spacing">
            <button type="button" class="btn btn-icon btn-label-primary">
              <span class="tf-icons mdi mdi-checkbox-marked-circle-outline"></span>
            </button>
            <button type="button" class="btn btn-icon btn-label-secondary">
              <span class="tf-icons mdi mdi-bell-outline"></span>
            </button>
            <button type="button" class="btn rounded-pill btn-icon btn-label-primary">
              <span class="tf-icons mdi mdi-checkbox-marked-circle-outline"></span>
            </button>
            <button type="button" class="btn rounded-pill btn-icon btn-label-secondary">
              <span class="tf-icons mdi mdi-bell-outline"></span>
            </button>
          </div>
        </div>
        <div class="col-lg-4 p-4">
          <small class="text-light fw-semibold">Outline</small>
          <div class="demo-inline-spacing">
            <button type="button" class="btn btn-icon btn-outline-primary">
              <span class="tf-icons mdi mdi-checkbox-marked-circle-outline"></span>
            </button>
            <button type="button" class="btn btn-icon btn-outline-secondary">
              <span class="tf-icons mdi mdi-bell-outline"></span>
            </button>
            <button type="button" class="btn rounded-pill btn-icon btn-outline-primary">
              <span class="tf-icons mdi mdi-checkbox-marked-circle-outline"></span>
            </button>
            <button type="button" class="btn rounded-pill btn-icon btn-outline-secondary">
              <span class="tf-icons mdi mdi-bell-outline"></span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Floating Action Buttons -->

  <div class="col-12">
    <div class="card mb-4">
      <h5 class="card-header">Floating Action Buttons</h5>
      <div class="row row-bordered g-0">
        <div class="col-lg-6 p-4">
          <small class="text-light fw-semibold">Basic</small>
          <div class="demo-inline-spacing">
            <button type="button" class="btn btn-icon btn-primary btn-fab demo">
              <span class="tf-icons mdi mdi-checkbox-marked-circle-outline mdi-24px"></span>
            </button>
            <button type="button" class="btn btn-icon btn-secondary btn-fab demo">
              <span class="tf-icons mdi mdi-bell-outline mdi-24px"></span>
            </button>
            <button type="button" class="btn rounded-pill btn-icon btn-primary btn-fab demo">
              <span class="tf-icons mdi mdi-checkbox-marked-circle-outline mdi-24px"></span>
            </button>
            <button type="button" class="btn rounded-pill btn-icon btn-secondary btn-fab demo">
              <span class="tf-icons mdi mdi-bell-outline mdi-24px"></span>
            </button>
          </div>
        </div>
        <div class="col-lg-6 p-4">
          <small class="text-light fw-semibold">Label(Tonal)</small>
          <div class="demo-inline-spacing">
            <button type="button" class="btn btn-icon btn-label-primary btn-fab demo">
              <span class="tf-icons mdi mdi-checkbox-marked-circle-outline mdi-24px"></span>
            </button>
            <button type="button" class="btn btn-icon btn-label-secondary btn-fab demo">
              <span class="tf-icons mdi mdi-bell-outline mdi-24px"></span>
            </button>
            <button type="button" class="btn rounded-pill btn-icon btn-label-primary btn-fab demo">
              <span class="tf-icons mdi mdi-checkbox-marked-circle-outline mdi-24px"></span>
            </button>
            <button type="button" class="btn rounded-pill btn-icon btn-label-secondary btn-fab demo">
              <span class="tf-icons mdi mdi-bell-outline mdi-24px"></span>
            </button>
          </div>
        </div>
      </div>
      <hr class="m-0" />
      <div class="row row-bordered g-0">
        <div class="col-lg-6 p-4">
          <small class="text-light fw-semibold">Extended Basic</small>
          <div class="demo-inline-spacing">
            <button type="button" class="btn btn-primary btn-fab demo">
              <span class="tf-icons mdi mdi-checkbox-marked-circle-outline me-1"></span>Primary
            </button>
            <button type="button" class="btn btn-secondary btn-fab demo">
              <span class="tf-icons mdi mdi-bell-outline me-1"></span>Secondary
            </button>
          </div>
          <div class="demo-inline-spacing">
            <button type="button" class="btn rounded-pill btn-primary btn-fab demo">
              <span class="tf-icons mdi mdi-checkbox-marked-circle-outline me-1"></span>Primary
            </button>
            <button type="button" class="btn rounded-pill btn-secondary btn-fab demo">
              <span class="tf-icons mdi mdi-bell-outline me-1"></span>Secondary
            </button>
          </div>
        </div>
        <div class="col-lg-6 p-4">
          <small class="text-light fw-semibold">Extended Label</small>
          <div class="demo-inline-spacing">
            <button type="button" class="btn btn-label-primary btn-fab demo">
              <span class="tf-icons mdi mdi-checkbox-marked-circle-outline me-1"></span>Primary
            </button>
            <button type="button" class="btn btn-label-secondary btn-fab demo">
              <span class="tf-icons mdi mdi-bell-outline me-1"></span>Secondary
            </button>
          </div>
          <div class="demo-inline-spacing">
            <button type="button" class="btn rounded-pill btn-label-primary btn-fab demo">
              <span class="tf-icons mdi mdi-checkbox-marked-circle-outline me-1"></span>Primary
            </button>
            <button type="button" class="btn rounded-pill btn-label-secondary btn-fab demo">
              <span class="tf-icons mdi mdi-bell-outline me-1"></span>Secondary
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Social Button -->

  <div class="col-12">
    <div class="card mb-4">
      <h5 class="card-header">Social Button</h5>
      <div class="row row-bordered g-0">
        <div class="col-lg-4 p-4">
          <small class="text-light fw-semibold">Basic</small>
          <div class="demo-inline-spacing">
            <button type="button" class="btn btn-facebook"><i class="tf-icons mdi mdi-facebook me-1"></i>Facebook</button>
            <button type="button" class="btn btn-twitter"><i class="tf-icons mdi mdi-twitter me-1"></i>Twitter</button>
          </div>
          <div class="demo-inline-spacing">
            <button type="button" class="btn rounded-pill btn-google-plus">
              <i class="tf-icons mdi mdi-google-plus me-1"></i>Google+
            </button>
            <button type="button" class="btn rounded-pill btn-instagram">
              <i class="tf-icons mdi mdi-instagram me-1"></i>Instagram
            </button>
          </div>
        </div>
        <div class="col-lg-4 p-4">
          <small class="text-light fw-semibold">Label(Tonal)</small>
          <div class="demo-inline-spacing">
            <button type="button" class="btn btn-label-linkedin"><i class="tf-icons mdi mdi-linkedin me-1"></i>Linkedin</button>
            <button type="button" class="btn btn-label-github"><i class="tf-icons mdi mdi-github me-1"></i>Github</button>
          </div>
          <div class="demo-inline-spacing">
            <button type="button" class="btn rounded-pill btn-label-pinterest"> <i class="tf-icons mdi mdi-pinterest me-1"></i>Pinterest </button>
            <button type="button" class="btn rounded-pill btn-label-slack"> <i class="tf-icons mdi mdi-slack me-1"></i>Slack </button>
          </div>
        </div>
        <div class="col-lg-4 p-4">
          <small class="text-light fw-semibold">Outline & Text</small>
          <div class="demo-inline-spacing">
            <button type="button" class="btn btn-outline-whatsapp"> <i class="tf-icons mdi mdi-whatsapp me-1"></i>WhatsApp</button>
            <button type="button" class="btn btn-text-reddit"><i class="tf-icons mdi mdi-reddit me-1"></i>Reddit</button>
          </div>
          <div class="demo-inline-spacing">
            <button type="button" class="btn rounded-pill btn-outline-youtube"> <i class="tf-icons mdi mdi-youtube me-1"></i>Youtube </button>
            <button type="button" class="btn rounded-pill btn-text-vimeo"> <i class="tf-icons mdi mdi-vimeo me-1"></i>Vimeo </button>
          </div>
        </div>
      </div>
      <hr class="m-0" />
      <div class="row row-bordered g-0">
        <div class="col-lg-4 p-4">
          <small class="text-light fw-semibold">Basic Icons</small>
          <div class="demo-inline-spacing">
            <button type="button" class="btn btn-icon btn-facebook"><i class="tf-icons mdi mdi-facebook"></i></button>
            <button type="button" class="btn btn-icon btn-twitter"><i class="tf-icons mdi mdi-twitter"></i></button>
            <button type="button" class="btn btn-icon rounded-pill btn-google-plus">
              <i class="tf-icons mdi mdi-google-plus"></i>
            </button>
            <button type="button" class="btn btn-icon rounded-pill btn-instagram">
              <i class="tf-icons mdi mdi-instagram"></i>
            </button>
          </div>
        </div>
        <div class="col-lg-4 p-4">
          <small class="text-light fw-semibold">Label(Tonal) Icons</small>
          <div class="demo-inline-spacing">
            <button type="button" class="btn btn-icon btn-label-linkedin"><i class="tf-icons mdi mdi-linkedin"></i></button>
            <button type="button" class="btn btn-icon btn-label-github"><i class="tf-icons mdi mdi-github"></i></button>
            <button type="button" class="btn btn-icon rounded-pill btn-label-pinterest">
              <i class="tf-icons mdi mdi-pinterest"></i>
            </button>
            <button type="button" class="btn btn-icon rounded-pill btn-label-slack">
              <i class="tf-icons mdi mdi-slack"></i>
            </button>
          </div>
        </div>
        <div class="col-lg-4 p-4">
          <small class="text-light fw-semibold">Outline & Text Icons</small>
          <div class="demo-inline-spacing">
            <button type="button" class="btn btn-icon btn-outline-whatsapp">
              <i class="tf-icons mdi mdi-whatsapp"></i>
            </button>
            <button type="button" class="btn btn-icon btn-text-reddit"><i class="tf-icons mdi mdi-reddit"></i></button>
            <button type="button" class="btn btn-icon rounded-pill btn-outline-youtube">
              <i class="tf-icons mdi mdi-youtube"></i>
            </button>
            <button type="button" class="btn btn-icon rounded-pill btn-text-vimeo">
              <i class="tf-icons mdi mdi-vimeo"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Button Options -->
  <div class="col-12">
    <div class="card mb-4">
      <h5 class="card-header">Button Options</h5>
      <div class="card-body">
        <small class="text-light fw-semibold">Sizes</small>
        <div class="demo-inline-spacing">
          <button type="button" class="btn btn-xl btn-primary">Button xl</button>
          <button type="button" class="btn btn-lg btn-primary">Button lg</button>
          <button type="button" class="btn btn-primary">Button</button>
          <button type="button" class="btn btn-sm btn-primary">Button sm</button>
          <button type="button" class="btn btn-xs btn-primary">Button xs</button>
        </div>
      </div>
      <hr class="m-0" />
      <div class="card-body">
        <small class="text-light fw-semibold">Buttons State</small>
        <div class="demo-inline-spacing">
          <button type="button" class="btn btn-primary">Normal</button>
          <button type="button" class="btn btn-primary active">Active</button>
          <button type="button" class="btn btn-primary" disabled>Disabled</button>
        </div>
      </div>
      <hr class="m-0" />
      <div class="card-body">
        <small class="text-light fw-semibold">Block level buttons</small>
        <div class="row mt-3">
          <div class="d-grid gap-2 col-lg-6 mx-auto">
            <button class="btn btn-primary btn-lg" type="button">Button</button>
            <button class="btn btn-secondary btn-lg" type="button">Button</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Button Plugin -->
  <div class="col-12">
    <div class="card mb-4">
      <h5 class="card-header">Button Plugin</h5>
      <div class="row row-bordered g-0">
        <div class="col-xl-3 p-4">
          <div class="text-light small fw-semibold">Toggle states</div>
          <div class="demo-vertical-spacing">
            <button type="button" class="btn btn-primary d-block" data-bs-toggle="button">Toggle button</button>
            <button type="button" class="btn btn-primary active d-block" data-bs-toggle="button" aria-pressed="true">Active toggle button</button>
            <button type="button" class="btn btn-primary d-block" disabled data-bs-toggle="button">Disabled toggle button</button>
          </div>
        </div>
        <div class="col-xl-3 p-4">
          <div class="text-light small fw-semibold">Checkbox toggle buttons</div>
          <div class="demo-vertical-spacing">
            <div class="d-block">
              <input type="checkbox" class="btn-check" id="btn-check">
              <label class="btn btn-primary" for="btn-check">Single toggle</label>
            </div>
            <div class="d-block">
              <input type="checkbox" class="btn-check" id="btn-check-2" checked>
              <label class="btn btn-primary" for="btn-check-2">Checked</label>
            </div>
            <div class="d-block">
              <input type="checkbox" class="btn-check" id="btn-check-3" checked>
              <label class="btn btn-primary" for="btn-check-3">Checked</label>
            </div>
          </div>
        </div>
        <div class="col-xl-6 p-4">
          <div class="text-light small fw-semibold">Checkbox and radio</div>

          <div class="demo-vertical-spacing">
            <!-- Checkbox -->

            <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
              <input type="checkbox" class="btn-check" id="btncheck1" checked>
              <label class="btn btn-outline-primary" for="btncheck1"><span class="d-block d-sm-none"><i class="mdi mdi-home-outline mdi-20px"></i></span><span class="d-none d-sm-block"> Checkbox 1(pre-checked)</span></label>

              <input type="checkbox" class="btn-check" id="btncheck2">
              <label class="btn btn-outline-primary" for="btncheck2"><span class="d-block d-sm-none"><i class="mdi mdi-airplane mdi-20px"></i></span><span class="d-none d-sm-block">Checkbox 2</span></label>

              <input type="checkbox" class="btn-check" id="btncheck3">
              <label class="btn btn-outline-primary" for="btncheck3"><span class="d-block d-sm-none"><i class="mdi mdi-bell-outline mdi-20px"></i></span><span class="d-none d-sm-block">Checkbox 3</span></label>
            </div>


            <br />

            <!-- Radio -->

            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
              <input type="radio" class="btn-check" name="btnradio" id="btnradio1" checked>
              <label class="btn btn-outline-primary" for="btnradio1">Radio 1</label>

              <input type="radio" class="btn-check" name="btnradio" id="btnradio2">
              <label class="btn btn-outline-primary" for="btnradio2">Radio 2</label>

              <input type="radio" class="btn-check" name="btnradio" id="btnradio3">
              <label class="btn btn-outline-primary" for="btnradio3">Radio 3</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Button Group -->
  <div class="col-12">
    <div class="card">
      <h5 class="card-header">Button Group</h5>
      <div class="card-body">
        <div class="row g-4">
          <div class="col-md-6 col-lg-4">
            <small class="text-light fw-semibold">Basic</small>
            <div class="mt-3">
              <div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" class="btn btn-secondary">Left</button>
                <button type="button" class="btn btn-secondary">Middle</button>
                <button type="button" class="btn btn-secondary">Right</button>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <small class="text-light fw-semibold">Label(Tonal)</small>
            <div class="mt-3">
              <div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" class="btn btn-label-secondary">Left</button>
                <button type="button" class="btn btn-label-secondary">Middle</button>
                <button type="button" class="btn btn-label-secondary">Right</button>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <small class="text-light fw-semibold">Outline</small>
            <div class="mt-3">
              <div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" class="btn btn-outline-secondary">Left</button>
                <button type="button" class="btn btn-outline-secondary">Middle</button>
                <button type="button" class="btn btn-outline-secondary">Right</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr class="m-0" />
      <div class="card-body">
        <div class="row">
          <div class="col-xl-6 mb-xl-0 mb-3">
            <small class="text-light fw-semibold">Button Toolbar</small>
            <div class="btn-toolbar demo-inline-spacing" role="toolbar" aria-label="Toolbar with button groups">
              <div class="btn-group" role="group" aria-label="First group">
                <button type="button" class="btn btn-outline-secondary"><i class="tf-icons mdi mdi-bell-outline"></i></button>
                <button type="button" class="btn btn-outline-secondary"><i class="tf-icons mdi mdi-calendar-blank-outline"></i></button>
                <button type="button" class="btn btn-outline-secondary"><i class="tf-icons mdi mdi-shield-check-outline"></i></button>
                <button type="button" class="btn btn-outline-secondary"><i class="tf-icons mdi mdi-chat-processing-outline"></i></button>
              </div>
              <div class="btn-group" role="group" aria-label="Second group">
                <button type="button" class="btn btn-outline-secondary"><i class="tf-icons mdi mdi-format-bold"></i></button>
                <button type="button" class="btn btn-outline-secondary"><i class="tf-icons mdi mdi-format-italic"></i></button>
                <button type="button" class="btn btn-outline-secondary"><i class="tf-icons mdi mdi-format-underline"></i></button>
              </div>
              <div class="btn-group" role="group" aria-label="Third group">
                <button type="button" class="btn btn-outline-secondary"><i class="tf-icons mdi mdi-volume-high"></i></button>
              </div>
            </div>
          </div>
          <div class="col-xl-6">
            <small class="text-light fw-semibold">Button Nesting</small>
            <div class="mt-3">
              <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <button type="button" class="btn btn-outline-secondary"><i class="tf-icons mdi mdi-car-outline"></i></button>
                <button type="button" class="btn btn-outline-secondary"><i class="tf-icons mdi mdi-rocket-launch-outline"></i></button>
                <button type="button" class="btn btn-outline-secondary"><i class="tf-icons mdi mdi-lightbulb-outline"></i></button>
                <div class="btn-group" role="group">
                  <button id="btnGroupDrop1" type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="mdi mdi-dots-vertical mdi-20px"></i><span class="d-none d-sm-block">Dropdown</span></button>
                  <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                    <a class="dropdown-item" href="javascript:void(0);">Dropdown link</a>
                    <a class="dropdown-item" href="javascript:void(0);">Dropdown link</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
