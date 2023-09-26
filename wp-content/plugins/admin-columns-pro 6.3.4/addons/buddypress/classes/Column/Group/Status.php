<?php

namespace ACA\BP\Column\Group;

use AC;
use ACA\BP\Editing;
use ACP;

class Status extends AC\Column
	implements ACP\Editing\Editable {

	public function __construct() {
		$this->set_type( 'status' );
		$this->set_original( true );
	}

	public function get_value( $id ) {
		return null;
	}

	public function editing() {
		return new Editing\Service\Group\Status();
	}

}