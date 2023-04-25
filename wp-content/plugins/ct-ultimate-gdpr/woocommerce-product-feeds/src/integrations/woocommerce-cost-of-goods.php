<?php

class WoocommerceCostOfGoods {

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
		$field_list['method:WoocommerceCostOfGoods::getCostPrice'] = __( 'Cost price from Cost of Goods extension', 'woocommerce_gpf' );

		return $field_list;
	}

	/**
	 * Generate the cost price value for a product.
	 *
	 * @param $wc_product
	 *
	 * @return string
	 *
	 * phpcs:disable WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	 */
	public static function getCostPrice( $wc_product ) {
		$cost_price = WC_COG_Product::get_cost( $wc_product );
		if ( '' === $cost_price ) {
			return '';
		}
		$price_string = number_format( (float) $cost_price, 2, '.', '' );

		return $price_string . ' ' . get_woocommerce_currency();
	}
	// phpcs:enable WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
}

