<?php
/**
 * WooCommerce Cost of Goods
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Cost of Goods to newer
 * versions in the future. If you wish to customize WooCommerce Cost of Goods for your
 * needs please refer to http://docs.woocommerce.com/document/cost-of-goods/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\COG\Integrations;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_2 as Framework;

/**
 * The Subscriptions integration handler.
 *
 * @since 2.8.2
 */
class Subscriptions {


	/**
	 * Constructs the class.
	 *
	 * @since 2.8.2
	 */
	public function __construct() {

		add_filter( 'wc_cost_of_goods_previous_orders_query', [ $this, 'add_subscriptions_previous_orders_query' ] );
		add_action( 'woocommerce_checkout_subscription_created', [ $this, 'recalculate_total_cost_for_subscription' ], 10, 3 );
		add_filter( 'wcs_new_order_created', [ $this, 'recalculate_total_cost_for_renewal_order' ], 10, 3 );
	}


	/**
	 * Adds subscriptions to the query when applying costs to previous orders.
	 *
	 * @internal
	 *
	 * @since 2.8.2
	 *
	 * @param array $query_args WP_Query args
	 * @return array
	 */
	public function add_subscriptions_previous_orders_query( $query_args ) {

		$query_args['post_type'] = isset( $query_args['post_type'] ) ? (array) $query_args['post_type'] : [];

		$query_args['post_type'][] = 'shop_subscription';

		return $query_args;
	}


	/**
	 * Re-calculates Cost of Goods for Subscriptions.
	 *
	 * This is needed because the cost may be different from the parent order.
	 *
	 * @internal
	 *
	 * @since 2.9.4
	 *
	 * @param \WC_Subscription $subscription subscription just created
	 * @param \WC_Order $order
	 * @param \WC_Cart $recurring_cart
	 */
	public function recalculate_total_cost_for_subscription( $subscription, $order, $recurring_cart ) {

		wc_cog()->set_order_cost_meta( $subscription->get_id(), true );
	}


	/**
	 * Re-calculates Cost of Goods for Subscriptions renewals.
	 *
	 * This is needed because the subscription may have been created with an incorrect cost
	 * (copied from the parent order).
	 *
	 * @internal
	 *
	 * @since 2.9.4
	 *
	 * @param \WC_Order $new_order new order
	 * @param \WC_Subscription $subscription subscription post
	 * @param string $type new order type
	 * @return \WC_Order
	 */
	public function recalculate_total_cost_for_renewal_order( $new_order, $subscription, $type ) {

		if ( 'renewal_order' === $type ) {
			wc_cog()->set_order_cost_meta( $new_order->get_id(), true );
		}

		return $new_order;
	}


}
