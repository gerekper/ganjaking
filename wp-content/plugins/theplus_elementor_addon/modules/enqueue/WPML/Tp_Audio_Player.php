<?php
namespace TheplusAddons\WPML;
use WPML_Elementor_Module_With_Items;

if ( ! defined('ABSPATH') ) exit; // No access of directly access

class Tp_Audio_Player extends WPML_Elementor_Module_With_Items {

	/**
	 * Get widget field name.
	 * 
	 * @return string
	 */
	public function get_items_field() {
		return 'playlist';
	}

	/**
	 * Get the fields inside the repeater.
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'title',
			'author',
			'split_text'
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
			case 'title':
				return esc_html__( 'Audio Player : Song Title', 'theplus' );
			
			case 'author':
				return esc_html__( 'Audio Player : Song Author', 'theplus' );

			case 'split_text':
				return esc_html__( 'Audio Player : Split Text', 'theplus' );	
			
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
			
			case 'author':
				return 'LINE';
				
			case 'split_text':
				return 'LINE';

			default:
				return '';
		}
	}

}
