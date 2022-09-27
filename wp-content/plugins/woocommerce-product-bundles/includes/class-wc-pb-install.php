<?php
/**
 * WC_PB_Install class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles installation and updating tasks.
 *
 * @class    WC_PB_Install
 * @version  6.17.1
 */
class WC_PB_Install {

	/** @var array DB updates and callbacks that need to be run per version */
	private static $db_updates = array(
		'3.0.0' => array(
			'wc_pb_update_300',
			'wc_pb_update_300_db_version'
		),
		'5.0.0' => array(
			'wc_pb_update_500_main',
			'wc_pb_update_500_delete_unused_meta',
			'wc_pb_update_500_db_version'
		),
		'5.1.0' => array(
			'wc_pb_update_510_main',
			'wc_pb_update_510_delete_unused_meta',
			'wc_pb_update_510_db_version'
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
	private static $bundle_term_exists;

	/**
	 * Background update class.
	 * @var WC_PB_Background_Updater
	 */
	private static $background_updater;

	/**
	 * Current plugin version.
	 * @var string
	 */
	private static $current_version;

	/**
	 * Current DB version.
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
		add_action( 'init', array( __CLASS__, 'maybe_install' ) );
		add_action( 'admin_init', array( __CLASS__, 'maybe_update' ) );

		// Show row meta on the plugin screen.
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );

		// Adds support for the Bundle type - added here instead of 'WC_PB_Meta_Box_Product_Data' as it's used in REST context.
		add_filter( 'product_type_selector', array( __CLASS__, 'product_selector_filter' ) );

		// Get PB plugin and DB versions.
		self::$current_version    = get_option( 'woocommerce_product_bundles_version', null );
		self::$current_db_version = get_option( 'woocommerce_product_bundles_db_version', null );

		include_once( 'class-wc-pb-background-updater.php' );
	}

	/**
	 * Add support for the 'bundle' product type.
	 *
	 * @param  array  $options
	 * @return array
	 */
	public static function product_selector_filter( $options ) {

		$options[ 'bundle' ] = __( 'Product bundle', 'woocommerce-product-bundles' );

		return $options;
	}

	/**
	 * Init background updates.
	 */
	public static function init_background_updater() {
		self::$background_updater = new WC_PB_Background_Updater();
	}

	/**
	 * Installation needed?
	 *
	 * @since  5.5.0
	 *
	 * @return boolean
	 */
	private static function must_install() {
		return version_compare( self::$current_version, WC_PB()->plugin_version(), '<' );
	}

	/**
	 * Installation possible?
	 *
	 * @since  5.5.0
	 *
	 * @return boolean
	 */
	private static function can_install() {
		return ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) && ! defined( 'IFRAME_REQUEST' ) && ! self::is_installing();
	}

	/**
	 * Check version and run the installer if necessary.
	 *
	 * @since  5.5.0
	 */
	public static function maybe_install() {
		if ( self::must_install() && self::can_install() ) {
			self::install();
		}
	}

	/**
	 * Check version and run the installer if necessary.
	 *
	 * @since  6.2.4
	 */
	private static function is_installing() {
		return 'yes' === get_transient( 'wc_pb_installing' );
	}

	/**
	 * Check version and run the installer if necessary.
	 *
	 * @since  6.2.4
	 */
	private static function is_new_install() {
		if ( is_null( self::$bundle_term_exists ) ) {
			self::$bundle_term_exists = get_term_by( 'slug', 'bundle', 'product_type' );
		}
		return ! self::$bundle_term_exists;
	}

	/**
	 * DB update needed?
	 *
	 * @since  5.5.0
	 *
	 * @return boolean
	 */
	private static function must_update() {

		if ( self::is_new_install() ) {
			return false;
		}

		$db_update_versions = array_keys( self::$db_updates );
		$db_version_target  = end( $db_update_versions );

		if ( is_null( self::$current_db_version ) ) {
			/*
			 * Back in the old days, PB didn't store its DB version at all. When updating from an ancient version like that, the DB version will be null but a DB upgrade will be needed.
			 * A DB upgrade will be needed if bundles exist in the posts table, but no items exist in the bundled items table.
			 */
			return 0 === WC_PB_DB::query_bundled_items( array( 'return' => 'count' ) ) && ! empty( wc_get_products( array( 'type' => 'bundle', 'return' => 'ids', 'limit' => 1 ) ) );
		} else {
			return version_compare( self::$current_db_version, $db_version_target, '<' );
		}
	}

	/**
	 * DB update possible?
	 *
	 * @since  5.5.0
	 *
	 * @return boolean
	 */
	private static function can_update() {
		return ( self::$is_install_request || self::can_install() ) && current_user_can( 'manage_woocommerce' ) && version_compare( self::$current_db_version, WC_PB()->plugin_version( true ), '<' );
	}

	/**
	 * Run the updater if triggered.
	 *
	 * @since  5.5.0
	 */
	public static function maybe_update() {

		if ( ! empty( $_GET[ 'force_wc_pb_db_update' ] ) && isset( $_GET[ '_wc_pb_admin_nonce' ] ) && wp_verify_nonce( wc_clean( $_GET[ '_wc_pb_admin_nonce' ] ), 'wc_pb_force_db_update_nonce' ) ) {

			if ( self::can_update() && self::must_update() ) {
				self::force_update();
			}

		} elseif ( ! empty( $_GET[ 'trigger_wc_pb_db_update' ] ) && isset( $_GET[ '_wc_pb_admin_nonce' ] ) && wp_verify_nonce( wc_clean( $_GET[ '_wc_pb_admin_nonce' ] ), 'wc_pb_trigger_db_update_nonce' ) ) {

			if ( self::can_update() && self::must_update() ) {
				self::trigger_update();
			}

		} else {

			// Queue upgrade tasks.
			if ( self::can_update() ) {

				if ( ! is_blog_installed() ) {
					return;
				}

				if ( self::must_update() ) {

					if ( ! class_exists( 'WC_PB_Admin_Notices' ) ) {
						require_once( WC_PB_ABSPATH . 'includes/admin/class-wc-pb-admin-notices.php' );
					}

					// Add 'update' notice and save early -- saving on the 'shutdown' action will fail if a chained request arrives before the 'shutdown' hook fires.
					WC_PB_Admin_Notices::add_maintenance_notice( 'update' );
					WC_PB_Admin_Notices::save_notices();

					if ( self::auto_update_enabled() ) {
						self::update();
					} else {
						delete_transient( 'wc_pb_installing' );
						delete_option( 'wc_pb_update_init' );
					}

				// Nothing found - this is a new install :)
				} else {
					self::update_db_version();
				}
			}
		}
	}

	/**
	 * If the DB version is out-of-date, a DB update must be in progress: define a 'WC_PB_UPDATING' constant.
	 *
	 * @since  5.5.0
	 */
	public static function define_updating_constant() {
		if ( self::is_update_pending() && ! defined( 'WC_PB_TESTING' ) ) {
			wc_maybe_define_constant( 'WC_PB_UPDATING', true );
		}
	}

	/**
	 * Install PB.
	 */
	public static function install() {

		if ( ! is_blog_installed() ) {
			return;
		}

		// Running for the first time? Set a transient now. Used in 'can_install' to prevent race conditions.
		set_transient( 'wc_pb_installing', 'yes', 10 );

		// Set a flag to indicate we're installing in the current request.
		self::$is_install_request = true;

		// Create tables.
		self::maybe_prepare_db_for_upgrade();
		self::create_tables();

		// Create events.
		self::create_events();

		// if bundle type does not exist, create it.
		if ( self::is_new_install() ) {
			wp_insert_term( 'bundle', 'product_type' );
		}

		if ( ! class_exists( 'WC_PB_Admin_Notices' ) ) {
			require_once( WC_PB_ABSPATH . 'includes/admin/class-wc-pb-admin-notices.php' );
		}

		if ( is_null( self::$current_version ) ) {
			// Add dismissible welcome notice.
			WC_PB_Admin_Notices::add_maintenance_notice( 'welcome' );
		}

		// Run a loopback test after every update. Will only run once if successful.
		WC_PB_Admin_Notices::add_maintenance_notice( 'loopback' );

		// Add feature plugin recommendations in the Inbox: These are only added once.
		WC_PB_Admin_Notices::add_note( 'bulk-discounts' );

		// Update plugin version - once set, 'maybe_install' will not call 'install' again.
		self::update_version();
	}

	/**
	 * Set up the database tables which the plugin needs to function.
	 *
	 * Tables:
	 *     woocommerce_bundled_items - Each bundled item id is associated with a "contained" product id (the bundled product), and a "container" bundle id (the product bundle).
	 *     woocommerce_bundled_itemmeta - Bundled item meta for storing extra data.
	 */
	private static function create_tables() {
		global $wpdb;
		$wpdb->hide_errors();
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( self::get_schema() );
	}

	/**
	 * Schedule cron events.
	 *
	 * @since 6.16.0
	 */
	public static function create_events() {
		if ( ! wp_next_scheduled( 'wc_pb_daily' ) ) {
			wp_schedule_event( time() + 10, 'daily', 'wc_pb_daily' );
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

		$max_index_length = 191;

		$tables = "
		CREATE TABLE {$wpdb->prefix}woocommerce_bundled_items (
			bundled_item_id BIGINT UNSIGNED NOT NULL auto_increment,
			product_id BIGINT UNSIGNED NOT NULL,
			bundle_id BIGINT UNSIGNED NOT NULL,
			menu_order BIGINT UNSIGNED NOT NULL,
			PRIMARY KEY  (bundled_item_id),
			KEY product_id (product_id),
			KEY bundle_id (bundle_id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}woocommerce_bundled_itemmeta (
			meta_id BIGINT UNSIGNED NOT NULL auto_increment,
			bundled_item_id BIGINT UNSIGNED NOT NULL,
			meta_key varchar(255) default NULL,
			meta_value longtext NULL,
			PRIMARY KEY  (meta_id),
			KEY bundled_item_id (bundled_item_id),
			KEY meta_key (meta_key($max_index_length))
		) $collate;
		CREATE TABLE {$wpdb->prefix}wc_order_bundle_lookup (
			order_item_id BIGINT UNSIGNED NOT NULL,
			parent_order_item_id BIGINT UNSIGNED NOT NULL,
			order_id BIGINT UNSIGNED NOT NULL,
			bundle_id BIGINT UNSIGNED NOT NULL,
			product_id BIGINT UNSIGNED NOT NULL,
			variation_id BIGINT UNSIGNED NOT NULL,
			customer_id BIGINT UNSIGNED NULL,
			date_created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			product_qty INT NOT NULL,
			product_net_revenue double DEFAULT 0 NOT NULL,
			product_gross_revenue double DEFAULT 0 NOT NULL,
			coupon_amount double DEFAULT 0 NOT NULL,
			tax_amount double DEFAULT 0 NOT NULL,
			PRIMARY KEY  (order_item_id),
			KEY order_id (order_id),
			KEY parent_order_item_id (parent_order_item_id),
			KEY bundle_id (bundle_id),
			KEY product_id (product_id),
			KEY customer_id (customer_id),
			KEY date_created (date_created)
		) $collate;
		";

		return $tables;
	}

	/**
	 * Update WC PB version to current.
	 */
	private static function update_version() {
		delete_option( 'woocommerce_product_bundles_version' );
		add_option( 'woocommerce_product_bundles_version', WC_PB()->plugin_version() );
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
				WC_PB_Core_Compatibility::log( sprintf( 'Updating to version %s.', $version ), 'info', 'wc_pb_db_updates' );

				foreach ( $update_callbacks as $update_callback ) {
					WC_PB_Core_Compatibility::log( sprintf( '- Queuing %s callback.', $update_callback ), 'info', 'wc_pb_db_updates' );
					self::$background_updater->push_to_queue( $update_callback );
				}
			}
		}

		if ( $update_queued ) {

			// Define 'WC_PB_UPDATING' constant.
			wc_maybe_define_constant( 'WC_PB_UPDATING', true );

			// Keep track of time.
			delete_option( 'wc_pb_update_init' );
			add_option( 'wc_pb_update_init', gmdate( 'U' ) );

			// Dispatch.
			self::$background_updater->save()->dispatch();
		}
	}

	/**
	 * Is auto-updating enabled?
	 *
	 * @since  5.5.0
	 *
	 * @return boolean
	 */
	public static function auto_update_enabled() {
		return apply_filters( 'woocommerce_bundles_auto_update_db', true );
	}

	/**
	 * Trigger DB update.
	 *
	 * @since  5.5.0
	 */
	public static function trigger_update() {
		self::update();
		wp_safe_redirect( admin_url() );
		exit;
	}

	/**
	 * Maybe prepare DB for upcoming upgrade.
	 *
	 * @since 6.11.1
	 * @return void
	 */
	protected static function maybe_prepare_db_for_upgrade() {

		// Fix db index for 6.10.0 till 6.11.0.
		if ( version_compare( self::$current_version, '6.10.0' ) > -1 && version_compare( '6.11.0', self::$current_version ) > -1 ) {
			global $wpdb;
			$wpdb->query( "ALTER TABLE `{$wpdb->prefix}wc_order_bundle_lookup` DROP KEY `bundle_id`" );
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
		exit;
	}

	/**
	 * Updates plugin DB version when all updates have been processed.
	 */
	public static function update_complete() {

		WC_PB_Core_Compatibility::log( 'Data update complete.', 'info', 'wc_pb_db_updates' );
		self::update_db_version();
		delete_option( 'wc_pb_update_init' );
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
	 * @since  5.5.0
	 *
	 * @return boolean
	 */
	public static function is_update_incomplete() {
		return false !== get_option( 'wc_pb_update_init', false );
	}


	/**
	 * True if a DB update is in progress.
	 *
	 * @since  5.5.0
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
	 * @since  5.5.0
	 *
	 * @return boolean
	 */
	public static function is_update_background_process_running() {
		return self::$background_updater->is_process_running();
	}

	/**
	 * True if a CLI update is running.
	 *
	 * @since  5.5.0
	 *
	 * @return boolean
	 */
	public static function is_update_cli_process_running() {
		return false !== get_transient( 'wc_pb_update_cli_init', false );
	}

	/**
	 * Update DB version to current.
	 *
	 * @param  string  $version
	 */
	public static function update_db_version( $version = null ) {

		$version = is_null( $version ) ? WC_PB()->plugin_version() : $version;

		// Remove suffixes.
		$version = WC_PB()->plugin_version( true, $version );

		delete_option( 'woocommerce_product_bundles_db_version' );
		add_option( 'woocommerce_product_bundles_db_version', $version );

		WC_PB_Core_Compatibility::log( sprintf( 'Database version is %s.', get_option( 'woocommerce_product_bundles_db_version', 'unknown' ) ), 'info', 'wc_pb_db_updates' );
	}

	/**
	 * Get list of DB update callbacks.
	 *
	 * @since  5.5.0
	 *
	 * @return array
	 */
	public static function get_db_update_callbacks() {
		return self::$db_updates;
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param	mixed  $links
	 * @param	mixed  $file
	 * @return	array
	 */
	public static function plugin_row_meta( $links, $file ) {

		if ( $file == WC_PB()->plugin_basename() ) {
			$row_meta = array(
				'docs'    => '<a href="' . WC_PB()->get_resource_url( 'docs-contents' ) . '">' . __( 'Documentation', 'woocommerce-product-bundles' ) . '</a>',
				'support' => '<a href="' . WC_PB()->get_resource_url( 'ticket-form' ) . '">' . __( 'Support', 'woocommerce-product-bundles' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}

		return $links;
	}
}

WC_PB_Install::init();
