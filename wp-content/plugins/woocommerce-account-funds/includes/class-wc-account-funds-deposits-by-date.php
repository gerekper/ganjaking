<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Report_Deposits_By_Date
 */
class WC_Report_Deposits_By_Date extends WC_Admin_Report {

	public $chart_colours = array();
	private $report_data;

	/**
	 * Get report data
	 *
	 * @return array
	 */
	public function get_report_data() {
		if ( empty( $this->report_data ) ) {
			$this->query_report_data();
		}
		return $this->report_data;
	}

	/**
	 * Get all data needed for this report and store in the class
	 */
	private function query_report_data() {
		$this->report_data                  = new stdClass();
		$this->report_data->deposit_count   = 0;
		$this->report_data->deposit_amount  = 0;
		$this->report_data->deposit_counts  = array();
		$this->report_data->deposit_amounts = array();

		$args = array(
			'type'         => 'shop_order',
			'limit'        => -1,
			'status'       => array( 'wc-completed', 'wc-processing', 'wc-on-hold' ),
			'funds_query'  => array(
				array(
					'key'   => '_funds_deposited',
					'value' => '1',
				),
			),
			'date_created' => gmdate( 'Y-m-d', $this->start_date ) . '...' . gmdate( 'Y-m-d', $this->end_date ),
		);

		$orders = wc_get_orders( $args );

		foreach ( $orders as $order ) {
			$time = strtotime( date( 'Ymd', strtotime( $order->get_date_created() ) ) ) . '000';

			foreach ( $order->get_items() as $item ) {
				$product = $item->get_product();

				if ( $product && ( $product->is_type( 'deposit' ) || $product->is_type( 'topup' ) ) ) {
					$this->report_data->deposit_count            ++;
					$this->report_data->deposit_amount += $order->get_line_total( $item );

					if ( ! isset( $this->report_data->deposit_counts[ $time ] ) ) {
						$this->report_data->deposit_counts[ $time ]  = (object) array(
							'date'  => $time,
							'value' => 0,
						);
						$this->report_data->deposit_amounts[ $time ] = (object) array(
							'date'  => $time,
							'value' => 0,
						);
					}

					$this->report_data->deposit_counts[ $time ]->value ++;
					$this->report_data->deposit_amounts[ $time ]->value += $order->get_line_total( $item );
				}
			}
		}

		$this->report_data->average_deposits = wc_format_decimal( $this->report_data->deposit_amount / ( $this->chart_interval + 1 ), 2 );
	}

	/**
	 * Get the legend for the main chart sidebar
	 *
	 * @return array
	 */
	public function get_chart_legend() {
		$legend = array();
		$data   = $this->get_report_data();

		switch ( $this->chart_groupby ) {
			case 'day':
				$average_deposits_title = sprintf( __( '%s average daily deposits', 'woocommerce-account-funds' ), '<strong>' . wc_price( $data->average_deposits ) . '</strong>' );
				break;
			case 'month':
			default:
				$average_deposits_title = sprintf( __( '%s average monthly deposits', 'woocommerce-account-funds' ), '<strong>' . wc_price( $data->average_deposits ) . '</strong>' );
				break;
		}

		$legend[] = array(
			'title'            => sprintf( __( '%s total deposits in this period', 'woocommerce-account-funds' ), '<strong>' . wc_price( $data->deposit_amount ) . '</strong>' ),
			'placeholder'      => __( 'This is the sum of the order totals after any refunds and including shipping and taxes.', 'woocommerce-account-funds' ),
			'color'            => $this->chart_colours['amount'],
			'highlight_series' => 6,
		);
		$legend[] = array(
			'title'            => $average_deposits_title,
			'color'            => $this->chart_colours['average'],
			'highlight_series' => 2,
		);
		$legend[] = array(
			'title'            => sprintf( __( '%s deposits made', 'woocommerce-account-funds' ), '<strong>' . absint( $data->deposit_count ) . '</strong>' ),
			'color'            => $this->chart_colours['count'],
			'highlight_series' => 1,
		);

		return $legend;
	}

	/**
	 * Output the report
	 */
	public function output_report() {
		// phpcs:disable WordPress.WP.I18n.TextDomainMismatch
		$ranges = array(
			'year'       => __( 'Year', 'woocommerce' ),
			'last_month' => __( 'Last month', 'woocommerce' ),
			'month'      => __( 'This month', 'woocommerce' ),
			'7day'       => __( 'Last 7 days', 'woocommerce' ),
		);
		// phpcs:enable WordPress.WP.I18n.TextDomainMismatch

		$this->chart_colours = array(
			'amount'  => '#b1d4ea',
			'average' => '#95a5a6',
			'count'   => '#dbe1e3',
		);

		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
			$current_range = '7day';
		}

		$this->calculate_current_range( $current_range );

		include WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php';
	}

	/**
	 * Round our totals correctly
	 *
	 * @param  string $amount
	 * @return string
	 */
	private function round_chart_totals( $amount ) {
		if ( is_array( $amount ) ) {
			return array_map( array( $this, 'round_chart_totals' ), $amount );
		} else {
			return wc_format_decimal( $amount, wc_get_price_decimals() );
		}
	}

	/**
	 * Get the main chart
	 *
	 * @return string
	 */
	public function get_main_chart() {
		global $wp_locale;

		$deposit_counts  = $this->prepare_chart_data( $this->report_data->deposit_counts, 'date', 'value', $this->chart_interval, $this->start_date, $this->chart_groupby );
		$deposit_amounts = $this->prepare_chart_data( $this->report_data->deposit_amounts, 'date', 'value', $this->chart_interval, $this->start_date, $this->chart_groupby );

		// Encode in json format
		$chart_data = json_encode(
			array(
				'order_counts'  => array_values( $deposit_counts ),
				'order_amounts' => array_map( array( $this, 'round_chart_totals' ), array_values( $deposit_amounts ) ),
			)
		);
		?>
		<div class="chart-container">
			<div class="chart-placeholder main"></div>
		</div>
		<script type="text/javascript">

			var main_chart;

			jQuery(function(){
				var order_data = jQuery.parseJSON( '<?php echo $chart_data; ?>' );
				var drawGraph = function( highlight ) {
					var series = [
						{
							label: "<?php echo esc_js( __( 'Number of deposits made', 'woocommerce-account-funds' ) ); ?>",
							data: order_data.order_counts,
							color: '<?php echo $this->chart_colours['count']; ?>',
							bars: { fillColor: '<?php echo $this->chart_colours['count']; ?>', fill: true, show: true, lineWidth: 0, barWidth: <?php echo $this->barwidth; ?> * 0.5, align: 'center' },
							shadowSize: 0,
							hoverable: false
						},
						{
							label: "<?php echo esc_js( __( 'Average sales amount', 'woocommerce-account-funds' ) ); ?>",
							data: [ [ <?php echo min( array_keys( $deposit_amounts ) ); ?>, <?php echo $this->report_data->average_deposits; ?> ], [ <?php echo max( array_keys( $deposit_amounts ) ); ?>, <?php echo $this->report_data->average_deposits; ?> ] ],
							yaxis: 2,
							color: '<?php echo $this->chart_colours['average']; ?>',
							points: { show: false },
							lines: { show: true, lineWidth: 2, fill: false },
							shadowSize: 0,
							hoverable: false
						},
						{
							label: "<?php echo esc_js( __( 'Deposits amount', 'woocommerce-account-funds' ) ); ?>",
							data: order_data.order_amounts,
							yaxis: 2,
							color: '<?php echo $this->chart_colours['amount']; ?>',
							points: { show: true, radius: 6, lineWidth: 4, fillColor: '#fff', fill: true },
							lines: { show: true, lineWidth: 5, fill: false },
							shadowSize: 0,
							<?php echo $this->get_currency_tooltip(); ?>
						}
					];

					if ( highlight !== 'undefined' && series[ highlight ] ) {
						highlight_series = series[ highlight ];

						highlight_series.color = '#9c5d90';

						if ( highlight_series.bars )
							highlight_series.bars.fillColor = '#9c5d90';

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
								timeformat: "
								<?php
								if ( $this->chart_groupby == 'day' ) {
									echo '%d %b';
								} else {
									echo '%b';
								}
								?>
								",
								monthNames: <?php echo json_encode( array_values( $wp_locale->month_abbrev ) ); ?>,
								tickLength: 1,
								minTickSize: [1, "<?php echo $this->chart_groupby; ?>"],
								font: {
									color: "#aaa"
								}
							} ],
							yaxes: [
								{
									min: 0,
									minTickSize: 1,
									tickDecimals: 0,
									color: '#d4d9dc',
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
