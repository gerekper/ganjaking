<?php

namespace ACA\EC\Column;

use ACA\EC\Export;
use ACA\EC\Settings;
use ACP;

abstract class UpcomingEvent extends Events
	implements ACP\Export\Exportable {

	public function __construct() {
		parent::__construct();

		$this->set_label( __( 'Upcoming Event', 'codepress-admin-columns' ) );
	}

	public function get_value( $id ) {
		$raw_value = $this->get_raw_value( $id );

		if ( ! $raw_value ) {
			return $this->get_empty_char();
		}

		$values = [
			$this->get_formatted_value( ac_helper()->post->get_title( $raw_value ), $raw_value ),
		];

		$setting = $this->get_setting( 'show_event_date' );

		if ( $setting && 'on' === $setting->get_value() ) {
			$values[] = tribe_events_event_schedule_details( $raw_value );
		}

		return implode( '<br>', $values );
	}

	public function get_raw_value( $id ) {
		$event = $this->get_events_by_id( $id, [
			'posts_per_page' => 1,
		] );

		if ( ! $event ) {
			return false;
		}

		return $event[0]->ID;
	}

	public function register_settings() {
		$this->add_setting( new Settings\EventLink( $this ) );
		$this->add_setting( new Settings\ShowEventDate( $this ) );
	}

	public function export() {
		return new Export\Model\UpcomingEvent( $this );
	}

}