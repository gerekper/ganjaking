<?php
namespace Happy_Addons_Pro\Extension\Conditions;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Time
 * contain all element of time condition
 * @package Happy_Addons_Pro\Extension\Conditions
 */
class Time  extends Condition {

	/**
	 * Get Condition Key
	 *
	 * @return string|void
	 */
	public function get_key_name() {
		return 'time';
	}

	/**
	 * Get Condition Title
	 *
	 * @return string|void
	 */
	public function get_title() {
		return __( 'Time', 'happy-addons-pro' );
	}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @param array $condition
	 * @return array|void
	 */
	public function get_repeater_control(array $condition) {
		$default = date('H:i');
		return[
			'label' 		=> $this->get_title(),
			'show_label' 	=> false,
			'type' => Controls_Manager::DATE_TIME,
			'default' => '12:00',
			'label_block' => true,
			'picker_options' => [
				'noCalendar' 	=> true,
				'enableTime'	=> true,
				'dateFormat' 	=> "H:i",
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
	public function compare_value ( $settings, $operator, $value ) {

		$time = strtotime($value);

		$local_time = hapro_get_server_time('H:i');
		if( 'local' === $settings['_ha_time_zone'] ){
			$local_time = hapro_get_local_time('H:i');
		}
		$local_time = strtotime($local_time);

		//if time is equal or grater then local time it return true
		$result = ( $time <= $local_time );

		return hapro_compare( $result, true, $operator );
	}

}
