<?php

namespace ACA\WC\Editing\ProductVariation;

use ACA\WC\Editing;

class Stock extends Editing\Product\Stock {

	public function is_editable( int $id ): bool {
		return true;
	}

}