<!-- Add Payment Sidebar -->
<div class="offcanvas offcanvas-end" id="addPaymentOffcanvas" aria-hidden="true">
  <div class="offcanvas-header mb-3">
    <h5 class="offcanvas-title">Add Payment</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body flex-grow-1">
    <div class="d-flex justify-content-between bg-lighter p-2 mb-3">
      <p class="mb-0">Invoice Balance:</p>
      <p class="fw-bold mb-0">$5000.00</p>
    </div>
    <form>
      <div class="input-group input-group-merge mb-4">
        <span class="input-group-text">$</span>
        <div class="form-floating form-floating-outline">
          <input type="text" id="invoiceAmount" name="invoiceAmount" class="form-control invoice-amount" placeholder="100" />
          <label for="invoiceAmount">Payment Amount</label>
        </div>
      </div>
      <div class="form-floating form-floating-outline mb-4">
        <input id="payment-date" class="form-control invoice-date" type="text" />
        <label for="payment-date">Payment Date</label>
      </div>
      <div class="form-floating form-floating-outline mb-4">
        <select class="form-select" id="payment-method">
          <option value="" selected disabled>Select payment method</option>
          <option value="Cash">Cash</option>
          <option value="Bank Transfer">Bank Transfer</option>
          <option value="Debit Card">Debit Card</option>
          <option value="Credit Card">Credit Card</option>
          <option value="Paypal">Paypal</option>
        </select>
        <label for="payment-method">Payment Method</label>
      </div>
      <div class="form-floating form-floating-outline mb-4">
        <textarea class="form-control" id="payment-note" style="height: 62px;"></textarea>
        <label for="payment-note">Internal Payment Note</label>
      </div>
      <div class="mb-3 d-flex flex-wrap">
        <button type="button" class="btn btn-primary me-3" data-bs-dismiss="offcanvas">Send</button>
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
      </div>
    </form>
  </div>
</div>
<!-- /Add Payment Sidebar -->
