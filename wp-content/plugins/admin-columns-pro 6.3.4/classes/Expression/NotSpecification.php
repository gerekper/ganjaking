<?php declare( strict_types=1 );

namespace ACP\Expression;

final class NotSpecification implements Specification {

	use SpecificationTrait;

	private $specification;

	public function __construct( Specification $specification ) {
		$this->specification = $specification;
	}

	public function is_satisfied_by( string $value ): bool {
		return ! $this->specification->is_satisfied_by( $value );
	}

}