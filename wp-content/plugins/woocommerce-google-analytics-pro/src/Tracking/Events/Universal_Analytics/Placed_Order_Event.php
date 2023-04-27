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
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Universal_Analytics;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Universal_Analytics_Event;

defined( 'ABSPATH' ) or exit;

/**
 * The "placed order" event.
 *
 * @since 2.0.0
 */
class Placed_Order_Event extends Universal_Analytics_Event {


	/** @var string the event ID */
	public const ID = 'placed_order';

	/** @var string the event trigger action hook  */
	protected string $trigger_hook = 'woocommerce_checkout_order_processed';


	/**
	 * @inheritdoc
	 */
	public function get_form_field_title(): string {

		return __( 'Placed Order', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_form_field_description(): string {

		return __( 'Triggered when a customer places an order via checkout.', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_default_name(): string {

		return 'placed order';
	}


	/**
	 * @inheritdoc
	 *
	 * @param int $order_id the order ID
	 */
	public function track( $order_id = null ): void {

		$order = wc_get_order( $order_id ) ;

		$properties = [
			'eventCategory'  => 'Checkout',
			'eventLabel'     => $order->get_order_number(),
			'nonInteraction' => true,
		];

		$ec = [ 'checkout' => [ 'order' => $order, 'step' => 4, 'option' => $order->get_shipping_method() ] ];

		$this->record_via_api( $properties, $ec );
	}


}
