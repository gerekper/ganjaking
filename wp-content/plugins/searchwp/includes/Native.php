<?php

/**
 * SearchWP Native.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP;

use WP_User_Query;
use SearchWP\Utils;
use SearchWP\Engine;
use SearchWP\Admin\AdminNotices\MissingEngineSourceAdminNotice;

/**
 * Class Native is responsible for taking over native WordPress searches and
 * returning SearchWP-provided results.
 *
 * @since 4.0
 */
class Native {

	/**
	 * Search results.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $results = [];

	/**
	 * Raw results (which includes relevance weight) for Native query.
	 *
	 * @since 4.0
	 * @var array
	 */
	public $weights;

	/**
	 * Whether a notice was displayed about an unsupported post type in an admin search.
	 *
	 * @since 4.0
	 * @var boolean
	 */
	private $notice_given = false;

	/**
	 * Post type for admin searches.
	 *
	 * @since 4.0
	 * @var string
	 */
	private $post_type;

	/**
	 * Native constructor.
	 *
	 * @since 4.0
	 */
	function __construct() {
		add_action( 'pre_get_posts', [ $this, 'init' ], 0 );
		add_action( 'pre_get_users', [ $this, 'init' ], 0 );

		add_filter( 'ajax_query_attachments_args', [ $this, 'find_media' ] );
	}

	/**
	 * Initializes native search integration, depending on query type.
	 *
	 * @since 4.0
	 * @param mixed $query
	 * @return void
	 */
	public function init( $query ) {
		if ( apply_filters( 'searchwp\native\short_circuit', ! $query->is_search(), $query ) ) {
			return $query;
		}

		// WP_User_Query doesn't have our expected methods and it alters the search query.
		if (
			$query instanceof \WP_User_Query
			&& empty( trim( $query->get( 'search' ) ) )
			&& ! apply_filters( 'searchwp\native\force', false, [ 'query' => $query ] )
		) {
			return;
		} elseif (
			! $query instanceof \WP_User_Query
			&& ! ( $query->is_main_query() && $query->is_search() )
			&& ! apply_filters( 'searchwp\native\force', false, [ 'query' => $query ] )
		) {
			return;
		}

		// Flag this $query for SearchWP to work with by defining the Engine name to use for this search.
		$engine = 'default';

		if ( is_admin() && ! wp_doing_ajax() ) {
			$admin_engine = Settings::get_admin_engine();

			// If there's no Admin Engine for this Admin Search, bail out.
			$short_circuit = apply_filters( 'searchwp\native\admin\short_circuit', false, $query );
			if ( empty( $admin_engine ) || $short_circuit ) {
				return;
			}

			$engine = $admin_engine;

			$current_screen  = get_current_screen();
			$this->post_type = $current_screen->post_type;
		}

		$query->set( 'searchwp', $engine );

		if ( $query instanceof \WP_User_Query ) {
			add_filter( 'users_pre_query', [ $this, 'find_users' ], 0, 2 );
		} else {
			$this->find_results();
		}
	}

	/**
	 * Outputs an Admin Notice when an Admin Engine has been defined, but the Source being searched is not added to it.
	 *
	 * @since 4.0
	 * @param Engine $engine      The Engine used.
	 * @param string $source_name The Source name.
	 * @return void
	 */
	public function admin_notice_missing_engine_source( Engine $engine, string $source_name ) {
		if ( ! apply_filters( 'searchwp\native\admin\engine_missing_source_notice', true, [
			'engine' => $engine->get_name(),
			'source' => $source_name,
		] ) ) {
			return;
		}

		$source = array_filter( \SearchWP::$index->get_sources(), function( $source ) use ( $source_name ) {
			return $source === $source_name;
		}, ARRAY_FILTER_USE_KEY );

		if ( empty( $source ) ) {
			return;
		}

		$source = $source[ $source_name ]->get_label();

		$this->notice_given = true;

		new MissingEngineSourceAdminNotice( $engine->get_label(), $source );
	}

	/**
	 * Performs a search on Users.
	 *
	 * @since 4.0
	 * @param mixed $native_results
	 * @param WP_User_Query $query
	 * @return void
	 */
	public function find_users( $native_results, \WP_User_Query $query ) {
		if ( ! $query->get( 'searchwp' ) ) {
			return $native_results;
		}

		// If Users weren't added to this Engine, they won't be in the index.
		$engine = new Engine( $query->get( 'searchwp' ) );

		if ( ! in_array( 'user', array_keys( $engine->get_sources() ) ) ) {
			$this->admin_notice_missing_engine_source( $engine, 'user' );

			return $native_results;
		}

		// There are asterisks flanking the search query.
		$query->set( 'search', str_replace( '*', '', $query->get( 'search' ) ) );

		// Limit the engine to Users only.
		$mod = new \SearchWP\Mod();
		$mod->set_where( [ [
			'column' => 'source',
			'value'  => 'user',
		] ] );

		$search = new \SearchWP\Query( $query->get( 'search' ), [
			'engine' => $engine->get_name(),
			'mods'   => [ $mod ],
		] );

		$users = wp_list_pluck( $search->get_results(), 'id' );
		if ( is_array( $query->query_vars['fields'] ) || 'all' == $query->query_vars['fields'] ) {
			$users = array_map( function( $user_id ) {
				return get_user_by( 'id', $user_id );
			}, $users );
		}

		$query->results = $users;
		$query->request = $search->get_sql();

		return $query->results;
	}

	/**
	 * Whether or not the submitted WP_Query is applicable for Native.
	 *
	 * @since 4.0
	 * @param WP_Query $query
	 * @return boolean
	 */
	public function is_applicable( $query ) {
		if (
			! $query->get( 'searchwp' )
			|| (
				isset( $query->query_vars['s'] ) && empty( $query->query_vars['s'] )
				&& ( isset( $query->query['s'] ) && empty( $query->query['s'] ) )
			)
		) {
			return false;
		}

		// Check for supported Source during Admin search.
		if ( is_admin() && ! wp_doing_ajax() ) {
			$engine = new Engine( $query->get( 'searchwp' ) );

			$supported_post_types = array_filter( array_map( function( $source_name ) {
				$prefix = 'post' . SEARCHWP_SEPARATOR;

				return substr( $source_name, 0, strlen( $prefix ) ) === $prefix
						? substr( $source_name, strlen( $prefix ) )
						: false;
			}, array_keys( $engine->get_sources() ) ) );

			if ( ! in_array( $this->post_type, $supported_post_types, true ) ) {
				if (
					! $this->notice_given
					&& ! in_array( $this->post_type, $supported_post_types )
				) {
					$this->admin_notice_missing_engine_source( $engine, 'post' . SEARCHWP_SEPARATOR . $this->post_type );
				}

				return false;
			}
		}

		return true;
	}

	/**
	 * AJAX callback when Media is being searched.
	 *
	 * @since 4.0
	 * @return array
	 */
	public function find_media( $args ) {
		$admin_engine = Settings::get_admin_engine();

		// If there's no search string or no Admin Engine for this Admin Search, bail out.
		if ( empty( $args['s'] ) || empty( $admin_engine ) ) {
			return $args;
		}

		$engine = new Engine( $admin_engine );

		// If Media was not added to the Admin Engine, bail out.
		if ( ! in_array( 'post' . SEARCHWP_SEPARATOR . 'attachment', array_keys( $engine->get_sources() ) ) ) {
			return $args;
		}

		if ( ! current_user_can( 'upload_files' ) ) {
			wp_send_json_error();
		}

		$query_args = array(
			's'           => Utils::decode_string( $args['s'] ),
			'engine'      => $engine->get_name(),
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'fields'      => 'ids',
		);

		if ( ! empty( $args['posts_per_page'] ) ) {
			$query_args['posts_per_page'] = absint( $args['posts_per_page'] );
		}

		if ( ! empty( $args['paged'] ) ) {
			$query_args['page'] = absint( $args['paged'] );
		}

		$search_results = new \SWP_Query( $query_args );

		// Override the original arguments to facilitate displaying these search results.
		$args = array(
			'post__in'      => empty( $search_results->posts ) ? [ 0 ] : $search_results->posts,
			'orderby'       => 'post__in',
			'post_type'     => 'attachment',
			'post_status'   => 'inherit',
			's'             => '',
		);

		// Re-implement pagination.
		if ( ! empty( $query_args['posts_per_page'] ) ) {
			$args['posts_per_page'] = absint( $query_args['posts_per_page'] );
		}

		if ( ! empty( $query_args['page'] ) && ! empty( $query_args['paged'] ) ) {
			$args['paged'] = absint( $query_args['paged'] );
		}

		return $args;
	}

	/**
	 * Adds hook to filter WP_Query's posts, returning SearchWP-provided results.
	 *
	 * @since 4.0
	 * @return void
	 */
	private function find_results() {
		add_filter( 'posts_pre_query', function( $posts, $query ) {
			if ( ! $this->is_applicable( $query ) ) {
				return $posts;
			}

			// Bail out if outside main query?
			$outside_main_query = apply_filters( 'searchwp\native\strict', true, $query );
			if ( ! $query->is_main_query() && $outside_main_query ) {
				return $posts;
			}

			// We're going to base our args on the query_vars which SWP_Query will pick up where supported.
			$args              = $query->query_vars;
			$args['s']         = get_search_query();
			$args['engine']    = $query->get( 'searchwp' );
			$args['post_type'] = is_admin() && ! wp_doing_ajax() ? $this->post_type : null;

			// Hierarchical post types use differing fields in the admin.
			$args['fields'] = is_admin() && 'id=>parent' !== $query->get( 'fields' ) ? 'ids' : 'all';

			if ( ! empty( $query->get_query_var( 'fields' ) ) ) {
				$args['fields'] = $query->get_query_var( 'fields' );
			}

			// tax_query and meta_query (and date_query) are direct properties.
			$args['tax_query']  = $query->tax_query->queries;
			$args['meta_query'] = $query->meta_query->queries;
			// Date query not supported at this time.

			$this->results = apply_filters(
				'searchwp\native\results',
				new \SWP_Query( apply_filters( 'searchwp\native\args', $args, $query ) )
			);

			// Also set max_num_pages while we're here.
			$query->posts_per_page = $this->results->posts_per_page;
			$query->max_num_pages  = $this->results->max_num_pages;
			$query->found_posts    = $this->results->found_posts;

			// Tack on calculated weights.
			$this->weights = $this->results->posts_weights;

			return $this->results->posts;
		}, 999, 2 );
	}
}
