<?php


if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Class WPML_ElementPack_Social_Share
 */
class WPML_ElementPack_Social_Share extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'share_buttons';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array('text');
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title($field) {
		switch ($field) {

			case 'text':
				return esc_html__('Custom Label', 'bdthemes-element-pack');

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
			case 'text':
				return 'LINE';

			default:
				return '';
		}
	}
}
