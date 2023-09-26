<?php declare( strict_types=1 );

namespace ACP\Expression;

final class OrSpecification implements Specification {

	use SpecificationTrait;

	private $left;

	private $right;

	public function __construct( Specification $left, Specification $right ) {
		$this->left = $left;
		$this->right = $right;
	}

	public function is_satisfied_by( string $value ): bool {
		return $this->left->is_satisfied_by( $value ) || $this->right->is_satisfied_by( $value );
	}

}