<?php

namespace ACA\EC\Column\Event;

use AC;
use ACA\EC\Editing;
use ACA\EC\Search;
use ACP;

class Recurring extends AC\Column implements ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'recurring' )
		     ->set_label( __( 'Recurring', 'tribe-events-calendar-pro' ) )
		     ->set_original( true );
	}

	public function search() {
		return new Search\Event\Recurring();
	}

}