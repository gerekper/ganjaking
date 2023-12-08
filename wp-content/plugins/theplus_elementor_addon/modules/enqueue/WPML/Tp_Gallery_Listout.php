<?php
namespace TheplusAddons\WPML;
use WPML_Elementor_Module_With_Items;

if ( ! defined('ABSPATH') ) exit; // No access of directly access

class Tp_Gallery_Listout extends WPML_Elementor_Module_With_Items {

	/**
	 * Get widget field name.
	 * 
	 * @return string
	 */
	public function get_items_field() {
		return 'loop_gallery';
	}

	/**
	 * Get the fields inside the repeater.
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'loop_title',
			'loop_content_caption'
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
				return esc_html__( 'Gallery Listout : Title', 'theplus' );

			case 'loop_content_caption':
				return esc_html__( 'Gallery Listout : Caption', 'theplus' );
			
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

			case 'loop_content_caption':
				return 'AREA';

			default:
				return '';
		}
	}

}
