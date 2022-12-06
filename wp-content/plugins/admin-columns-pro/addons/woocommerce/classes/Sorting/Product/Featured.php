<?php

namespace ACA\WC\Sorting\Product;

use ACP;
use WC_Product_Data_Store_CPT;

class Featured extends ACP\Sorting\Model\Post\Featured {

	protected function get_featured_ids() {
		return array_keys( ( new WC_Product_Data_Store_CPT() )->get_featured_product_ids() );
	}

}