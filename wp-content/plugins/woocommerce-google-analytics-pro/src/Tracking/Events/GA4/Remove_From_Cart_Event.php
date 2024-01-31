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

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\GA4;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Adapters\Cart_Item_Event_Data_Adapter;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\GA4_Event;

defined( 'ABSPATH' ) or exit;

/**
 * The "remove from cart" event.
 *
 * @since 2.0.0
 */
class Remove_From_Cart_Event extends GA4_Event {


	/** @var string the event ID */
	public const ID = 'remove_from_cart';

	/** @var string the event trigger action hook  */
	protected string $trigger_hook = 'woocommerce_remove_cart_item';

	/** @var bool whether this is a GA4 recommended event */
	protected bool $recommended_event = true;


	/**
	 * @inheritdoc
	 */
	public function get_form_field_title(): string {

		return __( 'Remove from Cart', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_form_field_description(): string {

		return __( 'Triggered when a customer removes an item from the cart.', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_default_name(): string {

		return 'remove_from_cart';
	}


	/**
	 * @inheritdoc
	 *
	 * @param string $cart_item_key the unique cart item ID
	 */
	public function track( $cart_item_key = null ): void {

		if ( ! $cart_item_key || empty( $item = WC()->cart->cart_contents[ $cart_item_key ] ) ) {
			return;
		}

		$this->record_via_api( [
			'category'       => 'Cart',
			'currency'       => get_woocommerce_currency(),
			'value'          => $item['line_total'],
			'items'          => [ ( new Cart_Item_Event_Data_Adapter( $item ) )->convert_from_source() ],
		] );
	}


}
