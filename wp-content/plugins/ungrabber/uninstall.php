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

namespace Merkulove;

/** Include plugin autoloader for additional classes. */
require __DIR__ . '/src/autoload.php';

use Merkulove\UnGrabber\Helper;

/** Exit if uninstall.php is not called by WordPress. */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Class used to implement Uninstall of UnGrabber plugin.
 *
 * @since 1.0.0
 * @author Alexandr Khmelnytsky (info@alexander.khmelnitskiy.ua)
 **/
final class Uninstall {

	/**
	 * The one true Uninstall.
	 *
	 * @var Uninstall
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new Uninstall instance.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function __construct() {

		/** Get Uninstall mode. */
		$uninstall_mode = $this->get_uninstall_mode();

		/** Send uninstall Action to our host. */
		Helper::get_instance()->send_action( 'uninstall', 'ungrabber', '2.0.1' );

		/** Remove Plugin and Settings. */
		if ( 'plugin+settings' === $uninstall_mode ) {

			/** Remove Plugin Settings. */
			$this->remove_settings();

		}

	}

	/**
	 * Return uninstall mode.
	 * plugin - Will remove the plugin only. Settings and Audio files will be saved. Used when updating the plugin.
	 * plugin+settings - Will remove the plugin and settings. Audio files will be saved. As a result, all settings will be set to default values. Like after the first installation.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function get_uninstall_mode() {

		$uninstall_settings = get_option( 'mdp_ungrabber_uninstall_settings' );

		if( isset( $uninstall_settings['mdp_ungrabber_uninstall_settings'] ) AND $uninstall_settings['mdp_ungrabber_uninstall_settings'] ) { // Default value.
			$uninstall_settings = [
				'delete_plugin' => 'plugin'
			];
		}

		return $uninstall_settings['delete_plugin'];

	}

	/**
	 * Delete Plugin Options.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function remove_settings() {

		$settings = [
			'mdp_ungrabber_envato_id',
			'mdp_ungrabber_settings',
			'mdp_ungrabber_assignments_settings',
			'mdp_ungrabber_uninstall_settings'
		];

		foreach ( $settings as $key ) {

			if ( is_multisite() ) { // For Multisite.
				if ( get_site_option( $key ) ) {
					delete_site_option( $key );
				}
			} else {
				if ( get_option( $key ) ) {
					delete_option( $key );
				}
			}
		}
	}

	/**
	 * Main Uninstall Instance.
	 *
	 * Insures that only one instance of Uninstall exists in memory at any one time.
	 *
	 * @static
	 * @return Uninstall
	 * @since 1.0.0
	 **/
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Uninstall ) ) {
			self::$instance = new Uninstall;
		}

		return self::$instance;
	}

}

/** Runs on Uninstall of UnGrabber plugin. */
Uninstall::get_instance();
