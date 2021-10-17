<?php

namespace ACP\Search\Comparison\Meta;

use AC\Helper\Select\Options;
use ACP\Search\Comparison;
use ACP\Search\Comparison\Values;
use ACP\Search\Helper\MetaQuery\SerializedComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class MultiSelect extends Comparison\Meta
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

	/**
	 * @param string $operator
	 * @param Value  $value
	 *
	 * @return array
	 */
	protected function get_meta_query( $operator, Value $value ) {
		$comparison = SerializedComparisonFactory::create(
			$this->get_meta_key(),
			$operator,
			$value
		);

		return $comparison();
	}

	public function get_values() {
		return Options::create_from_array( $this->choices );
	}

}