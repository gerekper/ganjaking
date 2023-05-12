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
 * @copyright   Copyright (c) 2012-2023, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_0 as Framework;


/**
 * Gets a product availability for local pickup.
 *
 * @since 2.0.0
 *
 * @param int|\WC_Product|\WP_Post $product the product ID or object
 * @param bool $dont_inherit if true, ignores the inherited status and instead returns 'inherit'
 * @return string product availability status
 */
function wc_local_pickup_plus_get_product_availability( $product, $dont_inherit = false ) {
	return wc_local_pickup_plus()->get_products_instance()->get_local_pickup_product_availability( $product, $dont_inherit );
}


/**
 * Get a product category availability for local pickup.
 *
 * @since 2.0.0
 *
 * @param int|\WP_Term $product_cat the product category term ID or object
 * @return string product category availability status
 */
function wc_local_pickup_plus_get_product_cat_availability( $product_cat ) {
	return wc_local_pickup_plus()->get_products_instance()->get_local_pickup_product_cat_availability( $product_cat );
}


/**
 * Check whether a product can be collected for local pickup.
 *
 * @since 2.0.0
 *
 * @param int|\WC_Product|\WP_Post $product product ID or object
 * @param null|int|\WC_Local_Pickup_Plus_Pickup_Location $pickup_location optional: Pickup Location ID or object to check specifically
 * @return bool
 */
function wc_local_pickup_plus_product_can_be_picked_up( $product, $pickup_location = null ) {
	return wc_local_pickup_plus()->get_products_instance()->product_can_be_picked_up( $product, $pickup_location );
}


/**
 * Check whether a product must be collected at a pickup location and can't be shipped.
 *
 * @since 2.0.0
 *
 * @param int|\WC_Product|\WP_Post $product product ID or object
 * @param null|int|\WC_Local_Pickup_Plus_Pickup_Location $pickup_location optional: Pickup Location ID or object to check specifically
 * @return bool
 */
function wc_local_pickup_plus_product_must_be_picked_up( $product, $pickup_location = null ) {
	return wc_local_pickup_plus()->get_products_instance()->product_must_be_picked_up( $product, $pickup_location );
}


/**
 * Check whether a product can only be shipped (ie. with a shipping method different than Local Pickup Plus).
 *
 * @since 2.0.0
 *
 * @param int|\WC_Product|\WP_Post $product product ID or object
 * @return bool
 */
function wc_local_pickup_plus_product_must_be_shipped( $product ) {
	return  wc_local_pickup_plus()->get_products_instance()->product_needs_shipping( $product ) && ! wc_local_pickup_plus_product_can_be_picked_up( $product );
}

