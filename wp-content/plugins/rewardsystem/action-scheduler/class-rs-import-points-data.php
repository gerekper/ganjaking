<?php

/**
 * Import Points Action Scheduler.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'RS_Import_Points_Data' ) ) {

	/**
	 * Class.
	 * */
	class RS_Import_Points_Data extends SRP_Action_Scheduler {

		/**
		 * Class Constructor.
		 */
		public function __construct() {

			$this->id                            = 'rs_import_points_data';
			$this->action_scheduler_name         = 'rs_import_points_data';
			$this->chunked_action_scheduler_name = 'rs_chunked_import_points_data';
			$this->option_name                   = 'rs_import_points_data';
			$this->settings_option_name          = 'rs_import_points_data_settings_args';

			// Admin init.
			add_action( 'admin_init', array( $this, 'admin_init' ) );

			parent::__construct();
		}

		/*
		 * Get progress bar label.
		 */

		public function get_progress_bar_label() {
			$label = __( 'Points Importing is under process...', 'rewardsystem' );
			return $label;
		}

		/**
		 * Get success message.
		 */
		public function get_success_message() {
			$msg = __( 'Points Imported Successfully.', 'rewardsystem' );
			return $msg;
		}

		/**
		 * Get redirect URL.
		 */
		public function get_redirect_url() {
			delete_option( 'rewardsystem_csv_array' );
			return add_query_arg( array( 'page' => 'rewardsystem_callback', 'tab' => 'fprsmodules', 'section' => 'fpimportexport' ), SRP_ADMIN_URL );
		}

		/*
		 * Admin init.
		 */

		public function admin_init() {

			if ( isset( $_REQUEST[ 'rs_new_action_reward_points' ] ) || isset( $_REQUEST[ 'rs_exist_action_reward_points' ] ) ) {
				$override_existing_import_option = ! empty( $_REQUEST[ 'rs_new_action_reward_points' ] ) ? wc_clean( wp_unslash( $_REQUEST[ 'rs_new_action_reward_points' ] ) ) : '';
				$checkpoint                      = ! empty( $override_existing_import_option ) ? 'IMPOVR' : 'IMPADD';
				$csv_data                        = get_option( 'rewardsystem_csv_array' );
				if ( ! srp_check_is_array( $csv_data ) ) {
					return;
				}

				$this->schedule_action( $csv_data, array( 'checkpoint' => $checkpoint ) );
				$redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback', 'rs_action_scheduler' => $this->get_id() ), SRP_ADMIN_URL ) );
				wp_safe_redirect( $redirect_url );
				exit;
			}
		}

		/*
		 * Chunked scheduler action.
		 */

		public function chunked_scheduler_action( $csv_data ) {

			if ( ! srp_check_is_array( $csv_data ) ) {
				return;
			}

			$settings_data = $this->get_settings_data();
			foreach ( $csv_data as $csv_data_value ) {

				if ( ! srp_check_is_array( $csv_data_value ) ) {
					continue;
				}

				$user_value = ( isset( $csv_data_value[ 0 ] ) && ! empty( $csv_data_value[ 0 ] ) ) ? $csv_data_value[ 0 ] : '';
				$points     = ( isset( $csv_data_value[ 1 ] ) && ! empty( $csv_data_value[ 1 ] ) ) ? $csv_data_value[ 1 ] : 0;
				$date       = ( isset( $csv_data_value[ 2 ] ) && ! empty( $csv_data_value[ 2 ] ) ) ? gmdate( 'm/d/Y h:i:s A T', $csv_data_value[ 2 ] ) : 999999999999;
				$date       = is_numeric( $date ) ? 999999999999 : strtotime( $date );

				$user = ! empty( get_user_by( 'login', $user_value ) ) ? get_user_by( 'login', $user_value ) : get_user_by( 'email', $user_value );
				if ( ! is_object( $user ) ) {
					continue;
				}

				if ( ! $points || ! $date || ! $user_value ) {
					continue;
				}

				$check_point = $settings_data[ 'checkpoint' ];
				if ( ! $check_point ) {
					continue;
				}

				if ( 'IMPOVR' == $check_point ) {
					global $wpdb;
					$table_name = "{$wpdb->prefix}rspointexpiry";
					$wpdb->delete( $table_name, array( 'userid' => $user->ID ) );
				}

				$table_args = array(
					'user_id'           => $user->ID,
					'pointstoinsert'    => $points,
					'checkpoints'       => $check_point,
					'totalearnedpoints' => $points,
					'date'              => $date,
				);

				RSPointExpiry::insert_earning_points( $table_args );
				RSPointExpiry::record_the_points( $table_args );
			}
		}
	}

}
