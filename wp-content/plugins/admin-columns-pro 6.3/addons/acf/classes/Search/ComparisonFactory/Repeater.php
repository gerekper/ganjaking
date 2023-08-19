<?php

namespace ACA\ACF\Search\ComparisonFactory;

use ACA\ACF\Field;
use ACA\ACF\FieldType;
use ACA\ACF\Search\Comparison;
use ACP;

class Repeater {

	/**
	 * @param Field  $field
	 * @param string $parent_meta_key
	 * @param string $meta_type
	 *
	 * @return ACP\Search\Comparison|null
	 */
	public function create( Field $field, $parent_meta_key, $meta_type ) {
		$meta_key = $field->get_meta_key();
		$base_arguments = [
			$meta_type,
			$parent_meta_key,
			$meta_key,
		];

		switch ( $field->get_type() ) {
			case FieldType::TYPE_BOOLEAN:
				return new Comparison\Repeater\Toggle( ...$base_arguments );

			case FieldType::TYPE_DATE_TIME_PICKER:
				return new Comparison\Repeater\DateTime( ...$base_arguments );

			case FieldType::TYPE_DATE_PICKER:
				return new Comparison\Repeater\Date( ...$base_arguments );

			case FieldType::TYPE_USER:
				return new Comparison\Repeater\User(
					$meta_type,
					$parent_meta_key,
					$meta_key,
					$field instanceof Field\Multiple ? $field->is_multiple() : null
				);

			case FieldType::TYPE_POST:
			case FieldType::TYPE_RELATIONSHIP:
				return new Comparison\Repeater\Posts(
					$meta_type,
					$parent_meta_key,
					$meta_key,
					$field instanceof Field\PostTypeFilterable ? $field->get_post_type() : null
				);

			case FieldType::TYPE_SELECT:
			case FieldType::TYPE_RADIO:
				return new Comparison\Repeater\Select(
					$meta_type,
					$parent_meta_key,
					$meta_key,
					$field instanceof Field\Choices ? $field->get_choices() : [],
					$field instanceof Field\Multiple ? $field->is_multiple() : null );

			case FieldType::TYPE_NUMBER:
			case FieldType::TYPE_RANGE:
				return new Comparison\Repeater\Number( ...$base_arguments );

			case FieldType::TYPE_FILE:
				return new Comparison\Repeater\Media( ...$base_arguments );

			case FieldType::TYPE_IMAGE:
				return new Comparison\Repeater\Image( ...$base_arguments );

			case FieldType::TYPE_COLOR_PICKER:
			case FieldType::TYPE_EMAIL:
			case FieldType::TYPE_OEMBED:
			case FieldType::TYPE_PASSWORD:
			case FieldType::TYPE_TEXT:
			case FieldType::TYPE_TEXTAREA:
			case FieldType::TYPE_TIME_PICKER:
			case FieldType::TYPE_URL:
			case FieldType::TYPE_WYSIWYG:
				return new Comparison\Repeater\Text( ...$base_arguments );

			default:
				return null;
		}

	}

}