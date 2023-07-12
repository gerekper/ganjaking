@extends('layouts/layoutMaster')

@section('title', 'FAQ - Pages')

<!-- Page -->
@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-faq.css')}}" />
@endsection

@section('content')
<div class="faq-header d-flex flex-column justify-content-center align-items-center">
  <h3 class="text-center text-primary mb-2 fw-semibold"> Hello, how can we help? </h3>
  <p class="text-body text-center mb-0 px-3">or choose a category to quickly find the help you need</p>
  <div class="input-wrapper my-3 input-group input-group-lg input-group-merge px-5">
    <span class="input-group-text" id="basic-addon1"><i class="mdi mdi-magnify mdi-20px"></i></span>
    <input type="text" class="form-control" placeholder="Ask a question...." aria-label="Search" aria-describedby="basic-addon1" />
  </div>
</div>

<div class="row mt-4">
  <!-- Navigation -->
  <div class="col-lg-3 col-md-4 col-12 mb-md-0 mb-3">
    <div class="d-flex justify-content-between flex-column mb-2 mb-md-0">
      <ul class="nav nav-align-left nav-pills flex-column">
        <li class="nav-item">
          <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#payment">
            <i class="mdi mdi-credit-card-outline me-1"></i>
            <span class="align-middle">Payment</span>
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#delivery">
            <i class="mdi mdi-cart-plus me-1"></i>
            <span class="align-middle">Delivery</span>
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cancellation">
            <i class="mdi mdi-reload me-1"></i>
            <span class="align-middle">Cancellation & Return</span>
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#orders">
            <i class="mdi mdi-wallet-giftcard me-1"></i>
            <span class="align-middle">My Orders</span>
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#product">
            <i class="mdi mdi-cog-outline me-1"></i>
            <span class="align-middle">Product & Services</span>
          </button>
        </li>
      </ul>
      <div class="d-none d-md-block">
        <div class="mt-5 text-center">
          <img src="{{asset('assets/img/illustrations/faq-illustration.png')}}" class="img-fluid w-px-120" alt="FAQ Image">
        </div>
      </div>
    </div>
  </div>
  <!-- /Navigation -->

  <!-- FAQ's -->
  <div class="col-lg-9 col-md-8 col-12">
    <div class="tab-content p-0">
      <div class="tab-pane fade show active" id="payment" role="tabpanel">
        <div class="d-flex mb-3 gap-3">
          <div class="avatar">
            <div class="avatar-initial bg-label-primary rounded">
              <i class="mdi mdi-credit-card-outline mdi-24px"></i>
            </div>
          </div>
          <div>
            <h5 class="mb-0">
              <span class="align-middle">Payment</span>
            </h5>
            <small class="text-muted">Get help with payment</small>
          </div>
        </div>
        <div id="accordionPayment" class="accordion">
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" aria-expanded="true" data-bs-target="#accordionPayment-1" aria-controls="accordionPayment-1">
                When is payment taken for my order?
              </button>
            </h2>

            <div id="accordionPayment-1" class="accordion-collapse collapse show">
              <div class="accordion-body">
                Payment is taken during the checkout process when you pay for
                your order. The order number that appears on the confirmation
                screen indicates payment has been successfully processed.
              </div>
            </div>
          </div>

          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordionPayment-2" aria-controls="accordionPayment-2">
                How do I pay for my order?
              </button>
            </h2>
            <div id="accordionPayment-2" class="accordion-collapse collapse">
              <div class="accordion-body">
                We accept Visa®, MasterCard®, American Express®, and PayPal®.
                Our servers encrypt all information submitted to them, so you
                can be confident that your credit card information will be kept
                safe and secure.
              </div>
            </div>
          </div>

          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordionPayment-3" aria-controls="accordionPayment-3">
                What should I do if I'm having trouble placing an order?
              </button>
            </h2>
            <div id="accordionPayment-3" class="accordion-collapse collapse">
              <div class="accordion-body">
                For any technical difficulties you are experiencing with our
                website, please contact us at our
                <a href="javascript:void(0);">support portal</a>, or you can call us toll-free at
                <strong>1-000-000-000</strong>, or email us at
                <a href="javascript:void(0);">order@companymail.com</a>
              </div>
            </div>
          </div>

          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordionPayment-4" aria-controls="accordionPayment-4">
                Which license do I need for an end product that is only accessible to paying users?
              </button>
            </h2>
            <div id="accordionPayment-4" class="accordion-collapse collapse">
              <div class="accordion-body">
                If you have paying users or you are developing any SaaS products then you need an Extended License.
                For each products, you need a license. You can get free lifetime updates as well.
              </div>
            </div>
          </div>

          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordionPayment-5" aria-controls="accordionPayment-5">
                Does my subscription automatically renew?
              </button>
            </h2>
            <div id="accordionPayment-5" class="accordion-collapse collapse">
              <div class="accordion-body">No, This is not subscription based item.Pastry pudding cookie toffee bonbon jujubes jujubes powder topping. Jelly beans gummi bears sweet roll bonbon muffin liquorice. Wafer lollipop sesame snaps.</div>
            </div>
          </div>
        </div>
      </div>
      <div class="tab-pane fade" id="delivery" role="tabpanel">
        <div class="d-flex mb-3 gap-3">
          <div class="avatar">
            <span class="avatar-initial bg-label-primary rounded">
              <i class="mdi mdi-cart-plus mdi-24px"></i>
            </span>
          </div>
          <div>
            <h5 class="mb-0">
              <span class="align-middle">Delivery</span>
            </h5>
            <small class="text-muted">Lorem ipsum, dolor sit amet.</small>
          </div>
        </div>
        <div id="accordionDelivery" class="accordion">
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" aria-expanded="true" data-bs-target="#accordionDelivery-1" aria-controls="accordionDelivery-1">
                How would you ship my order?
              </button>
            </h2>

            <div id="accordionDelivery-1" class="accordion-collapse collapse show">
              <div class="accordion-body">
                For large products, we deliver your product via a third party
                logistics company offering you the “room of choice” scheduled
                delivery service. For small products, we offer free parcel
                delivery.
              </div>
            </div>
          </div>

          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordionDelivery-2" aria-controls="accordionDelivery-2">
                What is the delivery cost of my order?
              </button>
            </h2>
            <div id="accordionDelivery-2" class="accordion-collapse collapse">
              <div class="accordion-body">The cost of scheduled delivery is $69 or $99 per order, depending on the destination postal code. The parcel delivery is free.
              </div>
            </div>
          </div>

          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordionDelivery-4" aria-controls="accordionDelivery-4">
                What to do if my product arrives damaged?
              </button>
            </h2>
            <div id="accordionDelivery-4" class="accordion-collapse collapse">
              <div class="accordion-body">
                We will promptly replace any product that is damaged in transit.
                Just contact our
                <a href="javascript:void(0);">support team</a>, to notify us of the situation
                within 48 hours of product arrival.
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="tab-pane fade" id="cancellation" role="tabpanel">
        <div class="d-flex mb-3 gap-3">
          <div class="avatar">
            <span class="avatar-initial bg-label-primary rounded">
              <i class="mdi mdi-reload mdi-24px"></i>
            </span>
          </div>
          <div>
            <h5 class="mb-0"><span class="align-middle">Cancellation & Return</span></h5>
            <small class="text-muted">Lorem ipsum, dolor sit amet.</small>
          </div>
        </div>
        <div id="accordionCancellation" class="accordion">
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" aria-expanded="true" data-bs-target="#accordionCancellation-1" aria-controls="accordionCancellation-1">
                Can I cancel my order?
              </button>
            </h2>

            <div id="accordionCancellation-1" class="accordion-collapse collapse show">
              <div class="accordion-body">
                <p>
                  Scheduled delivery orders can be cancelled 72 hours prior to
                  your selected delivery date for full refund.
                </p>
                <p class="mb-0">
                  Parcel delivery orders cannot be cancelled, however a free
                  return label can be provided upon request.
                </p>
              </div>
            </div>
          </div>

          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordionCancellation-2" aria-controls="accordionCancellation-2">
                Can I return my product?
              </button>
            </h2>
            <div id="accordionCancellation-2" class="accordion-collapse collapse">
              <div class="accordion-body">
                You can return your product within 15 days of delivery, by
                contacting our
                <a href="javascript:void(0);">support team</a>, All merchandise returned must be
                in the original packaging with all original items.
              </div>
            </div>
          </div>

          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" aria-controls="accordionCancellation-3" data-bs-target="#accordionCancellation-3">
                Where can I view status of return?
              </button>
            </h2>
            <div id="accordionCancellation-3" class="accordion-collapse collapse">
              <div class="accordion-body">
                <p>Locate the item from Your <a href="javascript:void(0);">Orders</a></p>
                <p class="mb-0">Select <strong>Return/Refund</strong> status</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="tab-pane fade" id="orders" role="tabpanel">
        <div class="d-flex mb-3 gap-3">
          <div class="avatar">
            <span class="avatar-initial bg-label-primary rounded">
              <i class="mdi mdi-wallet-giftcard mdi-24px"></i>
            </span>
          </div>
          <div>
            <h5 class="mb-0">
              <span class="align-middle">My Orders</span>
            </h5>
            <small class="text-muted">Lorem ipsum, dolor sit amet.</small>
          </div>
        </div>
        <div id="accordionOrders" class="accordion">
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" aria-expanded="true" data-bs-target="#accordionOrders-1" aria-controls="accordionOrders-1">
                Has my order been successful?
              </button>
            </h2>

            <div id="accordionOrders-1" class="accordion-collapse collapse show">
              <div class="accordion-body">
                <p>
                  All successful order transactions will receive an order
                  confirmation email once the order has been processed. If you
                  have not received your order confirmation email within 24
                  hours, check your junk email or spam folder.
                </p>
                <p class="mb-0">
                  Alternatively, log in to your account to check your order
                  summary. If you do not have a account, you can contact our
                  Customer Care Team on <strong>1-000-000-000</strong>.
                </p>
              </div>
            </div>
          </div>

          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordionOrders-2" aria-controls="accordionOrders-2">
                My Promotion Code is not working, what can I do?
              </button>
            </h2>
            <div id="accordionOrders-2" class="accordion-collapse collapse">
              <div class="accordion-body">
                If you are having issues with a promotion code, please contact
                us at <strong>1 000 000 000</strong> for assistance.
              </div>
            </div>
          </div>

          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordionOrders-3" aria-controls="accordionOrders-3">
                How do I track my Orders?
              </button>
            </h2>
            <div id="accordionOrders-3" class="accordion-collapse collapse">
              <div class="accordion-body">
                <p>
                  If you have an account just sign into your account from
                  <a href="javascript:void(0);">here</a> and select <strong>“My Orders”</strong>.
                </p>
                <p class="mb-0">
                  If you have a a guest account track your order from
                  <a href="javascript:void(0);">here</a> using the order number and the email
                  address.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="tab-pane fade" id="product" role="tabpanel">
        <div class="d-flex mb-3 gap-3">
          <div class="avatar">
            <span class="avatar-initial bg-label-primary rounded">
              <i class="mdi mdi-cog-outline mdi-24px"></i>
            </span>
          </div>
          <div>
            <h5 class="mb-0">
              <span class="align-middle">Product & Services</span>
            </h5>
            <small class="text-muted">Lorem ipsum, dolor sit amet.</small>
          </div>
        </div>
        <div id="accordionProduct" class="accordion">
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" aria-expanded="true" data-bs-target="#accordionProduct-1" aria-controls="accordionProduct-1">
                Will I be notified once my order has shipped?
              </button>
            </h2>

            <div id="accordionProduct-1" class="accordion-collapse collapse show">
              <div class="accordion-body">
                Yes, We will send you an email once your order has been shipped.
                This email will contain tracking and order information.
              </div>
            </div>
          </div>

          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordionProduct-2" aria-controls="accordionProduct-2">
                Where can I find warranty information?
              </button>
            </h2>
            <div id="accordionProduct-2" class="accordion-collapse collapse">
              <div class="accordion-body">
                We are committed to quality products. For information on
                warranty period and warranty services, visit our Warranty
                section <a href="javascript:void(0);">here</a>.
              </div>
            </div>
          </div>

          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordionProduct-3" aria-controls="accordionProduct-3">
                How can I purchase additional warranty coverage?
              </button>
            </h2>
            <div id="accordionProduct-3" class="accordion-collapse collapse">
              <div class="accordion-body">
                For the peace of your mind, we offer extended warranty plans
                that add additional year(s) of protection to the standard
                manufacturer’s warranty provided by us. To purchase or find out
                more about the extended warranty program, visit Extended
                Warranty section <a href="javascript:void(0);">here</a>.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /FAQ's -->
</div>

<!-- Contact -->
<div class="row mt-5">
  <div class="col-12 text-center mb-3">
    <div class="badge bg-label-primary rounded-pill">Question?</div>
    <h5 class="my-3">You still have a question?</h5>
    <p class="text-muted">If you can't find question in our FAQ, you can contact us. We'll answer you shortly!</p>
  </div>
</div>
<div class="row justify-content-center gap-sm-0 gap-3">
  <div class="col-sm-6">
    <div class="py-3 rounded bg-faq-section d-flex align-items-center flex-column">
      <div class="avatar">
        <span class="avatar-initial bg-label-secondary rounded">
          <i class="mdi mdi-phone mdi-24px"></i>
        </span>
      </div>
      <h5 class="mt-3"><a class="text-heading" href="tel:+(810)25482568">+ (810) 2548 2568</a></h5>
      <p>We are always happy to help</p>
    </div>
  </div>
  <div class="col-sm-6">
    <div class="py-3 rounded bg-faq-section d-flex align-items-center flex-column">
      <div class="avatar">
        <span class="avatar-initial bg-label-secondary rounded">
          <i class="mdi mdi-email-outline mdi-24px"></i>
        </span>
      </div>
      <h5 class="mt-3"><a class="text-heading" href="mailto:help@help.com">help@help.com</a></h5>
      <p>Best way to get a quick answer</p>
    </div>
  </div>
</div>
<!-- /Contact -->
@endsection
