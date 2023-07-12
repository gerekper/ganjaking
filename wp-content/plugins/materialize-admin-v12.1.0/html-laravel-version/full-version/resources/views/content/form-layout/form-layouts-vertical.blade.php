@extends('layouts/layoutMaster')

@section('title', ' Vertical Layouts - Forms')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/form-layouts.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Forms/</span> Vertical Layouts</h4>

<!-- Basic Layout -->
<div class="row">
  <div class="col-xl">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Basic Layout</h5> <small class="text-muted float-end">Default label</small>
      </div>
      <div class="card-body">
        <form>
          <div class="form-floating form-floating-outline mb-4">
            <input type="text" class="form-control" id="basic-default-fullname" placeholder="John Doe" />
            <label for="basic-default-fullname">Full Name</label>
          </div>
          <div class="form-floating form-floating-outline mb-4">
            <input type="text" class="form-control" id="basic-default-company" placeholder="ACME Inc." />
            <label for="basic-default-company">Company</label>
          </div>
          <div class="mb-4">
            <div class="input-group input-group-merge">
              <div class="form-floating form-floating-outline">
                <input type="text" id="basic-default-email" class="form-control" placeholder="john.doe" aria-label="john.doe" aria-describedby="basic-default-email2" />
                <label for="basic-default-email">Email</label>
              </div>
              <span class="input-group-text" id="basic-default-email2">@example.com</span>
            </div>
            <div class="form-text"> You can use letters, numbers & periods </div>
          </div>
          <div class="form-floating form-floating-outline mb-4">
            <input type="text" id="basic-default-phone" class="form-control phone-mask" placeholder="658 799 8941" />
            <label for="basic-default-phone">Phone No</label>
          </div>
          <div class="form-floating form-floating-outline mb-4">
            <textarea id="basic-default-message" class="form-control" placeholder="Hi, Do you have a moment to talk Joe?" style="height: 60px;"></textarea>
            <label for="basic-default-message">Message</label>
          </div>
          <button type="submit" class="btn btn-primary">Send</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-xl">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Basic with Icons</h5>
        <small class="text-muted float-end">Merged input group</small>
      </div>
      <div class="card-body">
        <form>
          <div class="input-group input-group-merge mb-4">
            <span id="basic-icon-default-fullname2" class="input-group-text"><i class="mdi mdi-account-outline"></i></span>
            <div class="form-floating form-floating-outline">
              <input type="text" class="form-control" id="basic-icon-default-fullname" placeholder="John Doe" aria-label="John Doe" aria-describedby="basic-icon-default-fullname2" />
              <label for="basic-icon-default-fullname">Full Name</label>
            </div>
          </div>
          <div class="input-group input-group-merge mb-4">
            <span id="basic-icon-default-company2" class="input-group-text"><i class="mdi mdi-office-building-outline"></i></span>
            <div class="form-floating form-floating-outline">
              <input type="text" id="basic-icon-default-company" class="form-control" placeholder="ACME Inc." aria-label="ACME Inc." aria-describedby="basic-icon-default-company2" />
              <label for="basic-icon-default-company">Company</label>
            </div>
          </div>
          <div class="mb-4">
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="mdi mdi-email-outline"></i></span>
              <div class="form-floating form-floating-outline">
                <input type="text" id="basic-icon-default-email" class="form-control" placeholder="john.doe" aria-label="john.doe" aria-describedby="basic-icon-default-email2" />
                <label for="basic-icon-default-email">Email</label>
              </div>
              <span id="basic-icon-default-email2" class="input-group-text">@example.com</span>
            </div>
            <div class="form-text"> You can use letters, numbers & periods </div>
          </div>
          <div class="input-group input-group-merge mb-4">
            <span id="basic-icon-default-phone2" class="input-group-text"><i class="mdi mdi-phone"></i></span>
            <div class="form-floating form-floating-outline">
              <input type="text" id="basic-icon-default-phone" class="form-control phone-mask" placeholder="658 799 8941" aria-label="658 799 8941" aria-describedby="basic-icon-default-phone2" />
              <label for="basic-icon-default-phone">Phone No</label>
            </div>
          </div>
          <div class="input-group input-group-merge mb-4">
            <span id="basic-icon-default-message2" class="input-group-text"><i class="mdi mdi-message-outline"></i></span>
            <div class="form-floating form-floating-outline">
              <textarea id="basic-icon-default-message" class="form-control" placeholder="Hi, Do you have a moment to talk Joe?" aria-label="Hi, Do you have a moment to talk Joe?" aria-describedby="basic-icon-default-message2" style="height: 60px;"></textarea>
              <label for="basic-icon-default-message">Message</label>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Send</button>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- Multi Column with Form Separator -->
<div class="card mb-4">
  <h5 class="card-header">Multi Column with Form Separator</h5>
  <form class="card-body">
    <h6>1. Account Details</h6>
    <div class="row g-4">
      <div class="col-md-6">
        <div class="form-floating form-floating-outline">
          <input type="text" id="multicol-username" class="form-control" placeholder="john.doe" />
          <label for="multicol-username">Username</label>
        </div>
      </div>
      <div class="col-md-6">
        <div class="input-group input-group-merge">
          <div class="form-floating form-floating-outline">
            <input type="text" id="multicol-email" class="form-control" placeholder="john.doe" aria-label="john.doe" aria-describedby="multicol-email2" />
            <label for="multicol-email">Email</label>
          </div>
          <span class="input-group-text" id="multicol-email2">@example.com</span>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-password-toggle">
          <div class="input-group input-group-merge">
            <div class="form-floating form-floating-outline">
              <input type="password" id="multicol-password" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="multicol-password2" />
              <label for="multicol-password">Password</label>
            </div>
            <span class="input-group-text cursor-pointer" id="multicol-password2"><i class="mdi mdi-eye-off-outline"></i></span>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-password-toggle">
          <div class="input-group input-group-merge">
            <div class="form-floating form-floating-outline">
              <input type="password" id="multicol-confirm-password" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="multicol-confirm-password2" />
              <label for="multicol-confirm-password">Confirm Password</label>
            </div>
            <span class="input-group-text cursor-pointer" id="multicol-confirm-password2"><i class="mdi mdi-eye-off-outline"></i></span>
          </div>
        </div>
      </div>
    </div>
    <hr class="my-4 mx-n4" />
    <h6>2. Personal Info</h6>
    <div class="row g-3">
      <div class="col-md-6">
        <div class="form-floating form-floating-outline">
          <input type="text" id="multicol-first-name" class="form-control" placeholder="John" />
          <label for="multicol-first-name">First Name</label>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-floating form-floating-outline">
          <input type="text" id="multicol-last-name" class="form-control" placeholder="Doe" />
          <label for="multicol-last-name">Last Name</label>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-floating form-floating-outline">
          <select id="multicol-country" class="select2 form-select" data-allow-clear="true">
            <option value="">Select</option>
            <option value="Australia">Australia</option>
            <option value="Bangladesh">Bangladesh</option>
            <option value="Belarus">Belarus</option>
            <option value="Brazil">Brazil</option>
            <option value="Canada">Canada</option>
            <option value="China">China</option>
            <option value="France">France</option>
            <option value="Germany">Germany</option>
            <option value="India">India</option>
            <option value="Indonesia">Indonesia</option>
            <option value="Israel">Israel</option>
            <option value="Italy">Italy</option>
            <option value="Japan">Japan</option>
            <option value="Korea">Korea, Republic of</option>
            <option value="Mexico">Mexico</option>
            <option value="Philippines">Philippines</option>
            <option value="Russia">Russian Federation</option>
            <option value="South Africa">South Africa</option>
            <option value="Thailand">Thailand</option>
            <option value="Turkey">Turkey</option>
            <option value="Ukraine">Ukraine</option>
            <option value="United Arab Emirates">United Arab Emirates</option>
            <option value="United Kingdom">United Kingdom</option>
            <option value="United States">United States</option>
          </select>
          <label for="multicol-country">Country</label>
        </div>
      </div>
      <div class="col-md-6 select2-primary">
        <div class="form-floating form-floating-outline">
          <select id="multicol-language" class="select2 form-select" multiple>
            <option value="en" selected>English</option>
            <option value="fr" selected>French</option>
            <option value="de">German</option>
            <option value="pt">Portuguese</option>
          </select>
          <label for="multicol-language">Language</label>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-floating form-floating-outline">
          <input type="text" id="multicol-birthdate" class="form-control dob-picker" placeholder="YYYY-MM-DD" />
          <label for="multicol-birthdate">Birth Date</label>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-floating form-floating-outline">
          <input type="text" id="multicol-phone" class="form-control phone-mask" placeholder="658 799 8941" aria-label="658 799 8941" />
          <label for="multicol-phone">Phone No</label>
        </div>
      </div>
    </div>
    <div class="pt-4">
      <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
      <button type="reset" class="btn btn-label-secondary">Cancel</button>
    </div>
  </form>
</div>

<!-- Collapsible Section -->
<div class="row my-4">
  <div class="col">
    <h6> Collapsible Section </h6>
    <div class="accordion" id="collapsibleSection">
      <div class="card accordion-item">
        <h2 class="accordion-header" id="headingDeliveryAddress">
          <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseDeliveryAddress" aria-expanded="true" aria-controls="collapseDeliveryAddress"> Delivery Address </button>
        </h2>
        <div id="collapseDeliveryAddress" class="accordion-collapse collapse show" data-bs-parent="#collapsibleSection">
          <div class="accordion-body">
            <div class="row g-4">
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="collapsible-fullname" class="form-control" placeholder="John Doe" />
                  <label for="collapsible-fullname">Full Name</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="collapsible-phone" class="form-control phone-mask" placeholder="658 799 8941" aria-label="658 799 8941" />
                  <label for="collapsible-phone">Phone No</label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-floating form-floating-outline">
                  <textarea name="collapsible-address" class="form-control" id="collapsible-address" rows="2" placeholder="1456, Mall Road" style="height: 60px;"></textarea>
                  <label for="collapsible-address">Address</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="collapsible-pincode" class="form-control" placeholder="658468" />
                  <label for="collapsible-pincode">Pincode</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="collapsible-landmark" class="form-control" placeholder="Nr. Wall Street" />
                  <label for="collapsible-landmark">Landmark</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="collapsible-city" class="form-control" placeholder="Jackson" />
                  <label for="collapsible-city">City</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <select id="collapsible-state" class="select2 form-select" data-allow-clear="true">
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
                  <label for="collapsible-state">State</label>
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
          </div>
        </div>
      </div>
      <div class="card accordion-item">
        <h2 class="accordion-header" id="headingDeliveryOptions">
          <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseDeliveryOptions" aria-expanded="false" aria-controls="collapseDeliveryOptions"> Delivery Options </button>
        </h2>
        <div id="collapseDeliveryOptions" class="accordion-collapse collapse" aria-labelledby="headingDeliveryOptions" data-bs-parent="#collapsibleSection">
          <div class="accordion-body">
            <div class="row">
              <div class="col-md mb-md-0 mb-2">
                <div class="form-check custom-option custom-option-basic">
                  <label class="form-check-label custom-option-content" for="radioStandard">
                    <input name="CustomRadioDelivery" class="form-check-input" type="radio" value="" id="radioStandard" checked />
                    <span class="custom-option-header">
                      <span class="h6 mb-0">Standard 3-5 Days</span>
                      <span>Free</span>
                    </span>
                    <span class="custom-option-body">
                      <small> Friday, 15 Nov - Monday, 18 Nov </small>
                    </span>
                  </label>
                </div>
              </div>
              <div class="col-md mb-md-0 mb-2">
                <div class="form-check custom-option custom-option-basic">
                  <label class="form-check-label custom-option-content" for="radioExpress">
                    <input name="CustomRadioDelivery" class="form-check-input" type="radio" value="" id="radioExpress" />
                    <span class="custom-option-header">
                      <span class="h6 mb-0">Express</span>
                      <span>$5.00</span>
                    </span>
                    <span class="custom-option-body">
                      <small> Friday, 15 Nov - Sunday, 17 Nov </small>
                    </span>
                  </label>
                </div>
              </div>
              <div class="col-md">
                <div class="form-check custom-option custom-option-basic">
                  <label class="form-check-label custom-option-content" for="radioOvernight">
                    <input name="CustomRadioDelivery" class="form-check-input" type="radio" value="" id="radioOvernight" />
                    <span class="custom-option-header">
                      <span class="h6 mb-0">Overnight</span>
                      <span>$10.00</span>
                    </span>
                    <span class="custom-option-body">
                      <small>Friday, 15 Nov - Saturday, 16 Nov</small>
                    </span>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card accordion-item">
        <h2 class="accordion-header" id="headingPaymentMethod">
          <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapsePaymentMethod" aria-expanded="false" aria-controls="collapsePaymentMethod"> Payment Method </button>
        </h2>
        <div id="collapsePaymentMethod" class="accordion-collapse collapse" aria-labelledby="headingPaymentMethod" data-bs-parent="#collapsibleSection">
          <form>
            <div class="accordion-body">
              <div class="mb-3">
                <div class="form-check form-check-inline">
                  <input name="collapsible-payment" class="form-check-input form-check-input-payment" type="radio" value="credit-card" id="collapsible-payment-cc" checked="" />
                  <label class="form-check-label" for="collapsible-payment-cc">
                    Credit/Debit/ATM Card <i class="mdi mdi-card-bulleted-outline"></i>
                  </label>
                </div>
                <div class="form-check form-check-inline">
                  <input name="collapsible-payment" class="form-check-input form-check-input-payment" type="radio" value="cash" id="collapsible-payment-cash" />
                  <label class="form-check-label" for="collapsible-payment-cash">
                    Cash On Delivery
                    <i class="mdi mdi-help-circle-outline text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="You can pay once you receive the product."></i>
                  </label>
                </div>
              </div>
              <div id="form-credit-card" class="row">
                <div class="col-12 col-md-8 col-xl-6">
                  <div class="input-group input-group-merge mb-4">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="creditCardMask" name="creditCardMask" class="form-control credit-card-mask" placeholder="1356 3215 6548 7898" aria-describedby="creditCardMask2" />
                      <label for="creditCardMask">Card Number</label>
                    </div>
                    <span class="input-group-text cursor-pointer p-1" id="creditCardMask2"><span class="card-type"></span></span>
                  </div>
                  <div class="row">
                    <div class="col-12 col-md-6">
                      <div class="form-floating form-floating-outline mb-4">
                        <input type="text" id="collapsible-payment-name" class="form-control" placeholder="John Doe" />
                        <label for="collapsible-payment-name">Name</label>
                      </div>
                    </div>
                    <div class="col-6 col-md-3">
                      <div class="form-floating form-floating-outline mb-4">
                        <input type="text" id="collapsible-payment-expiry-date" class="form-control expiry-date-mask" placeholder="MM/YY" />
                        <label for="collapsible-payment-expiry-date">Exp. Date</label>
                      </div>
                    </div>
                    <div class="col-6 col-md-3">
                      <div class="input-group input-group-merge mb-4">
                        <div class="form-floating form-floating-outline">
                          <input type="text" id="collapsible-payment-cvv" class="form-control cvv-code-mask" maxlength="3" placeholder="654" />
                          <label for="collapsible-payment-cvv">CVV Code</label>
                        </div>
                        <span class="input-group-text cursor-pointer" id="collapsible-payment-cvv2"><i class="mdi mdi-help-circle-outline text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Card Verification Value"></i></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mt-1">
                <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                <button type="reset" class="btn btn-label-secondary">Cancel</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Form with Tabs -->
<div class="row">
  <div class="col">
    <h6 class="mt-4"> Form with Tabs </h6>
    <div class="card mb-3">
      <div class="card-header">
        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#form-tabs-personal" role="tab" aria-selected="true">Personal Info</button>
          </li>
          <li class="nav-item">
            <button class="nav-link " data-bs-toggle="tab" data-bs-target="#form-tabs-account" role="tab" aria-selected="false">Account Details</button>
          </li>
          <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#form-tabs-social" role="tab" aria-selected="false">Social Links</button>
          </li>
        </ul>
      </div>

      <div class="tab-content">
        <div class="tab-pane fade active show" id="form-tabs-personal" role="tabpanel">
          <form>
            <div class="row g-4">
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="formtabs-first-name" class="form-control" placeholder="John" />
                  <label for="formtabs-first-name">First Name</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="formtabs-last-name" class="form-control" placeholder="Doe" />
                  <label for="formtabs-last-name">Last Name</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <select id="formtabs-country" class="select2 form-select" data-allow-clear="true">
                    <option value="">Select</option>
                    <option value="Australia">Australia</option>
                    <option value="Bangladesh">Bangladesh</option>
                    <option value="Belarus">Belarus</option>
                    <option value="Brazil">Brazil</option>
                    <option value="Canada">Canada</option>
                    <option value="China">China</option>
                    <option value="France">France</option>
                    <option value="Germany">Germany</option>
                    <option value="India">India</option>
                    <option value="Indonesia">Indonesia</option>
                    <option value="Israel">Israel</option>
                    <option value="Italy">Italy</option>
                    <option value="Japan">Japan</option>
                    <option value="Korea">Korea, Republic of</option>
                    <option value="Mexico">Mexico</option>
                    <option value="Philippines">Philippines</option>
                    <option value="Russia">Russian Federation</option>
                    <option value="South Africa">South Africa</option>
                    <option value="Thailand">Thailand</option>
                    <option value="Turkey">Turkey</option>
                    <option value="Ukraine">Ukraine</option>
                    <option value="United Arab Emirates">United Arab Emirates</option>
                    <option value="United Kingdom">United Kingdom</option>
                    <option value="United States">United States</option>
                  </select>
                  <label for="formtabs-country">Country</label>
                </div>
              </div>
              <div class="col-md-6 select2-primary">
                <div class="form-floating form-floating-outline">
                  <select id="formtabs-language" class="select2 form-select" multiple>
                    <option value="en" selected>English</option>
                    <option value="fr" selected>French</option>
                    <option value="de">German</option>
                    <option value="pt">Portuguese</option>
                  </select>
                  <label for="formtabs-language">Language</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="formtabs-birthdate" class="form-control dob-picker" placeholder="YYYY-MM-DD" />
                  <label for="formtabs-birthdate">Birth Date</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="formtabs-phone" class="form-control phone-mask" placeholder="658 799 8941" aria-label="658 799 8941" />
                  <label for="formtabs-phone">Phone No</label>
                </div>
              </div>
            </div>
            <div class="pt-4">
              <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
              <button type="reset" class="btn btn-label-secondary">Cancel</button>
            </div>
          </form>
        </div>
        <div class="tab-pane fade" id="form-tabs-account" role="tabpanel">
          <form>
            <div class="row g-4">
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="formtabs-username" class="form-control" placeholder="john.doe" />
                  <label for="formtabs-username">Username</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input-group input-group-merge">
                  <div class="form-floating form-floating-outline">
                    <input type="text" id="formtabs-email" class="form-control" placeholder="john.doe" aria-label="john.doe" aria-describedby="formtabs-email2" />
                    <label for="formtabs-email">Email</label>
                  </div>
                  <span class="input-group-text" id="formtabs-email2">@example.com</span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-password-toggle">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="password" id="formtabs-password" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="formtabs-password2" />
                      <label for="formtabs-password">Password</label>
                    </div>
                    <span class="input-group-text cursor-pointer" id="formtabs-password2"><i class="mdi mdi-eye-off-outline"></i></span>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-password-toggle">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="password" id="formtabs-confirm-password" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="formtabs-confirm-password2" />
                      <label for="formtabs-confirm-password">Confirm Password</label>
                    </div>
                    <span class="input-group-text cursor-pointer" id="formtabs-confirm-password2"><i class="mdi mdi-eye-off-outline"></i></span>
                  </div>
                </div>
              </div>
            </div>
            <div class="pt-4">
              <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
              <button type="reset" class="btn btn-label-secondary">Cancel</button>
            </div>
          </form>
        </div>
        <div class="tab-pane fade" id="form-tabs-social" role="tabpanel">
          <form>
            <div class="row g-4">
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="formtabs-twitter" class="form-control" placeholder="https://twitter.com/abc" />
                  <label for="formtabs-twitter">Twitter</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="formtabs-facebook" class="form-control" placeholder="https://facebook.com/abc" />
                  <label for="formtabs-facebook">Facebook</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="formtabs-google" class="form-control" placeholder="https://plus.google.com/abc" />
                  <label for="formtabs-google">Google+</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="formtabs-linkedin" class="form-control" placeholder="https://linkedin.com/abc" />
                  <label for="formtabs-linkedin">Linkedin</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="formtabs-instagram" class="form-control" placeholder="https://instagram.com/abc" />
                  <label for="formtabs-instagram">Instagram</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                  <input type="text" id="formtabs-quora" class="form-control" placeholder="https://quora.com/abc" />
                  <label for="formtabs-quora">Quora</label>
                </div>
              </div>
            </div>
            <div class="pt-4">
              <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
              <button type="reset" class="btn btn-label-secondary">Cancel</button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Form Alignment -->
<div class="card">
  <h5 class="card-header">Form Alignment</h5>
  <div class="card-body">
    <div class="d-flex align-items-center justify-content-center h-px-500">
      <form class="w-px-400 border rounded p-3 p-md-5">
        <h3 class="mb-4">Sign In</h3>

        <div class="form-floating form-floating-outline mb-4">
          <input type="text" id="form-alignment-username" class="form-control" placeholder="john.doe" />
          <label for="form-alignment-username">Username</label>
        </div>

        <div class="mb-4 form-password-toggle">
          <div class="input-group input-group-merge">
            <div class="form-floating form-floating-outline">
              <input type="password" id="form-alignment-password" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="form-alignment-password2" />
              <label for="form-alignment-password">Password</label>
            </div>
            <span class="input-group-text cursor-pointer" id="form-alignment-password2"><i class="mdi mdi-eye-off-outline"></i></span>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-check m-0">
            <input type="checkbox" class="form-check-input" />
            <span class="form-check-label">Remember me</span>
          </label>
        </div>
        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary">Login</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
