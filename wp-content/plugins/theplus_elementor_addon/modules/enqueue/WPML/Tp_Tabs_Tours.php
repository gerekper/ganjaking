<?php
namespace TheplusAddons\WPML;
use WPML_Elementor_Module_With_Items;

if ( ! defined('ABSPATH') ) exit; // No access of directly access

class Tp_Tabs_Tours extends WPML_Elementor_Module_With_Items {

	/**
	 * Get widget field name.
	 * 
	 * @return string
	 */
	public function get_items_field() {
		return 'tabs';
	}

	/**
	 * Get the fields inside the repeater.
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'tab_title',
			'tab_content',
			'content_template_id'
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
			case 'tab_title':
				return esc_html__( 'Tabs Tour : Title', 'theplus' );
			
			case 'tab_content':
				return esc_html__( 'Tabs Tour : Content', 'theplus' );
				
			case 'content_template_id':
				return esc_html__( 'Tabs Tour : Elementor Templates', 'theplus' );
				
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
			case 'tab_title':
				return 'LINE';
			
			case 'tab_content':
				return 'VISUAL';
				
			case 'content_template_id':
				return 'AREA';

			default:
				return '';
		}
	}

}
