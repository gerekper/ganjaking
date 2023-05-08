<?php
/**
 * Debug Tools class.
 *
 * @since       2.6.11
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @package     WooCommerce API Manager/Debug Tools
 */

defined( 'ABSPATH' ) || exit;

final class WC_AM_Debug_Tools {

	public function __construct() {
		add_filter( 'woocommerce_debug_tools', array( $this, 'add_debug_tools' ) );
	}

	/**
	 * Add  tools to display on the WooCommerce > System Status > Tools administration screen.
	 *
	 * - $tools['tool_key']:  The key used to add the tool to the array of available tools.
	 *      - 'name': The section name given to the tool.
	 *      - 'button': The text displayed on the tool's button.
	 *      - 'desc': The long description for the tool.
	 *      - 'callback': The callback used to perform the tool's action.
	 *
	 * array( 'tool_key' => array(
	 *      'name',
	 *      'button',
	 *      'desc',
	 *      callback' ) );
	 *
	 * @since 2.6.11
	 *
	 * @return array
	 */
	public function add_debug_tools( $tools ) {
		$next_cleanup = WC_AM_BACKGROUND_EVENTS()->get_next_scheduled_cleanup();

		$tools[ 'wc_am_queue_api_resources_repair' ] = array(
			'name'     => __( 'API Resources Repair', 'woocommerce-api-manager' ),
			'button'   => __( 'Regenerate Missing API Resources', 'woocommerce-api-manager' ),
			'desc'     => sprintf( '%s%s%s%s%s%s%s%s%s', __( 'This will build API Resources from order line items that contain API Products with a subscription that has not yet expired, and meets all requirements to be an active API Resource.', 'woocommerce-api-manager' ), '<br><strong class="red">', __( 'Note: ', 'woocommerce-api-manager' ), '</strong>', __( 'Only API Resources that are missing, but should exist, will be generated.', 'woocommerce-api-manager' ), '<br><strong class="red">', __( 'Note: ', 'woocommerce-api-manager' ), '</strong>', __( 'To rebuild existing API Resource(s), go to the Order edit screen, change the order status to On-hold then back to Completed. This will rebuild the API Resource(s), but will also delete any existing API Key activations.', 'woocommerce-api-manager' ) ),
			'callback' => array( $this, 'queue_repair_event' ),
		);

		$tools[ 'wc_am_queue_api_resources_cleanup' ] = array(
			'name'     => __( 'API Resources Cleanup', 'woocommerce-api-manager' ),
			'button'   => __( 'Cleanup API Resources', 'woocommerce-api-manager' ),
			'desc'     => sprintf( '%s%s%s%s%s', __( 'This cleanup process will delete any API Resources that have expired, or no longer exist as line items on orders or subscriptions. Related API Key activations will also be deleted.', 'woocommerce-api-manager' ), '<br><strong class="red">', __( 'Note: ', 'woocommerce-api-manager' ), '</strong>', ( ! empty( $next_cleanup ) ) ? __( 'The cleanup process will run automatically next on ', 'woocommerce-api-manager' ) . wc_clean( WC_AM_FORMAT()->unix_timestamp_to_date( $next_cleanup ) ) : __( 'The cleanup process is not scheduled to automatically run.', 'woocommerce-api-manager' ) ),
			'callback' => array( $this, 'queue_cleanup_event' ),
		);

		return $tools;
	}

	/**
	 * Queue the API Resources repair event.
	 *
	 * @since 2.6.11
	 */
	public function queue_repair_event() {
		WC_AM_BACKGROUND_EVENTS()->queue_repair_event();
	}

	/**
	 * Queue the API Resources cleanup event.
	 *
	 * @since 2.6.12
	 */
	public function queue_cleanup_event() {
		WC_AM_BACKGROUND_EVENTS()->queue_weekly_event();
	}
}