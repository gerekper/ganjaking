<?php
/**
 * UAEL Display Conditions feature.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\DisplayConditions\Conditions;

use Elementor\Controls_Manager;
use Elementor\Utils;
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
class Request_Parameter extends Condition {

	/**
	 * Get Condition Key
	 *
	 * @since 1.33.1
	 * @return string|void
	 */
	public function get_key_name() {
		return 'request_parameter';
	}

	/**
	 * Get key.
	 *
	 * @since 1.33.1
	 * @return string|void
	 */
	public function get_req_key() {
		return 'req_param_key';
	}

	/**
	 * Get value.
	 *
	 * @since 1.33.1
	 * @return string|void
	 */
	public function get_req_value() {
		return 'req_param_value';
	}
	/**
	 * Get Condition Title
	 *
	 * @since 1.33.1
	 * @return string|void
	 */
	public function get_title() {
		return __( 'Request Parameter', 'uael' );
	}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @since 1.33.1
	 * @param array $condition return key's.
	 * @return array|void
	 */
	public function get_repeater_control( array $condition ) {
		return array(
			$this->get_req_key(),
			'label'       => __( 'Enter Key', 'uael' ),
			'show_label'  => true,
			'type'        => Controls_Manager::TEXT,
			'label_block' => true,
			'condition'   => $condition,
		);
	}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @since 1.33.1
	 * @param array $condition return key's.
	 * @return array|void
	 */
	public function get_value_control( array $condition ) {
		return array(
			$this->get_req_value(),
			'label'       => __( 'Enter Value', 'uael' ),
			'show_label'  => true,
			'type'        => Controls_Manager::TEXT,
			'label_block' => true,
			'condition'   => $condition,
		);
	}

	/**
	 * Compare Condition value
	 *
	 * @param String $settings return settings.
	 * @param String $operator return relationship operator.
	 * @param String $key get key.
	 * @param String $value get value.
	 * @return bool|void
	 * @since 1.33.1
	 */
	public function compare_request_param( $settings, $operator, $key, $value ) {

		$show = false;

		$current_url = isset( $_SERVER['REQUEST_URI'] ) ? basename( esc_url_raw( $_SERVER['REQUEST_URI'] ) ) : '';

		$url_components = wp_parse_url( $current_url );

		if ( isset( $url_components['query'] ) ) {

			parse_str( $url_components['query'], $params );

			$show = ( isset( $params[ $key ] ) && $value === $params[ $key ] ) ? true : false;

			return UAEL_Helper::display_conditions_compare( $show, true, $operator );
		}

		return false;
	}
}
