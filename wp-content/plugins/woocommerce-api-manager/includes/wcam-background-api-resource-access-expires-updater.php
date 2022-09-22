<?php
/**
 * WooCommerce API Manager Background Updater Class
 *
 * Uses https://github.com/A5hleyRich/wp-background-processing to handle DB
 * updates in the background.
 *
 * @since       2.4
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @package     WooCommerce API Manager/Background Updater
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_AM_Background_Process', false ) ) {
	require_once( dirname( __FILE__ ) . '/abstracts/ab-wc-am-background-process.php' );
}

class WCAM_Background_API_Resource_Access_Expires_Updater extends WP_Background_Process {

	/**
	 * Initiate new background process.
	 */
	public function __construct() {
		// Uses unique prefix per blog so each blog has separate queue.
		$this->prefix = 'wp_' . get_current_blog_id();
		$this->action = 'wc_am_api_resource_access_expires_updater';

		parent::__construct();
	}

	/**
	 * Dispatch updater.
	 * Updater will still run via cron job if this fails for any reason.
	 */
	public function dispatch() {
		$dispatched = parent::dispatch();

		if ( is_wp_error( $dispatched ) ) {
			WC_AM_LOG()->log_error( esc_html__( 'Unable to dispatch WooCommerce API Manager API Resource Access Expires updater: ', 'woocommerce-api-manager' ) . $dispatched->get_error_message(), 'api-resource-access-expires-update' );
		}
	}

	/**
	 * Handle cron healthcheck
	 * Restart the background process if not already running and data exists in the queue.
	 */
	public function handle_cron_healthcheck() {
		if ( $this->is_process_running() ) {
			// Background process already running.
			return;
		}

		if ( $this->is_queue_empty() ) {
			// No data to process.
			$this->clear_scheduled_event();

			return;
		}

		$this->handle();
	}

	/**
	 * Schedule fallback event.
	 */
	protected function schedule_event() {
		if ( ! wp_next_scheduled( $this->cron_hook_identifier ) ) {
			wp_schedule_event( time() + 10, $this->cron_interval_identifier, $this->cron_hook_identifier );
		}
	}

	/**
	 * Is the updater running?
	 *
	 * @return boolean
	 */
	public function is_updating() {
		return $this->is_queue_empty() === false;
	}

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over.
	 *
	 * @return false
	 */
	protected function task( $item ) {
		if ( ! is_array( $item ) && ! isset( $item[ 'product_id' ] ) && ! isset( $item[ 'order_id' ] ) && ! isset( $item[ 'product_access_expires' ] ) ) {
			return false;
		}

		global $wpdb;

		$product_id             = absint( $item[ 'product_id' ] );
		$order_id               = absint( $item[ 'order_id' ] );
		$product_access_expires = $item[ 'product_access_expires' ];

		WC_AM_LOG()->log_info( esc_html__( 'API Resource Access Expires update started for Product ID# ', 'woocommerce-api-manager' ) . absint( $product_id ) . esc_html__( ' on Order ID# ', 'woocommerce-api-manager' ) . absint( $order_id ), 'api-resource-access-expires-update' );

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

		$wpdb->update( $wpdb->prefix . WC_AM_USER()->get_api_resource_table_name(), $data, $where, $data_format, $where_format );

		return false;
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		WC_AM_LOG()->log_info( esc_html__( 'API Resource Access Expires update completed.', 'woocommerce-api-manager' ), 'api-resource-access-expires-update' );

		parent::complete();
	}
}