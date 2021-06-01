<?php
/**
 * UnGrabber
 *
 * @encoding        UTF-8
 * @version         3.0.3
 * @copyright       (C) 2018 - 2021 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 * @license         Commercial Software
 *
 * @wordpress-plugin
 * Plugin Name: UnGrabber
 * Plugin URI: https://1.envato.market/ungrabber
 * Description: A most effective way to protect your online content from being copied or grabbed
 * Version: 3.0.3
 * Requires at least: 3.0
 * Requires PHP: 5.6
 * Author: Merkulove
 * Author URI: https://1.envato.market/cc-merkulove
 * License: Commercial Software
 * License URI: https://1.envato.market/KYbje
 * Text Domain: ungrabber
 * Domain Path: /languages
 * Tested up to: 5.7
 * Elementor tested up to: 3.3
 * Elementor Pro tested up to: 3.2
 **/

namespace Merkulove;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

/** Include plugin autoloader for additional classes. */
require __DIR__ . '/src/autoload.php';

use Merkulove\Ungrabber\Caster;
use Merkulove\Ungrabber\Config;
use Merkulove\Ungrabber\Unity\Unity;

/**
 * SINGLETON: Core class used to implement a plugin.
 *
 * This is used to define internationalization, admin-specific hooks, and public-facing site hooks.
 *
 * @since 1.0.0
 *
 **/
final class Ungrabber {

    /**
     * The one true Ungrabber.
     *
     * @var Ungrabber
     * @since 1.0.0
     * @access private
     **/
    private static $instance;

    /**
     * Sets up a new plugin instance.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function __construct() {

        /** Initialize Unity and Main variables. */
        Unity::get_instance();

    }

	/**
	 * Setup the plugin.
	 *
     * @since 1.0.0
	 * @access public
     *
	 * @return void
	 **/
	public function setup() {

        /** Do critical compatibility checks and stop work if fails. */
		if ( ! Unity::get_instance()->initial_checks( ['php56', 'curl'] ) ) { return; }

        /** Prepare custom plugin settings. */
        Config::get_instance()->prepare_settings();

		/** Setup the Unity. */
        Unity::get_instance()->setup();

        /** Custom setups for plugin. */
        Caster::get_instance()->setup();

	}

    /**
     * Called when a plugin is activated.
     *
     * @static
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
	public static function on_activation() {

        /** Call Unity on plugin activation.  */
        Unity::on_activation();

        /** Call Ungrabber on plugin activation */
		Caster::get_instance()->activation_hook();

	}

    /**
     * Called when a plugin is deactivated.
     *
     * @static
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public static function on_deactivation() {

        /** MP on plugin deactivation.  */
        Unity::on_deactivation();

    }

	/**
	 * Main Instance.
	 *
	 * Insures that only one instance of plugin exists in memory at any one time.
	 *
	 * @static
	 * @since 1.0.0
     *
     * @return Ungrabber
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}

/** Run 'on_activation' when the plugin is activated. */
register_activation_hook( __FILE__, [ Ungrabber::class, 'on_activation' ] );

/** Run 'on_deactivation' when the plugin is deactivated. */
register_deactivation_hook( __FILE__, [ Ungrabber::class, 'on_deactivation' ] );

/** Run Plugin class once after activated plugins have loaded. */
add_action( 'plugins_loaded', [ Ungrabber::get_instance(), 'setup' ] );
