<?php

namespace ACA\GravityForms\Column\Entry\Original;

use AC;
use ACA\GravityForms\Search;
use ACP;
use GFAPI;

class SourceUrl extends AC\Column implements ACP\Search\Searchable {

	public function __construct() {
		$this->set_original( true )
		     ->set_type( 'field_id-source_url' );
	}

	public function get_raw_value( $id ) {
		$entry = GFAPI::get_entry( $id );

		return $entry ? $entry['source_url'] : null;
	}

	protected function register_settings() {
		$this->add_setting( new AC\Settings\Column\LinkLabel( $this ) );
	}

	public function search() {
		return new Search\Comparison\Entry\TextColumn( 'source_url' );
	}

}