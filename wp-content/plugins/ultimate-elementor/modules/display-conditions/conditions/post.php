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
 * Contain all element of post condition
 *
 * @package UltimateElementor\Modules\DisplayConditions\Conditions
 */
class Post extends Condition {

	/**
	 * Get Condition Key
	 *
	 * @since 1.32.0
	 * @return string|void
	 */
	public function get_key_name() {
		return 'post';
	}

	/**
	 * Get Condition Title
	 *
	 * @since 1.32.0
	 * @return string|void
	 */
	public function get_title() {
		return __( 'Post', 'uael' );
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
			'label'     => __( 'Select Post', 'uael' ),
			'type'      => 'uael-query-posts',
			'post_type' => 'post',
			'multiple'  => true,
			'condition' => $condition,
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
		$show = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( is_single( $_value ) || is_singular( $_value ) ) {
					$show = true;
					break;
				}
			}
		} else {
			$show = is_single( $value ) || is_singular( $value );
		}

		return UAEL_Helper::display_conditions_compare( $show, true, $operator );
	}
}
