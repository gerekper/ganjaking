<?php

namespace ACA\BP\Column\User;

use AC;
use ACA\BP\Editing;
use ACA\BP\Search;
use ACP;

class MemberType extends AC\Column
	implements ACP\Editing\Editable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'bp_member_type' )
		     ->set_group( 'buddypress' )
		     ->set_original( true );
	}

	public function get_value( $id ) {
		return null;
	}

	public function get_raw_value( $id ) {
		return bp_get_member_type( $id );
	}

	public function is_valid() {
		$members = bp_get_member_types();

		return ! empty( $members );
	}

	public function editing() {
		return new Editing\Service\User\Membertype( $this->get_member_types() );
	}

	public function search() {
		return new Search\User\MemberTypes( $this->get_member_types() );
	}

	private function get_member_types() {
		$options = [];

		foreach ( bp_get_member_types( [], 'objects' ) as $key => $type ) {
			$options[ $key ] = isset( $type->labels['singular_name'] ) ? $type->labels['singular_name'] : $type->name;
		}

		return $options;
	}

}