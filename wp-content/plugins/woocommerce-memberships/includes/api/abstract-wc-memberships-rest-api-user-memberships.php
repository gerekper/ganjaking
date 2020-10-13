<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\API\Controller;

use SkyVerge\WooCommerce\Memberships\API\Controller;
use SkyVerge\WooCommerce\Memberships\Profile_Fields;
use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * User Memberships REST API handler.
 *
 * @since 1.12.0
 */
class User_Memberships extends Controller {


	/**
	 * User Memberships REST API constructor.
	 *
	 * @since 1.11.0
	 */
	public function __construct() {

		parent::__construct();

		$this->rest_base   = 'members';
		$this->post_type   = 'wc_user_membership';
		$this->object_name = __( 'User Membership', 'woocommerce-memberships' );
	}


	/**
	 * Gets a user membership from a valid identifier.
	 *
	 * @since 1.13.0
	 *
	 * @param string|int|\WP_Post $id membership identifier
	 * @return \WC_Memberships_User_Membership
	 */
	protected function get_object( $id ) {

		$membership = wc_memberships_get_user_membership( $id );

		return $membership instanceof \WC_Memberships_User_Membership ? $membership : null;
	}


	/**
	 * Registers user memberships WP REST API routes.
	 *
	 * @see \SkyVerge\WooCommerce\Memberships\REST_API::register_routes()
	 *
	 * @since 1.11.0
	 */
	public function register_routes() {

		// endpoint: 'wc/v<n>/memberships/members/'
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			// GET all user memberships
			array(
				'methods'             => \WP_REST_Server::READABLE,
				/* @see Controller::get_items() */
				'callback'            => array( $this, 'get_items' ),
				/* @see Controller::get_items_permissions_check() */
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'                => $this->get_collection_params(),
			),
			// POST a user membership
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				/** @see User_Memberships::create_item() */
				'callback'            => array( $this, 'create_item' ),
				/** @see Controller::create_item_permissions_check() */
				'permission_callback' => array( $this, 'create_item_permissions_check' ),
				'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::CREATABLE ),
			),
			/** @see User_Memberships::get_item_schema() */
			'schema' => array( $this, 'get_public_item_schema' ),
		), true );

		// endpoint: 'wc/v<n>/memberships/members/<id>'
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
			'args'   => array(
				'id' => array(
					'description' => __( 'Unique identifier of a user membership.', 'woocommerce-memberships' ),
					'type'        => 'integer',
				),
			),
			// GET a user membership
			array(
				'methods'             => \WP_REST_Server::READABLE,
				/** @see Controller::get_item() */
				'callback'            => array( $this, 'get_item' ),
				/** @see Controller::get_item_permissions_check() */
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'                => array(
					'context' => $this->get_context_param( array( 'default' => 'view' ) ),
				),
			),
			// UPDATE a user membership
			array(
				'methods'             => \WP_REST_Server::EDITABLE,
				/** @see User_Memberships::update_item() */
				'callback'            => array( $this, 'update_item' ),
				/** @see Controller::update_item_permissions_check() */
				'permission_callback' => array( $this, 'update_item_permissions_check' ),
				'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::EDITABLE ),
			),
			// DELETE a user membership
			array(
				'methods'             => \WP_REST_Server::DELETABLE,
				/** @see Controller::delete_item() */
				'callback'            => array( $this, 'delete_item' ),
				/** @see Controller::delete_item_permissions_check() */
				'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				'args'                => array(),
			),
			/** @see User_Memberships::get_item_schema() */
			'schema' => array( $this, 'get_public_item_schema' ),
		), true );

		// endpoint: 'wc/v<n>/memberships/members/batch'
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/batch', array(
			array(
				'methods'             => \WP_REST_Server::EDITABLE,
				/** @see Controller::batch_items() */
				'callback'            => array( $this, 'batch_items' ),
				/** @see Controller::batch_items_permissions_check() */
				'permission_callback' => array( $this, 'batch_items_permissions_check' ),
				'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::EDITABLE ),
			),
			/** @see User_Memberships::get_public_batch_schema() */
			'schema' => array( $this, 'get_public_batch_schema' ),
		) );
	}


	/**
	 * Creates a user membership via REST API.
	 *
	 * @internal
	 *
	 * @since 1.13.0
	 *
	 * @param \WP_REST_Request $request request object
	 * @return \WP_Error|\WP_REST_Response response object or error object
	 */
	public function create_item( $request ) {

		$existing_membership = null;

		try {

			$request->set_param( 'context', 'edit' );

			/**
			 * Filters the request before handling it for a user membership creation via REST API.
			 *
			 * @since 1.13.0
			 *
			 * @param \WP_REST_Request $request request object
			 */
			$request = apply_filters( 'wc_memberships_rest_api_create_user_membership_request', $request );

			// sanity check in case the filter above changes the request object to an invalid type
			if ( ! $request instanceof \WP_REST_Request ) {
				throw new \WC_REST_Exception( "woocommerce_rest_{$this->post_type}_invalid_request", __( 'Invalid request.', 'woocommerce-memberships' ), 500 );
			}

			// a user and a plan are required parameters in a CREATE request, so an exception may be thrown here
			foreach ( array( 'customer_id', 'plan_id' ) as $required_param ) {
				if ( ! isset( $request[ $required_param ] ) ) {
					throw new \WC_REST_Exception( "woocommerce_rest_{$this->post_type}_missing_{$required_param}", sprintf( __( 'Required %s is missing.', 'woocommerce-memberships' ), $required_param ), 400 );
				}
			}

			// at this point we're sure a user and a plan are in the request, but we need to verify if they exist
			$customer        = $this->get_request_customer_user( $request );
			$membership_plan = $this->get_request_membership_plan( $request );

			// also cannot create a new membership if user already has one for the same plan
			if ( $customer && $membership_plan && ( $existing_membership = wc_memberships_get_user_membership( $customer->ID, $membership_plan ) ) ) {
				/* translators: Placeholders: %1$s - customer ID, %2$s - membership plan ID */
				throw new \WC_REST_Exception( "woocommerce_rest_{$this->post_type}_exists", sprintf( __( 'A membership already exists for user %1$s and plan %2$s.', 'woocommerce-memberships' ), $existing_membership->get_user_id(), $existing_membership->get_plan_id() ), 400 );
			}

			// so far we're good: try to create the membership
			try {
				$user_membership = wc_memberships_create_user_membership( array( 'plan_id' => $membership_plan->get_id(), 'user_id' => $customer->ID ) );
			} catch( \Exception $e ) {
				throw new \WC_REST_Exception( "woocommerce_rest_{$this->post_type}_create_error", $e->getMessage(), 500 );
			}

			// then, set all its accessory properties
			$user_membership = $this->set_user_membership_data( $user_membership, $request );

			/**
			 * Fires after a user membership is created or updated via the REST API.
			 *
			 * @since 1.13.0
			 *
			 * @param \WC_Memberships_User_Membership $user_membership the membership object
			 * @param \WP_REST_Request $request the request object
			 * @param bool $creating true when creating a new user membership, false when updating
			 */
			do_action( "woocommerce_rest_insert_{$this->post_type}_object", $user_membership, $request, true );

			$response = $this->prepare_item_for_response( $user_membership, $request );

		}  catch ( \WC_REST_Exception $e ) {

			// if an exception occurs after a new membership has been created in database, we should probably delete that as the object is likely not what the API user expected it to be
			if ( ! $existing_membership && isset( $user_membership ) && $user_membership instanceof \WC_Memberships_User_Membership ) {
				wp_delete_post( $user_membership->get_id(), true );
			}

			$response = new \WP_Error( $e->getErrorCode(), $e->getMessage(), $e->getErrorData() );
		}

		return rest_ensure_response( $response );
	}


	/**
	 * Updates a user membership via REST API.
	 *
	 * @internal
	 *
	 * @since 1.13.0
	 *
	 * @param \WP_REST_Request $request request object
	 * @return \WP_Error|\WP_REST_Response response object or error object
	 */
	public function update_item( $request ) {

		$post = $this->get_post_object( (int) $request['id'] );

		if ( ! $post ) {

			$response = $this->get_invalid_id_error_response( $post );

		} else {

			try {

				$request->set_param( 'context', 'edit' );

				/**
				 * Filters the request before handling it for a user membership update via REST API.
				 *
				 * @since 1.13.0
				 *
				 * @param \WP_REST_Request $request request object
				 * @param \WP_Post|null $post normally this is expected to be the related membership post object
				 */
				$request = apply_filters( 'wc_memberships_rest_api_update_user_membership_request', $request, $post );

				// sanity check in case the filter above changes the request object to an invalid type
				if ( ! $request instanceof \WP_REST_Request ) {
					throw new \WC_REST_Exception( "woocommerce_rest_{$this->post_type}_invalid_request", __( 'Invalid request.', 'woocommerce-memberships' ), 500 );
				}

				$user_membership = wc_memberships_get_user_membership( $post );

				// first check if the membership to update exists
				if ( ! $user_membership instanceof \WC_Memberships_User_Membership ) {
					/* translators: Placeholder: %s - user membership identifier (may be empty) */
					throw new \WC_REST_Exception( "woocommerce_rest_{$this->post_type}_not_found", sprintf( __( 'User Membership%s invalid or not found.', 'woocommerce-memberships' ), $post instanceof \WP_Post ? ' ' . $post->ID . ' ' : ''  ), 404 );
				}

				// a user and a plan are optional in UPDATE requests
				$customer_user   = $this->get_request_customer_user( $request );
				$membership_plan = $this->get_request_membership_plan( $request );

				// if a customer is specified while an UPDATE request is processed, transfer the membership to the a new customer, if they differ
				if ( $customer_user instanceof \WP_User && (int) $customer_user->ID > 0 && (int) $customer_user->ID !== $user_membership->get_user_id() ) {

					try {
						$user_membership->transfer_ownership( $customer_user );
					} catch( \Exception $e ) {
						throw new \WC_REST_Exception( "woocommerce_rest_{$this->post_type}_update_error", $e->getMessage(), 500 );
					}
				}

				// if a plan is specified while an UPDATE request is processed, change the plan with any new one that does not match the existing
				if ( $membership_plan instanceof \WC_Memberships_Membership_Plan && $membership_plan->get_id() !== $user_membership->get_plan_id() ) {

					$success = wp_update_post( array(
						'ID'          => $user_membership->get_id(),
						'post_parent' => $membership_plan->get_id(),
					) );

					if ( ! $success || $success instanceof \WP_Error ) {
						throw new \WC_REST_Exception( "woocommerce_rest_{$this->post_type}_update_error", sprintf( __( 'Could not change the membership plan of this user membership. %s', 'woocommerce-memberships' ), $success ? $success->get_error_message() : '', 500 ) );
					}

					// makes sure the new plan ID will be returned in the final response later
					$user_membership->plan_id = $membership_plan->get_id();
				}

				// update other user membership details
				$user_membership = $this->set_user_membership_data( $user_membership, $request );

				/** @see User_Memberships::create_item() except the $creating param is false when updating */
				do_action( "woocommerce_rest_insert_{$this->post_type}_object", $user_membership, $request, false );

				$response = $this->prepare_item_for_response( $user_membership, $request );

			} catch ( \WC_REST_Exception $e ) {

				$response = new \WP_Error( $e->getErrorCode(), $e->getMessage(), $e->getErrorData() );
			}
		}

		return rest_ensure_response( $response );
	}


	/**
	 * Gets a membership plan object from a request.
	 *
	 * @since 1.13.0
	 *
	 * @param \WP_REST_Request $request request object
	 * @return \WC_Memberships_Membership_Plan|null a plan object, if requested
	 * @throws \WC_REST_Exception if the plan is requested but not found
	 */
	private function get_request_membership_plan( $request ) {

		$plan    = null;
		$plan_id = isset( $request['plan_id'] ) ? $request['plan_id'] : null;

		if ( $plan_id ) {

			$plan = wc_memberships_get_membership_plan( $request['plan_id'] );

			if ( ! $plan instanceof \WC_Memberships_Membership_Plan ) {
				/* translators: Placeholder: %s - customer identifier (may be empty) */
				throw new \WC_REST_Exception( "woocommerce_rest_{$this->post_type}_plan_not_found", sprintf( __( 'Membership Plan%s invalid or not found.', 'woocommerce-memberships' ), is_string( $plan_id ) || is_numeric( $plan_id ) ? ' ' . $plan_id : '' ), 404 );
			}
		}

		return $plan;
	}


	/**
	 * Gets a customer user object from a request.
	 *
	 * @since 1.13.0
	 *
	 * @param \WP_REST_Request $request request object
	 * @return \WP_User|null a customer user object, if requested
	 * @throws \WC_REST_Exception if the customer is requested but not found
	 */
	private function get_request_customer_user( $request ) {

		$user    = null;
		$user_id = isset( $request['customer_id'] ) ? $request['customer_id'] : null;

		if ( $user_id ) {

			$user = $this->get_customer_user( $user_id );

			if ( ! $user ) {
				/* translators: Placeholder: %s - customer identifier (may be empty) */
				throw new \WC_REST_Exception( "woocommerce_rest_{$this->post_type}_invalid_customer_id", sprintf( __( 'Customer%s invalid or not found.', 'woocommerce-memberships' ), is_string( $user_id ) || is_numeric( $user_id ) ? ' ' . $user_id : '' ), 404 );
			}
		}

		return $user;
	}


	/**
	 * Parses an API request to create or update a user membership.
	 *
	 * @since 1.13.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership the user membership object to set properties for
	 * @param \WP_REST_Request|array $request request object (accessible as an array)
	 * @return \WC_Memberships_User_Membership the updated user membership
	 * @throws \WC_REST_Exception on errors
	 */
	private function set_user_membership_data( $user_membership, $request ) {

		// set membership dates (by default newly created memberships start 'now')
		$dates = array(
			/** @see \WC_Memberships_User_Membership::set_start_date() */
			'start_date_gmt',
			/** @see \WC_Memberships_User_Membership::set_end_date() */
			'end_date_gmt',
			/** @see \WC_Memberships_User_Membership::set_cancelled_date() */
			'cancelled_date_gmt',
			/** @see \WC_Memberships_User_Membership::set_paused_date() */
			'paused_date_gmt',
		);

		foreach ( $dates as $date_id ) {

			if ( isset( $request[ $date_id ] ) ) {

				$date_value = '' !== $request[ $date_id ] ? wc_memberships_parse_date( $request[ $date_id ] ) : '';
				$set_date   = 'set_' . str_replace( '_gmt', '', $date_id );

				if ( is_string( $date_value ) && is_callable( array( $user_membership, $set_date ) ) ) {
					$user_membership->$set_date( $date_value );
				} else {
					/* translators: Placeholder: %s - date string name */
					throw new \WC_REST_Exception( "woocommerce_rest_invalid_{$this->post_type}_{$date_id}", sprintf( __( 'Invalid %s date value.', 'woocommerce-memberships' ), $date_id ), 400 );
				}
			}
		}

		if ( $product_id = isset( $request['product_id'] ) ? $request['product_id'] : null ) {

			$product = is_numeric( $product_id ) ? wc_get_product( $product_id ) : null;

			if ( $product instanceof \WC_Product ) {
				$user_membership->set_product_id( $product->get_id() );
			} else {
				/* translators: Placeholder: %s - product identifier (may be empty) */
				throw new \WC_REST_Exception( "woocommerce_rest_invalid_{$this->post_type}_product_id", sprintf( __( 'Product%s invalid or not found.', 'woocommerce-memberships' ), is_numeric( $product ) ? ' ' . $product : '' ), 404 );
			}
		}

		if ( $order_id = isset( $request['order_id'] ) ? $request['order_id'] : null ) {

			$order = is_numeric( $order_id ) ? wc_get_order( $order_id ) : null;

			if ( $order instanceof \WC_Order || $order instanceof \WC_Order_Refund ) {
				$user_membership->set_order_id( $order->get_id() );
			} else {
				/* translators: Placeholder: %s - order identifier (may be empty) */
				throw new \WC_REST_Exception( "woocommerce_rest_invalid_{$this->post_type}_order_id", sprintf( __( 'Order %s invalid or not found.', 'woocommerce-memberships' ), is_numeric( $order ) ? $order : '' ), 404 );
			}
		}

		if ( isset( $request['status'] ) ) {

			$status = is_string( $request['status'] ) ? trim( $request['status'] ) : null;

			if ( $status && wc_memberships()->get_user_memberships_instance()->is_user_membership_status( $status ) ) {
				$user_membership->update_status( $status );
			} else {
				throw new \WC_REST_Exception( "woocommerce_rest_invalid_{$this->post_type}_status", sprintf( __( 'Invalid user membership status: "%s".', 'woocommerce-memberships' ), is_string( $status ) ? $status : '' ), 400 );
			}
		}

		// maybe set the profile fields
		if ( ! empty( $request['profile_fields'] ) ) {

			if ( is_object( $request['profile_fields'] ) ) {
				$request_profile_fields = (array) $request['profile_fields'];
			} else {
				$request_profile_fields = $request['profile_fields'];
			}

			if ( ! is_array( $request_profile_fields ) ) {
				throw new \WC_REST_Exception( "woocommerce_rest_{$this->post_type}_invalid_profile_fields", __( 'Invalid profile fields.', 'woocommerce-memberships' ), 400 );
			}

			foreach ( $request_profile_fields as $key => $request_profile_field ) {

				$slug = ! empty( $request_profile_field['slug'] ) ? wc_clean( $request_profile_field['slug'] ) : '';

				// ensure the slug is passed - core handling will validate that it's a string
				if ( empty( $slug ) ) {

					throw new \WC_REST_Exception( "woocommerce_rest_{$this->post_type}_invalid_profile_field", sprintf(
						/* translators: Placeholders: %d - array item index */
						__( 'The profile field slug in "profile_fields[%d][slug]" is required.', 'woocommerce-memberships' ),
						$key
					), 400 );
				}

				// ensure at least the value property is passed - validation will happen later
				if ( ! isset( $request_profile_field['value'] ) ) {

					throw new \WC_REST_Exception( "woocommerce_rest_{$this->post_type}_invalid_profile_field", sprintf(
						/* translators: Placeholders: %d - array item index */
						__( 'The profile field value in "profile_fields[%d][value]" is required.', 'woocommerce-memberships' ),
						$key
					), 400 );
				}

				try {

					$field_definition = Profile_Fields::get_profile_field_definition( $slug );

					// this should already be validated by the schema enum, but just in case
					if ( ! $field_definition instanceof Profile_Fields\Profile_Field_Definition ) {
						/* translators: Placeholder: %s - profile field slug */
						throw new Framework\SV_WC_Plugin_Exception( sprintf( __( 'No profile field definition found for "%s".', 'woocommerce-memberships' ), $slug ), 404 );
					}

					$value = $request_profile_field['value'];

					if ( $field_definition->is_type( Profile_Fields::TYPE_FILE ) && ! wp_get_attachment_url( (int) $value ) ) {
						/* translators: Placeholders: %1$s - profile field value, %2$s - profile field slug */
						throw new Framework\SV_WC_Plugin_Exception( sprintf( __( 'The value "%1$s" for the "%2$s" profile field is not a valid attachment ID.', 'woocommerce-memberships' ), $value, $slug ) );
					}

					// store the value
					$user_membership->set_profile_field( $slug, $value );

				} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

					throw new \WC_REST_Exception( "woocommerce_rest_{$this->post_type}_invalid_profile_field", sprintf(
						/* translators: Placeholders: %1$s - array item index, %2$s - error message */
						__( 'The "profile_fields[%1$s]" data is invalid. %2$s', 'woocommerce-memberships' ),
						$key,
						$exception->getMessage()
					), $exception->getCode() );
				}
			}
		}

		if ( ! empty( $request['meta_data'] ) ) {

			if ( is_object( $request['meta_data'] ) ) {
				$meta_data = (array) $request['meta_data'];
			} else {
				$meta_data = $request['meta_data'];
			}

			if ( ! is_array( $meta_data ) ) {
				throw new \WC_REST_Exception( "woocommerce_rest_{$this->post_type}_invalid_meta_data", __( 'Invalid meta data.', 'woocommerce-memberships' ), 400 );
			}

			foreach ( $meta_data as $meta ) {

				$meta_key   = isset( $meta['key'] )   ? $meta['key']   : null;
				$meta_value = isset( $meta['value'] ) ? $meta['value'] : null;

				if ( ! is_string( $meta_key ) || '' === trim( $meta_key ) ) {
					throw new \WC_REST_Exception( "woocommerce_rest_{$this->post_type}_invalid_meta_data", __( 'Invalid meta data key.', 'woocommerce-memberships' ), 404 );
				}

				// accept values: null, bool, string, number, array
				if ( null !== $meta_value && ! is_bool( $meta_value ) && ! is_string( $meta_value ) && ! is_numeric( $meta_value ) && ! is_array( $meta_value ) ) {
					throw new \WC_REST_Exception( "woocommerce_rest_{$this->post_type}_invalid_meta_data", __( 'Invalid meta data value.', 'woocommerce-memberships' ), 404 );
				}

				update_post_meta( $user_membership->get_id(), $meta_key, $meta_value );
			}
		}

		$user_membership_id = $user_membership->get_id();

		/**
		 * Filters the membership after setting its properties.
		 *
		 * @since 1.13.0
		 *
		 * @param \WC_Memberships_User_Membership $user_membership the membership object
		 * @param \WP_REST_Request $request the related request object
		 */
		$user_membership = apply_filters( 'wc_memberships_rest_api_user_membership_set_data', $user_membership, $request );

		// sanity check in case the filter above changes the intended type of the membership
		if ( ! $user_membership instanceof \WC_Memberships_User_Membership ) {
			throw new \WC_REST_Exception( "woocommerce_rest_{$this->post_type}_error", sprintf( __( 'An error occurred while setting properties for the user membership %s.', 'woocommerce-memberships' ), $user_membership_id ), 500 );
		}

		return $user_membership;
	}


	/**
	 * Gets the available query parameters for collections.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 *
	 * @return array associative array
	 */
	public function get_collection_params() {

		$params = parent::get_collection_params();

		unset( $params['order'], $params['orderby'], $params['before'], $params['after'] );

		$params['status'] = array(
			'default'           => 'any',
			'description'       => __( 'Limit results to user memberships of a specific status.', 'woocommerce-memberships' ),
			'type'              => 'string',
			'enum'              => array_merge( array( 'any' ), wc_memberships_get_user_membership_statuses( false, false ) ),
			'sanitize_callback' => 'sanitize_key',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['plan'] = array(
			'description'       => __( 'Limit results to user memberships for a specific plan (matched by ID or slug).', 'woocommerce-memberships' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_key',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['customer'] = array(
			'description'       => __( 'Limit results to user memberships belonging to a specific customer (matched by ID, login name or email address).', 'woocommerce-memberships' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['order'] = array(
			'description'       => __( 'Limit results to user memberships related to a specific order (matched by ID).', 'woocommerce-memberships' ),
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['product'] = array(
			'description'       => __( 'Limit results to user memberships granted after the purchase of a specific product (matched by ID).', 'woocommerce-memberships' ),
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);

		/**
		 * Filters the user membership collection params for REST API queries.
		 *
		 * @since 1.11.0
		 *
		 * @param array $params associative array
		 */
		return (array) apply_filters( 'wc_memberships_rest_api_user_memberships_collection_params', $params );
	}


	/**
	 * Prepares query args for items collection query.
	 *
	 * @since 1.11.0
	 *
	 * @param array|\WP_REST_Request $request request object (with array access)
	 * @return array
	 */
	private function prepare_items_query_args( $request ) {

		// query args defaults
		$query_args = array(
			'post_type'           => $this->post_type,
			'offset'              => $request['offset'],
			'paged'               => $request['page'],
			'post__in'            => $request['include'],
			'post__not_in'        => $request['exclude'],
			'posts_per_page'      => $request['per_page'],
			'post_parent__in'     => $request['parent'],
			'post_parent__not_in' => $request['parent_exclude'],
			'orderby'             => isset( $request['orderby'] ) ? $request['orderby'] : 'post_date',
			'order'               => 'DESC', // may be overridden later, se below
		);

		// filter by status (default: any status)
		if ( 'any' !== $request['status'] ) {
			$query_args['post_status'] = Framework\SV_WC_Helper::str_starts_with( $query_args['post_status'], 'wcm-' ) ? $query_args['post_status'] : 'wcm-' . $request['status'];
		} else {
			$query_args['post_status'] = 'any';
		}

		// filter by plan
		if ( isset( $request['plan'] ) ) {

			if ( is_numeric( $request['plan'] ) ) {
				$plan_id = (int) $request['plan'];
			} elseif ( is_string( $request['plan'] ) && ( $plan = wc_memberships_get_membership_plan( $request['plan'] ) ) ) {
				$plan_id = $plan->get_id();
			} elseif( is_array( $request['plan'] ) ) {
				$plan_id = array_unique( array_map( 'absint', $request['plan'] ) );
			} else {
				$plan_id = 0;
			}

			if ( is_array( $plan_id ) ) {
				$query_args['post_parent__in'] = $plan_id;
			}  else {
				$query_args['post_parent'] = $plan_id;
			}
		}

		// filter by customer
		if ( isset( $request['customer'] ) ) {

			if ( is_numeric( $request['customer'] ) ) {
				$customer_id = max( 0, (int) $request['customer'] );
			} else {
				$customer    = $this->get_customer_user( $request['customer'] );
				$customer_id = $customer ? $customer->ID : 0;
			}

			$query_args['author'] = $customer_id;

			// enforces empty array on results if the author is undetermined
			if ( 0 === $customer_id ) {
				$query_args['post__in'] = array( 0 );
			}
		}

		// filter by order or sort order
		if ( isset( $request['order'] ) ) {

			// WooCommerce order
			if ( is_numeric( $request['order'] ) ) {

				if ( ! isset( $query_args['meta_query'] ) ) {
					$query_args['meta_query'] = array();
				}

				$query_args['meta_query'][] = array(
					'key'   => '_order_id',
					'value' => (int) $request['order'],
					'type'  => 'numeric',
				);

			// sort order
			} elseif ( is_string( $request['order'] ) ) {

				$query_args['order'] = $request['order'];
			}
		}

		// filter by product
		if ( isset( $request['product'] ) ) {

			if ( ! isset( $query_args['meta_query'] ) ) {
				$query_args['meta_query'] = array();
			}

			$query_args['meta_query'][] = array(
				'key'   => '_product_id',
				'value' => (int) $request['product'],
				'type'  => 'numeric',
			);
		}

		if ( isset( $query_args['meta_query'] ) && is_array( $query_args['meta_query'] ) && count( $query_args['meta_query'] ) > 1 ) {
			$query_args['meta_query']['relation'] = 'AND';
		}

		/**
		 * Filters the WP API query arguments for user memberships.
		 *
		 * This filter's name follows the WooCommerce core pattern.
		 * @see \WC_REST_Posts_Controller::get_items()
		 *
		 * @since 1.11.0
		 *
		 * @param array $args associative array of query args
		 * @param \WP_REST_Request $request request object
		 */
		return (array) apply_filters( "woocommerce_rest_{$this->post_type}s_query_args", $query_args, $request );
	}


	/**
	 * Gets a collection of User Membership items.
	 *
	 * @internal
	 * @see \WC_REST_Posts_Controller::get_items()
	 *
	 * @since 1.11.0
	 *
	 * @param \WP_REST_Request $request request object
	 * @return \WP_REST_Response response object
	 */
	public function get_items( $request ) {

		$collection  = array();
		$query_args  = $this->prepare_items_query_args( $request );
		$posts_query = new \WP_Query( $this->prepare_items_query( $query_args, $request ) );

		if ( ! empty( $posts_query->posts ) ) {

			foreach ( $posts_query->posts as $post ) {

				if ( ! wc_rest_check_post_permissions( $this->post_type, 'read', $post->ID ) ) {
					continue;
				}

				if ( $user_membership = wc_memberships_get_user_membership( $post ) ) {

					$response_data = $this->prepare_item_for_response( $user_membership, $request );
					$collection[]  = $this->prepare_response_for_collection( $response_data );
				}
			}
		}

		return $this->prepare_response_collection_paginated( $request, $collection, $posts_query, $query_args );
	}


	/**
	 * Returns user membership data for API responses.
	 *
	 * @since 1.11.0
	 *
	 * @param null|int|\WP_Post|\WC_Memberships_User_Membership $user_membership user membership
	 * @param null|\WP_REST_Response optional response object
	 * @return array associative array of data
	 */
	public function get_formatted_item_data( $user_membership, $request = null ) {

		if ( is_numeric( $user_membership ) || $user_membership instanceof \WP_Post ) {
			$user_membership = wc_memberships_get_user_membership( $user_membership );
		}

		if ( $user_membership instanceof \WC_Memberships_User_Membership ) {

			$datetime_format = $this->get_datetime_format();
			$order           = $user_membership->get_order();
			$product         = $user_membership->get_product( true );
			$data            = [
				'id'                 => $user_membership->get_id(),
				'customer_id'        => $user_membership->get_user_id(),
				'plan_id'            => $user_membership->get_plan_id(),
				'status'             => $user_membership->get_status(),
				'order_id'           => $order   ? $order->get_id()   : null,
				'product_id'         => $product ? $product->get_id() : null,
				'date_created'       => wc_memberships_format_date( $user_membership->post->post_date, $datetime_format ),
				'date_created_gmt'   => wc_memberships_format_date( $user_membership->post->post_date_gmt, $datetime_format ),
				'start_date'         => $user_membership->get_local_start_date( $datetime_format ),
				'start_date_gmt'     => $user_membership->get_start_date( $datetime_format ),
				'end_date'           => $user_membership->get_local_end_date( $datetime_format ),
				'end_date_gmt'       => $user_membership->get_end_date( $datetime_format ),
				'paused_date'        => $user_membership->get_local_paused_date( $datetime_format ),
				'paused_date_gmt'    => $user_membership->get_paused_date( $datetime_format ),
				'cancelled_date'     => $user_membership->get_local_cancelled_date( $datetime_format ),
				'cancelled_date_gmt' => $user_membership->get_cancelled_date( $datetime_format ),
				'view_url'           => $user_membership->get_view_membership_url(),
				'profile_fields'     => [],
				'meta_data'          => $this->prepare_item_meta_data( $user_membership ),
			];

			$profile_fields = $user_membership->get_profile_fields();

			foreach ( $profile_fields as $profile_field ) {

				$data['profile_fields'][] = [
					'slug'  => $profile_field->get_slug(),
					'value' => $profile_field->get_value(),
				];
			}

		} else {

			$data            = [];
			$user_membership = null;
		}

		if ( $request ) {

			$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
			$fields  = $this->add_additional_fields_to_object( $data, $request );
			$data    = $this->filter_response_by_context( $fields, $context );
		}

		/**
		 * Filters the user membership data for the REST API.
		 *
		 * @since 1.11.0
		 *
		 * @param array $data associative array of membership data
		 * @param null|\WC_Memberships_User_Membership $user_membership membership object or null if undetermined
		 * @param null|\WP_REST_Request optional request object
		 */
		return (array) apply_filters( 'wc_memberships_rest_api_user_membership_data', $data, $user_membership, $request );
	}


	/**
	 * Prepares an individual User Membership object data for API response.
	 *
	 * @since 1.11.0
	 *
	 * @param int|\WP_Post|\WC_Memberships_User_Membership $user_membership user membership object, ID or post object
	 * @param null|\WP_REST_Request $request WP API request, optional
	 * @return \WP_REST_Response response data
	 */
	public function prepare_item_for_response( $user_membership, $request = null ) {

		if ( is_numeric( $user_membership ) || $user_membership instanceof \WP_Post ) {
			$user_membership = wc_memberships_get_user_membership( $user_membership );
		}

		$response = rest_ensure_response( $this->get_formatted_item_data( $user_membership, $request ) );

		$response->add_links( $this->prepare_links( $user_membership, $request ) );

		/**
		 * Filters the data for a response.
		 *
		 * This filter's name follows the WooCommerce core pattern.
		 * @see \WC_REST_Posts_Controller::prepare_item_for_response()
		 *
		 * @since 1.11.0
		 *
		 * @param \WP_REST_Response $response the response object
		 * @param null|\WP_Post $post the user membership post object
		 * @param \WP_REST_Request $request the request object
		 */
		return apply_filters( "woocommerce_rest_prepare_{$this->post_type}", $response, $user_membership ? $user_membership->post : null, $request );
	}


	/**
	 * Prepares links to be added to user membership objects.
	 *
	 * @since 1.11.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership user membership object
	 * @param \WP_REST_Request $request WP API request
	 * @return array associative array
	 */
	protected function prepare_links( $user_membership, $request ) {

		$links = [
			'self'       => [
				'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $user_membership->get_id() ) ),
			],
			'collection' => [
				'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ),
			],
			'customer'   => [
				'href' => rest_url( sprintf( '/%s/customers/%d', $this->get_woocommerce_namespace(), $user_membership->get_user_id() ) ),
			],
		];

		// an order may not be associated to a membership
		if ( $order = $user_membership->get_order() ) {
			$links['order'] = [
				'href' => rest_url( sprintf( '/%s/orders/%d', $this->get_woocommerce_namespace(), $order->get_id() ) ),
			];
		}

		// likewise, a product might not be present
		if ( $product = $user_membership->get_product( true ) ) {
			$links['product'] = [
				'href' => rest_url( sprintf( '/%s/products/%d', $this->namespace, $product->get_id() ) ),
			];
		}

		// for any "file" profile fields, add a link to its associated media object
		if ( $profile_fields = $user_membership->get_profile_fields( [ 'type' => Profile_Fields::TYPE_FILE ] ) ) {

			foreach ( $profile_fields as $profile_field ) {

				if ( $attachment_id = $profile_field->get_value() ) {

					$links[ $profile_field->get_slug() ] = [
						'href' => rest_url( 'wp/v2/media/' . $attachment_id ),
					];
				}
			}
		}

		/**
		 * Filters the user membership item's links for WP API output.
		 *
		 * @since 1.11.0
		 *
		 * @param array $links associative array
		 * @param \WC_Memberships_User_Membership $user_membership membership object
		 * @param null|\WP_REST_Request $request WP API request
		 * @param \SkyVerge\WooCommerce\Memberships\API\v2\User_Memberships handler instance
		 */
		return (array) apply_filters( 'wc_memberships_rest_api_user_membership_links', $links, $user_membership, $request, $this );
	}


	/**
	 * Gets the user membership REST API schema.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 *
	 * @return array
	 */
	public function get_item_schema() {

		/**
		 * Filters the WP API user membership schema.
		 *
		 * @since 1.11.0
		 *
		 * @param array associative array
		 */
		$schema = (array) apply_filters( 'wc_memberships_rest_api_user_membership_schema', [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'user_membership', // this will be used as the base for WP REST CLI commands
			'type'       => 'object',
			'properties' => [
				'id'                 => [
					'description' => __( 'Unique identifier of the user membership.', 'woocommerce-memberships' ),
					'type'        => 'integer',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'customer_id'        => [
					'description' => __( 'Unique identifier of the user the membership belongs to.', 'woocommerce-memberships' ),
					'type'        => 'integer',
					'context'     => [ 'view', 'edit' ],
				],
				'plan_id'            => [
					'description' => __( 'Unique identifier of the plan the user membership grants access to.', 'woocommerce-memberships' ),
					'type'        => 'integer',
					'context'     => [ 'view', 'edit' ],
				],
				'status'             => [
					'description' => __( 'User membership status.', 'woocommerce-membership' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit' ],
					'enum'        => wc_memberships_get_user_membership_statuses( false, false ),
				],
				'order_id'           => [
					'description' => __( 'Unique identifier of the order that grants access.', 'woocommerce-memberships' ),
					'type'        => 'integer',
					'context'     => [ 'view', 'edit' ],
				],
				'product_id'         => [
					'description' => __( 'Unique identifier of the purchased product, or its variation, that grants access.', 'woocommerce-memberships' ),
					'type'        => 'integer',
					'context'     => [ 'view', 'edit' ],
				],
				'date_created'       => [
					'description' => __( 'The date when the user membership is created, in the site timezone.', 'woocommerce-memberships' ),
					'type'        => 'date-time',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'date_created_gmt'   => [
					'description' => __( 'The date when the user membership is created, in UTC.', 'woocommerce-memberships' ),
					'type'        => 'date-time',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'start_date'         => [
					'description' => __( 'The date when the user membership starts being active, in the site timezone.', 'woocommerce-memberships' ),
					'type'        => 'date-time',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'start_date_gmt'     => [
					'description' => __( 'The date when the user membership starts being active, in UTC.', 'woocommerce-memberships' ),
					'type'        => 'date-time',
					'context'     => [ 'view', 'edit' ],
				],
				'end_date'           => [
					'description' => __( 'The date when the user membership ends, in the site timezone.', 'woocommerce-memberships' ),
					'type'        => 'date-time',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'end_date_gmt'       => [
					'description' => __( 'The date when the user membership ends, in UTC.', 'woocommerce-memberships' ),
					'type'        => 'date-time',
					'context'     => [ 'view', 'edit' ],
				],
				'paused_date'        => [
					'description' => __( 'The date when the user membership was last paused, in the site timezone.', 'woocommerce-memberships' ),
					'type'        => 'date-time',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'paused_date_gmt'    => [
					'description' => __( 'The date when the user membership was last paused, in UTC.', 'woocommerce-memberships' ),
					'type'        => 'date-time',
					'context'     => [ 'view', 'edit' ],
				],
				'cancelled_date'     => [
					'description' => __( 'The date when the user membership was cancelled, in the site timezone.', 'woocommerce-memberships' ),
					'type'        => 'date-time',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'cancelled_date_gmt' => [
					'description' => __( 'The date when the user membership was cancelled, in UTC.', 'woocommerce-memberships' ),
					'type'        => 'date-time',
					'context'     => [ 'view', 'edit' ],
				],
				'view_url'           => [
					'description' => __( 'The URL pointing to the Members Area to view the membership.', 'woocommerce-memberships' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'profile_fields' => [
					'description' => __( 'User membership profile fields.', 'woocommerce-memberships' ),
					'type'        => 'array',
					'context'     => [ 'view', 'edit' ],
					'items'       => [
						'type'       => 'object',
						'properties' => [
							'slug' => [
								'description' => __( 'Profile field slug.', 'woocommerce-memberships' ),
								'type'        => 'string',
								'context'     => [ 'view', 'edit' ],
								'enum'        => array_keys( Profile_Fields::get_profile_field_definitions() ),
							],
							'value' => [
								'description' => __( 'Profile field value.', 'woocommerce-memberships' ),
								'type'        => 'mixed',
								'context'     => [ 'view', 'edit' ],
							],
						],
					],
				],
				'meta_data'          => [
					'description' => __( 'User membership additional meta data.', 'woocommerce-memberships' ),
					'type'        => 'array',
					'context'     => [ 'view', 'edit' ],
					'items'       => [
						'type'       => 'object',
						'properties' => [
							'id'     => [
								'description' => __( 'Meta ID.', 'woocommerce-memberships' ),
								'type'        => 'integer',
								'context'     => [ 'view', 'edit' ],
								'readonly'    => true,
							],
							'key'    => [
								'description' => __( 'Meta key.', 'woocommerce-memberships' ),
								'type'        => 'string',
								'context'     => [ 'view', 'edit' ],
							],
							'value'  => [
								'description' => __( 'Meta value.', 'woocommerce-memberships' ),
								'type'        => 'mixed',
								'context'     => [ 'view', 'edit' ],
							],
						],
					],
				],
			],
		] );

		return $this->add_additional_fields_schema( $schema );
	}


	/**
	 * Gets an array of endpoint arguments from the item schema for the controller.
	 *
	 * @internal
	 *
	 * @since 1.13.0
	 *
	 * @param string $method HTTP method of the request
	 * @return array associative array of arguments
	 */
	public function get_endpoint_args_for_item_schema( $method = \WP_REST_Server::CREATABLE ) {

		$endpoint_args = parent::get_endpoint_args_for_item_schema( $method );

		if ( in_array( $method, array( \WP_REST_Server::CREATABLE, \WP_REST_Server::EDITABLE ), true ) ) {

			foreach ( array_keys( $endpoint_args ) as $property ) {

				// a customer and a plan are required when creating a new membership, but optional when editing an existing one
				if ( in_array( $property, array( 'customer_id', 'plan_id' ), true ) ) {
					$endpoint_args[ $property ]['required'] = $method === \WP_REST_Server::CREATABLE;
				} else {
					$endpoint_args[ $property ]['required'] = false;
				}
			}
		}

		/**
		 * Filters user memberships endpoint arguments.
		 *
		 * @since 1.13.0
		 *
		 * @param array $endpoint_args associative array of arguments
		 * @param string $method HTTP method of the request
		 */
		return (array) apply_filters( 'wc_memberships_rest_api_user_membership_endpoint_args', $endpoint_args, $method );
	}


	/**
	 * Gets the public batch REST API schema.
	 *
	 * @internal
	 *
	 * @since 1.13.0
	 *
	 * @return array associative array
	 */
	public function get_public_batch_schema() {

		$schema = parent::get_public_batch_schema();

		// have the schema return something more descriptive
		if ( isset( $schema['properties'] ) ) {

			if ( isset( $schema['properties']['create']['description'] ) ) {
				$schema['properties']['create']['description'] = __( 'List of user memberships created.', 'woocommerce-memberships' );
			}

			if ( isset( $schema['properties']['update']['description'] ) ) {
				$schema['properties']['update']['description'] = __( 'List of updated user memberships.', 'woocommerce-memberships' );
			}

			if ( isset( $schema['properties']['delete']['description'] ) ) {
				$schema['properties']['delete']['description'] = __( 'List of deleted user memberships.', 'woocommerce-memberships' );
			}
		}

		return $schema;
	}


}
