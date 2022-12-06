<?php

namespace ACA\WC\Column\ShopOrder;

use ACA\WC\Search;
use ACA\WC\Sorting;

/**
 * @since 3.1
 */
class ProductCategories extends ProductTaxonomy {

	public function __construct() {
		parent::__construct();

		$this->set_type( 'column-wc-product_categories' )
		     ->set_label( __( 'Product Categories', 'codepress-admin-columns' ) );
	}

	public function get_taxonomy() {
		return 'product_cat';
	}

	public function search() {
		return new Search\ShopOrder\ProductCategories();
	}

}