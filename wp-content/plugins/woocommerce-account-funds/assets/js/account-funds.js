jQuery(document).ready(function($) {
    $( '#order_review' ).on('change', 'input[name=payment_method]', function() {
    	if ( $('#payment_method_accountfunds').size() ) {
    		$('body').trigger( 'update_checkout' );
    	}
    });
});