<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_AM_Background_Process', false ) ) {
	include_once( dirname( WCAM()->get_plugin_file() ) . '/includes/wcam-background-events-process.php' );
}

/**
 * WooCommerce API Manager Background Events Class
 *
 * @since       2.5.5
 *
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @package     WooCommerce API Manager/Events
 */
class WC_AM_Background_Events {

	protected $background_process;

	/**
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * @static
	 * @return \WC_AM_Background_Events
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	private function __construct() {
		if ( ! $this->background_process ) {
			$this->background_process = new WCAM_Events_Background_Process();
		}

		add_action( 'wc_am_weekly_event', array( $this, 'queue_weekly_event' ) );
	}

	/**
	 * Run weekly scheduled events.
	 *
	 * @since 2.5.5
	 */
	public function queue_weekly_event() {
		$this->queue_cleanup_expired_api_resources();
		$this->queue_cleanup_expired_api_activations();
		$this->queue_cleanup_expired_grace_periods();
		$this->background_process->push_to_queue( array( 'task' => 'cleanup_hash' ) );
		$this->background_process->save()->dispatch();
	}

	/**
	 * Queue cleanup_expired_api_resources().
	 *
	 * Runs in the background.
	 *
	 * @since 2.5.5
	 */
	private function queue_cleanup_expired_api_resources() {
		$order_ids = WC_AM_API_RESOURCE_DATA_STORE()->get_all_order_ids();

		if ( is_array( $order_ids ) && ! empty( $order_ids ) ) {
			foreach ( $order_ids as $key => $order_id ) {
				if ( ! empty( $order_id ) ) {
					$this->background_process->push_to_queue( array( 'task' => 'cleanup_expired_api_resources', 'order_id_api_resources' => $order_id ) );
				}
			}
		}
	}

	/**
	 * Delete expired API Resources, and associated API Key activations.
	 *
	 * Runs in the background.
	 *
	 * @since 2.5.5
	 */
	public function cleanup_expired_api_resources( $order_id ) {
		global $wpdb;

		if ( ! empty( $order_id ) ) {
			$sql = "
				SELECT *
				FROM {$wpdb->prefix}" . WC_AM_USER()->get_api_resource_table_name() . "
				WHERE order_id = %d
			";

			// Get the API resource order items for this user.
			$resources = $wpdb->get_results( $wpdb->prepare( $sql, $order_id ) );

			try {
				if ( ! WC_AM_FORMAT()->empty( $resources ) ) {
					WC_AM_API_RESOURCE_DATA_STORE()->get_active_resources( $resources );
				}
			} catch ( Exception $e ) {
				WC_AM_LOG()->log_info( esc_html__( 'Expired API Resources Cleanup ERROR for Order ID # ', 'woocommerce-api-manager' ) . $e, 'expired-api-resources-cleanup' );
			}
		}
	}

	/**
	 * Queue cleanup_expired_api_activations.
	 *
	 * Runs in the background.
	 *
	 * @since 2.5.5
	 */
	private function queue_cleanup_expired_api_activations() {
		$order_ids = WC_AM_API_ACTIVATION_DATA_STORE()->get_all_order_ids();

		if ( is_array( $order_ids ) && ! empty( $order_ids ) ) {
			foreach ( $order_ids as $key => $order_id ) {
				if ( ! empty( $order_id ) ) {
					$this->background_process->push_to_queue( array( 'task' => 'cleanup_expired_api_activations', 'order_id_api_activations' => $order_id ) );
				}
			}
		}
	}

	/**
	 * Delete expired API Key activations that are associated with expired API Resources, or associated with API Resources that no longer exists.
	 * An orphan activation is where there is no API Resource associated with the activation. Orhpans only exist due to direct database API Resource deletion.
	 * If API Manager methods are used orphan activations will not exist.
	 *
	 * Runs in the background.
	 *
	 * @since 2.5.5
	 */
	public function cleanup_expired_api_activations( $order_id ) {
		global $wpdb;

		if ( ! empty( $order_id ) ) {
			$sql = "
				SELECT *
				FROM {$wpdb->prefix}" . WC_AM_USER()->get_api_resource_table_name() . "
				WHERE order_id = %d
			";

			// Get the API resource order items for this user.
			$resources = $wpdb->get_results( $wpdb->prepare( $sql, $order_id ) );

			try {
				if ( ! WC_AM_FORMAT()->empty( $resources ) ) {
					WC_AM_API_RESOURCE_DATA_STORE()->get_active_resources( $resources );
				} else {
					/**
					 * The API Resource for this activation does not exist. Delete API Key activations and update Associated API Key activation_ids.
					 */
					$activation_ids = WC_AM_API_ACTIVATION_DATA_STORE()->get_activations_by_order_id_from_api_activation_table( $order_id );

					if ( ! WC_AM_FORMAT()->empty( $activation_ids ) ) {
						foreach ( $activation_ids as $k => $activation_id ) {
							$activation_resource = WC_AM_API_ACTIVATION_DATA_STORE()->get_activation_resource_by_activation_id( $activation_id );

							if ( ! WC_AM_FORMAT()->empty( $activation_resource ) ) {
								WC_AM_ASSOCIATED_API_KEY_DATA_STORE()->delete_associated_api_key_activation_ids( $activation_resource->associated_api_key_id, $activation_id );
							}

							// Deletes all the API Key activations with the activation ID.
							WC_AM_API_ACTIVATION_DATA_STORE()->delete_api_key_activation_by_activation_id( $activation_id );
						}
					}
				}
			} catch ( Exception $e ) {
				WC_AM_LOG()->log_info( esc_html__( 'Expired API Activations Cleanup ERROR for Order ID# ', 'woocommerce-api-manager' ) . $e, 'expired-api-activations-cleanup' );
			}
		}
	}

	/**
	 * Queue cleanup_expired_grace_periods.
	 *
	 * Runs in the background.
	 *
	 * @since 2.6
	 */
	private function queue_cleanup_expired_grace_periods() {
		$api_resource_ids = WC_AM_GRACE_PERIOD()->get_all_api_resource_ids();

		if ( is_array( $api_resource_ids ) && ! empty( $api_resource_ids ) ) {
			foreach ( $api_resource_ids as $key => $api_resource_id ) {
				if ( ! empty( $api_resource_id ) ) {
					$this->background_process->push_to_queue( array( 'task' => 'cleanup_expired_grace_periods', 'api_resource_id_grace_periods' => $api_resource_id ) );
				}
			}
		}
	}

	/**
	 * Delete orphaned grace periods.
	 *
	 * Runs in the background.
	 *
	 * @since 2.6
	 *
	 * @param int $api_resource_id
	 */
	public function cleanup_expired_grace_periods( $api_resource_id ) {
		if ( ! WC_AM_API_RESOURCE_DATA_STORE()->api_resource_id_exists( $api_resource_id ) ) {
			WC_AM_GRACE_PERIOD()->delete_expiration_by_api_resource_id( $api_resource_id );
		}
	}

	/**
	 * Queue add_new_api_product_orders().
	 *
	 * Runs in the background.
	 *
	 * @since   2.0
	 * @updated 2.5.5 Moved from WC_AM_Order
	 *
	 * @param int $product_id
	 *
	 * @throws \Exception
	 */
	public function queue_add_new_api_product_orders( $product_id ) {
		$order_ids = WC_AM_ORDER_DATA_STORE()->get_all_order_ids_by_meta_value( $product_id );

		if ( ! empty( $order_ids ) ) {
			foreach ( $order_ids as $key => $order_id ) {
				$order = WC_AM_ORDER_DATA_STORE()->get_order_object( $order_id );

				if ( is_object( $order ) ) {
					$user_id = WC_AM_ORDER_DATA_STORE()->get_customer_id( $order );
					$items   = $order->get_items();

					if ( ! empty( $user_id ) && WC_AM_FORMAT()->count( $items ) > 0 ) {
						foreach ( $items as $item_id => $item ) {
							$parent_product_id = WC_AM_PRODUCT_DATA_STORE()->get_parent_product_id( $item );
							$variation_id      = $item->get_variation_id();
							$is_api            = WC_AM_PRODUCT_DATA_STORE()->is_api_product( $parent_product_id );

							if ( $is_api && ( WC_AM_ORDER_DATA_STORE()->has_status_completed( $order ) || ( WCAM()->get_grant_access_after_payment() && WC_AM_ORDER_DATA_STORE()->has_status_processing( $order ) ) ) ) {
								$item_product_id = ! empty( $variation_id ) && WC_AM_PRODUCT_DATA_STORE()->has_valid_product_status( $variation_id ) ? $variation_id : $item->get_product_id();

								if ( $item_product_id == $product_id ) {
									// The order has an API Product.
									$this->background_process->push_to_queue( array(
										                                          'task'                                => 'add_new_api_product_orders',
										                                          'order_id_add_new_api_product_orders' => $order_id
									                                          ) );

									break;
								}
							}
						}
					}
				}

				unset( $order );
			}
		}
	}

	/**
	 * Confirms the Product ID exists in an order, then adds or updates the order data to the API Resources via a background update process.
	 * Should only be run when checking the API (is_api) checkbox for the first time on a product, so all orders containing that product are
	 * added to the API Resources.
	 *
	 * Runs in the background.
	 *
	 * @since   2.0
	 * @updated 2.5.5 Task code moved from wcam-background-api-product-updater.php
	 *
	 * @param int $order_id
	 *
	 * @throws \Exception
	 */
	public function add_new_api_product_orders( $order_id ) {
		WC_AM_ORDER()->update_order( $order_id );
	}

	/**
	 * Queue update_api_resource_activations_for_product().
	 *
	 * Runs in the background.
	 *
	 * @since 2.5.5 Moved from WC_AM_Order
	 *
	 * @param int $product_id
	 */
	public function queue_update_api_resource_activations_for_product( $product_id ) {
		$order_ids = WC_AM_API_RESOURCE_DATA_STORE()->get_all_order_ids_with_rows_containing_product_id( $product_id );

		if ( is_array( $order_ids ) && ! empty( $order_ids ) ) {
			foreach ( $order_ids as $order_id ) {
				if ( ! empty( $order_id ) ) {
					$this->background_process->push_to_queue( array(
						                                          'task'                                                   => 'update_api_resource_activations_for_product',
						                                          'product_id_update_api_resource_activations_for_product' => $product_id,
						                                          'order_id_update_api_resource_activations_for_product'   => $order_id
					                                          ) );
				}
			}
		}
	}

	/**
	 * Update the API Resource activations_purchased_total when the product activation limit increases.
	 *
	 * Runs in the background.
	 *
	 * @since   2.0.1
	 * @updated 2.5.5 Task code moved from wcam-background-api-resource-activations-updater.php
	 *
	 * @param int $product_id
	 * @param int $order_id
	 */
	public function update_api_resource_activations_for_product( $product_id, $order_id ) {
		global $wpdb;

		if ( ! empty( $product_id ) && ! empty( $order_id ) ) {
			$current_product_activations      = WC_AM_PRODUCT_DATA_STORE()->get_api_activations( $product_id );
			$item_quanity_and_refund_quantity = WC_AM_API_RESOURCE_DATA_STORE()->get_item_quantity_and_refund_quantity_by_order_id_and_product_id( $order_id, $product_id );
			$activations_purchased_total      = $current_product_activations * absint( $item_quanity_and_refund_quantity->item_qty - $item_quanity_and_refund_quantity->refund_qty );

			$data = array(
				'activations_purchased'       => $current_product_activations,
				'activations_purchased_total' => $activations_purchased_total
			);

			$where = array(
				'order_id'   => $order_id,
				'product_id' => $product_id
			);

			$data_format = array(
				'%d',
				'%d'
			);

			$where_format = array(
				'%d',
				'%d'
			);

			$updated = $wpdb->update( $wpdb->prefix . WC_AM_USER()->get_api_resource_table_name(), $data, $where, $data_format, $where_format );

			if ( ! empty( $updated ) ) {
				WC_AM_SMART_CACHE()->delete_activation_api_cache_by_order_id( $order_id );
				WC_AM_SMART_CACHE()->refresh_cache_by_order_id( $order_id, false );
			}
		}
	}

	/**
	 * Queue update_api_resource_access_expires_for_product().
	 *
	 * Runs in the background.
	 *
	 * @since 2.4
	 * @since 2.5.5 Moved from WC_AM_Order
	 *
	 * @param int $product_id
	 */
	public function queue_update_api_resource_access_expires_for_product( $product_id ) {
		// Value set on Product edit for API Access Expires.
		$product_access_expires = WC_AM_PRODUCT_DATA_STORE()->get_meta( $product_id, '_access_expires' );
		$order_ids              = WC_AM_API_RESOURCE_DATA_STORE()->get_all_order_ids_with_rows_containing_product_id( $product_id );

		if ( is_array( $order_ids ) && ! empty( $order_ids ) ) {
			foreach ( $order_ids as $order_id ) {
				if ( ! empty( $order_id ) ) {
					$this->background_process->push_to_queue( array(
						                                          'task'                                                                  => 'update_api_resource_access_expires_for_product',
						                                          'product_id_update_api_resource_access_expires_for_product'             => $product_id,
						                                          'order_id_update_api_resource_access_expires_for_product'               => $order_id,
						                                          'product_access_expires_update_api_resource_access_expires_for_product' => $product_access_expires
					                                          ) );
				}
			}
		}
	}

	/**
	 * Update the API Resource access_expires value when the product API Access Expires value is set to a value greater than 0.
	 *
	 * Runs in the background.
	 *
	 * @since   2.4
	 * @updated 2.5.5 Task code moved from wcam-background-api-resource-access-expires-updater.php
	 *
	 * @param int $product_id
	 * @param int $order_id
	 * @param int $product_access_expires
	 */
	public function update_api_resource_access_expires_for_product( $product_id, $order_id, $product_access_expires ) {
		global $wpdb;

		// Time when order created.
		$order_created_time = WC_AM_ORDER_DATA_STORE()->get_order_time_to_epoch_time_stamp( $order_id );
		// Value when API Access for the API Resource will expire.
		$line_item_access_expires = ! empty( $product_access_expires ) ? absint( ( (int) $product_access_expires * DAY_IN_SECONDS ) + $order_created_time ) : 0;

		$data = array(
			'access_expires' => $line_item_access_expires
		);

		$where = array(
			'order_id'   => $order_id,
			'product_id' => $product_id,
			'sub_id'     => 0
		);

		$data_format = array(
			'%d'
		);

		$where_format = array(
			'%d',
			'%d',
			'%d'
		);

		$updated = $wpdb->update( $wpdb->prefix . WC_AM_USER()->get_api_resource_table_name(), $data, $where, $data_format, $where_format );

		if ( ! empty( $updated ) ) {
			WC_AM_SMART_CACHE()->delete_activation_api_cache_by_order_id( $order_id );
			WC_AM_SMART_CACHE()->refresh_cache_by_order_id( $order_id, false );
		}
	}
}