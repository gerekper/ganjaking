@extends('layouts/layoutMaster')

@section('title', 'Sticky Actions - Forms')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/jquery-sticky/jquery-sticky.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/form-layouts.js')}}"></script>
@endsection

@section('content')

<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Forms/</span>
  Sticky Actions
</h4>
<!-- Sticky Actions -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header sticky-element bg-label-secondary d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row">
        <h5 class="card-title mb-sm-0 me-2">Sticky Action Bar</h5>
        <div class="action-btns">
          <button class="btn btn-label-primary me-3">
            <span class="align-middle"> Back</span>
          </button>
          <button class="btn btn-primary">
            Place Order
          </button>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-lg-8 mx-auto">
            <!-- 1. Delivery Address -->
            <h5 class="mb-4">1. Delivery Address</h5>
            <div class="row g-4">
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="fullname" class="form-control" placeholder="John Doe" />
                  <label for="fullname">Full Name</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input-group input-group-merge">
                  <div class="form-floating form-floating-outline">
                    <input class="form-control" type="text" id="email" name="email" placeholder="john.doe" aria-label="john.doe" aria-describedby="email3" />
                    <label for="email">Email</label>
                  </div>
                  <span class="input-group-text" id="email3">@example.com</span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="phone" class="form-control phone-mask" placeholder="658 799 8941" aria-label="658 799 8941" />
                  <label for="phone">Contact Number</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="alt-num" class="form-control phone-mask" placeholder="658 799 8941" />
                  <label for="alt-num">Alternate Number</label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-floating form-floating-outline">
                  <textarea name="address" class="form-control" id="address" rows="2" placeholder="1456, Mall Road" style="height: 65px;"></textarea>
                  <label for="address">Address</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="pincode" class="form-control" placeholder="658468" />
                  <label for="pincode">Pincode</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="landmark" class="form-control" placeholder="Nr. Wall Street" />
                  <label for="landmark">Landmark</label>
                </div>
              </div>
              <div class="col-md">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="city" class="form-control" placeholder="Jackson" />
                  <label for="city">City</label>
                </div>
              </div>
              <div class="col-md">
                <div class="form-floating form-floating-outline">
                  <select id="state" class="select2 form-select" data-allow-clear="true">
                    <option value="">Select</option>
                    <option value="AL">Alabama</option>
                    <option value="AK">Alaska</option>
                    <option value="AZ">Arizona</option>
                    <option value="AR">Arkansas</option>
                    <option value="CA">California</option>
                    <option value="CO">Colorado</option>
                    <option value="CT">Connecticut</option>
                    <option value="DE">Delaware</option>
                    <option value="DC">District Of Columbia</option>
                    <option value="FL">Florida</option>
                    <option value="GA">Georgia</option>
                    <option value="HI">Hawaii</option>
                    <option value="ID">Idaho</option>
                    <option value="IL">Illinois</option>
                    <option value="IN">Indiana</option>
                    <option value="IA">Iowa</option>
                    <option value="KS">Kansas</option>
                    <option value="KY">Kentucky</option>
                    <option value="LA">Louisiana</option>
                    <option value="ME">Maine</option>
                    <option value="MD">Maryland</option>
                    <option value="MA">Massachusetts</option>
                    <option value="MI">Michigan</option>
                    <option value="MN">Minnesota</option>
                    <option value="MS">Mississippi</option>
                    <option value="MO">Missouri</option>
                    <option value="MT">Montana</option>
                    <option value="NE">Nebraska</option>
                    <option value="NV">Nevada</option>
                    <option value="NH">New Hampshire</option>
                    <option value="NJ">New Jersey</option>
                    <option value="NM">New Mexico</option>
                    <option value="NY">New York</option>
                    <option value="NC">North Carolina</option>
                    <option value="ND">North Dakota</option>
                    <option value="OH">Ohio</option>
                    <option value="OK">Oklahoma</option>
                    <option value="OR">Oregon</option>
                    <option value="PA">Pennsylvania</option>
                    <option value="RI">Rhode Island</option>
                    <option value="SC">South Carolina</option>
                    <option value="SD">South Dakota</option>
                    <option value="TN">Tennessee</option>
                    <option value="TX">Texas</option>
                    <option value="UT">Utah</option>
                    <option value="VT">Vermont</option>
                    <option value="VA">Virginia</option>
                    <option value="WA">Washington</option>
                    <option value="WV">West Virginia</option>
                    <option value="WI">Wisconsin</option>
                    <option value="WY">Wyoming</option>
                  </select>
                  <label for="state">State</label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="" id="deliveryAdd" checked="">
                  <label class="form-check-label" for="deliveryAdd"> Use this as default delivery address </label>
                </div>
              </div>

              <label class="form-check-label">Address Type</label>
              <div class="col mt-2">
                <div class="form-check form-check-inline">
                  <input name="collapsible-address-type" class="form-check-input" type="radio" value="" id="collapsible-address-type-home" checked="" />
                  <label class="form-check-label" for="collapsible-address-type-home">Home (All day delivery)</label>
                </div>
                <div class="form-check form-check-inline">
                  <input name="collapsible-address-type" class="form-check-input" type="radio" value="" id="collapsible-address-type-office" />
                  <label class="form-check-label" for="collapsible-address-type-office"> Office (Delivery between 10 AM - 5 PM) </label>
                </div>
              </div>
            </div>
            <hr>
            <!-- 2. Delivery Type -->
            <h5 class="my-4">2. Delivery Type</h5>
            <div class="row gy-3">
              <div class="col-md">
                <div class="form-check custom-option custom-option-icon">
                  <label class="form-check-label custom-option-content" for="customRadioIcon1">
                    <span class="custom-option-body">
                      <i class='mdi mdi-briefcase-account-outline'></i>
                      <span class="custom-option-title"> Standard </span>
                      <small> Delivery in 3-5 days. </small>
                    </span>
                    <input name="customRadioIcon" class="form-check-input" type="radio" value="" id="customRadioIcon1" checked />
                  </label>
                </div>
              </div>
              <div class="col-md">
                <div class="form-check custom-option custom-option-icon">
                  <label class="form-check-label custom-option-content" for="customRadioIcon2">
                    <span class="custom-option-body">
                      <i class='mdi mdi-send-outline'></i>
                      <span class="custom-option-title"> Express </span>
                      <small>Delivery within 2 days.</small>
                    </span>
                    <input name="customRadioIcon" class="form-check-input" type="radio" value="" id="customRadioIcon2" />
                  </label>
                </div>
              </div>
              <div class="col-md">
                <div class="form-check custom-option custom-option-icon">
                  <label class="form-check-label custom-option-content" for="customRadioIcon3">
                    <span class="custom-option-body">
                      <i class='mdi mdi-crown-outline'></i>
                      <span class="custom-option-title"> Overnight </span>
                      <small> Delivery within a days. </small>
                    </span>
                    <input name="customRadioIcon" class="form-check-input" type="radio" value="" id="customRadioIcon3" />
                  </label>
                </div>
              </div>
            </div>
            <hr>
            <!-- 3. Apply Promo code -->
            <h5 class="my-4">3. Apply Promo code</h5>
            <div class="row g-3">

              <div class="col-lg-11 col-sm-10 col-8">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="promo-code" class="form-control" placeholder="TAKEITALL">
                  <label for="promo-code">Promo</label>
                </div>
              </div>
              <div class="col-lg-1 col-sm-2 col-4">
                <button class="btn btn-primary">Apply</button>
              </div>

              <div class="divider divider-dashed">
                <div class="divider-text">OR</div>
              </div>

              <div class="col-12">
                <ul class="list-group">
                  <li class="list-group-item d-flex justify-content-between flex-column flex-sm-row">
                    <div class="offer">
                      <p class="mb-0"><strong>TAKEITALL</strong></p>
                      <span>Apply this code to get 15% discount on orders above 20$.</span>
                    </div>
                    <div class="apply mt-3 mt-sm-0"><button class="btn btn-outline-primary">Apply</button></div>
                  </li>
                  <li class="list-group-item d-flex justify-content-between flex-column flex-sm-row">
                    <div class="offer">
                      <p class="mb-0"><strong>FESTIVE10</strong></p>
                      <span>Apply this code to get 10% discount on all orders.</span>
                    </div>
                    <div class="apply mt-3 mt-sm-0"><button class="btn btn-outline-primary">Apply</button></div>
                  </li>
                  <li class="list-group-item d-flex justify-content-between flex-column flex-sm-row">
                    <div class="offer">
                      <p class="mb-0"><strong>MYSTERYDEAL</strong></p>
                      <span>Apply this code to get discount between 10% - 50%.</span>
                    </div>
                    <div class="apply mt-3 mt-sm-0"><button class="btn btn-outline-primary">Apply</button></div>
                  </li>
                </ul>
              </div>
            </div>
            <hr>
            <!-- 4. Payment Method -->
            <h5 class="my-4">4. Payment Method</h5>
            <div class="row g-3">
              <div class="mb-3">
                <div class="form-check form-check-inline">
                  <input name="collapsible-payment" class="form-check-input" type="radio" value="" id="collapsible-payment-cc" checked="" />
                  <label class="form-check-label" for="collapsible-payment-cc">
                    Credit/Debit/ATM Card <i class="mdi mdi-card-bulleted-outline"></i>
                  </label>
                </div>
                <div class="form-check form-check-inline">
                  <input name="collapsible-payment" class="form-check-input" type="radio" value="" id="collapsible-payment-cash" />
                  <label class="form-check-label" for="collapsible-payment-cash">
                    Cash On Delivery
                    <i class="mdi mdi-help-circle-outline  text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="You can pay once you receive the product."></i>
                  </label>
                </div>
              </div>

              <div class="col-12 col-md-10 col-xxl-8">
                <div class="input-group input-group-merge mb-4">
                  <div class="form-floating form-floating-outline">
                    <input type="text" id="collapsible-payment-card" name="creditCardMask" class="form-control credit-card-mask" placeholder="1356 3215 6548 7898" aria-describedby="creditCardMask2" />
                    <label for="collapsible-payment-card">Card Number</label>
                  </div>
                  <span class="input-group-text cursor-pointer p-1" id="creditCardMask2"><span class="card-type"></span></span>
                </div>
                <div class="row">
                  <div class="col-12 col-md-6">
                    <div class="form-floating form-floating-outline mb-3">
                      <input type="text" id="collapsible-payment-name" class="form-control" placeholder="John Doe" />
                      <label for="collapsible-payment-name">Name</label>
                    </div>
                  </div>
                  <div class="col-6 col-md-3">
                    <div class="form-floating form-floating-outline mb-3">
                      <input type="text" id="collapsible-payment-expiry-date" class="form-control expiry-date-mask" placeholder="MM/YY" />
                      <label for="collapsible-payment-expiry-date">Exp. Date</label>
                    </div>
                  </div>
                  <div class="col-6 col-md-3">
                    <div class="input-group input-group-merge">
                      <div class="form-floating form-floating-outline">
                        <input type="text" id="collapsible-payment-cvv" class="form-control cvv-code-mask" maxlength="3" placeholder="654" />
                        <label for="collapsible-payment-cvv">CVV Code</label>
                      </div>
                      <span class="input-group-text cursor-pointer" id="collapsible-payment-cvv2"><i class="mdi mdi-help-circle-outline  text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Card Verification Value"></i></span>
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
</div>
<!-- /Sticky Actions -->
@endsection
