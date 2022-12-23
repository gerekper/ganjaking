<?php

if (!class_exists('WC_Wishlists_Rest_Controller')) {
	class WC_Wishlists_Rest_Controller extends WP_REST_Posts_Controller {

		private $loaded_lists;

		public function __construct() {
			parent::__construct( 'wishlist' );

			$this->loaded_lists = array();

			register_rest_field( 'wishlist', 'wishlist_type', array(
					'get_callback' => array( $this, 'get_field' ),
					'schema'       => array(
						'description' => __( 'List Type.' ),
						'type'        => 'string'
					),
				)
			);

			register_rest_field( 'wishlist', 'wishlist_sharing', array(
					'get_callback' => array( $this, 'get_field' ),
					'schema'       => array(
						'description' => __( 'List Sharing Status.' ),
						'type'        => 'string'
					),
				)
			);

			register_rest_field( 'wishlist', 'wishlist_status', array(
					'get_callback' => array( $this, 'get_field' ),
					'schema'       => array(
						'description' => __( 'List Status.' ),
						'type'        => 'string'
					),
				)
			);

			register_rest_field( 'wishlist', 'wishlist_first_name', array(
					'get_callback' => array( $this, 'get_field' ),
					'schema'       => array(
						'description' => __( 'List Owner First Name.' ),
						'type'        => 'string'
					),
				)
			);

			register_rest_field( 'wishlist', 'wishlist_last_name', array(
					'get_callback' => array( $this, 'get_field' ),
					'schema'       => array(
						'description' => __( 'List Owner Last Name.' ),
						'type'        => 'string'
					),
				)
			);

			register_rest_field( 'wishlist', 'wishlist_url', array(
					'get_callback' => array( $this, 'get_field' ),
					'schema'       => array(
						'description' => __( 'Public View URL.' ),
						'type'        => 'string'
					),
				)
			);

			register_rest_field( 'wishlist', 'wishlist_owner', array(
					'get_callback' => array( $this, 'get_protected_field' ),
					'schema'       => array(
						'description' => __( 'List Owner ID.' ),
						'type'        => 'string'
					),
				)
			);

			register_rest_field( 'wishlist', 'wishlist_owner_email', array(
					'get_callback' => array( $this, 'get_protected_field' ),
					'schema'       => array(
						'description' => __( 'List Owner Email.' ),
						'type'        => 'string'
					),
				)
			);


			register_rest_field( 'wishlist', 'items', array(
					'get_callback' => array( $this, 'get_list_items' ),
					'schema'       => array(
						'description' => __( 'The list items.' ),
						'type'        => 'string'
					),
				)
			);


		}

		public function register_routes() {
			parent::register_routes();

			$get_item_args = array(
				'context' => $this->get_context_param( array( 'default' => 'view' ) ),
			);

			register_rest_route(
				$this->namespace,
				'/' . $this->rest_base . '/(?P<id>[\d]+)/items',
				array(
					'args'        => array(
						'id' => array(
							'description' => __( 'Unique identifier for the post.' ),
							'type'        => 'integer',
						),
					),
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'endpoint_get_list_items' ),
						'permission_callback' => array( $this, 'get_item_permissions_check' ),
						'args'                => $get_item_args,
					),
					array(
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'endpoint_add_list_item' ),
						'permission_callback' => array( $this, 'create_list_item_permissions_check' ),
						'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
					),
					array(
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => array( $this, 'delete_item' ),
						'permission_callback' => array( $this, 'delete_item_permissions_check' ),
						'args'                => array(
							'force' => array(
								'type'        => 'boolean',
								'default'     => false,
								'description' => __( 'Whether to bypass Trash and force deletion.' ),
							),
						),
					),
					'allow_batch' => $this->allow_batch,
					'schema'      => array( $this, 'get_public_item_schema' ),
				)
			);
		}

		public function prepare_items_query( $prepared_args = array(), $request = null ) {

			if ( !current_user_can( 'manage_woocommerce' ) ) {
				if ( !isset( $prepared_args['meta_query'] ) ) {
					$prepared_args['meta_query'] = array();
				}

				$prepared_args['meta_query'] = array(
					'relation' => 'OR',
					array(
						'key'   => '_wishlist_sharing',
						'value' => 'public'
					)
				);

				if ( is_user_logged_in() ) {
					$key = WC_Wishlists_User::get_wishlist_key();
					if ( $key ) {
						$prepared_args['meta_query'][] = array(
							array(
								'key'   => '_wishlist_owner',
								'value' => $key,
							)
						);
					}
				}
			}

			return $prepared_args;
		}


		public function get_field( $object, $field_name, $request ) {

			$value = '';
			switch ( $field_name ) {
				case 'wishlist_sharing':
				case 'wishlist_first_name':
				case 'wishlist_last_name':
					$value = get_post_meta( $object['id'], '_' . $field_name, true );
					break;
				case 'wishlist_url':
					$value = add_query_arg( array( 'wlid' => $object['id'] ), ( WC_Wishlists_Pages::get_url_for( 'view-a-list' ) ) );
				default :
					break;
			}

			return $value;
		}

		public function get_protected_field( $object, $field_name, $request ) {
			$owner = get_post_meta( $object['id'], '_wishlist_owner', true );
			$value = '';
			if ( current_user_can( 'manage_woocommerce' ) || $owner == get_current_user_id() ) {
				switch ( $field_name ) {
					case 'wishlist_owner':
					case 'wishlist_owner_email':
						$value = get_post_meta( $object['id'], '_' . $field_name, true );
						break;
					default :
						break;
				}
			}

			return $value;
		}

		public function get_list_items( $object, $field_name, $request ) {
			return WC_Wishlists_Wishlist_Item_Collection::get_items( $object['id'] );
		}


		/** Items Handling */

		public function endpoint_get_list_items( $request ) {
			$items = WC_Wishlists_Wishlist_Item_Collection::get_items( $request['id'] );

			$data = array();
			foreach ( $items as $item ) {
				$data[]   = $this->prepare_response_for_collection( $item );
			}

			return rest_ensure_response( $data );
		}

		public function endpoint_add_list_item($request) {
			$wishlist_id = $request['id'];

			/*
			if ( ! is_user_logged_in() && ( WC_Wishlists_Settings::get_setting( 'wc_wishlist_guest_enabled', 'enabled' ) == 'disabled' ) ) {
				return new WP_Error(
					'rest_forbidden_context',
					__( 'Sorry, you are not allowed to add items to this list.' ),
					array( 'status' => rest_authorization_required_code() )
				);
			}
			*/

			$params = $request->get_params();
			$product_id = $params['product_id'] ?? false;
			$product    = wc_get_product( $product_id );

			if ( ! $product ) {
				return new WP_Error(
					'rest_invalid_product',
					__( 'Sorry, invalid product ID.' ),
					array( 'status' => 404 )
				);
			}

			$quantity = $params['quantity'] ?? 1;
			$variation_id = $params['variation_id'] ?? false;
			$variation = $params['variation'] ?? false;
			$wishlist_item_id = WC_Wishlists_Wishlist_Item_Collection::add_item( $wishlist_id, $product_id, $quantity, $variation_id, $variation );
			$wishlist_item = WC_Wishlists_Wishlist_Item_Collection::get_item( $wishlist_item_id );
			$response = $this->prepare_response_for_collection( $wishlist_item );
			return rest_ensure_response( $response );
		}

		/**
		 * Check if a given request has access to create items
		 *
		 * @param WP_REST_Request $request Full data about the request.
		 *
		 * @return WP_Error|bool
		 */
		public function create_item_permissions_check( $request ) {
			return false;
		}

		public function create_list_item_permissions_check($request) {
			return true;
		}

		/**
		 * Check if a given request has access to update a specific item
		 *
		 * @param WP_REST_Request $request Full data about the request.
		 *
		 * @return WP_Error|bool
		 */
		public function update_item_permissions_check( $request ) {
			return false;
		}

		/**
		 * Check if a given request has access to delete a specific item
		 *
		 * @param WP_REST_Request $request Full data about the request.
		 *
		 * @return WP_Error|bool
		 */
		public function delete_item_permissions_check( $request ) {
			return false;
		}

	}
}
