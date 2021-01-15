<?php
/**
 * Gallery Field.
 *
 * @package     weLaunchFramework/Fields
 * @author      Dovy Paukstys & Kevin Provance (kprovance)
 * @version     4.0.0
 */

defined( 'ABSPATH' ) || exit;

// Don't duplicate me!
if ( ! class_exists( 'weLaunch_Gallery', false ) ) {

	/**
	 * Main weLaunch_gallery class
	 *
	 * @since       3.0.0
	 */
	class weLaunch_Gallery extends weLaunch_Field {

		/**
		 * Field Render Function.
		 * Takes the vars and outputs the HTML for the field in the settings
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function render() {
			echo '<div class="screenshot">';

			if ( ! empty( $this->value ) ) {
				$ids = explode( ',', $this->value );

				foreach ( $ids as $attachment_id ) {
					$img = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
					$alt = wp_prepare_attachment_for_js( $attachment_id );
					$alt = isset( $alt['alt'] ) ? $alt['alt'] : '';

					echo '<a class="of-uploaded-image" href="' . esc_url( $img[0] ) . '">';
					echo '<img class="welaunch-option-image" id="image_' . esc_attr( $this->field['id'] ) . '_' . esc_attr( $attachment_id ) . '" src="' . esc_url( $img[0] ) . '" alt="' . esc_attr( $alt ) . '" target="_blank" rel="external" />';
					echo '</a>';
				}
			}

			echo '</div>';
			echo '<a href="#" onclick="return false;" id="edit-gallery" class="gallery-attachments button button-primary">' . esc_html__( 'Add/Edit Gallery', 'welaunch-framework' ) . '</a> ';
			echo '<a href="#" onclick="return false;" id="clear-gallery" class="gallery-attachments button">' . esc_html__( 'Clear Gallery', 'welaunch-framework' ) . '</a>';
			echo '<input type="hidden" class="gallery_values ' . esc_attr( $this->field['class'] ) . '" value="' . esc_attr( $this->value ) . '" name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '" />';
		}

		/**
		 * Enqueue Function.
		 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function enqueue() {

			if ( function_exists( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			} else {
				wp_enqueue_script( 'media-upload' );
				wp_enqueue_script( 'thickbox' );
				wp_enqueue_style( 'thickbox' );
			}

			wp_enqueue_script(
				'welaunch-field-gallery-js',
				weLaunch_Core::$url . 'inc/fields/gallery/welaunch-gallery' . weLaunch_Functions::is_min() . '.js',
				array( 'jquery', 'welaunch-js' ),
				$this->timestamp,
				true
			);
		}
	}
}

class_alias( 'weLaunch_Gallery', 'weLaunchFramework_Gallery' );
