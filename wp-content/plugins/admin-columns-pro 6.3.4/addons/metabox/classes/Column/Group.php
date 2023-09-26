<?php

namespace ACA\MetaBox\Column;

use ACA\MetaBox\Column;
use ACA\MetaBox\Export;
use ACA\MetaBox\Setting;
use ACA\MetaBox\Value\ValueFormatterFactory;

class Group extends Column {

	public function format_single_value( $value, $id = null ) {
		$sub_value = $this->get_sub_field_value( $value );
		$sub_field_setting = $this->get_sub_field_settings();

		if ( $sub_field_setting === null || null === $sub_value ) {
			return $this->get_empty_char();
		}

		$formatter = ( new ValueFormatterFactory() )->create( $this, $sub_field_setting );

		$value = $formatter->format( $sub_value, $id );

		return is_array( $value )
			? implode( $this->get_separator(), $value )
			: $value;
	}

	public function get_sub_field_value( $value ) {
		$field = $this->get_sub_field();

		if ( ! $field || ! is_array( $value ) ) {
			return null;
		}

		return $value[ $field ] ?? null;
	}

	public function get_sub_field(): ?string {
		$setting = $this->get_setting( 'group_field' );

		return $setting instanceof Setting\GroupField
			? $setting->get_value()
			: null;
	}

	private function get_sub_field_settings(): ?array {
		$setting = $this->get_setting( 'group_field' );

		return $setting instanceof Setting\GroupField
			? $setting->get_group_field_settings()
			: null;
	}

	protected function register_settings(): void {
		$this->add_setting( new Setting\GroupField( $this ) );
	}

	public function export() {
		$type = $this->get_sub_field_settings() ?? '';

		switch ( $type ) {
			case 'single_image':
			case 'image':
			case 'image_advanced':
			case 'image_upload':
				return new Export\Model\Group\Image( $this );
			default:
				return new Export\Model\StrippedValue( $this );
		}
	}

}