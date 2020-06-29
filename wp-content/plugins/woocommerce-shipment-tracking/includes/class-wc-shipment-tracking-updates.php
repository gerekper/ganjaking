<?php
/**
 * Class for version updates.
 *
 * @package WC_Shipment_Tracking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Shipment Tracking Updates.
 *
 * This class performs the update of a given version. If a particular version
 * needs update routine (e.g. DB migration) then the updater should be defined
 * in `self::get_updaters()`.
 *
 * @since 1.6.5
 * @version 1.6.5
 */
class WC_Shipment_Tracking_Updates {
	/**
	 * List of updaters from version-to-version.
	 *
	 * @since 1.6.5
	 * @version 1.6.5
	 *
	 * @see self::get_updaters()
	 *
	 * @var array
	 */
	protected static $updaters;

	/**
	 * Get list of version updaters.
	 *
	 * @since 1.6.5
	 * @version 1.6.5
	 *
	 * @return array Array of updaters where key is the version and value is
	 *               updater array.
	 */
	protected static function get_updaters() {
		if ( ! empty( self::$updaters ) ) {
			return self::$updaters;
		}

		self::$updaters = array(
			'1.6.5' => array(
				'path'  => self::get_updater_base_dir() . 'class-wc-shipment-tracking-updater-1.6.5.php',
				'class' => 'WC_Shipment_Tracking_Updater_1_6_5',
			),
		);

		return self::$updaters;
	}

	/**
	 * Check for update based on current plugin's version versus installed
	 * version. Perform update routine if version mismatches.
	 *
	 * Hooked into `init` so that it's checked on every request.
	 *
	 * @since 1.6.5
	 * @version 1.6.5
	 */
	public static function check_updates() {
		$installed_version = get_option( 'woocommerce_shipment_tracking_version' );
		if ( WC_SHIPMENT_TRACKING_VERSION !== $installed_version ) {
			self::update_version();
			self::maybe_perform_update( $installed_version );
		}
	}

	/**
	 * Update version that's stored in DB to the latest version.
	 *
	 * @since 1.6.5
	 * @version 1.6.5
	 */
	protected static function update_version() {
		delete_option( 'woocommerce_shipment_tracking_version' );
		add_option( 'woocommerce_shipment_tracking_version', WC_SHIPMENT_TRACKING_VERSION );
	}

	/**
	 * Maybe perform update if there's an udpate routine for a given version.
	 *
	 * @since 1.6.5
	 * @version 1.6.5
	 *
	 * @param string $installed_version Installed version found in DB.
	 */
	protected static function maybe_perform_update( $installed_version ) {
		require_once( self::get_updater_base_dir() . 'abstract-wc-shipment-tracking-updater.php' );

		foreach ( self::get_updaters() as $version => $updater ) {
			if ( version_compare( $installed_version, $version, '>=' ) ) {
				continue;
			}

			self::maybe_updater_runs_update( $updater );
		}
	}

	/**
	 * Maybe the updater will run `updates` routine.
	 *
	 * @since 1.6.5
	 * @version 1.6.5
	 *
	 * @param array $updater Updater array.
	 */
	protected static function maybe_updater_runs_update( array $updater ) {
		require_once( $updater['path'] );

		$updater_instance = new $updater['class']();
		if ( ! is_a( $updater_instance, 'WC_Shipment_Tracking_Updater' ) ) {
			return;
		}

		return $updater_instance->update();
	}

	/**
	 * Get updater base dir.
	 *
	 * @since 1.6.5
	 * @version 1.6.5
	 *
	 * @return string Updater base dir.
	 */
	protected static function get_updater_base_dir() {
		return wc_shipment_tracking()->plugin_dir . '/includes/updates/';
	}
}
