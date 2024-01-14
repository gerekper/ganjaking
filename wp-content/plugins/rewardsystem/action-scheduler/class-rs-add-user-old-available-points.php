<?php

/**
 * Add User Old Available Points Action Scheduler.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'RS_Add_User_Old_Available_Points' ) ) {

	/**
	 * Class.
	 * */
	class RS_Add_User_Old_Available_Points extends SRP_Action_Scheduler {

		/**
		 * Class Constructor.
		 */
		public function __construct() {

			$this->id                            = 'rs_add_user_old_available_points';
			$this->action_scheduler_name         = 'rs_add_user_old_available_points';
			$this->chunked_action_scheduler_name = 'rs_chunked_add_user_old_available_points';
			$this->option_name                   = 'rs_add_user_old_available_points_data';
			$this->settings_option_name          = 'rs_add_user_old_available_points_settings_args';

			// Do ajax action.
			add_action( 'wp_ajax_add_old_points', array( $this, 'do_ajax_action' ) );

			parent::__construct();
		}

		/*
		 * Get progress bar label.
		 */

		public function get_progress_bar_label() {
			$label = __( 'Adding Old Points for User(s) is under process...', 'rewardsystem' );
			return $label;
		}

		/**
		 * Get redirect URL.
		 */
		public function get_redirect_url() {
			return add_query_arg( array( 'page' => 'rewardsystem_callback', 'tab' => 'fprsadvanced' ), SRP_ADMIN_URL );
		}

		/**
		 * Get success message.
		 */
		public function get_success_message() {
			$msg = __( 'Adding Old Points for User(s) Completed Successfully.', 'rewardsystem' );
			return $msg;
		}

		/*
		 * Do ajax action.
		 */

		public function do_ajax_action() {
			check_ajax_referer( 'fp-old-points', 'sumo_security' );

			try {

				if ( ! isset( $_POST ) ) {
					throw new exception( esc_html__( 'Invalid Request', 'rewardsystem' ) );
				}

				$args = array(
					'fields'       => 'ids',
					'meta_key'     => '_my_reward_points',
					'meta_value'   => '',
					'meta_compare' => '!=',
				);

				$user_ids = get_users( $args );
				if ( ! srp_check_is_array( $user_ids ) ) {
					throw new exception( esc_html__( 'No User(s) Found', 'rewardsystem' ) );
				}

				$this->schedule_action( $user_ids, array() );
				$redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback', 'rs_action_scheduler' => $this->get_id() ), SRP_ADMIN_URL ) );
				wp_send_json_success( array( 'redirect_url' => $redirect_url ) );
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) );
			}
		}

		/*
		 * Chunked scheduler action.
		 */

		public function chunked_scheduler_action( $user_ids ) {

			if ( ! srp_check_is_array( $user_ids ) ) {
				return;
			}

			global $wpdb;
			$time = time();
			foreach ( $user_ids as $user_id ) {
				$user = get_user_by( 'ID', $user_id );
				if ( ! is_object( $user ) ) {
					continue;
				}

				$old_points  = get_user_meta( $user_id, '_my_reward_points', true );
				$points_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM' {$wpdb->prefix}rspointexpiry WHERE userid = %d and expirydate = 999999999999", $user_id ), ARRAY_A );
				if ( srp_check_is_array( $points_data ) ) {
					$total_points = $points_data[ 'earnedpoints' ] + $old_points;
					$wpdb->update( "{$wpdb->prefix}rspointexpiry", array( 'earnedpoints' => $total_points ), array( 'id' => $points_data[ 'id' ] ) );
				} else {
					$wpdb->insert( "{$wpdb->prefix}rspointexpiry", array(
						'earnedpoints'      => $old_points,
						'usedpoints'        => '',
						'expiredpoints'     => '0',
						'userid'            => $user_id,
						'earneddate'        => $time,
						'expirydate'        => 999999999999,
						'checkpoints'       => 'OUP',
						'orderid'           => '',
						'totalearnedpoints' => '',
						'totalredeempoints' => '',
						'reasonindetail'    => '',
					) );
				}
			}
		}
	}

}
