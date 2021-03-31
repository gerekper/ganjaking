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

namespace Merkulove\Ungrabber;

use Merkulove\Ungrabber\Unity\Plugin;
use Merkulove\Ungrabber\Unity\Settings;
use Merkulove\Ungrabber\Unity\TabAssignments;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Caster class contain main plugin logic.
 * @since 1.0.0
 *
 **/
final class Caster
{

	/**
	 * The one true Caster.
	 *
     * @since 1.0.0
     * @access private
	 * @var Caster
	 **/
	private static $instance;

    /**
     * Setup the plugin.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public function setup() {

        /** Define hooks that runs on both the front-end as well as the dashboard. */
        $this->both_hooks();

        /** Define public hooks. */
        $this->public_hooks();

        /** Define admin hooks. */
        $this->admin_hooks();

    }

    /**
     * Add JavaScript for the public-facing side of the site.
     * @return void
     **/
    public function enqueue_scripts() {

        /** Arbitrary JavaScript is not allowed in AMP. */
        if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) { return; }

        /** Checks if plugin should work on this page. */
        if ( ! TabAssignments::get_instance()->display() ) { return; }

        /** Get Plugin Settings. */
        $options = Settings::get_instance()->options;

        /** Check user role and admin setting */
        if ( 'on' === $options[ 'admin' ] && current_user_can( 'manage_options' ) ) { return; } // Exit if admin

        wp_enqueue_script( 'mdp-ungrabber-hotkeys', Plugin::get_url() . 'js/hotkeys' . Plugin::get_suffix() . '.js', [], Plugin::get_version(), true );
        wp_enqueue_script( 'mdp-ungrabber', Plugin::get_url() . 'js/ungrabber' . Plugin::get_suffix() . '.js', [ 'mdp-ungrabber-hotkeys' ], Plugin::get_version(), true );

        /** Add devtools-detect */
        if ( $options[ 'developer_tool' ] === 'on' )
        {
            wp_enqueue_script( 'mdp-ungrabber-devtools', Plugin::get_url() . 'js/devtools-detect' . Plugin::get_suffix() . '.js', [], Plugin::get_version(), true );
        }

        wp_localize_script( 'mdp-ungrabber', 'mdpUnGrabber',
            [
                'selectAll'     => $options['select_all'],
                'copy'          => $options['copy'],
                'cut'           => $options['cut'],
                'paste'         => $options['paste'],
                'save'          => $options['save'],
                'viewSource'    => $options['view_source'],
                'printPage'     => $options['print_page'],
                'developerTool' => $options['developer_tool'],
                'windowBlur'    => $options['window_blur'],
                'tabHidden'     => $options['tab_hidden'],
                'readerMode'    => $options['reader_mode'],
                'rightClick'    => $options['right_click'],
                'rightClickImage'    => $options['right_click_image'],
                'textSelection' => $options['text_selection'],
                'imageDragging' => $options['image_dragging']
            ]
        );

    }

    /**
     * Protect Content if JavaScript is Disabled.
     *
     * @since 1.0.0
     * @access public
     **/
    public function javascript_required()
    {

        /** Arbitrary JavaScript is not allowed in AMP. */
        if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) { return; }

        /** Get plugin settings */
        $options = Settings::get_instance()->options;

        if ( 'on' !== $options['javascript'] ) { return; }

        ob_start();
        ?>
        <noscript>
            <div id='mdp-ungrabber-js-disabled'>
                <div><?php echo wp_kses_post( $options['javascript_msg'] ); ?></div>
            </div>
            <style>
                #mdp-ungrabber-js-disabled {
                    position: fixed;
                    top: 0;
                    left: 0;
                    height: 100%;
                    width: 100%;
                    z-index: 999999;
                    text-align: center;
                    background-color: #FFFFFF;
                    color: #000000;
                    font-size: 40px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
            </style>
        </noscript>
        <?php
        $result = ob_get_clean();

        echo $result;

    }

    /**
     * Define hooks that runs on both the front-end as well as the dashboard.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function both_hooks() {

        /** Init shortcodes */
        Shortcodes::get_instance();

    }

    /**
     * Register all of the hooks related to the public-facing functionality.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function public_hooks() {

        /** Work only on frontend area. */
        if ( is_admin() ) { return; }

        /** Load JavaScript for Frontend Area. */
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

        /** JavaScript Required. */
        add_action( 'wp_footer', [ $this, 'javascript_required' ] );

    }

    /**
     * Register all of the hooks related to the admin area functionality.
     *
     * @since 1.0.0
     * @access private
     *
     * @return void
     **/
    private function admin_hooks() {

        /** Work only in admin area. */
        if ( ! is_admin() ) { return; }

    }

	/**
	 * This method used in register_activation_hook
	 * Everything written here will be done during plugin activation.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function activation_hook() {

		/** Activation hook */

	}

	/**
	 * Main Caster Instance.
	 * Insures that only one instance of Caster exists in memory at any one time.
	 *
	 * @static
     * @since 1.0.0
     * @access public
     *
	 * @return Caster
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}
