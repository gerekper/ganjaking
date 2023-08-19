<?php

namespace ACA\EC\Settings;

use AC;
use AC\View;

class EventDisplay extends AC\Settings\Column {

	/**
	 * @var string
	 */
	private $event_display;

	/**
	 * @return string
	 */
	public function get_event_display() {
		return $this->event_display;
	}

	/**
	 * @param string $event_display
	 */
	public function set_event_display( $event_display ) {
		$this->event_display = $event_display;
	}

	protected function define_options() {
		return [
			'event_display' => 'all',
		];
	}

	public function create_view() {
		$select = $this->create_element( 'select' )
		               ->set_options( $this->get_date_options() );

		$view = new View( [
			'label'   => __( 'Show Events', 'codepress-admin-columns' ),
			'setting' => $select,
		] );

		return $view;
	}

	protected function get_date_options() {
		$options = [
			'all'    => __( 'All' ),
			'future' => __( 'Upcoming Events', 'codepress-admin-columns' ),
			'past'   => __( 'Past Events', 'codepress-admin-columns' ),
		];

		return $options;
	}

}