<?php

namespace ACA\GravityForms\Column\Entry\Original;

use AC;
use ACA\GravityForms\Search;
use ACP;
use GFAPI;

class DateCreated extends AC\Column implements ACP\Search\Searchable {

	public function __construct() {
		$this->set_original( true )
		     ->set_type( 'field_id-date_created' );
	}

	public function get_raw_value( $id ) {
		$entry = GFAPI::get_entry( $id );

		return $entry ? $entry['date_created'] : null;
	}

	public function search() {
		return new Search\Comparison\Entry\DateColumn( 'date_created' );
	}

}