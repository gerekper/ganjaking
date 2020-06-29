<?php

/**
 * REST API shipment tracking controller.
 *
 * Handles requests to /orders/shipment-tracking endpoint.
 *
 * @since 1.5.0
 */

class WC_Shipment_Tracking_REST_API_Controller extends WC_REST_Controller {

	/**
	 * Endpoint namespace.
	 * This should not be in wc/* because shippment tracking does not need to follow WC core apis.
	 *
	 * @var string
	 */
	protected $namespace = 'wc-shipment-tracking/v3';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'shipment-trackings';

	/**
	 * @param $namespace
	 *
	 * @return WC_Shipment_Tracking_REST_API_Controller
	 */
	public function set_namespace( $namespace ) {
		$this->namespace = $namespace;
		return $this;
	}

	/**
	 * Register the routes for trackings.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/providers', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_providers' ),
			),
		) );
	}

	/**
	 * Get shipment-tracking providers.
	 *
	 * @param WP_REST_Request $request
	 * @return array
	 */
	public function get_providers( $request ) {
		$st = WC_Shipment_Tracking_Actions::get_instance();
		return rest_ensure_response( $st->get_providers() );
	}
}
