<?php

/**
 * Update Earned Points Action Scheduler.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'RS_Update_Earned_Points_Action' ) ) {

	/**
	 * Class.
	 * */
	class RS_Update_Earned_Points_Action extends SRP_Action_Scheduler {

		/**
		 * Class Constructor.
		 */
		public function __construct() {

			$this->id                            = 'rs_update_earned_points_action';
			$this->action_scheduler_name         = 'rs_update_earned_points_action';
			$this->chunked_action_scheduler_name = 'rs_chunked_update_earned_points_action_data';
			$this->option_name                   = 'rs_update_earned_points_action_data';
			$this->settings_option_name          = 'rs_update_earned_points_action_settings_args';

			// Admin init.
			add_action( 'admin_init', array( $this, 'admin_init' ) );

			parent::__construct();
		}

		/*
		 * Get progress bar label.
		 */

		public function get_progress_bar_label() {
			return '';
		}

		/**
		 * Get success message.
		 */
		public function get_success_message() {
			return '';
		}

		/*
		 * Admin init.
		 */

		public function admin_init() {

			if ( ! isset( $_GET[ 'page' ] ) ) {
				return;
			}

			if ( isset( $_GET[ 'page' ] ) && 'sumo-reward-points-welcome-page' != $_GET[ 'page' ] ) {
				return;
			}

			if ( 'yes' == get_option( 'rs_points_update_success' ) ) {
				return;
			}

			global $wpdb;
			$user_ids = $wpdb->get_results( "SELECT DISTINCT ID FROM {$wpdb->users} as p INNER JOIN {$wpdb->usermeta} as p1 ON p.ID=p1.user_id WHERE p1.meta_key = 'rs_expired_points_before_delete' AND p1.meta_value > '0'" );
			if ( ! srp_check_is_array( $user_ids ) ) {
				return;
			}

			$this->schedule_action( $user_ids, array() );
		}

		/*
		 * Chunked scheduler action.
		 */

		public function chunked_scheduler_action( $user_ids ) {

			if ( ! srp_check_is_array( $user_ids ) ) {
				return;
			}

			foreach ( $user_ids as $user_id ) {
				$user = get_user_by( 'ID', $user_id );
				if ( ! is_object( $user ) ) {
					continue;
				}

				if ( 'yes' == get_user_meta( $user_id, 'rs_check_if_points_updated', true ) ) {
					continue;
				}

				$new_points = ( float ) get_user_meta( $user_id, 'rs_earned_points_before_delete', true ) + ( float ) get_user_meta( $user_id, 'rs_expired_points_before_delete', true );
				update_user_meta( $user_id, 'rs_earned_points_before_delete', $new_points );
				update_user_meta( $user_id, 'rs_check_if_points_updated', 'yes' );
			}
		}
	}

}
