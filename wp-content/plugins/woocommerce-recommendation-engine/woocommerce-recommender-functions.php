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
	$user_id       = !$user_id && is_user_logged_in() ? get_current_user_id() : 0;

	woocommerce_recommender_record_product( $product_id, $session_id, $user_id, 0, $activity_type, $activity_date );
}

/**
 * Records when a product is added to the cart. Wrapper for woocommerce_recommender_record_product.
 *
 * @param int $product_id
 */
function woocommerce_recommender_record_product_in_cart( $product_id ) {
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
 * @global wpdb $wpdb
 * @global WC_Recommendation_Engine $woocommerce_recommender
 *
 * @param int $product_id
 * @param string $session_id
 * @param int $user_id
 * @param int $order_id
 * @param string $activity_type
 * @param string $activity_date MYSQL date formatted string.
 *
 * @return bool
 */
function woocommerce_recommender_record_product( $product_id, $session_id, $user_id, $order_id, $activity_type, $activity_date ) {
	global $wpdb, $woocommerce_recommender;

	$data = array(
		'session_id'    => $session_id,
		'activity_type' => $activity_type,
		'product_id'    => $product_id,
		'user_id'       => $user_id,
		'order_id'      => $order_id,
		'activity_date' => $activity_date
	);

	$format = array( '%s', '%s', '%d', '%d', '%d', '%s' );
	$result = $wpdb->insert( $woocommerce_recommender->db_tbl_session_activity, $data, $format );

	return $result;
}

/**
 * Updates the activity type of a previously recorded session history item.
 * @global wpdb $wpdb
 * @global WC_Recommendation_Engine $woocommerce_recommender
 *
 * @param type $order_id
 * @param type $product_id
 * @param type $activity_type
 */
function woocommerce_recommender_update_recorded_product( $order_id, $product_id, $activity_type ) {
	global $wpdb, $woocommerce_recommender;
	$activity_date = date( 'Y-m-d H:i:s' );

	$data = array(
		'activity_type' => $activity_type,
		'activity_date' => $activity_date
	);

	$format = array( '%s', '%s' );

	$where_data = array(
		'product_id' => $product_id,
		'order_id'   => $order_id,
	);

	$where_format = array( '%d', '%d' );
	$wpdb->update( $woocommerce_recommender->db_tbl_session_activity, $data, $where_data, $format, $where_format );
}

function woocommerce_recommender_get_simularity(
	$current_product_id, $activity_types = array(
	'completed',
	'in-cart',
	'viewed'
)
) {
	global $wpdb, $woocommerce_recommender;

	if ( !is_array( $activity_types ) ) {
		$activity_types = (array) $activity_types;
	}

	$key = 'wc_recommender_' . implode( '_', $activity_types ) . '_' . $current_product_id;

	$recommendations_sql = $wpdb->prepare( "SELECT related_product_id, score
				FROM $woocommerce_recommender->db_tbl_recommendations
				WHERE product_id = %d AND rkey = %s", $current_product_id, $key );

	$db_scores = $wpdb->get_results( $recommendations_sql );

	$scores = array();
	if ( is_array( $db_scores ) && !is_wp_error( $db_scores ) ) {

		foreach ( $db_scores as $db_score ) {
			$scores[ $db_score->related_product_id ] = (float) $db_score->score;
		}

		return $scores;
	} else {

	}
}

function woocommerce_recommender_get_purchased_together( $current_product_id, $activity_types = array( 'completed' ) ) {
	global $wpdb, $woocommerce_recommender;

	if ( !is_array( $activity_types ) ) {
		$activity_types = (array) $activity_types;
	}

	$key = 'wc_recommender_fpt_' . implode( '_', $activity_types ) . '_' . $current_product_id;

	$recommendations_sql = $wpdb->prepare( "SELECT related_product_id, score
				FROM $woocommerce_recommender->db_tbl_recommendations
				WHERE product_id = %d AND rkey = %s", $current_product_id, $key );

	$db_scores = $wpdb->get_results( $recommendations_sql );

	$scores = array();
	if ( is_array( $db_scores ) && !is_wp_error( $db_scores ) ) {

		foreach ( $db_scores as $db_score ) {
			$scores[ $db_score->related_product_id ] = (float) $db_score->score;
		}

		asort( $scores );

		return $scores;
	}
}

function woocommerce_recommender_sort_posts( &$posts, $simularity_scores ) {
	$sorter = new WC_Recommender_Sorting_Helper( $simularity_scores );
	usort( $posts, array( &$sorter, 'sort' ) );
}

function woocommerce_recommender_sort_also_viewed( &$posts, $simularity_scores ) {
	$sorter = new WC_Recommender_Sorting_Helper( $simularity_scores );
	usort( $posts, array( &$sorter, 'sort_also_viewed' ) );
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

class WC_Recommender_Recorder {

	public $posts_batch_size = 10;
	private $tbl_storage;

	public function __construct() {
		global $wpdb, $woocommerce_recommender;
		$this->tbl_storage = $woocommerce_recommender->db_tbl_recommendations;
	}

	public function build_all_async() {
		global $wpdb, $woocommerce_recommender;

		$wpdb->query( "DELETE FROM $woocommerce_recommender->db_tbl_recommendations" );
		$total = $wpdb->get_var( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type = 'product' and post_status='publish'" );
		$pages = absint( $total / $this->posts_batch_size ) + 1;

		$endpoints         = array();
		$endpoint_template = add_query_arg( array(
			'nocache'                             => '%s',
			'woocommerce_recommender_build_slice' => 'true',
			'start'                               => '%d'
		), trailingslashit( get_site_url() ) );

		for ( $p = 0; $p < $pages; $p ++ ) {
			$endpoints[] = sprintf( $endpoint_template, uniqid( '', true ), $p * $this->posts_batch_size );
		}

		//create a new RollingCurl object and pass it the name of your custom callback function
		$rc = new RollingCurl( "woocommerce_recommender_async_request_callback" );

		//the window size determines how many simultaneous requests to allow.  
		$rc->window_size = 20;

		foreach ( $endpoints as $url ) {
			// add each request to the RollingCurl object
			$request = new RollingCurlRequest( $url );
			$rc->add( $request );
		}

		$rc->execute();
	}

	function woocommerce_recommender_begin_build_simularity( $start = false, $count = 10, $skip_delete = false, $type = 'all' ) {
		global $wpdb, $woocommerce_recommender;
		$products_to_process = array();

		$sql = '';
		if ( $start !== false ) {
			$sql = $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'product' and post_status='publish' LIMIT %d,%d", $start, $count );
		} else {
			$sql = "SELECT ID FROM $wpdb->posts WHERE post_type = 'product' and post_status='publish'";
		}

		$products_to_process = $wpdb->get_col( $sql );

		if ( !$skip_delete && ( $start === false || $start === 0 ) ) {
			if ( $type == 'viewed' ) {
				$wpdb->query( "DELETE FROM $woocommerce_recommender->db_tbl_recommendations WHERE rkey LIKE '%recommender_viewed%'" );
			}

			if ( $type == 'purchased' ) {
				$wpdb->query( "DELETE FROM $woocommerce_recommender->db_tbl_recommendations WHERE rkey LIKE '%recommender_completed%'" );
			}

			if ( $type == 'purchased-together' ) {
				$wpdb->query( "DELETE FROM $woocommerce_recommender->db_tbl_recommendations WHERE rkey LIKE '%fpt_completed%'" );
			}

			if ( $type == 'all' ) {
				$wpdb->query( "DELETE FROM $woocommerce_recommender->db_tbl_recommendations" );
			}
		}

		foreach ( $products_to_process as $product_id ) {
			if ( $type == 'viewed' ) {
				$this->woocommerce_recommender_build_simularity( $product_id, array( 'viewed' ) );
			}


			if ( $type == 'purchased' ) {
				$this->woocommerce_recommender_build_simularity( $product_id, array( 'completed' ) );
			}


			if ( $type == 'purchased-together' ) {
				$this->woocommerce_build_purchased_together( $product_id, array( 'completed' ) );
			}

			if ( $type == 'all' ) {
				$this->woocommerce_recommender_build_simularity( $product_id, array( 'viewed' ) );
				$this->woocommerce_recommender_build_simularity( $product_id, array( 'completed' ) );
				$this->woocommerce_build_purchased_together( $product_id, array( 'completed' ) );
			}

		}
	}

	function woocommerce_recommender_build_simularity(
		$current_product_id, $activity_types = array(
		'completed',
		'in-cart',
		'viewed'
	)
	) {
		global $wpdb, $woocommerce_recommender;

		if ( !is_array( $activity_types ) ) {
			$activity_types = (array) $activity_types;
		}

		$key = 'wc_recommender_' . implode( '_', $activity_types ) . '_' . $current_product_id;

		$sql   = $wpdb->prepare( "SELECT DISTINCT session_id FROM $woocommerce_recommender->db_tbl_session_activity WHERE product_id = %d AND activity_type IN ('" . implode( "','", $activity_types ) . "')", $current_product_id );
		$item1 = $wpdb->get_col( $sql );

		$scores = array();

		$sql =
			"SELECT p.ID FROM $wpdb->posts p INNER JOIN (SELECT DISTINCT tbl1.product_id FROM $woocommerce_recommender->db_tbl_session_activity tbl1 INNER JOIN (
	              SELECT DISTINCT session_id FROM $woocommerce_recommender->db_tbl_session_activity WHERE product_id = %d
                ) tbl2 ON tbl1.session_id = tbl2.session_id) p_inner on p.ID = p_inner.product_id WHERE post_type = 'product' and post_status='publish'";


		$sql         = $wpdb->prepare( $sql, $current_product_id );
		$product_ids = $wpdb->get_col( $sql );
		foreach ( $product_ids as $product_id ) {
			if ( $product_id != $current_product_id ) {

				$item2 = $wpdb->get_col( $wpdb->prepare( "SELECT session_id FROM $woocommerce_recommender->db_tbl_session_activity WHERE product_id = %d AND activity_type IN ('" . implode( "','", $activity_types ) . "')", $product_id ) );

				$arr_intersection = array_intersect( $item1, $item2 );
				$arr_union        = array_merge( $item1, $item2 );

				if ( count( $arr_union ) ) {
					$coefficient = count( $arr_intersection ) / count( $arr_union );
					if ( $coefficient ) {
						$scores[ $product_id ] = (float) $coefficient;
					}
				}
			}
		}

		asort( $scores, SORT_NUMERIC );

		foreach ( $scores as $related_product_id => $score ) {

			$wpdb->insert( $this->tbl_storage, array(
				'rkey'               => $key,
				'product_id'         => (int) $current_product_id,
				'related_product_id' => (int) $related_product_id,
				'score'              => (float) $score
			) );
		}
	}

	function woocommerce_recommender_build_simularity_against_product(
		$current_product_id, $related_product_id, $activity_types = array(
		'completed',
		'in-cart',
		'viewed'
	)
	) {
		global $wpdb, $woocommerce_recommender;

		if ( !is_array( $activity_types ) ) {
			$activity_types = (array) $activity_types;
		}

		$key = 'wc_recommender_' . implode( '_', $activity_types ) . '_' . $current_product_id;

		$sql   = $wpdb->prepare( "SELECT DISTINCT session_id FROM $woocommerce_recommender->db_tbl_session_activity WHERE product_id = %d AND activity_type = %s", $current_product_id, $activity_types[0] );
		$item1 = $wpdb->get_col( $sql );

		$score = null;

		if ( $related_product_id != $current_product_id ) {
			$item2            = $wpdb->get_col( $wpdb->prepare( "SELECT session_id FROM $woocommerce_recommender->db_tbl_session_activity WHERE product_id = %d AND activity_type = %s", $related_product_id, $activity_types[0] ) );
			$arr_intersection = array_intersect( $item1, $item2 );
			$arr_union        = array_merge( $item1, $item2 );

			if ( count( $arr_union ) ) {
				$coefficient = count( $arr_intersection ) / count( $arr_union );
				if ( $coefficient ) {
					$score = (float) $coefficient;
				}
			}
		}

		if ( $score ) {
			$wpdb->insert( $this->tbl_storage, array(
				'rkey'               => $key,
				'product_id'         => (int) $current_product_id,
				'related_product_id' => (int) $related_product_id,
				'score'              => (float) $score
			) );
		}

	}


	function woocommerce_build_purchased_together( $current_product_id, $activity_types = array( 'completed' ) ) {
		global $wpdb, $woocommerce_recommender;

		$key   = 'wc_recommender_fpt_' . implode( '_', $activity_types ) . '_' . $current_product_id;
		$sql   = $wpdb->prepare( "SELECT DISTINCT order_id FROM $woocommerce_recommender->db_tbl_session_activity WHERE order_id > 0 AND product_id = %d AND activity_type = %s", $current_product_id, $activity_types[0] );
		$item1 = $wpdb->get_col( $sql );

		$scores      = array();
		$product_ids = $wpdb->get_col( "SELECT ID FROM $wpdb->posts WHERE post_type = 'product' and post_status='publish'" );
		foreach ( $product_ids as $product_id ) {
			if ( $product_id != $current_product_id ) {

				$item2 = $wpdb->get_col( $wpdb->prepare( "SELECT order_id FROM $woocommerce_recommender->db_tbl_session_activity WHERE order_id > 0 AND product_id = %d AND activity_type = %s", $product_id, $activity_types[0] ) );

				$arr_intersection = array_intersect( $item1, $item2 );
				$arr_union        = array_merge( $item1, $item2 );

				if ( count( $arr_union ) ) {
					$coefficient = count( $arr_intersection ) / count( $arr_union );
					if ( $coefficient ) {
						$scores[ $product_id ] = (float) $coefficient;
					}
				}
			}
		}

		asort( $scores );

		foreach ( $scores as $related_product_id => $score ) {

			$wpdb->insert( $this->tbl_storage, array(
				'rkey'               => $key,
				'product_id'         => (int) $current_product_id,
				'related_product_id' => (int) $related_product_id,
				'score'              => (float) $score
			) );
		}
	}


	function woocommerce_build_purchased_together_against_product( $current_product_id, $related_product_id, $activity_types = array( 'completed' ) ) {
		global $wpdb, $woocommerce_recommender;

		$key   = 'wc_recommender_fpt_' . implode( '_', $activity_types ) . '_' . $current_product_id;
		$sql   = $wpdb->prepare( "SELECT DISTINCT order_id FROM $woocommerce_recommender->db_tbl_session_activity WHERE order_id > 0 AND product_id = %d AND activity_type = %s", $current_product_id, $activity_types[0] );
		$item1 = $wpdb->get_col( $sql );

		$score = null;
		if ( $related_product_id != $current_product_id ) {

			$item2 = $wpdb->get_col( $wpdb->prepare( "SELECT order_id FROM $woocommerce_recommender->db_tbl_session_activity WHERE order_id > 0 AND product_id = %d AND activity_type = %s", $related_product_id, $activity_types[0] ) );

			$arr_intersection = array_intersect( $item1, $item2 );
			$arr_union        = array_merge( $item1, $item2 );

			if ( count( $arr_union ) ) {
				$coefficient = count( $arr_intersection ) / count( $arr_union );
				if ( $coefficient ) {
					$score = (float) $coefficient;
				}
			}
		}

		if ( $score ) {
			$wpdb->insert( $this->tbl_storage, array(
				'rkey'               => $key,
				'product_id'         => (int) $current_product_id,
				'related_product_id' => (int) $related_product_id,
				'score'              => (float) $score
			) );
		}
	}


}

add_action( 'init', 'woocommerce_recommender_manually_build_scores' );


//Cron Job
add_action( 'wc_recommender_build', 'woocommerce_recommender_build_scores' );

function woocommerce_recommender_build_scores() {
	$running = get_option( 'woocommerce_recommender_build_running', false );
	if ( empty( $running ) ) {
		update_option( 'woocommerce_recommender_build_running', true );
		update_option( 'woocommerce_recommender_cron_start', time() );
		set_time_limit( 0 );

		try {
			$builder = new WC_Recommender_Recorder();
			$builder->woocommerce_recommender_begin_build_simularity( false, 0 );
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

	if ( isset( $_REQUEST['woocommerce_recommender_dump'] ) ) {
		$results = $wpdb->get_results( "SELECT * FROM $woocommerce_recommender->db_tbl_recommendations ORDER BY product_id" );
		foreach ( $results as $result ) {
			echo '<br />' . $result->product_id . ' ' . $result->related_product_id;
		}

		die();
	}

	if ( isset( $_REQUEST['woocommerce_recommender_build_async'] ) ) {
		require 'lib/RollingCurl.php';
		header( "Content-Type: text/plain" );

		$force = $_REQUEST['woocommerce_recommender_build_async'];

		$builder = new WC_Recommender_Recorder();
		$running = get_option( 'woocommerce_recommender_build_running', false );
		if ( ( $force == 'force' ) || empty( $running ) ) {

			update_option( 'woocommerce_recommender_build_async_running', true );
			update_option( 'woocommerce_recommender_build_running', true );
			update_option( 'woocommerce_recommender_cron_start', time() );

			echo 'Begin Building Recommendations' . PHP_EOL;
			$builder->build_all_async();
			echo 'Completed Building Recommendations' . PHP_EOL;

			update_option( 'woocommerce_recommender_cron_end', time() );
			update_option( 'woocommerce_recommender_build_running', false );
			update_option( 'woocommerce_recommender_build_async_running', false );
		} else {
			echo 'Cron Job Already Running';
		}

		die();
	}

	if ( isset( $_REQUEST['woocommerce_recommender_build_slice'] ) ) {
		require 'lib/RollingCurl.php';

		$running = get_option( 'woocommerce_recommender_build_async_running', false );
		if ( !empty( $running ) ) {
			header( 'Content Type: text/plain' );
			$builder = new WC_Recommender_Recorder();
			//Set start at 10 so we never send the delete flag from this. 
			$start = ( isset( $_GET['start'] ) && $_GET['start'] ) ? $_GET['start'] : 0;
			//call the begin build, which will build 10 recommendations by default.   use skip delete param since the async operation deletes for us. 
			$builder->woocommerce_recommender_begin_build_simularity( $start, $builder->posts_batch_size, true );


			echo 'Built recommendations ' . $start . ' through ' . ( $builder->posts_batch_size + $start ) . PHP_EOL;
			echo PHP_EOL;

		} else {
			echo 'No Async Job Running, exiting';
			echo PHP_EOL;
		}

		die();
	}

	if ( isset( $_REQUEST['woocommerce_recommender_build'] ) && !empty( $_REQUEST['woocommerce_recommender_build'] ) ) {

		$total = $wpdb->get_var( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type = 'product' and post_status='publish'" );
		$start = isset( $_POST['woocommerce_recommender_build_start'] ) ? $_POST['woocommerce_recommender_build_start'] : 0;
		$count = 10;

		$time_pre = microtime( true );

		$builder = new WC_Recommender_Recorder();
		$builder->woocommerce_recommender_begin_build_simularity( $start, $count );

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
			wp_redirect( esc_url_raw( add_query_arg( array(
				'woocommerce_recommender_build' => false,
				'built'                         => $total
			) ) ) );
			die();
		}
	}
}

function woocommerce_recommender_async_request_callback( $response, $info ) {
	echo $response;
}
