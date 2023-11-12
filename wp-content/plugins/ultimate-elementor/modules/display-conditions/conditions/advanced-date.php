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
 * Class Advanced Date
 * contain all element of Advanced Date condition
 *
 * @package UltimateElementor\Modules\DisplayConditions\Conditions
 */
class Advanced_Date extends Condition {

	/**
	 * Get Condition Key
	 *
	 * @since 1.34.0
	 * @return string|void
	 */
	public function get_key_name() {
		return 'advanced_date';
	}

	/**
	 * Get Condition Title
	 *
	 * @since 1.34.0
	 * @return string|void
	 */
	public function get_title() {
		return __( 'Advanced Date', 'uael' );
	}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @since 1.34.0
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
	 * @since 1.34.0
	 */
	public function compare_value( $settings, $operator, $value ) {
		$result = '';

		$date  = strtotime( $value );
		$today = UAEL_Helper::get_server_time( 'd-m-Y' );

		if ( 'local' === $settings['display_condition_time_zone'] ) {
			$today = UAEL_Helper::get_local_time( 'd-m-Y' );
		}
		$today = strtotime( $today );

		switch ( $operator ) {
			case 'less':
				if ( $today < $date ) {
					return true;
				}
				break;
			case 'greater':
				if ( $today > $date ) {
					return true;
				}
				break;
			case 'less_than_equal':
				if ( $today >= $date ) {
					return true;
				}
				break;
			case 'greater_than_equal':
				if ( $today <= $date ) {
					return true;
				}
				break;
			default:
				return false;
		}

		return UAEL_Helper::display_conditions_compare( $result, true, $operator );

	}
}
