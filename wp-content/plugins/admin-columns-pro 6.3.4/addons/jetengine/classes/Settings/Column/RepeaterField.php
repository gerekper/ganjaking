<?php

namespace ACA\JetEngine\Settings\Column;

use AC\Settings\Column;
use AC\View;
use ACA\JetEngine\Column\Meta;
use ACA\JetEngine\Field\Field;
use ACA\JetEngine\Field\Type\Repeater;
use ACA\JetEngine\Settings\SettingFactory;

/** @property Meta $column */
class RepeaterField extends Column {

	const KEY = 'sub_field';

	/**
	 * @var string
	 */
	private $sub_field;

	/**
	 * @var Repeater
	 */
	private $field;

	public function __construct( Meta $column, Repeater $field ) {
		parent::__construct( $column );

		$this->field = $field;
	}

	protected function define_options() {
		return [ self::KEY ];
	}

	/**
	 * @return View
	 */
	public function create_view() {
		$view = new View( [
			'label'   => __( 'Subfield', 'codepress-admin-columns' ),
			'setting' => $this->get_setting_field(),
		] );

		return $view;
	}

	protected function get_sub_fields_options() {
		$options = [];

		foreach ( $this->field->get_repeated_fields() as $field ) {
			$options[ $field->get_name() ] = $field->get_title();
		}

		return $options;
	}

	/**
	 * @return Field
	 */
	public function get_sub_field_object() {
		$sub_key = $this->get_sub_field();

		foreach ( $this->field->get_repeated_fields() as $sub_field ) {
			if ( $sub_key === $sub_field->get_name() ) {
				return $sub_field;
			}
		}

		return $this->field->get_repeated_fields()[0] ?? null;
	}

	protected function get_setting_field() {
		return $this->create_element( 'select', self::KEY )
		            ->set_attribute( 'data-refresh', 'column' )
		            ->set_options( $this->get_sub_fields_options() );
	}

	public function get_dependent_settings() {
		return ( new SettingFactory() )->create( $this->get_sub_field_object(), $this->column );
	}

	public function get_sub_field() {
		return $this->sub_field;
	}

	public function set_sub_field( $sub_field ) {
		$this->sub_field = $sub_field;

		return $this;
	}

}