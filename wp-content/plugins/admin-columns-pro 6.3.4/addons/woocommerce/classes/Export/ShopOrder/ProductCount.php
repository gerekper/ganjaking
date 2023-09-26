<?php

namespace ACA\WC\Export\ShopOrder;

use ACP;
use WC_Order_Item_Product;

class ProductCount implements ACP\Export\Service {

	public function get_value( $id ) {
		$order = wc_get_order( $id );
		$order_items = $order->get_items();

		if ( 0 === count( $order_items ) ) {
			return '';
		}

		$result = [];

		foreach ( $order_items as $item ) {
			if ( ! $item instanceof WC_Order_Item_Product ) {
				continue;
			}

			if ( ! $item->get_quantity() ) {
				continue;
			}

			$quantity = absint( $item->get_quantity() );
			$product = $item->get_product();
			$name = $product ? $product->get_name() : $item->get_name();
			if ( $product && wc_product_sku_enabled() && $product->get_sku() ) {
				$name .= '(' . $product->get_sku() . ')';
			}

			$result[] = sprintf( '%sx %s', $quantity, $name );
		}

		return implode( ', ', $result );
	}

}