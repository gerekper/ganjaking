<?php
/**
 * UAEL WPML compatibility.
 *
 * @package UAEL
 */

namespace UltimateElementor\Compatibility\WPML;

use WPML_Elementor_Module_With_Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Video Gallery
 *
 * Registers translatable module with items
 *
 * @since 1.2.2
 */
class VideoGallery extends WPML_Elementor_Module_With_Items {

	/**
	 * Get items field
	 *
	 * @since 1.2.2
	 * @return string
	 */
	public function get_items_field() {
		return 'gallery_items';
	}

	/**
	 * Retrieve the fields inside the repeater
	 *
	 * @since 1.2.2
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'title',
			'tags',
			'video_url' => array( 'url' ),
		);
	}

	/**
	 * Method for setting the title for each translatable field
	 *
	 * @since 1.2.2
	 *
	 * @param string $field The name of the field.
	 * @return string
	 */
	protected function get_title( $field ) {
		if ( 'url' === $field ) {
			return __( 'Video Gallery: Video URL', 'uael' );
		}

		if ( 'title' === $field ) {
			return __( 'Video Gallery: Caption', 'uael' );
		}

		if ( 'tags' === $field ) {
			return __( 'Video Gallery: Categories', 'uael' );
		}

		return '';
	}

	/**
	 * Method for determining the editor type for each field
	 *
	 * @since 1.2.2
	 *
	 * @param  string $field Name of the field.
	 * @return string
	 */
	protected function get_editor_type( $field ) {

		switch ( $field ) {
			case 'tags':
			case 'title':
			case 'url':
				return 'LINE';

			default:
				return '';
		}
	}
}
