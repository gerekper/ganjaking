<?php

class WoocommerceProductFeedsWoocommerceGermanized {

	/**
	 * CCheck versions and run the integration if suitable.
	 */
	public function run() {
		// Check version is 3.7.0 or higher.
		$instance = WooCommerce_Germanized::instance();
		if ( empty( $instance->version ) || version_compare( '3.7.0', $instance->version, '>=' ) ) {
			return;
		}
		add_filter( 'woocommerce_gpf_custom_field_list', array( $this, 'register_fields' ) );
	}

	public function register_fields( $fields ) {
		$fields['disabled:wcgermanized']
			= __( '-- Fields from "WooCommerce Germanized" --', 'woocommerce_gpf' );
		$fields['meta:_ts_gtin']         = 'GTIN field from WooCommerce Germanized';
		$fields['meta:_ts_mpn']          = 'MPN field from WooCommerce Germanized';
		return $fields;
	}
}
