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
use WC_Product;

defined( 'ABSPATH' ) or exit;

/**
 * The "added to cart" event.
 *
 * @since 2.0.0
 */
class Added_To_Cart_Event extends Universal_Analytics_Event {


	/** @var string the event ID */
	public const ID = 'added_to_cart';


	/**
	 * @inheritdoc
	 */
	public function get_form_field_title(): string {

		return __( 'Added to Cart', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_form_field_description(): string {

		return __( 'Triggered when a customer adds an item to the cart.', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_default_name(): string {

		return 'added to cart';
	}


	/**
	 * @inheritdoc
	 */
	public function register_hooks() : void {

		add_action( 'woocommerce_add_to_cart', [ $this, 'handle_add_to_cart' ], 10, 5 );

	}


	/**
	 * Tracks the (non-ajax) add-to-cart event.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $cart_item_key the unique cart item ID
	 * @param int $product_id the product ID
	 * @param int $quantity the quantity added to the cart
	 * @param int $variation_id the variation ID
	 * @param array $variation the variation data
	 */
	public function handle_add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation ) : void {

		$product = $variation_id ? wc_get_product( $variation_id ) : wc_get_product( $product_id );

		$this->track( $product, $cart_item_key, $variation, $quantity );
	}


	/**
	 * @inheritdoc
	 *
	 * @param WC_Product $product the product that was added to cart
	 * @param string $cart_item_key the unique cart item ID
	 * @param array $variation the variation data
	 * @param int $quantity the quantity added to the cart, defaults to 1
	 */
	public function track( $product = null, $cart_item_key = null, $variation = null, $quantity = 1 ): void {

		if ( ! $product ) {
			return;
		}

		$properties = [
			'eventCategory' => 'Products',
			'eventLabel'    => htmlentities( $product->get_name(), ENT_QUOTES, 'UTF-8' ),
			'eventValue'    => (int) $quantity,
		];

		if ( ! empty( $variation ) ) {

			// added a variable product to cart:
			// - set attributes as properties
			// - remove 'pa_' from keys to keep property names consistent
			$variation  = array_flip( str_replace( 'attribute_', '', array_flip( $variation ) ) );
			$properties = array_merge( $properties, $variation );
		}

		$this->record_via_api(
			$properties,
			[
				'add_to_cart' => [
					'product'       => $product,
					'quantity'      => $quantity,
					'cart_item_key' => $cart_item_key,
					'variation'     => $variation,
				],
			]
		);
	}


}
