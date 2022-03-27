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
 * @copyright   Copyright (c) 2012-2022, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;


/**
 * Set a default / preferred pickup location for user.
 *
 * @since 2.0.0
 *
 * @param int|\WC_Local_Pickup_Plus_Pickup_Location $pickup_location a pickup location ID or object
 * @param int|\WP_User $user optional: a WordPress user (leave default null to use the current user)
 * @return bool
 */
function wc_local_pickup_plus_set_user_default_pickup_location( $pickup_location, $user = null ) {

	$success = false;

	if ( is_numeric( $user ) ) {
		$user = get_user_by( 'id', $user );
	} elseif ( null === $user ) {
		$user = wp_get_current_user();
	}

	if ( $user instanceof \WP_User ) {

		if ( is_numeric( $pickup_location ) ) {
			$pickup_location = wc_local_pickup_plus_get_pickup_location( $pickup_location );
		}

		if ( $pickup_location instanceof \WC_Local_Pickup_Plus_Pickup_Location ) {
			$success = update_user_meta( $user->ID, '_default_pickup_location', $pickup_location->get_id() );
		}
	}

	return (bool) $success;
}


/**
 * Gets the default / preferred pickup location for user.
 *
 * @since 2.0.0
 *
 * @param int|\WP_User $user optional: user ID or object (or leave default null to get data for the current logged in user)
 * @return null|\WC_Local_Pickup_Plus_Pickup_Location
 */
function wc_local_pickup_plus_get_user_default_pickup_location( $user = null ) {

	$pickup_location = null;

	if ( is_numeric( $user ) ) {
		$user = get_user_by( 'id', $user );
	} elseif ( null === $user ) {
		$user = wp_get_current_user();
	}

	if ( $user instanceof \WP_User ) {

		$pickup_location_id = get_user_meta( $user->ID, '_default_pickup_location', true );

		if ( is_numeric( $pickup_location_id ) && $pickup_location_id > 0 ) {
			$pickup_location = wc_local_pickup_plus_get_pickup_location( $pickup_location_id );
		}
	}

	/**
	 * Filters the default / preferred pickup location for user.
	 *
	 * @since 2.3.17
	 *
	 * @param null|\WC_Local_Pickup_Plus_Pickup_Location the pickup location
	 * @param \WP_User $user the user object
	 */
	return apply_filters( 'wc_local_pickup_plus_get_user_default_pickup_location', $pickup_location, $user );
}
