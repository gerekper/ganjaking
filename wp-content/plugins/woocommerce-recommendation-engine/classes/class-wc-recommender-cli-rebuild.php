<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allows rebuilding recommendations via WP-CLI.
 *
 * @class    WC_PB_CLI
 * @version  5.5.0
 */
class WC_Recommender_CLI_Rebuild {

	/**
	 * Registers the update command.
	 */
	public static function register_command() {
		WP_CLI::add_command( 'wc_recommender_rebuild', array( 'WC_Recommender_CLI_Rebuild', 'rebuild' ) );
		WP_CLI::add_command( 'wc_recommender_install', array( 'WC_Recommender_CLI_Rebuild', 'install_stats' ) );

	}

	/**
	 * Runs all pending WooCommerce database updates.
	 */
	public static function rebuild() {
		global $wpdb, $woocommerce_recommender;
		WP_CLI::success( __( 'Recommendations Rebuilding', 'wc_recommender' ) );

		update_option( 'woocommerce_recommender_build_running', true );
		update_option( 'woocommerce_recommender_cron_start', time() );

		try {
			$builder = new WC_Recommender_Recorder();


			$products_to_process = array();
			$sql                 = '';
			$sql                 = "SELECT ID FROM $wpdb->posts WHERE post_type = 'product' and post_status='publish'";

			$products_to_process = $wpdb->get_col( $sql );



			if ( $products_to_process ) {
				WP_CLI::success( __( 'Removing previous recommendations.', 'wc_recommender' ) );
				$wpdb->query( "DELETE FROM $woocommerce_recommender->db_tbl_recommendations" );
				WP_CLI::success( sprintf( __( 'Processing %d products.', 'wc_recommender' ), count( $products_to_process ) ) );
				foreach ( $products_to_process as $product_id ) {
					WP_CLI::success( sprintf( __( 'Adding Also Viewed for ProductID: %d', 'wc_recommender' ), $product_id ) );
					$builder->woocommerce_recommender_build_simularity( $product_id, array( 'viewed' ) );
					WP_CLI::success( sprintf( __( 'Adding Also Purchased for ProductID: %d', 'wc_recommender' ), $product_id ) );
					$status = apply_filters('woocommerce_recommender_also_purchased_status', 'completed');
					$builder->woocommerce_recommender_build_simularity( $product_id, array( $status ) );
					WP_CLI::success( sprintf( __( 'Adding Purchased Together for ProductID: %d', 'wc_recommender' ), $product_id ) );
					$status = apply_filters('woocommerce_recommender_purchased_together_status', 'completed');
					$builder->woocommerce_build_purchased_together( $product_id, array( $status ) );
				}
			}

			update_option( 'woocommerce_recommender_cron_result', 'OK' );
		} catch ( Exception $exc ) {
			update_option( 'woocommerce_recommender_cron_result', $exc->getTraceAsString() );
			WP_CLI::add_error( $exc->getTraceAsString() );
		}

		update_option( 'woocommerce_recommender_cron_end', time() );
		update_option( 'woocommerce_recommender_build_running', false );

		WP_CLI::success( __( 'Recommendations Rebuilt.', 'wc_recommender' ) );
	}


	public static function install_stats() {
		global $wpdb, $woocommerce_recommender;

		WP_CLI::success( __( 'Recommendations Engine Is Installing Stats', 'wc_recommender' ) );


		$post_status = wc_get_order_statuses();
		$posts       = get_posts( array(
			'post_status' => array_keys( $post_status ),
			'post_type'   => 'shop_order',
			'nopaging'    => true
		) );


		if ( $posts && count( $posts ) ) {
			WP_CLI::success( sprintf( __( 'Processing %d orders', 'wc_recommender' ), count( $posts ) ) );

			foreach ( $posts as $post ) {
				$order_id = $post->ID;
				WP_CLI::success( sprintf( __( 'Installing Stats for OrderID: %s', 'wc_recommender' ), $order_id ) );

				$wc_order       = new WC_Order( $order_id );
				$wc_order_items = $wc_order->get_items();
				if ( $wc_order_items && count( $wc_order_items ) ) {
					foreach ( $wc_order_items as $wc_order_item ) {

						if ( WC_Recommender_Compatibility::is_wc_version_gte_2_7() ) {
							$wc_ordered_product = $wc_order_item->get_product();
						} else {
							$wc_ordered_product = @$wc_order->get_product_from_item( $wc_order_item );
						}


						if ( $wc_ordered_product && is_object( $wc_ordered_product ) && $wc_ordered_product->exists() ) {
							$sql                   = $wpdb->prepare( "SELECT COUNT(*) FROM $woocommerce_recommender->db_tbl_session_activity WHERE order_id = %d AND product_id = %d", $order_id, $wc_ordered_product->get_id() );
							$order_tracking_exists = $wpdb->get_var( $sql );
							if ( ! $order_tracking_exists ) {
								if ( WC_Recommender_Compatibility::is_wc_version_gte_2_7() ) {

									$customer_id = $wc_order->get_customer_id();
									$session_id  = empty( $customer_id ) ? $wc_order->get_billing_email() : $wc_order->get_customer_id();
									$session_id  = md5( $session_id );

									$activity_date = date( 'Y-m-d H:i:s', strtotime( $wc_order->get_date_created() ) );

									$user_id = empty( $wc_order->get_customer_id() ) ? 0 : $wc_order->get_customer_id();

									woocommerce_recommender_record_product( $wc_ordered_product->get_id(), $session_id, $user_id, $wc_order->get_id(), $wc_order->get_status(), $activity_date );
									WP_CLI::success( sprintf( __( 'Recording Activity for ProductID: %s', 'wc_recommender' ), $wc_ordered_product->get_id() ) );

									if ( $wc_ordered_product->is_type( 'variable' ) ) {
										woocommerce_recommender_record_product( $wc_ordered_product->get_parent_id(), $session_id, $user_id, $order_id, $wc_order->get_status(), $activity_date );
									}
								} else {
									$session_id    = isset( $wc_order->customer_user ) ? $wc_order->customer_user : ( isset( $wc_order->user_id ) ? $wc_order->user_id : $wc_order->billing_email );
									$session_id    = md5( $session_id );
									$activity_date = date( 'Y-m-d H:i:s', strtotime( $wc_order->order_date ) );
									$user_id       = isset( $wc_order->customer_user ) ? $wc_order->customer_user : ( isset( $wc_order->user_id ) ? $wc_order->user_id : 0 );
									woocommerce_recommender_record_product( $wc_ordered_product->get_id(), $session_id, $user_id, $wc_order->get_id(), $wc_order->status, $activity_date );
									if ( $wc_ordered_product->is_type( 'variable' ) && isset( $wc_ordered_product->variation_id ) && $wc_ordered_product->variation_id ) {
										woocommerce_recommender_record_product( $wc_ordered_product->variation_id, $session_id, $user_id, $order_id, $wc_order->status, $activity_date );
									}
								}
							} else {

								if ( WC_Recommender_Compatibility::is_wc_version_gte_2_7() ) {
									WP_CLI::success( sprintf( __( 'Updating Activity for ProductID: %s', 'wc_recommender' ), $wc_ordered_product->get_id() ) );
									woocommerce_recommender_update_recorded_product( $wc_order->get_id(), $wc_ordered_product->get_id(), $wc_order->get_status() );
									if ( $wc_ordered_product->is_type( 'variable' ) ) {
										woocommerce_recommender_update_recorded_product( $wc_order->get_id(), $wc_ordered_product->get_parent_id(), $wc_order->get_status() );
									}
								} else {
									woocommerce_recommender_update_recorded_product( $wc_order->get_id(), $wc_ordered_product->get_id(), $wc_order->status );
									if ( $wc_ordered_product->is_type( 'variable' ) && isset( $wc_ordered_product->variation_id ) && $wc_ordered_product->variation_id ) {
										woocommerce_recommender_update_recorded_product( $wc_order->get_id(), $wc_ordered_product->variation_id, $wc_order->status );
									}
								}
							}

							$order_viewed_exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $woocommerce_recommender->db_tbl_session_activity WHERE order_id = %d AND product_id = %d AND activity_type = 'viewed'", 0, $wc_ordered_product->get_id() ) );

							if ( ! $order_viewed_exists ) {
								$product_id    = $wc_ordered_product->get_id();
								$activity_type = 'viewed';

								if ( WC_Recommender_Compatibility::is_wc_version_gte_2_7() ) {
									$session_id    = empty( $wc_order->get_customer_id() ) ? $wc_order->get_billing_email() : $wc_order->get_customer_id();
									$session_id    = md5( $session_id );
									$activity_date = date( 'Y-m-d H:i:s', strtotime( $wc_order->get_date_created() ) );
									$user_id       = empty( $wc_order->get_customer_id() ) ? 0 : $wc_order->get_customer_id();
									WP_CLI::success( sprintf( __( 'Recording View for ProductID: %s', 'wc_recommender' ), $product_id ) );
									woocommerce_recommender_record_product( $product_id, $session_id, $user_id, 0, $activity_type, $activity_date );
								} else {
									$session_id    = isset( $wc_order->customer_user ) ? $wc_order->customer_user : ( isset( $wc_order->user_id ) ? $wc_order->user_id : $wc_order->billing_email );
									$session_id    = md5( $session_id );
									$activity_date = date( 'Y-m-d H:i:s', strtotime( $wc_order->order_date ) );
									$user_id       = isset( $wc_order->customer_user ) ? $wc_order->customer_user : ( isset( $wc_order->user_id ) ? $wc_order->user_id : 0 );
									woocommerce_recommender_record_product( $product_id, $session_id, $user_id, 0, $activity_type, $activity_date );
								}
							}
						}
					}
				}
			}
		} else {
			WP_CLI::error( __( 'No orders found to process', 'wc_recommender' ) );
		}
	}

}
