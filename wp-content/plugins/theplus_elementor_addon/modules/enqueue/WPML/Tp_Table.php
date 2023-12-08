<?php
namespace TheplusAddons\WPML;
use WPML_Elementor_Module_With_Items;

if ( ! defined('ABSPATH') ) exit; // No access of directly access

class Tp_Table extends WPML_Elementor_Module_With_Items {

	/**
	 * Get widget field name.
	 * 
	 * @return string
	 */
	public function get_items_field() {
		return 'table_content';
	}

	/**
	 * Get the fields inside the repeater.
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'add_head_cell_row_description',
			'heading_text',
			'cell_text',
			'cell_button_text'
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
			case 'add_head_cell_row_description':
				return esc_html__( 'Table : Row Description', 'theplus' );
			
			case 'heading_text':
				return esc_html__( 'Table : Heading Text', 'theplus' );
			
			case 'cell_text':
				return esc_html__( 'Table : Cell Text', 'theplus' );
			
			case 'cell_button_text':
				return esc_html__( 'Table : Cell Button Text', 'theplus' );
				
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
			case 'add_head_cell_row_description':
				return 'LINE';

			case 'heading_text':
				return 'LINE';
				
			case 'cell_text':
				return 'LINE';
				
			case 'cell_button_text':
				return 'LINE';

			default:
				return '';
		}
	}

}
