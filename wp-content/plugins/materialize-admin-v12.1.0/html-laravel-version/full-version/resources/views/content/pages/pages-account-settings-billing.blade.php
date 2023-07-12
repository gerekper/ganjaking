@php
$pricingModal = true;
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Account settings - Pages')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>

<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/pages-account-settings-billing.js')}}"></script>
<script src="{{asset('assets/js/app-invoice-list.js')}}"></script>
<script src="{{asset('assets/js/modal-edit-cc.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Account Settings /</span> Billings & Plans
</h4>

<div class="row">
  <div class="col-md-12">
    <ul class="nav nav-pills flex-column flex-md-row mb-3 gap-2 gap-lg-0">
      <li class="nav-item"><a class="nav-link" href="{{url('pages/account-settings-account')}}"><i class="mdi mdi-account-outline mdi-20px me-1"></i> Account</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('pages/account-settings-security')}}"><i class="mdi mdi-lock-open-outline mdi-20px me-1"></i> Security</a></li>
      <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i class="mdi mdi-bookmark-outline mdi-20px me-1"></i> Billing & Plans</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('pages/account-settings-notifications')}}"><i class="mdi mdi-bell-outline mdi-20px me-1"></i> Notifications</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('pages/account-settings-connections')}}"><i class="mdi mdi-link mdi-20px me-1"></i> Connections</a></li>
    </ul>
    <div class="card mb-4">
      <!-- Current Plan -->
      <h5 class="card-header">Current Plan</h5>
      <div class="card-body pt-1">
        <div class="row">
          <div class="col-md-6 mb-1">
            <div class="mb-4">
              <h6 class="mb-1 fw-semibold">Your Current Plan is Basic</h6>
              <p>A simple start for everyone</p>
            </div>
            <div class="mb-4">
              <h6 class="mb-1 fw-semibold">Active until Dec 09, 2021</h6>
              <p>We will send you a notification upon Subscription expiration</p>
            </div>
            <div>
              <h6 class="mb-1 fw-semibold"><span class="me-2">$199 Per Month</span> <span class="badge bg-label-primary rounded-pill">Popular</span></h6>
              <p class="mb-0">Standard plan for small to medium businesses</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="alert alert-warning mb-4 alert-dismissible" role="alert">
              <h6 class="alert-heading mb-1 d-flex align-items-end"><i class="mdi mdi-alert-outline mdi-20px me-2"></i>
                <span>We need your attention!</span>
              </h6>
              <span class="ms-4 ps-1">Your plan requires update</span>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
            </div>
            <div class="plan-statistics">
              <div class="d-flex justify-content-between">
                <h6 class="mb-2">Days</h6>
                <h6 class="mb-2">24 of 30 Days</h6>
              </div>
              <div class="progress rounded" style="height: 8px">
                <div class="progress-bar w-75 rounded" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <small class="mt-1 text-muted">6 days remaining until your plan requires update</small>
            </div>
          </div>
          <div class="col-12 d-flex gap-2 mt-4 flex-wrap">
            <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#pricingModal">Upgrade Plan</button>
            <button class="btn btn-outline-secondary cancel-subscription">Cancel Subscription</button>
          </div>
        </div>
      </div>


      <!-- /Current Plan -->
    </div>
    <div class="card mb-4">
      <h5 class="card-header">Payment Methods</h5>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <form id="creditCardForm" class="row g-4" onsubmit="return false">
              <div class="col-12">
                <div class="form-check form-check-inline">
                  <input name="collapsible-payment" class="form-check-input" type="radio" value="" id="collapsible-payment-cc" checked="" />
                  <label class="form-check-label" for="collapsible-payment-cc">Credit/Debit/ATM Card</label>
                </div>
                <div class="form-check form-check-inline">
                  <input name="collapsible-payment" class="form-check-input" type="radio" value="" id="collapsible-payment-cash" />
                  <label class="form-check-label" for="collapsible-payment-cash">Paypal account</label>
                </div>
              </div>
              <div class="col-12">
                <div class="input-group input-group-merge">
                  <div class="form-floating form-floating-outline">
                    <input id="paymentCard" name="paymentCard" class="form-control credit-card-mask" type="text" placeholder="1356 3215 6548 7898" aria-describedby="paymentCard2" />
                    <label for="paymentCard">Card Number</label>
                  </div>
                  <span class="input-group-text cursor-pointer p-1" id="paymentCard2"><span class="card-type"></span></span>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="paymentName" class="form-control" placeholder="John Doe" />
                  <label for="paymentName">Name</label>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="paymentExpiryDate" class="form-control expiry-date-mask" placeholder="MM/YY" />
                  <label for="paymentExpiryDate">Exp. Date</label>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="input-group input-group-merge">
                  <div class="form-floating form-floating-outline">
                    <input type="text" id="paymentCvv" class="form-control cvv-code-mask" maxlength="3" placeholder="654" />
                    <label for="paymentCvv">CVV Code</label>
                  </div>
                  <span class="input-group-text cursor-pointer" id="paymentCvv2"><i class="mdi mdi-help-circle-outline text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Card Verification Value"></i></span>
                </div>
              </div>
              <div class="col-12">
                <label class="switch">
                  <input type="checkbox" class="switch-input">
                  <span class="switch-toggle-slider">
                    <span class="switch-on"></span>
                    <span class="switch-off"></span>
                  </span>
                  <span class="switch-label">Save card for future billing?</span>
                </label>
              </div>
              <div class="col-12 mt-4">
                <button type="submit" class="btn btn-primary me-sm-3 me-1">Save Changes</button>
                <button type="reset" class="btn btn-outline-secondary">Cancel</button>
              </div>
            </form>
          </div>
          <div class="col-md-6 mt-5 mt-md-0">
            <h6>My Cards</h6>
            <div class="added-cards">
              <div class="cardMaster bg-lighter p-3 rounded mb-3">
                <div class="d-flex justify-content-between flex-sm-row flex-column">
                  <div class="card-information me-2">
                    <img class="mb-3 img-fluid" src="{{asset('assets/img/icons/payments/mastercard.png')}}" alt="Master Card">
                    <div class="d-flex align-items-center mb-1 flex-wrap gap-2">
                      <h6 class="mb-0 me-2 fw-semibold">Tom McBride</h6>
                      <span class="badge bg-label-primary rounded-pill">Primary</span>
                    </div>
                    <span class="card-number">&#8727;&#8727;&#8727;&#8727; &#8727;&#8727;&#8727;&#8727; 9856</span>
                  </div>
                  <div class="d-flex flex-column text-start text-lg-end">
                    <div class="d-flex order-sm-0 order-1 mt-sm-0 mt-3">
                      <button class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#editCCModal">Edit</button>
                      <button class="btn btn-outline-secondary">Delete</button>
                    </div>
                    <small class="mt-sm-auto mt-2 order-sm-1 order-0 text-muted">Card expires at 12/26</small>
                  </div>
                </div>
              </div>
              <div class="cardMaster bg-lighter p-3 rounded">
                <div class="d-flex justify-content-between flex-sm-row flex-column">
                  <div class="card-information me-2">
                    <img class="mb-3 img-fluid" src="{{asset('assets/img/icons/payments/visa.png')}}" alt="Visa Card">
                    <h6 class="mb-1 fw-semibold">Mildred Wagner</h6>
                    <span class="card-number">&#8727;&#8727;&#8727;&#8727; &#8727;&#8727;&#8727;&#8727; 5896</span>
                  </div>
                  <div class="d-flex flex-column text-start text-lg-end">
                    <div class="d-flex order-sm-0 order-1 mt-sm-0 mt-3">
                      <button class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#editCCModal">Edit</button>
                      <button class="btn btn-outline-secondary">Delete</button>
                    </div>
                    <small class="mt-sm-auto mt-2 order-sm-1 order-0 text-muted">Card expires at 10/27</small>
                  </div>
                </div>
              </div>
            </div>
            <!-- Modal -->
            @include('_partials/_modals/modal-edit-cc')
            <!--/ Modal -->
          </div>
        </div>
      </div>
    </div>
    <div class="card mb-4">
      <!-- Billing Address -->
      <h5 class="card-header">Billing Address</h5>
      <div class="card-body">
        <form id="formAccountSettings" onsubmit="return false">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-floating form-floating-outline mb-4">
                <input type="text" id="companyName" name="companyName" class="form-control" placeholder="{{ config('variables.creatorName') }}" />
                <label for="companyName">Company Name</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-floating form-floating-outline mb-4">
                <input class="form-control" type="text" id="billingEmail" name="billingEmail" placeholder="john.doe@example.com" />
                <label for="billingEmail">Billing Email</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-floating form-floating-outline mb-4">
                <input type="text" id="taxId" name="taxId" class="form-control" placeholder="Enter Tax ID" />
                <label for="taxId">Tax ID</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-floating form-floating-outline mb-4">
                <input class="form-control" type="text" id="vatNumber" name="vatNumber" placeholder="Enter VAT Number" />
                <label for="vatNumber">VAT Number</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="input-group input-group-merge mb-4">
                <div class="form-floating form-floating-outline">
                  <input class="form-control mobile-number" type="text" id="mobileNumber" name="mobileNumber" placeholder="202 555 0111" />
                  <label for="mobileNumber">Mobile</label>
                </div>
                <span class="input-group-text">US (+1)</span>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-floating form-floating-outline mb-4">
                <select id="country" class="form-select select2" name="country">
                  <option selected>USA</option>
                  <option>Canada</option>
                  <option>UK</option>
                  <option>Germany</option>
                  <option>France</option>
                </select>
                <label for="country">Country</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating form-floating-outline mb-4">
                <input type="text" class="form-control" id="billingAddress" name="billingAddress" placeholder="Billing Address" />
                <label for="billingAddress">Billing Address</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-floating form-floating-outline mb-4">
                <input class="form-control" type="text" id="state" name="state" placeholder="California" />
                <label for="state">State</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-floating form-floating-outline mb-4">
                <input type="text" class="form-control zip-code" id="zipCode" name="zipCode" placeholder="231465" maxlength="6" />
                <label for="zipCode">Zip Code</label>
              </div>
            </div>
          </div>
          <div class="mt-2">
            <button type="submit" class="btn btn-primary me-2">Save changes</button>
            <button type="reset" class="btn btn-outline-secondary">Discard</button>
          </div>
        </form>
      </div>
      <!-- /Billing Address -->
    </div>
    <div class="card">
      <!-- Billing History -->
      <h5 class="card-header">Billing History</h5>
      <div class="card-datatable table-responsive">
        <table class="invoice-list-table table">
          <thead class="table-light">
            <tr>
              <th></th>
              <th></th>
              <th>#ID</th>
              <th><i class='mdi mdi-trending-up'></i></th>
              <th>Client</th>
              <th>Total</th>
              <th class="text-truncate">Issued Date</th>
              <th>Balance</th>
              <th>Invoice Status</th>
              <th class="cell-fit">Actions</th>
            </tr>
          </thead>
        </table>
      </div>
      <!--/ Billing History -->
    </div>
  </div>
</div>

@endsection
