jQuery(document).ready(function($){
  var delay_field = $('#woocommerce_yith_paypal_adaptive_payments_payment_delay');

   $('#woocommerce_yith_paypal_adaptive_payments_pay_method').on('change', function(){

       var value = $(this).val(),
           delay_row = delay_field.parents('tr');

       if( value == 'parallel' ){

           delay_row.hide();
       }else{

           delay_row.show();
       }
   }).change();
});
