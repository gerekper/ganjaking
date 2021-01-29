<?php
namespace WeDevs\PM_Pro\Core\Shortcodes;

use WeDevs\PM\Core\WP\Enqueue_Scripts;
use WeDevs\PM\Core\WP\Register_Scripts;
use WeDevs\PM_Pro\Core\WP\Enqueue_Scripts as Pro_Enqueue_Scripts;
use WeDevs\PM_Pro\Core\WP\Register_Scripts as Pro_Register_Scripts;
/**
 */
class PM_Shortcode {

	/**
	 * Get the shortcode content.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function get( $atts ) {
		return Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}

	/**
	 * Output the shortcode.
	 *
	 * @param array $atts
	 */
	public static function output( $atts = array() ) {
        if ( ! is_user_logged_in() ) {
            wp_login_form( array( 'echo' => true ) );

            return;
        }
		echo pm_root_element();
		self::scripts();

	}

	public static function scripts() {
        wp_enqueue_script(
            'pm-hooks',
            pm_config('frontend.assets_url') . 'vendor/wp-hooks/pm-hooks.js',
            '',
            false,
            false
        );

        //pro scripts
        Pro_Register_Scripts::scripts();
        Pro_Register_Scripts::styles();

        // free scripts
        Register_Scripts::scripts();
        Register_Scripts::styles();
        do_action( "pm_load_shortcode_script" );

        wp_enqueue_style( 'pm-frontend-style' );
        wp_enqueue_script('pm-frontend-scripts');

        //pro scripts
        Pro_Enqueue_Scripts::scripts();
        Pro_Enqueue_Scripts::styles();


        // free scripts
        Enqueue_Scripts::scripts();
        Enqueue_Scripts::styles();
	}

}
