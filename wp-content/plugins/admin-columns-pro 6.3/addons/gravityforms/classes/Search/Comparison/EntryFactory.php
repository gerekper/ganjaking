<?php

namespace ACA\GravityForms\Search\Comparison;

use ACA\GravityForms\Field\Field;
use ACA\GravityForms\Field\Type;
use ACA\GravityForms\Search\Comparison;

final class EntryFactory {

	public function create( Field $field ) {

		switch ( true ) {
			case $field instanceof Type\Date:
				return new Comparison\Entry\Date( $field->get_id() );
			case $field instanceof Type\Number:
				return new Comparison\Entry\Number( $field->get_id() );

			case $field instanceof Type\Input:
			case $field instanceof Type\Textarea:
				return new Comparison\Entry\Text( $field->get_id() );

			case $field instanceof Type\Select:
				return $field->is_multiple()
					? new Comparison\Entry\Choices( $field->get_id(), $field->get_options() )
					: new Comparison\Entry\Choice( $field->get_id(), $field->get_options() );

			case $field instanceof Type\Consent:
				return new Comparison\Entry\Consent( $field->get_id() );

			case $field instanceof Type\Radio:
			case $field instanceof Type\ProductSelect:
				return new Comparison\Entry\Choice( $field->get_id(), $field->get_options() );

			case $field instanceof Type\CheckboxGroup:
				return new Comparison\Entry\CheckboxGroup( $field->get_id(), $field->get_options(), $field->get_sub_fields() );

			case $field instanceof Type\Checkbox:
				return new Comparison\Entry\Checkbox( $field->get_id() );

			default:
				return false;
		}

	}

}