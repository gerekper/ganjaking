<?php
/**
 * UnGrabber
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         3.0.3
 * @copyright       (C) 2018 - 2021 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

namespace Merkulove\Ungrabber\Unity;

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
 *
 **/
final class PluginHelper {

	/**
	 * The one true PluginHelper.
	 *
     * @since 1.0.0
     * @access private
	 * @var PluginHelper
	 **/
	private static $instance;

	/**
	 * Sets up a new PluginHelper instance.
	 *
     * @since 1.0.0
     * @access private
     *
     * @return void
	 **/
	private function __construct() {

		/** Add plugin links. */
		add_filter( 'plugin_action_links_' . Plugin::get_basename(), [$this, 'add_links'] );

		/** Add plugin meta. */
		add_filter( 'plugin_row_meta', [ $this, 'add_row_meta' ], 10, 2 );

		/** Load JS and CSS for Backend Area. */
		$this->enqueue_backend();

        /** Remove unnecessary WordPress branding and messages. */

        /** Remove all "third-party" notices from plugin settings page. */
        add_action( 'in_admin_header', [ $this, 'remove_all_notices' ], 1000 );

        /** Remove "Thank you for creating with WordPress" and WP version only from plugin settings page. */
        add_action( 'admin_enqueue_scripts', [ $this, 'remove_wp_copyrights' ] );

	}

	/**
	 * Load JS and CSS for Backend Area.
	 *
     * @since 1.0.0
     * @access public
     *
     * @return void
	 **/
	public function enqueue_backend() {

		/** Add admin styles. */
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_styles' ] );

		/** Add admin javascript. */
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );

	}

	/**
	 * Add CSS for admin area.
     *
     * @since 1.0.0
     * @access public
	 *
	 * @return void
	 **/
	public function admin_styles() {

		$screen = get_current_screen();
		if ( null === $screen ) { return; }

		/** Add styles only on WP Plugins page. */
        if ( 'plugins' !== $screen->base ) { return; }

        wp_enqueue_style( 'mdp-plugins', Plugin::get_url() . 'src/Merkulove/Unity/assets/css/plugins' . Plugin::get_suffix() . '.css', [], Plugin::get_version() );

	}

	/**
	 * Add JS for admin area.
	 *
     * @since 1.0.0
     * @access public
     *
	 * @return void
	 **/
	public function admin_scripts() {

		$screen = get_current_screen();
		if ( null === $screen ) { return; }

		/** Add scripts only on WP Plugins page. */
        if ( 'plugins' !== $screen->base ) { return; }

        wp_enqueue_script( 'mdp-plugins', Plugin::get_url() . 'src/Merkulove/Unity/assets/js/plugins' . Plugin::get_suffix() . '.js', ['jquery'], Plugin::get_version(), true );

	}

	/**
	 * Add "merkulov.design" and  "Envato Profile" links on plugin page.
	 *
	 * @param array $links Current links: Deactivate | Edit
	 *
     * @since 1.0.0
	 * @access public
     *
     * @return array
	 **/
	public function add_links( $links ) {

		array_unshift( $links, '<a title="' . esc_html__( 'Settings', 'ungrabber' ) . '" href="' . admin_url( 'admin.php?page=mdp_ungrabber_settings' ) . '">' . esc_html__( 'Settings', 'ungrabber' ) . '</a>' );
		$links[] = '<a title="' . esc_html__( 'Documentation', 'ungrabber' ) . '" href="https://docs.merkulov.design/tag/ungrabber" target="_blank">' . esc_html__( 'Documentation', 'ungrabber' ) . '</a>';
		$links[] = '<a href="https://1.envato.market/cc-merkulove" target="_blank" class="cc-merkulove"><img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB2aWV3Qm94PSIwIDAgMTE3Ljk5IDY3LjUxIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8ZGVmcz4KPHN0eWxlPi5jbHMtMSwuY2xzLTJ7ZmlsbDojMDA5ZWQ1O30uY2xzLTIsLmNscy0ze2ZpbGwtcnVsZTpldmVub2RkO30uY2xzLTN7ZmlsbDojMDA5ZWUyO308L3N0eWxlPgo8L2RlZnM+CjxjaXJjbGUgY2xhc3M9ImNscy0xIiBjeD0iMTUiIGN5PSI1Mi41MSIgcj0iMTUiLz4KPHBhdGggY2xhc3M9ImNscy0yIiBkPSJNMzAsMmgwQTE1LDE1LDAsMCwxLDUwLjQ4LDcuNUw3Miw0NC43NGExNSwxNSwwLDEsMS0yNiwxNUwyNC41LDIyLjVBMTUsMTUsMCwwLDEsMzAsMloiLz4KPHBhdGggY2xhc3M9ImNscy0zIiBkPSJNNzQsMmgwQTE1LDE1LDAsMCwxLDk0LjQ4LDcuNUwxMTYsNDQuNzRhMTUsMTUsMCwxLDEtMjYsMTVMNjguNSwyMi41QTE1LDE1LDAsMCwxLDc0LDJaIi8+Cjwvc3ZnPgo=" alt="' . esc_html__( 'Plugins', 'ungrabber' ) . '">' . esc_html__( 'Plugins', 'ungrabber' ) . '</a>';

		return $links;

	}

	/**
	 * Add "Rate us" link on plugin page.
	 *
	 * @param array $links Current links: Deactivate | Edit
	 * @param $file - Path to the plugin file relative to the plugins directory.
     *
     * @since 1.0.0
	 * @access public
     *
     * @return array
	 **/
	public function add_row_meta( $links, $file ) {

		if ( Plugin::get_basename() !== $file ) { return $links; }

		$stars = esc_html__( 'Rate this plugin:', 'ungrabber' );

		$stars .= "<span class='mdp-rating-stars'>";
		for ( $i = 1; $i <= 5; $i++) {
			$stars .= "<a href='https://1.envato.market/cc-downloads' target='_blank'><span class='dashicons dashicons-star-filled'></span></a>";
		}
		$stars .= "<span>";

		$links[] = $stars;

		return $links;

	}

	/**
	 * Remove "Thank you for creating with WordPress" and WP version only from plugin settings page.
	 *
     * @since 1.0.0
     * @access public
     *
	 * @return void
	 **/
	public function remove_wp_copyrights() {

		/** Remove "Thank you for creating with WordPress" and WP version from plugin settings page. */
		$screen = get_current_screen();
		if ( null === $screen ) { return; }

        /** If not Plugin Settings Page. */
		if ( ! in_array( $screen->base, Plugin::get_menu_bases(), true ) ) { return; }

        /** Plugin Settings Page. */
        add_filter( 'admin_footer_text', '__return_empty_string', 11 );
        add_filter( 'update_footer', '__return_empty_string', 11 );

	}

	/**
	 * Remove all other notices.
	 *
     * @since 1.0.0
	 * @access public
     *
     * @return void
     **/
	public function remove_all_notices() {

		/** Work only on plugin settings page. */
		$screen = get_current_screen();
		if ( null === $screen ) { return; }

        /** If not Plugin Settings Page. */
        if ( ! in_array( $screen->base, Plugin::get_menu_bases(), true ) ) { return; }

        /** Plugin Settings Page. */
        remove_all_actions( 'admin_notices' );
        remove_all_actions( 'all_admin_notices' );

	}

    /**
     * Loads the plugin translated strings.
     *
     * @static
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public static function load_textdomain() {

        load_plugin_textdomain( 'ungrabber', false, Plugin::get_path() . '/languages/' );

    }

	/**
	 * Main PluginHelper Instance.
	 * Insures that only one instance of PluginHelper exists in memory at any one time.
	 *
	 * @static
     * @since 1.0.0
     * @access public
     *
	 * @return PluginHelper
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}

