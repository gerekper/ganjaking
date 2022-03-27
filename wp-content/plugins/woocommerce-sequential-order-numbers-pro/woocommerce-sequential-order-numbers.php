<?php
/**
 * WooCommerce Sequential Order Numbers Pro
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Social Login to newer
 * versions in the future. If you wish to customize WooCommerce Social Login for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-social-login/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Ensures the plugin remains active when updated after main plugin file name change.
 *
 * The main plugin file name was changed from
 *  `woocommerce-sequential-order-numbers.php`
 * to
 *  `woocommerce-sequential-order-numbers-pro.php`
 * matching the plugin slug and container directory.
 *
 * This can eventually be removed when most merchants will have upgraded to the new version.
 *
 * @since 1.13.0
 */
$active_plugins = get_option( 'active_plugins', array() );

foreach ( $active_plugins as $key => $active_plugin ) {

	// legacy plugin name
	if ( false !== strpos( $active_plugin, 'woocommerce-sequential-order-numbers-pro/woocommerce-sequential-order-numbers.php' ) ) {

		// replace with current plugin name
		$active_plugins[ $key ] = str_replace( 'woocommerce-sequential-order-numbers-pro/woocommerce-sequential-order-numbers.php', 'woocommerce-sequential-order-numbers-pro/woocommerce-sequential-order-numbers-pro.php', $active_plugin );
	}
}

update_option( 'active_plugins', $active_plugins );
