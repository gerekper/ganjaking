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

	private $choices;

	public function __construct( string $meta_key, array $choices ) {
		parent::__construct( new Operators( [
			Operators::EQ,
			Operators::NEQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] ), $meta_key );

		$this->choices = $choices;
	}

	protected function get_meta_query( string $operator, Value $value ): array {
		$comparison = SerializedComparisonFactory::create(
			$this->meta_key,
			$operator,
			$value
		);

		return $comparison();
	}

	public function get_values(): Options {
		return Options::create_from_array( $this->choices );
	}

}