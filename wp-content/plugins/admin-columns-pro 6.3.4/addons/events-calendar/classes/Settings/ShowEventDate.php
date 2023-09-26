<?php

namespace ACA\EC\Settings;

use AC;

class ShowEventDate extends AC\Settings\Column\Toggle {

	/**
	 * @var string
	 */
	private $show_event_date;

	protected function define_options() {
		return [
			'show_event_date' => 'on',
		];
	}

	public function create_view() {
		$view = parent::create_view();

		$view->set_data( [
			'label'   => __( 'Show Event Date', 'codepress-admin-columns' ),
			'tooltip' => __( 'This will show the event date below the events title.', 'codepress-admin-columns' ),
		] );

		return $view;
	}

	/**
	 * @return string
	 */
	public function get_show_event_date() {
		return $this->show_event_date;
	}

	/**
	 * @param string $show_event_date
	 *
	 * @return bool
	 */
	public function set_show_event_date( $show_event_date ) {
		$this->show_event_date = $show_event_date;

		return true;
	}

}