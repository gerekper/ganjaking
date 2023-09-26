<?php

namespace ACA\GravityForms\Search\Comparison\Entry;

use AC\Helper\Select\Options;
use ACA\GravityForms\Search;
use ACP;

class Choice extends Search\Comparison\Entry implements ACP\Search\Comparison\Values {

	/**
	 * @var array
	 */
	private $choices;

	public function __construct( $field, array $choices ) {
		$operators = new ACP\Search\Operators( [
			ACP\Search\Operators::EQ,
			ACP\Search\Operators::NEQ,
		] );

		parent::__construct( $field, $operators, ACP\Search\Value::STRING );

		$this->choices = $choices;
	}

	public function get_values() {
		return Options::create_from_array( $this->choices );
	}

}