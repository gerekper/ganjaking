<?php
declare( strict_types=1 );

namespace ACA\WC\Helper\Select\Product;

use WC_Product;

interface LabelFormatter {

	public function format_label( WC_Product $product ): string;

	public function format_label_unique( WC_Product $product ): string;

}