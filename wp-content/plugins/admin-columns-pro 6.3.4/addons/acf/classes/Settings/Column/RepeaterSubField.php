<?php

namespace ACA\ACF\Settings\Column;

use AC;
use AC\View;
use ACA\ACF\Column;
use ACA\ACF\Field;
use ACA\ACF\FieldFactory;
use ACA\ACF\Settings;

/**
 * @property Column $column
 */
class RepeaterSubField extends AC\Settings\Column {

	const KEY = 'sub_field';

	/**
	 * @var string
	 */
	private $sub_field;

	/**
	 * @var Field
	 */
	private $field;

	public function __construct( Column $column ) {
		parent::__construct( $column );

		$this->field = $column->get_field();
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

		if ( ! $this->field instanceof Field\Subfields ) {
			return [];
		}

		foreach ( $this->field->get_sub_fields() as $sub_field ) {
			$options[ $sub_field['key'] ] = $sub_field['label'];
		}

		return $options;
	}

	public function get_sub_field_object(): ?Field {
		$field_factory = new FieldFactory();
		$sub_key = $this->get_sub_field();

		foreach ( $this->field->get_sub_fields() as $sub_field ) {
			if ( $sub_key === $sub_field['key'] ) {
				return $field_factory->create( $sub_field );
			}
		}

		return null;
	}

	/**
	 * @return array|null
	 */
	protected function get_sub_field_settings() {
		if ( ! $this->field instanceof Field\Subfields ) {
			return null;
		}

		foreach ( $this->field->get_sub_fields() as $sub_field ) {
			if ( in_array( $sub_field['type'], $this->get_unsupported_sub_types() ) ) {
				continue;
			}

			if ( $sub_field['key'] === $this->get_sub_field() ) {
				return $sub_field;
			}
		}

		return null;
	}

	private function get_unsupported_sub_types() {
		return [ 'group', 'clone', 'repeater' ];
	}

	protected function get_setting_field() {
		return $this->create_element( 'select', self::KEY )
		            ->set_attribute( 'data-refresh', 'column' )
		            ->set_options( $this->get_sub_fields_options() );
	}

	public function get_dependent_settings() {
		$subfield = $this->get_sub_field_object();

		return $subfield
			? ( new Settings\SettingFactory() )->create( $subfield, $this->column )
			: [];
	}

	public function get_sub_field() {
		return $this->sub_field;
	}

	public function set_sub_field( $sub_field ) {
		$this->sub_field = $sub_field;

		return $this;
	}

}