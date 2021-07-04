<?php

class woocommerce_msrp_woocommerce_product_feeds_integration {
	/**
	 * Add filters.
	 */
	public function run() {
		add_filter( 'woocommerce_gpf_custom_field_list', array( $this, 'register_field' ) );
	}

	/**
	 * Register the field so it can be chosen as a prepopulate option.
	 *
	 * @param $field_list
	 *
	 * @return mixed
	 */
	public function register_field( $field_list ) {
		$field_list['method:woocommerce_msrp_woocommerce_product_feeds_integration::get_msrp'] = __( 'MSRP from MSRP Pricing extension', 'woocommerce_msrp' );

		return $field_list;
	}

	/**
	 * Generate the MSRP price value for a product.
	 *
	 * @param $wc_product
	 *
	 * @return string
	 */
	public static function get_msrp( $wc_product ) {
		if ( 'variation' === $wc_product->get_type() ) {
			$msrp_price = $wc_product->get_meta( '_msrp' );
		} else {
			$msrp_price = $wc_product->get_meta( '_msrp_price' );
		}
		if ( '' === $msrp_price ) {
			return '';
		}
		$price_string = number_format( (float) $msrp_price, 2, '.', '' );

		return $price_string . ' ' . get_woocommerce_currency();
	}
}

