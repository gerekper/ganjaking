<?php
namespace TheplusAddons\WPML;
use WPML_Elementor_Module_With_Items;

if ( ! defined('ABSPATH') ) exit; // No access of directly access

class Tp_Process_Steps extends WPML_Elementor_Module_With_Items {

	/**
	 * Get widget field name.
	 * 
	 * @return string
	 */
	public function get_items_field() {
		return 'loop_content';
	}

	/**
	 * Get the fields inside the repeater.
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'loop_title',
			'loop_content_desc',
			'loop_select_text',
			'dis_counter_custom_text'
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
			case 'loop_title':
				return esc_html__( 'Process/Steps : Title', 'theplus' );
			
			case 'loop_content_desc':
				return esc_html__( 'Process/Steps : Description', 'theplus' );
				
			case 'loop_select_text':
				return esc_html__( 'Process/Steps : Text', 'theplus' );
				
			case 'dis_counter_custom_text':
				return esc_html__( 'Process/Steps : Custom Text', 'theplus' );
			
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
			case 'loop_title':
				return 'LINE';

			case 'loop_content_desc':
				return 'VISUAL';
			
			case 'loop_select_text':
				return 'LINE';
			
			case 'dis_counter_custom_text':
				return 'LINE';

			default:
				return '';
		}
	}

}
