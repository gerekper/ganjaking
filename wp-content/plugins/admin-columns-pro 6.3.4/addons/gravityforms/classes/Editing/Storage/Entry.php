<?php

namespace ACA\GravityForms\Editing\Storage;

use ACP\Editing\Storage;
use GFAPI;

class Entry implements Storage {

	/**
	 * @var string
	 */
	private $field_id;

	public function __construct( $field_id ) {
		$this->field_id = $field_id;
	}

	public function get( int $id ) {
		$entry = GFAPI::get_entry( $id );

		if( ! is_array( $entry ) ){
			return false;
		}

		return ! is_wp_error( $entry )
		       && isset( $entry[ $this->field_id ] )
			? $entry[ $this->field_id ]
			: false;
	}

	public function update( int $id, $data ): bool {
		return GFAPI::update_entry_field( $id, $this->field_id, $data );
	}

}