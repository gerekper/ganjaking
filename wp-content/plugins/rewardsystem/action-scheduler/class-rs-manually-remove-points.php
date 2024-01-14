<?php

/**
 * Manually Remove Points Action Scheduler.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'RS_Manually_Remove_Points' ) ) {

	/**
	 * Class.
	 * */
	class RS_Manually_Remove_Points extends SRP_Action_Scheduler {

		/**
		 * Class Constructor.
		 */
		public function __construct() {

			$this->id                            = 'rs_manually_remove_points' ;
			$this->action_scheduler_name         = 'rs_manually_remove_points' ;
			$this->chunked_action_scheduler_name = 'rs_chunked_manually_remove_points' ;
			$this->option_name                   = 'rs_manually_remove_points_data' ;
			$this->settings_option_name          = 'rs_manually_remove_points_settings_args' ;
			// Do ajax action.
			add_action( 'wp_ajax_manually_remove_points_for_user' , array( $this, 'do_ajax_action' ) ) ;

			parent::__construct() ;
		}

		/*
		 * Get progress bar label.
		 */

		public function get_progress_bar_label() {
			$label = __( 'Manually Removing Points for User(s) is under process...' , 'rewardsystem' ) ;
			return $label ;
		}

		/**
		 * Get success message.
		 */
		public function get_success_message() {
			$msg = __( 'Manually Removing Points for User(s) Completed Successfully.' , 'rewardsystem' ) ;
			return $msg ;
		}

		/**
		 * Get redirect URL.
		 */
		public function get_redirect_url() {
			return add_query_arg( array( 'page' => 'rewardsystem_callback', 'tab' => 'fprsaddremovepoints' ) , SRP_ADMIN_URL ) ;
		}

		/*
		 * Do ajax action.
		 */

		public function do_ajax_action() {

			check_ajax_referer( 'fp-remove-points' , 'sumo_security' ) ;

			try {
				if ( ! isset( $_POST ) ) {
					throw new exception( __( 'Invalid data' , 'rewardsystem' ) ) ;
				}

				$email_subject  = isset( $_POST[ 'email_subject_to_remove_points' ] ) ? wp_kses_post( wp_unslash( $_POST[ 'email_subject_to_remove_points' ] ) ) : '' ;
				$email_message  = isset( $_POST[ 'email_message_to_remove_points' ] ) ? wp_kses_post( wp_unslash( $_POST[ 'email_message_to_remove_points' ] ) ) : '' ;
				$setting_values = array(
					'usertype'    => isset( $_POST[ 'usertype' ] ) ? wc_clean( wp_unslash( $_POST[ 'usertype' ] ) ) : '',
					'incuser'     => isset( $_POST[ 'includeuser' ] ) ? wc_clean( wp_unslash( $_POST[ 'includeuser' ] ) ) : '',
					'excuser'     => isset( $_POST[ 'excludeuser' ] ) ? wc_clean( wp_unslash( $_POST[ 'excludeuser' ] ) ) : '',
					'incuserrole' => isset( $_POST[ 'includeuserrole' ] ) ? wc_clean( wp_unslash( $_POST[ 'includeuserrole' ] ) ) : '',
					'excuserrole' => isset( $_POST[ 'excludeuserrole' ] ) ? wc_clean( wp_unslash( $_POST[ 'excludeuserrole' ] ) ) : '',
					'enablemail'  => isset( $_POST[ 'sendmail_to_remove_points' ] ) ? wc_clean( wp_unslash( $_POST[ 'sendmail_to_remove_points' ] ) ) : 'no',
					'subject'     => $email_subject,
					'message'     => $email_message,
					'email_type'  => isset( $_POST[ 'remove_points_email_type' ] ) ? absint( $_POST[ 'remove_points_email_type' ] ) : 1,
					'points'      => isset( $_POST[ 'points' ] ) ? wc_clean( wp_unslash( $_POST[ 'points' ] ) ) : 0,
					'reason'      => isset( $_POST[ 'reason' ] ) ? wc_clean( wp_unslash( $_POST[ 'reason' ] ) ) : '',
					'state'       => isset( $_POST[ 'state' ] ) ? wc_clean( wp_unslash( $_POST[ 'state' ] ) ) : 'no',
				) ;

				$args = array( 'fields' => 'ids' ) ;
				switch ( $setting_values[ 'usertype' ] ) {
					case '2':
						$args[ 'include' ]      = srp_check_is_array( $setting_values[ 'incuser' ] ) ? $setting_values[ 'incuser' ] : explode( ',' , $setting_values[ 'incuser' ] ) ;
						break ;
					case '3':
						$args[ 'exclude' ]      = srp_check_is_array( $setting_values[ 'excuser' ] ) ? $setting_values[ 'excuser' ] : explode( ',' , $setting_values[ 'excuser' ] ) ;
						break ;
					case '4':
						$args[ 'role__in' ]     = srp_check_is_array( $setting_values[ 'incuserrole' ] ) ? $setting_values[ 'incuserrole' ] : explode( ',' , $setting_values[ 'incuserrole' ] ) ;
						break ;
					case '5':
						$args[ 'role__not_in' ] = srp_check_is_array( $setting_values[ 'excuserrole' ] ) ? $setting_values[ 'excuserrole' ] : explode( ',' , $setting_values[ 'excuserrole' ] ) ;
						break ;
				}

				$user_ids = get_users( $args ) ;
				if ( ! srp_check_is_array( $user_ids ) ) {
					throw new exception( __( 'No User(s) Found' , 'rewardsystem' ) ) ;
				}

				update_option( 'rs_email_subject_for_remove' , $email_subject ) ;
				update_option( 'rs_email_message_for_remove' , $email_message ) ;
				$this->schedule_action( $user_ids , $setting_values ) ;
				$redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback', 'rs_action_scheduler' => $this->get_id() ) , SRP_ADMIN_URL ) ) ;
				wp_send_json_success( array( 'redirect_url' => $redirect_url ) ) ;
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) ) ;
			}
		}

		/*
		 * Chunked scheduler action.
		 */

		public function chunked_scheduler_action( $user_ids ) {

			if ( ! srp_check_is_array( $user_ids ) ) {
				return ;
			}

			$settings_data = $this->get_settings_data() ;
			foreach ( $user_ids as $user_id ) {
				$user = get_user_by( 'ID' , $user_id ) ;
				if ( ! is_object( $user ) ) {
					continue ;
				}

				if ( 'yes' != $settings_data[ 'state' ] ) {
					continue ;
				}

				$points = isset( $settings_data[ 'points' ] ) ? $settings_data[ 'points' ] : 0 ;
				if ( ! $points ) {
					continue ;
				}

				$PointsData = new RS_Points_data( $user_id ) ;
				$to         = $user->user_email ;
				if ( $points <= $PointsData->total_available_points() ) {
					RSPointExpiry::perform_calculation_with_expiry( $points , $user_id ) ;
					$table_args = array(
						'user_id'     => $user_id,
						'usedpoints'  => $points,
						'checkpoints' => 'MRP',
						'date'        => '999999999999',
						'reason'      => isset( $settings_data[ 'reason' ] ) ? $settings_data[ 'reason' ] : '',
					) ;
					RSPointExpiry::record_the_points( $table_args ) ;

					if ( ( 'true' == $settings_data[ 'enablemail' ] ) ) {
						$PointsData->reset( $user_id ) ;
						$finalmsg        = str_replace( array( '[rs_deleted_points]', '[balance_points]', '[site_name]' ) , array( $settings_data[ 'points' ], $PointsData->total_available_points(), get_option( 'blogname' ) ) , $settings_data[ 'message' ] ) ;
						$my_acccount_url = sprintf( '<a href="%s">%s</a>' , esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) , __( 'My Account' , 'rewardsystem' ) ) ;
						$finalmsg        = str_replace( array( '[username]', '[my_account_page]' ) , array( $user->user_login, $my_acccount_url ) , $finalmsg ) ;

						$headers = "MIME-Version: 1.0\r\n" ;
						$headers .= "Content-Type: text/html; charset=UTF-8\r\n" ;
						$headers .= 'From: ' . get_option( 'woocommerce_email_from_name' ) . ' <' . get_option( 'woocommerce_email_from_address' ) . ">\r\n" ;
						$headers .= 'Reply-To: ' . get_option( 'woocommerce_email_from_name' ) . ' <' . get_option( 'woocommerce_email_from_address' ) . ">\r\n" ;

						if ( '2' == $settings_data[ 'email_type' ] ) {
							// Plain Text Type.
							wp_mail( $to , $settings_data[ 'subject' ] , $finalmsg , $headers ) ;
						} else {
							// WC Template.
							global $unsublink2 ;
							$wpnonce    = wp_create_nonce( 'rs_unsubscribe_' . $user_id ) ;
							$unsublink  = esc_url_raw( add_query_arg( array( 'userid' => $user_id, 'unsub' => 'yes', 'nonce' => $wpnonce ) , site_url() ) ) ;
							$unsublink  = '<a href=' . $unsublink . '>' . $unsublink . '</a>' ;
							$unsublink2 = str_replace( '{rssitelinkwithid}' , $unsublink , get_option( 'rs_unsubscribe_link_for_email' ) ) ;
							add_filter( 'woocommerce_email_footer_text' , 'srp_footer_link' ) ;

							ob_start() ;
							wc_get_template( 'emails/email-header.php' , array( 'email_heading' => $settings_data[ 'subject' ] ) ) ;
							echo wp_kses_post( $finalmsg ) ;
							wc_get_template( 'emails/email-footer.php' ) ;

							$woo_temp_msg = ob_get_clean() ;

							if ( WC_VERSION <= ( float ) ( '2.2.0' ) ) {
								wp_mail( $to , $settings_data[ 'subject' ] , $woo_temp_msg , $headers = '' ) ;
							} else {
								$mailer = WC()->mailer() ;
								$mailer->send( $to , $settings_data[ 'subject' ] , $woo_temp_msg , $headers ) ;
							}
						}
					}
				}
			}
		}
	}

}
