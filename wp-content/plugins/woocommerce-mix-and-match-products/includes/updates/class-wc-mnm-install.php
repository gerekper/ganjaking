<?php
/**
 * Install
 *
 * @author   SomewhereWarm
 * @category Classes
 * @package  WooCommerce Mix and Match Products/Install
 * @since    1.2.0
 * @version  1.10.0
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
		),
		'1.10.0' => array(
			'wc_mnm_update_1x10_product_meta',
			'wc_mnm_update_1x10_order_item_meta',
			'wc_mnm_update_1x10_db_version',
		)
	);

	/**
	 * Whether install() ran in this request.
	 * @var boolean
	 */
	private static $is_install_request;

	/**
	 * Term runtime cache.
	 * @var boolean
	 */
	private static $mnm_term_exists;

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
		add_action( 'init', array( __CLASS__, 'define_updating_constant' ) );
		add_action( 'init', array( __CLASS__, 'maybe_install' ) );
		add_action( 'admin_init', array( __CLASS__, 'maybe_update' ) );

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
	 * Installation needed?
	 *
	 * @since  1.10.0
	 *
	 * @return boolean
	 */
	private static function must_install() {
		return version_compare( self::$current_version, WC_Mix_and_Match()->version, '<' );
	}

	/**
	 * Installation possible?
	 *
	 * @since  1.10.0
	 *
	 * @return boolean
	 */
	private static function can_install() {
		return ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) && ! defined( 'IFRAME_REQUEST' ) && ! self::is_installing();
	}

	/**
	 * Check version and run the installer if necessary.
	 *
	 * @since  1.10.0
	 */
	public static function maybe_install() {
		if ( self::can_install() && self::must_install() ) {
			self::install();
		}
	}

	/**
	 * Check version and run the installer if necessary.
	 *
	 * @since  1.10.0
	 */
	private static function is_installing() {
		return 'yes' === get_transient( 'wc_mnm_installing' );
	}

	/**
	 * Check version and run the installer if necessary.
	 *
	 * @since  1.10.0
	 */
	private static function is_new_install() {
		if ( is_null( self::$mnm_term_exists ) ) {
			self::$mnm_term_exists = get_term_by( 'slug', 'mix-and-match', 'product_type' );
		}
		return ! self::$mnm_term_exists;
	}

	/**
	 * DB update needed?
	 *
	 * @since  1.10.0
	 *
	 * @return boolean
	 */
	private static function must_update() {

		$db_update_versions = array_keys( self::$db_updates );
		$db_version_target  = end( $db_update_versions );

		return version_compare( self::$current_db_version, $db_version_target, '<' );
	}

	/**
	 * DB update possible?
	 *
	 * @since  1.10.0
	 *
	 * @return boolean
	 */
	private static function can_update() {
		return ( self::$is_install_request || self::can_install() ) && current_user_can( 'manage_woocommerce' ) && version_compare( self::$current_db_version, WC_Mix_and_Match()->version, '<' );
	}

	/**
	 * Run the updater if triggered.
	 *
	 * @since  1.10.0
	 */
	public static function maybe_update() {

		if ( ! empty( $_GET[ 'force_wc_mnm_db_update' ] ) && isset( $_GET[ '_wc_mnm_admin_nonce' ] ) && wp_verify_nonce( wc_clean( $_GET[ '_wc_mnm_admin_nonce' ] ), 'wc_mnm_force_db_update_nonce' ) ) {

			if ( self::can_update() && self::must_update() ) {
				self::force_update();
			}

		} elseif ( ! empty( $_GET[ 'trigger_wc_mnm_db_update' ] ) && isset( $_GET[ '_wc_mnm_admin_nonce' ] ) && wp_verify_nonce( wc_clean( $_GET[ '_wc_mnm_admin_nonce' ] ), 'wc_mnm_trigger_db_update_nonce' ) ) {

			if ( self::can_update() && self::must_update() ) {
				self::trigger_update();
			}

		} else {

			// Queue upgrade tasks.
			if ( self::can_update() ) {

				if ( ! is_blog_installed() ) {
					return;
				}

				if ( self::must_update() && ! self::is_new_install() ) {

					if ( ! class_exists( 'WC_MNM_Admin_Notices' ) ) {
						require_once( WC_Mix_and_Match()->plugin_path() . '/includes/admin/class-wc-mnm-admin-notices.php' );
					}

					// Add 'update' notice and save early -- saving on the 'shutdown' action will fail if a chained request arrives before the 'shutdown' hook fires.
					WC_MNM_Admin_Notices::add_maintenance_notice( 'update' );
					WC_MNM_Admin_Notices::save_notices();

					if ( self::auto_update_enabled() ) {
						self::update();
					} else {
						delete_transient( 'wc_mnm_installing' );
						delete_option( 'wc_mnm_update_init' );
					}

				// Nothing found - this is a new install :)
				} else {
					self::update_db_version();
				}
			}
		}
	}

	/**
	 * If the DB version is out-of-date, a DB update must be in progress: define a 'WC_MNM_UPDATING' constant.
	 *
	 * @since  1.10.0
	 */
	public static function define_updating_constant() {
		if ( self::is_update_pending() && ! defined( 'WC_MNM_TESTING' ) ) {
			wc_maybe_define_constant( 'WC_MNM_UPDATING', true );
		}
	}


	/**
	 * Install MNM.
	 */
	public static function install() {

		if ( ! is_blog_installed() ) {
			return;
		}

		// Running for the first time? Set a transient now. Used in 'can_install' to prevent race conditions.
		set_transient( 'wc_mnm_installing', 'yes', 10 );

		// Set a flag to indicate we're installing in the current request.
		self::$is_install_request = true;

		// if bundle type does not exist, create it.
		if ( self::is_new_install() ) {
			wp_insert_term( __( 'Mix and Match', 'woocommerce-mix-and-match-products' ), 'product_type', array( 'slug' => 'mix-and-match' ) );
		}

		if ( ! class_exists( 'WC_MNM_Admin_Notices' ) ) {
			require_once( WC_Mix_and_Match()->plugin_path() . '/includes/admin/class-wc-mnm-admin-notices.php' );
		}

		// Update plugin version - once set, 'maybe_install' will not call 'install' again.
		self::update_version();
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

		$update_queued = false;

		foreach ( self::$db_updates as $version => $update_callbacks ) {

			if ( version_compare( self::$current_db_version, $version, '<' ) ) {

				$update_queued = true;
				wc_get_logger()->log( 'info', sprintf( 'Updating to version %s.', $version ), 'wc_mnm_db_updates' );

				foreach ( $update_callbacks as $update_callback ) {
					wc_get_logger()->log( 'info', sprintf( '- Queuing %s callback.', $update_callback ), 'wc_mnm_db_updates' );
					self::$background_updater->push_to_queue( $update_callback );
				}
			}
		}

		if ( $update_queued ) {
			// Define 'WC_MNM_UPDATING' constant.
			wc_maybe_define_constant( 'WC_MNM_UPDATING', true );

			// Add option to keep track of time.
			delete_option( 'wc_mnm_update_init' );
			add_option( 'wc_mnm_update_init', gmdate( 'U' ) );

			// Dispatch.
			self::$background_updater->save()->dispatch();
		}
	}

	/**
	 * Is auto-updating enabled?
	 *
	 * @since  1.10.0
	 *
	 * @return boolean
	 */
	public static function auto_update_enabled() {
		return apply_filters( 'wc_mnm_auto_update_db', false ); // @todo true
	}

	/**
	 * Trigger DB update.
	 *
	 * @since  1.10.0
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
		return self::must_update();
	}

	/**
	 * True if a DB update was started but not completed.
	 *
	 * @since  1.10.0
	 *
	 * @return boolean
	 */
	public static function is_update_incomplete() {
		return false !== get_option( 'wc_mnm_update_init', false );
	}

	/**
	 * True if a DB update is in progress.
	 *
	 * @since  1.10.0
	 *
	 * @return boolean
	 */
	public static function is_update_queued() {
		return self::$background_updater->is_update_queued();
	}

	/**
	 * True if an update process is running.
	 *
	 * @since  1.10.0
	 * 
	 * @return boolean
	 */
	public static function is_update_process_running() {
		return self::is_update_cli_process_running() || self::is_update_background_process_running();
	}

	/**
	 * True if an update background process is running.
	 *
	 * @since  1.10.0
	 *
	 * @return boolean
	 */
	public static function is_update_background_process_running() {
		return self::$background_updater->is_process_running();
	}

	/**
	 * True if a CLI update is running.
	 *
	 * @since  1.10.0
	 *
	 * @return boolean
	 */
	public static function is_update_cli_process_running() {
		return false !== get_transient( 'wc_mnm_update_cli_init', false );
	}

	/**
	 * Update DB version to current.
	 *
	 * @param  string  $version
	 */
	public static function update_db_version( $version = null ) {

		$version = is_null( $version ) ? WC_Mix_and_Match()->version : $version;

		delete_option( 'wc_mix_and_match_db_version' );
		add_option( 'wc_mix_and_match_db_version', $version );
		wc_get_logger()->log( 'info', sprintf( 'Database version is %s.', get_option( 'wc_mix_and_match_db_version', 'unknown' ) ), 'wc_mnm_db_updates' );
	}

	/*
	|--------------------------------------------------------------------------
	| Deprecated methods.
	|--------------------------------------------------------------------------
	*/


	/**
	 * Check version and run the updater if necessary.
	 *
	 * @deprecated 1.10.0
	 */
	public static function check_version() {
		wc_deprecated_function( 'WC_MNM_Install::check_version()', '1.10.0', 'Function renamed maybe_update.' );
		return self::maybe_update();
	}


	/**
	 * If the DB version is out-of-date, a DB update must be in progress.
	 *
	 * @deprecated 1.10.0
	 */
	public static function check_updating() {
		return self::can_update() && self::must_update();
	}


	/**
	 * True if an update is in progress.
	 *
	 * @deprecated 1.10.0
	 * @return bool
	 */
	public static function is_update_in_progress() {
		return self::is_update_process_running();
	}

}

WC_MNM_Install::init();
