/**
 * ywsbs-subscription.coupon.js
 *
 * @author YITH
 * @package YITH WooCommerce Subscription
 * @version 2.3.0
 */
/* global ywsbs_subscription_admin */
jQuery(function ($) {
	$('.ywsbs_limited_for_payments_field').hide();

	$(document).on('change', '#discount_type', function(){
		var $t = $(this),
			value = $t.val();

		if( 'recurring_percent' === value || 'recurring_fixed'  === value ){
			$('.ywsbs_limited_for_payments_field').show();
		}else{
			$('.ywsbs_limited_for_payments_field').hide();
		}
	});

	$('#discount_type').change();
});