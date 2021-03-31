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
 * SINGLETON: Class used to implement Updates Tab on plugin settings page.
 *
 * @since 1.0.0
 *
 **/
final class TabUpdates extends Tab {

    /**
     * Slug of current tab.
     *
     * @since 1.0.0
     * @const TAB_SLUG
     **/
    const TAB_SLUG = 'updates';

	/**
	 * The one true TabUpdates.
	 *
     * @since 1.0.0
	 * @var TabUpdates
	 **/
	private static $instance;

    /**
     * Sets up a new TabUpdates instance.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function __construct() {

        /** Reset Settings. */
        add_action( 'wp_ajax_check_updates', [ __CLASS__, 'ajax_check_updates' ] );

    }

    /**
     * Generate Updates Tab.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
	public function add_settings() {

		/** Updates Tab. */
		$this->add_settings_base( self::TAB_SLUG );

		$group = 'Ungrabber' . self::TAB_SLUG . 'OptionsGroup';
		$section = 'mdp_ungrabber_' . self::TAB_SLUG . '_page_status_section';

        /** Check for Updates button. */
        add_settings_field( 'check_updates', esc_html__( 'Check for updates:', 'ungrabber' ), [$this, 'check_updates'], $group, $section );

	}

    /**
     * Render Check for Updates button.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public function check_updates() {

        UI::get_instance()->render_button(
            esc_html__( 'Check Updates', 'ungrabber' ),
            '',
            'autorenew',
            [
                "name" => 'mdp_ungrabber_' . self::TAB_SLUG . '_settings' . "[check_updates]",
                "id" => "mdp-updates-btn",
                "class" => "mdc-button--outlined"
            ]
        );

    }

    /**
     * Render tab content with all settings fields.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
	public function do_settings() {

        /** No updates tab, nothing to do. */
        if ( ! $this->is_enabled( self::TAB_SLUG ) ) { return; }

        /** Render title. */
        $this->render_title( self::TAB_SLUG );

        /** Render fields. */
        $this->do_settings_base( self::TAB_SLUG );

		/** Render "Changelog". */
		$this->render_changelog();

	}

	/**
	 * Render "Changelog" field.
	 *
     * @since 1.0.0
	 * @access public
     *
     * @return void
	 **/
	public function render_changelog() {

        /** Do we have changelog in cache? */
	    $cache = new Cache();
        $key = 'changelog';
        $cached_changelog = $cache->get( $key, true );

        /** Show changelog from cache. */
        if ( ! empty( $cached_changelog ) ) {

            /** Print HTML changelog. */
            $cached_changelog = json_decode( $cached_changelog, true );
            $this->print_changelog( $cached_changelog[$key] );
            return;

        }

        /** Get changelog from remote host. */
        $remote_changelog = $this->get_changelog_remote();
        if ( false === $remote_changelog ) { return; }

        /** Store changelog in cache. */
        $cache->set( $key, [$key => $remote_changelog], false );

		/** Print HTML changelog. */
        $this->print_changelog( $remote_changelog );

    }

    /**
     * Get changelog from remote host.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string|false
     **/
    private function get_changelog_remote() {

        /** Build changelog url. */
        $changelog_url = 'https://merkulove.host/changelog/' . Plugin::get_slug() . '.html';

        /** Get fresh changelog file. */
        $changelog = wp_remote_get( $changelog_url );

        /** Check for errors. */
        if ( is_wp_error( $changelog ) || empty( $changelog['body'] ) ) { return false; }

        /** Now in $changelog we have changelog in HTML. */
        $changelog = $changelog['body'];

        /** This is not like our changelog. */
        if ( false === strpos( $changelog, '<h3>Changelog</h3>' ) ) { return false; }

        return $changelog;

    }

    /**
     * Print HTML changelog.
     *
     * @since 1.0.0
     * @param string $changelog - Full changelog in HTML.
     * @access public
     *
     * @return void
     **/
    private function print_changelog( $changelog ) {

        ?><div class="mdc-changelog"><?php echo wp_kses( $changelog, Helper::get_kses_allowed_tags_svg() ); ?></div><?php

    }

    /**
     * Ajax Reset plugin settings.
     *
     * @access public
     * @return void
     **/
    public static function ajax_check_updates() {

        /** Check nonce for security. */
        check_ajax_referer( 'ungrabber', 'nonce' );

        /** Do we need to do a full reset? */
        if ( empty( $_POST['checkUpdates'] ) ) {  wp_send_json( 'Wrong Parameter Value.' ); }

        /** Clear cache table. */
        $cache = new Cache();
        $cache->drop_cache_table();

        /** Return JSON result. */
        wp_send_json( true );

    }

	/**
	 * Main TabUpdates Instance.
	 * Insures that only one instance of TabUpdates exists in memory at any one time.
	 *
	 * @static
     * @since 1.0.0
     * @access public
     *
	 * @return TabUpdates
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}
