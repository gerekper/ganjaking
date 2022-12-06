<?php

namespace ACA\ACF\Sorting;

use ACA\ACF\Column;
use ACA\ACF\Field;
use ACA\ACF\FieldType;
use ACA\ACF\Sorting;
use ACP;

class ModelFactory implements SortingModelFactory {

	public function create( Field $field, $meta_key, Column $column ) {
		$meta_type = $column->get_meta_type();

		switch ( $field->get_type() ) {
			case FieldType::TYPE_NUMBER:
			case FieldType::TYPE_RANGE:
				return ( new ACP\Sorting\Model\MetaFactory() )->create( $meta_type, $meta_key, new ACP\Sorting\Type\DataType( ACP\Sorting\Type\DataType::DECIMAL ) );

			case FieldType::TYPE_TEXT:
			case FieldType::TYPE_TEXTAREA:
			case FieldType::TYPE_WYSIWYG:
			case FieldType::TYPE_EMAIL:
			case FieldType::TYPE_COLOR_PICKER:
			case FieldType::TYPE_OEMBED:
			case FieldType::TYPE_URL:
			case FieldType::TYPE_PASSWORD:
			case FieldType::TYPE_TIME_PICKER:
			case FieldType::TYPE_IMAGE:
			case FieldType::TYPE_BOOLEAN:
				return ( new ACP\Sorting\Model\MetaFactory() )->create( $meta_type, $meta_key );

			case FieldType::TYPE_DATE_PICKER:
				return ( new ACP\Sorting\Model\MetaFactory() )->create( $meta_type, $meta_key, new ACP\Sorting\Type\DataType( ACP\Sorting\Type\DataType::NUMERIC ) );

			case FieldType::TYPE_DATE_TIME_PICKER:
				return ( new ACP\Sorting\Model\MetaFactory() )->create( $meta_type, $meta_key, new ACP\Sorting\Type\DataType( ACP\Sorting\Type\DataType::DATETIME ) );

			case FieldType::TYPE_CHECKBOX:
				$choices = $field instanceof Field\Choices ? $field->get_choices() : [];

				return ( new ACP\Sorting\Model\MetaFormatFactory() )->create( $meta_type, $meta_key, new Sorting\FormatValue\Select( $choices ) );
			case FieldType::TYPE_FILE:
				return ( new ACP\Sorting\Model\MetaFormatFactory() )->create( $meta_type, $meta_key, new Sorting\FormatValue\File() );

			case FieldType::TYPE_RADIO:
			case FieldType::TYPE_BUTTON_GROUP:
				$choices = $field instanceof Field\Choices ? $field->get_choices() : [];
				natcasesort( $choices );

				return ( new ACP\Sorting\Model\MetaMappingFactory )->create( $meta_type, $meta_key, array_keys( $choices ) );

			case FieldType::TYPE_SELECT:
				$choices = $field instanceof Field\Choices ? $field->get_choices() : [];
				natcasesort( $choices );

				return $field instanceof Field\Multiple && $field->is_multiple()
					? ( new ACP\Sorting\Model\MetaFormatFactory() )->create( $meta_type, $meta_key, new Sorting\FormatValue\Select( $choices ) )
					: ( new ACP\Sorting\Model\MetaMappingFactory )->create( $meta_type, $meta_key, array_keys( $choices ) );

			case FieldType::TYPE_RELATIONSHIP:
			case FieldType::TYPE_POST:
			case FieldType::TYPE_PAGE_LINK:
				return ( new Sorting\ModelFactory\Relation() )->create( $field, $meta_key, $column );

			case FieldType::TYPE_USER:
				return ( new Sorting\ModelFactory\User() )->create( $field, $meta_key, $column );

			case FieldType::TYPE_TAXONOMY:
				return ( new Sorting\ModelFactory\Taxonomy() )->create( $field, $meta_key, $column );

			default:
				return new ACP\Sorting\Model\Disabled();
		}
	}

}