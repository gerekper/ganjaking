<?php
/**
 * WC_Report_Sales_Commissions
 *
 * @author      Andrea Grillo <andrea.grillo@yithemes.com>
 * @category    Admin
 * @version     2.1.0
 */
class YITH_Report_Sale_Commissions extends WC_Admin_Report {

	public $chart_colours = array();

	private $report_data;

	/**
	 * Get report data
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
        $vendor = yith_get_vendor( 'current', 'user' );

        if( ! $vendor->is_valid() && ! $vendor->has_limited_access() ){
            return false;
        }

		$this->report_data = new stdClass;

        $date_query = YITH_Reports()->get_wp_query_date_args( $this->start_date, $this->end_date );
        $query_args = array_merge( array( 'vendor_id' => $vendor->id ) , $date_query );

        $commission_status = YITH_Commissions()->get_status();
        $commission_status['all'] = 'all';
        foreach( $commission_status as $key => $value ){
            $commission_ids = YITH_Commissions()->get_commissions( array_merge( $query_args, array( 'status' => $key ) ) );
            $data           = "commissions_{$key}";
            $data_amount    = "{$data}_amount";
            $data_count     = "{$data}_count";

            $this->report_data->{$data_amount} = 0;

            foreach ( $commission_ids as $commision_id ) {
                $commission = YITH_Commission( $commision_id );

                if( 'all' == $key && ( 'cancelled' == $commission->status || 'refunded' == $commission->status ) ){
                    continue;
                }

                $this->report_data->{$data}[ $commision_id ]                  = new stdClass();
                $this->report_data->{$data}[ $commision_id ]->amount          = $this->round_chart_totals( $commission->get_amount() );
                $this->report_data->{$data}[ $commision_id ]->commission_date = $commission->get_date();

                $this->report_data->{$data_count}[ $commision_id ]                  = new stdClass();
                $this->report_data->{$data_count}[ $commision_id ]->count           = 1;
                $this->report_data->{$data_count}[ $commision_id ]->commission_date = $commission->get_date();

                $this->report_data->{$data_amount} += $commission->get_amount();
            }
        }
	}

	/**
	 * Get the legend for the main chart sidebar
	 * @return array
	 */
	public function get_chart_legend() {
		$legend = array();
		$data   = $this->get_report_data();
        $count = ! empty( $data->commissions_all_count ) ? count( $data->commissions_all_count ) : 0;

		$legend[] = array(
			'title'            => sprintf( __( '%s commission amount', 'yith-woocommerce-product-vendors' ), '<strong>' . wc_price( $data->commissions_all_amount ). '</strong>' ),
			'placeholder'      => __( 'This is the sum of commission totals.', 'yith-woocommerce-product-vendors' ),
			'color'            => $this->chart_colours['commissions_amount'],
			'highlight_series' => 1
		);

        $legend[] = array(
			'title'            => sprintf( __( '%s paid commissions', 'yith-woocommerce-product-vendors' ), '<strong>' . wc_price( $data->commissions_paid_amount ). '</strong>' ),
			'placeholder'      => __( 'This is the sum of paid commission totals.', 'yith-woocommerce-product-vendors' ),
			'color'            => $this->chart_colours['commissions_paid'],
			'highlight_series' => 2
		);

         $legend[] = array(
			'title'            => sprintf( __( '%s unpaid commissions', 'yith-woocommerce-product-vendors' ), '<strong>' . wc_price( $data->commissions_unpaid_amount ). '</strong>' ),
			'placeholder'      => __( 'This is the sum of unpaid commission totals.', 'yith-woocommerce-product-vendors' ),
			'color'            => $this->chart_colours['commissions_unpaid'],
			'highlight_series' => 3
		);

         $legend[] = array(
			'title'            => sprintf( __( '%s processing commissions', 'yith-woocommerce-product-vendors' ), '<strong>' . wc_price( $data->commissions_processing_amount ). '</strong>' ),
			'placeholder'      => __( 'This is the sum of processing commission totals.', 'yith-woocommerce-product-vendors' ),
			'color'            => $this->chart_colours['commissions_processing'],
			'highlight_series' => 4
		);

        $legend[] = array(
			'title'            => sprintf( __( '%s pending commissions', 'yith-woocommerce-product-vendors' ), '<strong>' . wc_price( $data->commissions_pending_amount ). '</strong>' ),
			'placeholder'      => __( 'This is the sum of pending commission totals.', 'yith-woocommerce-product-vendors' ),
			'color'            => $this->chart_colours['commissions_pending'],
			'highlight_series' => 5
		);

        $legend[] = array(
			'title'            => sprintf( __( '%s commissions', 'yith-woocommerce-product-vendors' ), '<strong>' . $count . '</strong>' ),
			'placeholder'      => __( 'This is the sum of the commissions in this period.', 'yith-woocommerce-product-vendors' ),
			'color'            => $this->chart_colours['commissions_count'],
			'highlight_series' => 0
		);

        $legend[] = array(
            'title'            => sprintf( __( '%s refunded commissions', 'yith-woocommerce-product-vendors' ), '<strong>' . wc_price( $data->commissions_refunded_amount ). '</strong>' ),
            'placeholder'      => __( 'This is the sum of refunded commission totals.', 'yith-woocommerce-product-vendors' ),
            'color'            => $this->chart_colours['commissions_refunded'],
            'highlight_series' => 6
        );

        $legend[] = array(
			'title'            => sprintf( __( '%s cancelled commissions', 'yith-woocommerce-product-vendors' ), '<strong>' . wc_price( $data->commissions_cancelled_amount ). '</strong>' ),
			'placeholder'      => __( 'This is the sum of cancelled commission totals.', 'yith-woocommerce-product-vendors' ),
			'color'            => $this->chart_colours['commissions_cancelled'],
			'highlight_series' => 7
		);

		return $legend;
	}

	/**
	 * Output the report
	 */
	public function output_report() {

		$this->chart_colours = array(
			'commissions_amount'        => '#0300c6',
			'commissions_paid'          => '#2ea2cc',
            'commissions_unpaid'        => '#ffba00',
            'commissions_processing'    => '#73a724',
			'commissions_pending'       => '#e74c3c',
			'commissions_refunded'      => '#999',
			'commissions_cancelled'     => '#a00',
			'commissions_count'         => '#ecf0f1',
		);

		$current_range = YITH_Reports()->get_current_date_range();

		$this->calculate_current_range( $current_range );

        $args = array(
            'report'        => $this,
            'current_range' => $current_range,
            'ranges'        => YITH_Reports()->get_ranges()
        );

        yith_wcpv_get_template( 'sale-commissions', $args, 'woocommerce/admin/reports' );
	}

	/**
	 * Round our totals correctly
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

        // Encode in json format
        $chart_data = json_encode(
            array(
                'commissions'               => isset( $this->report_data->commissions_all )          ? array_values( $this->prepare_chart_data( $this->report_data->commissions_all, 'commission_date', 'amount', $this->chart_interval, $this->start_date, $this->chart_groupby ) ) : false,
                'commissions_paid'          => isset( $this->report_data->commissions_paid )         ? array_values( $this->prepare_chart_data( $this->report_data->commissions_paid, 'commission_date', 'amount', $this->chart_interval, $this->start_date, $this->chart_groupby ) ) : false,
                'commissions_unpaid'        => isset( $this->report_data->commissions_unpaid )       ? array_values( $this->prepare_chart_data( $this->report_data->commissions_unpaid, 'commission_date', 'amount', $this->chart_interval, $this->start_date, $this->chart_groupby ) ) : false,
                'commissions_processing'    => isset( $this->report_data->commissions_processing )   ? array_values( $this->prepare_chart_data( $this->report_data->commissions_processing, 'commission_date', 'amount', $this->chart_interval, $this->start_date, $this->chart_groupby ) ) : false,
                'commissions_pending'       => isset( $this->report_data->commissions_pending )      ? array_values( $this->prepare_chart_data( $this->report_data->commissions_pending, 'commission_date', 'amount', $this->chart_interval, $this->start_date, $this->chart_groupby ) ) : false,
                'commissions_refunded'      => isset( $this->report_data->commissions_refunded )     ? array_values( $this->prepare_chart_data( $this->report_data->commissions_refunded, 'commission_date', 'amount', $this->chart_interval, $this->start_date, $this->chart_groupby ) ) : false,
                'commissions_cancelled'     => isset( $this->report_data->commissions_cancelled )    ? array_values( $this->prepare_chart_data( $this->report_data->commissions_cancelled, 'commission_date', 'amount', $this->chart_interval, $this->start_date, $this->chart_groupby ) ) : false,
                'commissions_count'         => isset( $this->report_data->commissions_all_count )    ? array_values( $this->prepare_chart_data( $this->report_data->commissions_all_count, 'commission_date', 'count', $this->chart_interval, $this->start_date, $this->chart_groupby ) ) : false,
            )
        );

		?>
		<div class="chart-container">
			<div class="chart-placeholder main"></div>
		</div>
		<script type="text/javascript">

			var main_chart;

			jQuery(function(){
				var commissions_data = jQuery.parseJSON( '<?php echo $chart_data; ?>' );
				var drawGraph = function( highlight ) {
					var series = [
                        {
							label: "<?php echo esc_js( __( 'Commission Totals', 'yith-woocommerce-product-vendors' ) ) ?>",
							data: commissions_data.commissions_count,
							color: "<?php echo $this->chart_colours['commissions_count']; ?>",
							points: { show: true, radius: 0 },
							bars: { fillColor: '<?php echo $this->chart_colours['commissions_count']; ?>', fill: true, show: true, lineWidth: 0, barWidth: <?php echo $this->barwidth; ?> * 0.5, align: 'center' },
							shadowSize: 0,
							hoverable: true,
                            append_tooltip: "<?php echo ' ' . __( 'Commissions', 'yith-woocommerce-product-vendors' ) ?>",
                            yaxis: 1
                        },
                        {
							label: "<?php echo esc_js( __( 'Commissions amount', 'yith-woocommerce-product-vendors' ) ) ?>",
							data: commissions_data.commissions,
							color: "<?php echo $this->chart_colours['commissions_amount']; ?>",
							points: { show: true, radius: 5, lineWidth: 2, fillColor: '#fff', fill: true },
							lines: { show: true, lineWidth: 5, fill: false },
							shadowSize: 0,
							hoverable: true,
                            prepend_tooltip: "<?php echo get_woocommerce_currency_symbol(); ?>",
                            yaxis: 2

						},
                        {
							label: "<?php echo esc_js( __( 'Paid Commissions', 'yith-woocommerce-product-vendors' ) ) ?>",
							data: commissions_data.commissions_paid,
							color: "<?php echo $this->chart_colours['commissions_paid']; ?>",
							points: { show: true },
							lines: { show: true, lineWidth: 3, fill: false },
							shadowSize: 0,
							hoverable: true,
                            prepend_tooltip: "<?php echo get_woocommerce_currency_symbol(); ?>",
                            yaxis: 2
                        },
                        {
							label: "<?php echo esc_js( __( 'Unpaid Commissions', 'yith-woocommerce-product-vendors' ) ) ?>",
							data: commissions_data.commissions_unpaid,
							color: "<?php echo $this->chart_colours['commissions_unpaid']; ?>",
							points: { show: true },
							lines: { show: true, lineWidth: 3, fill: false },
							shadowSize: 0,
							hoverable: true,
                            prepend_tooltip: "<?php echo get_woocommerce_currency_symbol(); ?>",
                            yaxis: 2
						},
                        {
							label: "<?php echo esc_js( __( 'Processing Commissions', 'yith-woocommerce-product-vendors' ) ) ?>",
							data: commissions_data.commissions_processing,
							color: "<?php echo $this->chart_colours['commissions_processing']; ?>",
							points: { show: true },
							lines: { show: true, lineWidth: 2, fill: false },
							shadowSize: 0,
							hoverable: true,
                            prepend_tooltip: "<?php echo get_woocommerce_currency_symbol(); ?>",
                            yaxis: 2
						},
                        {
							label: "<?php echo esc_js( __( 'Pending Commissions', 'yith-woocommerce-product-vendors' ) ) ?>",
							data: commissions_data.commissions_pending,
							color: "<?php echo $this->chart_colours['commissions_pending']; ?>",
							points: { show: true },
							lines: { show: true, lineWidth: 2, fill: false },
							shadowSize: 0,
							hoverable: true,
                            prepend_tooltip: "<?php echo get_woocommerce_currency_symbol(); ?>",
                            yaxis: 2
						},
                        {
							label: "<?php echo esc_js( __( 'Refunded Commissions', 'yith-woocommerce-product-vendors' ) ) ?>",
							data: commissions_data.commissions_refunded,
							color: "<?php echo $this->chart_colours['commissions_refunded']; ?>",
							points: { show: true },
							lines: { show: true, lineWidth: 2, fill: false },
							shadowSize: 0,
							hoverable: true,
                            prepend_tooltip: "<?php echo get_woocommerce_currency_symbol(); ?>",
                            yaxis: 2
						},
                        {
							label: "<?php echo esc_js( __( 'Cancelled Commissions', 'yith-woocommerce-product-vendors' ) ) ?>",
							data: commissions_data.commissions_cancelled,
							color: "<?php echo $this->chart_colours['commissions_cancelled']; ?>",
							points: { show: true },
							lines: { show: true, lineWidth: 2, fill: false },
							shadowSize: 0,
							hoverable: true,
                            prepend_tooltip: "<?php echo get_woocommerce_currency_symbol(); ?>",
                            yaxis: 2
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
								timeformat: "<?php if ( $this->chart_groupby == 'day' ) echo '%d %b'; else echo '%b'; ?>",
								monthNames: <?php echo json_encode( array_values( $wp_locale->month_abbrev ) ) ?>,
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
