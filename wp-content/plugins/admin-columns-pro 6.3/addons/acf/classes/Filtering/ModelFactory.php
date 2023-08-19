<?php

namespace ACA\ACF\Filtering;

use ACA\ACF\Column;
use ACA\ACF\Field;
use ACA\ACF\FieldType;
use ACA\ACF\Filtering;
use ACP;

class ModelFactory implements Filtering\FilteringModelFactory {

	public function create( Field $field, Column $column ) {

		switch ( $field->get_type() ) {
			case FieldType::TYPE_CHECKBOX:
				return new Filtering\Model\SerializedChoices( $column, $field );

			case FieldType::TYPE_COLOR_PICKER:
			case FieldType::TYPE_EMAIL:
			case FieldType::TYPE_OEMBED:
			case FieldType::TYPE_PASSWORD:
			case FieldType::TYPE_TEXT:
			case FieldType::TYPE_TEXTAREA:
			case FieldType::TYPE_URL:
				return new Filtering\Model\Unformatted( $column );

			case FieldType::TYPE_DATE_PICKER:
				return new Filtering\Model\DatePicker( $column );

			case FieldType::TYPE_DATE_TIME_PICKER:
				return new Filtering\Model\DateTimePicker( $column );

			case FieldType::TYPE_FILE:
				return new Filtering\Model\File( $column );

			case FieldType::TYPE_IMAGE:
				return new Filtering\Model\Image( $column );

			case FieldType::TYPE_LINK:
				return new Filtering\Model\Link( $column );

			case FieldType::TYPE_NUMBER:
			case FieldType::TYPE_RANGE:
				return new Filtering\Model\Number( $column );

			case FieldType::TYPE_BUTTON_GROUP:
			case FieldType::TYPE_RADIO:
				return new Filtering\Model\Choices( $column, $field );

			case FieldType::TYPE_SELECT:
				return $field instanceof Field\Multiple && $field->is_multiple()
					? new Filtering\Model\SerializedChoices( $column, $field )
					: new Filtering\Model\Choices( $column, $field );

			case FieldType::TYPE_PAGE_LINK:
			case FieldType::TYPE_USER:
			case FieldType::TYPE_POST:
			case FieldType::TYPE_TAXONOMY:
				return $field instanceof Field\Multiple && $field->is_multiple()
					? new Filtering\Model\FormattedRelations( $column )
					: new Filtering\Model\FormattedRelation( $column );

			case FieldType::TYPE_RELATIONSHIP:
				return new Filtering\Model\FormattedRelations( $column );

			case FieldType::TYPE_BOOLEAN:
				return new Filtering\Model\Toggle( $column );

			default:
				return new ACP\Filtering\Model\Disabled( $column );
		}
	}

}