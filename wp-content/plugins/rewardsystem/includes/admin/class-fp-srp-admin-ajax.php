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
				'update_start_date'                        => false,
				'update_end_date'                          => false,
				'update_user_selection_format'             => false,
				'update_date_type'                         => false,
				'update_report_start_date'                 => false,
				'update_report_end_date'                   => false,
				'update_user_type'                         => false,
				'update_selected_user'                     => false,
				'update_report_date_type'                  => false,
				'update_type_of_points'                    => false,
				'updatestatusforemail'                     => false,
				'updatestatusforemailexpiry'               => false,
				'newemailexpirytemplate'                   => false,
				'editemailexpirytemplate'                  => false,
				'deletetemplateforemailexpiry'             => false,
				'unsubscribeuser'                          => false,
				'newemailtemplate'                         => false,
				'editemailtemplate'                        => false,
				'deletetemplateforemail'                   => false,
				'activatemodule'                           => false,
				'fp_reset_settings'                        => false,
				'fp_reset_users_data'                      => false,
				'fp_reset_order_meta'                      => false,
				'generatepointurl'                         => false,
				'removepointurl'                           => false,
				'add_wcf_fields'                           => false,
				'wcf_field_type'                           => false,
				'cus_field_search'                         => false,
				'srp_user_search'                          => true,
				'send_points_data'                         => true,
				'enable_reward_program'                    => true,
				'add_coupon_usage_reward_rule'             => false,
				'add_user_purchase_history_rule'           => false,
				'add_earning_percentage_rule'              => false,
				'add_redeeming_percentage_rule'            => false,
				'add_redeeming_user_purchase_history_rule' => false,
				'add_manual_referral_link_rule'            => false,
				'add_rule_for_range_based_earn_points'     => false,
				'json_search_pages_and_posts'              => false,
				'action_to_enable_disable_nominee'         => true,
				'add_rule_for_bonus_points_without_repeat_for_orders'       => false,
				'view_bonus_point_placed_order_ids_popup'                   => false,
				'srp_add_rule'                             => false,
				'srp_delete_rule'                          => false,
				'add_account_anniversary_rule'             => false,
				'add_custom_anniversary_rule'              => false, 
				'view_account_anniversary_points_popup'    => false,
				'view_single_anniversary_points_popup'     => false,
				'view_multiple_anniversary_points_popup'   => false,
				'progress_bar_action'                      => false,
				'update_report_user_selection_format'      => false,
				'srp_product_search'                       => false,
				'srp_display_redeem_point_popup'         => false,
				'srp_redeem_point_manually'              => false,
				'validate_gateway_redeemed_points'       => false,
				'validate_point_price_product'       => false,
			);

			foreach ( $actions as $action => $nopriv ) {
				add_action( 'wp_ajax_' . $action , array( __CLASS__, $action ) ) ;

				if ( $nopriv ) {
					add_action( 'wp_ajax_nopriv_' . $action , array( __CLASS__, $action ) ) ;
				}
			}
		}

		/**
		 * Product search.
		 * 
		 * @since 28.8
		 */
		public static function srp_product_search() {
			check_ajax_referer( 'srp-search-nonce' , 'srp_security' ) ;

			try {
				$term = isset( $_GET[ 'term' ] ) ? ( string ) wc_clean( wp_unslash( $_GET[ 'term' ] ) ) : '' ;
				
				if ( empty( $term ) ) {
					throw new exception( esc_html( 'No Product(s) found' , 'rewardsystem' ) ) ;
				}

				$data_store = WC_Data_Store::load( 'product' ) ;
				$ids        = $data_store->search_products( $term , '' , true , false , 30 ) ;

				$product_objects = array_filter( array_map( 'wc_get_product' , $ids ) , 'wc_products_array_filter_readable' ) ;
				$products        = array() ;

				$exclude_global_variable = isset( $_GET[ 'exclude_global_variable' ] ) ? wc_clean( wp_unslash( $_GET[ 'exclude_global_variable' ] ) ) : 'no' ; // @codingStandardsIgnoreLine.
				foreach ( $product_objects as $product_object ) {
					if ( 'yes' == $exclude_global_variable && $product_object->is_type( 'variable' ) ) {
						continue ;
					}

					$products[ $product_object->get_id() ] = rawurldecode( $product_object->get_formatted_name() ) ;
				}
				wp_send_json( $products ) ;
			} catch ( Exception $ex ) {
				wp_die() ;
			}
		}

		public static function update_start_date() {
			check_ajax_referer( 'fp-start-date' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'start_date' ] ) ) {
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				$selected_users = isset($_POST[ 'selectedusers' ]) ? wc_clean(wp_unslash($_POST[ 'selectedusers' ])):'';
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				global $wpdb ;
								$TableName = "{$wpdb->prefix}rs_expiredpoints_email" ;
				$Status    = 'ACTIVE' == wc_clean(wp_unslash($_POST[ 'status' ])) ? 'NOTACTIVE' : 'ACTIVE' ;
				$wpdb->update( $TableName , array( 'rs_status' => $Status ) , array( 'id' => absint($_POST[ 'row_id' ] ) ) ) ;
				wp_send_json_success( array( 'content' => $Status ) ) ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function newemailexpirytemplate() {
			check_ajax_referer( 'fp-new-template' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'templatename' ] ) ) {
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
			}
						
			if (!isset($_POST[ 'senderoption' ]) || !isset($_POST[ 'fromname' ])  || !isset($_POST[ 'fromemail' ])  || !isset($_POST[ 'subject' ]) || !isset($_POST[ 'message' ])  || !isset($_POST[ 'noofdays' ])  || !isset($_POST[ 'templatestatus' ]) ) {
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				global $wpdb ;
								$TableName = "{$wpdb->prefix}rs_expiredpoints_email" ;
				$wpdb->insert( $TableName , array(
					'template_name' => wc_clean(wp_unslash( $_POST[ 'templatename' ] ) ),
					'sender_opt'    => wc_clean(wp_unslash( $_POST[ 'senderoption' ] )),
					'from_name'     => wc_clean(wp_unslash( $_POST[ 'fromname' ] ) ),
					'from_email'    => wc_clean(wp_unslash( $_POST[ 'fromemail' ] ) ),
					'subject'       => wc_clean(wp_unslash( $_POST[ 'subject' ] ) ),
					'message'       => wp_kses_post(wp_unslash( $_POST[ 'message' ] ) ),
					'noofdays'      => wc_clean(wp_unslash( $_POST[ 'noofdays' ] ) ),
					'rs_status'     => wc_clean(wp_unslash( $_POST[ 'templatestatus' ] )),
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
			}
						
			if (!isset($_POST[ 'templatename' ]) || !isset($_POST[ 'senderoption' ]) || !isset($_POST[ 'fromname' ])  || !isset($_POST[ 'fromemail' ])  || !isset($_POST[ 'subject' ]) || !isset($_POST[ 'message' ])  || !isset($_POST[ 'noofdays' ])  || !isset($_POST[ 'templatestatus' ]) ) {
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				global $wpdb ;
				$TableName = "{$wpdb->prefix}rs_expiredpoints_email" ;
				$wpdb->update( $TableName , array(
					'template_name' => wc_clean(wp_unslash( $_POST[ 'templatename' ] ) ),
					'sender_opt'    => wc_clean(wp_unslash( $_POST[ 'senderoption' ] )),
					'from_name'     => wc_clean(wp_unslash( $_POST[ 'fromname' ] ) ),
					'from_email'    => wc_clean(wp_unslash( $_POST[ 'fromemail' ] ) ),
					'subject'       => wc_clean(wp_unslash( $_POST[ 'subject' ] ) ),
					'message'       => wp_kses_post(wp_unslash( $_POST[ 'message' ] ) ),
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
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
					'from_email'           => isset($_POST[ 'fromemail' ]) ? wc_clean(wp_unslash( $_POST[ 'fromemail' ] )):'',
					'subject'              => isset($_POST[ 'subject' ]) ? wc_clean(wp_unslash( $_POST[ 'subject' ] ) ):'',
					'message'              => isset($_POST[ 'message' ]) ? wp_kses_post(wp_unslash( $_POST[ 'message' ] ) ):'',
					'rs_status'            => isset($_POST[ 'templatestatus' ]) ? wc_clean(wp_unslash( $_POST[ 'templatestatus' ] )):'',
					'earningpoints'        => isset($_POST[ 'earningpoints' ]) ? wc_clean(wp_unslash( $_POST[ 'earningpoints' ] ) ):'',
					'redeemingpoints'      => isset($_POST[ 'redeemingpoints' ]) ? wc_clean(wp_unslash( $_POST[ 'redeemingpoints' ] )) :'',
					'mailsendingoptions'   => isset($_POST[ 'mailsendingoptions' ]) ? wc_clean(wp_unslash( $_POST[ 'mailsendingoptions' ] )):'',
					'rsmailsendingoptions' => isset($_POST[ 'rsmailsendingoptions' ]) ? wc_clean(wp_unslash( $_POST[ 'rsmailsendingoptions' ] )):'',
					'minimum_userpoints'   => isset($_POST[ 'minuserpoints' ]) ? wc_clean(wp_unslash( $_POST[ 'minuserpoints' ] )) :'',
					'sendmail_options'     => isset( $_POST[ 'sendmailoptions' ] ) ? wc_clean(wp_unslash( $_POST[ 'sendmailoptions' ]) ) : '',
					'sendmail_to'          => serialize($send_mail_selected),
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
			}
						
			if (! isset( $_POST[ 'templatename' ] ) || ! isset( $_POST[ 'senderoption' ] ) || ! isset( $_POST[ 'fromname' ] ) || ! isset( $_POST[ 'fromemail' ] ) || ! isset( $_POST[ 'subject' ] ) || ! isset( $_POST[ 'message' ] )) {
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
			}
						
			if (! isset( $_POST[ 'templatestatus' ] ) || ! isset( $_POST[ 'earningpoints' ] ) || ! isset( $_POST[ 'redeemingpoints' ] ) || ! isset( $_POST[ 'mailsendingoptions' ] ) || ! isset( $_POST[ 'rsmailsendingoptions' ] )) {
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
			}
						
			if (!isset( $_POST[ 'minuserpoints' ] )) {
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
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
					'rs_status'            => wc_clean(wp_unslash( $_POST[ 'templatestatus' ] )),
					'earningpoints'        => wc_clean(wp_unslash( $_POST[ 'earningpoints' ] ) ),
					'redeemingpoints'      => wc_clean(wp_unslash( $_POST[ 'redeemingpoints' ] )),
					'mailsendingoptions'   => wc_clean(wp_unslash( $_POST[ 'mailsendingoptions' ] )),
					'rsmailsendingoptions' => wc_clean(wp_unslash( $_POST[ 'rsmailsendingoptions' ] )),
					'minimum_userpoints'   => wc_clean(wp_unslash( $_POST[ 'minuserpoints' ] )),
					'sendmail_options'     => isset( $_POST[ 'sendmailoptions' ] ) ? wc_clean(wp_unslash( $_POST[ 'sendmailoptions' ] ) ) : '',
					'sendmail_to'          => isset( $_POST[ 'sendmailselected' ] ) ? serialize( wc_clean(wp_unslash($_POST[ 'sendmailselected' ] ))) :array(),
						) , array( 'id' => wc_clean(wp_unslash($_POST[ 'templateid' ] ) ) ));
				wp_send_json_success( array( 'content' => 'Settings Updated' ) ) ;
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function deletetemplateforemail() {
			check_ajax_referer( 'fp-delete-template' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'row_id' ] ) ) {
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
								$tabs = array( 'fprsgeneral', 'fprsmodules', 'fprsaddremovepoints', 'fprsmessage', 'fprslocalization', 'fprsadvanced' ) ;
				foreach ($tabs as $tab) {
					require_once SRP_PLUGIN_PATH . '/includes/admin/tabs/class-rs-' . $tab . '-tab.php' ;
				}
																
								$sections   = get_list_of_modules() ;
				foreach ($sections as $section_key => $section_value) {
					if ('yes' != $section_value || 'fpreset' == $section_key) {
						continue;
					}
									
					require_once SRP_PLUGIN_PATH . '/includes/admin/tabs/modules/class-rs-' . $section_key . '-module-tab.php' ;
				}
								
				if (class_exists('RSGeneralTabSetting')) {
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
				}
								
				if (class_exists('RSAddorRemovePoints')) {
					foreach ( RSAddorRemovePoints::reward_system_admin_fields() as $setting ) {
						if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
							delete_option( $setting[ 'newids' ] ) ;
							add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
						}
					}
				}
								
				if (class_exists('RSProductPurchaseModule')) {
					foreach ( RSProductPurchaseModule::reward_system_admin_fields() as $setting ) {
						if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
							delete_option( $setting[ 'newids' ] ) ;
							add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
						}
					}
				}

				if (class_exists('RSReferralSystemModule')) {
					foreach ( RSReferralSystemModule::reward_system_admin_fields() as $setting ) {
						if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
							delete_option( $setting[ 'newids' ] ) ;
							add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
						}
					}
				}
								
				if (class_exists('RSRewardPointsForAction')) {
					foreach ( RSRewardPointsForAction::reward_system_admin_fields() as $setting ) {
						if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
							delete_option( $setting[ 'newids' ] ) ;
							add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
						}
					}
				}

				if (class_exists('RSPointExpiryModule')) {
					foreach ( RSPointExpiryModule::reward_system_admin_fields() as $setting ) {
						if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
							delete_option( $setting[ 'newids' ] ) ;
							add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
						}
					}
				}

				if (class_exists('RSRedeemingModule')) {
					foreach ( RSRedeemingModule::reward_system_admin_fields() as $setting ) {
						if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
							delete_option( $setting[ 'newids' ] ) ;
							add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
						}
					}
				}

				if (class_exists('RSPointPriceModule')) {
					foreach ( RSPointPriceModule::reward_system_admin_fields() as $setting ) {
						if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
							delete_option( $setting[ 'newids' ] ) ;
							add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
						}
					}
				}

				if (class_exists('RSEmailModule')) {
					foreach ( RSEmailModule::reward_system_admin_fields() as $setting ) {
						if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
							delete_option( $setting[ 'newids' ] ) ;
							add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
						}
					}
				}

				if (class_exists('RSGiftVoucher')) {
					foreach ( RSGiftVoucher::reward_system_admin_fields() as $setting ) {
						if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
							delete_option( $setting[ 'newids' ] ) ;
							add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
						}
					}
				}

				if (class_exists('RSBuyingPoints')) {
					foreach ( RSBuyingPoints::reward_system_admin_fields() as $setting ) {
						if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
							delete_option( $setting[ 'newids' ] ) ;
							add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
						}
					}
				}

				if (class_exists('RSSocialReward')) {
					foreach ( RSSocialReward::reward_system_admin_fields() as $setting ) {
						if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
							delete_option( $setting[ 'newids' ] ) ;
							add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
						}
					}
				}

				if (class_exists('RSSms')) {
					foreach ( RSSms::reward_system_admin_fields() as $setting ) {
						if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
							delete_option( $setting[ 'newids' ] ) ;
							add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
						}
					}
				}

				if (class_exists('RSCashbackModule')) {
					foreach ( RSCashbackModule::reward_system_admin_fields() as $setting ) {
						if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
							delete_option( $setting[ 'newids' ] ) ;
							add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
						}
					}
				}

				if (class_exists('RSPointURL')) {
					foreach ( RSPointURL::reward_system_admin_fields() as $setting ) {
						if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
							delete_option( $setting[ 'newids' ] ) ;
							add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
						}
					}
				}

				if (class_exists('RSGatewayModule')) {
					foreach ( RSGatewayModule::reward_system_admin_fields() as $setting ) {
						if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
							delete_option( $setting[ 'newids' ] ) ;
							add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
						}
					}
				}

				if (class_exists('RSSendPointsModule')) {
					foreach ( RSSendPointsModule::reward_system_admin_fields() as $setting ) {
						if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
							delete_option( $setting[ 'newids' ] ) ;
							add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
						}
					}
				}
								
				if (class_exists('RSMessage')) {
					foreach ( RSMessage::reward_system_admin_fields() as $setting ) {
						if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
							delete_option( $setting[ 'newids' ] ) ;
							add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
						}
					}
				}

				if (class_exists('RSLocalization')) {
					foreach ( RSLocalization::reward_system_admin_fields() as $setting ) {
						if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
							delete_option( $setting[ 'newids' ] ) ;
							add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
						}
					}
				}

				if (class_exists('RSAdvancedSetting')) {
					foreach ( RSAdvancedSetting::reward_system_admin_fields() as $setting ) {
						if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
							delete_option( $setting[ 'newids' ] ) ;
							add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
						}
					}
				}

				if (class_exists('RSDiscountsCompatability')) {
					foreach ( RSDiscountsCompatability::reward_system_admin_fields() as $setting ) {
						if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
							delete_option( $setting[ 'newids' ] ) ;
							add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
						}
					}
				}

				if (class_exists('RSCouponCompatability')) {
					foreach ( RSCouponCompatability::reward_system_admin_fields() as $setting ) {
						if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
							delete_option( $setting[ 'newids' ] ) ;
							add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
						}
					}
				}

				delete_option( 'rewards_dynamic_rule_couponpoints' ) ;

				if (class_exists('RSNominee')) {
					foreach ( RSNominee::reward_system_admin_fields() as $setting ) {
						if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
							delete_option( $setting[ 'newids' ] ) ;
							add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
						}
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
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
					$args    = array( 'post_type' => 'shop_order', 'numberposts' => '-1', 'meta_query' => array( 'relation' => 'AND', array( 'key' => 'reward_points_awarded', 'compare' => 'EXISTS' ), array( 'key' => 'earning_point_once', 'compare' => 'EXISTS' ) ), 'post_status' => 'published', 'fields' => 'ids', 'cache_results' => false ) ;
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
					$UserIDs = get_users(array( 'fields'=>'ids' )) ;
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
				} elseif ( srp_check_is_array( array_filter( ( array ) ( $UserIDs ) ) ) && $reset_record_table_log ) {
						$db->query( "DELETE FROM $PointsTable WHERE userid IN ($UserIDs)" ) ;
				}

				wp_send_json_success( array( 'content' => $OrderId ) ) ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function fp_reset_order_meta() {
			check_ajax_referer( 'reset-previous-order-meta' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) || ! isset( $_POST[ 'ids' ] ) ) {
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				if ( 'done' == absint($_POST[ 'ids' ]) ) {
					wp_send_json_success( array( 'content' => 'success' ) ) ;
				}

				$order = absint($_POST[ 'ids' ]) ;
				foreach ( $order as $order_id ) {
					$order_obj = wc_get_order($order_id);
					$order_obj->delete_meta_data( 'reward_points_awarded' ) ;
					$order_obj->delete_meta_data( 'earning_point_once' ) ;
					$order_obj->save();
				}
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function srp_user_search() {
			check_ajax_referer( 'fp-user-search' , 'sumo_security' ) ;

			if ( ! isset( $_POST ) ) {
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				$display_user    = array() ;
								$term = isset($_POST[ 'term' ]) ? wc_clean(wp_unslash($_POST[ 'term' ] )):'';
				$customers_query = new WP_User_Query( array(
					'fields'         => 'all',
					'orderby'        => 'display_name',
					'search'         => "*$term*",
					'search_columns' => array( 'ID', 'user_login', 'user_email', 'user_nicename' ),
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
			}
						
			if (!isset($_POST[ 'senderid' ]) || !isset($_POST[ 'sendername' ]) || !isset($_POST[ 'senderpoints' ]) || !isset($_POST[ 'reason' ])) {
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
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
					throw new Exception( esc_html( 'Invalid User' , 'rewardsystem' ) ) ;
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
					$message       = str_replace( array( '[sender]', '[receiver]', '[points]' ) , array( $SenderName, $ReceiverName, $Points ) , $Email_message ) ;
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

					$ReplaceValue = ( '1' == $ApprovalType ) ? array( 'Manual Approval', 'Still Waiting' ) : array( 'Auto Approval', 'Accepted' ) ;
					$AdminMsg     = str_replace( array( '[Type]', '[request_status]' ) , $ReplaceValue , $message ) ;
					$AdminMsg     = do_shortcode( $AdminMsg ) ;
					ob_start() ;
					wc_get_template( 'emails/email-header.php' , array( 'email_heading' => $Email_subject ) ) ;
					echo esc_html($AdminMsg) ;
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
						$message                      = str_replace( array( '[rs_sendpoints]', '[specific_user]', '[user_name]' ) , array( $Points, $SenderName, $ReceiverName ) , $email_message ) ;
												$reason                       = isset($_POST[ 'reason' ]) ? wc_clean(wp_unslash($_POST[ 'reason' ])):'';
						$Email_message                = str_replace( array( '[status]', '[reason_message]', '[rsfirstname]', '[rslastname]' ) , array( 'Accepted', $reason, $ReceiverFirstName, $ReceiverLastName ) , $message ) ;
						$Email_message                = do_shortcode( $Email_message ) ;
						add_filter( 'woocommerce_email_footer_text' , 'srp_footer_link' ) ;
						ob_start() ;
						wc_get_template( 'emails/email-header.php' , array( 'email_heading' => $email_subject ) ) ;
						echo esc_html($Email_message) ;
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
						$message       = str_replace( array( '[user_name]', '[request]', '[points]', '[receiver_name]' ) , array( $SenderName, 'Accepted', $Points, $ReceiverName ) , $email_message ) ;
						$Email_message = str_replace( array( '[status]', '[reason_message]', '[rsfirstname]', '[rslastname]' ) , array( 'Accepted', wc_clean(wp_unslash($_POST[ 'reason' ])), $ReceiverFirstName, $ReceiverLastName ) , $message ) ;
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
						echo esc_html($Email_message) ;
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
						'user_id'           => $ReceiverId,
						'pointstoinsert'    => $Points,
						'checkpoints'       => 'SP',
						'totalearnedpoints' => $Points,
						'nomineeid'         => $SenderId,
							) ;
					RSPointExpiry::insert_earning_points( $table_args ) ;
					RSPointExpiry::record_the_points( $table_args ) ;
				}
				$wpdb->insert( "{$wpdb->prefix}sumo_reward_send_point_submitted_data" , array( 'userid' => $SenderId, 'userloginname' => $SenderName, 'pointstosend' => $Points, 'sendercurrentpoints' => wc_clean(wp_unslash($_POST[ 'senderpoints' ] )), 'status' => $Status, 'selecteduser' => $ReceiverId, 'date' => date_i18n( 'Y-m-d H:i:s' ) ) ) ;
				$redeempoints = RSPointExpiry::perform_calculation_with_expiry( $Points , $SenderId ) ;
				$table_args   = array(
					'user_id'     => $SenderId,
					'usedpoints'  => $Points,
					'checkpoints' => ( '1' == $ApprovalType ) ? 'SPB' : 'SENPM',
					'nomineeid'   => $ReceiverId,
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				$NewArr       = array( uniqid() => wc_clean(wp_unslash($_POST )) ) ;
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
			}

			try {
				$enable_reward_program = wc_clean(wp_unslash($_POST[ 'enable_reward_points' ] ));
				update_user_meta( get_current_user_id() , 'allow_user_to_earn_reward_points' , $enable_reward_program) ;

				if ('yes' === $enable_reward_program) {
					/**
					 * This hook is used to do extra action when user involved in Reward Program.
					 *
					 * @param int $userid User ID.
					 * @since 29.4
					 */
					do_action( 'fp_rs_reward_program_enabled', get_current_user_id() );
				}
				wp_send_json_success() ;
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) ) ;
			}
		}

		public static function add_wcf_fields() {
			check_ajax_referer( 'srp-cus-reg-fields-nonce' , 'sumo_security' ) ;

			try {
				if ( ! isset( $_POST ) || ! isset( $_POST[ 'count' ] ) ) {
					throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
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
						'class'              => 'wc-product-search rs_search_custom_field',
						'id'                 => 'rs_rule_for_custom_reg_field[' . $key . '][custom_fields]',
						'name'               => 'rs_rule_for_custom_reg_field[' . $key . '][custom_fields]',
						'type'               => 'customfields',
						'action'             => 'cus_field_search',
						'multiple'           => false,
						'css'                => 'width: 100%;',
						'placeholder'        => 'Select Custom Fields',
						'options'            => array(),
						'translation_string' => 'rewardsystem',
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
				throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
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
					throw new exception( esc_html( 'Invalid Request' , 'rewardsystem' ) ) ;
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
							'post_type'   => 'shop_coupon',
							'numberposts' => '-1',
							's'           => '-sumo_',
							'post_status' => 'publish',
				)
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
				include SRP_PLUGIN_PATH . '/includes/admin/views/add-coupon-usage-reward-rule.php' ;
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
				include SRP_PLUGIN_PATH . '/includes/admin/views/add-rule-for-range-based-earn-points.php' ;
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
				include SRP_PLUGIN_PATH . '/includes/admin/views/add-user-purchase-history-rule.php' ;
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
				include SRP_PLUGIN_PATH . '/includes/admin/views/add-earning-percentage-rule.php' ;
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
				include SRP_PLUGIN_PATH . '/includes/admin/views/add-redeeming-percentage-rule.php' ;
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
				include SRP_PLUGIN_PATH . '/includes/admin/views/redeeming-add-user-purchase-history-rule.php' ;
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
				include SRP_PLUGIN_PATH . '/includes/admin/views/add-manual-referral-link-rule.php' ;
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
					'fields'         => 'ids',
					'posts_per_page' => '-1',
					'post_type'      => array( 'page', 'post' ),
					'post_status'    => 'publish',
					'order'          => 'ASC',
					's'              => $term,
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
								/**
								 * Hook:woocommerce_json_search_found_pages_and_posts.
								 * 
								 * @since 1.0
								 */
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
							
					wp_send_json_success(array( 'success' => true )) ;
							
			} catch ( Exception $ex ) {
				wp_die() ;
			}
		}
				
		public static function add_rule_for_bonus_points_without_repeat_for_orders() {
			
			check_ajax_referer( 'fp-bonus-points-rule-for-orders-nonce' , 'sumo_security' ) ;

			try {

				if ( ! isset( $_POST ) ) {
					throw new exception( esc_html__( 'Invalid Request' , 'rewardsystem' ) ) ;
				}

				$key  = time() ;

				ob_start() ;
				include SRP_PLUGIN_PATH . '/includes/admin/views/bonus-points/html-add-rule-for-bonus-points-without-repeat-for-orders.php' ;
				$html = ob_get_contents() ;
				ob_end_clean() ;

				wp_send_json_success( array( 'html' => $html ) ) ;
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) ) ;
			}
		}
	   
		public static function view_bonus_point_placed_order_ids_popup() {
				
			check_ajax_referer( 'fp-view-bonus-point-placed-order-ids-popup-nonce' , 'sumo_security' ) ;
				
			try {

				if ( ! isset( $_POST ) ) {
					throw new exception( esc_html__( 'Invalid Request' , 'rewardsystem' ) ) ;
				}
					
				$user_id = isset($_POST[ 'user_id' ]) ? absint( $_POST[ 'user_id' ] ):0 ;
				if ( ! $user_id ) {
					throw new exception( esc_html__( 'Invalid User' , 'rewardsystem' ) ) ;
				}
					
				$order_id = isset($_POST['stored_order_id']) ? wc_clean(wp_unslash($_POST['stored_order_id'])):'' ;
				if (!$order_id) {
					throw new exception( esc_html__( 'Invalid Data' , 'rewardsystem' ) ) ;
				}
						 
				global $wpdb;
				$db = &$wpdb;
				$order_ids = $db->get_col($db->prepare("SELECT DISTINCT posts.ID
			FROM $db->posts as posts
			LEFT JOIN {$db->postmeta} AS meta ON posts.ID = meta.post_id
                        LEFT JOIN {$db->postmeta} AS meta1 ON posts.ID = meta1.post_id
                        WHERE   posts.post_type     = 'shop_order'
		        AND     posts.post_status   = 'wc-completed'
                        AND     meta.meta_key       = '_customer_user'
                        AND     meta.meta_value     = %d
                        AND     meta1.meta_key      ='rs_bonus_awarded_order_id'
                        AND     meta1.meta_value    = %d ORDER by posts.ID DESC", $user_id, $order_id));
						
				if (!srp_check_is_array($order_ids)) {
					throw new exception( esc_html__( 'No Data Found' , 'rewardsystem' ) ) ;
				}    
					
				$order_count = count($order_ids);
				$offset = isset($_POST['selected_page']) && !empty($_POST['selected_page']) ? absint($_POST['selected_page']) : 1;
				$per_page = 10;
				$offset = ( $per_page * $offset ) - $per_page;
				$order_ids = array_slice($order_ids, $offset, $per_page);
				$order_total_pages = ceil($order_count / $per_page);
																		
				ob_start();
				include SRP_PLUGIN_PATH . '/includes/admin/views/backbone-modal/html-bonus-placed-orders-table-content.php';
				$contents = ob_get_contents();
				ob_end_clean();
								
				wp_send_json_success( array( 'html' => $contents ) ) ;
					
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) ) ;
			}
		}
				/**
		 * Add Rule for Promotional.
		 */
		public static function srp_add_rule() {
			check_ajax_referer( 'srp-rule-nonce' , 'srp_security' ) ;

			try {
				if ( ! isset( $_POST[ 'count' ] ) ) {
					throw new exception( esc_html__( 'Invalid Request' , 'rewardsystem' ) ) ;
				}

				ob_start() ;

				$key = absint( $_POST[ 'count' ] ) ;

				include_once SRP_PLUGIN_PATH . '/includes/admin/views/promotional/promotional-rule-settings-new.php' ;

				$field = ob_get_contents() ;
				ob_end_clean() ;

				wp_send_json_success( array( 'field' => $field ) ) ;
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) ) ;
			}
		}

		/**
		 * Delete Rule.
		 */
		public static function srp_delete_rule() {
			check_ajax_referer( 'srp-rule-nonce' , 'srp_security' ) ;

			try {
				if ( ! isset( $_POST[ 'rule_id' ] ) ) {
					throw new exception( esc_html__( 'Invalid Request' , 'rewardsystem' ) ) ;
				}

				$rule_id = absint( $_POST[ 'rule_id' ] ) ;

				srp_delete_rule( $rule_id ) ;

				wp_send_json_success() ;
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) ) ;
			}
		}

		public static function add_account_anniversary_rule() {
					
					   check_ajax_referer( 'fp-add-account-anniversary-rule-nonce' , 'sumo_security' ) ;

			try {

				if ( ! isset( $_POST ) ) {
					throw new exception( esc_html__( 'Invalid Request' , 'rewardsystem' ) ) ;
				}

				$key  = time() ;

				ob_start() ;
				include SRP_PLUGIN_PATH . '/includes/admin/views/anniversary-points/html-add-account-anniversary-rule-based.php' ;
				$html = ob_get_contents() ;
				ob_end_clean() ;

				wp_send_json_success( array( 'html' => $html ) ) ;
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) ) ;
			}
		}
				
		public static function add_custom_anniversary_rule() {
					
				check_ajax_referer( 'fp-add-custom-anniversary-rule-nonce' , 'sumo_security' ) ;

			try {

				if ( ! isset( $_POST ) ) {
					throw new exception( esc_html__( 'Invalid Request' , 'rewardsystem' ) ) ;
				}

				$key  = time() ;

				ob_start() ;
				include SRP_PLUGIN_PATH . '/includes/admin/views/anniversary-points/html-add-custom-anniversary-rule-based.php' ;
				$html = ob_get_contents() ;
				ob_end_clean() ;

				wp_send_json_success( array( 'html' => $html ) ) ;
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) ) ;
			}
		}
				
		public static function view_account_anniversary_points_popup() {
					
			check_ajax_referer( 'fp-view-account-anniversary-points-popup-nonce' , 'sumo_security' ) ;
				
			try {

				if ( ! isset( $_POST ) ) {
					throw new exception( esc_html__( 'Invalid Request' , 'rewardsystem' ) ) ;
				}
					
				$user_id = isset($_POST[ 'user_id' ]) ? absint( $_POST[ 'user_id' ] ):0 ;
				if ( ! $user_id ) {
					throw new exception( esc_html__( 'Invalid User' , 'rewardsystem' ) ) ;
				}
								
								$user = get_user_by('ID', $user_id);
				if (!is_object($user)) {
						throw new exception( esc_html__( 'Invalid User' , 'rewardsystem' ) ) ;
				}
								
								global $wpdb;
								$db = &$wpdb;
								$account_anniv_data = $db->get_results($db->prepare("SELECT * FROM {$db->prefix}rsrecordpoints WHERE earnedpoints NOT IN(0) AND checkpoints IN('AAP') AND userid='%d' ORDER BY ID DESC", $user_id), ARRAY_A);
								
								ob_start();
				include SRP_PLUGIN_PATH . '/includes/admin/views/backbone-modal/html-account-anniversary-points-content.php';
				$contents = ob_get_contents();
				ob_end_clean();
								
								wp_send_json_success( array( 'html' => $contents ) ) ;
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) ) ;
			}
		}
				
		public static function view_single_anniversary_points_popup() {
					
			check_ajax_referer( 'fp-view-single-anniversary-points-popup-nonce' , 'sumo_security' ) ;
				
			try {

				if ( ! isset( $_POST ) ) {
					throw new exception( esc_html__( 'Invalid Request' , 'rewardsystem' ) ) ;
				}
					
				$user_id = isset($_POST[ 'user_id' ]) ? absint( $_POST[ 'user_id' ] ):0 ;
				if ( ! $user_id ) {
					throw new exception( esc_html__( 'Invalid User' , 'rewardsystem' ) ) ;
				}
								
								$user = get_user_by('ID', $user_id);
				if (!is_object($user)) {
						throw new exception( esc_html__( 'Invalid User' , 'rewardsystem' ) ) ;
				}
								
								global $wpdb;
								$db = &$wpdb;
								$single_anniv_data = $db->get_results($db->prepare("SELECT * FROM {$db->prefix}rsrecordpoints WHERE earnedpoints NOT IN(0) AND checkpoints IN('CSAP') AND userid='%d' ORDER BY ID DESC", $user_id), ARRAY_A);
								$single_anniv_date            = get_user_meta( $user_id, 'rs_single_anniversary_date', true );
								
								ob_start();
				include SRP_PLUGIN_PATH . '/includes/admin/views/backbone-modal/html-single-anniversary-points-content.php';
				$contents = ob_get_contents();
				ob_end_clean();
								
								wp_send_json_success( array( 'html' => $contents ) ) ;
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) ) ;
			}
		}
				
		public static function view_multiple_anniversary_points_popup() {
					
			check_ajax_referer( 'fp-view-multiple-anniversary-points-popup-nonce' , 'sumo_security' ) ;
				
			try {

				if ( ! isset( $_POST ) ) {
					throw new exception( esc_html__( 'Invalid Request' , 'rewardsystem' ) ) ;
				}
					
				$user_id = isset($_POST[ 'user_id' ]) ? absint( $_POST[ 'user_id' ] ):0 ;
				if ( ! $user_id ) {
					throw new exception( esc_html__( 'Invalid User' , 'rewardsystem' ) ) ;
				}
								
								$user = get_user_by('ID', $user_id);
				if (!is_object($user)) {
						throw new exception( esc_html__( 'Invalid User' , 'rewardsystem' ) ) ;
				}
								
								global $wpdb;
								$db = &$wpdb;
								$multiple_anniv_data = $db->get_results($db->prepare("SELECT * FROM {$db->prefix}rsrecordpoints WHERE earnedpoints NOT IN(0) AND checkpoints IN('CMAP') AND userid='%d' ORDER BY ID DESC", $user_id), ARRAY_A);                                
								$multiple_anniv_dates            = get_user_meta( $user_id, 'rs_multiple_anniversary_date', true );
								
								ob_start();
				include SRP_PLUGIN_PATH . '/includes/admin/views/backbone-modal/html-multiple-anniversary-points-content.php';
				$contents = ob_get_contents();
				ob_end_clean();
								
								wp_send_json_success( array( 'html' => $contents ) ) ;
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) ) ;
			}
		}
								
		/**
		 * Progress bar action.
		 * */
		public static function progress_bar_action() { 
					
			check_ajax_referer( 'fp-srp-upgrade', 'sumo_security' );

			try {
				if ( ! isset( $_POST ) ) {
					throw new exception( esc_html__( 'Invalid data', 'rewardsystem' ) );
				}
																								
				if (!isset($_POST['action_scheduler_class_id'])) {
					throw new exception( esc_html__( 'Invalid data', 'rewardsystem' ) );
				}
								
				$action_scheduler_id = isset($_POST['action_scheduler_class_id']) ? wc_clean(wp_unslash($_POST['action_scheduler_class_id'])):'';
				if (!$action_scheduler_id) {
					throw new exception( esc_html__( 'Invalid data', 'rewardsystem' ) );
				}
																								
				if (!class_exists('RS_Action_Scheduler_Instances')) {
					include_once SRP_PLUGIN_PATH . '/action-scheduler/class-rs-action-scheduler-instances.php'  ;
				}
								
				$action_scheduler_object = RS_Action_Scheduler_Instances::get_action_scheduler_by_id($action_scheduler_id);
				if (!is_object($action_scheduler_object)) {
					throw new exception( esc_html__( 'Invalid data', 'rewardsystem' ) );
				}
																								
				$progress_count = $action_scheduler_object->get_progress_count();
				if (!$progress_count) {
					$action_scheduler_object->update_progress_count( 2 );
				}

				$scheduled_actions = as_get_scheduled_actions( 
					array(
						'hook' => $action_scheduler_object->get_action_scheduler_name(),
						'status' => 'pending',
					)
				);
												
				if ( ! srp_check_is_array( $scheduled_actions ) ) {
					if ($progress_count < 10) {
						$action_scheduler_object->update_progress_count( 10 );
					}
																															
					$scheduled_chunk_actions = as_get_scheduled_actions( 
						array( 
							'hook' => $action_scheduler_object->get_chunked_action_scheduler_name(),
							'status' => 'pending', 
						) 
					);
														
					if ( ! srp_check_is_array( $scheduled_chunk_actions ) && $progress_count < 100 && $progress_count >= 80) {
						$action_scheduler_object->update_progress_count( 100 );
					} else if ( $progress_count >= 10 && $progress_count <= 80 ) {
						$percentage = $action_scheduler_object->get_progress_count() + 5;
						$action_scheduler_object->update_progress_count( $percentage );
					}
				}
						
				$percentage = $action_scheduler_object->get_progress_count();
				$response   = array(
					'percentage' => $percentage,
					'completed'  => 'no',
				);

				if ( 100 == $percentage ) {
					$response[ 'completed' ]    = 'yes';
					$response[ 'msg' ]          = $action_scheduler_object->get_success_message();
					$response[ 'redirect_url' ] = $action_scheduler_object->get_redirect_url();
				}

				wp_send_json_success( $response );
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}

		/**
		 * Update user selection type for report module. 
		 */

		public static function update_report_user_selection_format() {
			check_ajax_referer( 'fp-user-selection', 'sumo_security' );

			if ( ! isset( $_POST ) || ! isset( $_POST['selected_format'] ) ) {
				throw new exception( esc_html( 'Invalid Request', 'rewardsystem' ) );
			}

			try {
				update_option( 'selected_report_format', wc_clean( wp_unslash( $_POST['selected_format'] ) ) );
				wp_send_json_success();
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) );
			}
		}

		/**
		 * Display redeem point popup.
		 *
		 * @since 29.8.0
		 *
		 * @return void
		 */
		public static function srp_display_redeem_point_popup() {
			check_ajax_referer( 'srp-redeem-point-nonce', 'sumo_security' );

			try {
				if ( ! isset( $_POST ) ) {
					throw new exception( __( 'Invalid Request', 'rewardsystem' ) );
				}

				$order_id = ! empty( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
				$order = wc_get_order( $order_id );
				// Return if ID is not valid.
				if ( ! is_object( $order ) ) {
					throw new exception( __( 'No Order ID Found', 'rewardsystem' ) );
				}

				$user_id = ! empty( $_POST['user_id'] ) ? absint( $_POST['user_id'] ) : 0;
				if ( ! $user_id ) {
					throw new exception( __( 'Please select the user', 'rewardsystem' ) );
				}

				// Return if order items empty.
				if ( ! srp_check_is_array( $order->get_items() ) ) {
					throw new exception( __( 'No product(s) added. Please add item(s) to redeem the points', 'rewardsystem' ) );
				}

				$point_price = srp_pp_get_point_price_values( $order->get_items() );
				if ( srp_check_is_array( $point_price ) && isset($point_price['enable_point_price'])) {
					if ('yes' == $point_price['enable_point_price']) {
						throw new exception( __( 'You cannot apply the points for "Only Point Price" product(s).', 'rewardsystem' ) );
					}
				}

				$user_info = get_user_by('id', $user_id);
				$points_data      = new RS_Points_data( $user_id ) ;
				$available_points = $points_data->total_available_points() ;

				// Return if selected user doesn't have any points to apply.
				if ( empty( $available_points ) ) {
					throw new exception( __("The selected user doesn\'t have points on their account. Hence, you cannot apply points to this order", 'rewardsystem' ));
				}

				ob_start();
				include_once SRP_PLUGIN_PATH . '/includes/admin/views/html-redeem-point-popup.php';
				$html = ob_get_contents();
				ob_end_clean();

				wp_send_json_success( array( 'html' => $html ) );
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}

		/**
		 * Redeem point manually
		 *
		 * @since 29.8.0
		 *
		 * @return void
		 */
		public static function srp_redeem_point_manually() {
			check_ajax_referer( 'srp-redeem-point-nonce', 'sumo_security' );

			try {
				if ( ! isset( $_POST ) ) {
					throw new exception( __( 'Invalid Request', 'rewardsystem' ) );
				}

				$points_to_redeem    = isset( $_POST['point_value'] ) ? absint( $_POST['point_value'] ) : '';
				$order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : '';
				// Return if the points is empty.
				if ( ! $points_to_redeem ) {
					throw new exception( __( 'Please enter the points', 'rewardsystem' ) );
				}

				$user_id = ! empty( $_POST['user_id'] ) ? absint( $_POST['user_id'] ) : 0;
				$user_data = get_user_by( 'id', $user_id );
				$user_name = $user_data->user_login;
				$points_data  = new RS_Points_Data( $user_id );
				$available_points = $points_data->total_available_points();
				$redeemed_points = redeem_point_conversion( $points_to_redeem, $user_id );

				// Return if point entered is more than availabel points.
				if ( $points_to_redeem > $available_points ) {
					throw new exception( __( 'You have entered more than available points for selected user', 'rewardsystem' ) );
				}

				$order = wc_get_order( $order_id );

				$coupon_code = 'sumo_' . strtolower( $user_name );

				$old_coupon_id = get_user_meta( $user_id, 'redeemcouponids', true );
				wp_delete_post( $old_coupon_id, true );
				if ( class_exists( 'WC_Cache_Helper' ) ) {
					wp_cache_delete( WC_Cache_Helper::get_cache_prefix( 'coupons' ) . 'coupon_id_from_code_sumo_' . strtolower( $user_name ), 'coupons' );
				}

				$coupon_data = array(
					'post_title'   => $coupon_code,
					'post_content' => '',
					'post_status'  => 'publish',
					'post_author'  => $user_id,
					'post_type'    => 'shop_coupon',
				);
				
				$coupon_id   = wp_insert_post( $coupon_data );
				update_post_meta( $coupon_id, 'customer_email', $user_data->user_email );
				update_post_meta( $coupon_id, 'discount_type', 'fixed_cart' );
				$coupon_amnt     = redeem_point_conversion( $points_to_redeem, $user_id , 'price');
				$converted_point = redeem_point_conversion( $available_points, $user_id, 'price' );
				$amount         = ( $coupon_amnt > $available_points ) ? $converted_point : $coupon_amnt;
				update_post_meta( $coupon_id, 'coupon_amount', $amount );
				update_post_meta( $coupon_id, 'usage_count', '0' );
				update_post_meta( $coupon_id, 'usage_limit', '1' );
				update_post_meta( $coupon_id, 'expiry_date', '' );
				$apply_tax = ( '1' == get_option( 'rs_apply_redeem_before_tax' ) ) ? 'yes' : 'no';
				update_post_meta( $coupon_id, 'apply_before_tax', $apply_tax );
				$free_shipping = ( '1' == get_option( 'rs_apply_shipping_tax' ) ) ? 'yes' : 'no';
				update_post_meta( $coupon_id, 'free_shipping', $free_shipping );

				//Coupon Data in User id
				update_user_meta( $user_id, 'redeemcouponids', $coupon_id );

				$order->apply_coupon( $coupon_code );

				$order->calculate_totals();

				// Save Order.
				$order->save();

				wp_send_json_success( array( 'success' => __( 'Points Applied Successfully.', 'rewardsystem' ) ) );
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}

		/**
		 * Validate Gateway redeemed points
		 *
		 * @since 29.8.0
		 *
		 * @return void
		 */
		public static function validate_gateway_redeemed_points() {
			check_ajax_referer( 'srp-redeem-point-nonce', 'sumo_security' );

			try {
				if ( ! isset( $_POST ) ) {
					throw new exception( __( 'Invalid Request', 'rewardsystem' ) );
				}

				$order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : '';
				$order = wc_get_order( $order_id );
				if ( 'auto-draft' == $order->get_status() ) {
					$payment_method = ! empty( $_POST['payment_method'] ) ? absint( $_POST['payment_method'] ) : '';

					$user_id = ! empty( $_POST['user_id'] ) ? absint( $_POST['user_id'] ) : 0;
					if ( ! $user_id ) {
						throw new exception( __( 'Please select the user', 'rewardsystem' ) );
					}
	
					// Return if order items empty.
					if ( ! srp_check_is_array( $order->get_items() ) ) {
						throw new exception( __( 'No product(s) were added. Please add item(s) to place the order using the "SUMO Reward Points Payment Gateway".', 'rewardsystem' ) );
					}
	
					$points_data      = new RS_Points_data( $user_id ) ;
					$available_points = $points_data->total_available_points() ;
	
					if ( empty($available_points) ) {
						throw new exception( __("The selected user doesn\'t have points on their account. Hence, you cannot create the order", 'rewardsystem' ));
					}
	
					$user_data = get_user_by( 'id', $user_id );
					$user_name = $user_data->user_login;
					$coupon_code = 'sumo_' . strtolower( $user_name );
					if (srp_check_is_array($order->get_coupon_codes()) && ( in_array($coupon_code, $order->get_coupon_codes()) )) {
						/* translators: %1$s: Points needed %2$s: Available Points */
						throw new exception( __( 'Since you have applied the points, you cannot create this order using the "SUMO Reward Points Payment Gateway".', 'rewardsystem' ));
					}

					$point_price = srp_pp_get_point_price_values( $order->get_items() );
					$redeemed_points = srp_check_is_array($point_price) ? gateway_points( $order->get_id() ) : redeem_point_conversion( $order->get_total(), $user_id );
					if ( $redeemed_points > $available_points ) {
						/* translators: %1$s: Points needed %2$s: Available Points */
						throw new exception( sprintf(__( 'The selected user needs %1$s points to create this order but they have only %2$s points on their account', 'rewardsystem' ), $redeemed_points, $available_points));
					}
				}

				wp_send_json_success();
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}

		/**
		 * Validate point price product
		 *
		 * @since 29.8.0
		 *
		 * @return void
		 */
		public static function validate_point_price_product() {
			check_ajax_referer( 'srp-redeem-point-nonce', 'sumo_security' );

			try {
				if ( ! isset( $_POST ) ) {
					throw new exception( __( 'Invalid Request', 'rewardsystem' ) );
				}

				$order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : '';
				$order = wc_get_order( $order_id );
				if ( 'auto-draft' == $order->get_status() ) {
					$payment_method = ! empty( $_POST['payment_method'] ) ? absint( $_POST['payment_method'] ) : '';

					$user_id = ! empty( $_POST['user_id'] ) ? absint( $_POST['user_id'] ) : 0;
					if ( ! $user_id ) {
						throw new exception( __( 'Please select the user', 'rewardsystem' ) );
					}
	
					// Return if order items empty.
					if ( ! srp_check_is_array( $order->get_items() ) ) {
						throw new exception( __( 'No product(s) were added. Please add item(s) to place the order using the "SUMO Reward Points Payment Gateway".', 'rewardsystem' ) );
					}

					$point_price = srp_pp_get_point_price_values( $order->get_items() );
					if ( srp_check_is_array( $point_price ) && isset($point_price['enable_point_price'])) {
						if ('yes' == $point_price['enable_point_price']) {
							throw new exception( __( 'Since the order contains "Only Point Price" product, you can create this order only using "SUMO Reward Points Payment Gateway".', 'rewardsystem' ) );
						}
					}
				}

				wp_send_json_success();
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}
	}

	FP_Rewardsystem_Admin_Ajax::init() ;
}
