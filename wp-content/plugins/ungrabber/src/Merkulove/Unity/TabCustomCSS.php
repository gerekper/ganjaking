<?php
/**
 * UnGrabber
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         3.0.2
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
 * SINGLETON: Class used to implement Status Tab on plugin settings page.
 *
 * @since 1.0.0
 *
 **/
final class TabCustomCSS extends Tab {

    /**
     * Slug of current tab.
     *
     * @const TAB_SLUG
     **/
    const TAB_SLUG = 'custom_css';

	/**
	 * The one true StatusTab.
	 *
     * @var TabCustomCSS
	 **/
	private static $instance;

    /**
     * Sets up a new TabCustomCSS instance.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function __construct() {

        /** Add admin javascript. */
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );

    }

    /**
     * Load JS and CSS for Backend Area.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public function admin_scripts() {

        $screen = get_current_screen();
        if ( null === $screen ) { return; }

        /** Add styles only on plugin settings page */
        if ( ! in_array( $screen->base, Plugin::get_menu_bases(), true ) ) { return; }

        /** Add code editor for Custom CSS. */
        wp_enqueue_code_editor( ['type' => 'application/x-httpd-php'] );

    }


    /**
	 * Generate Status Tab.
	 *
	 * @access public
     * @return void
	 **/
	public function add_settings() {

		/** Custom CSS Tab. */
        $this->add_settings_base( self::TAB_SLUG );

	}

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

        /** Render CSS field. */
        $this->render_custom_css();
    }

    /**
     * Render Custom CSS editor field.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public function render_custom_css() {
        ?>
        <div>
            <label>
                <textarea id="mdp_custom_css_fld" name="mdp_ungrabber_<?php esc_attr_e( self::TAB_SLUG ); ?>_settings[custom_css]" class="mdp_custom_css_fld"><?php
                    echo esc_textarea( Settings::get_instance()->options['custom_css'] );
                ?></textarea>
            </label>

            <?php if ( Plugin::get_tabs()[ 'custom_css' ][ 'fields' ][ 'custom_css' ][ 'show_description' ] ) : ?>
                <p class="description"><?php esc_html_e( Plugin::get_tabs()[ 'custom_css' ][ 'fields' ][ 'custom_css' ][ 'description' ] ); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

	/**
	 * Main StatusTab Instance.
	 * Insures that only one instance of StatusTab exists in memory at any one time.
	 *
	 * @static
     * @return TabCustomCSS
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}
