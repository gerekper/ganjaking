<?php

namespace ACP\Column\CustomField;

use AC\Column;
use AC\Settings\Column\CustomFieldType;
use ACP\Export\Model;
use ACP\Export\Service;

class ExportModelFactory {

	public static function create( string $type, Column\CustomField $column ): Service {

		switch ( $type ) {
			case CustomFieldType::TYPE_ARRAY :
			case CustomFieldType::TYPE_COUNT :
			case CustomFieldType::TYPE_NON_EMPTY :
				return new Model\Value( $column );
			case CustomFieldType::TYPE_DATE :
				return new Model\CustomField\Date( $column );
			case CustomFieldType::TYPE_IMAGE :
			case CustomFieldType::TYPE_MEDIA :
				return new Model\CustomField\Image( $column );
			case CustomFieldType::TYPE_POST :
			case CustomFieldType::TYPE_USER :
				return new Model\StrippedValue( $column );
			case CustomFieldType::TYPE_BOOLEAN :
			case CustomFieldType::TYPE_COLOR :
			case CustomFieldType::TYPE_TEXT :
			case CustomFieldType::TYPE_URL :
			case CustomFieldType::TYPE_NUMERIC :
			default :
				return new Model\RawValue( $column );
		}
	}

}