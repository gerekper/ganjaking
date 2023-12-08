<?php
namespace TheplusAddons\WPML;
use WPML_Elementor_Module_With_Items;

if ( ! defined('ABSPATH') ) exit; // No access of directly access

class Tp_Social_Icon extends WPML_Elementor_Module_With_Items {

	/**
	 * Get widget field name.
	 * 
	 * @return string
	 */
	public function get_items_field() {
		return 'pt_plus_social_networks';
	}

	/**
	 * Get the fields inside the repeater.
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'social_text',
			'plus_tooltip_content_desc',
			'plus_tooltip_content_wysiwyg'
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
			case 'social_text':
				return esc_html__( 'Social Icon : Title', 'theplus' );
			
			case 'plus_tooltip_content_desc':
				return esc_html__( 'Social Icon : Tooltip Content', 'theplus' );
				
			case 'plus_tooltip_content_wysiwyg':
				return esc_html__( 'Social Icon : Tooltip Content', 'theplus' );
			
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
			case 'social_text':
				return 'LINE';

			case 'plus_tooltip_content_desc':
				return 'AREA';
			
			case 'plus_tooltip_content_wysiwyg':
				return 'VISUAL';

			default:
				return '';
		}
	}

}
