<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Installation/Migration Class.
 *
 * Handles the activation/installation of the plugin.
 *
 * @package  Installation
 * @version  6.1.3
 */
class WC_Product_Addons_Install {
	/**
	 * Initialize hooks.
	 *
	 * @since 3.0.0
	 * @return bool
	 */
	public static function init() {
		self::run();
	}

	/**
	 * Run the installation.
	 *
	 * @since 3.0.0
	 * @return bool
	 */
	private static function run() {
		$installed_version = get_option( 'wc_pao_version' );

		self::migration_3_0_product();

		// Check the version before running.
		if ( ! defined( 'IFRAME_REQUEST' ) && ( $installed_version !== WC_PRODUCT_ADDONS_VERSION ) ) {
			if ( ! defined( 'WC_PAO_INSTALLING' ) ) {
				define( 'WC_PAO_INSTALLING', true );
			}

			self::update_plugin_version();
			self::create_events();

			if ( version_compare( $installed_version, '3.0', '<' ) ) {
				self::migration_3_0();
			}

			do_action( 'wc_pao_updated' );
		}
	}

	/**
	 * Updates the plugin version in db.
	 *
	 * @since 3.0.0
	 * @return bool
	 */
	private static function update_plugin_version() {
		delete_option( 'wc_pao_version' );
		add_option( 'wc_pao_version', WC_PRODUCT_ADDONS_VERSION );
	}

	/**
	 * 3.0 migration script.
	 *
	 * @since 3.0.0
	 */
	private static function migration_3_0() {
		require_once( WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/updates/class-wc-product-addons-migration-3-0.php' );
	}

	/**
	 * 3.0 migration script for product level.
	 *
	 * @since 3.0.0
	 */
	private static function migration_3_0_product() {
		require_once( WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/updates/class-wc-product-addons-migration-3-0-product.php' );
	}

	/**
	 * Schedule cron events.
	 *
	 * @since 6.1.3
	 */
	private static function create_events() {
		if ( ! wp_next_scheduled( 'wc_pao_daily' ) ) {
			wp_schedule_event( time() + 10, 'daily', 'wc_pao_daily' );
		}

		if ( ! wp_next_scheduled( 'wc_pao_hourly' ) ) {
			wp_schedule_event( time() + 10, 'hourly', 'wc_pao_hourly' );
		}
	}
}

WC_Product_Addons_Install::init();
