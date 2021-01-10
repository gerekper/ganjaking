<?php

use WPMailSMTP\Options;
use WPMailSMTP\Pro\Emails\Control\Reload;

if ( ! function_exists( 'wp_new_user_notification' ) ) {
	/**
	 * Add additional logic to the way \wp_new_user_notification() function works.
	 * Allow to switch notifications dynamically for each recipient.
	 *
	 * @since 1.5.0
	 *
	 * @param int    $user_id    User ID.
	 * @param null   $deprecated Not used (argument deprecated).
	 * @param string $notify     Optional. Type of notification that should happen. Accepts 'admin' or an empty
	 *                           string (admin only), 'user', or 'both' (admin and user). Default empty.
	 */
	function wp_new_user_notification( $user_id, $deprecated = null, $notify = '' ) {

		// Accepts only 'user', 'admin' , 'both' or default '' as $notify.
		if ( ! in_array( $notify, array( 'user', 'admin', 'both', '' ), true ) ) {
			return;
		}

		$options = new Options();

		$is_admin_disabled = $options->get( 'control', 'dis_new_user_created_to_admin' );
		$is_user_disabled  = $options->get( 'control', 'dis_new_user_created_to_user' );

		switch ( $notify ) {
			case 'user':
				if ( ! $is_user_disabled ) {
					Reload::wp_new_user_notification( $user_id, $deprecated, 'user' );
				}
				break;
			case 'both':
				if ( ! $is_admin_disabled ) {
					Reload::wp_new_user_notification( $user_id, $deprecated, 'admin' );
				}
				if ( ! $is_user_disabled ) {
					Reload::wp_new_user_notification( $user_id, $deprecated, 'user' );
				}
				break;
			case 'admin':
			case '':
				if ( ! $is_admin_disabled ) {
					Reload::wp_new_user_notification( $user_id, $deprecated, 'admin' );
				}
				break;

			default:
				// Do not interfere with the unknown behavior.
				Reload::wp_new_user_notification( $user_id, $deprecated, $notify );
		}
	}
}
