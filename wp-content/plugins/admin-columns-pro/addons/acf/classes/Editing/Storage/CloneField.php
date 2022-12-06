<?php

namespace ACA\ACF\Editing\Storage;

use acf_field_clone;
use ACP\Editing\Storage;
use InvalidArgumentException;

final class CloneField implements Storage {

	/**
	 * @var string
	 */
	private $clone_hash;

	/**
	 * @var string
	 */
	private $field_hash;

	/**
	 * @var string
	 */
	private $id_prefix;

	/**
	 * @var ReadStorage
	 */
	private $read_storage;

	/**
	 * @var acf_field_clone
	 */
	private $acf_clone_field;

	public function __construct( $clone_hash, $field_hash, $id_prefix, ReadStorage $read_storage ) {
		$this->clone_hash = $clone_hash;
		$this->field_hash = $field_hash;
		$this->id_prefix = $id_prefix;
		$this->read_storage = $read_storage;
		$this->acf_clone_field = new acf_field_clone();

		$this->validate();
	}

	private function validate() {
		if ( ! is_string( $this->clone_hash ) ) {
			throw new InvalidArgumentException( 'Expected a string for clone hash.' );
		}
		if ( ! is_string( $this->field_hash ) ) {
			throw new InvalidArgumentException( 'Expected a string for field hash.' );
		}
		if ( ! is_string( $this->id_prefix ) ) {
			throw new InvalidArgumentException( 'Expected a string for id prefix.' );
		}
	}

	public function get( int $id ) {
		return $this->read_storage->get( $id );
	}

	public function update( int $id, $data ): bool {
		$clone_field = acf_get_field( $this->clone_hash );

		if ( ! $clone_field ) {
			return false;
		}

		$value_key = $clone_field['display'] === 'group'
			? $this->field_hash
			: $this->clone_hash . '_' . $this->field_hash;

		$values = [
			$value_key => $data,
		];

		return false !== $this->acf_clone_field->update_value( $values, $this->id_prefix . $id, $clone_field );
	}

}