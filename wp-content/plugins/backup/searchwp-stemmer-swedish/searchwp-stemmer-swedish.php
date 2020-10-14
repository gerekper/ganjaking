<?php
/*
Plugin Name: SearchWP Swedish Stemmer
Plugin URI: https://searchwp.com/
Description: Swedish keyword stemming
Version: 2.0.2
Author: SearchWP, LLC
Author URI: https://searchwp.com/

Copyright 2016-2018 Jonathan Christopher

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

if ( ! defined( 'SEARCHWP_STEMMER_SWEDISH_VERSION' ) ) {
	define( 'SEARCHWP_STEMMER_SWEDISH_VERSION', '2.0.2' );
}

/**
 * Instantiate the updater
 */
if ( ! class_exists( 'SWP_Stemmer_Swedish_Updater' ) ) {
	// load our custom updater
	include_once( dirname( __FILE__ ) . '/vendor/updater.php' );
}

/**
 * Set up the updater
 *
 * @return bool|SWP_Stemmer_Swedish_Updater
 */
function searchwp_stemmer_swedish_update_check(){

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return false;
	}

	// environment check
	if ( ! defined( 'SEARCHWP_PREFIX' ) ) {
		return false;
	}

	if ( ! defined( 'SEARCHWP_EDD_STORE_URL' ) ) {
		return false;
	}

	if ( ! defined( 'SEARCHWP_STEMMER_SWEDISH_VERSION' ) ) {
		return false;
	}

	// retrieve stored license key
	$license_key = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );
	$license_key = sanitize_text_field( $license_key );

	// instantiate the updater to prep the environment
	$searchwp_stemmer_swedish_updater = new SWP_Stemmer_Swedish_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 62718,
			'version'   => SEARCHWP_STEMMER_SWEDISH_VERSION,
			'license'   => $license_key,
			'item_name' => 'Swedish Keyword Stemmer',
			'author'    => 'Jonathan Christopher',
			'url'       => site_url(),
		)
	);

	return $searchwp_stemmer_swedish_updater;
}

add_action( 'admin_init', 'searchwp_stemmer_swedish_update_check' );

include_once( dirname( __FILE__ ) . '/vendor/autoload.php' );
include_once( dirname( __FILE__ ) . '/searchwp-stemmer-swedish-loader.php' );

function searchwp_stemmer_swedish_activate( $wp = '4.0', $php = '5.3.0' ) {
	global $wp_version;

	if ( version_compare( PHP_VERSION, $php, '<' ) ) {
		$flag = 'PHP';
	} elseif ( version_compare( $wp_version, $wp, '<' ) ) {
		$flag = 'WordPress';
	} else {
		return;
	}

	$version = 'PHP' == $flag ? $php : $wp;

	deactivate_plugins( basename( __FILE__ ) );

	wp_die( '<p>Swedish French Keyword Stemmer requires ' . esc_html( $flag ) . '  version ' . esc_html( $version ) . ' or greater.</p>' );
}

register_activation_hook( __FILE__, 'searchwp_stemmer_swedish_activate' );

function searchwp_stemmer_swedish_load() {
	new SearchWP_Stemmer_Swedish();
}
add_action( 'plugins_loaded', 'searchwp_stemmer_swedish_load' );
