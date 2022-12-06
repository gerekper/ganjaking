<?php

namespace ACA\EC\Export\Model;

use ACP;

/**
 * Export Model for Upcoming Event column
 * @since 1.0.2
 */
class UpcomingEvent extends ACP\Export\Model {

	public function get_value( $id ) {
		$event_id = $this->column->get_raw_value( $id );

		if ( ! $event_id ) {
			return false;
		}

		return get_the_title( $event_id );
	}

}