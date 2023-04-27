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

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\GA4;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Helpers\Identity_Helper;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\GA4_Event;

defined( 'ABSPATH' ) or exit;

/**
 * The "place order" event.
 *
 * @since 2.0.0
 */
class Place_Order_Event extends GA4_Event {


	/** @var string the event ID */
	public const ID = 'place_order';

	/** @var string the event trigger action hook  */
	protected string $trigger_hook = 'woocommerce_checkout_order_processed';


	/**
	 * @inheritdoc
	 */
	public function get_form_field_title(): string {

		return __( 'Place Order', 'woocommerce-google-analytics-pro' );
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

		return 'place_order';
	}


	/**
	 * @inheritdoc
	 *
	 * @param int $order_id the order ID
	 */
	public function track( $order_id = null ): void {

		if ( empty( $order = wc_get_order( $order_id ) ) ) {
			return;
		}

		$this->record_via_api( [
			'category'       => 'Checkout',
			'transaction_id' => $order->get_order_number(),
		] );
	}


}
