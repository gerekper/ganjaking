<?php

namespace ACP\Search\Comparison\Meta;

use ACP\Search\Operators;

class Toggle extends Select {

	public function __construct( $meta_key, $meta_type, $choices ) {
		parent::__construct( $meta_key, $meta_type, $choices );

		$this->operators = new Operators( [ Operators::EQ ] );
	}

}