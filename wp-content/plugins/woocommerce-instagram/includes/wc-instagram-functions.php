<?php
/**
 * Useful functions for the plugin
 *
 * @package WC_Instagram/Functions
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

// Include core functions.
require 'wc-instagram-compatibility-functions.php';
require 'wc-instagram-formatting-functions.php';
require 'wc-instagram-api-functions.php';
require 'wc-instagram-auth-functions.php';
require 'wc-instagram-product-functions.php';
require 'wc-instagram-product-category-functions.php';
require 'wc-instagram-product-catalog-functions.php';
require 'wc-instagram-deprecated-functions.php';

/**
 * Gets a string (hash) that uniquely identifies the specified data.
 *
 * @since 2.0.0
 *
 * @param mixed $data The data used to generate the hash.
 * @return string
 */
function wc_instagram_get_hash( $data ) {
	if ( is_array( $data ) || is_object( $data ) ) {
		$data = wp_json_encode( $data );
	}

	return md5( $data );
}

/**
 * Gets the suffix for the script filenames.
 *
 * @since 2.0.0
 *
 * @return string The scripts suffix.
 */
function wc_instagram_get_scripts_suffix() {
	return ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
}

/**
 * What type of request is this?
 *
 * @since 3.3.0
 *
 * @param string $type admin, ajax, cron, rest_api or frontend.
 * @return bool
 */
function wc_instagram_is_request( $type ) {
	$is_request = false;

	switch ( $type ) {
		case 'admin':
			$is_request = is_admin();
			break;
		case 'ajax':
			$is_request = wp_doing_ajax();
			break;
		case 'cron':
			$is_request = defined( 'DOING_CRON' );
			break;
		case 'frontend':
			$is_request = ( ! is_admin() || wp_doing_ajax() ) && ! defined( 'DOING_CRON' ) && ! wc_instagram_is_request( 'rest_api' );
			break;
		case 'rest_api':
			$is_request = ( defined( 'REST_REQUEST' ) && REST_REQUEST );
			break;
	}

	/**
	 * Filters if the request is of the specified type.
	 *
	 * @since 3.3.0
	 *
	 * @param bool   $is_request Whether the request is of the specified type.
	 * @param string $type       The request type.
	 */
	return apply_filters( 'wc_instagram_is_request', $is_request, $type );
}

/**
 * Gets templates passing attributes and including the file.
 *
 * @since 2.0.0
 *
 * @param string $template_name The template name.
 * @param array  $args          Optional. The template arguments.
 */
function wc_instagram_get_template( $template_name, $args = array() ) {
	wc_get_template( $template_name, $args, WC()->template_path(), WC_INSTAGRAM_PATH . 'templates/' );
}

/**
 * Gets the HTML markup for the specified attributes.
 *
 * @since 3.0.0
 *
 * @param array $attributes An array with pairs [key => value].
 * @return string
 */
function wc_instagram_get_attrs_html( $attributes ) {
	$attribute_strings = array();

	foreach ( $attributes as $key => $value ) {
		$attribute_strings[] = esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
	}

	return implode( ' ', $attribute_strings );
}

/**
 * Logs a message.
 *
 * @since 2.0.0
 *
 * @param string         $message The message to log.
 * @param string         $level   The level.
 * @param string         $handle  Optional. The log handlers.
 * @param WC_Logger|null $logger  Optional. The logger instance.
 */
function wc_instagram_log( $message, $level = 'notice', $handle = 'wc_instagram', $logger = null ) {
	if ( ! $logger ) {
		$logger = wc_get_logger();
	}

	if ( method_exists( $logger, $level ) ) {
		call_user_func( array( $logger, $level ), $message, array( 'source' => $handle ) );
	} else {
		$logger->add( $handle, $message );
	}
}

/**
 * Gets the specified admin url.
 *
 * @since 2.0.0
 *
 * @param array $extra_params Optional. Additional parameters in pairs key => value.
 * @return string The admin page url.
 */
function wc_instagram_get_settings_url( $extra_params = array() ) {
	$url = 'admin.php?page=wc-settings&tab=integration&section=instagram';

	if ( ! empty( $extra_params ) ) {
		foreach ( $extra_params as $param => $value ) {
			$url .= '&' . esc_attr( $param ) . '=' . rawurlencode( $value );
		}
	}

	return admin_url( $url );
}

/**
 * Gets the plugin settings.
 *
 * @since 2.0.0
 *
 * @return array An array with the plugin settings.
 */
function wc_instagram_get_settings() {
	return get_option( 'wc_instagram_settings', array() );
}

/**
 * Gets a setting value.
 *
 * @since 2.0.0
 *
 * @param string $name    The setting name.
 * @param mixed  $default Optional. The default value.
 * @return mixed The setting value.
 */
function wc_instagram_get_setting( $name, $default = null ) {
	$settings = wc_instagram_get_settings();

	return ( isset( $settings[ $name ] ) ? $settings[ $name ] : $default );
}

/**
 * Gets the expiration time for the transient used to cache the API requests.
 *
 * @since 3.0.0
 *
 * @param string $context The context.
 * @return int
 */
function wc_instagram_get_transient_expiration_time( $context = '' ) {
	// Backward compatibility.
	$expiration = apply_filters( 'woocommerce_instagram_transient_expire_time', DAY_IN_SECONDS ); // phpcs:ignore WooCommerce.Commenting.CommentHooks

	/**
	 * Filters the expiration time for the transient used to cache the API requests.
	 *
	 * @since 2.0.0
	 *
	 * @param int    $expiration Time until expiration in seconds.
	 * @param string $context    The context.
	 */
	return apply_filters( 'wc_instagram_transient_expiration_time', $expiration, $context );
}

/**
 * Gets the number of columns to use in a media grid.
 *
 * @since 2.0.0
 *
 * @param string $context The context.
 * @return int
 */
function wc_instagram_get_columns( $context = '' ) {
	$columns = 4;

	// Use the setting value if exists.
	if ( $context ) {
		$columns = wc_instagram_get_setting( "{$context}_columns", $columns );
	}

	// Backward compatibility.
	$columns = apply_filters( 'woocommerce_instagram_columns', $columns ); // phpcs:ignore WooCommerce.Commenting.CommentHooks

	/**
	 * Filters the columns to use in a media grid.
	 *
	 * @since 2.0.0
	 *
	 * @param int    $columns The number of columns.
	 * @param string $context The context.
	 */
	$columns = apply_filters( 'wc_instagram_get_columns', $columns, $context );

	return intval( $columns );
}

/**
 * Gets the number of images to display in a media grid.
 *
 * @since 2.0.0
 *
 * @param string $context The context.
 * @return int
 */
function wc_instagram_get_images_number( $context = '' ) {
	$number = 8;

	// Use the setting value if exists.
	if ( $context ) {
		$number = wc_instagram_get_setting( "{$context}_images", $number );
	}

	// Backward compatibility.
	$number = apply_filters( 'woocommerce_instagram_images', $number ); // phpcs:ignore WooCommerce.Commenting.CommentHooks

	/**
	 * Filters the number of images to display in a media grid.
	 *
	 * @since 2.0.0
	 *
	 * @param int    $number  The number of images.
	 * @param string $context The context.
	 */
	$number = apply_filters( 'wc_instagram_get_images_number', $number, $context );

	return intval( $number );
}

/**
 * Converts a bool to 'yes' or 'no'.
 *
 * The returned string is translatable.
 *
 * @since 3.6.1
 *
 * @see wc_string_to_bool()
 *
 * @param bool $value Bool to convert.
 * @return string
 */
function wc_instagram_bool_to_string( $value ) {
	return ( wc_string_to_bool( $value ) ? __( 'Yes', 'woocommerce-instagram' ) : __( 'No', 'woocommerce-instagram' ) );
}
