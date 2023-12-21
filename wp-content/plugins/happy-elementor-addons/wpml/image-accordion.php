<?php
/**
 * Image Accordion integration
 */
namespace Happy_Addons\Elementor;

defined( 'ABSPATH' ) || die();

class WPML_Image_Accordion extends WPML_Module_With_Items  {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'accordion_items';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return [
			'label',
			'title',
			'description',
			'button_label',
			'button_url' => ['url'],
			'link_url' => ['url'],
		];
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'label':
				return __( 'Image Accordion: Label', 'happy-elementor-addons' );
			case 'title':
				return __( 'Image Accordion: Title', 'happy-elementor-addons' );
			case 'description':
				return __( 'Image Accordion: Description', 'happy-elementor-addons' );
			case 'button_label':
				return __( 'Image Accordion: Button Label', 'happy-elementor-addons' );
			case 'button_url':
				return __( 'Image Accordion: Button URL', 'happy-elementor-addons' );
			case 'link_url':
				return __( 'Image Accordion: Link URL', 'happy-elementor-addons' );
			default:
				return '';
		}
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'label':
				return 'LINE';
			case 'title':
				return 'AREA';
			case 'description':
				return 'AREA';
			case 'button_label':
				return 'LINE';
			case 'button_url':
				return 'LINK';
			case 'link_url':
				return 'LINK';
			default:
				return '';
		}
	}
}