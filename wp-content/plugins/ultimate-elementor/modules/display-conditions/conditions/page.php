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
 * Contain all element of page condition
 *
 * @package UltimateElementor\Modules\DisplayConditions\Conditions
 */
class Page extends Condition {

	/**
	 * Get Condition Key
	 *
	 * @since 1.32.0
	 * @return string|void
	 */
	public function get_key_name() {
		return 'page';
	}

	/**
	 * Get Title
	 *
	 * Get the title of the module
	 *
	 * @since 1.32.0
	 * @return string
	 */
	public function get_title() {
		return __( 'Page', 'uael' );
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
			'label'     => __( 'Select Page', 'uael' ),
			'type'      => 'uael-query-posts',
			'post_type' => 'page',
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
		$show    = false;
		$page_id = get_the_id();

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( $page_id == $_value ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison

					if ( 'not' !== $operator ) {
						return UAEL_Helper::display_conditions_compare( true, true, $operator );
					} else {
						$show = true;
					}
				}
			}
		}

		if ( 'not' === $operator ) {
			return UAEL_Helper::display_conditions_compare( $show, true, $operator );
		}

		return false;
	}
}
