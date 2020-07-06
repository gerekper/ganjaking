<?php
/**
 * Useful functions for the plugin
 *
 * @package WC_Newsletter_Subscription/Functions
 * @since   2.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the suffix for the script filenames.
 *
 * @since 2.5.0
 *
 * @return string The scripts suffix.
 */
function wc_newsletter_subscription_get_scripts_suffix() {
	return ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
}

/**
 * Gets the service provider used to subscribe users to the newsletter.
 *
 * @since 2.5.0
 *
 * @global WC_Subscribe_To_Newsletter $WC_Subscribe_To_Newsletter Plugin global instance.
 *
 * @return mixed
 */
function wc_newsletter_subscription_get_provider() {
	global $WC_Subscribe_To_Newsletter;

	return $WC_Subscribe_To_Newsletter->provider();
}

/**
 * Subscribes a user to the newsletter.
 *
 * @since 2.5.0
 *
 * @param string $email The email address to subscribe.
 * @param array  $args  Optional. Additional arguments.
 * @return bool
 */
function wc_newsletter_subscription_subscribe( $email, $args = array() ) {
	$provider = wc_newsletter_subscription_get_provider();

	// No service provider defined.
	if ( ! $provider ) {
		return false;
	}

	$args = wp_parse_args(
		$args,
		array(
			'first_name' => '',
			'last_name'  => '',
			'list_id'    => 'false',
		)
	);

	$provider->subscribe( $args['first_name'], $args['last_name'], $email, $args['list_id'] );

	return true;
}

/**
 * Processes the subscription widget.
 *
 * @since 2.5.0
 */
function wc_newsletter_subscription_process_widget() {
	check_ajax_referer( 'wc_subscribe_to_newsletter_widget' );

	$list_id = ( ! empty( $_POST['list_id'] ) ? wc_clean( wp_unslash( $_POST['list_id'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification

	if ( ! $list_id ) {
		wp_send_json_error( array( 'message' => _x( 'An unexpected error happened.', 'widget error', 'woocommerce-subscribe-to-newsletter' ) ) );
	}

	$email = ( ! empty( $_POST['newsletter_email'] ) ? wc_clean( wp_unslash( $_POST['newsletter_email'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification

	if ( ! $email || ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => _x( 'Please, enter a valid email address.', 'widget error', 'woocommerce-subscribe-to-newsletter' ) ) );
	}

	// Honeypot field.
	if ( ! empty( $_POST['newsletter_phone'] ) ) {
		wp_send_json_error( array( 'message' => _x( 'Are you a robot?', 'widget error', 'woocommerce-subscribe-to-newsletter' ) ) );
	}

	// Check too many attempts.
	$transient_key = 'wc_newsletter_subscription_widget_attempts_' . md5( WC_Geolocation::get_ip_address() );
	$attempts      = get_transient( $transient_key );

	if ( ! is_array( $attempts ) ) {
		$attempts = array(
			'count'     => 0,
			'expire_at' => time() + 300, // Expires in 5 minutes.
		);
	}

	if ( 3 <= $attempts['count'] ) {
		wp_send_json_error( array( 'message' => _x( 'Too many attempts to subscribe. Try it again later.', 'widget error', 'woocommerce-subscribe-to-newsletter' ) ) );
	}

	$name_fields = array();

	foreach ( array( 'name', 'first_name', 'last_name' ) as $key ) {
		$field_key = "newsletter_{$key}";

		$name_fields[ $key ] = ( ! empty( $_POST[ $field_key ] ) ? wc_clean( wp_unslash( $_POST[ $field_key ] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification
	}

	// Split the name into two parts.
	if ( ! empty( $name_fields['name'] ) ) {
		$name_parts = explode( ' ', trim( $name_fields['name'] ) );

		$name_fields['first_name'] = current( $name_parts );
		$name_fields['last_name']  = '';

		if ( 1 < count( $name_parts ) ) {
			$name_fields['last_name'] = join( ' ', array_slice( $name_parts, 1 ) );
		}
	}

	wc_newsletter_subscription_subscribe(
		$email,
		array(
			'first_name' => $name_fields['first_name'],
			'last_name'  => $name_fields['last_name'],
			'list_id'    => $list_id,
		)
	);

	// Set attempts transient.
	$attempts['count'] += 1;
	$expiration         = ( $attempts['expire_at'] - time() );

	if ( 0 < $expiration ) {
		set_transient( $transient_key, $attempts, $expiration );
	}

	wp_send_json_success( array( 'message' => _x( 'Thanks for subscribing.', 'widget success', 'woocommerce-subscribe-to-newsletter' ) ) );
}
add_action( 'wp_ajax_subscribe_to_newsletter', 'wc_newsletter_subscription_process_widget' );
add_action( 'wp_ajax_nopriv_subscribe_to_newsletter', 'wc_newsletter_subscription_process_widget' );

/**
 * Gets the specified admin url.
 *
 * @since 2.6.0
 *
 * @param array $extra_params Optional. Additional parameters in pairs key => value.
 * @return string The admin page url.
 */
function wc_newsletter_subscription_get_settings_url( $extra_params = array() ) {
	$url = 'admin.php?page=wc-settings&tab=newsletter';

	if ( ! empty( $extra_params ) ) {
		foreach ( $extra_params as $param => $value ) {
			$url .= '&' . esc_attr( $param ) . '=' . rawurlencode( $value );
		}
	}

	return admin_url( $url );
}

/**
 * Get if the newsletter provider is connected or not.
 *
 * @since 2.8.0
 *
 * @return bool
 */
function wc_newsletter_subscription_is_connected() {
	$provider = wc_newsletter_subscription_get_provider();

	return ( $provider && ( $provider instanceof WC_Mailpoet_Integration || $provider->has_api_key() ) );
}

/**
 * Gets if the newsletter provider has a list set.
 *
 * @since 2.9.0
 *
 * @return bool
 */
function wc_newsletter_subscription_provider_has_list() {
	$provider = wc_newsletter_subscription_get_provider();

	return ( $provider && $provider->has_list() );
}

/**
 * Disconnect the current provider.
 *
 * @since 2.8.0
 *
 * @return bool
 */
function wc_newsletter_subscription_disconnect_provider() {
	$provider = wc_newsletter_subscription_get_provider();

	if ( $provider ) {
		delete_option( 'woocommerce_newsletter_service' );

		switch ( get_class( $provider ) ) {
			case 'WC_Mailchimp_Newsletter_Integration':
				delete_option( 'woocommerce_mailchimp_api_key' );
				break;
			case 'WC_CM_Integration':
				delete_option( 'woocommerce_cmonitor_api_key' );
				break;
		}

		return true;
	}

	return false;
}

/**
 * What type of request is this?
 *
 * @since 2.9.0
 *
 * @param string $type admin, ajax, cron, rest_api or frontend.
 * @return bool
 */
function wc_newsletter_subscription_is_request( $type ) {
	$is_request = false;

	switch ( $type ) {
		case 'admin':
			$is_request = is_admin();
			break;
		case 'ajax':
			$is_request = defined( 'DOING_AJAX' );
			break;
		case 'cron':
			$is_request = defined( 'DOING_CRON' );
			break;
		case 'frontend':
			$is_request = ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! wc_newsletter_subscription_is_request( 'rest_api' );
			break;
		case 'rest_api':
			if ( ! empty( $_SERVER['REQUEST_URI'] ) ) {
				$rest_prefix = trailingslashit( rest_get_url_prefix() );
				$is_request  = ( false !== strpos( $_SERVER['REQUEST_URI'], $rest_prefix ) ); // phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			}
			break;
	}

	/**
	 * Filters if the request is of the specified type.
	 *
	 * @since 2.9.0
	 *
	 * @param bool   $is_request Whether the request is of the specified type.
	 * @param string $type       The request type.
	 */
	return apply_filters( 'wc_newsletter_subscription_is_request', $is_request, $type );
}

/**
 * Gets templates passing attributes and including the file.
 *
 * @since 2.9.0
 *
 * @param string $template_name The template name.
 * @param array  $args          Optional. The template arguments.
 */
function wc_newsletter_subscription_get_template( $template_name, $args = array() ) {
	wc_get_template( $template_name, $args, '', WC_NEWSLETTER_SUBSCRIPTION_PATH . 'templates/' );
}

/**
 * Gets if the specified plugin is active.
 *
 * @since 2.9.0
 *
 * @param string $plugin Base plugin path from plugins directory.
 * @return bool
 */
function wc_newsletter_subscription_is_plugin_active( $plugin ) {
	$active_plugins = (array) get_option( 'active_plugins', array() );

	if ( is_multisite() ) {
		$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	}

	return ( in_array( $plugin, $active_plugins, true ) || array_key_exists( $plugin, $active_plugins ) );
}
