<?php

class AV8_Cart_Reports_Page {

	public function __construct() {
		add_action( 'woocommerce_reports_charts', array( $this, 'cart_manager_tab' ) );
	}

	public function cart_manager_tab( $tabs ) {

		//legacy support for old woocommerce reports

		/* TODO: remove conditional WC added in 2.1 */
		if ( function_exists( 'WC' ) ) {
			$tabs['carts'] = array(
				'title' => __( 'Carts', 'woocommerce' ),
				'reports' => array(
					'carts_by_date' => array(
						'title' => __( 'Carts By Date', 'woocommerce' ),
						'description' => '',
						'hide_title' => true,
						'callback' => 'get_report_cart_reports'
					),
					'carts_by_product' => array(
						'title' => __( 'Carts By Product', 'woocommerce' ),
						'description' => '',
						'hide_title' => true,
						'callback' => 'get_report_cart_reports'
					),
				)
			);
		} else {

			$tabs['carts'] = array(
				'title' => __( 'Carts', 'woocommerce' ),
				'charts' => array(
					array(
						'title' => __( 'Overview', 'woocommerce' ),
						'description' => '',
						'hide_title' => false,
						'function' => 'woocommerce_carts_overview'
					),
					array(
						'title' => __( 'Most Frequently Abandoned Products', 'woocommerce' ),
						'description' => '',
						'hide_title' => false,
						'function' => 'woocommerce_top_abandoned'
					),
					array(
						'title' => __( 'Product Abandonment', 'woocommerce' ),
						'description' => '',
						'hide_title' => false,
						'function' => 'woocommerce_product_abandoned'
					)
				)
			);

		}

		return $tabs;
	}
} //END CLASS


function get_report_cart_reports( $name ) {
	$name  = sanitize_title( str_replace( '_', '-', $name ) );
	$class = 'WC_Report_' . str_replace( '-', '_', $name );
	include_once 'reports/class-wc-report-' . $name . '.php';


	if ( ! class_exists( $class ) ) {
		return;
	}

	$report = new $class();
	$report->output_report();
}


function woocommerce_top_abandoned() {

	global $start_date, $end_date, $woocommerce;

	$start_date = ( isset( $_POST['start_date'] ) ) ? $_POST['start_date'] : '';
	$end_date   = ( isset( $_POST['end_date'] ) ) ? $_POST['end_date'] : '';

	if ( ! $start_date ) {
		$start_date = date( 'Ymd', strtotime( date( 'Ym', current_time( 'timestamp' ) ) . '01' ) );
	}
	if ( ! $end_date ) {
		$end_date = date( 'Ymd', current_time( 'timestamp' ) );
	}

	$start_date = strtotime( $start_date );
	$end_date   = strtotime( $end_date );

	// Get orders to display in widget
	add_filter( 'posts_where', 'carts_abandoned_within_range' );

	$args  = array(
		'numberposts' => - 1,
		'orderby' => 'post_modified',
		'order' => 'ASC',
		'post_type' => 'carts',
		'post_status' => 'publish',
		'suppress_filters' => 0,
		'tax_query' => array(
			array(
				'taxonomy' => 'shop_cart_status',
				'terms' => array( 'open' ),
				'field' => 'slug',
				'operator' => 'IN'
			)
		)
	);
	$carts = get_posts( $args );

	$found_products = array();

	if ( $carts ) :
		foreach ( $carts as $cart ) :
			$cart_items = (array) get_post_meta( $cart->ID, 'av8_cartitems', true );
			foreach ( $cart_items as $cart ) :
				$found_products[ $cart['product_id'] ] = isset( $found_products[ $cart['product_id'] ] ) ? $found_products[ $cart['product_id'] ] + $cart['quantity'] : $cart['quantity'];
			endforeach;
		endforeach;
	endif;

	asort( $found_products );
	$found_products = array_reverse( $found_products, true );
	$found_products = array_slice( $found_products, 0, 25, true );
	reset( $found_products );

	remove_filter( 'posts_where', 'carts_abandoned_within_range' );
	?>
	<form method="post" action="">
		<p><label for="from"><?php _e( 'From:', 'woocommerce_cart_reports' ); ?></label>
			<input type="text" name="start_date" id="from" readonly="readonly" value="
		<?php echo esc_attr( date( 'Y-m-d', $start_date ) ); ?>
		"/> <label for="to"><?php _e( 'To:', 'woocommerce' ); ?></label>
			<input type="text" name="end_date" id="to" readonly="readonly" value="
		<?php echo esc_attr( date( 'Y-m-d', $end_date ) ); ?>
		"/> <input type="submit" class="button" value="<?php _e( 'Show', 'woocommerce' ); ?>"/></p>
	</form>
	<table class="bar_chart">
		<thead>
		<tr>
			<th><?php _e( 'Product', 'woocommerce_cart_reports' ); ?></th>
			<th><?php _e( 'Carts Abandoned', 'woocommerce_cart_reports' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		$max_sales = current( $found_products );
		foreach ( $found_products as $product_id => $sales ) :
			$width = ( $sales > 0 ) ? ( $sales / $max_sales ) * 100 : 0;

			$product = get_post( $product_id );
			if ( $product ) :
				$product_name = '<a href="' . get_permalink( $product->ID ) . '">' . $product->post_title . '</a>';
				$orders_link  = admin_url(
					'edit.php?s&post_status=all&post_type=carts&action=-1&s=' . urlencode(
						$product->post_title
					) . '&start_date=' . date( 'Y-m-d', $start_date ) . '&end_date=' . date(
						'Y-m-d',
						$end_date
					) . '&mv=Abandoned'
				);
			else :
				$product_name = __( 'Product does not exist', 'woocommerce_cart_reports' );
				$orders_link  = admin_url(
					'edit.php?s&post_status=all&post_type=carts&action=-1&start_date=' . date(
						'Y-m-d',
						$start_date
					) . '&end_date=' . date( 'Y-m-d', $end_date ) . '&mv=Abandoned'
				);
			endif;

			echo '<tr><th>' . $product_name . '</th><td width="1%"><span>' . $sales . '</span></td><td class="bars"><a href="' . $orders_link . '" style="width:' . $width . '%">&nbsp;</a></td></tr>';
		endforeach;
		?>
		</tbody>
	</table>
	<?php
}

function woocommerce_product_abandoned() {

	global $wpdb, $woocommerce;

	$chosen_product_ids = ( isset( $_POST['product_ids'] ) ) ? (array) $_POST['product_ids'] : '';
	if ( $chosen_product_ids && is_array( $chosen_product_ids ) ) {

		$start_date = date( 'Ym', strtotime( '-12 MONTHS', current_time( 'timestamp' ) ) ) . '01';
		$end_date   = date( 'Ymd', current_time( 'timestamp' ) );

		$max_sales     = $max_totals = 0;
		$product_sales = $product_totals = array();

		// Get titles and ID's related to product
		$chosen_product_titles = array();
		$children_ids          = array();

		foreach ( $chosen_product_ids as $product_id ) {
			$children                = (array) get_posts(
				'post_parent=' . $product_id . '&fields=ids&post_status=any&numberposts=-1'
			);
			$children_ids            = $children_ids + $children;
			$chosen_product_titles[] = get_the_title( $product_id );
		}

		// Get order items

		$sql            = "
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

		if ( $order_items ) {

			foreach ( $order_items as $order_item ) {

				$date = date( 'Ym', strtotime( $order_item->post_modified ) );

				//This is a hack to remove any unsupported objects from older versions of WC (2.0 support)
				$items_arr = str_replace(
					array( 'O:17:"WC_Product_Simple"', 'O:10:"WC_Product"' ),
					'O:8:"stdClass"',
					$order_item->items
				);

				$items = maybe_unserialize( $items_arr );
				foreach ( $items as $item ) {
					if ( isset( $item['line_total'] ) ) {
						$row_cost = $item['line_total'];
					}

					$product_sales[ $date ] = isset( $product_sales[ $date ] ) ? $product_sales[ $date ] + $item['quantity'] : $item['quantity'];

					if ( $product_sales[ $date ] > $max_sales ) {
						$max_sales = $product_sales[ $date ];
					}
				}

			}

		}

		?>

		<h4>
			<?php
			printf(
				__( 'Abandoned Carts containing %s:', 'woocommerce_cart_reports' ),
				implode( ', ', $chosen_product_titles )
			);
			?>
		</h4>
		<table class="bar_chart">
			<thead>
			<tr>
				<th><?php _e( 'Month', 'woocommerce_cart_reports' ); ?></th>
				<th colspan="2"><?php _e( 'Carts', 'woocommerce_cart_reports' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php
			if ( count( $product_sales ) > 0 ) {
				foreach ( $product_sales as $date => $sales ) :
					$width = ( $sales > 0 ) ? ( round( $sales ) / round( $max_sales ) ) * 100 : 0;

					$orders_link = admin_url(
						'edit.php?s&post_status=all&post_type=carts&action=-1&s=' . urlencode(
							implode( ' ', $chosen_product_titles )
						) . '&end_date=' . date(
							'Y-m-d',
							strtotime( date( 'Y' ) . '-' . ( date( 'm' ) + 1 ) . '-' . '01' ) - ( 60 * 60 * 24 )
						) . '&start_date=' . date( 'Y-m-d', strtotime( $date . '01' ) ) . '&mv=Abandoned'
					);

					echo '<tr><th><a href="' . $orders_link . '">' . date_i18n( 'F', strtotime( $date . '01' ) ) . '</a></th>
						<td width="1%"><span>' . $sales . '</span></td>
						<td class="bars">
							<span style="width:' . $width . '%">&nbsp;</span>
						</td></tr>';
				endforeach;
			} else {
				echo '<tr><td colspan="3">' . __( 'No Carts :)', 'woocommerce_cart_reports' ) . '</td></tr>';
			}
			?>
			</tbody>
		</table>
		<?php

	} else {
		?>
		<form method="post" action="">
			<p>
				<select id="product_ids" name="product_ids[]" class="ajax_chosen_select_products" multiple="multiple" data-placeholder="
		<?php _e( 'Search for a product&hellip;', 'woocommerce' ); ?>
		" style="width: 400px;"></select>
				<input type="submit" style="vertical-align: top;" class="button" value="
		<?php _e( 'Show', 'woocommerce' ); ?>
		"/></p>
			<script type="text/javascript">
				jQuery( function () {
					// Ajax Chosen Product Selectors
					jQuery( 'select.ajax_chosen_select_products' ).ajaxChosen( {
						method: 'GET',
						url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
						dataType: 'json',
						afterTypeDelay: 100,
						data: {
							action: 'woocommerce_json_search_products',
							security: '<?php echo wp_create_nonce( 'search-products' ); ?>'
						}
					}, function ( data ) {

						var terms = {}

						jQuery.each( data, function ( i, val ) {
							terms[i] = val
						} )

						return terms
					} )
				} )
			</script>
		</form>
		<?php
	}
}

/**
 *
 *
 */
function woocommerce_carts_overview() {
	global $start_date, $end_date, $woocommerce, $wpdb, $wp_locale;
	$current_month = date( 'j/n/Y', mktime( 0, 0, 0, 1, date( 'm' ), date( 'Y' ) ) );

	$start_date = ( isset( $_POST['start_date'] ) ) ? $_POST['start_date'] : '';
	$end_date   = ( isset( $_POST['end_date'] ) ) ? $_POST['end_date'] : '';

	if ( ! $start_date ) {
		$start_date = $current_month;
	}
	if ( ! $end_date ) {
		$end_date = strtotime( date( 'Ymd', current_time( 'timestamp' ) ) );
	}

	$start_date = strtotime( $start_date );
	$end_date   = strtotime( $end_date );

	//$start_date = strtotime(date('Ymd', strtotime( date('Ym', current_time('timestamp')).'01' )));
	$end_date = strtotime( date( 'Ymd', current_time( 'timestamp' ) ) );
	// Get orders to display in widget

	$args = array(
		'numberposts' => - 1,
		'orderby' => 'post_modified',
		'order' => 'DESC',
		'post_type' => 'carts',
		'post_status' => 'publish',
		'suppress_filters' => false,
		'tax_query' => array(
			array(
				'taxonomy' => 'shop_cart_status',
				'terms' => apply_filters( 'woocommerce_reports_cart_statuses', array( 'open' ) ),
				'field' => 'slug',
				'operator' => 'IN'
			)
		)
	);
	add_filter( 'posts_where', 'carts_abandoned_within_range' );
	$open_carts = get_posts( $args );

	$args            = array(
		'numberposts' => - 1,
		'orderby' => 'post_modified',
		'order' => 'DESC',
		'post_type' => 'carts',
		'post_status' => 'publish',
		'suppress_filters' => false,
		'tax_query' => array(
			array(
				'taxonomy' => 'shop_cart_status',
				'terms' => apply_filters( 'woocommerce_reports_cart_statuses', array( 'converted' ) ),
				'field' => 'slug',
				'operator' => 'IN'
			)
		)
	);
	$converted_carts = get_posts( $args );

	$converted_counts = array();
	$updated_counts   = array();
	$total_converted  = 0;
	$total_updated    = 0;
	// Blank date ranges to begin
	$count = 0;
	$days  = ( $end_date - $start_date ) / ( 60 * 60 * 24 );
	if ( $days == 0 ) {
		$days = 1;
	}

	while ( $count < $days + 1 ) :
		$time                      = strtotime(
				date( 'Ymd', strtotime( '+ ' . $count . ' DAY', $start_date ) )
			) . '000';
		$converted_counts[ $time ] = 0;
		$updated_counts[ $time ]   = 0;
		$count ++;
	endwhile;

	if ( $converted_carts ) :
		foreach ( $converted_carts as $converted_cart ) :

			$time = strtotime( date( 'Ymd', strtotime( $converted_cart->post_modified ) ) ) . '000';

			if ( isset( $converted_counts[ $time ] ) ) :
				$converted_counts[ $time ] ++;
				$total_converted ++;
			else :
				$converted_counts[ $time ] = 1;
			endif;

		endforeach;
	endif;


	if ( $open_carts ) :
		foreach ( $open_carts as $open_cart ) :

			$time = strtotime( date( 'Ymd', strtotime( $open_cart->post_modified ) ) ) . '000';
			if ( isset( $updated_counts[ $time ] ) ) :
				$updated_counts[ $time ] ++;
				$total_updated ++;
			else :
				$updated_counts[ $time ] = 1;
			endif;

		endforeach;

	endif;

	remove_filter( 'posts_where', 'carts_abandoned_within_range' );

	/* Script variables */

	$params = array(
		'currency_symbol' => get_woocommerce_currency_symbol(),
		'number_of_converted_carts' => __( 'Converted Carts', 'woocommerce' ),
		'number_of_updated_carts' => __( 'dsaOpen and Abandoned Carts', 'woocommerce' ),
	);

	$converted_counts_array = array();
	foreach ( $converted_counts as $key => $count ) :
		$converted_counts_array[] = array( $key, $count );
	endforeach;

	$updated_counts_array = array();
	foreach ( $updated_counts as $key => $amount ) :
		$updated_counts_array[] = array( $key, $amount );
	endforeach;

	$cart_data = array( 'converted_counts' => $converted_counts_array, 'updated_counts' => $updated_counts_array );

	$cart_data_json = json_encode( $cart_data );
	?>

	<form method="post" action="">
		<p><label for="from"><?php _e( 'From:', 'woocommerce' ); ?></label>
			<input type="text" name="start_date" id="from" readonly="readonly" value="
		<?php echo esc_attr( date( 'Y-m-d', $start_date ) ); ?>
		"/> <label for="to"><?php _e( 'To:', 'woocommerce' ); ?></label>
			<input type="text" name="end_date" id="to" readonly="readonly" value="
		<?php echo esc_attr( date( 'Y-m-d', $end_date ) ); ?>
		"/> <input type="submit" class="button" value="<?php _e( 'Show', 'woocommerce' ); ?>"/></p>
	</form>
	<div id="poststuff" class="woocommerce-reports-wrap">
		<div class="woocommerce-reports-sidebar">
			<div class="postbox">
				<?php
				$link        = add_query_arg(
					array(
						'post_type' => 'carts',
						'mv' => 'OandA',
						'start_date' => date( 'Y-m-d', time() - ( 60 * 60 * 24 * $days ) ),
						'end_date' => date( 'Y-m-d', time() )
					),
					get_admin_url( null, 'edit.php' )
				);
				$num_updated = '<a style = "color:#333;" href="' . $link . '">' . $total_updated . '</a>';
				?>
				<h3><span><?php _e( 'Open & Abandoned Carts In Range', 'woocommerce' ); ?></span></h3>
				<div class="inside">
					<p class="stat">
						<?php
						if ( $total_updated != '' ) {
							echo $num_updated . ' Carts';
						} else {
							_e( 'n/a', 'woocommerce' );
						}
						?>
					</p>
				</div>
			</div>
			<div class="postbox">
				<?php
				$link          = add_query_arg(
					array(
						'post_type' => 'carts',
						'mv' => 'Converted',
						'start_date' => date( 'Y-m-d', time() - ( 60 * 60 * 24 * $days ) ),
						'end_date' => date( 'Y-m-d', time() )
					),
					get_admin_url( null, 'edit.php' )
				);
				$num_converted = '<a style = "color:#333;"href="' . $link . '">' . $total_converted . '</a>';
				?>
				<h3><span><?php _e( 'Converted Carts In Range', 'woocommerce' ); ?></span></h3>
				<div class="inside">
					<p class="stat">
						<?php
						count( $total_converted );
						if ( $total_converted != '' ) {
							echo $num_converted . ' Carts';
						} else {
							_e( 'n/a', 'woocommerce' );
						}
						?>
					</p>
				</div>
			</div>

		</div>
		<div class="woocommerce-reports-main">
			<div class="postbox">
				<h3><span><?php _e( 'Open & Abandoned Carts vs. Converted Carts', 'woocommerce' ); ?></span></h3>
				<div class="inside chart">
					<div id="placeholder" style="width:100%; overflow:hidden; height:568px; position:relative;"></div>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		jQuery( function () {
			var cart_data = jQuery.parseJSON( '<?php echo $cart_data_json; ?>' )

			var d = cart_data.converted_counts
			var d2 = cart_data.updated_counts

			for ( var i = 0; i < d.length; ++ i ) {
				d[i][0] += 60 * 60 * 1000
			}
			for ( var i = 0; i < d2.length; ++ i ) {
				d2[i][0] += 60 * 60 * 1000
			}

			var placeholder = jQuery( '#placeholder' )

			var plot = jQuery.plot( placeholder, [
				{ label: "<?php echo esc_js( __( 'Converted Carts', 'woocommerce' ) ); ?>", data: d }, {
					label: "<?php echo esc_js( __( 'Open & Abandoned Carts', 'woocommerce' ) ); ?>", data: d2, yaxis: 1
				}
			], {
				series: {
					stack: true, lines: {
						fill: true, show: !0
					}, points: {
						show: 0
					}
				}, grid: {
					show: true,
					aboveData: false,
					color: '#ccc',
					backgroundColor: '#fff',
					borderWidth: 2,
					borderColor: '#ccc',
					clickable: false,
					hoverable: true,
					markings: weekendAreas
				}, xaxis: {
					mode: 'time', timeformat: '%d %b', monthNames:
					<?php
					echo json_encode(
						array_values( $wp_locale->month_abbrev )
					);
					?>
					, tickLength: 1, minTickSize: [1, 'day']
				}, yaxes: [
					{
						position: 'right', min: 0, tickSize: 10, tickDecimals: 0
					}, {
						position: 'right', min: 0, tickDecimals: 2
					}
				],

				colors: ['green', 'red']
			} )

			placeholder.resize()

			<?php woocommerce_tooltip_js_carts(); ?>
		} )
	</script>
	<?php

	wp_enqueue_script(
		'carts-flot-carts-resize',
		get_bloginfo( 'url' ) . '/wp-content/plugins/woocommerce-cart-reports/assets/js/jquery.flot.resize.js'
	);
	wp_enqueue_script(
		'flot-stack',
		get_bloginfo( 'url' ) . '/wp-content/plugins/woocommerce-cart-reports/assets/js/jquery.flot.stack.min.js'
	);
}

/**
 *
 *
 */
function woocommerce_tooltip_js_carts() {
	?>
	function showTooltip(x, y, contents) {
	jQuery('
	<div id="tooltip">' + contents + '</div>').css( {
	position: 'absolute',
	display: 'none',
	top: y + 5,
	left: x - 50,
	padding: '5px 10px',
	border: '3px solid #3da5d5',
	background: '#288ab7'
	}).appendTo("body").fadeIn(200);
	}

	var previousPoint = null;
	jQuery("#placeholder").bind("plothover", function (event, pos, item) {
	if (item) {
	if (previousPoint != item.dataIndex) {
	previousPoint = item.dataIndex;

	jQuery("#tooltip").remove();

	if (item.series.label=="<?php echo esc_js( __( 'Converted Carts', 'woocommerce' ) ); ?>") {

	var y = item.datapoint[1].toFixed(2);
	showTooltip(item.pageX, item.pageY, item.series.label + " - " + Math.round(y));

	} else if (item.series.label=="<?php echo esc_js( __( 'Open & Abandoned Carts', 'woocommerce' ) ); ?>") {

	var y = item.datapoint[1];
	showTooltip(item.pageX, item.pageY, item.series.label + " - " + Math.round(y));

	} else {

	var y = item.datapoint[1];
	showTooltip(item.pageX, item.pageY, y);
	}
	}
	}
	else {
	jQuery("#tooltip").remove();
	previousPoint = null;
	}
	});
	<?php
}

function carts_abandoned_within_range( $where = '' ) {
	global $start_date, $end_date, $woocommerce_cart_reports_options, $offset;
	$timeout = $woocommerce_cart_reports_options['timeout'];

	$current_date = date( 'Y-m-d' );

	if ( $end_date == strtotime( $current_date ) ) {
		$end_date = time() + ( $offset * 3600 );
		$before   = date( 'Y-m-d G:i:s', $end_date - $timeout );
	} else {
		$before = date( 'Y-m-d', strtotime( '+1 day', $end_date ) );
	}
	$timeout = $woocommerce_cart_reports_options['timeout'];
	$after   = date( 'Y-m-d', (int) $start_date );
	$where  .= " AND post_modified > '$after'";
	$where  .= " AND post_modified < '$before'";

	return sprintf( ' AND post_modified > %s AND post_modified < %s', $after, $before );
}


?>
