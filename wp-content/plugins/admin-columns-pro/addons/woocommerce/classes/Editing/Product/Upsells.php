<?php

namespace ACA\WC\Editing\Product;

use ACA\WC\Editing\ProductRelations;
use ACA\WC\Editing\Storage;

class Upsells extends ProductRelations {

	public function __construct() {
		parent::__construct( new Storage\Product\UpSells() );
	}

}