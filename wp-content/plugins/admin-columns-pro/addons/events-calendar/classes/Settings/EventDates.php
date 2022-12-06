<?php

namespace ACA\EC\Settings;

use AC;
use AC\View;

class EventDates extends AC\Settings\Column {

	/**
	 * @var string
	 */
	private $event_date;

	/**
	 * @return string
	 */
	public function get_event_date() {
		return $this->event_date;
	}

	/**
	 * @param string $event_date
	 */
	public function set_event_date( $event_date ) {
		$this->event_date = $event_date;
	}

	protected function define_options() {
		return [
			'event_date' => '_EventStartDate',
		];
	}

	public function create_view() {
		$select = $this->create_element( 'select' )
		               ->set_options( $this->get_date_options() );

		$view = new View( [
			'label'   => __( 'Event Date', 'codepress-admin-columns' ),
			'setting' => $select,
		] );

		return $view;
	}

	protected function get_date_options() {
		$options = [
			'_EventStartDate' => __( 'Start Date' ),
			'_EventEndDate'   => __( 'End Date' ),
		];

		return $options;
	}

}