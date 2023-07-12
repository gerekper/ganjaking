@push('pricing-script')
<script src="{{asset('assets/js/pages-pricing.js')}}"></script>
@endpush

<!-- Pricing Modal -->
<div class="modal fade" id="pricingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-simple modal-pricing">
    <div class="modal-content p-2 p-md-5">
      <div class="modal-body py-3 py-md-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <!-- Pricing Plans -->
        <div class="pb-3 rounded-top">
          <h3 class="text-center mb-2">Subscription Plan</h3>
          <p class="text-center pt-1 mb-0"> All plans include 40+ advanced tools and features to boost your product. Choose the best plan to fit your needs. </p>
          <div class="d-flex align-items-center justify-content-center flex-wrap gap-2 py-3 mb-0 mb-md-3">
            <label class="switch switch-primary ms-sm-5 ps-sm-5 me-0">
              <span class="switch-label">Monthly</span>
              <input type="checkbox" class="switch-input price-duration-toggler" checked />
              <span class="switch-toggle-slider">
                <span class="switch-on"></span>
                <span class="switch-off"></span>
              </span>
              <span class="switch-label">Annual</span>
            </label>
          </div>

          <div class="row mx-0 gy-3">
            <!-- Basic -->
            <div class="col-xl mb-md-0 mb-4">
              <div class="card border rounded shadow-none">
                <div class="card-body">
                  <div class="my-3 pt-2 text-center">
                    <img src="{{asset('assets/img/illustrations/pricing-basic.png')}}" alt="Basic Image" height="100">
                  </div>
                  <h4 class="card-title fw-medium text-center text-capitalize mb-1 pt-1">Basic</h4>
                  <p class="text-center mb-2">A simple start for everyone</p>
                  <div class="text-center">
                    <div class="d-flex justify-content-center">
                      <sup class="h6 pricing-currency mt-3 mb-0 me-1 fw-normal">$</sup>
                      <h1 class="fw-medium display-3 mb-0 text-primary">0</h1>
                      <sub class="h6 pricing-duration mt-auto mb-2 fw-normal">/month</sub>
                    </div>
                  </div>

                  <ul class="ps-3 my-4 pt-3">
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
            <div class="col-xl mb-md-0 mb-4">
              <div class="card border-primary border shadow-none">
                <div class="card-body position-relative">
                  <div class="position-absolute end-0 me-4 top-0 mt-4">
                    <span class="badge bg-label-primary rounded-pill">Popular</span>
                  </div>
                  <div class="my-3 pt-2 text-center">
                    <img src="{{asset('assets/img/illustrations/pricing-standard.png')}}" alt="Pro Image" height="100">
                  </div>
                  <h4 class="card-title fw-medium text-center text-capitalize mb-1 pt-1">Standard</h4>
                  <p class="text-center mb-2">For small to medium businesses</p>
                  <div class="text-center">
                    <div class="d-flex justify-content-center">
                      <sup class="h6 pricing-currency mt-3 mb-0 me-1">$</sup>
                      <h1 class="price-toggle price-yearly fw-semibold display-3 text-primary mb-0">7</h1>
                      <h1 class="price-toggle price-monthly fw-semibold display-3 text-primary mb-0 d-none">9</h1>
                      <sub class="h6 pricing-duration mt-auto mb-2 fw-normal">/month</sub>
                    </div>
                    <small class="price-yearly price-yearly-toggle">$ 90 / year</small>
                  </div>

                  <ul class="ps-3 mb-4 pt-3">
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
            <div class="col-xl">
              <div class="card border rounded shadow-none">
                <div class="card-body">

                  <div class="my-3 pt-2 text-center">
                    <img src="{{asset('assets/img/illustrations/pricing-enterprise.png')}}" alt="Enterprise Image" height="100">
                  </div>
                  <h4 class="card-title text-center text-capitalize fw-medium mb-1 pt-1">Enterprise</h4>
                  <p class="text-center mb-2">Solution for big organizations</p>

                  <div class="text-center">
                    <div class="d-flex justify-content-center">
                      <sup class="h6 pricing-currency mt-3 mb-0 me-1">$</sup>
                      <h1 class="price-toggle price-yearly fw-semibold display-3 text-primary mb-0">16</h1>
                      <h1 class="price-toggle price-monthly fw-semibold display-3 text-primary mb-0 d-none">19</h1>
                      <sub class="h6 pricing-duration mt-auto mb-2 fw-normal">/month</sub>
                    </div>
                    <small class="price-yearly price-yearly-toggle">$ 190 / year</small>
                  </div>

                  <ul class="ps-3 mb-4 pt-3">
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
        <!--/ Pricing Plans -->
        <div class="text-center">
          <p>Still Not Convinced? Start with a 14-day FREE trial!</p>
          <a href="javascript:void(0);" class="btn btn-primary">Start your trial</a>
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Pricing Modal -->
