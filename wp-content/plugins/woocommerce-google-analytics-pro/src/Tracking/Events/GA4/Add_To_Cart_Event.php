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

use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Adapters\Product_Item_Event_Data_Adapter;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\GA4_Event;
use WC_Product;

defined( 'ABSPATH' ) or exit;

/**
 * The "add to cart" event.
 *
 * @since 2.0.0
 */
class Add_To_Cart_Event extends GA4_Event {


	/** @var string the event ID */
	public const ID = 'add_to_cart';

	/** @var bool whether this is a GA4 recommended event */
	protected bool $recommended_event = true;


	/**
	 * @inheritdoc
	 */
	public function get_form_field_title(): string {

		return __( 'Add to Cart', 'woocommerce-google-analytics-pro' );
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

		return 'add_to_cart';
	}


	/**
	 * @inheritdoc
	 */
	public function register_hooks() : void {

		add_action( 'woocommerce_add_to_cart', [ $this, 'handle_add_to_cart' ], 10, 5 );
	}


	/**
	 * Handles the add to cart hook.
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

		$this->track( wc_get_product( $variation_id ?: $product_id ), $quantity, $variation );
	}


	/**
	 * @inheritdoc
	 *
	 * @param WC_Product $product the product that was added to cart
	 * @param int $quantity the quantity added to the cart, defaults to 1
	 * @param array $variation the variation data, defaults to an empty array
	 */
	public function track( $product = null, $quantity = 1, $variation = [] ): void {

		if ( ! $product ) {
			return;
		}

		$this->record_via_api( [
			'category'       => 'Products',
			'currency'       => get_woocommerce_currency(),
			'value'          => $quantity * $product->get_price(),
			'items'          => [ ( new Product_Item_Event_Data_Adapter( $product ) )->convert_from_source( $quantity, (array) $variation ) ],
		] );
	}


}
