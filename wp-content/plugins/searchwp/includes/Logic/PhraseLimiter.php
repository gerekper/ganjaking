<?php

/**
 * SearchWP Phrase Limiter.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Logic;

use SearchWP\Utils;
use SearchWP\Query;
use SearchWP\Tokens;
use SearchWP\Attribute;

/**
 * Class PhraseLimiter is responsible for generating a phrase logic clause.
 *
 * @since 4.0
 */
class PhraseLimiter {

	/**
	 * Query for the limiter.
	 *
	 * @since 4.0
	 * @var Query
	 */
	private $query;

	/**
	 * Phrases to consider.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $phrases;

	// These are placeholder vars for Issue #329.
	// @link https://github.com/searchwp/searchwp/issues/329
	private $do_and_logic;
	private $and_logic_strict;
	private $and_logic_tokens = [];

	/**
	 * Index
	 *
	 * @since 4.0
	 * @var Index
	 */
	private $index;

	/**
	 * Attributes that support phrases.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $attributes;

	/**
	 * SQL values storage.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $values;

	/**
	 * PhraseLimiter constructor.
	 *
	 * @since 4.0
	 */
	function __construct( Query $query, $do_and_logic = true, $and_logic_strict = false ) {
		$this->query   = $query;
		$this->phrases = Utils::get_phrases_from_string( $query->get_keywords() );
		$this->index   = \SearchWP::$index;

		$this->do_and_logic     = $do_and_logic;
		$this->and_logic_strict = $and_logic_strict;
		$this->and_logic_tokens = array_filter( explode( ' ', trim(
			str_ireplace( array_merge( $this->phrases, [ '"' ] ), '', $query->get_keywords() )
		) ) );

		if ( empty( $this->and_logic_tokens ) ) {
			$this->do_and_logic = false;
		}

		$this->parse_sources_attributes();
	}

	/**
	 * Generate the (prepared) SQL for Phrase logic.
	 *
	 * @since 4.0
	 * @return false|string
	 */
	public function get_sql() {
		global $wpdb;

		$phrase_results = $this->get_entries();

		// If there are no phrase results, bail out. Returning false will progress to the next algorithm.
		if ( empty( $phrase_results ) ) {
			return false;
		}

		do_action( 'searchwp\debug\log', 'Performing phrase search', 'query' );

		$clauses = [];
		foreach ( $phrase_results as $source_name => $ids ) {
			$clauses[] = $wpdb->prepare(
				"(s.source = %s AND s.id IN (" . implode( ', ', array_fill( 0, count( $ids ), '%s' ) ) . '))',
				array_merge( [ $source_name ], $ids )
			);
		}

		return ! empty( $clauses ) ? '(' . implode( ' OR ', $clauses ) . ')' : '';
	}

	/**
	 * Finds IDs that have our phrases.
	 *
	 * @since 4.0
	 * @return string
	 */
	public function get_entries() {
		global $wpdb;

		$results = [];
		$site_in = '1=1';

		if ( 'all' !== $this->query->get_args()['site'] ) {
			$site_in = $this->query->get_site_limit_sql();
		}

		foreach( $this->group_sources_attributes() as $source_name => $source_phrases ) {
			$subqueries   = [];
			$attributes   = [];
			$this->values = [];
			$attribute_values = [];

			foreach ( $source_phrases as $table => $ids ) {
				foreach ( $ids as $id => $columns ) {
					$wheres = [];

					foreach ( $columns as $column => $source_attribute ) {
						$wheres[] = implode( ' OR ', array_map( function( $phrase ) use ( $column, $source_attribute, $wpdb ) {
							$this->values[] = '%' . $wpdb->esc_like( trim( $phrase ) ) . '%';

							// If this attribute is a meta_value, we must include the meta_keys on the sql as well.
							if ( $column === 'meta_value' ) {
								if ( ! empty( $source_attribute['value'] ) && is_array( $source_attribute['value'] ) ) {
									$meta_keys = array_key_exists( '*', $source_attribute['value'] ) ? [] : array_keys( $source_attribute['value'] );

									if ( ! empty( $meta_keys ) ) {

										$meta_key_clause = implode( ' OR ', array_map( function( $meta_key ) {
											// Replace wildcard with SQL wildcard.
											$meta_key = str_replace( '*', '%', $meta_key );

											return "meta_key LIKE '{$meta_key}'";
										}, $meta_keys ) );

										return "{$column} LIKE %s AND ({$meta_key_clause})";
									}
								}
							}

							// Default sql for all other cases.
							return "{$column} LIKE %s";
						}, $this->phrases ) );

						if ( $source_attribute['attribute']->get_options() ) {
							$attributes[]       = 's.attribute LIKE %s';
							$attribute_values[] = $wpdb->esc_like( $source_attribute['attribute']->get_name() . SEARCHWP_SEPARATOR ) . '%';
						} else {
							$attributes[]       = 's.attribute = %s';
							$attribute_values[] = $source_attribute['attribute']->get_name();
						}
					}

					$subqueries[] = "SELECT {$id} AS id FROM {$table} WHERE 1=1 AND (" . implode( ' OR ', $wheres ) . ')';
				}
			}

			$sql = "
				SELECT p.id
				FROM (" . implode( ' UNION ', $subqueries ) . ") AS p
				LEFT JOIN {$this->index->get_tables()['index']->table_name} s ON s.id = p.id
				WHERE {$site_in}
					AND s.source = %s
					AND (" . implode( ' OR ', $attributes ) . ")
				GROUP BY id";

			if ( 'all' !== $this->query->get_args()['site'] ) {
				$this->values = array_merge(
					$this->values,
					$this->query->get_args()['site'],
					[ $source_name ],
					$attribute_values
				);
			} else {
				$this->values = array_merge(
					$this->values,
					[ $source_name ],
					$attribute_values
				);
			}

			$sql = $wpdb->prepare( $sql, $this->values );

			$time_start              = microtime( true );
			$results[ $source_name ] = $wpdb->get_col( $sql );
			$time_finish             = number_format( microtime( true ) - $time_start, 5 );

			$source_name_undot = str_replace( '.', '_', $source_name );
			$this->query->set_debug_data( 'subqueries.phrase.queries.' . $source_name_undot, $sql );
			$this->query->set_debug_data( 'subqueries.phrase.times.' . $source_name_undot, $time_finish );
			$this->query->set_debug_data( 'subqueries.phrase.results.' . $source_name_undot, $results[ $source_name ] );
		}

		$results = array_filter( $results );

		// Apply AND logic if applicable.
		if ( ! empty( $results ) && $this->do_and_logic ) {
			$mods = [];

			foreach ( $results as $source_name => $ids ) {
				$mod = new \SearchWP\Mod( $source_name );
				$mod->set_where( [ [
					'column'  => 'id',
					'value'   => $ids,
					'compare' => 'IN',
				] ] );

				$mods[] = $mod;
			}

			// Prevent recursion caused by quoted/phrase searches set by synonyms.
			add_filter( 'searchwp\synonyms', '__return_empty_array' );

			$and_logic_query = new \SearchWP\Query( implode( ' ', $this->and_logic_tokens ), [
				'caller_id' => $this->query->get_id(),
				'engine'    => $this->query->get_engine()->get_name(),
				'per_page'  => -1,
				'mods'      => $mods,
			] );

			remove_filter( 'searchwp\synonyms', '__return_empty_array' );

			if (
				empty( $and_logic_query->get_results() )
				&& apply_filters( 'searchwp\query\logic\phrase\and_logic_strict', $this->and_logic_strict )
			) {
				$results = [];
			} elseif ( ! empty( $and_logic_query->get_results() ) ) {
				$and_logic_results_normalized = [];
				foreach ( $and_logic_query->get_results() as $and_logic_result ) {
					if ( ! array_key_exists( $and_logic_result->source, $and_logic_results_normalized ) ) {
						$and_logic_results_normalized[ $and_logic_result->source ] = [];
					}

					$and_logic_results_normalized[ $and_logic_result->source ][] = $and_logic_result->id;
				}

				if ( ! empty( $and_logic_results_normalized ) ) {
					$results = $and_logic_results_normalized;
				}
			}
		}

		// If there were no results and we're not strict about that, tell the Query the search is revised.
		if ( empty( $results ) && ! apply_filters( 'searchwp\query\logic\phrase\strict', false ) ) {
			$this->query->set_suggested_search(
				new Tokens( str_replace( '"', '', $this->query->get_keywords() ) )
			);
		}

		return $results;
	}

	/**
	 * Groups Phrases configs into associative array of tables and columns.
	 *
	 * @since 4.0
	 * @return array
	 */
	private function group_sources_attributes() {
		$groups = [];

		foreach ( $this->attributes as $source_name => $attributes ) {
			if ( ! array_key_exists( $source_name, $groups ) ) {
				$groups[ $source_name ] = [];
			}

			foreach ( $attributes as $attribute => $phrase_cols ) {
				foreach ( $phrase_cols as $phrase_col ) {
					if ( ! array_key_exists( $phrase_col['table'], $groups[ $source_name ] ) ) {
						$groups[ $source_name ][ $phrase_col['table'] ] = [];
					}

					if ( ! array_key_exists( $phrase_col['id'], $groups[ $source_name ][ $phrase_col['table'] ] ) ) {
						$groups[ $source_name ][ $phrase_col['table'] ][ $phrase_col['id'] ] = [];
					}

					if ( ! array_key_exists( $phrase_col['column'], $groups[ $source_name ][ $phrase_col['table'] ][ $phrase_col['id'] ] ) ) {
						$groups[ $source_name ][ $phrase_col['table'] ][ $phrase_col['id'] ][ $phrase_col['column'] ] = [];
					}

					$source = $this->index->get_source_by_name( $source_name );
					$groups[ $source_name ][ $phrase_col['table'] ][ $phrase_col['id'] ][ $phrase_col['column'] ] = [
						'attribute' => $source->get_attribute( $attribute ),
						'value'     => $phrase_col['value']
					];
				}
			}
		}

		return $groups;
	}

	/**
	 * Retrieves Source Attributes that have Phrases support.
	 *
	 * @since 4.0
	 * @return array
	 */
	private function parse_sources_attributes() {
		foreach ( $this->query->get_engine()->get_sources() as $source ) {
			$applicable_source_attributes = array_filter(
				array_map( function( Attribute $attribute ) use ( $source ) {
					$phrases = $attribute->get_phrases();
					$settings = $attribute->get_settings();

					if ( is_string( $phrases ) ) {
						$table  = $source->get_db_table();

						if ( ! Utils::valid_db_column( $table, $phrases ) ) {
							return false;
						}

						$phrases = [ [
							'table'  => $source->get_db_table(),
							'column' => $phrases,
							'value'  => $settings,
							'id'     => $source->get_db_id_column(),
						] ];
					}
					else if( is_array( $phrases ) ){
						$phrases[0]['value'] = $settings;
					}

					// We need to verify that the Attribute with phrase support has been added to the engine.
					return $phrases && ! empty( $attribute->get_settings() ) ? $phrases : false;
				}, $source->get_attributes() )
			);

			if ( ! empty( $applicable_source_attributes ) ) {
				$this->attributes[ $source->get_name() ] = $applicable_source_attributes;
			}
		}
	}
}
