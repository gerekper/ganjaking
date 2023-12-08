<?php
declare( strict_types=1 );

namespace ACA\WC\Helper\Select\Product;

use WC_Product;

interface GroupFormatter {

	public function format( WC_Product $product ): string;

}