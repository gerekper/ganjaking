<?php
/**
 * Class to handle the Store Credit products.
 *
 * @package WC_Store_Credit/Classes
 * @since   3.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Store_Credit_Products class.
 */
class WC_Store_Credit_Products {

	/**
	 * Constructor.
	 *
	 * @since 3.2.0
	 */
	public function __construct() {
		add_filter( 'product_type_selector', array( $this, 'product_type_selector' ) );
		add_filter( 'woocommerce_product_class', array( $this, 'product_class' ), 10, 2 );
		add_filter( 'woocommerce_order_item_needs_processing', array( $this, 'order_item_needs_processing' ), 10, 2 );
		add_action( 'woocommerce_store_credit_add_to_cart', 'woocommerce_simple_add_to_cart' );

		add_action( 'woocommerce_order_status_pending', array( $this, 'copy_coupon_data' ), 10, 2 );
		add_action( 'woocommerce_order_status_on-hold', array( $this, 'copy_coupon_data' ), 10, 2 );
		add_action( 'woocommerce_order_status_processing', array( $this, 'copy_coupon_data' ), 5, 2 );
		add_action( 'woocommerce_order_status_completed', array( $this, 'copy_coupon_data' ), 5, 2 );

		// Before sending the email with priority 10.
		add_action( 'woocommerce_order_status_processing', array( $this, 'process_purchased_credit' ), 7, 2 );
		add_action( 'woocommerce_order_status_completed', array( $this, 'process_purchased_credit' ), 7, 2 );
	}

	/**
	 * Registers custom product types.
	 *
	 * @since 3.2.0
	 *
	 * @param array $types The product types.
	 * @return array
	 */
	public function product_type_selector( $types ) {
		$types['store_credit'] = __( 'Store Credit', 'woocommerce-store-credit' );

		return $types;
	}

	/**
	 * Filters the product class.
	 *
	 * @since 3.2.0
	 *
	 * @param string $class The product class.
	 * @param string $type  The product type.
	 * @return mixed
	 */
	public function product_class( $class, $type ) {
		if ( 'store_credit' === $type ) {
			$class = 'WC_Store_Credit_Product';
		}

		return $class;
	}

	/**
	 * Filters if the item needs to be processed before completing the order.
	 *
	 * @since 3.2.0
	 *
	 * @param bool       $needs_processing Needs processing?.
	 * @param WC_Product $product          Product object.
	 * @return bool
	 */
	public function order_item_needs_processing( $needs_processing, $product ) {
		if ( $product->is_type( 'store_credit' ) ) {
			$needs_processing = false;
		}

		return $needs_processing;
	}

	/**
	 * Copy the Store Credit coupon data to the order items.
	 *
	 * Snapshot the data used to generate the Store Credit coupons from the Store Credit products.
	 *
	 * @since 3.2.0
	 *
	 * @param int      $order_id Order ID.
	 * @param WC_Order $order    Order object.
	 */
	public function copy_coupon_data( $order_id, $order ) {
		$items = $order->get_items();

		foreach ( $items as $item ) {
			if ( $item->get_meta( '_store_credit_data' ) ) {
				continue;
			}

			$product = $item->get_product();

			if ( ! $product || ! $product->is_type( 'store_credit' ) ) {
				continue;
			}

			$data = $product->get_meta( '_store_credit_data' );

			// Use the product price when it was purchased.
			if ( empty( $data['amount'] ) ) {
				$data['amount'] = $product->get_regular_price();
			}

			/**
			 * Filters the data used to generate the Store Credit coupon from a Store Credit product.
			 *
			 * @since 3.2.0
			 *
			 * @param array         $data Store Credit coupon data.
			 * @param WC_Order_Item $item Order item.
			 */
			$data = apply_filters( 'wc_store_credit_order_item_coupon_data', $data, $item );

			if ( ! empty( $data ) ) {
				$item->add_meta_data( '_store_credit_data', $data, true );
				$item->save_meta_data();
			}
		}
	}

	/**
	 * Processes the purchased Store Credit from an order.
	 *
	 * @since 3.2.0
	 *
	 * @param int      $order_id Order ID.
	 * @param WC_Order $order    Order object.
	 */
	public function process_purchased_credit( $order_id, $order ) {
		$items = $order->get_items();

		foreach ( $items as $item ) {
			if ( ! $item->get_meta( '_store_credit_data' ) ) {
				continue;
			}

			wc_store_credit_create_coupon_from_order_item( $item );
		}
	}
}

return new WC_Store_Credit_Products();
