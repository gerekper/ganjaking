<?php

namespace ACA\MetaBox\Search\Comparison\Table;

use AC\Helper\Select\Options;
use ACP;

class Select extends TableStorage implements ACP\Search\Comparison\Values {

	/**
	 * @var array
	 */
	protected $choices;

	public function __construct( $operators, $table, $column, $choices ) {
		$this->choices = $choices;

		parent::__construct( $operators, $table, $column );
	}

	public function get_values(): Options {
		$options = empty( $this->choices ) ? [] : $this->choices;

		return Options::create_from_array( $options );
	}

}