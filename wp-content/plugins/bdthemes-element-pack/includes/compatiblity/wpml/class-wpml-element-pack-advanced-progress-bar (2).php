<?php

/**
 * Class WPML_ElementPack_Advanced_Progress_Bar
 */
if (!defined('ABSPATH')) exit; // Exit if accessed directly
class WPML_ElementPack_Advanced_Progress_Bar extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'progress_bars';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'name' );
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title( $field ) {
		switch( $field ) {

			case 'name':
				return esc_html__( 'Progress Name', 'bdthemes-element-pack' );

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
			case 'name':
				return 'LINE';

			default:
				return '';
		}
	}

}
