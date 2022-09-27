<?php
/**
 * Class responsible for handling ajax calls on the admin for Pre-Orders
 *
 * WooCommerce Pre-Orders
 *
 * @package   WC_Pre_Orders/Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Pre-Orders Admin class.
 */
class WC_Pre_Orders_Admin_Ajax {

	public function __construct() {
		//Adds validation to make sure only one pre-order product is added to order
		add_action( 'woocommerce_ajax_add_order_item_validation', array( $this, 'can_add_product_to_order' ), 10, 4 );

		//Adds fees to pre-order items when creating order from admin
		add_action( 'woocommerce_ajax_order_items_added', array( $this, 'maybe_add_pre_order_fee_admin' ), 10, 2 );

		//Remove fees from order when removing a pre-order item from admin
		add_action( 'woocommerce_before_delete_order_item', array( $this, 'maybe_remove_pre_order_fee_admin' ), 10, 1 );

		//Remove fees from order when removing a pre-order item from admin
		add_action( 'woocommerce_order_before_calculate_totals', array( $this, 'maybe_adjust_pre_order_fee_admin' ), 10, 2 );
	}

	/**
	 * Adds validation to make sure only one pre-order product is added to an order
	 * @param WP_Error $validation_error
	 * @param WC_Product $product
	 * @param WC_Order $order
	 * @param int $qty
	 *
	 * @return WP_Error
	 */
	public function can_add_product_to_order( $validation_error, $product, $order, $qty ) {
		$items                      = $order->get_items();
		$is_added_product_pre_order = WC_Pre_Orders_Product::product_can_be_pre_ordered( $product );

		foreach ( $items as $item ) {
			if ( ! WC_Pre_Orders_Product::product_can_be_pre_ordered( $item->get_product() ) && ! $is_added_product_pre_order ) {
				continue;
			}

			if ( $item->get_product()->get_id() === $product->get_id() ) {
				$validation_error->add( 'multiple-pre-order-products', __( "You can't add multiple products on a pre-order. Change the quantity of the item instead of adding more items.", 'wc-pre-orders' ) );
				break;
			}

			if ( $item->get_product()->get_id() !== $product->get_id() ) {
				$validation_error->add( 'multiple-pre-order-products', __( "You can't add multiple products on a pre-order", 'wc-pre-orders' ) );
				break;
			}
		}

		return $validation_error;
	}

	/**
	 * Add pre-order fee when a pre-order product is added
	 *
	 * @param WC_Order_Item[] $added_items
	 * @param WC_Order $order
	 *
	 * @since 1.6.0
	 */
	public function maybe_add_pre_order_fee_admin( $added_items, $order ) {
		$wc_pre_order_cart = new WC_Pre_Orders_Cart();

		foreach ( $added_items as $item_id => $item ) {
			$fee = $wc_pre_order_cart->generate_fee( $item->get_product() );

			if ( ! $fee ) {
				continue;
			}

			$item_fee = new WC_Order_Item_Fee();
			$item_fee->set_name( $fee['label'] );
			$item_fee->set_tax_status( $fee['tax_status'] );
			$item_fee->set_total( $fee['amount'] * $item->get_quantity() );
			$item_fee->add_meta_data( 'pre_order_parent_item_id', $item_id, true );
			$item_fee->save();

			$order->add_item( $item_fee );
		}

		$order->save();
	}

	/**
	 * Removes pre-order fees from the order when the pre-order product is removed
	 *
	 * @param int $item_id
	 *
	 * @since 1.6.0
	 */
	public function maybe_remove_pre_order_fee_admin( $item_id ) {

		$item = WC_Order_Factory::get_order_item( absint( $item_id ) );

		if ( ! $item || 'line_item' !== $item->get_type() || ! WC_Pre_Orders_Product::product_can_be_pre_ordered( $item->get_product() ) ) {
			return;
		}

		$order = $item->get_order();
		$fees  = $order->get_fees();

		foreach ( $fees as $fee_id => $fee ) {
			if ( $item_id === (int) $fee->get_meta( 'pre_order_parent_item_id', true ) ) {
				$order->remove_item( $fee_id );
				$order->save();

				return;
			}
		}
	}

	/**
	 * Adjusts pre-order fees when product quantity changes
	 *
	 * @param bool $and_taxes
	 * @param WC_Order $order
	 */
	public function maybe_adjust_pre_order_fee_admin( $and_taxes, $order ) {

		$items = $order->get_items();

		foreach ( $items as $item ) {
			if ( WC_Pre_Orders_Product::product_can_be_pre_ordered( $item->get_product() ) ) {
				foreach ( $order->get_fees() as $item_fee ) {
					if ( $item->get_id() === (int) $item_fee->get_meta( 'pre_order_parent_item_id' ) ) {
						$wc_pre_order_cart = new WC_Pre_Orders_Cart();
						$fee               = $wc_pre_order_cart->generate_fee( $item->get_product() );

						$item_fee->set_total( $fee['amount'] * $item->get_quantity() );
						break;
					}
				}
			}
		}
	}
}


new WC_Pre_Orders_Admin_Ajax();
