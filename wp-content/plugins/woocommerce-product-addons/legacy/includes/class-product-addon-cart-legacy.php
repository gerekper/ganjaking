<?php
/**
 * Product Add-ons cart
 *
 * @package WC_Product_Addons/Classes/Legacy/Cart
 * @since   2.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product_Addon_Cart_Legacy class.
 */
class Product_Addon_Cart_Legacy extends Product_Addon_Cart {

	/**
	 * Constructor.
	 */
	function __construct() {
		parent::__construct();

		// Add meta to order.
		add_action( 'woocommerce_add_order_item_meta', array( $this, 'order_item_meta' ), 10, 2 );
	}

	/**
	 * add_cart_item function.
	 *
	 * @param array $cart_item
	 *
	 * @return array
	 */
	public function add_cart_item( $cart_item ) {
		// Adjust price if addons are set
		if ( ! empty( $cart_item['addons'] ) && apply_filters( 'woocommerce_product_addons_adjust_price', true, $cart_item ) ) {

			$extra_cost = 0;

			foreach ( $cart_item['addons'] as $addon ) {
				if ( $addon['price'] > 0 ) {
					$extra_cost += $addon['price'];
				}
			}

			$cart_item['data']->adjust_price( $extra_cost );
		}

		return $cart_item;
	}

	/**
	 * Add an error
	 */
	public function add_error( $error ) {
		wc_add_notice( $error, 'error' );
	}

	/**
	 * Add meta to orders.
	 *
	 * @param int $item_id
	 * @param array $values
	 */
	public function order_item_meta( $item_id, $values ) {
		if ( ! empty( $values['addons'] ) ) {
			foreach ( $values['addons'] as $addon ) {

				$name = $addon['name'];

				if ( $addon['price'] > 0 && apply_filters( 'woocommerce_addons_add_price_to_name', true ) ) {
					$name .= ' (' . strip_tags( wc_price( WC_Product_Addons_Helper::get_product_addon_price_for_display ( $addon['price'], $values[ 'data' ], true ) ) ) . ')';
				}

				wc_add_order_item_meta( $item_id, $name, $addon['value'] );
			}
		}
	}
}
