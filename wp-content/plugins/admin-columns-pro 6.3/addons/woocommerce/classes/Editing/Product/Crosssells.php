<?php

namespace ACA\WC\Editing\Product;

use ACA\WC\Editing\ProductRelations;
use ACA\WC\Editing\Storage;

class Crosssells extends ProductRelations {

	public function __construct() {
		parent::__construct( new Storage\Product\CrossSells() );
	}

}