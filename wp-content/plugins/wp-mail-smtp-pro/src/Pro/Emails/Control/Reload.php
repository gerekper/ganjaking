<?php

namespace WPMailSMTP\Pro\Emails\Control;

use WPMailSMTP\Options;

/**
 * Class Reload contains methods that have a copied from WP-core functionality.
 *
 * @since 1.5.0
 */
class Reload {

	/**
	 * Email login credentials to a newly-registered user.
	 *
	 * A new user registration notification is also sent to admin email.
	 *
	 * @since 1.5.0 Method content copied from \wp_new_user_notification() in WP 5.1.1.
	 * @since 3.1.0 Method content copied from \wp_new_user_notification() in WP 5.8.1.
	 * @since 3.9.0 Method content copied from \wp_new_user_notification() in WP 6.2.2.
	 *
	 * @param int    $user_id    User ID.
	 * @param null   $deprecated Not used (argument deprecated).
	 * @param string $notify     Optional. Type of notification that should happen. Accepts 'admin' or an empty
	 *                           string (admin only), 'user', or 'both' (admin and user). Default empty.
	 */
	public static function wp_new_user_notification( $user_id, $deprecated = null, $notify = '' ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		// Accepts only 'user', 'admin', 'both' or default '' as $notify.
		if ( ! in_array( $notify, array( 'user', 'admin', 'both', '' ), true ) ) {
			return;
		}

		$user = get_userdata( $user_id );

		// The blogname option is escaped with esc_html() on the way into the database in sanitize_option().
		// We want to reverse this for the plain text arena of emails.
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

		/**
		 * Filters whether the admin is notified of a new user registration.
		 *
		 * @param bool    $send Whether to send the email. Default true.
		 * @param WP_User $user User object for new user.
		 */
		$send_notification_to_admin = apply_filters( 'wp_send_new_user_notification_to_admin', true, $user );

		if ( 'user' !== $notify && true === $send_notification_to_admin ) {
			$switched_locale = switch_to_locale( get_locale() );

			/* translators: %s: Site title. */
			$message = sprintf( __( 'New user registration on your site %s:' ), $blogname ) . "\r\n\r\n";
			/* translators: %s: User login. */
			$message .= sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
			/* translators: %s: User email address. */
			$message .= sprintf( __( 'Email: %s' ), $user->user_email ) . "\r\n";

			$wp_new_user_notification_email_admin = array(
				'to'      => get_option( 'admin_email' ),
				/* translators: New user registration notification email subject. %s: Site title. */
				'subject' => __( '[%s] New User Registration' ),
				'message' => $message,
				'headers' => '',
			);

			/**
			 * Filters the contents of the new user notification email sent to the site admin.
			 *
			 * @param array    $wp_new_user_notification_email_admin {
			 *     Used to build wp_mail().
			 *
			 *     @type string $to      The intended recipient - site admin email address.
			 *     @type string $subject The subject of the email.
			 *     @type string $message The body of the email.
			 *     @type string $headers The headers of the email.
			 * }
			 *
			 * @param \WP_User $user     User object for new user.
			 * @param string   $blogname The site title.
			 */
			$wp_new_user_notification_email_admin = apply_filters( 'wp_new_user_notification_email_admin', $wp_new_user_notification_email_admin, $user, $blogname );

			wp_mail(
				$wp_new_user_notification_email_admin['to'],
				wp_specialchars_decode( sprintf( $wp_new_user_notification_email_admin['subject'], $blogname ) ),
				$wp_new_user_notification_email_admin['message'],
				$wp_new_user_notification_email_admin['headers']
			);

			if ( $switched_locale ) {
				restore_previous_locale();
			}
		}

		/**
		 * Filters whether the user is notified of their new user registration.
		 *
		 * @param bool    $send Whether to send the email. Default true.
		 * @param WP_User $user User object for new user.
		 */
		$send_notification_to_user = apply_filters( 'wp_send_new_user_notification_to_user', true, $user );

		// `$deprecated` was pre-4.3 `$plaintext_pass`. An empty `$plaintext_pass` didn't sent a user notification.
		if ( 'admin' === $notify || true !== $send_notification_to_user || ( empty( $deprecated ) && empty( $notify ) ) ) {
			return;
		}

		$key = get_password_reset_key( $user );
		if ( is_wp_error( $key ) ) {
			return;
		}

		$switched_locale = switch_to_locale( get_user_locale( $user ) );

		/* translators: %s: User login. */
		$message  = sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
		$message .= __( 'To set your password, visit the following address:' ) . "\r\n\r\n";
		$message .= network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user->user_login ), 'login' ) . "\r\n\r\n";

		$message .= wp_login_url() . "\r\n";

		$wp_new_user_notification_email = array(
			'to'      => $user->user_email,
			/* translators: Login details notification email subject. %s: Site title. */
			'subject' => __( '[%s] Login Details' ),
			'message' => $message,
			'headers' => '',
		);

		/**
		 * Filters the contents of the new user notification email sent to the new user.
		 *
		 * @param array   $wp_new_user_notification_email {
		 *     Used to build wp_mail().
		 *
		 *     @type string $to      The intended recipient - New user email address.
		 *     @type string $subject The subject of the email.
		 *     @type string $message The body of the email.
		 *     @type string $headers The headers of the email.
		 * }
		 *
		 * @param \WP_User $user     User object for new user.
		 * @param string  $blogname The site title.
		 */
		$wp_new_user_notification_email = apply_filters( 'wp_new_user_notification_email', $wp_new_user_notification_email, $user, $blogname );

		wp_mail(
			$wp_new_user_notification_email['to'],
			wp_specialchars_decode( sprintf( $wp_new_user_notification_email['subject'], $blogname ) ),
			$wp_new_user_notification_email['message'],
			$wp_new_user_notification_email['headers']
		);

		if ( $switched_locale ) {
			restore_previous_locale();
		}
	}

	/**
	 * Redefine the $send_as_email param, to not actually send an email.
	 *
	 * @since 1.5.0 Method overloaded copied from \wp_privacy_process_personal_data_export_page() in WP 5.1.1.
	 *
	 * @param array  $response       The response from the personal data exporter for the given page.
	 * @param int    $exporter_index The index of the personal data exporter. Begins at 1.
	 * @param string $email_address  The email address of the user whose personal data this is.
	 * @param int    $page           The page of personal data for this exporter. Begins at 1.
	 * @param int    $request_id     The request ID for this personal data export.
	 * @param bool   $send_as_email  Whether the final results of the export should be emailed to the user.
	 * @param string $exporter_key   The slug (key) of the exporter.
	 *
	 * @return array The filtered response.
	 */
	public static function wp_privacy_process_personal_data_export_page( $response, $exporter_index, $email_address, $page, $request_id, $send_as_email, $exporter_key ) {

		$is_email_disabled = Options::init()->get( 'control', 'dis_personal_data_sent_export_link' );

		if ( $is_email_disabled && $send_as_email ) {
			$send_as_email = false;
		}

		return \wp_privacy_process_personal_data_export_page( $response, $exporter_index, $email_address, $page, $request_id, $send_as_email, $exporter_key );
	}
}
