<?php

namespace ACP\Search\Comparison\Meta;

use AC\Helper\Select\Options;
use ACP\Search\Comparison;
use ACP\Search\Comparison\Values;
use ACP\Search\Operators;

class Select extends Comparison\Meta
	implements Values {

	/**
	 * @var array
	 */
	private $choices;

	public function __construct( $meta_key, $meta_type, $choices ) {
		parent::__construct( new Operators( [
			Operators::EQ,
			Operators::NEQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] ), $meta_key, $meta_type );

		$this->choices = $choices;
	}

	public function get_values() {
		return Options::create_from_array( $this->choices );
	}

}
