<?php
/**
 * WooCommerce Pre-Orders
 *
 * @package     WC_Pre_Orders/Admin
 * @author      WooThemes
 * @copyright   Copyright (c) 2013, WooThemes
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Pre-Orders Cron class
 *
 * Adds custom wp-cron schedule and handles pre-order completion checks
 *
 * @since 1.0
 */
class WC_Pre_Orders_Cron {


	/**
	 * Adds hooks and filters
	 *
	 * @since 1.0
	 * @return \WC_Pre_Orders_Cron
	 */
	public function __construct() {

		// Add custom schedule, the default interval for pre-order check is every 5 minutes
		add_filter( 'cron_schedules', array( $this, 'add_custom_schedules' ) );

		// Schedule a complete pre-order check event if it doesn't exist - activation hooks are unreliable, so attempt to schedule events on every page load
		add_action( 'init', array( $this, 'add_scheduled_events' ) );

	}


	/**
	 * Adds custom wp-cron schedule named 'wc_pre_orders_completion_check' with custom 5 minute interval
	 *
	 * @since 1.0
	 * @param array $schedules existing WP recurring schedules
	 * @return array
	 */
	public function add_custom_schedules( $schedules ) {

		$interval = apply_filters( 'wc_pre_orders_completion_check_interval', 3600, $schedules );

		$schedules['wc_pre_orders_completion_check'] = array(
			'interval' => $interval,
			'display'  => sprintf( __( 'Every %d minutes', 'wc-pre-orders' ), $interval / 60 )
		);

		return $schedules;
	}


	/**
	 * Add scheduled events to wp-cron if not already added
	 *
	 * @since 1.0
	 * @return array
	 */
	public function add_scheduled_events() {

		// Schedule pre-order completion check with custom interval named 'wc_pre_orders_completion_check'
		// note the next execution time if the plugin is deactivated then reactivated is the current time + 5 minutes
		if ( ! wp_next_scheduled( 'wc_pre_orders_completion_check' ) )
			wp_schedule_event( time() + 300, 'wc_pre_orders_completion_check', 'wc_pre_orders_completion_check' );
	}


} // end \WC_Pre_Orders_Cron class
