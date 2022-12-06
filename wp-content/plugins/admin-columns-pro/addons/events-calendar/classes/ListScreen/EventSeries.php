<?php

namespace ACA\EC\ListScreen;

use ACP;
use ACA\EC\Column;

class EventSeries extends ACP\ListScreen\Post {

	public function __construct() {
		parent::__construct( 'tribe_event_series' );

		$this->set_group( 'events-calendar' );
	}

	protected function register_column_types() {
		parent::register_column_types();

		$this->register_column_types_from_list( [
			Column\EventSeries\Events::class
		] );
	}

}