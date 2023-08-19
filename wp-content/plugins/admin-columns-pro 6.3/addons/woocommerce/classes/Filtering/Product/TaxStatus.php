<?php

namespace ACA\WC\Filtering\Product;

use ACA\WC\Column;
use ACP;

/**
 * @property Column\Product\TaxStatus $column
 */
class TaxStatus extends ACP\Filtering\Model\Meta {

	public function __construct( Column\Product\TaxStatus $column ) {
		parent::__construct( $column );
	}

	public function get_filtering_data() {
		return [
			'empty_option' => false,
			'options'      => (array) $this->column->get_tax_status(),
		];
	}

}