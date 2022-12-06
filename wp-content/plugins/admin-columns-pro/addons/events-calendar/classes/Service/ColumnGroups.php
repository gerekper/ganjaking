<?php

namespace ACA\EC\Service;

use AC;
use AC\Registerable;

final class ColumnGroups implements Registerable {

	const EVENTS_CALENDAR = 'events_calendar';
	const EVENTS_CALENDAR_FIELDS = 'events_calendar_fields';

	public function register() {
		add_action( 'ac/column_groups', [ $this, 'register_column_groups' ] );
	}

	/**
	 * @param AC\Groups $groups
	 */
	public function register_column_groups( $groups ) {
		$groups->register_group( self::EVENTS_CALENDAR, 'The Events Calendar', 11 );
		$groups->register_group( self::EVENTS_CALENDAR_FIELDS, 'The Events Calendar' . ' - ' . __( 'Additional Fields', 'tribe-events-calendar-pro' ), 11 );
	}

}