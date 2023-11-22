<?php


if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Class WPML_ElementPack_Profile_Card
 */
class WPML_ElementPack_Profile_Card extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return array('social_link_list', 'custom_navs');
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array('social_link_title', 'custom_nav_title');
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title($field) {
		switch ($field) {

			case 'social_link_title':
				return esc_html__('Social Name', 'bdthemes-element-pack');

			case 'custom_nav_title':
				return esc_html__('Nav Title', 'bdthemes-element-pack');

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
			case 'social_link_title':
				return 'LINE';

			case 'custom_nav_title':
				return 'LINE';

			default:
				return '';
		}
	}
}
