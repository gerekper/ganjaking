<?php
/*
Plugin Name: SearchWP EDD Integration
Plugin URI: https://searchwp.com/
Description: Integrate SearchWP with Easy Digital Downloads
Version: 1.1.0
Author: SearchWP
Author URI: https://searchwp.com/

Copyright 2015-2020 SearchWP

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

if ( ! defined( 'SEARCHWP_EDD_VERSION' ) ) {
	define( 'SEARCHWP_EDD_VERSION', '1.1.0' );
}

/**
 * instantiate the updater
 */
if ( ! class_exists( 'SWP_EDD_Updater' ) ) {
	// load our custom updater
	include_once( dirname( __FILE__ ) . '/vendor/updater.php' );
}

// set up the updater
function searchwp_edd_update_check() {

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

	// SearchWP 4 compat.
	if ( class_exists( '\\SearchWP\\License' ) ) {
		$license_key = \SearchWP\License::get_key();
	} else {
		$license_key = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );
		$license_key = sanitize_text_field( $license_key );
	}

	// instantiate the updater to prep the environment
	$searchwp_edd_updater = new SWP_EDD_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 41570,
			'version'   => SEARCHWP_EDD_VERSION,
			'license'   => $license_key,
			'item_name' => 'EDD Integration',
			'author'    => 'SearchWP',
			'url'       => site_url(),
		)
	);

	return $searchwp_edd_updater;
}

add_action( 'admin_init', 'searchwp_edd_update_check' );

/**
 * SearchWP EDD Integration
 *
 * Class SearchWP_EDD
 */
class SearchWP_EDD {

	function __construct() {
		add_filter( 'edd_downloads_query', array( $this, 'maybe_hijack_edd_downloads_shortcode' ), 999, 2 );
	}

	function maybe_hijack_edd_downloads_shortcode( $query, $atts ) {
		if ( empty( $query['s'] ) ) {
			return $query;
		}

		// allow devs to customize which engine gets used
		$engine = apply_filters( 'searchwp_edd_engine', 'default', $query, $atts );

		// make sure if it's defined it's an array
		if ( ! empty( $query['post__in'] ) ) {
			// make sure it's an array
			$source = $query['post__in'];

			if ( is_string( $source ) ) {
				$source = explode( ',' , $source );
			}

			$source = array_map( 'trim', $source );
			$source = array_map( 'absint', $source );
			$source = array_unique( $source );

			$query['post__in'] = $source;
		}

		// retrieve results via SearchWP
		$searchwp_results = new SWP_Query( array(
			's'          => $query['s'],
			'nopaging'   => true,
			'load_posts' => false,
			'engine'     => sanitize_text_field( $engine ),
		) );

		$results = $searchwp_results->posts;

		// kill the search because we're going to 'search' via post__in
		unset( $query['s'] );

		// check to see if it's already being limited
		if ( is_array( $query['post__in'] ) && count( $query['post__in'] ) ) {
			$query['post__in'] = array_intersect( $query['post__in'], $results );
		} else {
			// post__in wasn't set so let's just set it
			$query['post__in'] = $results;

			// if it was empty, we want to be sure it's empty
			if ( empty( $query['post__in'] ) ) {
				// no results, so force that
				$query['post__in'] = array( 0 );
			}
		}

		// sort the results by SearchWP relevance
		if ( ! empty( $query['post__in'] ) ) {
			$query['orderby'] = 'post__in';
		}

		return $query;
	}
}

new SearchWP_EDD();
