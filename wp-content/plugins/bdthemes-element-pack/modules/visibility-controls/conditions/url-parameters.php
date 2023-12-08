<?php

namespace ElementPack\Modules\VisibilityControls\Conditions;

use ElementPack\Base\Condition;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	
class Url_Parameters extends Condition {
	
	/**
	 * Get the name of condition
	 * @return string as per our condition control name
	 * @since  5.11.0
	 */
	public function get_name() {
		return 'url_parameters';
	}
	
	/**
	 * Get the title of condition
	 * @return string as per condition control title
	 * @since  5.11.0
	 */
	public function get_title() {
		return esc_html__( 'URL Parameters', 'bdthemes-element-pack' );
	}

	/**
	 * Get the group of condition
	 * @return string as per our condition control name
	 * @since  6.11.3
	 */
	public function get_group() {
		return 'url';
	}
	
	/**
	 * Get the control value
	 * @return array as per condition control value
	 * @since  5.11.0
	 */
	public function get_control_value() {
		return [
			'type'        => Controls_Manager::TEXTAREA,
			'placeholder' => 'param1=value1
param2=value2',
			'description' => __( 'Enter each parameter of URL on a new line as pairs of param=value', 'bdthemes-element-pack' ),
			'ai'          => [
				'active' => false,
			],
		];
	}
	
	/**
	 * Check the condition
	 * @param string $relation Comparison operator for compare function
	 * @param mixed $val will check the control value as per condition needs
	 * @since 5.11.0
	 */
	public function check( $relation, $val ) {
		if ( ! isset( $_SERVER['REQUEST_URI'] ) || empty( $_SERVER['REQUEST_URI'] ) ) {
			return;
		}

		$url = wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) );

		if ( ! $url || ! isset( $url['query'] ) || empty( $url['query'] ) ) {
			return false;
		}

		$query_params = explode( '&', $url['query'] );

		$val = explode( "\n", sanitize_textarea_field( $val ) );

		foreach ( $val as $index => $param ) {

			$is_strict = strpos( $param, '=' );
			if ( ! $is_strict ) {

				$ref = isset( $_GET[ $param ] ) ? sanitize_text_field( wp_unslash( $_GET[ $param ] ) ) : '';

				$val[ $index ] = $val[ $index ] . '=' . rawurlencode( $ref );
			}
		}

		$res = ! empty( array_intersect( $val, $query_params ) ) ? true : false;

		return  $this->compare( $res, true, $relation );
	}
}
