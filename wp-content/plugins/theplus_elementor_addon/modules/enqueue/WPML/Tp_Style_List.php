<?php
namespace TheplusAddons\WPML;
use WPML_Elementor_Module_With_Items;

if ( ! defined('ABSPATH') ) exit; // No access of directly access

class Tp_Style_List extends WPML_Elementor_Module_With_Items {

	/**
	 * Get widget field name.
	 * 
	 * @return string
	 */
	public function get_items_field() {
		return 'icon_list';
	}

	/**
	 * Get the fields inside the repeater.
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'hint_text',
			'content_description',
			'tooltip_content_wysiwyg',
			'tooltip_content_desc'
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
			case 'pin_text':
				return esc_html__( 'Style List : Hint Text', 'theplus' );
			
			case 'content_description':
				return esc_html__( 'Style List : Description', 'theplus' );
				
			case 'tooltip_content_wysiwyg':
				return esc_html__( 'Style List : Tooltip Content', 'theplus' );
				
			case 'tooltip_content_desc':
				return esc_html__( 'Style List : Tooltip Content', 'theplus' );
			
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
			case 'pin_text':
				return 'LINE';

			case 'content_description':
				return 'VISUAL';
			
			case 'tooltip_content_wysiwyg':
				return 'VISUAL';
			
			case 'tooltip_content_desc':
				return 'AREA';

			default:
				return '';
		}
	}

}
