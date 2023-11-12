<?php

class WC_Recommender_Recorder {

	private $tbl_storage;

	public function __construct() {
		global $wpdb, $woocommerce_recommender;
		$this->tbl_storage = $woocommerce_recommender->db_tbl_recommendations;
	}

	function woocommerce_recommender_begin_build_similarity( $start = false, $count = 10, $skip_delete = false, $type = 'all' ) {
		global $wpdb, $woocommerce_recommender;

		if ( $start !== false ) {
			$sql = $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'product' and post_status='publish' LIMIT %d,%d", $start, $count );
		} else {
			$sql = "SELECT ID FROM $wpdb->posts WHERE post_type = 'product' and post_status='publish'";
		}

		$products_to_process = $wpdb->get_col( $sql );

		if ( ! $skip_delete && ( $start === false || $start === 0 ) ) {
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
				$this->woocommerce_recommender_build_similarity( $product_id, [ 'viewed' ] );
			}


			if ( $type == 'purchased' ) {
				$status = apply_filters( 'woocommerce_recommender_also_purchased_status', 'completed' );
				$this->woocommerce_recommender_build_similarity( $product_id, [ $status ] );
			}


			if ( $type == 'purchased-together' ) {
				$status = apply_filters( 'woocommerce_recommender_purchased_together_status', 'completed' );
				$this->woocommerce_build_purchased_together( $product_id, [ $status ] );
			}

			if ( $type == 'all' ) {
				$this->woocommerce_recommender_build_similarity( $product_id, [ 'viewed' ] );
				$status = apply_filters( 'woocommerce_recommender_also_purchased_status', 'completed' );
				$this->woocommerce_recommender_build_similarity( $product_id, [ $status ] );
				$status = apply_filters( 'woocommerce_recommender_purchased_together_status', 'completed' );
				$this->woocommerce_build_purchased_together( $product_id, [ $status ] );
			}
		}
	}

	function woocommerce_recommender_build_similarity(
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

		$sql   = $wpdb->prepare( "SELECT DISTINCT session_id FROM $woocommerce_recommender->db_tbl_session_activity WHERE product_id = %d AND activity_type IN ('" . implode( "','", $activity_types ) . "')", $current_product_id );
		$item1 = $wpdb->get_col( $sql );

		$scores = [];

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

			$wpdb->insert( $this->tbl_storage, [
				'rkey'               => $key,
				'product_id'         => (int) $current_product_id,
				'related_product_id' => (int) $related_product_id,
				'score'              => (float) $score
			] );
		}
	}

	function woocommerce_recommender_build_similarity_against_product(
		$current_product_id, $related_product_id, $activity_types = [
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
			$wpdb->insert( $this->tbl_storage, [
				'rkey'               => $key,
				'product_id'         => (int) $current_product_id,
				'related_product_id' => (int) $related_product_id,
				'score'              => (float) $score
			] );
		}

	}


	function woocommerce_build_purchased_together( $current_product_id, $activity_types = [ 'completed' ] ) {
		global $wpdb, $woocommerce_recommender;

		$key   = 'wc_recommender_fpt_' . implode( '_', $activity_types ) . '_' . $current_product_id;
		$sql   = $wpdb->prepare( "SELECT DISTINCT order_id FROM $woocommerce_recommender->db_tbl_session_activity WHERE order_id > 0 AND product_id = %d AND activity_type = %s", $current_product_id, $activity_types[0] );
		$item1 = $wpdb->get_col( $sql );

		$scores      = [];
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

			$wpdb->insert( $this->tbl_storage, [
				'rkey'               => $key,
				'product_id'         => (int) $current_product_id,
				'related_product_id' => (int) $related_product_id,
				'score'              => (float) $score
			] );
		}
	}


	function woocommerce_build_purchased_together_against_product( $current_product_id, $related_product_id, $activity_types = [ 'completed' ] ) {
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
			$wpdb->insert( $this->tbl_storage, [
				'rkey'               => $key,
				'product_id'         => (int) $current_product_id,
				'related_product_id' => (int) $related_product_id,
				'score'              => (float) $score
			] );
		}
	}
}
