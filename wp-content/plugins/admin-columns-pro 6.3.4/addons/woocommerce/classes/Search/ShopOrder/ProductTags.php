<?php

namespace ACA\WC\Search\ShopOrder;

class ProductTags extends ProductTaxonomy {

	public function __construct() {
		parent::__construct( 'product_tag' );
	}

}