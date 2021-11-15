<?php
/*
 * Admin Ajax
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'FP_Rewardsystem_Admin_Ajax' ) ) {

	/**
	 * FP_Rewardsystem_Admin_Ajax Class
	 */
	class FP_Rewardsystem_Admin_Ajax {

		/**
		 * FP_Rewardsystem_Admin_Ajax Class initialization
		 */
		public static function init() {
			$actions = array(
				'update_start_date'                        => false ,
				'update_end_date'                          => false ,
				'update_user_selection_format'             => false ,
				'update_date_type'                         => false ,
				'update_report_start_date'                 => false ,
				'update_report_end_date'                   => false ,
				'update_user_type'                         => false ,
				'update_selected_user'                     => false ,
				'update_report_date_type'                  => false ,
				'update_type_of_points'                    => false ,
				'updatestatusforemail'                     => false ,
				'updatestatusforemailexpiry'               => false ,
				'newemailexpirytemplate'                   => false ,
				'editemailexpirytemplate'                  => false ,
				'deletetemplateforemailexpiry'             => false ,
				'unsubscribeuser'                          => false ,
				'sendmail'                                 => false ,
				'newemailtemplate'                         => false ,
				'editemailtemplate'                        => false ,
				'deletetemplateforemail'                   => false ,
				'activatemodule'                           => false ,
				'fp_reset_settings'                        => false ,
				'fp_reset_users_data'                      => false ,
				'fp_reset_order_meta'                      => false ,
				'generatepointurl'                         => false ,
				'removepointurl'                           => false ,
				'add_wcf_fields'                           => false ,
				'wcf_field_type'                           => false ,
				'cus_field_search'                         => false ,
				'srp_user_search'                          => true ,
				'send_points_data'                         => true ,
				'enable_reward_program'                    => true ,
				'add_coupon_usage_reward_rule'             => false ,
				'add_user_purchase_history_rule'           => false ,
				'add_earning_percentage_rule'              => false ,
				'add_redeeming_percentage_rule'            => false ,
				'add_redeeming_user_purchase_history_rule' => false ,
				'add_manual_referral_link_rule'            => false ,
				'add_rule_for_range_based_earn_points'     => false ,
				'json_search_pages_and_posts'              => false ,
				'action_to_enable_disable_nominee'         => true  ,
					) ;

			foreach ( $actions as $action => $nopriv ) {
				add_action( 'wp_ajax_' . $action , array( __CLASS__ , $action ) ) ;

				if ( $nopriv ) {
					add_action( 'wp_ajax_nopriv_' . $action , array( __CLASS__ , $action ) ) ;
				}
			}
		}

		public static function update_start_date() {
			check_ajax_referer( 'fp-start-date' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'start_date' ] ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				delete_option( 'selected_start_date' ) ;
				update_option( 'selected_start_date' , wc_clean(wp_unslash($_POST[ 'start_date' ] ))) ;
				wp_send_json_success() ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function update_end_date() {
			check_ajax_referer( 'fp-end-date' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'end_date' ] ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				delete_option( 'selected_end_date' ) ;
				update_option( 'selected_end_date' , wc_clean(wp_unslash($_POST[ 'end_date' ])) ) ;
				wp_send_json_success() ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function update_user_selection_format() {
			check_ajax_referer( 'fp-user-selection' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'selected_format' ] ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				update_option( 'selected_format' , wc_clean(wp_unslash($_POST[ 'selected_format' ] ))) ;
				wp_send_json_success() ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function update_report_start_date() {
			check_ajax_referer( 'fp-start-date' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'export_report_startdate' ] ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				delete_option( 'selected_report_start_date' ) ;
				update_option( 'selected_report_start_date' , wc_clean(wp_unslash($_POST[ 'export_report_startdate' ])) ) ;
				wp_send_json_success() ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function update_report_end_date() {
			check_ajax_referer( 'fp-end-date' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'export_report_enddate' ] ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				delete_option( 'selected_report_end_date' ) ;
				update_option( 'selected_report_end_date' , wc_clean(wp_unslash( $_POST[ 'export_report_enddate' ])) ) ;
				wp_send_json_success() ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function update_user_type() {
			check_ajax_referer( 'fp-user-type' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'user_type' ] ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				update_option( 'selected_user_type_report' , wc_clean(wp_unslash($_POST[ 'user_type' ])) ) ;
				wp_send_json_success() ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function update_selected_user() {
			check_ajax_referer( 'fp-selected-user' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				$selected_users = isset($_POST[ 'selectedusers' ]) ? wc_clean(wp_unslash($_POST[ 'selectedusers' ])):array();
				$selected_users = srp_check_is_array( $selected_users ) ? $selected_users : explode( ',' , $selected_users ) ;
				update_option( 'rs_selected_user_list_export_report' , $selected_users ) ;
				wp_send_json_success() ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function update_report_date_type() {
			check_ajax_referer( 'fp-date-type' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'datetype' ] ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				update_option( 'fp_date_type' , wc_clean(wp_unslash($_POST[ 'datetype' ] ))) ;
				wp_send_json_success() ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function update_date_type() {
			check_ajax_referer( 'fp-date-type' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'datetype' ] ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				update_option( 'fp_date_type_selection' , wc_clean(wp_unslash( $_POST[ 'datetype' ])) ) ;
				wp_send_json_success() ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function update_type_of_points() {
			check_ajax_referer( 'fp-points-type' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				if ( isset( $_POST[ 'totalpoints' ] ) ) {
					delete_option( 'export_total_points' ) ;
					update_option( 'export_total_points' , wc_clean(wp_unslash($_POST[ 'totalpoints' ])) ) ;
				}
				if ( isset( $_POST[ 'earnpoints' ] ) ) {
					delete_option( 'export_earn_points' ) ;
					update_option( 'export_earn_points' , wc_clean(wp_unslash($_POST[ 'earnpoints' ])) ) ;
				}
				if ( isset( $_POST[ 'redeempoints' ] ) ) {
					delete_option( 'export_redeem_points' ) ;
					update_option( 'export_redeem_points' , wc_clean(wp_unslash($_POST[ 'redeempoints' ])) ) ;
				}
				wp_send_json_success() ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function updatestatusforemail() {
			check_ajax_referer( 'fp-update-status' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'row_id' ] ) || !isset($_POST[ 'status' ]) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				global $wpdb ;
								$TableName = "{$wpdb->prefix}rs_templates_email" ;
				$Status    = 'ACTIVE' == wc_clean(wp_unslash($_POST[ 'status' ] ))? 'NOTACTIVE' : 'ACTIVE' ;
				$wpdb->update( $TableName , array( 'rs_status' => $Status ) , array( 'id' => absint($_POST[ 'row_id' ]) ) ) ;
				wp_send_json_success( array( 'content' => $Status ) ) ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function updatestatusforemailexpiry() {
			check_ajax_referer( 'fp-update-status' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'row_id' ] ) || !isset($_POST[ 'status' ] )) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				global $wpdb ;
								$TableName = "{$wpdb->prefix}rs_expiredpoints_email" ;
				$Status    = 'ACTIVE' == wc_clean(wp_unslash($_POST[ 'status' ])) ? 'NOTACTIVE' : 'ACTIVE' ;
				$wpdb->update( $TableName , array( 'rs_status' => $Status ) , array( 'id' => absint($_POST[ 'row_id' ] )) ) ;
				wp_send_json_success( array( 'content' => $Status ) ) ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function newemailexpirytemplate() {
			check_ajax_referer( 'fp-new-template' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'templatename' ] ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}
						
			if (!isset($_POST[ 'senderoption' ]) || !isset($_POST[ 'fromname' ])  || !isset($_POST[ 'fromemail' ])  || !isset($_POST[ 'subject' ]) || !isset($_POST[ 'message' ])  || !isset($_POST[ 'noofdays' ])  || !isset($_POST[ 'templatestatus' ]) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				global $wpdb ;
								$TableName = "{$wpdb->prefix}rs_expiredpoints_email" ;
				$wpdb->insert( $TableName , array(
					'template_name' => wc_clean(wp_unslash( $_POST[ 'templatename' ] ) ),
					'sender_opt'    => wc_clean(wp_unslash( $_POST[ 'senderoption' ] )) ,
					'from_name'     => wc_clean(wp_unslash( $_POST[ 'fromname' ] ) ),
					'from_email'    => wc_clean(wp_unslash( $_POST[ 'fromemail' ] ) ),
					'subject'       => wc_clean(wp_unslash( $_POST[ 'subject' ] ) ),
					'message'       => wc_clean(wp_unslash( $_POST[ 'message' ] ) ),
					'noofdays'      => wc_clean(wp_unslash( $_POST[ 'noofdays' ] ) ),
					'rs_status'     => wc_clean(wp_unslash( $_POST[ 'templatestatus' ] )) ,
				) ) ;
				update_option( 'rs_new_template_id_for_expiry' , $wpdb->insert_id ) ;
				wp_send_json_success( array( 'content' => 'Settings Saved' ) ) ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function editemailexpirytemplate() {
			check_ajax_referer( 'fp-edit-template' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'templateid' ] ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}
						
			if (!isset($_POST[ 'templatename' ]) || !isset($_POST[ 'senderoption' ]) || !isset($_POST[ 'fromname' ])  || !isset($_POST[ 'fromemail' ])  || !isset($_POST[ 'subject' ]) || !isset($_POST[ 'message' ])  || !isset($_POST[ 'noofdays' ])  || !isset($_POST[ 'templatestatus' ]) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				global $wpdb ;
				$TableName = "{$wpdb->prefix}rs_expiredpoints_email" ;
				$wpdb->update( $TableName , array(
					'template_name' => wc_clean(wp_unslash( $_POST[ 'templatename' ] ) ),
					'sender_opt'    => wc_clean(wp_unslash( $_POST[ 'senderoption' ] )) ,
					'from_name'     => wc_clean(wp_unslash( $_POST[ 'fromname' ] ) ),
					'from_email'    => wc_clean(wp_unslash( $_POST[ 'fromemail' ] ) ),
					'subject'       => wc_clean(wp_unslash( $_POST[ 'subject' ] ) ),
					'message'       => wc_clean(wp_unslash( $_POST[ 'message' ] ) ),
					'noofdays'      => wc_clean(wp_unslash( $_POST[ 'noofdays' ] ) ),
					'rs_status'     => wc_clean(wp_unslash( $_POST[ 'templatestatus' ] ) ),
						) , array( 'id' => wc_clean(wp_unslash($_POST[ 'templateid' ])) ) ) ;
				wp_send_json_success( array( 'content' => 'Settings Updated' ) ) ;
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function deletetemplateforemailexpiry() {
			check_ajax_referer( 'fp-delete-template' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'row_id' ] ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				global $wpdb ;
				$TableName = "{$wpdb->prefix}rs_expiredpoints_email" ;
				$wpdb->delete( $TableName , array( 'id' => absint($_POST[ 'row_id' ]) ) ) ;
				wp_send_json_success() ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function unsubscribeuser() {
			check_ajax_referer( 'fp-unsubscribe-email' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'unsubscribe' ] ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
								$unsubscribe = wc_clean(wp_unslash($_POST[ 'unsubscribe' ]));
				if ( is_array( $unsubscribe ) ) {
					foreach ( $unsubscribe as $unsubscribeuser ) {
						$user_info        = get_userdata( $unsubscribeuser ) ;
						$headers          = "MIME-Version: 1.0\r\n" ;
						$headers          .= "Content-Type: text/html; charset=UTF-8\r\n" ;
						$headers          .= 'From: ' . get_option( 'woocommerce_email_from_name' ) . ' <' . get_option( 'woocommerce_email_from_address' ) . ">\r\n" ;
						$headers          .= 'Reply-To: ' . get_option( 'woocommerce_email_from_name' ) . ' <' . get_option( 'woocommerce_email_from_address' ) . ">\r\n" ;
						$emailsubject     = isset($_POST[ 'emailsubject' ]) ?wc_clean(wp_unslash($_POST[ 'emailsubject' ])):''  ;
						$findemailsubject = str_replace( '[sitename]' , get_option( 'blogname' ) , $emailsubject ) ;
						$message          = isset($_POST[ 'emailmessage' ] ) ? wc_clean(wp_unslash($_POST[ 'emailmessage' ])):'';
						update_option( 'rs_subject_for_user_unsubscribe' , $emailsubject) ;
						update_option( 'rs_message_for_user_unsubscribe' , $message) ;
						$subject          = $findemailsubject ;
						$to               = is_object( $user_info ) ? $user_info->user_email : '' ;
						update_user_meta( $unsubscribeuser , 'unsub_value' , 'yes' ) ;
						wp_mail( $to , $subject , $message , $headers ) ;
					}
				}
				wp_send_json_success() ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function newemailtemplate() {
			check_ajax_referer( 'fp-new-template' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'templatename' ] ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				global $wpdb ;
				$TableName = "{$wpdb->prefix}rs_templates_email" ;
								
								$send_mail_selected = isset( $_POST[ 'sendmailselected' ]) ? wc_clean(wp_unslash($_POST[ 'sendmailselected' ])):'';
				if ( ! is_array( $send_mail_selected ) ) {
					$send_mail_selected = explode( ',' , $send_mail_selected ) ;
				}

				$wpdb->insert( $TableName , array(
					'template_name'        => isset($_POST[ 'templatename' ]) ? wc_clean(wp_unslash( $_POST[ 'templatename' ] )) :'',
					'sender_opt'           => isset($_POST[ 'senderoption' ]) ? wc_clean(wp_unslash( $_POST[ 'senderoption' ] ) ):'',
					'from_name'            => isset($_POST[ 'fromname' ]) ? wc_clean(wp_unslash( $_POST[ 'fromname' ] ) ):'',
					'from_email'           => isset($_POST[ 'fromemail' ]) ? wc_clean(wp_unslash( $_POST[ 'fromemail' ] )):'' ,
					'subject'              => isset($_POST[ 'subject' ]) ? wc_clean(wp_unslash( $_POST[ 'subject' ] ) ):'',
					'message'              => isset($_POST[ 'message' ]) ? wp_kses_post(wp_unslash( $_POST[ 'message' ] ) ):'',
					'rs_status'            => isset($_POST[ 'templatestatus' ]) ? wc_clean(wp_unslash( $_POST[ 'templatestatus' ] )):'' ,
					'earningpoints'        => isset($_POST[ 'earningpoints' ]) ? wc_clean(wp_unslash( $_POST[ 'earningpoints' ] ) ):'',
					'redeemingpoints'      => isset($_POST[ 'redeemingpoints' ]) ? wc_clean(wp_unslash( $_POST[ 'redeemingpoints' ] )) :'',
					'mailsendingoptions'   => isset($_POST[ 'mailsendingoptions' ]) ? wc_clean(wp_unslash( $_POST[ 'mailsendingoptions' ] )):'' ,
					'rsmailsendingoptions' => isset($_POST[ 'rsmailsendingoptions' ]) ? wc_clean(wp_unslash( $_POST[ 'rsmailsendingoptions' ] )):'' ,
					'minimum_userpoints'   => isset($_POST[ 'minuserpoints' ]) ? wc_clean(wp_unslash( $_POST[ 'minuserpoints' ] )) :'',
					'sendmail_options'     => isset( $_POST[ 'sendmailoptions' ] ) ? wc_clean(wp_unslash( $_POST[ 'sendmailoptions' ]) ) : '' ,
					'sendmail_to'          => serialize($send_mail_selected)  ,
				) ) ;
				update_option( 'rs_new_template_id' , $wpdb->insert_id ) ;
				wp_send_json_success( array( 'content' => 'Settings Saved' ) ) ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function editemailtemplate() {
			check_ajax_referer( 'fp-edit-template' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'templateid' ] ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}
						
			if (! isset( $_POST[ 'templatename' ] ) || ! isset( $_POST[ 'senderoption' ] ) || ! isset( $_POST[ 'fromname' ] ) || ! isset( $_POST[ 'fromemail' ] ) || ! isset( $_POST[ 'subject' ] ) || ! isset( $_POST[ 'message' ] )) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}
						
			if (! isset( $_POST[ 'templatestatus' ] ) || ! isset( $_POST[ 'earningpoints' ] ) || ! isset( $_POST[ 'redeemingpoints' ] ) || ! isset( $_POST[ 'mailsendingoptions' ] ) || ! isset( $_POST[ 'rsmailsendingoptions' ] )) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}
						
			if (!isset( $_POST[ 'minuserpoints' ] )) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				global $wpdb ;
				$TableName = "{$wpdb->prefix}rs_templates_email" ;
				$wpdb->update( $TableName , array(
					'template_name'        => wc_clean(wp_unslash( $_POST[ 'templatename' ] ) ),
					'sender_opt'           => wc_clean(wp_unslash( $_POST[ 'senderoption' ] ) ),
					'from_name'            => wc_clean(wp_unslash( $_POST[ 'fromname' ] ) ),
					'from_email'           => wc_clean(wp_unslash( $_POST[ 'fromemail' ] ) ),
					'subject'              => wc_clean(wp_unslash( $_POST[ 'subject' ] ) ),
					'message'              => wp_kses_post(wp_unslash( $_POST[ 'message' ] ) ),
					'rs_status'            => wc_clean(wp_unslash( $_POST[ 'templatestatus' ] )) ,
					'earningpoints'        => wc_clean(wp_unslash( $_POST[ 'earningpoints' ] ) ),
					'redeemingpoints'      => wc_clean(wp_unslash( $_POST[ 'redeemingpoints' ] )) ,
					'mailsendingoptions'   => wc_clean(wp_unslash( $_POST[ 'mailsendingoptions' ] )) ,
					'rsmailsendingoptions' => wc_clean(wp_unslash( $_POST[ 'rsmailsendingoptions' ] )) ,
					'minimum_userpoints'   => wc_clean(wp_unslash( $_POST[ 'minuserpoints' ] )) ,
					'sendmail_options'     => isset( $_POST[ 'sendmailoptions' ] ) ? wc_clean(wp_unslash( $_POST[ 'sendmailoptions' ] ) ) : '' ,
					'sendmail_to'          => isset( $_POST[ 'sendmailselected' ] ) ? serialize( wc_clean(wp_unslash($_POST[ 'sendmailselected' ] ))) :array(),
						) , array( 'id' => wc_clean(wp_unslash($_POST[ 'templateid' ] ) )));
				wp_send_json_success( array( 'content' => 'Settings Updated' ) ) ;
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function sendmail() {
					
			check_ajax_referer( 'fp-send-mail' , 'sumo_security' ) ;
						
			if ( ! isset( $_POST ) || ! isset( $_POST[ 'email_id' ] ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				$to             = sanitize_email($_POST[ 'email_id' ]) ;
				$email_subject  = isset( $_POST[ 'rs_subject' ] ) ? wc_clean(wp_unslash( $_POST[ 'rs_subject' ] )) : '' ;
				$content        = 'Hi, You have earned X amount of points on this site which can be used for getting discount on future purchases. Thanks.' ;
				$templatestatus = isset( $_POST[ 'rs_status_template' ] ) ? wc_clean(wp_unslash( $_POST[ 'rs_status_template' ] )) : 'NOTACTIVE' ;
				$senderoption   = isset( $_POST[ 'rs_sender_options' ] ) ? wc_clean(wp_unslash( $_POST[ 'rs_sender_options' ] )) : 'woo' ;
				$from_name      = isset( $_POST[ 'rs_from_name' ] ) ? wc_clean(wp_unslash( $_POST[ 'rs_from_name' ] )) : '' ;
				$from_email     = isset( $_POST[ 'rs_from_email' ] ) ? wc_clean(wp_unslash( $_POST[ 'rs_from_email' ] )) : '' ;
				if ( 'ACTIVE' == $templatestatus ) {
					ob_start() ;
					wc_get_template( 'emails/email-header.php' , array( 'email_heading' => $email_subject ) ) ;
					echo wp_kses_post($content) ;
					wc_get_template( 'emails/email-footer.php' ) ;
					$msg_content = ob_get_clean() ;
					$headers     = "MIME-Version: 1.0\r\n" ;
					$headers     .= "Content-Type: text/html; charset=UTF-8\r\n" ;
					if ( 'local' == $senderoption ) {
						FPRewardSystem::$rs_from_email_address = $from_email ;
						FPRewardSystem::$rs_from_name          = $from_name ;
					}
					add_filter( 'woocommerce_email_from_address' , 'rs_alter_from_email_of_woocommerce' , 10 , 2 ) ;
					add_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 2 ) ;
					$mailer                                = WC()->mailer() ;
					if ( $mailer->send( $to , $email_subject , $msg_content , $headers ) ) {
						wp_send_json_success( array( 'content' => 'Mail Sent' ) ) ;
					}
					remove_filter( 'woocommerce_email_from_address' , 'rs_alter_from_email_of_woocommerce' , 10 , 2 ) ;
					remove_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 2 ) ;
					FPRewardSystem::$rs_from_email_address = false ;
					FPRewardSystem::$rs_from_name          = false ;
				}
				wp_send_json_success( array( 'content' => 'Mail Not Sent' ) ) ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function deletetemplateforemail() {
			check_ajax_referer( 'fp-delete-template' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'row_id' ] ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				global $wpdb ;
				$TableName = "{$wpdb->prefix}rs_templates_email" ;
				$wpdb->delete( $TableName , array( 'id' => absint($_POST[ 'row_id' ]) ) ) ;
				wp_send_json_success() ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function activatemodule() {
			check_ajax_referer( 'fp-activate-module' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'enable' ] ) || !isset($_POST[ 'metakey' ]) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				$metakey = wc_clean(wp_unslash($_POST[ 'metakey' ])) ;
				$enable  = wc_clean(wp_unslash($_POST[ 'enable' ])) ;
				update_option( $metakey , $enable ) ;
				wp_send_json_success() ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function fp_reset_settings() {
			check_ajax_referer( 'rs-reset-tab' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				foreach ( RSGeneralTabSetting::reward_system_admin_fields() as $setting ) {
					if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
						delete_option( $setting[ 'newids' ] ) ;
						add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
						update_option( 'rs_earn_point' , '1' ) ;
						update_option( 'rs_earn_point_value' , '1' ) ;
						update_option( 'rs_redeem_point' , '1' ) ;
						update_option( 'rs_redeem_point_value' , '1' ) ;
						update_option( 'rs_redeem_point_for_cash_back' , '1' ) ;
						update_option( 'rs_redeem_point_value_for_cash_back' , '1' ) ;
					}
				}

				foreach ( RSAddorRemovePoints::reward_system_admin_fields() as $setting ) {
					if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
						delete_option( $setting[ 'newids' ] ) ;
						add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
					}
				}

				foreach ( RSProductPurchaseModule::reward_system_admin_fields() as $setting ) {
					if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
						delete_option( $setting[ 'newids' ] ) ;
						add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
					}
				}

				foreach ( RSReferralSystemModule::reward_system_admin_fields() as $setting ) {
					if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
						delete_option( $setting[ 'newids' ] ) ;
						add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
					}
				}

				foreach ( RSRewardPointsForAction::reward_system_admin_fields() as $setting ) {
					if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
						delete_option( $setting[ 'newids' ] ) ;
						add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
					}
				}

				foreach ( RSPointExpiryModule::reward_system_admin_fields() as $setting ) {
					if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
						delete_option( $setting[ 'newids' ] ) ;
						add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
					}
				}

				foreach ( RSRedeemingModule::reward_system_admin_fields() as $setting ) {
					if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
						delete_option( $setting[ 'newids' ] ) ;
						add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
					}
				}

				foreach ( RSPointPriceModule::reward_system_admin_fields() as $setting ) {
					if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
						delete_option( $setting[ 'newids' ] ) ;
						add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
					}
				}

				foreach ( RSEmailModule::reward_system_admin_fields() as $setting ) {
					if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
						delete_option( $setting[ 'newids' ] ) ;
						add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
					}
				}

				foreach ( RSGiftVoucher::reward_system_admin_fields() as $setting ) {
					if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
						delete_option( $setting[ 'newids' ] ) ;
						add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
					}
				}

				foreach ( RSMessage::reward_system_admin_fields() as $setting ) {
					if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
						delete_option( $setting[ 'newids' ] ) ;
						add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
					}
				}

				foreach ( RSSocialReward::reward_system_admin_fields() as $setting ) {
					if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
						delete_option( $setting[ 'newids' ] ) ;
						add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
					}
				}

				foreach ( RSSms::reward_system_admin_fields() as $setting ) {
					if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
						delete_option( $setting[ 'newids' ] ) ;
						add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
					}
				}

				foreach ( RSCashbackModule::reward_system_admin_fields() as $setting ) {
					if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
						delete_option( $setting[ 'newids' ] ) ;
						add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
					}
				}

				foreach ( RSPointURL::reward_system_admin_fields() as $setting ) {
					if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
						delete_option( $setting[ 'newids' ] ) ;
						add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
					}
				}

				foreach ( RSGatewayModule::reward_system_admin_fields() as $setting ) {
					if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
						delete_option( $setting[ 'newids' ] ) ;
						add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
					}
				}

				foreach ( RSSendPointsModule::reward_system_admin_fields() as $setting ) {
					if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
						delete_option( $setting[ 'newids' ] ) ;
						add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
					}
				}

				foreach ( RSLocalization::reward_system_admin_fields() as $setting ) {
					if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
						delete_option( $setting[ 'newids' ] ) ;
						add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
					}
				}

				foreach ( RSAdvancedSetting::reward_system_admin_fields() as $setting ) {
					if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
						delete_option( $setting[ 'newids' ] ) ;
						add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
					}
				}

				foreach ( RSDiscountsCompatability::reward_system_admin_fields() as $setting ) {
					if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
						delete_option( $setting[ 'newids' ] ) ;
						add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
					}
				}

				foreach ( RSCouponCompatability::reward_system_admin_fields() as $setting ) {
					if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
						delete_option( $setting[ 'newids' ] ) ;
						add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
					}
				}

				delete_option( 'rewards_dynamic_rule_couponpoints' ) ;

				foreach ( RSNominee::reward_system_admin_fields() as $setting ) {
					if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
						delete_option( $setting[ 'newids' ] ) ;
						add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
					}
				}

				delete_option( 'rewards_dynamic_rule_manual' ) ;

				delete_transient( 'woocommerce_cache_excluded_uris' ) ;

				wp_send_json_success() ;
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function fp_reset_users_data() {
			check_ajax_referer( 'reset-data-for-user' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'resetdatafor' ] ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				global $wpdb ;
				$db = &$wpdb;
				$PointsTable    = $db->prefix . 'rspointexpiry' ;
				$PointsLogTable = $db->prefix . 'rsrecordpoints' ;
				$UsermetaTable  = $db->prefix . 'usermeta' ;
				$UserIDs        = array() ;
				$OrderId        = array() ;

				if ( isset( $_POST[ 'resetpreviousorder' ] ) && 1 == wc_clean(wp_unslash($_POST[ 'resetpreviousorder' ] ))) {
					$args    = array( 'post_type' => 'shop_order' , 'numberposts' => '-1' , 'meta_query' => array( 'relation' => 'AND' , array( 'key' => 'reward_points_awarded' , 'compare' => 'EXISTS' ) , array( 'key' => 'earning_point_once' , 'compare' => 'EXISTS' ) ) , 'post_status' => 'published' , 'fields' => 'ids' , 'cache_results' => false ) ;
					$OrderId = get_posts( $args ) ;
				}

				if ( isset( $_POST[ 'resetmanualreferral' ] ) && 1 == wc_clean(wp_unslash($_POST[ 'resetmanualreferral' ])) ) {
					delete_option( 'rewards_dynamic_rule_manual' , true ) ;
				}

				$ResetUserPoints  = isset( $_POST[ 'rsresetuserpoints' ] ) ? wc_clean(wp_unslash($_POST[ 'rsresetuserpoints' ])) : '' ;
				$ResetUserLogs    = isset( $_POST[ 'rsresetuserlogs' ] ) ? wc_clean(wp_unslash($_POST[ 'rsresetuserlogs' ] )): '' ;
				$ResetMasterLogs  = isset( $_POST[ 'rsresetmasterlogs' ] ) ? wc_clean(wp_unslash($_POST[ 'rsresetmasterlogs' ])) : '' ;
				$ResetReferrallog = isset( $_POST[ 'resetreferrallog' ] ) ? wc_clean(wp_unslash($_POST[ 'resetreferrallog' ])) : '' ;

				if ( '2' == wc_clean(wp_unslash($_POST[ 'resetdatafor' ])) && isset( $_POST[ 'rsselectedusers' ] ) ) {   //Selected User                    
					$selected_users = isset($_POST[ 'rsselectedusers' ]) ? wc_clean(wp_unslash($_POST[ 'rsselectedusers' ])):'';
										$UserIDs = ! is_array( $selected_users ) ? explode( ',' , $selected_users ) : $selected_users ;
					if ( srp_check_is_array( $UserIDs ) && '1' == $ResetReferrallog ) {
						foreach ( $UserIDs as $UserId ) {
							$ReferralLogs = get_option( 'rs_referral_log' ) ;
							if ( ! isset( $ReferralLogs[ $UserId ] ) ) {
								continue ;
							}

							unset( $ReferralLogs[ $UserId ] ) ;
							update_option( 'rs_referral_log' , $ReferralLogs ) ;
						}
					}
				} else {
					$UserIDs = get_users(array('fields'=>'ids')) ;
				}
				$UserIDs = implode( ',' , $UserIDs ) ;
				if ( '1' == $ResetUserPoints ) {
					$db->query( "DELETE FROM $PointsTable WHERE userid IN ($UserIDs)" ) ;
					$db->query( "DELETE FROM $UsermetaTable WHERE meta_key IN ('_my_reward_points','rs_earned_points_before_delete','rs_user_total_earned_points','rs_expired_points_before_delete','rs_redeem_points_before_delete') AND user_id IN ($UserIDs)" ) ;
				}
				if ( '1' == $ResetUserLogs ) {
					$db->query( "UPDATE $PointsLogTable SET showuserlog = true WHERE userid IN ($UserIDs)" ) ;
					$db->query( "DELETE FROM $UsermetaTable WHERE meta_key IN ('_my_points_log') AND user_id IN ($UserIDs)" ) ;
				}
				if ( '1' == $ResetMasterLogs  ) {
					$db->query( "UPDATE $PointsLogTable SET showmasterlog = true WHERE userid IN ($UserIDs)" ) ;
					delete_option( 'rsoveralllog' ) ;
				}
				if ( '1' == $ResetReferrallog ) {
					delete_option( 'rs_referral_log' , true ) ;
				}

				$reset_record_table_log = isset( $_POST[ 'resetrecordlogtable' ] ) ? wc_clean( wp_unslash( $_POST[ 'resetrecordlogtable' ] ) ) : '' ;
				$reset_type             = isset( $_POST[ 'resetdatafor' ] ) ? wc_clean( wp_unslash( $_POST[ 'resetdatafor' ] ) ) : wc_clean( wp_unslash( $_POST[ 'resetdatafor' ] ) ) ;
				$UserIDs                = ! is_array( $_POST[ 'rsselectedusers' ] ) ? explode( ',' , wc_clean( wp_unslash( $_POST[ 'rsselectedusers' ] ) ) ) : wc_clean( wp_unslash( $_POST[ 'rsselectedusers' ] ) ) ;

				if ( '1' == $reset_type ) {
					$db->query( "TRUNCATE TABLE $PointsLogTable" ) ;
				} else {
					if ( srp_check_is_array( array_filter( ( array ) ( $UserIDs ) ) ) && $reset_record_table_log ) {
						$db->query( "DELETE FROM $PointsTable WHERE userid IN ($UserIDs)" ) ;
					}
				}

				wp_send_json_success( array( 'content' => $OrderId ) ) ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function fp_reset_order_meta() {
			check_ajax_referer( 'reset-previous-order-meta' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'ids' ] ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				if ( 'done' == absint($_POST[ 'ids' ]) ) {
					wp_send_json_success( array( 'content' => 'success' ) ) ;
				}

				$order = absint($_POST[ 'ids' ]) ;
				foreach ( $order as $order_id ) {
					delete_post_meta( $order_id , 'reward_points_awarded' ) ;
					delete_post_meta( $order_id , 'earning_point_once' ) ;
				}
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function srp_user_search() {
			check_ajax_referer( 'fp-user-search' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				$display_user    = array() ;
								$term = isset($_POST[ 'term' ]) ? wc_clean(wp_unslash($_POST[ 'term' ] )):'';
				$customers_query = new WP_User_Query( array(
					'fields'         => 'all' ,
					'orderby'        => 'display_name' ,
					'search'         => "*$term*" ,
					'search_columns' => array( 'ID' , 'user_login' , 'user_email' , 'user_nicename' )
						) ) ;
				$customers       = $customers_query->get_results() ;
				$current_user_id = get_current_user_id() ;
				if ( '1' == get_option( 'rs_select_send_points_user_type' ) ) {
					if ( ! empty( $customers ) ) {
						foreach ( $customers as $customer ) {
							if ( $customer->ID != $current_user_id ) {
								$display_user[ $customer->ID ] = $customer->display_name . ' (#' . $customer->ID . ' - ' . sanitize_email( $customer->user_email ) . ')' ;
							}
						}
					}
				}
				if ( '2' == get_option( 'rs_select_send_points_user_type' ) ) {
					if ( '' != get_option( 'rs_select_users_list_for_send_point' ) ) {
						$userids      = ! is_array( get_option( 'rs_select_users_list_for_send_point' ) ) ? array_filter( array_map( 'absint' , ( array ) explode( ',' , get_option( 'rs_select_users_list_for_send_point' ) ) ) ) : get_option( 'rs_select_users_list_for_send_point' ) ;
						$display_user = self::display_select_field( $userids , $customers , $current_user_id ) ;
					}
				}
				wp_send_json( $display_user ) ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function display_select_field( $userids, $customers, $current_user_id ) {
			$found_customers = array() ;
			if ( ! empty( $customers ) ) {
				foreach ( $customers as $customer ) {
					if ( $customer->ID != $current_user_id  ) {
						if ( in_array( $customer->ID , $userids ) ) {
							$found_customers[ $customer->ID ] = $customer->display_name . ' (#' . $customer->ID . ' - ' . sanitize_email( $customer->user_email ) . ')' ;
						}
					}
				}
			}
			return $found_customers ;
		}

		public static function send_points_data() {
			check_ajax_referer( 'fp-send-points-data' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'points' ] ) || ! isset( $_POST[ 'receiver_info' ] ) || ( '' == wc_clean(wp_unslash($_POST[ 'receiver_info' ])) ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}
						
			if (!isset($_POST[ 'senderid' ]) || !isset($_POST[ 'sendername' ]) || !isset($_POST[ 'senderpoints' ]) || !isset($_POST[ 'reason' ])) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				global $wpdb ;
				global $woocommerce ;
				$ApprovalType  = get_option( 'rs_request_approval_type' ) ;
				$SenderId      = absint($_POST[ 'senderid' ]) ;
				$SenderEmail   = get_userdata( $SenderId )->user_email ;
				$SenderName    = wc_clean(wp_unslash($_POST[ 'sendername' ])) ;
				$Points        = wc_clean(wp_unslash($_POST[ 'points' ])) ;
				$Receiver_info = wc_clean(wp_unslash($_POST[ 'receiver_info' ] ));

				if ( '1' == get_option( 'rs_send_points_user_selection_field' , 1 ) ) {
					$user = get_user_by( 'ID' , $Receiver_info ) ;
				} else {
					$user = is_object( get_user_by( 'login' , $Receiver_info ) ) ? get_user_by( 'login' , $Receiver_info ) : get_user_by( 'email' , $Receiver_info ) ;
				}

				if ( ! is_object( $user ) ) {
					throw new Exception( 'invalid_username_error' ) ;
				}

				$ReceiverId = $user->ID ;
				if ( get_current_user_id() == $ReceiverId ) {
					throw new Exception( esc_html__( 'Invalid User' , 'rewardsystem' ) ) ;
				}

				if ( '2' == get_option( 'rs_select_send_points_user_type' ) && ! in_array( $ReceiverId , ( array ) get_option( 'rs_select_users_list_for_send_point' ) ) ) {
					throw new Exception( 'restricted_username_error' ) ;
				}

				$ReceiverName      = $user->user_login ;
				$ReceiverFirstName = $user->first_name ;
				$ReceiverLastName  = $user->last_name ;
				$ReceiverEmail     = $user->user_email ;
								
								$status = isset($_POST[ 'status' ]) ? wc_clean(wp_unslash($_POST[ 'status' ])):'';
				$Status = ( '1' == $ApprovalType ) ?$status : 'Paid' ;
				update_option( 'rs_reason_for_send_points_mail' , wc_clean(wp_unslash($_POST[ 'reason' ])) ) ;
				if ( 'yes' == get_option( 'rs_mail_for_send_points_notification_admin' ) ) {
					$Email_subject = get_option( 'rs_email_subject_for_send_points_notification_admin' ) ;
					$Email_message = get_option( 'rs_email_message_for_send_points_notification_admin' ) ;
					$message       = str_replace( array( '[sender]' , '[receiver]' , '[points]' ) , array( $SenderName , $ReceiverName , $Points ) , $Email_message ) ;
					if ( 'woocommerce' == get_option( 'rs_mail_sender_for_admin' ) ) {
						$admin_email = get_option( 'admin_email' ) ;
						$admin_name  = get_bloginfo( 'name' , 'display' ) ;
					} else {
						$admin_email = get_option( 'rs_from_email_for_sendpoints_for_admin' ) ;
						$admin_name  = get_option( 'rs_from_name_for_sendpoints_for_admin' ) ;
					}
					$headers = "MIME-Version: 1.0\r\n" ;
					$headers .= "Content-Type: text/html; charset=UTF-8\r\n" ;
					$headers .= 'Reply-To: ' . $admin_name . ' <' . $admin_email . ">\r\n" ;

					$ReplaceValue = ( '1' == $ApprovalType ) ? array( 'Manual Approval' , 'Still Waiting' ) : array( 'Auto Approval' , 'Accepted' ) ;
					$AdminMsg     = str_replace( array( '[Type]' , '[request_status]' ) , $ReplaceValue , $message ) ;
					$AdminMsg     = do_shortcode( $AdminMsg ) ;
					ob_start() ;
					wc_get_template( 'emails/email-header.php' , array( 'email_heading' => $Email_subject ) ) ;
					echo wp_kses_post($AdminMsg) ;
					wc_get_template( 'emails/email-footer.php' ) ;
					$woo_temp_msg = ob_get_clean() ;
					if ( ( float ) $woocommerce->version <= ( float ) ( '2.2.0' ) ) {
						wp_mail( $admin_email , $Email_subject , $AdminMsg , $headers ) ;
					} else {
						$mailer = WC()->mailer() ;
						$mailer->send( $admin_email , $Email_subject , $woo_temp_msg , $headers ) ;
					}
				}
				if ('2' ==  $ApprovalType ) {
					if ( 'yes' == get_option( 'rs_mail_for_send_points_for_user' ) ) {
						$email_subject                = get_option( 'rs_email_subject_for_send_points' ) ;
						$email_message                = get_option( 'rs_email_message_for_send_points' ) ;
						$message                      = str_replace( array( '[rs_sendpoints]' , '[specific_user]' , '[user_name]' ) , array( $Points , $SenderName , $ReceiverName ) , $email_message ) ;
												$reason                       = isset($_POST[ 'reason' ]) ? wc_clean(wp_unslash($_POST[ 'reason' ])):'';
						$Email_message                = str_replace( array( '[status]' , '[reason_message]' , '[rsfirstname]' , '[rslastname]' ) , array( 'Accepted' , $reason , $ReceiverFirstName , $ReceiverLastName ) , $message ) ;
						$Email_message                = do_shortcode( $Email_message ) ;
						add_filter( 'woocommerce_email_footer_text' , 'srp_footer_link' ) ;
						ob_start() ;
						wc_get_template( 'emails/email-header.php' , array( 'email_heading' => $email_subject ) ) ;
						echo wp_kses_post($Email_message) ;
						wc_get_template( 'emails/email-footer.php' ) ;
						$woo_temp_msg                 = ob_get_clean() ;
						$headers                      = "MIME-Version: 1.0\r\n" ;
						$headers                      .= "From: \"{$SenderName}\" <{$SenderEmail}>\n" . 'Content-Type: text/html; charset="' . get_option( 'blog_charset' ) . "\"\n" ;
						$headers                      .= 'Reply-To: ' . $ReceiverName . ' <' . $ReceiverEmail . ">\r\n" ;
						FPRewardSystem::$rs_from_name = $SenderName ;
						add_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 2 ) ;
						if ( ( float ) $woocommerce->version <= ( float ) ( '2.2.0' ) ) {
							wp_mail( $ReceiverEmail , $email_subject , $Email_message , $headers ) ;
						} else {
							$mailer = WC()->mailer() ;
							$mailer->send( $ReceiverEmail , $email_subject , $woo_temp_msg , $headers ) ;
						}
						remove_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 1 ) ;
					}
					if ( 'yes' == get_option( 'rs_mail_for_send_points_confirmation_mail_for_user' )) {
						$email_subject = get_option( 'rs_email_subject_for_send_points_confirmation' ) ;
						$email_message = get_option( 'rs_email_message_for_send_points_confirmation' ) ;
						$message       = str_replace( array( '[user_name]' , '[request]' , '[points]' , '[receiver_name]' ) , array( $SenderName , 'Accepted' , $Points , $ReceiverName ) , $email_message ) ;
						$Email_message = str_replace( array( '[status]' , '[reason_message]' , '[rsfirstname]' , '[rslastname]' ) , array( 'Accepted' , wc_clean(wp_unslash($_POST[ 'reason' ])) , $ReceiverFirstName , $ReceiverLastName ) , $message ) ;
						$Email_message = do_shortcode( $Email_message ) ;
						if ( 'woocommerce' == get_option( 'rs_mail_sender_for_admin' ) ) {
							$admin_email = get_option( 'admin_email' ) ;
							$admin_name  = get_bloginfo( 'name' , 'display' ) ;
						} else {
							$admin_email = get_option( 'rs_from_email_for_sendpoints_for_admin' ) ;
							$admin_name  = get_option( 'rs_from_name_for_sendpoints_for_admin' ) ;
						}
						add_filter( 'woocommerce_email_footer_text' , 'srp_footer_link' ) ;
						ob_start() ;
						wc_get_template( 'emails/email-header.php' , array( 'email_heading' => $email_subject ) ) ;
						echo wp_kses_post($Email_message) ;
						wc_get_template( 'emails/email-footer.php' ) ;
						$woo_temp_msg                 = ob_get_clean() ;
						$headers                      = "MIME-Version: 1.0\r\n" ;
						$headers                      .= "From: \"{$admin_name}\" <{$admin_email}>\n" . 'Content-Type: text/html; charset="' . get_option( 'blog_charset' ) . "\"\n" ;
						$headers                      .= 'Reply-To: ' . $SenderName . ' <' . $SenderEmail . ">\r\n" ;
						FPRewardSystem::$rs_from_name = $admin_name ;
						add_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 2 ) ;
						if ( ( float ) $woocommerce->version <= ( float ) ( '2.2.0' ) ) {
							wp_mail( $SenderEmail , $email_subject , $Email_message , $headers ) ;
						} else {
							$mailer = WC()->mailer() ;
							$mailer->send( $SenderEmail , $email_subject , $woo_temp_msg , $headers ) ;
						}
						remove_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 1 ) ;
					}
					$table_args = array(
						'user_id'           => $ReceiverId ,
						'pointstoinsert'    => $Points ,
						'checkpoints'       => 'SP' ,
						'totalearnedpoints' => $Points ,
						'nomineeid'         => $SenderId ,
							) ;
					RSPointExpiry::insert_earning_points( $table_args ) ;
					RSPointExpiry::record_the_points( $table_args ) ;
				}
				$wpdb->insert( "{$wpdb->prefix}sumo_reward_send_point_submitted_data" , array( 'userid' => $SenderId , 'userloginname' => $SenderName , 'pointstosend' => $Points , 'sendercurrentpoints' => wc_clean(wp_unslash($_POST[ 'senderpoints' ] )), 'status' => $Status , 'selecteduser' => $ReceiverId , 'date' => date_i18n( 'Y-m-d H:i:s' ) ) ) ;
				$redeempoints = RSPointExpiry::perform_calculation_with_expiry( $Points , $SenderId ) ;
				$table_args   = array(
					'user_id'     => $SenderId ,
					'usedpoints'  => $Points ,
					'checkpoints' => ( '1' == $ApprovalType ) ? 'SPB' : 'SENPM' ,
					'nomineeid'   => $ReceiverId ,
						) ;

				RSPointExpiry::record_the_points( $table_args ) ;

				wp_send_json_success( array( 'content' => 'success' ) ) ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function generatepointurl() {
			check_ajax_referer( 'fp-generate-url' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'points' ] ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				$NewArr       = array( uniqid() => wc_clean(wp_unslash($_POST ))) ;
				$PreValue     = get_option( 'points_for_url_click' ) ;
				$UpdatedValue = srp_check_is_array( $PreValue ) ? array_merge( $PreValue , $NewArr ) : $NewArr ;
				update_option( 'points_for_url_click' , $UpdatedValue ) ;
				wp_send_json_success() ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function removepointurl() {
			check_ajax_referer( 'fp-remove-url' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'uniqueid' ] ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				$PreValue = get_option( 'points_for_url_click' ) ;
				if ( srp_check_is_array( $PreValue ) ) {
					if ( array_key_exists( wc_clean(wp_unslash($_POST[ 'uniqueid' ])) , $PreValue ) ) {
						unset( $PreValue[ wc_clean(wp_unslash($_POST[ 'uniqueid' ])) ] ) ;
					}
				}

				$PreValue = array_filter( $PreValue ) ;
				update_option( 'points_for_url_click' , $PreValue ) ;
				wp_send_json_success() ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function enable_reward_program() {
			check_ajax_referer( 'earn-reward-points' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'enable_reward_points' ] ) ) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				update_user_meta( get_current_user_id() , 'allow_user_to_earn_reward_points' , wc_clean(wp_unslash($_POST[ 'enable_reward_points' ] ))) ;
				wp_send_json_success() ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function add_wcf_fields() {
			check_ajax_referer( 'srp-cus-reg-fields-nonce' , 'sumo_security' ) ;

			try {
				if ( ! isset( $_POST ) || ! isset( $_POST[ 'count' ] ) ) {
					throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
				}

				ob_start() ;
				$key     = absint($_POST[ 'count' ]) ;
								$contents = '.fp-srp-custom-field-points{
                                        width:75% !important;
                                }' ;
																
				wp_register_style( 'fp-srp-wcf-style' , false , array() , SRP_VERSION ) ; // phpcs:ignore
				wp_enqueue_style( 'fp-srp-wcf-style' ) ;
				wp_add_inline_style( 'fp-srp-wcf-style' , $contents ) ; 
								
				?>                
				<tr class="rs_rule_creation_for_custom_reg_field">
				<input type="hidden" id="rs_rule_id_for_custom_reg_field" value="<?php echo esc_attr($key) ; ?>"/>
				<td class="column-columnname">
					<?php
					$args    = array(
						'class'              => 'wc-product-search rs_search_custom_field' ,
						'id'                 => 'rs_rule_for_custom_reg_field[' . $key . '][custom_fields]' ,
						'name'               => 'rs_rule_for_custom_reg_field[' . $key . '][custom_fields]' ,
						'type'               => 'customfields' ,
						'action'             => 'cus_field_search' ,
						'multiple'           => false ,
						'css'                => 'width: 100%;' ,
						'placeholder'        => 'Select Custom Fields' ,
						'options'            => array() ,
						'translation_string' => 'rewardsystem'
							) ;
					rs_custom_search_fields( $args ) ;
					?>
				</td>
				<td class="column-columnname">
					<p class="rs_label_for_cus_field_type"></p>
					<input type="hidden" class="rs_label_for_cus_field_type_hidden" name="rs_rule_for_custom_reg_field[<?php echo esc_attr($key) ; ?>][field_type]" value=""/>
				</td>
				<td class="column-columnname">
									<input class="fp-srp-custom-field-points" type="number" name="rs_rule_for_custom_reg_field[<?php echo esc_attr($key) ; ?>][reward_points]" min="0" value=""/>
				</td>
				<td class="column-columnname">
					<p class="rs_label_for_datepicker_type"></p>
				</td>
				<td class="column-columnname">
					<p class="rs_label_award_points_for_filling_datepicker"></p>
				</td>
				<td class="column-columnname">
					<span class="rs_remove_rule_for_custom_reg_field button-primary"><?php esc_html_e( 'Remove Rule' , 'rewardsystem' ) ; ?></span>
				</td>
				</tr>
				<?php
				$content = ob_get_clean() ;
				ob_end_clean() ;
				wp_send_json_success( array( 'content' => $content ) ) ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function cus_field_search() {
					
			if (!isset($_GET[ 'term' ])) {
				throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
			}
					
			try {
				global $wpdb ;
				$listofcusfields = array() ;
				$data            = $wpdb->get_results( $wpdb->prepare( "SELECT ID as id, post_title as title FROM $wpdb->posts WHERE post_type=%s AND post_status=%s And post_title  LIKE %s" , 'fpcf_custom_fields' , 'fpcf_enabled' , '%' . wc_clean(wp_unslash($_GET[ 'term' ])) . '%' ) , ARRAY_A ) ;
				if ( srp_check_is_array( $data ) ) {
					foreach ( $data as $data_value ) {
						$listofcusfields[ $data_value[ 'id' ] ] = $data_value[ 'title' ] ;
					}
				}
				wp_send_json( $listofcusfields ) ;
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) ) ;
			}
		}

		public static function wcf_field_type() {
			check_ajax_referer( 'srp-cus-reg-fields-nonce' , 'sumo_security' ) ;

			try {
				if ( ! isset( $_POST ) || ! isset( $_POST[ 'field_id' ] ) ) {
					throw new exception( __( 'Invalid Request' , 'rewardsystem' ) ) ;
				}

				$field_id   = wc_clean(wp_unslash($_POST[ 'field_id' ])) ;
				$field_data = fpcf_get_custom_fields( $field_id ) ;
				wp_send_json_success( array( 'content' => strtoupper( $field_data->field_type ) ) ) ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function add_coupon_usage_reward_rule() {

			check_ajax_referer( 'srp-add-coupon-usage-rule-nonce' , 'sumo_security' ) ;

			try {

				if ( ! isset( $_POST[ 'rule_count' ] ) ) {
					throw new exception( esc_html__( 'Invalid Request' , 'rewardsystem' ) ) ;
				}

				$coupons = get_posts(
						array(
							'post_type'   => 'shop_coupon' ,
							'numberposts' => '-1' ,
							's'           => '-sumo_' ,
							'post_status' => 'publish' )
						) ;
				if ( ! srp_check_is_array( $coupons ) ) {
					throw new exception( esc_html__( 'Since there is no coupon created in WooCommerce, you cannot add a rule.' , 'rewardsystem' ) ) ;
				}

				$saved_rules = get_option( 'rewards_dynamic_rule_couponpoints' ) ;
				if ( srp_check_is_array( $saved_rules ) && 1 == absint($_POST[ 'rule_count' ]) ) {
					$key = count( $saved_rules ) + absint( $_POST[ 'rule_count' ] ) ;
				} else {
					$key = absint( $_POST[ 'rule_count' ] ) ;
				}

				ob_start() ;
				include (SRP_PLUGIN_PATH . '/includes/admin/views/add-coupon-usage-reward-rule.php') ;
				$html = ob_get_contents() ;
				ob_end_clean() ;

				wp_send_json_success( array( 'html' => $html ) ) ;
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) ) ;
			}
		}
	

		public static function add_rule_for_range_based_earn_points() {

			check_ajax_referer( 'srp-range-based-rule-nonce' , 'sumo_security' ) ;

			try {

				if ( ! isset( $_POST ) || ! isset( $_POST[ 'rule_count' ] ) ) {
					throw new exception( esc_html__( 'Invalid Request' , 'rewardsystem' ) ) ;
				}

				$rule_count  = absint( $_POST[ 'rule_count' ] ) ;
				$saved_rules = get_option( 'rs_range_based_points' , array() ) ;
				if ( srp_check_is_array( $saved_rules ) && 1 == $rule_count ) {
					$key = count( $saved_rules ) + absint( $rule_count ) ;
				} else {
					$key = absint( $rule_count ) ;
				}

				ob_start() ;
				include (SRP_PLUGIN_PATH . '/includes/admin/views/add-rule-for-range-based-earn-points.php') ;
				$html = ob_get_contents() ;
				ob_end_clean() ;

				wp_send_json_success( array( 'html' => $html ) ) ;
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) ) ;
			}
		}

		public static function add_user_purchase_history_rule() {

			check_ajax_referer( 'srp-add-user-purchase-history-nonce' , 'sumo_security' ) ;

			try {

				if ( ! isset( $_POST ) || ! isset( $_POST[ 'random_value' ] ) ) {
					throw new exception( esc_html__( 'Invalid Request' , 'rewardsystem' ) ) ;
				}

				$random_value = isset( $_POST[ 'random_value' ] ) ? absint( $_POST[ 'random_value' ] ) : 0 ;
				if ( ! $random_value ) {
					throw new exception( esc_html__( 'Invalid Rule' , 'rewardsystem' ) ) ;
				}

				ob_start() ;
				include (SRP_PLUGIN_PATH . '/includes/admin/views/add-user-purchase-history-rule.php') ;
				$html = ob_get_contents() ;
				ob_end_clean() ;

				wp_send_json_success( array( 'html' => $html ) ) ;
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) ) ;
			}
		}

		public static function add_earning_percentage_rule() {

			check_ajax_referer( 'srp-add-earning-percentage-nonce' , 'sumo_security' ) ;

			try {

				if ( ! isset( $_POST ) || ! isset( $_POST[ 'random_value' ] ) ) {
					throw new exception( esc_html__( 'Invalid Request' , 'rewardsystem' ) ) ;
				}

				$random_value = isset( $_POST[ 'random_value' ] ) ? absint( $_POST[ 'random_value' ] ) : 0 ;
				if ( ! $random_value ) {
					throw new exception( esc_html__( 'Invalid Rule' , 'rewardsystem' ) ) ;
				}

				ob_start() ;
				include (SRP_PLUGIN_PATH . '/includes/admin/views/add-earning-percentage-rule.php') ;
				$html = ob_get_contents() ;
				ob_end_clean() ;

				wp_send_json_success( array( 'html' => $html ) ) ;
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) ) ;
			}
		}

		public static function add_redeeming_percentage_rule() {

			check_ajax_referer( 'srp-redeeming-percentage-nonce' , 'sumo_security' ) ;

			try {

				if ( ! isset( $_POST ) || ! isset( $_POST[ 'random_value' ] ) ) {
					throw new exception( esc_html__( 'Invalid Request' , 'rewardsystem' ) ) ;
				}

				$random_value = isset( $_POST[ 'random_value' ] ) ? absint( $_POST[ 'random_value' ] ) : 0 ;
				if ( ! $random_value ) {
					throw new exception( esc_html__( 'Invalid Rule' , 'rewardsystem' ) ) ;
				}

				ob_start() ;
				include (SRP_PLUGIN_PATH . '/includes/admin/views/add-redeeming-percentage-rule.php') ;
				$html = ob_get_contents() ;
				ob_end_clean() ;

				wp_send_json_success( array( 'html' => $html ) ) ;
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) ) ;
			}
		}

		public static function add_redeeming_user_purchase_history_rule() {
			
			 check_ajax_referer( 'srp-redeeming-user-purchase-history-nonce' , 'sumo_security' ) ;

			try {

				if ( ! isset( $_POST ) || ! isset( $_POST[ 'random_value' ] ) ) {
					throw new exception( esc_html__( 'Invalid Request' , 'rewardsystem' ) ) ;
				}

				$random_value = isset( $_POST[ 'random_value' ] ) ? absint( $_POST[ 'random_value' ] ) : 0 ;
				if ( ! $random_value ) {
					throw new exception( esc_html__( 'Invalid Rule' , 'rewardsystem' ) ) ;
				}

				ob_start() ;
				include (SRP_PLUGIN_PATH . '/includes/admin/views/redeeming-add-user-purchase-history-rule.php') ;
				$html = ob_get_contents() ;
				ob_end_clean() ;

				wp_send_json_success( array( 'html' => $html ) ) ;
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) ) ;
			}
		}

		public static function add_manual_referral_link_rule() {

			check_ajax_referer( 'srp-manual-referral-link-nonce' , 'sumo_security' ) ;

			try {

				if ( ! isset( $_POST ) || ! isset( $_POST[ 'rule_count' ] ) ) {
					throw new exception( esc_html__( 'Invalid Request' , 'rewardsystem' ) ) ;
				}

				$rule_count = isset( $_POST[ 'rule_count' ] ) ? absint( $_POST[ 'rule_count' ] ) : 0 ;
				if ( ! $rule_count ) {
					throw new exception( esc_html__( 'Invalid Rule' , 'rewardsystem' ) ) ;
				}

				ob_start() ;
				include (SRP_PLUGIN_PATH . '/includes/admin/views/add-manual-referral-link-rule.php') ;
				$html = ob_get_contents() ;
				ob_end_clean() ;

				wp_send_json_success( array( 'html' => $html ) ) ;
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) ) ;
			}
		}

		public static function json_search_pages_and_posts() {

			check_ajax_referer( 'fp-pages-and-posts-search-nonce' , 'sumo_security' ) ;

			try {

				if ( isset( $_GET[ 'term' ] ) ) {
					$term = isset( $_GET[ 'term' ] ) ? wc_clean( wp_unslash( $_GET[ 'term' ] ) ) : '' ;
				}

				if ( empty( $term ) ) {
					throw new exception( esc_html__( 'No Page/Post found' , 'rewardsystem' ) ) ;
				}

				$post_ids = get_posts( array(
					'fields'         => 'ids' ,
					'posts_per_page' => '-1' ,
					'post_type'      => array( 'page' , 'post' ) ,
					'post_status'    => 'publish' ,
					'order'          => 'ASC' ,
					's'              => $term ,
						) ) ;

				if ( ! srp_check_is_array( $post_ids ) ) {
					return '' ;
				}

				$posts = array() ;
				foreach ( $post_ids as $post_id ) {
					$post_object = get_post( $post_id ) ;
					if ( ! is_object( $post_object ) ) {
						continue ;
					}

					$posts[ $post_object->ID ] = rawurldecode( $post_object->post_title ) ;
				}

				wp_send_json( apply_filters( 'woocommerce_json_search_found_pages_and_posts' , $posts ) ) ;
			} catch ( Exception $ex ) {
				wp_die() ;
			}
		}
				
		public static function action_to_enable_disable_nominee() {
										
				check_ajax_referer( 'fp-nominee-nonce' , 'sumo_security' ) ;
						
			try {
							
				if ( empty( $_POST[ 'userid' ]  ) ) {
					throw new exception( esc_html__( 'User is invalid' , 'rewardsystem' ) ) ;
				}
								
					$userid    = wc_clean(wp_unslash($_POST[ 'userid' ])) ;
				if ( isset( $_POST[ 'checkboxvalue' ] ) ) {
						update_user_meta( $userid , 'rs_enable_nominee' , wc_clean(wp_unslash($_POST[ 'checkboxvalue' ])) ) ;
				}
							
					wp_send_json_success(array('success' => true)) ;
							
			} catch ( Exception $ex ) {
				wp_die() ;
			}
		}

	}

	FP_Rewardsystem_Admin_Ajax::init() ;
}
