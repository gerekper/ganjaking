<?php
/*
Plugin Name: SearchWP Fuzzy Matches
Plugin URI: https://searchwp.com/
Description: Fuzzy matching for search terms and primitive spelling error affordance
Version: 1.4.4
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

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'SEARCHWP_FUZZY_MATCHES_VERSION' ) ) {
	define( 'SEARCHWP_FUZZY_MATCHES_VERSION', '1.4.4' );
}

/**
 * Instantiate the updater
 */
if ( ! class_exists( 'SWP_Fuzzy_Matches_Updater' ) ) {
	// load our custom updater
	include_once( dirname( __FILE__ ) . '/vendor/updater.php' );
}

/**
 * Set up the EDD updater
 *
 * @return bool|SWP_Fuzzy_Matches_Updater
 */
function searchwp_fuzzy_matches_update_check(){

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

	if ( ! defined( 'SEARCHWP_FUZZY_MATCHES_VERSION' ) ) {
		return false;
	}

	// retrieve stored license key
	$license_key = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );
	$license_key = sanitize_text_field( $license_key );

	// instantiate the updater to prep the environment
	$searchwp_fuzzy_matches_updater = new SWP_Fuzzy_Matches_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 31018,
			'version'   => SEARCHWP_FUZZY_MATCHES_VERSION,
			'license'   => $license_key,
			'item_name' => 'Fuzzy Matches',
			'author'    => 'Jonathan Christopher',
			'url'       => site_url(),
		)
	);

	return $searchwp_fuzzy_matches_updater;
}

add_action( 'admin_init', 'searchwp_fuzzy_matches_update_check' );


/**
 * SearchWP Fuzzy Matches
 *
 * Class SearchWPFuzzy
 */
class SearchWPFuzzy {

	/**
	 * SearchWPFuzzy constructor.
	 */
	function __construct() {
		add_action( 'after_plugin_row_' . plugin_basename( __FILE__ ), array( $this, 'plugin_row' ), 11 );

		// NOTE: searchwp_term_in is fired for each term in a search
		add_filter( 'searchwp_term_in', array( $this, 'find_fuzzy_matches' ), 100, 3 );
	}

	/**
	 * Find fuzzy matches using MySQL's SOUNDEX feature
	 *
	 * @param $terms
	 * @param $engine
	 * @param $original_prepped_term
	 *
	 * @return array
	 */
	function find_fuzzy_matches( $terms, $engine, $original_prepped_term ) {
		global $wpdb, $searchwp;

		if ( ! class_exists( 'SearchWP' ) || version_compare( $searchwp->version, '2.4.11', '<' ) ) {
			return $terms;
		}

		if ( isset( $engine ) ) {
			$engine = null;
		}

		$prefix = $wpdb->prefix;

		// there has to be at least a term
		if ( ! is_array( $terms ) || empty( $terms ) ) {
			return $terms;
		}

		// by default we're only going to apply fuzzy logic if we need to (e.g. confirmed misspelling)
		$missing_match = '';
		$found_term = $wpdb->get_col( $wpdb->prepare( "SELECT term FROM {$prefix}swp_terms WHERE term = %s LIMIT 1", $original_prepped_term ) );

		if ( empty( $found_term ) ) {
			$missing_match = $original_prepped_term;
		}

		// if everything was an exact match there's no more work to do
		if ( ! empty( $missing_match ) ) {

			// dynamic minimum character length
			$minCharLength = absint( apply_filters( 'searchwp_fuzzy_min_length', 5 ) ) - 1;

			$sql = "SELECT term FROM {$prefix}swp_terms WHERE CHAR_LENGTH(term) > {$minCharLength} AND (";

			// need to query for fuzzy matches in terms table and append them
			$count = 0;
			$the_terms = array();
			foreach ( $terms as $term ) {

				if ( $count > 0 ) {
					$sql .= ' OR ';
				}

				// Allow developers to control whether LIKE is applied before or after the term
				$filter_args = array(
					'term' => $term,
					'terms' => $terms,
					'engine' => $engine,
					'original_prepped_term' => $original_prepped_term,
				);
				$before_term = apply_filters( 'searchwp_fuzzy_prefix', true, $filter_args ) ? '%' : '';
				$after_term  = apply_filters( 'searchwp_fuzzy_suffix', true, $filter_args ) ? '%' : '';

				$sql .= $wpdb->prepare( "
					( term LIKE %s
					OR reverse LIKE CONCAT(REVERSE( %s ), '%%') ", $before_term . $wpdb->esc_like( $term ) . $after_term, $term );

				// check for the number of digits (e.g. SKUs being sent through would result in disaster)
				preg_match_all( '/[0-9]/', $term, $digits );
				$percentDigits = ! empty( $digits ) && isset( $digits[0] ) ? ( count( $digits[0] ) / strlen( $term ) ) * 100 : 0;

				$percentDigitsThreshold = absint( apply_filters( 'searchwp_fuzzy_digit_threshold', 10 ) );
				if ( $percentDigits < $percentDigitsThreshold ) {
					$sql .= $wpdb->prepare( ' OR SOUNDEX(term) LIKE SOUNDEX( %s ) ', $term );
					$the_terms[] = $term;
				}

				// close it up
				$sql .= ' ) ';

				$count++;
			}

			$sql .= ')';

			$wickedFuzzyTerms = array();

			if ( ! empty( $the_terms ) ) {
				$wickedFuzzyTerms = $wpdb->get_col( $sql );
			}

			// depending on whether we actually used SOUNDEX, we need to trim out potential results
			// determine whether each match should be included based on how many characters match
			$threshold = absint( apply_filters( 'searchwp_fuzzy_threshold', 85 ) );

			if ( $threshold > 100 ) {
				$threshold = 100;
			}

			// loop through all of the wicked fuzzy terms and pluck out what's really relevant
			$actualTerms = array();
			if ( ! empty( $wickedFuzzyTerms ) ) {
				foreach ( $wickedFuzzyTerms as $wickedFuzzyTerm ) {
					foreach ( $terms as $term ) {

						similar_text( $wickedFuzzyTerm, $term, $percent );

						if ( $percent > $threshold ) {
							$actualTerms[] = $wickedFuzzyTerm;
						}
					}
				}
			}

			// clean up our dupes
			if ( ! empty( $actualTerms ) ) {
				$terms = array_values( array_unique( $actualTerms ) );
				$terms = array_map( 'sanitize_text_field', $terms );
			}
		}

		return $terms;
	}

	/**
	 * Callback for plugin row; performs SearchWP core version compat
	 */
	function plugin_row() {
		if ( ! class_exists( 'SearchWP' ) ) {
			return;
		}

		$searchwp = SWP();
		if ( version_compare( $searchwp->version, '2.4.11', '<' ) ) { ?>
			<tr class="plugin-update-tr searchwp">
				<td colspan="3" class="plugin-update">
					<div class="update-message">
						<?php esc_html_e( 'SearchWP Fuzzy Matches requires SearchWP 2.4.11 or greater', 'searchwp' ); ?>
					</div>
				</td>
			</tr>
		<?php }
	}

}

new SearchWPFuzzy();
