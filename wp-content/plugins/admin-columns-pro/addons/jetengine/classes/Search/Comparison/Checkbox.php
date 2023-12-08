<?php

namespace ACA\JetEngine\Search\Comparison;

use AC\Helper\Select\Options;
use ACP;
use ACP\Search\Operators;
use ACP\Search\Value;

class Checkbox extends ACP\Search\Comparison\Meta implements ACP\Search\Comparison\Values {

	/**
	 * @var array
	 */
	private $choices;

	/**
	 * @var bool
	 */
	private $value_is_array;

	public function __construct( $meta_key, array $choices, $value_is_array ) {
		parent::__construct( new Operators( [
			Operators::EQ,
			Operators::NEQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] ), $meta_key );

		$this->choices = $choices;
		$this->value_is_array = (bool) $value_is_array;
	}

	private function map_default_value( $operator, $value ) {
		$serialized_value = serialize( $value->get_value() ) . serialize( $operator === Operators::EQ ? 'true' : 'false' );

		return new Value( $serialized_value, $value->get_type() );
	}

	private function map_array_value( Value $value ) {
		return new Value( serialize( $value->get_value() ), $value->get_type() );
	}

	/**
	 * @param string $operator
	 * @param Value  $value
	 *
	 * @return array
	 */
	protected function get_meta_query( string $operator, Value $value ): array {
		$operators = [
			Operators::EQ  => 'LIKE',
			Operators::NEQ => 'LIKE',
		];

		if ( array_key_exists( $operator, $operators ) ) {
			$mapped_value = $this->value_is_array ? $this->map_array_value( $value ) : $this->map_default_value( $operator, $value );
			$comparison = new ACP\Search\Helper\MetaQuery\Comparison( $this->get_meta_key(), $operators[ $operator ], $mapped_value );

			return $comparison();
		}

		$comparison = ACP\Search\Helper\MetaQuery\ComparisonFactory::create( $this->get_meta_key(), $operator, $value );

		return $comparison();
	}

	public function get_values(): Options {
		return Options::create_from_array( $this->choices );
	}

}