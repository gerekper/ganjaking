<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Product_Addons_Migration_3_0_Product {
	/**
	 * Constructor
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		add_action( 'template_redirect', array( $this, 'migrate' ) );

		if ( is_admin() ) {
			add_action( 'pre_get_posts', array( $this, 'migrate' ) );
		}
	}

	/**
	 * Try to run migration on product.
	 *
	 * @since 3.0.0
	 */
	public function migrate() {
		if ( 'product' !== get_post_type() ) {
			return;
		}

		$post_id   = get_the_ID();
		$converted = get_post_meta( $post_id, '_product_addons_converted', true );

		if ( empty( $converted ) ) {
			$addon_fields = get_post_meta( $post_id, '_product_addons', true );

			if ( empty( $addon_fields ) ) {
				return;
			}

			/*
			 * If the addon is already 3.0, don't convert again.
			 * We just need to check the first array element for "adjust_price"
			 * as that is a new 3.0 element.
			 */
			if ( array_key_exists( 'adjust_price', $addon_fields[0] ) ) {
				return;
			}

			// Save a backup of non converted addons just in case.
			update_post_meta( $post_id, '_product_addons_old', $addon_fields );

			$updated_addon_fields = WC_Product_Addons_3_0_Conversion_Helper::do_conversion( $addon_fields );
			update_post_meta( $post_id, '_product_addons', $updated_addon_fields );
			update_post_meta( $post_id, '_product_addons_converted', 'yes' );
		}		
	}
}

new WC_Product_Addons_Migration_3_0_product();
