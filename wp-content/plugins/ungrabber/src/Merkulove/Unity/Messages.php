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

final class Messages {

	/**
	 * The one true Messages.
	 *
     * @access private
	 * @var Messages
	 **/
	private static $instance;

	/**
	 * @return void
	 */
	private function __construct() {

		/** Show activation warning */
		add_action( 'admin_footer', [ $this, 'plugin_settings_messages' ] );

	}

	/**
     * Plugin settings page messages
	 * @return void
	 */
    public function plugin_settings_messages() {

	    /** Get current screen. */
	    $screen = get_current_screen();
	    if ( null === $screen ) { return; }

	    if ( ! in_array( $screen->base ,Plugin::get_menu_bases() ) ) { return; }

        /** Activation cURL error message */
	    $this->message_activation_error();

    }

	/**
     * Render message with activation cURL error
	 * @return void
	 */
    private function message_activation_error() {

	    // Exit if not activation tab
	    if ( ! isset( $_REQUEST[ 'tab' ] ) || $_REQUEST[ 'tab' ] !== 'activation' ) { return; }

	    $errors = get_transient( 'mdp_ungrabber_activation_error' );
	    if ( ! $errors || ! is_array( $errors ) ) { return; }

	    foreach ( $errors as $type => $message ) {

		    UI::get_instance()->render_snackbar(
			    esc_html__( 'ERROR', 'ungrabber' ) . ' [' . strtoupper( esc_html( $type ) ) . '] ' . esc_html( $message[ 0 ] ?? '' ),
			    'error',
			    -1,
			    true,
			    array(
				    array(
					    'caption' => esc_html__( 'Troubleshooting', 'ungrabber' ),
					    'link'    => esc_url( 'https://merkulove.zendesk.com/hc/en-us/articles/360006100998-Troubleshooting-of-the-plugin-activation' )
				    )
			    )
		    );

	    }

    }

	/**
	 * Main Messages Instance.
	 * Insures that only one instance of Messages exists in memory at any one time.
	 *
	 * @static
     * @access public
     *
	 * @return Messages
	 **/
	public static function get_instance(): Messages {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}
