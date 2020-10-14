<?php
/*
Plugin Name: SearchWP Redirects
Plugin URI: https://searchwp.com/extensions/redirects/
Description: Automatically redirect to a specific URL when certain searches are performed
Version: 1.4.0
Requires PHP: 5.6
Author: SearchWP, LLC
Author URI: https://searchwp.com/
Text Domain: searchwp_redirects
Domain Path: languages

Copyright 2017-2020 SearchWP

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SEARCHWP_REDIRECTS_VERSION', '1.4.0' );
define( 'SEARCHWP_REDIRECTS_PLUGIN_DIR', dirname( __FILE__ ) );

if ( ! version_compare( PHP_VERSION, '5.3', '<' ) ) {
	include_once SEARCHWP_REDIRECTS_PLUGIN_DIR . '/includes/SearchWP_Redirects.php';

	$searchwp_redirects = new SearchWP_Redirects();

	add_action( 'admin_init', 'searchwp_redirects_update_check' );
} else {
	add_action( 'admin_notices', 'searchwp_redirects_below_php_version_notice' );
}

/**
 * Show an error to sites running PHP < 5.3
 *
 * @since 1.0.0
 */
function searchwp_redirects_below_php_version_notice() {
	// Translators: this message outputs a minimum PHP requirement
	echo '<div class="error"><p>' . esc_html( sprintf( __( 'Your version of PHP (%s) is below the minimum version of PHP required by SearchWP Redirects (5.3). Please contact your host and request that your version be upgraded to 5.3 or later.', 'searchwp_redirects' ), PHP_VERSION ) ) . '</p></div>';
}

/**
 * Set up the updater
 *
 * @return bool|SWP_Redirects_Updater
 */
function searchwp_redirects_update_check() {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return false;
	}

	// Environment check
	if ( ! defined( 'SEARCHWP_PREFIX' ) ) {
		return false;
	}

	if ( ! defined( 'SEARCHWP_EDD_STORE_URL' ) ) {
		return false;
	}

	// Custom updater
	if ( ! class_exists( 'SWP_Redirects_Updater' ) ) {
		include_once dirname( __FILE__ ) . '/updater.php';
	}

	// SearchWP 4 compat.
	if ( class_exists( '\\SearchWP\\License' ) ) {
		$license_key = \SearchWP\License::get_key();
	} else {
		$license_key = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );
		$license_key = sanitize_text_field( $license_key );
	}

	// Instantiate the updater to prep the environment
	$searchwp_redirects_updater = new SWP_Redirects_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
		'item_id'   => 89175,
		'version'   => SEARCHWP_REDIRECTS_VERSION,
		'license'   => $license_key,
		'item_name' => 'Redirects',
		'author'    => 'SearchWP',
		'url'       => site_url(),
	) );

	return $searchwp_redirects_updater;
}

/**
 * Loads the plugin language files
 *
 * @since 1.0.0
 */
function searchwp_redirects_load_textdomain() {

	// Set filter for plugin's languages directory
	$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
	$lang_dir = apply_filters( 'searchwp_redirects_languages_directory', $lang_dir );

	// Traditional WordPress plugin locale filter
	global $wp_version;
	$get_locale = get_locale();
	if ( $wp_version >= 4.7 ) {
		$get_locale = get_user_locale();
	}

	/**
	 * Defines the plugin language locale used in SearchWP Redirect
	 *
	 * @var $get_locale string The locale to use. Uses get_user_locale()` in WordPress 4.7 or greater,
	 *                  otherwise uses `get_locale()`.
	 */
	$locale        = apply_filters( 'plugin_locale', $get_locale, 'searchwp_redirects' );
	$mofile        = sprintf( '%1$s-%2$s.mo', 'searchwp_redirects', $locale );

	// Setup paths to current locale file
	$mofile_local  = $lang_dir . $mofile;
	$mofile_global = WP_LANG_DIR . '/searchwp-redirects/' . $mofile;

	if ( file_exists( $mofile_global ) ) {
		// Look in global /wp-content/languages/searchwp-redirects/ folder
		load_textdomain( 'searchwp_redirects', $mofile_global );
	} elseif ( file_exists( $mofile_local ) ) {
		// Look in local /wp-content/plugins/searchwp-redirects/languages/ folder
		load_textdomain( 'searchwp_redirects', $mofile_local );
	} else {
		// Load the default language files
		load_plugin_textdomain( 'searchwp_redirects', false, $lang_dir );
	}
}

add_action( 'plugins_loaded', 'searchwp_redirects_load_textdomain' );

/**
 * Callback (used internally) to return false for a hook
 *
 * @return bool
 */
function searchwp_redirects_disable_hook() {
	return false;
}

/**
 * Retrieve the settings for SearchWP Redirect
 *
 * @return mixed
 */
function searchwp_redirects_get_settings() {
	return get_option( 'searchwp_redirects' );
}
