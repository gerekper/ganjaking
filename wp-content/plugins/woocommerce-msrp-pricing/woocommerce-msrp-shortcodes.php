<?php

class woocommerce_msrp_shortcodes {

	/**
	 * @var woocommerce_msrp_frontend
	 */
	private $msrp_frontend;

	public function __construct() {
		// Store a reference to the frontend class.
		global $woocommerce_msrp_frontend;
		$this->msrp_frontend = $woocommerce_msrp_frontend;

		// Register our shortcodes.
		add_shortcode( 'product_msrp_info', array( $this, 'product_msrp_shortcode' ) );
	}

	public function product_msrp_shortcode( $atts, $content ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : null;
		if ( ! $product_id ) {
			return $content;
		}
		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return $content;
		}

		ob_start();
		$this->msrp_frontend->show_msrp( $product );
		return ob_get_clean();
	}
}
