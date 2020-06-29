<?php
/**
 * Install
 *
 * @author   SomewhereWarm
 * @category Classes
 * @package  WooCommerce Mix and Match Products/Install
 * @since    1.2.0
 * @version  1.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_Install Class.
 *
 * Handles installation and updating tasks.
 */
class WC_MNM_Install {

	/**
	 * DB updates and callbacks that need to be run per version.
	 *
	 * @var array
	 */
	private static $db_updates = array(
		'1.2.0' => array(
			'wc_mnm_update_120_main',
		)
	);

	/**
	 * Background update class.
	 *
	 * @var obj
	 */
	private static $background_updater;

	/**
	 * Plugin version.
	 *
	 * @var str
	 */
	private static $current_version;

	/**
	 * Plugin DB version.
	 *
	 * @var array
	 */
	private static $current_db_version;

	/**
	 * Hook in tabs.
	 */
	public static function init() {

		add_action( 'init', array( __CLASS__, 'init_background_updater' ), 5 );
		add_action( 'init', array( __CLASS__, 'check_updating' ) );
		add_action( 'admin_init', array( __CLASS__, 'check_version' ) );

		// Adds support for the 'mix-and-match' product type - added here instead of 'WC_Mix_and_Match_Admin_Meta_Boxes' as it's used by front-end and REST.
		add_filter( 'product_type_selector', array( __CLASS__, 'product_selector_filter' ) );

		// Get plugin and plugin DB versions.
		self::$current_version    = get_option( 'wc_mix_and_match_version', null );
		self::$current_db_version = get_option( 'wc_mix_and_match_db_version', null );

		// Include the Updater class. 
		include_once( 'class-wc-mnm-background-updater.php' );
	}

	/**
	 * Adds support for the 'mix and match' product type.
	 *
	 * @param  array 	$options
	 * @return array
	 * @since  1.8.0
	 */
	public static function product_selector_filter( $options ) {
		$options[ 'mix-and-match' ] = __( 'Mix and Match product', 'woocommerce-mix-and-match-products' );
		return $options;
	}

	/**
	 * Init background updates.
	 */
	public static function init_background_updater() {
		self::$background_updater = new WC_MNM_Background_Updater();
	}

	/**
	 * Check version and run the updater if necessary.
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 */
	public static function check_version() {
		if ( current_user_can( 'manage_woocommerce' ) && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
			if ( self::$current_version !== WC_Mix_and_Match()->version ) {
				self::install();
			} else {
				if ( ! empty( $_GET[ 'force_wc_mnm_db_update' ] ) && wp_verify_nonce( $_GET[ '_wc_mnm_admin_nonce' ], 'wc_mnm_force_db_update_nonce' ) ) {
					self::force_update();
				}
			}
		}
	}

	/**
	 * If the DB version is out-of-date, a DB update must be in progress: define a 'WC_MNM_UPDATING' constant.
	 */
	public static function check_updating() {
		if ( is_null( self::$current_db_version ) || version_compare( self::$current_db_version, max( array_keys( self::$db_updates ) ), '<' ) ) {
			if ( ! defined( 'WC_MNM_UPDATING' ) ) {
				define( 'WC_MNM_UPDATING', true );
			}
		}
	}

	/**
	 * Install MnM.
	 */
	public static function install() {

		// If MNM type does not exist, create it.
		if ( false === $mnm_term_exists = get_term_by( 'slug', 'mix-and-match', 'product_type' ) ) {
			wp_insert_term( __( 'Mix and Match', 'woocommerce-mix-and-match-products' ), 'product_type', array( 'slug' => 'mix-and-match' ) );
		}

		// Update plugin version - once set, 'check_version()' will not call 'install()' again.
		self::update_version();

		// Plugin data exists - queue upgrade tasks.
		if ( $mnm_term_exists && ( is_null( self::$current_db_version ) || version_compare( self::$current_db_version, max( array_keys( self::$db_updates ) ), '<' ) ) ) {
			self::update();
			// Nothing found - this is a new install :)
		} else {
			self::update_db_version();
		}
	}

	/**
	 * Update WC MnM version to current.
	 */
	private static function update_version() {
		delete_option( 'wc_mix_and_match_version' );
		add_option( 'wc_mix_and_match_version', WC_Mix_and_Match()->version );
	}

	/**
	 * Push all needed DB updates to the queue for processing.
	 */
	private static function update() {

		if ( ! is_object( self::$background_updater ) ) {
			self::init_background_updater();
		}

		wp_cache_flush();

		$update_queued = false;

		foreach ( self::$db_updates as $version => $update_callbacks ) {
			if ( version_compare( self::$current_db_version, $version, '<' ) ) {
				wc_get_logger()->log( 'info', sprintf( 'Updating to version %s.', $version ), 'wc_mnm_db_updates' );
				foreach ( $update_callbacks as $update_callback ) {
					wc_get_logger()->log( 'info', sprintf( '- Queuing %s callback.', $update_callback ), 'wc_mnm_db_updates' );
					self::$background_updater->push_to_queue( $update_callback );
					$update_queued = true;
				}
			}
		}

		if ( $update_queued ) {
			// Define 'WC_MNM_UPDATING' constant.
			if ( ! defined( 'WC_MNM_UPDATING' ) ) {
				define( 'WC_MNM_UPDATING', true );
			}
			// Add option to keep track of time.
			delete_option( 'wc_mnm_update_init' );
			add_option( 'wc_mnm_update_init', gmdate( 'U' ) );
			// Add 'updating' notice and save early (saving on the 'shutdown' action will fail if a chained request arrives before the 'shutdown' hook fires).
			WC_MNM_Admin_Notices::add_maintenance_notice( 'updating' );
			WC_MNM_Admin_Notices::save_notices();
			// Dispatch.
			self::$background_updater->save()->dispatch();
		}
	}

	/**
	 * Force re-start the update cron if everything else fails.
	 */
	public static function force_update() {

		if ( ! is_object( self::$background_updater ) ) {
			self::init_background_updater();
		}

		/**
		 * Updater cron action.
		 */
		do_action( self::$background_updater->get_cron_hook_identifier() );
		wp_safe_redirect( admin_url() );
	}

	/**
	 * Updates plugin DB version when all updates have been processed.
	 */
	public static function update_complete() {

		wc_get_logger()->log( 'info', 'Data update complete.', 'wc_mnm_db_updates' );
		self::update_db_version();
		delete_option( 'wc_mnm_update_init' );

		wp_cache_flush();
	}

	/**
	 * True if an update is in progress.
	 *
	 * @return bool
	 */
	public static function is_update_pending() {
		return defined( 'WC_MNM_UPDATING' );
	}

	/**
	 * True if an update is in progress.
	 *
	 * @return bool
	 */
	public static function is_update_in_progress() {
		return self::$background_updater->is_updating();
	}

	/**
	 * True if an update process is running.
	 *
	 * @return bool
	 */
	public static function is_update_process_running() {
		return self::$background_updater->is_process_running();
	}

	/**
	 * Update DB version to current.
	 *
	 * @param  string  $version
	 */
	private static function update_db_version( $version = null ) {

		$version = is_null( $version ) ? WC_Mix_and_Match()->version : $version;

		delete_option( 'wc_mix_and_match_db_version' );
		add_option( 'wc_mix_and_match_db_version', $version );

		wc_get_logger()->log( 'info', sprintf( 'Database version is %s.', get_option( 'wc_mix_and_match_db_version', 'unknown' ) ), 'wc_mnm_db_updates' );
	}

}

WC_MNM_Install::init();
