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
 * Class Date_Range
 * contain all element of date range condition
 *
 * @package UltimateElementor\Modules\DisplayConditions\Conditions
 */
class Date_Range extends Condition {

	/**
	 * Get Condition Key
	 *
	 * @since 1.32.0
	 * @return string|void
	 */
	public function get_key_name() {
		return 'date_range';
	}

	/**
	 * Get Condition Title
	 *
	 * @since 1.32.0
	 * @return string|void
	 */
	public function get_title() {
		return __( 'Date Range', 'uael' );
	}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @since 1.32.0
	 * @param array $condition return key's.
	 *
	 * @return array|void
	 */
	public function get_repeater_control( array $condition ) {

		$default = gmdate( 'd-m-Y' ) . ' to ' . gmdate( 'd-m-Y', strtotime( '+ 2 day' ) );

		return array(
			'label'          => $this->get_title(),
			'show_label'     => false,
			'type'           => Controls_Manager::DATE_TIME,
			'default'        => $default,
			'label_block'    => true,
			'picker_options' => array(
				'enableTime' => false,
				'dateFormat' => 'd-m-Y',
				'mode'       => 'range',
			),
			'condition'      => $condition,
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

		$range_date = explode( ' to ', $value );
		if ( ! is_array( $range_date ) || 2 !== count( $range_date ) ) {
			return;
		}
		$start = strtotime( $range_date[0] );
		$end   = strtotime( $range_date[1] );

		$today = UAEL_Helper::get_server_time( 'd-m-Y' );
		if ( 'local' === $settings['display_condition_time_zone'] ) {
			$today = UAEL_Helper::get_local_time( 'd-m-Y' );
		}
		$today = strtotime( $today );

		// if $today is between $start and $end it return true otherwise false.
		$result = ( ( $today >= $start ) && ( $today <= $end ) );
		return UAEL_Helper::display_conditions_compare( $result, true, $operator );

	}
}
