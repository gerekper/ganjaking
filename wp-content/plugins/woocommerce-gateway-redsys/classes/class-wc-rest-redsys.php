<?php
/**
 * Class WC Rest Redsys
 *
 * @package WooCommerce Redsys Gateway (WooCommerce.com)
 */

/**
 * Class WC Rest Redsys
 */
class WC_REST_Redsys {
	/**
	 * You can extend this class with
	 * WP_REST_Controller / WC_REST_Controller / WC_REST_Products_V2_Controller / WC_REST_CRUD_Controller etc.
	 * Found in packages/woocommerce-rest-api/src/Controllers/
	 */
	protected $namespace = 'wc/v3';

	protected $rest_base = 'redsys';

	public function get_custom( $data ) {
		return array( 'redsys' => 'Data' );
	}

	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'get_custom' ),
			)
		);
	}
}
add_filter( 'woocommerce_rest_api_get_rest_namespaces', 'redsys_custom_api' );

function redsys_custom_api( $controllers ) {
	$controllers['wc/v3']['redsys'] = 'WC_REST_Redsys';

	return $controllers;
}
