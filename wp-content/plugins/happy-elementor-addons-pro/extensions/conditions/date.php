<?php
namespace Happy_Addons_Pro\Extension\Conditions;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Date
 * contain all element of date condition
 * @package Happy_Addons_Pro\Extension\Conditions
 */
class Date  extends Condition {

	/**
	 * Get Condition Key
	 *
	 * @return string|void
	 */
	public function get_key_name() {
		return 'date';
	}

	/**
	 * Get Condition Title
	 *
	 * @return string|void
	 */
	public function get_title() {
		return __( 'Date', 'happy-addons-pro' );
	}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @param array $condition
	 * @return array|void
	 */
	public function get_repeater_control(array $condition) {
		$default = date('d-m-Y');
		return[
			'label' 		=> $this->get_title(),
			'show_label' 	=> false,
			'type' => Controls_Manager::DATE_TIME,
			'default' => $default,
			'label_block' => true,
			'picker_options' => [
				'enableTime'	=> false,
				'dateFormat' 	=> 'd-m-Y',
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
		$date = strtotime($value);
		$today = hapro_get_server_time('d-m-Y');
		if( 'local' === $settings['_ha_time_zone'] ){
			$today = hapro_get_local_time('d-m-Y');
		}
		$today = strtotime($today);

		//if $today is equal to $date or grater then $date it return true otherwise false
		$result = ( ($today >= $date ) );

		return hapro_compare( $result, true, $operator );

	}
}
