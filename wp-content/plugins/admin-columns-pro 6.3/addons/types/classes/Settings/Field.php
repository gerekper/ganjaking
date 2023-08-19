<?php

namespace ACA\Types\Settings;

use AC;
use AC\View;
use ACA\Types\Column;

/**
 * @property Column $column
 */
class Field extends AC\Settings\Column {

	/**
	 * @var string
	 */
	private $types_field;

	protected function define_options() {
		return [ 'types_field' ];
	}

	public function get_dependent_settings() {
		return $this->column->get_field()->get_dependent_settings();
	}

	public function create_view() {
		$select = $this->create_element( 'select' );

		$select
			->set_no_result( sprintf( __( 'No %s fields available.', 'codepress-admin-columns' ), __( 'Types', 'codepress-admin-columns' ) ) )
			->set_options( $this->get_field_types() )
			->set_attribute( 'data-refresh', 'column' )
			->set_attribute( 'data-label', 'update' );

		$view = new View( [
			'label'       => __( 'Field', 'codepress-admin-columns' ),
			'description' => sprintf( __( 'Select your %s field.', 'codepress-admin-columns' ), __( 'Types', 'codepress-admin-columns' ) ) . '<em>' . __( 'Type', 'codepress-admin-columns' ) . ': ' . $this->get_types_field() . '</em>',
			'setting'     => $select,
		] );

		return $view;
	}

	/**
	 * @return string
	 */
	public function get_types_field() {
		if ( null === $this->types_field ) {

			// Default
			$this->set_types_field( $this->get_first_types_field() );
		}

		return $this->types_field;
	}

	/**
	 * @param string $types_field
	 *
	 * @return true
	 */
	public function set_types_field( $types_field ) {
		$this->types_field = $types_field;

		return true;
	}

	/**
	 * @return string
	 */
	private function get_first_types_field() {
		$fields = $this->get_field_types();

		reset( $fields );

		return key( $fields );
	}

	// Common

	private function get_field_types() {
		return $this->column->get_fields();
	}

}