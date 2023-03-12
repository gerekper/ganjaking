<?php

namespace ACA\WC\Export\Product;

use ACA\WC\Column;
use ACP;

class Attributes implements ACP\Export\Service {

	protected $column;

	public function __construct( Column\Product\Attributes $column ) {
		$this->column = $column;
	}

	private function get_delimiter(): string {
		return defined( 'WC_DELIMITER' ) && WC_DELIMITER
			? (string) WC_DELIMITER
			: ' | ';
	}

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