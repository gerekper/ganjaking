<?php

namespace ACA\ACF\Editing\Storage;

use ACP;

final class Group implements ACP\Editing\Storage {

	/**
	 * @var string
	 */
	private $group_key;

	/**
	 * @var string
	 */
	private $sub_key;

	/**
	 * @var string
	 */
	private $id_prefix;

	/**
	 * @var ReadStorage
	 */
	private $read_storage;

	public function __construct( $group_key, $sub_key, $id_prefix, ReadStorage $read_storage ) {
		$this->group_key = (string) $group_key;
		$this->sub_key = (string) $sub_key;
		$this->id_prefix = (string) $id_prefix;
		$this->read_storage = $read_storage;
	}

	public function get( int $id ) {
		return $this->read_storage->get( $id );
	}

	public function update( int $id, $data ): bool {
		$raw_group_value = get_field( $this->group_key, $this->id_prefix . $id, false );
		$group_value = [];

		foreach ( $raw_group_value as $field_key => $field_value ) {
			$field = acf_get_field( $field_key );

			if ( ! $field || ! isset( $field['name'] ) ) {
				exit;
			}

			$group_value[ $field['name'] ] = $field_value;
		}

		$group_value[ $this->sub_key ] = $data;

		return false !== update_field( $this->group_key, $group_value, $this->id_prefix . $id );
	}

}