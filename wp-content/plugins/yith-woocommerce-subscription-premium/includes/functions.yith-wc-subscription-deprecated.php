<?php
/**
 * Deprecated functions from past YITH WooCommerce Subscription versions. You shouldn't use these
 * functions and look for the alternatives instead. The functions will be
 * removed in a later version.
 *
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

global $yith_ywsbs_db_version;
$yith_ywsbs_db_version = '1.0.0';

if ( ! function_exists( 'yith_ywsbs_db_install' ) ) {

	/**
	 * Install the table yith_ywsbs_activities_log.
	 *
	 * @return     void
	 * @deprecated 2.0.0 Use YITH_WC_Subscription_DB class
	 * @see        YITH_WC_Subscription_DB::install()
	 *
	 * @since 1.0.0
	 */
	function yith_ywsbs_db_install() {
		_deprecated_function( __FUNCTION__, '2.0.0', 'new YITH_WC_Subscription_DB class' );

		global $wpdb;
		global $yith_ywsbs_db_version;

		$installed_ver = get_option( 'yith_ywsbs_db_version' );

		if ( $installed_ver !== $yith_ywsbs_db_version ) {

			$table_name = $wpdb->prefix . 'yith_ywsbs_activities_log';

			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`activity` varchar(255) NOT NULL,
		`status` varchar(255) NOT NULL,
		`subscription` int(11) NOT NULL,
		`order` int(11) NOT NULL,
		`description` varchar(255) NOT NULL,
		`timestamp_date` datetime NOT NULL,
		PRIMARY KEY (id)
		) $charset_collate;";

			include_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );

			add_option( 'yith_ywsbs_db_version', $yith_ywsbs_db_version );
		}
	}
}

if ( ! function_exists( 'yith_ywsbs_update_db_check' ) ) {

	/**
	 * Check if the function yith_ywsbs_db_install must be installed or updated.
	 *
	 * @return     void
	 * @deprecated 2.0.0 Use YITH_WC_Subscription_DB class
	 * @see        YITH_WC_Subscription_DB::install()
	 *
	 * @since 1.0.0
	 */
	function yith_ywsbs_update_db_check() {
		_deprecated_function( __FUNCTION__, '2.0.0', 'new YITH_WC_Subscription_DB class' );

		global $yith_ywsbs_db_version;

		if ( get_site_option( 'yith_ywsbs_db_version' ) !== $yith_ywsbs_db_version ) {
			yith_ywsbs_db_install();
		}
	}
}

if ( ! function_exists( 'yith_check_privacy_enabled' ) ) {
	/**
	 * Check if the tool for export and erase personal data are enabled.
	 *
	 * @param      bool $wc Tell if WooCommerce privacy is needed.
	 * @return     bool
	 * @deprecated 2.0.0
	 * @since      1.0.0
	 */
	function yith_check_privacy_enabled( $wc = false ) {
		global $wp_version;
		_deprecated_function( __FUNCTION__, '2.0.0' );
		$enabled = $wc ? version_compare( WC()->version, '3.4.0', '>=' ) && version_compare( $wp_version, '4.9.5', '>' ) : version_compare( $wp_version, '4.9.5', '>' );
		return apply_filters( 'yith_check_privacy_enabled', $enabled, $wc );
	}
}
