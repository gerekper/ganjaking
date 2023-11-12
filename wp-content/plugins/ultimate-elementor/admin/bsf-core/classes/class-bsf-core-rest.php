<?php
/**
 * BSF Core REST API
 *
 * @package bsf-core
 */

/**
 * License Activation/Deactivation REST API.
 */
class Bsf_Core_Rest {

	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 * The namespace of this controller's route.
	 *
	 * @var string
	 */
	public $namespace;

	/**
	 * The base of this controller's route.
	 *
	 * @var string
	 */
	public $rest_base;

	/**
	 * Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->namespace = 'bsf-core/v1';
		$this->rest_base = '/license';

		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			$this->rest_base . '/activate',
			array(
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'activate_license' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args' => array(
					'product-id' => array(
						'type'     => 'string',
						'required' => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'license-key' => array(
						'type'     => 'string',
						'required' => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);
	}

	/**
	 * Check if a given request has access to activate license.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Activate License Key.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response Rest Response with access key.
	 */
	public function activate_license( $request ) {
		$product_id = $request->get_param( 'product-id' );
		$license_key = $request->get_param( 'license-key' );

		$data = array(
			'privacy_consent' => true,
			'terms_conditions_consent' => true,
			'product_id' => $product_id,
			'license_key' => $license_key,
		);

		return rest_ensure_response( BSF_License_Manager::instance()->bsf_process_license_activation( $data ) );
	}

}

Bsf_Core_Rest::get_instance();
