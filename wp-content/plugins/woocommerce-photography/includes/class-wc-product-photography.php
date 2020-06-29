<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WooCommerce Photography Product.
 *
 * @package  WC_Photography/Product
 * @category Class
 * @author   WooThemes
 */
class WC_Product_Photography extends WC_Product {

	/**
	 * Initialize the Photography product.
	 *
	 * @param mixed $product
	 */
	public function __construct( $product ) {
		$this->product_type = 'photography';
		$this->supports[]   = 'ajax_add_to_cart';
		parent::__construct( $product );
	}

	/**
	 * Get the add to url used mainly in loops.
	 *
	 * @return string
	 */
	public function add_to_cart_url() {
		$url = esc_url( remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->id ) ) );

		return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
	}

	/**
	 * Get the add to cart button text.
	 *
	 * @return string
	 */
	public function add_to_cart_text() {
		return apply_filters( 'woocommerce_product_add_to_cart_text', __( 'Add to cart', 'woocommerce-photography' ), $this );
	}

	/**
	 * Returns the product categories.
	 *
	 * @param string $sep (default: ', ')
	 * @param string $before (default: '')
	 * @param string $after (default: '')
	 *
	 * @return string
	 */
	public function get_collections( $sep = ', ', $before = '', $after = '' ) {
		return get_the_term_list( $this->id, 'images_collections', $before, $sep, $after );
	}

}
