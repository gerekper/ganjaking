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
 * SINGLETON: Class used to implement plugin settings.
 *
 * @since 1.0.0
 *
 **/
final class Settings {

	/**
	 * Plugin settings.
     *
     * @since 1.0.0
     * @access public
	 * @var array
	 **/
	public $options = [];

	/**
	 * The one true Settings.
	 *
     * @since 1.0.0
     * @access private
	 * @var Settings
	 **/
	private static $instance;

	/**
	 * Sets up a new Settings instance.
	 *
     * @since 1.0.0
	 * @access private
     *
     * @return void
	 **/
	private function __construct() {

		/** Get plugin settings. */
	    $this->get_options();

        /** Add plugin settings page. */
        $this->add_settings_page();

		/** Ajax handler for media library control */
		add_action( 'wp_ajax_unity_media_library', [ $this, 'field_media_library' ] );

    }

	/**
	 * Filed media library
	 * Refresh image in media library
	 * @return void
	 */
	public function field_media_library() {

		if( isset( $_GET[ 'id' ] ) ) {

			$image = wp_get_attachment_image(
				filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT ),
				'medium'
			);
			$data = array(
				'image'    => $image,
			);
			wp_send_json_success( $data );

		} else {

			wp_send_json_error();

		}

	}

	/**
	 * Render Tabs Headers.
	 *
	 * @param string $current_tab - current tab slug
     *
     * @return void
	 */
	private function render_tabs( string $current_tab = '') {

		/** Tabs array. */
		$tabs = Plugin::get_tabs();

		/** If the plugin haven't Envato ID, remove activation tab. */
		if ( 0 === EnvatoItem::get_instance()->get_id() ) {
		    unset( $tabs['activation'] );
        }

		/** Render Tabs. */
		?>
        <aside class="mdc-drawer">
            <div class="mdc-drawer__content">
                <nav class="mdc-list">

                    <?php $this->render_logo(); ?>
                    <hr class="mdc-plugin-menu">
                    <hr class="mdc-list-divider">

                    <h6 class="mdc-list-group__subheader"><?php echo esc_html__( 'Plugin settings', 'ungrabber' ) ?></h6>
					<?php

					foreach ( $tabs as $tab => $value ) {

                        /** Skip disabled tabs. */
					    if ( ! Tab::is_tab_enabled( $tab ) ) { continue; }

						/** Prepare CSS classes. */
						$classes = [];
						$classes[] = 'mdc-list-item';
                        $classes[] = 'mdp-menu-tab-' . esc_attr( $tab );

						/** Mark Active Tab. */
						if ( $tab === $current_tab ) {
							$classes[] = 'mdc-list-item--activated';
						}

						/** Prepare link. */
						$link = admin_url( 'admin.php?page=mdp_ungrabber_settings&tab=' . $tab );

						?>
                        <a class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" href="<?php echo esc_attr( $link ); ?>">
                            <i class='material-icons mdc-list-item__graphic' aria-hidden='true'><?php esc_html_e( $value['icon'] ); ?></i>
                            <span class='mdc-list-item__text'><?php esc_html_e( $value['label'] ); ?></span>
                        </a>
						<?php

					}

					/** Helpful links. */
					$this->support_link();

					/** Activation Status. */
					TabActivation::get_instance()->display_status();

                    /** Social links */
                    $this->social_link();

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
     * @access private
	 *
     * @return void
	 **/
	private function support_link() {

	    /** Disable this method for custom type plugins. */
	    if ( 'custom' === Plugin::get_type() ) { return; }

	    ?>
        <hr class="mdc-list-divider">
        <h6 class="mdc-list-group__subheader"><?php echo esc_html__( 'Helpful links', 'ungrabber' ) ?></h6>

        <a class="mdc-list-item" href="https://docs.merkulov.design/tag/ungrabber" target="_blank">
            <i class="material-icons mdc-list-item__graphic" aria-hidden="true">collections_bookmark</i>
            <span class="mdc-list-item__text"><?php echo esc_html__( 'Documentation', 'ungrabber' ) ?></span>
        </a>

		<?php if ( TabActivation::get_instance()->is_activated() ) : /** Activated. */ ?>
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
            <i class="material-icons mdc-list-item__graphic" aria-hidden="true">store</i>
            <span class="mdc-list-item__text"><?php echo esc_html__( 'More plugins', 'ungrabber' ) ?></span>
        </a>
		<?php

	}

    /**
     * Render social links
     * @return void
     */
    private function social_link() {

        ?>
        <div class="mdc-social">

            <a class="mdc-list-item" href="https://www.facebook.com/merkuloveteam" target="_blank" title="Facebook">
                <svg width="16" aria-hidden="true" focusable="false" data-icon="facebook" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M504 256C504 119 393 8 256 8S8 119 8 256c0 123.78 90.69 226.38 209.25 245V327.69h-63V256h63v-54.64c0-62.15 37-96.48 93.67-96.48 27.14 0 55.52 4.84 55.52 4.84v61h-31.28c-30.8 0-40.41 19.12-40.41 38.73V256h68.78l-11 71.69h-57.78V501C413.31 482.38 504 379.78 504 256z"></path></svg>
            </a>

            <a class="mdc-list-item" href="https://twitter.com/merkuloveteam" target="_blank" title="Twitter">
                <svg width="16" aria-hidden="true" focusable="false" data-icon="twitter" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"></path></svg>
            </a>
            <a class="mdc-list-item" href="https://www.instagram.com/merkulove.team" target="_blank" title="Instagram">
                <svg width="16" aria-hidden="true" focusable="false" data-icon="instagram" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"></path></svg>
            </a>

            <a class="mdc-list-item" href="https://dribbble.com/merkuloveteam" target="_blank" title="Dribbble">
                <svg width="16" aria-hidden="true" focusable="false" data-icon="dribbble" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 8C119.252 8 8 119.252 8 256s111.252 248 248 248 248-111.252 248-248S392.748 8 256 8zm163.97 114.366c29.503 36.046 47.369 81.957 47.835 131.955-6.984-1.477-77.018-15.682-147.502-6.818-5.752-14.041-11.181-26.393-18.617-41.614 78.321-31.977 113.818-77.482 118.284-83.523zM396.421 97.87c-3.81 5.427-35.697 48.286-111.021 76.519-34.712-63.776-73.185-116.168-79.04-124.008 67.176-16.193 137.966 1.27 190.061 47.489zm-230.48-33.25c5.585 7.659 43.438 60.116 78.537 122.509-99.087 26.313-186.36 25.934-195.834 25.809C62.38 147.205 106.678 92.573 165.941 64.62zM44.17 256.323c0-2.166.043-4.322.108-6.473 9.268.19 111.92 1.513 217.706-30.146 6.064 11.868 11.857 23.915 17.174 35.949-76.599 21.575-146.194 83.527-180.531 142.306C64.794 360.405 44.17 310.73 44.17 256.323zm81.807 167.113c22.127-45.233 82.178-103.622 167.579-132.756 29.74 77.283 42.039 142.053 45.189 160.638-68.112 29.013-150.015 21.053-212.768-27.882zm248.38 8.489c-2.171-12.886-13.446-74.897-41.152-151.033 66.38-10.626 124.7 6.768 131.947 9.055-9.442 58.941-43.273 109.844-90.795 141.978z"></path></svg>
            </a>

        </div>
        <?php

    }

	/**
	 * Add plugin settings page.
	 *
     * @since 1.0.0
	 * @access public
     *
     * @return void
	 **/
	public function add_settings_page() {

		add_action( 'admin_menu', [ $this, 'add_admin_menu' ], 1000 );
		add_action( 'admin_init', [ $this, 'settings_init' ] );

	}

	/**
	 * Generate Settings Page.
	 *
     * @since 1.0.0
	 * @access public
     *
     * @return void
	 **/
	public function settings_init() {

        /** Add settings foreach tab. */
        foreach ( Plugin::get_tabs() as $tab_slug => $tab ) {

            /** Skip tabs without handlers. */
            if ( empty( $tab['class'] ) ) { continue; }

            /** Call add_settings from appropriate class for each tab. */
            call_user_func( [ $tab['class'], 'get_instance' ] )->add_settings( $tab_slug );

        }

	}

	/**
	 * Add admin menu for plugin settings.
	 *
     * @since 1.0.0
	 * @access public
     *
     * @return void
	 **/
	public function add_admin_menu() {

        $options = Settings::get_instance()->options;

	    /** Submenu for Elementor plugins. */
        if ( 'elementor' === Plugin::get_type() ) {

            $this->add_submenu_elementor( $options );

        /** Submenu for WPBakery plugins. */
        } else if ( 'wpbakery' === Plugin::get_type() ) {

            $this->add_submenu_wpbakery( $options );

        /** Root level menu for WordPress plugins. */
        } else {

            $this->add_menu_wordpress( $options );

        }

	}

    /**
     * Add admin menu for Elementor plugins.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
	private function add_submenu_elementor( $options ) {

        /** Check if Elementor installed and activated. */
        $parent = 'options-general.php';
        if ( did_action( 'elementor/loaded' ) ) {
            $parent = 'elementor';
            //$parent = 'edit-comments.php';

        }

        add_submenu_page(
            $parent,
	        'Ungrabber ' . esc_html__( 'Settings', 'ungrabber' ),
	        'Ungrabber ' . esc_html__( 'for ', 'ungrabber' ),
	        $options[ 'capability_settings' ] ?? 'manage_options',
            'mdp_ungrabber_settings',
            [ $this, 'options_page' ]
        );

    }

    /**
     * Add admin menu for WPBakery plugins.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function add_submenu_wpbakery( $options ) {

        /** Check if Elementor installed and activated. */
        $parent = 'options-general.php';

        add_submenu_page(
            $parent,
	        'Ungrabber ' . esc_html__( 'Settings', 'ungrabber' ),
	        'Ungrabber ' . esc_html__( 'for ', 'ungrabber' ),
	        $options[ 'capability_settings' ] ?? 'manage_options',
            'mdp_ungrabber_settings',
            [ $this, 'options_page' ]
        );

    }

    /**
     * Add admin menu for WordPress plugins.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
	private function add_menu_wordpress( $options ) {

        add_menu_page(
            'UnGrabber ' . esc_html__( 'Settings', 'ungrabber' ),
            'UnGrabber',
	        $options[ 'capability_settings' ] ?? 'manage_options',
            'mdp_ungrabber_settings',
            [ $this, 'options_page' ],
            $this->get_admin_menu_icon(),
            $this->get_admin_menu_position()
        );

    }

    /**
     * Return path to admin menu icon or base64 encoded image.
     *
     * @return string
     **/
	private function get_admin_menu_icon(): string {

	    return 'data:image/svg+xml;base64,' . base64_encode( file_get_contents( Plugin::get_path() . 'images/logo-menu.svg' ) );

    }

    /**
     * Calculate admin menu position based on plugin slug value.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string
     **/
	private function get_admin_menu_position(): string {

        $hash = md5( Plugin::get_slug() );

        $int = (int) filter_var( $hash, FILTER_SANITIZE_NUMBER_INT );
        $int =  (int) ( $int / 1000000000000 );

        return '58.' . $int;

    }

	/**
	 * Plugin Settings Page.
	 *
     * @return void
	 */
	public function options_page() {

		$options = Settings::get_instance()->options;

		/** User rights check. */
		if ( ! current_user_can( $options[ 'capability_settings' ] ?? 'manage_options' ) ) { return; } ?>

        <!--suppress HtmlUnknownTarget -->
        <form action='options.php' method='post'>
            <div class="wrap">
				<?php

				/** Current tab slug */
				$current_tab = $this->get_current_tab();

				/** Render "Settings saved!" message. */
				$this->render_nags();

				/** Render Tabs Headers. */
				?><section class="mdp-aside"><?php $this->render_tabs( $current_tab ); ?></section><?php

				/** Render Tabs Body. */
				?><section class="mdp-tab-content mdp-tab-name-<?php echo esc_attr( $current_tab ) ?>"><?php

                /** Call settings from appropriate class for current tab. */
                foreach ( Plugin::get_tabs() as $tab_slug => $tab ) {

                    /** Work only on current tab. */
                    if ( ! $this->is_tab( $tab_slug, $current_tab ) ) { continue; }

                    /** Skip tabs without handlers. */
                    if ( empty( $tab['class'] ) ) { continue; }

                    call_user_func( [ $tab['class'], 'get_instance' ] )->do_settings( $tab_slug );

                }

                ?>
                </section>
            </div>
        </form>

		<?php
	}

    /**
     * Return current selected tab or first tab.
     *
     * @access private
     * @return string
     */
	private function get_current_tab(): string {

        $tab = key( Plugin::get_tabs() ); // First tab is default tab

        if ( isset ( $_GET['tab'] ) ) {

            $tab = sanitize_key( $_GET['tab'] );

        }

		return apply_filters( 'ungrabber_current_tab', $tab );

	}

	/**
	 * Check if passed tab is current tab and tab is enabled.
	 *
	 * @param string $tab - Current tab slug.
	 * @param string $current_tab - Current tab slug.
	 *
	 * @return bool
	 */
	private function is_tab( string $tab, string $current_tab ): bool {

        return ( $tab === $current_tab ) && Tab::is_tab_enabled( $current_tab );

    }

	/**
	 * Render nags on after settings save.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
	 **/
	private function render_nags() {

        /** Exit if this is not settings update. */
		if ( ! isset( $_GET['settings-updated'] ) ) { return; }

        /** Render "Settings Saved" message. */
        $this->render_nag_saved();

        /** Render Activation message. */
        $this->render_nag_activation();

	}

    /**
     * Render "Settings Saved" message.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function render_nag_saved() {

        /** Exit if settings saving was not successful. */
        if ( 'true' !== $_GET['settings-updated'] ) { return; }

        /** Render "Settings Saved" message. */
        UI::get_instance()->render_snackbar( esc_html__( 'Settings saved!', 'ungrabber' ) );

    }

    /**
     * Render Activation message.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function render_nag_activation() {

        /** Exit if haven't tab slug. */
        if ( ! isset( $_GET['tab'] ) ) { return; }

        /** Exit if this not activation tab. */
        if ( 'activation' !== $_GET['tab'] ) { return; }

        /** Render Activation message. */
        if ( TabActivation::get_instance()->is_activated() ) {

            /** Render "Activation success" message. */
            UI::get_instance()->render_snackbar( esc_html__( 'Plugin activated successfully.', 'ungrabber' ), 'success', 5500 );
            return;

        }

        /** Render "Activation failed" message. */
        UI::get_instance()->render_snackbar( esc_html__( 'Invalid purchase code.', 'ungrabber' ), 'error', 5500 );

    }

	/**
	 * Render logo and Save changes button in plugin settings.
	 *
     * @since 1.0.0
	 * @access private
     *
	 * @return void
	 **/
	private function render_logo() {

		?>
        <div class="mdc-drawer__header mdc-plugin-fixed">
            <a class="mdc-list-item mdp-plugin-title" href="#">
                <i class="mdc-list-item__graphic" aria-hidden="true">
                    <img src="<?php echo esc_attr( Plugin::get_url() . 'images/logo-color.svg' ); ?>" alt="<?php echo esc_attr( 'Ungrabber' ) ?>">
                </i>
                <span class="mdc-list-item__text">
                    <?php if ( 'wordpress' === Plugin::get_type() ) : ?>
                        <?php echo esc_html( 'UnGrabber' ); ?>
                    <?php else: ?>
                        <?php echo esc_html( 'Ungrabber' ); ?>
                    <?php endif; ?>
                </span>
                <span class="mdc-list-item__text">
                    <sup>
                        <?php esc_html_e( 'v.', 'ungrabber' ); ?>
                        <?php echo esc_attr( Plugin::get_version() ); ?>
                    </sup>
                </span>
            </a>
            <button type="submit" name="submit" id="submit" class="mdc-button mdc-button--dense mdc-button--raised">
                <span class="mdc-button__label"><?php esc_html_e( 'Save changes', 'ungrabber' ) ?></span>
            </button>
        </div>
		<?php

	}

    /**
     * Return settings array with default values.
     *
     * @param bool $tabs - setting tabs
     *
     * @return array
     */
	public function get_default_settings( bool $tabs = false ): array {

        /** Get all plugin tabs with settings fields  */
        if ( ! $tabs ) { $tabs = Plugin::get_tabs(); }

        $default = [];

        /** Collect settings from each tab. */
        foreach ( $tabs as $tab ) {

            /** If current tab haven't fields. */
            if ( empty( $tab['fields'] ) ) { continue; }

            /** Collect default values from each field. */
            foreach ( $tab['fields'] as $field_slug => $field ) {

                $default[$field_slug] = $field[ 'default' ] ?? '';

            }

        }

        return $default;

    }

	/**
	 * Get plugin settings with default values.
	 *
     * @since 1.0.0
	 * @access public
     *
	 * @return void
	 **/
	public function get_options() {

        /** Default values. */
        $defaults = $this->get_default_settings();

        $results = [];

        /** Get all plugin tabs with settings fields. */
        $tabs = Plugin::get_tabs();

        /** Collect settings from each tab. */
        foreach ( $tabs as $tab_slug => $tab ) {

	        $opt_name = "mdp_ungrabber_{$tab_slug}_settings";
            $options = get_option( $opt_name );
            $results = wp_parse_args( $options, $defaults );
            $defaults = $results;

        }

		$this->options = $results;

	}

    /**
     * Get CSS string from sides control
     *
     * @param array $options
     * @param $slug
     *
     * @return string
     */
    public static function get_sides_css( $slug, array $options = array() ): string {

        if ( empty( $options ) ) {
            $options = Settings::get_instance()->options;
        }

        $css = '';
        foreach ( [ 'top', 'right', 'bottom', 'left' ] as $side ) {

	        $value = $options[ $slug . '_' .$side ] ?? $options[ $slug ][ $side ] ?? 0;
            $unit = $options[ $slug . '_unit' ] ?? 'px';
            $css .= $value . $unit . ' ';

        }
        return trim( $css );

    }

	/**
	 * Main Settings Instance.
     *
	 * @return Settings
	 */
	public static function get_instance(): Settings {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}
