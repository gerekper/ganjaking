<?php

namespace ACA\ACF\Settings\Column;

use AC;
use ACA\ACF\Column;

/**
 * @property Column $column
 */
class Date extends AC\Settings\Column\Date {

	public function __construct( Column $column ) {
		parent::__construct( $column );

		$this->set_default( 'acf' );
	}

	protected function get_acf_date_format() {
		$field = $this->column->get_field()->get_settings();

		return isset( $field['display_format'] ) && $field['display_format'] ? $field['display_format'] : 'Y-m-d';
	}

	protected function get_custom_formats() {
		$values = parent::get_custom_formats();
		$values[] = 'acf';

		return $values;
	}

	protected function get_custom_format_options() {
		$label = __( 'ACF Date Format', 'codepress-admin-columns' );

		$options = [
			'acf' => $this->get_html_label(
				$label,
				$this->get_acf_date_format(),
				sprintf( __( "%s uses the %s from it's field settings.", 'codepress-admin-columns' ), $label, '"' . __( 'Display Format', 'codepress-admin-columns' ) . '"' )
			),
		];

		return ac_helper()->array->insert( parent::get_custom_format_options(), $options, 'diff' );
	}

	public function format( $value, $original_value ) {
		if ( ! $value ) {
			return false;
		}

		if ( 'acf' === $this->get_date_format() ) {
			return ac_format_date( $this->get_acf_date_format(), strtotime( $value ) );
		}

		return parent::format( strtotime( $value ), $original_value );
	}

}