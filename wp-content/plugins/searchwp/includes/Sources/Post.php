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
	 * Constructor.
	 *
	 * @since 4.0
	 */
	function __construct( $post_type = 'post' ) {
		global $wpdb;

		$post_type_model = get_post_type_object( $post_type );

		if ( is_null( $post_type_model ) && is_search() ) {
			wp_die(
				__( 'Invalid post type for SearchWP Source Post:', 'searchwp' ) . ' <code>' . esc_html( $post_type ) . '</code>',
				__( 'SearchWP Source Error', 'searchwp' )
			);
		}

		$this->name       = 'post' . SEARCHWP_SEPARATOR . $post_type;
		$this->post_type  = $post_type;
		$this->db_table   = $this->db_table . $wpdb->posts;
		$this->attributes = $this->attributes();
		$this->rules      = $this->rules();
		$this->labels     = [
			'plural'   => $post_type_model->labels->name,
			'singular' => $post_type_model->labels->singular_name,
		];
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
					$do_blocks     = function_exists( 'has_blocks' )
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
				'default' => $this->is_excluded_from_search() ? false : Utils::get_max_engine_weight(),
				'data'    => function( $post_id ) {
					return get_the_excerpt( $post_id );
				},
			],
			[	// Post comments.
				'name'    => 'comments',
				'label'   => __( 'Comments', 'searchwp' ),
				'default' => false,
				'data'    => function( $post_id ) {
					$get_author = apply_filters( 'searchwp\source\post\attributes\comments\author', false, [
						'post_id' => $post_id,
					] );

					$get_email = apply_filters( 'searchwp\source\post\attributes\comments\email', false, [
						'post_id' => $post_id,
					] );

					do_action( 'searchwp\source\post\attributes\comments', [ 'post_id' => $post_id ] );

					$comments = array_map( function( $comment_object ) use ( $get_author, $get_email ) {
						$comment_object = apply_filters( 'searchwp\source\post\attributes\comment', $comment_object );
						$comment        = [ 'comment_content' => $comment_object->comment_content ];

						if ( $get_author ) {
							$comment['comment_author'] = $comment_object->comment_author;
						}

						if ( $get_email ) {
							$comment['comment_author_email'] = $comment_object->comment_author_email;
						}

						return $comment;

					}, get_comments(
						apply_filters( 'searchwp\source\post\attributes\comments\args', [
							'post_id' => $post_id,
						], [
						'post_id' => $post_id
					] ) ) );

					return $comments;
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
								$post_meta = array_map( 'do_shortcode', $post_meta );
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
						return new Option( $taxonomy->name, $taxonomy->label );
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
						get_the_terms( $post_id, $taxonomy ),
						[
							'taxonomy'  => $taxonomy,
							'post_id'   => $post_id,
							'post_type' => $this->post_type,
						]
					);

					if ( is_array( $terms ) && ! empty( $terms ) ) {
						$terms = array_map( function( $term ) {
							return apply_filters( 'searchwp\source\post\attributes\taxonomy\term', $term, [
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
	protected function weight_transfer_options() {
		$options = [];

		if ( apply_filters( 'searchwp\source\post\\' . $this->post_type . '\attribution', true ) ) {
			$options[] = [ 'option' => new Option( 'id', sprintf(
				// Translators: placeholder is singular post type label.
				__( 'To %s ID', 'searchwp' ),
				$this->labels['singular']
			) ) ];
		}

		$enable_parent_attribution = is_post_type_hierarchical( $this->post_type ) || 'attachment' === $this->post_type;

		if ( apply_filters(
			'searchwp\source\post\\' . $this->post_type . '\parent_attribution',
			$enable_parent_attribution
		) ) {
			$option = [
				'option' => new Option( 'col', sprintf(
					// Translators: placeholder is singular post type label.
					__( 'To %s Parent', 'searchwp' ),
					$this->labels['singular']
				) ),
				'value'  => 'post_parent', // Just the column name, an alias is created for this Source's table.
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

		return [
			[	// Taxonomies.
				'name'    => 'taxonomy',
				'label'   => __( 'Taxonomy', 'searchwp' ),
				'options' => function() {
					// The Options for this Rule are Taxonomy names.
					return array_map( function( $taxonomy ) {
						return new Option( $taxonomy->name, $taxonomy->label );
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

		$highlighter = Settings::get( 'highlighting', 'boolean' ) ? new Highlighter() : false;

		// Determine whether we're going to find a global excerpt based on whether highlighting is enabled.
		$global_excerpt = apply_filters( 'searchwp\source\post\global_excerpt', ! empty( $highlighter ), [ 'entry' => $entry, ] );
		$global_excerpt = apply_filters( 'searchwp\source\post\global_excerpt\\' . $this->post_type, $global_excerpt, [ 'entry' => $entry, ] );

		// Set the excerpt early if global excerpt is applicable.
		if ( $doing_query instanceof Query && $global_excerpt ) {
			$post->post_excerpt = $this->get_global_excerpt( $entry, $doing_query );
		}

		// Apply highlights if applicable.
		if ( $doing_query instanceof Query && $highlighter ) {
			$search_terms = explode( ' ', preg_quote( $doing_query->get_keywords(), '/' ) );

			$post->post_title   = $highlighter::apply( get_the_title( $post ), $search_terms );
			$post->post_excerpt = $highlighter::apply( $post->post_excerpt, $search_terms);
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
		$search_terms = $query instanceof Query ? $query->get_keywords() : sanitize_text_field( (string) $query );
		$post_id      = $entry->get_id();
		$post         = get_post( $post_id );

		if ( ! $post instanceof \WP_Post ) {
			return '';
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

		$entry_data = $entry->get_data( true );

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
		return ! empty( $excerpt ) ? $excerpt : get_the_title( $post_id );
	}

	/**
	 * Add class hooks.
	 *
	 * @since 4.0
	 * @return array
	 */
	public function add_hooks() {
		// Prevent invalid IDs from being returned.
		if ( ! has_filter( 'searchwp\query', [ $this, 'prevent_invalid_post_ids' ] ) ) {
			add_filter( 'searchwp\query', [ $this, 'prevent_invalid_post_ids' ], 10, 2 );
		}

		// Custom Fields.
		if ( ! has_filter( 'searchwp\source\attribute\options', [ $this, 'special_meta_keys' ] ) ) {
			add_filter( 'searchwp\source\attribute\options', [ $this, 'special_meta_keys' ], 9, 2 );
		}

		if ( ! has_filter( 'searchwp\source\attribute\options\special', [ $this, 'special_meta_keys' ] ) ) {
			add_filter( 'searchwp\source\attribute\options\special', [ $this, 'special_meta_keys' ], 9, 2 );
		}

		// We want ACF Repeatables to be integrated.
		new \SearchWP\Integrations\AdvancedCustomFields( $this );

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

		if ( ! has_action( 'set_object_terms', [ $this, 'purge_post_via_term' ] ) ) {
			add_action( 'set_object_terms', [ $this, 'purge_post_via_term' ], 10, 6 );
		}

		if ( ! has_action( 'comment_post', [ $this, 'comment_post' ] ) ) {
			add_action( 'comment_post', [ $this, 'comment_post' ], 10, 2 );
		}

		if ( ! has_action( 'edit_comment', [ $this, 'drop_comment_post' ] ) ) {
			add_action( 'edit_comment', [ $this, 'drop_comment_post' ] );
		}

		if ( ! has_action( 'transition_comment_status', [ $this, 'transition_comment_status' ] ) ) {
			add_action( 'transition_comment_status', [ $this, 'transition_comment_status' ], 10, 3 );
		}
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
	 * Whether Comments have been added to this Source.
	 *
	 * @since 4.0
	 * @return bool
	 */
	public function comments_in_use( $post_id = 0 ) {
		if ( ! empty( $post_id ) && $this->post_type !== get_post_type( $post_id ) ) {
			return false;
		}

		// Comment changes trigger post updates.
		$index_comments = apply_filters(
			'searchwp\source\post\index_comments',
			Utils::any_engine_has_source_attribute( $this->get_attributes()['comments'], $this )
		);
		$index_comments = apply_filters( 'searchwp\source\post\index_comments\\' . $this->post_type, $index_comments );

		return $index_comments;
	}

	/**
	 * Callback when a taxonomy term is edited.
	 *
	 * @since 4.0
	 * @param int    $object_id  Object ID.
	 * @param array  $terms      An array of object terms.
	 * @param array  $tt_ids     An array of term taxonomy IDs.
	 * @param string $taxonomy   Taxonomy slug.
	 * @param bool   $append     Whether to append new terms to the old terms.
	 * @param array  $old_tt_ids Old array of term taxonomy IDs.
	 * @return bool Whether the post was dropped.
	 */
	public function purge_post_via_term( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
		// FUTURE: Check engines to see if $taxonomy is used before proceeding, so we can bail early.
		return $this->drop_post( $object_id );
	}

	/**
	 * Callback when a comment changes status.
	 *
	 * @since 4.0
	 * @param mixed $new_status string The new status of the comment.
	 * @param mixed $old_status string The old status of the comment.
	 * @param mixed $comment The comment object.
	 * @return bool Whether the post was dropped.
	 */
	public function transition_comment_status( $new_status, $old_status, $comment ) {
		if ( $new_status === $old_status || ! $this->comments_in_use( $comment->comment_post_ID ) ) {
			return;
		}

		$indexed_statuses = apply_filters( 'searchwp', [ 'approved' ], [
			'source'     => $this,
			'comment'    => $comment,
			'new_status' => $new_status,
			'old_status' => $old_status,
		] );

		if (
			! in_array( $new_status, $indexed_statuses, true )
			&& ! in_array( $old_status, $indexed_statuses, true )
		) {
			return;
		}

		do_action( 'searchwp\source\post\drop', [ 'post_id' => $comment->comment_post_ID, 'source' => $this, ] );

		// Drop this post from the index.
		\SearchWP::$index->drop( $this, $comment->comment_post_ID );
	}

	/**
	 * Drop a comment's post from the index.
	 *
	 * @since 4.0
	 * @param int $comment_id The ID of the comment.
	 * @return bool Whether the post was dropped.
	 */
	public function drop_comment_post( int $comment_id ) {
		$comment = get_comment( $comment_id );

		if ( ! $this->comments_in_use( $comment->comment_post_ID ) ) {
			return;
		}

		return $this->drop_post( $comment->comment_post_ID );
	}

	/**
	 * Callback when a comment is posted.
	 *
	 * @since 4.0
	 * @param mixed $comment_id The ID of the comment that was posted.
	 * @param mixed $comment_approved Whether the comment is approved.
	 * @return bool Whether the post was dropped.
	 */
	public function comment_post( $comment_id, $comment_approved ) {
		$comment = get_comment( $comment_id );

		if ( 1 !== $comment_approved || ! $this->comments_in_use( $comment->comment_post_ID ) ) {
			return;
		}

		return $this->drop_comment_post( $comment_id );
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
		remove_action( 'set_object_terms',  [ $this, 'purge_post_via_term' ], 10 );

		do_action( 'searchwp\source\post\drop', [ 'post_id' => $post_id, 'source' => $this, ] );

		// Drop this post from the index.
		\SearchWP::$index->drop( $this, $post_id );
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
				&& 'inline-save' === $_REQUEST['action']
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

		// Remove our match if it's already there.
		$keys = array_filter( $keys, function( $option ) {
			return '*' !== $option->get_value();
		});

		// Add 'Any Meta Key' to the top.
		array_unshift( $keys, new Option( '*', __( 'Any Meta Key', 'searchwp' ), 'dashicons dashicons-star-filled' ) );

		return $keys;
	}
}
