<?php

namespace ACA\EC\Column\Venue;

use ACA\EC\Column;

class Events extends Column\Events {

	public function __construct() {
		parent::__construct();

		$this->set_type( 'column-ec-venue_events' )
		     ->set_label( 'Events' );
	}

	protected function get_events_by_id( $id, array $args = [] ) {
		$args = wp_parse_args( $args, [
			'fields' => 'ids',
			'venue'  => $id,
		] );

		return $this->get_events( $args );
	}

}