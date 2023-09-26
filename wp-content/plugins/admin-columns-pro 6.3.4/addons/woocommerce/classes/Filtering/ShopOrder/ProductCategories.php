<?php

namespace ACA\WC\Filtering\ShopOrder;

use AC;
use ACA\WC\Column;

/**
 * @property Column\ShopOrder\ProductCategories $column
 */
class ProductCategories extends ProductTaxonomy {

	public function __construct( AC\Column $column ) {
		parent::__construct( $column, 'product_cat' );
	}

}


