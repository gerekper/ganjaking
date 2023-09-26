<?php
/**
 * Cart.
 *
 * @since 1.3.0
 */

namespace KoiLab\WC_Force_Sells;

use KoiLab\WC_Force_Sells\Utilities\Product_Utils;

defined( 'ABSPATH' ) || exit;

/**
 * Cart class.
 */
class Cart {

	/**
	 * Cart init.
	 *
	 * @since 1.3.0
	 */
	public static function init() {
		add_action( 'woocommerce_ordered_again', array( __CLASS__, 'ordered_again' ), 10, 3 );
	}

	/**
	 * Makes synced products irremovable.
	 *
	 * @since 1.3.0
	 *
	 * @param int                      $order_id    Order ID.
	 * @param \WC_Order_Item_Product[] $order_items Order items.
	 * @param array                    $cart        The cart content.
	 */
	public static function ordered_again( $order_id, $order_items, &$cart ) {
		foreach ( $cart as $key => $item ) {
			// Check if this product is forced in itself, so it can't force in others (to prevent adding in loops).
			if ( isset( $item['forced_by'], $cart[ $item['forced_by'] ] ) ) {
				continue;
			}

			// Check if this product is already forcing a cart item. If so, we don't need to handle add to cart logic because qty will be updated by update_force_sell_quantity_in_cart.
			foreach ( $cart as $value ) {
				if ( isset( $value['forced_by'] ) && $key === $value['forced_by'] ) {
					break;
				}
			}

			$synced_ids = Product_Utils::get_valid_force_sells( $item['product_id'], 'synced' );

			if ( ! $synced_ids ) {
				continue;
			}

			foreach ( $cart as $s_key => $s_item ) {
				if ( in_array( $s_item['product_id'], $synced_ids, true ) ) {
					$cart[ $s_key ]['forced_by'] = $key;
				}
			}
		}
	}
}
