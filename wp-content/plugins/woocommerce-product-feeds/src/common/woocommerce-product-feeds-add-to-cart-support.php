<?php

class WoocommerceProductFeedsAddToCartSupport {
	/**
	 * Strip the woocommerce_gpf_ prefix from any add-to-cart query arguments if present.
	 *
	 * @return void
	 */
	public function initialise() {
		add_action( 'init', [ $this, 'init' ] );
	}

	/**
	 * @return void
	 */
	public function init() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_REQUEST['add-to-cart'] ) ) {
			return;
		}
		$_REQUEST['add-to-cart'] = str_replace( 'woocommerce_gpf_', '', sanitize_text_field( $_REQUEST['add-to-cart'] ) );
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}
}
