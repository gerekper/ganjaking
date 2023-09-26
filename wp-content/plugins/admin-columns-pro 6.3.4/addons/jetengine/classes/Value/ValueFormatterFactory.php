<?php

namespace ACA\JetEngine\Value;

use ACA\JetEngine\Column\Meta;
use ACA\JetEngine\Field\Field;
use ACA\JetEngine\Field\Type;

class ValueFormatterFactory {

	public function create( Meta $column, Field $field ): ValueFormatter {

		switch ( true ) {
			case $field instanceof Type\DateTime:
				return new Format\DateTime( $column, $field );
			case $field instanceof Type\Date:
				return new Format\Date( $column, $field );
			case $field instanceof Type\Gallery:
				return new Format\Gallery( $column, $field );
			case $field instanceof Type\Checkbox:
				return new Format\Checkbox( $column, $field );
			case $field instanceof Type\ColorPicker:
				return new Format\Color( $column, $field );
			case $field instanceof Type\Media:
				return new Format\Media( $column, $field );
			case $field instanceof Type\Switcher:
				return new Format\Switcher( $column, $field );
			case $field instanceof Type\Posts:
				return new Format\Posts( $column, $field );
			case $field instanceof Type\Radio:
				return new Format\Options( $column, $field );
			case $field instanceof Type\Select:
				return $field->is_multiple()
					? new Format\MultipleOptions( $column, $field )
					: new Format\Options( $column, $field );
			default:
				return new Format\DefaultFormatter( $column, $field );
		}

	}

}