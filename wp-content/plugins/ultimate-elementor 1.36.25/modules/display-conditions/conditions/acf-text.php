<?php
/**
 * UAEL Display Conditions ACF feature.
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
 * Class Acf_Text
 *
 * @package UltimateElementor\Modules\DisplayConditions\Conditions
 */
class Acf_Text extends Condition {
	/**
	 * Get Name
	 *
	 * Get the name of the module
	 *
	 * @since  1.35.1
	 * @return string
	 */
	public function get_key_name() {
		return 'acf_text';
	}

	/**
	 * ID for ACF field.
	 *
	 * @since 1.35.1
	 * @return string|void
	 */
	public function get_acf_field_name() {
		return 'acf_text_key';
	}

	/**
	 * ID for ACF value field.
	 *
	 * @since 1.35.1
	 * @return string|void
	 */
	public function get_acf_field_value() {
		return 'acf_text_value';
	}
	/**
	 * Get Condition Title
	 *
	 * @since 1.35.1
	 * @return string|void
	 */
	public function get_title() {
		return __( 'ACF Field', 'uael' );
	}

	/**
	 * Get Name Control
	 *
	 * Get the settings for the name control
	 *
	 * @param array $condition Condition.
	 * @since  1.35.1
	 * @return array
	 */
	public function get_acf_field( $condition ) {

		return wp_parse_args(
			array(
				$this->get_acf_field_name(),
				'type'          => 'uael-control-query',
				'description'   => __( 'Search ACF fields ( Types: textual, select, date, boolean, post, taxonomy ) by name.', 'uael' ),
				'placeholder'   => __( 'Search Fields', 'uael' ),
				'post_type'     => '',
				'options'       => array(),
				'query_type'    => 'acf',
				'label_block'   => true,
				'multiple'      => false,
				'query_options' => array(
					'show_type'       => false,
					'show_field_type' => true,
					'field_type'      => array(
						'textual',
						'select',
						'date',
						'boolean',
						'post',
						'taxonomy',
					),
				),
				'condition'     => $condition,
			)
		);
	}

	/**
	 * Get Value Control.
	 * Get the settings for the value control.
	 *
	 * @param array $condition Condition.
	 *
	 * @since  1.35.1
	 * @return array
	 */
	public function get_repeater_control( array $condition ) {
		return array(
			$this->get_acf_field_value(),
			'type'        => Controls_Manager::TEXT,
			'default'     => '',
			'placeholder' => __( 'Value', 'uael' ),
			'label_block' => true,
			'condition'   => $condition,
		);
	}


	/**
	 * Compare Condition value.
	 *
	 * @param array  $settings Extension settings.
	 * @param string $operator Relationship operator.
	 * @param string $key The ACF field key to check.
	 * @param mixed  $value The value to check the key against.
	 *
	 * @return bool|string
	 * @access public
	 * @since 1.35.1
	 */
	public function acf_compare_value( $settings, $operator, $key, $value ) {

		$show = false;

		// Handle string value for correct comparison boolean (true_false) acf field.
		if ( ( 'true_false' === get_field_object( $key )['type'] ) && 'true' === $value ) {
			$value = true;
		}

		global $post;

		$field_value = get_field( $key );

		if ( is_archive() ) {
			$term = get_queried_object();

			if ( get_class( $term ) === 'WP_Term' ) {
				$field_value = get_field( $key, $term );
			}
		}

		if ( $field_value ) {
			$field_settings = get_field_object( $key );

			switch ( $field_settings['type'] ) {
				default:
					$show = $value === $field_value;
					break;
			}
		}

		return UAEL_Helper::display_conditions_compare( $show, true, $operator );
	}
}

