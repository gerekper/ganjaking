<?php
/**
 * WooCommerce Google Analytics Pro
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Google Analytics Pro to newer
 * versions in the future. If you wish to customize WooCommerce Google Analytics Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-google-analytics-pro/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2024, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Adapters;

use Automattic\WooCommerce\Utilities\NumberUtil;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking;
use WC_Abstract_Order;
use WC_Order;

defined( 'ABSPATH' ) or exit;

/**
 * The Order Event Data Adapter class.
 *
 * @since 2.0.0
 */
class Order_Event_Data_Adapter extends Event_Data_Adapter {


	/** @var WC_Abstract_Order the source order or refund */
	protected WC_Abstract_Order $order;


	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_Abstract_Order $order order or refund
	 */
	public function __construct( WC_Abstract_Order $order ) {

		$this->order = $order;
	}


	/**
	 * Converts the source order into an array.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function convert_from_source(): array {

		return [
			'currency'       => $this->order->get_currency(),
			'transaction_id' => $this->order instanceof WC_Order ? $this->order->get_order_number() : $this->order->get_id(), // refunds do not have a number
			'value'          => $this->get_order_value(),
			'coupon'         => implode( ',', $this->order->get_coupon_codes() ),
			'shipping'       => abs( NumberUtil::round( $this->order->get_shipping_total(), wc_get_price_decimals() ) ),
			'tax'            => abs( NumberUtil::round( $this->order->get_total_tax(), wc_get_price_decimals() ) ),
			'items'          => array_values( array_map(
				function ($item) {
					return ( new Order_Item_Event_Data_Adapter( $this->order, $item ) )->convert_from_source();
				},
				$this->order->get_items()
			) ),
		];
	}


	/**
	 * Gets the order value, either with or without tax and shipping.
	 *
	 * @since 2.0.10
	 *
	 * @return float
	 */
	protected function get_order_value() : float {

		$order_value = $this->order->get_total();

		if ( ! Tracking::revenue_should_include_tax_and_shipping() ) {

			// unfortunately order has no method for getting the total without shipping and tax, so we have to manually
			// subtract shipping and tax totals from the order total
			$order_value += - $this->order->get_shipping_total() - $this->order->get_total_tax();
		}

		// absolute values are required by GA4, regardless if this is an order or refund
		return abs( NumberUtil::round( $order_value, wc_get_price_decimals() ) );
	}


}
