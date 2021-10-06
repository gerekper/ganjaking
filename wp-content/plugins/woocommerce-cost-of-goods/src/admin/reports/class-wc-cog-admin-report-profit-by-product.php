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
 * Cost of Goods Profit by Product Admin Report Class
 *
 * Handles generating and rendering the Profit by Product report
 *
 * @since 2.0.0
 */
class WC_COG_Admin_Report_Profit_by_Product extends \WC_COG_Admin_Report {


	/** @var array product IDs for the report */
	protected $product_ids;

	/** @var array define the chart colors for this report */
	protected $chart_colors = array(
		'total_sales'  => '#b1d4ea',
		'total_cogs'   => '#3498db',
		'total_profit' => '#5cc488',
		'total_items'  => '#dbe1e3',
	);


	/**
	 * Bootstrap class
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->set_product_ids();
	}


	/**
	 * Set the product IDs for the report
	 *
	 * @since 2.0.0
	 */
	protected function set_product_ids() {

		// get the products selected for the report
		$this->product_ids = isset( $_GET['product_ids'] ) ? array_filter( array_map( 'absint', (array) $_GET['product_ids'] ) ) : array();
	}


	/** Chart legend methods  *************************************************/


	/**
	 * Get the chart legend data
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function get_chart_legend() {

		if ( empty( $this->product_ids ) ) {
			return array();
		}

		$data = $this->get_report_data();

		$legend = array(

			// total product sales
			array(
				/* translators: Placeholders: %1$s is the formatted total product sales with surrounding <strong> tags, e.g. <strong>$7.77</strong> */
				'title'            => sprintf( __( '%1$s sales for the selected items', 'woocommerce-cost-of-goods' ), '<strong>' . wc_price( $data->total_sales ) . '</strong>' ),
				'color'            => $this->chart_colors['total_sales'],
				'highlight_series' => 1
			),

			// total product cost of goods
			array(
				/* translators: Placeholders: %1$s is the formatted total product cost of goods with surrounding <strong> tags, e.g. <strong>$4.77</strong> */
				'title'            => sprintf( __( '%1$s cost of goods for the selected items', 'woocommerce-cost-of-goods' ), '<strong>' . wc_price( $data->total_cogs ) . '</strong>' ),
				'color'            => $this->chart_colors['total_cogs'],
				'highlight_series' => 2
			),

			// total product profit
			array(
				/* translators: Placeholders: %1$s is the formatted total product profit with surrounding <strong> tags, e.g. <strong>$3.00</strong> */
				'title'            => sprintf( __( '%1$s profit for the selected items', 'woocommerce-cost-of-goods' ), '<strong>' . wc_price( $data->total_profit ) . '</strong>' ),
				'color'            => $this->chart_colors['total_profit'],
				'highlight_series' => 3
			),

			// total product items
			array(
				/* translators: Placeholders: %1$s is the the total number of purchased items with surrounding <strong> tags, e.g. <strong>5</strong> */
				'title'            => sprintf( __( '%1$s purchases for the selected items', 'woocommerce-cost-of-goods' ), '<strong>' . $data->total_items . '</strong>' ),
				'color'            => $this->chart_colors['total_items'],
				'highlight_series' => 0
			),
		);

		return $legend;
	}


	/** Chart Widget methods  *************************************************/


	/**
	 * Get the widgets for this report:
	 *
	 * 1) Product Search - search for a product to report on
	 * 2) Profitable Sellers - a simple listing of the most/least profitable products
	 * 4) Filters - indicates which product(s) are active for the report
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function get_chart_widgets() {

		$widgets = array(
			array(
				'title'    => __( 'Product Search', 'woocommerce-cost-of-goods' ),
				'callback' => array( $this, 'output_product_search_widget' ),
			),
			array(
				'title'    => '',
				'callback' => array( $this, 'output_profitable_sellers_widget' ),
			),
		);

		// add filter widget if filtering by product
		if ( ! empty( $this->product_ids ) ) {

			array_unshift( $widgets, array(
				'title'    => __( 'Showing reports for:', 'woocommerce-cost-of-goods' ),
				'callback' => array( $this, 'output_current_filters_widget' )
			) );
		}

		return $widgets;
	}


	/**
	 * Show current product filters for the report
	 *
	 * @since 2.0.0
	 */
	public function output_current_filters_widget() {

		$product_titles = array();

		foreach ( $this->product_ids as $product_id ) {

			$product = wc_get_product( $product_id );

			$product_titles[] = $product instanceof \WC_Product ? $product->get_formatted_name() : '#' . $product_id;
		}

		printf( '<p><strong>%1$s</strong></p><p><a class="button" href="%2$s">%3$s</a></p>', implode( ', ', $product_titles ), esc_url( remove_query_arg( 'product_ids' ) ), __( 'Reset', 'woocommerce-cost-of-goods' ) );
	}


	/**
	 * Show the product search widget
	 *
	 * @since 2.0.0
	 */
	public function output_product_search_widget() {

		?>
		<div class="section">
			<form method="GET">
				<div>
					<select
						name="product_ids[]"
						class="wc-product-search"
						style="width:203px;"
						data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce-cost-of-goods' ); ?>"
						data-action="woocommerce_json_search_products_and_variations">
					</select>
					<input type="submit" class="submit button" value="<?php esc_attr_e( 'Show', 'woocommerce-cost-of-goods' ); ?>" />
					<input type="hidden" name="range" value="<?php if ( ! empty( $_GET['range'] ) ) echo esc_attr( $_GET['range'] ); ?>" />
					<input type="hidden" name="start_date" value="<?php if ( ! empty( $_GET['start_date'] ) ) echo esc_attr( $_GET['start_date'] ); ?>" />
					<input type="hidden" name="end_date" value="<?php if ( ! empty( $_GET['end_date'] ) ) echo esc_attr( $_GET['end_date'] ); ?>" />
					<input type="hidden" name="page" value="<?php if ( ! empty( $_GET['page'] ) ) echo esc_attr( $_GET['page'] ); ?>" />
					<input type="hidden" name="tab" value="<?php if ( ! empty( $_GET['tab'] ) ) echo esc_attr( $_GET['tab'] ); ?>" />
					<input type="hidden" name="report" value="<?php if ( ! empty( $_GET['report'] ) ) echo esc_attr( $_GET['report'] ); ?>" />
				</div>
			</form>
		</div>
		<?php
	}


	/**
	 * Show the profitable sellers widget
	 *
	 * @since 2.0.0
	 */
	public function output_profitable_sellers_widget() {

		if ( isset( $_GET['show_least_profitable'] ) ) {
			$order_by = 'ASC';
			$title = esc_html__( 'Least Profitable Sellers', 'woocommerce-cost-of-goods' );
		} else {
			$order_by = 'DESC';
			$title = esc_html__( 'Most Profitable Sellers', 'woocommerce-cost-of-goods' );
		}

		?>
		<h4 class="section_title"><span><?php echo $title; ?></span></h4>
		<div class="section">
			<table cellspacing="0">
				<?php
				$sellers = $this->get_order_report_data( array(
					'data' => array(
						'_line_total' => array(
							'type'            => 'order_item_meta',
							'order_item_type' => 'line_item',
							'function'        => 'SUM',
							'name'            => 'total_sales',
						),
						'_wc_cog_item_total_cost' => array(
							'type'            => 'order_item_meta',
							'order_item_type' => 'line_item',
							'function'        => 'SUM( order_item_meta__line_total.meta_value ) - SUM', // hack so we can order by the calculated profit
							'name'            => 'total_profit',
							'join_type'       => 'LEFT',
						),
						'_product_id' => array(
								'type'            => 'order_item_meta',
								'order_item_type' => 'line_item',
								'function'        => '',
								'name'            => 'product_id',
						),
					),
					'order_by'     => "total_profit {$order_by}",
					'group_by'     => 'product_id',
					'limit'        => 12,
					'query_type'   => 'get_results',
					'filter_range' => true,
				) );

				if ( $sellers ) {
					foreach ( $sellers as $product ) {

						$profit_margin = 0;

						if ( $product->total_sales > 0 ) {
							$profit_margin = ( $product->total_profit / $product->total_sales ) * 100;
						}

						?>
						<tr class="<?php echo in_array( $product->product_id, $this->product_ids, false ) ? 'active' : ''; ?>">
							<td class="tips" data-tip="<?php /* translators: Placeholders: %1$s - profit margin as a percentage, e.g. 85.4% */ printf( esc_attr__( '%1$s%% profit margin', 'woocommerce-cost-of-goods' ), $this->format_decimal( $profit_margin ) ); ?>">
								<?php echo wc_price( $product->total_profit ) . ' <small>' . esc_html__( 'total profit', 'woocommerce-cost-of-goods' ) . '</small>'; ?></td>
							<td class="name"><a href="<?php echo esc_url( add_query_arg( 'product_ids', $product->product_id ) ) . '">' . get_the_title( $product->product_id ); ?></a></td>
							<td class="sparkline"><?php echo $this->sales_sparkline( $product->product_id, 7, 'sales' ); ?></td>
						</tr>
						<?php
					}
				} else {
					echo '<tr><td colspan="3">' . __( 'No products found in range', 'woocommerce-cost-of-goods' ) . '</td></tr>';
				}
				?><tr><td colspan="3" style="text-align: right"><small><?php
					if ( isset( $_GET['show_least_profitable'] ) ) {
						echo '<a href="' . esc_url( remove_query_arg( 'show_least_profitable' ) ) . '">' . esc_html__( 'show most profitable', 'woocommerce-cost-of-goods' ) . '</a>';
					} else {
						echo '<a href="' . esc_url( add_query_arg( array( 'show_least_profitable' => 1 ) ) ) . '">' . esc_html__( 'show least profitable', 'woocommerce-cost-of-goods' ) . '</a>';
					}
				?>
				</small></td></tr>
			</table>
		</div>
		<script type="text/javascript">
			jQuery('.section_title' ).click( function() {
				var section = jQuery(this).next( '.section' );

				if ( section.is( ':visible' ) ) {
					section.slideUp();
				} else {
					section.slideDown();
				}

				return false;
			});
		</script>
		<?php
	}


	/** Chart Methods *********************************************************/


	/**
	 * Render the "Export to CSV" button
	 *
	 * @since 2.0.0
	 */
	public function get_export_button() {

		$this->output_export_button();
	}


	/**
	 * Render the main chart
	 *
	 * @since 2.0.0
	 */
	public function get_main_chart() {

		if ( empty( $this->product_ids ) ) {
			?>
			<div class="chart-container">
				<p class="chart-prompt"><?php _e( '&larr; Choose a product to view stats', 'woocommerce-cost-of-goods' ); ?></p>
			</div>
			<?php
			return;
		}

		$data = $this->get_report_data();

		// prep data for charting
		$sales       = $this->prepare_chart_data( $data->sales,       'post_date', 'order_item_amount',     $this->chart_interval, $this->start_date, $this->chart_groupby );
		$cogs        = $this->prepare_chart_data( $data->cogs,        'post_date', 'order_item_total_cost', $this->chart_interval, $this->start_date, $this->chart_groupby );
		$profits     = $this->prepare_chart_data( $data->profits,     'post_date', 'order_item_profit',     $this->chart_interval, $this->start_date, $this->chart_groupby );
		$item_counts = $this->prepare_chart_data( $data->item_counts, 'post_date', 'order_item_count',      $this->chart_interval, $this->start_date, $this->chart_groupby );

		$chart_data = array(
			'sales'       => array_values( $sales ),
			'cogs'        => array_values( $cogs ),
			'profits'     => array_values( $profits ),
			'item_counts' => array_values( $item_counts ),
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
							label     : "<?php echo esc_js( __( 'Number of items sold', 'woocommerce-cost-of-goods' ) ) ?>",
							data      : order_data.item_counts,
							color     : '<?php echo $this->chart_colors['total_items']; ?>',
							bars      : { fillColor: '<?php echo $this->chart_colors['total_items']; ?>', fill: true, show: true, lineWidth: 0, barWidth: <?php echo $this->barwidth; ?> * 0.5, align: 'center' },
							shadowSize: 0,
							hoverable : false
						},
						{
							label     : "<?php echo esc_js( __( 'Sales amount', 'woocommerce-cost-of-goods' ) ) ?>",
							data      : order_data.sales,
							yaxis     : 2,
							color     : '<?php echo $this->chart_colors['total_sales']; ?>',
							points    : { show: true, radius: 5, lineWidth: 3, fillColor: '#fff', fill: true },
							lines     : { show: true, lineWidth: 4, fill: false },
							shadowSize: 0,
							<?php echo $this->get_currency_tooltip(); ?>
						},
						{
							label     : "<?php echo esc_js( __( 'Cost of Goods Sold', 'woocommerce-cost-of-goods' ) ) ?>",
							data      : order_data.cogs,
							yaxis     : 2,
							color     : '<?php echo $this->chart_colors['total_cogs']; ?>',
							points    : { show: true, radius: 5, lineWidth: 3, fillColor: '#fff', fill: true },
							lines     : { show: true, lineWidth: 4, fill: false },
							shadowSize: 0,
							<?php echo $this->get_currency_tooltip(); ?>
						},
						{
							label     : "<?php echo esc_js( __( 'Profit amount', 'woocommerce-cost-of-goods' ) ) ?>",
							data      : order_data.profits,
							yaxis     : 2,
							color     : '<?php echo $this->chart_colors['total_profit']; ?>',
							points    : { show: true, radius: 5, lineWidth: 3, fillColor: '#fff', fill: true },
							lines     : { show: true, lineWidth: 4, fill: false },
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
									color       : '#ecf0f1',
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
	 * Get the data for the report legend:
	 *
	 * total_sales - total sales for the product(s)
	 * total_cogs - total cost of goods for the product(s)
	 * total_profit - total profit for the product(s)
	 * total_items - total count of product(s) sold
	 *
	 * @since 2.0.0
	 * @return \stdClass
	 */
	protected function get_report_data() {

		if ( ! empty( $this->report_data ) ) {
			return $this->report_data;
		}

		$this->report_data = new stdClass();

		// sales
		$this->report_data->sales = $this->get_order_report_data( array(
			'data' => array(
				'_line_total' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'line_item',
					'function'        => 'SUM',
					'name'            => 'order_item_amount',
				),
				'post_date' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date',
				),
				'_product_id' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'line_item',
					'function'        => '',
					'name'            => 'product_id',
				)
			),
			'where_meta' => array(
				'relation' => 'OR',
				array(
					'type'       => 'order_item_meta',
					'meta_key'   => array( '_product_id', '_variation_id' ),
					'meta_value' => $this->product_ids,
					'operator'   => 'IN',
				),
			),
			'group_by'     => 'product_id, ' . $this->group_by_query,
			'order_by'     => 'post_date ASC',
			'query_type'   => 'get_results',
			'filter_range' => true,
		) );

		$this->report_data->total_sales = $this->format_decimal( array_sum( wp_list_pluck( $this->report_data->sales, 'order_item_amount' ) ) );

		// COGS
		$this->report_data->cogs = $this->get_order_report_data( array(
			'data' => array(
				'_wc_cog_item_total_cost' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'line_item',
					'function'        => 'SUM',
					'name'            => 'order_item_total_cost',
				),
				'post_date' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date',
				),
				'_product_id' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'line_item',
					'function'        => '',
					'name'            => 'product_id',
				),
			),
			'where_meta' => array(
				'relation' => 'OR',
				array(
						'type'       => 'order_item_meta',
						'meta_key'   => array( '_product_id', '_variation_id' ),
						'meta_value' => $this->product_ids,
						'operator'   => 'IN',
				),
			),
			'group_by'     => 'product_id, ' . $this->group_by_query,
			'order_by'     => 'post_date ASC',
			'query_type'   => 'get_results',
			'filter_range' => true,
		) );

		$this->report_data->total_cogs = $this->format_decimal( array_sum( wp_list_pluck( $this->report_data->cogs, 'order_item_total_cost' ) ) );

		// profit
		$this->report_data->profits = $this->get_order_report_data( array(
			'data' => array(
				'_line_total' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'line_item',
					'function' => 'SUM',
					'name'     => 'order_item_amount',
				),
				'_wc_cog_item_total_cost' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'line_item',
					'function'        => 'SUM( order_item_meta__line_total.meta_value ) - SUM',
					'name'            => 'order_item_profit',
					'join_type'       => 'LEFT', // so refunds are included, which prior to 2.0.0 had no cost meta set
				),
				'post_date' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date',
				),
				'_product_id' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'line_item',
					'function'        => '',
					'name'            => 'product_id',
				),
			),
			'where_meta' => array(
				'relation' => 'OR',
				array(
					'type'       => 'order_item_meta',
					'meta_key'   => array( '_product_id', '_variation_id' ),
					'meta_value' => $this->product_ids,
					'operator'   => 'IN',
				),
			),
			'group_by'     => 'product_id, ' . $this->group_by_query,
			'order_by'     => 'post_date ASC',
			'query_type'   => 'get_results',
			'filter_range' => true,
		) );

		$this->report_data->total_profit = $this->format_decimal( array_sum( wp_list_pluck( $this->report_data->profits, 'order_item_profit' ) ) );

		// item counts
		$this->report_data->item_counts = $this->get_order_report_data( array(
			'data' => array(
				'_qty' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'line_item',
					'function'        => 'SUM',
					'name'            => 'order_item_count',
				),
				'post_date' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date',
				),
				'_product_id' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'line_item',
					'function'        => '',
					'name'            => 'product_id',
				),
			),
			'where_meta' => array(
				'relation' => 'OR',
				array(
						'type'       => 'order_item_meta',
						'meta_key'   => array( '_product_id', '_variation_id' ),
						'meta_value' => $this->product_ids,
						'operator'   => 'IN',
				),
			),
			'group_by'     => 'product_id,' . $this->group_by_query,
			'order_by'     => 'post_date ASC',
			'query_type'   => 'get_results',
			'order_types'  => wc_get_order_types( 'order-count' ),
			'filter_range' => true,
		) );

		$this->report_data->total_items = absint( array_sum( wp_list_pluck( $this->report_data->item_counts, 'order_item_count' ) ) );


		/**
		 * Profit by Product Report Data Filter.
		 *
		 * Allow actors to filter the data returned for the profit by product
		 * report.
		 *
		 * @since 2.0.0
		 * @param array $report_data
		 * @param array $product_ids product IDs being reported on
		 * @param \WC_COG_Admin_Report_Profit_by_Product $this instance
		 */
		return apply_filters( 'wc_cost_of_goods_profit_by_product_report_data', $this->report_data, $this->product_ids, $this );
	}


}
