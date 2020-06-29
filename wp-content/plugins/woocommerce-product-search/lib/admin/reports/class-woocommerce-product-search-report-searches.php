<?php
/**
 * class-woocommerce-product-search-report-searches.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 2.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Report on searches.
 */
class WooCommerce_Product_Search_Report_Searches extends WC_Admin_Report {

	private $total_searches                 = 0;
	private $total_searches_with_results    = 0;
	private $total_searches_without_results = 0;
	private $queries_results = array(
		'results'    => null,
		'no_results' => null
	);

	private $search_query = null;
	private $search_query_mode = 'startswith';
	private $top_query_ids_with_results = null;
	private $top_query_ids_without_results = null;
	private $query_id = null;

	/**
	 * Chart colors.
	 *
	 * @var array
	 */
	public $chart_colours = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->initialize_search_data();
	}

	/**
	 * Get the legend for the main chart sidebar.
	 *
	 * @return array
	 */
	public function get_chart_legend() {
		$legend = array();

		$legend[] = array(
			
			'title' => sprintf( __( '%d Searches with results', 'woocommerce-product-search' ), $this->total_searches_with_results ),
			'color' => $this->chart_colours['searches_results'],
			'highlight_series' => 0,
		);

		$legend[] = array(
			
			'title' => sprintf( __( '%d Searches without results', 'woocommerce-product-search' ), $this->total_searches_without_results ),
			'color' => $this->chart_colours['searches_no_results'],
			'highlight_series' => 1,
		);

		return $legend;
	}

	/**
	 * Output the report.
	 */
	public function output_report() {

		$ranges = array(
			'year'         => __( 'Year', 'woocommerce-product-search' ),
			'last_month'   => __( 'Last month', 'woocommerce-product-search' ),
			'month'        => __( 'This month', 'woocommerce-product-search' ),
			'7day'         => __( 'Last 7 days', 'woocommerce-product-search' ),
		);

		$this->chart_colours = array(
			'searches_results' => '#5cc488',
			'searches_no_results' => '#e74c3c'
		);

		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : '7day';

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
			$current_range = '7day';
		}

		$this->check_current_range_nonce( $current_range );
		$this->calculate_current_range( $current_range );

		include( WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php' );
	}

	/**
	 * Get chart widgets.
	 *
	 * @return array
	 */
	public function get_chart_widgets() {
		$widgets = array();

		$widgets[] = array(
			'title'    => '',
			'callback' => array( $this, 'searches_widgets' ),
		);

		return $widgets;
	}

	/**
	 * Outputs the widgets for searches.
	 */
	public function searches_widgets() {
		global $wpdb;
		$query_table = WooCommerce_Product_Search_Controller::get_tablename( 'query' );
		$is_top_query_with_results = false;
		$is_top_query_without_results = false;
		?>
		<h4 class="section_title"><span><?php esc_html_e( 'Filter by query', 'woocommerce-product-search' ); ?></span></h4>
		<div class="section">
			<form method="GET">
				<div>
					<?php 
?>
					<div>
					<input type="text" name="search_query" value="<?php echo esc_attr( isset( $_GET['search_query'] ) ? trim ( $_GET['search_query'] ) : '' ); ?>" />
					<?php
					echo '&nbsp;';
					printf( '<label title="%s" style="display:inline-block;white-space:nowrap">', esc_attr__( 'Queries that start with &hellip;', 'woocommerce-product-search' ) );
					printf(
						'<input type="radio" name="search_query_mode" value="startswith" %s/>',
						$this->search_query_mode === 'startswith' ? 'checked="checked"' : ''
					);
					esc_html_e( 'Starts', 'woocommerce-product-search' );
					echo '</label>';
					echo '&ensp;';
					printf( '<label title="%s" style="display:inline-block;white-space:nowrap">', esc_attr__( 'Queries that exactly match &hellip;', 'woocommerce-product-search' ) );
					printf(
						'<input type="radio" name="search_query_mode" value="exact" %s/>',
						$this->search_query_mode === 'exact' ? 'checked="checked"' : ''
					);
					esc_html_e( 'Exact', 'woocommerce-product-search' );
					echo '</label>';
					echo '&ensp;';
					printf( '<label title="%s" style="display:inline-block;white-space:nowrap">', esc_attr__( 'Queries that contain &hellip;', 'woocommerce-product-search' ) );
					printf(
						'<input type="radio" name="search_query_mode" value="contains" %s/>',
						$this->search_query_mode === 'contains' ? 'checked="checked"' : ''
					);
					esc_html_e( 'Contains', 'woocommerce-product-search' );
					echo '</label>';
					?>
					</div>
					<div>
					<button style="vertical-align: middle" type="submit" class="submit button" value="<?php esc_attr_e( 'Show', 'woocommerce-product-search' ); ?>"><?php esc_html_e( 'Show', 'woocommerce-product-search' ); ?></button>
					<a style="padding: 1em; vertical-align: middle; float:right;" href="<?php echo esc_url( remove_query_arg( array( 'search_query', 'search_query_mode' ) ) ); ?>"><?php esc_html_e( 'Clear', 'woocommerce-product-search' ); ?></a>
					</div>
					<input type="hidden" name="range" value="<?php echo ( ! empty( $_GET['range'] ) ) ? esc_attr( wp_unslash( $_GET['range'] ) ) : ''; ?>" />
					<input type="hidden" name="start_date" value="<?php echo ( ! empty( $_GET['start_date'] ) ) ? esc_attr( wp_unslash( $_GET['start_date'] ) ) : ''; ?>" />
					<input type="hidden" name="end_date" value="<?php echo ( ! empty( $_GET['end_date'] ) ) ? esc_attr( wp_unslash( $_GET['end_date'] ) ) : ''; ?>" />
					<input type="hidden" name="page" value="<?php echo ( ! empty( $_GET['page'] ) ) ? esc_attr( wp_unslash( $_GET['page'] ) ) : ''; ?>" />
					<input type="hidden" name="tab" value="search" />
					<input type="hidden" name="report" value="searches" />
					<?php wp_nonce_field( 'custom_range', 'wc_reports_nonce', false ); ?>
					<?php 
?>
				</div>
			</form>
		</div>
		<h4 class="section_title"><span><?php esc_html_e( 'Top queries with results', 'woocommerce-product-search' ); ?></span></h4>
		<div class="section">
			<table cellspacing="0">
				<?php
				if ( ! empty( $this->top_query_ids_with_results ) && is_array( $this->top_query_ids_with_results ) ) {
					foreach ( $this->top_query_ids_with_results as $query_id => $count ) {
						$query = $wpdb->get_var( $wpdb->prepare( "SELECT query FROM $query_table WHERE query_id = %d", intval( $query_id ) ) );
						if ( $query ) {
							if ( $this->query_id == $query_id ) {
								$is_top_query_with_results = true;
							}
							echo '<tr class="' . ( ( $this->query_id == $query_id ) ? 'active' : '' ) . '">';
							echo '<td class="count" width="1%">' . esc_html( $count ) . '</td>';
							echo '<td class="name"><a href="' . esc_url( add_query_arg( 'query_id', $query_id ) ) . '">' . esc_html( $query ) . '</a></td>';
							echo '</tr>';
						}
					}
				} else {
					echo '<tr><td colspan="2">' . esc_html__( 'No queries found in range', 'woocommerce-product-search' ) . '</td></tr>';
				}
				?>
				<?php if ( !empty( $_REQUEST['query_id'] ) ) : ?>
				<tr><td colspan="2">
				<a style="padding: 1em; vertical-align: middle; float:right;" href="<?php echo esc_url( remove_query_arg( array( 'query_id' ) ) ); ?>"><?php esc_html_e( 'Clear', 'woocommerce-product-search' ); ?></a>
				</td></tr>
				<?php endif; ?>
			</table>
		</div>
		<h4 class="section_title"><span><?php esc_html_e( 'Top queries without results', 'woocommerce-product-search' ); ?></span></h4>
		<div class="section">
			<table cellspacing="0">
				<?php
				if ( ! empty( $this->top_query_ids_without_results ) && is_array( $this->top_query_ids_without_results ) ) {
					foreach ( $this->top_query_ids_without_results as $query_id => $count ) {
						$query = $wpdb->get_var( $wpdb->prepare( "SELECT query FROM $query_table WHERE query_id = %d", intval( $query_id ) ) );
						if ( $query ) {
							if ( $this->query_id == $query_id ) {
								$is_top_query_without_results = true;
							}
							echo '<tr class="' . ( ( $this->query_id == $query_id ) ? 'active' : '' ) . '">';
							echo '<td class="count" width="1%">' . esc_html( $count ) . '</td>';
							echo '<td class="name"><a href="' . esc_url( add_query_arg( 'query_id', $query_id ) ) . '">' . esc_html( $query ) . '</a></td>';
							echo '</tr>';
						}
					}
				} else {
					echo '<tr><td colspan="2">' . esc_html__( 'No queries found in range', 'woocommerce-product-search' ) . '</td></tr>';
				}
				?>
				<?php if ( !empty( $_REQUEST['query_id'] ) ) : ?>
				<tr><td colspan="2">
				<a style="padding: 1em; vertical-align: middle; float:right;" href="<?php echo esc_url( remove_query_arg( array( 'query_id' ) ) ); ?>"><?php esc_html_e( 'Clear', 'woocommerce-product-search' ); ?></a>
				</td></tr>
				<?php endif; ?>
			</table>
		</div>
		<script type="text/javascript">
			jQuery( '.section_title' ).click( function() {
				var next_section = jQuery( this ).next( '.section' );
				if ( jQuery( next_section ).is( ':visible' ) ) {
					return false;
				}
				jQuery( '.section:visible' ).slideUp();
				jQuery( '.section_title' ).removeClass( 'open' );
				jQuery( this ).addClass( 'open' ).next( '.section' ).slideDown();
				return false;
			} );
			jQuery( '.section' ).slideUp( 100, function() {
				<?php if ( !empty( $this->search_query ) ) : ?>
					jQuery( '.section_title:eq(0)' ).click();
				<?php elseif ( $is_top_query_with_results ) : ?>
					jQuery( '.section_title:eq(1)' ).click();
				<?php elseif ( $is_top_query_without_results ) : ?>
					jQuery( '.section_title:eq(2)' ).click();
				<?php else : ?>
					jQuery( '.section_title:eq(1)' ).click();
				<?php endif; ?>
			});
		</script>
		<?php
	}

	/**
	 * Output an export link.
	 */
	public function get_export_button() {
		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : '7day';
		?>
		<a
			href="#"
			download="report-<?php echo esc_attr( $current_range ); ?>-<?php echo esc_attr( date_i18n( 'Y-m-d', current_time( 'timestamp' ) ) ); ?>.csv"
			class="export_csv"
			data-export="chart"
			data-xaxes="<?php esc_attr_e( 'Date', 'woocommerce-product-search' ); ?>"
			data-groupby="<?php echo esc_attr( $this->chart_groupby ); ?>"
		>
			<?php esc_html_e( 'Export CSV', 'woocommerce-product-search' ); ?>
		</a>
		<?php
	}

	private function initialize_search_data() {
		$this->initialize_search_data_widgets();
		$this->initialize_search_data_chart();
	}

	private function initialize_search_data_widgets() {
		global $wpdb, $wp_locale;

		$current_range = !empty( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : '7day';
		if ( !in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
			$current_range = '7day';
		}
		$this->check_current_range_nonce( $current_range );
		$this->calculate_current_range( $current_range );

		if ( isset( $_GET['search_query'] ) ) {
			$this->search_query = sanitize_text_field( wp_unslash( $_GET['search_query'] ) );
		}
		$this->search_query_mode  = !empty( $_REQUEST['search_query_mode'] ) ? $_REQUEST['search_query_mode'] : 'startswith';
		switch( $this->search_query_mode ) {
			case 'startswith' :
			case 'exact' :
			case 'contains' :
				break;
			default :
				$this->search_query_mode = 'startswith';
		}
		if ( !empty( $_GET['query_id'] ) ) {
			$this->query_id = intval( $_GET['query_id'] );
		}

		$hit_table   = WooCommerce_Product_Search_Controller::get_tablename( 'hit' );
		$query_table = WooCommerce_Product_Search_Controller::get_tablename( 'query' );

		$join        = '';
		$conditions  = array( 'h.count > %d' );
		$values      = array( 0 );
		if ( !empty( $this->start_date ) ) {
			$conditions[] = 'h.date >=  %s ';
			$values[]     = date( 'Y-m-d', $this->start_date );
		}
		if ( !empty( $this->end_date ) ) {
			$conditions[] = 'h.date <=  %s ';
			$values[]     = date( 'Y-m-d', $this->end_date );
		}
		if ( !empty( $this->search_query ) ) {
			$join = "LEFT JOIN $query_table q ON h.query_id = q.query_id";
			switch( $this->search_query_mode ) {
				case 'startswith' :
					$conditions[] = "q.query LIKE %s";
					$values[]     = $wpdb->esc_like( $this->search_query ) . '%';
					break;
				case 'exact' :
					$conditions[] = 'q.query = %s';
					$values[]     = $this->search_query;
					break;
				case 'contains' :
					$conditions[] = "q.query LIKE %s";
					$values[]     = '%' . $wpdb->esc_like( $this->search_query ) . '%';
					break;
			}
		}
		if ( !empty( $this->query_id ) ) {
			$conditions[] = 'h.query_id = %d';
			$values[] = intval( $this->query_id );
		}
		$where = implode( ' AND ', $conditions );

		$conditions_no_results = $conditions;
		array_splice( $conditions_no_results, 0, 1, 'count = %d' );
		$where_no_results = implode( ' AND ', $conditions_no_results );

		$rows = $wpdb->get_results( $wpdb->prepare(
			"SELECT COUNT(DISTINCT h.ip) hits, h.query_id from $hit_table h $join WHERE $where GROUP BY h.query_id ORDER BY hits DESC, h.query_id DESC LIMIT 10",
			$values
		) );
		if ( $rows !== null ) {
			foreach ( $rows as $row ) {
				$this->top_query_ids_with_results[$row->query_id] = $row->hits;
			}
		}

		$rows = $wpdb->get_results( $wpdb->prepare(
			"SELECT COUNT(DISTINCT h.ip) hits, h.query_id from $hit_table h $join WHERE $where_no_results GROUP BY h.query_id ORDER BY hits DESC, h.query_id DESC LIMIT 10",
			$values
		) );
		if ( $rows !== null ) {
			foreach ( $rows as $row ) {
				$this->top_query_ids_without_results[$row->query_id] = $row->hits;
			}
		}
	}

	private function initialize_search_data_chart() {
		global $wpdb, $wp_locale;

		$current_range = !empty( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : '7day';
		if ( !in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
			$current_range = '7day';
		}
		$this->check_current_range_nonce( $current_range );
		$this->calculate_current_range( $current_range );

		if ( isset( $_GET['search_query'] ) ) {
			$this->search_query = sanitize_text_field( wp_unslash( $_GET['search_query'] ) );
		}
		$this->search_query_mode  = !empty( $_REQUEST['search_query_mode'] ) ? $_REQUEST['search_query_mode'] : 'startswith';
		switch( $this->search_query_mode ) {
			case 'startswith' :
			case 'exact' :
			case 'contains' :
				break;
			default :
				$this->search_query_mode = 'startswith';
		}
		if ( !empty( $_GET['query_id'] ) ) {
			$this->query_id = intval( $_GET['query_id'] );
		}

		$hit_table   = WooCommerce_Product_Search_Controller::get_tablename( 'hit' );
		$query_table = WooCommerce_Product_Search_Controller::get_tablename( 'query' );

		$join        = '';
		$conditions  = array( 'count > %d' );
		$values      = array( 0 );
		if ( !empty( $this->start_date ) ) {
			$conditions[] = 'h.date >=  %s ';
			$values[]     = date( 'Y-m-d', $this->start_date );
		}
		if ( !empty( $this->end_date ) ) {
			$conditions[] = 'h.date <=  %s ';
			$values[]     = date( 'Y-m-d', $this->end_date );
		}
		if ( !empty( $this->search_query ) ) {
			$join = "LEFT JOIN $query_table q ON h.query_id = q.query_id";
			switch( $this->search_query_mode ) {
				case 'startswith' :
					$conditions[] = "q.query LIKE %s";
					$values[]     = $wpdb->esc_like( $this->search_query ) . '%';
					break;
				case 'exact' :
					$conditions[] = 'q.query = %s';
					$values[]     = $this->search_query;
					break;
				case 'contains' :
					$conditions[] = "q.query LIKE %s";
					$values[]     = '%' . $wpdb->esc_like( $this->search_query ) . '%';
					break;
			}
		}
		if ( !empty( $this->query_id ) ) {
			$conditions[] = 'h.query_id = %d';
			$values[] = intval( $this->query_id );
		}
		$where = implode( ' AND ', $conditions );

		$conditions_no_results = $conditions;
		array_splice( $conditions_no_results, 0, 1, 'count = %d' );
		$where_no_results = implode( ' AND ', $conditions_no_results );

		$query = $wpdb->prepare(
			"SELECT h.date, h.query_id, COUNT(DISTINCT h.ip) searches from $hit_table h $join WHERE $where GROUP BY h.date, h.query_id",
			$values
		);

		$query_no_results = $wpdb->prepare(
			"SELECT h.date, h.query_id, COUNT(DISTINCT h.ip) searches from $hit_table h $join WHERE $where_no_results GROUP BY h.date, h.query_id",
			$values
		);

		$this->queries_results = array(
			'results'    => $wpdb->get_results( $query ),
			'no_results' => $wpdb->get_results( $query_no_results )
		);

		$this->total_searches                 = 0;
		$this->total_searches_with_results    = 0;
		$this->total_searches_without_results = 0;
		foreach ( $this->queries_results as $key => $rows ) {
			if ( $rows !== null ) {
				foreach ( $rows as $row ) {
					$count = intval( $row->searches );
					$this->total_searches += $count;
					switch( $key ) {
						case 'results' :
							$this->total_searches_with_results += $count;
							break;
						case 'no_results' :
							$this->total_searches_without_results += $count;
							break;
					}
				}
			}
		}

	}

	/**
	 * Get the main chart.
	 */
	public function get_main_chart() {

		global $wpdb, $wp_locale;

		$searches = array(
			'results' => array_values(
				$this->prepare_chart_data(
					$this->queries_results['results'],
					'date',
					'searches',
					$this->chart_interval,
					$this->start_date,
					$this->chart_groupby
				)
			),
			'no_results' => array_values(
				$this->prepare_chart_data(
					$this->queries_results['no_results'],
					'date',
					'searches',
					$this->chart_interval,
					$this->start_date,
					$this->chart_groupby
				)
			)
		);

		$chart_data = json_encode( array(
			'searches_results'    => $searches['results'],
			'searches_no_results' => $searches['no_results']
		) );

		?>
		<div class="chart-container">
			<div class="chart-placeholder main"></div>
		</div>
		<script type="text/javascript">
			var main_chart;

			jQuery(function(){
				var order_data = jQuery.parseJSON( '<?php echo $chart_data; ?>' );<?php 
?>

				var drawGraph = function( highlight ) {
					var series = [
						{
							label: "<?php echo esc_js( __( 'Number of searches with matching results', 'woocommerce-product-search' ) ) ?>",
							data: order_data.searches_results,
							color: '<?php echo esc_js( $this->chart_colours['searches_results'] ); ?>',
							points: { show: true, radius: 5, lineWidth: 3, fillColor: '#fff', fill: true },
							lines: { show: true, lineWidth: 4, fill: false },
							shadowSize: 0
						},
						{
							label: "<?php echo esc_js( __( 'Number of searches without matching results', 'woocommerce-product-search' ) ) ?>",
							data: order_data.searches_no_results,
							color: '<?php echo esc_js( $this->chart_colours['searches_no_results'] ); ?>',
							points: { show: true, radius: 5, lineWidth: 3, fillColor: '#fff', fill: true },
							lines: { show: true, lineWidth: 4, fill: false },
							shadowSize: 0
						}
					];

					if ( highlight !== 'undefined' && series[ highlight ] ) {
						highlight_series = series[ highlight ];
						highlight_series.color = '#9c5d90';
						if ( highlight_series.bars ) {
							highlight_series.bars.fillColor = '#9c5d90';
						}
						if ( highlight_series.lines ) {
							highlight_series.lines.lineWidth = 5;
						}
					}

					main_chart = jQuery.plot(
						jQuery('.chart-placeholder.main'),
						series,
						{
							legend: {
								show: false
							},
							grid: {
								color: '#aaa',
								borderColor: 'transparent',
								borderWidth: 0,
								hoverable: true
							},
							xaxes: [ {
								color: '#aaa',
								position: "bottom",
								tickColor: 'transparent',
								mode: "time",
								timeformat: "<?php echo ( 'day' === $this->chart_groupby ) ? '%d %b' : '%b'; ?>",
								monthNames: <?php echo json_encode( array_values( $wp_locale->month_abbrev ) ) ?>,
								tickLength: 1,
								minTickSize: [1, "<?php echo esc_js( $this->chart_groupby ); ?>"],
								font: {
									color: "#aaa"
								}
							} ],
							yaxes: [
								{
									min: 0,
									minTickSize: 1,
									tickDecimals: 0,
									color: '#ecf0f1',
									font: { color: "#aaa" }
								},
								{
									position: "right",
									min: 0,
									tickDecimals: 2,
									alignTicksWithAxis: 1,
									color: 'transparent',
									font: { color: "#aaa" }
								}
							],
						}
					);

					jQuery('.chart-placeholder').resize();
				}

				drawGraph();

				jQuery('.highlight_series').hover(
					function() {
						drawGraph( jQuery(this).data('series') );
					},
					function() {
						drawGraph();
					}
				);
			});
		</script>
		<?php
	}
}
