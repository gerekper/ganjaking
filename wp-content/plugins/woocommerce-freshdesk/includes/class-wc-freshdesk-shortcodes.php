<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Freshdesk Shortcodes.
 *
 * @package  WC_Freshdesk_Shortcodes
 * @category Shortcodes
 * @author   WooThemes
 */
class WC_Freshdesk_Shortcodes {

	/**
	 * Initialize the shortcodes actions.
	 */
	public function __construct() {
		// Actions.
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );

		// Shortcodes.
		add_shortcode( 'wc_freshdesk_form', array( $this, 'form' ) );
	}

	/**
	 * Shortcode scripts.
	 *
	 * @return void
	 */
	public function scripts() {
		wp_register_script( 'wc-freshdesk-widget', 'https://s3.amazonaws.com/assets.freshdesk.com/widget/freshwidget.js', array(), WC_FRESHDESK_VERSION, true );
		wp_register_style( 'wc-freshdesk-widget', 'https://s3.amazonaws.com/assets.freshdesk.com/widget/freshwidget.css', array(), WC_FRESHDESK_VERSION, 'screen, projection' );
	}

	/**
	 * Form shortcode.
	 *
	 * @param  array $atts
	 *
	 * @return string
	 */
	public function form( $atts ) {
		wp_enqueue_script( 'wc-freshdesk-widget' );
		wp_enqueue_style( 'wc-freshdesk-widget' );

		$options = shortcode_atts( array(
			'height'         => '500px',
			'title'          => '',
			'submit_message' => '',
			'attach_file'    => 'no',
			'search_area'    => 'no'
		), $atts );

		$title       = ( '' != $options['title'] ) ? '&formTitle=' . urlencode( $options['title'] ) : '';
		$message     = ( '' != $options['submit_message'] ) ? '&submitThanks=' . urlencode( $options['submit_message'] ) : '';
		$attach_file = ( 'no' == $options['attach_file'] ) ? '&attachFile=no' : '';
		$search_area = ( 'no' == $options['search_area'] ) ? '&searchArea=no' : '';
		$settings    = get_option( 'woocommerce_freshdesk_settings', array() );

		if ( isset( $settings['url'] ) ) {
			return '<iframe class="freshwidget-embedded-form" id="freshwidget-embedded-form" src="https://' . sanitize_title( $settings['url'] ) . '.freshdesk.com/widgets/feedback_widget/new?&widgetType=embedded&screenshot=no' . $title . $message . $attach_file . $search_area . '" scrolling="no" height="' . esc_attr( $options['height'] ) . '" width="100%" frameborder="0">
			</iframe>';
		}

		return '<strong>' . esc_html__( 'You must set the Freshdesk URL in the integration settings!', 'woocommerce-freshdesk' ) . '</strong>';
	}
}

new WC_Freshdesk_Shortcodes();
