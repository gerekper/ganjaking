<?php
/**
 * WooCommerce API Manager Events Background Process Class.
 *
 * @since       2.5.5
 *
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @package     WooCommerce API Manager/Events
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_AM_Background_Process', false ) ) {
	include_once( dirname( WCAM()->get_plugin_file() ) . '/includes/abstracts/wcam-background-process.php' );
}

/**
 * WC_Privacy_Background_Process class.
 */
class WCAM_Events_Background_Process extends WC_AM_Background_Process {

	/**
	 * Initiate new background process.
	 */
	public function __construct() {
		// Uses unique prefix per blog so each blog has separate queue.
		$this->prefix = 'wp_' . get_current_blog_id();
		$this->action = 'wcam_events_background_process';

		parent::__construct();
	}

	/**
	 * Code to execute for each item in the queue
	 *
	 * @param string $item Queue item to iterate over.
	 *
	 * @return bool
	 */
	protected function task( $item ) {
		if ( ! is_array( $item ) || empty( $item[ 'task' ] ) ) {
			return false;
		}

		switch ( $item[ 'task' ] ) {
			case 'cleanup_expired_api_resources':
				WC_AM_BACKGROUND_EVENTS()->cleanup_expired_api_resources( absint( $item[ 'order_id_api_resources' ] ) );
				break;
			case 'cleanup_expired_api_activations':
				WC_AM_BACKGROUND_EVENTS()->cleanup_expired_api_activations( absint( $item[ 'order_id_api_activations' ] ) );
				break;
			case 'cleanup_expired_grace_periods':
				WC_AM_BACKGROUND_EVENTS()->cleanup_expired_grace_periods( absint( $item[ 'api_resource_id_grace_periods' ] ) );
				break;
			case 'cleanup_hash':
				WC_AM_HASH()->cleanup_hash();
				break;
			case 'update_api_resource_activations_for_product':
				WC_AM_BACKGROUND_EVENTS()->update_api_resource_activations_for_product( absint( $item[ 'product_id_update_api_resource_activations_for_product' ] ), absint( $item[ 'order_id_update_api_resource_activations_for_product' ] ) );
				break;
			case 'add_new_api_product_orders':
				WC_AM_BACKGROUND_EVENTS()->add_new_api_product_orders( absint( $item[ 'order_id_add_new_api_product_orders' ] ) );
				break;
			case 'update_api_resource_access_expires_for_product':
				WC_AM_BACKGROUND_EVENTS()->update_api_resource_access_expires_for_product( absint( $item[ 'product_id_update_api_resource_access_expires_for_product' ] ), absint( $item[ 'order_id_update_api_resource_access_expires_for_product' ] ), absint( $item[ 'product_access_expires_update_api_resource_access_expires_for_product' ] ) );
				break;
		}

		return false;
	}
}