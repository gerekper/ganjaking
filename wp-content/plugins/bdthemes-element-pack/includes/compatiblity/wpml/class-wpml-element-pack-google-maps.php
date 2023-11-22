<?php



if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Class WPML_ElementPack_GoogleMaps
 */
class WPML_ElementPack_GoogleMaps extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'marker';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'marker_lat', 'marker_lng', 'marker_title', 'marker_content' );
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title( $field ) {
		switch( $field ) {

			case 'marker_lat':
				return esc_html__( 'Latitude', 'bdthemes-element-pack' );

			case 'marker_lng':
				return esc_html__( 'Longitude', 'bdthemes-element-pack' );

			case 'marker_title':
				return esc_html__( 'Marker Title', 'bdthemes-element-pack' );

			case 'marker_content':
				return esc_html__( 'Marker Content', 'bdthemes-element-pack' );

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
			case 'marker_lat':
				return 'LINE';

			case 'marker_lng':
				return 'LINE';

			case 'marker_title':
				return 'LINE';

			case 'marker_content':
				return 'AREA';

			default:
				return '';
		}
	}

}
