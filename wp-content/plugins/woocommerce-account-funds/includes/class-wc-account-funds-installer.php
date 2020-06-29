<?php
/**
 * Installer and updater class.
 *
 * @package WC_Account_Funds
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Account Funds installer and updater.
 */
class WC_Account_Funds_Installer {

	/**
	 * List of updates from version-to-version.
	 *
	 * @var array
	 */
	protected static $_updates = array(
		'2.0.9' => 'updates/class-wc-account-funds-updater-2.0.9.php',
		'2.1.3' => 'updates/class-wc-account-funds-updater-2.1.3.php',
	);

	/**
	 * Install the plugin. Called by handler of register_activation_hook.
	 *
	 * @param string $version Plugin's version.
	 */
	public static function install( $version ) {
		self::_set_options( $version );
	}

	/**
	 * Set options used by this plugin.
	 *
	 * @see self::install
	 *
	 * @param string $version Plugin's version.
	 *
	 * @return void
	 */
	protected static function _set_options( $version ) {
		$old_settings = get_option( 'wcaf_settings', array(
			'give_discount'   => 0,
			'discount_type'   => 'fixed',
			'discount_amount' => 0,
		) );

		add_option( 'account_funds_give_discount', $old_settings['give_discount'] );
		add_option( 'account_funds_discount_type', $old_settings['discount_type'] );
		add_option( 'account_funds_discount_amount', $old_settings['discount_amount'] );
		add_option( 'account_funds_enable_topup', 'no' );
		add_option( 'account_funds_min_topup', '' );
		add_option( 'account_funds_max_topup', '' );
		add_option( 'account_funds_partial_payment', 'no' );

		self::_update_version( $version );
	}

	/**
	 * Update version on DB. Called by version check routine that's called when
	 * plugin is loaded.
	 *
	 * @see WC_Account_Funds::version_check
	 *
	 * @param string $version Version to store in DB.
	 *
	 * @return void
	 */
	protected static function _update_version( $version ) {
		delete_option( 'account_funds_version' );
		add_option( 'account_funds_version', $version );
	}

	/**
	 * Check for update based on current plugin's version versus installed
	 * version. Perform update routine if version mismatches.
	 *
	 * @param string $current_version Plugin's version.
	 *
	 * @return void
	 */
	public static function update_check( $current_version ) {
		$installed_version = get_option( 'account_funds_version' );
		if ( $current_version !== $installed_version ) {
			self::_update( $installed_version );
		}
	}

	/**
	 * Perform update.
	 *
	 * @param string $installed_version Installed version.
	 *
	 * @return void
	 */
	protected static function _update( $installed_version ) {
		require_once( 'updates/interface-wc-account-funds-updater.php' );

		foreach ( self::$_updates as $version => $updater_file ) {
			if ( version_compare( $installed_version, $version, '<' ) ) {
				$updater = include( $updater_file );
				$updater->update();

				self::_update_version( $version );
			}
		}
	}

	/**
	 * Flush rewrite rules to add the account page endpoint.
	 *
	 * @return void
	 */
	public static function flush_rewrite_rules() {
		$endpoint = get_option( 'woocommerce_myaccount_account_funds_endpoint', 'account-funds' );
		add_rewrite_endpoint( $endpoint, EP_ROOT | EP_PAGES );
		flush_rewrite_rules();
	}
}

