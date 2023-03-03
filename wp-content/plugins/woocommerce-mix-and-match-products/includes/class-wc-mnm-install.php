<?php
/**
 * Install
 *
 * @package  WooCommerce Mix and Match Products/Install
 * @since    1.2.0
 * @version  2.4.0
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
		),
		'2.0.0' => array(
			'wc_mnm_update_2x00_remove_notices',
			'wc_mnm_update_2x00_customizer_settings',
			'wc_mnm_update_2x00_custom_tables',
			'wc_mnm_update_2x00_order_item_meta',
			'wc_mnm_update_2x00_category_contents_meta',
			'wc_mnm_update_2x00_product_meta',
		),
		'2.2.0' => array( 
			'wc_mnm_update_2x2x0_delete_duplicate_meta',
		),
	);

	/**
	 * Term runtime cache.
	 * @var boolean
	 */
	private static $mnm_term_exists;

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

		// Adds support for the 'mix-and-match' product type - added here instead of 'WC_Mix_and_Match_Admin_Meta_Boxes' as it's used by front-end and REST.
		add_filter( 'product_type_selector', array( __CLASS__, 'product_selector_filter' ) );

		// Get plugin and plugin DB versions.
		self::$current_version    = get_option( 'wc_mix_and_match_version' );
		self::$current_db_version = get_option( 'wc_mix_and_match_db_version' );

		// Install and update hooks.
		add_action( 'init', array( __CLASS__, 'define_updating_constant' ) ); // @todo - need this?
		add_action( 'init', array( __CLASS__, 'check_version' ) );
		add_action( 'admin_init', array( __CLASS__, 'admin_db_update_notice' ) );

		// Action scheduler hook.
		add_action( 'wc_mnm_run_update_callback', array( __CLASS__, 'run_update_callback' ) );
		add_action( 'wc_mnm_update_db_to_current_version', array( __CLASS__, 'update_db_version' ) );

		// Handle any actions from notices.
		add_action( 'admin_init', array( __CLASS__, 'install_actions' ) );

	}

	/**
	 * Adds support for the 'mix and match' product type.
	 *
	 * @param  array    $options
	 * @return array
	 * @since  1.8.0
	 */
	public static function product_selector_filter( $options ) {
		$options['mix-and-match'] = __( 'Mix and Match product', 'woocommerce-mix-and-match-products' );
		return $options;
	}

	/**
	 * If the DB version is out-of-date, a DB update must be in progress: define a 'WC_MNM_NEEDS_DB_UPDATE' constant.
	 *
	 * @since  1.10.0
	 */
	public static function define_updating_constant() {
		if ( self::needs_db_update() && ! defined( 'WC_MNM_TESTING' ) ) {
			wc_maybe_define_constant( 'WC_MNM_NEEDS_DB_UPDATE', true );
		}
	}

	/**
	 * Check version and run the installer if necessary.
	 */
	public static function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( self::$current_version, WC_Mix_and_Match()->version, '<' ) ) {
			self::install();
		}
	}

	/**
	 * Test if we are using WC Admin Notes or classic notices.
	 *
	 * @since  2.4.0
	 */
	private static function is_wc_admin_active() {
		return WC()->is_wc_admin_active() && false !== get_option( 'woocommerce_admin_install_timestamp' );
	}

	/**
	 * See if we need to show or run database updates during install.
	 *
	 * @since  2.0.0
	 */
	public static function admin_db_update_notice() {

		// Add WC Admin based db update notice.
		if ( self::is_wc_admin_active() ) {

			self::remove_admin_notices();
			
			new WC_MNM_Notes_Run_Db_Update();

			// If WC Admin is disabled show the old style notices.
		} else if (
			self::needs_db_update() && 
			! self::is_new_install() )
			{

				if ( self::auto_update_enabled() ) {
					self::update();
				} else {

					// Add 'update' notice.
					WC_MNM_Admin_Notices::add_notice( 'update', true );

				}

		}
	}

	/*
	|--------------------------------------------------------------------------
	| Handle action scheduler.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Run an update callback when triggered by ActionScheduler.
	 *
	 * @param string $update_callback Callback name.
	 *
	 * @since 2.0.0
	 */
	public static function run_update_callback( $update_callback ) {
		include_once dirname( __FILE__ ) . '/wc-mnm-update-functions.php';

		if ( is_callable( $update_callback ) ) {
			self::run_update_callback_start( $update_callback );
			$result = (bool) call_user_func( $update_callback );
			self::run_update_callback_end( $update_callback, $result );
		}
	}

	/**
	 * Triggered when a callback will run.
	 *
	 * @since 2.0.0
	 */
	protected static function run_update_callback_start() {
		wc_maybe_define_constant( 'WC_MNM_UPDATING', true );
		wc_maybe_define_constant( 'WC_UPDATING', true );
	}

	/**
	 * Triggered when a callback has ran.
	 *
	 * @since 2.0.0
	 * @param string $callback Callback name.
	 * @param bool   $result Return value from callback. Non-false need to run again.
	 */
	protected static function run_update_callback_end( $callback, $result ) {
		if ( $result ) {
			WC()->queue()->add(
				'wc_mnm_run_update_callback',
				array(
					'update_callback' => $callback,
				),
				'wc_mnm_db_updates'
			);
		}
	}

	/**
	 * Install actions when a update button is clicked within the admin area.
	 *
	 * This function is hooked into admin_init to affect admin only.
	 *
	 * @since  2.0.0
	 */
	public static function install_actions() {
		if ( ! empty( $_GET[ 'wc_mnm_update_action' ] ) ) {

			$result = check_admin_referer( 'wc_mnm_update_action', 'wc_mnm_update_action_nonce' );

			$action = wc_clean( $_GET[ 'wc_mnm_update_action' ] );

			if ( is_callable( array( __CLASS__, $action ) ) ) {
				call_user_func( array( __CLASS__, $action ) );
			} else {
				do_action( 'wc_mnm_update_action_' . $action );
			}

			if ( ! self::is_wc_admin_active() ) {
				WC_MNM_Admin_Notices::add_notice( 'update', true );
			}
		}
	}


	/**
	 * Install MNM.
	 */
	public static function install() {

		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'wc_mnm_installing' ) ) {
			return;
		}

		// Running for the first time? Set a transient now.
		set_transient( 'wc_mnm_installing', 'yes', MINUTE_IN_SECONDS * 10 );
		wc_maybe_define_constant( 'WC_MNM_INSTALLING', true );

		self::remove_admin_notices();

		// Create tables.
		self::create_tables();

		// If mix and match type does not exist, create it.
		if ( self::is_new_install() ) {
			wp_insert_term( __( 'Mix and Match', 'woocommerce-mix-and-match-products' ), 'product_type', array( 'slug' => 'mix-and-match' ) );
		}

		// Update plugin version - once set, will not call 'install' again.
		self::update_version();
		self::maybe_update_db_version();

		delete_transient( 'wc_mnm_installing' );
		do_action( 'woocommerce_flush_rewrite_rules' );

	}


	/**
	 * Add new tables for 2.0.
	 *
	 * @since 2.0.0
	 */
	private static function create_tables() {
		global $wpdb;
		$wpdb->hide_errors();

		// Load the file with the required upgrade functions
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$result = dbDelta( self::get_schema() );

		if ( ! empty( $result ) )  {
			wc_get_logger()->log( 'info', 'Mix and Match database table installed.', array( 'source' => 'wc_mnm_db_updates' ) );
		} else {
			wc_get_logger()->log( 'error', 'Mix and Match database table failed to install.', array( 'source' => 'wc_mnm_db_updates' ) );
		}

		// Need to check if the foreign key already exists.
		// @link https://github.com/kathyisawesome/woocommerce-mix-and-match-products/issues/426
		$fk_result = $wpdb->get_row( "SHOW CREATE TABLE {$wpdb->prefix}wc_mnm_child_items" ); // WPCS: unprepared SQL ok.

		// Remove 2.0 foreign keys, so we can update them with prefixed keys.
		if ( false !== strpos( $fk_result->{'Create Table'}, "FK_CHILD_ID" ) ) {
			$wpdb->query(
                "ALTER TABLE {$wpdb->prefix}wc_mnm_child_items
				DROP CONSTRAINT `FK_CHILD_ID`
				,
				DROP CONSTRAINT `FK_PRODUCT_ID`
			" 
            );
		}

		// Add 2.0.7 foreign keys using prefixes.
		if ( false === strpos( $fk_result->{'Create Table'}, "fk_{$wpdb->prefix}wc_mnm_child_items_container_id" ) ) {

			// Add foreign key constraints to the custom table, to ensure data integrity and
			// delete child rows when the parents are deleted
			//
			// Note: we can't add the foreign keys in the dbDelta() call, because it doesn't
			// support them.
			// @link https://developer.wordpress.org/reference/functions/dbdelta/#comment-4027
			$wpdb->query(
                "ALTER TABLE {$wpdb->prefix}wc_mnm_child_items
				-- Foreign key to parent container products. Ensures that the container_id matches
				-- a valid product ID (post ID) and deletes the row if the parent is deleted
				ADD CONSTRAINT `fk_{$wpdb->prefix}wc_mnm_child_items_container_id`
					FOREIGN KEY (container_id)
					REFERENCES {$wpdb->prefix}posts(ID)
					ON DELETE CASCADE
				,
				-- Foreign key to child products. Ensures that the product_id matches
				-- a valid product ID (post ID) and deletes the row if the product is deleted
				ADD CONSTRAINT fk_{$wpdb->prefix}wc_mnm_child_items_product_id
					FOREIGN KEY (product_id)
					REFERENCES {$wpdb->prefix}posts(ID)
					ON DELETE CASCADE
			" 
            );
		}

	}

	/**
	 * Get table schema.
	 *
	 * @return string
	 */
	private static function get_schema() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		/* Add a parent/child relationship table to keep track of the child items
		 and the products in which they have been included. */
		$tables = "
		CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wc_mnm_child_items (
			child_item_id BIGINT UNSIGNED NOT NULL auto_increment,
			product_id BIGINT UNSIGNED NOT NULL,
			container_id BIGINT UNSIGNED NOT NULL,
			menu_order BIGINT UNSIGNED NOT NULL,
			PRIMARY KEY  (child_item_id),
			KEY product_id (product_id),
			KEY container_id (container_id)
			) $collate;
		";
		return $tables;
	}

	/**
	 * Reset any notices added to admin.
	 *
	 * @since 2.0.0
	 */
	private static function remove_admin_notices() {
		include_once WC_Mix_and_Match()->plugin_path() . '/includes/admin/class-wc-mnm-admin-notices.php';
		WC_MNM_Admin_Notices::remove_all_notices();
	}

	/**
	 * Check version and run the installer if necessary.
	 *
	 * @since  1.10.0
	 */
	public static function is_new_install() {

		if ( is_null( self::$mnm_term_exists ) ) {
			self::$mnm_term_exists = get_term_by( 'slug', 'mix-and-match', 'product_type' );
		}

		return ! self::$mnm_term_exists;
	}

	/**
	 * DB update needed?
	 *
	 * @since  2.0.0
	 *
	 * @return boolean
	 */
	public static function needs_db_update() {
		return self::$current_db_version && version_compare( self::$current_db_version, self::get_latest_update_version(), '<' );
	}

	/**
	 * Get the most recent updated version.
	 *
	 * @since  2.0.0
	 *
	 * @return string
	 */
	public static function get_latest_update_version() {
		$updates            = self::get_db_update_callbacks();
		$update_versions    = array_keys( $updates );
		usort( $update_versions, 'version_compare' );
		return end( $update_versions );
	}

	/**
	 * See if we need to show or run database updates during install.
	 *
	 * @since 2.0.0
	 */
	private static function maybe_update_db_version() {
		if ( self::needs_db_update() ) {
			if ( self::auto_update_enabled() ) {
				self::update();
			} else {
				WC_MNM_Admin_Notices::add_notice( 'update', true );
			}
		} else {
			self::update_db_version();
		}
	}

	/**
	 * Update WC MnM version to current.
	 */
	private static function update_version() {
		update_option( 'wc_mix_and_match_version', WC_Mix_and_Match()->version );
	}

	/**
	 * Get list of DB update callbacks.
	 *
	 * @since  2.0.0
	 * @return array
	 */
	public static function get_db_update_callbacks() {
		return self::$db_updates;
	}

	/**
	 * Is auto-updating enabled?
	 *
	 * @since  1.10.0
	 *
	 * @return boolean
	 */
	public static function auto_update_enabled() {
		return apply_filters( 'wc_mnm_auto_update_db', false );
	}

	/**
	 * Push all needed DB updates to the queue for processing.
	 */
	private static function update() {
		$loop = 0;

		foreach (self::get_db_update_callbacks() as $version => $update_callbacks ) {

			if ( version_compare( self::$current_db_version, $version, '<' ) ) {
				foreach ( $update_callbacks as $update_callback ) {
					WC()->queue()->schedule_single(
						time() + $loop,
						'wc_mnm_run_update_callback',
						array(
							'update_callback' => $update_callback,
						),
						'wc_mnm_db_updates'
					);
					$loop++;
				}
			}
		}

		// After the callbacks finish, update the db version to the current WC version.
		if ( version_compare( self::$current_db_version, self::$current_version, '<' ) &&
			! WC()->queue()->get_next( 'wc_mnm_update_db_to_current_version' ) ) {
			WC()->queue()->schedule_single(
				time() + $loop,
				'wc_mnm_update_db_to_current_version',
				array(
					'version' => self::$current_version,
				),
				'wc_mnm_db_updates'
			);
		}

	}

	/**
	 * Update DB version to current.
	 *
	 * @param  string  $version
	 */
	public static function update_db_version( $version = null ) {
		$version = is_null( $version ) ? WC_Mix_and_Match()->version: $version;
		update_option( 'wc_mix_and_match_db_version', $version );
	}


	/*
	|--------------------------------------------------------------------------
	| Notice action callbacks.
	|--------------------------------------------------------------------------
	*/


	/**
	 * Run the updater if triggered.
	 *
	 * @since  2.0.0
	 */
	public static function do_update_db() {
		self::update();
	}

	/**
	 * Delete the old mnm child meta data.
	 *
	 * @since  2.0.0
	 */
	public static function do_2x00_cleanup_legacy_child_meta() {

		$result = WC()->queue()->schedule_single(
			time(),
			'wc_mnm_run_update_callback',
			array(
				'update_callback' => 'wc_mnm_update_2x00_cleanup_legacy_child_meta',
			),
			'wc_mnm_db_updates'
		);

		// If scheduled, we can delete the transient.
		if ( $result ) {
			delete_transient( 'wc_mnm_show_2x00_cleanup_legacy_child_meta' );
		}

	}

	/*
	|--------------------------------------------------------------------------
	| Deprecated methods.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Init background updates.
	 *
	 * @deprecated 2.0.0
	 */
	public static function init_background_updater() {
		wc_deprecated_function( 'WC_MNM_Install::init_background_updater()', '2.0.0', 'WC_MNM_Background_Updater is replaced by WooCommerce Action scheduler. There is no direct replacement.' );
		return false;
	}

	/**
	 * True if a DB update was started but not completed.
	 *
	 * @since  1.10.0
	 * @deprecated 2.0.0
	 *
	 * @return boolean
	 */
	public static function is_update_incomplete() {
		wc_deprecated_function( 'WC_MNM_Install::is_update_incomplete()', '2.0.0', 'WC_MNM_Background_Updater is replaced by WooCommerce Action scheduler.' );
		return false !== get_option( 'wc_mnm_update_init', false );
	}

	/**
	 * True if an update process is running.
	 *
	 * @since  1.10.0
	 * @deprecated 2.0.0
	 *
	 * @return boolean
	 */
	public static function is_update_process_running() {
		$next_scheduled_date = WC()->queue()->get_next( 'wc_mnm_run_update_callback', null, 'wc_mnm_db_updates' );
		wc_deprecated_function( 'WC_MNM_Install::init_background_updater()', '2.0.0', 'WC_MNM_Background_Updater is replaced by WooCommerce Action scheduler.' );
		return $next_scheduled_date || ! empty( $_GET['do_update_wc_mnm'] );
	}

	/**
	 * True if an update background process is running.
	 *
	 * @since  1.10.0
	 * @deprecated 2.0.0
	 *
	 * @return boolean
	 */
	public static function is_update_background_process_running() {
		wc_deprecated_function( 'WC_MNM_Install::init_background_updater()', '2.0.0', 'WC_MNM_Background_Updater is replaced by WooCommerce Action scheduler, so there is never a background process running. There is no direct replacement.' );
		return false;
	}

	/**
	 * True if a CLI update is running.
	 *
	 * @since  1.10.0
	 * @deprecated 2.0.0
	 *
	 * @return boolean
	 */
	public static function is_update_cli_process_running() {
		wc_deprecated_function( 'WC_MNM_Install::is_update_cli_process_running()', '2.0.0', 'No need to know if CLI is running as updates are handled by the Action Scheduler. There is no direct replacement.' );
		return false !== get_transient( 'wc_mnm_update_cli_init' );
	}

	/**
	 * Force re-start the update cron if everything else fails.
	 *
	 * @deprecated 2.0.0
	 */
	public static function force_update() {
		wc_deprecated_function( 'WC_MNM_Install::force_update()', '2.0.0', 'Updates are handled in the Action Scheduler and so run when the queue is triggered. There is no direct replacement.' );
		return self::update();
	}

	/**
	 * Check version and run the installer if necessary.
	 *
	 * @since  1.10.0
	 * @deprecated 2.0.0
	 */
	public static function maybe_install() {
		wc_deprecated_function( 'WC_MNM_Install::maybe_install()', '2.0.0', 'Method renamed check_version().' );
		return self::check_version();
	}

	/**
	 * See if we need to show or run database updates during install.
	 *
	 * @deprecated 2.0.0
	 */
	public static function maybe_update() {
		wc_deprecated_function( 'WC_MNM_Install::maybe_update()', '2.0.0', 'Method renamed admin_db_update_notice().' );
		return self::admin_db_update_notice();
	}

	/**
	 * Trigger DB update.
	 *
	 * @deprecated 2.0.0
	 */
	public static function trigger_update() {
		wc_deprecated_function( 'WC_MNM_Install::trigger_update()', '2.0.0', 'Use update() instead.' );
		return self::update();
	}

	/**
	 * If the DB version is out-of-date, a DB update must be in progress.
	 *
	 * @deprecated 1.10.0
	 */
	public static function check_updating() {
		wc_deprecated_function( 'WC_MNM_Install::check_updating()', '1.10.0', 'Method renamed maybe_update_db_version().' );
		return ! defined( 'IFRAME_REQUEST' ) && current_user_can( 'manage_woocommerce' ) && self::needs_db_update();
	}

	/**
	 * True if an update is in progress.
	 *
	 * @return bool
	 */
	public static function is_update_pending() {
		wc_deprecated_function( 'WC_MNM_Install::check_updating()', '1.10.0', 'Method renamed needs_db_update().' );
		return self::needs_db_update();
	}

	/**
	 * True if an update is in progress.
	 *
	 * @deprecated 1.10.0
	 * @return bool
	 */
	public static function is_update_in_progress() {
		wc_deprecated_function( 'WC_MNM_Install::is_update_in_progress()', '1.10.0', 'Method renamed maybe_update_db_version().' );
		return WC()->queue()->get_next( 'wc_mnm_run_update_callback' );
	}

}

WC_MNM_Install::init();
