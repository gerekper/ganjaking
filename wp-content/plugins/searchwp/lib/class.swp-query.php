<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class SWP_Query
 *
 * This class is used to perform a search on WP_Post Sources ONLY. All arguments
 * and methods assume that we are working only with WP_Post objects. For all other
 * queries you should use \SearchWP\Query.
 */
class SWP_Query {

	/**
	 * Search query.
	 *
	 * @since 2.6
	 * @access public
	 * @var string
	 */
	public $s;

	/**
	 * List of posts
	 *
	 * @since 2.6
	 * @access public
	 * @var array
	 */
	public $posts = [];

	/**
	 * Engine config in use.
	 *
	 * @since 4.0
	 * @access public
	 * @var \SearchWP\Engine
	 */
	public $engine;

	/**
	 * Pagination limiter
	 *
	 * @since 2.6
	 * @access public
	 * @var int
	 */
	public $posts_per_page = 10;

	/**
	 * Whether to load post objects (vs. IDs only)
	 *
	 * @since 2.6
	 * @access public
	 * @var bool
	 */
	public $load_posts = true;

	/**
	 * Whether to load post objects (vs. IDs only)
	 *
	 * @since 2.6.2
	 * @access public
	 * @var string
	 */
	public $fields = 'all';

	/**
	 * Whether to use paging
	 *
	 * @since 2.6
	 * @access public
	 * @var bool
	 */
	public $nopaging = false;

	/**
	 * The page of results to display
	 *
	 * @since 2.6
	 * @access public
	 * @var int
	 */
	public $paged = 1;

	/**
	 * Post type(s) limiter
	 *
	 * @since 2.6.2
	 * @access public
	 * @var array
	 */
	public $post_type = [];

	/**
	 * Post status limiter
	 *
	 * @since 2.9.16
	 * @access public
	 * @var array
	 */
	public $post_status = [];

	/**
	 * Results pool limiter
	 *
	 * @since 2.6
	 * @access public
	 * @var array
	 */
	public $post__in = [];

	/**
	 * Results pool exclusions
	 *
	 * @since 2.6
	 * @access public
	 * @var array
	 */
	public $post__not_in = [];

	/**
	 * Taxonomy query, as passed to get_tax_sql()
	 *
	 * @since 2.6
	 * @access public
	 * @var object WP_Tax_Query
	 */
	public $tax_query = [];

	/**
	 * Metadata query container
	 *
	 * @since 2.6
	 * @access public
	 * @var object WP_Meta_Query
	 */
	public $meta_query = [];

	/**
	 * Date query container
	 *
	 * @since 2.6
	 * @access public
	 * @var object WP_Date_Query
	 */
	public $date_query = false;

	/**
	 * List of weights for returned posts
	 *
	 * @since 2.6
	 * @access public
	 * @var array
	 */
	public $posts_weights;

	/**
	 * The amount of posts for the current query
	 *
	 * @since 2.6
	 * @access public
	 * @var int
	 */
	public $post_count = 0;

	/**
	 * The amount of found posts for the current query
	 *
	 * If limit clause was not used, equals $post_count
	 *
	 * @since 2.6
	 * @access public
	 * @var int
	 */
	public $found_posts = 0;

	/**
	 * The amount of pages
	 *
	 * @since 2.6
	 * @access public
	 * @var int
	 */
	public $max_num_pages = 0;

	/**
	 * The SQL used to generate search results
	 *
	 * @since 2.6
	 * @access public
	 * @var string
	 */
	public $request;

	/**
	 * The suggested search when no results were found.
	 *
	 * @since 3.1
	 * @access public
	 * @var string
	 */
	public $suggested_search;

	/**
	 * The order clause
	 *
	 * @since 2.9.16
	 * @access public
	 * @var string
	 */
	public $order;

	/**
	 * The (limited) orderby clause
	 *
	 * @since 2.9.16
	 * @access public
	 * @var string
	 */
	public $orderby;

	public $current_post = -1;
	public $in_the_loop = false;
	public $post;

	/**
	 * Query modifications.
	 *
	 * @since 4.0
	 * @access public
	 * @var array
	 */
	public $mods = [];

	/**
	 * Reference to the \SearchWP\Query used.
	 *
	 * @since 4.1
	 * @access public
	 * @var \SearchWP\Query
	 */
	public $query;

	/**
	 * Constructor; fires the search, results are stored in the posts property
	 *
	 * @since 2.6
	 * @param array $args
	 */
	function __construct( array $args = [] ) {
		$defaults = array(
			's'                 => '',
			'engine'            => 'default',
			'posts_per_page'    => intval( get_option( 'posts_per_page' ) ),
			'load_posts'        => true,
			'fields'            => 'all',
			'nopaging'          => false,
			'page'              => null,
			'paged'             => 1,
			'post__in'          => [],
			'post__not_in'      => [],
			'post_type'         => [],
			'post_status'       => [ 'publish' ],
			'tax_query'         => [],
			'meta_query'        => [],
			'date_query'        => [],
			'order'             => 'DESC',
			'orderby'           => 'relevance',
		);

		$args = wp_parse_args( $args, $defaults );

		// Maybe disable paging via nopaging arg.
		if ( $args['nopaging'] ) {
			$args['posts_per_page'] = -1;
		}

		// Support for fields argument.
		if ( 'ids' === $args['fields'] ) {
			$args['load_posts'] = false;
		}

		// A post type of 'any' will simply not limit to one of
		// the post types that has been added to the Engine.
		if (
			'any' === $args['post_type']
			|| ( is_array( $args['post_type'] ) && in_array( 'any', $args['post_type'], true ) )
		) {
			$args['post_type'] = [];
		}

		// WP_Query uses 'paged' so give that precedence.
		if ( ! is_null( $args['page'] ) && is_numeric( $args['page'] ) ) {
			$args['paged'] = intval( $args['page'] );
		}

		// Initial processing of search query. \SearchWP\Query decodes.
		$args['s'] = empty( $args['s'] ) ? get_search_query() : $args['s'];

		if ( isset( $_REQUEST['orderby'] ) ) {
			$this->orderby = is_string( $_REQUEST['orderby'] )
				? stripslashes( $_REQUEST['orderby'] ) : stripslashes_deep( $_REQUEST['orderby'] );
		}

		if ( isset( $_REQUEST['order'] ) ) {
			$this->order = ! empty( $_REQUEST['order'] ) ? $_REQUEST['order'] : 'DESC';
		}

		// Set up properties based on arguments.
		$args = apply_filters( 'searchwp\swp_query\args', $args );
		if ( is_array( $args ) ) {
			foreach ( $args as $property => $val ) {
				$this->__set( $property, $val );
			}
		}

		// If an invalid Engine was passed, bail out.
		if ( empty( \SearchWP\Settings::get_engine_settings( $args['engine'] ) ) ) {
			return;
		}

		$this->engine = new \SearchWP\Engine( $args['engine'] );

		// We're going to JOIN to the wp_posts table.
		$post_types = \SearchWP\Utils::get_post_types();

		if ( empty( $post_types ) ) {
			return;
		}

		// Prep the query based on args.
		$this->maybe_post_type();
		$this->maybe_post_status();
		$this->maybe_post__( 'in' );
		$this->maybe_post__( 'not_in' );
		$this->maybe_tax_query();
		$this->maybe_meta_query();
		$this->maybe_date_query();
		$this->maybe_orderby();

		// Retrieve the results.
		$this->get_search_results();
	}

	/**
	 * Implementation of get_posts() method for consistency; fires a search and returns posts.
	 *
	 * @since 2.6
	 * @return array
	 */
	function get_posts() {
		return $this->posts;
	}

	/**
	 * Magic getter
	 *
	 * @since 2.6
	 * @param string $property The property to get.
	 * @return mixed
	 */
	public function __get( $property ) {
		if ( property_exists( $this, $property ) ) {
			return $this->$property;
		}

		return null;
	}

	/**
	 * Magic setter
	 *
	 * @since 2.6
	 *
	 * @param $property
	 * @param $value
	 *
	 * @return $this
	 */
	public function __set( $property, $value ) {
		if ( property_exists( $this, $property ) ) {
			$this->$property = $value;
		}

		return $this;
	}

	/**
	 * Support post_type argument which allows limiting by post type
	 * and in doing so overriding the submitted engine settings
	 *
	 * @since 2.6.2
	 */
	function maybe_post_type() {
		if ( empty( $this->post_type ) && isset( $_REQUEST['post_type'] ) ) {
			$this->post_type = is_string( $_REQUEST['post_type'] )
				? stripslashes( $_REQUEST['post_type'] ) : stripslashes_deep( $_REQUEST['post_type'] );
		}

		if ( empty( $this->post_type ) ) {
			$this->post_type = [];
		}

		$this->post_type = is_array( $this->post_type ) ? $this->post_type : explode( ',', $this->post_type );
		$this->post_type = array_map( 'trim', $this->post_type );

		// Remove any Sources that are not a WP_Post type.
		foreach ( array_keys( $this->engine->get_sources() ) as $engine_source ) {
			if (
				// Not a WP_Post Source.
				'post' . SEARCHWP_SEPARATOR !== substr( $engine_source, 0, strlen( 'post' . SEARCHWP_SEPARATOR ) )
				|| (
					// Not in the post_type arg.
					! empty( $this->post_type )
					&& ! in_array( substr( $engine_source, strlen( 'post' . SEARCHWP_SEPARATOR ) ), $this->post_type )
				)
			) {
				$this->engine->remove_source( $engine_source );
			}
		}
	}

	/**
	 * Validates supported ORDER BY orders.
	 *
	 * @since 4.0
	 * @return void
	 */
	function validate_orderby_order() {
		// TODO: consider supporting RAND seed.

		$allowed_orderby = array(
			'relevance',
			'date',
			'rand',
			'random',
			'title',
		);

		// Manually correlate ORDERBY and ORDER arrays.
		if ( ! is_array( $this->order ) ) {
			$this->order = [ $this->order ];
		}

		if ( ! is_array( $this->orderby ) ) {
			$this->orderby = [ $this->orderby ];
		}

		// Validate orderbys.
		$this->orderby = array_filter( $this->orderby, function( $orderby ) use ( $allowed_orderby ) {
			return in_array( $orderby, $allowed_orderby, true );
		} );

		if ( empty( $this->orderby ) ) {
			$this->orderby = [ 'relevance' ];
		}

		// Validate orders.
		foreach ( $this->orderby as $index => $orderby ) {
			if ( ! array_key_exists( $index, $this->order ) ) {
				$this->order[ $index ] = 'DESC';
			}

			if ( 'ASC' !== $this->order[ $index ] ) {
				$this->order[ $index ] = 'DESC';
			}
		}
	}

	/**
	 * Support customizing the orderby clause
	 *
	 * @since 2.9.16
	 */
	function maybe_orderby() {
		$this->validate_orderby_order();

		foreach ( (array) $this->orderby as $priority => $orderby ) {
			switch ( $orderby ) {
				case 'date':
					$mod = new \SearchWP\Mod();

					$mod->raw_join_sql( function( $runtime ) {
						global $wpdb;

						return "LEFT JOIN {$wpdb->posts} swpqueryorder ON (swpqueryorder.ID = {$runtime->get_foreign_alias()}.id)";
					} );

					$mod->order_by( 'swpqueryorder.post_date', $this->order[ $priority ], $priority + 1 );

					$this->mods[] = $mod;

					break;

				case 'rand':
				case 'random':
					$mod = new \SearchWP\Mod();
					$mod->order_by( 'random', $priority );

					$this->mods[] = $mod;

					break;

				case 'relevance':
					// By default there is already a relevance Mod in place at priority 10.
					// $mod = new \SearchWP\Mod();
					// $mod->order_by( 'relevance', $this->order[ $priority ], $priority + 1 );

					// $this->mods[] = $mod;
					break;

				case 'title':
					$mod = new \SearchWP\Mod();

					$mod->raw_join_sql( function( $runtime ) {
						global $wpdb;

						return "LEFT JOIN {$wpdb->posts} swpqueryorder ON (swpqueryorder.ID = {$runtime->get_foreign_alias()}.id)";
					} );

					$mod->order_by( 'swpqueryorder.post_title', $this->order[ $priority ], $priority + 1 );

					$this->mods[] = $mod;

					break;
			}
		}
	}

	/**
	 * Support post_status argument which allows limiting by post status
	 * and in doing so overriding the submitted engine settings
	 *
	 * @since 2.6.2
	 */
	function maybe_post_status() {
		if ( empty( $this->post_status ) || empty( $this->post_type ) ) {
			return;
		}

		$this->post_status = is_array( $this->post_status ) ? $this->post_status : explode( ',', $this->post_status );
		$this->post_status = array_map( 'trim', $this->post_status );

		foreach ( $this->post_type as $post_type ) {

			if ( ! post_type_exists( $post_type ) ) {
				continue;
			}

			$mod = new \SearchWP\Mod( 'post' . SEARCHWP_SEPARATOR . $post_type );
			$mod->set_where( [ [
				'column'  => 'post_status',
				'value'   => $this->post_status,
				'compare' => 'IN',
			] ] );

			$this->mods[] = $mod;
		}
	}

	/**
	 * Support post__in argument which allows limitation of potential results
	 * pool either by array of post IDs or by string of comma separated post IDs
	 *
	 * @since 2.6
	 */
	function maybe_post__( $in_or_not = 'in' ) {
		$in_or_not = 'in' === $in_or_not ? 'in' : 'not_in';
		$property  = 'post__' . $in_or_not;

		if ( empty( $this->{$property} ) ) {
			return;
		}

		if ( ! is_array( $this->{$property} ) ) {
			$this->{$property} = explode( ',', \SearchWP\Utils::get_integer_csv_string_from( $this->{$property} ) );
		}

		$this->{$property} = array_unique( array_map( 'intval', $this->{$property} ) );

		if ( empty( $this->{$property} ) ) {
			return;
		}

		$mod = new \SearchWP\Mod();
		$mod->set_where( [ [
			'column'  => 'id',
			'value'   => $this->{$property},
			'compare' => 'post__in' === $property ? 'IN' : 'NOT IN',
			'type'    => 'NUMERIC',
		] ] );

		$this->mods[] = $mod;
	}

	/**
	 * Convert tax_query to something SearchWP understands
	 *
	 * @since 2.6
	 */
	function maybe_tax_query() {
		global $wpdb;

		if ( empty( $this->tax_query ) || ! is_array( $this->tax_query ) ) {
			return;
		}

		// We need to do a bit of detective work depending on the tax_query.
		$alias     = 'swpquerytax';
		$tax_query = new WP_Tax_Query( (array) $this->tax_query );
		$tq_sql    = $tax_query->get_sql( $alias, 'ID' );
		$mod       = new \SearchWP\Mod();

		// If the JOIN is empty, WP_Tax_Query assumes we have a JOIN with wp_posts, so let's make that.
		if ( ! empty( $tq_sql['join'] ) ) {
			// Queue the assumed wp_posts JOIN using our alias.
			$mod->raw_join_sql( function( $runtime ) use ( $wpdb, $alias ) {
				return "LEFT JOIN {$wpdb->posts} {$alias} ON {$alias}.ID = {$runtime->get_foreign_alias()}.id";
			} );

			// Queue the WP_Tax_Query JOIN which already has our alias.
			$mod->raw_join_sql( $tq_sql['join'] );

			// Queue the WP_Tax_Query WHERE which already has our alias.
			$mod->raw_where_sql( '1=1 ' . $tq_sql['where'] );
		} else {
			// There's no JOIN here because WP_Tax_Query assumes a JOIN with wp_posts already
			// exists. We need to rebuild the tax_query SQL to use a functioning alias. The Mod
			// will ensure the JOIN, and we can use that Mod's alias to rebuild our tax_query.
			$mod->set_local_table( $wpdb->posts );
			$mod->on( 'ID', [ 'column' => 'id' ] );

			$mod->raw_where_sql( function( $runtime ) use ( $tax_query ) {
				$tq_sql = $tax_query->get_sql( $runtime->get_local_table_alias(), 'ID' );

				return '1=1 ' . $tq_sql['where'];
			} );
		}

		$this->mods[] = $mod;
	}

	/**
	 * Convert meta_query to something SearchWP understands
	 *
	 * @since 2.6
	 */
	function maybe_meta_query() {
		global $wpdb;

		if ( empty( $this->meta_query ) || ! is_array( $this->meta_query ) ) {
			return;
		}

		$alias      = 'swpquerymeta';
		$meta_query = new WP_Meta_Query( $this->meta_query );
		$mq_sql     = $meta_query->get_sql( 'post', $alias, 'ID', null );

		$mod = new \SearchWP\Mod();
		$mod->set_local_table( $wpdb->posts );
		$mod->on( 'ID', [ 'column' => 'id' ] );

		$mod->raw_join_sql( function( $runtime ) use ( $mq_sql, $alias ) {
			return str_replace( $alias, $runtime->get_local_table_alias(), $mq_sql['join'] );
		} );

		$mod->raw_where_sql( function( $runtime ) use ( $mq_sql, $alias ) {
			return '1=1 ' . str_replace( $alias, $runtime->get_local_table_alias(), $mq_sql['where'] );
		} );

		$this->mods[] = $mod;
	}

	/**
	 * Convert date_query to something SearchWP understands
	 *
	 * @since 2.6
	 */
	function maybe_date_query() {
		global $wpdb;

		if ( empty( $this->date_query ) || ! is_array( $this->date_query ) ) {
			return;
		}

		$date_query = new WP_Date_Query( (array) $this->date_query );
		$dq_sql     = $date_query->get_sql();

		$mod = new \SearchWP\Mod();
		$mod->set_local_table( $wpdb->posts );
		$mod->on( 'ID', [ 'column' => 'id' ] );

		$mod->raw_where_sql( function( $runtime ) use ( $dq_sql ) {
			global $wpdb;

			return '1=1 ' . str_replace( $wpdb->posts . '.', $runtime->get_local_table_alias() . '.', $dq_sql );
		} );

		$this->mods[] = $mod;
	}

	/**
	 * Retrieve the number of posts per page to return
	 *
	 * @since 2.6
	 *
	 * @return int Number of posts per page
	 */
	function get_posts_per_page() {
		return intval( $this->posts_per_page );
	}

	// function get_search_suggestion( $search_query ) {
	// 	if ( empty( $search_query ) ) {
	// 		$search_query = $this->s;
	// 	}

	// 	return SWP()->get_search_suggestion( $search_query, $this->engine );
	// }

	/**
	 * Retrieve search results from SearchWP
	 *
	 * @since 2.6
	 */
	function get_search_results() {
		$search = new \SearchWP\Query( $this->s, [
			'engine'   => $this->engine,
			'per_page' => $this->posts_per_page,
			'page'     => $this->paged,
			'fields'   => $this->fields,
			'mods'     => apply_filters( 'searchwp\swp_query\mods', $this->mods, [ 'swp_query' => $this, ] ),
		] );

		$this->query = $search;
		$this->posts = $search->get_results();

		// Entry ids in SearchWP are strings.
		if ( 'ids' === $this->fields ) {
			$this->posts = array_map( 'absint', $this->posts );
		}

		$this->request       = $search->get_sql();
		$this->found_posts   = $search->found_results;
		$this->max_num_pages = $search->max_num_pages;
		$this->post_count    = count( $search->get_results() );
		$this->posts_weights = $search->get_raw_results();

		do_action( 'searchwp\swp_query\shutdown', $this );
	}

	/**
	 * This is WP_Query->have_posts().
	 *
	 * @since 3.0
	 *
	 * @return bool True if posts are available, false if end of loop.
	 */
	public function have_posts() {
		if ( $this->current_post + 1 < $this->post_count ) {
			return true;
		} elseif ( $this->current_post + 1 == $this->post_count && $this->post_count > 0 ) {
			$this->rewind_posts();
		} elseif ( 0 === $this->post_count ) {
			// No results.
		}

		$this->in_the_loop = false;
		return false;
	}

	/**
	 * Rewind the posts and reset post index.
	 *
	 * @since 1.5.0
	 */
	public function rewind_posts() {
		$this->current_post = -1;
		if ( $this->post_count > 0 ) {
			$this->post = $this->posts[0];
		}
	}

	/**
	 * This is WP_Query->the_post().
	 *
	 * @since 3.0
	 *
	 * @global WP_Post $post
	 */
	public function the_post() {
		global $post;

		$this->in_the_loop = true;
		$post = $this->next_post();

		setup_postdata( $post );
	}

	/**
	 * This is WP_Query->next_post().
	 *
	 * @since 3.0
	 *
	 * @return WP_Post Next post.
	 */
	public function next_post() {
		$this->current_post++;
		$this->post = $this->posts[ $this->current_post ];

		return $this->post;
	}
}
