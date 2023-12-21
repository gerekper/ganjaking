<?php

namespace Happy_Addons_Pro\Extension\Conditions;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Browser
 * contain all element of browser condition
 * @package Happy_Addons_Pro\Extension\Conditions
 */
class Browser extends Condition {

	/**
	 * Get Condition Key
	 *
	 * @return string|void
	 */
	public function get_key_name () {
		return 'browser';
	}

	/**
	 * Get Condition Title
	 *
	 * @return string|void
	 */
	public function get_title () {
		return __( 'Browser', 'happy-addons-pro' );
	}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @param array $condition
	 * @return array|void
	 */
	public function get_repeater_control ( array $condition ) {
		return [
			'label' => $this->get_title(),
			'show_label' => false,
			'type' => Controls_Manager::SELECT,
			'default' => 'chrome',
			'label_block' => true,
			'options' 		=> [
				'opera'			=> __( 'Opera', 'happy-addons-pro' ),
				'edge'			=> __( 'Edge', 'happy-addons-pro' ),
				'chrome'		=> __( 'Google Chrome', 'happy-addons-pro' ),
				'safari'		=> __( 'Safari', 'happy-addons-pro' ),
				'firefox'		=> __( 'Mozilla Firefox', 'happy-addons-pro' ),
				'ie'			=> __( 'Internet Explorer', 'happy-addons-pro' ),
				'others'			=> __( 'Others', 'happy-addons-pro' ),
			],
			'condition' => $condition,
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
		$user_agent = hapro_get_browser_name( $_SERVER['HTTP_USER_AGENT'] );
		//if $user_agent and $value is equal it return true
		return hapro_compare( $user_agent, $value, $operator );
	}
}
