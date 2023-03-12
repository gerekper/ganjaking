<?php

namespace ACA\MetaBox\Export\Model;

use AC;
use ACA\MetaBox\Column;
use ACP;

class Raw implements ACP\Export\Service {

	protected $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		if ( $this->column->is_clonable() ) {
			return (string) $this->get_multiple_values( $id );
		}

		$single_value = $this->get_rmwb_value( (int) $id );

		return $this->format_single_value( $single_value, $id );
	}

	private function get_rmwb_value( int $id ) {
		return rwmb_get_value(
			$this->column->get_meta_key(),
			[
				'object_type' => $this->column->get_meta_type(),
			],
			$id
		);
	}

	public function format_single_value( $value, $id = null ) {
		return $value
			? (string) $value
			: '';
	}

	public function get_multiple_values( $id ) {
		$value = $this->get_rmwb_value( (int) $id );

		if ( ! $value ) {
			return null;
		}

		$collection = new AC\Collection( (array) $value );
		$result = [];

		foreach ( $collection as $value ) {
			$result[] = $this->format_single_value( $value );
		}

		return implode( "\r\n", $result );
	}

}