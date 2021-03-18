<?php

class WC_Swatches_Ajax_Handler {

	private static $instance;

	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Swatches_Ajax_Handler;
		}
	}

	private function __construct() {
		add_action( 'wp_ajax_get_product_variations', array($this, 'get_product_variations') );
		add_action( 'wp_ajax_nopriv_get_product_variations', array($this, 'get_product_variations') );

		add_action( 'woocommerce_delete_product_transients', array($this, 'on_deleted_transient'), 10, 1 );
	}

	public function get_product_variations() {
		if ( !isset( $_POST['product_id'] ) || empty( $_POST['product_id'] ) ) {
			wp_send_json_error();
			die();
		}

		$product = wc_get_product( $_POST['product_id'] );
		$variations = $this->wc_swatches_get_available_variations( $product );

		wp_send_json_success( $variations );
		die();
	}

	/**

	/**
	 * Get an array of available variations for the a product.
	 * Use this function as it's faster than the core implementation.
	 * @param WC_Product $product
	 *
	 * @return array|mixed
	 */
	private function wc_swatches_get_available_variations( $product ) {
		global $wpdb;
		
		$transient_name = 'wc_swatches_av_' . $product->get_id();
		$transient_data = get_transient($transient_name);
		if (!empty($transient_data)){
			return $transient_data;
		}
		
		$available_variations = array();

		//Get the children all in one call.  
		//This will prime the WP_Post cache so calls to get_child are much faster. 

		$args = array(
		    'post_parent' => $product->get_id(),
		    'post_type' => 'product_variation',
		    'orderby' => 'menu_order',
		    'order' => 'ASC',
		    'post_status' => 'publish',
		    'numberposts' => -1,
		    'no_found_rows' => true
		);
		$children = get_posts( $args );

		foreach ( $children as $child ) {
			$variation = wc_get_product( $child );

			// Hide out of stock variations if 'Hide out of stock items from the catalog' is checked
			$id = $variation->get_id();
			if ( empty( $id ) || ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && !$variation->is_in_stock() ) ) {
				continue;
			}


			// Filter 'woocommerce_hide_invisible_variations' to optionally hide invisible variations (disabled variations and variations with empty price)
			if ( apply_filters( 'woocommerce_hide_invisible_variations', false, $product->get_id(), $variation ) && !$variation->variation_is_visible() ) {
				continue;
			}

			$available_variations[] = array(
			    'variation_id' => $variation->get_id(),
			    'variation_is_active' => $variation->variation_is_active(),
			    'attributes' => $variation->get_variation_attributes(),
			);
		}
		set_transient( $transient_name, $available_variations, DAY_IN_SECONDS * 30 );
		return $available_variations;
	}

	public function on_deleted_transient( $product_id ) {
		delete_transient( 'wc_swatches_av_' . $product_id );
	}

}

WC_Swatches_Ajax_Handler::register();
