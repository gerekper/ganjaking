@php
$configData = Helper::appClasses();
@endphp
<!-- Create App Modal -->
<div class="modal fade" id="createApp" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-simple modal-upgrade-plan">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body p-1">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center">
          <h3 class="mb-2 pb-1">Create App</h3>
          <p class="mb-4">Provide data with this form to create your app.</p>
        </div>
        <!-- Property Listing Wizard -->
        <div id="wizard-create-app" class="bs-stepper vertical wizard-vertical-icons mt-2 shadow-none">
          <div class="bs-stepper-header border-0 p-1">
            <div class="step" data-target="#details">
              <button type="button" class="step-trigger">
                <span class="avatar">
                  <span class="avatar-initial rounded-2">
                    <i class="mdi mdi-content-paste mdi-24px"></i>
                  </span>
                </span>
                <span class="bs-stepper-label flex-column align-items-start gap-1 ps-1 ms-2">
                  <span class="bs-stepper-title text-uppercase fw-normal">Details</span>
                  <small class="bs-stepper-subtitle text-muted">Enter Details</small>
                </span>
              </button>
            </div>
            <div class="step" data-target="#frameworks">
              <button type="button" class="step-trigger">
                <span class="avatar">
                  <span class="avatar-initial rounded-2">
                    <i class="mdi mdi-star-outline mdi-24px"></i>
                  </span>
                </span>
                <span class="bs-stepper-label flex-column align-items-start gap-1 ps-1 ms-2">
                  <span class="bs-stepper-title text-uppercase fw-normal">Frameworks</span>
                  <small class="bs-stepper-subtitle text-muted">Select Framework</small>
                </span>
              </button>
            </div>
            <div class="step" data-target="#database">
              <button type="button" class="step-trigger">
                <span class="avatar">
                  <span class="avatar-initial rounded-2">
                    <i class="mdi mdi-chart-donut mdi-24px"></i>
                  </span>
                </span>
                <span class="bs-stepper-label flex-column align-items-start gap-1 ps-1 ms-2">
                  <span class="bs-stepper-title text-uppercase fw-normal">Database</span>
                  <small class="bs-stepper-subtitle text-muted">Select Database</small>
                </span>
              </button>
            </div>
            <div class="step" data-target="#billing">
              <button type="button" class="step-trigger">
                <span class="avatar">
                  <span class="avatar-initial rounded-2">
                    <i class="mdi mdi-credit-card-outline mdi-24px"></i>
                  </span>
                </span>
                <span class="bs-stepper-label flex-column align-items-start gap-1 ps-1 ms-2">
                  <span class="bs-stepper-title text-uppercase fw-normal">Billing</span>
                  <small class="bs-stepper-subtitle text-muted">Payment Details</small>
                </span>
              </button>
            </div>
            <div class="step" data-target="#submit">
              <button type="button" class="step-trigger">
                <span class="avatar">
                  <span class="avatar-initial rounded-2">
                    <i class="mdi mdi-check mdi-24px"></i>
                  </span>
                </span>
                <span class="bs-stepper-label flex-column align-items-start gap-1 ps-1 ms-2">
                  <span class="bs-stepper-title text-uppercase fw-normal">Submit</span>
                  <small class="bs-stepper-subtitle text-muted">Submit</small>
                </span>
              </button>
            </div>
          </div>
          <div class="bs-stepper-content p-1">
            <form onSubmit="return false">
              <!-- Details -->
              <div id="details" class="content pt-3 pt-lg-0">
                <div class="form-floating form-floating-outline mb-3">
                  <input type="email" class="form-control form-control-lg" id="modalAppName" placeholder="Application Name">
                  <label for="modalAppName">Application Name</label>
                </div>
                <h4>Category</h4>
                <ul class="p-0 m-0">
                  <li class="d-flex align-items-center mb-4">
                    <div class="avatar avatar-md bg-label-info d-flex align-items-center justify-content-center flex-shrink-0 me-3 rounded"><i class="mdi mdi-briefcase-outline mdi-24px"></i></div>
                    <div class="d-flex justify-content-between w-100">
                      <div class="me-2">
                        <p class="mb-0">CRM Application</p>
                        <small class="text-muted">Scales with any business</small>
                      </div>
                      <div class="d-flex align-items-center">
                        <div class="form-check form-check-inline">
                          <input name="details-radio" class="form-check-input" type="radio" value="" />
                        </div>
                      </div>
                    </div>
                  </li>
                  <li class="d-flex align-items-center mb-4">
                    <div class="avatar avatar-md bg-label-success d-flex align-items-center justify-content-center flex-shrink-0 me-3 rounded"><i class="mdi mdi-cart-outline mdi-24px"></i></div>
                    <div class="d-flex justify-content-between w-100">
                      <div class="me-2">
                        <p class="mb-0">eCommerce Platforms</p>
                        <small class="text-muted">Grow Your Business With App</small>
                      </div>
                      <div class="d-flex align-items-center">
                        <div class="form-check form-check-inline">
                          <input name="details-radio" class="form-check-input" type="radio" value="" checked />
                        </div>
                      </div>
                    </div>
                  </li>
                  <li class="d-flex align-items-center">
                    <div class="avatar avatar-md bg-label-danger d-flex align-items-center justify-content-center flex-shrink-0 me-3 rounded"><i class="mdi mdi-medal-outline mdi-24px"></i></div>
                    <div class="d-flex justify-content-between w-100">
                      <div class="me-2">
                        <p class="mb-0">Online Learning platform</p>
                        <small class="text-muted">Start learning today</small>
                      </div>
                      <div class="d-flex align-items-center">
                        <div class="form-check form-check-inline">
                          <input name="details-radio" class="form-check-input" type="radio" value="" />
                        </div>
                      </div>
                    </div>
                  </li>
                </ul>
                <div class="col-12 d-flex justify-content-between mt-4">
                  <button class="btn btn-outline-secondary btn-prev" disabled> <i class="mdi mdi-arrow-left me-2"></i>
                    <span class="align-middle">Previous</span>
                  </button>
                  <button class="btn btn-primary btn-next"> <span class="align-middle me-sm-1">Next</span> <i class="mdi mdi-arrow-right"></i></button>
                </div>
              </div>

              <!-- Frameworks -->
              <div id="frameworks" class="content pt-3 pt-lg-0">
                <h4>Select Framework</h4>
                <ul class="p-0 m-0">
                  <li class="d-flex align-items-center mb-4">
                    <div class="avatar avatar-md bg-label-info d-flex align-items-center justify-content-center flex-shrink-0 me-3 rounded"><i class="mdi mdi-react mdi-24px"></i></div>
                    <div class="d-flex justify-content-between w-100">
                      <div class="me-2">
                        <p class="mb-0">React Native</p>
                        <small class="text-muted">Create truly native apps</small>
                      </div>
                      <div class="d-flex align-items-center">
                        <div class="form-check form-check-inline">
                          <input name="frameworks-radio" class="form-check-input" type="radio" value="" />
                        </div>
                      </div>
                    </div>
                  </li>
                  <li class="d-flex align-items-center mb-4">
                    <div class="avatar avatar-md bg-label-danger d-flex align-items-center justify-content-center flex-shrink-0 me-3 rounded"><i class="mdi mdi-angular mdi-24px"></i></div>
                    <div class="d-flex justify-content-between w-100">
                      <div class="me-2">
                        <p class="mb-0">Angular</p>
                        <small class="text-muted">Most suited for your application</small>
                      </div>
                      <div class="d-flex align-items-center">
                        <div class="form-check form-check-inline">
                          <input name="frameworks-radio" class="form-check-input" type="radio" value="" checked="" />
                        </div>
                      </div>
                    </div>
                  </li>
                  <li class="d-flex align-items-center mb-4">
                    <div class="avatar avatar-md bg-label-warning d-flex align-items-center justify-content-center flex-shrink-0 me-3 rounded"><i class="mdi mdi-language-html5 mdi-24px"></i></div>
                    <div class="d-flex justify-content-between w-100">
                      <div class="me-2">
                        <p class="mb-0">HTML</p>
                        <small class="text-muted">Progressive Framework</small>
                      </div>
                      <div class="d-flex align-items-center">
                        <div class="form-check form-check-inline">
                          <input name="frameworks-radio" class="form-check-input" type="radio" value="" checked />
                        </div>
                      </div>
                    </div>
                  </li>
                  <li class="d-flex align-items-start">
                    <div class="avatar avatar-md bg-label-success d-flex align-items-center justify-content-center flex-shrink-0 me-3 rounded"><i class="mdi mdi-vuejs mdi-24px"></i></div>
                    <div class="d-flex justify-content-between w-100">
                      <div class="me-2">
                        <p class="mb-0">VueJs</p>
                        <small class="text-muted">JS web frameworks</small>
                      </div>
                      <div class="d-flex align-items-center">
                        <div class="form-check form-check-inline">
                          <input name="frameworks-radio" class="form-check-input" type="radio" value="" />
                        </div>
                      </div>
                    </div>
                  </li>
                </ul>

                <div class="col-12 d-flex justify-content-between mt-4">
                  <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-2"></i> <span class="align-middle">Previous</span> </button>
                  <button class="btn btn-primary btn-next"> <span class="align-middle me-sm-1">Next</span> <i class="mdi mdi-arrow-right"></i></button>
                </div>
              </div>

              <!-- Database -->
              <div id="database" class="content pt-3 pt-lg-0">
                <div class="form-floating form-floating-outline mb-3">
                  <input type="email" class="form-control form-control-lg" id="modalAppDatabaseName" placeholder="Database Name">
                  <label for="modalAppDatabaseName">Database Name</label>
                </div>
                <h4>Select Database Engine</h4>
                <ul class="p-0 m-0">
                  <li class="d-flex align-items-center mb-4">
                    <div class="avatar avatar-md d-flex align-items-center justify-content-center flex-shrink-0 bg-label-danger me-3 rounded"><i class="mdi mdi-firebase mdi-24px"></i></div>
                    <div class="d-flex justify-content-between w-100">
                      <div class="me-2">
                        <p class="mb-0">Firebase</p>
                        <small class="text-muted">Cloud Firestone</small>
                      </div>
                      <div class="d-flex align-items-center">
                        <div class="form-check form-check-inline">
                          <input name="database-radio" class="form-check-input" type="radio" value="" />
                        </div>
                      </div>
                    </div>
                  </li>
                  <li class="d-flex align-items-center mb-4">
                    <div class="avatar avatar-md d-flex align-items-center justify-content-center flex-shrink-0 bg-label-warning me-3 rounded"><i class="mdi mdi-aws mdi-24px"></i></div>
                    <div class="d-flex justify-content-between w-100">
                      <div class="me-2">
                        <p class="mb-0">AWS</p>
                        <small class="text-muted">Amazon Fast NoSQL Database</small>
                      </div>
                      <div class="d-flex align-items-center">
                        <div class="form-check form-check-inline">
                          <input name="database-radio" class="form-check-input" type="radio" value="" checked />
                        </div>
                      </div>
                    </div>
                  </li>
                  <li class="d-flex align-items-start">
                    <div class="avatar avatar-md d-flex align-items-center justify-content-center flex-shrink-0 bg-label-info me-3 rounded"><i class="mdi mdi-database mdi-24px"></i></div>
                    <div class="d-flex justify-content-between w-100">
                      <div class="me-2">
                        <p class="mb-0">MySQL</p>
                        <small class="text-muted">Basic MySQL database</small>
                      </div>
                      <div class="d-flex align-items-center">
                        <div class="form-check form-check-inline">
                          <input name="database-radio" class="form-check-input" type="radio" value="" />
                        </div>
                      </div>
                    </div>
                  </li>
                </ul>
                <div class="col-12 d-flex justify-content-between mt-4">
                  <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-2"></i> <span class="align-middle">Previous</span> </button>
                  <button class="btn btn-primary btn-next"> <span class="align-middle me-sm-1">Next</span> <i class="mdi mdi-arrow-right"></i></button>
                </div>
              </div>

              <!-- billing -->
              <div id="billing" class="content">
                <div id="AppNewCCForm" class="row g-4 pt-3 pt-lg-0 mb-4" onsubmit="return false">
                  <div class="col-12">
                    <div class="input-group input-group-merge">
                      <div class="form-floating form-floating-outline">
                        <input class="form-control app-credit-card-mask" id="modalAppAddCardNumber" type="text" placeholder="1356 3215 6548 7898" aria-describedby="modalAppAddCard" />
                        <label for="modalAppAddCardNumber">Card Number</label>
                      </div>
                      <span class="input-group-text cursor-pointer p-1" id="modalAppAddCard"><span class="app-card-type"></span></span>
                    </div>
                  </div>
                  <div class="col-12 col-md-6">
                    <div class="form-floating form-floating-outline">
                      <input type="text" class="form-control" id="modalAppAddCardName" placeholder="John Doe" />
                      <label for="modalAppAddCardName">Name</label>
                    </div>
                  </div>
                  <div class="col-6 col-md-3">
                    <div class="form-floating form-floating-outline">
                      <input type="text" class="form-control app-expiry-date-mask" id="modalAppAddCardDate" placeholder="MM/YY" />
                      <label for="modalAppAddCardDate">Exp. Date</label>
                    </div>
                  </div>
                  <div class="col-6 col-md-3">
                    <div class="input-group input-group-merge">
                      <div class="form-floating form-floating-outline">
                        <input type="text" id="modalAppAddCardCvv" class="form-control app-cvv-code-mask" maxlength="3" placeholder="654" />
                        <label for="modalAppAddCardCvv">CVV Code</label>
                      </div>
                      <span class="input-group-text cursor-pointer" id="modalAppAddCardCvv2"><i class="text-muted mdi mdi-help-circle-outline" data-bs-toggle="tooltip" data-bs-placement="top" title="Card Verification Value"></i></span>
                    </div>
                  </div>
                  <div class="col-12">
                    <label class="switch">
                      <input type="checkbox" class="switch-input" checked>
                      <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                      </span>
                      <span class="switch-label">Save card for future billing?</span>
                    </label>
                  </div>
                </div>
                <div class="col-12 d-flex justify-content-between mt-4">
                  <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-2"></i> <span class="align-middle">Previous</span> </button>
                  <button class="btn btn-primary btn-next"> <span class="align-middle me-sm-1">Next</span> <i class="mdi mdi-arrow-right"></i></button>
                </div>
              </div>

              <!-- submit -->
              <div id="submit" class="content text-center pt-3 pt-lg-0">
                <h4 class="mb-2 mt-3">Submit</h4>
                <p>Submit to kick start your project.</p>
                <!-- image -->
                <img src="{{ asset('assets/img/illustrations/create-app-modal-illustration-'.$configData['style'].'.png')}}" alt="Create App img" width="265" class="img-fluid" data-app-light-img="illustrations/create-app-modal-illustration-light.png" data-app-dark-img="illustrations/create-app-modal-illustration-dark.png">
                <div class="col-12 d-flex justify-content-between mt-4 pt-2">
                  <button class="btn btn-outline-secondary btn-prev"> <i class="mdi mdi-arrow-left me-2"></i> <span class="align-middle">Previous</span> </button>
                  <button class="btn btn-success btn-next btn-submit"><span class="align-middle">Submit</span><i class="mdi mdi-check ms-2"></i></button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!--/ Property Listing Wizard -->
    </div>
  </div>
</div>
<!--/ Create App Modal -->
