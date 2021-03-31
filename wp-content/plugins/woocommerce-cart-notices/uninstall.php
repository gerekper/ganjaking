<?php
/**
 * WooCommerce Cart Notices
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Cart Notices to newer
 * versions in the future. If you wish to customize WooCommerce Cart Notices for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-cart-notices/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

// Disable direct access and ensure the WP_UNINSTALL_PLUGIN constant is set
if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Uninstall script.  This is *not* automatically invoked, but can be used to
 * conditionally uninstall the plugin data when needed.
 *
 * This script:
 * - drops the cart_notices table
 * - deletes the cart notices db version option
 */

// Delete data only if the option is set
if ( 'yes' === get_option( 'wc_cart_notices_uninstall_data', 'no' ) ) {

	global $wpdb;

	$table = $wpdb->prefix . 'cart_notices';
	$wpdb->query( "DROP TABLE {$table}" );

	delete_option( 'wc_cart_notices_version' );
	delete_option( 'wc_cart_notices_uninstall_data' );
}
