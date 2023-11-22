<?php
/**
 * PA WPML Image Layers.
 */

namespace PremiumAddonsPro\Compatibility\WPML\Widgets;

use WPML_Elementor_Module_With_Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No access of directly access.
}

/**
 * Image Layers
 *
 * Registers translatable widget with items.
 *
 * @since 1.4.8
 */
class Image_Layers extends WPML_Elementor_Module_With_Items {

	/**
	 * Retrieve the field name.
	 *
	 * @since 1.4.8
	 * @return string
	 */
	public function get_items_field() {
		return 'premium_img_layers_images_repeater';
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
			'img_layer_text',
			'premium_img_layers_link' => array( 'url' ),
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
			case 'img_layer_text':
				return esc_html__( 'Image Layers: Text', 'premium-addons-pro' );

			case 'url':
				return esc_html__( 'Image Layers: Link', 'premium-addons-pro' );

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

			case 'url':
				return 'LINK';

			case 'img_layer_text':
				return 'LINE';

			default:
				return '';
		}

	}

}
