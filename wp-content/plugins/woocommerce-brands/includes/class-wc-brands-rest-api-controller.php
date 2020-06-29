<?php
/**
 * REST API Brands controller.
 *
 * Handles requests to /products/brands endpoint.
 *
 * @since 1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Brands_REST_API_Controller extends WC_REST_Product_Categories_Controller {

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
