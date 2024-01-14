<?php

/**
 * Reports Module Export Points Action Scheduler.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'RS_Reports_Module_Export_Points' ) ) {

	/**
	 * Class.
	 * */
	class RS_Reports_Module_Export_Points extends SRP_Action_Scheduler {

		/**
		 * Class Constructor.
		 */
		public function __construct() {

			$this->id                            = 'rs_reports_module_export_points' ;
			$this->action_scheduler_name         = 'rs_reports_module_export_points' ;
			$this->chunked_action_scheduler_name = 'rs_chunked_reports_module_export_points_data' ;
			$this->option_name                   = 'rs_reports_module_export_points_data' ;
			$this->settings_option_name          = 'rs_reports_module_export_points_settings_args' ;

			// Do ajax action.
			add_action( 'wp_ajax_export_report' , array( $this, 'do_ajax_action' ) ) ;

			parent::__construct() ;
		}

		/*
		 * Get progress bar label.
		 */

		public function get_progress_bar_label() {
			$label = __( 'Exporting Report for User(s) is under process...' , 'rewardsystem' ) ;
			return $label ;
		}

		/**
		 * Get success message.
		 */
		public function get_success_message() {
			$msg = __( 'Exporting Report for User(s) Completed Successfully.' , 'rewardsystem' ) ;
			return $msg ;
		}

		/**
		 * Get settings URL.
		 */
		public function get_settings_url() {
			return add_query_arg( array( 'page' => 'rewardsystem_callback', 'tab' => 'fprsmodules', 'section' => 'fpreportsincsv' ) , SRP_ADMIN_URL ) ;
		}

		/**
		 * Get redirect URL.
		 */
		public function get_redirect_url() {
			return add_query_arg( array( 'page' => 'rewardsystem_callback', 'tab' => 'fprsmodules', 'section' => 'fpreportsincsv', 'export_report' => 'yes' ) , SRP_ADMIN_URL ) ;
		}

		/*
		 * Admin init.
		 */

		public function do_ajax_action() {
			check_ajax_referer( 'fp-export-report' , 'sumo_security' ) ;

			try {

				if ( ! isset( $_POST ) ) {
					throw new exception( esc_html__( 'Invalid Request' , 'rewardsystem' ) ) ;
				}

				delete_option( 'heading' ) ;
				delete_option( 'rs_export_report' ) ;
				$setting_values                           = array() ;
				$setting_values[ 'user_type' ]              = isset( $_POST[ 'usertype' ] ) ? wc_clean( wp_unslash( $_POST[ 'usertype' ] ) ) : '' ;
				$setting_values[ 'selected_users' ]         = isset( $_POST[ 'selecteduser' ] ) ? wc_clean( wp_unslash( $_POST[ 'selecteduser' ] ) ) : array() ;
				$setting_values[ 'selected_user_roles' ]    = isset( $_POST[ 'selected_user_roles' ] ) ? wc_clean( wp_unslash( $_POST[ 'selected_user_roles' ] ) ) : array() ;
				$setting_values[ 'expired_column_enabled' ] = isset( $_POST[ 'expired_column_enabled' ] ) ? wc_clean( wp_unslash( $_POST[ 'expired_column_enabled' ] ) ) : '' ;
				$setting_values[ 'expired_column_enabled' ] = true == $setting_values[ 'expired_column_enabled' ] ? 'yes' : 'no' ;
				$args                                       = array(
					'fields' => 'ids',
				) ;

				if ( '2' == $setting_values[ 'user_type' ] ) {
					if ( ! srp_check_is_array( $setting_values[ 'selected_users' ] ) ) {
						throw new exception( esc_html__( 'Selected User(s) data is empty' , 'rewardsystem' ) ) ;
					}

					$args[ 'include' ] = $setting_values[ 'selected_users' ] ;
				}

				if ( '3' == $setting_values[ 'user_type' ] ) {
					if ( ! srp_check_is_array( $setting_values[ 'selected_user_roles' ] ) ) {
						throw new exception( esc_html__( 'Selected User Role(s) data is empty' , 'rewardsystem' ) ) ;
					}

					$args[ 'role__in' ] = $setting_values[ 'selected_user_roles' ] ;
				}

				if ( 'yes' == get_option( 'rs_enable_reward_program' ) ) {
					$args[ 'meta_key' ]   = 'allow_user_to_earn_reward_points' ;
					$args[ 'meta_value' ] = 'yes' ;
				}

				$user_ids = get_users( $args ) ;
				if ( ! srp_check_is_array( $user_ids ) ) {
					throw new exception( esc_html__( 'No User(s) Found' , 'rewardsystem' ) ) ;
				}

				$this->schedule_action( $user_ids , $setting_values ) ;
				$redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback', 'rs_action_scheduler' => $this->get_id() ) , SRP_ADMIN_URL ) ) ;
				wp_send_json_success( array( 'redirect_url' => $redirect_url ) ) ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		/*
		 * Chunked scheduler action.
		 */

		public function chunked_scheduler_action( $user_ids ) {

			if ( ! srp_check_is_array( $user_ids ) ) {
				return ;
			}

			global $wpdb ;
			$data          = array() ;
			$settings_data = $this->get_settings_data() ;
			foreach ( $user_ids as $user_id ) {
				$user = get_user_by( 'ID' , $user_id ) ;
				if ( ! is_object( $user ) ) {
					continue ;
				}

				$date_type              = get_option( 'fp_date_type' ) ;
				$export_earned_points   = get_option( 'export_earn_points' ) ;
				$export_redeemed_points = get_option( 'export_redeem_points' ) ;
				$export_total_points    = get_option( 'export_total_points' ) ;
				$total_user_points      = array() ;
				$points_data            = new RS_Points_data( $user_id ) ;
				$expired_points         = 0 ;
				if ( '1' == $date_type ) {
					$earned_points   = $wpdb->get_col( $wpdb->prepare( "SELECT SUM(earnedpoints) FROM {$wpdb->prefix}rsrecordpoints WHERE userid = %d" , $user_id ) ) ;
					$redeemed_points = $wpdb->get_col( $wpdb->prepare( "SELECT SUM(redeempoints) FROM {$wpdb->prefix}rsrecordpoints WHERE userid = %d" , $user_id ) ) ;
					$expired_points  = isset( $settings_data[ 'expired_column_enabled' ] ) && 'yes' == $settings_data[ 'expired_column_enabled' ] ? $points_data->total_expired_points() : 0 ;
				} else {
					$start_date      = get_option( 'selected_report_start_date' ) ;
					$start_time      = strtotime( "$start_date 00:00:00" ) ;
					$end_date        = get_option( 'selected_report_end_date' ) ;
					$end_time        = strtotime( "$end_date 23:59:00" ) ;
					$earned_points   = $wpdb->get_col( $wpdb->prepare( "SELECT SUM(earnedpoints) FROM {$wpdb->prefix}rsrecordpoints WHERE userid = %d AND earneddate >= %d AND earneddate <= %d" , $user_id , $start_time , $end_time ) ) ;
					$redeemed_points = $wpdb->get_col( $wpdb->prepare( "SELECT SUM(redeempoints) FROM {$wpdb->prefix}rsrecordpoints WHERE userid = %d AND earneddate >= %d AND earneddate <= %d" , $user_id , $start_time , $end_time ) ) ;
				}

				$total_user_points[ $user_id ] = array( array_sum( $earned_points ), array_sum( $redeemed_points ) ) ;

				$available_points = $points_data->total_available_points() ;
				$headings         = array() ;
				if ( srp_check_is_array( $total_user_points ) ) {
					foreach ( $total_user_points as $id => $points ) {

						$points_earned       = isset( $points[ 0 ] ) ? round_off_type( $points[ 0 ] ) : 0 ;
						$points_redeemed     = isset( $points[ 1 ] ) ? round_off_type( $points[ 1 ] ) : 0 ;
						$headings[]          = 'Username' ;
						$data[ $user->ID ][] = ( '1' == get_option( 'selected_report_format' ) ) ? $user->user_login : $user->user_email ;

						if ( '1' == $export_earned_points ) {
							$headings[]          = 'Total Earned Points' ;
							$data[ $user->ID ][] = $points_earned ;
						}

						if ( '1' == $export_redeemed_points ) {
							$headings[]          = 'Total Redeemed Points' ;
							$data[ $user->ID ][] = $points_redeemed ;
						}

						if ( '1' == $export_total_points ) {
							$headings[]          = 'Available Points' ;
							$data[ $user->ID ][] = $available_points ;
						}

						if ( 'yes' == $settings_data[ 'expired_column_enabled' ] ) {
							$headings[]          = 'Total Expired Points' ;
							$data[ $user->ID ][] = $expired_points ;
						}

						$headings[] = "\n" ;
						update_option( 'heading' , implode( ',' , $headings ) ) ;
					}
				}
			}

			$old_data   = ( array ) get_option( 'rs_export_report' ) ;
			$merge_data = array_merge( $old_data , array_values( $data ) ) ;
			update_option( 'rs_export_report' , $merge_data ) ;
		}
	}

}
