<?php
/**
 * Factory Function Library
 *
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>, repo: https://github.com/alexkovalevv
 * @author        Webcraftic <wordpress.webraftic@gmail.com>, site: https://webcraftic.com
 *
 * @package       factory-core
 * @since         1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'get_user_locale' ) ) {
	function get_user_locale( $user_id = 0 ) {
		$user = false;
		if ( 0 === $user_id && function_exists( 'wp_get_current_user' ) ) {
			$user = wp_get_current_user();
		} else if ( $user_id instanceof WP_User ) {
			$user = $user_id;
		} else if ( $user_id && is_numeric( $user_id ) ) {
			$user = get_user_by( 'id', $user_id );
		}

		if ( ! $user ) {
			return get_locale();
		}

		$locale = $user->locale;

		return $locale ? $locale : get_locale();
	}
}

/**
 * Fires functions attached to a deprecated filter hook.
 *
 * When a filter hook is deprecated, the apply_filters() call is replaced with
 * apply_filters_deprecated(), which triggers a deprecation notice and then fires
 * the original filter hook.
 *
 * This is a copy of `apply_filters_deprecated` introduced in WP 4.6.
 *
 * @since 1.0.0
 *
 * @param string $tag           The name of the filter hook.
 * @param array  $args          Array of additional function arguments to be passed to apply_filters().
 * @param string $version       The version of BP Block Users that deprecated the hook.
 * @param string $replacement   Optional. The hook that should have been used.
 * @param string $message       Optional. A message regarding the change.
 *
 * @return mixed
 * @see   wbcr_factory_439_deprecated_hook()
 *
 */
function wbcr_factory_439_apply_filters_deprecated( $tag, $args, $version, $replacement = false, $message = null ) {
	if ( function_exists( 'apply_filters_deprecated' ) ) {
		return apply_filters_deprecated( $tag, $args, $version, $replacement, $message );
	}
	if ( ! has_filter( $tag ) ) {
		return $args[0];
	}
	wbcr_factory_439_deprecated_hook( $tag, $version, $replacement, $message );

	return apply_filters_ref_array( $tag, $args );
}

/**
 * Fires functions attached to a deprecated action hook.
 *
 * When an action hook is deprecated, the do_action() call is replaced with
 * do_action_deprecated(), which triggers a deprecation notice and then fires
 * the original hook.
 *
 * This is a copy of `do_action_deprecated` introduced in WP 4.6.
 *
 * @since 1.0.0
 *
 * @param string $tag           The name of the action hook.
 * @param array  $args          Array of additional function arguments to be passed to do_action().
 * @param string $version       The version of BP Block Users that deprecated the hook.
 * @param string $replacement   Optional. The hook that should have been used.
 * @param string $message       Optional. A message regarding the change.
 *
 * @return void
 * @see   _deprecated_hook()
 *
 */
function wbcr_factory_439_do_action_deprecated( $tag, $args, $version, $replacement = false, $message = null ) {
	if ( function_exists( 'do_action_deprecated' ) ) {
		do_action_deprecated( $tag, $args, $version, $replacement, $message );

		return;
	}
	if ( ! has_action( $tag ) ) {
		return;
	}
	wbcr_factory_439_deprecated_hook( $tag, $version, $replacement, $message );
	do_action_ref_array( $tag, $args );
}

/**
 * Marks a deprecated action or filter hook as deprecated and throws a notice.
 *
 * Use the 'wbcr_factory_439_deprecated_hook_run' action to get the backtrace describing where the
 * deprecated hook was called.
 *
 * Default behavior is to trigger a user error if WP_DEBUG is true.
 *
 * This function is called by the do_action_deprecated() and apply_filters_deprecated()
 * functions, and so generally does not need to be called directly.
 *
 * This is a copy of `_deprecated_hook` introduced in WP 4.6.
 *
 * @since  1.0.0
 * @access private
 *
 * @param string $hook          The hook that was used.
 * @param string $version       The version of WordPress that deprecated the hook.
 * @param string $replacement   Optional. The hook that should have been used.
 * @param string $message       Optional. A message regarding the change.
 */
function wbcr_factory_439_deprecated_hook( $hook, $version, $replacement = null, $message = null ) {
	/**
	 * Fires when a deprecated hook is called.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook          The hook that was called.
	 * @param string $replacement   The hook that should be used as a replacement.
	 * @param string $version       The version of BP Block Users that deprecated the argument used.
	 * @param string $message       A message regarding the change.
	 */
	do_action( 'deprecated_hook_run', $hook, $replacement, $version, $message );

	/**
	 * Filter whether to trigger deprecated hook errors.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $trigger   Whether to trigger deprecated hook errors. Requires
	 *                        `WP_DEBUG` to be defined true.
	 */
	if ( WP_DEBUG && apply_filters( 'deprecated_hook_trigger_error', true ) ) {
		$message = empty( $message ) ? '' : ' ' . $message;
		if ( ! is_null( $replacement ) ) {
			trigger_error( sprintf( __( '%1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.' ), $hook, $version, $replacement ) . $message );
		} else {
			trigger_error( sprintf( __( '%1$s is <strong>deprecated</strong> since version %2$s with no alternative available.' ), $hook, $version ) . $message );
		}
	}
}

if ( ! function_exists( '_sanitize_text_fields' ) ) {
	function _sanitize_text_fields( $str, $keep_newlines = false ) {
		$filtered = wp_check_invalid_utf8( $str );

		if ( strpos( $filtered, '<' ) !== false ) {
			$filtered = wp_pre_kses_less_than( $filtered );
			// This will strip extra whitespace for us.
			$filtered = wp_strip_all_tags( $filtered, false );

			// Use html entities in a special case to make sure no later
			// newline stripping stage could lead to a functional tag
			$filtered = str_replace( "<\n", "&lt;\n", $filtered );
		}

		if ( ! $keep_newlines ) {
			$filtered = preg_replace( '/[\r\n\t ]+/', ' ', $filtered );
		}
		$filtered = trim( $filtered );

		$found = false;
		while( preg_match( '/%[a-f0-9]{2}/i', $filtered, $match ) ) {
			$filtered = str_replace( $match[0], '', $filtered );
			$found    = true;
		}

		if ( $found ) {
			// Strip out the whitespace that may now exist after removing the octets.
			$filtered = trim( preg_replace( '/ +/', ' ', $filtered ) );
		}

		return $filtered;
	}
}

if ( ! function_exists( 'sanitize_textarea_field' ) ) {
	function sanitize_textarea_field( $str ) {
		$filtered = _sanitize_text_fields( $str, true );

		/**
		 * Filters a sanitized textarea field string.
		 *
		 * @since 4.7.0
		 *
		 * @param string $filtered   The sanitized string.
		 * @param string $str        The string prior to being sanitized.
		 */
		return apply_filters( 'sanitize_textarea_field', $filtered, $str );
	}
}
