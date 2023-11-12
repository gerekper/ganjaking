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
 * Class Date
 * contain all element of date condition
 *
 * @package UltimateElementor\Modules\DisplayConditions\Conditions
 */
class Date extends Condition {

	/**
	 * Get Condition Key
	 *
	 * @since 1.32.0
	 * @return string|void
	 */
	public function get_key_name() {
		return 'date';
	}

	/**
	 * Get Condition Title
	 *
	 * @since 1.32.0
	 * @return string|void
	 */
	public function get_title() {
		return __( 'Date', 'uael' );
	}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @since 1.32.0
	 * @param array $condition return key's.
	 * @return array|void
	 */
	public function get_repeater_control( array $condition ) {
		$default = gmdate( 'd-m-Y' );
		return array(
			'label'          => $this->get_title(),
			'show_label'     => false,
			'type'           => Controls_Manager::DATE_TIME,
			'default'        => $default,
			'label_block'    => true,
			'picker_options' => array(
				'enableTime' => false,
				'dateFormat' => 'd-m-Y',
			),
			'condition'      => $condition,
		);
	}

	/**
	 * Compare Condition value
	 *
	 * @param String $settings return settings.
	 * @param String $operator return relationship operator.
	 * @param String $value value.
	 * @return bool|void
	 * @since 1.32.0
	 */
	public function compare_value( $settings, $operator, $value ) {
		$result = '';
		$date   = strtotime( $value );
		$today  = UAEL_Helper::get_server_time( 'd-m-Y' );
		if ( 'local' === $settings['display_condition_time_zone'] ) {
			$today = UAEL_Helper::get_local_time( 'd-m-Y' );
		}
		$today = strtotime( $today );

		if ( $today > $date || $today < $date ) {
			$result = false;
		} elseif ( $today === $date ) {
			$result = true;
		}

		return UAEL_Helper::display_conditions_compare( $result, true, $operator );

	}
}
