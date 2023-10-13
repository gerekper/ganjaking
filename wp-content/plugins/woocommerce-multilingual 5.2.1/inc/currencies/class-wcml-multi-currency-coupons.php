<?php

class WCML_Multi_Currency_Coupons {

	public function __construct() {

		add_action('woocommerce_coupon_loaded', array($this, 'filter_coupon_data'));

	}

	public function filter_coupon_data( $coupon ) {

		$discount_type = $coupon->get_discount_type();

		if ( $discount_type === 'fixed_cart' || $discount_type === 'fixed_product' ) {
			$coupon->set_amount( apply_filters( 'wcml_raw_price_amount', $coupon->get_amount() ) );
		}

		$coupon->set_minimum_amount( apply_filters('wcml_raw_price_amount', $coupon->get_minimum_amount() ) );
		$coupon->set_maximum_amount( apply_filters('wcml_raw_price_amount', $coupon->get_maximum_amount() ) );

	}

}
