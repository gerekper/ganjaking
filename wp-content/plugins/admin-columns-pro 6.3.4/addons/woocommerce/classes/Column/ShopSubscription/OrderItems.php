<?php

namespace ACA\WC\Column\ShopSubscription;

use AC;
use ACA\WC\Search;
use ACP;
use WC_Order_Item_Product;

/**
 * @since 3.4
 */
class OrderItems extends AC\Column
	implements ACP\Search\Searchable, ACP\Export\Exportable {

	public function __construct() {
		$this->set_type( 'order_items' )
		     ->set_original( true );
	}

	public function search() {
		return new Search\ShopOrder\Product( $this->get_post_type() );
	}

	public function get_value( $id ) {
		return null;
	}

	public function get_raw_value( $id ) {
		$subscription = wcs_get_subscription( $id );

		$values = [];

		foreach ( $subscription->get_items() as $item ) {
			if ( ! $item instanceof WC_Order_Item_Product ) {
				continue;
			}

			$data = $item->get_data();
			$quantity = isset( $data['quantity'] ) ? $data['quantity'] : null;

			$value = sprintf( '%s (%d)', $item->get_name(), $item->get_product_id() );

			if ( $quantity && $quantity > 1 ) {
				$value = sprintf( '%sx %s', $quantity, $value );
			}

			$values[] = $value;
		}

		return implode( ', ', $values );
	}

	public function export() {
		return new ACP\Export\Model\RawValue( $this );
	}

}