<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Account_Funds_Shortcodes
 */
class WC_Account_Funds_Shortcodes {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_shortcode( 'get-account-funds', array( $this, 'get_account_funds' ) );
	}

	/**
	 * Show account funds for current user
	 * @return string
	 */
	public function get_account_funds() {
		return WC_Account_Funds::get_account_funds();
	}
}

new WC_Account_Funds_Shortcodes();