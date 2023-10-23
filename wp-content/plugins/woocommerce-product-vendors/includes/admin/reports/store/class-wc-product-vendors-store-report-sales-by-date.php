<?php
/**
 * Report class responsible for handling sales by date reports.
 *
 * @since      2.0.0
 *
 * @package    WooCommerce Product Vendors
 * @subpackage WooCommerce Product Vendors/Reports
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php' );

class WC_Product_Vendors_Store_Report_Sales_By_Date extends WC_Admin_Report {
	public $chart_colors = array();
	public $current_range;
	public $vendor_id;
	private $report_data;

	/**
	 * Constructor
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return bool
	 */
	public function __construct() {
		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
			$current_range = '7day';
		}

		$this->current_range = $current_range;

		$this->vendor_id = ! empty( $_GET['vendor_id'] ) ? sanitize_text_field( $_GET['vendor_id'] ) : '';
	}

	/**
	 * Get the report data
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return array of objects
	 */
	public function get_report_data() {
		if ( empty( $this->report_data ) ) {
			$this->query_report_data();
		}

		return $this->report_data;
	}

	/**
	 * Get the report based on parameters
	 *
	 * @access public
	 * @since 2.0.0
	 * @since 2.2.0 Use WC_Product_Vendor_Transient_Manager to get and set data in transient.
	 * @version 2.0.0
	 * @return array of objects
	 */
	public function query_report_data() {
		global $wpdb;

		$this->report_data = new stdClass;

		// check if table exists before continuing
		if ( ! WC_Product_Vendors_Utils::commission_table_exists() ) {
			return $this->report_data;
		}

		$transient_name = 'store_sales_by_date_' . $this->current_range;

		$start_date = '';
		$end_date   = '';
		if ( ! empty( $_GET['start_date'] ) ) {
			$start_date = sanitize_text_field( $_GET['start_date'] );
			$start_date = WC_Product_Vendors_Utils::is_valid_mysql_formatted_date( $start_date ) ? $start_date : '';
		}

		if ( ! empty( $_GET['end_date'] ) ) {
			$end_date = sanitize_text_field( $_GET['end_date'] );
			$end_date = WC_Product_Vendors_Utils::is_valid_mysql_formatted_date( $end_date ) ? $end_date : '';
		}

		if ( 'custom' === $this->current_range ) {
			$transient_name .= '_' . $start_date . '-' . $end_date;
		}

		$sql = "SELECT * FROM " . WC_PRODUCT_VENDORS_COMMISSION_TABLE . " AS commission";
		$sql .= " WHERE 1=1";

		if ( ! empty( $this->vendor_id ) ) {
			$sql .= " AND commission.vendor_id = %d";
		}

		$sql .= " AND commission.commission_status != 'void'";
		$sql .= WC_Product_Vendors_Utils::get_commission_date_sql_query_from_range( $this->current_range, $start_date, $end_date );

		$vendor_report_transient_manager = WC_Product_Vendor_Transient_Manager::make();
		$results                         = $vendor_report_transient_manager->get( $transient_name );

		if ( ! $results ) {
			// Enable big selects for reports
			$wpdb->query( 'SET SESSION SQL_BIG_SELECTS=1' );

			if ( ! empty( $this->vendor_id ) ) {
				$results = $wpdb->get_results( $wpdb->prepare( $sql, $this->vendor_id ) ); // nosemgrep:audit.php.wp.security.sqli.input-in-sinks

			} else {
				$results = $wpdb->get_results( $sql ); // nosemgrep:audit.php.wp.security.sqli.input-in-sinks
			}

			$vendor_report_transient_manager->save( $transient_name, $results );
		}

		$total_product_amount      = 0.00;
		$total_shipping_amount     = 0.00;
		$total_shipping_tax_amount = 0.00;
		$total_product_tax_amount  = 0.00;
		$total_commission_amount   = 0.00;

		$total_orders = array();

		foreach( $results as $data ) {

			$total_orders[] = $data->order_id;

			$total_product_amount      += (float) sanitize_text_field( $data->product_amount );
			$total_product_tax_amount  += (float) sanitize_text_field( $data->product_tax_amount );
			$total_shipping_amount     += (float) sanitize_text_field( $data->product_shipping_amount );
			$total_shipping_tax_amount += (float) sanitize_text_field( $data->product_shipping_tax_amount );
			$total_commission_amount   += (float) sanitize_text_field( $data->total_commission_amount );
		}

		$total_orders = count( array_unique( $total_orders ) );
		$total_sales = $total_product_amount + $total_product_tax_amount + $total_shipping_amount + $total_shipping_tax_amount;
		$net_sales = $total_sales - $total_product_tax_amount - $total_shipping_amount - $total_shipping_tax_amount;
		$total_tax_amount = $total_product_tax_amount + $total_shipping_tax_amount;

		$this->report_data->total_sales           = $total_sales;
		$this->report_data->net_sales             = wc_format_decimal( $net_sales );
		$this->report_data->average_sales         = wc_format_decimal( $net_sales / ( $this->chart_interval + 1 ), 2 );
		$this->report_data->total_orders          = $total_orders;
		$this->report_data->total_items           = count( $results );
		$this->report_data->total_shipping        = wc_format_decimal( $total_shipping_amount );
		$this->report_data->total_commission      = wc_format_decimal( $total_commission_amount );
		$this->report_data->total_tax             = wc_format_decimal( $total_tax_amount );
	}

	/**
	 * Get the legend for the main chart sidebar
	 * @return array
	 */
	public function get_chart_legend() {
		$legend = array();
		$data   = $this->get_report_data();

		switch ( $this->chart_groupby ) {
			case 'day' :
				$average_sales_title = sprintf( __( '%s average daily sales', 'woocommerce-product-vendors' ), '<strong>' . wc_price( $data->average_sales ) . '</strong>' );
			break;
			case 'month' :
			default :
				$average_sales_title = sprintf( __( '%s average monthly sales', 'woocommerce-product-vendors' ), '<strong>' . wc_price( $data->average_sales ) . '</strong>' );
			break;
		}

		$legend[] = array(
			'title'            => sprintf( __( '%s gross sales in this period', 'woocommerce-product-vendors' ), '<strong>' . wc_price( $data->total_sales ) . '</strong>' ),
			'placeholder'      => __( 'This is the sum of the order totals after any refunds and including shipping and taxes.', 'woocommerce-product-vendors' ),
			'color'            => $this->chart_colors['sales_amount'],
			'highlight_series' => 4
		);

		$legend[] = array(
			'title'            => sprintf( __( '%s net sales in this period', 'woocommerce-product-vendors' ), '<strong>' . wc_price( $data->net_sales ) . '</strong>' ),
			'placeholder'      => __( 'This is the sum of the order totals after any refunds and excluding shipping and taxes.', 'woocommerce-product-vendors' ),
			'color'            => $this->chart_colors['net_sales_amount'],
			'highlight_series' => 5
		);

		if ( $data->average_sales > 0 ) {
			$legend[] = array(
				'title' => $average_sales_title,
				'color' => $this->chart_colors['average'],
				'highlight_series' => 3
			);
		}

		$legend[] = array(
			'title' => sprintf( __( '%s orders placed', 'woocommerce-product-vendors' ), '<strong>' . $data->total_orders . '</strong>' ),
			'color' => $this->chart_colors['order_count'],
			'highlight_series' => 0
		);

		$legend[] = array(
			'title' => sprintf( __( '%s items purchased', 'woocommerce-product-vendors' ), '<strong>' . $data->total_items . '</strong>' ),
			'color' => $this->chart_colors['item_count'],
			'highlight_series' => 1
		);

		$legend[] = array(
			'title' => sprintf( __( '%s charged for shipping', 'woocommerce-product-vendors' ), '<strong>' . wc_price( $data->total_shipping ) . '</strong>' ),
			'color' => $this->chart_colors['shipping_amount'],
			'highlight_series' => 2
		);

		$legend[] = array(
			'title' => sprintf( __( '%s total commission (vendors)', 'woocommerce-product-vendors' ), '<strong>' . wc_price( $data->total_commission ) . '</strong>' ),
			'placeholder'      => __( 'This is the sum of the commission including shipping and taxes if applicable.', 'woocommerce-product-vendors' ),
			'color' => $this->chart_colors['commission'],
			'highlight_series' => 6
		);

		return $legend;
	}

	/**
	 * Output the report
	 */
	public function output_report() {
		$ranges = array(
			'year'         => __( 'Year', 'woocommerce-product-vendors' ),
			'last_month'   => __( 'Last Month', 'woocommerce-product-vendors' ),
			'month'        => __( 'This Month', 'woocommerce-product-vendors' ),
			'7day'         => __( 'Last 7 Days', 'woocommerce-product-vendors' ),
		);

		$this->chart_colors = array(
			'sales_amount'     => '#b1d4ea',
			'net_sales_amount' => '#3498db',
			'average'          => '#95a5a6',
			'order_count'      => '#dbe1e3',
			'item_count'       => '#ecf0f1',
			'shipping_amount'  => '#5cc488',
			'commission'       => '#FF69B4',
		);

		$current_range = $this->current_range;

		$this->calculate_current_range( $this->current_range );

		/**
		 * Optionally override the views/html-report-by-date.php view: filters must return a string to be passed into include_once.
		 *
		 * @since 2.1.77
		 *
		 * @param string $path Default path to the view.
		 */
		include( apply_filters( 'wcpv_report_by_date_template', 'views/html-report-by-date.php' ) );
	}

	/**
	 * Output an export link
	 */
	public function get_export_button() {
		?>
		<a
			href="#"
			download="report-<?php echo esc_attr( $this->current_range ); ?>-<?php echo esc_attr( date_i18n( 'Y-m-d', current_time('timestamp') ) ); ?>.csv"
			class="export_csv"
			data-export="chart"
			data-xaxes="<?php esc_attr_e( 'Date', 'woocommerce-product-vendors' ); ?>"
			data-exclude_series="2"
			data-groupby="<?php echo esc_attr( $this->chart_groupby ); ?>"
			data-range="<?php echo esc_attr( $this->current_range ); ?>"
			data-custom-range="<?php echo 'custom' === $this->current_range ? esc_attr( $this->start_date . '-' . $this->end_date ) : ''; ?>"
		>
			<?php esc_html_e( 'Export CSV', 'woocommerce-product-vendors' ); ?>
		</a>
		<?php
	}

	/**
	 * Round our totals correctly
	 * @param  string $amount
	 * @return string
	 */
	private function round_chart_totals( $amount ) {
		if ( is_array( $amount ) ) {
			return array( $amount[0], wc_format_decimal( $amount[1], wc_get_price_decimals() ) );
		} else {
			return wc_format_decimal( $amount, wc_get_price_decimals() );
		}
	}

	/**
	 * Get the main chart
	 *
	 * @since 2.2.0 Use WC_Product_Vendor_Transient_Manager to get and set data in transient.
	 * @return string
	 */
	public function get_main_chart() {
		global $wp_locale, $wpdb;

		// check if table exists before continuing
		if ( ! WC_Product_Vendors_Utils::commission_table_exists() ) {
			return $this->report_data;
		}

		$transient_name = 'store_main_sales_by_date_' . $this->current_range;

		$start_date = '';
		$end_date   = '';
		if ( ! empty( $_GET['start_date'] ) ) {
			$start_date = sanitize_text_field( $_GET['start_date'] );
			$start_date = WC_Product_Vendors_Utils::is_valid_mysql_formatted_date( $start_date ) ? $start_date : '';
		}

		if ( ! empty( $_GET['end_date'] ) ) {
			$end_date = sanitize_text_field( $_GET['end_date'] );
			$end_date = WC_Product_Vendors_Utils::is_valid_mysql_formatted_date( $end_date ) ? $end_date : '';
		}


		if ( 'custom' === $this->current_range ) {
			$transient_name .= '_' . $start_date . '-' . $end_date;
		}

		$select = "SELECT COUNT( DISTINCT commission.order_id ) AS count, COUNT( commission.order_id ) AS order_item_count, SUM( commission.product_amount + commission.product_shipping_amount + commission.product_tax_amount + commission.product_shipping_tax_amount ) AS total_sales, SUM( commission.product_shipping_amount ) AS total_shipping, SUM( commission.product_tax_amount ) AS total_tax, SUM( commission.product_shipping_tax_amount ) AS total_shipping_tax, SUM( commission.total_commission_amount ) AS total_commission, commission.order_date";

		$sql = $select;
		$sql .= " FROM " . WC_PRODUCT_VENDORS_COMMISSION_TABLE . " AS commission";
		$sql .= " WHERE 1=1";

		if ( ! empty( $this->vendor_id ) ) {
			$sql .= " AND commission.vendor_id = %d";
		}

		$sql .= " AND commission.commission_status != 'void'";
		$sql .= WC_Product_Vendors_Utils::get_commission_date_sql_query_from_range( $this->current_range, $start_date, $end_date );
		$sql .= " GROUP BY DATE( commission.order_date )";

		$vendor_report_transient_manager = WC_Product_Vendor_Transient_Manager::make();
		$results                         = $vendor_report_transient_manager->get( $transient_name );

		if ( ! $results ) {
			// Enable big selects for reports
			$wpdb->query( 'SET SESSION SQL_BIG_SELECTS=1' );

			if ( ! empty( $this->vendor_id ) ) {
				$results = $wpdb->get_results( $wpdb->prepare( $sql, $this->vendor_id ) ); // nosemgrep:audit.php.wp.security.sqli.input-in-sinks
			} else {
				$results = $wpdb->get_results( $sql ); // nosemgrep:audit.php.wp.security.sqli.input-in-sinks
			}

			$vendor_report_transient_manager->save( $transient_name, $results );
		}

		// Prepare data for report
		$order_counts         = $this->prepare_chart_data( $results, 'order_date', 'count', $this->chart_interval, $this->start_date, $this->chart_groupby );

		$order_item_counts    = $this->prepare_chart_data( $results, 'order_date', 'order_item_count', $this->chart_interval, $this->start_date, $this->chart_groupby );

		$order_amounts        = $this->prepare_chart_data( $results, 'order_date', 'total_sales', $this->chart_interval, $this->start_date, $this->chart_groupby );

		$shipping_amounts     = $this->prepare_chart_data( $results, 'order_date', 'total_shipping', $this->chart_interval, $this->start_date, $this->chart_groupby );

		$shipping_tax_amounts = $this->prepare_chart_data( $results, 'order_date', 'total_shipping_tax', $this->chart_interval, $this->start_date, $this->chart_groupby );

		$tax_amounts          = $this->prepare_chart_data( $results, 'order_date', 'total_tax', $this->chart_interval, $this->start_date, $this->chart_groupby );

		$total_commission     = $this->prepare_chart_data( $results, 'order_date', 'total_commission', $this->chart_interval, $this->start_date, $this->chart_groupby );

		$net_order_amounts = array();

		foreach ( $order_amounts as $order_amount_key => $order_amount_value ) {
			$net_order_amounts[ $order_amount_key ]    = $order_amount_value;
			$net_order_amounts[ $order_amount_key ][1] = $net_order_amounts[ $order_amount_key ][1] - $shipping_amounts[ $order_amount_key ][1] - $shipping_tax_amounts[ $order_amount_key ][1] - $tax_amounts[ $order_amount_key ][1];
		}

		// Encode in json format
		$chart_data = rawurlencode( wp_json_encode( array(
			'order_counts'      => array_values( $order_counts ),
			'order_item_counts' => array_values( $order_item_counts ),
			'order_amounts'     => array_map( array( $this, 'round_chart_totals' ), array_values( $order_amounts ) ),
			'net_order_amounts' => array_map( array( $this, 'round_chart_totals' ), array_values( $net_order_amounts ) ),
			'shipping_amounts'  => array_map( array( $this, 'round_chart_totals' ), array_values( $shipping_amounts ) ),
			'total_commission'  => array_map( array( $this, 'round_chart_totals' ), array_values( $total_commission ) ),
		) ) );
		?>
		<div class="chart-container">
			<div class="chart-placeholder main"></div>
		</div>
		<script type="text/javascript">

			var main_chart;

			jQuery(function(){
				var order_data = JSON.parse( decodeURIComponent( <?php echo wp_json_encode( $chart_data ); ?> ) );
				var drawGraph = function( highlight ) {
					var series = [
						{
							label: <?php echo wp_json_encode( __( 'Number of orders', 'woocommerce-product-vendors' ) ); ?>,
							data: order_data.order_counts,
							color: <?php echo wp_json_encode( $this->chart_colors['order_count'] ); ?>,
							bars: { fillColor: <?php echo wp_json_encode( $this->chart_colors['order_count'] ); ?>, fill: true, show: true, lineWidth: 0, barWidth: <?php echo wp_json_encode( (int) $this->barwidth ); ?> * 0.5, align: 'left' },
							shadowSize: 0,
							hoverable: false
						},
						{
							label: <?php echo wp_json_encode( __( 'Number of items sold', 'woocommerce-product-vendors' ) ); ?>,
							data: order_data.order_item_counts,
							color: <?php echo wp_json_encode( $this->chart_colors['item_count'] ); ?>,
							bars: { fillColor: <?php echo wp_json_encode( $this->chart_colors['item_count'] ); ?>, fill: true, show: true, lineWidth: 0, barWidth: <?php echo wp_json_encode( (int) $this->barwidth ); ?> * 0.5, align: 'center' },
							shadowSize: 0,
							hoverable: false
						},
						{
							label: <?php echo wp_json_encode( __( 'Shipping amount', 'woocommerce-product-vendors' ) ); ?>,
							data: order_data.shipping_amounts,
							yaxis: 2,
							color: <?php echo wp_json_encode( $this->chart_colors['shipping_amount'] ); ?>,
							points: { show: true, radius: 5, lineWidth: 2, fillColor: '#fff', fill: true },
							lines: { show: true, lineWidth: 2, fill: false },
							shadowSize: 0,
							prepend_tooltip: <?php echo wp_json_encode( get_woocommerce_currency_symbol() ); ?>
						},
						{
							label: <?php echo wp_json_encode( __( 'Average sales amount', 'woocommerce-product-vendors' ) ); ?>,
							data: [ [ <?php echo wp_json_encode( min( array_keys( $order_amounts ) ) ); ?>, <?php echo wp_json_encode( $this->report_data->average_sales ); ?> ], [ <?php echo wp_json_encode( max( array_keys( $order_amounts ) ) ); ?>, <?php echo wp_json_encode( $this->report_data->average_sales ); ?> ] ],
							yaxis: 2,
							color: <?php echo wp_json_encode( $this->chart_colors['average'] ); ?>,
							points: { show: false },
							lines: { show: true, lineWidth: 2, fill: false },
							shadowSize: 0,
							hoverable: false
						},
						{
							label: <?php echo wp_json_encode( __( 'Gross Sales amount', 'woocommerce-product-vendors' ) ); ?>,
							data: order_data.order_amounts,
							yaxis: 2,
							color: <?php echo wp_json_encode( $this->chart_colors['sales_amount'] ); ?>,
							points: { show: true, radius: 5, lineWidth: 2, fillColor: '#fff', fill: true },
							lines: { show: true, lineWidth: 2, fill: false },
							shadowSize: 0,
							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already in JSON format.
							echo $this->get_currency_tooltip();
							?>
						},
						{
							label: <?php echo wp_json_encode( __( 'Net Sales amount', 'woocommerce-product-vendors' ) ); ?>,
							data: order_data.net_order_amounts,
							yaxis: 2,
							color: <?php echo wp_json_encode( $this->chart_colors['net_sales_amount'] ); ?>,
							points: { show: true, radius: 6, lineWidth: 4, fillColor: '#fff', fill: true },
							lines: { show: true, lineWidth: 5, fill: false },
							shadowSize: 0,
							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already in JSON format.
							echo $this->get_currency_tooltip();
							?>
						},
						{
							label: <?php echo wp_json_encode( __( 'Total Commission Amount (vendors)', 'woocommerce-product-vendors' ) ); ?>,
							data: order_data.total_commission,
							yaxis: 2,
							color: <?php echo wp_json_encode( $this->chart_colors['commission'] ); ?>,
							points: { show: true, radius: 6, lineWidth: 4, fillColor: '#fff', fill: true },
							lines: { show: true, lineWidth: 5, fill: false },
							shadowSize: 0,
							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already in JSON format.
							echo $this->get_currency_tooltip();
							?>
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
								timeformat: "<?php if ( $this->chart_groupby == 'day' ) echo '%d %b'; else echo '%b'; ?>",
								monthNames: <?php echo wp_json_encode( array_values( $wp_locale->month_abbrev ) ); ?>,
								tickLength: 1,
								minTickSize: [1, <?php echo wp_json_encode( $this->chart_groupby ); ?>],
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

					jQuery('.chart-placeholder').trigger( 'resize' );
				}

				drawGraph();

				jQuery('.highlight_series').on( 'mouseenter',
					function() {
						drawGraph( jQuery(this).data('series') );
					} ).on( 'mouseleave',
					function() {
						drawGraph();
					}
				);
			});
		</script>
		<?php
	}

	/**
	 * [get_chart_widgets description]
	 *
	 * @return array
	 */
	public function get_chart_widgets() {
		$widgets = array();

		if ( ! empty( $_GET['vendor_id'] ) ) {
			$widgets[] = array(
				'title'    => __( 'Showing reports for:', 'woocommerce-product-vendors' ),
				'callback' => array( $this, 'current_filters' )
			);
		}

		$widgets[] = array(
			'title'    => '',
			'callback' => array( $this, 'vendor_widget' )
		);

		return $widgets;
	}

	/**
	 * Show current filters
	 */
	public function current_filters() {
		$vendor_id = sanitize_text_field( $_GET['vendor_id'] );

		$vendor = get_term( $vendor_id, WC_PRODUCT_VENDORS_TAXONOMY );

		echo '<p>' . ' <strong>' . esc_html( $vendor->name ) . '</strong></p>';
		echo '<p><a class="button" href="' . esc_url( remove_query_arg( 'vendor_id' ) ) . '">' . esc_html__( 'Reset', 'woocommerce-product-vendors' ) . '</a></p>';
	}

	/**
	 * Vendor selection
	 */
	public function vendor_widget() {
		?>
		<h4 class="section_title wcpv-vendor-search"><span><?php esc_html_e( 'Vendor Search', 'woocommerce-product-vendors' ); ?></span></h4>
		<div class="section">
			<form method="GET">
				<div>
					<select style="width: 50%;" class="wcpv-vendor-search-bar" name="vendor_id" data-placeholder="<?php esc_attr_e( 'Search for a vendor&hellip;', 'woocommerce-product-vendors' ); ?>">
					</select>
					<input type="submit" class="submit button" value="<?php esc_attr_e( 'Show', 'woocommerce-product-vendors' ); ?>" />
					<input type="hidden" name="range" value="<?php echo esc_attr( wc_clean( wp_unslash( $_GET['range'] ?? '' ) ) ); ?>" />
					<input type="hidden" name="start_date" value="<?php echo esc_attr( wc_clean( wp_unslash( $_GET['start_date'] ?? '' ) ) ); ?>" />
					<input type="hidden" name="end_date" value="<?php echo esc_attr( wc_clean( wp_unslash( $_GET['end_date'] ?? '' ) ) ); ?>" />
					<input type="hidden" name="page" value="<?php echo esc_attr( wc_clean( wp_unslash( $_GET['page'] ?? '' ) ) ); ?>" />
					<input type="hidden" name="tab" value="<?php echo esc_attr( wc_clean( wp_unslash( $_GET['tab'] ?? '' ) ) ); ?>" />
				</div>
			</form>
		</div>
	<?php
	}
}
