<?php

/**
 * Advanced Tabs integration
 */

namespace Happy_Addons_Pro;

defined('ABSPATH') || die();

class WPML_Advanced_Slider extends WPML_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'slides';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return [
			'slide_content_title',
			'slide_content_sub_title',
			'slide_content_description',
			'slide_content_button_1_text',
			'slide_content_button_1_link' => ['url'],
			'slide_content_button_2_text',
			'slide_content_button_2_link' => ['url'],
		];
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'slide_content_title':
				return __( 'Advanced Slider: Title', 'happy-addons-pro' );
			case 'slide_content_sub_title':
				return __( 'Advanced Slider: Sub Title', 'happy-addons-pro' );
			case 'slide_content_description':
				return __( 'Advanced Slider: Description', 'happy-addons-pro' );
			case 'slide_content_button_1_text':
				return __( 'Advanced Slider: Button 1 Text', 'happy-addons-pro' );
			case 'slide_content_button_1_link':
				return __( 'Advanced Slider: Button 1 Link', 'happy-addons-pro' );
			case 'slide_content_button_2_text':
				return __( 'Advanced Slider: Button 2 Text', 'happy-addons-pro' );
			case 'slide_content_button_2_link':
				return __( 'Advanced Slider: Button 2 Link', 'happy-addons-pro' );
			default:
				return '';
		}
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'slide_content_title':
				return 'LINE';
			case 'slide_content_sub_title':
				return 'LINE';
			case 'slide_content_description':
				return 'AREA';
			case 'slide_content_button_1_text':
				return 'LINE';
			case 'slide_content_button_1_link':
				return 'LINK';
			case 'slide_content_button_2_text':
				return 'LINE';
			case 'slide_content_button_2_link':
				return 'LINK';
			default:
				return '';
		}
	}
}
