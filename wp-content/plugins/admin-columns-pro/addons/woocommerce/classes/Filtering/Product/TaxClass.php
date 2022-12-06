<?php

namespace ACA\WC\Filtering\Product;

use ACA\WC\Column;
use ACP;

/**
 * @property Column\Product\TaxClass $column
 */
class TaxClass extends ACP\Filtering\Model\Meta {

	public function __construct( Column\Product\TaxClass $column ) {
		parent::__construct( $column );
	}

	public function get_filtering_data() {
		return [
			'empty_option' => true,
			'options'      => $this->column->get_tax_classes(),
		];
	}

}