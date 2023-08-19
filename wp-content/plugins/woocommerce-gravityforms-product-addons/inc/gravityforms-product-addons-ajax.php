<?php
/** Disable AJAX features from common themes and page builders */

class WC_GFPA_AJAX {
	private static WC_GFPA_AJAX $instance;

	public static function register(): WC_GFPA_AJAX {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		add_filter( 'astra_get_option_single-product-add-to-cart-action', [
			$this,
			'astra_get_option_single_product_add_to_cart_action'
		], 10, 1 );
	}

	public function astra_get_option_single_product_add_to_cart_action( $single_ajax_add_to_cart ) {
		if ( is_singular( 'product' ) ) {
			$product = wc_get_product( get_the_id() );
			if ( false !== $product && wc_gfpa()->get_gravity_form_data( $product->get_id() ) ) {
				// Disable Ajax Add to Cart feature for External/Affiliate product.
				$single_ajax_add_to_cart = 'default';
			}
		}

		return $single_ajax_add_to_cart;
	}
}
