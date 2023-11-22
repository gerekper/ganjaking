<?php



if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Class WPML_ElementPack_Logo_Carousel
 */
class WPML_ElementPack_Logo_Carousel extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'logo_list';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array('name', 'description');
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title($field) {
		switch ($field) {

			case 'name':
				return esc_html__('Title', 'bdthemes-element-pack');

			case 'description':
				return esc_html__('Content', 'bdthemes-element-pack');

			default:
				return '';
		}
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_editor_type($field) {
		switch ($field) {
			case 'name':
				return 'LINE';

			case 'description':
				return 'AREA';

			default:
				return '';
		}
	}
}
