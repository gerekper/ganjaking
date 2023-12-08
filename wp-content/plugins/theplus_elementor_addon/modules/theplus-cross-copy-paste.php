<?php
namespace TheplusAddons;
use Elementor\Utils;
use Elementor\Controls_Stack;

if ( ! defined( 'WPINC' ) ) {
	die;
}
/*
 * Cross Domain Copy Paste Theplus
 */
if ( ! class_exists( 'Theplus_Cross_Copy_Paste' ) ) {

	/**
	 * Define Theplus_Cross_Copy_Paste class
	 */
	class Theplus_Cross_Copy_Paste {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 3.4.0
		 */
		private static $instance = null;

		/**
		 * Initalize integration hooks
		 *
		 * @return void
		 */
		public function init() {
			add_action( 'wp_ajax_plus_cross_cp_import', array( $this, 'cross_copy_paste_media_import' ) );
		}
		
		/**
		 * Cross copy paste media import
		 *
		 * @since  3.4.0
		 */
		public static function cross_copy_paste_media_import() {
			
			check_ajax_referer( 'plus_cross_cp_import', 'nonce' );

			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_send_json_error(
					__( 'Not a Valid', 'theplus' ),
					403
				);
			}

			$media_import = isset( $_POST['copy_content'] ) ? wp_unslash( $_POST['copy_content'] ) : '';
			
			if ( empty( $media_import ) ) {
				wp_send_json_error( __( 'Empty Content.', 'theplus' ) );
			}

			$media_import = array( json_decode( $media_import, true ) ); 
			$media_import = self::tp_elements_id_change( $media_import );
			$media_import = self::tp_import_media_copy_content( $media_import );

			wp_send_json_success( $media_import );
		}
		
		/**
		 * Replace media items.
		 *
		 * @since  3.4.0
		 */
		protected static function tp_elements_id_change( $media_import ) {
		
			return \Elementor\Plugin::instance()->db->iterate_data(
				$media_import,
				function( $element ) {
					$element['id'] = Utils::generate_random_string();
					return $element;
				}
			);
			
		}

		/**
		 * Media import copy content.
		 *
		 * @since  3.4.0
		 */
		protected static function tp_import_media_copy_content( $media_import ) {
		
			return \Elementor\Plugin::instance()->db->iterate_data(
				$media_import,
				function( $element_data ) {
					$elements = \Elementor\Plugin::instance()->elements_manager->create_element_instance( $element_data );

					if ( ! $elements ) {
						return null;
					}

					return self::element_copy_content_import_start( $elements );
				}
			);
			
		}

		/**
		 * Start element copy content for media import.
		 *
		 * @since  3.4.0
		 */
		protected static function element_copy_content_import_start( Controls_Stack $element ) {
			$get_element_instance = $element->get_data();
			$tp_mi_on_fun = 'on_import';

			if ( method_exists( $element, $tp_mi_on_fun ) ) {
				$get_element_instance = $element->{$tp_mi_on_fun}( $get_element_instance );
			}

			foreach ( $element->get_controls() as $get_control ) {
				$control_type = \Elementor\Plugin::instance()->controls_manager->get_control( $get_control['type'] );
				$control_name  = $get_control['name'];

				if ( ! $control_type ) {
					return $get_element_instance;
				}

				if ( method_exists( $control_type, $tp_mi_on_fun ) ) {
					$get_element_instance['settings'][ $control_name ] = $control_type->{$tp_mi_on_fun}( $element->get_settings( $control_name ), $get_control );
				}
			}

			return $get_element_instance;
		}
		
		/**
		 * Returns the instance.
		 *
		 * @since  3.4.0
		 * @return object
		 */
		public static function get_instance( $shortcodes = array() ) {
			
			if ( null == self::$instance ) {
				self::$instance = new self( $shortcodes );
			}
			return self::$instance;
		}
	}
}

/**
 * Returns instance of Theplus_Cross_Copy_Paste
 *
 * @return object
 */
function theplus_cross_copy_paste() {
	return Theplus_Cross_Copy_Paste::get_instance();
}
theplus_cross_copy_paste()->init();