<?php
/*
Plugin Name: SearchWP Greek Stemmer
Plugin URI: https://searchwp.com/
Description: Greek keyword stemming
Version: 1.0.2
Author: Jonathan Christopher
Author URI: https://searchwp.com/

Copyright 2015-2018 Jonathan Christopher

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

if ( ! defined( 'SEARCHWP_STEMMER_GREEK_VERSION' ) ) {
	define( 'SEARCHWP_STEMMER_GREEK_VERSION', '1.0.2' );
}

/**
 * instantiate the updater
 */
if ( ! class_exists( 'SWP_Stemmer_Greek_Updater' ) ) {
	// load our custom updater
	include_once( dirname( __FILE__ ) . '/vendor/updater.php' );
}

// set up the updater
function searchwp_stemmer_greek_update_check(){

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

	if ( ! defined( 'SEARCHWP_STEMMER_GREEK_VERSION' ) ) {
		return false;
	}

	// retrieve stored license key
	$license_key = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );
	$license_key = sanitize_text_field( $license_key );

	// instantiate the updater to prep the environment
	$searchwp_stemmer_greek_updater = new SWP_Stemmer_Greek_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 51042,
			'version'   => SEARCHWP_STEMMER_GREEK_VERSION,
			'license'   => $license_key,
			'item_name' => 'Greek Keyword Stemmer',
			'author'    => 'Jonathan Christopher',
			'url'       => site_url(),
		)
	);

	return $searchwp_stemmer_greek_updater;
}

add_action( 'admin_init', 'searchwp_stemmer_greek_update_check' );

if ( ! class_exists( 'SearchWP_GreekStemmer' ) ) {
	include_once( dirname( __FILE__ ) . '/vendor/mod-stemmer.php' );
}

class SearchWP_Stemmer_Greek_Wrapper {

	function __construct() {

		// tell SearchWP we have a stemmer
		add_filter( 'searchwp_keyword_stem_locale', '__return_true' );

		// add our custom stemmer
		add_filter( 'searchwp_custom_stemmer', array( $this, 'greek_stemmer' ) );
	}

	function greek_stemmer( $unstemmed ) {
		if ( ! class_exists( 'SearchWP_GreekStemmer' ) ) {
			return $unstemmed;
		}

		// the stemmer doesn't work well with *any* numbers in the string
		$pattern = '/([0-9]{1,})/iu';
		preg_match( $pattern, $unstemmed, $matches );
		if ( ! empty( $matches ) ) {
			return $unstemmed;
		}

		$stemmer = new SearchWP_GreekStemmer();
		$stemmed = $stemmer->stem_word( $unstemmed, true );

		return sanitize_text_field( $stemmed );
	}

}

new SearchWP_Stemmer_Greek_Wrapper();
