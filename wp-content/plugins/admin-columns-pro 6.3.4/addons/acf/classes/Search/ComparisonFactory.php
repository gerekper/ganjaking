<?php

namespace ACA\ACF\Search;

use ACA\ACF\Field;
use ACA\ACF\FieldType;
use ACA\ACF\Search;
use ACP;

class ComparisonFactory implements SearchComparisonFactory {

	/**
	 * @param Field  $field
	 * @param string $meta_key
	 * @param string $meta_type
	 *
	 * @return ACP\Search\Comparison|false
	 */
	public function create( Field $field, $meta_key, $meta_type ) {
		switch ( $field->get_type() ) {
			case FieldType::TYPE_BOOLEAN:
				return new ACP\Search\Comparison\Meta\Checkmark( $meta_key, $meta_type );

			case FieldType::TYPE_BUTTON_GROUP:
				return new Search\Comparison\Select( $meta_key, $meta_type, $field instanceof Field\Choices ? $field->get_choices() : [] );

			case FieldType::TYPE_CHECKBOX:
				return new Search\Comparison\MultiSelect( $meta_key, $meta_type, $field instanceof Field\Choices ? $field->get_choices() : [] );

			case FieldType::TYPE_DATE_PICKER:
				return new Search\Comparison\DatePicker( $meta_key, $meta_type );

			case FieldType::TYPE_DATE_TIME_PICKER:
				return new ACP\Search\Comparison\Meta\DateTime\ISO( $meta_key, $meta_type );

			case FieldType::TYPE_TEXT:
			case FieldType::TYPE_PASSWORD:
			case FieldType::TYPE_TEXTAREA:
			case FieldType::TYPE_WYSIWYG:
			case FieldType::TYPE_EMAIL:
			case FieldType::TYPE_COLOR_PICKER:
			case FieldType::TYPE_TIME_PICKER:
			case FieldType::TYPE_OEMBED:
			case FieldType::TYPE_URL:
				return new ACP\Search\Comparison\Meta\Text( $meta_key, $meta_type );

			case FieldType::TYPE_IMAGE:
				$post_type = $field instanceof Field\PostTypeFilterable ? $field->get_post_type() : false;

				return new ACP\Search\Comparison\Meta\Image( $meta_key, $meta_type, $post_type );

			case FieldType::TYPE_GALLERY:
				return new ACP\Search\Comparison\Meta\EmptyNotEmpty( $meta_key, $meta_type );

			case FieldType::TYPE_NUMBER:
			case FieldType::TYPE_RANGE:
				return new ACP\Search\Comparison\Meta\Decimal( $meta_key, $meta_type );

			case FieldType::TYPE_SELECT:
			case FieldType::TYPE_RADIO:
				$choices = $field instanceof Field\Choices ? $field->get_choices() : [];

				return $field instanceof Field\Multiple && $field->is_multiple()
					? new Search\Comparison\MultiSelect( $meta_key, $meta_type, $choices )
					: new Search\Comparison\Select( $meta_key, $meta_type, $choices );

			case FieldType::TYPE_FILE:
				$post_type = $field instanceof Field\PostTypeFilterable
					? $field->get_post_type()
					: null;

				return new ACP\Search\Comparison\Meta\Media( $meta_key, $meta_type, $post_type );

			case FieldType::TYPE_RELATIONSHIP:
			case FieldType::TYPE_POST:
				$post_type = $field instanceof Field\PostTypeFilterable
					? $field->get_post_type()
					: null;

				$terms = $field instanceof Field\TaxonomyFilterable
					? $field->get_taxonomies()
					: [];

				return $field instanceof Field\Multiple && $field->is_multiple()
					? new ACP\Search\Comparison\Meta\Posts( $meta_key, $meta_type, $post_type, $terms )
					: new ACP\Search\Comparison\Meta\Post( $meta_key, $meta_type, $post_type, $terms );

			case FieldType::TYPE_TAXONOMY:
				if ( ! $field instanceof Field\Type\Taxonomy ) {
					return null;
				}

				if ( $field->uses_native_term_relation() ) {
					return new ACP\Search\Comparison\Post\Taxonomy( $field->get_taxonomy() );
				}

				return $field->is_multiple()
					? new Search\Comparison\Taxonomies( $meta_key, $meta_type, $field->get_taxonomy() )
					: new Search\Comparison\Taxonomy( $meta_key, $meta_type, $field->get_taxonomy() );

			case FieldType::TYPE_LINK:
				return new Search\Comparison\Link( $meta_key, $meta_type );

			case FieldType::TYPE_USER:
				return $field instanceof Field\Multiple && $field->is_multiple()
					? new Search\Comparison\Users( $meta_key, $meta_type )
					: new Search\Comparison\User( $meta_key, $meta_type );

			default:
				return false;
		}
	}

}