<?php



if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Class WPML_ElementPack_Fancy_Tabs
 */
class WPML_ElementPack_Fancy_Tabs extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'tabs';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'tab_title', 'tab_sub_title', 'tab_content', 'tabs_button' );
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title( $field ) {
		switch( $field ) {

			case 'tab_title':
				return esc_html__( 'Title', 'bdthemes-element-pack' );

			case 'tab_sub_title':
				return esc_html__( 'Sub Title', 'bdthemes-element-pack' );

			case 'tab_content':
				return esc_html__( 'Description', 'bdthemes-element-pack' );

			case 'tabs_button':
				return esc_html__( 'Button Text', 'bdthemes-element-pack' );

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
			case 'tab_title':
                return 'LINE';

			case 'tab_sub_title':
				return 'LINE';

			case 'tab_content':
				return 'AREA';

			case 'tabs_button':
				return 'LINE';

			default:
				return '';
		}
	}

}
