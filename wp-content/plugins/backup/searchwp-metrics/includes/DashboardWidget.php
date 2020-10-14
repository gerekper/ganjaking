<?php

namespace SearchWP_Metrics;

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Class DashboardWidget
 * @package SearchWP_Metrics
 */
class DashboardWidget {
	protected $override_swp_core_widget = true;
	private $engines;

	/**
	 * Initializer
	 */
	function init() {
		add_filter( 'searchwp\admin\dashboard_widgets\statistics', '__return_false' );
		add_action( 'wp_dashboard_setup', array( $this, 'setup_dashboard_widgets' ), 999 );
	}

	/**
	 * Determines most popular content based on click count
	 */
	function get_popular_clicks_from_popular_searches( $searches ) {
		$popular = array();

		if ( empty( $searches ) ) {
			return $popular;
		}

		foreach ( $this->engines as $engine ) {
			$clicks_for_query = new QueryPopularClicksOverTime( array(
				'engine'              => $engine,
				'limited_to_searches' => $this->get_query_id_from_string( $searches[0]['query'] ),
			) );

			$clicks_for_query_details = $clicks_for_query->get_results();

			if ( empty( $clicks_for_query_details ) ) {
				continue;
			}

			// We're going to build an accumulation array of total counts for popular clicks
			foreach ( $clicks_for_query_details as $popular_click ) {
				if ( ! array_key_exists( $popular_click->post_id, $popular ) ) {
					$popular[ $popular_click->post_id ] = array(
						'post_id'    => absint( $popular_click->post_id ),
						'post_title' => $popular_click->post_title,
						'permalink'  => get_permalink( $popular_click->post_id ),
						'count'      => absint( $popular_click->clicks ),
					);
				} else {
					$popular[ $popular_click->post_id ]['count'] += absint( $popular_click->clicks );
				}
			}
		}

		// Sort the popular posts by overall number of clicks
		usort( $popular, function( $a, $b ) {
			if ( $a['count'] === $b['count'] ) {
				return 0;
			}
			return ( $a['count'] > $b['count'] ) ? -1 : 1;
		});

		$max_popular_posts = apply_filters( 'searchwp_metrics_dashboard_widget_max_popular_posts', 5 );
		if ( count( $popular ) > absint( $max_popular_posts ) ) {
			$popular = array_slice( $popular, 0, absint( $max_popular_posts ) );
		}

		return $popular;
	}

	/**
	 * Finds top popular queries based on search count
	 */
	function find_popular_queries() {
		$popular = array();

		foreach ( $this->engines as $engine ) {
			$engine_popular = new QueryPopularQueriesOverTime( array(
				'engine' => $engine,
			) );

			$engine_popular = $engine_popular->get_results();

			if ( empty( $engine_popular ) ) {
				continue;
			}

			// We're going to build an accumulation array of total counts for popular searches
			foreach ( $engine_popular as $popular_query ) {
				$query_hash = md5( $popular_query->query );

				if ( ! array_key_exists( $query_hash, $popular ) ) {
					$popular[ $query_hash ] = array(
						'query' => $popular_query->query,
						'count' => absint( $popular_query->searchcount ),
					);
				} else {
					$popular[ $query_hash ]['count'] += absint( $popular_query->searchcount );
				}
			}
		}

		// Sort the popular queries by overall number of searches
		usort( $popular, function( $a, $b ) {
			if ( $a['count'] === $b['count'] ) {
				return 0;
			}
			return ( $a['count'] > $b['count'] ) ? -1 : 1;
		});

		// Limit popular queries to a reasonable number
		$max_popular_searches = apply_filters( 'searchwp_metrics_dashboard_widget_max_popular_searches', 5 );
		if ( count( $popular ) > absint( $max_popular_searches ) ) {
			$popular = array_slice( $popular, 0, absint( $max_popular_searches ) );
		}

		return $popular;
	}

	/**
	 * Displays the Widget content
	 */
	function display( $post, $callback_args ) {

		$this->engines = array();

		if ( function_exists( 'SWP' ) ) {
			$swp_settings = SWP()->settings;
			$this->engines = array_keys( $swp_settings['engines'] );
		} else if ( class_exists( '\\SearchWP\\Settings' ) ) {
			$this->engines = array_keys( \SearchWP\Settings::get_engines() );
		}

		$popular = $this->find_popular_queries();

		echo '<div class="searchwp-metrics__widget">';

		$this->display_table(array(
			'title' => __( 'Popular Searches (across all engines)', 'searchwp-metrics' ),
			'data' => $popular,
			'cols' => array(
				'query' => 'Query',
				'count' => 'Searches',
			),
		));

		$this->display_table(array(
			'title' => __( 'Popular Search Content (Based on clicks across all engines)', 'searchwp-metrics' ),
			'data' => $this->get_popular_clicks_from_popular_searches( $popular ),
			'cols' => array(
				'post_title' => 'Title',
				'count' => 'Clicks',
			),
		));

		echo '</div>';

		$this->output_css();
	}

	/**
	 * Translates a string into a query ID
	 */
	function get_query_id_from_string( $query ) {
		global $wpdb;

		$query_id = new QuerySearchSearchQueries( array(
			'query' => $query,
		) );

		$query_id->build_sql();
		$sql = $query_id->get_sql();

		// Might be case insensitive
		$results = $wpdb->get_results( $wpdb->prepare(
			$sql,
			$query
		) );

		return empty( $results ) ? 0 : array_map( 'absint', wp_list_pluck( $results, 'id' ) );
	}


	/**
	 * Outputs HTML table based on array values
	 */
	function display_table( array $args ) {
		?>
		<div class="searchwp-metrics__table">
			<h3><?php echo esc_html( $args['title'] ); ?></h3>
			<table>
				<colgroup>
					<col class="searchwp-metrics__table--primary"/>
					<col class="searchwp-metrics__table--secondary"/>
				</colgroup>
				<thead>
					<tr>
						<?php foreach ( $args['cols'] as $heading ) : ?>
							<th><?php echo esc_html( $heading ); ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $args['data'] as $row ) : ?>
						<tr>
							<?php foreach ( $args['cols'] as $key => $val ) : ?>
								<td>
									<?php if ( 'post_title' === $key && ! empty( $row['permalink'] ) ) : ?>
										<a href="<?php echo esc_url( $row['permalink'] ); ?>">
									<?php endif; ?>
										<?php echo esc_html( $row[ $key ] ); ?>
									<?php if ( 'post_title' === $key && ! empty( $row['permalink'] ) ) : ?>
										</a>
									<?php endif; ?>
								</td>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Output CSS for Widget
	 */
	function output_css() {
		?>
		<style type="text/css">
			.searchwp-metrics__widget table {
				width: 100%;
				margin-right: 0.5em;
			}

			.searchwp-metrics__widget th,
			.searchwp-metrics__widget td {
				text-align: left;
				padding: 0.3em 0;
			}

			.searchwp-metrics__widget td {
				border-top: 1px solid #eee;
			}

			.searchwp-metrics__widget th:first-of-type,
			.searchwp-metrics__widget td:first-of-type {
				padding-right: 2em;
			}

			.searchwp-metrics__table--primary {
				width: 80%;
			}

			.searchwp-metrics__table--secondary {
				width: 20%;
			}

			.searchwp-metrics__table + .searchwp-metrics__table {
				margin-top: 2em;
			}
		</style>
		<?php
	}

	/**
	 * Prevents automatic removal of SearchWP core Widget
	 */
	function prevent_searchwp_stats_override() {
		$this->override_swp_core_widget = false;
	}

	/**
	 * Implements our Dashboard Widget. Removes SearchWP core Widget when applicable.
	 */
	function setup_dashboard_widgets() {
		global $wp_meta_boxes;

		if ( $this->override_swp_core_widget ) {
			if ( ! empty( $wp_meta_boxes['dashboard']['normal']['core']['searchwp_stats'] ) ) {
				unset( $wp_meta_boxes['dashboard']['normal']['core']['searchwp_stats'] );
			}
		}

		$do_widget = apply_filters( 'searchwp_metrics_dashboard_widget', true );

		if ( ! empty( $do_widget ) ) {
			wp_add_dashboard_widget( 'dashboard_widget', __( 'Search Metrics', 'searchwp-metrics' ), array( $this, 'display' ) );
		}
	}
}
