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

use Merkulove\UnGrabber;
use WP_Query;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Class used to render plugin settings fields.
 *
 * @since 1.0.0
 * @author Alexandr Khmelnytsky (info@alexander.khmelnitskiy.ua)
 **/
final class SettingsFields {

	/**
	 * The one true SettingsFields.
	 *
	 * @var SettingsFields
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Render Disable Select All field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function select_all() {

		/** Render Disable Select All switcher. */
		UI::get_instance()->render_switches(
			Settings::get_instance()->options['select_all'],

			esc_html__('Protect Your Text from Being Copied by Select All HotKeys.', 'ungrabber' ),

			'' .
			esc_html__( 'Disable: ', 'ungrabber' ) . ' <b>Ctrl+A</b> ' .
            esc_html__( '(Windows and Linux),', 'ungrabber' ) . ' <b>⌘+A</b> ' .
            esc_html__( '(macOS)', 'ungrabber' ),

			[
				'name' => 'mdp_ungrabber_settings[select_all]',
				'id' => 'mdp_ungrabber_settings_select_all'
			]
		);

	}

	/**
	 * Render Disable Copy field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function copy() {

		/** Render Disable Copy switcher. */
		UI::get_instance()->render_switches(
			Settings::get_instance()->options['copy'],

			esc_html__('Protect Your Text from Being Copied by Copy HotKeys.', 'ungrabber' ),

			'' .
			esc_html__( 'Disable', 'ungrabber' ) . ' <b>Ctrl+C</b> ' .
			esc_html__( '(Windows and Linux),', 'ungrabber' ) . ' <b>⌘+C</b> ' .
			esc_html__( '(macOS)', 'ungrabber' ),

			[
				'name' => 'mdp_ungrabber_settings[copy]',
				'id' => 'mdp_ungrabber_settings_copy'
			]
		);

	}

	/**
	 * Render Disable Cut field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function cut() {

		/** Render Disable Cut switcher. */
		UI::get_instance()->render_switches(
			Settings::get_instance()->options['cut'],

			esc_html__('Protect Your Text from Being Copied by Cut HotKeys.', 'ungrabber' ),

			'' .
			esc_html__( 'Disable', 'ungrabber' ) . ' <b>Ctrl+X</b> ' .
			esc_html__( '(Windows and Linux),', 'ungrabber' ) . ' <b>⌘+X</b> ' .
			esc_html__( '(macOS)', 'ungrabber' ),

			[
				'name' => 'mdp_ungrabber_settings[cut]',
				'id' => 'mdp_ungrabber_settings_cut'
			]
		);

	}

	/**
	 * Render Disable Paste field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function paste() {

		/** Render Disable Paste switcher. */
		UI::get_instance()->render_switches(
			Settings::get_instance()->options['paste'],

			esc_html__('Disable Paste HotKeys.', 'ungrabber' ),

			'' .
			esc_html__( 'Disable', 'ungrabber' ) . ' <b>Ctrl+V</b> ' .
			esc_html__( '(Windows and Linux),', 'ungrabber' ) . ' <b>⌘+V</b> ' .
			esc_html__( '(macOS)', 'ungrabber' ),

			[
				'name' => 'mdp_ungrabber_settings[paste]',
				'id' => 'mdp_ungrabber_settings_paste'
			]
		);

	}

	/**
	 * Render Disable Save field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function save() {

		/** Render Disable Save switcher. */
		UI::get_instance()->render_switches(
			Settings::get_instance()->options['save'],

			esc_html__('Protect Your Text from Being Saved by Save HotKeys.', 'ungrabber' ),

			'' .
			esc_html__( 'Disable', 'ungrabber' ) . ' <b>Ctrl+S</b> ' .
			esc_html__( '(Windows and Linux),', 'ungrabber' ) . ' <b>⌘+S</b> ' .
			esc_html__( '(macOS)', 'ungrabber' ),

			[
				'name' => 'mdp_ungrabber_settings[save]',
				'id' => 'mdp_ungrabber_settings_save'
			]
		);

	}

	/**
	 * Render Disable View Source field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function view_source() {

		/** Render Disable View Source switcher. */
		UI::get_instance()->render_switches(
			Settings::get_instance()->options['view_source'],

			esc_html__('Disable to View Source Code of Page by HotKeys.', 'ungrabber' ),

			'' .
			esc_html__( 'Disable', 'ungrabber' ) . ' <b>Ctrl+U</b> ' .
			esc_html__( '(Windows and Linux),', 'ungrabber' ) . ' <b>⌘+U</b> ' .
			esc_html__( '(macOS)', 'ungrabber' ),

			[
				'name' => 'mdp_ungrabber_settings[view_source]',
				'id' => 'mdp_ungrabber_settings_view_source'
			]
		);

	}

	/**
	 * Render Disable Print Page field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function print_page() {

		/** Render Disable Print Page switcher. */
		UI::get_instance()->render_switches(
			Settings::get_instance()->options['print_page'],

			esc_html__('Protect Your Page from Being Printed by HotKeys.', 'ungrabber' ),

			'' .
			esc_html__( 'Disable', 'ungrabber' ) . ' <b>Ctrl+P</b> ' .
			esc_html__( '(Windows and Linux),', 'ungrabber' ) . ' <b>⌘+P</b> ' .
			esc_html__( '(macOS)', 'ungrabber' ),

			[
				'name' => 'mdp_ungrabber_settings[print_page]',
				'id' => 'mdp_ungrabber_settings_print_page'
			]
		);

	}

	/**
	 * Render Disable Developer Tool field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function developer_tool() {

		/** Render Disable Developer Tool switcher. */
		UI::get_instance()->render_switches(
			Settings::get_instance()->options['developer_tool'],

			esc_html__('Disable Developer Tools.', 'ungrabber' ),

			'' .
			esc_html__( 'Disable', 'ungrabber' ) . ' <b>Ctrl+Shift+I</b> ' .
			esc_html__( '(Windows and Linux),', 'ungrabber' ) . ' <b>⌘+⌥+I</b> ' .
			esc_html__( '(macOS)', 'ungrabber' ),

			[
				'name' => 'mdp_ungrabber_settings[developer_tool]',
				'id' => 'mdp_ungrabber_settings_developer_tool'
			]
		);

	}

	/**
	 * Render Disable Safari Reader Mode field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function reader_mode() {

		/** Render Disable Safari Reader Mode switcher. */
		UI::get_instance()->render_switches(
			Settings::get_instance()->options['reader_mode'],

			esc_html__('Protect Your Text and Images from being copied in the Safari Reader mode.', 'ungrabber' ),

			'' .
			esc_html__( 'Disable', 'ungrabber' ) . ' <b>⌘+Shift+R</b> ' .
			esc_html__( '(macOS)', 'ungrabber' ),

			[
				'name' => 'mdp_ungrabber_settings[reader_mode]',
				'id' => 'mdp_ungrabber_settings_reader_mode'
			]
		);

	}

	/**
	 * Render Disable Right Click field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function right_click() {

		/** Render Disable Right Click switcher. */
		UI::get_instance()->render_switches(
			Settings::get_instance()->options['right_click'],

			esc_html__('Protect Your Content from Being Copied by Context Menu.', 'ungrabber' ),

			'' .
			esc_html__( 'Disable Mouse Right Click ', 'ungrabber' ) . ' <b><span class="dashicons dashicons-menu"></span></b> ',

			[
				'name' => 'mdp_ungrabber_settings[right_click]',
				'id' => 'mdp_ungrabber_settings_right_click'
			]
		);

	}

	/**
	 * Render Disable Text Selection field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function text_selection() {

		/** Render Disable Text Selection switcher. */
		UI::get_instance()->render_switches(
			Settings::get_instance()->options['text_selection'],
			esc_html__( 'Disable Text Highlight (Text Selection) by Mouse.', 'ungrabber' ),
			esc_html__( 'Turn off highlight of any text', 'ungrabber' ),
			[
				'name' => 'mdp_ungrabber_settings[text_selection]',
				'id' => 'mdp_ungrabber_settings_text_selection'
			]
		);

	}

	/**
	 * Render Disable Image Dragging field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function image_dragging() {

		/** Render Disable Image Dragging switcher. */
		UI::get_instance()->render_switches(
			Settings::get_instance()->options['image_dragging'],
			esc_html__('Disable Image Dragging by Mouse.', 'ungrabber' ),
			esc_html__( 'Disable Image Dragging', 'ungrabber' ) . ' <b><span class="dashicons dashicons-move"></span></b> ',
			[
				'name' => 'mdp_ungrabber_settings[image_dragging]',
				'id' => 'mdp_ungrabber_settings_image_dragging'
			]
		);

	}

	/**
	 * Render JavaScript Required field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function javascript() {

		/** Render JavaScript Required switcher. */
		UI::get_instance()->render_switches(
			Settings::get_instance()->options['javascript'],
			esc_html__('Protect Content if JavaScript is Disabled.', 'ungrabber' ),
			'',
			[
				'name' => 'mdp_ungrabber_settings[javascript]',
				'id' => 'mdp_ungrabber_settings_javascript'
			]
		);

	}

	/**
	 * Render JavaScript Required Message field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public static function javascript_msg() {

		/** Render JavaScript Required Message editor. */
		wp_editor( Settings::get_instance()->options['javascript_msg'], 'mdpungrabbersettingsjavascriptmsg', array( 'textarea_rows' => 3, 'textarea_name' => 'mdp_ungrabber_settings[javascript_msg]' ) );
		?>
		<div class="mdc-text-field-helper-line">
            <div class="mdc-text-field-helper-text mdc-text-field-helper-text--persistent"><?php esc_html_e( 'Message to show if JavaScript is Disabled.', 'ungrabber' ); ?></div>
        </div>
		<?php

	}

	/**
	 * Render "SettingsFields Saved" nags.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 **/
	public static function render_nags() {

		/** Did we try to save settings? */
		if ( ! isset( $_GET['settings-updated'] ) ) { return; }

		/** Are the settings saved successfully? */
		if ( $_GET['settings-updated'] === 'true' ) {

			/** Render "SettingsFields Saved" message. */
			UI::get_instance()->render_snackbar( esc_html__( 'Settings saved!', 'ungrabber' ) );
		}

		if ( ! isset( $_GET['tab'] ) ) { return; }

		if ( strcmp( $_GET['tab'], "activation" ) == 0 ) {

			if ( PluginActivation::get_instance()->is_activated() ) {

				/** Render "Activation success" message. */
				UI::get_instance()->render_snackbar( esc_html__( 'Plugin activated successfully.', 'ungrabber' ), 'success', 5500 );

			} else {

				/** Render "Activation failed" message. */
				UI::get_instance()->render_snackbar( esc_html__( 'Invalid purchase code.', 'ungrabber' ), 'error', 5500 );

			}

		}

	}

	/**
	 * Main SettingsFields Instance.
	 *
	 * Insures that only one instance of SettingsFields exists in memory at any one time.
	 *
	 * @static
	 * @return SettingsFields
	 * @since 1.0.0
	 **/
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SettingsFields ) ) {
			self::$instance = new SettingsFields;
		}

		return self::$instance;
	}
	
} // End Class SettingsFields.
