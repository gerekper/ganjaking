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
 * The "changed cart quantity" event.
 *
 * @since 2.0.0
 */
class Changed_Cart_Quantity_Event extends Universal_Analytics_Event {


	/** @var string the event ID */
	public const ID = 'changed_cart_quantity';


	/**
	 * @inheritdoc
	 */
	public function get_form_field_title(): string {

		return __( 'Changed Cart Quantity', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_form_field_description(): string {

		return __( 'Triggered when a customer changes the quantity of an item in the cart.', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_default_name(): string {

		return 'changed cart quantity';
	}


	/**
	 * @inheritdoc
	 */
	public function register_hooks() : void {

		add_action( 'woocommerce_after_cart_item_quantity_update', [ $this, 'track' ], 10, 2 );
	}


	/**
	 * @inheritdoc
	 *
	 * @param string $cart_item_key the unique cart item ID
	 * @param int $quantity the changed quantity
	 */
	public function track( $cart_item_key = null, $quantity = null ): void {

		if ( isset( WC()->cart->cart_contents[ $cart_item_key ] ) ) {

			$item    = WC()->cart->cart_contents[ $cart_item_key ];
			$product = wc_get_product( $item[ 'product_id' ] );

			$this->record_via_api( [
				'eventCategory' => 'Cart',
				'eventLabel'    => htmlentities( $product->get_title(), ENT_QUOTES, 'UTF-8' ),
			] );
		}
	}


}
