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
		$this->index         = \SearchWP::$index;
		$this->query         = $query;
		$this->tokens        = new Tokens( $this->query->get_keywords() );
		$keywords_tokens     = array_unique( array_merge( $tokens, $this->tokens->get() ) );
		$this->exact_matches = $this->index->has_tokens( $keywords_tokens, array_keys( $engine->get_sources() ), $query->get_args()['site'] );
		$values              = [];
		$excluded            = '1=1';
		$this->force         = apply_filters( 'searchwp\query\partial_matches\force', $this->force, [
			'tokens'        => $this->tokens,
			'query'         => $this->query,
			'exact_matches' => $this->exact_matches,
		] );

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

		// If no partials were found with no wildcard before, maybe add a wildcard before and try again.
		if (
			$this->force
			|| (
				empty( $partial_tokens )
				&& ! $this->use_wildcard_before()
				&& apply_filters( 'searchwp\query\partial_matches\adaptive', true, [
					'tokens'   => $this->tokens,
					'query'    => $this->query,
					'partials' => $partial_tokens,
				] )
			)
		) {
			$partials       = $this->prepare_tokens( $keywords_tokens, true );
			$values         = array_merge( $values_before, $partials ); // Need to undo earlier merge.
			$partial_tokens = $this->get_partial_tokens( $partials, $excluded, $values );
		}

		$partial_args   = [
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
			return array_merge( $tokens, $partial_tokens, $this->exact_matches );
		}

		if ( ! apply_filters( 'searchwp\query\partial_matches\fuzzy', true ) ) {
			return $tokens;
		}

		// Integrate fuzzy matching.
		$fuzzy = new FuzzyMatches( $partial_tokens, $partial_args );

		// Give priority to "Did you mean?" as it will essentially short circut fuzzy match finding.
		if ( apply_filters( 'searchwp\query\partial_matches\did_you_mean', true, [
			'tokens' => $this->tokens,
			'query'  => $this->query,
		] ) ) {
			// Remove the exact match buoy because we're making an automatic suggestion.
			remove_filter( 'searchwp\query\mods', [ $this, 'exact_match_buoy' ], 5 );

			return $fuzzy->did_you_mean();
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

		$col = $this->query->use_stems ? 'stem' : 'token';

		return $wpdb->get_col( $wpdb->prepare(
			"SELECT {$col}
			FROM {$this->index->get_tables()['tokens']->table_name}
			WHERE {$excluded}
				{$this->get_boundaries_sql()}
				AND " . implode( ' OR ', array_fill( 0, count( $partials ), "{$col} LIKE %s" ) ),
			$values
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
		if ( empty( $this->exact_matches ) || ! apply_filters( 'searchwp\query\partial_matches\buoy', false, [
			'tokens' => $this->tokens,
			'query'  => $this->query,
		] ) ) {
			return $mods;
		}

		$alias = $this->index->get_alias();
		$tokens_table = $this->index->get_tables()['tokens']->table_name;

		$mod = new Mod();
		$mod->column_as( "(SELECT count(token) FROM {$tokens_table} WHERE {$alias}.token IN("
			. implode( ',',
				array_map( 'absint', array_keys( $this->exact_matches ) ) )
			. ") GROUP BY {$alias}.site, {$alias}.source, {$alias}.attribute, {$alias}.id)",
			'searchwp_exact_matches' );
		$mod->order_by( "{$alias}.searchwp_exact_matches+0", 'DESC', 5 );
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
