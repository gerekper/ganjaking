<?php

namespace ACA\WC\Column\ProductVariation;

use ACA\WC\Column;
use ACA\WC\Editing;
use ACP;

/**
 * @since 3.0
 */
class Weight extends Column\Product\Weight {

	public function __construct() {
		parent::__construct();

		$this->set_type( 'column-wc-variation_weight' );
	}

	public function editing() {
		return new Editing\ProductVariation\Weight();
	}

	public function filtering() {
		return new ACP\Filtering\Model\Disabled( $this );
	}

}