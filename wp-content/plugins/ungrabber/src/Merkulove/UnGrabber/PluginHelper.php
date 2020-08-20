<?php
/**
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on Envato Market: https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         2.0.1
 * @copyright       Copyright (C) 2018 - 2020 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Alexander Khmelnitskiy (info@alexander.khmelnitskiy.ua), Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

namespace Merkulove\UnGrabber;

use Merkulove\UnGrabber as UnGrabber;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Class used to implement base plugin features.
 *
 * @since 1.0.0
 * @author Alexandr Khmelnytsky (info@alexander.khmelnitskiy.ua)
 */
final class PluginHelper {

	/**
	 * The one true Helper.
	 *
	 * @var PluginHelper
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new Helper instance.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function __construct() {

		/** Add plugin links. */
		add_filter( 'plugin_action_links_' . UnGrabber::$basename, [ $this, 'add_links' ] );

		/** Add plugin meta. */
		add_filter( 'plugin_row_meta', [ $this, 'add_row_meta' ], 10, 2 );

		/** Load JS and CSS for Backend Area. */
		$this->enqueue_backend();

	}

	/**
	 * Load JS and CSS for Backend Area.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	function enqueue_backend() {

		/** Add admin styles. */
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_styles' ] );

		/** Add admin javascript. */
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );

	}

	/**
	 * Add CSS for admin area.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 **/
	public function admin_styles() {

		$screen = get_current_screen();

		/** Add styles only on WP Plugins page. */
		if ( $screen->base == 'plugins' ) {
			wp_enqueue_style( 'mdp-ungrabber-plugins', UnGrabber::$url . 'css/plugins' . UnGrabber::$suffix . '.css', [], UnGrabber::$version );
		}

	}

	/**
	 * Add JS for admin area.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 **/
	public function admin_scripts() {

		$screen = get_current_screen();

		/** Add scripts only on WP Plugins page. */
		if ( $screen->base == 'plugins' ) {
			wp_enqueue_script( 'mdp-ungrabber-plugins', UnGrabber::$url . 'js/plugins' . UnGrabber::$suffix . '.js', [ 'jquery' ], UnGrabber::$version, true );
		}
	}

	/**
	 * Add "merkulov.design" and  "Envato Profile" links on plugin page.
	 *
	 * @param array $links Current links: Deactivate | Edit
	 * @return array
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 */
	public function add_links( $links ) {

		array_unshift( $links, '<a title="' . esc_html__( 'Settings', 'ungrabber' ) . '" href="' . admin_url( 'admin.php?page=mdp_ungrabber_settings' ) . '">' . esc_html__( 'Settings', 'ungrabber' ) . '</a>' );
		array_push( $links, '<a title="' . esc_html__( 'Documentation', 'ungrabber' ) . '" href="https://docs.merkulov.design/tag/ungrabber/" target="_blank">' . esc_html__( 'Documentation', 'ungrabber' ) . '</a>' );
		array_push( $links, '<a href="https://1.envato.market/cc-merkulove" target="_blank" class="cc-merkulove"><img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB2aWV3Qm94PSIwIDAgMTE3Ljk5IDY3LjUxIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8ZGVmcz4KPHN0eWxlPi5jbHMtMSwuY2xzLTJ7ZmlsbDojMDA5ZWQ1O30uY2xzLTIsLmNscy0ze2ZpbGwtcnVsZTpldmVub2RkO30uY2xzLTN7ZmlsbDojMDA5ZWUyO308L3N0eWxlPgo8L2RlZnM+CjxjaXJjbGUgY2xhc3M9ImNscy0xIiBjeD0iMTUiIGN5PSI1Mi41MSIgcj0iMTUiLz4KPHBhdGggY2xhc3M9ImNscy0yIiBkPSJNMzAsMmgwQTE1LDE1LDAsMCwxLDUwLjQ4LDcuNUw3Miw0NC43NGExNSwxNSwwLDEsMS0yNiwxNUwyNC41LDIyLjVBMTUsMTUsMCwwLDEsMzAsMloiLz4KPHBhdGggY2xhc3M9ImNscy0zIiBkPSJNNzQsMmgwQTE1LDE1LDAsMCwxLDk0LjQ4LDcuNUwxMTYsNDQuNzRhMTUsMTUsMCwxLDEtMjYsMTVMNjguNSwyMi41QTE1LDE1LDAsMCwxLDc0LDJaIi8+Cjwvc3ZnPgo=" alt="' . esc_html__( 'Plugins', 'ungrabber' ) . '">' . esc_html__( 'Plugins', 'ungrabber' ) . '</a>' );

		return $links;
	}

	/**
	 * Add "Rate us" link on plugin page.
	 *
	 * @param array $links Current links: Deactivate | Edit
	 * @param $file - Path to the plugin file relative to the plugins directory.
	 *
	 * @return array
	 * @since 1.0.0
	 * @access public
	 */
	public function add_row_meta( $links, $file ) {

		if ( UnGrabber::$basename !== $file ) {
			return $links;
		}

		$links[] = esc_html__( 'Rate this plugin:', 'ungrabber' )
		           . "<span class='mdp-ungrabber-rating-stars'>"
		           . "     <a href='https://1.envato.market/cc-downloads' target='_blank'>"
		           . "         <span class='dashicons dashicons-star-filled'></span>"
		           . "     </a>"
		           . "     <a href='https://1.envato.market/cc-downloads' target='_blank'>"
		           . "         <span class='dashicons dashicons-star-filled'></span>"
		           . "     </a>"
		           . "     <a href='https://1.envato.market/cc-downloads' target='_blank'>"
		           . "         <span class='dashicons dashicons-star-filled'></span>"
		           . "     </a>"
		           . "     <a href='https://1.envato.market/cc-downloads' target='_blank'>"
		           . "         <span class='dashicons dashicons-star-filled'></span>"
		           . "     </a>"
		           . "     <a href='https://1.envato.market/cc-downloads' target='_blank'>"
		           . "         <span class='dashicons dashicons-star-filled'></span>"
		           . "     </a>"
		           . "<span>";

		return $links;
	}

	/**
	 * Main Helper Instance.
	 *
	 * Insures that only one instance of Helper exists in memory at any one time.
	 *
	 * @static
	 * @return PluginHelper
	 * @since 1.0.0
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof PluginHelper ) ) {
			self::$instance = new PluginHelper();
		}

		return self::$instance;

	}

} // End Class Helper.
