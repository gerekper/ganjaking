<?php
/**
 * Definition of the `fue_subscribe` shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'FUE_Shortcode_Subscribe' ) ):
class FUE_Shortcode_Subscribe {

	/**
	 * Constructor. Register the `fue_subscribe` shortcode
	 */
	public function __construct() {
		add_shortcode( 'fue_subscribe', 'FUE_Shortcode_Subscribe::render' );
	}

	/**
	 * Render the subscription form.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function render( $atts ) {
		$default = array(
			'label_email'       => __( 'Email:', 'follow_up_emails' ),
			'placeholder_email' => 'email@example.org',
			'label_first_name'  => __( 'First name:', 'follow_up_emails' ),
			'label_last_name'   => __( 'Last name:', 'follow_up_emails' ),
			'submit_text'       => __( 'Subscribe', 'follow_up_emails' ),
			'success_message'   => __( 'Thank you. You are now subscribed to our list.', 'follow_up_emails' ),
			'list'              => '',
		);
		$atts = shortcode_atts( $default, $atts, 'fue_subscribe' );

		ob_start();
		fue_get_template( 'subscribe.php', $atts );
		return ob_get_clean();
	}

}
endif;

return new FUE_Shortcode_Subscribe();
