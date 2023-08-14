<?php

namespace SearchWP\Sources;

use SearchWP\Utils;
use SearchWP\Notice;

/**
 * Class Comment is a Source for WP_Comments.
 *
 * @since 4.1
 */
class Comment extends \SearchWP\Source {

	/**
	 * Column name used to track index status.
	 *
	 * @since 4.1
	 * @var   string
	 */
	protected $db_id_column = 'comment_ID';

	/**
	 * Column used for post parent ID.
	 *
	 * @since 4.1
	 * @var   string
	 */
	protected $db_post_parent_column = 'comment_post_ID';

	/**
	 * Whether parent transfers are strict.
	 *
	 * @since 4.1
	 * @var boolean
	 */
	public $strict_transfer = true;

	/**
	 * Constructor.
	 *
	 * @since 4.1
	 */
	function __construct() {
		global $wpdb;

		// Because this Source force transfers weight to parent WP_Posts, we need to give it a name that follows suit.
		// There are ancillary reasons too e.g. we don't want this Source to be exempt from SWP_Query.
		$this->name     = 'post' . SEARCHWP_SEPARATOR . 'comment';
		$this->db_table = $wpdb->comments;
		$this->labels   = [
			'plural'   => __( 'Comments', 'searchwp' ),
			'singular' => __( 'Comment', 'searchwp' ),
		];

		$this->strict_transfer = (bool) apply_filters( 'searchwp\source\comment\parent_attribution\strict', true );

		// Extend Post Attributes with Attachment Attributes.
		$this->attributes = array_merge( [
			[
				'name'    => 'content',
				'label'   => __( 'Comment', 'searchwp' ),
				'default' => false,
				'data'    => function( $comment_id ) {
					return self::get_comment_attribute( $comment_id, 'comment_content' );
				},
				'phrases' => [ [
					'table'  => $wpdb->comments,
					'column' => 'comment_content',
					'id'     => 'comment_ID'
				] ],
			], [
				'name'    => 'author_name',
				'label'   => __( 'Author Name', 'searchwp' ),
				'default' => false,
				'data'    => function( $comment_id ) {
					return self::get_comment_attribute( $comment_id, 'comment_author' );
				},
			], [
				'name'    => 'author_email',
				'label'   => __( 'Author Email', 'searchwp' ),
				'default' => false,
				'data'    => function( $comment_id ) {
					return self::get_comment_attribute( $comment_id, 'comment_author_email' );
				},
			],
			[	// Custom Fields.
				'name'    => 'meta',
				'label'   => __( 'Custom Fields', 'searchwp' ),
				'notes'   => [
					__( 'Tip: Match multiple keys using * as wildcard and hitting Enter', 'searchwp' ),
				],
				'default' => false,
				'options' => function( $search = false, array $include = [] ) {
					// If we're retrieving a specific set of options, get them and return.
					if ( ! empty( $include ) ) {
						return array_map( function( $meta_key ) {
							return new \SearchWP\Option( (string) $meta_key );
						}, $include );
					}

					return array_map( function( $meta_key ) {
						return new \SearchWP\Option( $meta_key );
					}, Utils::get_meta_keys_for_comments( $search ) );
				},
				'allow_custom' => true,
				'data'    => function( $comment_id, $meta_key ) {
					// Because partial matching is supported, we're going to work with an array of meta keys even if it's one.
					if ( false !== strpos( '*', $meta_key ) ) {
						$meta_keys = Utils::get_meta_keys_for_comments( $meta_key );
					} else {
						$meta_keys = [ $meta_key ];
					}

					$do_shortcodes = apply_filters(
						'searchwp\source\comment\attributes\content\do_shortcodes',
						\SearchWP\Settings::get_single( 'parse_shortcodes', 'boolean' ),
						[ 'post' => $comment_id, ]
					);

					$meta_value = array_filter( array_map( function( $meta_key ) use ( $comment_id, $do_shortcodes ) {
						$comment_meta = get_comment_meta( $comment_id, $meta_key, false );

						// If there was only one record, let's clean it up.
						if ( is_array( $comment_meta ) && 1 === count( $comment_meta ) ) {
							$comment_meta = array_values( $comment_meta );
							$comment_meta = array_shift( $comment_meta );
						}

						if ( $do_shortcodes ) {
							if ( is_array( $comment_meta ) ) {
								$comment_meta = array_map( 'do_shortcode', $comment_meta );
							} else {
								$comment_meta = do_shortcode( $comment_meta );
							}
						}

						return $comment_meta;
					}, $meta_keys ) );

					$meta_value = apply_filters(
						'searchwp\source\comment\attributes\meta',
						apply_filters(
							'searchwp\source\comment\attributes\meta\\' . $meta_key,
							$meta_value,
							[ 'comment_id' => $comment_id, ]
						), [
						'comment_id' => $comment_id,
						'meta_key'   => $meta_key,
						'meta_value' => $meta_value,
					] );

					return $meta_value;
				},
				'phrases' => [ [
					'table'  => $wpdb->commentmeta,
					'column' => 'meta_value',
					'id'     => 'comment_id'
				] ],
			],
		], $this->attributes );

		// TODO: add Rules?

		// Integrate with \SearchWP\Query.
		$this->implement_source_options();
		$this->consider_parent_rules_during_queries();
	}

	/**
	 * Modifies Queries to respect Rules added to parent Sources.
	 *
	 * @since 4.1.9
	 * @return void
	 */
	private function consider_parent_rules_during_queries() {
		add_filter( 'searchwp\query\mods', function( $mods, $query ) {
			global $wpdb;

			$global_source_names = $this->get_global_source_names();

			if ( empty( $global_source_names ) ) {
				return $mods;
			}

			foreach ( $global_source_names as $post_type_name ) {
				foreach ( $query->get_engine()->get_sources() as $engine_source ) {
					if ( 'post' . SEARCHWP_SEPARATOR . $post_type_name !== $engine_source->get_name() ) {
						continue;
					}

					$rules = $engine_source->get_rules_as_sql_clauses();

					if ( empty( $rules ) ) {
						continue;
					}

					$mod = new \SearchWP\Mod( $this );

					// We need to re-retireve the Rules SQL at runtime so as to work with the local alias.
					$mod->raw_where_sql( function( $runtime ) use ( $engine_source ) {
						$rules = $engine_source->get_rules_as_sql_clauses( $runtime->get_local_table_alias() . '.comment_post_ID' );

						return '1=1 AND ' . implode( ' AND ', $rules );
					} );

					$mods[] = $mod;
				}
			}

			return $mods;
		}, 5, 2 );
	}

	/**
	 * Implements Options for this Source.
	 *
	 * @since 4.1.9
	 */
	private function implement_source_options() {
		add_filter( 'searchwp\query\source\options', function( $options, $params ) {
			if ( $this->name !== $params['source']->get_name() ) {
				return $options;
			}

			// There aren't any publicly exposed weight transfer options, but we're going to force a parent transfer.
			$options['weight_transfer'] = [
				'options' => [ [
					'option'     => new \SearchWP\Option( 'col', sprintf(
						// Translators: placeholder is singular post type label.
						__( 'To %s Parent', 'searchwp' ),
						$this->labels['singular']
					) ),
					'value'      => $this->db_post_parent_column, // Just the column name, an alias is created for this Source's table.
					'conditions' => function( $args ) {
						global $wpdb;

						$post_source = new \SearchWP\Sources\Post();

						$debug = "Transferring post.comment weight to {$this->db_post_parent_column}";
						$debug .= $this->strict_transfer ? ' (strict)' : ' (not strict)';
						do_action( 'searchwp\debug\log', $debug, 'source:comment' );

						return [
							'id' => $post_source->get_post_parent_id_case_sql(
								$args,
								$wpdb->posts,
								$this->db_post_parent_column,
								$post_source->get_potential_post_parent_types( $args, 'comment' ),
								$this->strict_transfer
							),
							'source' => $post_source->get_post_parent_source_case_sql(
								$args,
								$wpdb->posts,
								$this->db_post_parent_column,
								$post_source->get_potential_post_parent_types( $args, 'comment' ),
								$this->strict_transfer
							),
						];
					}
				] ],
				'enabled' => true,
				'value'   => null,
				'option'  => 'col',
			];

			return $options;
		}, 5, 2 );
	}

	/**
	 * Adds notice about forced parent attribution.
	 *
	 * @since 4.1
	 * @param Notice[] Existing notices
	 * @return Notice[]
	 */
	protected function notices( $notices ) {
		$notices[] = new Notice( __( 'Note: Comment relevance is automatically transferred to its parent, causing the Comment parent to be returned as a result.', 'searchwp' ), [
			'type'      => 'info',
			'icon'      => 'dashicons dashicons-info',
			'placement' => 'details',
		] );

		return $notices;
	}

	/**
	 * Gets an attribute from a WP_Comment.
	 *
	 * @since 4.1
	 * @param int|string $comment_id The WP_Comment ID.
	 * @param string $attribute The attribute to retrieve.
	 * @return mixed The attribute value.
	 */
	public static function get_comment_attribute( $comment_id, string $attribute ) {
		$comment = get_comment( $comment_id );
		$content = empty( $comment ) || ! isset( $comment->{$attribute} ) ? '' : $comment->{$attribute};

		return apply_filters(
			'searchwp\source\comment\attributes\\' . $attribute,
			$content,
			[ 'comment_id' => $comment_id, ]
		);
	}

	/**
	 * Reuse parent attribution from Posts.
	 *
	 * @since 4.1
	 * @return array
	 */
	protected function weight_transfer_options() {
		// We're going to force parent attribution by default. If we don't then Comments
		// would be considered a custom Source, but that use case is very rare.

		return [];
	}

	/**
	 * Add class hooks.
	 *
	 * @since 4.1
	 * @return void
	 */
	public function add_hooks( array $params = [] ) {
		// Custom Fields.
		if ( ! has_filter( 'searchwp\source\attribute\options', [ $this, 'special_meta_keys' ] ) ) {
			add_filter( 'searchwp\source\attribute\options', [ $this, 'special_meta_keys' ], 9, 2 );
		}

		if ( ! has_filter( 'searchwp\source\attribute\options\special', [ $this, 'special_meta_keys' ] ) ) {
			add_filter( 'searchwp\source\attribute\options\special', [ $this, 'special_meta_keys' ], 9, 2 );
		}

		// If this Source is not active we can bail out early.
		if ( isset( $params['active'] ) && ! $params['active'] ) {
			return;
		}

		if ( ! has_action( 'comment_post', [ $this, 'comment_post' ] ) ) {
			add_action( 'comment_post', [ $this, 'comment_post' ], 10, 2 );
		}

		if ( ! has_action( 'edit_comment', [ $this, 'drop' ] ) ) {
			add_action( 'edit_comment', [ $this, 'drop' ] );
		}

		if ( ! has_action( 'transition_comment_status', [ $this, 'transition_comment_status' ] ) ) {
			add_action( 'transition_comment_status', [ $this, 'transition_comment_status' ], 10, 3 );
		}

		if ( ! has_action( 'save_post', [ $this, 'save_post' ] ) ) {
			add_action( 'save_post', [ $this, 'save_post' ], 999 );
		}

		if ( ! has_action( 'delete_post', [ $this, 'drop_comments_for_post_id' ] ) ) {
			add_action( 'delete_post', [ $this, 'drop_comments_for_post_id' ], 999 );
		}
	}

	/**
	 * Callback from save_post action to see if we need to drop any Comments.
	 *
	 * @since 4.1
	 * @param int|string $post_id The post ID to drop.
	 * @return bool Whether the opration was successful.
	 */
	public function save_post( $post_id ) {
		// Post status valid?
		if ( in_array(
			get_post_status( $post_id ),
			Utils::get_post_type_stati( get_post_type( $post_id ) ), true )
		) {
			return;
		}

		// This post should not have any Comments in the index.
		$this->drop_comments_for_post_id( $post_id );
	}

	/**
	 * Drops all Comments that belong to a \WP_Post.
	 *
	 * @since 4.1
	 * @param int $post_id The \WP_Post ID.
	 * @return void
	 */
	public function drop_comments_for_post_id( int $post_id ) {
		$comment_ids = new \WP_Comment_Query( [
			'fields'  => 'ids',
			'orderby' => false,
			'post_id' => $post_id,
		] );

		if ( ! is_array( $comment_ids ) || empty( $comment_ids ) ) {
			return;
		}

		foreach ( $comment_ids as $comment_id ) {
			\SearchWP::$index->drop( $this, $comment_id );
		}
	}

	/**
	 * Callback when a comment is posted.
	 *
	 * @since 4.1
	 * @param mixed $comment_id The ID of the comment that was posted.
	 * @param mixed $comment_approved Whether the comment is approved.
	 * @return bool Whether the post was dropped.
	 */
	public function comment_post( $comment_id, $comment_approved ) {
		if ( 1 !== $comment_approved ) {
			return;
		}

		$this->drop( $comment_id );
	}

	/**
	 * Drop a Comment from the Index.
	 *
	 * @since 4.1
	 * @param int $comment_id The ID of the comment.
	 * @return bool Whether the post was dropped.
	 */
	public function drop( int $comment_id ) {
		$comment = get_comment( $comment_id );

		return \SearchWP::$index->drop( $this, $comment->comment_ID );
	}

	/**
	 * Callback when a comment changes status.
	 *
	 * @since 4.1
	 * @param mixed $new_status string The new status of the comment.
	 * @param mixed $old_status string The old status of the comment.
	 * @param mixed $comment The comment object.
	 * @return bool Whether the post was dropped.
	 */
	public function transition_comment_status( $new_status, $old_status, $comment ) {
		if ( $new_status === $old_status ) {
			return;
		}

		$indexed_statuses = apply_filters( 'searchwp\source\comment\stati', [ 'approved' ], [
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

		$this->drop( $comment->comment_ID );
	}

	/**
	 * Restrict indexed Comments to those only with an applicable parent Source.
	 *
	 * @since 4.1
	 * @return array
	 */
	protected function db_where() {
		global $wpdb;

		$db_where = [
			'relation' => 'AND',
			[
				'column'  => 'comment_type',
				'value'   => 'comment',
			], [
				'column'  => 'comment_approved',
				'value'   => '1',
			],
		];

		// We can restrict the indexed Comments to those that have applicable parents.
		if ( $this->strict_transfer ) {
			// Filter the array to only WP_Post Sources.
			$global_source_names = $this->get_global_source_names();

			// If there are no applicable parents, bail out.
			if ( empty( $global_source_names ) ) {
				$db_where[] = [
					'column'  => 'comment_post_ID',
					'compare' => '=',
					'value'   => '0',
				];
			} else {
				// TODO: In order to avoid a degree of Index bloat (e.g. indexing Comments belonging to Posts
				// that are excluded by a Post Rule) we should apply Rules here, but that's a huge challenge.
				$sql = $wpdb->prepare( "
					SELECT ID
					FROM {$wpdb->posts}
					WHERE post_type IN (" . implode( ',', array_fill( 0, count( $global_source_names ), '%s' ) ) . ")
				", $global_source_names );

				$db_where[] = [
					'column'  => 'comment_post_ID',
					'compare' => 'IN',
					'value'   => $sql,
					'type'    => 'SQL',
				];
			}
		}

		return apply_filters( 'searchwp\source\comment\db_where', $db_where, [ 'source' => $this, ] );
	}

	/**
	 * Retrieves list of post type names that are potential parents.
	 *
	 * @since 4.1.9
	 * @return string[] Post type names.
	 */
	public function get_global_source_names() {
		return array_filter( array_map(
			function( $source_name ) {
				$flag = 'post' . SEARCHWP_SEPARATOR;

				if ( $flag !== substr( $source_name, 0, strlen( $flag ) ) ) {
					return false;
				}

				$post_type = substr( $source_name, strlen( $flag ) );

				return post_type_exists( $post_type ) ? $post_type : false;
			},
			\SearchWP\Utils::get_global_engine_source_potential_parents( $this )
		) );
	}

	/**
	 * Convert an Entry into a WP_Comment.
	 *
	 * @since 4.1
	 * @return \WP_Comment
	 */
	public function entry( \SearchWP\Entry $entry, $query = false ) {
		return get_comment( $entry->get_id() );
	}

	/**
	 * Callback to group meta Attribute Options
	 *
	 * @since 4.1
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
		array_unshift( $keys, new \SearchWP\Option( '*', __( 'Any Meta Key', 'searchwp' ), 'dashicons dashicons-star-filled' ) );

		return $keys;
	}
}
