<?php

namespace ACA\WC\Settings;

use AC;
use AC\View;

abstract class DateType extends AC\Settings\Column
	implements AC\Settings\FormatValue {

	/**
	 * @var string
	 */
	private $date_type;

	/**
	 * @return array
	 */
	abstract protected function get_display_options();

	/**
	 * @return array
	 */
	protected function define_options() {
		return [
			'date_type',
		];
	}

	/**
	 * @return View
	 */
	public function create_view() {
		$select = $this->create_element( 'select' )
		               ->set_attribute( 'data-label', 'update' )
		               ->set_attribute( 'data-refresh', 'column' )
		               ->set_options( $this->get_display_options() );

		return new View( [
			'label'   => __( 'Display', 'codepress-admin-columns' ),
			'setting' => $select,
		] );
	}

	public function get_dependent_settings() {
		return [ new AC\Settings\Column\Date( $this->column ) ];
	}

	/**
	 * @param string $date_type
	 */
	public function set_date_type( $date_type ) {
		$this->date_type = $date_type;
	}

	/**
	 * @return string
	 */
	public function get_date_type() {
		return $this->date_type;
	}

	/**
	 * @param string $value
	 * @param int    $id
	 *
	 * @return string|false
	 */
	public function format( $value, $id ) {
		$field = $this->column->get_field();

		if ( ! $field instanceof \ACA\WC\Field ) {
			return false;
		}

		return $field->get_value( $id );
	}

}