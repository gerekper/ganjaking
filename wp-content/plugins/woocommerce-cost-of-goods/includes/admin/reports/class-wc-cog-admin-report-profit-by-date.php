<?php
/**
 * WooCommerce Cost of Goods
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Cost of Goods to newer
 * versions in the future. If you wish to customize WooCommerce Cost of Goods for your
 * needs please refer to http://docs.woocommerce.com/document/cost-of-goods/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_2 as Framework;

/**
 * Cost of Goods Profit by Date Admin Report Class
 *
 * Handles generating and rendering the Profit by Date report
 *
 * @since 2.0.0
 */
class WC_COG_Admin_Report_Profit_by_Date extends \WC_COG_Admin_Report {


	/** @var array define the chart colors for this report */
	protected $chart_colors = array(
		'net_sales'    => '#b1d4ea',
		'total_cogs'   => '#3498db',
		'total_profit' => '#5cc488',
		'total_orders' => '#dbe1e3',
		'avg_profit'   => '#a9ffd5',
	);


	/**
	 * Get the chart legend data
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function get_chart_legend() {

		$data = $this->get_report_data();

		return array(

			// total sales
			array(
				/* translators: Placeholders: %1$s is the formatted net sales amount with surrounding <strong> tags, e.g. <strong>$7.77</strong> */
				'title'            => sprintf( __( '%1$s net sales in this period', 'woocommerce-cost-of-goods' ), '<strong>' . wc_price( $data->net_sales ) . '</strong>' ),
				'placeholder'      => __( 'This is the sum of the order totals after any refunds and excluding fees, taxes, and shipping (unless you have toggled the settings to include them)', 'woocommerce-cost-of-goods' ),
				'color'            => $this->chart_colors['net_sales'],
				'highlight_series' => 1
			),

			// total cost of goods
			array(
				/* translators: Placeholders: %1$s is the formatted total cost of goods amount with surrounding <strong> tags, e.g. <strong>$2.00</strong> */
				'title'            => sprintf( __( '%1$s total cost of goods in this period', 'woocommerce-cost-of-goods' ), '<strong>' . wc_price( $data->total_cogs ) . '</strong>' ),
				'placeholder'      => __( 'This is the sum of the item cost of goods after any refunds', 'woocommerce-cost-of-goods' ),
				'color'            => $this->chart_colors['total_cogs'],
				'highlight_series' => 2,
			),

			// total profit
			array(
				/* translators: Placeholders: %1$s is the formatted total profit amount with surrounding <strong> tags, e.g. <strong>$5.77</strong> */
				'title'            => sprintf( __( '%1$s total profit in this period', 'woocommerce-cost-of-goods' ), '<strong>' . wc_price( $data->total_profit ) . '</strong>' ),
				'placeholder'      => __( 'This is the sum of the order profit after any refunds', 'woocommerce-cost-of-goods' ),
				'color'            => $this->chart_colors['total_profit'],
				'highlight_series' => 3,
			),

			// order count
			array(
				/* translators: Placeholders: %1$s is the total orders count with surrounding <strong> tags, e.g. <strong>7</strong> */
				'title' => sprintf( __( '%1$s orders placed', 'woocommerce-cost-of-goods' ), '<strong>' . $data->total_orders . '</strong>' ),
				'color' => $this->chart_colors['total_orders'],
				'highlight_series' => 0,
			),

			// average profit per order
			array(
				/* translators: Placeholders: %1$s is the formatted average profit per order amount with surrounding <strong> tags, e.g. <strong>$1.77</strong> */
				'title'            => sprintf( __( '%1$s average profit per order in this period', 'woocommerce-cost-of-goods' ), '<strong>' . wc_price( $data->avg_profit ) . '</strong>' ),
				'placeholder'      => __( 'This is the average profit per order after any refunds', 'woocommerce-cost-of-goods' ),
				'color'            => $this->chart_colors['avg_profit'],
			),

		);
	}


	/**
	 * Render the "Export to CSV" button
	 *
	 * @since 2.0.0
	 */
	public function get_export_button() {

		$this->output_export_button( array( 'exclude_series' => 4 ) );
	}


	/**
	 * Render the main chart
	 *
	 * @since 2.0.0
	 */
	public function get_main_chart() {

		// prep data for charting
		$order_amounts        = $this->prepare_chart_data( $this->report_data->orders, 'post_date', 'total_sales', $this->chart_interval, $this->start_date, $this->chart_groupby );
		$fees_amounts         = $this->prepare_chart_data( $this->report_data->fees, 'post_date', 'total_fees', $this->chart_interval, $this->start_date, $this->chart_groupby );
		$tax_amounts          = $this->prepare_chart_data( $this->report_data->orders, 'post_date', 'total_tax', $this->chart_interval, $this->start_date, $this->chart_groupby );
		$shipping_amounts     = $this->prepare_chart_data( $this->report_data->orders, 'post_date', 'total_shipping', $this->chart_interval, $this->start_date, $this->chart_groupby );
		$shipping_tax_amounts = $this->prepare_chart_data( $this->report_data->orders, 'post_date', 'total_shipping_tax', $this->chart_interval, $this->start_date, $this->chart_groupby );
		$cogs_amounts         = $this->prepare_chart_data( $this->report_data->cogs, 'post_date', 'total_cogs', $this->chart_interval, $this->start_date, $this->chart_groupby );
		$order_counts         = $this->prepare_chart_data( $this->report_data->orders_count, 'post_date', 'count', $this->chart_interval, $this->start_date, $this->chart_groupby );

		$net_order_amounts = $profit_amounts = array();
		$exclude_fees      = $this->exclude_fees();
		$exclude_taxes     = $this->exclude_taxes();
		$exclude_shipping  = $this->exclude_shipping();

		// calculate the net order and profit amounts for each interval
		foreach ( $order_amounts as $interval => $value ) {
			$net_order_amounts[ $interval ] = $profit_amounts[ $interval ] = $value;

			if ( $exclude_fees ) {
				$net_order_amounts[ $interval ][1] -= $fees_amounts[ $interval ][1];
			}

			if ( $exclude_taxes ) {
				$net_order_amounts[ $interval ][1] -= ( $tax_amounts[ $interval ][1] + $shipping_tax_amounts[ $interval ][1] );
			}

			if ( $exclude_shipping ) {
				$net_order_amounts[ $interval ][1] -= $shipping_amounts[ $interval ][1];
			}

			// profit
			$profit_amounts[ $interval ][1] = $net_order_amounts[ $interval ][1] - $cogs_amounts[ $interval ][1];
		}

		$chart_data = array(
			'net_order_amounts' => array_map( array( $this, 'format_decimal' ), array_values( $net_order_amounts ) ),
			'cogs_amounts'      => array_map( array( $this, 'format_decimal' ), array_values( $cogs_amounts ) ),
			'profit_amounts'    => array_map( array( $this, 'format_decimal' ), array_values( $profit_amounts ) ),
			'order_counts'      => array_values( $order_counts ),
		);
		?>
		<div class="chart-container">
			<div class="chart-placeholder main"></div>
		</div>
		<script type="text/javascript">

			var main_chart;

			jQuery(function(){
				var order_data = jQuery.parseJSON( '<?php echo json_encode( $chart_data ); ?>' );
				var drawGraph = function( highlight ) {
					var series = [
						{
							label     : "<?php echo esc_js( __( 'Number of orders', 'woocommerce-cost-of-goods' ) ) ?>",
							data      : order_data.order_counts,
							color     : '<?php echo $this->chart_colors['total_orders']; ?>',
							bars      : { fillColor: '<?php echo $this->chart_colors['total_orders']; ?>', fill: true, show: true, lineWidth: 0, barWidth: <?php echo $this->barwidth; ?> * 0.5, align: 'center' },
							shadowSize: 0,
							hoverable : false
						},
						{
							label     : "<?php echo esc_js( __( 'Net Sales amount', 'woocommerce-cost-of-goods' ) ) ?>",
							data      : order_data.net_order_amounts,
							yaxis     : 2,
							color     : '<?php echo $this->chart_colors['net_sales']; ?>',
							points    : { show: true, radius: 6, lineWidth: 4, fillColor: '#fff', fill: true },
							lines     : { show: true, lineWidth: 5, fill: false },
							shadowSize: 0,
							<?php echo $this->get_currency_tooltip(); ?>
						},
						{
							label     : "<?php echo esc_js( __( 'Cost of Goods Sold', 'woocommerce-cost-of-goods' ) ) ?>",
							data      : order_data.cogs_amounts,
							yaxis     : 2,
							color     : '<?php echo $this->chart_colors['total_cogs']; ?>',
							points    : { show: true, radius: 5, lineWidth: 2, fillColor: '#fff', fill: true },
							lines     : { show: true, lineWidth: 2, fill: false },
							shadowSize: 0,
							<?php echo $this->get_currency_tooltip(); ?>
						},
						{
							label     : "<?php echo esc_js( __( 'Profit amount', 'woocommerce-cost-of-goods' ) ) ?>",
							data      : order_data.profit_amounts,
							yaxis     : 2,
							color     : '<?php echo $this->chart_colors['total_profit']; ?>',
							points    : { show: true, radius: 5, lineWidth: 2, fillColor: '#fff', fill: true },
							lines     : { show: true, lineWidth: 2, fill: false },
							shadowSize: 0,
							<?php echo $this->get_currency_tooltip(); ?>
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
						jQuery( '.chart-placeholder.main' ),
						series,
						{
							legend: {
								show: false
							},
							grid: {
								color      : '#aaa',
								borderColor: 'transparent',
								borderWidth: 0,
								hoverable  : true
							},
							xaxes: [ {
								color      : '#aaa',
								position   : 'bottom',
								tickColor  : 'transparent',
								mode       : 'time',
								timeformat : "<?php if ( $this->chart_groupby == 'day' ) echo '%d %b'; else echo '%b'; ?>",
								monthNames : <?php echo json_encode( array_values( $GLOBALS['wp_locale']->month_abbrev ) ) ?>,
								tickLength : 1,
								minTickSize: [1, "<?php echo esc_js( $this->chart_groupby ); ?>"],
								font       : {
									color: '#aaa'
								}
							} ],
							yaxes: [
								{
									min         : 0,
									minTickSize : 1,
									tickDecimals: 0,
									color       : '#d4d9dc',
									font        : { color: '#aaa' }
								},
								{
									position          : 'right',
									min               : 0,
									tickDecimals      : 2,
									alignTicksWithAxis: 1,
									color             : 'transparent',
									font              : { color: '#aaa' }
								}
							],
						}
					);

					jQuery( '.chart-placeholder' ).resize();
				}

				drawGraph();

				jQuery( '.highlight_series' ).hover(
					function() {
						drawGraph( jQuery( this ).data( 'series' ) );
					},
					function() {
						drawGraph();
					}
				);
			});
		</script>
		<?php
	}


	/**
	 * Get the report data required for the legend and chart.
	 *
	 * Note that an admin can change (via settings) whether fees, taxes, and shipping
	 * are included in net sales. By default, none are included.
	 *
	 * # Displayed:
	 * + net_sales - sales amount net of refunds, fees, taxes, and shipping
	 * + total_cogs - total cost of goods sold amount
	 * + total_profit - net sales minus total cogs amount
	 * + total_orders - total count of orders placed
	 * + avg_profit - average profit per order
	 *
	 * # Not displayed, but used for calculations:
	 * + total_sales - total sales amount net of refunds but including fees, taxes, and shipping
	 * + total_tax - total tax amount
	 * + total_shipping - total shipping amount
	 * + total_shipping_tax - total shipping tax
	 * + total_fees - total fees amount
	 * + total_fees_tax - total fees tax amount
	 *
	 * @since 2.0.0
	 * @return \stdClass
	 */
	protected function get_report_data() {

		if ( ! empty( $this->report_data ) ) {
			return $this->report_data;
		}

		$this->report_data = new stdClass();

		$this->report_data->orders = (array) $this->get_order_report_data( array(
			'data' => array(
				'_order_total' => array(
					'type'     => 'meta',
					'function' => 'SUM',
					'name'     => 'total_sales'
				),
				'_order_shipping' => array(
					'type'     => 'meta',
					'function' => 'SUM',
					'name'     => 'total_shipping'
				),
				'_order_tax' => array(
					'type'     => 'meta',
					'function' => 'SUM',
					'name'     => 'total_tax'
				),
				'_order_shipping_tax' => array(
					'type'     => 'meta',
					'function' => 'SUM',
					'name'     => 'total_shipping_tax'
				),
				'post_date' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date'
				),
			),
			'group_by'            => $this->group_by_query,
			'order_by'            => 'post_date ASC',
			'query_type'          => 'get_results',
			'filter_range'        => true,
			'order_types'         => array_merge( array( 'shop_order_refund' ), wc_get_order_types( 'sales-reports' ) ),
			'order_status'        => array( 'completed', 'processing', 'on-hold' ),
			'parent_order_status' => array( 'completed', 'processing', 'on-hold' ),
		) );

		$this->report_data->total_sales        = $this->format_decimal( array_sum( wp_list_pluck( $this->report_data->orders, 'total_sales' ) ) );
		$this->report_data->total_tax          = $this->format_decimal( array_sum( wp_list_pluck( $this->report_data->orders, 'total_tax' ) ) );
		$this->report_data->total_shipping     = $this->format_decimal( array_sum( wp_list_pluck( $this->report_data->orders, 'total_shipping' ) ) );
		$this->report_data->total_shipping_tax = $this->format_decimal( array_sum( wp_list_pluck( $this->report_data->orders, 'total_shipping_tax' ) ) );

		$this->report_data->fees = (array) $this->get_order_report_data( array(
			'data' => array(
				'_line_total' => array(
					'type'            => 'order_item_meta',
					'function'        => 'SUM',
					'name'            => 'total_fees',
					'order_item_type' => 'fee'
				),
				'post_date' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date'
				),
			),
			'group_by'            => $this->group_by_query,
			'order_by'            => 'post_date ASC',
			'query_type'          => 'get_results',
			'filter_range'        => true,
			'order_types'         => array_merge( array( 'shop_order_refund' ), wc_get_order_types( 'sales-reports' ) ),
			'order_status'        => array( 'completed', 'processing', 'on-hold' ),
			'parent_order_status' => array( 'completed', 'processing', 'on-hold' ),
		) );

		$this->report_data->total_fees = $this->format_decimal( array_sum( wp_list_pluck( $this->report_data->fees, 'total_fees' ) ) );

		$this->report_data->cogs = (array) $this->get_order_report_data( array(
			'data' => array(
				'_wc_cog_order_total_cost' => array(
					'type'     => 'meta',
					'function' => 'SUM',
					'name'     => 'total_cogs',
				),
				'post_date' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date'
				),
			),
			'group_by'            => $this->group_by_query,
			'order_by'            => 'post_date ASC',
			'query_type'          => 'get_results',
			'filter_range'        => true,
			'order_types'         => array_merge( array( 'shop_order_refund' ), wc_get_order_types( 'sales-reports' ) ),
			'order_status'        => array( 'completed', 'processing', 'on-hold' ),
			'parent_order_status' => array( 'completed', 'processing', 'on-hold' ),
		) );

		$this->report_data->total_cogs = $this->format_decimal( array_sum( wp_list_pluck( $this->report_data->cogs, 'total_cogs' ) ) );

		$this->report_data->orders_count = (array) $this->get_order_report_data( array(
			'data' => array(
				'ID' => array(
					'type'     => 'post_data',
					'function' => 'COUNT',
					'name'     => 'count',
					'distinct' => true,
				),
				'post_date' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date'
				)
			),
			'group_by'            => $this->group_by_query,
			'order_by'            => 'post_date ASC',
			'query_type'          => 'get_results',
			'filter_range'        => true,
			'order_types'         => wc_get_order_types( 'order-count' ),
			'order_status'        => array( 'completed', 'processing', 'on-hold' )
		) );

		$this->report_data->total_orders = max( 0, absint( array_sum( wp_list_pluck( $this->report_data->orders_count, 'count' ) ) ) );

		/**
		 * calculate net sales. Note the total sales amount is already net of refunds.
		 * This excludes fees, taxes, and shipping (as determined by admin settings)
		 */
		$this->report_data->net_sales = $this->report_data->total_sales;

		// exclude fees
		if ( $this->exclude_fees() ) {
			$this->report_data->net_sales -= $this->report_data->total_fees;
		}

		// exclude taxes (order tax and shipping tax)
		if ( $this->exclude_taxes() ) {
			$this->report_data->net_sales -= ( $this->report_data->total_tax + $this->report_data->total_shipping_tax );
		}

		// excluding shipping
		if ( $this->exclude_shipping() ) {
			$this->report_data->net_sales -= $this->report_data->total_shipping;
		}

		// format
		$this->report_data->net_sales = $this->format_decimal( $this->report_data->net_sales );

		// finally, calculate total profit and average profit per order
		$this->report_data->total_profit = $this->format_decimal( ( $this->report_data->net_sales - $this->report_data->total_cogs ) );
		$this->report_data->avg_profit   = $this->format_decimal( ( $this->report_data->total_profit / max( 1, $this->report_data->total_orders ) ) );


		/**
		 * Profit by Date Report Data Filter.
		 *
		 * Allow actors to filter the data returned for the profit by date
		 * report.
		 *
		 * @since 2.0.0
		 * @param array $report_data
		 * @param \WC_COG_Admin_Report_Profit_by_Date $this instance
		 */
		return apply_filters( 'wc_cost_of_goods_profit_by_date_report_data', $this->report_data, $this );
	}


}
