<?php

/**
 * SearchWP's Query.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP;

use SearchWP\Mod;
use SearchWP\Support\Arr;
use SearchWP\Utils;
use SearchWP\Entry;
use SearchWP\Engine;
use SearchWP\Source;
use SearchWP\Tokens;
use SearchWP\Logic\AndLimiter;
use SearchWP\Logic\PhraseLimiter;

/**
 * Class Query performs searches against the Index.
 *
 * @since 4.0
 */
class Query {

	/**
	 * Unique query id.
	 *
	 * @since 4.2.9
	 *
	 * @var string
	 */
	private $id;

	/**
	 * The submitted search string.
	 *
	 * @since 4.1.20
	 * @var string
	 */
	private $keywords_orig;

	/**
	 * The submitted search string with modifications.
	 *
	 * @since 4.0
	 * @var string
	 */
	private $keywords;

	/**
	 * Suggested search string.
	 *
	 * @since 4.0
	 * @var bool|string
	 */
	private $suggested_search = false;

	/**
	 * The tokens used for this search.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $tokens;

	/**
	 * Total number of results found.
	 *
	 * @since 4.0
	 * @var int
	 */
	public $found_results = 0;

	/**
	 * Total number of pages of results found.
	 *
	 * @since 4.0
	 * @var int
	 */
	public $max_num_pages = 1;

	/**
	 * Time (in seconds) this Query took to run.
	 *
	 * @since 4.0
	 * @var float
	 */
	public $query_time;

	/**
	 * The results of this search.
	 *
	 * @since 4.0
	 * @var array
	 */
	public $results = [];

	/**
	 * The raw results of this search.
	 *
	 * @since 4.0
	 * @var array
	 */
	public $raw_results = [];

	/**
	 * The arguments for this search.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $args = [];

	/**
	 * The final SQL for this Query.
	 *
	 * @since 4.0
	 * @var string
	 */
	private $sql = '';

	/**
	 * The engine for this Query.
	 *
	 * @since 4.0
	 * @var Engine
	 */
	private $engine;

	/**
	 * Whether to use keyword stems.
	 *
	 * @since 4.0.4
	 * @var boolean
	 */
	public $use_stems;

	/**
	 * The index.
	 *
	 * @since 4.0
	 * @var Index
	 */
	private $index;

	/**
	 * The values to be prepared in the SQL.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $values = [];

	/**
	 * The aliases used for this Query.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $aliases = [];

	/**
	 * The JOINs used for this query.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $joins = [];

	/**
	 * The Mods for this Query.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $mods = [];

	/**
	 * Errors for this Query.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $errors = [];

	/**
	 * Internal LIKE placeholder.
	 *
	 * @since 4.0
	 * @var string
	 */
	private $placeholder;

	/**
	 * The logic modes for the search algorithm.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $algorithm_logic_passes = [ 'or' ];

	/**
	 * The debugging data for the query.
	 *
	 * @since 4.2.9
	 *
	 * @var array
	 */
	private $debug_data = [];

	/**
	 * Query constructor.
	 *
	 * @since 4.0
	 * @param string $search Search string.
	 * @param array  $args   Arguments.
	 * @return void
	 */
	function __construct( string $search, array $args = [] ) {
		// The Index may not exist yet.
		if ( ! did_action( 'wp_loaded' ) && ! doing_action( 'wp_loaded' ) ) {
			do_action( 'searchwp\debug\log', 'Query instantiated before wp_loaded', 'query' );
			$this->errors[] = new \WP_Error(
				'init',
				__( '\\SearchWP\\Query cannot be instaniated until the wp_loaded action has fired.','searchwp' )
			);
		} elseif ( empty( Settings::get_engines() ) ) {
			do_action( 'searchwp\debug\log', 'Query instantiated before initial settings have been saved', 'query' );
			$this->errors[] = new \WP_Error(
				'init',
				__( '\\SearchWP\\Query cannot be instaniated until the initial settings have been saved.','searchwp' )
			);
		} else {
			$time_start  = microtime( true );
			$this->index = \SearchWP::$index;

			// Allow for filtration of the search string.
			$this->keywords_orig = Utils::decode_string( $search );

			$this->set_debug_data( 'string.filter.before', $this->keywords_orig );
			$this->keywords = (string) apply_filters( 'searchwp\query\search_string', $this->keywords_orig, $this );
			$this->set_debug_data( 'string.filter.after', $this->keywords );

			do_action( 'searchwp\debug\log', "Query for: {$this->keywords}", 'query' );

			$this->setup( $args );

			do_action( 'searchwp\query\before', $this );
			$this->set_mods();
			$this->run();
			do_action( 'searchwp\query\after', $this );

			$this->query_time = number_format( microtime( true ) - $time_start, 5 );

			do_action( 'searchwp\debug\log', "Execution time: {$this->query_time}", 'query' );
		}
	}

	/**
	 * Initializer sets our tokens, engine, and arguments.
	 *
	 * @since 4.0
	 * @param array $args Arguments.
	 * @return void
	 */
	public function setup( array $args = [] ) {
		$this->set_id();
		$this->set_placeholder();
		$this->set_args( $args );
		$this->set_engine();

		if ( empty( $this->engine ) ) {
			do_action( 'searchwp\debug\log', 'Invalid Engine', 'query' );
			wp_die(
				'An invalid Engine was provided to <code>\SearchWP\Query</code>:<br><br><code>' . esc_html( print_r( $args['engine'], true ) ) . '</code>',
				__( 'Invalid SearchWP Engine', 'searchwp' ),
				[
					'response'  => 500,
					'link_url'  => 'https://searchwp.com/?p=218851',
					'link_text' => __( 'Review SearchWP Documentation', 'searchwp' ),
				]
			);
		}

		$this->set_tokens( $this->keywords );
	}

	/**
	 * Sets the unique id for the query.
	 *
	 * @since 4.2.9
	 *
	 * @return void
	 */
	private function set_id() {

		$this->id = substr( Utils::get_random_hash(), 0, 7 );
	}

	/**
	 * Gets the unique query id.
	 *
	 * @since 4.2.9
	 *
	 * @return string
	 */
	public function get_id() {

		return $this->id;
	}

	/**
	 * Sets the placeholder to be used with LIKE clauses.
	 *
	 * @since 4.0
	 * @return void
	 */
	private function set_placeholder() {
		$this->placeholder = Utils::get_placeholder();
	}

	/**
	 * Gets the placeholder to be used with LIKE clauses.
	 *
	 * @since 4.1
	 * @return string
	 */
	public function get_placeholder() {
		return $this->placeholder;
	}

	/**
	 * Sets query arguments based on submitted arguments.
	 *
	 * @since 4.0
	 * @param array $args {
	 *     Optional. The arguments to set.
	 *
	 *     @type    string     $engine      Engine name.
	 *     @type    array      $mods        Mods to apply.
	 *     @type    array      $site        Site ID(s) to search.
	 *     @type    int        $per_page    Number of results per page.
	 *     @type    int        $page        Which page of results to return.
	 *     @type    int        $offset      Offset to apply to results.
	 *     @type    string     $fields      Fields to return. Accepts 'default', 'ids', 'all', or 'entries'.
	 *                                          - 'default'    Returns object[] with properties: 'id', 'source',
	 *                                                         'site', 'relevance' (weight).
	 *                                          - 'ids'        Returns int[] of result IDs (NOTE: Source is not
	 *                                                         supplied, use only when Source can be inferred).
	 *                                          - 'all'        Returns array of of results as their native objects.
	 *                                          - 'entries'    Returns Entry[] of results.
	 * }
	 *
	 * @return void.
	 */
	private function set_args( array $args = [] ) {
		$args = apply_filters( 'searchwp\query\args', $args, $this );

		$defaults = [
			'engine'   => 'default',
			'mods'     => [],
			'site'     => is_multisite() ? [ get_current_blog_id() ] : 'all',
			'per_page' => get_option( 'posts_per_page' ),
			'page'     => get_query_var( 'paged', 1 ),
			'offset'   => 0,
			'fields'   => 'default',
		];

		$this->args = wp_parse_args( $args, $defaults );

		if ( $this->args['page'] < 1 ) {
			$this->args['page'] = 1;
		}

		if ( ! in_array( (string) $this->args['fields'], [ 'default', 'ids', 'all', 'entries' ] ) ) {
			$this->args['fields'] = 'default';
		}

		if (
			is_array( $this->args['site'] )
			&& 1 === count( $this->args['site'] )
			&& in_array( 'all', $this->args['site'], true )
		) {
			$this->args['site'] = 'all';
		}

		if ( 'all' !== $this->args['site'] ) {
			if ( ! is_array( $this->args['site'] ) ) {
				$this->args['site'] = explode( ',', $this->args['site'] );
			}

			$this->args['site'] = array_map( 'absint', $this->args['site'] );
		}

		// Late customizations (that may find the parsed args useful).
		$this->args['per_page'] = (int) apply_filters( 'searchwp\query\per_page', $this->args['per_page'], $this->args );

		do_action( 'searchwp\debug\log', 'Arguments: ' . implode( ' ', array_map( function( $property, $value ) {
			if ( $value instanceof Engine ) {
				$value = $value->get_name() . ' (previously instantiated)';
			}

			if ( 'mods' === strtolower( $property ) ) {
				$value = count( $value );
			} else if ( 'site' === strtolower( $property ) ) {
				$value = is_array( $value ) ? implode( ', ', $value ) : $value;
			} else if ( is_array( $value ) ) {
				$value = empty( $value ) ? '[NONE]' : print_r( $value, true );
			}

			return "{$property}: {$value}";
		}, array_keys( $this->args ), array_values( $this->args ) ) ), 'query' );
	}

	/**
	 * Sets the Engine model for this Query.
	 *
	 * @since 4.0
	 * @return void
	 */
	private function set_engine() {
		$engine = false;

		if ( $this->args['engine'] instanceof Engine ) {
			$engine = $this->args['engine'];
		} else {
			$saved_engines = Settings::get_engines();

			if ( array_key_exists( (string) $this->args['engine'], $saved_engines ) ) {
				$engine = Settings::get_engines()[ $this->args['engine'] ];
			}
		}

		if ( $engine && empty( $engine->get_errors() ) ) {
			$this->engine = $engine;

			do_action( 'searchwp\debug\log', "Engine: {$this->engine->get_name()}", 'query' );
		} else {
			do_action( 'searchwp\debug\log', "Invalid engine: {$engine}", 'query' );

			$this->errors[] = new \WP_Error(
				'engine',
				__( 'Invalid engine provided to \\SearchWP\\Query', 'searchwp' ),
				$engine
			);
		}
	}

	/**
	 * Gathers all applicable query modifications and prepares optimized JOINs
	 * with aliases, WHERE and ORDER BY clauses.
	 *
	 * @since 4.0
	 * @return array The modifications.
	 */
	private function set_mods() {
		// Performance can be gained by omitting the Source db_where details, but it's
		// an additional safety net to have in case database records were edited manually.
		// We can only apply this limit if we are searching the current site only.
		if (
			apply_filters( 'searchwp\query\do_source_db_where', true, $this )
			&& (
				(
					$this->args['site'] === 'all'
					&& ! is_multisite()
				)
				|| (
					is_array( $this->args['site'] )
					&& count( $this->args['site'] ) === 1
					&& isset( $this->args['site'][0] )
					&& $this->args['site'][0] == get_current_blog_id()
				)
			)
		) {
			$this->set_core_mods();
		} else {
			// Fire an action in case developer wants to implement core mods.
			do_action( 'searchwp\query\core_mods_out_of_bounds', $this );
		}

		// Developers can add their own Mods, but not tinker with the core Mods (if applicable).
		$this->mods = array_merge( $this->mods, array_filter(
			apply_filters( 'searchwp\query\mods', $this->args['mods'], $this ),
			function( $mod ) {
				return $mod instanceof Mod;
			}
		) );

		// Append Mods values to tracking property.
		if ( ! empty( $this->mods ) ) {
			$this->values = array_merge(
				$this->values,
				call_user_func_array( 'array_merge', array_map( function( $mod ) {
					return $mod->get_values();
				}, $this->mods ) )
			);
		}

		// Establish Mods aliases for subsequent use.
		$this->assign_mods_aliases();

		if ( ! empty( $this->mods ) ) {
			do_action( 'searchwp\debug\log', 'Mods: ' . count( $this->mods ), 'query' );
			// do_action( 'searchwp\debug\log', $this->mods, 'query' );
		}
	}

	/**
	 * Getter for errors.
	 *
	 * @since 4.0
	 * @return array
	 */
	public function get_errors() {
		return $this->errors;
	}

	/**
	 * Setter for suggested search string.
	 *
	 * @since 4.0
	 * @param Tokens $tokens Tokens for the suggested search.
	 * @return void
	 */
	public function set_suggested_search( Tokens $tokens ) {
		$this->suggested_search = implode( ' ', $tokens->get() );
	}

	/**
	 * Getter for suggested search string.
	 *
	 * @since 4.0
	 * @return bool|string
	 */
	public function get_suggested_search() {
		return $this->suggested_search;
	}

	/**
	 * Getter for Engine.
	 *
	 * @since 4.0
	 * @return Engine
	 */
	public function get_engine() {
		return $this->engine;
	}

	/**
	 * Adds Mods for Soruce db_where clauses.
	 *
	 * @since 4.0
	 * @return void
	 */
	private function set_core_mods() {
		$this->mods = array_map( function( $source_name ) {
			$source = $this->index->get_source_by_name( $source_name );
			$mod = new Mod( $source );
			$mod->set_where( $source );

			return $mod;
		}, array_keys( $this->get_engine_sources() ) );
	}

	/**
	 * Generate aliases for our Mods.
	 *
	 * @since 4.0
	 * @return void
	 */
	private function assign_mods_aliases() {
		$aliases = [];
		$joins   = [];

		// Loop through all JOINs and assign an alias for each unique JOIN.
		$alias_index = 1;
		foreach ( $this->mods as $mod ) {
			// If there's no ON clauses, we can bail out because this Mod had only raw SQL.
			if ( ! empty( $mod->get_on() ) ) {
				// Establish alias for the local table.
				$join_key = $mod->get_local_table() . SEARCHWP_SEPARATOR .
					implode( SEARCHWP_SEPARATOR, array_map( function( $clause ) {
							return $clause['local'] . SEARCHWP_SEPARATOR .
								implode( SEARCHWP_SEPARATOR, (array) $clause['foreign'] );
					}, $mod->get_on() )
				);

				// If we have a redundant JOIN we can assign the alias to the Mod and bail out.
				if ( in_array( $join_key, $aliases, true ) ) {
					$alias = array_search( $join_key, $aliases );
					$mod->set_local_table_alias( $alias );
				} else {
					// We have a new JOIN, process it.
					$aliases[ $this->index->get_alias() . (string) $alias_index ] = $join_key;
					$alias_index++;

					// Teach the Mod about its local table alias.
					$mod_alias = array_search( $join_key, $aliases );
					$mod->set_local_table_alias( $mod_alias );

					$joins[ $mod_alias ] = $mod->get_join_sql();
				}
			}

			// Handle raw JOINs now that we have an alias defined.
			$raw_join_sql = $mod->get_raw_join_sql();
			if ( ! empty( $raw_join_sql ) ) {
				// Facilitate raw JOIN clauses as closures.
				$raw_join_sql = array_unique( array_map( function( $clause ) use ( $mod ) {
					if ( is_callable( $clause ) ) {
						$clause = call_user_func( $clause, $mod, [ 'query' => $this ] );
					}

					return $clause;
				}, $raw_join_sql ) );

				$joins = array_merge( $joins, $raw_join_sql );
			}
		}

		$this->aliases = $aliases;
		$this->joins   = $joins;
	}

	/**
	 * Generate the weight calculation clause.
	 *
	 * @since 4.0
	 * @return array The clauses.
	 */
	private function weight_calc_sql( $relevance = false ) {
		$weights = array_filter( array_map( function( $mod ) use ( $relevance ) {
			$weights = $relevance ? $mod->get_relevances() : $mod->get_weights();
			if ( empty( $weights ) ) {
				return false;
			}

			// Weights can be defined as closures (e.g. if the local alias needs to be referenced).
			$weights = array_map( function( $weight ) use ( $mod ) {
				return is_callable( $weight ) ? call_user_func( $weight, $mod, [ 'query' => $this ] ) : $weight;
			}, $weights );

			return implode( ' + ', $weights );
		}, $this->mods ) );

		return empty( $weights ) ? '' : ' + (' . implode( '+', $weights ) . ')';
	}

	/**
	 * Retrieves custom columns implemented by Mods.
	 *
	 * @since 4.0
	 * @return string
	 */
	private function custom_columns() {
		$cols = array_filter( array_map( function( $mod ) {
			$cols = $mod->get_columns();
			if ( empty( $cols ) ) {
				return false;
			}

			return implode( ',', array_map( function( $alias, $sql ) {
				return $sql . ' AS ' . $alias;
			}, array_keys( $cols ), array_values( $cols ) ) );
		}, $this->mods ) );

		return empty( $cols ) ? '' : implode( ',', $cols );
	}

	/**
	 * Retrieves search results.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function run() {
		global $wpdb;

		// Empty tokens are permitted only if the search string was empty as well.
		$has_valid_tokens = ! ( ! empty( $this->keywords_orig ) && empty( $this->tokens ) );

		if ( ! empty( $this->engine ) && ! empty( $this->engine->get_sources() ) && $has_valid_tokens ) {
			// Build the base query and process query values.
			$query = $this->build();
			$this->process_values();

			// Find search results.
			$this->raw_results   = $this->find_results( $query );
			$this->sql           = preg_replace( '/[\n\t\r]{1,}/m', ' ', $wpdb->last_query );
			$this->found_results = (int) $wpdb->get_var( 'SELECT FOUND_ROWS()' );
			$this->max_num_pages = $this->args['per_page'] < 1 ? 1 : ceil( $this->found_results / $this->args['per_page'] );

			// Maybe load native Source Entry objects.
			$results = $this->raw_results;
			$fields  = $this->args['fields'];
			if ( 'entries' === $fields || 'all' === $fields || 'ids' === $fields ) {
				$current_site_id = get_current_blog_id();
				$results = array_map( function( $result ) use ( $current_site_id, $fields ) {
					if ( 'ids' === $fields ) {
						return $result->id;
					}

					$switched_site = false;
					if ( $result->site != $current_site_id ) {
						switch_to_blog( $result->site );
						$switched_site = true;
					}

					$load_data = apply_filters( 'searchwp\query\result\load_data', false, [
						'source' => $result->source,
						'id'     => $result->id,
						'query'  => $this,
					] );

					$all_attributes = apply_filters( 'searchwp\query\result\load_data\all_attributes', false, [
						'source' => $result->source,
						'id'     => $result->id,
						'query'  => $this,
					] );

					$entry = new Entry( $result->source, $result->id, $load_data, $all_attributes );

					// Maybe substitute a native Entry object in for the Entry itself.
					if ( 'all' === $fields ) {
						$entry = $entry->native( $this );
					}

					if ( $switched_site ) {
						restore_current_blog();
					}

					return $entry;
				}, $this->raw_results );
			}

			$this->results = (array) apply_filters( 'searchwp\query\results', $results, $this );
		}

		if ( ! empty( $this->engine ) && ! empty( $this->engine->get_sources() ) ) {
			do_action( 'searchwp\debug\log', "Request: {$this->sql}", 'query' );
			do_action( 'searchwp\debug\log', "Results: {$this->found_results} Pages of results: {$this->max_num_pages}", 'query' );
			do_action( 'searchwp\query\ran', $this );
		}
	}

	/**
	 * Getter for raw results.
	 *
	 * @since 4.0
	 * @return array
	 */
	public function get_raw_results() {
		return $this->raw_results;
	}

	/**
	 * Determine and set the applicable logic passes for the search algorithm.
	 *
	 * @since 4.0
	 * @return void
	 */
	private function set_algorithm_logic_passes() {
		if ( count( $this->tokens ) < 2 ) {
			return;
		}

		if ( apply_filters( 'searchwp\query\logic\and', true, $this ) ) {
			array_unshift( $this->algorithm_logic_passes, 'and' );
		}

		if ( Utils::search_string_has_phrases( $this->keywords, $this ) ) {
			// Phrase logic takes highest priority.
			array_unshift( $this->algorithm_logic_passes, 'phrase' );
		}
	}

	/**
	 * Performs logical passes to retrieve optimal results set.
	 *
	 * @since 4.0
	 * @param mixed $query Query
	 * @return array Search results.
	 */
	private function find_results( $query ) {
		$results = [];

		$this->set_algorithm_logic_passes();

		// Logic goes from most restricted to least. Loop until we've got results.
		foreach( $this->algorithm_logic_passes as $logic ) {
			$logic_sql = '';
			$logic_is_strict = apply_filters( 'searchwp\query\logic\\' . $logic . '\strict', false );

			switch ( $logic ) {
				case 'phrase':
					// We only get here if there are phrases to search to begin with.
					$phrase_logic = new PhraseLimiter( $this, apply_filters( 'searchwp\query\logic\and', true, $this ), $logic_is_strict );
					$logic_sql    = $phrase_logic->get_sql();
					break;

				case 'and':
					$and_logic = new AndLimiter( $this, $logic_is_strict );
					$logic_sql = $and_logic->get_sql();

					break;
			}

			// The logic may have failed in a way that should prevent executing the search.
			if ( false === $logic_sql && ! $logic_is_strict ) {
				continue;
			} else if ( false === $logic_sql && $logic_is_strict ) {
				$results = [];
				break;
			}

			$query['from']['where']['_logic'] = $logic_sql;

			// If we're doing OR logic (or the clause is otherwise empty) clean up the query.
			if ( empty( $logic_sql ) && isset( $query['from']['where']['_logic'] ) ) {
				unset( $query['from']['where']['_logic'] );

				if ( count( $this->tokens ) > 1 ) {
					do_action( 'searchwp\debug\log', 'Using OR logic', 'query' );
				}
			}

			$results = $this->execute( $query );

			// If we want this logic pass to be strict, enforce that.
			if ( empty( $results ) && $logic_is_strict ) {
				do_action( 'searchwp\debug\log', 'Breaking on strict logic pass: ' . $logic, 'query' );
				break;
			}

			// If results were found using this logic, there's no more to do.
			if ( ! empty( $results ) ) {
				do_action( 'searchwp\debug\log', "Found results using: {$logic} logic", 'query' );
				break;
			}
		}

		return $results;
	}

	/**
	 * Getter for keywords.
	 *
	 * @since 4.0
	 * return string.
	 */
	public function get_keywords( $original = false ) {
		return $original ? $this->keywords_orig : $this->keywords;
	}

	/**
	 * Executes the query and returns results.
	 *
	 * @since 4.0
	 * @param array $query The search query.
	 * @return array The search results.
	 */
	private function execute( array $query ) {
		global $wpdb;

		$results = $wpdb->get_results(
			apply_filters(
				'searchwp\query\sql',
				$wpdb->prepare(
					$this->generate_sql_from_query( $query ),
					$this->values
				),
				[ 'context' => $this, ]
			)
		);

		return $results;
	}

	/**
	 * Parses query values for LIKE placeholder application.
	 *
	 * @since 4.0
	 * @return void
	 */
	private function process_values() {
		$this->values = array_map( function( $value ) {
			if ( ! is_string( $value ) ) {
				return $value;
			}

			return str_replace( $this->placeholder, '%', $value );
		}, $this->values );
	}

	/**
	 * Implements all cases for Sources that may be transfering weights.
	 *
	 * @since 4.0
	 * @param string $index_alias Index alias.
	 * @return bool|array
	 */
	private function get_weight_transfer_clauses( $index_alias = 's' ) {
		global $wpdb;

		$id_cases     = [];
		$source_cases = [];
		$joins        = [];

		$transfers = array_filter( array_map( function( $source ) {
			$source_options = array_filter(
				apply_filters( 'searchwp\query\source\options', $source->get_options(), [ 'source' => $source, ] ),
				function( $settings, $option ) {
					return 'weight_transfer' === $option && ! empty( $settings['enabled'] );
				},
				ARRAY_FILTER_USE_BOTH
			);

			if ( empty( $source_options ) ) {
				return false;
			}

			return [
				'source'   => $source,
				'transfer' => $source_options,
			];
		}, $this->engine->get_sources() ) );

		$transfers = (array) apply_filters( 'searchwp\query\weight_transfers', $transfers, [
			'query' => $this,
		] );

		// Normalize ID transfer.
		if ( ! empty( $transfers ) ) {
			foreach ( $transfers as $transfer_key => $transfer ) {
				if ( 'id' === $transfer['transfer']['weight_transfer']['option']
					&& ! empty( $transfer['transfer']['weight_transfer']['enabled'] )
					&& empty( $transfer['transfer']['weight_transfer']['value'] )
				) {
					unset( $transfers[ $transfer_key ] );
				}
			}

			$transfers = array_values( $transfers );
		}

		if ( empty( $transfers ) ) {
			return false;
		}

		// We need to build the CASEs for each weight transfer.
		$index = 1;
		foreach ( $transfers as $transfer ) {
			$transfer_alias = 't' . (string) $index;
			$fallback       = '';

			if ( 'col' === $transfer['transfer']['weight_transfer']['option'] ) {
				$column_name = '';
				foreach( $transfer['transfer']['weight_transfer']['options'] as $option ) {
					if ( 'col' === $option['option']->get_value() ) {
						$column_name = $option['value'];
						$column = "{$transfer_alias}.{$column_name}";

						// Allow for fallback conditions.
						if ( isset( $option['fallback'] ) ) {
							$fallback = (array) $option['fallback'];
							$fallback = $wpdb->prepare( " AND {$column} NOT IN(" .
								implode( ', ', array_fill( 0, count( $fallback ), '%s' ) ) . ')', $fallback ); // Assumes string, potentially problematic?
						}

						// Facilitate parent conditions. Expects raw SQL.
						if ( isset( $option['conditions'] ) ) {
							$conditions = $option['conditions'];

							if ( is_callable( $conditions ) ) {
								$conditions = call_user_func( $conditions, [
									'transfer'    => $transfer,
									'alias'       => $transfer_alias,
									'index_alias' => $index_alias,
									'query'       => $this,
								] );
							}

							if ( ! empty( $conditions ) ) {
								// Because we have conditions, $column is now complex.
								$column = $conditions;
							}
						}

						break;
					}
				}
			} else {
				$source_map = null;

				foreach ( (array) $transfer['transfer']['weight_transfer']['options'] as $key => $option ) {
					if ( $transfer['transfer']['weight_transfer']['option'] === $option['option']->get_value() ) {
						$source_map = $option['source_map'];

						break;
					}
				}

				$column = [
					'id'     => $wpdb->prepare( '%s', $transfer['transfer']['weight_transfer']['value'] ),
					'source' => ! is_callable( $source_map ) ? 'null' : call_user_func( $source_map, [
						'transfer'    => $transfer,
						'alias'       => $transfer_alias,
						'index_alias' => $index_alias,
						'query'       => $this,
						'id'          => $transfer['transfer']['weight_transfer']['value'],
					] ),
				];
			}

			$id_cases[]     = $wpdb->prepare( "WHEN {$index_alias}.source = %s {$fallback} THEN {$column['id']}", $transfer['source']->get_name() );
			$source_cases[] = $wpdb->prepare( "WHEN {$index_alias}.source = %s {$fallback} THEN {$column['source']}", $transfer['source']->get_name() );

			// We also need to JOIN to each weight transfer table.
			$joins[] = " LEFT JOIN {$transfer['source']->get_db_table()} {$transfer_alias}
							ON {$index_alias}.id = {$transfer_alias}.{$transfer['source']->get_db_id_column()} ";

			$index++;
		}


		return [
			'id_cases'     => empty( $id_cases )     ? [ "{$index_alias}.id" ]     : 'CASE ' . implode( ' ', $id_cases )     . " ELSE {$index_alias}.id END AS id",
			'source_cases' => empty( $source_cases ) ? [ "{$index_alias}.source" ] : 'CASE ' . implode( ' ', $source_cases ) . " ELSE {$index_alias}.source END AS source",
			'joins'        => $joins,
		];
	}

	/**
	 * Builds the query array.
	 *
	 * @since 4.0
	 * @return array The search query as an associative array.
	 */
	private function build() {
		$index_alias      = $this->index->get_alias();
		$weight_transfers = $this->get_weight_transfer_clauses( $index_alias );

		return (array) apply_filters( 'searchwp\query', [
			'select'   => [
				"{$index_alias}.id",
				"{$index_alias}.source",
				"{$index_alias}.site",
				"SUM(relevance) {$this->weight_calc_sql( true )} AS relevance"
			],
			'from'     => [
				'select'   => [
					false === $weight_transfers ? "{$index_alias}.id"     : $weight_transfers['id_cases'],
					false === $weight_transfers ? "{$index_alias}.source" : $weight_transfers['source_cases'],
					"{$index_alias}.site",
					! empty( $this->keywords_orig ) ? "{$index_alias}.attribute" : '',
					! empty( $this->keywords_orig )
						? "((SUM({$index_alias}.occurrences) {$this->weight_cases()}) {$this->weight_calc_sql()} ) AS relevance"
						: "1 AS relevance",
					$this->custom_columns()
				],
				'from'     => [
					! empty( $this->keywords_orig )
						? "{$this->index->get_tables()['index']->table_name} {$index_alias}"
						: "{$this->index->get_tables()['status']->table_name} {$index_alias}"
				],
				'join'     => false === $weight_transfers
					? $this->joins
					: array_merge( $this->joins, $weight_transfers['joins'] ),
				'where'    => [
					'1=1',
					$this->site_where(),
					$this->token_where(),
					$this->index_where(),
					$this->sources_where(),
				],
				'group_by' => [
					"{$index_alias}.site",
					"{$index_alias}.source",
					! empty( $this->keywords_orig ) ? "{$index_alias}.attribute" : '',
					"{$index_alias}.id",
				],
			],
			'join'     => $this->joins,
			'where'    => [ '1=1' ],
			'group_by' => [
				"{$index_alias}.site",
				"{$index_alias}.source",
				"{$index_alias}.id",
			],
			'having'   => [ "relevance > "
				. absint( apply_filters( 'searchwp\query\min_relevance', 0, [ 'query' => $this ] ) )
			],
			'order_by' => $this->build_order_by(),
			'limit'    => $this->limit_sql(),
		], [
			'index_alias' => $index_alias,
			'args'        => $this->args,
			'values'      => &$this->values, // Passed by reference in case an update is necessary.
		] );
	}

	/**
	 * Generates a clause which ensures that any returned results still exist in the Source database table.
	 *
	 * @since 4.0
	 * @return array
	 */
	private function _sources_entries_exist() {
		global $wpdb;

		// An optimization can be gained by skipping this, but it's a good safety net.
		// If we're applying the Source db_where logic this is unncessary, so it will provide our default.
		$applicable = ! apply_filters( 'searchwp\query\do_source_db_where', true, $this );
		if ( ! apply_filters( 'searchwp\query\ensure_source_entries_exist', $applicable, $this ) ) {
			return [];
		}

		$sources_entries_exist = [];
		$index_alias   = $this->index->get_alias();

		foreach ( $this->get_engine_sources() as $source => $settings ) {
			$source_model     = $this->index->get_source_by_name( $source );
			$source_name      = $source_model->get_name();
			$source_db_table  = $source_model->get_db_table();
			$source_db_id_col = $source_model->get_db_id_column();

			$sources_entries_exist[] = $wpdb->prepare( "
				EXISTS (
					SELECT {$source_db_id_col}
					FROM {$source_db_table}
					WHERE {$index_alias}.id = {$source_db_table}.{$source_db_id_col}
						AND {$index_alias}.source = %s
				)",
			$source_name );
		}

		return $sources_entries_exist;
	}

	/**
	 * Builds LIMIT SQL clause, applies pagination.
	 *
	 * @since 4.0
	 * @return string SQL clause.
	 */
	private function limit_sql() {
		$per_page = (int) $this->args['per_page'];

		// Disable pagination if posts per page is -1.
		if ( $per_page < 1 ) {
			return '';
		}

		// Defining the offset takes precedence (and breaks pagination).
		$offset = ( (int) $this->args['page'] * $per_page ) - $per_page;
		if ( ! empty( $this->args['offset'] ) ) {
			$offset = (int) $this->args['offset'];
		}

		$this->values[] = (int) apply_filters( 'searchwp\query\limit_offset', $offset,   $this );
		$this->values[] = (int) apply_filters( 'searchwp\query\limit_total',  $per_page, $this );

		return "LIMIT %d, %d";
	}

	/*
	 * Implements site ID limiter.
	 *
	 * @since 4.0
	 * @return string SQL clause.
	 */
	private function site_where() {
		if ( 'all' !== $this->args['site'] ) {
			$this->values = array_merge( $this->values, $this->args['site'] );

			return $this->get_site_limit_sql();
		}

		return '1=1';
	}

	/**
	 * Generates SQL clause to limit to the current site(s).
	 *
	 * @since 4.0
	 * @return string
	 */
	public function get_site_limit_sql() {
		return "{$this->index->get_alias()}.site IN("
			. implode( ',',
				array_fill( 0, count( $this->args['site'] ), '%d' )
			) . ')';
	}

	/**
	 * Implements token ID limiter.
	 *
	 * @since 4.0
	 * @return string SQL clause.
	 */
	private function token_where() {
		if ( empty( $this->keywords_orig ) ) {
			return '';
		}

		$this->values = array_merge( $this->values, array_keys( $this->tokens ) );

		return "{$this->index->get_alias()}.token IN ("
			. implode( ',',
				array_fill( 0, count( $this->tokens ), '%d' )
			) . ')';
	}

	/**
	 * Implements index lmiter(s).
	 *
	 * @since 4.0
	 * @return string SQL clause.
	 */
	private function index_where() {
		$index_mods = [];

		foreach ( $this->mods as $mod ) {

			// If this is a Source Mod, skip it.
			if ( $mod->get_source() ) {
				continue;
			}

			// Handle raw WHERE clauses.
			$raw_where = $mod->get_raw_where_sql();
			if ( ! empty( $raw_where ) ) {
				// Facilitate WHERE clauses as closures.
				$raw_where = array_map( function( $clause ) use ( $mod ) {
					if ( is_callable( $clause ) ) {
						$clause = call_user_func( $clause, $mod, [ 'query' => $this ] );
					}

					return $clause;
				}, $raw_where );
				$index_mods[] = '(' . implode( ' ', $raw_where ) . ')';
			}

			$where = $mod->get_where();

			// Handle WHERE clauses and values.
			$mod_where = Utils::parse_where( $mod->get_local_table(), $where );

			if ( is_array( $mod_where ) ) {
				$index_mods[] = '(' . implode( $mod_where['relation'], $mod_where['placeholders'] ) . ')';
				$this->values = array_merge( $this->values, $mod_where['values'] );
			}
		}

		return empty( $index_mods ) ? ' 1=1 ' : implode( ' AND ', $index_mods );
	}

	/**
	 * Implements Sources limiter(s).
	 *
	 * @since 4.0
	 * @return string SQL clause.
	 */
	private function sources_where() {
		$sources_where = [];
		$index_alias   = $this->index->get_alias();

		foreach ( $this->get_engine_sources() as $source => $settings ) {
			// If there are no Attributes, there's nothing to do.
			if ( empty( $settings['attributes'] ) ) {
				continue;
			}

			// Source limiter.
			$source_where = [ "{$index_alias}.source = %s" ];
			$this->values[] = $source;

			// Source Attributes limiter.
			if ( ! empty( $this->keywords_orig ) ) {
				$source_where[]   = $this->get_source_attributes_as_where_sql( array_keys( $settings['attributes'] ) );
				$attribute_values = $this->get_source_attributes_as_values( array_keys( $settings['attributes'] ) );
				$this->values     = array_merge( $this->values, $attribute_values );
			}

			// Consider Mods for this Source.
			$source_where = array_merge( $source_where, $this->source_where( $source ) );

			// Apply Source Rules.
			$rules = $this->engine->get_source( $source )->get_rules_as_sql_clauses(
				$this->index->get_alias() . '.id'
			);
			if ( ! empty( $rules ) ) {
				$source_where = array_merge( $source_where, $rules );
			}

			// Append these clauses.
			$sources_where[] = '(' . implode( ' AND ', $source_where ) . ')';
		}

		return empty( $sources_where ) ? ' 1=1 ' : '(' . implode( ' OR ', $sources_where ) . ')';
	}

	/**
	 * Generates values from Attributes with support for partial matches.
	 *
	 * @since 4.0
	 * @param array $attributes Engine Source Attributes.
	 * @return array Values for Engine Source Attributes.
	 */
	public function get_source_attributes_as_values( array $attributes ) {
		global $wpdb;

		$attributes = Utils::separate_partial_matches( $attributes );

		// The values must be added in order; full then partial.
		$values = $attributes['full'];

		// Add placeholder'd partial matches.
		foreach ( $attributes['partial'] as $partial ) {
			$values[] = str_replace( '*', $this->placeholder, $wpdb->esc_like( $partial ) );
		}

		return $values;
	}

	/**
	 * Generates SQL clause for submitted Engine Source Attributes.
	 *
	 * @since 4.0
	 * @param array $attributes Engine Source Attributes.
	 * @return string SQL for WHERE clause.
	 */
	public function get_source_attributes_as_where_sql( array $attributes, $index_alias = '' ) {
		$index_alias = empty( $index_alias ) ? $this->index->get_alias() : $index_alias;
		$attributes  = Utils::separate_partial_matches( $attributes);

		$sql = [];

		// Set full matches.
		if ( ! empty( $attributes['full'] ) ) {
			$sql[] = "{$index_alias}.attribute IN ("
				. implode( ',', array_fill( 0, count( $attributes['full'] ), '%s' ) )
				. ')';
		}

		// Maybe set partial matches.
		if ( ! empty( $attributes['partial'] ) ) {
			$sql[] = '('
				. implode( ' OR ',
					array_fill( 0, count( $attributes['partial'] ), "{$index_alias}.attribute LIKE %s" )
				)
				. ')';
		}

		return '(' . implode( ' OR ', $sql ) . ')';
	}

	/**
	 * Implements Source limiter(s).
	 *
	 * @since 4.0
	 * @return array SQL clauses.
	 */
	private function source_where( string $source_name ) {
		$source_join_wheres = [];

		foreach ( $this->mods as $mod ) {
			// If this Mod doesn't have a matching Source there's nothing to do.
			if ( ! $mod->get_source() || $source_name !== $mod->get_source()->get_name() ) {
				continue;
			}

			// There is a Mod registered for this Source, so we need to output the WHERE.
			// If the where or values are empty, utilize those native to the Source.
			$mod_where = $mod->get_where();
			if ( ! empty( $mod_where ) ) {
				$where_clauses = $mod_where instanceof Source
					? $mod_where->db_where_as_values_placeholders(
						$mod->get_local_table_alias() )
					: $mod->get_source()->db_where_as_values_placeholders(
						$mod->get_local_table_alias(),
						$mod->get_where() );

				if ( ! empty( $where_clauses['values'] ) && ! empty( $where_clauses['placeholders'] ) ) {
					$this->values = array_merge( $this->values, $where_clauses['values'] );
					$source_join_wheres[] = '( ' . implode( ' AND ', $where_clauses['placeholders'] ) . ' )';
				}
			}

			// Check for closures in RAW WHEREs.
			$raw_wheres = array_map( function( $where ) use ( $mod ) {
				return is_callable( $where ) ? call_user_func( $where, $mod, [ 'query' => $this ] ) : $where;
			}, $mod->get_raw_where_sql() );

			// Handle any raw WHERE clauses for this Mod.
			$source_join_wheres = array_merge( $source_join_wheres, $raw_wheres );
		}

		return $source_join_wheres;
	}

	/**
	 * Generates a SQL query from the submitted query defined as an array.
	 *
	 * @since 4.0
	 * @param array $query The query structured as an array.
	 * @return string The generated SQL.
	 */
	private function generate_sql_from_query( array $query ) {

		// Clean up the array.
		foreach ( $query['from'] as $group => $clauses ) {
			$query['from'][ $group ] = array_filter( array_map( 'trim', $clauses ) );
		}

		// Process the index query first.
		$index_select   = implode( ',', $query['from']['select'] );
		$index_from     = implode( ' ', $query['from']['from'] );
		$index_join     = implode( ' ', $query['from']['join'] );
		$index_where    = implode( ' AND ', $query['from']['where'] );
		$index_group_by = implode( ',', $query['from']['group_by'] );

		$index_query    = "SELECT {$index_select}
			FROM {$index_from} {$index_join}
			WHERE {$index_where}
			GROUP BY {$index_group_by}";

		// Build the SQL itself
		$index_alias = $this->index->get_alias();
		$select      = implode( ',', $query['select'] );
		$from        = $index_query;
		$join        = implode( ' ', $query['join'] );
		$where       = implode( ' AND ', $query['where'] );
		$group_by    = implode( ', ', $query['group_by'] );
		$having      = implode( ' AND ', $query['having'] );
		$order_by    = implode( ', ', array_unique( $query['order_by'] ) );
		$limit       = $query['limit'];

		return "SELECT SQL_CALC_FOUND_ROWS {$select}
				FROM ({$from}) AS {$index_alias}
				{$join}
				WHERE {$where}
				GROUP BY {$group_by}
				HAVING {$having}
				ORDER BY {$order_by}
				{$limit}";
	}

	/**
	 * Generates ORDER BY clause.
	 *
	 * @since 4.0
	 * @return array Clauses with orders.
	 */
	private function build_order_by() {
		$order_by = [ 10 => [ [ 'column' => 'relevance', 'direction' => 'DESC' ] ], ];

		foreach ( $this->mods as $mod ) {
			$order_bys = $mod->get_order_by();

			if ( empty( $order_bys ) ) {
				continue;
			}

			foreach ( $order_bys as $priority => $clauses ) {
				if ( ! array_key_exists( $priority, $order_by ) ) {
					$order_by[ $priority ] = [];
				}

				// Execute closures if applicable.
				$clauses = array_map( function( $clause ) use ( $mod ) {
					if ( is_callable( $clause['column'] ) ) {
						$clause['column'] = call_user_func( $clause['column'], $mod, [ 'query' => $this ] );
					}

					return $clause;
				}, $clauses );

				$order_by[ $priority ] = array_merge( $order_by[ $priority ], $clauses );
			}
		}

		// Sort by priority.
		ksort( $order_by );

		// Concatenate everything and return.
		return array_map( function( $clause ) {
			$key       = $clause['column'];

			if ( empty( $clause['direction'] ) ) {
				$direction = '';
			} else {
				$direction = 'ASC' === strtoupper( $clause['direction'] ) ? 'ASC' : 'DESC';
			}

			return $key . ' ' . $direction;
		}, call_user_func_array( 'array_merge', $order_by ) );
	}

	/**
	 * If the same Attribute has been added to ALL Sources, we can group. To do that
	 * accurately we are going to first find these 'universal' Source Attributes
	 * and extract them to our weight groups.
	 *
	 * @since 4.0
	 * @param array $sources The Engine Sources
	 * @return array The universal weight groups
	 */
	private function get_universal_weight_groups( array $sources ) {
		$all_attributes = array_keys(
			call_user_func_array(
				'array_merge',
				array_values( wp_list_pluck( $sources, 'attributes' ) )
			)
		);

		$universal_attributes = [];

		foreach ( $all_attributes as $attribute ) {
			$weight       = false;
			$inapplicable = false;

			foreach ( $sources as $source => $source_settings ) {
				// If the Attribute isn't added to this Source, it doesn't apply.
				if ( ! array_key_exists( $attribute, $source_settings['attributes'] ) ) {
					$inapplicable = true;
					break;
				}

				// If the weights don't match across the board, it doesn't apply.
				if ( false === $weight ) {
					$weight = $source_settings['attributes'][ $attribute];
				}

				if ( $weight !== false && $weight !== $source_settings['attributes'][ $attribute] ) {
					$inapplicable = true;
					break;
				}
			}

			if ( $inapplicable || false === $weight ) {
				continue;
			}

			// This Attribute is in ALL Sources, so we can group it.
			if ( ! array_key_exists( $weight, $universal_attributes ) ) {
				$universal_attributes[ $weight ] = [
					'attributes' => [],
					'sources'    => [],
				];
			}

			$universal_attributes[ $weight ]['attributes'][] = $attribute;
			$universal_attributes[ $weight ]['sources']      = array_unique(
				array_merge( $universal_attributes[ $weight ]['sources'], array_keys( $sources ) )
			);
		}

		return $universal_attributes;
	}

	/**
	 * Retrieves engine sources for this Query.
	 *
	 * @since 4.0
	 * @return array Sources.
	 */
	private function get_engine_sources() {
		return Utils::normalize_engine_source_settings( $this->engine );
	}

	/**
	 * Generate weight groups for this engine configuration. Weight groups allow us to
	 * optimize the search query and reduce overhead.
	 *
	 * @since 4.0
	 * @return array The weight groups.
	 */
	public function get_weight_groups() {
		$weight_groups = [];
		$sources       = $this->get_engine_sources();
		$universal     = [];

		// Optimize weight grouping to reduce SQL query length.
		if ( apply_filters( 'searchwp\query\gather_weight_groups', true, $this ) ) {
			$universal = $this->get_universal_weight_groups( $sources );

			if ( ! empty( $universal ) ) {
				foreach( $universal as $weight => $groups ) {
					foreach ( $groups['sources'] as $source ) {
						foreach ( $groups['attributes'] as $attribute ) {
							unset( $sources[ $source ]['attributes'][ $attribute ] );
						}
					}
				}
			}
		}

		// Build weight groups.
		$ungrouped_weight_groups = array_merge(
			array_map( function( $weight, $group ) {
				return [
					'weight'     => $weight,
					'sources'    => $group['sources'],
					'attributes' => $group['attributes'],
				];
			}, array_keys( $universal ), array_values( $universal ) ),
			call_user_func_array( 'array_merge',
				array_map( function( $source, $source_settings ) {
					return array_map( function( $attribute, $weight ) use ( $source, $source_settings ) {
						return [
							'weight'     => $weight,
							'sources'    => [ $source ],
							'attributes' => [ $attribute ],
						];
					},
					array_keys( $source_settings['attributes'] ),
					array_values( $source_settings['attributes'] ) );
			}, array_keys( $sources ), array_values( $sources ) ) )
		);

		// Bundle weight groups by weight.
		foreach( $ungrouped_weight_groups as $ungrouped_weight_group ) {
			$weight = $ungrouped_weight_group['weight'];

			if ( ! array_key_exists( $weight, $weight_groups ) ) {
				$weight_groups[ $weight ] = [];
			}

			$weight_groups[ $weight ][] = $ungrouped_weight_group;
		}

		return $weight_groups;
	}

	/**
	 * Generate a SQL/values pair for our weight calculation CASE.
	 *
	 * @since 4.0
	 */
	private function weight_cases() {
		$weight_groups = $this->get_weight_groups();
		$case          = [];
		$index_alias   = $this->index->get_alias();

		foreach ( $weight_groups as $weight => $pairs ) {
			$case_ors = [];
			foreach ( $pairs as $pair ) {
				$case_or  = "({$index_alias}.source ";
				$case_or .= count( $pair['sources'] ) > 1
					? 'IN (' . implode( ',', array_fill( 0, count( $pair['sources'] ), '%s' ) ) . ')'
					: '= %s';

				$case_or .= " AND {$this->get_source_attributes_as_where_sql( $pair['attributes'] )}";

				$case_ors[] = $case_or . ')';

				$this->values = array_merge(
					$this->values,
					$pair['sources'],
					$this->get_source_attributes_as_values( $pair['attributes'] )
				);
			}

			$case[] = 'WHEN ( ' . implode( ' OR ', $case_ors ) . ' ) THEN %d';

			$this->values[] = $weight;
		}

		if ( empty( $case ) ) {
			return '';
		}

		return '* CASE ' . implode( ' ', $case ) . ' END';
	}

	/**
	 * Retrieves and sets token IDs for this query.
	 *
	 * @since 4.0
	 * @param string $search_string The search query.
	 * @return void
	 */
	private function set_tokens( string $search_string ) {
		// Unless tokens have been made strict, we're going to remove accents from
		// searches as that makes the most sense to find the best search results.
		if ( ! apply_filters( 'searchwp\tokens\strict', false, $this ) ) {
			$search_string = remove_accents( $search_string );
		}

		$this->use_stems = apply_filters(
			'searchwp\query\tokens\use_stems',
			! empty( $this->engine->get_settings()['stemming'] ),
			$this
		);

		// Tokenize the search string.
		$tokens    = new Tokens( $search_string );
		$tokenized = $tokens->get();
		$tokenized = (array) apply_filters( 'searchwp\query\tokens', $tokenized, $this );

		$token_limit = absint( apply_filters( 'searchwp\query\tokens\limit', 10, $this ) );
		$this->set_debug_data( 'query.tokens.limit', $token_limit );

		if ( count( $tokenized ) > $token_limit ) {
			$tokenized = array_slice( $tokenized, 0, $token_limit );
		}

		if ( $this->use_stems ) {
			$this->set_debug_data( 'tokens.stemming.before', $tokenized );
		}

		// Retrieve the token IDs for the tokenized search string.
		$tokens_ids   = Utils::map_token_ids( $tokenized, $this->use_stems, $this );
		$this->tokens = empty( $tokens_ids ) ? [] : $tokens_ids;

		if ( $this->use_stems ) {
			$this->set_debug_data( 'tokens.stemming.after', $this->tokens );
		}

		do_action( 'searchwp\debug\log', 'Tokens: ' . implode( ', ', $this->tokens ), 'query' );
	}

	/**
	 * Getter for the results.
	 *
	 * @since 4.0
	 * @return array The search results.
	 */
	public function get_results() {
		return $this->results;
	}

	/**
	 * Getter for the SQL used to execute this query.
	 *
	 * @since 4.0
	 * @return string The SQL.
	 */
	public function get_sql() {
		return $this->sql;
	}

	/**
	 * Getter for query args.
	 *
	 * @since 4.0
	 * @return array Arguments.
	 */
	public function get_args() {
		return $this->args;
	}

	/**
	 * Getter for query tokens.
	 *
	 * @since 4.0
	 * @return array Tokens.
	 */
	public function get_tokens() {
		return $this->tokens;
	}

	/**
	 * Getter for debug data.
	 *
	 * @since 4.2.9
	 *
	 * @return array
	 */
	public function get_debug_data( $key = null ) {

		return Arr::get( $this->debug_data, $key );
	}

	/**
	 * Write debug data to the query.
	 *
	 * @since 4.2.9
	 *
	 * @param string $key   Debug data array key to add data to.
	 * @param mixed  $value Data to add.
	 */
	public function set_debug_data( $key, $value ) {

		Arr::set( $this->debug_data, $key, $value );
	}
}
