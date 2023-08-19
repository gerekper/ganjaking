<?php

namespace ACA\MetaBox\Value;

use ACA\MetaBox\Column;

class ValueFormatterFactory {

	public function create( Column $column, array $field ): Formatter {

		switch ( $field['type'] ) {
			case 'color':

				return new Formatter\Color();
			case 'checkbox':
			case 'switch':
				return new Formatter\Checkbox();
			case 'date':
			case 'datetime':
				if ( $field['timestamp'] ) {
					return new Formatter\FormattedDate();
				}

				return new Formatter\SettingFormatter( $column );
			case 'file':
			case 'file_advanced':
			case 'file_upload':
				return new Formatter\File();
			case 'select':
			case 'select_advanced':
			case 'radio':

				return new Formatter\Select( $field );
			default:
				return new Formatter\SettingFormatter( $column );
		}

	}

}