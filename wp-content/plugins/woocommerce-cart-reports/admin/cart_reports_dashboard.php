<?php

class AV8_Cart_Dashboard {

	/**
	 *
	 *
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'setup_widgets' ) );
		add_action( 'admin_head', array( $this, 'woocommerce_cart_widget_css' ) );
	}

	/**
	 *
	 * Set up the dashboard widgets with the appropriate callback functions.
	 */

	public function setup_widgets() {
		global $wp_roles;
		global $current_user;

		global $current_month_offset, $the_month_num, $the_year;

		$current_month_offset = 0;

		if ( isset( $_GET['wc_sales_month'] ) ) {
			$current_month_offset = (int) $_GET['wc_sales_month'];
		}

		$the_month_num = date( 'n', strtotime( 'NOW ' . ( $current_month_offset ) . ' MONTH' ) );
		$the_year      = date( 'Y', strtotime( 'NOW ' . ( $current_month_offset ) . ' MONTH' ) );

		$cart_heading = '';

		$tooltip = '';

		if ( $the_month_num != date( 'm' ) ) :
			$cart_heading .= '<a href="index.php?wc_cart_month=' . ( $current_month_offset + 1 ) . '" class="next">' . date_i18n(
					'F',
					strtotime( '01-' . ( $the_month_num + 1 ) . '-2011' )
				) . ' &rarr;</a>';
		endif;

		$cart_heading .= '<a href="index.php?wc_cart_month=' . ( $current_month_offset - 1 ) . '" class="previous">&larr; ' . date_i18n(
				'F',
				strtotime( '01-' . ( $the_month_num - 1 ) . '-2011' )
			) . '</a><span>' . __( 'Monthly Carts', 'woocommerce_cart_reports' ) . $tooltip . '</span>';

		if ( function_exists( 'WC' ) ) {
			if ( current_user_can( 'edit_shop_orders' ) ) {
				wp_add_dashboard_widget(
					'woocommerce_dashboard_carts_right_now',
					__( 'Recent Cart Activity', 'woocommerce_cart_reports' ),
					array( $this, 'woocommerce_recent_cart_activity' )
				);
			}
		} else {
			if ( current_user_can( 'edit_shop_orders' ) ) {
				wp_add_dashboard_widget(
					'woocommerce_dashboard_carts_right_now',
					__( 'Recent Cart Activity', 'woocommerce_cart_reports' ),
					array( $this, 'woocommerce_recent_cart_activity_legacy' )
				);
			}
			if ( current_user_can( 'view_woocommerce_reports' ) && current_user_can( 'edit_shop_orders' ) ) {
				wp_add_dashboard_widget(
					'woocommerce_dashboard_carts',
					$cart_heading,
					array( $this, 'woocommerce_dashboard_carts' )
				);
			}
		}
	}

	/**
	 * Enqueue our dashboard widget css stuff
	 *
	 */
	public function woocommerce_cart_widget_css() {
		global $pagenow;
		if ( is_admin() ) {
			wp_enqueue_style(
				'woocommerce_cart_report_admin_gen_css',
				plugins_url() . '/woocommerce-cart-reports/assets/css/cart_reports_admin_general.css'
			);
			if ( ( $pagenow == 'index.php' ) ) {
				if ( function_exists( 'WC' ) ) {
					wp_enqueue_style(
						'woocommerce_cart_report_admin_dashboard_css',
						plugins_url() . '/woocommerce-cart-reports/assets/css/cart_reports_admin_dashboard_mp6.css'
					);
				} else {
					wp_enqueue_style(
						'woocommerce_cart_report_admin_dashboard_css_legacy',
						plugins_url() . '/woocommerce-cart-reports/assets/css/cart_reports_admin_dashboard.css'
					);
				}
			}
		}
	}

	/**
	 * Widget to display at-a-glance information about cart usage on the site. For pre 2.1
	 *
	 */
	public function woocommerce_recent_cart_activity_legacy() {
		//add_filter( 'posts_where', 'dashboard_stats_where' );

		global $woocommerce;
		global $woocommerce_cart_reports_options;
		$days           = $woocommerce_cart_reports_options['dashboardrange'];
		$nums_right_now = av8_woocommerce_cart_numbers( true );
		$nums_lifetime  = av8_woocommerce_cart_numbers();

		//grab hours from options

		if ( $days > 1 ) {
			$plural = 's';
		} else {
			$plural = '';
		}
		?>
		<div class="table table_carts_right_now">
			<p class="sub woocommerce_sub"><?php _e( 'Right Now', 'woocommerce_cart_reports' ); ?></p>
			<table>
				<tr class="first">

					<?php
					$num  = number_format_i18n( $nums_lifetime['Open'] );
					$text = __( 'Open', 'woocommerce_cart_reports' );
					$link = add_query_arg(
						array( 'post_type' => 'carts', 'mv' => 'Open' ),
						get_admin_url( null, 'edit.php' )
					);
					$num  = '<a href="' . $link . '">' . $num . '</a>';
					$text = '<a href="' . $link . '">' . $text . '</a>';
					?>

					<td class="b b-open"><?php echo $num; ?></td>
					<td class="last t open"><?php echo $text; ?></td>
				</tr>

			</table>
		</div>
		<div class="table table_carts">
			<p class="sub woocommerce_sub">
				<?php
				_e(
					"The Last $days Day" . $plural,
					'woocommerce_cart_reports'
				);
				?>
			</p>
			<table>
				<tr class="first">

					<?php
					$num  = number_format_i18n(
						$nums_right_now['Abandoned']
					); //We're adding these together for the "Not Converted" field.
					$text = __( 'Abandoned', 'woocommerce_cart_reports' );
					$link = add_query_arg(
						array(
							'post_type' => 'carts',
							'mv' => 'Abandoned',
							'start_date' => date( 'Y-m-d', time() - ( 60 * 60 * 24 * $days ) ),
							'end_date' => date( 'Y-m-d', time() )
						),
						get_admin_url( null, 'edit.php' )
					);
					$num  = '<a href="' . $link . '">' . $num . '</a>';
					$text = '<a href="' . $link . '">' . $text . '</a>';
					?>

					<td class="b b-abandoned"><?php echo $num; ?></td>
					<td class="last t abandoned"><?php echo $text; ?></td>
				</tr>
				<tr>
					<?php
					$num  = number_format_i18n( $nums_right_now['Converted'] );
					$text = __( 'Converted', 'woocommerce_cart_reports' );
					$link = add_query_arg(
						array(
							'post_type' => 'carts',
							'mv' => 'Converted',
							'start_date' => date( 'Y-m-d', time() - ( 60 * 60 * 24 * $days ) ),
							'end_date' => date( 'Y-m-d', time() )
						),
						get_admin_url( null, 'edit.php' )
					);
					$num  = '<a href="' . $link . '">' . $num . '</a>';
					$text = '<a href="' . $link . '">' . $text . '</a>';
					?>

					<td class="b b-converted"><?php echo $num; ?></td>
					<td class="last t converted"><?php echo $text; ?></td>
				</tr>
			</table>
		</div>
		<div class="table table_carts_life">
			<p class="sub woocommerce_sub"><?php _e( 'Lifetime', 'woocommerce_cart_reports' ); ?></p>
			<table>
				<tr class="first">

					<?php
					$num  = number_format_i18n(
						$nums_lifetime['Abandoned']
					); //We add open + abandoned to get "Not Converted"
					$text = __( 'Abandonded', 'woocommerce_cart_reports' );
					$link = add_query_arg(
						array( 'post_type' => 'carts', 'mv' => 'Abandoned', 'lifetime' => '' ),
						get_admin_url( null, 'edit.php' )
					);
					$num  = '<a href="' . $link . '">' . $num . '</a>';
					$text = '<a href="' . $link . '">' . $text . '</a>';
					?>

					<td class="b b-abandoned"><?php echo $num; ?></td>
					<td class="last t abandoned"><?php echo $text; ?></td>
				</tr>

				<tr>

					<?php
					$num  = number_format_i18n( $nums_lifetime['Converted'] );
					$text = __( 'Converted', 'woocommerce_cart_reports' );
					$link = add_query_arg(
						array( 'post_type' => 'carts', 'mv' => 'Converted', 'lifetime' => '' ),
						get_admin_url( null, 'edit.php' )
					);
					$num  = '<a href="' . $link . '">' . $num . '</a>';
					$text = '<a href="' . $link . '">' . $text . '</a>';
					?>

					<td class="b b-converted"><?php echo $num; ?></td>
					<td class="last t converted"><?php echo $text; ?></td>
				</tr>
			</table>
		</div>
		<div style="clear:both;"></div>

		<?php
	}


	public function woocommerce_recent_cart_activity() {
		//add_filter( 'posts_where', 'dashboard_stats_where' );

		global $woocommerce;
		global $woocommerce_cart_reports_options;
		$days           = $woocommerce_cart_reports_options['dashboardrange'];
		$nums_right_now = av8_woocommerce_cart_numbers( true );
		$nums_lifetime  = av8_woocommerce_cart_numbers();

		//grab hours from options

		if ( $days > 1 ) {
			$plural = 's';
		} else {
			$plural = '';
		}
		?>

		<ul class="wc_status_list">
			<li class="open-carts">
				<?php
				$num  = number_format_i18n( $nums_lifetime['Open'] );
				$text = __( 'carts currently open', 'woocommerce_cart_reports' );
				$link = add_query_arg(
					array( 'post_type' => 'carts', 'mv' => 'Open' ),
					get_admin_url( null, 'edit.php' )
				);
				?>
				<a href="<?php echo $link; ?>">
					<strong>
						<span><?php echo $num; ?></span>
					</strong>
					<?php echo $text; ?>
				</a>
			</li>

			<li class="abandoned-carts">
				<?php
				$num  = number_format_i18n(
					$nums_right_now['Abandoned']
				); //We're adding these together for the "Not Converted" field.
				$text = __(
					sprintf( 'carts abandoned over the last %s Day%s', $days, $plural ),
					'woocommerce_cart_reports'
				);
				$link = add_query_arg(
					array(
						'post_type' => 'carts',
						'mv' => 'Abandoned',
						'start_date' => date( 'Y-m-d', time() - ( 60 * 60 * 24 * $days ) ),
						'end_date' => date( 'Y-m-d', time() )
					),
					get_admin_url( null, 'edit.php' )
				);
				?>
				<a href="<?php echo $link; ?>">
					<strong>
						<span><?php echo $num; ?></span>
					</strong>
					<?php echo $text; ?>
				</a>
			</li>
		</ul>


		<?php
	}

	/**
	 * Cart widget
	 */
	public function woocommerce_dashboard_carts() {

		add_action( 'admin_footer', array( $this, 'woocommerce_dashboard_carts_js' ) );

		?>
		<div id="placeholder_carts" style="width:100%; height:300px; position:relative;"></div><?php
	}

	/**
	 * Cart widget javascript
	 */
	public function woocommerce_dashboard_carts_js() {

		global $woocommerce, $wp_locale;
		$screen = get_current_screen();
		if ( ! $screen || $screen->id !== 'dashboard' ) {
			return;
		}
		global $current_month_offset, $the_month_num, $the_year;

		// Get orders to display in widget
		add_filter( 'posts_where', 'orders_this_month' );

		$args       = array(
			'numberposts' => - 1,
			'orderby' => 'post_date',
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
		$open_carts = get_posts( $args );

		$args = array(
			'numberposts' => - 1,
			'orderby' => 'post_date',
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

		// Blank date ranges to begin
		$month = $the_month_num;
		$year  = (int) $the_year;

		$first_day = strtotime( "{$year}-{$month}-01" );
		$last_day  = strtotime( '-1 second', strtotime( '+1 month', $first_day ) );

		if ( ( date( 'm' ) - $the_month_num ) == 0 ) :
			$up_to = date( 'd', strtotime( 'NOW' ) );
		else :
			$up_to = date( 'd', $last_day );
		endif;
		$count = 0;

		while ( $count < $up_to ) :
			$time                      = strtotime(
					date( 'Ymd', strtotime( '+ ' . $count . ' DAY', $first_day ) )
				) . '000';
			$converted_counts[ $time ] = 0;
			$updated_counts[ $time ]   = 0;
			$count ++;
		endwhile;

		if ( $converted_carts ) :
			foreach ( $converted_carts as $converted_cart ) :

				$time = strtotime( date( 'Ymd', strtotime( $converted_cart->post_date ) ) ) . '000';

				if ( isset( $converted_counts[ $time ] ) ) :
					$converted_counts[ $time ] ++;
				else :
					$converted_counts[ $time ] = 1;
				endif;

			endforeach;
		endif;

		if ( $open_carts ) :
			foreach ( $open_carts as $open_cart ) :
				$time = strtotime( date( 'Ymd', strtotime( $open_cart->post_date ) ) ) . '000';

				if ( isset( $updated_counts[ $time ] ) ) :
					$updated_counts[ $time ] ++;
				else :
					$updated_counts[ $time ] = 1;
				endif;
			endforeach;
		endif;

		remove_filter( 'posts_where', 'orders_this_month' );

		/* Script variables */
		$params = array(
			'currency_symbol' => get_woocommerce_currency_symbol(),
			'number_of_converted_carts' => __( 'Converted Carts', 'woocommerce_cart_reports' ),
			'number_of_updated_carts' => __( 'Open + Abandoned Carts', 'woocommerce_cart_reports' ),
			'month_names' => array_values( $wp_locale->month_abbrev ),
		);

		$converted_counts_array = array();

		foreach ( $converted_counts as $key => $count ) :
			$converted_counts_array[] = array( $key, $count );
		endforeach;

		$updated_counts_array = array();

		foreach ( $updated_counts as $key => $amount ) :
			$updated_counts_array[] = array( $key, $amount );
		endforeach;

		$order_data = array( 'converted_counts' => $converted_counts_array, 'updated_counts' => $updated_counts_array );

		$params['order_data'] = json_encode( $order_data );

		// Queue scripts
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script(
			'woocommerce-dashboard-carts',
			get_bloginfo(
				'url'
			) . '/wp-content/plugins/woocommerce-cart-reports/assets/js/dashboard_carts' . $suffix . '.js',
			'jquery',
			'1.0'
		);

		wp_register_script(
			'carts-flot-carts-resize',
			get_bloginfo( 'url' ) . '/wp-content/plugins/woocommerce-cart-reports/assets/js/jquery.flot.resize.js',
			'jquery',
			'1.0'
		);
		wp_enqueue_script(
			'flot-carts',
			get_bloginfo( 'url' ) . '/wp-content/plugins/woocommerce-cart-reports/assets/js/jquery.flot.min.js'
		);
		wp_enqueue_script(
			'flot-stack',
			get_bloginfo( 'url' ) . '/wp-content/plugins/woocommerce-cart-reports/assets/js/jquery.flot.stack.min.js'
		);
		wp_enqueue_script(
			'flot-carts-resize',
			get_bloginfo( 'url' ) . '/wp-content/plugins/woocommerce-cart-reports/assets/js/jquery.flot.resize.min.js'
		);

		wp_localize_script( 'woocommerce-dashboard-carts', 'paramscarts', $params );

		wp_print_scripts( 'flot-carts' );
		wp_print_scripts( 'flot-carts-resize' );
		wp_print_scripts( 'woocommerce-dashboard-carts' );
		//We want stacks!!!!

	}

}

?>
