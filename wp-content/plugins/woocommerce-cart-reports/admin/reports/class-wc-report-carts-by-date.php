<?php

/**
 * WC_Report_Carts_By_Date class
 */
class WC_Report_Carts_By_Date extends WC_Admin_Report {
	/**
	 * Chart colors.
	 *
	 * @var array
	 */
	public $chart_colours = array();

	/**
	 * The report data.
	 *
	 * @var stdClass|null
	 */
	private $report_data;

	/**
	 * Get report data.
	 *
	 * @return stdClass
	 */
	public function get_report_data() {
		if ( empty( $this->report_data ) ) {
			$this->query_report_data();
		}

		return $this->report_data;
	}

	/**
	 * Get all data needed for this report and store in the class.
	 */
	private function query_report_data() {
		$this->report_data = new stdClass();

		/**
		 * Get orders and dates in range - we want the SUM of order totals,
		 * COUNT of order items, COUNT of orders, and the date
		 */
		$this->report_data->order_counts = (array) $this->get_order_report_data(
			array(
				'nocache'      => true,
				'data'         => array(
					'ID'            => array(
						'type'     => 'post_data',
						'function' => 'COUNT',
						'name'     => 'count',
						'distinct' => true,
					),
					'post_modified' => array(
						'type'     => 'post_data',
						'function' => '',
						'name'     => 'post_modified',
					),
					'post_date'     => array(
						'type'     => 'post_data',
						'function' => '',
						'name'     => 'post_date',
					),
				),
				'group_by'     => $this->group_by_query,
				'order_by'     => 'post_date ASC',
				'query_type'   => 'get_results',
				'filter_range' => true,
				'order_types'  => wc_get_order_types( 'order-count' ),
				'order_status' => array( 'completed', 'processing', 'on-hold', 'refunded' ),
			)
		);

		$this->report_data->total_orders = absint(
			array_sum( wp_list_pluck( $this->report_data->order_counts, 'count' ) )
		);

		// Now get the cart details to compare.
		$abandoned_carts_items = $this->get_abandoned_cart_items_within_range();

		/**
		 * We need to transform our abandoned carts into an object for the
		 * prepare_chart_data call below.
		 */
		$this->report_data->abandoned_carts_items = array();
		foreach ( $abandoned_carts_items as $abandoned_cart ) {
			$object                = new stdClass();
			$object->post_modified = $abandoned_cart->post_modified;
			$object->count         = 1;
			$object->items         = $abandoned_cart->items;

			$this->report_data->abandoned_carts_items[] = $object;
		}

		$this->report_data->abandoned_carts_count = absint(
			count( wp_list_pluck( $this->report_data->abandoned_carts_items, 'post_modified' ) )
		);
	}

	/**
	 * Use a custom function for just counting the abandoned carts in the range
	 *
	 * @return array|object|null
	 */
	private function get_abandoned_cart_items_within_range() {
		global $wpdb;

		return $wpdb->get_results(
			$wpdb->prepare(
				"
			SELECT posts.ID, meta.meta_value AS items, posts.post_modified FROM {$wpdb->posts} AS posts

			LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
			LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID=rel.object_ID
			LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
			LEFT JOIN {$wpdb->terms} AS term USING( term_id )

			WHERE 	meta.meta_key 		= 'av8_cartitems'
			AND 	posts.post_type 	= 'carts'
			AND 	posts.post_status 	= 'publish'
			AND 	tax.taxonomy		= 'shop_cart_status'
			AND		term.slug			IN ('open')
			AND posts.post_modified >= %s AND posts.post_modified < %s
			ORDER BY posts.post_modified
			",
				date( 'Y-m-d H:i:s', $this->start_date ),
				date( 'Y-m-d H:i:s', strtotime( '+1 DAY', $this->end_date ) )
			)
		);
	}

	/**
	 * Get the legend for the main chart sidebar
	 *
	 * @return array
	 */
	public function get_chart_legend() {
		$legend = array();
		$data   = $this->get_report_data();

		$legend[] = array(
			'title'            => sprintf(
			/* translators: %s: total carts in this period */
				__( '%s abandoned carts in this period.', 'woocommerce_cart_reports' ),
				'<strong>' . $data->abandoned_carts_count . '</strong>'
			),
			'color'            => $this->chart_colours['abandoned_carts'],
			'highlight_series' => 1,
		);
		$legend[] = array(
			'title'            => sprintf(
			/* translators: %s: number of orders placed */
				__( '%s orders placed', 'woocommerce_cart_reports' ),
				'<strong>' . $data->total_orders . '</strong>'
			),
			'color'            => $this->chart_colours['order_counts'],
			'highlight_series' => 0,
		);

		return $legend;
	}

	/**
	 * Output the report
	 */
	public function output_report() {
		global $woocommerce, $wpdb, $wp_locale;

		$ranges = array(
			'year'       => __( 'Year', 'woocommerce' ),
			'last_month' => __( 'Last month', 'woocommerce' ),
			'month'      => __( 'This month', 'woocommerce' ),
			'7day'       => __( 'Last 7 days', 'woocommerce' ),
		);

		$this->chart_colours = array(
			'order_counts'    => 'green',
			'abandoned_carts' => '#d54e21',
		);

		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field(
			wp_unslash( $_GET['range'] )
		) : '7day';

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ), true ) ) {
			$current_range = '7day';
		}

		$this->calculate_current_range( $current_range );

		include WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php';
	}

	/**
	 * Get the main chart
	 *
	 * @return string
	 */
	public function get_main_chart() {
		global $wp_locale;

		$data = array(
			'order_counts'         => $this->prepare_chart_data(
				$this->report_data->order_counts,
				'post_date',
				'count',
				$this->chart_interval,
				$this->start_date,
				$this->chart_groupby
			),
			'abandoned_cart_count' => $this->prepare_chart_data(
				$this->report_data->abandoned_carts_items,
				'post_modified',
				'count',
				$this->chart_interval,
				$this->start_date,
				$this->chart_groupby
			),
		);

		$chart_data = wp_json_encode(
			array(
				'order_counts'         => array_values( $data['order_counts'] ),
				'abandoned_cart_count' => array_values( $data['abandoned_cart_count'] ),
			)
		);

		$time_format = 'day' === $this->chart_groupby ? '%d %b' : '%b';
		?>

		<?php //echo $chart_data; ?>
		<div class="chart-container">
			<div class="chart-placeholder main"></div>
		</div>
		<script type="text/javascript">

			var main_chart

			jQuery( function () {
				var order_data = jQuery.parseJSON( '<?php echo $chart_data; ?>' )
				var drawGraph = function ( highlight ) {
					var series = [
						{
							label: "<?php echo esc_js( __( 'Number of conversions', 'woocommerce_cart_reports' ) ); ?>",
							data: order_data.order_counts,
							color: '<?php echo $this->chart_colours['order_counts']; ?>',
							bars: {
								fillColor: '<?php echo $this->chart_colours['order_counts']; ?>',
								fill: true,
								show: true,
								lineWidth: 0,
								barWidth: <?php echo $this->barwidth; ?>
								* 0.5,
								align: 'center'
							},
							shadowSize: 0,
							hoverable: true
						}, {
							label: "<?php echo esc_js( __( 'Abandoned Carts', 'woocommerce_cart_reports' ) ); ?>",
							data: order_data.abandoned_cart_count,
							color: '<?php echo $this->chart_colours['abandoned_carts']; ?>',
							points: { show: true, radius: 5, lineWidth: 3, fillColor: '#fff', fill: true },
							lines: { show: true, lineWidth: 4, fill: false },
							shadowSize: 0,
							hoverable: true
						}
					]

					if ( highlight !== 'undefined' && series[highlight] ) {
						highlight_series = series[highlight]

						highlight_series.color = '#9c5d90'

						if ( highlight_series.bars ) {
							highlight_series.bars.fillColor = '#9c5d90'
						}

						if ( highlight_series.lines ) {
							highlight_series.lines.lineWidth = 5
						}
					}

					main_chart = jQuery.plot( jQuery( '.chart-placeholder.main' ), series, {
						legend: {
							show: false
						}, grid: {
							color: '#aaa', borderColor: 'transparent', borderWidth: 0, hoverable: true
						}, xaxes: [
							{
								color: '#aaa',
								position: 'bottom',
								tickColor: 'transparent',
								mode: 'time',
								timeformat: "<?php echo $time_format; ?>",
								monthNames: <?php echo json_encode( array_values( $wp_locale->month_abbrev ) ); ?>,
								tickLength: 1,
								minTickSize: [1, "<?php echo $this->chart_groupby; ?>"],
								font: {
									color: '#aaa'
								}
							}
						], yaxes: [
							{
								min: 0, minTickSize: 1, tickDecimals: 0, color: '#d4d9dc', font: { color: '#aaa' }
							}, {
								position: 'right',
								min: 0,
								tickDecimals: 2,
								alignTicksWithAxis: 1,
								color: 'transparent',
								font: { color: '#aaa' }
							}
						],
					} )

					jQuery( '.chart-placeholder' ).resize()
				}

				drawGraph()

				jQuery( '.highlight_series' ).hover( function () {
					drawGraph( jQuery( this ).data( 'series' ) )
				}, function () {
					drawGraph()
				} )
			} )
		</script>
		<?php
	}
}
