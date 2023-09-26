<?php

namespace ACA\Types\Search;

use AC;
use ACP;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class Select extends ACP\Search\Comparison\Meta
	implements Comparison\Values {

	/** @var array options */
	private $options;

	public function __construct( $meta_key, $type, array $options ) {
		$this->options = $options;

		$operators = new Operators( [
			Operators::EQ,
			Operators::NEQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators, $meta_key, $type );
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( $this->options );
	}

}