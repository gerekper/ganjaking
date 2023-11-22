<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Class WPML_ElementPack_Timeline
 */
class WPML_ElementPack_Timeline extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'timeline_items';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array('timeline_title', 'timeline_date', 'timeline_text', 'timeline_link');
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title($field) {
		switch ($field) {

			case 'timeline_title':
				return esc_html__('Title', 'bdthemes-element-pack');

			case 'timeline_date':
				return esc_html__('Date', 'bdthemes-element-pack');

			case 'timeline_text':
				return esc_html__('Content', 'bdthemes-element-pack');

			case 'timeline_link':
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
			case 'timeline_title':
				return 'LINE';

			case 'timeline_date':
				return 'LINE';

			case 'timeline_text':
				return 'AREA';

			case 'timeline_link':
				return 'LINE';

			default:
				return '';
		}
	}
}
