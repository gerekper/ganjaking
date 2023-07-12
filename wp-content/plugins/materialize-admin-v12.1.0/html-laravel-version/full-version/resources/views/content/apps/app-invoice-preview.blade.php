@extends('layouts/layoutMaster')

@section('title', 'Preview - Invoice')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-invoice.css')}}" />
@endsection

@section('page-script')
<script src="{{asset('assets/js/offcanvas-add-payment.js')}}"></script>
<script src="{{asset('assets/js/offcanvas-send-invoice.js')}}"></script>
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
@endsection

@section('content')

<div class="row invoice-preview">
  <!-- Invoice -->
  <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
    <div class="card invoice-preview-card">
      <div class="card-body">
        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
          <div class="mb-xl-0 pb-3">
            <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
              <span class="app-brand-logo demo">@include('_partials.macros',["width"=>25,"withbg"=>'#666cff'])</span>
              <span class="h4 mb-0 app-brand-text fw-bold">{{ config('variables.templateName') }}</span>
            </div>
            <p class="mb-1">Office 149, 450 South Brand Brooklyn</p>
            <p class="mb-1">San Diego County, CA 91905, USA</p>
            <p class="mb-0">+1 (123) 456 7891, +44 (876) 543 2198</p>
          </div>
          <div>
            <h5>INVOICE #86423</h5>
            <div class="mb-1">
              <span>Date Issues:</span>
              <span>April 25, 2021</span>
            </div>
            <div>
              <span>Date Due:</span>
              <span>May 25, 2021</span>
            </div>
          </div>
        </div>
      </div>
      <hr class="my-0" />
      <div class="card-body">
        <div class="d-flex justify-content-between flex-wrap">
          <div class="my-3">
            <h6 class="pb-2">Invoice To:</h6>
            <p class="mb-1">Thomas shelby</p>
            <p class="mb-1">Shelby Company Limited</p>
            <p class="mb-1">Small Heath, B10 0HF, UK</p>
            <p class="mb-1">718-986-6062</p>
            <p class="mb-0">peakyFBlinders@gmail.com</p>
          </div>
          <div class="my-3">
            <h6 class="pb-2">Bill To:</h6>
            <table>
              <tbody>
                <tr>
                  <td class="pe-3 fw-medium">Total Due:</td>
                  <td>$12,110.55</td>
                </tr>
                <tr>
                  <td class="pe-3 fw-medium">Bank name:</td>
                  <td>American Bank</td>
                </tr>
                <tr>
                  <td class="pe-3 fw-medium">Country:</td>
                  <td>United States</td>
                </tr>
                <tr>
                  <td class="pe-3 fw-medium">IBAN:</td>
                  <td>ETD95476213874685</td>
                </tr>
                <tr>
                  <td class="pe-3 fw-medium">SWIFT code:</td>
                  <td>BR91905</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table m-0">
          <thead class="table-light border-top">
            <tr>
              <th>Item</th>
              <th>Description</th>
              <th>Cost</th>
              <th>Qty</th>
              <th>Price</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="text-nowrap">Vuexy Admin Template</td>
              <td class="text-nowrap">HTML Admin Template</td>
              <td>$32</td>
              <td>1</td>
              <td>$32.00</td>
            </tr>
            <tr>
              <td class="text-nowrap">Frest Admin Template</td>
              <td class="text-nowrap">Angular Admin Template</td>
              <td>$22</td>
              <td>1</td>
              <td>$22.00</td>
            </tr>
            <tr>
              <td class="text-nowrap">Apex Admin Template</td>
              <td class="text-nowrap">HTML Admin Template</td>
              <td>$17</td>
              <td>2</td>
              <td>$34.00</td>
            </tr>
            <tr>
              <td class="text-nowrap">Robust Admin Template</td>
              <td class="text-nowrap">React Admin Template</td>
              <td>$66</td>
              <td>1</td>
              <td>$66.00</td>
            </tr>
            <tr>
              <td colspan="3" class="align-top px-4 py-5">
                <p class="mb-2">
                  <span class="me-1 fw-semibold">Salesperson:</span>
                  <span>Alfie Solomons</span>
                </p>
                <span>Thanks for your business</span>
              </td>
              <td class="text-end px-4 py-5">
                <p class="mb-2">Subtotal:</p>
                <p class="mb-2">Discount:</p>
                <p class="mb-2">Tax:</p>
                <p class="mb-0">Total:</p>
              </td>
              <td class="px-4 py-5">
                <p class="fw-semibold mb-2 text-end">$154.25</p>
                <p class="fw-semibold mb-2 text-end">$00.00</p>
                <p class="fw-semibold mb-2 text-end">$50.00</p>
                <p class="fw-semibold mb-0 text-end">$204.25</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-body">
        <div class="row">
          <div class="col-12">
            <span class="fw-bold">Note:</span>
            <span>It was a pleasure working with you and your team. We hope you will keep us in mind for future freelance
              projects. Thank You!</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /Invoice -->

  <!-- Invoice Actions -->
  <div class="col-xl-3 col-md-4 col-12 invoice-actions">
    <div class="card">
      <div class="card-body">
        <button class="btn btn-primary d-grid w-100 mb-3" data-bs-toggle="offcanvas" data-bs-target="#sendInvoiceOffcanvas">
          <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="mdi mdi-send-outline scaleX-n1-rtl me-1"></i>Send Invoice</span>
        </button>
        <button class="btn btn-outline-secondary d-grid w-100 mb-3">
          Download
        </button>
        <a class="btn btn-outline-secondary d-grid w-100 mb-3" target="_blank" href="{{url('app/invoice/print')}}">
          Print
        </a>
        <a href="{{url('app/invoice/edit')}}" class="btn btn-outline-secondary d-grid w-100 mb-3">
          Edit Invoice
        </a>
        <button class="btn btn-success d-grid w-100" data-bs-toggle="offcanvas" data-bs-target="#addPaymentOffcanvas">
          <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="mdi mdi-currency-usd me-1"></i>Add Payment</span>
        </button>
      </div>
    </div>
  </div>
  <!-- /Invoice Actions -->
</div>

<!-- Offcanvas -->
@include('_partials/_offcanvas/offcanvas-send-invoice')
@include('_partials/_offcanvas/offcanvas-add-payment')
<!-- /Offcanvas -->
@endsection
