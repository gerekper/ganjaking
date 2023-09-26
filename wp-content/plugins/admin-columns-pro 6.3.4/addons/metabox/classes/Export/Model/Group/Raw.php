<?php

namespace ACA\MetaBox\Export\Model\Group;

use ACA\MetaBox\Column;
use ACP;

class Raw implements ACP\Export\Service {

	private $column;

	public function __construct( Column\Group $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$value = $this->column->get_raw_value( $id );

		if ( empty( $value ) ) {
			return '';
		}

		if ( $this->column->is_clonable() ) {
			$new_value = [];

			foreach ( (array) $value as $single_value ) {
				$new_value[] = $this->get_single_value( $single_value );
			}

			return implode( ' | ', $new_value );
		}

		return $this->get_single_value( $value );
	}

	public function get_single_value( $value ): string {
		$value = $value[ $this->column->get_sub_field() ] ?: '';

		return is_array( $value )
			? implode( ', ', $value )
			: (string) $value;
	}

}