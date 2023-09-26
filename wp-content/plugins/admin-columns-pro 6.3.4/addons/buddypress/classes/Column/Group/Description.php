<?php

namespace ACA\BP\Column\Group;

use AC;
use ACA\BP\Editing;
use ACP;

class Description extends AC\Column
	implements ACP\Editing\Editable {

	public function __construct() {
		$this->set_type( 'description' );
		$this->set_original( true );
	}

	public function get_value( $id ) {
		return null;
	}

	public function get_raw_value( $group_id ) {
		return groups_get_group( $group_id )->description;
	}

	public function editing() {
		return new Editing\Service\Group\Description( $this );
	}

}