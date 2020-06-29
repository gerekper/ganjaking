jQuery(function($) {

	/**
	 * Cart Info
	 */
	$('.cart-collaterals').on('click', 'input.shipping_method', function(){
		if ( $(this).val() == 'yith_wc_product_shipping_method' ) {
			$('.cart-collaterals').addClass('show_yith_wcps_info_cart');
		} else {
			$('.cart-collaterals').removeClass('show_yith_wcps_info_cart');
		}
	});
	if ( $('.cart-collaterals input.shipping_method[checked]').val() == 'yith_wc_product_shipping_method' ) {
		$('.cart-collaterals').addClass('show_yith_wcps_info_cart');
	} else {
		$('.cart-collaterals').removeClass('show_yith_wcps_info_cart');
	}

});
