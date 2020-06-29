<?php
/**
 * Installation related functions and actions
 *
 * Inspired in the WC_Install class.
 *
 * @package WC_Store_Credit
 * @since   2.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Store_Credit_Install.
 */
class WC_Store_Credit_Install {

	/**
	 * Database updates that need to be run per version.
	 *
	 * @var array
	 */
	private static $db_updates = array(
		'2.4.0' => array(
			'wc_store_credit_update_240_orders_to_sync_credit_used',
			'wc_store_credit_update_240_sync_credit_used_by_orders',
			'wc_store_credit_update_240_set_payment_method_to_orders',
			'wc_store_credit_update_240_clear_exhausted_coupons',
			'wc_store_credit_update_240_db_version',
		),
		'3.0.0' => array(
			'wc_store_credit_update_300_migrate_settings',
			'wc_store_credit_update_300_orders_to_update_credit_version',
			'wc_store_credit_update_300_update_orders_credit_version',
			'wc_store_credit_update_300_orders_to_update_credit_discounts',
			'wc_store_credit_update_300_update_orders_credit_discounts',
			'wc_store_credit_update_300_coupons_to_update',
			'wc_store_credit_update_300_update_coupons',
			'wc_store_credit_update_300_db_version',
		),
	);

	/**
	 * Background update class.
	 *
	 * @var object
	 */
	private static $background_updater;

	/**
	 * Init installation.
	 *
	 * @since 2.4.0
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'init_background_updater' ), 5 );
		add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
		add_action( 'init', array( __CLASS__, 'add_endpoints' ) );
		add_action( 'admin_init', array( __CLASS__, 'install_actions' ) );
		add_action( 'admin_init', array( __CLASS__, 'add_notices' ), 20 );
		add_action( 'wc_store_credit_updater_complete', array( __CLASS__, 'updated' ) );
		add_action( 'update_option_wc_store_credit_db_version', array( __CLASS__, 'add_feature_notices' ), 10, 2 );
	}

	/**
	 * Get the database updates.
	 *
	 * @since 2.4.0
	 *
	 * @return array
	 */
	public static function get_db_updates() {
		return self::$db_updates;
	}

	/**
	 * Init background updates.
	 *
	 * @since 2.4.0
	 */
	public static function init_background_updater() {
		include_once dirname( __FILE__ ) . '/class-wc-store-credit-background-updater.php';
		self::$background_updater = new WC_Store_Credit_Background_Updater();
	}

	/**
	 * Check the plugin version and run the updater is necessary.
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 *
	 * @since 2.4.0
	 */
	public static function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( get_option( 'wc_store_credit_version' ), WC_STORE_CREDIT_VERSION, '<' ) ) {
			self::install();
			do_action( 'wc_store_credit_updated' );
		}
	}

	/**
	 * Install actions when an update button is clicked within the admin area.
	 *
	 * @since 2.4.0
	 */
	public static function install_actions() {
		if ( ! empty( $_GET['do_update_wc_store_credit'] ) ) {
			check_admin_referer( 'wc_store_credit_db_update', 'wc_store_credit_db_update_nonce' );
			self::update();
		}

		if ( ! empty( $_GET['force_update_wc_store_credit'] ) ) {
			check_admin_referer( 'wc_store_credit_force_db_update', 'wc_store_credit_force_db_update_nonce' );
			$blog_id = get_current_blog_id();
			do_action( 'wp_' . $blog_id . '_wc_store_credit_updater_cron' );
			wp_safe_redirect( wc_store_credit_get_settings_url() );
			exit;
		}
	}

	/**
	 * Removes any notices added to admin.
	 *
	 * @since 2.4.2
	 */
	private static function remove_notices() {
		include_once dirname( __FILE__ ) . '/admin/class-wc-store-credit-admin-notices.php';

		// Remove not dismissed notice from a previous update.
		WC_Store_Credit_Admin_Notices::remove_notice( 'wc_store_credit_updated' );
	}

	/**
	 * Add installer/updater notices + styles if needed.
	 *
	 * @since 2.4.0
	 */
	public static function add_notices() {
		if ( WC_Store_Credit_Admin_Notices::has_notice( 'wc_store_credit_updated' ) ) {
			return;
		}

		self::update_notice();
	}

	/**
	 * Adds the update notices.
	 *
	 * @since 2.4.0
	 */
	public static function update_notice() {
		if ( self::needs_db_update() ) {
			if ( self::$background_updater->is_updating() || ! empty( $_GET['do_update_wc_store_credit'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				WC_Store_Credit_Admin_Notices::add_notice( 'updating' );
			} else {
				WC_Store_Credit_Admin_Notices::add_notice( 'update' );
			}
		}
	}

	/**
	 * Adds notices with the features of the new version of this plugin.
	 *
	 * @since 3.2.0
	 *
	 * @param string $old_version The old version.
	 * @param string $new_version The new version.
	 */
	public static function add_feature_notices( $old_version, $new_version ) {
		if ( $old_version && version_compare( $old_version, '3.2.0', '<' ) ) {
			WC_Store_Credit_Admin_Notices::add_dismiss_notice( 'wc_store_credit_sell_credit' );
		}
	}

	/**
	 * Init installation.
	 *
	 * @since 2.4.0
	 */
	public static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running the installation process.
		if ( 'yes' === get_transient( 'wc_store_credit_installing' ) ) {
			return;
		}

		// Add transient to indicate that we are running the installation process.
		set_transient( 'wc_store_credit_installing', 'yes', MINUTE_IN_SECONDS * 10 );

		self::remove_notices();
		self::add_endpoints();
		self::update_version();
		self::maybe_update_db();

		// Installation finished.
		delete_transient( 'wc_store_credit_installing' );

		flush_rewrite_rules();
	}

	/**
	 * Registers custom endpoints.
	 *
	 * @since 3.0.0
	 */
	public static function add_endpoints() {
		$mask = ( function_exists( 'WC' ) && ! is_null( WC()->query ) ? WC()->query->get_endpoints_mask() : EP_PAGES );

		add_rewrite_endpoint( 'store-credit', $mask );
	}

	/**
	 * Update the plugin version to current.
	 *
	 * @since 2.4.0
	 */
	private static function update_version() {
		update_option( 'wc_store_credit_version', WC_STORE_CREDIT_VERSION );
	}

	/**
	 * Update database version to current.
	 *
	 * @since 2.4.0
	 *
	 * @param string|null $version Optional. The new database version. Plugin version by default.
	 */
	public static function update_db_version( $version = null ) {
		update_option( 'wc_store_credit_db_version', is_null( $version ) ? WC_STORE_CREDIT_VERSION : $version );
	}

	/**
	 * Update the database if necessary.
	 *
	 * @since 2.4.0
	 */
	private static function maybe_update_db() {
		if ( ! self::needs_db_update() ) {
			self::update_db_version();
		}
	}

	/**
	 * Get if the database needs to be updated or not.
	 *
	 * @since 2.4.0
	 *
	 * @return bool
	 */
	private static function needs_db_update() {
		$needs_update = false;
		$db_version   = get_option( 'wc_store_credit_db_version', null );
		$updates      = self::get_db_updates();

		// It's the first time we store the database version.
		if ( is_null( $db_version ) ) {
			// An older version of the plugin is installed.
			$needs_update = self::exists_older_coupons();
		} elseif ( version_compare( $db_version, max( array_keys( $updates ) ), '<' ) ) {
			$needs_update = true;
		}

		return $needs_update;
	}

	/**
	 * Gets if it exists any coupon from older versions of this plugin in the database.
	 *
	 * @since 2.4.0
	 *
	 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
	 *
	 * @return bool
	 */
	public static function exists_older_coupons() {
		global $wpdb;

		$count = $wpdb->get_var(
			"SELECT COUNT(*)
			 FROM $wpdb->posts AS posts
			 LEFT JOIN $wpdb->postmeta AS meta on posts.ID = meta.post_id
			 WHERE posts.post_type = 'shop_coupon' AND
			       meta.meta_key   = 'discount_type' AND
			       meta.meta_value = 'store_credit'"
		);

		return ( !! $count );
	}

	/**
	 * Push all needed database updates to the queue for processing.
	 *
	 * @since 2.4.0
	 */
	private static function update() {
		$db_version    = get_option( 'wc_store_credit_db_version' );
		$update_queued = false;

		foreach ( self::get_db_updates() as $version => $update_callbacks ) {
			if ( version_compare( $db_version, $version, '<' ) ) {
				foreach ( $update_callbacks as $update_callback ) {
					self::$background_updater->push_to_queue( $update_callback );
					$update_queued = true;
				}
			}
		}

		if ( $update_queued ) {
			self::$background_updater->save()->dispatch();
		}
	}

	/**
	 * Database updated.
	 *
	 * @since 2.4.2
	 */
	public static function updated() {
		include_once dirname( __FILE__ ) . '/admin/wc-store-credit-admin-functions.php';
		include_once dirname( __FILE__ ) . '/admin/class-wc-store-credit-admin-notices.php';

		self::update_db_version();

		WC_Store_Credit_Admin_notices::add_notice(
			'wc_store_credit_updated',
			_x( 'WooCommerce Store Credit update complete. Thank you for updating to the latest version!', 'admin notice', 'woocommerce-store-credit' )
		);

		// Add a notice about the requirements for enabling the coupons with tax included.
		if ( 'yes' === get_option( 'wc_store_credit_inc_tax', 'no' ) && ! wc_store_credit_coupons_can_inc_tax() ) {
			WC_Store_Credit_Admin_notices::add_notice(
				'wc_store_credit_inc_tax_not_available',
				sprintf(
					/* translators: %s plugin settings URL */
					_x( '<strong>WooCommerce Store Credit</strong> &#8211; There are new requirements to enable store credit coupons with tax included. Please, review your <a href="%s">settings</a>.', 'admin notice', 'woocommerce-store-credit' ),
					esc_url( wc_store_credit_get_settings_url() )
				)
			);
		}
	}
}

WC_Store_Credit_Install::init();
