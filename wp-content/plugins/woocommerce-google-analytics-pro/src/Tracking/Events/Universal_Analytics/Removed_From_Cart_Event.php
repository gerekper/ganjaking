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

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Universal_Analytics;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Universal_Analytics_Event;

defined( 'ABSPATH' ) or exit;

/**
 * The "removed from" cart event.
 *
 * @since 2.0.0
 */
class Removed_From_Cart_Event extends Universal_Analytics_Event {


	/** @var string the event ID */
	public const ID = 'removed_from_cart';

	/** @var string the event trigger action hook  */
	protected string $trigger_hook = 'woocommerce_remove_cart_item';


	/**
	 * @inheritdoc
	 */
	public function get_form_field_title(): string {

		return __( 'Removed from Cart', 'woocommerce-google-analytics-pro' );
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

		return 'removed from cart';
	}


	/**
	 * @inheritdoc
	 *
	 * @param string $cart_item_key the unique cart item ID
	 */
	public function track( $cart_item_key = null ): void {

		if ( isset( WC()->cart->cart_contents[ $cart_item_key ] ) ) {

			$item    = WC()->cart->cart_contents[$cart_item_key];
			$product = ! empty( $item['variation_id'] ) ? wc_get_product( $item['variation_id'] ) : wc_get_product( $item['product_id'] );

			if ( ! $product ) {
				return;
			}

			$this->record_via_api(
				[
					'eventCategory' => 'Cart',
					'eventLabel'    => htmlentities( $product->get_name(), ENT_QUOTES, 'UTF-8' ),
				],
				[
					'remove_from_cart' => [
						'product'   => $product,
						'cart_item' => $item,
					]
				]
			);
		}
	}


}
