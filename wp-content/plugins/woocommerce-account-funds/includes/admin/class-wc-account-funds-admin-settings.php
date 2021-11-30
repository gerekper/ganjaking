<?php
/**
 * Admin Settings.
 *
 * @package WC_Account_Funds/Admin
 * @since   2.6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Account_Funds_Settings_Page', false ) ) {
	include_once WC_ACCOUNT_FUNDS_PATH . 'includes/abstracts/abstract-wc-account-funds-settings-page.php';
}

if ( class_exists( 'WC_Account_Funds_Admin_Settings', false ) ) {
	return new WC_Account_Funds_Admin_Settings();
}

/**
 * Class WC_Account_Funds_Admin_Settings.
 */
class WC_Account_Funds_Admin_Settings extends WC_Account_Funds_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @since 2.6.0
	 */
	public function __construct() {
		$this->id    = 'account_funds';
		$this->label = __( 'Account Funds', 'woocommerce-account-funds' );

		parent::__construct();
	}

	/**
	 * Initializes the settings API.
	 *
	 * @since 2.6.0
	 */
	public function init_settings_api() {
		include_once WC_ACCOUNT_FUNDS_PATH . 'includes/admin/settings/class-wc-account-funds-settings-general.php';

		$this->settings_api = new WC_Account_Funds_Settings_General();
	}
}

return new WC_Account_Funds_Admin_Settings();
