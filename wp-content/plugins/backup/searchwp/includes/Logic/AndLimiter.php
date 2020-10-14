<?php

/**
 * SearchWP AND Limiter.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Logic;

use SearchWP\Query;

/**
 * Class AndLimiter is responsible for generating an AND logic clause.
 *
 * @since 4.0
 */
class AndLimiter {

	/**
	 * Query for the limiter
	 *
	 * @since 4.0
	 * @var Query
	 */
	private $query;

	/**
	 * AndLimiter constructor.
	 *
	 * @since 4.0
	 */
	function __construct( Query $query ) {
		$this->query = $query;
	}

	/**
	 * Generates and returns the SQL clause for AND logic.
	 *
	 * @since 4.0
	 * @return string
	 */
	public function get_sql() {
		global $wpdb;

		$index   = \SearchWP::$index;
		$args    = $this->query->get_args();
		$tokens  = $this->query->get_tokens();
		$site_in = $this->query->get_site_limit_sql();

		// AND logic is based on token groups. In order for AND logic to be satisfied there
		// must be a match for all token groups. A token group consists of a token and its
		// keyword stem and any partial matches when applicable.
		$token_groups = array_map( function( $token ) {
			return [ $token ];
		}, array_keys( $tokens ) );

		// Group tokens based on stemming/partial matches if applicable.
		if ( $this->query->use_stems ) {
			$token_groups = $index->group_tokens_by_stem_from_tokens( array_keys( $tokens ) );
		}

		// If we're dealing with partial matches we can further group the groups.
		if ( apply_filters( 'searchwp\query\partial_matches', \SearchWP\Settings::get_single( 'partial_matches', 'boolean' ), [
			'tokens' => $tokens,
			'query'  => $this->query,
		] ) ) {
			// Rebuild the token groups based on partial matches from the original search string.
			$raw_token_groups = $token_groups;
			$token_groups     = [];

			$original_search_tokens = \SearchWP\Utils::tokenize( $this->query->get_keywords() )->get();

			foreach ( $original_search_tokens as $token ) {
				foreach ( $raw_token_groups as $raw_token_group_tokens ) {
					foreach ( $raw_token_group_tokens as $raw_token_group_token ) {
						if ( false !== stripos( $tokens[ $raw_token_group_token ], $token ) ) {
							if ( ! array_key_exists( $token, $token_groups ) ) {
								$token_groups[ $token ] = [];
							}

							$token_groups[ $token ] = array_unique( array_merge(
								(array) $token_groups[ $token ],
								$raw_token_group_tokens
							) );
						}
					}
				}
			}
		}

		if ( count( $token_groups ) < 2 ) {
			return '';
		}

		$token_groups = array_values( $token_groups );

		do_action( 'searchwp\debug\log', 'Trying AND logic', 'query' );

		// These limiters build on one another, and piggyback a parent condition for the first token
		// in the array, which is why the $token_limiters is off by one; we need that 'parent' clause
		// to establish the AND logic limiter itself, so we're structuring the children first.
		$token_limiters = implode( ' AND ', array_map( function( $token_group ) use ( $index, $site_in ) {
			return "id IN (
				SELECT id
				FROM {$index->get_tables()['index']->table_name} s
				WHERE {$site_in}
					AND token IN ("
					. implode( ', ', array_fill( 0, count( $token_group ), '%d' ) )
					. ') GROUP BY id)';
		}, array_slice( $token_groups, 1 ) ) ); // Off by one because the 'parent' below uses that.

		// Build values array for preparation.
		$values = call_user_func_array( 'array_merge', array_map( function( $tokens ) use ( $args ) {
			return array_merge( $args['site'], $tokens );
		}, $token_groups ) );

		// We are going to prepare this clause because it may be dropped if AND logic fails.
		// We don't want to have to somehow track which values should also be removed in that case.
		return "s.id IN (
			SELECT id
			FROM (" .
				$wpdb->prepare("
					SELECT id
					FROM {$index->get_tables()['index']->table_name} s
					WHERE {$site_in}
						AND token IN ("
							. implode( ', ', array_fill( 0, count( $token_groups[0] ), '%d' ) )
						. ")
						AND {$token_limiters}
					GROUP BY id",
					$values
				)
			. ") AS a
			)";
	}
}
