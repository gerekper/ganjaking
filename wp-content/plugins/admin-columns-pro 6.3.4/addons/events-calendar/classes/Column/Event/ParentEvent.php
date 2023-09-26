<?php

namespace ACA\EC\Column\Event;

use ACA\EC\Editing;
use ACA\EC\Search;
use ACA\EC\Service\ColumnGroups;
use ACP;

class ParentEvent extends ACP\Column\Post\PostParent {

	public function __construct() {
		parent::__construct();

		$this->set_label( __( 'Parent Event' ) );
		$this->set_group( ColumnGroups::EVENTS_CALENDAR );
	}

	public function is_valid() {
		return true;
	}

	public function editing() {
		return false;
	}

}