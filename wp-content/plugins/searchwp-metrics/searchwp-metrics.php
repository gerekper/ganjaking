<?php
/*
Plugin Name: SearchWP Metrics
Plugin URI: https://searchwp.com/extensions/metrics/
Description: Advanced search metrics from SearchWP
Version: 1.3.2
Author: SearchWP
Author URI: https://searchwp.com/
Text Domain: searchwp-metrics
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

define( 'SEARCHWP_METRICS_VERSION',    '1.3.2' );
define( 'SEARCHWP_METRICS_PREFIX',     'searchwp_metrics_' );
define( 'SEARCHWP_METRICS_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'SEARCHWP_METRICS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( ! version_compare( PHP_VERSION, '5.6', '<' ) ) {
	include_once SEARCHWP_METRICS_PLUGIN_DIR . '/searchwp-metrics-i18n.php';
	include_once SEARCHWP_METRICS_PLUGIN_DIR . '/includes/SearchWP_Metrics.php';

	$searchwp_metrics = new SearchWP_Metrics();

	add_action( 'init', array( $searchwp_metrics, 'init' ) );

	add_action( 'admin_init', 'searchwp_metrics_update_check' );
} else {
	add_action( 'admin_notices', 'searchwp_metrics_below_php_version_notice' );
}

/**
 * Show an error to sites running PHP < 5.4
 *
 * @since 1.0.0
 */
function searchwp_metrics_below_php_version_notice() {
	// Translators: this message outputs a minimum PHP requirement
	echo '<div class="error"><p>' . esc_html( sprintf( __( 'Your version of PHP (%s) is below the minimum version of PHP required by SearchWP Metrics (5.6). Please contact your host and request that your version be upgraded to 5.6 or later.', 'searchwp-metrics' ), PHP_VERSION ) ) . '</p></div>';
}

/**
 * Set up the updater
 *
 * @return bool|SWP_Metrics_Updater
 */
function searchwp_metrics_update_check() {

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
	if ( ! class_exists( 'SWP_Metrics_Updater' ) ) {
		include_once( dirname( __FILE__ ) . '/updater.php' );
	}

	// SearchWP 4 compat.
	if ( class_exists( '\\SearchWP\\License' ) ) {
		$license_key = \SearchWP\License::get_key();
	} else {
		$license_key = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );
		$license_key = sanitize_text_field( $license_key );
	}

	// Instantiate the updater to prep the environment
	$searchwp_metrics_updater = new SWP_Metrics_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
		'item_id'   => 86386,
		'version'   => SEARCHWP_METRICS_VERSION,
		'license'   => $license_key,
		'item_name' => 'Metrics',
		'author'    => 'SearchWP',
		'url'       => site_url(),
	) );

	return $searchwp_metrics_updater;
}

/**
 * Callback for hooks to return false, but be remove-able
 *
 * @return bool
 */
function searchwp_metrics_return_false() {
	return false;
}
