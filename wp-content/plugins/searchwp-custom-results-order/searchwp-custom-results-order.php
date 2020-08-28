<?php
/*
Plugin Name: SearchWP Custom Results Order
Plugin URI: https://searchwp.com/extensions/custom-results-order/
Description: Customize the order of SearchWP's results
Version: 1.3.3
Author: SearchWP
Author URI: https://searchwp.com/
Text Domain: searchwpcro
Domain Path: languages

Copyright 2019-2020 SearchWP

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

define( 'SEARCHWP_CRO_VERSION', '1.3.3' );
define( 'SEARCHWP_CRO_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'SEARCHWP_CRO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( ! version_compare( PHP_VERSION, '5.6', '<' ) ) {
	include_once SEARCHWP_CRO_PLUGIN_DIR . '/i18n.php';
	include_once SEARCHWP_CRO_PLUGIN_DIR . '/includes/functions.php';
	include_once SEARCHWP_CRO_PLUGIN_DIR . '/includes/SearchWP_CRO.php';

	$custom_results_order = new SearchWP_CRO();
	$custom_results_order->init();

	add_action( 'admin_init', 'searchwp_cro_update_check' );
} else {
	add_action( 'admin_notices', 'searchwp_cro_below_php_version_notice' );
}

/**
 * Show an error to sites running PHP < 5.6
 *
 * @since 1.0
 */
function searchwp_cro_below_php_version_notice() {
	// Translators: this message outputs a minimum PHP requirement
	echo '<div class="error"><p>' . esc_html( sprintf( __( 'Your version of PHP (%s) is below the minimum version of PHP required by SearchWP Custom Results Order (5.6). Please contact your host and request that your version be upgraded to 5.6 or later.', 'swpcustomizeresults' ), PHP_VERSION ) ) . '</p></div>';
}

/**
 * Set up the updater
 *
 * @return bool|SWP_CRO_Updater
 */
function searchwp_cro_update_check() {

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

	if ( ! defined( 'SEARCHWP_CRO_VERSION' ) ) {
		return false;
	}

	// Custom updater
	if ( ! class_exists( 'SWP_CRO_Updater' ) ) {
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
	$searchwp_cro_updater = new SWP_CRO_Updater(
		SEARCHWP_EDD_STORE_URL,
		__FILE__,
		array(
			'item_id'   => 177762,
			'version'   => SEARCHWP_CRO_VERSION,
			'license'   => $license_key,
			'item_name' => 'Custom Results Order',
			'author'    => 'SearchWP',
			'url'       => site_url(),
		)
	);

	return $searchwp_cro_updater;
}
