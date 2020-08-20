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
 * SINGLETON: Class used to implement UninstallTab tab on plugin settings page.
 *
 * @since 1.0.0
 * @author Alexandr Khmelnytsky (info@alexander.khmelnitskiy.ua)
 **/
final class UninstallTab {

	/**
	 * The one true UninstallTab.
	 *
	 * @var UninstallTab
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new UninstallTab instance.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function __construct() {

	}

	/**
	 * Render form with all settings fields.
	 *
	 * @access public
	 * @since 1.0.0
	 **/
	public function render_form() {

		settings_fields( 'UnGrabberUninstallOptionsGroup' );
		do_settings_sections( 'UnGrabberUninstallOptionsGroup' );

	}

	/**
	 * Generate Uninstall Tab.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function add_settings() {

		/** Uninstall Tab. */
		register_setting( 'UnGrabberUninstallOptionsGroup', 'mdp_ungrabber_uninstall_settings' );
		add_settings_section( 'mdp_ungrabber_settings_page_uninstall_section', '', null, 'UnGrabberUninstallOptionsGroup' );

		/** Delete plugin. */
		add_settings_field( 'delete_plugin', esc_html__( 'Removal settings:', 'ungrabber' ), [$this, 'render_delete_plugin'], 'UnGrabberUninstallOptionsGroup', 'mdp_ungrabber_settings_page_uninstall_section' );

	}

	/**
	 * Render "Delete Plugin" field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function render_delete_plugin() {

		/** Get uninstall settings. */
		$uninstall_settings = get_option( 'mdp_ungrabber_uninstall_settings' );

		/** Set Default value 'plugin' . */
		if ( ! isset( $uninstall_settings['delete_plugin'] ) ) {
			$uninstall_settings = [
				'delete_plugin' => 'plugin'
			];
		}

		/** Prepare options for select. */
		$options = [
			'plugin' => esc_html__( 'Delete plugin only', 'ungrabber' ),
			'plugin+settings' => esc_html__( 'Delete plugin and settings', 'ungrabber' )
		];

		/** Prepare description. */
		$helper_text = esc_html__( 'Choose which data to delete upon using the "Delete" action in the "Plugins" admin page.', 'ungrabber' );

		/** Render select. */
		UI::get_instance()->render_select(
			$options,
			$uninstall_settings['delete_plugin'], // Selected option.
			esc_html__('Delete plugin', 'ungrabber' ),
			$helper_text,
			['name' => 'mdp_ungrabber_uninstall_settings[delete_plugin]']
		);

	}

	/**
	 * Main UninstallTab Instance.
	 *
	 * Insures that only one instance of UninstallTab exists in memory at any one time.
	 *
	 * @static
	 * @return UninstallTab
	 * @since 1.0.0
	 **/
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof UninstallTab ) ) {
			self::$instance = new UninstallTab;
		}

		return self::$instance;
	}

} // End Class UninstallTab.
