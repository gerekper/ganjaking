<?php

/**
 * SearchWP Posts Source.
 *
 * @package SearchWP
 * @author  SearchWP
 */

namespace SearchWP\Sources;

use SearchWP\Option;
use SearchWP\Source;
use SearchWP\Entry;
use SearchWP\Utils;

/**
 * Class Taxonomy is a Source for WP_Terms.
 *
 * @since 4.3.3
 */
class Taxonomy extends Source {

	/**
	 * The taxonomy name.
	 *
	 * @since 4.3.3
	 * @package SearchWP\Sources
	 * @var string
	 */
	private $taxonomy;

	/**
	 * Column name used to track index status.
	 *
	 * @since 4.3.3
	 * @var   string
	 */
	protected $db_id_column = 'term_id';

	/**
	 * Whether empty taxonomy terms should be excluded.
	 *
	 * @since 4.3.3
	 * @var boolean
	 */
	public $exclude_empty_terms = true;

	/**
	 * Constructor.
	 *
	 * @param string $taxonomy_name
	 * @since 4.3.3
	 */
	function __construct( string $taxonomy_name = 'category' ) {
		global $wpdb;

		$labels = get_taxonomy_labels( get_taxonomy( $taxonomy_name ) );

		$this->labels = [
			'plural'   => $labels->name,
			'singular' => $labels->singular_name,
		];

		$this->exclude_empty_terms = (bool) apply_filters( 'searchwp\source\taxonomy\exclude_empty_terms', true );

		$this->name       = 'taxonomy' . SEARCHWP_SEPARATOR . $taxonomy_name;
		$this->taxonomy   = $taxonomy_name;
		$this->db_table   = $wpdb->term_taxonomy;
		$this->attributes = $this->attributes();
		$this->rules      = $this->rules();
	}

	/**
	 * Restrict available WP_Terms to this taxonomy.
	 *
	 * @since 4.3.3
	 * @return array
	 */
	protected function db_where() {

		$db_where = [
			'relation' => 'AND',
			[   // Only include applicable taxonomy terms.
				'column'  => 'taxonomy',
				'value'   => $this->taxonomy,
			]
		];

		if ( $this->exclude_empty_terms ) {

			$db_where[] = [
				'column'  => 'count',
				'compare' => '!=',
				'value'   => '0'
			];
		}

		return apply_filters( 'searchwp\source\taxonomy\db_where', $db_where, [ 'source' => $this ] );
	}

	/**
	 * Defines the Attributes for this Source.
	 *
	 * @since 4.3.3
	 * @return array
	 */
	protected function attributes() {
		global $wpdb;

		return [
			[ // Term name
				'name'    => 'name',
				'label'   => __( 'Name', 'searchwp' ),
				'default' => Utils::get_max_engine_weight(),
				'data'    => function( $entry_id ) {
					return get_term_field( 'name', $entry_id );
				},
				'phrases' => 'name',
			],
			[ // Term description
				'name'    => 'description',
				'label'   => __( 'Description', 'searchwp' ),
				'default' => Utils::get_min_engine_weight(),
				'data'    => function( $entry_id ) {
					return get_term_field( 'description', $entry_id );
				},
				'phrases' => 'description',
			],
			[ // Term slug
				'name'    => 'slug',
				'label'   => __( 'Slug', 'searchwp' ),
				'default' => Utils::get_min_engine_weight(),
				'data'    => function( $entry_id ) {
					return get_term_field( 'slug', $entry_id );
				},
			],
			[ // Custom Fields
				'name'    => 'meta',
				'label'   => __( 'Custom Fields', 'searchwp' ),
				'notes'   => [
					__( 'Tip: Match multiple keys using * as wildcard and hitting Enter', 'searchwp' ),
				],
				'default' => Utils::get_min_engine_weight(),
				'options' => function( $search = false, array $include = [] ) {
					// If we're retrieving a specific set of options, get them and return.
					if ( ! empty( $include ) ) {
						return array_map( function( $meta_key ) {
							return new Option( (string) $meta_key );
						}, $include );
					}

					return array_map( function( $meta_key ) {
						return new Option( $meta_key );
					}, Utils::get_meta_keys_for_tax_terms( $search ) );
				},
				'allow_custom' => true,
				'data'    => function( $entry_id, $meta_key ) {
					return get_term_meta( $entry_id, $meta_key, false );
				},
				'phrases' => [ [
					'table'  => $wpdb->termmeta,
					'column' => 'meta_value',
					'id'     => 'term_id'
				] ],
			]
		];
	}

	/**
	 * Defines the Rules for this Source.
	 *
	 * @since 4.3.3
	 * @return array
	 */
	protected function rules() {
		return [
			[	// ID.
				'name'        => 'term_id',
				'label'       => __( 'ID', 'searchwp' ),
				'options'     => false,
				'conditions'  => [ 'IN', 'NOT IN' ],
				'application' => function( $properties ) {
					global $wpdb;

					$condition = 'NOT IN' === $properties['condition'] ? 'NOT IN' : 'IN';
					$ids = explode( ',', Utils::get_integer_csv_string_from( $properties['value'] ) );

					return $wpdb->prepare( "SELECT term_id FROM {$wpdb->terms} WHERE term_id {$condition}  ("
						. implode( ',', array_fill( 0, count( $ids ), '%s' ) )
						. ')', $ids );
				},
			]
		];
	}

	/**
	 * Weight Transfer Option options.
	 *
	 * @since 4.3.3
	 * @return array
	 */
	protected function weight_transfer_options() {
		
		return [ [
			'option' => new Option( 'id', sprintf(
			// Translators: placeholder is singular taxonomy label.
				__( 'To %s ID', 'searchwp' ),
				$this->labels['singular']
			) ),
			'source_map' => function( $args ) {
				global $wpdb;

				$taxonomy = $this->taxonomy;

				do_action( 'searchwp\debug\log', "Transferring {$this->get_name()} weight to {$taxonomy}:{$args['id']}", 'source' );

				return $wpdb->prepare( '%s', $this->name );
			}
		] ];
	}

	/**
	 * Maps an Entry for this Source to its native model.
	 *
	 * @since  4.3.3
	 * @param Entry   $entry The Entry
	 * @param Boolean $doing_query Whether a query is being run
	 * @return \WP_Term
	 */
	public function entry( Entry $entry, $doing_query = false ) {
		return get_term( $entry->get_id() );
	}

	/**
	 * Add class hooks.
	 *
	 * @since 4.3.3
	 * @param array $params Parameters.
	 * @return array
	 */
	public function add_hooks( array $params = [] ) {
		if ( ! has_action( 'edited_term', [ $this, 'drop' ] ) ) {
			add_action( "edited_{$this->taxonomy}", [ $this, 'drop' ], 10, 1 );
		}

		if ( ! has_action( 'delete_term', [ $this, 'drop' ] ) ) {
			add_action( "delete_{$this->taxonomy}", [ $this, 'drop' ], 10, 1 );
		}
	}

	/**
	 * Callback to drop a Taxonomy Term from the index.
	 *
	 * @since 4.3.3
	 * @param $entry_id
	 */
	public function drop( $entry_id ) {

		// Drop this entry from the index.
		\SearchWP::$index->drop( $this, $entry_id );
	}
}
