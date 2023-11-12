<?php
/**
 * UAEL Cross Domain Copy Paste.
 *
 * @package UAEL
 */

namespace UltimateElementor;

use Elementor\Utils;
use Elementor\Controls_Stack;

/**
 * UAEL Cross Domain Copy Paste.
 *
 * @package UAEL
 */
class UAEL_Cross_Domain_Copy_Paste {

	/**
	 * Init ajax functions.
	 */
	public static function init() {

		add_action( 'wp_ajax_uael_process_import', array( __CLASS__, 'process_media_import' ) );

	}

	/**
	 * Media import support
	 *
	 * @return void
	 */
	public static function process_media_import() {

		check_ajax_referer( 'uael_process_import', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error(
				__( 'Unable to complete the task. Not a valid user.', 'uael' ),
				403
			);
		}

		$media = isset( $_POST['content'] ) ? sanitize_text_field( wp_unslash( $_POST['content'] ) ) : '';

		if ( empty( $media ) ) {
			wp_send_json_error( __( 'Looks like content is empty. Cannot be processed.', 'uael' ) );
		}

		$media = array( json_decode( $media, true ) );
		$media = self::replace_elements_ids( $media );
		$media = self::import_media_content( $media );

		wp_send_json_success( $media );
	}

	/**
	 * Replace media items IDs.
	 *
	 * @since 1.24.0
	 * @access protected
	 *
	 * @param array $media Widgets media content.
	 * @return string content
	 */
	protected static function replace_elements_ids( $media ) {
		return \Elementor\Plugin::instance()->db->iterate_data(
			$media,
			function( $element ) {
				$element['id'] = Utils::generate_random_string();
				return $element;
			}
		);
	}

	/**
	 * Media import process.
	 *
	 * @since 1.24.0
	 * @access protected
	 *
	 * @param array $media Widgets media content.
	 * @return string content
	 */
	protected static function import_media_content( $media ) {
		return \Elementor\Plugin::instance()->db->iterate_data(
			$media,
			function( $element_instance ) {
				$element = \Elementor\Plugin::instance()->elements_manager->create_element_instance( $element_instance );

				if ( ! $element ) {
					return null;
				}

				return self::import_content_process( $element );
			}
		);
	}

	/**
	 * Process element content for import.
	 *
	 * @since 1.24.0
	 * @access protected
	 *
	 * @param Controls_Stack $element Element.
	 * @return array Processed element data.
	 */
	protected static function import_content_process( Controls_Stack $element ) {
		$element_instance = $element->get_data();
		$method           = 'on_import';

		if ( method_exists( $element, $method ) ) {
			$element_instance = $element->{$method}( $element_instance );
		}

		foreach ( $element->get_controls() as $control ) {
			$control_class = \Elementor\Plugin::instance()->controls_manager->get_control( $control['type'] );
			$control_name  = $control['name'];

			if ( ! $control_class ) {
				return $element_instance;
			}

			if ( method_exists( $control_class, $method ) ) {
				$element_instance['settings'][ $control_name ] = $control_class->{$method}( $element->get_settings( $control_name ), $control );
			}
		}

		return $element_instance;
	}
}

UAEL_Cross_Domain_Copy_Paste::init();
