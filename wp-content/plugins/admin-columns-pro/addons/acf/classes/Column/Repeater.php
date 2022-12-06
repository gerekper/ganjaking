<?php

namespace ACA\ACF\Column;

use ACA\ACF;
use ACA\ACF\Column;
use ACA\ACF\Export;
use ACP\Export\Model\StrippedValue;

class Repeater extends Column {

	public function get_value( $id ) {
		$raw_value = $this->get_raw_value( $id );

		if ( empty( $raw_value ) ) {
			return $this->get_empty_char();
		}

		if ( $this->get_display() === ACF\Settings\Column\RepeaterDisplay::DISPLAY_COUNT ) {
			return count( $raw_value );
		}

		$formatter = ( new ACF\Value\FormatterFactory() )->create( $this, $this->get_sub_field() );

		$result = [];
		$sub_field = $this->get_sub_field();

		foreach ( $raw_value as $row ) {
			$formatted_value = $formatter->format( $row[ $this->get_sub_field_key() ], $id );

			if ( $sub_field instanceof ACF\Field\ValueWrapper ) {
				$formatted_value = sprintf( '%s%s%s', $sub_field->get_prepend(), $formatted_value, $sub_field->get_append() );
			}

			$result[] = $formatted_value;
		}

		return implode( $this->get_separator(), $result );
	}

	/**
	 * @return string
	 */
	private function get_display() {
		return $this->get_setting( ACF\Settings\Column\RepeaterDisplay::KEY )->get_value();
	}

	/**
	 * @return string|null
	 */
	protected function get_sub_field_key() {
		$setting = $this->get_setting( ACF\Settings\Column\RepeaterSubField::KEY );

		return $setting
			? $setting->get_value()
			: null;
	}

	protected function get_sub_field() {
		$setting = $this->get_setting( ACF\Settings\Column\RepeaterSubField::KEY );

		return $setting instanceof ACF\Settings\Column\RepeaterSubField
			? $setting->get_sub_field_object()
			: null;
	}

	public function get_separator() {
		return '<div class="ac-repeater-divider"></div>';
	}

	public function search() {
		$field = $this->get_sub_field();

		return $field !== null
			? ( new ACF\Search\ComparisonFactory\Repeater() )->create( $field, $this->get_meta_key(), $this->get_meta_type() )
			: false;
	}

	public function export() {
		if ( $this->get_display() === ACF\Settings\Column\RepeaterDisplay::DISPLAY_SUBFIELD ) {
			return new Export\Model\RepeaterSubField( $this );
		}

		return new StrippedValue( $this );
	}

	public function register_settings() {
		$this->add_setting( new ACF\Settings\Column\RepeaterDisplay( $this ) );
	}

}