<?php declare( strict_types=1 );

namespace ACP\Expression;

interface Specification {

	public function is_satisfied_by( string $value ): bool;

	public function and_specification( Specification $specification ): self;

	public function or_specification( Specification $specification ): self;

	public function not(): self;

}