<?php

namespace ACA\ACF\Value;

use ACA\ACF\Column;
use ACA\ACF\Field;
use ACA\ACF\Settings;

class FormatterFactory {

	/**
	 * @param Column $column
	 * @param Field  $field
	 *
	 * @return Formatter
	 */
	public function create( Column $column, Field $field ) {
		switch ( true ) {
			case $field instanceof Field\Type\Color:
				return new Formatter\Color( $column, $field );
			case $field instanceof Field\Type\Radio:
			case $field instanceof Field\Type\ButtonGroup:
			case $field instanceof Field\Type\Checkbox:
			case $field instanceof Field\Type\Select:
				return new Formatter\Select( $column, $field );
			case $field instanceof Field\Type\Boolean:
				return new Formatter\Boolean( $column, $field );
			case $field instanceof Field\Type\File:
				return new Formatter\File( $column, $field );
			case $field instanceof Field\Type\FlexibleContent:
				return 'structure' === $column->get_setting( Settings\Column\FlexibleContent::NAME )->get_value()
					? new Formatter\FlexStructure( $column, $field )
					: new Formatter\FlexCount( $column, $field );
			case $field instanceof Field\Type\Link:
				return new Formatter\Link( $column, $field );
			case $field instanceof Field\Type\GoogleMap:
				return new Formatter\Maps( $column, $field );
			case $field instanceof Field\Type\Relationship:
			case $field instanceof Field\Type\PageLinks:
			case $field instanceof Field\Type\PostObject:
			case $field instanceof Field\Type\Taxonomy:
			case $field instanceof Field\Type\User:
				return $field instanceof Field\Multiple && $field->is_multiple()
					? new Formatter\MultipleFormatted( $column, $field )
					: new Formatter\DefaultFormatter( $column, $field );
			default:
				return new Formatter\DefaultFormatter( $column, $field );
		}

	}

}