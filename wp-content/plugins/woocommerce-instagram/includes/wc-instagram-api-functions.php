<?php
/**
 * API Functions
 *
 * @package WC_Instagram/Functions
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Makes a request to the Instagram Graph API.
 *
 * @since 2.1.0
 *
 * @param string $endpoint Optional. The request endpoint.
 * @param array  $args     Optional. The request arguments.
 * @param string $method   Optional. The request method.
 * @param string $version  Optional. The API version.
 * @return mixed The request response. WP_Error on failure.
 */
function wc_instagram_api_request( $endpoint = '', $args = array(), $method = 'get', $version = 'v12.0' ) {
	// The Instagram Graph API uses the Facebook Graph API.
	$url = 'https://graph.facebook.com/' . wp_unslash( $version ) . '/' . wp_unslash( $endpoint );

	// Clean arguments with a falsy value.
	$args = array_filter( $args );

	$request_method = 'wp_remote_' . strtolower( $method );

	// Invalid HTTP method.
	if ( ! function_exists( $request_method ) ) {
		return wc_instagram_log_api_error( sprintf( 'Invalid HTTP method: %s.', esc_attr( $method ) ) );
	}

	// Backward compatibility with WP 4.5 and lower.
	if ( 'get' === $method ) {
		$url  = add_query_arg( $args, $url );
		$args = null;
	}

	$response = call_user_func(
		$request_method,
		$url,
		array(
			'timeout' => 30,
			'body'    => $args,
		)
	);

	// Request error.
	if ( is_wp_error( $response ) ) {
		return wc_instagram_log_api_error( $response );
	}

	// Invalid API request.
	if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return wc_instagram_log_api_error( 'Invalid API request:', $response );
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );

	// API error response.
	if ( is_array( $data ) && ! empty( $data['error'] ) ) {
		return wc_instagram_log_api_error( 'The API returned an error:', $data['error'], 'API Error Response' );
	}

	return $data;
}

/**
 * Gets the user pages.
 *
 * @since 2.0.0
 *
 * @return array
 */
function wc_instagram_get_user_pages() {
	$accounts = wc_instagram()->api()->user()->accounts();

	return ( is_wp_error( $accounts ) ? array() : $accounts['data'] );
}

/**
 * Gets the user pages choices to use them in a select field.
 *
 * @since 2.0.0
 *
 * @return array
 */
function wc_instagram_get_user_pages_choices() {
	$accounts = wc_instagram_get_user_pages();
	$choices  = wp_list_pluck( $accounts, 'name', 'id' );

	// Don't use array_merge to avoid reindexing.
	return array( '' => _x( 'Choose a page', 'Facebook page setting placeholder', 'woocommerce-instagram' ) ) + $choices;
}

/**
 * Gets the Instagram Business Account associated to the specified Facebook Page ID.
 *
 * @since 2.0.0
 *
 * @param int $page_id The Facebook Page ID.
 * @return false|array An array with the account info. False otherwise.
 */
function wc_instagram_get_business_account_from_page( $page_id ) {
	$data = wc_instagram()->api()->page()->get( $page_id, array( 'instagram_business_account' ) );

	return ( ! is_wp_error( $data ) && ! empty( $data['instagram_business_account'] ) ? $data['instagram_business_account'] : false );
}

/**
 * Searches an Instagram hashtag by name.
 *
 * @since 2.0.0
 *
 * @param string $hashtag The hashtag name.
 * @return int The hashtag ID. False otherwise.
 */
function wc_instagram_search_hashtag( $hashtag ) {
	$hashtags = get_option( 'wc_instagram_hashtags', array() );

	// Fetch from cache.
	if ( ! empty( $hashtags[ $hashtag ] ) ) {
		$hashtag_id = intval( $hashtags[ $hashtag ] );
	} else {
		$hashtag_id = wc_instagram()->api()->hashtag()->search( $hashtag );

		// The request failed or the hashtag was not found.
		if ( ! is_int( $hashtag_id ) ) {
			return false;
		}

		$hashtags[ $hashtag ] = $hashtag_id;

		// Cache the hashtag.
		update_option( 'wc_instagram_hashtags', $hashtags );
	}

	return $hashtag_id;
}

/**
 * Gets the media objects tagged with the specified hashtag.
 *
 * @since 2.0.0
 * @since 2.2.0 Added `exclude` parameter to the arguments.
 *
 * @param mixed $hashtag The hashtag name or ID.
 * @param array $args    Optional. Additional arguments.
 * @return array|false An array with the images. False on failure.
 */
function wc_instagram_get_hashtag_media( $hashtag, $args = array() ) {
	$hashtag_id = ( is_int( $hashtag ) ? $hashtag : wc_instagram_search_hashtag( $hashtag ) );

	if ( ! $hashtag_id ) {
		return false;
	}

	$defaults = array(
		'hashtag_id' => $hashtag_id, // The hashtag ID.
		'edge'       => 'recent',    // The hashtag media edge. Allowed values: 'recent', 'top'.
		'type'       => '',          // Filter media objects by type. Accept an array with multiple media types.
		'count'      => 8,           // The number of media objects to retrieve.
		'exclude'    => array(),     // Exclude the specified media Ids from the list.
		'fields'     => array(       // The media fields to retrieve.
			'media_type',
			'caption',
			'permalink',
			'media_url',
			'like_count',
		),
	);

	$args = wp_parse_args( $args, $defaults );

	// Convert the 'type' argument to array.
	$args['type'] = ( is_array( $args['type'] ) ? $args['type'] : array( $args['type'] ) );

	// Remove empty values and convert to uppercase the media types.
	$args['type'] = array_map( 'strtoupper', array_filter( $args['type'] ) );

	// Sanitize value.
	$args['count'] = absint( $args['count'] );

	// Sanitize media Ids.
	$args['exclude'] = array_map( 'intval', $args['exclude'] );

	/**
	 * Filters the arguments used to fetch the media objects.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args The arguments.
	 */
	$args = apply_filters( 'wc_instagram_get_hashtag_media_args', $args );

	// Generate an unique identifier for this query.
	$args_hash = wc_instagram_get_hash( $args );

	$hashtag_transient = "wc_instagram_hashtag_media_{$args_hash}";

	// Get cached result.
	$media = get_transient( $hashtag_transient );

	// Fetch media if the transient has expired.
	if ( false === $media ) {
		$media          = array();
		$filter_by_type = ! empty( $args['type'] );
		$after          = ''; // After cursor. Allow pagination.
		$end_loop       = false;
		$node           = wc_instagram()->api()->hashtag();

		do {
			// Trigger the request.
			$response = call_user_func(
				array( $node, "{$args['edge']}_media" ),
				$hashtag_id,
				array(
					'fields' => $args['fields'],
					'after'  => $after,
				)
			);

			if ( is_wp_error( $response ) || empty( $response['data'] ) ) {
				$end_loop = true;
				continue;
			}

			foreach ( $response['data'] as $media_object ) {
				$valid = true;

				// Not valid media type.
				if ( $filter_by_type && ! in_array( $media_object['media_type'], $args['type'], true ) ) {
					$valid = false;
				}

				// Exclude media object.
				if ( in_array( (int) $media_object['id'], $args['exclude'], true ) ) {
					$valid = false;
				}

				/**
				 * Filters if the media object is valid or not.
				 *
				 * @since 2.0.0
				 *
				 * @param bool  $valid        True if the media object is valid. False otherwise.
				 * @param array $media_object The media object.
				 * @param array $args         The arguments used for the query.
				 */
				$valid = apply_filters( 'wc_instagram_is_valid_hashtag_media', $valid, $media_object, $args );

				if ( $valid ) {
					$media[] = $media_object;

					// We've enough media objects.
					if ( count( $media ) === $args['count'] ) {
						$end_loop = true;
						break;
					}
				}
			}

			// Set the cursor.
			$after = ( isset( $response['paging']['cursors']['after'] ) ? $response['paging']['cursors']['after'] : '' );

			// There is no more pages.
			if ( ! $after ) {
				$end_loop = true;
			}
		} while ( ! $end_loop );

		// Cache the result.
		set_transient( $hashtag_transient, $media, wc_instagram_get_transient_expiration_time() );
	}

	/**
	 * Filters the media objects tagged with the specified hashtag.
	 *
	 * @since 2.0.0
	 *
	 * @param array $media      An array with the media objects.
	 * @param int   $hashtag_id The hashtag ID.
	 * @param array $args       The arguments used for the query.
	 */
	return apply_filters( 'wc_instagram_get_hashtag_media', $media, $hashtag_id, $args );
}

/**
 * Clears the transients used for caching the hashtag media requests.
 *
 * @since 2.2.0
 *
 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
 */
function wc_instagram_clear_hashtag_media_transients() {
	global $wpdb;

	// Delete transients.
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_wc_instagram_hashtag_media_%';" );
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_wc_instagram_hashtag_media_%';" );
}

/**
 * Logs an API error.
 *
 * Logs the error and return a `WP_Error` object.
 *
 * @since 2.0.0
 *
 * @param mixed  $error  The error to log. It can be a string or a WP_Error object.
 * @param array  $params Optional. Error arguments. Only if the first parameter is a string.
 * @param string $tag    Optional. Error tag.
 * @return WP_Error
 */
function wc_instagram_log_api_error( $error, $params = array(), $tag = 'API Error' ) {
	if ( ! is_wp_error( $error ) ) {
		$error = new WP_Error( 'wc_instagram_api', $error, $params );
	}

	$error_code = $error->get_error_code();
	$error_data = $error->get_error_data();

	$error_log = sprintf(
		'[%1$s]%2$s %3$s %4$s',
		$tag,
		( 'wc_instagram_api' !== $error_code ? " {$error_code}:" : '' ),
		$error->get_error_message(),
		( is_array( $error_data ) ? wc_print_r( $error_data, true ) : '' )
	);

	wc_instagram_log( $error_log, 'error', 'wc_instagram_api' );

	return $error;
}
