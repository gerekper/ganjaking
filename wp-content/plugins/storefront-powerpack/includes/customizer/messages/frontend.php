<?php
/**
 * Storefront Powerpack Frontend Messages Class
 *
 * @author   WooThemes
 * @package  Storefront_Powerpack
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Frontend_Messages' ) ) :

	/**
	 * The Frontend class.
	 */
	class SP_Frontend_Messages extends SP_Frontend {

		/**
		 * Setup class.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'add_customizer_css' ), 999 );
		}

		/**
		 * Add CSS in <head> for styles handled by the Customizer
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function add_customizer_css() {
			$success_bg_color   = get_theme_mod( 'sp_message_background_color' );
			$success_text_color = get_theme_mod( 'sp_message_text_color' );
			$message_bg_color   = get_theme_mod( 'sp_info_background_color' );
			$message_text_color = get_theme_mod( 'sp_info_text_color' );
			$error_bg_color     = get_theme_mod( 'sp_error_background_color' );
			$error_text_color   = get_theme_mod( 'sp_error_text_color' );

			$wc_style = '
				.woocommerce-message {
					background-color: ' . $success_bg_color . ' !important;
					color: ' . $success_text_color . ' !important;
				}

				.woocommerce-message a,
				.woocommerce-message a:hover,
				.woocommerce-message .button,
				.woocommerce-message .button:hover {
					color: ' . $success_text_color . ' !important;
				}

				.woocommerce-info {
					background-color: ' . $message_bg_color . ' !important;
					color: ' . $message_text_color . ' !important;
				}

				.woocommerce-info a,
				.woocommerce-info a:hover,
				.woocommerce-info .button,
				.woocommerce-info .button:hover {
					color: ' . $message_text_color . ' !important;
				}

				.woocommerce-error {
					background-color: ' . $error_bg_color . ' !important;
					color: ' . $error_text_color . ' !important;
				}

				.woocommerce-error a,
				.woocommerce-error a:hover,
				.woocommerce-error .button,
				.woocommerce-error .button:hover {
					color: ' . $error_text_color . ' !important;
				}

			';

			wp_add_inline_style( 'storefront-woocommerce-style', $wc_style );
		}

	}

endif;

return new SP_Frontend_Messages();