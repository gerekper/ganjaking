<?php
/**
 * Add extra profile fields for users in admin
 *
 * @package  WooCommerce Redsys Gateway
 * @version  19.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WC_Gateway_Redsys_Scheduled_Actions {

	public function __construct() {

		add_action( 'init', array( $this, 'redsys_schedule_actions' ) );
		add_action( 'resdys_clean_transients', array( $this, 'redsys_clean_action' ) );
	}

	public function redsys_schedule_actions() {
		// Add conditional using settings. Active or not active.
		if ( false === as_next_scheduled_action( 'resdys_clean_transients' ) ) {
			// Add conditional using settings. Active or not active.
			if ( 'yes' === lo-que-sea ) {
			as_schedule_recurring_action( strtotime( 'now' ), DAY_IN_SECONDS, 'resdys_clean_transients' );
			}
			// Add recurring para eliminar datos asociados a tokens que ya no existen.
		}
	}

	public function redsys_clean_action() {
		global $wpdb;

		$expired = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout%' AND option_value < UNIX_TIMESTAMP()" );
		foreach( $expired as $transient ) {
			$key = str_replace('_transient_timeout_', '', $transient );
			delete_transient( $key );
		}
	}

	public function redsys_clean_tokens() {
		// Search for WordPress options with name like _transient_wc_redsys_tokens_ and delete them.
		$texid = $wpdb->get_col( "SELECT option_name FROM{$wpdb->options} WHERE option_name LIKE '%_texid%'" );
	}

}
return new WC_Gateway_Redsys_Scheduled_Actions();
