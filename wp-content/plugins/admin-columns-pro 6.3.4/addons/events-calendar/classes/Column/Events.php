<?php

namespace ACA\EC\Column;

use AC;
use ACA\EC\Service\ColumnGroups;
use ACA\EC\Settings;

abstract class Events extends AC\Column {

	public function __construct() {
		$this->set_group( ColumnGroups::EVENTS_CALENDAR );
	}

	/**
	 * @param int   $id
	 * @param array $args
	 *
	 * @return array
	 */
	abstract protected function get_events_by_id( $id, array $args = [] );

	public function get_value( $id ) {
		$value = $this->get_raw_value( $id );

		if ( ! $value ) {
			return $this->get_empty_char();
		}

		$value .= ' ' . _n( 'Event', 'Events', $value, 'the-events-calendar' );

		return $this->get_formatted_value( $value, $id );
	}

	public function get_raw_value( $id ) {
		return count( $this->get_events_by_id( $id ) );
	}

	public function register_settings() {
		$this->add_setting( new Settings\ShowFilterLink( $this ) );
		$this->add_setting( new Settings\EventDisplay( $this ) );
	}

	protected function get_events( array $args ) {
		$args = wp_parse_args( $args, [
			'posts_per_page' => -1,
		] );

		$event_display = $this->get_setting( 'event_display' );

		if ( $event_display ) {
			if ( 'future' === $event_display->get_value() ) {
				$args['start_date'] = date( 'Y-m-d H:i' );
			}

			if ( 'past' === $event_display->get_value() ) {
				$args['end_date'] = date( 'Y-m-d H:i' );
			}
		}

		return tribe_get_events( $args );
	}

	protected function get_upcoming_events( array $args ) {
		$args = wp_parse_args( $args, [
			'start_date' => date( 'Y-m-d H:i:s' ),
		] );

		return $this->get_events( $args );
	}

}