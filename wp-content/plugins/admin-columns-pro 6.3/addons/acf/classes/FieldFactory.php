<?php

namespace ACA\ACF;

use ACA\ACF\Field\Type;

class FieldFactory {

	public function create( array $settings ) {

		switch ( $settings['type'] ) {
			case FieldType::TYPE_BOOLEAN:
				return new Type\Boolean( $settings );

			case FieldType::TYPE_BUTTON_GROUP:
				return new Type\ButtonGroup( $settings );

			case FieldType::TYPE_CHECKBOX:
				return new Type\Checkbox( $settings );

			case FieldType::TYPE_COLOR_PICKER:
				return new Type\Color( $settings );

			case FieldType::TYPE_CLONE:
				return new Type\CloneField( $settings );

			case FieldType::TYPE_DATE_PICKER:
				return new Type\Date( $settings );

			case FieldType::TYPE_DATE_TIME_PICKER:
				return new Type\DateTime( $settings );

			case FieldType::TYPE_EMAIL:
				return new Type\Email( $settings );

			case FieldType::TYPE_FILE:
				return new Type\File( $settings );

			case FieldType::TYPE_FLEXIBLE_CONTENT:
				return new Type\FlexibleContent( $settings );

			case FieldType::TYPE_GALLERY:
				return new Type\Gallery( $settings );

			case FieldType::TYPE_GOOGLE_MAP:
				return new Type\GoogleMap( $settings );

			case FieldType::TYPE_IMAGE:
			case FieldType::TYPE_IMAGE_CROP:
				return new Type\Image( $settings );

			case FieldType::TYPE_LINK:
				return new Type\Link( $settings );

			case FieldType::TYPE_NUMBER:
				return new Type\Number( $settings );

			case FieldType::TYPE_OEMBED:
				return new Type\Oembed( $settings );

			case FieldType::TYPE_PASSWORD:
				return new Type\Password( $settings );

			case FieldType::TYPE_PAGE_LINK:
				return new Type\PageLinks( $settings );

			case FieldType::TYPE_POST:
				return new Type\PostObject( $settings );

			case FieldType::TYPE_RADIO:
				return new Type\Radio( $settings );

			case FieldType::TYPE_RANGE:
				return new Type\Range( $settings );

			case FieldType::TYPE_RELATIONSHIP:
				return new Type\Relationship( $settings );

			case FieldType::TYPE_REPEATER:
				return new Type\Repeater( $settings );

			case FieldType::TYPE_SELECT:
				return new Type\Select( $settings );

			case FieldType::TYPE_TAXONOMY:
				return new Type\Taxonomy( $settings );

			case FieldType::TYPE_TEXT:
				return new Type\Text( $settings );

			case FieldType::TYPE_TEXTAREA:
				return new Type\Textarea( $settings );

			case FieldType::TYPE_TIME_PICKER:
				return new Type\Time( $settings );

			case FieldType::TYPE_URL:
				return new Type\Url( $settings );

			case FieldType::TYPE_USER:
				return new Type\User( $settings );

			case FieldType::TYPE_WYSIWYG:
				return new Type\Wysiwyg( $settings );
		}

		return new Field( $settings );
	}

}