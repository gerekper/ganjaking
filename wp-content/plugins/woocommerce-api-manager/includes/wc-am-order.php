<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce API Manager Order Class
 *
 * @since       2.0
 *
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @package     WooCommerce API Manager/Order
 */
class WC_AM_Order {

	private $api_resource_table   = '';
	private $api_activation_table = '';

	/**
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * @static
	 * @return \WC_AM_Order
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	private function __construct() {
		// Background API Product updater.
		$this->api_resource_table   = WC_AM_USER()->get_api_resource_table_name();
		$this->api_activation_table = WC_AM_USER()->get_api_activation_table_name();
		/**
		 * Use woocommerce_order_status_changed in lieu of woocommerce_order_status_completed,
		 * otherwise checking if an item is on an active WooCommerce Subscription will fail.
		 */
		if ( WCAM()->get_grant_access_after_payment() ) {
			/**
			 * If Digital downloads are being sold with physical products.
			 *
			 * @since 2.0.20
			 */
			add_action( 'woocommerce_order_status_processing', array( $this, 'update_order' ) );
		}

		add_action( 'woocommerce_order_status_completed', array( $this, 'update_order' ) );
		add_action( 'woocommerce_order_status_changed', array( $this, 'remove_order' ), 10, 3 );
		add_action( 'woocommerce_order_partially_refunded', array( $this, 'order_partially_refunded' ), 10, 2 );
		add_action( 'woocommerce_order_fully_refunded', array( $this, 'order_fully_refunded' ), 10, 2 );
		add_action( 'woocommerce_refund_deleted', array( $this, 'refund_deleted' ), 10, 2 );
		add_action( 'woocommerce_delete_order_items', array( $this, 'delete_order' ) );
		add_action( 'woocommerce_before_delete_order', array( $this, 'before_delete_order' ) );
		add_action( 'woocommerce_before_trash_order', array( $this, 'before_delete_order' ) );
		add_action( 'woocommerce_delete_order', array( $this, 'delete_order' ) );
		add_action( 'woocommerce_trash_order', array( $this, 'delete_order' ) );
		add_action( 'woocommerce_before_delete_order_item', array( $this, 'delete_order_item' ) );
		add_action( 'wp_trash_post', array( $this, 'trash_order' ) );
		add_action( 'untrashed_post', array( $this, 'untrashed_order' ) );
		add_action( 'edit_post', array( $this, 'edit_order' ), 10, 2 );
		add_action( 'woocommerce_email_before_order_table', array( $this, 'email_api_keys' ), 10, 3 );
		add_action( 'woocommerce_order_details_before_order_table', array( $this, 'order_details_before_order_table' ), 10, 1 );
	}

	/**
	 * Adds a new order, or updates an existing order.
	 *
	 * @since 2.0
	 *
	 * @param int $order_id
	 *
	 * @throws \Exception
	 */
	public function update_order( $order_id ) {
		$order = WC_AM_ORDER_DATA_STORE()->get_order_object( $order_id );

		if ( is_object( $order ) && WC_AM_FORMAT()->count( $order->get_items() ) > 0 && ( $order->get_status() == 'completed' || ( WCAM()->get_grant_access_after_payment() && $order->get_status() == 'processing' ) ) ) {
			$user_id = WC_AM_ORDER_DATA_STORE()->get_customer_id( $order );

			if ( ! empty( $user_id ) ) {
				$non_sub_order_updated = false;
				$sub_order_updated     = false;
				WC_AM_SMART_CACHE()->delete_activation_api_cache_by_order_id( $order_id );
				WC_AM_SMART_CACHE()->refresh_cache_by_order_id( $order_id, false );

				if ( WCAM()->get_wc_subs_exist() ) {
					// Updates only WooCommerce Subscription item products marked as API Products.
					$sub_order_updated = $this->update_wc_subscription_order( $order_id );
				}

				// Updates products marked as API Products, but filters out WooCommerce Subscription items.
				$non_sub_order_updated = $this->update_api_order( $order_id );

				if ( ! empty( $non_sub_order_updated ) || ! empty( $sub_order_updated ) ) {
					if ( ! empty( $non_sub_order_updated ) ) {
						// To update API Resource after a renewal order.
						$this->update_api_resource_after_renewal_order_completed( $order_id );
					}

					$this->delete_cache( $order_id );

					/**
					 * Returns the order_id for a new order or updated order.
					 *
					 * @since   2.4.7
					 */
					do_action( 'wc_am_new_or_updated_order', $order_id );
				}
			}
		}
	}

	/**
	 * Update only API Product items from the order.
	 *
	 * @since   2.0
	 * @updated 3.0 to return api_resource_id or 0.
	 *
	 * @param int $order_id
	 *
	 * @return int
	 * @throws \Exception
	 */
	public function update_api_order( $order_id ) {
		global $wpdb;

		$updated          = false;
		$existng_products = array();
		$order            = WC_AM_ORDER_DATA_STORE()->get_order_object( $order_id );
		$line_item_data   = WC_AM_ORDER_DATA_STORE()->get_line_item_data_from_order( $order_id );

		if ( is_object( $order ) && ! empty( $line_item_data ) ) {
			foreach ( $line_item_data as $k => $v ) {
				// Populate only with API products.
				$existng_products[] = $v[ 'product_id' ];

				$sql = "
							SELECT *
							FROM {$wpdb->prefix}" . $this->api_resource_table . "
							WHERE order_id = %d
							AND product_id = %d
						";

				$args = array(
					$order_id,
					$v[ 'product_id' ]
				);

				// Get the API resource order item for this product.
				$result = $wpdb->get_row( $wpdb->prepare( $sql, $args ) );

				// Check if the API resource already exists for this order item.
				if ( empty( $result ) ) {
					$order_created_time = WC_AM_ORDER_DATA_STORE()->get_order_time_to_epoch_time_stamp( $order );

					/**
					 * Every customer must have a Master API Key, and it is missing, so create it now.
					 */
					if ( empty( WC_AM_USER()->get_master_api_key( $v[ 'user_id' ] ) ) ) {
						WC_AM_USER()->set_registration_master_key_and_status( $v[ 'user_id' ] );
					}

					// If the _api_resource_product_id meta value is missing on the product, add it now.
					if ( ! empty( $v[ 'parent_id' ] ) && ! empty( $v[ 'product_id' ] ) ) {
						WC_AM_PRODUCT_DATA_STORE()->update_missing_api_resource_product_id( $v[ 'product_id' ], $v[ 'parent_id' ] );
					}

					$new_access_expires     = ! empty( $v[ 'access_expires' ] ) ? (int) ( $v[ 'access_expires' ] * DAY_IN_SECONDS ) + $order_created_time : 0;
					$current_access_expires = ! empty( $v[ 'old_access_expires' ] ) ? $v[ 'old_access_expires' ] + $new_access_expires : $new_access_expires;

					$data = array(
						'activation_ids'              => '',
						'activations_total'           => 0,
						'activations_purchased'       => ! empty( $v[ 'api_activations' ] ) ? (int) $v[ 'api_activations' ] : 0,
						'activations_purchased_total' => ! empty( $v[ 'activations_total' ] ) ? (int) $v[ 'activations_total' ] : 0,
						'access_expires'              => (int) $current_access_expires,
						'access_granted'              => (int) $order_created_time,
						'item_qty'                    => ! empty( $v[ 'item_qty' ] ) ? (int) $v[ 'item_qty' ] : 0,
						'master_api_key'              => (string) WC_AM_USER()->get_master_api_key( $v[ 'user_id' ] ),
						'order_id'                    => (int) $order_id,
						'order_item_id'               => (int) $v[ 'order_item_id' ],
						'order_key'                   => (string) $order->get_order_key(),
						'parent_id'                   => ! empty( $v[ 'parent_id' ] ) ? (int) $v[ 'parent_id' ] : 0,
						'product_id'                  => (int) $v[ 'product_id' ],
						'product_order_api_key'       => (string) apply_filters( 'wc_api_manager_custom_product_order_api_key', WC_AM_HASH()->rand_hash(), $v[ 'product_id' ], $order_id, $order ),
						'product_title'               => (string) $v[ 'product_title' ],
						'refund_qty'                  => ! empty( $v[ 'refund_qty' ] ) ? (int) $v[ 'refund_qty' ] : 0,
						'user_id'                     => (int) $v[ 'user_id' ],
						'variation_id'                => ! empty( $v[ 'variation_id' ] ) ? (int) $v[ 'variation_id' ] : 0
					);

					$format = array(
						'%s',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%s',
						'%d',
						'%d',
						'%s',
						'%d',
						'%d',
						'%s',
						'%s',
						'%d',
						'%d',
						'%d'
					);

					/**
					 * Insert API resource data for new order items only.
					 * This includes newly purchased order items, and order items added to an existing order.
					 */
					$updated = $wpdb->insert( $wpdb->prefix . $this->api_resource_table, $data, $format );
				} else { // API resource exists, and needs to be updated.
					$data = array(
						'activations_purchased_total' => ! empty( $v[ 'activations_total' ] ) ? (int) $v[ 'activations_total' ] : 0,
						'item_qty'                    => ! empty( $v[ 'item_qty' ] ) ? (int) $v[ 'item_qty' ] : 0,
						'order_id'                    => (int) $order_id,
						'order_item_id'               => (int) $v[ 'order_item_id' ],
						'refund_qty'                  => ! empty( $v[ 'refund_qty' ] ) ? (int) $v[ 'refund_qty' ] : 0
					);

					$where = array(
						'order_id'      => (int) $order_id,
						'order_item_id' => (int) $v[ 'order_item_id' ],
						'product_id'    => (int) $v[ 'product_id' ]
					);

					$data_format = array(
						'%d',
						'%d',
						'%d',
						'%d',
						'%d'
					);

					$where_format = array(
						'%d',
						'%d',
						'%d'
					);

					/**
					 * Update an existing API resource for this order item if the order status changed from Completed to something
					 * other than Completed, the item was updated, then the order status was changed back to Completed status.
					 *
					 * The order cannot be edited once it has a Completed status, so API resource updates only happen when
					 * the order status is changed back to Completed status.
					 */
					$updated = $wpdb->update( $wpdb->prefix . $this->api_resource_table, $data, $where, $data_format, $where_format );
				}
			}
		}

		/**
		 * Delete any order item API resources that no longer exist on the order.
		 */
		if ( ! empty( $existng_products ) ) {
			$sql = "
						SELECT product_id
						FROM {$wpdb->prefix}" . $this->api_resource_table . "
						WHERE order_id = %d
					";

			$resources = $wpdb->get_col( $wpdb->prepare( $sql, $order_id ) );
			$orphans   = array_diff( $resources, $existng_products );

			if ( ! empty( $orphans ) ) {
				foreach ( $orphans as $orphan ) {
					$is_wc_sub = WC_AM_SUBSCRIPTION()->is_wc_subscription( $orphan );

					if ( ! $is_wc_sub ) {
						$where = array(
							'order_id'   => $order_id,
							'product_id' => $orphan
						);

						$where_format = array(
							'%d',
							'%d'
						);

						/**
						 * Delete orphaned order item API resources that no longer exist on the order.
						 */
						$wpdb->delete( $wpdb->prefix . $this->api_resource_table, $where, $where_format );
					}
				}
			}
		}

		return ! WC_AM_FORMAT()->empty( $updated );
	}

	/**
	 * Update only WooCommerce Subscriptions API Product items from the order.
	 *
	 * @since   2.0
	 * @updated 3.0 to return api_resource_id or 0.
	 *
	 * @param int $order_id
	 *
	 * @return int
	 * @throws \Exception
	 */
	public function update_wc_subscription_order( $order_id ) {
		global $wpdb;

		$updated          = false;
		$existng_products = array();
		$order            = WC_AM_ORDER_DATA_STORE()->get_order_object( $order_id );
		$line_item_data   = WC_AM_SUBSCRIPTION()->get_subscription_line_item_data_from_order( $order_id );

		if ( is_object( $order ) && ! empty( $line_item_data ) ) {
			foreach ( $line_item_data as $k => $v ) {
				// Populate only with API products.
				$existng_products[] = $v[ 'product_id' ];

				$sql = "
							SELECT *
							FROM {$wpdb->prefix}" . $this->api_resource_table . "
							WHERE sub_id = %d
							AND product_id = %d
						";

				$args = array(
					$v[ 'sub_id' ],
					$v[ 'product_id' ]
				);

				// Get the API resource order item for this product.
				$result = $wpdb->get_row( $wpdb->prepare( $sql, $args ) );

				// Check if the API resource already exists for this order item.
				if ( empty( $result ) ) {
					$order_created_time = WC_AM_ORDER_DATA_STORE()->get_order_time_to_epoch_time_stamp( $order );

					/**
					 * Every customer must have a Master API Key, and it is missing, so create it now.
					 */
					if ( empty( WC_AM_USER()->get_master_api_key( $v[ 'user_id' ] ) ) ) {
						WC_AM_USER()->set_registration_master_key_and_status( $v[ 'user_id' ] );
					}

					// If the _api_resource_product_id meta value is missing on the product, add it now.
					if ( ! empty( $v[ 'parent_id' ] ) && ! empty( $v[ 'product_id' ] ) ) {
						WC_AM_PRODUCT_DATA_STORE()->update_missing_api_resource_product_id( $v[ 'product_id' ], $v[ 'parent_id' ] );
					}

					/**
					 * order_item_id is zero so this resource is not deleted if the corresponding line_item is removed from the order,
					 * as it may still exist on the subscription.
					 */
					$data = array(
						'activation_ids'              => '',
						'activations_total'           => 0,
						'activations_purchased'       => ! empty( $v[ 'api_activations' ] ) ? (int) $v[ 'api_activations' ] : 0,
						'activations_purchased_total' => ! empty( $v[ 'activations_total' ] ) ? (int) $v[ 'activations_total' ] : 0,
						'access_expires'              => 0,
						'access_granted'              => (int) $order_created_time,
						'item_qty'                    => ! empty( $v[ 'item_qty' ] ) ? (int) $v[ 'item_qty' ] : 0,
						'master_api_key'              => (string) WC_AM_USER()->get_master_api_key( $v[ 'user_id' ] ),
						'order_id'                    => (int) $order_id,
						'order_item_id'               => 0,
						'order_key'                   => (string) $order->get_order_key(),
						'parent_id'                   => (int) $v[ 'parent_id' ],
						'product_id'                  => (int) $v[ 'product_id' ],
						'product_order_api_key'       => (string) apply_filters( 'wc_api_manager_custom_product_order_api_key', WC_AM_HASH()->rand_hash(), $v[ 'product_id' ], $order_id, $order ),
						'product_title'               => (string) $v[ 'product_title' ],
						'refund_qty'                  => ! empty( $v[ 'refund_qty' ] ) ? (int) $v[ 'refund_qty' ] : 0,
						'sub_id'                      => (int) $v[ 'sub_id' ],
						'sub_item_id'                 => (int) $v[ 'sub_item_id' ],
						'sub_previous_order_id'       => ! empty( $v[ 'sub_previous_order_id' ] ) ? (int) $v[ 'sub_previous_order_id' ] : 0,
						'sub_order_key'               => (string) $v[ 'sub_order_key' ],
						'sub_parent_id'               => (int) $v[ 'sub_parent_id' ],
						'user_id'                     => (int) $v[ 'user_id' ],
						'variation_id'                => ! empty( $v[ 'variation_id' ] ) ? (int) $v[ 'variation_id' ] : 0
					);

					$format = array(
						'%s',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%s',
						'%d',
						'%d',
						'%s',
						'%d',
						'%d',
						'%s',
						'%s',
						'%d',
						'%d',
						'%d',
						'%d',
						'%s',
						'%d',
						'%d',
						'%d'
					);

					/**
					 * Insert API resource data for new order items only.
					 * This includes newly purchased order items, and order items added to an existing order.
					 */
					$updated = $wpdb->insert( $wpdb->prefix . $this->api_resource_table, $data, $format );
				} else { // API resource exists, and needs to be updated.
					/**
					 * order_item_id is zero so this resource is not deleted if the corresponding line_item is removed from the order,
					 * as it may still exist on the subscription.
					 */
					$data = array(
						'activations_purchased_total' => ! empty( $v[ 'activations_total' ] ) ? (int) $v[ 'activations_total' ] : 0,
						'item_qty'                    => ! empty( $v[ 'item_qty' ] ) ? (int) $v[ 'item_qty' ] : 0,
						'order_id'                    => (int) $order_id,
						'order_item_id'               => 0,
						'refund_qty'                  => ! empty( $v[ 'refund_qty' ] ) ? (int) $v[ 'refund_qty' ] : 0,
						'sub_id'                      => (int) $v[ 'sub_id' ],
						'sub_item_id'                 => (int) $v[ 'sub_item_id' ],
						'sub_order_key'               => (string) $v[ 'sub_order_key' ],
						'sub_parent_id'               => (int) $v[ 'sub_parent_id' ],
						'sub_previous_order_id'       => ! empty( $v[ 'sub_previous_order_id' ] ) ? (int) $v[ 'sub_previous_order_id' ] : 0
					);

					$where = array(
						'sub_id'      => (int) $v[ 'sub_id' ],
						'product_id'  => (int) $v[ 'product_id' ],
						'sub_item_id' => (int) $v[ 'sub_item_id' ]
					);

					$data_format = array(
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d'
					);

					$where_format = array(
						'%d',
						'%d',
						'%d'
					);

					/**
					 * Update an existing API resource for this order item if the order status changed from Completed to something
					 * other than Completed, the item was updated, then the order status was changed back to Completed status.
					 *
					 * The order cannot be edited once it has a Completed status, so API resource updates only happen when
					 * the order status is changed back to Completed.
					 */
					$updated = $wpdb->update( $wpdb->prefix . $this->api_resource_table, $data, $where, $data_format, $where_format );
				}
			}
		}

		if ( ! empty( $existng_products ) ) {
			$sql = "
						SELECT product_id
						FROM {$wpdb->prefix}" . $this->api_resource_table . "
						WHERE order_id = %d
					";

			$resources = $wpdb->get_col( $wpdb->prepare( $sql, $order_id ) );
			$orphans   = array_diff( $resources, $existng_products );

			if ( ! empty( $orphans ) ) {
				foreach ( $orphans as $orphan ) {
					$is_wc_sub = WC_AM_SUBSCRIPTION()->is_wc_subscription( $orphan );

					if ( $is_wc_sub ) {
						$where = array(
							'order_id'   => $order_id,
							'product_id' => $orphan
						);

						$where_format = array(
							'%d',
							'%d'
						);

						/**
						 * Delete orphaned order item API resources that no longer exist on the order.
						 */
						$wpdb->delete( $wpdb->prefix . $this->api_resource_table, $where, $where_format );
					}
				}
			}
		}

		return ! WC_AM_FORMAT()->empty( $updated );
	}

	/**
	 * Update the API resource order items for the order when an order is partially refunded.
	 *
	 * @since 2.0
	 *
	 * @param int $order_id
	 * @param int $refund_id
	 *
	 * @throws \Exception
	 */
	public function order_partially_refunded( $order_id, $refund_id ) {
		do_action( 'wc_am_before_order_partially_refunded', $order_id, $refund_id );
		// Delete grace periods.
		WC_AM_GRACE_PERIOD()->delete_expiration_by_order( $order_id );

		$this->update_order( $order_id );

		do_action( 'wc_am_after_order_partially_refunded', $order_id, $refund_id );
	}

	/**
	 * Update the API resource order items for the order when an order is fully refunded.
	 *
	 * @since 2.3.10
	 *
	 * @param int $order_id
	 * @param int $refund_id
	 *
	 * @throws \Exception
	 */
	public function order_fully_refunded( $order_id, $refund_id ) {
		do_action( 'wc_am_before_order_fully_refunded', $order_id, $refund_id );
		// Delete grace periods.
		WC_AM_GRACE_PERIOD()->delete_expiration_by_order( $order_id );

		$this->update_order( $order_id );

		do_action( 'wc_am_after_order_fully_refunded', $order_id, $refund_id );
	}

	/**
	 * Update the API resource order items for the order when a refund is deleted.
	 *
	 * @since 2.0
	 *
	 * @param int $refund_id
	 * @param int $order_id
	 *
	 * @throws \Exception
	 */
	public function refund_deleted( $refund_id, $order_id ) {
		do_action( 'wc_am_before_refund_deleted', $refund_id, $order_id );
		// Delete grace periods.
		WC_AM_GRACE_PERIOD()->delete_expiration_by_order( $order_id );

		$this->update_order( $order_id );

		do_action( 'wc_am_after_refund_deleted', $refund_id, $order_id );
	}

	/**
	 * Delete API resource order items when the order status is no longer completed.
	 *
	 * @since 2.0
	 *
	 * @param int    $order_id
	 * @param string $old_status
	 * @param string $new_status
	 *
	 * @throws \Exception
	 */
	public function remove_order( $order_id, $old_status, $new_status ) {
		do_action( 'wc_am_before_remove_order', $order_id, $old_status, $new_status );

		// Delete grace periods.
		WC_AM_GRACE_PERIOD()->delete_expiration_by_order( $order_id );
		// Clear the Database Cache
		$this->delete_cache( $order_id );

		$order_statuses = WCAM()->get_grant_access_after_payment() ? array(
			'completed',
			'processing'
		) : array(
			'completed'
		);

		if ( ! in_array( $new_status, $order_statuses ) ) {
			/**
			 * Delete the activations assigned to resources that are assigned to this order_id.
			 */
			$activation_ids = WC_AM_API_ACTIVATION_DATA_STORE()->get_activations_by_order_id( $order_id );

			if ( $activation_ids ) {
				foreach ( $activation_ids as $k => $activation_id ) {
					$activation_resource = WC_AM_API_ACTIVATION_DATA_STORE()->get_activation_resource_by_activation_id( $activation_id );

					if ( ! empty( $activation_resource ) ) {
						WC_AM_ASSOCIATED_API_KEY_DATA_STORE()->delete_associated_api_key_activation_ids( $activation_resource->associated_api_key_id, $activation_id );
					}

					// Deletes all the API Key activations with the activation ID.
					WC_AM_API_ACTIVATION_DATA_STORE()->delete_api_key_activation_by_activation_id( $activation_id );
				}
			}

			/**
			 * Delete order.
			 */ global $wpdb;

			/**
			 * Delete the resources assigned to this order_id.
			 */
			$sql = "
				SELECT product_id
				FROM {$wpdb->prefix}" . $this->api_resource_table . "
				WHERE order_id = %d
			";

			// Get the API resource order items for this product.
			$resources = $wpdb->get_col( $wpdb->prepare( $sql, $order_id ) );

			// Only delete API resource order items that exist.
			if ( $resources ) {
				foreach ( $resources as $product_id ) {
					$where = array(
						'order_id'   => (int) $order_id,
						'product_id' => (int) $product_id
					);

					$where_format = array(
						'%d',
						'%d'
					);

					/**
					 * Delete API resource order items that existed on the order being deleted.
					 */
					$wpdb->delete( $wpdb->prefix . $this->api_resource_table, $where, $where_format );
				}
			}
		}

		do_action( 'wc_am_after_remove_order', $order_id, $old_status, $new_status );
	}

	/**
	 * Tasks to complete before an order is deleted, and while the order still exists.
	 *
	 * @since 2.6.3
	 *
	 * @param int $order_id
	 */
	public function before_delete_order( $order_id ) {
		do_action( 'wc_am_before_delete_order', $order_id );

		// Clear the Database Cache
		$this->delete_cache( $order_id );

		// Delete grace periods.
		WC_AM_GRACE_PERIOD()->delete_expiration_by_order( $order_id );
	}

	/**
	 * Delete API resource order items from the deleted order.
	 *
	 * @since 2.0
	 *
	 * @param int $order_id
	 *
	 * @throws \Exception
	 */
	public function delete_order( $order_id ) {
		global $wpdb;

		/**
		 * Delete the activations assigned to resources that are assigned to this order_id.
		 */
		WC_AM_API_ACTIVATION_DATA_STORE()->delete_all_api_key_activations_by_order_id( $order_id );

		/**
		 * Delete order.
		 */
		$sql = "
			SELECT product_id
			FROM {$wpdb->prefix}" . $this->api_resource_table . "
			WHERE order_id = %d
		";

		// Get the API resource order items for this product.
		$resources = $wpdb->get_col( $wpdb->prepare( $sql, $order_id ) );

		// Only delete API resource order items that exist.
		if ( $resources ) {
			foreach ( $resources as $product_id ) {
				$where = array(
					'order_id'   => $order_id,
					'product_id' => $product_id
				);

				$where_format = array(
					'%d',
					'%d'
				);

				/**
				 * Delete API resource order items that existed on the order being deleted.
				 */
				$wpdb->delete( $wpdb->prefix . $this->api_resource_table, $where, $where_format );
			}
		}

		do_action( 'wc_am_after_delete_order', $order_id );
	}

	/**
	 * Delete an API resource order item that was deleted from an order.
	 *
	 * @since 2.0
	 *
	 * @param int $item_id
	 *
	 * @throws \Exception
	 */
	public function delete_order_item( $item_id ) {
		do_action( 'wc_am_before_delete_order_item', $item_id );

		$api_resource_id = WC_AM_API_RESOURCE_DATA_STORE()->get_api_resource_id_by_item_id( $item_id );
		// Delete grace period.
		WC_AM_GRACE_PERIOD()->delete_expiration_by_api_resource_id( $api_resource_id );

		global $wpdb;

		// Clear the Database Cache
		$this->delete_cache( WC_AM_API_RESOURCE_DATA_STORE()->get_order_id_by_order_item_id( $item_id ) );

		/**
		 * Delete the activations assigned to resources that are assigned to this order_id.
		 */
		$activation_ids = WC_AM_API_ACTIVATION_DATA_STORE()->get_activations_by_order_id( $item_id );

		if ( ! empty( $activation_ids ) ) {
			foreach ( $activation_ids as $k => $activation_id ) {
				$activation_resource = WC_AM_API_ACTIVATION_DATA_STORE()->get_activation_resource_by_activation_id( $activation_id );

				if ( ! empty( $activation_resource ) ) {
					WC_AM_ASSOCIATED_API_KEY_DATA_STORE()->delete_associated_api_key_activation_ids( $activation_resource->associated_api_key_id, $activation_id );
				}

				// Deletes all the API Key activations with the activation ID.
				WC_AM_API_ACTIVATION_DATA_STORE()->delete_api_key_activation_by_activation_id( $activation_id );
			}
		}

		/**
		 * Delete order item.
		 */
		$where = array(
			'order_item_id' => $item_id
		);

		$where_format = array(
			'%d'
		);

		/**
		 * Delete orphaned order item API resources that no longer exist on the order.
		 */
		$wpdb->delete( $wpdb->prefix . $this->api_resource_table, $where, $where_format );

		do_action( 'wc_am_after_delete_order_item', $item_id );
	}

	/**
	 * Delete an API resource subscription item that was deleted from a subscription.
	 *
	 * @since 2.0
	 *
	 * @param int $item_id Previous itemm ID.
	 *
	 * @throws \Exception
	 */
	public function delete_sub_order_item( $item_id ) {
		do_action( 'wc_am_before_delete_sub_order_item', $item_id );

		global $wpdb;

		// Clear the Database Cache
		$this->delete_cache( WC_AM_API_RESOURCE_DATA_STORE()->get_order_id_by_sub_item_id( $item_id ) );

		/**
		 * Delete order item.
		 */
		$where = array(
			'sub_item_id' => $item_id
		);

		$where_format = array(
			'%d'
		);

		/**
		 * Delete orphaned order item API resources that no longer exist on the order.
		 */
		$wpdb->delete( $wpdb->prefix . $this->api_resource_table, $where, $where_format );

		/**
		 * Delete the activations assigned to resources that are assigned to this order_id.
		 */
		$activation_ids = WC_AM_API_ACTIVATION_DATA_STORE()->get_activations_by_sub_item_id( $item_id );

		if ( ! empty( $activation_ids ) ) {
			foreach ( $activation_ids as $k => $activation_id ) {
				$activation_resource = WC_AM_API_ACTIVATION_DATA_STORE()->get_activation_resource_by_activation_id( $activation_id );

				if ( ! empty( $activation_resource ) ) {
					WC_AM_ASSOCIATED_API_KEY_DATA_STORE()->delete_associated_api_key_activation_ids( $activation_resource->associated_api_key_id, $activation_id );
				}

				// Deletes all the API Key activations with the activation ID.
				WC_AM_API_ACTIVATION_DATA_STORE()->delete_api_key_activation_by_activation_id( $activation_id );
			}
		}

		do_action( 'wc_am_after_delete_sub_order_item', $item_id );
	}

	/**
	 * Delete the API resource order items when the order is trashed.
	 *
	 * @since     2.0
	 * @updated   2.5 For WooCommerce HPOS.
	 * @depecated 2.5
	 *
	 * @param int $post_id
	 *
	 * @throws \Exception
	 */
	public function trash_post( $post_id ) {
		_deprecated_function( 'WC_AM_ORDER()->trash_post', 2.5, 'WC_AM_ORDER()->trash_order' );

		return $this->trash_order( $post_id );
	}

	/**
	 * Delete the API resource order items when the order is trashed.
	 *
	 * @since   2.0
	 * @updated 2.5 For WooCommerce HPOS.
	 *
	 * @param int $order_id
	 *
	 * @throws \Exception
	 */
	public function trash_order( $order_id ) {
		$order = WC_AM_ORDER_DATA_STORE()->get_order_object( $order_id );

		$this->delete_cache( $order_id );

		// Delete grace periods.
		WC_AM_GRACE_PERIOD()->delete_expiration_by_order( $order_id );

		if ( is_object( $order ) && $order->get_type() === 'shop_order' ) {
			$this->delete_order( $order_id );
		}
	}

	/**
	 * Restore the API resource order items when the order is restored from the trash.
	 *
	 * @since     2.0
	 * @updated   2.5 For WooCommerce HPOS.
	 * @depecated 2.5
	 *
	 * @param int $post_id
	 *
	 * @throws \Exception
	 */
	public function untrashed_post( $post_id ) {
		_deprecated_function( 'WC_AM_ORDER()->untrashed_post', 2.5, 'WC_AM_ORDER()->untrashed_order' );

		return $this->untrashed_order( $post_id );
	}

	/**
	 * Restore the API resource order items when the order is restored from the trash.
	 *
	 * @since   2.0
	 * @updated 2.5 For WooCommerce HPOS.
	 *
	 * @param int $order_id
	 *
	 * @throws \Exception
	 */
	public function untrashed_order( $order_id ) {
		$order = WC_AM_ORDER_DATA_STORE()->get_order_object( $order_id );

		// Delete grace periods.
		WC_AM_GRACE_PERIOD()->delete_expiration_by_order( $order_id );

		if ( is_object( $order ) && $order->get_type() === 'shop_order' ) {
			$this->update_order( $order_id );
		}
	}

	/**
	 * Update the API resource product title when the shop product title is updated.
	 *
	 * @since     2.0
	 * @depecated 2.5
	 *
	 * @param int     $post_ID Post ID.
	 * @param WP_Post $post    Post object.
	 */
	public function edit_post( $post_ID, $post ) {
		_deprecated_function( 'WC_AM_ORDER()->edit_post', 2.5, 'WC_AM_ORDER()->edit_order' );

		return $this->edit_order( $post_ID, $post );
	}

	/**
	 * Update the API resource product title when the shop product title is updated.
	 *
	 * @since 2.0
	 *
	 * @param int     $post_ID Post ID.
	 * @param WP_Post $post    Post object.
	 */
	public function edit_order( $post_ID, $post ) {
		global $wpdb;

		$product_title = $wpdb->get_var( $wpdb->prepare( "
			SELECT product_title
			FROM {$wpdb->prefix}" . $this->api_resource_table . "
			WHERE parent_id = %d
			LIMIT 1
		", absint( $post_ID ) ) );

		$product_object = WC_AM_PRODUCT_DATA_STORE()->get_product_object( $post_ID );
		$title          = $product_object ? $product_object->get_title() : '';

		if ( WC_AM_FORMAT()->strcmp( $product_title, $title ) ) {
			$data = array(
				'product_title' => $title
			);

			$where = array(
				'parent_id' => $post_ID
			);

			$data_format = array(
				'%s'
			);

			$where_format = array(
				'%d'
			);

			$wpdb->update( $wpdb->prefix . $this->api_resource_table, $data, $where, $data_format, $where_format );
		}
	}

	/**
	 * Email API License Key and API License Email after order complete.
	 *
	 * @since     2.0
	 * @depecated 2.5
	 *
	 * @param object $order WC_Order
	 * @param bool   $sent_to_admin
	 * @param bool   $plain_text
	 */
	public function email_license_keys( $order, $sent_to_admin = false, $plain_text = false ) {
		_deprecated_function( 'WC_AM_ORDER()->email_license_keys', 2.5, 'WC_AM_ORDER()->email_api_keys' );

		$this->email_api_keys( $order, $sent_to_admin = false, $plain_text = false );
	}

	/**
	 * Email API License Key and API License Email after order complete.
	 *
	 * @since 2.0
	 *
	 * @param object $order WC_Order
	 * @param bool   $sent_to_admin
	 * @param bool   $plain_text
	 */
	public function email_api_keys( $order, $sent_to_admin = false, $plain_text = false ) {
		$not_renewal_order = true;
		$cancelled         = false;

		if ( WCAM()->get_wc_subs_exist() ) {
			$not_renewal_order = ! WC_AM_SUBSCRIPTION()->is_subscription_renewal_order( $order->get_id() );
			$cancelled         = WC_AM_SUBSCRIPTION()->is_subscription_cancelled_status( $order->get_id() );
		}

		if ( $not_renewal_order && $cancelled === false ) {
			$resources = WC_AM_ORDER_DATA_STORE()->get_api_resource_items_for_order( $order );

			if ( ! empty( $resources ) && WC_AM_ORDER_DATA_STORE()->has_api_product( $order ) ) {
				$template = ( $plain_text ) ? 'emails/plain/api-keys-order-complete.php' : 'emails/api-keys-order-complete.php';

				wc_get_template( $template, array(
					'order'     => $order,
					'resources' => $resources
				), '', WCAM()->plugin_path() . '/templates/' );
			}
		}
	}

	/**
	 * Add API Resources to view order template.
	 *
	 * @since 3.1
	 *
	 * @param object $order
	 */
	public function order_details_before_order_table( $order ) {
		$order     = WC_AM_ORDER_DATA_STORE()->get_order_object( $order );
		$resources = WC_AM_ORDER_DATA_STORE()->get_api_resource_items_for_order( $order );

		if ( ! empty( $resources ) && WC_AM_ORDER_DATA_STORE()->has_api_product( $order ) ) {
			wc_get_template( 'myaccount/related-api-resources.php', array(
				'order'     => $order,
				'resources' => $resources
			), '', WCAM()->plugin_path() . '/templates/' );
		}
	}

	/**
	 * Delete cached API Resources.
	 *
	 * @since 2.2.0
	 *
	 * @param $order_id
	 */
	private function delete_cache( $order_id ) {
		$order = WC_AM_ORDER_DATA_STORE()->get_order_object( $order_id );

		if ( is_object( $order ) ) {
			$user_id = WC_AM_ORDER_DATA_STORE()->get_customer_id( $order );

			if ( ! empty( $user_id ) ) {
				/**
				 * Refresh cache.
				 *
				 * @since 2.1.7
				 */
				WC_AM_SMART_CACHE()->delete_cache( array(
					'admin_resources' => array(
						'order_id' => $order_id,
						'user_id'  => $user_id
					)
				) );
			}
		}
	}

	/**
	 * The API Resource of a renewal order item is updated with select data from the previous API Resource,
	 * then the previous API Resource is deleted and cannot be rebuilt as it is expired.
	 *
	 * @since 3.0
	 *
	 * @param int $order_id
	 *
	 * @throws \Exception
	 */
	private function update_api_resource_after_renewal_order_completed( $order_id ) {
		global $wpdb;

		$current_api_resources = WC_AM_API_RESOURCE_DATA_STORE()->get_all_api_resources_for_order_id( $order_id );

		if ( ! WC_AM_FORMAT()->empty( $current_api_resources ) ) {
			foreach ( $current_api_resources as $current_api_resource ) {
				if ( ! WC_AM_FORMAT()->empty( $current_api_resource ) ) {
					$is_renewed_api_resource          = ! WC_AM_FORMAT()->empty( wc_get_order_item_meta( $current_api_resource->order_item_id, '_wc_am_is_renewed_api_resource' ) );
					$api_resource_renewal_not_updated = WC_AM_FORMAT()->empty( wc_get_order_item_meta( $current_api_resource->order_item_id, '_wc_am_api_resource_renewal_updated' ) );

					if ( $is_renewed_api_resource && $api_resource_renewal_not_updated ) {
						$previous_api_resource_object = wc_get_order_item_meta( $current_api_resource->order_item_id, '_wc_am_previous_api_resource_object' );

						if ( ! WC_AM_FORMAT()->empty( $previous_api_resource_object ) && $current_api_resource->product_id === $previous_api_resource_object->product_id ) {
							// From previous API Resource
							$previous_activation_ids = WC_AM_API_ACTIVATION_DATA_STORE()->get_activation_ids_by_api_resource_id( $previous_api_resource_object->api_resource_id );

							if ( ! empty( $previous_activation_ids ) ) {
								$updated = false;

								$order = WC_AM_ORDER_DATA_STORE()->get_order_object( $order_id );

								// If not a downloadable product, set status to completed so activations are not messed up after renewal is completed.
								if ( $order->get_status() == 'processing' ) {
									$order->update_status( 'completed' );
								}

								// Delete the old API Resource to allow the unique Product Order API Key from the old API Resource to be copied to the new API Resource.
								WC_AM_API_RESOURCE_DATA_STORE()->delete_api_resource_by_api_resource_id( $previous_api_resource_object->api_resource_id );

								if ( count( $previous_activation_ids ) == $previous_api_resource_object->activations_total ) {
									$data = array(
										'activation_ids'              => WC_AM_FORMAT()->json_encode( $previous_activation_ids ),
										'activations_total'           => $previous_api_resource_object->activations_total,
										'activations_purchased'       => $previous_api_resource_object->activations_purchased,
										'activations_purchased_total' => $previous_api_resource_object->activations_purchased_total,
										'product_order_api_key'       => $previous_api_resource_object->product_order_api_key
									);

									$where = array(
										'api_resource_id' => $current_api_resource->api_resource_id
									);

									$data_format = array(
										'%s',
										'%d',
										'%s'
									);

									$where_format = array(
										'%d'
									);

									$updated = $wpdb->update( $wpdb->prefix . $this->api_resource_table, $data, $where, $data_format, $where_format );
								}

								if ( ! WC_AM_FORMAT()->empty( $updated ) ) {
									$this->update_api_key_activations_after_order_completed( $previous_activation_ids, $current_api_resource );
									// Flag old order item as expired.
									wc_add_order_item_meta( $previous_api_resource_object->order_item_id, '_wc_am_is_expired_api_resource', 'yes' );
									// Flag as updated, so it cannot be updated again.
									wc_add_order_item_meta( $current_api_resource->order_item_id, '_wc_am_api_resource_renewal_updated', __( 'yes', 'woocommerce-api-manager' ) );
									// Delete grace periods.
									WC_AM_GRACE_PERIOD()->delete_expiration_by_api_resource_id( $previous_api_resource_object->api_resource_id );
									// Remove the expired API Resource cache.
									WC_AM_SMART_CACHE()->refresh_cache_by_order_id( $previous_api_resource_object->order_id, false );
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Updates select API Key activation data to match the API Resource data where the Product ID matches.
	 *
	 * @since 3.0
	 *
	 * @param array  $previous_activation_ids
	 * @param object $current_api_resource
	 */
	private function update_api_key_activations_after_order_completed( $previous_activation_ids, $current_api_resource ) {
		global $wpdb;

		if ( ! empty( $previous_activation_ids ) && ! WC_AM_FORMAT()->empty( $current_api_resource ) ) {
			foreach ( $previous_activation_ids as $previous_activation_id ) {
				$data = array(
					'api_resource_id' => $current_api_resource->api_resource_id,
					'order_id'        => $current_api_resource->order_id,
					'order_item_id'   => $current_api_resource->order_item_id
				);

				$where = array(
					'activation_id' => $previous_activation_id
				);

				$data_format = array(
					'%d',
					'%d',
					'%d'
				);

				$where_format = array(
					'%d'
				);

				$wpdb->update( $wpdb->prefix . $this->api_activation_table, $data, $where, $data_format, $where_format );
			}
		}
	}
}