<?php

namespace ACA\WC\Export\Product;

use ACA\WC\Column;
use ACP;

/**
 * @since 3.0
 * @property Column\Product\Downloads $column
 */
class Downloads extends ACP\Export\Model {

	public function get_value( $id ) {
		$values = [];

		foreach ( $this->column->get_raw_value( $id ) as $product_id => $download ) {
			$values[] = $download->get_file();
		}

		return implode( $this->column->get_separator(), $values );
	}

}