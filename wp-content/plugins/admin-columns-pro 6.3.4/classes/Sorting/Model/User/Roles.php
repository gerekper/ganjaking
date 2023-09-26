<?php

namespace ACP\Sorting\Model\User;

use ACP\Sorting\FormatValue;

class Roles extends MetaFormat {

	public function __construct( $meta_key ) {
		parent::__construct( new FormatValue\Roles(), $meta_key );
	}

}