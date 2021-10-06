<?php
/**
 * WooCommerce Local Pickup Plus
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2021, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

require_once( wc_local_pickup_plus()->get_plugin_path() . '/src/functions/wc-local-pickup-plus-pickup-locations-functions.php' );
require_once( wc_local_pickup_plus()->get_plugin_path() . '/src/functions/wc-local-pickup-plus-product-functions.php' );
require_once( wc_local_pickup_plus()->get_plugin_path() . '/src/functions/wc-local-pickup-plus-user-functions.php' );


/**
 * Get the shipping method.
 *
 * @since 2.0.0
 *
 * @return \WC_Shipping_Local_Pickup_Plus
 */
function wc_local_pickup_plus_shipping_method() {
	return wc_local_pickup_plus()->get_shipping_method_instance();
}


/**
 * Get the shipping method ID.
 *
 * @since 2.0.0
 *
 * @return string
 */
function wc_local_pickup_plus_shipping_method_id() {
	return wc_local_pickup_plus_shipping_method()->get_method_id();
}


/**
 * Get the appointments mode setting.
 *
 * @since 2.0.0
 *
 * @return string either 'disabled', 'enabled' or 'required'
 */
function wc_local_pickup_plus_appointments_mode() {

	$appointments = 'disabled';

	if ( $shipping_method = wc_local_pickup_plus_shipping_method() ) {
		$appointments = $shipping_method->pickup_appointments_mode();
	}

	return $appointments;
}
