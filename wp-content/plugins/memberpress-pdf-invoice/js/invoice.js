jQuery(document).ready(function ($) {
  // alert('hi');
  $('body').on('click', '#mepr-biz-logo-remove', function (e) {
    e.preventDefault();
    $('input[name="mepr_biz_logo_remove"]').val('1');
    $(this).closest('form').submit();
  });

  // Add Color Picker to all inputs that have 'color-field' class
  $('.mepr-color-picker').wpColorPicker();

  // Prompt
  $('#submit').click(function() {


    if(
      $("input[name='invoice_num']").length > 0 &&
      $("input[name='invoice_num']").val() > 0 &&
      MeprPDFInvoice.last_txn > 0)
    {
      let invoice_num = parseInt($("input[name='invoice_num']").val());
      let last_txn = parseInt(MeprPDFInvoice.last_txn);

      if(invoice_num < last_txn){
        if (confirm(MeprPDFInvoice.invoice_num_confirm)) {
          return true
        } else {
          return false;
        }
      }
    }
    return true; // return false to cancel form action
  });

});
