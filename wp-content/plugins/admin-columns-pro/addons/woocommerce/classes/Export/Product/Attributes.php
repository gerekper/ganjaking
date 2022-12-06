<?php

namespace ACA\WC\Export\Product;

use ACA\WC\Column;
use ACP;

/**
 * @since 3.0
 * @property Column\Product\Attributes $column
 */
class Attributes extends ACP\Export\Model {

	public function __construct( Column\Product\Attributes $column ) {
		parent::__construct( $column );
	}

	/**
	 * @return string
	 */
	private function get_delimiter() {
		return defined( 'WC_DELIMITER' ) && WC_DELIMITER ? WC_DELIMITER : ' | ';
	}

	/**
	 * @param int $id
	 *
	 * @return string
	 */
	public function get_value( $id ) {
		$values = [];

		foreach ( $this->column->get_raw_value( $id ) as $name => $attribute ) {
			$options = $attribute->get_options();

			if ( $attribute->is_taxonomy() ) {
				$options = wc_get_product_terms( $id, $name, [ 'fields' => 'names' ] );
			}

			$value = implode( ' ' . $this->get_delimiter() . ' ', $options );

			if ( ! $this->column->get_attribute() ) {
				$value = wc_attribute_label( $name ) . ': ' . $value;
			}

			$values[] = $value;
		}

		return implode( ', ', $values );
	}

}