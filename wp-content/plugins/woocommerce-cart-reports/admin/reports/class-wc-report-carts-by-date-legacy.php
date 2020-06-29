<?php

/**
 * WC_Report_Carts_By_Date class
 */
class WC_Report_Carts_By_Date extends WC_Admin_Report {

	/**
	 * Get the legend for the main chart sidebar
	 *
	 * @return array
	 */
	public function get_chart_legend() {
		$legend = array();

		$order_totals = $this->get_order_report_data(
			array(
				'data' => array(
					'_order_total' => array(
						'type' => 'meta',
						'function' => 'SUM',
						'name' => 'total_sales'
					),
					'_order_shipping' => array(
						'type' => 'meta',
						'function' => 'SUM',
						'name' => 'total_shipping'
					),
					'ID' => array(
						'type' => 'post_data',
						'function' => 'COUNT',
						'name' => 'total_orders'
					)
				),
				'filter_range' => true
			)
		);

		$total_orders = absint( $order_totals->total_orders );

		//Now get the cart details to compare...

		global $wpdb;
		$sql = "
			SELECT meta.meta_value AS items, posts.post_modified FROM {$wpdb->posts} AS posts

			LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
			LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID=rel.object_ID
			LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
			LEFT JOIN {$wpdb->terms} AS term USING( term_id )

			WHERE 	meta.meta_key 		= 'av8_cartitems'
			AND 	posts.post_type 	= 'carts'
			AND 	posts.post_status 	= 'publish'
			AND 	tax.taxonomy		= 'shop_cart_status'
			AND		term.slug			IN ('open')
			AND		posts.post_modified		> date_sub( NOW(), INTERVAL 1 YEAR )
			ORDER BY posts.post_modified ASC
		";

		$abandoned_carts_items = $wpdb->get_results( $sql );

		//Use a custom function for just counting the abandoned carts

		$abandoned_carts = $this->get_count_within_range(
			$abandoned_carts_items,
			$this->start_date,
			$this->chart_interval
		);

		$legend[] = array(
			'title' => sprintf(
				__( '%s abandoned carts in this period.', 'woocommerce_cart_reports' ),
				'<strong>' . count( $abandoned_carts ) . '</strong>'
			),
			'color' => $this->chart_colours['abandoned_carts'],
			'highlight_series' => 1
		);
		$legend[] = array(
			'title' => sprintf(
				__( '%s orders placed', 'woocommerce_cart_reports' ),
				'<strong>' . $total_orders . '</strong>'
			),
			'color' => $this->chart_colours['order_counts'],
			'highlight_series' => 0
		);

		return $legend;
	}

	/**
	 * Output the report
	 */
	public function output_report() {
		global $woocommerce, $wpdb, $wp_locale;

		$ranges = array(
			'year' => __( 'Year', 'woocommerce_cart_reports' ),
			'last_month' => __( 'Last Month', 'woocommerce_cart_reports' ),
			'month' => __( 'This Month', 'woocommerce_cart_reports' ),
			'7day' => __( 'Last 7 Days', 'woocommerce_cart_reports' )
		);

		$this->chart_colours = array(
			'order_counts' => 'green',
			'abandoned_carts' => '#d54e21'
		);

		$current_range = ! empty( $_GET['range'] ) ? $_GET['range'] : '7day';

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
			$current_range = '7day';
		}

		$this->calculate_current_range( $current_range );

		include WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php';
	}

	/**
	 * Custom Cart Reports function to calculate the number of cart items in the range
	 */

	public function get_count_within_range( $data, $start_date, $chart_interval ) {
		$results = array();
		foreach ( $data as $d ) {
			$date_time = strtotime( $d->post_modified );

			//Add one more, so it includes the current day
			if ( ( $date_time <= ( $start_date + ( $chart_interval * ONEDAY ) + ONEDAY ) && $date_time >= $start_date ) || $chart_interval == 0 ) {
				$results[] = $d;
			}
		}

		return $results;
	}

	/**
	 * Output an export link
	 */
	public function get_export_button() {
		$current_range = ! empty( $_GET['range'] ) ? $_GET['range'] : '7day';
		$download_name = [ 'report', $current_range, date_i18n( 'Y-m-d', current_time( 'timestamp' ) ) ];
		?>
		<a
			href="#"
			download="<?php echo implode( '-', $download_name ); ?>.csv"
			class="export_csv"
			data-export="chart"
			data-xaxes="<?php _e( 'Date', 'woocommerce_cart_reports' ); ?>"
			data-exclude_series="2"
			data-groupby="<?php echo $this->chart_groupby; ?>"
		>
			<?php _e( 'Export CSV', 'woocommerce_cart_reports' ); ?>
		</a>
		<?php
	}

	/**
	 * Get the main chart
	 *
	 * @return string
	 */
	public function get_main_chart() {
		global $wp_locale;

		// Get orders and dates in range - we want the SUM of order totals, COUNT of order items, COUNT of orders, and the date
		$orders = $this->get_order_report_data(
			array(
				'data' => array(
					'ID' => array(
						'type' => 'post_data',
						'function' => 'COUNT',
						'name' => 'total_orders',
						'distinct' => true,
					),
					'post_modified' => array(
						'type' => 'post_data',
						'function' => '',
						'name' => 'post_modified'
					),
				),
				'group_by' => $this->group_by_query,
				'order_by' => 'post_modified ASC',
				'query_type' => 'get_results',
				'filter_range' => true
			)
		);

		// Get Carts in range

		global $wpdb;
		$sql = "
			SELECT meta.meta_value AS items, posts.post_modified FROM {$wpdb->posts} AS posts

			LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
			LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID=rel.object_ID
			LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
			LEFT JOIN {$wpdb->terms} AS term USING( term_id )

			WHERE 	meta.meta_key 		= 'av8_cartitems'
			AND 	posts.post_type 	= 'carts'
			AND 	posts.post_status 	= 'publish'
			AND 	tax.taxonomy		= 'shop_cart_status'
			AND		term.slug			IN ('open')
			AND		posts.post_modified		> date_sub( NOW(), INTERVAL 1 YEAR )
			ORDER BY posts.post_modified ASC
		";

		$abandoned_carts_items = $wpdb->get_results( $sql );
		$ee_abandoned_posts    = array();

		if ( $abandoned_carts_items ) {

			foreach ( $abandoned_carts_items as $order_item ) {
				$date = $order_item->post_modified;

				//This is a hack to remove any unsupported objects from older versions of WC (2.0 support)
				$items_arr = str_replace(
					array( 'O:17:"WC_Product_Simple"', 'O:10:"WC_Product"' ),
					'O:8:"stdClass"',
					$order_item->items
				);

				$obj                      = new stdClass();
				$obj->post_modified       = $date;
				$obj->abandoned_cart_itms = 1;

				$ee_abandoned_posts[] = $obj;
			}
		}

		// Prepare data for report
		$order_counts            = $this->prepare_chart_data(
			$orders,
			'post_modified',
			'total_orders',
			$this->chart_interval,
			$this->start_date,
			$this->chart_groupby
		);
		$abandoned_carts_encoded = $this->prepare_chart_data(
			$ee_abandoned_posts,
			'post_modified',
			'abandoned_cart_itms',
			$this->chart_interval,
			$this->start_date,
			$this->chart_groupby
		);

		// Encode in json format
		$chart_data = json_encode(
			array(
				'order_counts' => array_values( $order_counts ),
				'abandoned_cart_itms' => array_values( $abandoned_carts_encoded )
			)
		);
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
							data: order_data.abandoned_cart_itms,
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
