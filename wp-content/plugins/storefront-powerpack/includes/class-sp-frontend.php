<?php
/**
 * Storefront Powerpack Frontend Class
 *
 * @author   WooThemes
 * @package  Storefront_Powerpack
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Frontend' ) ) :

	/**
	 * The Frontend Class
	 */
	class SP_Frontend {
		/**
		 * Setup class.
		 *
		 * @since 1.4.10
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		}

		/**
		 * Enqueue scrips and styles.
		 *
		 * @since 1.4.10
		 * @return void
		 */
		public function scripts() {
			wp_register_style( 'sp-fontawesome-4', SP_PLUGIN_URL . 'assets/css/fontawesome-4.css', '', storefront_powerpack()->version );
		}
	}


endif;

return new SP_Frontend();
