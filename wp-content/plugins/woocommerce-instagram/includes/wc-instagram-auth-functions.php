<?php
/**
 * Authentication functions
 *
 * @package WC_Instagram/Functions
 * @since   2.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the Instagram authentication URL.
 *
 * @since 2.0.0
 */
function wc_instagram_get_auth_url() {
	return add_query_arg(
		array(
			'nonce'    => wp_create_nonce( 'wc_instagram_auth' ),
			'redirect' => rawurlencode( wc_instagram_get_settings_url() ),
		),
		'https://connect.themesquad.com/facebook/login/'
	);
}

/**
 * Gets if the user is connected or not.
 *
 * @since 2.0.0
 *
 * @return bool
 */
function wc_instagram_is_connected() {
	$access_token = wc_instagram_get_setting( 'access_token' );

	return ! empty( $access_token );
}

/**
 * Gets if the user has an Instagram Business account or not.
 *
 * @since 2.0.0
 *
 * @return bool
 */
function wc_instagram_has_business_account() {
	$has_business_account = false;

	if ( wc_instagram_is_connected() ) {
		$business_account = wc_instagram_get_setting( 'instagram_business_account' );

		$has_business_account = ! empty( $business_account );
	}

	return $has_business_account;
}

/**
 * Adds the Instagram access credentials.
 *
 * @since 2.0.0
 * @since 2.1.0 First parameter is an array of arguments now.
 *
 * @param array $args The connection arguments.
 * @return bool
 */
function wc_instagram_connect( $args ) {
	$api = wc_instagram()->api();

	$args = wp_parse_args(
		$args,
		array(
			'access_token' => '',
			'expires_at'   => strtotime( '+60 days' ), // The access token is valid during 60 days by default.
		)
	);

	// No access token.
	if ( ! $args['access_token'] ) {
		return false;
	}

	// Set the access token on the fly.
	$api->set_access_token( $args['access_token'] );

	// Fetch the user info including the user_id parameter.
	$user = $api->user()->me( array( 'id', 'name' ) );

	if ( is_wp_error( $user ) ) {
		return false;
	}

	$settings = array_merge(
		wc_instagram_get_settings(),
		array(
			'access_token'               => $args['access_token'],
			'expires_at'                 => $args['expires_at'],
			'user_id'                    => ( ! empty( $user['id'] ) ? $user['id'] : '' ),
			'user_name'                  => ( ! empty( $user['name'] ) ? $user['name'] : '' ),
			'page_id'                    => '',
			'instagram_business_account' => array(),
		)
	);

	update_option( 'wc_instagram_settings', $settings );
	delete_option( 'wc_instagram_renew_access_notice' );

	wc_instagram_schedule_access_renewal();

	return true;
}

/**
 * Removes the Instagram access credentials.
 *
 * @since 2.0.0
 * @since 2.1.0 Only remove the credentials and keep the other settings.
 *
 * @return bool
 */
function wc_instagram_disconnect() {
	$settings = wc_instagram_get_settings();

	$access_params = array(
		'access_token',
		'expires_at',
		'user_id',
		'user_name',
		'page_id',
		'instagram_business_account',
	);

	// Remove credentials.
	$settings = array_diff_key( $settings, array_flip( $access_params ) );

	// Remove the scheduled access renewal event.
	wc_instagram_unschedule_access_renewal();

	// Remove the renewal access notice.
	delete_option( 'wc_instagram_renew_access_notice' );

	return update_option( 'wc_instagram_settings', $settings );
}

/**
 * Renews the Instagram access credentials.
 *
 * @since 2.1.0
 *
 * @return bool True if the access was renewed. False otherwise.
 */
function wc_instagram_renew_access() {
	if ( ! wc_instagram_is_connected() ) {
		return false;
	}

	$response = wp_remote_post(
		'https://connect.themesquad.com/facebook/renew-access/',
		array(
			'timeout' => 30,
			'body'    => array(
				'site_url'     => site_url(),
				'access_token' => wc_instagram_get_setting( 'access_token' ),
			),
		)
	);

	$renewed = false;

	if ( is_wp_error( $response ) ) {
		wc_instagram_log_api_error( $response );
	} else {
		$code = wp_remote_retrieve_response_code( $response );
		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 === $code && is_array( $data ) && ! empty( $data['access_token'] ) ) {
			$settings = wc_instagram_get_settings();

			$settings['access_token'] = sanitize_text_field( $data['access_token'] );
			$settings['expires_at']   = ( ! empty( $data['expires_at'] ) ? intval( $data['expires_at'] ) : false );

			update_option( 'wc_instagram_settings', $settings );
			delete_option( 'wc_instagram_renew_access_notice' );

			$renewed = true;
		} else {
			$error_data = ( 200 !== $code && ! empty( $response['response'] ) ? $response['response'] : $data );

			wc_instagram_log_api_error( 'The renewal of the Instagram access credentials failed.', $error_data );

			/**
			 * Fires when the renewal of the Instagram access credentials failed.
			 *
			 * @since 2.2.0
			 *
			 * @param array $error_data The error data.
			 */
			do_action( 'wc_instagram_renew_access_error', $error_data );

			// Re-authentication required.
			if ( ! empty( $error_data['error_code'] ) && 'invalid_token' === $error_data['error_code'] ) {
				update_option( 'wc_instagram_renew_access_notice', 'yes' );
			}
		}
	}

	wc_instagram_schedule_access_renewal();

	return $renewed;
}

/**
 * Schedules the event for renewing the Instagram access credentials.
 *
 * @since 2.1.0
 */
function wc_instagram_schedule_access_renewal() {
	$expires_at = (int) wc_instagram_get_setting( 'expires_at' );

	if ( ! $expires_at ) {
		return;
	}

	$timestamp = false;

	if ( 'yes' !== get_option( 'wc_instagram_renew_access_notice', 'no' ) ) {
		$current_time = time();

		// Not expired.
		if ( $expires_at > $current_time ) {
			// By default, 10 days before it expires.
			$timestamp = strtotime( '-10 days', $expires_at );

			// We're in the latest 10 days before the access expires.
			if ( $current_time > $timestamp ) {
				$timestamp = strtotime( 'tomorrow' );
			}

			/*
			 * It remains less than a week for the access token expires.
			 * That means we tried to renew it at least three times without success, and it's time to notify the merchant.
			 */
			if ( $expires_at - $timestamp <= WEEK_IN_SECONDS ) {
				$timestamp = false;
			}
		}
	}

	/**
	 * Filters the timestamp for when to renew the access.
	 *
	 * @since 2.1.0
	 *
	 * @param int $timestamp  Unix timestamp (UTC) for when to renew the access.
	 * @param int $expires_at Unix timestamp (UTC) for when the current access expires.
	 */
	$timestamp = apply_filters( 'wc_instagram_schedule_access_renewal', $timestamp, $expires_at );

	if ( $timestamp ) {
		wp_clear_scheduled_hook( 'wc_instagram_renew_access' );
		wp_schedule_single_event( $timestamp, 'wc_instagram_renew_access' );
	} else {
		wc_instagram_unschedule_access_renewal();

		// Add the notice to renew the access manually.
		update_option( 'wc_instagram_renew_access_notice', 'yes' );
	}
}

/**
 * Un-schedules the event for renewing the Instagram access credentials.
 *
 * @since 2.1.0
 */
function wc_instagram_unschedule_access_renewal() {
	$timestamp = wp_next_scheduled( 'wc_instagram_renew_access' );

	if ( $timestamp ) {
		wp_unschedule_event( $timestamp, 'wc_instagram_renew_access' );
	}
}
