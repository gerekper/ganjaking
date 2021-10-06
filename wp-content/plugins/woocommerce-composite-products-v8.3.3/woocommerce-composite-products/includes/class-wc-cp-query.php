<?php
/**
 * WC_CP_Query class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    2.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fetches component option IDs.
 *
 * Supports two query types: 1) By product ID and 2) by product category ID.
 * Note that during composite product initialization, custom queries are used to fetch an unpaginated array of product IDs -- @see 'WC_Composite_Product::sync'.
 * This is necessary to sync prices and initialize template parameters.
 * When a component is rendered, sorting / filtering / pagination are handled via 'WC_CP_Component_View::get_options()' which uses the results of the initialization query.
 * Therefore, all rendering queries are done by fetching product IDs directly.
 *
 * You can add your own custom query types by hooking into 'woocommerce_composite_component_query_types' to add the query key/description.
 * Then, implement the query itself by hooking into 'woocommerce_composite_component_options_query_args'.
 *
 * You can add you own custom sorting function by hooking into 'woocommerce_composite_component_orderby' - or you can extend/modfify the behaviour of the 'default' orderby case.
 * To implement it, hook into 'woocommerce_composite_component_options_query_args'.
 *
 * @class    WC_CP_Query
 * @version  3.14.0
 */
class WC_CP_Query {

	/**
	 * Queried results.
	 * @var array
	 */
	private $results;

	/**
	 * Constructor.
	 *
	 * @param  array  $component_data
	 * @param  array  $query_args
	 */
	public function __construct( $component_data, $query_args = array() ) {

		/**
		 * Action 'woocommerce_composite_component_query_start'.
		 *
		 * @param  array  $component_data
		 * @param  array  $query_args
		 */
		do_action( 'woocommerce_composite_component_query_start', $component_data, $query_args );

		$this->query( $component_data, $query_args );

		/**
		 * Action 'woocommerce_composite_component_query_end'.
		 *
		 * @param  array        $component_data
		 * @param  array        $query_args
		 * @param  WC_CP_Query  $this
		 */
		do_action( 'woocommerce_composite_component_query_end', $component_data, $query_args, $this );
	}

	/**
	 * Get queried component option IDs.
	 *
	 * @return array
	 */
	public function get_component_options() {
		return ! empty( $this->results[ 'component_options' ] ) ? $this->results[ 'component_options' ] : array();
	}

	/**
	 * Query args getter.
	 *
	 * @return array
	 */
	public function get_query_args() {
		return ! empty( $this->results[ 'query_args' ] ) ? $this->results[ 'query_args' ] : array();
	}

	/**
	 * True if the query was paged and there is more than one page to show.
	 *
	 * @return boolean
	 */
	public function has_pages() {
		return isset( $this->results[ 'pages' ] ) ? $this->results[ 'pages' ] > 1 : false;
	}

	/**
	 * Get the page number of the query.
	 *
	 * @return int
	 */
	public function get_current_page() {
		return ! empty( $this->results[ 'current_page' ] ) ? $this->results[ 'current_page' ] : array();
	}

	/**
	 * Get the total number of pages.
	 *
	 * @return int
	 */
	public function get_pages_num() {
		return isset( $this->results[ 'pages' ] ) ? $this->results[ 'pages' ] : 1;
	}

	/**
	 * Runs the query.
	 *
	 * @param  array  $component_data
	 * @param  array  $query_args
	 */
	private function query( $component_data, $query_args ) {
		$data_store    = WC_Data_Store::load( 'product-composite' );
		$this->results = $data_store->query_component_options( $component_data, $query_args );
	}
}
