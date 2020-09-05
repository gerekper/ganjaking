<?php
/*
Plugin Name: snip - The Rich Snippets & Structured Data Plugin
Plugin URI: https://rich-snippets.io
Description: Allows to create Rich Snippets and general structured data readable by search engines.
Version: 2.19.2
Author: wpbuddy
Author URI: https://wp-buddy.com
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: rich-snippets-schema
Domain Path: /languages
Requires PHP: 7.0.0
NotActiveWarning: Your copy of the Rich Snippets Plugin has not yet been activated.
ActivateNow: Activate it now.
Active: Your copy is active.

Copyright 2012-2020  WP-Buddy  (email : support@wp-buddy.com)

SNIP is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

SNIP is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

define( 'WPB_RS_FILE', __FILE__ );

/**
 *
 * PHP Version check.
 *
 */
if ( ! call_user_func( function () {
	if ( version_compare( PHP_VERSION, '7.0', '<' ) ) {
		add_action( 'admin_notices', 'wpb_rs_old_php_notice' );

		function wpb_rs_old_php_notice() {

			printf(
				'<div class="notice error"><p>%s</p></div>',
				sprintf(
					__( 'Hey mate! Sorry for interrupting you. It seem\'s that you\'re using an old PHP version (your current version is %s). You should upgrade to at least %s or higher in order to use SNIP. Thank you!', 'rich-snippets-schema' ),
					esc_html( PHP_VERSION ),
					'7.2'
				)
			);
		}

		$plugin_file = substr( str_replace( WP_PLUGIN_DIR, '', __FILE__ ), 1 );

		add_action( 'after_plugin_row_' . $plugin_file, 'wpb_rs_plugin_upgrade_notice', 10, 2 );

		function wpb_rs_plugin_upgrade_notice( $plugin_data, $status ) {

			printf(
				'<tr><td></td><td colspan="2"><div class="notice notice-error notice-error-alt inline"><p>%s</p></div></td></tr>',
				sprintf( __( 'SNIP needs at least PHP version %s to run properly. <a href="https://wordpress.org/support/update-php/" target="_blank">Read more about how to upgrade here.</a>', 'rich-snippets-schema' ), '7.2.x' )
			);
		}

		# sorry. The plugin will not work with an old PHP version.
		return false;
	}

	global $wp_version;

	if ( version_compare( $wp_version, '5.0.0', '<' ) ) {
		add_action( 'admin_notices', 'wpb_rs_old_php_notice' );

		function wpb_rs_old_php_notice() {
			global $wp_version;

			printf(
				'<div class="notice error"><p>%s</p></div>',
				sprintf(
					__( 'Hey mate! Sorry for interrupting you. It seem\'s that you\'re using an old WordPress version (your current version is %s). You should upgrade to at least %s or higher in order to use SNIP. Thank you!', 'rich-snippets-schema' ),
					esc_html( $wp_version ),
					'5.0.0'
				)
			);
		}

		return false;
	}

	if ( function_exists( 'rich_snippets' ) ) {
		add_action( 'admin_notices', 'wpb_rs_already_exists' );

		function wpb_rs_already_exists() {
			printf(
				'<div class="notice error"><p>%s</p></div>',
				__( 'Hey mate! Sorry for interrupting you. It seem\'s that another version of SNIP is already installed and active. Make sure only one version is active.', 'rich-snippets-schema' )
			);
		}

		return false;
	}

	return true;
} ) ) {
	return;
}


/**
 *
 * WP Version check.
 *
 */
if ( version_compare( get_bloginfo( 'version' ), '4.6', '<' ) ) {
	add_action( 'admin_notices', 'wpb_rss_old_php_notice' );

	function wpb_rss_old_php_notice() {

		printf(
			'<div class="notice error"><p>%s</p></div>',
			sprintf(
				__( 'Hey mate! Sorry for interrupting you. It seem\'s that you\'re using an old version WordPress (your current version is %s). You should upgrade to at least 4.6 or higher in order to use the Rich Snippets plugin. Thank you!', 'rich-snippets-schema' ),
				esc_html( get_bloginfo( 'version' ) )
			)
		);
	}

	# sorry. The plugin will not work with an old WP version.
	return;
}


/**
 *
 *
 * Bootstrapping
 *
 */
require_once( __DIR__ . '/bootstrap.php' );