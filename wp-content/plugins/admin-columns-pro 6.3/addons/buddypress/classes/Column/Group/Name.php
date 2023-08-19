<?php

namespace ACA\BP\Column\Group;

use AC;
use ACA\BP\Editing;
use ACP;

class Name extends AC\Column
	implements ACP\Editing\Editable {

	public function __construct() {
		$this->set_type( 'comment' )
		     ->set_original( true );
	}

	public function editing() {
		return new Editing\Service\Group\NameOnly();
	}

}