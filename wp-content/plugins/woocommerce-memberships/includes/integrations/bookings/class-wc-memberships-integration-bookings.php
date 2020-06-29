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
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

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

		// adjusts discounts
		add_filter( 'booking_form_calculated_booking_cost', array( $this, 'adjust_booking_cost' ), 10, 3 );
	}


	/**
	 * Adjusts booking cost.
	 *
	 * @since 1.3.0
	 *
	 * @param float $cost
	 * @param \WC_Booking_Form $form
	 * @param array $posted
	 * @return float
	 */
	public function adjust_booking_cost( $cost, WC_Booking_Form $form, $posted ) {

		// don't discount the price when adding a booking to the cart
		if ( doing_action( 'woocommerce_add_cart_item_data' ) ) {
			$discounted_cost = $cost;
		} else {
			$discounted_cost = wc_memberships()->get_member_discounts_instance()->get_discounted_price( $cost, $form->product );
		}

		return is_numeric( $discounted_cost ) ? (float) $discounted_cost : (float) $cost;
	}


	/**
	 * Removes the add to cart button for non-purchasable bookable products.
	 *
	 * @since 1.6.2
	 * @deprecated 1.17.2
	 */
	public function add_purchaseable_product_to_cart() {

		wc_deprecated_function( __METHOD__, '1.17.2' );
	}


}
