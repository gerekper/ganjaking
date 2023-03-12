<?php
declare( strict_types=1 );

namespace ACP\Export\Type;

use InvalidArgumentException;

class ColumnState {

	private $column_name;

	private $active;

	public function __construct( string $column_name, bool $active ) {
		$this->column_name = $column_name;
		$this->active = $active;

		$this->validate();
	}

	private function validate(): void {
		if ( '' === $this->column_name ) {
			throw new InvalidArgumentException( 'Empty column name' );
		}
	}

	public function get_column_name(): string {
		return $this->column_name;
	}

	public function is_active(): bool {
		return $this->active;
	}

}