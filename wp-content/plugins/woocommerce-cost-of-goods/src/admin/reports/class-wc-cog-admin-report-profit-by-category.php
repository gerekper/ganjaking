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
 * Cost of Goods Profit by Category Admin Report Class
 *
 * Handles generating and rendering the Profit by Category report
 *
 * @since 2.0.0
 */
class WC_COG_Admin_Report_Profit_by_Category extends \WC_COG_Admin_Report {


	/** @var array report data, array value instead of stdClass for this report */
	protected $report_data = array();

	/** @var array category IDs for the report */
	protected $category_ids;

	/** @var array define the chart colors for this report */
	protected $chart_colors = array( '#3498db', '#34495e', '#1abc9c', '#2ecc71',
		'#f1c40f', '#e67e22', '#e74c3c', '#2980b9', '#8e44ad', '#2c3e50',
		'#16a085', '#27ae60', '#f39c12', '#d35400', '#c0392b'
	);


	/**
	 * Bootstrap class
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->set_category_ids();
	}


	/**
	 * Set the category IDs for the report
	 *
	 * @since 2.0.0
	 */
	protected function set_category_ids() {

		$this->category_ids = isset( $_GET['category_ids'] ) ? array_filter( array_map( 'absint', (array) $_GET['category_ids'] ) ) : array();
	}


	/**
	 * Get all product IDs in a parent category and its children
	 *
	 * @since 2.0.0
	 * @param int $category_id
	 * @return array
	 */
	protected function get_product_ids_in_category( $category_id ) {

		$term_ids = get_term_children( $category_id, 'product_cat' );

		return array_unique( get_objects_in_term( array_merge( $term_ids, (array) $category_id ), 'product_cat' ) );
	}


	/** Chart Legend methods  *************************************************/


	/**
	 * Get the chart legend data
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function get_chart_legend() {

		$legend = array();
		$index  = 0;

		foreach ( $this->category_ids as $category_id ) {

			$category = get_term( $category_id, 'product_cat' );

			$data = $this->get_report_data( $category->term_id );

			$legend[] = array(
				/* translators: Placeholders: %1$s - formatted total profit amount surrounded by <strong> tags, e.g. <strong>$66.77</strong>, %1$s - product category name, e.g. t-shirts */
				'title'            => sprintf( __( '%1$s total profit in %2$s', 'woocommerce-cost-of-goods' ), '<strong>' . wc_price( ( ! empty( $data->total_profit ) ? $data->total_profit : 0 ) ) . '</strong>', $category->name ),
				'color'            => isset( $this->chart_colors[ $index ] ) ? $this->chart_colors[ $index ] : $this->chart_colors[ 0 ],
				'highlight_series' => $index,
			);

			$index++;
		}

		return $legend;
	}


	/** Chart Widget methods  *************************************************/


	/**
	 * Get the widgets for this report:
	 *
	 * 1) the category select
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function get_chart_widgets() {

		return array(
			array(
				'title'    => __( 'Categories', 'woocommerce-cost-of-goods' ),
				'callback' => array( $this, 'output_category_widget' ),
			),
		);
	}


	/**
	 * Output the category select
	 *
	 * @since 2.0.0
	 */
	public function output_category_widget() {

		$categories = get_terms( 'product_cat', array( 'orderby' => 'name' ) );
		?>
		<form method="GET">
			<div>
				<select multiple="multiple" data-placeholder="<?php _e( 'Select categories&hellip;', 'woocommerce-cost-of-goods' ); ?>" class="wc-enhanced-select" id="category_ids" name="category_ids[]" style="width: 205px;">
					<?php
					$r                 = array();
					$r['pad_counts']   = 1;
					$r['hierarchical'] = 1;
					$r['hide_empty']   = 1;
					$r['value']        = 'id';
					$r['selected']     = $this->category_ids;

					include_once( WC()->plugin_path() . '/includes/walkers/class-product-cat-dropdown-walker.php' );

					echo wc_walk_category_dropdown_tree( $categories, 0, $r );
					?>
				</select>
				<a href="#" class="select_none"><?php esc_html_e( 'None', 'woocommerce-cost-of-goods' ); ?></a>
				<a href="#" class="select_all"><?php esc_html_e( 'All', 'woocommerce-cost-of-goods' ); ?></a>
				<input type="submit" class="submit button" value="<?php esc_attr_e( 'Show', 'woocommerce-cost-of-goods' ); ?>" />
				<input type="hidden" name="range" value="<?php if ( ! empty( $_GET['range'] ) ) echo esc_attr( $_GET['range'] ) ?>" />
				<input type="hidden" name="start_date" value="<?php if ( ! empty( $_GET['start_date'] ) ) echo esc_attr( $_GET['start_date'] ) ?>" />
				<input type="hidden" name="end_date" value="<?php if ( ! empty( $_GET['end_date'] ) ) echo esc_attr( $_GET['end_date'] ) ?>" />
				<input type="hidden" name="page" value="<?php if ( ! empty( $_GET['page'] ) ) echo esc_attr( $_GET['page'] ) ?>" />
				<input type="hidden" name="tab" value="<?php if ( ! empty( $_GET['tab'] ) ) echo esc_attr( $_GET['tab'] ) ?>" />
				<input type="hidden" name="report" value="<?php if ( ! empty( $_GET['report'] ) ) echo esc_attr( $_GET['report'] ) ?>" />
			</div>
			<script type="text/javascript">
				jQuery( function() {
					// select all
					jQuery( '.chart-widget' ).on( 'click', '.select_all', function() {
						jQuery(this).closest( 'div' ).find( 'select option' ).attr( "selected", "selected" );
						jQuery(this).closest( 'div' ).find('select').change();
						return false;
					} );

					// select none
					jQuery( '.chart-widget').on( 'click', '.select_none', function() {
						jQuery(this).closest( 'div' ).find( 'select option' ).removeAttr( "selected" );
						jQuery(this).closest( 'div' ).find('select').change();
						return false;
					} );
				} );
			</script>
		</form>
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

		if ( empty( $this->category_ids ) ) {
			?>
			<div class="chart-container">
				<p class="chart-prompt"><?php _e( '&larr; Choose a category to view stats', 'woocommerce-cost-of-goods' ); ?></p>
			</div>
			<?php
		}

		$chart_data = array();
		$index      = 0;

		foreach ( $this->category_ids as $category_id ) {

			$category = get_term( $category_id, 'product_cat' );
			$data     = $this->get_report_data( $category->term_id );

			$chart_data[ $category->term_id ]['category'] = $category->name;
			$chart_data[ $category->term_id ]['data']     = array_values( $this->prepare_chart_data( $data->profits, 'post_date', 'order_item_profit', $this->chart_interval, $this->start_date, $this->chart_groupby ) );

			$index ++;
		}

		?>
		<div class="chart-container">
			<div class="chart-placeholder main"></div>
		</div>
		<script type="text/javascript">
			var main_chart;

			jQuery(function(){
				var drawGraph = function( highlight ) {
					var series = [
						<?php
							$index = 0;
							foreach ( $chart_data as $data ) {
								$color  = isset( $this->chart_colors[ $index ] ) ? $this->chart_colors[ $index ] : $this->chart_colors[0];
								$width  = $this->barwidth / sizeof( $chart_data );
								$offset = ( $width * $index );
								$series = $data['data'];
								foreach ( $series as $key => $series_data )
									$series[ $key ][0] = $series_data[0] + $offset;
								echo '{
									label: "' . esc_js( $data['category'] ) . '",
									data : jQuery.parseJSON( "' . json_encode( $series ) . '" ),
									color: "' . $color . '",
									bars : {
										fillColor: "' . $color . '",
										fill     : true,
										show     : true,
										lineWidth: 1,
										align    : "center",
										barWidth : ' . $width * 0.75 . ',
										stack    : false
									},
									' . $this->get_currency_tooltip() . ',
									enable_tooltip: true,
									prepend_label : true
								},';
								$index++;
							}
						?>
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
								color       : '#aaa',
								reserveSpace: true,
								position    : 'bottom',
								tickColor   : 'transparent',
								mode        : 'time',
								timeformat  : "<?php if ( $this->chart_groupby == 'day' ) echo '%d %b'; else echo '%b'; ?>",
								monthNames  : <?php echo json_encode( array_values( $GLOBALS['wp_locale']->month_abbrev ) ); ?>,
								tickLength  : 1,
								minTickSize : [1, "<?php echo esc_js( $this->chart_groupby ); ?>"],
								tickSize    : [1, "<?php echo esc_js( $this->chart_groupby ); ?>"],
								font        : {
									color: '#aaa'
								}
							} ],
							yaxes: [
								{
									min         : 0,
									tickDecimals: 2,
									color       : 'transparent',
									font        : { color: "#aaa" }
								}
							],
						}
					);

					jQuery('.chart-placeholder').resize();

				};

				drawGraph();

				jQuery('.highlight_series').hover(
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
	 * total_profit - total profit for all products sold in a given category
	 *
	 * @since 2.0.0
	 * @param int $category_id
	 * @return \stdClass
	 */
	protected function get_report_data( $category_id ) {

		if ( ! empty( $this->report_data[ $category_id ] ) ) {
			return $this->report_data[ $category_id ];
		}

		$this->report_data[ $category_id ] = new stdClass();

		$product_ids = $this->get_product_ids_in_category( $category_id );

		if ( empty( $product_ids ) ) {
			return $this->report_data[ $category_id ] = null;
		}

		$this->report_data[ $category_id ]->profits = $this->get_order_report_data( array(
			'data' => array(
				'_line_total' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'line_item',
					'function'        => 'SUM',
					'name'            => 'order_item_amount',
				),
				'_wc_cog_item_total_cost' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'line_item',
					'function'        => 'SUM( order_item_meta__line_total.meta_value ) - SUM',
					'name'            => 'order_item_profit',
					'join_type'       => 'LEFT', // so refunds are included, which prior to 2.0.0 had no cost meta set
				),
				'_product_id' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'line_item',
					'function'        => '',
					'name'            => 'product_id',
				),
				'post_date' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date',
				),
			),
			'where_meta' => array(
				'relation' => 'OR',
				array(
					'type'       => 'order_item_meta',
					'meta_key'   => array( '_product_id', '_variation_id' ),
					'meta_value' => $this->get_product_ids_in_category( $category_id ),
					'operator'   => 'IN',
				),
			),
			'group_by'     => 'product_id, ' . $this->group_by_query,
			'order_by'     => 'post_date ASC',
			'query_type'   => 'get_results',
			'filter_range' => true,
		) );

		$this->report_data[ $category_id ]->total_profit = array_sum( wp_list_pluck( $this->report_data[ $category_id ]->profits, 'order_item_profit' ) );

		/**
		 * Profit by Category Report Data Filter.
		 *
		 * Allow actors to filter the data returned for the profit by category
		 * report.
		 *
		 * @since 2.0.0
		 * @param array $report_data
		 * @param int $category_id
		 * @param array $product_ids
		 * @param \WC_COG_Admin_Report_Profit_by_Category $this instance
		 */
		return apply_filters( 'wc_cost_of_goods_profit_by_category_report_data', $this->report_data[ $category_id ], $category_id, $product_ids, $this );
	}


}
