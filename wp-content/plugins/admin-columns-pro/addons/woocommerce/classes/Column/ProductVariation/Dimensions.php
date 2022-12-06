<?php

namespace ACA\WC\Column\ProductVariation;

use ACA\WC\Column;

/**
 * @since 3.0
 */
class Dimensions extends Column\Product\Dimensions {

	public function __construct() {
		parent::__construct();
		$this->set_type( 'column-wc-variation_dimensions' );
	}

}