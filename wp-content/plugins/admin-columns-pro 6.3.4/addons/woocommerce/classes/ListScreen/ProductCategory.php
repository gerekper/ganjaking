<?php

namespace ACA\WC\ListScreen;

use ACA\WC\Column;
use ACP;

class ProductCategory extends ACP\ListScreen\Taxonomy {

	public function __construct() {
		parent::__construct( 'product_cat' );

        $this->group = 'woocommerce';
	}

	protected function register_column_types(): void
    {
		parent::register_column_types();

		$this->register_column_type( new Column\ProductCategory\Image() );
	}

}