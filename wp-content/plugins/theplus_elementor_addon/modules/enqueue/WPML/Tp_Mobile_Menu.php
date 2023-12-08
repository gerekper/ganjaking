<?php
namespace TheplusAddons\WPML;
use WPML_Elementor_Module_With_Items;

if ( ! defined('ABSPATH') ) exit; // No access of directly access

class Tp_Mobile_Menu extends WPML_Elementor_Module_With_Items {

	/**
	 * Get widget field name.
	 * 
	 * @return string
	 */
	public function get_items_field() {
		return 'mm_st1_content';
	}

	/**
	 * Get the fields inside the repeater.
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'mm_st1_text',
			'mm_st1_pin_text'
		);
	}

  	/**
     * @param string $field
	 * 
	 * Get the field title string
     *
     * @return string
     */
	protected function get_title( $field ) {
		switch($field) {
			case 'mm_st1_text':
				return esc_html__( 'Mobile Menu : Menu 1 Text', 'theplus' );
				
			case 'mm_st1_pin_text':
				return esc_html__( 'Mobile Menu : Menu 1 Pin Text', 'theplus' );
				
			default:
				return '';
		}
	}

	/**
	 * @param string $field
	 * 
	 * Get perspective field types.
	 *
	 * @return string
	 */
	protected function get_editor_type( $field ) {
		switch($field) {
			case 'mm_st1_text':
				return 'LINE';
			
			case 'mm_st1_pin_text':
				return 'LINE';

			default:
				return '';
		}
	}

}
