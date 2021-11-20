<?php
/**
 * WooCommerce Order Status Manager
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Order Status Manager to newer
 * versions in the future. If you wish to customize WooCommerce Order Status Manager for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-order-status-manager/ for more information.
 *
 * @package     WC-Order-Status-Manager
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Order_Status_Manager\Integration;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Integration class for WooCommerce Subscriptions.
 *
 * @since 1.13.3
 */
class Subscriptions {


	/**
	 * Adds actions and filters.
	 *
	 * @since 1.13.3
	 */
	public function __construct() {

		// add actions just before Subscriptions invokes the `woocommerce_valid_order_statuses_for_payment` filter
		add_action( 'woocommerce_order_status_changed', [ $this, 'attach_ensure_on_hold_in_valid_statuses' ], 8, 1 );
		add_action( 'woocommerce_checkout_order_processed', [ $this, 'attach_ensure_on_hold_in_valid_statuses' ], 49, 1 );
		// @TODO this hook is marked "experimental" by WooCommerce, but Subscriptions uses it at the moment, and we decided to use it for the time being, but in future versions it may have to be updated {unfulvio 2021-11-15}
		add_action( '__experimental_woocommerce_blocks_checkout_order_processed', [ $this, 'attach_ensure_on_hold_in_valid_statuses' ], 49, 1 );
	}


	/**
	 * Attaches the actions and filters that ensure the 'on-hold' status is in the list of unpaid statuses.
	 *
	 * @see \SkyVerge\WooCommerce\Order_Status_Manager\Integration\Subscriptions::ensure_on_hold_in_valid_statuses()
	 *
	 * @since 1.13.3
	 *
	 * @internal
	 *
	 * @param int|\WC_Order $order
	 */
	public function attach_ensure_on_hold_in_valid_statuses( $order ) {

		if ( wcs_order_contains_subscription( $order ) ) {

			// applies the filter to ensure the 'on-hold' status is in the list of valid statuses for payment
			add_filter( 'woocommerce_valid_order_statuses_for_payment', [ $this, 'ensure_on_hold_in_valid_statuses'], 20, 1 );

			// removes the filter added above
			add_action( 'woocommerce_order_status_changed', [ $this, 'detach_ensure_on_hold_in_valid_statuses' ], 11, 1 );
			add_action( 'woocommerce_checkout_order_processed', [ $this, 'detach_ensure_on_hold_in_valid_statuses' ], 51, 1 );
			// @TODO this hook is marked "experimental" by WooCommerce, but Subscriptions uses it at the moment, and we decided to use it for the time being, but in future versions it may have to be updated {unfulvio 2021-11-15}
			add_action( '__experimental_woocommerce_blocks_checkout_order_processed', [ $this, 'detach_ensure_on_hold_in_valid_statuses' ], 51, 1 );
		}
	}


	/**
	 * Removes a previously-attached filter after it's been applied.
	 *
	 * @see \SkyVerge\WooCommerce\Order_Status_Manager\Integration\Subscriptions::attach_ensure_on_hold_in_valid_statuses()
	 *
	 * @internal
	 *
	 * @since 1.13.3
	 */
	public function detach_ensure_on_hold_in_valid_statuses() {

		remove_filter( 'woocommerce_valid_order_statuses_for_payment', [ $this, 'ensure_on_hold_in_valid_statuses' ], 20 );
	}


	/**
	 * Adds 'on-hold' to the list of valid order statuses for payment.
	 *
	 * OSM users can mark the 'on-hold' order status as an order status that
	 * doesn't require payment. This creates problems with Subscriptions,
	 * because Subscriptions will *only* activate a subscription on an order
	 * status change if the order is transitioning from a status that requires
	 * payment, to a status that is paid.
	 *
	 * This means that if the merchant's gateway uses the on-hold status while
	 * a payment is processing, and the merchant has on-hold marked as a
	 * status that isn't in need of payment, then when the transaction finally
	 * clears OSM will prevent the subscription from being activated, by
	 * removing 'on-hold' inside the `woocommerce_valid_order_statuses_
	 * for_payment` filter. Here, we add 'on-hold' back if the order is
	 * subscription-related, so the subscription will be activated as
	 * expected.
	 *
	 * @since 1.13.3
	 *
	 * @internal
	 *
	 * @param string[] $statuses the list of statuses to filter
	 * @return string[] the list of statuses, possibly with 'on-hold' re-added
	 */
	public function ensure_on_hold_in_valid_statuses( $statuses ) {

		if ( is_array( $statuses ) && ! in_array( 'on-hold', $statuses, true ) ) {
			$statuses[] = 'on-hold';
		}

		return $statuses;
	}


}
