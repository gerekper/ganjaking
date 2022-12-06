<?php

namespace ACA\WC\Export\Product;

use ACA\WC\Column;
use ACP;

/**
 * @since 3.0
 * @property Column\Product\Sale $column
 */
class Sale extends ACP\Export\Model {

	public function __construct( Column\Product\Sale $column ) {
		parent::__construct( $column );
	}

	public function get_value( $id ) {
		$product = wc_get_product( $id );

		if ( $this->column->is_scheduled( $product ) ) {
			$date_from = $product->get_date_on_sale_from( 'edit' ) ? $product->get_date_on_sale_from( 'edit' )->format( 'Y-m-d' ) : null;
			$date_to = $product->get_date_on_sale_to( 'edit' ) ? $product->get_date_on_sale_to( 'edit' )->format( 'Y-m-d' ) : null;

			if ( $date_from && $date_to ) {
				return sprintf( '%s / %s', $date_from, $date_to );
			}

			if ( $date_from ) {
				return _x( 'From', 'Product on sale from (date)', 'codepress-admin-columns' ) . ' ' . $date_from;
			}

			if ( $date_to ) {
				return _x( 'Until', 'Product on sale from (date)', 'codepress-admin-columns' ) . ' ' . $date_to;
			}
		}

		return $product->is_on_sale( 'edit' );
	}

}