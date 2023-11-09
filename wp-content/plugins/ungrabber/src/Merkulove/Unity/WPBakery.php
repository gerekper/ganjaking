<?php
/**
 * UnGrabber
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         3.0.4
 * @copyright       (C) 2018 - 2023 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

namespace Merkulove\Ungrabber\Unity;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Class to implement WPBakery Widget.
 **/
final class WPBakery {

	/**
	 * The one true WPBakery.
	 *
	 * @var WPBakery
	 **/
	private static $instance;

    /**
     * Setup the Unity.
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     **/
    public function setup() {

        /** Define hooks that runs on both the front-end and the dashboard. */
        $this->both_hooks();

        /** Define public hooks. */
        $this->public_hooks();

        /** Define admin hooks. */
        $this->admin_hooks();

    }

    /**
     * Define hooks that runs on both the front-end and the dashboard.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function both_hooks() {

        /** Register WPBakery addons. */
        $this->register_wpbakery_addons();

    }

    /**
     * Register all of the hooks related to the public-facing functionality.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function public_hooks() {

        /** Work only on frontend area. */
        if ( is_admin() ) { return; }

        /** Public hooks and filters. */

    }

    /**
     * Register all of the hooks related to the admin area functionality.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function admin_hooks() {

        /** Work only in admin area. */
        if ( ! is_admin() ) { return; }

        /** Admin hooks and filters here. */
        add_action( 'admin_enqueue_scripts', [ $this, 'editor_styles' ] );

    }

    /**
     * Add our css to admin editor.
     *
     * @access public
     **/
    public function editor_styles() {

        wp_enqueue_style( 'mdp-ungrabber-wpbakery-admin', Plugin::get_url() . 'src/Merkulove/Unity/assets/css/wpbakery-admin' . Plugin::get_suffix() . '.css', [], Plugin::get_version() );

    }

    /**
     * Registers a WPBakery element.
     *
     * @return void
     * @access public
     **/
    public function register_wpbakery_addons() {

        /** Check if WPBakery is installed */
        if ( ! defined( 'WPB_VC_VERSION' ) ) { return; }

        /** Load WPBakery addons. */
        add_action( 'vc_before_init', [ $this, 'load_addons' ] );

        /** Load WPBakery templates */
        add_action( 'vc_before_init', [ $this, 'load_templates' ] );

    }

    /**
     * Load all available WPBakery addons.
     *
     * @access public
     **/
    public function load_addons() {

        /** Load WPBakery addons, file must ends by ".wpbakery.php" */
        $path = Plugin::get_path() . 'src/Merkulove/Ungrabber/WPBakery/addons/';

        foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $path ) ) as $filename ) {

            if ( substr( $filename, -13 ) === ".wpbakery.php" ) {
                require_once $filename;
            }

        }

    }

    /**
     * Load all available WPBakery Templates.
     *
     * @access public
     **/
    public function load_templates() {

        /** Load WPBakery templates, file must ends by ".template.php" */
        $path = Plugin::get_path() . 'src/Merkulove/Ungrabber/WPBakery/templates/';

        foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $path ))  as $filename ) {

            if ( substr( $filename, -13 ) === ".template.php" ) {
                require_once $filename;
            }

        }

    }

    /**
     * Detect if we are in the "Frontend Editor" mode in WPBakery.
     *
     * @return bool
     * @since 1.0.0
     **/
    public function is_vc_build() {

        return function_exists( 'vc_is_inline' ) && vc_is_inline();

    }

	/**
	 * Main WPBakery Instance.
	 *
	 * Insures that only one instance of WPBakery exists in memory at any one time.
	 *
	 * @static
     *
	 * @return WPBakery
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}