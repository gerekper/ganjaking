<?php

namespace ElementPack\Modules\VisibilityControls\Conditions;

use ElementPack\Base\Condition;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	
class Url_String extends Condition {
	
	/**
	 * Get the name of condition
	 * @return string as per our condition control name
	 * @since  5.11.0
	 */
	public function get_name() {
		return 'url_string';
	}
	
	/**
	 * Get the title of condition
	 * @return string as per condition control title
	 * @since  5.11.0
	 */
	public function get_title() {
		return esc_html__( 'URL String', 'bdthemes-element-pack' );
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
			'label'       => __( 'Value', 'bdthemes-element-pack' ),
			'type'        => Controls_Manager::TEXT,
			'label_block' => true,
			'description' => __( 'Enter the string you want to check if exists in the page URL.', 'bdthemes-element-pack' ),
			'ai' => [
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

		$url = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );

		if ( ! $url ) {
			return false;
		}

		$res = false !== strpos( $url, $val ) ? true : false;

		return  $this->compare( $res, true, $relation );
	}
}
