<?php
declare( strict_types=1 );

namespace ACP\Type\HideOnScreen;

use LogicException;

class Group {

	public const ELEMENT = 'element';
	public const FEATURE = 'feature';

	private $group;

	public function __construct( string $group ) {
		$this->group = $group;

		$this->validate();
	}

	public function get_value(): string {
		return $this->group;
	}

	private function validate(): void {
		if ( ! in_array( $this->group, [ self::ELEMENT, self::FEATURE ], true ) ) {
			throw new LogicException( 'Invalid group.' );
		}
	}

	public function equals( Group $group ): bool {
		return (string) $group === $this->group;
	}

	public function __toString(): string {
		return $this->group;
	}

}