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
 * SINGLETON: Class used to implement Uninstall tab on plugin settings page.
 *
 * @since 1.0.0
 *
 **/
final class TabUninstall extends Tab {

    /**
     * Slug of current tab.
     *
     * @since 1.0.0
     * @const TAB_SLUG
     **/
    const TAB_SLUG = 'uninstall';

	/**
	 * The one true TabUninstall.
	 *
     * @since 1.0.0
     * @access private
	 * @var TabUninstall
	 **/
	private static $instance;

    /**
     * Render form with all settings fields.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public function do_settings() {

        /** No status tab, nothing to do. */
        if ( ! $this->is_enabled( self::TAB_SLUG ) ) { return; }

        /** Render title. */
        $this->render_title( self::TAB_SLUG );

        /** Render fields. */
        $this->do_settings_base( self::TAB_SLUG );

    }

	/**
	 * Generate Uninstall Tab.
	 *
	 * @access public
	 **/
	public function add_settings() {

        /** Custom Uninstall Tab. */
        $this->add_settings_base( self::TAB_SLUG );

        $group = 'Ungrabber' . self::TAB_SLUG . 'OptionsGroup';
        $section = 'mdp_ungrabber_' . self::TAB_SLUG . '_page_status_section';

		/** Delete plugin. */
		add_settings_field( 'delete_plugin', esc_html__( 'Removal settings:', 'ungrabber' ), [$this, 'render_delete_plugin'], $group, $section );

	}

	/**
	 * Render "Delete Plugin" field.
	 *
     * @since 1.0.0
	 * @access public
     *
     * @return void
	 **/
	public function render_delete_plugin() {

		/** Get uninstall settings. */
		$uninstall_settings = get_option( 'mdp_ungrabber_uninstall_settings' );

		/** Set Default value 'plugin'. */
		if ( ! isset( $uninstall_settings[ 'delete_plugin' ] ) ) {
			$uninstall_settings = [
				'delete_plugin' => 'plugin'
			];
		}

		$options = Plugin::get_tabs()['uninstall']['fields']['delete_plugin']['options'];

		/** Prepare description. */
		$helper_text = esc_html__( 'Choose which data to delete upon using the "Delete" action in the "Plugins" admin page.', 'ungrabber' );

		/** Render select. */
		UI::get_instance()->render_select(
			$options,
			$uninstall_settings[ 'delete_plugin' ], // Selected option.
			esc_html__( 'Delete plugin', 'ungrabber' ),
			$helper_text,
			[
			    'name' => 'mdp_ungrabber_uninstall_settings[delete_plugin]'
            ]
		);

	}

	/**
	 * Main TabUninstall Instance.
	 * Insures that only one instance of TabUninstall exists in memory at any one time.
	 *
	 * @static
	 * @return TabUninstall
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}
