<?php
/*
Plugin Name: SearchWP Dutch Stemmer
Plugin URI: https://searchwp.com/
Description: Dutch keyword stemming
Version: 1.2.4
Author: SearchWP, LLC
Author URI: https://searchwp.com/

Copyright 2015-2016 Jonathan Christopher

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

if ( ! defined( 'SEARCHWP_STEMMER_DUTCH_VERSION' ) ) {
	define( 'SEARCHWP_STEMMER_DUTCH_VERSION', '1.2.4' );
}

/**
 * instantiate the updater
 */
if ( ! class_exists( 'SWP_Stemmer_Dutch_Updater' ) ) {
	// load our custom updater
	include_once( dirname( __FILE__ ) . '/vendor/updater.php' );
}

// set up the updater
function searchwp_stemmer_dutch_update_check(){

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

	if ( ! defined( 'SEARCHWP_STEMMER_DUTCH_VERSION' ) ) {
		return false;
	}

	// retrieve stored license key
	$license_key = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );
	$license_key = sanitize_text_field( $license_key );

	// instantiate the updater to prep the environment
	$searchwp_stemmer_dutch_updater = new SWP_Stemmer_Dutch_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 33262,
			'version'   => SEARCHWP_STEMMER_DUTCH_VERSION,
			'license'   => $license_key,
			'item_name' => 'Dutch Keyword Stemmer',
			'author'    => 'Jonathan Christopher',
			'url'       => site_url(),
		)
	);

	return $searchwp_stemmer_dutch_updater;
}

add_action( 'admin_init', 'searchwp_stemmer_dutch_update_check' );

if ( ! class_exists( 'DutchStemmer' ) ) {
	include_once( dirname( __FILE__ ) . '/vendor/src/DutchStemmer.php' );
}

class SearchWP_Stemmer_Dutch {

	function __construct() {

		// tell SearchWP we have a stemmer
		add_filter( 'searchwp_keyword_stem_locale', '__return_true' );

		// add our custom stemmer
		add_filter( 'searchwp_custom_stemmer', array( $this, 'dutch_stemmer' ) );
	}

	function dutch_stemmer( $unstemmed ) {
		if ( ! class_exists( 'DutchStemmer' ) ) {
			return $unstemmed;
		}

		$stemmer = new DutchStemmer();
		$stemmed = $stemmer->stemWord( $unstemmed );

		return sanitize_text_field( $stemmed );
	}

}

new SearchWP_Stemmer_Dutch();
