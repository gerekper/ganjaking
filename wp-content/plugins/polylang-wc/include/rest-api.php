<?php
/**
 * @package Polylang-WC
 */

/**
 * A class to filter the REST API.
 * Needs Polylang Pro 2.2.1 or later.
 * Tested with the WC API v2 or later ( WC 3.0 or later ).
 *
 * @since 0.9
 */
class PLLWC_REST_API {
	/**
	 * @var PLLWC_REST_Product
	 */
	public $product;

	/**
	 * @var PLLWC_REST_Order
	 */
	public $order;

	/**
	 * Constructor.
	 * Setups actions and filters.
	 *
	 * @since 0.9
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'init' ), 20 ); // After Polylang.
		add_filter( 'pll_rest_api_post_types', array( $this, 'post_types' ) );
		add_filter( 'pll_rest_api_taxonomies', array( $this, 'taxonomies' ) );
	}

	/**
	 * Initializes filters after the Polylang REST API has been initialized.
	 *
	 * @since 0.9
	 *
	 * @return void
	 */
	public function init() {
		$this->product = new PLLWC_REST_Product();
		$this->order = new PLLWC_REST_Order();
	}

	/**
	 * Removes the translations from the response when querying orders.
	 *
	 * @since 0.9
	 *
	 * @param array $args Options passed to PLL_REST_Post.
	 * @return array
	 */
	public function post_types( $args ) {
		$args['product_variation'] = array();
		$args['shop_order']['translations'] = false;
		return $args;
	}

	/**
	 * Adds the language and translations in the response when querying product attributes terms.
	 *
	 * @since 0.9
	 *
	 * @param array $args Options passed to PLL_REST_Term.
	 * @return array
	 */
	public function taxonomies( $args ) {
		$args['product_cat'] = array();
		$args['product_tag'] = array();
		$args['product_attribute_term']['filters'] = false;
		return $args;
	}
}
