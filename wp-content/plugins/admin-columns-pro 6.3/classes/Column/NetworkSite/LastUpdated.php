<?php

namespace ACP\Column\NetworkSite;

use AC;

class LastUpdated extends AC\Column {

	public function __construct() {
		$this->set_type( 'lastupdated' )
		     ->set_original( true );
	}

	public function register_settings() {
		$this->get_setting( 'width' )->set_default( 20 );
	}

}