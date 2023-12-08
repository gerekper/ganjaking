<?php


if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Class WPML_Jet_Elements_Open_Street_Map
 */
class WPML_ElementPack_Open_Street_Map extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'markers';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array('marker_title', 'marker_lat', 'marker_lng', 'marker_content');
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title($field) {
		switch ($field) {
			case 'marker_title':
				return esc_html__('Title', 'bdthemes-element-pack');

			case 'marker_lat':
				return esc_html__('Latitude', 'bdthemes-element-pack');

			case 'marker_lng':
				return esc_html__('Longitude', 'bdthemes-element-pack');

			case 'marker_content':
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
			case 'marker_title':
				return 'LINE';

			case 'marker_lat':
				return 'LINE';

			case 'marker_lng':
				return 'LINE';

			case 'marker_content':
				return 'AREA';

			default:
				return '';
		}
	}
}
