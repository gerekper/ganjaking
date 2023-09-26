<?php declare( strict_types=1 );

namespace ACP\Expression;

use AC\Helper\Strings;

class EndsWithSpecification implements Specification {

	use SpecificationTrait;

	private $fact;

	public function __construct( string $fact ) {
		$this->fact = $fact;
	}

	public function is_satisfied_by( string $value ): bool {
		return $this->fact !== '' && ( new Strings() )->ends_with( $value, $this->fact );
	}

}