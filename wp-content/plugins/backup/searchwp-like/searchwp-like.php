<?php
/*
Plugin Name: SearchWP LIKE Terms
Plugin URI: https://searchwp.com/
Description: Add partial matches to search queries
Version: 2.4.6
Author: SearchWP, LLC
Author URI: https://searchwp.com/

Copyright 2013-2018 Jonathan Christopher

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

if ( ! defined( 'SEARCHWP_LIKE_TERMS_VERSION' ) ) {
	define( 'SEARCHWP_LIKE_TERMS_VERSION', '2.4.6' );
}

/**
 * instantiate the updater
 */
if ( ! class_exists( 'SWP_Like_Terms_Updater' ) ) {
	// load our custom updater
	include_once( dirname( __FILE__ ) . '/vendor/updater.php' );
}

// set up the updater
function searchwp_like_terms_update_check(){

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

	if ( ! defined( 'SEARCHWP_LIKE_TERMS_VERSION' ) ) {
		return false;
	}

	// retrieve stored license key
	$license_key = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );
	$license_key = sanitize_text_field( $license_key );

	// instantiate the updater to prep the environment
	$searchwp_like_terms_updater = new SWP_Like_Terms_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 33247,
			'version'   => SEARCHWP_LIKE_TERMS_VERSION,
			'license'   => $license_key,
			'item_name' => 'LIKE Terms',
			'author'    => 'Jonathan Christopher',
			'url'       => site_url(),
		)
	);

	return $searchwp_like_terms_updater;
}

add_action( 'admin_init', 'searchwp_like_terms_update_check' );

class SearchWPLike {

	function __construct() {
		add_action( 'after_plugin_row_' . plugin_basename( __FILE__ ), array( $this, 'plugin_row' ), 11 );

		add_filter( 'searchwp_term_in', array( $this, 'find_like_terms' ), 10, 2 );
	}

	function find_like_terms( $terms, $engine ) {
		global $wpdb, $searchwp;

		if ( ! class_exists( 'SearchWP' ) || version_compare( $searchwp->version, '2.0.3', '<' ) ) {
			return $terms;
		}

		$prefix = $wpdb->prefix;

		if ( is_string( $terms ) ) {
			$terms = explode( ' ', $terms );
		}

		// check against the regex pattern whitelist
		$terms = ' ' . implode( ' ', $terms ) . ' ';
		$whitelisted_terms = array();

		if ( method_exists( $searchwp, 'extract_terms_using_pattern_whitelist' ) ) { // added in SearchWP 1.9.5
			// extract terms based on whitelist pattern, allowing for approved indexing of terms with punctuation
			$whitelisted_terms = $searchwp->extract_terms_using_pattern_whitelist( $terms );

			// add the buffer so we can whole-word replace
			$terms = '  ' . $terms . '  ';

			// remove the matches
			if ( ! empty( $whitelisted_terms ) ) {
				$terms = str_ireplace( $whitelisted_terms, '', $terms );
			}

			// clean up the double space flag we used
			$terms = str_replace( '  ', ' ', $terms );
		}

		// rebuild our terms array
		$terms = explode( ' ', $terms );

		// maybe append our whitelist
		if ( is_array( $whitelisted_terms ) && ! empty( $whitelisted_terms ) ) {
			$whitelisted_terms = array_map( 'trim', $whitelisted_terms );
			$terms = array_merge( $terms, $whitelisted_terms );
		}

		$terms = array_map( 'trim', $terms );
		$terms = array_filter( $terms, 'strlen' );
		$terms = array_map( 'sanitize_text_field', $terms );

		// dynamic minimum character length
		$minCharLength = absint( apply_filters( 'searchwp_like_min_length', 4 ) ) - 1;

		// Filter out $terms based on min length
		foreach ( $terms as $key => $term ) {
			if ( strlen( $term ) < $minCharLength ) {
				unset ( $terms[ $key ] );
			}
		}

		$terms = array_values( $terms );

		$likeTerms = array();

		if ( ! empty( $terms ) ) {

			// by default we will compare to both the term and the stem, but give developers the option to prevent comparison to the stem
			$term_or_stem = 'stem';
			if ( ! apply_filters( 'searchwp_like_stem', false, $terms, $engine ) ) {
				$term_or_stem = 'term';
			}

			$sql = "SELECT {$term_or_stem} FROM {$prefix}swp_terms WHERE CHAR_LENGTH({$term_or_stem}) > {$minCharLength} AND (";

			$wildcard_before = apply_filters( 'searchwp_like_wildcard_before', true );
			if ( ! empty( $wildcard_before ) ) {
				$wildcard_before = '%';
			} else {
				$wildcard_before = '';
			}

			$wildcard_after = apply_filters( 'searchwp_like_wildcard_after', true );
			if ( ! empty( $wildcard_after ) ) {
				$wildcard_after = '%';
			} else {
				$wildcard_after = '';
			}

			// need to query for LIKE matches in terms table and append them
			$count = 0;
			foreach ( $terms as $term ) {
				if ( $count > 0 ) {
					$sql .= ' OR ';
				}
				if ( 'stem' == $term_or_stem ) {
					$sql .= $wpdb->prepare( ' ( term LIKE %s OR stem LIKE %s ) ', $wildcard_before . $wpdb->esc_like( $term ) . $wildcard_after, $wildcard_before . $wpdb->esc_like( $term ) . $wildcard_after );
				} else {
					$sql .= $wpdb->prepare( ' ( term LIKE %s ) ', $wildcard_before . $wpdb->esc_like( $term ) . $wildcard_after );
				}
				$count ++;
			}
			$sql .= ')';

			$likeTerms = $wpdb->get_col( $sql );
		}

		$term = array_values( array_unique( array_merge( $likeTerms, $terms ) ) );

		$term = array_map( 'sanitize_text_field', $term );

		return $term;
	}

	function plugin_row() {
		if ( ! class_exists( 'SearchWP' ) ) {
			return;
		}

		$searchwp = SWP();
		if ( version_compare( $searchwp->version, '2.0.3', '<' ) ) { ?>
			<tr class="plugin-update-tr searchwp">
				<td colspan="3" class="plugin-update">
					<div class="update-message">
						<?php esc_html_e( 'SearchWP LIKE Terms requires SearchWP 2.0.3 or greater', 'searchwp' ); ?>
					</div>
				</td>
			</tr>
		<?php }
	}

}

new SearchWPLike();
