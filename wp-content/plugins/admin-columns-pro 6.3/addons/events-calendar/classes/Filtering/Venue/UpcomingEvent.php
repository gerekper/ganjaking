<?php

namespace ACA\EC\Filtering\Venue;

use ACA\EC\Filtering;

class UpcomingEvent extends Filtering\UpcomingEvent {

	protected function get_related_meta_key() {
		return '_EventVenueID';
	}

}