<?php
/**
 * Add extra profile fields for users in admin
 *
 * @package WooCommerce Redsys Gateway WooCommerce.com > https://woocommerce.com/products/redsys-gateway/
 * @since 13.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2023 José Conti.
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Gateway_Redsys_Scheduled_Actions Class.
 */
class WC_Gateway_Redsys_Scheduled_Actions {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'redsys_schedule_actions' ) );
		add_action( 'resdys_clean_transients', array( $this, 'redsys_clean_transients_action' ) );
		add_action( 'resdys_clean_tokens', array( $this, 'redsys_clean_tokens_action' ) );
	}
	/**
	 * Debug
	 *
	 * @param string $log Log.
	 */
	public function debug( $log ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$debug = new WC_Logger();
			$debug->add( 'redsys-scheduled-actions', $log );
		}
	}
	/**
	 * Schedule actions
	 */
	public function redsys_schedule_actions() {
		// Add conditional using settings. Active or not active.
		if ( false === as_next_scheduled_action( 'resdys_clean_transients' ) ) {
			as_schedule_recurring_action( strtotime( 'now' ), DAY_IN_SECONDS, 'resdys_clean_transients' );
		}
		// Add recurring para eliminar datos asociados a tokens que ya no existen.
		if ( false === as_next_scheduled_action( 'resdys_clean_tokens' ) ) {
			as_schedule_recurring_action( strtotime( 'now' ), DAY_IN_SECONDS, 'resdys_clean_tokens' );
		}
	}
	/**
	 * Clean transients
	 */
	public function redsys_clean_transients_action() {
		global $wpdb;

		$expired = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout%' AND option_value < UNIX_TIMESTAMP()" );
		foreach ( $expired as $transient ) {
			$key = str_replace( '_transient_timeout_', '', $transient );
			delete_transient( $key );
			$this->debug( 'Transient deleted: ' . $key );
		}
	}
	/**
	 * Clean tokens
	 */
	public function redsys_clean_tokens_action() {
		global $wpdb;

		$texids = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '%txnid_%'" );
		foreach ( $texids as $texid ) {
			$key = str_replace( 'txnid_', '', $texid );
			$this->debug( 'Texid_id: ' . $key );
			$this->debug( 'Checking if Token exist' );
			$token = WC_Payment_Tokens::get( $key );
			if ( ! $token ) {
				$this->debug( 'Token not exist' );
				$this->debug( 'Deleting information related to token ID: ' . $key );
				delete_option( 'token_type_' . $key );
				delete_option( 'txnid_' . $key );
				$this->debug( 'Deleted: token_type_' . $key );
				$this->debug( 'Deleted: txnid_' . $key );
			} else {
				$this->debug( 'Token exist' );
			}
		}
	}
}
return new WC_Gateway_Redsys_Scheduled_Actions();
