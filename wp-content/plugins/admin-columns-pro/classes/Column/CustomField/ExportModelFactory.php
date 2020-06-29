<?php

namespace ACP\Column\CustomField;

use AC\Column;
use AC\Settings\Column\CustomFieldType;
use ACP\Export\Model;

class ExportModelFactory {

	/**
	 * @param string             $type
	 * @param Column\CustomField $column
	 *
	 * @return Model
	 */
	public static function create( $type, Column\CustomField $column ) {

		switch ( $type ) {

			case CustomFieldType::TYPE_ARRAY :
				return new Model\Value( $column );
			case CustomFieldType::TYPE_BOOLEAN :
				return new Model\RawValue( $column );
			case CustomFieldType::TYPE_COLOR :
				return new Model\RawValue( $column );
			case CustomFieldType::TYPE_COUNT :
				return new Model\Value( $column );
			case CustomFieldType::TYPE_DATE :
				return new Model\CustomField\Date( $column );
			case CustomFieldType::TYPE_TEXT :
				return new Model\RawValue( $column );
			case CustomFieldType::TYPE_NON_EMPTY :
				return new Model\Value( $column );
			case CustomFieldType::TYPE_IMAGE :
				return new Model\CustomField\Image( $column );
			case CustomFieldType::TYPE_MEDIA :
				return new Model\CustomField\Image( $column );
			case CustomFieldType::TYPE_URL :
				return new Model\RawValue( $column );
			case CustomFieldType::TYPE_NUMERIC :
				return new Model\RawValue( $column );
			case CustomFieldType::TYPE_POST :
				return new Model\StrippedValue( $column );
			case CustomFieldType::TYPE_USER :
				return new Model\StrippedValue( $column );
			default :
				return new Model\RawValue( $column );
		}
	}

}