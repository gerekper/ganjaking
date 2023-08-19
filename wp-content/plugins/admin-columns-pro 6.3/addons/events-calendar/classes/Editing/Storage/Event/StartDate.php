<?php

namespace ACA\EC\Editing\Storage\Event;

use ACP;
use DateTime;

class StartDate implements ACP\Editing\Storage {

	public function get( int $id ) {
		return get_post_meta( $id, '_EventStartDate', true );
	}

	public function update( int $id, $data ): bool {
		$start_date = DateTime::createFromFormat( 'Y-m-d H:i:s', $data );
		$end_date = DateTime::createFromFormat( 'Y-m-d H:i:s', get_post_meta( $id, '_EventEndDate', true ) );

		$args = [
			'EventStartDate'   => $start_date->format( 'Y-m-d' ),
			'EventStartHour'   => $start_date->format( 'H' ),
			'EventStartMinute' => $start_date->format( 'i' ),
			'EventEndDate'     => $end_date->format( 'Y-m-d' ),
			'EventEndHour'     => $end_date->format( 'H' ),
			'EventEndMinute'   => $end_date->format( 'i' ),
		];

		tribe_update_event( $id, $args );

		return true;
	}

}