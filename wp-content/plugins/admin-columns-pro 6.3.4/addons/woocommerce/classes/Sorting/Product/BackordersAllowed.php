<?php

namespace ACA\WC\Sorting\Product;

use ACP;

class BackordersAllowed extends ACP\Sorting\Model\Post\Meta {

	public function __construct() {
		parent::__construct( '_backorders' );
	}

}