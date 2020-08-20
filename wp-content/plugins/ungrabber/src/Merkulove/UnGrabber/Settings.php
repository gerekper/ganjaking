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

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Class used to implement plugin settings.
 *
 * @since 1.0.0
 * @author Alexandr Khmelnytsky (info@alexander.khmelnitskiy.ua)
 **/
final class Settings {

	/**
	 * UnGrabber Plugin settings.
	 *
	 * @var array()
	 * @since 1.0.0
	 **/
	public $options = [];

	/**
	 * The one true Settings.
	 *
	 * @var Settings
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new Settings instance.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function __construct() {

		/** Get plugin settings. */
		$this->get_options();

	}

	/**
	 * Render Tabs Headers.
	 *
	 * @param string $current - Selected tab key.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render_tabs( $current = 'general' ) {

		/** Tabs array. */
		$tabs = [];
		$tabs['general'] = [
			'icon' => 'tune',
			'name' => esc_html__( 'General', 'ungrabber' )
		];

        $tabs['assignments'] = [
            'icon' => 'flag',
            'name' => esc_html__( 'Assignments', 'ungrabber' )
        ];

		/** Activation tab enable only if plugin have Envato ID. */
		$plugin_id = EnvatoItem::get_instance()->get_id();
		if ( (int)$plugin_id > 0 ) {
			$tabs['activation'] = [
				'icon' => 'vpn_key',
				'name' => esc_html__( 'Activation', 'ungrabber' )
			];
		}

		$tabs['status'] = [
			'icon' => 'info',
			'name' => esc_html__( 'Status', 'ungrabber' )
		];

		$tabs['uninstall'] = [
			'icon' => 'delete_sweep',
			'name' => esc_html__( 'Uninstall', 'ungrabber' )
		];

		/** Render Tabs. */
		?>
        <aside class="mdc-drawer">
            <div class="mdc-drawer__content">
                <nav class="mdc-list">

                    <div class="mdc-drawer__header mdc-plugin-fixed">
                        <!--suppress HtmlUnknownAnchorTarget -->
                        <a class="mdc-list-item mdp-plugin-title" href="#wpwrap">
                            <i class="mdc-list-item__graphic" aria-hidden="true">
                                <img src="<?php echo esc_attr( UnGrabber::$url . 'images/logo-color.svg' ); ?>" alt="<?php echo esc_html__( 'UnGrabber', 'ungrabber' ) ?>">
                            </i>
                            <span class="mdc-list-item__text">
                                <?php echo esc_html__( 'UnGrabber', 'ungrabber' ) ?>
                                <sup><?php echo esc_html__( 'ver.', 'ungrabber' ) . esc_html( UnGrabber::$version ); ?></sup>
                            </span>
                        </a>
                        <button type="submit" name="submit" id="submit"
                                class="mdc-button mdc-button--dense mdc-button--raised">
                            <span class="mdc-button__label"><?php echo esc_html__( 'Save changes', 'ungrabber' ) ?></span>
                        </button>
                    </div>

                    <hr class="mdc-plugin-menu">
                    <hr class="mdc-list-divider">
                    <h6 class="mdc-list-group__subheader"><?php echo esc_html__( 'Plugin settings', 'ungrabber' ) ?></h6>

					<?php

					// Plugin settings tabs
					foreach ( $tabs as $tab => $value ) {
						$class = ( $tab == $current ) ? ' mdc-list-item--activated' : '';
						echo "<a class='mdc-list-item " . $class . "' href='?post_type=ungrabber_record&page=mdp_ungrabber_settings&tab=" . $tab . "'><i class='material-icons mdc-list-item__graphic' aria-hidden='true'>" . $value['icon'] . "</i><span class='mdc-list-item__text'>" . $value['name'] . "</span></a>";
					}

					/** Helpful links. */
					$this->support_link();

					/** Activation Status. */
					PluginActivation::get_instance()->display_status();

					?>
                </nav>
            </div>
        </aside>
		<?php
	}

	/**
	 * Displays useful links for an activated and non-activated plugin.
	 *
	 * @since 1.0.0
     *
     * @return void
	 **/
	public function support_link() { ?>

        <hr class="mdc-list-divider">
        <h6 class="mdc-list-group__subheader"><?php echo esc_html__( 'Helpful links', 'ungrabber' ) ?></h6>

        <a class="mdc-list-item" href="https://docs.merkulov.design/tag/ungrabber/" target="_blank">
            <i class="material-icons mdc-list-item__graphic" aria-hidden="true"><?php echo esc_html__( 'collections_bookmark' ) ?></i>
            <span class="mdc-list-item__text"><?php echo esc_html__( 'Documentation', 'ungrabber' ) ?></span>
        </a>

		<?php if ( PluginActivation::get_instance()->is_activated() ) : /** Activated. */ ?>
            <a class="mdc-list-item" href="https://1.envato.market/ungrabber-support" target="_blank">
                <i class="material-icons mdc-list-item__graphic" aria-hidden="true">mail</i>
                <span class="mdc-list-item__text"><?php echo esc_html__( 'Get help', 'ungrabber' ) ?></span>
            </a>
            <a class="mdc-list-item" href="https://1.envato.market/cc-downloads" target="_blank">
                <i class="material-icons mdc-list-item__graphic" aria-hidden="true">thumb_up</i>
                <span class="mdc-list-item__text"><?php echo esc_html__( 'Rate this plugin', 'ungrabber' ) ?></span>
            </a>
		<?php endif; ?>

        <a class="mdc-list-item" href="https://1.envato.market/cc-merkulove" target="_blank">
            <i class="material-icons mdc-list-item__graphic" aria-hidden="true"><?php echo esc_html__( 'store' ) ?></i>
            <span class="mdc-list-item__text"><?php echo esc_html__( 'More plugins', 'ungrabber' ) ?></span>
        </a>
		<?php

	}

	/**
	 * Add plugin settings page.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function add_settings_page() {

		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
		add_action( 'admin_init', [ $this, 'settings_init' ] );

	}

	/**
	 * Create General Tab.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
    public function tab_general() {

	    /** General Tab. */
	    $group_name = 'UnGrabberOptionsGroup';
	    $section_id = 'mdp_ungrabber_settings_page_general_section';
	    $option_name = 'mdp_ungrabber_settings';

	    /** Create settings section. */
	    register_setting( $group_name, $option_name );
	    add_settings_section( $section_id, '', null, $group_name );

	    /** Render Settings fields. */

	    /** Disable Select All. */
	    add_settings_field( 'select_all', esc_html__( 'Disable Select All:', 'ungrabber' ), ['\Merkulove\UnGrabber\SettingsFields', 'select_all'], $group_name, $section_id );

	    /** Disable Copy. */
	    add_settings_field( 'copy', esc_html__( 'Disable Copy:', 'ungrabber' ), ['\Merkulove\UnGrabber\SettingsFields', 'copy'], $group_name, $section_id );

	    /** Disable Cut. */
	    add_settings_field( 'cut', esc_html__( 'Disable Cut:', 'ungrabber' ), ['\Merkulove\UnGrabber\SettingsFields', 'cut'], $group_name, $section_id );

	    /** Disable Paste. */
	    add_settings_field( 'paste', esc_html__( 'Disable Paste:', 'ungrabber' ), ['\Merkulove\UnGrabber\SettingsFields', 'paste'], $group_name, $section_id );

	    /** Disable Save. */
	    add_settings_field( 'save', esc_html__( 'Disable Save:', 'ungrabber' ), ['\Merkulove\UnGrabber\SettingsFields', 'save'], $group_name, $section_id );

	    /** Disable View Source. */
	    add_settings_field( 'view_source', esc_html__( 'Disable View Source:', 'ungrabber' ), ['\Merkulove\UnGrabber\SettingsFields', 'view_source'], $group_name, $section_id );

	    /** Disable Print Page. */
	    add_settings_field( 'print_page', esc_html__( 'Disable Print Page:', 'ungrabber' ), ['\Merkulove\UnGrabber\SettingsFields', 'print_page'], $group_name, $section_id );

	    /** Disable Developer Tool. */
	    add_settings_field( 'developer_tool', esc_html__( 'Disable Developer Tool:', 'ungrabber' ), ['\Merkulove\UnGrabber\SettingsFields', 'developer_tool'], $group_name, $section_id );

	    /** Disable Safari reader mode. */
	    add_settings_field( 'reader_mode', esc_html__( 'Disable Safari Reader Mode:', 'ungrabber' ), ['\Merkulove\UnGrabber\SettingsFields', 'reader_mode'], $group_name, $section_id );

	    /** Disable Right Click. */
	    add_settings_field( 'right_click', esc_html__( 'Disable Right Click:', 'ungrabber' ), ['\Merkulove\UnGrabber\SettingsFields', 'right_click'], $group_name, $section_id );

	    /** Disable Text Selection. */
	    add_settings_field( 'text_selection', esc_html__( 'Disable Text Selection:', 'ungrabber' ), ['\Merkulove\UnGrabber\SettingsFields', 'text_selection'], $group_name, $section_id );

	    /** Disable Image Dragging. */
	    add_settings_field( 'image_dragging', esc_html__( 'Disable Image Dragging:', 'ungrabber' ), ['\Merkulove\UnGrabber\SettingsFields', 'image_dragging'], $group_name, $section_id );

	    /** JavaScript Required. */
	    add_settings_field( 'javascript', esc_html__( 'JavaScript Required:', 'ungrabber' ), ['\Merkulove\UnGrabber\SettingsFields', 'javascript'], $group_name, $section_id );

	    /** JavaScript Required Message. */
	    add_settings_field( 'javascript_msg', esc_html__( 'JavaScript Message:', 'ungrabber' ), ['\Merkulove\UnGrabber\SettingsFields', 'javascript_msg'], $group_name, $section_id );

    }

	/**
	 * Generate Settings Page.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function settings_init() {

		/** General Tab. */
	    $this->tab_general();

		/** Create Assignments Tab. */
		AssignmentsTab::get_instance()->add_settings();

		/** Activation Tab. */
		PluginActivation::get_instance()->add_settings();

		/** Create Status Tab. */
		StatusTab::get_instance()->add_settings();

		/** Create Uninstall Tab. */
		UninstallTab::get_instance()->add_settings();

	}

	/**
	 * Add admin menu for plugin settings.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function add_admin_menu() {

		add_menu_page(
			esc_html__( 'UnGrabber Settings', 'ungrabber' ),
			esc_html__( 'UnGrabber', 'ungrabber' ),
			'manage_options',
			'mdp_ungrabber_settings',
			[ $this, 'options_page' ],
			'data:image/svg+xml;base64,' . base64_encode( file_get_contents( UnGrabber::$path . 'images/logo-menu.svg' ) ),
			'58.0907'// Always change digits after "." for different plugins.
		);

	}

	/**
	 * Plugin Settings Page.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function options_page() {

		/** User rights check. */
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		} ?>
        <!--suppress HtmlUnknownTarget -->
        <form action='options.php' method='post'>
            <div class="wrap">

				<?php
				$tab = 'general';
				if ( isset ( $_GET['tab'] ) ) { $tab = $_GET['tab']; }

				/** Render "UnGrabber settings saved!" message. */
				SettingsFields::get_instance()->render_nags();

				/** Render Tabs Headers. */
				?><section class="mdp-aside"><?php $this->render_tabs( $tab ); ?></section><?php

				/** Render Tabs Body. */
				?><section class="mdp-tab-content mdp-tab-<?php echo esc_attr( $tab ) ?>"><?php

					/** General Tab. */
					if ( 'general' === $tab ) {
						echo '<h3>' . esc_html__( 'UnGrabber Settings', 'ungrabber' ) . '</h3>';
						settings_fields( 'UnGrabberOptionsGroup' );
						do_settings_sections( 'UnGrabberOptionsGroup' );

                    /** Assignments Tab. */
					} elseif ( 'assignments' === $tab ) {
						echo '<h3>' . esc_html__( 'Assignments Settings', 'ungrabber' ) . '</h3>';
						settings_fields( 'UnGrabberAssignmentsOptionsGroup' );
						do_settings_sections( 'UnGrabberAssignmentsOptionsGroup' );
						AssignmentsTab::get_instance()->render_assignments();

					/** Activation Tab. */
					} elseif ( 'activation' === $tab ) {
						settings_fields( 'UnGrabberActivationOptionsGroup' );
						do_settings_sections( 'UnGrabberActivationOptionsGroup' );
						PluginActivation::get_instance()->render_pid();

                    /** Status tab. */
					} elseif ( 'status' === $tab ) {
						echo '<h3>' . esc_html__( 'System Requirements', 'ungrabber' ) . '</h3>';
						StatusTab::get_instance()->render_form();

					} /** Uninstall Tab. */
                    elseif ( 'uninstall' === $tab ) {
						echo '<h3>' . esc_html__( 'Uninstall Settings', 'ungrabber' ) . '</h3>';
						UninstallTab::get_instance()->render_form();
					}

					?>
                </section>
            </div>
        </form>

		<?php
	}

	/**
	 * Get plugin settings with default values.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 **/
	public function get_options() {

		/** General Tab Options. */
		$options = get_option( 'mdp_ungrabber_settings' );

		/** Default values. */
		$defaults = [
			// General Tab
			'select_all'        => isset( $options[ 'select_all' ] ) ? $options[ 'select_all' ] : 'on',
		    'copy'              => isset( $options[ 'copy' ] ) ? $options[ 'copy' ] : 'on',
		    'cut'               => isset( $options[ 'cut' ] ) ? $options[ 'cut' ] : 'on',
		    'paste'             => isset( $options[ 'paste' ] ) ? $options[ 'paste' ] : 'on',
		    'save'              => isset( $options[ 'save' ] ) ? $options[ 'save' ] : 'on',
		    'view_source'       => isset( $options[ 'view_source' ] ) ? $options[ 'view_source' ] : 'on',
		    'print_page'        => isset( $options[ 'print_page' ] ) ? $options[ 'print_page' ] : 'on',
		    'developer_tool'    => isset( $options[ 'developer_tool' ] ) ? $options[ 'developer_tool' ] : 'on',
		    'reader_mode'       => isset( $options[ 'reader_mode' ] ) ? $options[ 'reader_mode' ] : 'on',
		    'right_click'       => isset( $options[ 'right_click' ] ) ? $options[ 'right_click' ] : 'on',
		    'text_selection'    => isset( $options[ 'text_selection' ] ) ? $options[ 'text_selection' ] : 'on',
		    'image_dragging'    => isset( $options[ 'image_dragging' ] ) ? $options[ 'image_dragging' ] : 'on',
		    'javascript'        => isset( $options[ 'javascript' ] ) ? $options[ 'javascript' ] : 'on',
		    'javascript_msg'    => '<h3>' . esc_html__( 'Please Enable JavaScript in your Browser to Visit this Site.', 'ungrabber' ) . '<h3>',
        ];

		$results = wp_parse_args( $options, $defaults );

		$this->options = $results;
	}

	/**
	 * Main Settings Instance.
	 *
	 * Insures that only one instance of Settings exists in memory at any one time.
	 *
	 * @static
	 * @return Settings
	 * @since 1.0.0
	 **/
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Settings ) ) {
			self::$instance = new Settings;
		}

		return self::$instance;
	}

} // End Class Settings.
