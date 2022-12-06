<?php

namespace ACA\GravityForms\Editing\Storage\Entry;

use ACA\GravityForms\Editing\Storage;
use ACA\GravityForms\Field\Field;
use ACA\GravityForms\Value\EntryValue;
use GF_Field_MultiSelect;

class MultiSelect extends Storage\Entry {

	/**
	 * @var Field
	 */
	private $field;

	public function __construct( Field $field ) {
		parent::__construct( $field->get_id() );

		$this->field = $field;
	}

	public function get( int $id ) {
		$entry_value = ( new EntryValue( $this->field ) )->get_value( $id );

		return ( new GF_Field_MultiSelect )->to_array( $entry_value );
	}

	public function update( int $id, $data ): bool {
		return parent::update( $id, $data ? json_encode( $data ) : '' );
	}

}