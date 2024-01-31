<?php
/**
 * YITH Debug Class.
 *
 * @class   YITH_Debug
 * @package YITH\PluginFramework\Classes
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_Debug' ) ) {
	/**
	 * YITH_Debug class.
	 *
	 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_Debug {

		/**
		 * The single instance of the class.
		 *
		 * @var YITH_Debug
		 */
		private static $instance;

		/**
		 * Singleton implementation.
		 *
		 * @return YITH_Debug
		 */
		public static function instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Deprecated singleton implementation.
		 * Kept for backward compatibility.
		 *
		 * @return YITH_Debug
		 * @deprecated 3.5 | use YITH_Debug::get_instance() instead.
		 */
		public static function get_instance() {
			return self::instance();
		}

		/**
		 * YITH_Debug constructor.
		 */
		private function __construct() {
			add_action( 'init', array( $this, 'init' ) );
		}

		/**
		 * Init
		 */
		public function init() {
			if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
				return;
			}

			$is_debug = apply_filters( 'yith_plugin_fw_is_debug', isset( $_GET['yith-debug'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( $is_debug ) {
				add_action( 'admin_bar_menu', array( $this, 'add_debug_in_admin_bar' ), 99 );
			}
		}

		/**
		 * Add debug node in admin bar.
		 *
		 * @param WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance.
		 */
		public function add_debug_in_admin_bar( $wp_admin_bar ) {
			$args = array(
				'id'    => 'yith-debug-admin-bar',
				'title' => 'YITH Debug',
				'href'  => '',
				'meta'  => array(
					'class' => 'yith-debug-admin-bar',
				),
			);
			$wp_admin_bar->add_node( $args );

			$subnodes = array();

			foreach ( $this->get_debug_information() as $key => $information ) {
				$label = $information['label'];
				$value = $information['value'];
				$url   = ! empty( $information['url'] ) ? $information['url'] : '';

				if ( ! ! $value ) {
					$title = "<strong>$label:</strong> $value";
				} else {
					$title = "<strong>$label</strong>";
				}

				$subnodes[] = array(
					'id'     => 'yith-debug-admin-bar-' . $key,
					'parent' => 'yith-debug-admin-bar',
					'title'  => $title,
					'href'   => $url,
					'meta'   => array(
						'class' => 'yith-debug-admin-bar-' . $key,
					),
				);

				if ( isset( $information['subsub'] ) ) {
					foreach ( $information['subsub'] as $sub_key => $sub_value ) {
						$title      = isset( $sub_value['title'] ) ? $sub_value['title'] : '';
						$html       = isset( $sub_value['html'] ) ? $sub_value['html'] : '';
						$subnodes[] = array(
							'id'     => 'yith-debug-admin-bar-' . $key . '-' . $sub_key,
							'parent' => 'yith-debug-admin-bar-' . $key,
							'title'  => $title,
							'href'   => '',
							'meta'   => array(
								'class' => 'yith-debug-admin-bar-' . $key . '-' . $sub_key,
								'html'  => $html,
							),
						);
					}
				}
			}

			foreach ( $subnodes as $subnode ) {
				$wp_admin_bar->add_node( $subnode );
			}
		}


		/**
		 * Return an array of debug information.
		 *
		 * @return array
		 */
		public function get_debug_information() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_dump

			$debug = array(
				'plugin-fw-info'       => array(
					'label' => 'Framework',
					'value' => $this->get_plugin_framework_info(),
				),
				'yith-premium-plugins' => array(
					'label'  => 'YITH Premium Plugins',
					'value'  => '',
					'subsub' => $this->get_premium_plugins_info(),
				),
				'wc-version'           => array(
					'label' => 'WooCommerce',
					'value' => $this->get_woocommerce_version_info(),
				),
				'theme'                => array(
					'label' => 'Theme',
					'value' => $this->get_theme_info(),
				),
				'screen-id'            => array(
					'label' => 'Screen ID',
					'value' => $this->get_current_screen_info(),
				),
				'post-meta'            => array(
					'label' => 'Post Meta',
					'value' => '',
					'url'   => add_query_arg( array( 'yith-debug-post-meta' => 'all' ) ),
				),
				'option'               => array(
					'label' => 'Option',
					'value' => '',
					'url'   => add_query_arg( array( 'yith-debug-option' => '' ) ),
				),
			);

			// Post Meta debug.
			global $post;
			if ( ! empty( $_GET['yith-debug-post-meta'] ) && $post ) {
				$meta_key   = sanitize_key( wp_unslash( $_GET['yith-debug-post-meta'] ) );
				$meta_value = 'all' !== $meta_key ? get_post_meta( $post->ID, $meta_key, true ) : get_post_meta( $post->ID );

				ob_start();
				echo '<pre>';
				var_dump( $meta_value );
				echo '</pre>';
				$meta_value_html = ob_get_clean();

				$debug['post-meta']['value']  = $meta_key;
				$debug['post-meta']['subsub'] = array( array( 'html' => $meta_value_html ) );
			}

			// Option debug.
			if ( ! empty( $_GET['yith-debug-option'] ) ) {
				$option_key   = sanitize_key( wp_unslash( $_GET['yith-debug-option'] ) );
				$option_value = get_option( $option_key );

				ob_start();
				echo '<pre>';
				var_dump( $option_value );
				echo '</pre>';
				$option_value_html = ob_get_clean();

				$debug['option']['value']  = $option_key;
				$debug['option']['subsub'] = array( array( 'html' => $option_value_html ) );
			}

			// phpcs:enable

			return $debug;
		}

		/** -----------------------------------------------------------
		 *                          GETTER INFO
		 *  -----------------------------------------------------------
		 */

		/**
		 * Return the current screen ID.
		 *
		 * @return string
		 */
		public function get_current_screen_info() {
			$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;

			return ! ! $screen ? $screen->id : 'null';
		}

		/**
		 * Return the current theme name and version.
		 *
		 * @return string
		 */
		public function get_theme_info() {
			$theme = function_exists( 'wp_get_theme' ) ? wp_get_theme() : false;

			return ! ! $theme ? $theme->get( 'Name' ) . ' (' . $theme->get( 'Version' ) . ')' : 'null';
		}

		/**
		 * Return the WooCommerce version if active.
		 *
		 * @return string
		 */
		public function get_woocommerce_version_info() {
			return function_exists( 'WC' ) ? WC()->version : 'not active';
		}

		/**
		 * Return plugin framework information (version and loaded_by).
		 *
		 * @return string
		 */
		public function get_plugin_framework_info() {
			$plugin_fw_version   = yith_plugin_fw_get_version();
			$plugin_fw_loaded_by = basename( dirname( YIT_CORE_PLUGIN_PATH ) );

			return "$plugin_fw_version (by $plugin_fw_loaded_by)";
		}

		/**
		 * Return premium plugins list with versions.
		 *
		 * @return array
		 */
		public function get_premium_plugins_info() {
			$plugins      = YIT_Plugin_Licence()->get_products();
			$plugins_info = array();

			if ( ! ! $plugins ) {
				foreach ( $plugins as $plugin ) {
					$plugins_info[ $plugin['product_id'] ] = array( 'title' => $plugin['Name'] . ' (' . $plugin['Version'] . ')' );
				}

				sort( $plugins_info );
			}

			return $plugins_info;
		}
	}
}
if ( ! function_exists( 'yith_debug' ) ) {
	/**
	 * Single instance of YITH_Debug
	 *
	 * @return YITH_Debug
	 */
	function yith_debug() {
		return YITH_Debug::instance();
	}

	yith_debug();
}
