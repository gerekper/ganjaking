<?php

namespace ACA\ACF\Search\Comparison\Repeater;

use AC\Helper\Select\Options;
use ACA\ACF\Search\Comparison;
use ACP\Search\Comparison\Values;
use ACP\Search\Operators;

class Select extends Comparison\Repeater
	implements Values {

	/** @var array */
	private $choices;

	public function __construct( $meta_type, $parent_key, $sub_key, $choices, $multiple = false ) {
		$operators = new Operators( [
			Operators::EQ,
		] );

		$this->choices = $choices;

		parent::__construct( $meta_type, $parent_key, $sub_key, $operators, null, $multiple );
	}

	public function get_values() {
		return Options::create_from_array( $this->choices );
	}

}