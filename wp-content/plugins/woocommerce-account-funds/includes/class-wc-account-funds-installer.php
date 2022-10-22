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
		'2.3.0' => 'updates/class-wc-account-funds-updater-2.3.0.php',
		'2.3.7' => 'updates/class-wc-account-funds-updater-2.3.7.php',
	);

	/**
	 * Init installation.
	 *
	 * @since 2.3.7
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'check_version' ) );
		add_action( 'admin_init', array( __CLASS__, 'install_actions' ) );
		add_action( 'admin_init', array( __CLASS__, 'add_notices' ), 15 );
	}

	/**
	 * Check the plugin version and run the updater if necessary.
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 *
	 * @since 2.3.7
	 */
	public static function check_version() {
		$installed_version = get_option( 'account_funds_version' );

		if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( $installed_version, WC_ACCOUNT_FUNDS_VERSION, '<' ) ) {
			self::_update( $installed_version );
			do_action( 'wc_account_funds_updated' );
		}
	}

	/**
	 * Install actions when an update button is clicked within the admin area.
	 *
	 * @since 2.3.7
	 */
	public static function install_actions() {
		if ( ! empty( $_GET['wc_account_funds_2_3_7_update'] ) ) {
			check_admin_referer( 'wc_account_funds_2_3_7_update', 'wc_account_funds_2_3_7_update_nonce' );

			include_once WC_ACCOUNT_FUNDS_PATH . 'includes/updates/interface-wc-account-funds-updater.php';
			include_once WC_ACCOUNT_FUNDS_PATH . 'includes/updates/class-wc-account-funds-updater-2.3.7.php';

			$action  = ( isset( $_GET['action'] ) ? wc_clean( wp_unslash( $_GET['action'] ) ) : 'skip' );
			$updater = new WC_Account_Funds_Updater_2_3_7();
			$updater->process_update_action( $action );
		}
	}

	/**
	 * Add installer notices.
	 *
	 * @since 2.3.7
	 */
	public static function add_notices() {
		if ( ! get_option( 'account_funds_update_2_3_7_action' ) && get_option( 'account_funds_update_2_3_7_fix_order_balances' ) ) {
			include_once WC_ACCOUNT_FUNDS_PATH . 'includes/updates/interface-wc-account-funds-updater.php';
			include_once WC_ACCOUNT_FUNDS_PATH . 'includes/updates/class-wc-account-funds-updater-2.3.7.php';

			WC_Account_Funds_Admin_Notices::add_notice( 'wc_account_funds_update_2_3_7' );
		}
	}

	/**
	 * Install the plugin. Called by handler of register_activation_hook.
	 *
	 * @since 2.3.7 Deprecated parameter `$version`.
	 *
	 * @param null $deprecated No longer used.
	 */
	public static function install( $deprecated = null ) {
		self::_set_options();
	}

	/**
	 * Set options used by this plugin.
	 *
	 * @see self::install
	 *
	 * @since 2.3.7 Deprecated parameter `$version`.
	 *
	 * @param null $deprecated No longer used.
	 */
	protected static function _set_options( $deprecated = null ) {
		$old_settings = get_option(
			'wcaf_settings',
			array(
				'give_discount'   => 0,
				'discount_type'   => 'fixed',
				'discount_amount' => 0,
			)
		);

		add_option( 'account_funds_give_discount', $old_settings['give_discount'] );
		add_option( 'account_funds_discount_type', $old_settings['discount_type'] );
		add_option( 'account_funds_discount_amount', $old_settings['discount_amount'] );
		add_option( 'account_funds_enable_topup', 'no' );
		add_option( 'account_funds_min_topup', '' );
		add_option( 'account_funds_max_topup', '' );
		add_option( 'account_funds_partial_payment', 'no' );
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
	 * Perform update.
	 *
	 * @param string $installed_version Installed version.
	 */
	protected static function _update( $installed_version ) {
		require_once 'updates/interface-wc-account-funds-updater.php';

		foreach ( self::$_updates as $version => $updater_file ) {
			if ( version_compare( $installed_version, $version, '<' ) ) {
				$updater = include $updater_file;
				$updater->update();

				self::_update_version( $version );
			}
		}

		self::_update_version( WC_ACCOUNT_FUNDS_VERSION );
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

	/**
	 * Check for update based on current plugin's version versus installed
	 * version. Perform update routine if version mismatches.
	 *
	 * @param string $current_version Plugin's version.
	 *
	 * @deprecated 2.3.7
	 */
	public static function update_check( $current_version ) {
		wc_deprecated_function( __FUNCTION__, '2.3.7', 'WC_Account_Funds_Installer::check_version()' );

		self::check_version();
	}
}

WC_Account_Funds_Installer::init();
