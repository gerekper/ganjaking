<?php

namespace ACA\GravityForms\Column\Entry\Original;

use AC;
use ACA\GravityForms\Search;
use ACP;

class UserIp extends AC\Column implements ACP\Search\Searchable {

	public function __construct() {
		$this->set_original( true )
		     ->set_type( 'field_id-ip' );
	}

	public function search() {
		return new Search\Comparison\Entry\TextColumn( 'ip' );
	}

}