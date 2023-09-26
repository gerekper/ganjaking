<?php

namespace ACP\Settings\Column;

use AC;
use AC\Collection;

class CustomFieldType extends AC\Settings\Column\CustomFieldType {

	public function get_dependent_settings() {
		$settings = parent::get_dependent_settings();

		switch ( $this->get_field_type() ) {

			case AC\Settings\Column\CustomFieldType::TYPE_POST :
				$settings[] = new AC\Settings\Column\Post( $this->column );

				break;
			case AC\Settings\Column\CustomFieldType::TYPE_USER :
				$settings[] = new AC\Settings\Column\User( $this->column );

				break;
			case AC\Settings\Column\CustomFieldType::TYPE_IMAGE :
			case AC\Settings\Column\CustomFieldType::TYPE_MEDIA :
				$settings[] = new AC\Settings\Column\NumberOfItems( $this->column );

				break;
			case AC\Settings\Column\CustomFieldType::TYPE_ARRAY :
				$settings[] = new SerializedArray( $this->column );

				break;
		}

		return $settings;
	}

	public function format( $value, $original_value ) {
		switch ( $this->get_field_type() ) {

			case AC\Settings\Column\CustomFieldType::TYPE_POST :
			case AC\Settings\Column\CustomFieldType::TYPE_USER :
				$string = ac_helper()->array->implode_recursive( ',', $value );
				$ids = ac_helper()->string->string_to_array_integers( $string );

				return new Collection( $ids );
			case AC\Settings\Column\CustomFieldType::TYPE_IMAGE :
			case AC\Settings\Column\CustomFieldType::TYPE_MEDIA :
				$value = parent::format( $value, $original_value );
				$value->limit( $this->column->get_setting( 'number_of_items' )->get_value() );

				return $value;
			case AC\Settings\Column\CustomFieldType::TYPE_ARRAY :

				return $value;
			default :
				return parent::format( $value, $original_value );
		}
	}

	/**
	 * Get possible field types
	 * @return array
	 */
	protected function get_field_type_options() {
		$field_types = parent::get_field_type_options();

		$field_types['relational'][ AC\Settings\Column\CustomFieldType::TYPE_POST ] = __( 'Post', 'codepress-admin-columns' );
		$field_types['relational'][ AC\Settings\Column\CustomFieldType::TYPE_USER ] = __( 'User', 'codepress-admin-columns' );

		return $field_types;
	}

}