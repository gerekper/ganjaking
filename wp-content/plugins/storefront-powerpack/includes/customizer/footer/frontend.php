<?php
/**
 * Storefront Powerpack Frontend Footer Class
 *
 * @author   WooThemes
 * @package  Storefront_Powerpack
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Frontend_Footer' ) ) :

	/**
	 * The Frontend class.
	 */
	class SP_Frontend_Footer extends SP_Frontend {

		/**
		 * Setup class.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'wp', array( $this, 'remove_handheld_footer_bar' ), 99 );
		}

		/**
		 * Initialize custom header.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function remove_handheld_footer_bar() {
			$footer_credit       = get_theme_mod( 'sp_footer_credit', true );
			$footer_copyright    = trim( get_theme_mod( 'sp_footer_copyright', '' ) );
			$handheld_footer_bar = get_theme_mod( 'sp_handheld_footer_bar', true );

			if ( false === $footer_credit ) {
				add_filter( 'storefront_credit_link', '__return_false' );
			}

			if ( ! empty( $footer_copyright ) ) {
				add_filter( 'storefront_copyright_text', array( $this, 'tweak_copyright_text' ), 20 );
			}

			if ( true !== $handheld_footer_bar ) {
				remove_action( 'storefront_footer', 'storefront_handheld_footer_bar', 999 );
			}
		}

		/**
		 * Tweak the copyright section text in the footer.
		 *
		 * @since 1.1.0
		 * @return void
		 */
		public function tweak_copyright_text() {
			echo wp_kses_post( get_theme_mod( 'sp_footer_copyright', '' ) );
		}
	}

endif;

return new SP_Frontend_Footer();