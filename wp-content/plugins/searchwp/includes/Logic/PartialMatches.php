<?php

/**
 * SearchWP partial matches logic.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Logic;

use SearchWP\Mod;
use SearchWP\Query;
use SearchWP\Engine;
use SearchWP\Tokens;
use SearchWP\Settings;
use SearchWP\Logic\FuzzyMatches;

/**
 * Class PartialMatches is responsible for finding partial matches for search strings.
 *
 * @since 4.0
 */
class PartialMatches {

	/**
	 * Index
	 *
	 * @since 4.0
	 * @var Index
	 */
	private $index;

	/**
	 * Query
	 *
	 * @since 4.0
	 * @var Query
	 */
	private $query;

	/**
	 * Tokens
	 *
	 * @since 4.0
	 * @var Tokens
	 */
	private $tokens;

	/**
	 * Exact token matches
	 *
	 * @since 4.0
	 * @var array
	 */
	private $exact_matches = [];

	/**
	 * Whether to force partial matches.
	 *
	 * @since 4.0
	 * @var false
	 */
	private $force = false;

	/**
	 * Partial constructor.
	 *
	 * @since 4.0
	 */
	function __construct() {

		add_filter( 'searchwp\query\tokens', [ $this, 'find' ], 5, 2 );
	}

	/**
	 * Finds partial matches for tokens.
	 *
	 * @since 4.0
	 * @param array $tokens Incoming tokens.
	 * @param Query $query  The Query being run.
	 * @return array Tokens with partial matches.
	 */
	public function find( array $tokens, Query $query ) {

		$query->set_debug_data( 'tokens.partial_matches.before', $tokens );
		$results = $this->find_tokens( $tokens, $query );
		$query->set_debug_data( 'tokens.partial_matches.after', $results );

		return $results;
	}

	/**
	 * Finds partial matches for tokens.
	 *
	 * @since 4.2.9
	 *
	 * @param array $tokens Incoming tokens.
	 * @param Query $query  The Query being run.
	 *
	 * @return array Tokens with partial matches.
	 */
	private function find_tokens( array $tokens, Query $query ) {

		if ( ! apply_filters( 'searchwp\query\partial_matches', Settings::get_single( 'partial_matches', 'boolean' ), [
			'tokens' => $tokens,
			'query'  => $query,
		] ) ) {
			return $tokens;
		}

		$engine = $query->get_args()['engine'] instanceof Engine
			? $query->get_args()['engine']
			: new Engine( $query->get_args()['engine'] );

		// We want to ensure we're working with the original search string, not anything filtered.
		$this->index  = \SearchWP::$index;
		$this->query  = $query;
		$this->tokens = new Tokens( $this->query->get_keywords() );

		$keywords_tokens = array_unique( array_merge( $tokens, $this->tokens->get() ) );

		$this->exact_matches = $this->index->has_tokens(
			$keywords_tokens,
			array_keys( $engine->get_sources() ),
			$query->get_args()['site']
		);

		$keywords_tokens = apply_filters( 'searchwp\query\partial_matches\keywords', $keywords_tokens, [
			'tokens'        => $this->tokens,
			'query'         => $this->query,
			'exact_matches' => $this->exact_matches,
		] );

		$values      = [];
		$excluded    = '1=1';
		$this->force = apply_filters( 'searchwp\query\partial_matches\force', $this->force, [
			'tokens'        => $this->tokens,
			'query'         => $this->query,
			'exact_matches' => $this->exact_matches,
		] );

		// If the search string contains only exact matches, we can bail out.
		if ( apply_filters( 'searchwp\query\partial_matches\strict', ! $this->force ) ) {
			if ( empty( array_diff( array_values( $tokens ), array_values( $this->exact_matches ) ) ) ) {
				return $tokens;
			}
		}

		if ( ! $this->force ) {
			// Remove exact matches from consideration.
			$keywords_tokens = array_diff( $keywords_tokens, $this->exact_matches );

			// Exclude exact matches from being returned.
			if ( ! empty( $this->exact_matches ) ) {
				$excluded = 'token NOT IN (' . implode( ',', array_fill( 0, count( $this->exact_matches ), '%s' ) ) . ')';
				$values   = $this->exact_matches;
			}
		}

		// If there are no tokens to work with, bail out.
		if ( empty( $keywords_tokens ) ) {
			return $tokens;
		}

		// Find partial match tokens.
		$values_before  = $values;
		$partials       = $this->prepare_tokens( $keywords_tokens );
		$values         = array_merge( $values, $partials );
		$partial_tokens = $this->get_partial_tokens( $partials, $excluded, $values );

		// If a wildcard after found no results, should we adapt by adding a wildcard before?
		$adaptive = apply_filters( 'searchwp\query\partial_matches\adaptive', true, [
			'tokens'   => $this->tokens,
			'query'    => $this->query,
			'partials' => $partial_tokens,
		] );

		// If no partials were found with no wildcard before, add a wildcard before and try again if we're adapting.
		if ( empty( $partial_tokens ) && $adaptive ) {
			$partials       = $this->prepare_tokens( $keywords_tokens, true );
			$values         = array_merge( $values_before, $partials ); // Need to undo earlier merge.
			$partial_tokens = $this->get_partial_tokens( $partials, $excluded, $values );
		}

		$partial_args = [
			'keyword_tokens' => $keywords_tokens,
			'exact_matches'  => $this->exact_matches,
			'query'          => $query,
			'partial'        => $this,
		];

		// We also want to give exact matches a buoy.
		add_filter( 'searchwp\query\mods', [ $this, 'exact_match_buoy' ], 5, 2 );

		// If we found partial matches (and aren't forcing fuzzy matches despite that) return them.
		if ( ! empty( $partial_tokens ) && ! apply_filters( 'searchwp\query\partial_matches\fuzzy\force', false, [
			'tokens'   => $this->tokens,
			'query'    => $this->query,
			'partials' => $partial_tokens,
		] ) ) {
			return array_unique( array_merge(
				$this->get_original_tokens( $tokens, $partial_tokens ),
				$partial_tokens,
				$this->exact_matches
			) );
		}

		// There were no partial matches so return the original tokens.
		if ( ! apply_filters( 'searchwp\query\partial_matches\fuzzy', true ) ) {
			return $tokens;
		}

		// Integrate fuzzy matching.
		$fuzzy = new FuzzyMatches( $partial_tokens, $partial_args );

		// Give priority to "Did you mean?" as it will essentially short circuit fuzzy match finding.
		if ( apply_filters( 'searchwp\query\partial_matches\did_you_mean', \SearchWP\Settings::get( 'do_suggestions', 'boolean' ), [
			'tokens' => $this->tokens,
			'query'  => $this->query,
		] ) ) {
			// Remove the exact match buoy because we're making an automatic suggestion.
			remove_filter( 'searchwp\query\mods', [ $this, 'exact_match_buoy' ], 5 );

			$did_you_mean_matches = $fuzzy->did_you_mean();

			// If the "Did you mean?" feature found no tokens we should return the original tokens.
			return ! empty( $did_you_mean_matches ) ? $did_you_mean_matches : $tokens;
		} else {
			// Passive fuzzy matches.
			add_filter( 'searchwp\query\partial_matches\tokens', [ $fuzzy, 'find' ], 9, 2 );
		}

		// Allow for additional token finding logic.
		$partial_tokens = apply_filters( 'searchwp\query\partial_matches\tokens', $partial_tokens, [
			'args'   => $partial_args,
			'tokens' => $this->tokens,
			'query'  => $this->query,
		] );

		return array_merge( $tokens, $partial_tokens, $this->exact_matches );
	}

	/**
	 * Control whether the invalid original tokens are returned. Check to see if each
	 * token is included in the found partial matches and if it's not it is an invalid
	 * token and will be removed unless the developer wants otherwise with the hook.
	 *
	 * @since 4.1.5
	 * @param mixed $tokens
	 * @param mixed $partial_tokens
	 * @return array
	 */
	private function get_original_tokens( $tokens, $partial_tokens ) {
		if ( apply_filters( 'searchwp\query\partial_matches\remove_invalid_tokens', true ) ) {
			$tokens = array_filter( array_map( function( $token ) use ( $partial_tokens ) {
				return in_array( $token, $partial_tokens, true ) ? $token : false;
			}, $tokens ) );
		}

		return $tokens;
	}

	/**
	 * Retrieves tokens for the submitted partial strings.
	 *
	 * @since 4.0
	 * @param array $partials Strings that need partial matches.
	 * @param string $excluded Strings to exclude (e.g. exact matches).
	 * @param array $values Values to prepare.
	 * @return array Tokens for partial matches of submitted $partials strings.
	 */
	private function get_partial_tokens( array $partials, string $excluded, array $values ) {
		global $wpdb;

		$sql = "SELECT token
			FROM {$this->index->get_tables()['tokens']->table_name}
			WHERE {$excluded}
				{$this->get_boundaries_sql()}
				AND (" . implode( ' OR ', array_fill( 0, count( $partials ), "token LIKE %s" ) ) . ")";

		$sql = $wpdb->prepare( $sql, $values );

		$time_start  = microtime( true );
		$results     = $wpdb->get_col( $sql );
		$time_finish = number_format( microtime( true ) - $time_start, 5 );

		$this->query->set_debug_data( 'tokens.partial_matches.query.sql', $sql );
		$this->query->set_debug_data( 'tokens.partial_matches.query.time', $time_finish );
		$this->query->set_debug_data( 'tokens.partial_matches.query.results', $results );

		return $results;
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
			'searchwp\query\partial_matches\minimum_length',
			$this->tokens->get_minimum_length(),
		[
			'tokens' => $this->tokens,
			'query'  => $this->query,
		] ) );

		$max_length = absint( apply_filters( 'searchwp\query\partial_matches\maximum_length', 0, [
			'tokens' => $this->tokens,
			'query'  => $this->query,
		] ) );

		$boundaries = [];

		// Site limiter.
		if ( 'all' !== $this->query->get_args()['site'] ) {
			$boundaries[] = $wpdb->prepare( "id IN (
				SELECT token
				FROM {$this->index->get_tables()['index']->table_name}
				WHERE site IN (" .
					implode( ', ', array_fill( 0, count( $this->query->get_args()['site'] ), '%d' ) )
					. ') )',
				$this->query->get_args()['site'] );
		}

		if ( $min_length ) { $boundaries[] = "CHAR_LENGTH(token) >= {$min_length}"; }
		if ( $max_length ) { $boundaries[] = "CHAR_LENGTH(token) <= {$max_length}"; }

		return empty( $boundaries ) ? '' : ' AND (' . implode( ' AND ', $boundaries ) . ')';
	}

	/**
	 * Prepares the tokens.
	 *
	 * @since 4.0
	 * @param array $tokens Tokens to prepare.
	 * @return array Prepared tokens.
	 */
	private function prepare_tokens( array $tokens, $force_wildcards = false ) {
		global $wpdb;

		return array_map( function( $token ) use ( $wpdb, $force_wildcards ) {
			$wildcard_before = $this->use_wildcard_before() || $force_wildcards ? '%' : '';

			$wildcard_after  = apply_filters( 'searchwp\query\partial_matches\wildcard_after', true, [
				'tokens' => $this->tokens,
				'query'  => $this->query,
			] ) || $force_wildcards ? '%' : '';

			return $wildcard_before . $wpdb->esc_like( $token ) . $wildcard_after;
		}, $tokens );
	}

	/**
	 * Whether to use a wildcard before tokens.
	 *
	 * @since 4.0
	 * @return boolean
	 */
	public function use_wildcard_before() {
		return apply_filters( 'searchwp\query\partial_matches\wildcard_before', false, [
			'tokens' => $this->tokens,
			'query'  => $this->query,
		] );
	}

	/**
	 * Adds a buoy to ensure exact matches rank first.
	 *
	 * @since 4.0
	 * @param array $mods Incoming Mods.
	 * @param array $args Query arguments.
	 * @return array Mods with our buoy.
	 */
	public function exact_match_buoy( array $mods, Query $query ) {
		// If there are no exact matches bail out else we'll have an Error.
		if ( empty( $this->exact_matches ) || ! apply_filters( 'searchwp\query\partial_matches\buoy', true, [
			'tokens' => $this->tokens,
			'query'  => $this->query,
		] ) ) {
			return $mods;
		}

		$alias = $this->index->get_alias();
		$index_table  = $this->index->get_tables()['index']->table_name;

		$mod = new Mod();
		$mod->column_as( "(
			SELECT SUM({$index_table}.occurrences)
			FROM {$index_table}
			WHERE
				{$index_table}.id = {$alias}.id
				AND {$index_table}.site = {$alias}.site
				AND {$index_table}.source = {$alias}.source
				AND {$index_table}.token IN(" . implode( ',',
					array_map( 'absint', array_keys( $this->exact_matches ) ) )
				. ")
			GROUP BY {$index_table}.id
			)",
			'searchwp_exacts' );
		$mod->order_by( "searchwp_exacts+0", 'DESC', 5 );

		$mods[] = $mod;

		return $mods;
	}

	/**
	 * Getter for Query.
	 *
	 * @since 4.0
	 * @return SearchWP\Query
	 */
	public function get_query() {
		return $this->query;
	}

	/**
	 * Getter for Tokens.
	 *
	 * @since 4.0
	 * @return SearchWP\Tokens
	 */
	public function get_tokens() {
		return $this->tokens;
	}
}
