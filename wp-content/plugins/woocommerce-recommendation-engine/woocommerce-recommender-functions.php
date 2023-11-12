<?php

/**
 * Future expansion point to record the geo location of session activity.
 */
function woocommerce_recommender_record_session_location() {
	//record the geo located information about the current browsing session
}

/**
 * Records views of a product. Wrapper for woocommerce_recommender_record_product.
 *
 * @param int $product_id
 * @param int $user_id
 */
function woocommerce_recommender_record_product_view( $product_id, $user_id = 0 ) {

	$session_id    = WC_Recommender_Compatibility::WC()->session->get_customer_id();
	$activity_date = date( 'Y-m-d H:i:s' );
	$activity_type = 'viewed';
	$user_id       = ! $user_id && is_user_logged_in() ? get_current_user_id() : 0;

	woocommerce_recommender_record_product( $product_id, $session_id, $user_id, 0, $activity_type, $activity_date );
}

/**
 * Records when a product is added to the cart. Wrapper for woocommerce_recommender_record_product.
 *
 * @param int $product_id
 */
function woocommerce_recommender_record_product_in_cart( int $product_id ) {
	$session_id = WC_Recommender_Compatibility::WC()->session->get_customer_id();

	$activity_date = date( 'Y-m-d H:i:s' );
	$activity_type = 'in-cart';
	$user_id       = is_user_logged_in() ? get_current_user_id() : 0;
	woocommerce_recommender_record_product( $product_id, $session_id, $user_id, 0, $activity_type, $activity_date );
}

/**
 * Records when a product is ordered.  Wrapper for woocommerce_recommender_record_product.
 *
 * @param int $order_id
 * @param int $product_id
 * @param string $activity_type
 */
function woocommerce_recommender_record_product_ordered( $order_id, $product_id, $activity_type = 'ordered' ) {
	$session_id    = WC_Recommender_Compatibility::WC()->session->get_customer_id();
	$activity_date = date( 'Y-m-d H:i:s' );
	$user_id       = is_user_logged_in() ? get_current_user_id() : 0;
	woocommerce_recommender_record_product( $product_id, $session_id, $user_id, $order_id, $activity_type, $activity_date );
}

/**
 * Generic version of the record functions.  This function writes the records to the databsae.
 *
 * @param int $product_id
 * @param string $session_id
 * @param int $user_id
 * @param int $order_id
 * @param string $activity_type
 * @param string $activity_date MYSQL date formatted string.
 *
 * @return bool
 * @global WC_Recommendation_Engine $woocommerce_recommender
 *
 * @global wpdb $wpdb
 */
function woocommerce_recommender_record_product( $product_id, $session_id, $user_id, $order_id, $activity_type, $activity_date ) {
	global $wpdb, $woocommerce_recommender;

	if ( apply_filters( 'wc_recommender_record_product', true, $product_id, $session_id, $user_id, $order_id, $activity_type ) ) {

		$data = [
			'session_id'    => $session_id,
			'activity_type' => $activity_type,
			'product_id'    => $product_id,
			'user_id'       => $user_id,
			'order_id'      => $order_id,
			'activity_date' => $activity_date
		];

		$format = [ '%s', '%s', '%d', '%d', '%d', '%s' ];
		$result = $wpdb->insert( $woocommerce_recommender->db_tbl_session_activity, $data, $format );

		return $result;
	} else {
		return 0;
	}
}

/**
 * Updates the activity type of previously recorded session history item.
 *
 * @param int $order_id
 * @param int $product_id
 * @param string $activity_type
 *
 * @global WC_Recommendation_Engine $woocommerce_recommender
 *
 * @global wpdb $wpdb
 */
function woocommerce_recommender_update_recorded_product( int $order_id, int $product_id, string $activity_type ) {
	global $wpdb, $woocommerce_recommender;
	$activity_date = date( 'Y-m-d H:i:s' );

	$data = [
		'activity_type' => $activity_type,
		'activity_date' => $activity_date
	];

	$format = [ '%s', '%s' ];

	$where_data = [
		'product_id' => $product_id,
		'order_id'   => $order_id,
	];

	$where_format = [ '%d', '%d' ];
	$wpdb->update( $woocommerce_recommender->db_tbl_session_activity, $data, $where_data, $format, $where_format );
}

function woocommerce_recommender_get_simularity(
	$current_product_id, $activity_types = [
	'completed',
	'in-cart',
	'viewed'
]
) {
	global $wpdb, $woocommerce_recommender;

	if ( ! is_array( $activity_types ) ) {
		$activity_types = (array) $activity_types;
	}

	$key = 'wc_recommender_' . implode( '_', $activity_types ) . '_' . $current_product_id;

	$recommendations_sql = $wpdb->prepare( "SELECT related_product_id, score
				FROM $woocommerce_recommender->db_tbl_recommendations
				WHERE product_id = %d AND rkey = %s", $current_product_id, $key );

	$db_scores = $wpdb->get_results( $recommendations_sql );

	$scores = [];
	if ( is_array( $db_scores ) && ! is_wp_error( $db_scores ) ) {

		foreach ( $db_scores as $db_score ) {
			$product = wc_get_product( $db_score->related_product_id );
			// If the product exists, and the product is visible, then we can keep it in the array
			if ( $product && $product->exists() && $product->is_visible() ) {
				$scores[ $db_score->related_product_id ] = (float) $db_score->score;
			}
		}

		asort( $scores );
	}

	return $scores;
}

function woocommerce_recommender_get_purchased_together( $current_product_id, $activity_types = [ 'completed' ] ) {
	global $wpdb, $woocommerce_recommender;

	if ( ! is_array( $activity_types ) ) {
		$activity_types = (array) $activity_types;
	}

	$key = 'wc_recommender_fpt_' . implode( '_', $activity_types ) . '_' . $current_product_id;

	$recommendations_sql = $wpdb->prepare( "SELECT related_product_id, score
				FROM $woocommerce_recommender->db_tbl_recommendations
				WHERE product_id = %d AND rkey = %s", $current_product_id, $key );

	$db_scores = $wpdb->get_results( $recommendations_sql );

	$scores = [];
	if ( is_array( $db_scores ) && ! is_wp_error( $db_scores ) ) {

		foreach ( $db_scores as $db_score ) {
			$product = wc_get_product( $db_score->related_product_id );
			// If the product exists, and the product is visible, then we can keep it in the array
			if ( $product && $product->exists() && $product->is_visible() ) {
				$scores[ $db_score->related_product_id ] = (float) $db_score->score;
			}
		}

		asort( $scores );
	}

	return $scores;
}

function woocommerce_recommender_sort_posts( &$posts, $simularity_scores ) {
	$sorter = new WC_Recommender_Sorting_Helper( $simularity_scores );
	usort( $posts, [ &$sorter, 'sort' ] );
}

function woocommerce_recommender_sort_also_viewed( &$posts, $simularity_scores ) {
	$sorter = new WC_Recommender_Sorting_Helper( $simularity_scores );
	usort( $posts, [ &$sorter, 'sort_also_viewed' ] );
}

function woocommerce_recommender_get_posts_and_columns() {
	global $related_posts_per_page, $related_columns;
	$related_posts_per_page = get_option( 'wc_recommender_item_count' ) ? (int) get_option( 'wc_recommender_item_count' ) : 2;
	$related_columns        = get_option( 'wc_recommender_column_count' ) ? (int) get_option( 'wc_recommender_column_count' ) : 2;
}

function woocommerce_recommender_get_posts_and_columns_template( $template ) {
	global $woocommerce_recommender;

	return $woocommerce_recommender->plugin_dir() . '/get-posts-and-columns.php';
}

function woocommerce_recommender_disable_related( $template, $template_name, $template_path ) {
	global $woocommerce_recommender;

	if ( $template_name == 'single-product/related.php' || $template_name == 'single-product/up-sells.php' ) {
		if ( apply_filters( 'woocommerce_recommender_disable_related', $woocommerce_recommender->get_setting( 'wc_recommender_builtin_enabled', 'enabled' ) == 'disabled', get_queried_object_id() ) ) {
			$template = $woocommerce_recommender->plugin_dir() . 'templates/single-product/related.php';
		}
	}

	return $template;
}

add_action( 'init', 'woocommerce_recommender_manually_build_scores' );


//Cron Job
add_action( 'wc_recommender_build', 'woocommerce_recommender_build_scores' );

function woocommerce_recommender_build_scores() {
	$enable_cron = get_option( 'wc_recommender_enable_cron', 'enabled' );
	if ( $enable_cron == 'disabled' ) {
		update_option( 'woocommerce_recommender_cron_result', 'WP Cron Disabled');
		update_option( 'woocommerce_recommender_cron_end', time() );
		update_option( 'woocommerce_recommender_build_running', false );
		return;
	}

	$running = get_option( 'woocommerce_recommender_build_running', false );
	if ( empty( $running ) ) {
		update_option( 'woocommerce_recommender_build_running', true );
		update_option( 'woocommerce_recommender_cron_start', time() );
		set_time_limit( 0 );

		try {
			$builder = new WC_Recommender_Recorder();
			$builder->woocommerce_recommender_begin_build_similarity( false, 0 );
			update_option( 'woocommerce_recommender_cron_result', 'OK' );
		} catch ( Exception $exc ) {
			update_option( 'woocommerce_recommender_cron_result', $exc->getTraceAsString() );
		}

		update_option( 'woocommerce_recommender_cron_end', time() );
		update_option( 'woocommerce_recommender_build_running', false );
	}
}

function woocommerce_recommender_manually_build_scores() {
	global $wpdb, $woocommerce_recommender;

	if ( isset( $_REQUEST['woocommerce_recommender_build'] ) && ! empty( $_REQUEST['woocommerce_recommender_build'] ) ) {

		$total = $wpdb->get_var( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type = 'product' and post_status='publish'" );
		$start = isset( $_POST['woocommerce_recommender_build_start'] ) ? $_POST['woocommerce_recommender_build_start'] : 0;
		$count = 10;

		$time_pre = microtime( true );

		$builder = new WC_Recommender_Recorder();
		$builder->woocommerce_recommender_begin_build_similarity( $start, $count );

		$time_post = microtime( true );
		$exec_time = floatval( ( $time_post - $time_pre ) * ( ( $total - $start ) / $count ) );

		$d = date( 'H:i:s', $exec_time );


		$next_start = $start + $count;
		if ( $next_start < $total ) {
			echo '<html><body onload="document.forms.woocommerce_recommender_build.submit();">';
			echo '<h1>Building Recommendations: ' . $next_start . ' through ' . ( $next_start + $count ) . ' of ' . $total . '</h1>';
			echo '<br />Estimated Time Remaining: <strong>' . $d . '</strong>';
			echo '<form name="woocommerce_recommender_build" method="POST">';
			echo '<input type="hidden" name="woocommerce_recommender_build" value="1" />';
			echo '<input type="hidden" name="woocommerce_recommender_build_start" value="' . $next_start . '" />';
			echo '</form>';
			echo '</body></html>';
			die();
		} else {
			wp_redirect( esc_url_raw( add_query_arg( [
				'woocommerce_recommender_build' => false,
				'built'                         => $total
			] ) ) );
			die();
		}
	}
}
