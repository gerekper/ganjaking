<?php

namespace ACP\Search\Helper\TaxQuery;

use ACP\Search\Operators;
use ACP\Search\Value;
use LogicException;

final class ComparisonFactory {

	/**
	 * @param string $taxonomy
	 * @param string $operator
	 * @param Value  $terms
	 * @param string $field
	 *
	 * @return Comparison
	 */
	public static function create( $taxonomy, $operator, Value $terms, $field = 'term_id' ) {
		$operators = [
			Operators::EQ           => 'IN',
			Operators::NEQ          => 'NOT IN',
			Operators::IS_EMPTY     => 'NOT EXISTS',
			Operators::NOT_IS_EMPTY => 'EXISTS',
		];

		if ( ! array_key_exists( $operator, $operators ) ) {
			throw new LogicException( 'Invalid operator found.' );
		}

		return new Comparison( $taxonomy, $operators[ $operator ], $terms, $field );
	}

}