<?php
/**
 * Shortcode base class
 *
 * @package YITH\FAQPluginForWordPress\Abstracts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_FAQ_Shortcode' ) ) {

	/**
	 * Implements shortcode for FAQ plugin
	 *
	 * @class   YITH_FAQ_Shortcode
	 * @since   1.0.0
	 * @author  YITH <plugins@yithemes.com>
	 * @package YITH\FAQPluginForWordPress\Abstracts
	 */
	abstract class YITH_FAQ_Shortcode {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_shortcode_scripts' ), 99 );
			add_action( 'init', array( $this, 'register_styles' ) );
		}

		/**
		 * Register styles
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function register_styles() {

			if ( ! wp_script_is( 'jseldom', 'enqueued' ) ) {
				wp_register_script( 'jseldom', yit_load_css_file( YITH_FWP_ASSETS_URL . '/js/jquery-jseldom/jquery.jseldom.js' ), array( 'jquery' ), '0.0.2', false );
			}

			wp_register_style( 'yith-faq-shortcode-icons', yit_load_css_file( YITH_FWP_ASSETS_URL . '/css/icons.css' ), array(), YITH_FWP_VERSION );
			wp_register_style( 'yith-faq-shortcode-frontend', yit_load_css_file( YITH_FWP_ASSETS_URL . '/css/frontend.css' ), array( 'yith-faq-shortcode-icons', 'dashicons' ), YITH_FWP_VERSION );
			wp_register_script( 'yith-faq-shortcode-frontend', yit_load_js_file( YITH_FWP_ASSETS_URL . '/js/frontend.js' ), array( 'jquery', 'jseldom' ), YITH_FWP_VERSION, true );

			$style_options = array();
			$loader        = '';
			$settings      = wp_parse_args( get_option( 'yit_faq_wp_options', array() ), yfwp_get_default() );

			foreach ( $settings as $key => $value ) {
				if ( in_array( $key, array( 'filters-layout', 'faq-layout', 'pagination-layout', 'faq-copy-button', 'faq-loader-type' ), true ) ) {
					continue;
				}
				if ( is_array( $value ) ) {
					foreach ( $value as $subkey => $subvalue ) {
						if ( 'linked' === $subkey || 'unit' === $subkey ) {
							continue;
						}
						$style_options[] = "--yfwp-$key-$subkey: " . ( is_array( $subvalue ) ? implode( 'px ', $subvalue ) . 'px;' : "$subvalue;" );
					}
				} else {
					if ( 'faq-loader-custom' === $key ) {
						$loader = ".yith-faqs-container.yith-faqs-loading.custom-loader:before{\nbackground-image: url($value);\n}";
					} else {
						$style_options[] = "--yfwp-$key: $value;";
					}
				}
			}
			$custom_css = sprintf( ":root{\n%s\n}\n\n%s", implode( "\n", $style_options ), $loader );

			wp_add_inline_style( 'yith-faq-shortcode-frontend', $custom_css );
		}

		/**
		 * Add scripts and styles
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function frontend_shortcode_scripts() {

			global $post;

			if ( ! $post ) {
				return;
			}

			/**
			 * APPLY_FILTERS: yith_faq_prevent_loading_scripts
			 *
			 * Prevent loading of the plugin scripts on a specific page.
			 *
			 * @param boolean $value Value to enable/disable the loading.
			 *
			 * @return boolean
			 */
			if ( true === apply_filters( 'yith_faq_prevent_loading_scripts', false, $post ) || wp_script_is( 'yith-faq-shortcode-frontend' ) ) {
				return;
			}

			wp_enqueue_style( 'yith-faq-shortcode-frontend' );
			wp_enqueue_script( 'yith-faq-shortcode-frontend' );

			$params = array(
				'ajax_url'      => admin_url( 'admin-ajax.php' ),
				'page_id'       => $post->ID,
				/**
				 * APPLY_FILTERS: yith_faq_enable_scroll
				 *
				 * Enable scrolling to a specific FAQ.
				 *
				 * @param boolean $value Value to enable/disable the scrolling.
				 *
				 * @return boolean
				 */
				'enable_scroll' => apply_filters( 'yith_faq_enable_scroll', true ),
				/**
				 * APPLY_FILTERS: yith_faq_scroll_offset
				 *
				 * Offset for the scrolling.
				 *
				 * @param integer $value Scrolling offset value.
				 *
				 * @return integer
				 */
				'scroll_offset' => apply_filters( 'yith_faq_scroll_offset', 150 ),
			);

			wp_localize_script( 'yith-faq-shortcode-frontend', 'yith_faq', $params );

		}

	}

}
