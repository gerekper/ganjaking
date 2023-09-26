<?php

namespace ACA\BP\Settings;

use AC;
use AC\View;

/**
 * class ActivityType
 * @since 1.3
 */
class ActivityType extends AC\Settings\Column {

	/**
	 * @var string
	 */
	private $activity_type;

	protected function set_name() {
		$this->name = 'activity_type';
	}

	protected function define_options() {
		return [
			'activity_type' => '',
		];
	}

	public function create_view() {
		$select = $this->create_element( 'select' )
		               ->set_attribute( 'data-refresh', 'column' )
		               ->set_options( $this->get_display_options() );

		$view = new View( [
			'label'   => __( 'Activity Type', 'codepress-admin-columns' ),
			'setting' => $select,
		] );

		return $view;
	}

	protected function get_display_options() {
		$options = [
			'' => __( 'All' ),
		];

		$activities = bp_activity_get_actions();
		foreach ( $activities->activity as $activity ) {
			$options[ $activity['key'] ] = $activity['value'];
		}

		return $options;
	}

	/**
	 * @return string
	 */
	public function get_activity_type() {
		return $this->activity_type;
	}

	/**
	 * @param string $activity_type
	 *
	 * @return bool
	 */
	public function set_activity_type( $activity_type ) {
		$this->activity_type = $activity_type;

		return true;
	}

}