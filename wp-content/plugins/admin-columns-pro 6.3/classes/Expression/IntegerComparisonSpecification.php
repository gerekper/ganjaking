<?php declare( strict_types=1 );

namespace ACP\Expression;

class IntegerComparisonSpecification implements Specification {

	use SpecificationTrait;
	use ComparisonTrait;

	protected $fact;

	public function __construct( int $fact, string $operator ) {
		$this->fact = $fact;
		$this->operator = $operator;

		$this->validate_operator();
	}

	public function is_satisfied_by( string $value ): bool {
		return $this->compare( $this->operator, $this->fact, (int) $value );
	}

}