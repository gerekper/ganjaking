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
 *
 * Contain all element of post condition
 *
 * @package UltimateElementor\Modules\DisplayConditions\Conditions
 */
class Time_Span extends Condition {

	/**
	 * Get Condition Key
	 *
	 * @since 1.32.0
	 * @return string|void
	 */
	public function get_key_name() {
		return 'time_span';
	}

	/**
	 * Get start time
	 *
	 * @since 1.32.0
	 * @return string|void
	 */
	public function get_start_time() {
		return 'time_span_start';
	}

	/**
	 * Get due time
	 *
	 * @since 1.32.0
	 * @return string|void
	 */
	public function get_due_time() {
		return 'time_span_end';
	}
	/**
	 * Get Condition Title
	 *
	 * @since 1.32.0
	 * @return string|void
	 */
	public function get_title() {
		return __( 'Time Span', 'uael' );
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
			$this->get_start_time(),
			'label'          => __( 'Start Time', 'uael' ), // get_titel we are using.
			'show_label'     => true,
			'type'           => Controls_Manager::DATE_TIME,
			'default'        => '10:00',
			'label_block'    => true,
			'picker_options' => array(
				'noCalendar' => true,
				'enableTime' => true,
				'dateFormat' => 'H:i',
			),
			'condition'      => $condition,
		);
	}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @since 1.32.0
	 * @param array $condition return key's.
	 * @return array|void
	 */
	public function get_due_control( array $condition ) {
		return array(
			$this->get_due_time(),
			'label'          => __( 'Due Time', 'uael' ), // get_titel we are using.
			'show_label'     => true,
			'type'           => Controls_Manager::DATE_TIME,
			'default'        => '11:00',
			'label_block'    => true,
			'picker_options' => array(
				'noCalendar' => true,
				'enableTime' => true,
				'dateFormat' => 'H:i',
			),
			'condition'      => $condition,
		);
	}

	/**
	 * Compare Condition value
	 *
	 * @param String $settings return settings.
	 * @param String $operator return relationship operator.
	 * @param String $start_time start time.
	 * @param String $end_time end time.
	 * @return bool|void
	 * @since 1.32.0
	 */
	public function time_compare_value( $settings, $operator, $start_time, $end_time ) {

		$s_time = gmdate( 'H:i', strtotime( preg_replace( '/\s+/', '', $start_time ) ) );
		$e_time = gmdate( 'H:i', strtotime( preg_replace( '/\s+/', '', $end_time ) ) );

		$show = false;

		if ( \DateTime::createFromFormat( 'H:i', $s_time ) === false && \DateTime::createFromFormat( 'H:i', $e_time ) === false ) { // Make sure it's a valid DateTime format.
			return;
		}

		$now = UAEL_Helper::get_server_time( 'H:i' );

		if ( 'local' === $settings['display_condition_time_zone'] ) {
			$now = UAEL_Helper::get_local_time( 'H:i' );
		}

		$now_ts    = strtotime( $now );
		$s_time_ts = strtotime( $s_time );
		$e_time_ts = strtotime( $e_time );

		$show = ( $s_time_ts <= $now_ts ) && ( $now_ts <= $e_time_ts );

		return UAEL_Helper::display_conditions_compare( $show, true, $operator );
	}
}
