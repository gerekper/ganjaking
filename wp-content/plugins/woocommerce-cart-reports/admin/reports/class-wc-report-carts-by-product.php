<?php

/**
 * WC_Report_Carts_By_Product class
 */
class WC_Report_Carts_By_Product extends WC_Admin_Report {

	public $product_ids = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		if ( isset( $_GET['product_ids'] ) && is_array( $_GET['product_ids'] ) ) {
			$this->product_ids = array_map( 'absint', $_GET['product_ids'] );
		} elseif ( isset( $_GET['product_ids'] ) ) {
			$this->product_ids = array( absint( $_GET['product_ids'] ) );
		}
	}

	/**
	 * Get the legend for the main chart sidebar
	 *
	 * @return array
	 */
	public function get_chart_legend() {
		if ( ! $this->product_ids ) {
			return array();
		}

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

		$order_items    = $wpdb->get_results( $sql );
		$found_products = array();

		$product_objs = array();

		if ( $order_items ) {

			foreach ( $order_items as $order_item ) {

				$date = $order_item->post_modified;

				// This is a hack to remove any unsupported objects from older versions of WC (2.0 support).
				$items_arr = str_replace(
					array( 'O:17:"WC_Product_Simple"', 'O:10:"WC_Product"' ),
					'O:8:"stdClass"',
					$order_item->items
				);

				$items = maybe_unserialize( $items_arr );
				foreach ( $items as $item ) {

					if ( $this->product_ids[0] !== $item['product_id'] ) {
						continue;
					}

					if ( isset( $item['line_total'] ) ) {
						$row_cost = $item['line_total'];
					}

					$obj = new stdClass();

					$obj->post_modified    = $date;
					$obj->order_item_count = $item['quantity'];
					$obj->product_id       = $item['product_id'];

					$product_objs[] = $obj;

				}
			}
		}

		// Prepare data for report.
		$order_item_counts = $this->prepare_chart_data(
			$product_objs,
			'post_modified',
			'order_item_count',
			$this->chart_interval,
			$this->start_date,
			$this->chart_groupby
		);

		$count = 0;
		foreach ( $order_item_counts as $order_counter => $val ) :
			$count += $val[1];
		endforeach;

		$legend = array();

		$total_items = $count;
		$legend[]    = array(
			'title'            => sprintf(
				__( '%s Product Abandonments', 'woocommerce_cart_reports' ),
				'<strong>' . $total_items . '</strong>'
			),
			'color'            => $this->chart_colours['item_counts'],
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
			'year'       => __( 'Year', 'woocommerce_cart_reports' ),
			'last_month' => __( 'Last Month', 'woocommerce_cart_reports' ),
			'month'      => __( 'This Month', 'woocommerce_cart_reports' ),
			'7day'       => __( 'Last 7 Days', 'woocommerce_cart_reports' ),
		);

		$this->chart_colours = array(
			'item_counts' => '#d54e21',
		);

		$current_range = ! empty( $_GET['range'] ) ? $_GET['range'] : '7day';

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
			$current_range = '7day';
		}

		$this->calculate_current_range( $current_range );

		include WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php';
	}

	/**
	 * [get_chart_widgets description]
	 *
	 * @return array
	 */
	public function get_chart_widgets() {

		$widgets = array();

		if ( ! empty( $this->product_ids ) ) {
			$widgets[] = array(
				'title'    => __( 'Showing reports for:', 'woocommerce_cart_reports' ),
				'callback' => array( $this, 'current_filters' ),
			);
		}

		$widgets[] = array(
			'title'    => '',
			'callback' => array( $this, 'products_widget' ),
		);

		return $widgets;
	}

	/**
	 * Show current filters
	 *
	 * @return void
	 */
	public function current_filters() {
		$this->product_ids_titles = array();

		foreach ( $this->product_ids as $product_id ) {
			$product                    = wc_get_product( $product_id );
			$this->product_ids_titles[] = $product->get_formatted_name();
		}

		echo '<p>' . ' <strong>' . implode( ', ', $this->product_ids_titles ) . '</strong></p>';
		echo '<p><a class="button" href="' . esc_url( remove_query_arg( 'product_ids' ) ) . '">' . __(
			'Reset',
			'woocommerce_cart_reports'
		) . '</a></p>';
	}

	/**
	 * Product selection
	 *
	 * @return void
	 */
	public function products_widget() {
		$range      = ! empty( $_GET['range'] ) ? esc_attr( $_GET['range'] ) : '';
		$start_date = ! empty( $_GET['start_date'] ) ? esc_attr( $_GET['start_date'] ) : '';
		$end_date   = ! empty( $_GET['end_date'] ) ? esc_attr( $_GET['end_date'] ) : '';
		$page       = ! empty( $_GET['page'] ) ? esc_attr( $_GET['page'] ) : '';
		$tab        = ! empty( $_GET['tab'] ) ? esc_attr( $_GET['tab'] ) : '';
		$report     = ! empty( $_GET['report'] ) ? esc_attr( $_GET['report'] ) : '';
		?>
		<h4 class="section_title"><span><?php esc_attr_e( 'Product Search', 'woocommerce_cart_reports' ); ?></span></h4>
		<div class="section">
			<form method="GET">
				<div>
					<select class="wc-product-search" style="width:203px;" multiple="multiple" id="product_ids" name="product_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations"></select>
					<input type="submit" class="submit button" value="<?php esc_attr_e( 'Show', 'woocommerce' ); ?>"/>
					<input type="hidden" name="range" value="<?php echo $range; ?>"/>
					<input type="hidden" name="start_date" value="<?php echo $start_date; ?>"/>
					<input type="hidden" name="end_date" value="<?php echo $end_date; ?>"/>
					<input type="hidden" name="page" value="<?php echo $page; ?>"/>
					<input type="hidden" name="tab" value="<?php echo $tab; ?>"/>
					<input type="hidden" name="report" value="<?php echo $report; ?>"/>
					<?php wp_nonce_field( 'custom_range', 'wc_reports_nonce', false ); ?>
				</div>
			</form>
		</div>


		<script type="text/javascript">
			// Ajax product search box
			// Copied from
			// https://github.com/woocommerce/woocommerce/blob/339ab41bf1abd4b3af83cc5a4eb9a280298c0e09/includes/admin/reports/class-wc-report-sales-by-product.php#L200
			jQuery( function ( $ ) {

				$( ':input.wc-product-search' ).filter( ':not(.enhanced)' ).each( function () {
					var select2_args = {
						allowClear: $( this ).data( 'allow_clear' ) ? true : false,
						placeholder: $( this ).data( 'placeholder' ),
						minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data(
							'minimum_input_length' ) : '3',
						escapeMarkup: function ( m ) {
							return m;
						},
						ajax: {
							url: wc_enhanced_select_params.ajax_url,
							dataType: 'json',
							delay: 250,
							data: function ( params ) {
								return {
									term: params.term,
									action: $( this ).data( 'action' ) || 'woocommerce_json_search_products_and_variations',
									security: wc_enhanced_select_params.search_products_nonce,
									exclude: $( this ).data( 'exclude' ),
									include: $( this ).data( 'include' ),
									limit: $( this ).data( 'limit' )
								};
							},
							processResults: function ( data ) {
								var terms = [];
								if ( data ) {
									$.each( data, function ( id, text ) {
										terms.push( { id: id, text: text } );
									} );
								}
								return {
									results: terms
								};
							},
							cache: true
						}
					};

					select2_args = $.extend( select2_args, getEnhancedSelectFormatString() );

					$( this ).select2( select2_args ).addClass( 'enhanced' );

					if ( $( this ).data( 'sortable' ) ) {
						var $select = $( this );
						var $list = $( this ).next( '.select2-container' ).find( 'ul.select2-selection__rendered' );

						$list.sortable( {
							placeholder: 'ui-state-highlight select2-selection__choice',
							forcePlaceholderSize: true,
							items: 'li:not(.select2-search__field)',
							tolerance: 'pointer',
							stop: function () {
								$( $list.find( '.select2-selection__choice' ).get().reverse() ).each( function () {
									var id = $( this ).data( 'data' ).id;
									var option = $select.find( 'option[value="' + id + '"]' )[0];
									$select.prepend( option );
								} );
							}
						} );
					}
				} );
			} );

			jQuery( '.section_title' ).click( function () {
				var next_section = jQuery( this ).next( '.section' );

				if ( jQuery( next_section ).is( ':visible' ) ) return false;

				jQuery( '.section:visible' ).slideUp();
				jQuery( '.section_title' ).removeClass( 'open' );
				jQuery( this ).addClass( 'open' ).next( '.section' ).slideDown();

				return false;
			} );
			jQuery( '.section' ).slideUp( 100, function () {
				<?php if ( empty( $this->product_ids ) ) : ?>
				jQuery( '.section_title:eq(1)' ).click();
				<?php endif; ?>
			} );
		</script>
		<?php
	}

	/**
	 * Output an export link
	 */
	public function get_export_button() {
		$current_range = ! empty( $_GET['range'] ) ? $_GET['range'] : '7day';
		?>
		<a
			href="#"
			download="report-<?php echo $current_range; ?>-
										<?php
										echo date_i18n(
											'Y-m-d',
											current_time( 'timestamp' )
										);
										?>
			.csv"
			class="export_csv"
			data-export="chart"
			data-xaxes="<?php _e( 'Date', 'woocommerce_cart_reports' ); ?>"
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

		if ( ! $this->product_ids ) {
			?>
			<div class="chart-container">
				<p class="chart-prompt">
				<?php
				_e(
					'&larr; Choose a product to view stats',
					'woocommerce_cart_reports'
				);
				?>
					</p>
			</div>
			<?php
		} else {
			// Get orders and dates in range - we want the SUM of order totals, COUNT of order items, COUNT of orders, and the date

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

			$order_items    = $wpdb->get_results( $sql );
			$found_products = array();

			$product_objs = array();

			if ( $order_items ) {

				foreach ( $order_items as $order_item ) {

					$date = $order_item->post_modified;

					// This is a hack to remove any unsupported objects from older versions of WC (2.0 support)
					$items_arr = str_replace(
						array( 'O:17:"WC_Product_Simple"', 'O:10:"WC_Product"' ),
						'O:8:"stdClass"',
						$order_item->items
					);

					$items = maybe_unserialize( $items_arr );
					foreach ( $items as $item ) {

						if ( $this->product_ids[0] != $item['product_id'] ) {
							continue;
						}
						if ( isset( $item['line_total'] ) ) {
							$row_cost = $item['line_total'];
						}
						$obj = new stdClass();

						$obj->post_modified    = $date;
						$obj->order_item_count = $item['quantity'];
						$obj->product_id       = $item['product_id'];

						$product_objs[] = $obj;

					}
				}
			}

			// Prepare data for report.
			$order_item_counts = $this->prepare_chart_data(
				$product_objs,
				'post_modified',
				'order_item_count',
				$this->chart_interval,
				$this->start_date,
				$this->chart_groupby
			);

			// Encode in json format.
			$chart_data = wp_json_encode(
				array(
					'order_item_counts' => array_values( $order_item_counts ),
				)
			);

			$time_format = 'day' === $this->chart_groupby ? '%d %b' : '%b';
			$label       = __( 'Number of items Abandoned', 'woocommerce_cart_reports' );
			?>
			<div class="chart-container">
				<div class="chart-placeholder main"></div>
			</div>
			<script type="text/javascript">
				var main_chart;

				jQuery( function () {
					var order_data = jQuery.parseJSON( '<?php echo $chart_data; ?>' );

					var drawGraph = function ( highlight ) {

						var series = [
							{
								label: "<?php echo esc_js( $label ); ?>",
								data: order_data.order_item_counts,
								color: '<?php echo $this->chart_colours['item_counts']; ?>',
								points: { show: true, radius: 5, lineWidth: 3, fillColor: '#fff', fill: true },
								lines: { show: true, lineWidth: 4, fill: false },
								shadowSize: 0,
								hoverable: true
							}
						];

						if ( highlight !== 'undefined' && series[highlight] ) {
							highlight_series = series[highlight];

							highlight_series.color = '#9c5d90';

							if ( highlight_series.bars ) highlight_series.bars.fillColor = '#9c5d90';

							if ( highlight_series.lines ) {
								highlight_series.lines.lineWidth = 5;
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
									position: "bottom",
									tickColor: 'transparent',
									mode: "time",
									timeformat: "<?php echo esc_js( $time_format ); ?>",
									monthNames: <?php echo wp_json_encode( array_values( $wp_locale->month_abbrev ) ); ?>,
									tickLength: 1,
									minTickSize: [1, "<?php echo $this->chart_groupby; ?>"],
									font: {
										color: "#aaa"
									}
								}
							], yaxes: [
								{
									min: 0, minTickSize: 1, tickDecimals: 0, color: '#ecf0f1', font: { color: "#aaa" }
								}, {
									position: "right",
									min: 0,
									tickDecimals: 2,
									alignTicksWithAxis: 1,
									color: 'transparent',
									font: { color: "#aaa" }
								}
							],
						} );

						jQuery( '.chart-placeholder' ).resize();
					}

					drawGraph();

					jQuery( '.highlight_series' ).hover( function () {
						drawGraph( jQuery( this ).data( 'series' ) );
					}, function () {
						drawGraph();
					} );
				} );
			</script>
			<?php
		}
	}
}
