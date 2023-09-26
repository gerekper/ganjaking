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
			'desc'     => sprintf( '%s%s%s%s%s', __( 'This cleanup process will delete any API Resources that have expired, or no longer exist as line items on orders or subscriptions. Related API Key activations will also be deleted.', 'woocommerce-api-manager' ), '<br><strong class="red">', __( 'Note: ', 'woocommerce-api-manager' ), '</strong>', ( ! empty( $next_cleanup ) ) ? __( 'The cleanup process will run automatically next on ', 'woocommerce-api-manager' ) . '<code>' . wc_clean( WC_AM_FORMAT()->unix_timestamp_to_date( $next_cleanup ) ) . '</code>' : __( 'The cleanup process is not scheduled to automatically run.', 'woocommerce-api-manager' ) ),
			'callback' => array( $this, 'queue_cleanup_event' ),
		);

		$import_has_run = get_option( 'wc_software_add_on_data_added' ) == 'yes';

		$tools[ 'wc_am_queue_wc_software_add_on_data_import' ] = array(
			'name'     => __( 'Import WC Software Add-On Data', 'woocommerce-api-manager' ),
			'button'   => __( 'Import WC Software Add-On Data', 'woocommerce-api-manager' ),
			'desc'     => sprintf( __( '%s%s%s%sNote:%s This tool automatically imports the WooCommerce Software Add-On License Keys and Activations, and builds Order line items into API Resources. %sThe import event will run only once.%s%sNote:%s For each Product that should be an API Product, select the Product edit > API checkbox, set the Activation Limit on the Product edit > API form, and set the API Access Expires value if desired. All customer API Resources will be udpated with the new values.%sNote:%s Either add the %sWooCommerce API Manager PHP Library for Plugins and Themes%s to your plugin or theme, or build a new client to connect to the API Manager APIs according to the %sdocumentation%s to take advantage of all the features available.', 'woocommerce-api-manager' ), '<strong class="red">', ( $import_has_run ) ? __( 'Import has already run.', 'woocommerce-api-manager' ) : '', '</strong><br>', '<strong class="red">', '</strong>', '<strong class="red">', '</strong>', '<br><strong class="red">', '</strong>', '<br><strong class="red">', '</strong>', '<a href="' . esc_url( 'https://www.toddlahman.com/shop/woocommerce-api-manager-php-library-for-plugins-and-themes/' ) . '" target="blank">', '</a>', '<a href="' . esc_url( 'https://woocommerce.com/document/woocommerce-api-manager/' ) . '" target="blank">', '</a>' ),
			'callback' => array( $this, 'queue_wc_software_add_on_data_import' ),
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

	/**
	 * Queue the API Resources cleanup event.
	 *
	 * @since 2.7
	 */
	public function queue_wc_software_add_on_data_import() {
		WC_AM_BACKGROUND_EVENTS()->queue_wc_software_add_on_data_import_event();
	}
}