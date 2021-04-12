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

    }

	/**
	 * Render Tabs Headers.
	 *
     * @since 1.0.0
	 * @access private
     *
     * @return void
	 **/
	private function render_tabs() {

	    /** Selected tab key. */
        $current = $this->get_current_tab();

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
                        $classes[] = "mdp-menu-tab-{$tab}";

						/** Mark Active Tab. */
						if ( $tab === $current ) {
							$classes[] = 'mdc-list-item--activated';
						}

						/** Prepare link. */
						$link = '?page=mdp_ungrabber_settings&tab=' . $tab;

						?>
                        <a class="<?php esc_attr_e( implode( ' ', $classes ) ); ?>" href="<?php esc_attr_e( $link ); ?>">
                            <i class='material-icons mdc-list-item__graphic' aria-hidden='true'><?php esc_html_e( $value['icon'] ); ?></i>
                            <span class='mdc-list-item__text'><?php esc_html_e( $value['label'] ); ?></span>
                        </a>
						<?php

					}

					/** Helpful links. */
					$this->support_link();

					/** Activation Status. */
					TabActivation::get_instance()->display_status();

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

	    /** Submenu for Elementor plugins. */
        if ( 'elementor' === Plugin::get_type() ) {

            $this->add_submenu_elementor();

        /** Root level menu for WordPress plugins. */
        } else {

            $this->add_menu_wordpress();

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
	private function add_submenu_elementor() {

        /** Check if Elementor installed and activated. */
        $parent = 'options-general.php';
        if ( did_action( 'elementor/loaded' ) ) {
            $parent = 'elementor';
            //$parent = 'edit-comments.php';

        }

        add_submenu_page(
            $parent,
            esc_html__( 'Ungrabber Settings', 'ungrabber' ),
            esc_html__( 'Ungrabber for ', 'ungrabber' ),
            'manage_options',
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
	private function add_menu_wordpress() {

        add_menu_page(
            esc_html__( 'Ungrabber Settings', 'ungrabber' ),
            esc_html__( 'UnGrabber', 'ungrabber' ),
            'manage_options',
            'mdp_ungrabber_settings',
            [ $this, 'options_page' ],
            $this->get_admin_menu_icon(),
            $this->get_admin_menu_position()
        );

    }

    /**
     * Return path to admin menu icon or base64 encoded image.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string
     **/
	private function get_admin_menu_icon() {

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
	private function get_admin_menu_position() {

        $hash = md5( Plugin::get_slug() );

        $int = (int) filter_var( $hash, FILTER_SANITIZE_NUMBER_INT );
        $int =  (int) ( $int / 1000000000000 );

        return '58.' . $int;

    }

	/**
	 * Plugin Settings Page.
	 *
     * @since 1.0.0
	 * @access public
     *
     * @return void
	 **/
	public function options_page() {

		/** User rights check. */
		if ( ! current_user_can( 'manage_options' ) ) { return; } ?>

        <!--suppress HtmlUnknownTarget -->
        <form action='options.php' method='post'>
            <div class="wrap">
				<?php

				/** Render "Settings saved!" message. */
				$this->render_nags();

				/** Render Tabs Headers. */
				?><section class="mdp-aside"><?php $this->render_tabs(); ?></section><?php

				/** Render Tabs Body. */
				?><section class="mdp-tab-content mdp-tab-name-<?php esc_attr_e( $this->get_current_tab() ) ?>"><?php

                /** Call settings from appropriate class for current tab. */
                foreach ( Plugin::get_tabs() as $tab_slug => $tab ) {

                    /** Work only on current tab. */
                    if ( ! $this->is_tab( $tab_slug ) ) { continue; }

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
     * @since  1.0.0
     * @access private
     *
     * @return string
     **/
	private function get_current_tab() {

        $tab = key( Plugin::get_tabs() ); // First tab is default tab

        if ( isset ( $_GET['tab'] ) ) {

            $tab = $_GET['tab'];

        }

        return $tab;
    }

    /**
     * Check if passed tab is current tab and tab is enabled.
     *
     * @param string $tab - Tab slug to check.
     *
     * @since  1.0.0
     * @access private
     *
     * @return bool
     **/
	private function is_tab( $tab ) {

        $current_tab = $this->get_current_tab();

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
                    <img src="<?php echo esc_attr( Plugin::get_url() . 'images/logo-color.svg' ); ?>" alt="<?php esc_html_e( 'Ungrabber', 'ungrabber' ) ?>">
                </i>
                <span class="mdc-list-item__text">
                    <?php if ( 'wordpress' === Plugin::get_type() ) : ?>
                        <?php esc_html_e( 'UnGrabber', 'ungrabber' ) ?>
                    <?php else: ?>
                        <?php esc_html_e( 'Ungrabber', 'ungrabber' ) ?>
                    <?php endif; ?>
                </span>
                <span class="mdc-list-item__text">
                    <sup>
                        <?php esc_html_e( 'v.', 'ungrabber' ); ?>
                        <?php esc_attr_e( Plugin::get_version() ); ?>
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
     * @since 1.0.0
     * @access public
     *
     * @return array
     **/
	private function get_default_settings() {

        /** Get all plugin tabs with settings fields. */
        $tabs = Plugin::get_tabs();

        $default = [];

        /** Collect settings from each tab. */
        foreach ( $tabs as $tab_slug => $tab ) {

            /** If current tab haven't fields. */
            if ( empty( $tab['fields'] ) ) { continue; }

            /** Collect default values from each field. */
            foreach ( $tab['fields'] as $field_slug => $field ) {

                $default[$field_slug] = $field['default'];

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
	 * Main Settings Instance.
	 * Insures that only one instance of Settings exists in memory at any one time.
	 *
	 * @static
     * @since 1.0.0
     * @access public
     *
	 * @return Settings
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}
