<?php
/**
 * PA WPML Accordion.
 */

namespace PremiumAddonsPro\Compatibility\WPML\Widgets;

use WPML_Elementor_Module_With_Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No access of directly access.
}

/**
 * Accordion
 *
 * Registers translatable widget with items.
 *
 * @since 1.4.8
 */
class Accordion extends WPML_Elementor_Module_With_Items {

	/**
	 * Retrieve the field name.
	 *
	 * @since 1.4.8
	 * @return string
	 */
	public function get_items_field() {
		return 'image_content';
	}

	/**
	 * Retrieve the fields inside the repeater.
	 *
	 * @since 1.4.8
	 *
	 * @return array
	 */
	public function get_fields() {

		return array(
			'image_title',
			'image_desc',
			'link_title',
			'temp_content',
			'link' => array( 'url' ),
		);

	}

	/**
	 * Get the title for each repeater string
	 *
	 * @since 1.4.8
	 *
	 * @param string $field Control ID.
	 *
	 * @return string
	 */
	protected function get_title( $field ) {

		switch ( $field ) {
			case 'image_title':
				return esc_html__( 'Image Accordion: Image Title', 'premium-addons-pro' );

			case 'temp_content':
				return esc_html__( 'Image Accordion: Content Template ID', 'premium-addons-pro' );

			case 'image_desc':
				return esc_html__( 'Image Accordion: Image Description', 'premium-addons-pro' );

			case 'url':
				return esc_html__( 'Image Accordion: Image Link', 'premium-addons-pro' );

			case 'link_title':
				return esc_html__( 'Image Accordion: Image Link Title', 'premium-addons-pro' );

			default:
				return '';
		}
	}

	/**
	 * Get `editor_type` for each repeater string
	 *
	 * @since 1.4.8
	 *
	 * @param string $field Control ID.
	 *
	 * @return string
	 */
	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'image_title':
			case 'link_title':
			case 'temp_content':
				return 'LINE';

			case 'image_desc':
				return 'AREA';

			case 'url':
				return 'LINK';

			default:
				return '';
		}
	}

}
