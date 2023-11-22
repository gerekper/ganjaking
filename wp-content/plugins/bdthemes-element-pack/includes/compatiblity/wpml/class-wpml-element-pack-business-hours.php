<?php

/**
 * Class WPML_ElementPack_Business_Hours
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly


class WPML_ElementPack_Business_Hours extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return ['business_days_times', 'dynamic_days_times'];
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'enter_day', 'enter_time', 'dynamic_enter_day_level', 'dynamic_close_text' );
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title( $field ) {
		switch( $field ) {
			case 'enter_day':
				return esc_html__( 'Enter Day', 'bdthemes-element-pack' );

			case 'enter_time':
				return esc_html__( 'Enter Time', 'bdthemes-element-pack' );

			case 'dynamic_enter_day_level':
				return esc_html__( 'Day Level', 'bdthemes-element-pack' );

			case 'dynamic_close_text':
				return esc_html__( 'Close Level', 'bdthemes-element-pack' );

			default:
				return '';
		}
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'enter_day':
				return 'LINE';

			case 'enter_time':
				return 'LINE';

			case 'dynamic_enter_day_level':
				return 'LINE';

			case 'dynamic_close_text':
				return 'LINE';

			default:
				return '';
		}
	}

}
