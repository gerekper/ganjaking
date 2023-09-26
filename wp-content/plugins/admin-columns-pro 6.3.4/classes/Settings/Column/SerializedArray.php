<?php

namespace ACP\Settings\Column;

use AC;
use AC\View;

class SerializedArray extends AC\Settings\Column implements AC\Settings\FormatValue {

	/**
	 * @var string
	 */
	private $array_keys;

	protected function set_name() {
		$this->name = 'array_keys';
	}

	protected function define_options() {
		return [ 'array_keys' ];
	}

	public function create_view() {
		$setting = $this->create_element( 'text' )
		                ->set_attribute( 'data-label', 'update' )
		                ->set_attribute( 'placeholder', sprintf( '%s: %s', __( 'example', 'codepress-admin-columns' ), 'sizes.medium.file' ) );

		$instructions = ( new View() )->set_template( 'tooltip/serialized' );

		return new View( [
			'setting'      => $setting,
			'label'        => __( 'Array Keys', 'codepress-admin-columns' ),
			'instructions' => $instructions->render(),
		] );
	}

	public function set_array_keys( $keys ) {
		$this->array_keys = $keys;
	}

	public function get_array_keys() {
		return $this->array_keys;
	}

	public function get_keys() {
		return array_filter( array_map( 'trim', explode( '.', $this->array_keys ) ) );
	}

	private function get_expanded_level() {
		return (int) apply_filters( 'acp/column/settings/serialized/expanded_level', 1, $this->column );
	}

	public function format( $value, $original_value ) {
		if ( ! is_array( $value ) ) {
			return $value;
		}

		$value = ac_helper()->array->get_nested_value(
			$value,
			$this->get_keys()
		);

		if ( ac_helper()->array->is_associative( $value ) ) {
			return sprintf(
				'<div data-component="ac-json" data-json="%s" data-level="%s" ></div>',
				esc_attr( json_encode( $value ) ),
				$this->get_expanded_level()
			);
		}

		return ac_helper()->array->implode_recursive( __( ', ' ), $value );
	}

}