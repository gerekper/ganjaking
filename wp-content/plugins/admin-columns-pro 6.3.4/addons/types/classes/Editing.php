<?php

namespace ACA\Types;

use ACP;

/**
 * @property Column $column
 */
class Editing extends ACP\Editing\Model {

	public function __construct( Column $column ) {
		parent::__construct( $column );
	}

	public function get_view_settings() {
		$data = [
			'type' => 'text',
		];

		$placeholder = $this->column->get_field()->get( 'placeholder' );

		if ( $placeholder ) {
			$data['placeholder'] = $placeholder;
		}

		if ( ! $this->column->get_field()->is_required() ) {
			$data['clear_button'] = true;
		}

		return $data;
	}

	/**
	 * @param int $id
	 *
	 * @return array|mixed|string
	 */
	public function get_edit_value( $id ) {
		$value = $this->column->get_field()->get_raw_value( $id );

		if ( empty( $value ) ) {
			return false;
		}

		return $value;
	}

	/**
	 * @param int          $id
	 * @param array|string $value
	 *
	 * @return bool
	 */
	public function save( $id, $value ) {
		return false !== $this->update_metadata( $id, $value );
	}

	/**
	 * @param int   $id
	 * @param array $values
	 *
	 * @return bool
	 */
	public function save_multi_input( $id, $values ) {
		$this->delete_metadata( $id );

		$results = [];

		foreach ( $values as $value ) {
			$results[] = $this->add_metadata( $id, $value );
		}

		return ! in_array( false, $results, true );
	}

	/**
	 * @param int $id
	 *
	 * @return bool
	 */
	public function delete_metadata( $id ) {
		return delete_metadata( $this->column->get_meta_type(), $id, $this->column->get_meta_key(), null );
	}

	/**
	 * @param int   $id
	 * @param mixed $value
	 *
	 * @return false|int
	 */
	public function add_metadata( $id, $value ) {
		return add_metadata( $this->column->get_meta_type(), $id, $this->column->get_meta_key(), $value );
	}

	/**
	 * @param int   $id
	 * @param mixed $value
	 *
	 * @return bool|int
	 */
	public function update_metadata( $id, $value ) {
		return update_metadata( $this->column->get_meta_type(), $id, $this->column->get_meta_key(), $value );
	}

}