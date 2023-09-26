<?php declare( strict_types=1 );

namespace ACP\Expression;

use ACP\Expression\Exception\OperatorNotFoundException;

trait ComparisonTrait {

	use OperatorTrait;

	protected function get_operators(): array {
		return [
			ComparisonOperators::EQUAL,
			ComparisonOperators::NOT_EQUAL,
			ComparisonOperators::LESS_THAN,
			ComparisonOperators::LESS_THAN_EQUAL,
			ComparisonOperators::GREATER_THAN,
			ComparisonOperators::GREATER_THAN_EQUAL,
		];
	}

	protected function compare( string $operator, $fact, $value ): bool {
		switch ( $operator ) {
			case ComparisonOperators::EQUAL:
				return $value === $fact;
			case ComparisonOperators::NOT_EQUAL:
				return $value !== $fact;
			case ComparisonOperators::GREATER_THAN:
				return $value > $fact;
			case ComparisonOperators::GREATER_THAN_EQUAL:
				return $value >= $fact;
			case ComparisonOperators::LESS_THAN:
				return $value < $fact;
			case ComparisonOperators::LESS_THAN_EQUAL:
				return $value <= $fact;
		}

		throw new OperatorNotFoundException( $operator );
	}

}