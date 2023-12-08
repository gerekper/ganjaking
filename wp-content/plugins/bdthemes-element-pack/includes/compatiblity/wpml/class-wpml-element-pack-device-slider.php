<?php


if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Class WPML_Jet_Elements_Device_Slider
 */
class WPML_ElementPack_Device_Slider extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'slides';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'title', 'video_link', 'youtube_link' );
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title( $field ) {
		switch( $field ) {
			case 'title':
				return esc_html__( 'Title', 'bdthemes-element-pack' );

			case 'video_link':
				return esc_html__( 'Video Link', 'bdthemes-element-pack' );

			case 'youtube_link':
				return esc_html__( 'Youtube Link', 'bdthemes-element-pack' );

			default:
				return '';
		}
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'title':
				return 'LINE';

			case 'video_link':
				return 'LINE';

			case 'youtube_link':
				return 'LINE';

			default:
				return '';
		}
	}

}
