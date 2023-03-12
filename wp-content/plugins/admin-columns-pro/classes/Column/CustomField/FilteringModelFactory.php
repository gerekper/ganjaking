<?php

namespace ACP\Column\CustomField;

use AC\Column;
use AC\Settings\Column\CustomFieldType;
use ACP\Filtering\Model;

class FilteringModelFactory {

	public static function create( string $type, Column\CustomField $column ): Model {

		switch ( $type ) {

			case CustomFieldType::TYPE_ARRAY :
			case CustomFieldType::TYPE_COUNT :
				return new Model\Disabled( $column );
			case CustomFieldType::TYPE_BOOLEAN :
				return new Model\CustomField\Checkmark( $column );
			case CustomFieldType::TYPE_DATE :
				return new Model\CustomField\Date( $column );
			case CustomFieldType::TYPE_NON_EMPTY :
				return new Model\CustomField\Hascontent( $column );
			case CustomFieldType::TYPE_IMAGE :
				return new Model\CustomField\Image( $column );
			case CustomFieldType::TYPE_MEDIA :
				return new Model\CustomField\Media( $column );
			case CustomFieldType::TYPE_URL :
				return new Model\CustomField\Link( $column );
			case CustomFieldType::TYPE_NUMERIC :
				return new Model\CustomField\Number( $column );
			case CustomFieldType::TYPE_POST :
				return new Model\CustomField\Post( $column );
			case CustomFieldType::TYPE_USER :
				return new Model\CustomField\User( $column );
			case CustomFieldType::TYPE_COLOR :
			case CustomFieldType::TYPE_TEXT :
			default :
				return new Model\CustomField( $column );
		}
	}

}