<?php
/**
 * WooCommerce Bookings API
 *
 * @package WooCommerce\Bookings\Rest
 */

/**
 * API class which registers all the routes.
 */
class WC_Bookings_REST_API {

	const V1_NAMESPACE = 'wc-bookings/v1';

	/**
	 * Construct.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	/**
	 * Initialize the REST API.
	 */
	public function rest_api_init() {
		$controller = new WC_Bookings_REST_Products_Controller();
		$controller->register_routes();

		$controller = new WC_Bookings_REST_Products_Categories_Controller();
		$controller->register_routes();

		$controller = new WC_Bookings_REST_Resources_Controller();
		$controller->register_routes();

		$controller = new WC_Bookings_REST_Booking_Controller();
		$controller->register_routes();

		$controller = new WC_Bookings_REST_Products_Slots_Controller();
		$controller->register_routes();
	}
}
