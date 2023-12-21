<?php
namespace Happy_Addons_Pro\Extension\Conditions;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Date_Range
 * contain all element of date range condition
 * @package Happy_Addons_Pro\Extension\Conditions
 */
class Date_Range  extends Condition {

	/**
	 * Get Condition Key
	 *
	 * @return string|void
	 */
	public function get_key_name() {
		return 'date_range';
	}

	/**
	 * Get Condition Title
	 *
	 * @return string|void
	 */
	public function get_title() {
		return __( 'Date Range', 'happy-addons-pro' );
	}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @param array $condition
	 * @return array|void
	 */
	public function get_repeater_control(array $condition) {
		$default = date('d-m-Y').' to '.date('d-m-Y', strtotime("+ 2 day") );
		return[
			'label' 		=> $this->get_title(),
			'show_label' 	=> false,
			'type' => Controls_Manager::DATE_TIME,
			'default' => $default,
			'label_block' => true,
			'picker_options' => [
				'enableTime'	=> false,
				'dateFormat' 	=> 'd-m-Y',
				'mode' 			=> 'range',
			],
			'condition'	=> $condition,
		];
	}

	/**
	 * Compare Condition value
	 *
	 * @param $settings
	 * @param $operator
	 * @param $value
	 * @return bool|void
	 */
	public function compare_value( $settings, $operator, $value) {

		$range_date = explode( ' to ', $value );
		if ( !is_array( $range_date ) || 2 !== count( $range_date ) ) return;
		$start = strtotime($range_date[0]);
		$end = strtotime($range_date[1]);

		$today = hapro_get_server_time('d-m-Y');
		if( 'local' === $settings['_ha_time_zone'] ){
			$today = hapro_get_local_time('d-m-Y');
		}
		$today = strtotime($today);

		//if $today is between $start and $end it return true otherwise false
		$result = ( ($today >= $start ) && ( $today <= $end ) );

		return hapro_compare( $result, true, $operator );

	}
}
