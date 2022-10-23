<?php
/**
 * WC_Free_Gift_Coupons_Install class
 *
 * @package  WooCommerce Free Gift Coupons
 * @since    2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles installation and updating tasks.
 *
 * @class    WC_Free_Gift_Coupons_Install
 * @version  2.0.0
 */
class WC_Free_Gift_Coupons_Install {

	/** 
	 * DB updates and callbacks that need to be run per version
	 * 
	 * @var array
	 */
	private static $db_updates = array(
		'2.0.0' => array(
			'wc_fgc_update_200',
			'wc_fgc_update_200_db_version'
		)
	);

	/**
	 * Background update class.
	 *
	 * @var WC_Free_Gift_Coupons_Updater
	 */
	private static $background_updater;

	/**
	 * Current plugin version.
	 *
	 * @var string
	 */
	private static $current_version;

	/**
	 * Current DB version.
	 *
	 * @var string
	 */
	private static $current_db_version;

	/**
	 * Hook in tabs.
	 */
	public static function init() {

		// Installation and DB updates handling.
		add_action( 'init', array( __CLASS__, 'init_background_updater' ), 5 );
		add_action( 'init', array( __CLASS__, 'define_updating_constant' ) );
		add_action( 'admin_init', array( __CLASS__, 'maybe_install' ) );
		add_action( 'admin_init', array( __CLASS__, 'maybe_update' ) );

		// Get PB plugin and DB versions.
		self::$current_version    = get_option( 'woocommerce_free_gift_coupons_version', null );
		self::$current_db_version = get_option( 'woocommerce_free_gift_coupons_db_version', null );

		include_once  'class-wc-free-gift-coupons-background-updater.php' ;
	}

	/**
	 * Init background updates.
	 */
	public static function init_background_updater() {
		self::$background_updater = new WC_Free_Gift_Coupons_Background_Updater();
	}

	/**
	 * Installation needed?
	 *
	 * @since  2.0.0
	 *
	 * @return boolean
	 */
	private static function must_install() {
		return self::$current_version !== WC_Free_Gift_Coupons::$version;
	}

	/**
	 * Installation possible?
	 *
	 * @since  2.0.0
	 *
	 * @return boolean
	 */
	private static function can_install() {
		return false === get_transient( 'wc_fgc_installing' ) && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) && ! defined( 'IFRAME_REQUEST' ) && current_user_can( 'manage_woocommerce' );
	}

	/**
	 * Check version and run the installer if necessary.
	 *
	 * @since  2.0.0
	 */
	public static function maybe_install() {
		if ( self::can_install() && self::must_install() ) {
			self::install();
		}
	}


	/**
	 * DB data exists?
	 *
	 * @since  2.0.0
	 *
	 * @return boolean
	 */
	private static function data_exists() {
		global $wpdb;
		$has_data = $wpdb->get_results( "
			SELECT DISTINCT posts.ID AS coupon_id FROM {$wpdb->posts} AS posts
			LEFT OUTER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id AND postmeta.meta_key = 'gift_ids'
			WHERE posts.post_type = 'shop_coupon'
			AND postmeta.meta_value IS NOT NULL
			LIMIT 1
		" );
		return ! is_wp_error( $has_data ) && ! empty( $has_data );
	}
	

	/**
	 * DB update needed?
	 *
	 * @since  2.0.0
	 *
	 * @return boolean
	 */
	private static function must_update() {
		$db_update_versions = array_keys( self::$db_updates );
		$db_version_target  = end( $db_update_versions );
		return is_null( self::$current_db_version ) || version_compare( self::$current_db_version, $db_version_target, '<' );
	}

	/**
	 * DB update possible?
	 *
	 * @since  2.0.0
	 *
	 * @return boolean
	 */
	private static function can_update() {
		return self::can_install() && self::$current_version === WC_Free_Gift_Coupons::$version;
	}

	/**
	 * Run the updater if triggered.
	 *
	 * @since  2.0.0
	 */
	public static function maybe_update() {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( isset( $_GET[ 'force_wc_fgc_db_update' ] ) && isset( $_GET[ '_wc_fgc_admin_nonce' ] ) && wp_verify_nonce( $_GET[ '_wc_fgc_admin_nonce' ], 'wc_fgc_force_db_update_nonce' ) ) {
			if ( self::can_update() && self::must_update() ) {
				self::force_update();
			}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		} elseif ( isset( $_GET[ 'trigger_wc_fgc_db_update' ] ) && isset( $_GET[ '_wc_fgc_admin_nonce' ] ) && wp_verify_nonce( $_GET[ '_wc_fgc_admin_nonce' ], 'wc_fgc_trigger_db_update_nonce' ) ) {
			if ( self::can_update() && self::must_update() ) {
				self::trigger_update();
			}
		}
	}

	/**
	 * If the DB version is out-of-date, a DB update must be in progress: define a 'WC_FGC_UPDATING' constant.
	 *
	 * @since  2.0.0
	 */
	public static function define_updating_constant() {
		if ( self::is_update_pending() && ! defined( 'WC_FGC_TESTING' ) ) {
			wc_maybe_define_constant( 'WC_FGC_UPDATING', true );
		}
	}

	/**
	 * Install PB.
	 */
	public static function install() {

		// Running for the first time? Set a transient now. Used in 'can_install' to prevent race conditions.
		set_transient( 'wc_fgc_installing', 'yes', 10 );

		// Update plugin version - once set, 'maybe_install' will not call 'install' again.
		self::update_version();

		// Plugin data exists - queue upgrade tasks.
		if ( self::data_exists() && self::must_update() ) {
			// Add 'update' notice and save early -- saving on the 'shutdown' action will fail if a chained request arrives before the 'shutdown' hook fires.
			WC_Free_Gift_Coupons_Admin_Notices::add_maintenance_notice( 'update' );
			WC_Free_Gift_Coupons_Admin_Notices::save_notices();

			if ( self::auto_update_enabled() ) {
				self::update();
			} else {
				delete_transient( 'wc_fgc_installing' );
				delete_option( 'wc_fgc_update_init' );
			}

			// Nothing found - this is a new install :)
		} else {
			self::update_db_version();
		}
	}

	/**
	 * Update WC FGC version to current.
	 */
	private static function update_version() {
		delete_option( 'woocommerce_free_gift_coupons_version' );
		add_option( 'woocommerce_free_gift_coupons_version', WC_Free_Gift_Coupons::$version );
	}

	/**
	 * Push all needed DB updates to the queue for processing.
	 */
	private static function update() {

		if ( ! is_object( self::$background_updater ) ) {
			self::init_background_updater();
		}

		$update_queued = false;

		foreach ( self::$db_updates as $version => $update_callbacks ) {

			if ( version_compare( self::$current_db_version, $version, '<' ) ) {

				$update_queued = true;
				wc_get_logger()->info( sprintf( 'Updating to version %s.', $version ), array( 'source' => 'wc_fgc_db_updates' ) );

				foreach ( $update_callbacks as $update_callback ) {
					wc_get_logger()->info( sprintf( '- Queuing %s callback.', $update_callback ), array( 'source' => 'wc_fgc_db_updates' ) );
					self::$background_updater->push_to_queue( $update_callback );
				}
			}
		}

		if ( $update_queued ) {

			// Define 'WC_FGC_UPDATING' constant.
			wc_maybe_define_constant( 'WC_FGC_UPDATING', true );

			// Keep track of time.
			delete_option( 'wc_fgc_update_init' );
			add_option( 'wc_fgc_update_init', gmdate( 'U' ) );

			// Dispatch.
			self::$background_updater->save()->dispatch();
		}
	}

	/**
	 * Is auto-updating enabled?
	 *
	 * @since  2.0.0
	 *
	 * @return boolean
	 */
	public static function auto_update_enabled() {
		return apply_filters( 'wc_fgc_auto_update_db', true );
	}

	/**
	 * Trigger DB update.
	 *
	 * @since  2.0.0
	 */
	public static function trigger_update() {
		self::update();
		wp_safe_redirect( admin_url() );
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
		wc_get_logger()->info( 'Data update complete.', array( 'source' => 'wc_fgc_db_updates' ) );
		self::update_db_version();
		delete_option( 'wc_fgc_update_init' );
		wp_cache_flush();
	}

	/**
	 * True if a DB update is pending.
	 *
	 * @return boolean
	 */
	public static function is_update_pending() {
		return self::must_update();
	}

	/**
	 * True if a DB update was started but not completed.
	 *
	 * @since  2.0.0
	 *
	 * @return boolean
	 */
	public static function is_update_incomplete() {
		return false !== get_option( 'wc_fgc_update_init', false );
	}


	/**
	 * True if a DB update is in progress.
	 *
	 * @since  2.0.0
	 *
	 * @return boolean
	 */
	public static function is_update_queued() {
		return self::$background_updater->is_update_queued();
	}

	/**
	 * True if an update process is running.
	 *
	 * @return boolean
	 */
	public static function is_update_process_running() {
		return self::is_update_cli_process_running() || self::is_update_background_process_running();
	}

	/**
	 * True if an update background process is running.
	 *
	 * @since  2.0.0
	 *
	 * @return boolean
	 */
	public static function is_update_background_process_running() {
		return self::$background_updater->is_process_running();
	}

	/**
	 * True if a CLI update is running.
	 *
	 * @since  2.0.0
	 *
	 * @return boolean
	 */
	public static function is_update_cli_process_running() {
		return false !== get_transient( 'wc_fgc_update_cli_init', false );
	}

	/**
	 * Update DB version to current.
	 *
	 * @param  string  $version
	 */
	public static function update_db_version( $version = null ) {

		$version = is_null( $version ) ? WC_Free_Gift_Coupons::$version : $version;

		// Remove suffixes.
		$version_parts = explode( '-', $version );
		$version       = count( $version_parts ) === 2 ? $version_parts[ 0 ] : $version;

		delete_option( 'woocommerce_free_gift_coupons_db_version' );
		add_option( 'woocommerce_free_gift_coupons_db_version', $version );

		wc_get_logger()->info( sprintf( 'Database version is %s.', get_option( 'woocommerce_free_gift_coupons_db_version', 'unknown' ) ), array( 'source' => 'wc_fgc_db_updates' ) );
	}

	/**
	 * Get list of DB update callbacks.
	 *
	 * @since  2.0.0
	 *
	 * @return array
	 */
	public static function get_db_update_callbacks() {
		return self::$db_updates;
	}

}

WC_Free_Gift_Coupons_Install::init();
