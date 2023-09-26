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

		if ( get_option( 'woocommerce_api_manager_api_resoure_cleanup_data' ) == 'yes' ) {
			add_action( 'wc_am_weekly_event', array( $this, 'queue_weekly_event' ) );
		}

		add_action( 'wc_am_daily_event', array( $this, 'queue_daily_event' ) );
	}

	/**
	 * Run daily scheduled events.
	 *
	 * @since 3.0
	 */
	public function queue_daily_event() {
		$this->queue_subscription_30_day_expiration_notification();
		$this->queue_subscription_7_day_expiration_notification();
		$this->queue_subscription_1_day_expiration_notification();
		$this->background_process->push_to_queue( array( 'task' => 'sub_expire_notification' ) );
		$this->background_process->save()->dispatch();
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

		/**
		 * Log option.
		 * @since 3.1
		 */
		if ( get_option( 'woocommerce_api_manager_api_resource_log_cleanup_event_data' ) == 'yes' ) {
			WC_AM_LOG()->log_info( esc_html__( 'Expired API Resources Cleanup event was completed on ', 'woocommerce-api-manager' ) . WC_AM_FORMAT()->unix_timestamp_to_date( WC_AM_ORDER_DATA_STORE()->get_current_time_stamp() ), 'expired-api-resources-cleanup' );
		}
	}

	/**
	 * Run on-demand.
	 *
	 * @since 2.6.8
	 */
	public function queue_repair_event() {
		$this->queue_repair_missing_api_resources();
		$this->background_process->push_to_queue( array( 'task' => 'wc_am_repair_hash' ) );
		$this->background_process->save()->dispatch();

		WC_AM_LOG()->log_info( esc_html__( 'Missing API Resources Repair event was completed on ', 'woocommerce-api-manager' ) . WC_AM_FORMAT()->unix_timestamp_to_date( WC_AM_ORDER_DATA_STORE()->get_current_time_stamp() ), 'missing-api-resources-repair' );
	}

	/**
	 * Run on-demand.
	 *
	 * @since 2.7
	 */
	public function queue_wc_software_add_on_data_import_event() {
		if ( get_option( 'woocommerce_api_manager_translate_software_add_on_queries' ) == 'yes' && get_option( 'wc_software_add_on_data_added' ) === false ) {
			$this->queue_repair_event();
			$this->queue_add_wc_software_add_on_data();
			$this->background_process->push_to_queue( array( 'task' => 'wc_am_add_wc_software_add_on_data_event' ) );
			$this->background_process->save()->dispatch();

			WC_AM_LOG()->log_info( esc_html__( 'Add WC Software Add-on data event was completed on ', 'woocommerce-api-manager' ) . WC_AM_FORMAT()->unix_timestamp_to_date( WC_AM_ORDER_DATA_STORE()->get_current_time_stamp() ), 'wc-software-add-on-add-data' );

			update_option( 'wc_software_add_on_data_added', 'yes' );
		}
	}

	/**
	 * Queue send_subscription_30_day_expiration_notification().
	 *
	 * Runs in the background.
	 *
	 * @since 3.0
	 */
	private function queue_subscription_30_day_expiration_notification() {
		$api_resource_ids = WC_AM_API_RESOURCE_DATA_STORE()->get_api_resource_ids_by_access_expires_number_of_days_before_expiration( 30 );

		if ( is_array( $api_resource_ids ) && ! empty( $api_resource_ids ) ) {
			foreach ( $api_resource_ids as $key => $api_resource_id ) {
				if ( ! empty( $api_resource_id ) ) {
					$this->background_process->push_to_queue( array(
						                                          'task'                                           => 'send_subscription_30_day_expiration_notification',
						                                          'subscription_30_day_expiration_api_resource_id' => $api_resource_id
					                                          ) );
				}
			}
		}
	}

	/**
	 * Sends 30 day subscription expiration notification email.
	 *
	 * Runs in the background.
	 *
	 * @since 3.0
	 */
	public function send_subscription_30_day_expiration_notification( $api_resource_id ) {
		$this->send_subscription_expiration_notification( $api_resource_id );
	}

	/**
	 * Queue send_subscription_7_day_expiration_notification().
	 *
	 * Runs in the background.
	 *
	 * @since 3.0
	 */
	private function queue_subscription_7_day_expiration_notification() {
		$api_resource_ids = WC_AM_API_RESOURCE_DATA_STORE()->get_api_resource_ids_by_access_expires_number_of_days_before_expiration( 7 );

		if ( is_array( $api_resource_ids ) && ! empty( $api_resource_ids ) ) {
			foreach ( $api_resource_ids as $key => $api_resource_id ) {
				if ( ! empty( $api_resource_id ) ) {
					$this->background_process->push_to_queue( array(
						                                          'task'                                          => 'send_subscription_7_day_expiration_notification',
						                                          'subscription_7_day_expiration_api_resource_id' => $api_resource_id
					                                          ) );
				}
			}
		}
	}

	/**
	 * Sends 7 day subscription expiration notification email.
	 *
	 * Runs in the background.
	 *
	 * @since 3.0
	 */
	public function send_subscription_7_day_expiration_notification( $api_resource_id ) {
		$this->send_subscription_expiration_notification( $api_resource_id );
	}

	/**
	 * Queue send_subscription_1_day_after_expiration_notification().
	 *
	 * Runs in the background.
	 *
	 * @since 3.0
	 */
	private function queue_subscription_1_day_expiration_notification() {
		$api_resource_ids = WC_AM_API_RESOURCE_DATA_STORE()->get_api_resource_ids_by_access_expires_number_of_days_after_expiration( 1 );

		if ( is_array( $api_resource_ids ) && ! empty( $api_resource_ids ) ) {
			foreach ( $api_resource_ids as $key => $api_resource_id ) {
				// The $api_resource_id must also have a Grace Period since this is sent after the API Resource has expired.
				if ( ! empty( $api_resource_id ) && WC_AM_GRACE_PERIOD()->exists( $api_resource_id ) ) {
					$this->background_process->push_to_queue( array(
						                                          'task'                                                => 'send_subscription_1_day_after_expiration_notification',
						                                          'subscription_1_day_after_expiration_api_resource_id' => $api_resource_id
					                                          ) );
				}
			}
		}
	}

	/**
	 * Sends 1 day subscription expiration notification email after the expiration.
	 *
	 * Runs in the background.
	 *
	 * @since 3.0
	 */
	public function send_subscription_1_day_after_expiration_notification( $api_resource_id ) {
		$this->send_subscription_expiration_notification( $api_resource_id );
	}

	/**
	 * Sends subscription expiration notification email.
	 *
	 * Runs in the background.
	 *
	 * @since 3.0
	 * @updated 3.1 Confirm $api_resource_id is not a lifetime subscription.
	 */
	public function send_subscription_expiration_notification( $api_resource_id ) {
		if ( ! empty( $api_resource_id ) && ! WC_AM_API_RESOURCE_DATA_STORE()->is_lifetime_subscription( $api_resource_id ) ) {
			WC_AM_EMAILS()->send_subscription_expiration_notification( $api_resource_id );
		}
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
				WC_AM_LOG()->log_info( esc_html__( 'Expired API Activations Cleanup ERROR for Order ID# ', 'woocommerce-api-manager' ) . $order_id . '. ' . $e, 'expired-api-activations-cleanup' );
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
		if ( ! empty( $api_resource_id ) && ! WC_AM_API_RESOURCE_DATA_STORE()->api_resource_id_exists( $api_resource_id ) ) {
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
		if ( ! empty( $product_id ) ) {
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
		if ( ! empty( $order_id ) ) {
			WC_AM_ORDER()->update_order( $order_id );
		}
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
		if ( ! empty( $product_id ) ) {
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
			$item_quanity_and_refund_quantity = WC_AM_API_RESOURCE_DATA_STORE()->get_api_resource_by_order_id_and_product_id( $order_id, $product_id );
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
		if ( ! empty( $product_id ) ) {
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

		if ( ! empty( $product_id ) && ! empty( $order_id ) ) {

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

	/**
	 * Queue repair_missing_api_resources().
	 *
	 * Runs in the background.
	 *
	 * @since 2.6.8
	 */
	private function queue_repair_missing_api_resources() {
		$am_order_ids = WC_AM_API_RESOURCE_DATA_STORE()->get_all_order_ids();
		$wc_order_ids = WC_AM_API_RESOURCE_DATA_STORE()->get_all_woocommerce_order_ids();

		if ( ! empty( $am_order_ids ) && ! empty( $wc_order_ids ) ) {
			$order_ids = array_diff( $wc_order_ids, $am_order_ids );

			if ( ! empty( $order_ids ) ) {
				foreach ( $order_ids as $order_id ) {
					$this->background_process->push_to_queue( array( 'task' => 'repair_missing_api_resources', 'repair_order_id_api_resources' => $order_id ) );
				}

				WC_AM_LOG()->log_info( esc_html__( 'Missing API Resources Repaired.', 'woocommerce-api-manager' ), 'missing-api-resources-repair' );
			} else {
				WC_AM_LOG()->log_info( esc_html__( 'There Were No Missing API Resources To Repair.', 'woocommerce-api-manager' ), 'missing-api-resources-repair' );
			}
		}
	}

	/**
	 * Adds missing API Resources from the API Resources database table.
	 *
	 * Runs in the background.
	 *
	 * @since   2.6.8
	 *
	 * @param int $order_id
	 */
	public function repair_missing_api_resources( $order_id ) {
		global $wpdb;

		if ( ! empty( $order_id ) ) {
			try {
				WC_AM_ORDER()->update_order( $order_id );

				$sql = "
					SELECT *
					FROM {$wpdb->prefix}" . WC_AM_USER()->get_api_resource_table_name() . "
					WHERE order_id = %d
				";

				// Get the API resource order items for this user.
				$resources = $wpdb->get_results( $wpdb->prepare( $sql, $order_id ) );

				if ( ! WC_AM_FORMAT()->empty( $resources ) ) {
					WC_AM_API_RESOURCE_DATA_STORE()->get_active_resources( $resources );
				}
			} catch ( Exception $e ) {
				WC_AM_LOG()->log_info( esc_html__( 'Missing API Resources Repair ERROR for Order ID # ', 'woocommerce-api-manager' ) . $order_id . '. ' . $e, 'missing-api-resources-repair' );
			}
		}
	}

	/**
	 * Return the timestamp of the next scheduled weekly cleanup event, or false if nothing is scheduled.
	 *
	 * @since 2.6.12
	 *
	 * @return false|int
	 */
	public function get_next_scheduled_cleanup() {
		return wp_next_scheduled( 'wc_am_weekly_event' );
	}

	/**
	 * Queue repair_missing_api_resources().
	 *
	 * Runs in the background.
	 *
	 * @since 2.7
	 */
	private function queue_add_wc_software_add_on_data() {
		global $wpdb;

		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}woocommerce_software_licenses';" ) ) {
			$key_ids = $wpdb->get_col( "
				SELECT key_id
				FROM {$wpdb->prefix}woocommerce_software_licenses
			" );

			if ( ! empty( $key_ids ) ) {
				foreach ( $key_ids as $key_id ) {
					$this->background_process->push_to_queue( array( 'task' => 'add_wc_software_add_on_data', 'wc_software_add_on_data_key_id' => $key_id ) );
				}

				WC_AM_LOG()->log_info( esc_html__( 'WC Software Add-on API Keys added to the Asscociated API Keys database table.', 'woocommerce-api-manager' ), 'wc-software-add-on-add-data' );
			} else {
				WC_AM_LOG()->log_info( esc_html__( 'There Were No WC Software Add-on API Keys added to the Asscociated API Keys database table.', 'woocommerce-api-manager' ), 'wc-software-add-on-add-data' );
			}
		}
	}

	/**
	 * Adds WC Software Add-on API Keys to the wc_am_associated_api_key database table
	 * and API Key/License Key activations to the wc_am_api_activation database table.
	 *
	 * Runs in the background.
	 *
	 * @since   2.7
	 *
	 * @param int $key_id
	 */
	public function add_wc_software_add_on_data( $key_id ) {
		global $wpdb;

		if ( ! empty( $key_id ) ) {
			// Add Software Licenses as API Keys.
			try {
				$license_sql = "
					SELECT *
					FROM {$wpdb->prefix}woocommerce_software_licenses
					WHERE key_id = %d
				";

				$software_license_data = $wpdb->get_row( $wpdb->prepare( $license_sql, $key_id ) );

				if ( ! WC_AM_FORMAT()->empty( $software_license_data ) ) {
					// Translate String product_id to integer product_id.
					$product_id = WC_AM_LEGACY_PRODUCT_ID()->get_product_id_integer( $software_license_data->software_product_id, '_software_product_id' );

					WC_AM_ASSOCIATED_API_KEY_DATA_STORE()->add_associated_api_key( $software_license_data->license_key, $software_license_data->order_id, $product_id );

					// Add Software License activations as API Key activations.
					$activation_sql = "
						SELECT *
						FROM {$wpdb->prefix}woocommerce_software_activations
						WHERE key_id = %d
					";

					$software_activation_data = $wpdb->get_row( $wpdb->prepare( $activation_sql, $key_id ) );

					if ( ! WC_AM_FORMAT()->empty( $software_activation_data ) ) {
						$api_resource = WC_AM_API_RESOURCE_DATA_STORE()->get_api_resource_by_order_id_and_product_id( $software_license_data->order_id, $product_id );

						$request_data = array(
							'api_key'         => ! empty( $software_activation_data->license_key ) ? $software_activation_data->license_key : '',
							'user_ip'         => '',
							'instance'        => ! empty( $software_activation_data->instance ) ? $software_activation_data->instance : '',
							'object'          => ! empty( $software_activation_data->activation_platform ) ? $software_activation_data->activation_platform : '',
							'product_id'      => $product_id,
							'version'         => $software_license_data->software_version,
							'update_requests' => 0
						);

						if ( $software_activation_data->activation_active == 1 && ! empty( $request_data[ 'product_id' ] ) && ! empty( $request_data[ 'api_key' ] ) && ! empty( $request_data[ 'instance' ] ) ) {
							WC_AM_API_ACTIVATION_DATA_STORE()->add_api_key_activation( $api_resource->user_id, $api_resource, $request_data );
						}
					}
				}
			} catch ( Exception $e ) {
				WC_AM_LOG()->log_info( esc_html__( 'Add WC Software Add-on data ERROR for key_id ', 'woocommerce-api-manager' ) . $key_id . '. ' . $e, 'wc-software-add-on-add-data' );
			}
		}
	}
}