<?php

namespace ACA\ACF\Export;

use AC;
use ACA\ACF\FieldType;
use ACP;

class ModelFactory {

	public function create( string $type, AC\Column $column ): ACP\Export\Service {
		switch ( $type ) {
			case FieldType::TYPE_DATE_PICKER:
				return new Model\Date( $column );
			case FieldType::TYPE_LINK:
				return new Model\Link( $column );
			case FieldType::TYPE_BUTTON_GROUP:
			case FieldType::TYPE_SELECT:
			case FieldType::TYPE_RADIO:
			case FieldType::TYPE_CHECKBOX:
			case FieldType::TYPE_POST:
			case FieldType::TYPE_GOOGLE_MAP:
			case FieldType::TYPE_RELATIONSHIP:
			case FieldType::TYPE_USER:
			case FieldType::TYPE_TAXONOMY:
				return new ACP\Export\Model\StrippedValue( $column );

			case FieldType::TYPE_FILE:
			case FieldType::TYPE_GALLERY:
			case FieldType::TYPE_IMAGE:
				return new ACP\Export\Model\CustomField\Image( $column );
			default:
				return new ACP\Export\Model\RawValue( $column );
		}
	}

}