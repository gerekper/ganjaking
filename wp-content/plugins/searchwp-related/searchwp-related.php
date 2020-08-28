<?php
/*
Plugin Name: SearchWP Related
Plugin URI: https://searchwp.com/extensions/related/
Description: Utilize SearchWP to find related content
Version: 1.4.3
Requires PHP: 5.6
Author: SearchWP
Author URI: https://searchwp.com/
Text Domain: searchwp-related
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

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SEARCHWP_RELATED_VERSION', '1.4.3' );
define( 'SEARCHWP_RELATED_PLUGIN_DIR', dirname( __FILE__ ) );

if ( ! version_compare( PHP_VERSION, '5.3', '<' ) ) {
	include_once SEARCHWP_RELATED_PLUGIN_DIR . '/includes/SearchWP_Related.php';

	$searchwp_related = new SearchWP_Related();
	$searchwp_related->init();

	include_once SEARCHWP_RELATED_PLUGIN_DIR . '/searchwp-related-widget.php';

	add_action( 'admin_init', 'searchwp_related_update_check' );
} else {
	add_action( 'admin_notices', 'searchwp_related_below_php_version_notice' );
}

/**
 * Show an error to sites running PHP < 5.3
 *
 * @since 1.0.0
 */
function searchwp_related_below_php_version_notice() {
	// Translators: this message outputs a minimum PHP requirement
	echo '<div class="error"><p>' . esc_html( sprintf( __( 'Your version of PHP (%s) is below the minimum version of PHP required by SearchWP Related (5.3). Please contact your host and request that your version be upgraded to 5.3 or later.', 'searchwp-related' ), PHP_VERSION ) ) . '</p></div>';
}

/**
 * Set up the updater
 *
 * @return bool|SWP_Related_Updater
 */
function searchwp_related_update_check() {

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
	if ( ! class_exists( 'SWP_Related_Updater' ) ) {
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
	$searchwp_related_updater = new SWP_Related_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
		'item_id'   => 85457,
		'version'   => SEARCHWP_RELATED_VERSION,
		'license'   => $license_key,
		'item_name' => 'Related',
		'author'    => 'SearchWP',
		'url'       => site_url(),
	) );

	return $searchwp_related_updater;
}

/**
 * Loads the plugin language files
 *
 * @since 1.0.0
 */
function searchwp_related_load_textdomain() {
	global $wp_version;

	// Set filter for plugin's languages directory
	$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
	$lang_dir = apply_filters( 'searchwp_related_languages_directory', $lang_dir );

	// Traditional WordPress plugin locale filter
	$get_locale = get_locale();
	if ( $wp_version >= 4.7 ) {
		$get_locale = get_user_locale();
	}

	/**
	 * Defines the plugin language locale used in SearchWP Related
	 *
	 * @var $get_locale string The locale to use. Uses get_user_locale()` in WordPress 4.7 or greater,
	 *                  otherwise uses `get_locale()`.
	 */
	$locale = apply_filters( 'plugin_locale', $get_locale, 'searchwp-related' );
	$mofile  = sprintf( '%1$s-%2$s.mo', 'searchwp-related', $locale );

	// Setup paths to current locale file
	$mofile_local  = $lang_dir . $mofile;
	$mofile_global = WP_LANG_DIR . '/searchwp-related/' . $mofile;

	if ( file_exists( $mofile_global ) ) {
		// Look in global /wp-content/languages/searchwp-related/ folder
		load_textdomain( 'searchwp-related', $mofile_global );
	} elseif ( file_exists( $mofile_local ) ) {
		// Look in local /wp-content/plugins/searchwp-related/languages/ folder
		load_textdomain( 'searchwp-related', $mofile_local );
	} else {
		// Load the default language files
		load_plugin_textdomain( 'searchwp-related', false, $lang_dir );
	}
}

add_action( 'plugins_loaded', 'searchwp_related_load_textdomain' );

/**
 * Callback (used internally) to return false for a hook
 *
 * @return bool
 */
function searchwp_related_disable_hook() {
	return false;
}

/**
 * Retrieve the settings for SearchWP Related
 *
 * @return mixed
 */
function searchwp_related_get_settings() {
	return get_option( 'searchwp_related' );
}
