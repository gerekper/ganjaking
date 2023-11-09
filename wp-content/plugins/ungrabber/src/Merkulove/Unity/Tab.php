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

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

/**
 * Base methods for Tabs Classes.
 *
 * @since 1.0.0
 *
 **/
abstract class Tab {

    /**
     * Check if tab exist and enabled.
     *
     * @param string $tab_slug - Slug of tub to check.
     *
     * @since  1.0.0
     * @access protected
     *
     * @return bool - True if Tab is enabled, false otherwise.
     **/
    protected function is_enabled( $tab_slug = null ) {

        /** Foolproof. */
        if ( null === $tab_slug ) { return false; }

        /** Get all tabs and settings. */
        $tabs = Plugin::get_tabs();

        /** Check if status tab exist. */
        if ( ! isset( $tabs[ $tab_slug ] ) ) { return false; }

        /** Check if 'enabled' field of status tab exist. */
        if ( ! isset( $tabs[ $tab_slug ][ 'enabled' ] ) ) { return false; }

        /** Check if status tab is enabled. */
        return true === $tabs[ $tab_slug ][ 'enabled' ];

    }

    /**
     * Render tab title.
     *
     * @param string $tab_slug - Slug of tub to check.
     *
     * @since  1.0.0
     * @access protected
     *
     * @return void
     **/
    protected function render_title( $tab_slug = null ) {

        /** Foolproof. */
        if ( null === $tab_slug ) { return; }

        /** Get all tabs and settings. */
        $tabs = Plugin::get_tabs();

        /** Get selected to process tab. */
        $tab = $tabs[ $tab_slug ];

        /** If title exists */
        if ( isset( $tab[ 'title' ] ) ) {

            echo '<h3 class="mdp-tab-title">' . esc_html__( $tab[ 'title' ] ) . '</h3>';

        }

        /** If description exists */
        if ( isset( $tab[ 'description' ] ) ) {

            echo '<p class="mdp-tab-description">' . wp_kses_post( $tab[ 'description' ] ) . '</p>';

        }

    }

    /**
     * Output nonce, action, and option_page fields for a settings page.
     * Prints out all settings sections added to a particular settings page
     *
     * @param string $tab_slug - Slug of tub to check.
     *
     * @since  1.0.0
     * @access protected
     *
     * @return void
     **/
    protected function do_settings_base( $tab_slug = null ) {

        /** Foolproof. */
        if ( null === $tab_slug ) { return; }

        settings_fields( 'Ungrabber' . $tab_slug . 'OptionsGroup' );
        do_settings_sections( 'Ungrabber' . $tab_slug. 'OptionsGroup' );

    }

    /**
     * Registers a setting and its data.
     * Add a new section to a settings page.
     *
     * @param string $tab_slug - Slug of tub to check.
     *
     * @since  1.0.0
     * @access protected
     *
     * @return void
     **/
    protected function add_settings_base( $tab_slug = null ) {

        /** Foolproof. */
        if ( null === $tab_slug ) { return; }

        /** Status Tab. */
        register_setting( 'Ungrabber' . $tab_slug . 'OptionsGroup', 'mdp_ungrabber_' . $tab_slug . '_settings' );
        add_settings_section( 'mdp_ungrabber_' . $tab_slug . '_page_status_section', '', null, 'Ungrabber' . $tab_slug . 'OptionsGroup' );

    }

    /**
     * Check if tab is enabled by tab slug.
     *
     * @param string $tab_slug - Tab slug.
     *
     * @since  1.0.0
     * @access private
     *
     * @return bool
     **/
    public static function is_tab_enabled( $tab_slug ) {

        /** Get all tabs and settings. */
        $tabs = Plugin::get_tabs();

        return isset( $tabs[ $tab_slug ][ 'enabled' ] ) && $tabs[ $tab_slug ][ 'enabled' ];

    }

    /**
     * Add new tab to plugin settings.
     *
     * @param string $slug - Tab slug.
     * @param int $offset - Position of tab in settings. 0 - first, 1 - second, etc.
     * @param string|bool $icon - Icon of tab.
     * @param string|bool $label - Label of tab in sidebar.
     * @param string|bool $title - Title of tab on the tab page.
     * @param string|bool $description - Description of tab on the tab page.
     *
     * @return void
     */
    public static function add_settings_tab(
        string $slug,
        int $offset = 0,
        $icon = false,
        $label = false,
        $title = false,
        $description = false,
        $class = TabGeneral::class
    ) {

        $tabs = Plugin::get_tabs();

        // Check if tab already exist
        if ( isset( $tabs[ $slug ] ) ) {
            return;
        }

        // Create new tab
        $new_tab = array(
            'enabled'       => true,
            'class'         => $class,
            'label'         => $label ?? esc_html__( 'Tab', 'ungrabber' ),
            'title'         => $title ?? esc_html__( 'Tab', 'ungrabber' ),
            'description'   => $description ?? false,
            'show_title'    => isset( $title ),
            'icon'          => $icon ?? 'position_bottom_left',
        );

        // Insert new tab after $offset
        Plugin::set_tabs(
            array_slice( $tabs, 0, $offset, true ) +
            [ $slug => $new_tab ] +
            array_slice( $tabs, $offset, NULL, true )
        );

    }

}
