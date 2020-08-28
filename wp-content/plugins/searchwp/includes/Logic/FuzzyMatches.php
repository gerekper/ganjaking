<?php

/**
 * SearchWP fuzzy matches logic.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Logic;

use SearchWP\Utils;
use SearchWP\Tokens;

/**
 * Class FuzzyMatches is responsible for finding fuzzy matches for search strings.
 *
 * @since 4.0
 */
class FuzzyMatches {

	/**
	 * Partial match handler.
	 *
	 * @since 4.0
	 */
	private $partial;

	/**
	 * Fuzzy tokens.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $tokens = [];

	/**
	 * Tokens from the original search string.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $keywords_tokens;

	private $exact_matches;

	private $query;

	/**
	 * Fuzzy constructor.
	 *
	 * @param array $tokens Incoming tokens.
	 * @param array $args   Arguments for partial match tokens.
	 * @since 4.0
	 */
	function __construct( array $partial_match_tokens, array $args ) {
		// FUTURE: $partial_match_tokens not used now, but could they be useful. @see PartialMatches@find where this class is instantiated for usage.
		// We want to ensure we're working with the original search string, not anything filtered.
		$this->partial   = $args['partial'];
		$keywords_tokens = $args['keyword_tokens'];
		$exact_matches   = $args['exact_matches'];
		$this->query     = $args['query'];

		// Remove exact match tokens.
		$this->exact_matches = $exact_matches;
		$this->keywords_tokens = array_diff( $keywords_tokens, $exact_matches );

		if ( ! empty( $this->keywords_tokens ) ) {
			$this->tokens = $this->get_fuzzy_tokens( $this->keywords_tokens );
		}
	}

	/**
	 * Finds the best fuzzy match for any non-exact token matches.
	 *
	 * @since 4.0
	 * @return array Revised suggested search based on best fuzzy match(es).
	 */
	public function did_you_mean() {
		$suggested_terms        = [];
		$original_search_tokens = Utils::tokenize( $this->partial->get_query()->get_keywords() )->get();

		foreach ( $original_search_tokens as $search_token ) {
			$suggested_term = '';
			$shortest       = -1;

			// If this is an exact match, put it in place and continue.
			if ( in_array( $search_token, $this->exact_matches, true ) ) {
				$suggested_terms[] = $search_token;
				continue;
			}

			// Loop through the fuzzy tokens to find the best match.
			foreach ( $this->tokens as $fuzzy_token ) {
				$lev = levenshtein( $fuzzy_token, $search_token );

				if ( $lev <= $shortest || $shortest < 0 ) {
					$suggested_term  = $fuzzy_token;
					$shortest = $lev;
				}
			}

			if ( ! empty( $suggested_term ) ) {
				$suggested_terms[] = $suggested_term;
			}

			$suggested_term = '';
			$shortest = -1;
		}

		// Tell the Query that we've made a suggestion.
		if ( ! empty( $suggested_terms ) && ! empty( array_diff( $suggested_terms, $original_search_tokens ) ) ) {
			$this->partial->get_query()->set_suggested_search( new Tokens( implode( ' ', $suggested_terms ) ) );
		}

		return $suggested_terms;
	}

	/**
	 * Finds fuzzy matches for tokens.
	 *
	 * @since 4.0
	 * @return array Tokens with fuzzy matches.
	 */
	public function find( array $partial_match_tokens, array $args ) {
		// Maybe bail out if finding fuzzy matches isn't applicable.
		if ( ! empty( $partial_match_tokens ) && ! apply_filters( 'searchwp\query\partial_matches\fuzzy\force', false, [
			'partial_match_tokens' => $partial_match_tokens,
			'query' => $args['query'],
		] ) ) {
			return $partial_match_tokens;
		}

		// Making it this far means partial matching is enabled and it didn't find anything yet (or fuzzy is forced).
		if ( ! apply_filters( 'searchwp\query\partial_matches\fuzzy', true, [
			'partial_match_tokens' => $partial_match_tokens,
			'query' => $args['query'],
		] ) ) {
			return $partial_match_tokens;
		}

		// If there were no fuzzy tokens found, there's nothing to do.
		if ( empty( $this->tokens ) ) {
			return $partial_match_tokens;
		}

		// Limit the tokens to those that meet the minimum similarity threshold.
		return $this->refine_fuzzy_tokens( $this->tokens, $this->keywords_tokens );
	}

	/**
	 * Refines (very) fuzzy tokens to only those that meet a minimum similarity threshold.
	 *
	 * @since 4.0
	 * @param array $fuzzy_tokens Very fuzzy tokens needing refinement.
	 * @param array $search_tokens Original search tokens.
	 * @return mixed Refined tokens that meet our minimum similarity threshold.
	 */
	private function refine_fuzzy_tokens( array $fuzzy_tokens, array $search_tokens ) {
		$threshold = absint( apply_filters( 'searchwp\query\partial_matches\fuzzy\threshold', 70 ) );

		$fuzzy_tokens = call_user_func_array(
			'array_merge',
			array_map(
				function( $search_term ) use ( $fuzzy_tokens, $threshold ) {
					return array_filter(
						$fuzzy_tokens,
						function( $fuzzy_token ) use ( $search_term, $threshold ) {
							similar_text(
								$fuzzy_token,
								$search_term,
								$similarity_percentage
							);

							return $similarity_percentage >= $threshold;
						}
					);
				},
				$search_tokens
			)
		);

		return $fuzzy_tokens;
	}

	/**
	 * Retrieves a (very) fuzzy set of potential matches.
	 *
	 * @since 4.0
	 * @param array $keywords_tokens Original search tokens.
	 * @return array Extremely fuzzy token matches.
	 */
	private function get_fuzzy_tokens( array $keywords_tokens ) {
		global $wpdb;

		$index = \SearchWP::$index;

		// FUTURE: Do stems need to be considered here, or is the SOUNDEX too far off anyway?
		return $wpdb->get_col( $wpdb->prepare(
			"SELECT token
			FROM {$index->get_tables()['tokens']->table_name}
			WHERE "
				. implode( ' OR ',
					array_fill( 0, count( $keywords_tokens ), "SOUNDEX(token) LIKE SOUNDEX(%s)" )
				) . " AND {$this->get_boundaries_sql()}",
			$keywords_tokens
		) );
	}

	/**
	 * Generates SQL clause to limit the length boundaries of tokens to find.
	 *
	 * @since 4.0
	 * @return string The gereated SQL clause.
	 */
	private function get_boundaries_sql() {
		global $wpdb;

		$min_length = absint( apply_filters(
			'searchwp\query\partial_matches\fuzzy\minimum_length',
			$this->partial->get_tokens()->get_minimum_length()
		) );

		$max_length = absint( apply_filters( 'searchwp\query\partial_matches\fuzzy\maximum_length', 0 ) );

		$boundaries = [];

		// Site limiter.
		if ( 'all' !== $this->query->get_args()['site'] ) {
			$index_table  = \SearchWP::$index->get_tables()['index']->table_name;
			$boundaries[] = $wpdb->prepare( "id IN (
				SELECT token
				FROM {$index_table}
				WHERE site IN (" .
					implode( ', ', array_fill( 0, count( $this->query->get_args()['site'] ), '%d' ) )
					. ') )',
				$this->query->get_args()['site'] );
		}

		if ( $min_length ) { $boundaries[] = "CHAR_LENGTH(token) >= {$min_length}"; }
		if ( $max_length ) { $boundaries[] = "CHAR_LENGTH(token) <= {$max_length}"; }

		return empty( $boundaries ) ? '' : '(' . implode( ' AND ', $boundaries ) . ')';
	}
}
