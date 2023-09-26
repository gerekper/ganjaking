<?php declare( strict_types=1 );

namespace ACP\ConditionalFormat\Type;

final class Format {

	private $class;

	public function __construct( string $class ) {
		$this->class = $class;
	}

	public function __toString(): string {
		return $this->class;
	}

}