<?php
/**
 * UnGrabber
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         3.0.4
 * @copyright       (C) 2018 - 2021 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

namespace Merkulove\Ungrabber;

use Merkulove\Ungrabber\Unity\Plugin;
use Merkulove\Ungrabber\Unity\Settings;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Settings class used to modify default plugin settings.
 *
 * @since 1.0.0
 *
 **/
final class Config {

	/**
	 * The one true Settings.
	 *
     * @since 1.0.0
     * @access private
	 * @var Config
	 **/
	private static $instance;

    /**
     * Prepare plugin settings by modifying the default one.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public function prepare_settings() {

        /** Get default plugin settings. */
        $tabs = Plugin::get_tabs();

        $tabs['general']['title'] = esc_html__( 'UnGrabber General', 'ungrabber' );  // Change General tab label.

        $tabs['general']['fields']['select_all'] = [
            'type'              => 'switcher',
            'label'             => esc_html__( 'Select All', 'ungrabber' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Protect Your Text from Being Copied by Select All HotKeys', 'ungrabber' ),
            'description'       => esc_html__( 'Disable: ', 'ungrabber' ) . ' <b>Ctrl+A</b> ' .
                esc_html__( '(Windows and Linux),', 'ungrabber' ) . ' <b>⌘+A</b> ' .
                esc_html__( '(macOS)', 'ungrabber' ),
            'show_description'  => true,
            'default'           => 'on',
        ];

        $tabs['general']['fields']['copy'] = [
            'type'              => 'switcher',
            'label'             => esc_html__( 'Copy', 'ungrabber' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Protect Your Text from Being Copied by Copy HotKeys', 'ungrabber' ),
            'description'       => esc_html__( 'Disable', 'ungrabber' ) . ' <b>Ctrl+C</b> ' .
                esc_html__( '(Windows and Linux),', 'ungrabber' ) . ' <b>⌘+C</b> ' .
                esc_html__( '(macOS)', 'ungrabber' ),
            'show_description'  => true,
            'default'           => 'on',
        ];

        $tabs['general']['fields']['cut'] = [
            'type'              => 'switcher',
            'label'             => esc_html__( 'Cut', 'ungrabber' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Protect Your Text from Being Copied by Cut HotKeys', 'ungrabber' ),
            'description'       => esc_html__( 'Disable', 'ungrabber' ) . ' <b>Ctrl+X</b> ' .
                esc_html__( '(Windows and Linux),', 'ungrabber' ) . ' <b>⌘+X</b> ' .
                esc_html__( '(macOS)', 'ungrabber' ),
            'show_description'  => true,
            'default'           => 'on',
        ];

        $tabs['general']['fields']['paste'] = [
            'type'              => 'switcher',
            'label'             => esc_html__( 'Paste', 'ungrabber' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Disable Paste HotKeys', 'ungrabber' ),
            'description'       => esc_html__( 'Disable', 'ungrabber' ) . ' <b>Ctrl+V</b> ' .
                esc_html__( '(Windows and Linux),', 'ungrabber' ) . ' <b>⌘+V</b> ' .
                esc_html__( '(macOS)', 'ungrabber' ),
            'show_description'  => true,
            'default'           => 'on',
        ];

        $tabs['general']['fields']['save'] = [
            'type'              => 'switcher',
            'label'             => esc_html__( 'Save', 'ungrabber' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Protect Your Text from Being Saved by Save HotKeys', 'ungrabber' ),
            'description'       => esc_html__( 'Disable', 'ungrabber' ) . ' <b>Ctrl+S</b> ' .
                esc_html__( '(Windows and Linux),', 'ungrabber' ) . ' <b>⌘+S</b> ' .
                esc_html__( '(macOS)', 'ungrabber' ),
            'show_description'  => true,
            'default'           => 'on',
        ];

        $tabs['general']['fields']['view_source'] = [
            'type'              => 'switcher',
            'label'             => esc_html__( 'Disable View Source', 'ungrabber' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Disable to View Source Code of Page by HotKeys', 'ungrabber' ),
            'description'       => esc_html__( 'Disable', 'ungrabber' ) . ' <b>Ctrl+U</b> ' .
                esc_html__( '(Windows and Linux),', 'ungrabber' ) . ' <b>⌘+U</b> ' .
                esc_html__( '(macOS)', 'ungrabber' ),
            'show_description'  => true,
            'default'           => 'on',
        ];

        $tabs['general']['fields']['print_page'] = [
            'type'              => 'switcher',
            'label'             => esc_html__( 'Print', 'ungrabber' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Protect Your Page from Being Printed by HotKeys', 'ungrabber' ),
            'description'       => esc_html__( 'Disable', 'ungrabber' ) . ' <b>Ctrl+P</b> ' .
                esc_html__( '(Windows and Linux),', 'ungrabber' ) . ' <b>⌘+P</b> ' .
                esc_html__( '(macOS)', 'ungrabber' ),
            'show_description'  => true,
            'default'           => 'on',
        ];

        $tabs['general']['fields']['developer_tool'] = [
            'type'              => 'switcher',
            'label'             => esc_html__( 'Developer Tools', 'ungrabber' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Disable Developer Tools', 'ungrabber' ),
            'description'       => esc_html__( 'Disable', 'ungrabber' ) . ' <b>Ctrl+Shift+I</b> ' .
                esc_html__( '(Windows and Linux),', 'ungrabber' ) . ' <b>⌘+⌥+I</b> ' .
                esc_html__( '(macOS)', 'ungrabber' ),
            'show_description'  => true,
            'default'           => 'on',
        ];

        $tabs['general']['fields']['window_blur'] = [
            'type'              => 'switcher',
            'label'             => esc_html__( 'Window blur', 'ungrabber' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Protect content if browser window becomes inactive', 'ungrabber' ),
            'description'       => esc_html__( 'Hide content if the window has lost focus', 'ungrabber' ),
            'show_description'  => true,
            'default'           => 'off',
        ];

        $tabs['general']['fields']['tab_hidden'] = [
            'type'              => 'switcher',
            'label'             => esc_html__( 'Tab change', 'ungrabber' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Protect content if tab becomes inactive', 'ungrabber' ),
            'description'       => esc_html__( 'Hide content if the tab has lost focus', 'ungrabber' ),
            'show_description'  => true,
            'default'           => 'off',
        ];

        $tabs['general']['fields']['reader_mode'] = [
            'type'              => 'switcher',
            'label'             => esc_html__( 'Safari Reader Mode', 'ungrabber' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Protect Your Text and Images from being copied in the Safari Reader mode', 'ungrabber' ),
            'description'       => esc_html__( 'Disable', 'ungrabber' ) . ' <b>⌘+Shift+R</b> ' .
                esc_html__( '(macOS)', 'ungrabber' ),
            'show_description'  => true,
            'default'           => 'on',
        ];

        $tabs['general']['fields']['right_click'] = [
            'type'              => 'switcher',
            'label'             => esc_html__( 'Right Click', 'ungrabber' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Protect Your Content from Being Copied by Context Menu', 'ungrabber' ),
            'description'       => esc_html__( 'Disable Mouse Right Click ', 'ungrabber' ) . ' <b><span class="dashicons dashicons-menu"></span></b> ',
            'show_description'  => true,
            'default'           => 'on',
        ];

        $tabs['general']['fields']['right_click_image'] = [
            'type'              => 'switcher',
            'label'             => esc_html__( 'Right Click on Image', 'ungrabber' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Protect Images from Being Copied by Context Menu', 'ungrabber' ),
            'description'       => esc_html__( 'Disable Mouse Right Click on Image', 'ungrabber' ) . ' <b><span class="dashicons dashicons-menu"></span></b> ',
            'show_description'  => true,
            'default'           => 'off',
        ];

        $tabs['general']['fields']['text_selection'] = [
            'type'              => 'switcher',
            'label'             => esc_html__( 'Text Selection', 'ungrabber' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Disable Text Highlight (Text Selection) by Mouse', 'ungrabber' ),
            'description'       => esc_html__( 'Turn off highlight of any text', 'ungrabber' ),
            'show_description'  => true,
            'default'           => 'on',
        ];

        $tabs['general']['fields']['image_dragging'] = [
            'type'              => 'switcher',
            'label'             => esc_html__( 'Image Dragging', 'ungrabber' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Disable Image Dragging by Mouse', 'ungrabber' ),
            'description'       => esc_html__( 'Disable Image Dragging', 'ungrabber' ) . ' <b><span class="dashicons dashicons-move"></span></b> ',
            'show_description'  => true,
            'default'           => 'on',
        ];

        $tabs['general']['fields']['javascript'] = [
            'type'              => 'switcher',
            'label'             => esc_html__( 'JavaScript Required', 'ungrabber' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Protect Content if JavaScript is Disabled', 'ungrabber' ),
            'description'       => esc_html__( 'Some Switcher field description.', 'ungrabber' ),
            'show_description'  => true,
            'default'           => 'on',
        ];

        $tabs['general']['fields']['javascript_msg'] = [
            'type'              => 'editor',
            'label'             => esc_html__( 'JavaScript Message', 'ungrabber' ),
            'show_label'        => true,
            'description'       => esc_html__( 'Message to show if JavaScript is Disabled', 'ungrabber' ),
            'show_description'  => false,
            'default'           => '<h3>' . esc_html__( 'Please Enable JavaScript in your Browser to Visit this Site.', 'ungrabber' ) . '<h3>',
            'attr'              => [
                'textarea_rows' => '3',
            ]
        ];

        $tabs['general']['fields']['admin'] = [
            'type'              => 'switcher',
            'label'             => esc_html__( 'Administrator mode', 'ungrabber' ),
            'show_label'        => true,
            'placeholder'       => esc_html__( 'Disable plugin functionality for administrator', 'ungrabber' ),
            'description'       => esc_html__( 'Manages the plugin for users logged in as an administrator', 'ungrabber' ),
            'show_description'  => true,
            'default'           => 'on',
        ];

        unset( $tabs['custom_css'] );
        unset( $tabs['status']['reports']['server']['allow_url_fopen'] );
        unset( $tabs['status']['reports']['server']['dom_installed'] );
        unset( $tabs['status']['reports']['server']['xml_installed'] );
        unset( $tabs['status']['reports']['server']['bcmath_installed'] );

        /** Set updated tabs. */
        Plugin::set_tabs( $tabs );

        /** Refresh settings. */
        Settings::get_instance()->get_options();

    }

	/**
	 * Main Settings Instance.
	 * Insures that only one instance of Settings exists in memory at any one time.
	 *
	 * @static
     * @since 1.0.0
     * @access public
     *
	 * @return Config
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}
