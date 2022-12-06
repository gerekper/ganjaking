<?php

namespace ACA\MetaBox\Export\Model;

use AC;
use ACA\MetaBox\Column;
use ACP;

/**
 * @property Column $column
 */
class Raw extends ACP\Export\Model\RawValue {

	public function __construct( Column $column ) {
		parent::__construct( $column );
	}

	public function get_value( $id ) {
		if ( $this->column->is_clonable() ) {
			return $this->get_multiple_values( $id );
		}

		return $this->format_single_value( rwmb_get_value( $this->column->get_meta_key(), [ 'object_type' => $this->column->get_meta_type() ], $id ), $id );
	}

	public function format_single_value( $value, $id = null ) {
		if ( ! $value ) {
			return '';
		}

		return $value;
	}

	public function get_multiple_values( $id ) {
		$value = rwmb_get_value( $this->column->get_meta_key(), [ 'object_type' => $this->column->get_meta_type() ], $id );

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