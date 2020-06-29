<?php

/**
 * Class WC_Helper_Order.
 *
 * This helper class should ONLY be used for unit tests!.
 */
class WC_Helper_Order {

	/**
	 * Delete a product.
	 *
	 * @param int $order_id ID of the order to delete.
	 */
	public static function delete_order( $order_id ) {

		$order = wc_get_order( $order_id );

		// Delete all products in the order.
		foreach ( $order->get_items() as $item ) {
			WC_Helper_Product::delete_product( $item['product_id'] );
		}

		WC_Helper_Shipping::delete_simple_flat_rate();

		// Delete the order post.
		$order->delete( true );
	}

	/**
	 * Create a order.
	 *
	 * @since   2.4
	 * @version 3.0 New parameter $product.
	 *
	 * @param int        $customer_id
	 * @param WC_Product $product
	 *
	 * @return WC_Order
	 */
	public static function create_order( $gift_cards_instances ) {

        $address = array(
            'first_name' => '111Joe',
            'last_name'  => 'Conlin',
            'company'    => 'Speed Society',
            'email'      => 'joe@testing.com',
            'phone'      => '760-555-1212',
            'address_1'  => '123 Main st.',
            'address_2'  => '104',
            'city'       => 'San Diego',
            'state'      => 'Ca',
            'postcode'   => '92121',
            'country'    => 'US'
        );

        // Now we create the order
        $order = wc_create_order();


        // The add_product() function below is located in /plugins/woocommerce/includes/abstracts/abstract_wc_order.php
        foreach ( $gift_cards_instances as $gift_cards_instance )
            foreach ( $gift_cards_instance[ 'instances' ] as $instance ){
                $order->add_product( $gift_cards_instance[ 'gift_card_product' ], $instance[ 'quantity' ] );
            }


        $order->set_address( $address, 'billing' );

        $order->calculate_totals();

		return $order;
	}
}
