<?php
/**
 * UnGrabber
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         3.0.1
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
 * SINGLETON: Unity class used to control common functionality for all Merkulove plugins.
 *
 * @since 1.0.0
 *
 **/
final class Unity {

    public $allow_svg_uploads = false;

    /**
     * The one true Unity.
     *
     * @since 1.0.0
     * @var Unity
     **/
    private static $instance;

    /**
     * Sets up a new plugin instance.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    private function __construct() {

	    /** Initialize main variables. */
	    Plugin::get_instance();

    }

    /**
     * Do critical compatibility checks and stop work if fails.
     *
     * @param array $checks - List of critical initial checks to run. List of available checks: 'php56', 'curl'
     *
     * @since  1.0.0
     * @access public
     *
     * @return bool
     **/
    public function initial_checks( $checks ) {

        /** Do critical initial checks. */
		if ( ! CheckCompatibility::get_instance()->do_initial_checks( $checks, true ) ) { return  false; }

        return true;

    }

    /**
     * Setup the Unity.
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     **/
	public function setup() {

		/** Restore settings from previous version */
		self::restore_settings();

		/** Send install Action to our host. */
		self::send_install_action();

        /** Define hooks that runs on both the front-end as well as the dashboard. */
        $this->both_hooks();

		/** Define admin hooks. */
		$this->admin_hooks();

		/** Extra setup for Elementor plugins. */
        if ( 'elementor' === Plugin::get_type() ) {
            Elementor::get_instance()->setup();
        }

        // TODO: Add Extra setup for WPBakery plugins.

	}

	/**
	 * Define hooks that runs on both the front-end as well as the dashboard.
	 *
     * @since 1.0.0
	 * @access private
     *
	 * @return void
	 **/
	private function both_hooks() {

    	/** Load the plugin text domain for translation. */
        PluginHelper::load_textdomain();

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

		if ( ! is_admin() ) { return; }

		/** Initialize plugin settings. */
		Settings::get_instance();

        /** Add admin CSS */
		AdminStyles::get_instance();

		/** Add admin JS */
		AdminScripts::get_instance();

        /** Initialize PluginHelper. */
		PluginHelper::get_instance();

		/** Plugin update mechanism enable only if plugin have Envato ID. */
		PluginUpdater::get_instance();

    }

	/**
	 * Restore settings after update from old plugin to Unity
	 *
	 * @since 1.0.0
	 * @access private
	 */
    public static function restore_settings() {

    	/** Return is new settings exist */
    	if ( is_array( get_option( 'mdp_ungrabber_general_settings' ) ) ) { return; }

    	/** Try to get old options */
	    $old_general_options = get_option( 'mdp_ungrabber_settings' );
	    $old_css_options = get_option( 'mdp_ungrabber_css_settings' );

	    /** Restore General and remove old General settings */
	    if ( is_array( $old_general_options ) ) {

		    update_option( 'mdp_ungrabber_general_settings', $old_general_options, true );
		    delete_option( 'mdp_ungrabber_settings' );

	    }

	    /** Restore Custom CSS and remove old Custom CSS */
	    if ( is_array( $old_css_options ) ) {

		    update_option( 'mdp_ungrabber_custom_css_settings', $old_css_options, true );
		    delete_option( 'mdp_ungrabber_css_settings' );

	    }

    }

	/**
	 * Run when the plugin is activated.
	 *
     * @static
     * @since 1.0.0
     * @access public
     *
     * @return void
	 **/
	public static function on_activation() {

		/** Security checks. */
		if ( ! current_user_can( 'activate_plugins' ) ) { return; }

		/** We need to know plugin to activate it. */
		if ( ! isset( $_REQUEST[ 'plugin' ] ) ) { return; }

		/** Get plugin. */
		$plugin = filter_var( $_REQUEST[ 'plugin' ], FILTER_SANITIZE_STRING );

		/** Checks that a user was referred from admin page with the correct security nonce. */
		check_admin_referer( "activate-plugin_{$plugin}" );

		/** Do critical initial checks. */
		if ( ! CheckCompatibility::get_instance()->do_initial_checks( ['php56', 'curl'], false ) ) { return; }

		/** Restore settings */
		self::restore_settings();

		/** Send install Action to our host. */
		self::send_install_action();

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
    public static function on_deactivation() {}

	/**
	 * Send install Action to our host.
	 *
	 * @static
     * @since 1.0.0
     *
     * @return void
	 **/
	private static function send_install_action() {

		/** Have we already sent 'install' for this version? */
		$opt_name = 'mdp_ungrabber_send_action_install';
		$installed_version = get_option( $opt_name );

		if ( ! $installed_version || Plugin::get_version() !== $installed_version ) {

            /** Send install Action to our host. */
			Helper::get_instance()->send_action( 'install', 'ungrabber', Plugin::get_version() );
			update_option( $opt_name, Plugin::get_version() );

		}

	}

    /**
     * Main Instance.
     *
     * Insures that only one instance of Unity exists in memory at any one time.
     *
     * @static
     * @since 1.0.0
     *
     * @return Unity
     **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self();

		}

		return self::$instance;

	}

}