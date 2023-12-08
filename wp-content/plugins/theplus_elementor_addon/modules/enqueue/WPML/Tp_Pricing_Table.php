<?php
namespace TheplusAddons\WPML;
use WPML_Elementor_Module_With_Items;

if ( ! defined('ABSPATH') ) exit; // No access of directly access

class Tp_Pricing_Table extends WPML_Elementor_Module_With_Items {

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
			'list_description',
			'call_to_action_text',
			'tooltip_content_desc',
			'tooltip_content_wysiwyg'
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
			case 'list_description':
				return esc_html__( 'Pricing Table : List Description', 'theplus' );
			
			case 'call_to_action_text':
				return esc_html__( 'Pricing Table : Call To Action(CTA) Text', 'theplus' );
			
			case 'tooltip_content_desc':
				return esc_html__( 'Pricing Table : Tooltip Content', 'theplus' );
				
			case 'tooltip_content_wysiwyg':
				return esc_html__( 'Pricing Table : Tooltip Content', 'theplus' );
			
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
			case 'list_description':
				return 'VISUAL';

			case 'call_to_action_text':
				return 'VISUAL';
			
			case 'tooltip_content_desc':
				return 'AREA';
			
			case 'tooltip_content_wysiwyg':
				return 'VISUAL';

			default:
				return '';
		}
	}

}
