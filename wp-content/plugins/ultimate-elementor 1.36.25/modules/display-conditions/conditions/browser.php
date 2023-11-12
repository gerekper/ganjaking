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
 * Class Browser
 * contain all element of browser condition
 *
 * @package UltimateElementor\Modules\DisplayConditions\Conditions
 */
class Browser extends Condition {

	/**
	 * Get Condition Key
	 *
	 * @since 1.32.0
	 * @return string|void
	 */
	public function get_key_name() {
		return 'browser';
	}

	/**
	 * Get Condition Title
	 *
	 * @since 1.32.0
	 * @return string|void
	 */
	public function get_title() {
		return __( 'Browser', 'uael' );
	}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @param array $condition return key's.
	 * @return array|void
	 * @since 1.32.0
	 */
	public function get_repeater_control( array $condition ) {
		return array(
			'label'       => $this->get_title(),
			'show_label'  => false,
			'type'        => Controls_Manager::SELECT2,
			'default'     => 'chrome',
			'label_block' => true,
			'options'     => array(
				'opera'   => __( 'Opera', 'uael' ),
				'edge'    => __( 'Microsoft Edge', 'uael' ),
				'chrome'  => __( 'Google Chrome', 'uael' ),
				'safari'  => __( 'Safari', 'uael' ),
				'firefox' => __( 'Mozilla Firefox', 'uael' ),
				'ie'      => __( 'Internet Explorer', 'uael' ),
			),
			'multiple'    => true,
			'condition'   => $condition,
		);

	}

	/**
	 * Compare Condition value
	 *
	 * @since 1.32.0
	 * @param String $settings return settings.
	 * @param String $operator return relationship operator.
	 * @param String $value value.
	 * @return bool|void
	 */
	public function compare_value( $settings, $operator, $value ) {

		$show       = false;
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? UAEL_Helper::get_browser_name( sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) ) : ''; // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___SERVER__HTTP_USER_AGENT__

		$show = is_array( $value ) && ! empty( $value ) ? in_array( $user_agent, $value, true ) : $value === $user_agent;

		// if $user_agent and $value is equal it return true.
		return UAEL_Helper::display_conditions_compare( $show, true, $operator );
	}
}
