<?php
/**
 * UAEL Display Conditions feature.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\DisplayConditions\Conditions;

use Elementor\Controls_Manager;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Day
 * contain all element of day condition
 *
 * @package UltimateElementor\Modules\DisplayConditions\Conditions
 */
class Day extends Condition {

	/**
	 * Get Condition Key
	 *
	 * @since 1.32.0
	 * @return string|void
	 */
	public function get_key_name() {
		return 'day';
	}

	/**
	 * Get Condition Title
	 *
	 * @since 1.32.0
	 * @return string|void
	 */
	public function get_title() {
		return __( 'Day', 'uael' );
	}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @since 1.32.0
	 * @param array $condition return key's.
	 * @return array|void
	 */
	public function get_repeater_control( array $condition ) {
		return array(
			'label'       => $this->get_title(),
			'show_label'  => false,
			'type'        => Controls_Manager::SELECT,
			'default'     => 'monday',
			'label_block' => true,
			'options'     => array(
				'monday'    => __( 'Monday', 'uael' ),
				'tuesday'   => __( 'Tuesday', 'uael' ),
				'wednesday' => __( 'Wednesday', 'uael' ),
				'thursday'  => __( 'Thursday', 'uael' ),
				'friday'    => __( 'Friday', 'uael' ),
				'saturday'  => __( 'Saturday', 'uael' ),
				'sunday'    => __( 'Sunday', 'uael' ),
			),
			'condition'   => $condition,
		);
	}

	/**
	 * Compare Condition value
	 *
	 * @since 1.32.0
	 * @param String $settings return settings.
	 * @param String $operator return relationship operator.
	 * @param String $value value.
	 * @return bool|void
	 */
	public function compare_value( $settings, $operator, $value ) {

		$today = UAEL_Helper::get_server_time( 'l' );
		if ( 'local' === $settings['display_condition_time_zone'] ) {
			$today = UAEL_Helper::get_local_time( 'l' );
		}

		return UAEL_Helper::display_conditions_compare( strtolower( $today ), $value, $operator );
	}

}
