<?php
namespace WeDevs\PM_Pro\Modules\Invoice\includes\shortcodes;

// use WeDevs\PM_Pro\Modules\Invoice\includes\Shortcodes;
// use WeDevs\PM\Core\WP\Enqueue_Scripts;
// use WeDevs\PM_Pro\Core\WP\Enqueue_Scripts as Pro_Enqueue_Scripts;
// use WeDevs\PM\Core\WP\Enqueue_Scripts as PM_Scripts;

/**
 */
class Invoice {

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
		if ( !is_user_logged_in() ) {
            wp_login_form( array( 'echo' => true ) );

            return;
        }

		$project_id = empty( $atts['project_id'] ) ? false : absint( $atts['project_id'] );

		echo '<div id="wedevs-pm-pro-invoice"></div>';

		pm_pro_invoice_front_end_scripts( $project_id );
	}

}
