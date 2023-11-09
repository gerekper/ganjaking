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

final class Dashboard {

	/**
	 * The one true Dashboard.
	 *
     * @access private
	 * @var Dashboard
	 **/
	private static $instance;

	/**
	 * @return void
	 */
	private function __construct() {

        /** Run only if plugin ID is available */
        if ( ! EnvatoItem::get_instance()->get_id() ) { return; }

		/** Add status widget to dashboard. */
		add_action( 'wp_dashboard_setup', [ $this, 'unity_dashboard_setup' ] );

		/** Load JS and CSS for Backend Area. */
		$this->enqueue_backend();

	}

	/**
	 * Load JS and CSS for Backend Area.
	 *
	 * @access public
	 **/
	public function enqueue_backend() {

		/** Add admin styles. */
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_styles' ] );

	}

	/**
	 * Add CSS for admin area.
	 *
	 * @return void
	 **/
	public function admin_styles() {

		/** Get current screen. */
		$screen = get_current_screen();
		if ( null === $screen ) { return; }

		/** Add styles only on dashboard page. */
		if ( 'dashboard' === $screen->base ) {

			wp_enqueue_style(
				'mdp-dashboard',
				Plugin::get_url() . 'src/Merkulove/Unity/assets/css/dashboard' . Plugin::get_suffix() . '.css',
                [],
				Plugin::get_version()
			);

			wp_enqueue_script(
				'mdp-dashboard',
				Plugin::get_url() . 'src/Merkulove/Unity/assets/js/dashboard' . Plugin::get_suffix() . '.js',
                [ 'jquery' ],
				[],
				Plugin::get_version(),
                true
			);

			wp_localize_script(
				'mdp-dashboard',
				'mdpDashboard',
				[
					'ajaxURL'   => admin_url( 'admin-ajax.php' ),
					'restBase'  => get_rest_url(),
					'nonce'     => wp_create_nonce( 'mdp-dashboard' ),
                    'translation' => array(
                        'licenseActive'     => esc_html__( 'Licensed', 'ungrabber' ),
                        'licenseInactive'   => esc_html__( 'No license', 'ungrabber' ),
                        'licenseUnknown'    => esc_html__( 'No licence data', 'ungrabber' ),
                        'wait'              => esc_html__( 'Wait', 'ungrabber' ),
                    )
				]
			);

		}

	}

	/**
	 * Fires after core widgets for the admin dashboard have been registered.
	 *
	 * @access public
	 *
	 * @return void
	 **/
	public function unity_dashboard_setup() {

		wp_add_dashboard_widget(
			'mdp_unity_dashboard_widget',
			esc_html__( 'Plugins Manager', 'ungrabber' ),
			[ $this, 'unity_dashboard_widget' ]
		);

	}

	/**
	 * Show widget.
	 *
	 * @access public
	 *
	 * @return void
	 **/
	public function unity_dashboard_widget() {

		?><div class="mdp-dashboard"><?php

            echo $this->header_markup();

            echo $this->plugins_markup();

			echo $this->footer_markup();

		?></div><?php

	}

	/**
	 * Return header markup
	 * @return string
	 */
	private function header_markup( ): string {

		return wp_sprintf(
			'<div class="mdp-dashboard__header">
                <a href="https://1.envato.market/cc-merkulove" title="%1$s" target="_blank">
                    %1$s
                    <span class="screen-reader-text">(%2$s)</span>
					<span aria-hidden="true" class="dashicons dashicons-external"></span>
                </a>
            </div>',
            esc_html__( 'Explore more plugins', 'ungrabber' ),
			esc_html__( 'opens in a new window', 'ungrabber' )
		);

	}

	/**
     * Return plugins markup
	 * @return string
	 */
	private function plugins_markup(): string {

        $plugins = get_plugins();

        $markup = '';
        foreach ( $plugins as $slug => $plugin ) {

	        if ( $plugin[ 'Author' ] !== 'Merkulove' ) { continue; }

	        $is_activated = is_plugin_active( $slug );

            $markup .= wp_sprintf('
                <div class="mdp-dashboard__plugin%1$s" data-plugin="%2$s">
                   %3$s
                   %4$s
                </div>
                ',
	            ! $is_activated ? ' inactive-plugin' : '',
	            explode( '/', $slug )[ 0 ],
                $this->plugin_profile_markup( $slug, $plugin ),
                $this->plugin_buttons_markup( $slug, $is_activated )
            );

        }

        return wp_sprintf( '<div class="mdp-dashboard__plugins-list">%s</div>', $markup );

	}

	/**
     * Plugin main info markup for single plugin
	 * @param $slug
	 * @param $plugin
	 *
	 * @return string
	 */
    private function plugin_profile_markup( $slug, $plugin ): string {

        return wp_sprintf(
            '<div class="mdp-dashboard__title">
                %1$s
                %2$s
            </div>',
            $this->plugin_logo_markup( $slug, $plugin ),
            $this->plugin_title_markup( $slug, $plugin )
        );

    }

	/**
	 * Plugin logo markup for single plugin
	 *
	 * @param $slug
	 * @param $plugin
	 *
	 * @return string
	 */
    private function plugin_logo_markup( $slug, $plugin ): string {

	    $plugin_dir = explode( '/', $slug )[ 0 ];

        return wp_sprintf(
            '<a class="mdp-dashboard__logo" href="%1$s" title="%2$s" target="_blank">
                    <img src="%3$s" alt="%2$s">
                </a>',
	        esc_url( $plugin[ 'PluginURI' ] ) ?? '',
	        esc_html( $plugin[ 'Name' ] ) ?? '',
            esc_url( plugins_url(). '/' . $plugin_dir . '/images/logo-color.svg' ) ?? ''
        );

    }

	/**
     * Plugin title
	 * @param $slug
	 * @param $plugin
	 *
	 * @return string
	 */
    private function plugin_title_markup( $slug, $plugin ): string {

        return wp_sprintf(
            '<div class="mdp-dashboard__header__title">
                <h4>
                    <a href="%1$s" target="_blank" title="%2$s">%2$s</a>
                    <span class="screen-reader-text">(%3$s)</span>
					<span aria-hidden="true" class="dashicons dashicons-external"></span>
                </h4>
                %4$s
            </div>',
	        esc_url( $plugin[ 'PluginURI' ] ) ?? '',
	        esc_html( $plugin[ 'Name' ] ) ?? '',
	        esc_html__( 'opens in a new window', 'ungrabber' ),
            $this->plugin_status_markup(  $slug, $plugin )
        );

    }

	/**
	 * Plugin status markup for single plugin
	 *
	 * @param $slug
	 * @param $plugin
	 *
	 * @return string
	 */
    private function plugin_status_markup( $slug, $plugin ): string {

	    $plugin_slug = explode( '/', $slug )[ 0 ];
        $plugin_slug = str_replace( '-', '_', $plugin_slug );

        $license_status = wp_sprintf(
            '<span class="mdp-dashboard__status__license">
                <a href="%1$s" title="%2$s">
                    <i class="dashicons"></i>
                </a>
            </span>',
	        esc_url( admin_url( 'admin.php?page=mdp_' . $plugin_slug . '_settings&tab=activation'  ) ),
	        esc_html__( 'Plugin licence status', 'ungrabber' )
        );

        $update_status = wp_sprintf(
            '<span class="mdp-dashboard__status__update" data-version="%4$s">
                <a class="mdp-dashboard__status__update-needed" style="display: none" href="%1$s" title="%2$s %4$s">
                    %2$s <span class="mdp-dashboard__status__version">%4$s</span>
                </a>
                <span class="mdp-dashboard__status__update-no-needed"  style="display: none">
                    %3$s (<span class="mdp-dashboard__status__version">%4$s</span>)
                </span>
            </span>',
	        esc_url( admin_url( 'update-core.php' ) ),
	        esc_html__( 'Update to', 'ungrabber' ),
	        esc_html__( 'Latest', 'ungrabber' ),
	        esc_attr( $plugin[ 'Version' ] ) ?? ''
        );

        return wp_sprintf(
            '<span class="mdp-dashboard__header__status">
                %1$s
                %2$s
            </span>',
	        $update_status,
            $license_status
        );

    }

	/**
	 * Buttons markup for single plugin
	 *
	 * @param $plugin
	 * @param $is_activated
	 *
	 * @return string
	 */
    private function plugin_buttons_markup( $plugin, $is_activated ): string {

        $plugin_slug = explode( '/', $plugin )[ 0 ];

        $settings_url = admin_url( 'admin.php?page=mdp_' . $plugin_slug . '_settings'  );
        $activation_url = wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . $plugin ), 'activate-plugin_' . $plugin );

        $activation_button = wp_sprintf(
            '<a href="%1$s" class="button button-primary button-activate" title="%2$s" rel="noopener">%2$s</a>',
            esc_url( $activation_url ),
            esc_html__( 'Activate', 'ungrabber' )
        );

        return wp_sprintf(
            '<div class="mdp-dashboard__buttons">
                %1$s
                <a href="%2$s" class="button" title="%3$s" rel="noopener"%4$s>%3$s</a>
            </div>',
	        ! $is_activated ? $activation_button : '',
	        $settings_url,
	        esc_html__( 'Settings', 'ungrabber' ),
            ! $is_activated ? ' style="display:none;"' : ''
        );

    }

	/**
	 * Get footer markup
	 *
	 * @return string
	 **/
	private function footer_markup(): string {

		return wp_sprintf(
			'<div class="mdp-dashboard__footer">
				<a href="https://www.instagram.com/merkulove.team/" target="_blank" title="Instagram" rel="noopener">
					Instagram
					<span class="screen-reader-text">(%1$s)</span>
					<span aria-hidden="true" class="dashicons dashicons-external"></span>
				</a>
				<a href="https://www.facebook.com/merkuloveteam/" target="_blank" title="Facebook" rel="noopener">
					Facebook
					<span class="screen-reader-text">(%1$s)</span>
					<span aria-hidden="true" class="dashicons dashicons-external"></span>
				</a>
				<a href="https://twitter.com/merkuloveteam" target="_blank" title="Twitter" rel="noopener">
					Twitter
					<span class="screen-reader-text">(%1$s)</span>
					<span aria-hidden="true" class="dashicons dashicons-external"></span>
				</a>
				<a href="https://www.youtube.com/channel/UC0ljU_QnBte_bwPfbQnMAnA" target="_blank" title="YouTube" rel="noopener">
					YouTube
					<span class="screen-reader-text">(%1$s)</span>
					<span aria-hidden="true" class="dashicons dashicons-external"></span>
				</a>
			</div>',
			esc_html__( 'opens in a new window', 'ungrabber' )
		);

	}

	/**
	 * Main Dashboard Instance.
	 * Insures that only one instance of Dashboard exists in memory at any one time.
	 *
	 * @static
     * @access public
     *
	 * @return Dashboard
	 **/
	public static function get_instance(): Dashboard {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}
