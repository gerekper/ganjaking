<?php

class WoocommerceProductFeedsFacebookForWoocommerce {

	/**
	 * Add filters.
	 */
	public function run() {
		add_filter( 'woocommerce_gpf_custom_field_list', array( $this, 'register_fields' ) );
	}

	/**
	 * Register the field so it can be chosen as a prepopulate option.
	 *
	 * @param $field_list
	 *
	 * @return mixed
	 */
	public function register_fields( $field_list ) {
		$field_list['disabled:fb4wc']
			= __( '-- Fields from Facebook for WooCommerce --', 'woocommerce_gpf' );

		$field_list['meta:_wc_facebook_enhanced_catalog_attributes_brand']    = __( 'Brand', 'woocommerce_gpf' );
		$field_list['meta:_wc_facebook_enhanced_catalog_attributes_color']    = __( 'Color', 'woocommerce_gpf' );
		$field_list['meta:_wc_facebook_enhanced_catalog_attributes_material'] = __( 'Material', 'woocommerce_gpf' );
		$field_list['meta:_wc_facebook_enhanced_catalog_attributes_pattern']  = __( 'Pattern', 'woocommerce_gpf' );
		$field_list['meta:_wc_facebook_enhanced_catalog_attributes_size']     = __( 'Size', 'woocommerce_gpf' );

		return $field_list;
	}
}

