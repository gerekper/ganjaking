<?php
namespace TheplusAddons\WPML;
use WPML_Elementor_Module_With_Items;

if ( ! defined('ABSPATH') ) exit; // No access of directly access

class Tp_Timeline extends WPML_Elementor_Module_With_Items {

	/**
	 * Get widget field name.
	 * 
	 * @return string
	 */
	public function get_items_field() {
		return 'content_loop_section';
	}

	/**
	 * Get the fields inside the repeater.
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'pin_title',
			'loop_content_title',
			'loop_content_desc',
			'loop_button_text'
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
			case 'pin_title':
				return esc_html__( 'Timeline : Pin Title', 'theplus' );
			
			case 'loop_content_title':
				return esc_html__( 'Timeline : Title', 'theplus' );
				
			case 'loop_content_desc':
				return esc_html__( 'Timeline : Description', 'theplus' );
				
			case 'loop_button_text':
				return esc_html__( 'Timeline : Button Text', 'theplus' );
				
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
			case 'pin_title':
				return 'LINE';

			case 'loop_content_title':
				return 'LINE';
			
			case 'loop_content_desc':
				return 'VISUAL';
			
			case 'loop_button_text':
				return 'LINE';

			default:
				return '';
		}
	}

}
