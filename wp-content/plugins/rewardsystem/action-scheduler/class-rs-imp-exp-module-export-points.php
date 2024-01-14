<?php

/**
 * Import/Export Module Export Points Action Scheduler.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'RS_Imp_Exp_Module_Export_Points' ) ) {

	/**
	 * Class.
	 * */
	class RS_Imp_Exp_Module_Export_Points extends SRP_Action_Scheduler {

		/**
		 * Class Constructor.
		 */
		public function __construct() {

			$this->id                            = 'rs_imp_exp_module_export_points' ;
			$this->action_scheduler_name         = 'rs_imp_exp_module_export_points' ;
			$this->chunked_action_scheduler_name = 'rs_chunked_imp_exp_module_export_points_data' ;
			$this->option_name                   = 'rs_imp_exp_module_export_points_data' ;
			$this->settings_option_name          = 'rs_imp_exp_module_export_points_settings_args' ;

			// Do ajax action.
			add_action( 'wp_ajax_imp_exp_module_export_points' , array( $this, 'do_ajax_action' ) ) ;

			parent::__construct() ;
		}

		/*
		 * Get progress bar label.
		 */

		public function get_progress_bar_label() {
			$label = __( 'Exporting Points for User(s) is under process...' , 'rewardsystem' ) ;
			return $label ;
		}

		/**
		 * Get success message.
		 */
		public function get_success_message() {
			$msg = __( 'Exporting Points for User(s) Completed Successfully.' , 'rewardsystem' ) ;
			return $msg ;
		}

		/**
		 * Get settings URL.
		 */
		public function get_settings_url() {
			return add_query_arg(
					array(
				'page'    => 'rewardsystem_callback',
				'tab'     => 'fprsmodules',
				'section' => 'fpimportexport',
					) , SRP_ADMIN_URL
			) ;
		}

		/**
		 * Get redirect URL.
		 */
		public function get_redirect_url() {
			return add_query_arg(
					array(
				'page'          => 'rewardsystem_callback',
				'tab'           => 'fprsmodules',
				'section'       => 'fpimportexport',
				'export_points' => 'yes',
					) , SRP_ADMIN_URL
			) ;
		}

		/*
		 * Do ajax action.
		 */

		public function do_ajax_action() {
			check_ajax_referer( 'fp-export-points' , 'sumo_security' ) ;

			try {

				if ( ! isset( $_POST ) ) {
					throw new exception( esc_html__( 'Invalid Request' , 'rewardsystem' ) ) ;
				}

				delete_option( 'rs_data_to_impexp' ) ;
				$setting_values                          = array() ;
				$setting_values[ 'user_type' ]           = isset( $_POST[ 'usertype' ] ) ? wc_clean( wp_unslash( $_POST[ 'usertype' ] ) ) : '' ;
				$setting_values[ 'selected_users' ]      = isset( $_POST[ 'selecteduser' ] ) ? wc_clean( wp_unslash( $_POST[ 'selecteduser' ] ) ) : array() ;
				$setting_values[ 'selected_user_roles' ] = isset( $_POST[ 'selected_user_roles' ] ) ? wc_clean( wp_unslash( $_POST[ 'selected_user_roles' ] ) ) : array() ;

				$args = array(
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
				$redirect_url = esc_url_raw(
						add_query_arg(
								array(
					'page'                => 'rewardsystem_callback',
					'rs_action_scheduler' => $this->get_id(),
								) , SRP_ADMIN_URL
						)
				) ;
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

			$data_to_merge      = array() ;
			$thousand_separator = ! empty( wc_get_price_thousand_separator() ) ? wc_get_price_thousand_separator() : ',' ;
			foreach ( $user_ids as $user_id ) {
				$user = get_user_by( 'ID' , $user_id ) ;
				if ( ! is_object( $user ) ) {
					continue ;
				}

				$user_name   = ( '1' == get_option( 'selected_format' ) ) ? $user->user_login : $user->user_email ;
				$points_data = new RS_Points_Data( $user_id ) ;
				$where       = 'AND earnedpoints NOT IN(0)' ;
				$points_logs = $points_data->points_log_for_specific_user( $where ) ;
				if ( ! srp_check_is_array( $points_logs ) ) {
					continue ;
				}

				foreach ( $points_logs as $points_value ) {

					if ( '1' == get_option( 'fp_date_type_selection' ) ) {
						$validate = true ;
					} else {
						$start_date = get_option( 'selected_start_date' ) ;
						$from_date  = strtotime( "$start_date 00:00:00" ) ;
						$end_date   = get_option( 'selected_end_date' ) ;
						$end_date   = strtotime( "$end_date 23:59:00" ) ;
						$validate   = ( $from_date <= $points_value[ 'earneddate' ] && $end_date >= $points_value[ 'earneddate' ] ) ;
					}

					if ( ! $validate ) {
						continue ;
					}

					$overall_points  = $points_value[ 'earnedpoints' ] - $points_value[ 'usedpoints' ] ;
					$expiry_date     = ( 999999999999 != $points_value[ 'expirydate' ] ) ? date_display_format( $points_value[ 'expirydate' ] ) : '-' ;
					$data_to_merge[] = array(
						'user_name' => $user_name,
						'points'    => ( float ) str_replace( $thousand_separator , '' , round_off_type( $overall_points ) ),
						'date'      => $expiry_date,
					) ;
				}
			}

			$old_data     = ! empty( get_option( 'rs_data_to_impexp' ) ) ? get_option( 'rs_data_to_impexp' ) : array() ;
			$merged_array = array_merge( $old_data , $data_to_merge ) ;
			update_option( 'rs_data_to_impexp' , $merged_array ) ;
		}
	}

}
