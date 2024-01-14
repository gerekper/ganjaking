<?php

/**
 * Apply Previous Order Points Action Scheduler.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'RS_Apply_Previous_Order_Points' ) ) {

	/**
	 * Class.
	 * */
	class RS_Apply_Previous_Order_Points extends SRP_Action_Scheduler {

		/**
		 * Class Constructor.
		 */
		public function __construct() {

			$this->id                            = 'rs_apply_previous_order_points';
			$this->action_scheduler_name         = 'rs_apply_previous_order_points';
			$this->chunked_action_scheduler_name = 'rs_chunked_apply_previous_order_points_data';
			$this->option_name                   = 'rs_apply_previous_order_points_data';
			$this->settings_option_name          = 'rs_apply_previous_order_points_settings_args';

			// Do ajax action.
			add_action( 'wp_ajax_apply_points_previous_orders', array( $this, 'do_ajax_action' ) );

			parent::__construct();
		}

		/*
		 * Get progress bar label.
		 */

		public function get_progress_bar_label() {
			$label = __( 'Applying Points for Previous Orders is under process...', 'rewardsystem' );
			return $label;
		}

		/**
		 * Get redirect URL.
		 */
		public function get_redirect_url() {
			return add_query_arg(
				array(
					'page' => 'rewardsystem_callback',
					'tab'  => 'fprsadvanced',
				),
				SRP_ADMIN_URL
			);
		}

		/**
		 * Get success message.
		 */
		public function get_success_message() {
			$msg = __( 'Applying Points for Previous Orders Completed Successfully.', 'rewardsystem' );
			return $msg;
		}

		/**
		 * Do ajax action.
		 */
		public function do_ajax_action() {

			check_ajax_referer( 'fp-apply-points', 'sumo_security' );

			try {

				if ( ! isset( $_POST ) ) {
					throw new exception( esc_html__( 'Invalid Request', 'rewardsystem' ) );
				}

				if ( ! isset( $_POST['previousorderpointsfor'] ) ) {
					throw new exception( esc_html__( 'Invalid Data', 'rewardsystem' ) );
				}

				if ( ! isset( $_POST['awardpointson'] ) ) {
					throw new exception( esc_html__( 'Invalid Data', 'rewardsystem' ) );
				}

				$setting_values                               = array();
				$setting_values['previous_order_points_type'] = wc_clean( wp_unslash( $_POST['previousorderpointsfor'] ) );
				$setting_values['award_points_based_on']      = wc_clean( wp_unslash( $_POST['awardpointson'] ) );
				$setting_values['from_date']                  = isset( $_POST['fromdate'] ) ? wc_clean( wp_unslash( $_POST['fromdate'] ) ) : '';
				$setting_values['to_date']                    = isset( $_POST['todate'] ) ? wc_clean( wp_unslash( $_POST['todate'] ) ) : '';

				$settings_order_statuses = get_option( 'rs_order_status_control', array( 'processing', 'completed' ) );
				if ( empty( $settings_order_statuses ) ) {
					throw new exception( esc_html__( 'Order Status left empty in General Settings', 'rewardsystem' ) );
				}

				$order_statuses = array();
				foreach ( $settings_order_statuses as $status_name ) {
					$order_statuses[] = 'wc-' . $status_name;
				}

				if ( empty( $order_statuses ) ) {
					throw new exception( esc_html__( 'No Data Found', 'rewardsystem' ) );
				}

				if ( '1' == $setting_values['previous_order_points_type'] ) {
					$args = array(
						'post_type'      => 'shop_order',
						'posts_per_page' => -1,
						'meta_query'     => array(
							array(
								'key'     => 'reward_points_awarded',
								'compare' => 'NOT EXISTS',
							),
							array(
								'key'     => '_customer_user',
								'value'   => '0',
								'compare' => '!=',
							),
						),
						'post_status'    => $order_statuses,
						'fields'         => 'ids',
					);
				} else {
					$args = array(
						'post_type'      => 'shop_order',
						'posts_per_page' => -1,
						'meta_query'     => array(
							array(
								'key'     => 'reward_points_awarded',
								'compare' => 'EXISTS',
							),
							array(
								'key'     => '_customer_user',
								'value'   => '0',
								'compare' => '!=',
							),
						),
						'post_status'    => $order_statuses,
						'fields'         => 'ids',
					);
				}

				$order_ids = get_posts( $args );
				if ( ! srp_check_is_array( $order_ids ) ) {
					throw new exception( esc_html__( 'No Order(s) Found', 'rewardsystem' ) );
				}

				$this->schedule_action( $order_ids, $setting_values );
				$redirect_url = esc_url_raw(
					add_query_arg(
						array(
							'page'                => 'rewardsystem_callback',
							'rs_action_scheduler' => $this->get_id(),
						),
						SRP_ADMIN_URL
					)
				);
				wp_send_json_success( array( 'redirect_url' => $redirect_url ) );
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) );
			}
		}

		/*
		 * Chunked scheduler action.
		 */

		public function chunked_scheduler_action( $order_ids ) {

			if ( ! srp_check_is_array( $order_ids ) ) {
				return;
			}

			foreach ( $order_ids as $order_id ) {
				$order = wc_get_order( $order_id );
				if ( ! is_object( $order ) ) {
					continue;
				}

				$order_user_id = $order->get_user_id();
				if ( ! $order_user_id ) {
					continue;
				}

				$is_points_awarded         = $order->get_meta( 'reward_points_awarded' );
				$settings_data             = $this->get_settings_data();
				$previous_order_points_for = isset( $settings_data['previous_order_points_type'] ) ? $settings_data['previous_order_points_type'] : '1';
				$from_date                 = isset( $settings_data['from_date'] ) ? $settings_data['from_date'] : '';
				$to_date                   = isset( $settings_data['to_date'] ) ? $settings_data['to_date'] : '';
				$award_points_based_on     = isset( $settings_data['award_points_based_on'] ) ? $settings_data['award_points_based_on'] : '';

				if ( '1' === $award_points_based_on ) {
					if ( '1' === $previous_order_points_for ) {
						if ( 'yes' === $is_points_awarded ) {
							continue;
						}

						$new_obj = new RewardPointsOrder( $order_id, $apply_previous_order_points = 'yes' );
						$new_obj->update_earning_points_for_user();
					} else {
						$pointstodelete[] = $this->get_already_earned_points( $order_id, $order_user_id );
						$totalpoints      = array_sum( $pointstodelete );
						$this->replace_the_points_already_earned_in_order( $order_id, $order_user_id, $totalpoints );
						$order->delete_meta_data( 'reward_points_awarded' );
						$order->delete_meta_data( 'earning_point_once' );
						$new_obj = new RewardPointsOrder( $order_id, $apply_previous_order_points = 'yes' );
						$new_obj->update_earning_points_for_user( 'Replaced' );
					}
				} elseif ( '' != $from_date || '' != $to_date ) {
						$from_date     = strtotime( $from_date );
						$to_date       = strtotime( $to_date );
						$modified_date = strtotime( get_the_time( 'Y-m-d', $order_id ) );
					if ( ( $from_date <= $modified_date ) && ( $modified_date <= $to_date ) ) {
						if ( '1' === $previous_order_points_for ) {
							if ( 'yes' == $is_points_awarded ) {
								continue;
							}

							$new_obj = new RewardPointsOrder( $order_id, $apply_previous_order_points = 'yes' );
							$new_obj->update_earning_points_for_user();
						} else {
							$pointstodelete[] = $this->get_already_earned_points( $order_id, $order_user_id );
							$totalpoints      = array_sum( $pointstodelete );
							$this->replace_the_points_already_earned_in_order( $order_id, $order_user_id, $totalpoints );
							$order->delete_meta_data( 'reward_points_awarded' );
							$order->delete_meta_data( 'earning_point_once' );
							$new_obj = new RewardPointsOrder( $order_id, $apply_previous_order_points = 'yes' );
							$new_obj->update_earning_points_for_user( 'Replaced' );
						}
					}
				}
				$order->save();
			}
		}

		/**
		 * Replace the points already earned in order.
		 *
		 * @param int   $order_id Order ID.
		 * @param int   $user_id User ID.
		 * @param float $totalpoints Total points.
		 */
		public function replace_the_points_already_earned_in_order( $order_id, $user_id, $totalpoints ) {

			global $wpdb;
			$query = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}rspointexpiry WHERE userid = %d AND expirydate = 999999999999", $user_id ), ARRAY_A );
			if ( ! empty( $query ) ) {
				$id               = $query['id'];
				$available_points = $query['earnedpoints'] - $query['usedpoints'];
				if ( $totalpoints >= $available_points ) {
					$used_points = $query['usedpoints'] + $available_points;
				} else {
					$used_points = $query['usedpoints'] + $totalpoints;
				}

				$wpdb->update( "{$wpdb->prefix}rspointexpiry", array( 'usedpoints' => $used_points ), array( 'id' => $id ) );
			}

			$query2 = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}rspointexpiry WHERE checkpoints = 'PPRP' AND orderid = %d AND userid = %d AND expirydate != 999999999999", $order_id, $user_id ), ARRAY_A );
			if ( ! empty( $query2 ) ) {
				$id               = $query2['id'];
				$available_points = $query2['earnedpoints'] - $query2['usedpoints'];
				if ( $totalpoints >= $available_points ) {
					$used_points = $query2['usedpoints'] + $available_points;
				} else {
					$used_points = $query2['usedpoints'] + $totalpoints;
				}

				$wpdb->update( "{$wpdb->prefix}rspointexpiry", array( 'usedpoints' => $used_points ), array( 'id' => $id ) );
			}
		}

		/*
		 * Get already earned points.
		 */

		public function get_already_earned_points( $order_id, $user_id ) {

			global $wpdb;
			$rsrecordpoints_table = $wpdb->get_results( $wpdb->prepare( "SELECT earnedpoints,userid FROM {$wpdb->prefix}rspointexpiry WHERE checkpoints = 'PPRP' AND orderid = %d AND userid = %d", $order_id, $user_id ), ARRAY_A );
			if ( ! srp_check_is_array( $rsrecordpoints_table ) ) {
				return;
			}

			$earned_points = 0;
			foreach ( $rsrecordpoints_table as $earnedpoints ) {
				if ( isset( $earnedpoints['userid'] ) && $earnedpoints['userid'] == $user_id ) {
					$earned_points = isset( $earnedpoints['earnedpoints'] ) ? $earnedpoints['earnedpoints'] : 0;
				}
			}

			return $earned_points;
		}
	}

}
