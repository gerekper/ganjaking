<?php
/**
 * Uninstall plugin
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

// If uninstall not called from WordPress exit
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

if ( defined( 'YITH_WCAF_REMOVE_ALL_DATA' ) && true === YITH_WCAF_REMOVE_ALL_DATA ) {
	// remove role
	$role = YITH_WCAF_Affiliate_Handler()->get_role_name();
	$affiliates = get_users( array( 'role' => $role ) );

	if( ! empty( $affiliates ) ){
		foreach ( $affiliates as $affiliate ){
			/**
			 * @var $affiliate \WP_User
			 */
			$affiliate->remove_role( $role );
		}
	}

	// delete pages created for this plugin
	wp_delete_post( get_option( 'yith_wcaf_dashboard_page_id' ), true );

	// remove plugins options
	$sql = "DELETE FROM `" . $wpdb->options . "` WHERE option_name LIKE 'yith_wcaf%'";
	$wpdb->query( $sql );

	// remove plugins meta
	$sql = "DELETE FROM `" . $wpdb->postmeta . "` WHERE meta_key LIKE 'yith_wcaf%'";
	$wpdb->query( $sql );

	// remove custom tables
	$sql = "DROP TABLE `" . $wpdb->yith_affiliates . "`";
	$wpdb->query( $sql );
	$sql = "DROP TABLE `" . $wpdb->yith_commissions . "`";
	$wpdb->query( $sql );
	$sql = "DROP TABLE `" . $wpdb->yith_commission_notes . "`";
	$wpdb->query( $sql );
	$sql = "DROP TABLE `" . $wpdb->yith_clicks . "`";
	$wpdb->query( $sql );
	$sql = "DROP TABLE `" . $wpdb->yith_payments . "`";
	$wpdb->query( $sql );
	$sql = "DROP TABLE `" . $wpdb->yith_payment_commission . "`";
	$wpdb->query( $sql );
	$sql = "DROP TABLE `" . $wpdb->yith_payment_notes . "`";
	$wpdb->query( $sql );
}

?>