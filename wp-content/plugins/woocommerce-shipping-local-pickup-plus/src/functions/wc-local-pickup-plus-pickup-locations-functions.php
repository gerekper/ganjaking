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


/**
 * Main function for returning a pickup location.
 *
 * @since 2.0.0
 *
 * @param int|\WP_Post|\WC_Local_Pickup_Plus_Pickup_Location $pickup_location the pickup location post, post id or main object
 * @return null|\WC_Local_Pickup_Plus_Pickup_Location a pickup location object
 */
function wc_local_pickup_plus_get_pickup_location( $pickup_location ) {
	return wc_local_pickup_plus()->get_pickup_locations_instance()->get_pickup_location( $pickup_location );
}


/**
 * Main function for returning pickup locations.
 *
 * @since 2.0.0
 *
 * @param array $args optional array of arguments passed to` get_posts()`
 * @return \WC_Local_Pickup_Plus_Pickup_Location[] array of location objects or empty array if none found
 */
function wc_local_pickup_plus_get_pickup_locations( $args = array() ) {
	return wc_local_pickup_plus()->get_pickup_locations_instance()->get_pickup_locations( $args );
}


/**
 * Main functions for returning locations by distance or address.
 *
 * @since 2.0.0
 *
 * @param array|\WC_Local_Pickup_Plus_Address $origin either coordinates (array) or address (object)
 * @param array $args optional array of arguments similar to those expected by `get_posts()`
 * @return \WC_Local_Pickup_Plus_Pickup_Location[] array of pickup locations
 */
function wc_local_pickup_plus_get_pickup_locations_nearby( $origin, $args = array() ) {
	return wc_local_pickup_plus()->get_pickup_locations_instance()->get_pickup_locations_nearby( $origin, $args );
}
