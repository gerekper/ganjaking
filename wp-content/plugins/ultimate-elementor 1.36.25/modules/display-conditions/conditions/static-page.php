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
 *
 * Contain all element of static page condition
 *
 * @package UltimateElementor\Modules\DisplayConditions\Conditions
 */
class Static_Page extends Condition {

	/**
	 * Get Condition Key
	 *
	 * @since 1.32.0
	 * @return string|void
	 */
	public function get_key_name() {
		return 'static_page';
	}

	/**
	 * Get Condition Title
	 *
	 * @since 1.32.0
	 * @return string|void
	 */
	public function get_title() {
		return __( 'Static Page', 'uael' );
	}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @since 1.32.0
	 * @param array $condition return key's.
	 * @return array|void
	 */
	public function get_repeater_control( array $condition ) {
		return array(
			'type'        => Controls_Manager::SELECT,
			'default'     => 'home',
			'label_block' => true,
			'options'     => array(
				'home'   => __( 'Homepage', 'uael' ),
				'static' => __( 'Front Page', 'uael' ),
				'blog'   => __( 'Blog', 'uael' ),
				'404'    => __( '404 Page', 'uael' ),
			),
			'condition'   => $condition,
		);
	}

	/**
	 * Check condition
	 *
	 * @access public
	 *
	 * @param String $settings return settings.
	 * @param String $operator return relationship operator.
	 * @param String $value value.
	 * @since 1.32.0
	 */
	public function compare_value( $settings, $operator, $value ) {
		if ( 'home' === $value ) {
			return UAEL_Helper::display_conditions_compare( ( is_front_page() && is_home() ), true, $operator );
		} elseif ( 'static' === $value ) {
			return UAEL_Helper::display_conditions_compare( ( is_front_page() && ! is_home() ), true, $operator );
		} elseif ( 'blog' === $value ) {
			return UAEL_Helper::display_conditions_compare( ( ! is_front_page() && is_home() ), true, $operator );
		} elseif ( '404' === $value ) {
			return UAEL_Helper::display_conditions_compare( is_404(), true, $operator );
		}
	}
}
