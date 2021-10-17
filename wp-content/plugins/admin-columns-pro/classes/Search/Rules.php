<?php

namespace ACP\Search;

use AC\TypedArrayIterator;

final class Rules extends TypedArrayIterator {

	public function __construct( array $array ) {
		parent::__construct( $array, __NAMESPACE__ . '\Rule' );
	}

	/**
	 * @return Rule
	 */
	public function current() {
		return parent::current();
	}

}