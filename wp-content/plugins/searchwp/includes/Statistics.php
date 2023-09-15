<?php

/**
 * SearchWP Statistics.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP;

use SearchWP\Query;
use SearchWP\Settings;
use SearchWP\Index\Tables\LogTable;

/**
 * Class Statistics is responsible for logging searches and displaying statistics.
 *
 * @since 4.0
 */
class Statistics {

	/**
	 * Database table.
	 *
	 * @since 4.0
	 * @var   LogTable
	 */
	private $db_table;

	/**
	 * Capability requirement for viewing Statistics.
	 *
	 * @since 4.0
     * @since 4.2.6 Visibility changed from public to private.
     *
	 * @var string
	 */
	private static $capability = 'edit_others_posts';

	/**
	 * Statistics constructor.
	 *
	 * @since 4.0
	 */
	function __construct() {

		add_action( 'searchwp\query\ran', [ $this, 'log' ] );
		add_action( SEARCHWP_PREFIX . 'maintenance', [ $this, 'maintenance' ] );
	}

	/**
	 * Getter for capability tag.
	 *
	 * @since 4.2.6
	 *
	 * @return string
	 */
	public static function get_capability() {

		return (string) apply_filters( 'searchwp\statistics\capability', self::$capability );
	}

	/**
	 * Callback for WP_Cron event that executes once a day.
	 *
	 * @since 4.1
	 * @return void
	 */
	public function maintenance() {
		$trim_after = Settings::get( 'trim_stats_logs_after', 'int' );

		if ( empty( $trim_after ) ) {
			return;
		}

		$this->trim_logs( [
			'before' => $trim_after + 1, // Add a day to the interval.
		] );
	}

	/**
	 * Trims the logs table based on passed arguments.
	 * Note: This method is very immature and supports only trimming before a time.
	 *
	 * @since 4.1
	 * @param array $args Arguments
	 * @return void
	 */
	public function trim_logs( array $args ) {
		global $wpdb;

		$db_table = \SearchWP::$index->get_tables()['log'];
		$defaults = [
			'before' => '',
		];

		$args         = wp_parse_args( $args, $defaults );
		$args['site'] = [ get_current_blog_id() ];

		// Store the values for WPDB prepare call.
		$values = array_merge(
			[ absint( $args['before'] ) ],
			$args['site']
		);

		$wpdb->query( $wpdb->prepare( "
			DELETE FROM {$db_table->table_name}
			WHERE tstamp < DATE_SUB(NOW(), INTERVAL %d DAY)
			AND site IN (" . implode( ', ', array_fill( 0, count( $args['site'] ), '%d' ) ) . ")
		", $values ) );
	}

	/**
	 * Logs searches.
	 *
	 * @since 4.0
	 * @param Query $query
	 * @return false|int
	 */
	public function log( Query $query ) {
		global $wpdb;

		// Only log the initial search page of results. All other pages can be skipped to prevent logging multiple times the same search.
		if ( $query->get_args()['page'] > 1 ) {
			return false;
		}

		$keywords = $query->get_keywords();

		if ( empty( $keywords ) ) {
			return false;
		}

		// If it's an ignored query we don't need to clutter the database with it.
		if ( ! apply_filters(
			'searchwp\statistics\log',
			! self::is_query_ignored( $query->get_keywords() ),
			$query
		) ) {
			return false;
		}

		$this->db_table = \SearchWP::$index->get_tables()['log'];

		return $wpdb->insert(
			$this->db_table->table_name,
			[
				'query'  => $keywords,
				'tstamp' => current_time( 'mysql' ),
				'hits'   => $query->found_results,
				'engine' => $query->get_engine()->get_name(),
				'site'   => get_current_blog_id(),
			],
			[ '%s', '%s', '%d', '%s', '%d' ]
		);
	}

	/**
	 * Whether the submitted query is ignored.
	 *
	 * @since 4.1
	 * @var string $query The query to check.
	 * @return boolean
	 */
	public static function is_query_ignored( string $query ) {
		$ignored_queries = Settings::get( 'ignored_queries', 'array' );
		$ignored_queries = array_map( 'strtolower', $ignored_queries );

		$ignored = array_filter( $ignored_queries, function( $ignored ) use ( $query, $ignored_queries ) {
			$ignore_this = false;

			// Exact match?
			if ( $ignored === $query ) {
				$ignore_this = true;
			}

			// Partial match?
			if ( false !== strpos( $ignored, '*' ) ) {
				$ignore_this = fnmatch( $ignored, $query );
			}

			// Filtered match?
			$ignore_this = (bool) apply_filters( 'searchwp\statistics\ignored_query', $ignore_this, [
				'query'   => $query,
				'ignored' => $ignored_queries,
			] );

			return $ignore_this;
		} );

		return ! empty( $ignored );
	}

	/**
	 * Resets Statistics data.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function reset( $all_sites = false ) {
		global $wpdb;

		$db_table = \SearchWP::$index->get_tables()['log'];

		if ( $all_sites ) {
			$db_table->truncate();
		} else {
			$wpdb->query( $wpdb->prepare( "
				DELETE FROM {$db_table->table_name}
				WHERE site = %d",
				get_current_blog_id()
			) );
		}
	}

	/**
	 * Ignore a search query.
	 *
	 * @since 4.0.18
	 * @param string $query The query to ignore.
	 * @return void
	 */
	public static function ignore_query( string $query) {
		$query = strtolower( trim( $query ) );

		if ( empty( $query ) ) {
			return false;
		}

		$ignored = Settings::get( 'ignored_queries' );

		if ( ! is_array( $ignored ) ) {
			$ignored = [];
		}

		$ignored[] = $query;
		$ignored   = array_map( 'strtolower', $ignored );
		$ignored   = array_unique( $ignored );

		Settings::update( 'ignored_queries', $ignored );

		return true;
	}

	/**
	 * Unignore an ignored search query.
	 *
	 * @since 4.0.18
	 * @param string $query The query to ignore.
	 * @return void
	 */
	public static function unignore_query( string $query) {
		$query = strtolower( trim( $query ) );

		if ( empty( $query ) ) {
			return false;
		}

		$ignored = Settings::get( 'ignored_queries' );

		if ( ! is_array( $ignored ) ) {
			$ignored = [];
		}

		$ignored = array_map( 'strtolower', $ignored );
		$ignored = array_values( array_diff( $ignored, [ $query ] ) );

		Settings::update( 'ignored_queries', $ignored );

		return true;
	}

	/**
	 * Retreives all Statistics.
	 *
	 * @since 4.0
	 * @return array
	 */
	public static function get() {
		$ignored = Settings::get( 'ignored_queries', 'array' );
		$ignored = array_map( 'strtolower', $ignored );

		return [
			'ignored' => $ignored,
			'engines' => array_map( function( $engine ) use ( $ignored ) {
				$over_time = self::get_searches_over_time( [
					'days'    => 30,
					'engine'  => $engine->get_name(),
					'exclude' => $ignored,
				] );

				return [
					'engine' => $engine->get_name(),
					'label'  => $engine->get_label(),
					'data'   => [
						'labels'  => wp_list_pluck( $over_time, 'day' ),
						'counts'  => wp_list_pluck( $over_time, 'searches' ),
					],
					'details' => [ [
						'label' => __( 'No Results Searches', 'searchwp' ),
						'data'  => self::get_popular_searches( [
							'days'     => absint( apply_filters( 'searchwp\statistics\no_results\days_30', 30 ) ),
							'engine'   => $engine->get_name(),
							'min_hits' => 0,
							'max_hits' => 0,
							'exclude'  => $ignored,
						] ),
					], [
						'label' => __( 'Today', 'searchwp' ),
						'data'  => self::get_popular_searches( [
							'days'    => absint( apply_filters( 'searchwp\statistics\popular\days_1', 1 ) ),
							'engine'  => $engine->get_name(),
							'exclude' => $ignored,
						] ),
					], [
						'label' => __( 'This Month', 'searchwp' ),
						'data'  => self::get_popular_searches( [
							'days'    => absint( apply_filters( 'searchwp\statistics\popular\days_30', 30 ) ),
							'engine'  => $engine->get_name(),
							'exclude' => $ignored,
						] ),
					], [
						'label' => __( 'This Year', 'searchwp' ),
						'data'  => self::get_popular_searches( [
							'days'    => absint( apply_filters( 'searchwp\statistics\popular\days_365', 365 ) ),
							'engine'  => $engine->get_name(),
							'exclude' => $ignored,
						] ),
					], ],
				];
			}, Settings::get_engines() ),
		];
	}

	/**
	 * Displays the submitted statistics in an HTML table.
	 *
	 * @since 4.0
	 * @param array $statistics Stats as returned by @get_popular_searches.
	 * @param bool  $echo       Whether to echo.
	 * @return string|false|void
	 */
	public static function display( $statistics, $echo = true ) {
		if ( empty( $echo ) ) {
			ob_start();
		}

		if ( empty( $statistics ) ) {
			?>
			<p class="description"><?php esc_html_e( 'No searches for this time period.', 'searchwp' ); ?></p>
			<?php
		} else {
			$classes = apply_filters( 'searchwp\statistics\display\table\class', [] );
			?>
			<table cellpadding="0" cellspacing="0" class="<?php echo esc_attr( implode( ' ', (array) $classes ) ); ?>">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Query', 'searchwp' ); ?></th>
						<th><?php esc_html_e( 'Count', 'searchwp' ); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ( $statistics as $stat ) : ?>
					<tr>
						<td>
							<div title="<?php echo esc_attr( $stat->query ); ?>">
								<?php echo esc_html( $stat->query ); ?>
							</div>
						</td>
						<td>
							<?php echo absint( $stat->searches ); ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<?php
		}

		if ( empty( $echo ) ) {
			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}
	}

	/**
	 * Retrieves searches over time.
	 *
	 * @since 4.0
	 * @param array $args Arguments to consider when finding searches.
	 */
	public static function get_searches_over_time( array $args = [] ) {
		global $wpdb;

		$defaults = [
			'days'     => 1,                         // How many days (from now) to go back.
			'engine'   => 'default',                 // Engine used.
			'exclude'  => [],                        // Excluded search queries.
			'site'     => [ get_current_blog_id() ], // Site(s) to consider.
		];

		$args = wp_parse_args( $args, $defaults );

		if ( 'all' === $args['site'] ) {
			$site_in = '1=1';
			$values  = [ $args['days'], $args['engine'] ];
		} else {
			$site_in = 'site IN (' . implode( ', ', array_fill( 0, count( $args['site'] ), '%d' ) ) . ')';
			$values  = array_merge(
				[ $args['days'], $args['engine'] ],
				$args['site']
			);
		}

		$exclude = '';
		if ( is_array( $args['exclude'] ) && ! empty( $args['exclude'] ) ) {
			$exclude = " AND query NOT IN (" . implode( ', ', array_fill( 0, count( $args['exclude'] ), '%s' ) ) . ') ';
			$values  = array_merge( $values, $args['exclude'] );
		}

		$db_table = \SearchWP::$index->get_tables()['log'];

		$searches_per_day = $wpdb->get_results( $wpdb->prepare( "
			SELECT
				MONTH(tstamp) AS month,
				DAY(tstamp) AS day,
				COUNT(tstamp) AS searches
			FROM {$db_table->table_name}
				WHERE tstamp > DATE_SUB(NOW(), INTERVAL %d day)
					AND engine = %s
					AND query <> ''
					AND {$site_in}
				{$exclude}
			GROUP BY TO_DAYS(tstamp)
			ORDER BY tstamp ASC
			", $values ) );

		return array_reverse( array_map( function( $index ) use ( $searches_per_day ) {
			$timestamp = strtotime( '-'. ( $index ) .' days' );
			$month = date_i18n( 'M', $timestamp );
			$day   = date_i18n( 'd', $timestamp );

			$search_day = array_values( array_filter( $searches_per_day,
				function( $search_from_day ) use ( $timestamp ) {
					return date_i18n( 'j', $timestamp ) === $search_from_day->day
						&& date_i18n( 'n', $timestamp ) === $search_from_day->month;
				}
			) );

			return [
				'day'      => $month . ' ' . $day,
				'searches' => empty( $search_day ) ? 0 : $search_day[0]->searches,
			];
		}, range( 0, $args['days'] ) ) );
	}

	/**
	 * Retrieves popular searches based on submitted arguments.
	 *
	 * @since 4.0
	 * @param array $args Arguments to consider when finding popular searches.
	 */
	public static function get_popular_searches( array $args = [] ) {
		global $wpdb;

		$defaults = [
			'days'     => 1,                         // How many days (from now) to go back.
			'engine'   => 'default',                 // Engine used.
			'exclude'  => [],                        // Excluded search queries.
			'limit'    => 10,                        // How many searches to retrieve.
			'min_hits' => 1,                         // Minimum number of results returned for each search.
			'max_hits' => false,                     // Maximum number of results returned for each search.
			'site'     => [ get_current_blog_id() ], // Site(s) to consider.
		];

		$args = wp_parse_args( $args, $defaults );

		if ( 'all' === $args['site'] ) {
			$site_in = '1=1';
			$values  = [ $args['days'], $args['engine'] ];
		} else {
			$site_in = 'site IN (' . implode( ', ', array_fill( 0, count( $args['site'] ), '%d' ) ) . ')';
			$values  = array_merge(
				[ $args['days'], $args['engine'] ],
				$args['site']
			);
		}

		$min_hits = '';
		if ( false !== $args['min_hits'] ) {
			$min_hits = " AND hits >= %d";
			$values   = array_merge( $values, [ absint( $args['min_hits'] ) ] );
		}

		$max_hits = '';
		if ( false !== $args['max_hits'] ) {
			$max_hits = " AND hits <= %d";
			$values   = array_merge( $values, [ absint( $args['max_hits'] ) ] );
		}

		$exclude = '';
		if ( is_array( $args['exclude'] ) && ! empty( $args['exclude'] ) ) {
			// We need to separate exact and partial match ignored queries because they're processed differently.
			$exacts = array_filter( $args['exclude'], function( $ignored ) {
				return false === strpos( $ignored, '*' );
			} );

			$partials = array_filter( $args['exclude'], function( $ignored ) {
				return false !== strpos( $ignored, '*' );
			} );

			if ( ! empty( $exacts ) ) {
				$exclude .= " AND LOWER(query) NOT IN (" . implode( ', ', array_fill( 0, count( $exacts ), '%s' ) ) . ') ';
				$values  = array_merge( $values, array_map( 'strtolower', $exacts ) );
			}

			if ( ! empty( $partials ) ) {
				$exclude .= " AND (" . implode( ' AND ', array_fill( 0, count( $partials ), 'LOWER(query) NOT LIKE %s' ) ) . ') ';
				$values  = array_merge( $values, array_map( 'strtolower', array_map( function( $partial ) use ( $wpdb ) {
					return str_replace( '*', '%', $wpdb->esc_like( $partial ) );
				}, $partials ) ) );
			}
		}

		$values   = array_merge( $values, [ $args['limit'] ] );
		$db_table = \SearchWP::$index->get_tables()['log'];

		return $wpdb->get_results( $wpdb->prepare( "
			SELECT query, count(query) AS searches
			FROM {$db_table->table_name}
			WHERE tstamp > DATE_SUB(NOW(), INTERVAL %d DAY)
			AND engine = %s
			AND {$site_in}
			{$min_hits}
			{$max_hits}
			{$exclude}
			GROUP BY query
			ORDER BY searches DESC, tstamp DESC
			LIMIT %d
		", $values ) );
	}
}
