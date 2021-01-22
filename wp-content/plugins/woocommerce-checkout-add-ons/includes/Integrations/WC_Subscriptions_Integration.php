<?php
/**
 * WooCommerce Checkout Add-Ons
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Checkout Add-Ons to newer
 * versions in the future. If you wish to customize WooCommerce Checkout Add-Ons for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-checkout-add-ons/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Checkout_Add_Ons\Integrations;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Class WC_Subscriptions_Integration
 * @package SkyVerge\WooCommerce\Checkout_Add_Ons\Integrations
 *
 * @since 2.0.5
 */
class WC_Subscriptions_Integration {

	/**
	 * WC_Subscriptions_Integration constructor.
	 * Set up hooks
	 */
	public function __construct() {

		add_filter( 'wc_checkout_add_ons_fields',                               array( $this, 'hide_add_on_fields_for_renewals' ) );
		add_action( 'woocommerce_cart_calculate_fees',                          array( $this, 'add_cart_fees' ), PHP_INT_MAX, 1 );
	}

	/**
	 * Hides add-on fields if cart contains renewal.
	 *
	 * @since 2.0.5
	 *
	 * @param array $fields
	 * @return array
	 */
	public function hide_add_on_fields_for_renewals( $fields ) {

		if ( ! wc_checkout_add_ons()->is_subscriptions_active() ) {
			return $fields;
		}

		$cart_items = wcs_cart_contains_renewal();
		if ( ! empty( $cart_items ) ) {
			unset( $fields['add_ons'] );
		}

		return $fields;
	}

	/**
	 * Adds order add-ons as fees to the cart only if add-ons are renewable.
	 *
	 * @since 2.0.5
	 *
	 * @param \WC_Cart $cart
	 */
	public function add_cart_fees( $cart ) {

		if ( ! wc_checkout_add_ons()->is_subscriptions_active() ) {
			return;
		}

		$cart_item = wcs_cart_contains_renewal();

		if ( $cart_item && isset( $cart_item['subscription_renewal'] ) ) {

			/** @var $original_order \WC_Order */
			$original_order = wcs_get_subscription( $cart_item['subscription_renewal']['subscription_id'] )->get_parent();

			if ( $original_order ) {

				$order_id      = $original_order->get_id();
				$order_add_ons = wc_checkout_add_ons()->get_order_renewable_add_ons( $order_id );
				$add_on_data   = [];

				if ( ! empty( $order_add_ons ) ) {

					foreach ( $order_add_ons as $add_on_id => $add_on ) {
						$add_on_data[ $add_on_id ] = $add_on['value'];
					}

					wc_checkout_add_ons()->get_frontend_instance()->add_ons_as_fees( $cart, $add_on_data );
				}
			}
		}
	}
}
