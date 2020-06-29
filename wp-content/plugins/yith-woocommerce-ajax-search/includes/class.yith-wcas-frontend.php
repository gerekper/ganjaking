<?php
/**
 * Frontend class
 *
 * @author YITH
 * @package YITH WooCommerce Ajax Search
 * @version 1.1.1
 */

if ( ! defined( 'YITH_WCAS' ) ) {
	exit; } // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAS_Frontend' ) ) {
	/**
	 * Admin class.
	 * The class manage all the Frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAS_Frontend {

		/**
		 * Constructor
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function __construct( ) {

			// custom styles and javascript.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );
		}

		/**
		 * Enqueue styles and scripts
		 *
		 * @access public
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue_styles_scripts() {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_register_script( 'yith_autocomplete', YITH_WCAS_URL . 'assets/js/yith-autocomplete' . $suffix . '.js', array( 'jquery' ), '1.2.7', true );
			wp_register_script( 'yith_wcas_jquery-autocomplete', YITH_WCAS_URL . 'assets/js/devbridge-jquery-autocomplete' . $suffix . '.js', array( 'jquery' ), '1.2.7', true );

			wp_register_script( 'yith_wcas_frontend', YITH_WCAS_URL . 'assets/js/frontend' . $suffix . '.js', array( 'jquery' ), '1.0', true );

			$css = file_exists( get_stylesheet_directory() . '/woocommerce/yith_ajax_search.css' ) ? get_stylesheet_directory_uri() . '/woocommerce/yith_ajax_search.css' : YITH_WCAS_URL . 'assets/css/yith_wcas_ajax_search.css';
			wp_enqueue_style( 'yith_wcas_frontend', $css, array(), YITH_WCAS_VERSION );

			wp_localize_script(
				'yith_wcas_frontend',
				'yith_wcas_params',
				array(
					'loading'  => YITH_WCAS_ASSETS_IMAGES_URL . 'ajax-loader.gif',
					'ajax_url' => admin_url( 'admin-ajax.php' ),

				)
			);

			wp_enqueue_script( 'yith_autocomplete' );

		}
	}
}
