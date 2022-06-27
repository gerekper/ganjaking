<?php
/**
 * Useful functions for the plugin
 *
 * @package WC_Account_Funds/Functions
 * @since   2.2.0
 */

defined( 'ABSPATH' ) || exit;

// Include core functions.
require 'wc-account-funds-order-functions.php';

/**
 * Gets the suffix for the script filenames.
 *
 * @since 2.2.0
 *
 * @return string The scripts suffix.
 */
function wc_account_funds_get_scripts_suffix() {
	return ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
}

/**
 * What type of request is this?
 *
 * @since 2.2.0
 *
 * @param string $type admin, ajax, cron, rest_api or frontend.
 * @return bool
 */
function wc_account_funds_is_request( $type ) {
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
			$is_request = ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! wc_account_funds_is_request( 'rest_api' );
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
	 * @since 2.2.0
	 *
	 * @param bool   $is_request Whether the request is of the specified type.
	 * @param string $type       The request type.
	 */
	return apply_filters( 'wc_account_funds_is_request', $is_request, $type );
}

/**
 * Gets templates passing attributes and including the file.
 *
 * @since 2.2.0
 *
 * @param string $template_name The template name.
 * @param array  $args          Optional. The template arguments.
 */
function wc_account_funds_get_template( $template_name, $args = array() ) {
	wc_get_template( $template_name, $args, '', WC_ACCOUNT_FUNDS_PATH . 'templates/' );
}

/**
 * Gets whether the current user has the capability to accomplish the specified action.
 *
 * @since 2.7.0
 *
 * @param string $action  The action name.
 * @param mixed  ...$args Additional parameters to pass to the callback functions.
 * @return bool
 */
function wc_account_funds_current_user_can( $action, ...$args ) {
	/**
	 * Filters whether the current user has the capability to accomplish the specified action.
	 *
	 * The dynamic portion of the hook name, $action, refers to the action to accomplish.
	 *
	 * @since 2.7.0
	 *
	 * @param bool  $has_capability Whether the current user has the capability.
	 * @param mixed ...$args        Additional parameters to pass to the callback functions.
	 */
	return apply_filters( "wc_account_funds_current_user_can_{$action}", current_user_can( 'manage_woocommerce' ), ...$args );
}
