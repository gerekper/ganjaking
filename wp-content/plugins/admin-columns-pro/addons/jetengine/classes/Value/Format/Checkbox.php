<?php

namespace ACA\JetEngine\Value\Format;

use ACA\JetEngine\Utils\FieldOptions;
use ACA\JetEngine\Value\Formatter;

class Checkbox extends Formatter {

	public function format( $raw_value ): ?string {
		if( ! $this->field instanceof \ACA\JetEngine\Field\Type\Checkbox ){
			return $raw_value;
		}

		$options = $this->field->get_options();

		$result = array_map( static function ( $key ) use ( $options ) {
			return $options[ $key ] ?? null;
		}, $this->field->value_is_array() ? $raw_value : FieldOptions::get_checked_options( $raw_value ) );

		if( empty( $result ) ){
			return $this->column->get_empty_char();
		}

		$setting_limit = $this->column->get_setting( 'number_of_items' );

		return empty( $result ) ?
			$this->column->get_empty_char() :
			ac_helper()->html->more( array_filter( $result ), $setting_limit ? $setting_limit->get_value() : false );
	}

}