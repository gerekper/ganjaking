<?php
/**
 * REST API Groups Controller
 *
 * Handles requests to the groups endpoints.
 *
 * @author   WooCommerce
 * @category API
 * @package  Product-Add-Ons/API
 * @since    2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Product_Add_Ons_Groups_Controller' ) ) {
	return;
}

class WC_Product_Add_Ons_Groups_Controller extends WP_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc-product-add-ons/v1';

	/**
	 * Endpoint method (GET/POST/PUT...)
	 * @var string
	 */
	protected $method;

	public function __construct() {
	}

	/**
	 * Register the route for groups
	 *
	 * @since 2.9.0
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/product-add-ons', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_item' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			),
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_all' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			),
		) );
		register_rest_route( $this->namespace, '/product-add-ons/(?P<id>\d+)', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			),
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_item' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			),
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_item' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			),
		) );
	}

	/**
	 * Add (POST/CREATABLE) a global group
	 *
	 * @since 2.9.0
	 * @param $request WP_REST_Request
	 * @return WP_REST_Response| mixed
	 */
	public function create_item( $request ) {
		$allowed_keys = array( 'name', 'priority', 'restrict_to_categories', 'fields' );
		$filtered_request = array_intersect_key( $request->get_params(), array_flip( $allowed_keys ) );
		$result = Product_Addon_Global_Group::create_group( wc_clean( $filtered_request ) );
		if ( is_wp_error( $result ) ) {
			return new WP_Error(
				'woocommerce_product_add_ons_rest__' . $result->get_error_code(),
				$result->get_error_message(),
				array( 'status' => 400 ) // bad request
			);
		}

		return rest_ensure_response( $result );
	}

	/**
	 * Get (GET/READABLE) all (global) groups
	 *
	 * @since 2.9.0
	 * @param $request WP_REST_Request
	 * @return WP_REST_Response| mixed
	 */
	public function get_all( $request ) {
		return rest_ensure_response( Product_Addon_Groups::get_all_global_groups() );
	}

	/**
	 * Get (GET/READABLE) a single group
	 *
	 * @since 2.9.0
	 * @param $request WP_REST_Request
	 * @return WP_REST_Response| WP_Error
	 */
	public function get_item( $request ) {
		$result = Product_Addon_Groups::get_group( wc_clean( $request['id'] ) );
		if ( is_wp_error( $result ) ) {
			return new WP_Error(
				'woocommerce_product_add_ons_rest__' . $result->get_error_code(),
				$result->get_error_message(),
				array( 'status' => 404 ) // not found
			);
		}

		return rest_ensure_response( $result );
	}

	/**
	 * Update (PUT/EDITABLE) a global group or product
	 *
	 * @since 2.9.0
	 * @param $request WP_REST_Request
	 * @return WP_REST_Response| WP_Error
	 */
	public function update_item( $request ) {
		$allowed_keys = array( 'name', 'priority', 'restrict_to_categories', 'fields', 'exclude_global_add_ons' );
		$filtered_request = array_intersect_key( $request->get_params(), array_flip( $allowed_keys ) );
		$result = Product_Addon_Groups::update_group( wc_clean( $request['id'] ), wc_clean( $filtered_request ) );
		if ( is_wp_error( $result ) ) {
			return new WP_Error(
				'woocommerce_product_add_ons_rest__' . $result->get_error_code(),
				$result->get_error_message(),
				array( 'status' => 400 ) // bad request
			);
		}

		return rest_ensure_response( $result );
	}

	/**
	 * Delete (DELETE/DELETABLE) a global group
	 *
	 * @since 2.9.0
	 * @param $request array REST API request
	 * @return WP_REST_Response| WP_Error
	 */
	public function delete_item( $request ) {
		$result = Product_Addon_Groups::delete_group( wc_clean( $request['id'] ) );
		if ( is_wp_error( $result ) ) {
			return new WP_Error(
				'woocommerce_product_add_ons_rest__' . $result->get_error_code(),
				$result->get_error_message(),
				array( 'status' => 404 ) // not found
			);
		}
		return rest_ensure_response( $result );
	}

	/* Validate the requester's permissions
	 *
	 * @since 2.9.0
	 *
	 * @param $request
	 * @return boolean
	 */
	public function permissions_check( $request ) {
		if ( current_user_can( 'manage_woocommerce' ) || current_user_can( 'manage_options' ) ) {
			return true;
		};

		return new WP_Error(
			'woocommerce_product_add_ons_rest__unauthorized',
			'You do not have permission to access this resource.',
			array( 'status' => is_user_logged_in() ? 403 : 401 )
		);
	}
}
