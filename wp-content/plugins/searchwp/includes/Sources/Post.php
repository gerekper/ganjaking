<?php

/**
 * SearchWP Posts Source.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Sources;

use SearchWP\Utils;
use SearchWP\Entry;
use SearchWP\Query;
use SearchWP\Source;
use SearchWP\Option;
use SearchWP\Notice;
use SearchWP\Settings;
use SearchWP\Highlighter;

/**
 * Class Post is a Source for WP_Post objects.
 *
 * @since 4.0
 */
class Post extends Source {

	/**
	 * The post type name.
	 *
	 * @since 4.0
	 * @package SearchWP\Sources
	 * @var string
	 */
	private $post_type;

	/**
	 * Column name used to track index status.
	 *
	 * @since 4.0
	 * @var   string
	 */
	protected $db_id_column = 'ID';

	/**
	 * Column used for post parent ID.
	 *
	 * @since 4.1
	 * @var   string
	 */
	protected $db_post_parent_column = 'post_parent';

	/**
	 * Allowed Ajax requests for post edit
	 *
	 * @since 4.3.8
	 * @var string[]
	 */
	protected $allowed_ajax_edits = ['inline-save'];

	/**
	 * Constructor.
	 *
	 * @since 4.0
	 */
	function __construct( string $post_type = 'post' ) {
		global $wpdb, $wp_query;

		$post_type_model = get_post_type_object( $post_type );

		if ( is_null( $post_type_model ) && ( isset( $wp_query ) && is_search() ) ) {
			wp_die(
				__( 'Invalid post type for SearchWP Source Post:', 'searchwp' ) . ' <code>' . esc_html( $post_type ) . '</code>',
				__( 'SearchWP Source Error', 'searchwp' )
			);
		}

		if ( ! $post_type_model instanceof \WP_Post_type ) {
			do_action( 'searchwp\debug\log', "Invalid post type for SearchWP Source Post:", 'source' );
			do_action( 'searchwp\debug\log', print_r( $post_type, true ), 'source' );

			if ( current_user_can( \SearchWP\Settings::get_capability() ) ) {
				wp_die(
					__( 'Invalid post type for SearchWP Source Post:', 'searchwp' ) . ' <code>' . esc_html( $post_type ) . '</code>',
					__( 'SearchWP Source Error', 'searchwp' )
				);
			}
		} else {
			$this->labels     = [
				'plural'   => $post_type_model->labels->name,
				'singular' => $post_type_model->labels->singular_name,
			];
		}

		$this->name       = 'post' . SEARCHWP_SEPARATOR . $post_type;
		$this->post_type  = $post_type;
		$this->db_table   = $this->db_table . $wpdb->posts;
		$this->attributes = $this->attributes();
		$this->rules      = $this->rules();
	}

	/**
	 * Gets permalink for Source Entry ID.
	 *
	 * @since 4.1.14
	 * @param int $id ID of the Entry
	 * @return null|string
	 */
	public static function get_permalink( int $post_id ) {
		return get_permalink( $post_id );
	}

	/**
	 * Gets edit link for Source Entry ID.
	 *
	 * @since 4.1.14
	 * @param int $id ID of the Entry
	 * @return null|string
	 */
	public static function get_edit_link( int $id ) {
		return get_edit_post_link( $id, '' ); // Pass empty context to prevent urlencode.
	}

	/**
	 * Adds notice when this post type is intended to be excluded from search.
	 *
	 * @since 4.0
	 * @param Notice[] Existing notices
	 * @return Notice[]
	 */
	protected function notices( $notices ) {
		if ( $this->is_excluded_from_search() ) {
			$notices[] = new Notice( '', [
				'tooltip'    => sprintf(
					// Translators: %s is the plural label of a post type.
					__( 'Note: by default %s are set to be excluded from search. Enabling %s overrides this.', 'searchwp' ),
					$this->labels['plural'],
					$this->labels['plural']
				),
			] );
		}

		return $notices;
	}

	/**
	 * Whether this post types was intended to be excluded from search.
	 *
	 * @since 4.0
	 * @return bool
	 */
	public function is_excluded_from_search() {
		$post_type = get_post_type_object( $this->post_type );

		return ! is_null( $post_type ) ? $post_type->exclude_from_search : true;
	}

	/**
	 * Restrict available Posts to this post type with the proper post stati and exclusions.
	 *
	 * @since 4.0
	 * @return array
	 */
	protected function db_where() {
		$args = [
			'post_type' => $this->post_type,
			'source'    => $this,
		];

		return apply_filters( 'searchwp\source\post\db_where', [
			'relation' => 'AND',
			[ 	// Only include applicable post type.
				'column'  => 'post_type',
				'value'   => $this->post_type,
			],
			[ 	// Only include applicable post stati.
				'column'  => 'post_status',
				'value'   => Utils::get_post_type_stati( $this->post_type ),
				'compare' => 'IN',
			],
			[ 	// ID-based limiter.
				'column'  => 'ID',
				'value'   => Utils::get_filtered_post__in( $args ),
				'compare' => 'IN',
				'type'    => 'NUMERIC',
			],
			[ 	// ID-based exclusions.
				'column'  => 'ID',
				'value'   => Utils::get_filtered_post__not_in( $args ),
				'compare' => 'NOT IN',
				'type'    => 'NUMERIC',
			],
		], $args );
	}

	/**
	 * Defines the Attributes for this Source.
	 *
	 * @since 4.0
	 * @return array
	 */
	protected function attributes() {
		global $wpdb;

		$attributes = [
			[	// Title.
				'name'    => 'title',
				'label'   => __( 'Title', 'searchwp' ),
				'default' => $this->is_excluded_from_search() ? false : Utils::get_max_engine_weight(),
				'data'    => function( $post_id ) {
					return get_the_title( $post_id );
				},
				'phrases' => 'post_title',
			],
			[	// Post content.
				'name'    => 'content',
				'label'   => __( 'Content', 'searchwp' ),
				'default' => $this->is_excluded_from_search() ? false : Utils::get_min_engine_weight(),
				'data'    => function( $post_id ) {
					$post    = get_post( $post_id );
					$content = ! is_null( $post ) ? $post->post_content : '';

					$do_shortcodes = apply_filters(
						'searchwp\source\post\attributes\content\do_shortcodes',
						Settings::get_single( 'parse_shortcodes', 'boolean' ),
						[ 'post' => $post, ]
					);

					$do_blocks = function_exists( 'has_blocks' )
						&& function_exists( 'do_blocks' )
						&& apply_filters( 'searchwp\source\post\attributes\content\do_blocks', true, [
							'post' => $post,
						] );

					if ( $do_shortcodes && $do_blocks ) {
						$content = apply_filters( 'the_content', $content );
					} else if ( ! $do_shortcodes && $do_blocks && has_blocks( $content ) ) {
						$content = do_blocks( $content );
					} else if ( $do_shortcodes && ! $do_blocks ) {
						$content = do_shortcode( $content );
					}

					return apply_filters( 'searchwp\source\post\attributes\content', $content, [
						'post' => $post,
					] );
				},
				'phrases' => 'post_content',
			],
			[	// Post slug.
				'name'    => 'slug',
				'label'   => __( 'Slug', 'searchwp' ),
				'default' => $this->is_excluded_from_search() ? false : Utils::get_max_engine_weight(),
				'data'    => function( $post_id ) {
					$slug = get_post_field( 'post_name', get_post( $post_id ) );

					// By default regex pattern matches are exclusive, but in this case we want
					// to index the parts of the slug because they're an exception to the rule.
					if ( ! apply_filters( 'searchwp\source\post\attributes\slug\strict', false ) ) {
						$slug = str_replace( [ '-', '_' ], ' ', $slug );
					}

					return $slug;
				},
			],
			[	// Post excerpt.
				'name'    => 'excerpt',
				'label'   => __( 'Excerpt', 'searchwp' ),
				'default' => $this->is_excluded_from_search() ? false : Utils::get_min_engine_weight(),
				'data'    => function( $post_id ) {
					return get_the_excerpt( $post_id );
				},
				'phrases' => 'post_excerpt',
			],
			[	// Post Author.
				'name'    => 'author',
				'label'   => __( 'Author', 'searchwp' ),
				'default' => $this->is_excluded_from_search() ? false : Utils::get_max_engine_weight(),
				'data'    => function( $post_id ) {
					return apply_filters(
						'searchwp\source\post\attributes\author',
						get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) ),
						[ 'post_id' => $post_id ]
					);
				},
			],
			[	// Custom Fields.
				'name'    => 'meta',
				'label'   => __( 'Custom Fields', 'searchwp' ),
				'notes'   => [
					__( 'Tip: Match multiple keys using * as wildcard and hitting Enter', 'searchwp' ),
				],
				'default' => $this->is_excluded_from_search() ? false : Utils::get_min_engine_weight(),
				'options' => function( $search = false, array $include = [] ) {
					// If we're retrieving a specific set of options, get them and return.
					if ( ! empty( $include ) ) {
						return array_map( function( $meta_key ) {
							return new Option( (string) $meta_key );
						}, $include );
					}

					return array_map( function( $meta_key ) {
						return new Option( $meta_key );
					}, Utils::get_meta_keys_for_post_type( $this->post_type, $search ) );
				},
				'allow_custom' => true,
				'data'    => function( $post_id, $meta_key ) {
					// Because partial matching is supported, we're going to work with an array of meta keys even if it's one.
					if ( false !== strpos( '*', $meta_key ) ) {
						$meta_keys = Utils::get_meta_keys_for_post_type( $this->post_type, $meta_key );
					} else {
						$meta_keys = [ $meta_key ];
					}

					$do_shortcodes = apply_filters(
						'searchwp\source\post\attributes\content\do_shortcodes',
						Settings::get_single( 'parse_shortcodes', 'boolean' ),
						[ 'post' => $post_id, ]
					);

					$meta_value = array_filter( array_map( function( $meta_key ) use ( $post_id, $do_shortcodes ) {
						$post_meta = get_post_meta( $post_id, $meta_key, false );

						// If there was only one record, let's clean it up.
						if ( is_array( $post_meta ) && 1 === count( $post_meta ) ) {
							$post_meta = array_values( $post_meta );
							$post_meta = array_shift( $post_meta );
						}

						if ( $do_shortcodes ) {
							if ( is_array( $post_meta ) ) {
								// Support string[] but anything more advanced can use a hook.
								$post_meta = array_map( function( $this_meta ) {
									if ( is_string( $this_meta ) ) {
										return do_shortcode( $this_meta );
									} else {
										return $this_meta;
									}
								}, $post_meta );
							} else {
								$post_meta = do_shortcode( $post_meta );
							}
						}

						return $post_meta;
					}, $meta_keys ) );

					$meta_value = apply_filters(
						'searchwp\source\post\attributes\meta',
						apply_filters(
							'searchwp\source\post\attributes\meta\\' . $meta_key,
							$meta_value,
							[ 'post_id' => $post_id, ]
						), [
						'post_id'    => $post_id,
						'meta_key'   => $meta_key,
						'meta_value' => $meta_value,
					] );

					return $meta_value;
				},
				'phrases' => [ [
					'table'  => $wpdb->postmeta,
					'column' => 'meta_value',
					'id'     => 'post_id'
				] ],
			],
			[	// Taxonomies.
				'name'    => 'taxonomy',
				'label'   => __( 'Taxonomies', 'searchwp' ),
				'default' => $this->is_excluded_from_search() ? false : Utils::get_max_engine_weight(),
				'options' => function() {
					return array_map( function( $taxonomy ) {
						return new Option( $taxonomy->name, $taxonomy->label . ' (' . $taxonomy->name . ')' );
					}, get_object_taxonomies( $this->post_type, 'objects' ) );
				},
				'data'    => function( $post_id, $taxonomy ) {
					do_action( 'searchwp\source\post\attributes\taxonomy', [
						'taxonomy'  => $taxonomy,
						'post_id'   => $post_id,
						'post_type' => $this->post_type,
					] );

					$terms = apply_filters(
						'searchwp\source\post\attributes\taxonomy\terms',
						get_the_terms( $post_id, $taxonomy ), [
							'taxonomy'  => $taxonomy,
							'post_id'   => $post_id,
							'post_type' => $this->post_type,
						] );

					if ( is_array( $terms ) && ! empty( $terms ) ) {
						$terms = array_map( function( $term ) {
							$term       = get_term( $term ); // Allow hooks to run.
							$term_array = [
								'name'     => $term->name,
								'slug'     => $term->slug,
								'desc'     => $term->description,
							];

							return apply_filters( 'searchwp\source\post\attributes\taxonomy\term', $term_array, [
								'taxonomy' => $term->taxonomy,
								'name'     => $term->name,
								'slug'     => $term->slug,
								'desc'     => $term->description,
							] );
						}, $terms );
					}

					return $terms;
				},
			],
		];

		return $attributes;
	}

	/**
	 * Weight Transfer Option options.
	 *
	 * @since 4.0
	 * @return array
	 */
	protected function weight_transfer_options( $force_parent_attribution = false ) {
		$options = [];

		if ( apply_filters( 'searchwp\source\post\\' . $this->post_type . '\attribution', true ) ) {
			$options[] = [
				'option' => new Option( 'id', sprintf(
					// Translators: placeholder is singular post type label.
					__( 'To %s ID', 'searchwp' ),
					$this->labels['singular']
				) ),
				'source_map' => function( $args ) {
					global $wpdb;

					$post_type = get_post_type( $args['id'] );

					do_action( 'searchwp\debug\log', "Transferring {$this->get_name()} weight to {$post_type}:{$args['id']}", 'source' );

					return $wpdb->prepare( '%s', 'post' . SEARCHWP_SEPARATOR . $post_type );
				}
			];
		}

		// TODO: this reference to Attachment should be handled by the Attachment Source.
		$enable_parent_attribution = is_post_type_hierarchical( $this->post_type ) || 'attachment' === $this->post_type;

		if ( $force_parent_attribution || apply_filters(
			'searchwp\source\post\\' . $this->post_type . '\parent_attribution',
			$enable_parent_attribution
		) ) {
			$option = [
				'option'     => new Option( 'col', sprintf(
					// Translators: placeholder is singular post type label.
					__( 'To %s Parent', 'searchwp' ),
					$this->labels['singular']
				) ),
				'value'      => $this->db_post_parent_column, // Just the column name, an alias is created for this Source's table.
				'conditions' => function( $args ) {
					// TODO: This checks only post stati, should it be more comprehensive and check db_where?

					if ( ! apply_filters(
						'searchwp\source\post\\' . $this->post_type . '\parent_attribution\check_post_stati',
						true
					) ) {
						return '';
					}

					do_action( 'searchwp\debug\log', "Transferring {$this->post_type} weight to {$this->db_post_parent_column}", 'source' );

					return [
						'id'     => $this->get_post_parent_id_case_sql( $args ),
						'source' => $this->get_post_parent_source_case_sql( $args ),
					];
				}
			];

			// If attribution is strict, entries without a post_parent will be dropped from the results set.
			// If attribution is not strict, child entries will be returned as a fallback.
			if ( ! apply_filters( 'searchwp\source\post\\' . $this->post_type . '\parent_attribution\strict', false ) ) {
				$option[ 'fallback' ] = [ '0' ]; // Entries with a post_parent of zero have no parent.
			}

			$options[] = $option;
		}

		return $options;
	}

	/**
	 * Generates the SQL necessary for the s.id clause for post parent weight transfer.
	 *
	 * @since 4.1
	 * @param array $args The incoming arguments.
	 * @param string $db_table The name of the database table.
	 * @param string $db_post_parent_column The name of the column that stores the parent ID.
	 * @param string[] $potential_parents The potential parent post types.
	 * @param bool $strict Whether results should be strict i.e. only contain added Engine Sources as opposed to any Source.
	 * @return string SQL
	 */
	public function get_post_parent_source_case_sql( $args, $db_table = '', $db_post_parent_column = '', $potential_parents = [], $strict = true ) {
		global $wpdb;

		if ( empty( $db_table ) ) {
			$db_table = $this->db_table;
		}

		if ( empty( $db_post_parent_column ) ) {
			$db_post_parent_column = $this->db_post_parent_column;
		}

		if ( empty( $potential_parents ) ) {
			$potential_parents = $this->get_potential_post_parent_types( $args );
		}

		// If we're not strict then we're always going to return the parent post type.
		if ( ! $strict ) {
			return $wpdb->prepare(
				"CONCAT( %s,
					( SELECT post_type
					FROM {$wpdb->posts}
					WHERE {$wpdb->posts}.ID = {$args['alias']}.{$db_post_parent_column} )
				)",
				'post' . SEARCHWP_SEPARATOR
			);
		}

		// CASE each applicable post type with appropriate post stati.
		$conditions = array_map( function( $post_type ) use ( $args, $db_table, $db_post_parent_column ) {
			global $wpdb;

			$post_type_stati = array_map( function( $status ) use ( $wpdb ) {
				return $wpdb->prepare( '%s', $status );
			}, Utils::get_post_type_stati( $post_type ) );

			return 'WHEN ' . $wpdb->prepare( '%s', $post_type ) . " = (
					SELECT post_type
					FROM {$db_table}
					WHERE {$db_table}.ID = {$args['alias']}.{$db_post_parent_column}
				) AND (
					SELECT post_status
					FROM {$db_table}
					WHERE {$db_table}.ID = {$args['alias']}.{$db_post_parent_column}
				) IN (" .
				implode( ', ', $post_type_stati ) . ') THEN ' . $wpdb->prepare( '%s', 'post' . SEARCHWP_SEPARATOR . $post_type );
		}, $potential_parents );

		return 'CASE ' . implode( ' ', $conditions ) . " ELSE {$args['index_alias']}.source END";
	}

	/**
	 * Generates the SQL necessary for the s.source clause for post parent weight transfer.
	 *
	 * @since 4.1
	 * @param array $args The incoming arguments.
	 * @param string $db_table The name of the database table.
	 * @param string $db_post_parent_column The name of the column that stores the parent ID.
	 * @param string[] $potential_parents The potential parent post types.
	 * @param bool $strict Whether results should be strict i.e. only contain added Engine Sources as opposed to any Source.
	 * @return string SQL
	 */
	public function get_post_parent_id_case_sql( $args, $db_table = '', $db_post_parent_column = '', $potential_parents = [], $strict = true ) {
		if ( empty( $db_table ) ) {
			$db_table = $this->db_table;
		}

		if ( empty( $db_post_parent_column ) ) {
			$db_post_parent_column = $this->db_post_parent_column;
		}

		if ( empty( $potential_parents ) ) {
			$potential_parents = $this->get_potential_post_parent_types( $args );
		}

		// If we're not strict then we're always going to return the actual Source ID.
		if ( ! $strict ) {
			return "{$args['alias']}.{$db_post_parent_column}";
		}

		// CASE each applicable post type with appropriate post stati.
		$conditions = array_map( function( $post_type ) use ( $args, $db_table, $db_post_parent_column ) {
			global $wpdb;

			$post_type_stati = array_map( function( $status ) use ( $wpdb ) {
				return $wpdb->prepare( '%s', $status );
			}, Utils::get_post_type_stati( $post_type ) );

			return $wpdb->prepare( '%s', $post_type ) . " = (
					SELECT post_type
					FROM {$db_table}
					WHERE {$db_table}.ID = {$args['alias']}.{$db_post_parent_column}
				) AND (
					SELECT post_status
					FROM {$db_table}
					WHERE {$db_table}.ID = {$args['alias']}.{$db_post_parent_column}
				) IN (" .
				implode( ', ', $post_type_stati ) . ')';
		}, $potential_parents );

		return 'CASE WHEN ' . implode( ' OR ', $conditions ) . " THEN {$args['alias']}.{$db_post_parent_column} ELSE 0 END";
	}

	/**
	 * Retrieves all potential parent post types from the current Engine.
	 *
	 * @since 4.1
	 * @param array $args The arguments for the weight transfer option.
	 * @return string[] Post type names.
	 */
	public function get_potential_post_parent_types( $args, $child_post_type = '' ) {
		if ( empty( $child_post_type ) ) {
			$child_post_type = $this->get_post_type();
		}

		// We need to ensure that parent post type is taken into consideration when attribution applies.
		// Unfortunately this means we need to iterate over all post types because they each have uniuqe stati.
		$flag = 'post' . SEARCHWP_SEPARATOR;

		// Get a list of applicable post type names (WP_Post-based and not $this->post_type).
		return array_map( function( $source_name ) use ( $flag ) {
			return substr( $source_name, strlen( $flag ) );
		}, array_filter(
			array_keys( $args['query']->get_engine()->get_sources() ),
			function( $source_name ) use ( $flag, $child_post_type ) {
				// Parents need to be a WP_Post but can be the same post type e.g. Pages.
				return $flag === substr( $source_name, 0, strlen( $flag ) );
			}
		) );
	}

	/**
	 * Returns a baseline set of WP_Query arguments.
	 *
	 * @since 4.0
	 * @return (string|array|true)[]
	 */
	protected function get_base_wp_query_args() {
		return [
			'post_type'        => $this->post_type,
			'post_status'      => Utils::get_post_type_stati( $this->post_type ),
			'post__in'         => Utils::get_filtered_post__in(),
			'post__not_in'     => Utils::get_filtered_post__not_in(),
			'orderby'          => 'none',
			'fields'           => 'ids',
			'nopaging'         => true,
			'suppress_filters' => true,
		];
	}

	/**
	 * Defines the Rules for this Source.
	 *
	 * @since 4.0
	 * @return array
	 */
	protected function rules() {
		$base_wp_query_args = $this->get_base_wp_query_args();

		$rules = [
			[	// Taxonomies.
				'name'    => 'taxonomy',
				'label'   => __( 'Taxonomy', 'searchwp' ),
				'options' => function() {
					// The Options for this Rule are Taxonomy names.
					return array_map( function( $taxonomy ) {
						return new Option( $taxonomy->name, $taxonomy->label . ' (' . $taxonomy->name . ')' );
					}, get_object_taxonomies( $this->post_type, 'objects' ) );
				},
				'conditions' => [ 'IN', 'NOT IN' ],
				'values' => function( $option, $search = false, array $include = [] ) {
					$args = [
						'taxonomy'   => $option,
						'hide_empty' => false,
					];

					if ( $search ) {
						$args['name__like'] = $search;
					}

					if ( count( $include ) ) {
						$args['include'] = $include;
					}

					// The Conditions for each Option of this Rule are Taxonomy Terms.
					 return array_map( function( $term ) {
						 return new Option( $term->term_id, $term->name );
					 }, get_terms( $args ) );
				},
				'application' => function( $properties ) use ( $base_wp_query_args ) {
					$tax_rule_wp_query = new \WP_Query( array_merge( [
						'tax_query'    => [ [
							'taxonomy' => $properties['option'],
							'field'    => 'term_id',
							'terms'    => $properties['value'],
							'operator' => $properties['condition'],
						] ],
					], $base_wp_query_args ) );

					// Return the IDs we already did the work to find if there aren't too many.
					if ( empty( $tax_rule_wp_query->posts ) ) {
						return [ 0 ];
					} else if ( $tax_rule_wp_query->found_posts < 20 ) {
						return $tax_rule_wp_query->posts;
					} else {
						return $tax_rule_wp_query->request;
					}
				}
			],
			[	// Publish date.
				'name'        => 'published',
				'label'       => __( 'Publish Date', 'searchwp' ),
				'tooltip'     => __( 'Any strtotime()-compatible string e.g. "6 months ago"', 'searchwp' ),
				'options'     => false,
				'conditions'  => [ '<', '>' ],
				'application' => function( $properties ) use ( $base_wp_query_args ) {
					$condition  = $properties['condition'] === '<' ? 'before' : 'after';
					$date_query = [ 'inclusive' => false ];
					$date_query[ $condition ] = $properties['value'];

					$published_rule_wp_query = new \WP_Query( array_merge( [
						'date_query' => [ $date_query ],
					], $base_wp_query_args ) );

					// Return the IDs we already did the work to find if there aren't too many.
					if ( empty( $published_rule_wp_query->posts ) ) {
						return [ 0 ];
					} else if ( $published_rule_wp_query->found_posts < 20 ) {
						return $published_rule_wp_query->posts;
					} else {
						return $published_rule_wp_query->request;
					}
				},
			],
			[	// ID.
				'name'        => 'post_id',
				'label'       => __( 'ID', 'searchwp' ),
				'options'     => false,
				'conditions'  => [ 'IN', 'NOT IN' ],
				'application' => function( $properties ) {
					global $wpdb;

					$condition = 'NOT IN' === $properties['condition'] ? 'NOT IN' : 'IN';
					$ids = explode( ',', Utils::get_integer_csv_string_from( $properties['value'] ) );

					return $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE ID {$condition}  ("
						. implode( ',', array_fill( 0, count( $ids ), '%s' ) )
						. ')', $ids );
				},
			],
		];

		// Some rules apply only when the post type is hierarchical.
		$post_type = get_post_type_object( $this->post_type );

		if ( $post_type->hierarchical ) {
			$rules = array_merge( $rules, [
				[	// Ancestor.
					'name'        => 'ancestor',
					'label'       => __( 'Ancestor ID', 'searchwp' ),
					'tooltip'     => __( 'Ancestor and all descendants will apply to this Rule, comma separate multiple ancestors', 'searchwp' ),
					'options'     => false,
					'conditions'  => [ 'IN', 'NOT IN' ],
					'application' => function( $properties ) {
						global $wpdb;

						$condition = 'NOT IN' === $properties['condition'] ? 'NOT IN' : 'IN';
						$ancestors = explode( ',', Utils::get_integer_csv_string_from( $properties['value'] ) );
						$ids       = [];

						foreach ( $ancestors as $ancestor ) {
							$ids = array_merge( $ids, \SearchWP\Utils::get_descendant_post_parents( $ancestor ) );
						}

						// Force empty IDs if applicable.
						if ( empty( $ids ) ) {
							$ids = [ '' ];
						}

						return $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_parent {$condition} ("
							. implode( ',', array_fill( 0, count( $ids ), '%s' ) )
							. ')', $ids );
					},
				],
				[	// Post Parent.
					'name'        => 'post_parent',
					'label'       => __( 'Post Parent ID', 'searchwp' ),
					'tooltip'     => __( 'Applies only to children, add another Rule to consider Post Parent itself if necessary', 'searchwp' ),
					'options'     => false,
					'conditions'  => [ 'IN', 'NOT IN' ],
					'application' => function( $properties ) {
						global $wpdb;

						$condition = 'NOT IN' === $properties['condition'] ? 'NOT IN' : 'IN';
						$ids = explode( ',', Utils::get_integer_csv_string_from( $properties['value'] ) );

						// Force empty IDs if applicable.
						if ( empty( $ids ) ) {
							$ids = [ '' ];
						}

						return $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_parent {$condition}  ("
							. implode( ',', array_fill( 0, count( $ids ), '%s' ) )
							. ')', $ids );
					},
				],
			] );
		}

		return $rules;
	}

	/**
	 * Maps an Entry for this Source to its native model.
	 *
	 * @since  4.0
	 * @param Entry   $entry The Entry
	 * @param Boolean $doing_query Whether a query is being run
	 * @return mixed
	 */
	public function entry( Entry $entry, $doing_query = false ) {
		$post = get_post( $entry->get_id() );

		if ( ! $post instanceof \WP_Post ) {
			return $post;
		}

		// Set up highlighter if applicable.
		$highlighter = Settings::get( 'highlighting', 'boolean' ) ? new Highlighter() : false;
		$highlighter = apply_filters( 'searchwp\source\post\do_highlighting', $highlighter, [
			'entry' => $entry,
			'query' => $doing_query,
		] );
		
		// Skip highlighting if this is an empty search.
		$highlighter = ! empty( $doing_query ) && ! empty( $doing_query->get_tokens() ) ? $highlighter : false;

		// Determine whether we're going to find a global excerpt based on whether highlighting is enabled.
		$global_excerpt = apply_filters( 'searchwp\source\post\global_excerpt', ! empty( $highlighter ), [ 'entry' => $entry, ] );
		$global_excerpt = apply_filters( 'searchwp\source\post\global_excerpt\\' . $this->post_type, $global_excerpt, [ 'entry' => $entry, ] );

		// Set the excerpt early if global excerpt is applicable.
		if ( $doing_query instanceof Query && $global_excerpt ) {
			$post->post_excerpt = self::get_global_excerpt( $entry, $doing_query );
		}

		// Apply highlights if applicable.
		if ( $doing_query instanceof Query && $highlighter ) {
			// Be sure to check suggested search strings and not just the submitted search.
			if ( ! empty( $doing_query->get_suggested_search() ) ) {
				$search_terms = $doing_query->get_suggested_search();
			} else {
				// We have to be careful here e.g. with synonyms. We only really want to work with
				// the original, submitted search string. If we consider synonyms or any other
				// modifications to the search string itself, we could get both a weird excerpt
				// and further weird ancillary changes like highlighting the modifications.
				// However in some cases devs may want that, so leave the option.
				$search_terms = apply_filters( 'searchwp\source\post\global_excerpt\use_original_search_string', true )
					? $doing_query->get_keywords( true )
					: implode( ' ', array_merge( [ $doing_query->get_keywords() ], $doing_query->get_tokens() ) );
			}

			// If this is a quoted search, limit the highlight to the quoted search.
			if ( false !== strpos( $search_terms, '"' ) ) {
				$search_terms = str_replace( '"', '', $doing_query->get_keywords() );
			}

			$search_terms = explode( ' ', $search_terms );

			$post->post_title   = $highlighter::apply( get_the_title( $post ), $search_terms );
			$post->post_excerpt = $highlighter::apply( $post->post_excerpt, $search_terms );
		}

		return $post;
	}

	/**
	 * Getter for post type.
	 *
	 * @since 4.0
	 * @return string
	 */
	public function get_post_type() {
		return $this->post_type;
	}

	/**
	 * Returns a global excerpt based on the submitted WP_Post. Will check all enabled Attributes.
	 *
	 * @since 4.0
	 * @param Entry $entry The entry to consider.
	 * @param string|Query $query Either the search string or a Query proper.
	 * @return string An excerpt containing (at least) the first search term.
	 */
	public static function get_global_excerpt( Entry $entry, $query, $length = 55 ) {
		do_action( 'searchwp\get_global_excerpt' );

		$post_id = $entry->get_id();
		$post    = get_post( $post_id );

		if ( ! $post instanceof \WP_Post ) {
			return '';
		}

		// Prevent the SearchWP Forms shortcode from being rendered in the excerpt.
		add_filter( 'pre_do_shortcode_tag', function( $output, $tag ) {
			return $tag === 'searchwp_form' ? '' : $output;
		}, 10, 2 );

		// Prevent the SearchWP Forms Gutenberg block from being rendered in the excerpt.
		add_filter( 'render_block_searchwp/search-form', '__return_empty_string' );

		if ( $query instanceof Query ) {
			// Be sure to check suggested search strings and not just the submitted search.
			if ( ! empty( $query->get_suggested_search() ) ) {
				$search_terms = $query->get_suggested_search();
			} else {
				// We have to be careful here e.g. with synonyms. We only really want to work with
				// the original, submitted search string. If we consider synonyms or any other
				// modifications to the search string itself, we could get both a weird excerpt
				// and further weird ancillary changes like highlighting the modifications.
				// However in some cases devs may want that, so leave the option.
				if ( ! apply_filters( 'searchwp\source\post\global_excerpt\use_original_search_string', true )
					&& ! empty( array_diff( explode( ' ', str_replace( '"', '', $query->get_keywords() ) ), $query->get_tokens() ) ) )
				{
					$search_terms = implode( ' ', array_merge( [ $query->get_keywords() ], $query->get_tokens() ) );
				} else {
					$search_terms = $query->get_keywords( true );
				}
			}
		} else {
			$search_terms = (string) $query;
		}

		// If this is a quoted search, we should remove the quotes before proceeding
		if ( false !== strpos( $search_terms, '"' ) ) {
			$search_terms = str_replace( '"', '', $search_terms );
		}

		// Priority is the existing Excerpt.
		$excerpt = isset( $post->post_excerpt ) ? $post->post_excerpt : '';
		$excerpt = apply_filters( 'searchwp\source\post\excerpt_haystack', $excerpt, [
			'search' => $search_terms,
			'post'   => $post,
			'query'  => $query,
		] );
		if ( ! empty( $excerpt ) && Utils::string_has_substring_from_string( $excerpt, $search_terms ) ) {
			return Utils::trim_string_around_substring(
				$excerpt,
				$search_terms,
				$length
			);
		}

		// Next check the post content.
		$content = Utils::stringify_html( apply_filters( 'the_content', $post->post_content ) );

		if ( ! empty( $content ) ) {
			$content = apply_filters( 'searchwp\source\post\excerpt_haystack', $content, [
				'search' => $search_terms,
				'post'   => $post,
				'query'  => $query,
			] );
			if ( ! empty( $content ) && Utils::string_has_substring_from_string( $content, $search_terms ) ) {
				return Utils::trim_string_around_substring(
					$content,
					$search_terms,
					$length
				);
			}
		}

		// Facilitate a kill switch.
		if ( apply_filters( 'searchwp\source\post\global_excerpt_break', false, [
			'search' => $search_terms,
			'post'   => $post,
			'query'  => $query,
		] ) ) {
			return ! empty( $excerpt ) ? $excerpt : get_the_title( $post_id );
		}

		$entry_data = $entry->get_data( true, true );

		// Check Document Content.
		if ( isset( $entry_data['document_content'] ) && ! empty( $entry_data['document_content'] ) ) {
			$content = apply_filters( 'searchwp\source\post\excerpt_haystack', $entry_data['document_content'], [
				'search' => $search_terms,
				'post'   => $post,
				'query'  => $query,
			] );

			if ( ! empty( $content ) && Utils::string_has_substring_from_string( $content, $search_terms ) ) {
				return Utils::trim_string_around_substring(
					$content,
					$search_terms,
					$length
				);
			}
		}

		// Lastly check postmeta.
		$meta_value_excerpt = false;
		if ( ! empty( $entry_data['meta'] ) && is_array( $entry_data['meta'] ) ) {
			foreach ( $entry_data['meta'] as $meta_key => $meta_data ) {
				$meta_value = apply_filters( 'searchwp\source\post\excerpt_haystack', $meta_data, [
					'search'   => $search_terms,
					'post'     => $post,
					'query'    => $query,
					'meta_key' => $meta_key,
				] );

				$meta_value = Utils::get_string_from( $meta_value );

				if ( ! empty( $meta_value ) && Utils::string_has_substring_from_string( $meta_value, $search_terms ) ) {
					$do_shortcodes = apply_filters(
						'searchwp\source\post\attributes\content\do_shortcodes',
						Settings::get_single( 'parse_shortcodes', 'boolean' ),
						[ 'post' => $post, ]
					);

					if ( $do_shortcodes ) {
						$meta_value = do_shortcode( $meta_value );
					}

					$meta_value = Utils::stringify_html( $meta_value );
					$meta_value_excerpt = Utils::trim_string_around_substring(
						$meta_value,
						$search_terms,
						$length
					);

					break;
				}
			}

			if ( ! empty( $meta_value_excerpt ) ) {
				return $meta_value_excerpt;
			}
		}

		// Nothing was found, send back the native excerpt or worst case the title.
		return ! empty( $excerpt ) ? $excerpt : apply_filters( 'searchwp\source\post\excerpt_fallback', get_the_excerpt( $post_id ), [
			'search' => $search_terms,
			'post'   => $post,
			'query'  => $query,
		] );
	}

	/**
	 * Add class hooks.
	 *
	 * @since 4.0
	 * @param array $params Parameters.
	 * @return array
	 */
	public function add_hooks( array $params = [] ) {

		// Custom Fields.
		if ( ! has_filter( 'searchwp\source\attribute\options', [ $this, 'special_meta_keys' ] ) ) {
			add_filter( 'searchwp\source\attribute\options', [ $this, 'special_meta_keys' ], 9, 2 );
		}

		if ( ! has_filter( 'searchwp\source\attribute\options\special', [ $this, 'special_meta_keys' ] ) ) {
			add_filter( 'searchwp\source\attribute\options\special', [ $this, 'special_meta_keys' ], 9, 2 );
		}

		// Output taxonomy names.
		// TODO: Refactor with Issue #264.
		if ( ! has_filter( 'searchwp\source\attribute\options', [ $this, 'add_taxonomy_names' ] ) ) {
			add_filter( 'searchwp\source\attribute\options', [ $this, 'add_taxonomy_names' ], 5, 2 );
		}
		if ( ! has_filter( 'searchwp\source\attribute\options\special', [ $this, 'add_taxonomy_names' ] ) ) {
			add_filter( 'searchwp\source\attribute\options\special', [ $this, 'add_taxonomy_names' ], 5, 2 );
		}

		// We want ACF Repeatables to be integrated.
		new \SearchWP\Integrations\AdvancedCustomFields( $this );
		new \SearchWP\Integrations\WooCommerceAdminSearch();

		// If this Source is not active we can bail out early.
		if ( isset( $params['active'] ) && ! $params['active'] ) {
			return;
		}

		// Prevent invalid IDs from being returned.
		if ( ! has_filter( 'searchwp\query', [ $this, 'prevent_invalid_post_ids' ] ) ) {
			add_filter( 'searchwp\query', [ $this, 'prevent_invalid_post_ids' ], 10, 2 );
		}

		// Cycle Posts when they are saved or deleted. This covers:
		//      - Initial save
		//      - Edit
		//      - Delete
		//      - Status change (e.g. scheduled publishing)
		if ( ! has_action( 'save_post', [ $this, 'drop_post' ] ) ) {
			add_action( 'save_post', [ $this, 'drop_post' ], 999 );
		}

		if ( ! has_action( 'delete_post', [ $this, 'drop_post' ] ) ) {
			add_action( 'delete_post', [ $this, 'drop_post' ], 999 );
		}

		if ( ! has_action( 'updated_post_meta', [ $this, 'updated_post_meta' ] ) ) {
			add_action( 'updated_post_meta', [ $this, 'updated_post_meta' ], 999, 4 );
		}

		if ( ! has_action( 'deleted_post_meta', [ $this, 'updated_post_meta' ] ) ) {
			add_action( 'deleted_post_meta', [ $this, 'updated_post_meta' ], 999, 4 );
		}

		if ( ! has_action( 'deleted_term_relationships', [ $this, 'updated_post_term' ] ) ) {
			add_action( 'deleted_term_relationships', [ $this, 'updated_post_term' ], 10, 3 );
		}

		if ( ! has_action( 'added_term_relationship', [ $this, 'updated_post_term' ] ) ) {
			add_action( 'added_term_relationship', [ $this, 'updated_post_term' ], 10, 3 );
		}

		if ( ! has_action( 'edited_term', [ $this, 'updated_taxonomy_term' ] ) ) {
			add_action( 'edited_term', [ $this, 'updated_taxonomy_term' ], 10, 3 );
		}

		if ( ! has_action( 'profile_update', [ $this, 'updated_author_profile' ] ) ) {
			add_action( 'profile_update', [ $this, 'updated_author_profile' ], 10, 3 );
		}

		if ( ! has_action( 'delete_user', [ $this, 'updated_author_profile' ] ) ) {
			add_action( 'delete_user', [ $this, 'deleted_author_profile' ], 10 );
		}
	}

	/**
	 * Callback to include Taxonomy Names in dropdown by default.
	 *
	 * @since 4.1
	 * @param mixed $keys
	 * @param mixed $args
	 * @return mixed|array
	 */
	public function add_taxonomy_names( $keys, $args ) {

		if ( $args['source'] !== $this->name || $args['attribute'] !== 'taxonomy' ) {
			return $keys;
		}

		foreach ( get_object_taxonomies( $this->post_type, 'objects' ) as $taxonomy ) {
			$key    = $taxonomy->name;
			$option = new Option( $taxonomy->name, $taxonomy->label . ' (' . $taxonomy->name . ')' );

			// If there's already a match, remove it because we want ours there.
			$keys = array_filter( $keys, function( $option ) use ( $key ) {
				return $key !== $option->get_value();
			});

			$keys[] = $option;
		}

		return $keys;
	}

	/**
	 * Callback catch-all to prevent invalid Posts from being returned.
	 *
	 * @since 4.0.6
	 * @param mixed $query The query being executed.
	 * @param mixed $params Hook parameters.
	 * @return string[][] The query.
	 */
	public function prevent_invalid_post_ids( $query, $params ) {
		$key = 'searchwp_prevent_invalid_post_ids';

		if ( ! array_key_exists( $key, $query['where'] ) ) {
			$query['where'][ $key ] = "(SUBSTRING({$params['index_alias']}.source, 1, 5) != 'post"
			. SEARCHWP_SEPARATOR . "' OR (SUBSTRING({$params['index_alias']}.source, 1, 5) = 'post"
					. SEARCHWP_SEPARATOR . "' AND {$params['index_alias']}.id != '0'))";
		}

		return $query;
	}

	/**
	 * Callback when a taxonomy term is edited.
	 *
	 * @since 4.0
	 * @deprecated 4.2.2 Use updated_post_term()
	 *
	 * @param int    $object_id  Object ID.
	 * @param array  $terms      An array of object terms.
	 * @param array  $tt_ids     An array of term taxonomy IDs.
	 * @param string $taxonomy   Taxonomy slug.
	 * @param bool   $append     Whether to append new terms to the old terms.
	 * @param array  $old_tt_ids Old array of term taxonomy IDs.
	 *
	 * @return bool Whether the post was dropped.
	 */
	public function purge_post_via_term( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {

		_deprecated_function( __FUNCTION__, '4.2.2', 'updated_post_term()' );

		return $this->drop_post( $object_id );
	}

	/**
	 * Callback when a taxonomy term is added to or removed from a post.
	 *
	 * @since 4.2.2
	 *
	 * @param int    $object_id Object ID.
	 * @param array  $tt_ids    An array of term taxonomy IDs.
	 * @param string $taxonomy  Taxonomy slug.
	 *
	 * @return bool Whether the post was dropped.
	 */
	public function updated_post_term( $object_id, $tt_ids, $taxonomy ) {

		$allowed_ajax_requests = (array) apply_filters( 'searchwp\source\post\drop\proper_update_term_request', [ 'delete-tag' ] );

		// If doing AJAX check this is a proper request to drop a post.
		if (
			defined( 'DOING_AJAX' )
			&& DOING_AJAX
			&& ! (
				isset( $_REQUEST['action'] )
				&& in_array( $_REQUEST['action'], $allowed_ajax_requests )
			)
		) {
			return false;
		}

		// If this taxonomy is included in any engine settings drop the post.
		if ( Utils::any_engine_has_source_attribute_option( $this->get_attributes()['taxonomy'], $this, $taxonomy ) ) {

			do_action( 'searchwp\source\post\drop', [ 'post_id' => $object_id, 'source' => $this ] );

			// Drop this post from the index.
			\SearchWP::$index->drop( $this, $object_id );
		}
	}

	/**
	 * Callback when a taxonomy term has been updated.
	 *
	 * @since 4.2.3
	 *
	 * @param int    $term_id  Term ID.
	 * @param int    $tt_id    Term taxonomy ID.
	 * @param string $taxonomy Taxonomy slug.
	 */
	public function updated_taxonomy_term( $term_id, $tt_id, $taxonomy ) {

		// If this taxonomy is not included in any engine settings bail out.
		if ( ! Utils::any_engine_has_source_attribute_option( $this->get_attributes()['taxonomy'], $this, $taxonomy ) ) {
			return;
		}

		// Fetch all post IDs associated with the taxonomy term.
		$term_posts = new \WP_Query(
			[
				'post_type'   => 'any',
				'post_status' => 'any',
				'fields'      => 'ids',
				'nopaging'    => true,
				'tax_query'   => [
					[
						'taxonomy' => $taxonomy,
						'field'    => 'term_id',
						'terms'    => $term_id,
					],
				],
			]
		);

		$this->drop_posts_by_attribute( $term_posts->posts, 'taxonomy.' . $taxonomy );

		do_action( 'searchwp\debug\log', "{$taxonomy} id {$term_id} updated dropping posts" );
	}

	/**
	 * Returns whether the submitted meta key is used in any Engine.
	 *
	 * @param string $meta_key   The meta key.
	 * @param int    $object_id  The object ID.
	 * @return bool
	 */
	public function meta_key_in_use( $meta_key, $object_id = 0 ) {
		if ( ! empty( $object_id ) && $this->post_type !== get_post_type( $object_id ) ) {
			return false;
		}

		if ( in_array( $meta_key, Utils::get_ignored_meta_keys( $this->post_type ) ) ) {
			return false;
		}

		return Utils::any_engine_has_source_attribute_option( $this->get_attributes()['meta'], $this, $meta_key );
	}

	/**
	 * Callback to drop an entry when a Custom Field is edited.
	 *
	 * @param mixed $meta_id      ID of metadata entry.
	 * @param mixed $object_id    ID of metadata object.
	 * @param mixed $meta_key     Metadata key
	 * @param mixed $_meta_value  Metadata value.
	 * @return void
	 */
	public function updated_post_meta( $meta_id, $object_id, $meta_key, $_meta_value ) {
		// Applies only if this meta key applies.
		if ( ! $this->meta_key_in_use( $meta_key, $object_id ) ) {
			return;
		}

		// Remove redundant hooks.
		remove_action( 'updated_post_meta', [ $this, 'updated_post_meta' ], 999 );
		remove_action( 'deleted_post_meta', [ $this, 'updated_post_meta' ], 999 );
		remove_action( 'save_post',         [ $this, 'drop_post' ], 999 );
		remove_action( 'delete_post',       [ $this, 'drop_post' ], 999 );

		do_action( 'searchwp\source\post\drop', [ 'post_id' => $object_id, 'source' => $this, ] );

		// Drop this post from the index.
		\SearchWP::$index->drop( $this, $object_id );
	}

	/**
	 * Callback from save_post action to drop a post from the index.
	 *
	 * @since 4.0
	 * @param int|string $post_id The post ID to drop.
	 * @return bool Whether the opration was successful.
	 */
	public function drop_post( $post_id ) {
		if ( ! $this->is_proper_edit_request( $post_id ) ) {
			return false;
		}

		if ( ! $this->is_valid_edit_request( $post_id ) ) {
			return false;
		}

		// Prevent redundant hooks.
		remove_action( 'updated_post_meta', [ $this, 'drop_post' ], 999 );

		do_action( 'searchwp\source\post\drop', [ 'post_id' => $post_id, 'source' => $this, ] );

		// Drop this post from the index.
		\SearchWP::$index->drop( $this, $post_id );
	}

	/**
	 * Callback from delete_post action to drop a post from the index.
	 *
	 * @since 4.3.4
	 *
	 * @param $user_id
	 * @param $old_user_data
	 * @param $new_user_data
	 * @return void
	 */
	public function updated_author_profile( $user_id, $old_user_data, $new_user_data ) {

		// If the Author display name hasn't changed, bail out.
		if ( $old_user_data->data->display_name === $new_user_data['display_name'] ) {
			return;
		}

		$this->updated_posts_author( $user_id );
	}

	/**
	 * Callback from delete_user action to drop a post from the index.
	 *
	 * @since 4.3.4
	 *
	 * @param $user_id
	 * @return void
	 */
	public function deleted_author_profile( $user_id ) {

		$this->updated_posts_author( $user_id );
	}

	/**
	 * Drop posts from the index if the author's profile is updated.
	 * @param $user_id
	 * @return void
	 */
	private function updated_posts_author( $user_id ) {
		// If Author is not included in any engine settings, bail out.
		if ( ! Utils::any_engine_has_source_attribute( $this->get_attributes()['author'], $this ) ) {
			return;
		}

		// Fetch all post IDs associated with the author.
		$author_posts = new \WP_Query(
			[
				'post_type'   => 'any',
				'post_status' => 'any',
				'fields'      => 'ids',
				'nopaging'    => true,
				'author'      => $user_id,
			]
		);

		// Drop the posts from the index.
		$this->drop_posts_by_attribute( $author_posts->posts, 'author' );

		do_action( 'searchwp\debug\log', "Author id {$user_id} updated, dropping posts" );
	}

	/**
	 * Drop posts from the index by attribute.
	 *
	 * @since 4.3.4
	 *
	 * @param $post_ids
	 * @param $attribute
	 * @return void
	 */
	private function drop_posts_by_attribute( $post_ids, $attribute ) {
		global $wpdb;

		$index        = \SearchWP::$index;
		$tables       = $index->get_tables();
		$index_table  = $tables['index']->table_name;
		$status_table = $tables['status']->table_name;

		// Split the array of post IDs into batches.
		$post_batches = array_chunk( $post_ids, 500 );

		// Process each batch separately to prevent database issues.
		foreach ( $post_batches as $batch ) {

			// Delete the entries in the index and status tables.
			$wpdb->query(
				$wpdb->prepare( "
					DELETE i, s
					FROM {$index_table} AS i
					LEFT JOIN {$status_table} AS s
					ON i.id = s.id
					WHERE i.attribute = %s
					AND i.id IN (" . implode( ', ', array_fill( 0, count( $batch ), '%s' ) ) . ')',
					array_merge( [ $attribute ], $batch )
				)
			);
		}
	}

	/**
	 * Determine whether this request is a valid edit request, meaning the
	 * post has not already been flagged for editing (to reduce duplicates)
	 * and the current user has the ability to make this edit.
	 *
	 * @since 4.0
	 * @param int|string $post_id
	 * @return bool
	 */
	public function is_valid_edit_request( $post_id ) {
		$cache_key = 'searchwp_source_post';

		// This action is fired multiple times per request, but we only want to drop a post once.
		$cache = (array) wp_cache_get( $cache_key, '' );
		if ( in_array( $post_id, $cache, true ) ) {
			return false;
		}

		// This action is fired regardless of post type so we need to check against ours.
		if ( $this->post_type !== get_post_type( $post_id ) ) {
			return false;
		}

		// Permissions check.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		// Flag this post as one that has been flagged as handled.
		$cache[] = $post_id;
		wp_cache_set( $cache_key, $cache, '', 1 );

		return true;
	}

	/**
	 * Determine whether this request is a proper post edit request as opposed to
	 * an AJAX call, an autosave, a revision, or Quick Edit.
	 *
	 * @since 4.0
	 * @param int|string $post_id
	 * @return bool
	 */
	public function is_proper_edit_request( $post_id ) {
		if (
			wp_is_post_revision( $post_id )
			|| wp_is_post_autosave( $post_id )
			|| 'auto-draft' === get_post_status( $post_id )
		) {
			return false;
		}

		// Doing AJAX and *not* Quick Editing?
		if (
			defined( 'DOING_AJAX' )
			&& DOING_AJAX
			&& ! (
				// Quick Edit is still applicable.
				isset( $_REQUEST['action'] )
				&& in_array( $_REQUEST['action'], $this->allowed_ajax_edits )
			)
		) {
			return false;
		}

		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return false;
		}

		return true;
	}

	/**
	 * Callback to group meta Attribute Options
	 *
	 * @since 4.0
	 * @param mixed $keys
	 * @param mixed $args
	 * @return mixed|array
	 */
	public function special_meta_keys( $keys, $args ) {
		if ( $args['source'] !== $this->name || $args['attribute'] !== 'meta' ) {
			return $keys;
		}

		// If there's a match, remove it.
		$keys = array_filter( $keys, function( $option ) {
			return '*' !== $option->get_value();
		} );

		// Add 'Any Meta Key' to the top.
		array_unshift( $keys, new Option( '*', __( 'Any Meta Key', 'searchwp' ), 'dashicons dashicons-star-filled' ) );

		return $keys;
	}
}
