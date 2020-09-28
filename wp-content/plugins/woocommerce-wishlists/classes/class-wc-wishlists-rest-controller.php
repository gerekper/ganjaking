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
