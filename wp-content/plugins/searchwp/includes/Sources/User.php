<?php

/**
 * SearchWP Users Source.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Sources;

use SearchWP\Entry;
use SearchWP\Source;
use SearchWP\Utils;
use SearchWP\Option;
use SearchWP\Settings;

/**
 * Class User is a Source for WP_User objects.
 *
 * @since 4.0
 */
final class User extends Source {

	/**
	 * Name used for canonical reference to source.
	 *
	 * @since 4.0
	 * @var   string
	 */
	protected $name = 'user';

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
	function __construct() {
		global $wpdb;

		$this->db_table   = $wpdb->users;

		$this->labels     = [
			'plural'   => __( 'Users', 'searchwp' ),
			'singular' => __( 'User', 'searchwp' ),
		];

		$this->attributes = [
			[	// User.
				'name'    => 'user',
				'label'   => __( 'User', 'searchwp' ),
				'default' => Utils::get_min_engine_weight(),
				'data'    => function( $user_id ) {
					$user = new \WP_User( $user_id );

					return $user;
				},
			],
			[	// First Name.
				'name'    => 'firstname',
				'label'   => __( 'First Name', 'searchwp' ),
				'default' => Utils::get_min_engine_weight(),
				'data'    => function( $user_id ) {
					$user = get_user_by( 'id', $user_id );

					return $user->first_name;
				},
			],
			[	// Last Name.
				'name'    => 'lastname',
				'label'   => __( 'Last Name', 'searchwp' ),
				'default' => Utils::get_min_engine_weight(),
				'data'    => function( $user_id ) {
					$user = get_user_by( 'id', $user_id );

					return $user->last_name;
				},
			],
			[	// Custom Fields.
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
					}, Utils::get_meta_keys_for_users( $search ) );
				},
				'allow_custom' => true,
				'data'    => function( $user_id, $meta_key ) {
					// Because partial matching is supported, we're going to work with an array of meta keys even if it's one.
					if ( false !== strpos( '*', $meta_key ) ) {
						$meta_keys = Utils::get_meta_keys_for_users( $meta_key );
					} else {
						$meta_keys = [ $meta_key ];
					}

					$do_shortcodes = Settings::get_single( 'parse_shortcodes', 'boolean' );

					$meta_value = array_filter( array_map( function( $meta_key ) use ( $user_id, $do_shortcodes ) {
						$user_meta = get_user_meta( $user_id, $meta_key, false );

						// If there was only one record, let's clean it up.
						if ( is_array( $user_meta ) && 1 === count( $user_meta ) ) {
							$user_meta = array_values( $user_meta );
							$user_meta = array_shift( $user_meta );
						}

						if ( $do_shortcodes ) {
							$user_meta = is_array( $user_meta ) ? json_decode( do_shortcode( wp_json_encode( $user_meta ) ), true ) : do_shortcode( $user_meta );
						}

						return $user_meta;
					}, $meta_keys ) );

					$meta_value = apply_filters(
						'searchwp\source\user\attributes\meta',
						apply_filters(
							'searchwp\source\user\attributes\meta\\' . $meta_key,
							$meta_value,
							[ 'user_id' => $user_id, ]
						), [
						'user_id'    => $user_id,
						'meta_key'   => $meta_key,
						'meta_value' => $meta_value,
					] );

					return $meta_value;
				},
				'phrases' => [ [
					'table'  => $wpdb->usermeta,
					'column' => 'meta_value',
					'id'     => 'user_id'
				] ],
			],
		];

		$this->rules = $this->rules();
	}

	/**
	 * Restrict available Users to the current Site.
	 *
	 * @since 4.0
	 * @return array
	 */
	protected function db_id_in() {
		global $wpdb;

		return [ $wpdb->prepare( "
			SELECT user_id
			FROM {$wpdb->usermeta}
			WHERE ({$wpdb->usermeta}.meta_key = %s AND {$wpdb->usermeta}.user_id = {$wpdb->users}.ID)",
			$wpdb->get_blog_prefix( get_current_blog_id() ) . 'capabilities'
		) ];
	}

	/**
	 * Defines the Rules for this Source.
	 *
	 * @since 4.0
	 * @return array
	 */
	protected function rules() {
		return [
			[	// Roles.
				'name'        => 'role',
				'label'       => __( 'Role', 'searchwp' ),
				'options'     => false,
				'conditions'  => [ 'IN', 'NOT IN' ],
				'values'      => function( $search = '', $include = [] ) {
					$roles = new \WP_Roles();
					$roles = $roles->get_names();

					// Are we limiting to a subset of Roles?
					if ( ! empty( $include ) && is_array( $include ) ) {
						$roles = array_filter( $roles, function( $role ) use ( $include ) {
							return in_array( $role, $include, true );
						}, ARRAY_FILTER_USE_KEY );
					}

					$roles = array_map( function( $name, $label ) {
						return new Option( $name, $label );
					}, array_keys( $roles ), array_values( $roles ) );

					return $roles;
				},
				'application' => function( $properties ) {
					$condition = 'NOT IN' === $properties['condition'] ? 'role__not_in' : 'role__in';
					$args = [
						'fields' => 'ID',
						'count_total' => false,
					];
					$args[ $condition ] = $properties['value'];
					$users = new \WP_User_Query( $args );

					return $users->request;
				}
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
		return get_user_by( 'id', $entry->get_id() );
	}

	/**
	 * Add class hooks.
	 *
	 * @since 4.0
	 * @return array
	 */
	public function add_hooks( array $params = [] ) {
		// If this Source is not active we can bail out early.
		if ( isset( $params['active'] ) && ! $params['active'] ) {
			return;
		}

		// Reindex when profile is updated.
		if ( ! has_action( 'profile_update', [ $this, 'drop' ] ) ) {
			add_action( 'profile_update', [ $this, 'drop' ], 999 );
		}

		// Drop User when User is deleted.
		if ( ! has_action( 'deleted_user', [ $this, 'drop' ] ) ) {
			add_action( 'deleted_user', [ $this, 'drop' ], 999 );
		}

		// Drop User when User is removed from a site.
		if ( ! has_action( 'remove_user_from_blog', [ $this, 'drop' ] ) ) {
			add_action( 'remove_user_from_blog', [ $this, 'drop' ], 999, 2 );
		}
	}

	/**
	 * Callback to drop a User from the index.
	 *
	 * @since 4.0
	 * @param int $user_id The user ID.
	 * @return bool Whether the operation was successful.
	 */
	public function drop( $user_id, $site_id = 0 ) {
		if ( empty( $site_id ) ) {
			$site_id = get_current_blog_id();
		}

		$switched        = false;
		$current_site_id = get_current_blog_id();

		if ( $site_id !== $current_site_id ) {
			$switched = $current_site_id;
			switch_to_blog( $site_id );
		}

		$index  = \SearchWP::$index;
		$result = $index->drop( $this, $user_id );

		if ( $switched ) {
			restore_current_blog();
		}

		return $result;
	}
}