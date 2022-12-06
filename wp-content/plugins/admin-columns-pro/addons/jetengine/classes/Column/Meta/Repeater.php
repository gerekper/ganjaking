<?php

namespace ACA\JetEngine\Column\Meta;

use AC;
use AC\Collection;
use ACA\JetEngine\Column;
use ACA\JetEngine\Field;
use ACA\JetEngine\Settings;
use ACA\JetEngine\Value\ValueFormatter;
use ACA\JetEngine\Value\ValueFormatterFactory;
use ACP;

class Repeater extends Column\Meta implements ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	protected function register_settings() {
		if ( $this->field instanceof Field\Type\Repeater ) {
			$this->add_setting( new Settings\Column\RepeaterField( $this, $this->field ) );
		}

		$this->add_setting( new AC\Settings\Column\Separator( $this ) );
	}

	public function get_value( $id ) {
		$values = $this->get_value_collection( $id );

		if ( $values->count() === 0 ) {
			return $this->get_empty_char();
		}

		$formatted = [];
		$formatter = $this->get_value_formatter();

		foreach ( $values->get_copy() as $value ) {
			$formatted[] = $formatter->format( $value );
		}

		return implode( $this->get_separator(), $formatted );
	}

	public function get_separator() {
		$setting = $this->get_setting( 'separator' );

		return $setting ? $setting->get_separator_formatted() : parent::get_separator();
	}

	private function get_value_collection( $id ): Collection {
		$sub_key = $this->get_sub_field() ? $this->get_sub_field()->get_name() : null;

		if ( $sub_key === null ) {
			return new Collection();
		}

		$raw_array = parent::get_raw_value( $id );

		if ( empty( $raw_array ) ) {
			return new Collection();
		}

		$value = [];

		foreach ( $raw_array as $row ) {
			if ( array_key_exists( $sub_key, $row ) ) {
				$value[] = $row[ $sub_key ];
			}
		}

		return new Collection( $value );
	}

	public function get_raw_value( $id ) {
		$this->get_value_collection( $id );
	}

	private function get_value_formatter(): ValueFormatter {
		return ( new ValueFormatterFactory() )->create( $this, $this->get_sub_field() );
	}

	private function get_sub_field() {
		$setting = $this->get_setting( Settings\Column\RepeaterField::KEY );

		return $setting instanceof Settings\Column\RepeaterField
			? $setting->get_sub_field_object()
			: null;
	}

}