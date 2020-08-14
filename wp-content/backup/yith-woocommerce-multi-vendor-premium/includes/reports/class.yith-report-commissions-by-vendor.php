<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * WC_Report_Sales_Commissions
 *
 * @author      Andrea Grillo <andrea.grillo@yithemes.com>
 * @category    Admin
 * @version     2.1.0
 */
if ( ! defined( 'YITH_WPV_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YITH_Report_Commissions_By_Vendor' ) ) {

	class YITH_Report_Commissions_By_Vendor extends WC_Admin_Report {

		private $report_data;
		public $chart_colours   = array();
		public $vendor_ids = array();
		public $vendor_ids_name = array();

		public function __construct(){
			$this->prepare_report_data();
		}

		/**
		 * Get report data
		 * @return array
		 */
		public function get_report_data( $series = true ) {
			$data = $this->report_data;

			if( false === $series ){
				unset( $data['series'] );
			}

			return ! empty( $data ) ? $data : false;
		}

		/**
		 * set report data
		 *
		 * @param $report_data
		 *
		 * @return array
		 */
		public function set_report_data( $report_data ) {
			$this->report_data = $report_data;
		}

		/**
		 * Output the report
		 */
		public function output_report(){

			if ( empty( $this->vendor_ids ) && ! empty( $_GET['vendor_ids'] ) ) {
				$this->vendor_ids = $_GET['vendor_ids'];
			}

			$this->chart_colours = array(
				'vendor_sales'  => '#d4d9dc',
				'vendor_amount'  => '#3498db',
			);

			$args = array(
				'report' => $this,
				'current_range' => YITH_Reports()->get_current_date_range(),
				'ranges'        => YITH_Reports()->get_ranges()
			);

			yith_wcpv_get_template( 'vendor-sales', $args, 'woocommerce/admin/reports' );
		}

		/**
		 * Get the legend for the main chart sidebar
		 * @return array
		 */
		public function get_chart_legend() {

			$vendor_id      = ! empty( $_GET['vendor_ids'] ) ? $_GET['vendor_ids'] : false;
			$report_data    = $this->get_report_data();

			if( ! $vendor_id ){
				return;
			}

			if( ! isset( $report_data[ $vendor_id ] ) ) {
				$report_data[ $vendor_id ]['sales'] = $report_data[ $vendor_id ]['items_number'] = 0;
			}

			$legend   = array();

			$legend[] = array(
				'title' => sprintf( __( '%s commissions for the selected vendor', 'yith-woocommerce-product-vendors' ), '<strong>' . wc_price( $report_data[ $vendor_id ]['sales'] ) . '</strong>' ),
				'color' => $this->chart_colours['vendor_amount'],
				'highlight_series' => 1
			);

			$legend[] = array(
				'title' => sprintf( __( '%s commissions for the selected vendor', 'yith-woocommerce-product-vendors' ), '<strong>' . $report_data[ $vendor_id ]['items_number'] . '</strong>' ),
				'color' => $this->chart_colours['vendor_sales'],
				'highlight_series' => 0
			);

			return $legend;
		}

		/**
		 * [get_chart_widgets description]
		 *
		 * @return array
		 */
		public function get_chart_widgets() {

			$widgets = array();

			if ( ! empty( $this->vendor_ids ) ) {
				$widgets[] = array(
					'title'    => __( 'Showing reports for:', 'yith-woocommerce-product-vendors' ),
					'callback' => array( $this, 'current_filters' )
				);
			}

			$widgets[] = array(
				'title'    => '',
				'callback' => array( $this, 'vendors_widget' )
			);

			return $widgets;
		}

		/**
		 * Show current filters
		 */
		public function current_filters() {

			$this->vendor_ids_name = array();

			$vendor = yith_get_vendor( $this->vendor_ids );

			if ( $vendor->is_valid() ) {
				$this->vendor_ids_name[] = $vendor->name;
			} else {
				$this->vendor_ids_name[] = '#' . $vendor->id;
			}

			echo '<p>' . ' <strong>' . implode( ', ', $this->vendor_ids_name ) . '</strong></p>';
			echo '<p><a class="button" href="' . esc_url( remove_query_arg( 'vendor_ids' ) ) . '">' . __( 'Reset', 'yith-woocommerce-product-vendors' ) . '</a></p>';
		}

		/**
		 * Product selection
		 */
		public function vendors_widget() {
			$limit = get_option( 'yith_wpv_reports_limit', 10 );
			?>
			<h4 class="section_title"><span><?php _e( 'Search Vendors', 'yith-woocommerce-product-vendors' ); ?></span></h4>
			<div class="section">
				<form method="GET">
					<div>
                        <?php yit_add_select2_fields( YITH_Reports()->get_select2_args() ); ?>
						<input type="submit" class="submit button" value="<?php _e( 'Show', 'yith-woocommerce-product-vendors' ); ?>" />
						<input type="hidden" name="range" value="<?php if ( ! empty( $_GET['range'] ) ) echo esc_attr( $_GET['range'] ) ?>" />
						<input type="hidden" name="start_date" value="<?php if ( ! empty( $_GET['start_date'] ) ) echo esc_attr( $_GET['start_date'] ) ?>" />
						<input type="hidden" name="end_date" value="<?php if ( ! empty( $_GET['end_date'] ) ) echo esc_attr( $_GET['end_date'] ) ?>" />
						<input type="hidden" name="page" value="<?php if ( ! empty( $_GET['page'] ) ) echo esc_attr( $_GET['page'] ) ?>" />
						<input type="hidden" name="tab" value="<?php if ( ! empty( $_GET['tab'] ) ) echo esc_attr( $_GET['tab'] ) ?>" />
						<input type="hidden" name="report" value="<?php if ( ! empty( $_GET['report'] ) ) echo esc_attr( $_GET['report'] ) ?>" />
					</div>
				</form>
			</div>
			<h4 class="section_title"><span><?php _e( 'Top Sellers', 'yith-woocommerce-product-vendors' ); ?></span></h4>
			<div class="section">
				<table cellspacing="0">
					<?php
					$top_sellers = $this->get_report_data( false );
					if ( $top_sellers ) {
						uasort( $top_sellers, array( $this, 'item_sort' ) );
						$limit = ! empty( $limit ) ? $limit : count( $top_sellers );
						foreach ( array_slice( $top_sellers, 0, $limit ) as $top_seller ) {
							echo '<tr class="' . ( $top_seller['vendor']->id == $this->vendor_ids ? 'active' : '' ) . '">
                                <td class="count">' . $top_seller['items_number'] . '</td>
                                <td class="name"><a href="' . esc_url( add_query_arg( 'vendor_ids', $top_seller['vendor']->id ) ) . '">' . $top_seller['vendor']->name . '</a></td>
                                <td class="sparkline">' . '' . '</td>
                            </tr>';
						}
					} else {
						echo '<tr><td colspan="3">' . __( 'No vendors found in range', 'yith-woocommerce-product-vendors' ) . '</td></tr>';
					}
					?>
				</table>
			</div>
			<h4 class="section_title"><span><?php _e( 'Top Earners', 'yith-woocommerce-product-vendors' ); ?></span></h4>
			<div class="section">
				<table cellspacing="0">
					<?php
					$top_earners = $this->get_report_data( false );
					if ( $top_earners ) {
						uasort( $top_earners, array( $this, 'sales_sort' ) );
						$limit = ! empty( $limit ) ? $limit : count( $top_earners );
						foreach ( array_slice( $top_earners, 0, $limit ) as $top_earner ) {
							echo '<tr class="' . ( $top_earner['vendor']->id == $this->vendor_ids ? 'active' : '' ) . '">
                                <td class="count">' . wc_price( $top_earner['sales'] ) . '</td>
                                <td class="name"><a href="' . esc_url( add_query_arg( 'vendor_ids', $top_earner['vendor']->id ) ) . '">' . $top_earner['vendor']->name . '</a></td>
                                <td class="sparkline">' . '' . '</td>
                            </tr>';
						}
					} else {
						echo '<tr><td colspan="3">' . __( 'No vendors found in range', 'yith-woocommerce-product-vendors' ) . '</td></tr>';
					}
					?>
				</table>
			</div>
			<script type="text/javascript">
				jQuery('.section_title').click(function(){
					var next_section = jQuery(this).next('.section');

					if ( jQuery(next_section).is(':visible') )
						return false;

					jQuery('.section:visible').slideUp();
					jQuery('.section_title').removeClass('open');
					jQuery(this).addClass('open').next('.section').slideDown();

					return false;
				});
				jQuery('.section').slideUp( 100, function() {
					<?php if ( empty( $this->vendor_ids ) ) : ?>
					jQuery('.section_title:eq(1)').click();
					<?php endif; ?>
				});
			</script>
			<?php
		}

		/**
		 * Get the main chart
		 *
		 * @return string|void
		 */
		public function get_main_chart() {
			global $wp_locale;

			if ( ! $this->vendor_ids ) {
				?>
				<div class="chart-container">
					<p class="chart-prompt"><?php _e( '&larr; Choose a vendor to view stats', 'yith-woocommerce-product-vendors' ); ?></p>
				</div>
				<?php
			} elseif( isset( $this->report_data['series'] ) ) {
				// Prepare data for report
				$vendor_item_counts  = $this->prepare_chart_data( $this->report_data['series'][ $this->vendor_ids ], 'order_date', 'qty', $this->chart_interval, $this->start_date, $this->chart_groupby );
				$vendor_item_amounts = $this->prepare_chart_data( $this->report_data['series'][ $this->vendor_ids ], 'order_date', 'commission', $this->chart_interval, $this->start_date, $this->chart_groupby );

				// Encode in json format
				$chart_data = json_encode( array(
					'vendor_item_counts'  => array_values( $vendor_item_counts ),
					'vendor_item_amounts' => array_values( $vendor_item_amounts )
				) );
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
									label: "<?php echo esc_js( __( 'Number of commissions granted', 'yith-woocommerce-product-vendors' ) ) ?>",
									data: order_data.vendor_item_counts,
									color: '<?php echo $this->chart_colours['vendor_sales']; ?>',
									bars: { fillColor: '<?php echo $this->chart_colours['vendor_sales']; ?>', fill: true, show: true, lineWidth: 0, barWidth: <?php echo $this->barwidth; ?> * 0.5, align: 'center' },
								shadowSize: 0,
								hoverable: false
						},
							{
								label: "<?php echo esc_js( __( 'Commissions amount', 'yith-woocommerce-product-vendors' ) ) ?>",
									data: order_data.vendor_item_amounts,
								yaxis: 2,
								color: '<?php echo $this->chart_colours['vendor_amount']; ?>',
								points: { show: true, radius: 5, lineWidth: 3, fillColor: '#fff', fill: true },
								lines: { show: true, lineWidth: 4, fill: false },
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

		/**
		 * Array sort
		 *
		 * @param $array
		 */
		public function sales_sort( $a, $b ){
			if( $a['sales'] == $b['sales'] ){
				return 0;
			} elseif( $a['sales'] < $b['sales'] ){
				return 1;
			} else{
				return -1;
			}
		}

		/**
		 * Array sort
		 *
		 * @param $array
		 */
		public function item_sort( $a, $b ){
			if( $a['items_number'] == $b['items_number'] ){
				return 0;
			} elseif( $a['items_number'] < $b['items_number'] ){
				return 1;
			} else{
				return -1;
			}
		}

		/*
		 * Prepare the report data
		 */
		public function prepare_report_data() {
			$report_data    = array();
			$series         = array();
			$current_range = YITH_Reports()->get_current_date_range();

			$this->calculate_current_range( $current_range );

			$vendors = YITH_Vendors()->get_vendors();

			/**
			 * Skip the commissions with this status
             * from Commissions by vendor report
			 */
			$not_allowed_status = apply_filters( 'yith_wcmv_report_commissions_by_vendor_not_allowed_status', array(
					'cancelled',
					'refunded',
					'pending'
				)
			);

			/* @var $vendor YITH_Vendor */
			foreach ( $vendors as $vendor ) {
				$amount = $items_number = 0;

				$commission_ids = YITH_Commissions()->get_commissions( array( 'vendor_id' => $vendor->id, 'status' => 'all' ) );

				$not_allowed_status = apply_filters( 'yith_wcmv_report_commissions_by_vendor_not_allowed_status', array(
						'cancelled',
						'refunded',
						'pending',
					)
				);

				foreach ( $commission_ids as $commision_id ) {
					$commission = YITH_Commission( $commision_id );

					if( in_array( $commission->status, $not_allowed_status ) ){
						continue;
					}

					$order = $commission->get_order();
					/**
					 * WC return start date and end date in midnight form.
					 * To compare it with wc order date I need to convert
					 * order date in midnight form too.
					 */
					$order_date = $order instanceof WC_Order ? strtotime( 'midnight', strtotime( yit_get_prop( $order, 'order_date' ) ) ) : false;

					$order_date = apply_filters( 'yith_wcmv_report_commissions_by_vendor_order_date', $order_date, $commission );

					$is_valid_commission = $order_date && $order_date >= $this->start_date && $order_date <= $this->end_date;

					if ( $is_valid_commission ) {
						$items_number++;
						$amount += $commission->get_amount();
					}

					/* === Chart Data === */
					$series = new stdClass();
					$series->order_date = yit_get_prop( $order, 'order_date' );
					$series->qty = 1;
					$series->commission = wc_format_decimal( $commission->get_amount(), wc_get_price_decimals() );
					$report_data['series'][ $vendor->id ][] = $series;
				}

				if ( ! empty( $amount ) ) {
					$report_data[ $vendor->id ] = array(
						'vendor'       => $vendor,
						'sales'        => $amount,
						'items_number' => $items_number,
					);
				}
			}
			$this->set_report_data( $report_data );
		}
	}
}
