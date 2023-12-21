<?php
namespace Happy_Addons_Pro\Extension\Conditions;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Day
 * contain all element of day condition
 * @package Happy_Addons_Pro\Extension\Conditions
 */
class Day  extends Condition {

	/**
	 * Get Condition Key
	 *
	 * @return string|void
	 */
	public function get_key_name() {
		return 'day';
	}

	/**
	 * Get Condition Title
	 *
	 * @return string|void
	 */
	public function get_title() {
		return __( 'Day', 'happy-addons-pro' );
	}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @param array $condition
	 * @return array|void
	 */
	public function get_repeater_control(array $condition) {
		return[
			'label' 		=> $this->get_title(),
			'show_label' 	=> false,
			'type' => Controls_Manager::SELECT,
			'default' => 'monday',
			'label_block' => true,
			'options' => [
				'monday'    => __( 'Monday', 'happy-addons-pro' ),
				'tuesday'   => __( 'Tuesday', 'happy-addons-pro' ),
				'wednesday' => __( 'Wednesday', 'happy-addons-pro' ),
				'thursday'  => __( 'Thursday', 'happy-addons-pro' ),
				'friday'    => __( 'Friday', 'happy-addons-pro' ),
				'saturday'  => __( 'Saturday', 'happy-addons-pro' ),
				'sunday'    => __( 'Sunday', 'happy-addons-pro' ),
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

		$today = hapro_get_server_time('l');
		if( 'local' === $settings['_ha_time_zone'] ){
			$today = hapro_get_local_time('l');
		}

		return hapro_compare( strtolower($today), $value, $operator );
	}

}
