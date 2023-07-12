@extends('layouts/layoutMaster')

@section('title', 'Pricing - Pages')

<!-- Page -->
@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-pricing.css')}}" />
@endsection

@section('page-script')
<script src="{{asset('assets/js/pages-pricing.js')}}"></script>
@endsection

@section('content')
<div class="card">
  <!-- Pricing Plans -->
  <div class="pb-sm-5 pb-2 rounded-top">
    <div class="container py-5">
      <h2 class="text-center mb-2 mt-0 mt-md-4">Pricing Plans</h2>
      <p class="text-center text-muted">All plans include 40+ advanced tools and features to boost your product. Choose the best plan to fit your needs.</p>
      <div class="d-flex align-items-center justify-content-center flex-wrap gap-2 py-5 mb-0 mb-md-4">
        <label class="switch switch-secondary ms-sm-5 ps-sm-5 me-0">
          <span class="switch-label">Monthly</span>
          <input type="checkbox" class="switch-input price-duration-toggler" checked />
          <span class="switch-toggle-slider">
            <span class="switch-on"></span>
            <span class="switch-off"></span>
          </span>
          <span class="switch-label">Annual</span>
        </label>
        <div class="mt-n5 ms-n5 ml-2 mb-2 d-none d-sm-flex align-items-center gap-2">
          <i class="mdi mdi-arrow-down-left mdi-24px text-muted scaleX-n1-rtl"></i>
          <span class="badge badge-sm bg-label-primary rounded-pill mb-2 ">Get 2 months free</span>
        </div>
      </div>

      <div class="pricing-plans row mx-0 gy-3 px-lg-5">
        <!-- Basic -->
        <div class="col-lg mb-md-0 mb-4">
          <div class="card border rounded shadow-none">
            <div class="card-body">
              <div class="my-3 pt-2 text-center">
                <img src="{{asset('assets/img/illustrations/pricing-basic.png')}}" alt="Basic Image" height="100">
              </div>
              <h3 class="card-title fw-semibold text-center text-capitalize mb-1">Basic</h3>
              <p class="text-center pb-2">A simple start for everyone</p>
              <div class="text-center">
                <div class="d-flex justify-content-center">
                  <sup class="h6 pricing-currency mt-3 mb-0 me-1 text-body fw-normal">$</sup>
                  <h1 class="fw-semibold display-3 mb-0 text-primary">0</h1>
                  <sub class="h6 pricing-duration mt-auto mb-2 text-body fw-normal">/month</sub>
                </div>
              </div>

              <ul class="ps-3 my-4 pt-2">
                <li class="mb-1">100 responses a month</li>
                <li class="mb-1">Unlimited forms and surveys</li>
                <li class="mb-1">Unlimited fields</li>
                <li class="mb-1">Basic form creation tools</li>
                <li class="mb-0">Up to 2 subdomains</li>
              </ul>

              <a href="{{url('auth/register-basic')}}" class="btn btn-outline-success d-grid w-100">Your Current Plan</a>
            </div>
          </div>
        </div>

        <!-- Standard -->
        <div class="col-lg mb-md-0 mb-4">
          <div class="card border-primary border shadow-none">
            <div class="card-body position-relative">
              <div class="position-absolute end-0 me-4 top-0 mt-4">
                <span class="badge bg-label-primary rounded-pill">Popular</span>
              </div>
              <div class="my-3 pt-2 text-center">
                <img src="{{asset('assets/img/illustrations/pricing-standard.png')}}" alt="Standard Image" height="100">
              </div>
              <h3 class="card-title fw-semibold text-center text-capitalize mb-1">Standard</h3>
              <p class="text-center">For small to medium businesses</p>
              <div class="text-center">
                <div class="d-flex justify-content-center">
                  <sup class="h6 pricing-currency mt-3 mb-0 me-1 text-body fw-normal">$</sup>
                  <h1 class="price-toggle price-yearly fw-semibold display-3 text-primary mb-0">7</h1>
                  <h1 class="price-toggle price-monthly fw-semibold display-3 text-primary mb-0 d-none">9</h1>
                  <sub class="h6 text-body pricing-duration mt-auto mb-2 fw-normal">/month</sub>
                </div>
                <small class="price-yearly price-yearly-toggle text-body">$ 90 / year</small>
              </div>

              <ul class="ps-3 my-4 pt-3">
                <li class="mb-1">Unlimited responses</li>
                <li class="mb-1">Unlimited forms and surveys</li>
                <li class="mb-1">Instagram profile page</li>
                <li class="mb-1">Google Docs integration</li>
                <li class="mb-0">Custom “Thank you” page</li>
              </ul>

              <a href="{{url('auth/register-basic')}}" class="btn btn-primary d-grid w-100">Upgrade</a>
            </div>
          </div>
        </div>

        <!-- Enterprise -->
        <div class="col-lg">
          <div class="card border rounded shadow-none">
            <div class="card-body">

              <div class="my-3 pt-2 text-center">
                <img src="{{asset('assets/img/illustrations/pricing-enterprise.png')}}" alt="Enterprise Image" height="100">
              </div>
              <h3 class="card-title text-center text-capitalize fw-semibold mb-1">Enterprise</h3>
              <p class="text-center">Solution for big organizations</p>

              <div class="text-center">
                <div class="d-flex justify-content-center">
                  <sup class="h6 pricing-currency mt-3 mb-0 me-1 text-body fw-normal">$</sup>
                  <h1 class="price-toggle price-yearly fw-semibold display-3 text-primary mb-0">16</h1>
                  <h1 class="price-toggle price-monthly fw-semibold display-3 text-primary mb-0 d-none">19</h1>
                  <sub class="h6 pricing-duration mt-auto mb-2 fw-normal text-body">/month</sub>
                </div>
                <small class="price-yearly price-yearly-toggle text-body">$ 190 / year</small>
              </div>

              <ul class="ps-3 my-4 pt-3">
                <li class="mb-1">PayPal payments</li>
                <li class="mb-1">Logic Jumps</li>
                <li class="mb-1">File upload with 5GB storage</li>
                <li class="mb-1">Custom domain support</li>
                <li class="mb-0">Stripe integration</li>
              </ul>

              <a href="{{url('auth/register-basic')}}" class="btn btn-outline-primary d-grid w-100">Upgrade</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Pricing Plans -->
  <!-- Pricing Free Trial -->
  <div class="pricing-free-trial">
    <div class="container">
      <div class="position-relative">
        <div class="d-flex justify-content-between flex-column-reverse flex-lg-row align-items-center py-4 my-2 px-5">
          <div class="text-center text-lg-start mt-3 ms-3">
            <h4 class="text-primary mb-1">Still not convinced? Start with a 14-day FREE trial!</h4>
            <p class="text-body mb-1">You will get full access to with all the features for 14 days.</p>
            <a href="{{url('auth/register-basic')}}" class="btn btn-primary mt-4 mb-3">Start 14-day FREE trial</a>
          </div>
          <!-- image -->
          <div class="text-center">
            <img src="{{asset('assets/img/illustrations/pricing-illustration.png')}}" class="img-fluid me-lg-5 pe-lg-1 mb-3 mb-lg-0" alt="Api Key Image" width="210">
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Pricing Free Trial -->
  <!-- Plans Comparison -->
  <div class="pricing-plans-comparison">
    <div class="container py-5 px-lg-5">
      <div class="row mt-0 mt-md-4">
        <div class="col-12 text-center mb-4">
          <h4 class="mb-4">Pick a plan that works best for you</h4>
          <p class="mb-1">Stay cool, we have a 48-hour money back guarantee!</p>
        </div>
      </div>
      <div class="row mx-4">
        <div class="col-12">
          <div class="table-responsive border rounded">
            <table class="table table-striped text-center mb-0">
              <thead>
                <tr>
                  <th scope="col">
                    <p class="mb-1">Features</p>
                    <p class="fw-normal text-capitalize mb-0">Native front features</p>
                  </th>
                  <th scope="col">
                    <p class="mb-1">Starter</p>
                    <p class="fw-normal mb-0">Free</p>
                  </th>
                  <th scope="col">
                    <div class="d-flex justify-content-center align-items-center">
                      <p class="mb-0 me-1">Pro</p>
                      <span class="badge badge-pro rounded-pill bg-primary w-px-20 h-px-20 d-flex align-items-center justify-content-center"><i class="mdi mdi-star mdi-14px"></i></span>
                    </div>
                    <p class="fw-normal text-capitalize mb-0">$7.5/month</p>
                  </th>
                  <th scope="col">
                    <p class="mb-1">Enterprise</p>
                    <small class="fw-normal text-capitalize">$16/month</small>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>14-days free trial</td>
                  <td>
                    <span class="badge rounded-pill bg-primary h-px-18 w-px-18 d-flex align-items-center justify-content-center mx-auto"><i class="mdi mdi-check mdi-14px"></i></span>
                  </td>
                  <td>
                    <span class="badge rounded-pill bg-primary h-px-18 w-px-18 d-flex align-items-center justify-content-center mx-auto"><i class="mdi mdi-check mdi-14px"></i></span>
                  </td>
                  <td>
                    <span class="badge rounded-pill bg-primary h-px-18 w-px-18 d-flex align-items-center justify-content-center mx-auto"><i class="mdi mdi-check mdi-14px"></i></span>
                  </td>
                </tr>
                <tr>
                  <td>No user limit</td>
                  <td>
                    <span class="badge rounded-pill bg-light w-px-18 h-px-18 d-flex align-items-center justify-content-center mx-auto"><i class="mdi mdi-close mdi-14px"></i></span>
                  </td>
                  <td>
                    <span class="badge rounded-pill bg-light w-px-18 h-px-18 d-flex align-items-center justify-content-center mx-auto"><i class="mdi mdi-close mdi-14px"></i></span>
                  </td>
                  <td>
                    <span class="badge rounded-pill bg-primary h-px-18 w-px-18 d-flex align-items-center justify-content-center mx-auto"><i class="mdi mdi-check mdi-14px"></i></span>
                  </td>
                </tr>
                <tr>
                  <td>Product Support</td>
                  <td>
                    <span class="badge rounded-pill bg-light w-px-18 h-px-18 d-flex align-items-center justify-content-center mx-auto"><i class="mdi mdi-close mdi-14px"></i></span>
                  </td>
                  <td>
                    <span class="badge rounded-pill bg-primary h-px-18 w-px-18 d-flex align-items-center justify-content-center mx-auto"><i class="mdi mdi-check mdi-14px"></i></span>
                  </td>
                  <td>
                    <span class="badge rounded-pill bg-primary h-px-18 w-px-18 d-flex align-items-center justify-content-center mx-auto"><i class="mdi mdi-check mdi-14px"></i></span>
                  </td>
                </tr>
                <tr>
                  <td>Email Support</td>
                  <td>
                    <span class="badge rounded-pill bg-light w-px-18 h-px-18 d-flex align-items-center justify-content-center mx-auto"><i class="mdi mdi-close mdi-14px"></i></span>
                  </td>
                  <td>
                    <span class="badge bg-label-primary badge-sm rounded-pill text-uppercase">Add-on Available</span>
                  </td>
                  <td>
                    <span class="badge rounded-pill bg-primary h-px-18 w-px-18 d-flex align-items-center justify-content-center mx-auto"><i class="mdi mdi-check mdi-14px"></i></span>
                  </td>
                </tr>
                <tr>
                  <td>Integrations</td>
                  <td>
                    <span class="badge rounded-pill bg-light w-px-18 h-px-18 d-flex align-items-center justify-content-center mx-auto"><i class="mdi mdi-close mdi-14px"></i></span>
                  </td>
                  <td>
                    <span class="badge rounded-pill bg-primary h-px-18 w-px-18 d-flex align-items-center justify-content-center mx-auto"><i class="mdi mdi-check mdi-14px"></i></span>
                  </td>
                  <td>
                    <span class="badge rounded-pill bg-primary h-px-18 w-px-18 d-flex align-items-center justify-content-center mx-auto"><i class="mdi mdi-check mdi-14px"></i></span>
                  </td>
                </tr>
                <tr>
                  <td>Removal of Front branding</td>
                  <td>
                    <span class="badge rounded-pill bg-light w-px-18 h-px-18 d-flex align-items-center justify-content-center mx-auto"><i class="mdi mdi-close mdi-14px"></i></span>
                  </td>
                  <td>
                    <span class="badge bg-label-primary badge-sm rounded-pill text-uppercase">Add-on Available</span>
                  </td>
                  <td>
                    <span class="badge rounded-pill bg-primary h-px-18 w-px-18 d-flex align-items-center justify-content-center mx-auto"><i class="mdi mdi-check mdi-14px"></i></span>
                  </td>
                </tr>
                <tr>
                  <td>Active maintenance & support</td>
                  <td>
                    <span class="badge rounded-pill bg-light w-px-18 h-px-18 d-flex align-items-center justify-content-center mx-auto"><i class="mdi mdi-close mdi-14px"></i></span>
                  </td>
                  <td>
                    <span class="badge rounded-pill bg-light w-px-18 h-px-18 d-flex align-items-center justify-content-center mx-auto"><i class="mdi mdi-close mdi-14px"></i></span>
                  </td>
                  <td>
                    <span class="badge rounded-pill bg-primary h-px-18 w-px-18 d-flex align-items-center justify-content-center mx-auto"><i class="mdi mdi-check mdi-14px"></i></span>
                  </td>
                </tr>
                <tr>
                  <td>Data storage for 365 days</td>
                  <td>
                    <span class="badge rounded-pill bg-light w-px-18 h-px-18 d-flex align-items-center justify-content-center mx-auto"><i class="mdi mdi-close mdi-14px"></i></span>
                  </td>
                  <td>
                    <span class="badge rounded-pill bg-light w-px-18 h-px-18 d-flex align-items-center justify-content-center mx-auto"><i class="mdi mdi-close mdi-14px"></i></span>
                  </td>
                  <td>
                    <span class="badge rounded-pill bg-primary h-px-18 w-px-18 d-flex align-items-center justify-content-center mx-auto"><i class="mdi mdi-check mdi-14px"></i></span>
                  </td>
                </tr>
                <tr>
                  <td></td>
                  <td>
                    <a href="{{url('auth/register-basic')}}" class="btn text-nowrap btn-outline-primary">Choose Plan</a>
                  </td>
                  <td>
                    <a href="{{url('auth/register-basic')}}" class="btn text-nowrap btn-primary">Choose Plan</a>
                  </td>
                  <td>
                    <a href="{{url('auth/register-basic')}}" class="btn text-nowrap btn-outline-primary">Choose Plan</a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Plans Comparison -->
  <!-- FAQS -->
  <div class="pricing-faqs bg-alt-pricing rounded-bottom">
    <div class="container py-5 px-lg-5">
      <div class="row mt-0 mt-md-4">
        <div class="col-12 text-center mb-4">
          <h4 class="mb-2">Frequently Asked Questions</h4>
          <p>Let us help answer the most common questions you might have.</p>
        </div>
      </div>
      <div class="row mx-3">
        <div class="col-12">
          <div id="faq" class="accordion">
            <div class="accordion-item">
              <h6 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" aria-expanded="true" data-bs-target="#faq-1" aria-controls="faq-1">
                  What counts towards the 100 responses limit?
                </button>
              </h6>

              <div id="faq-1" class="accordion-collapse collapse show" data-bs-parent="#faq">
                <div class="accordion-body">
                  We count all responses submitted through all your forms in a month.
                  If you already received 100 responses this month, you won’t be able to receive any more of them until next
                  month when the counter resets.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h6 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq-2" aria-expanded="false" aria-controls="faq-2">
                  How do you process payments?
                </button>
              </h6>
              <div id="faq-2" class="accordion-collapse collapse" data-bs-parent="#faq">
                <div class="accordion-body">
                  We accept Visa®, MasterCard®, American Express®, and PayPal®.
                  So you can be confident that your credit card information will be kept
                  safe and secure.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h6 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq-3" aria-expanded="false" aria-controls="faq-3">
                  What payment methods do you accept?
                </button>
              </h6>
              <div id="faq-3" class="accordion-collapse collapse" data-bs-parent="#faq">
                <div class="accordion-body">
                  2Checkout accepts all types of credit and debit cards.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h6 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq-4" aria-expanded="false" aria-controls="faq-4">
                  Do you have a money-back guarantee?
                </button>
              </h6>
              <div id="faq-4" class="accordion-collapse collapse" data-bs-parent="#faq">
                <div class="accordion-body">
                  Yes. You may request a refund within 30 days of your purchase without any additional explanations.
                </div>
              </div>
            </div>

            <div class="accordion-item mb-0 mb-md-4">
              <h6 class="accordion-header">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq-5" aria-expanded="false" aria-controls="faq-5">
                  I have more questions. Where can I get help?
                </button>
              </h6>
              <div id="faq-5" class="accordion-collapse collapse" data-bs-parent="#faq">
                <div class="accordion-body">
                  Please <a href="javascript:void(0);">contact</a> us if you have any other questions or concerns. We’re
                  here to help!
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ FAQS -->
</div>
@endsection
