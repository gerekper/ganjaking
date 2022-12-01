<?php
/**
 * WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Integration class for Bookings plugin
 *
 * @since 1.0.0
 */
class WC_Memberships_Integration_Bookings {


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$version = $this->get_bookings_version();

		// adjusts discounts (apply latest filter if unable to determine version)
		if ( empty( $version ) || version_compare( $version, '1.15.0', '>=' ) ) {
			add_filter( 'woocommerce_bookings_calculated_booking_cost', [ $this, 'adjust_booking_cost' ], 10, 2 );
		} else {
			add_filter( 'booking_form_calculated_booking_cost', [ $this, 'adjust_booking_cost' ], 10, 3 );
		}
	}


	/**
	 * Gets the WooCommerce Bookings version.
	 *
	 * @return string
	 */
	public function get_bookings_version() : string {

		if ( defined( 'WC_BOOKINGS_VERSION' ) ) {
			return (string) WC_BOOKINGS_VERSION;
		}

		if ( $version = get_option( 'wc_bookings_version' ) ) {
			return (string) $version;
		}

		return '';
	}


	/**
	 * Adjusts booking cost.
	 *
	 * @since 1.3.0
	 *
	 * @internal
	 *
	 * @param float|int|mixed $cost
	 * @param \WC_Booking_Form|\WC_Product_Booking|mixed $object
	 * @param array|mixed $data
	 * @return float
	 */
	public function adjust_booking_cost( $cost, $object, $data = [] ) {

		$discounted_cost = $product = $user_id = null;

		// don't discount the price when adding a booking to the cart
		if ( doing_action( 'woocommerce_add_cart_item_data' ) ) {

			$discounted_cost = $cost;

		} else {

			if ( $object instanceof WC_Booking_Form ) {
				$product = $object->product ?? null;
			} elseif ( $object instanceof WC_Product_Booking ) {
				$product = $object;
			}

			if ( $product ) {

				// handling to grab the user ID may be slightly different across WooCommerce Bookings versions
				if ( isset( $_REQUEST['customer_id'] ) ) {
					$user_id = $_REQUEST['customer_id'];
				} elseif ( isset( $_REQUEST['form'] ) ) {
					parse_str( $_REQUEST['form'], $query );
					$user_id = $query['customer_id'] ?? null;
				}

				$discounted_cost = wc_memberships()->get_member_discounts_instance()->get_discounted_price( $cost, $product, $user_id );
			}
		}

		return is_numeric( $discounted_cost ) ? (float) $discounted_cost : (float) $cost;
	}


}
