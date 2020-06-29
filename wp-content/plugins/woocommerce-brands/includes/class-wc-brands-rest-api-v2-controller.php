<?php
/**
 * REST API Brands controller for WC 3.5+
 *
 * Handles requests to /products/brands endpoint.
 *
 * @since 1.6.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Brands_REST_API_V2_Controller extends WC_REST_Product_Categories_V2_Controller {

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'products/brands';

	/**
	 * Taxonomy.
	 *
	 * @var string
	 */
	protected $taxonomy = 'product_brand';
}
