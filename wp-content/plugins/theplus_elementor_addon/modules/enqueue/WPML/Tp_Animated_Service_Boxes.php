<?php
namespace TheplusAddons\WPML;
use WPML_Elementor_Module_With_Items;

if ( ! defined('ABSPATH') ) exit; // No access of directly access

class Tp_Animated_Service_Boxes extends WPML_Elementor_Module_With_Items {

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
			'loop_sub_title',
			'loop_button_text',
			'loop_content_list',
			'loop_content_desc',
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
				return esc_html__( 'Animated Service Boxes Title', 'theplus' );
			
			case 'loop_sub_title':
				return esc_html__( 'Animated Service Boxes Sub Title', 'theplus' );
			
			case 'loop_button_text':
				return esc_html__( 'Animated Service Boxes Button Text', 'theplus' );
			
			case 'loop_content_list':
				return esc_html__( 'Animated Service Boxes List Content', 'theplus' );
				
			case 'loop_content_desc':
				return esc_html__( 'Animated Service Boxes Description', 'theplus' );

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
			
			case 'loop_sub_title':
				return 'LINE';
			
			case 'loop_button_text':
				return 'LINE';
				
			case 'loop_content_list':
				return 'AREA';
			
			case 'loop_content_desc':
				return 'VISUAL';

			default:
				return '';
		}
	}

}
