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

namespace SkyVerge\WooCommerce\COG\Utilities;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_2 as Framework;

/**
 * Background job handler to apply costs to previous orders.
 *
 * @since 2.8.0
 */
class Previous_Orders_Handler extends Framework\SV_WP_Background_Job_Handler {


	/**
	 * Background job constructor.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {

		$this->prefix   = 'wc_cog';
		$this->action   = 'apply_costs_previous_orders';
		$this->data_key = 'object_ids';

		parent::__construct();
	}


	/**
	 * Processes a previous order to apply costs.
	 *
	 * @since 2.8.0
	 *
	 * @param int $object_id previous order ID
	 * @param \stdClass|null $job optional job object
	 */
	public function process_item( $object_id, $job = null ) {

		$order = wc_get_order( $object_id );
		$apply = false;

		if ( $order && $order instanceof \WC_Order ) {

			/**
			 * Filters whether to process an order for applying costs.
			 *
			 * @since 2.8.0
			 *
			 * @param bool $apply whether to apply or not (default true if order is valid)
			 * @param \WC_Order $order the order object
			 */
			$apply = (bool) apply_filters( 'wc_cog_apply_costs_to_previous_order', true, $order );

			if ( $apply ) {

				// when set, this value will force recalculating costs for order/order items which have already set costs; otherwise, calculation will normally run only for orders without set costs
				$force = ! empty( $job->orders ) && 'all-orders' === $job->orders;

				// set costs
				wc_cog()->set_order_cost_meta( $order, $force );

				// account for possible refunds
				foreach ( $order->get_refunds() as $refund ) {

					wc_cog()->add_refund_order_costs( $refund->get_id() );
				}
			}
		}

		/**
		 * Fires after an order has been processed to apply costs.
		 *
		 * @since 2.8.0
		 *
		 * @param int $object_id order ID (or other object ID)
		 * @param bool $apply whether costs where applied to the objects
		 */
		do_action( 'wc_cost_of_goods_applied_costs_to_previous_order', $object_id, $apply );
	}


}
