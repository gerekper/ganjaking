<?php

/*
 * Admin Side Enqueues
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSFrontendEnqueues' ) ) {

	class RSFrontendEnqueues {

		protected static $in_footer ;

		public static function init() {
			self::$in_footer = ( 'wp_footer' == get_option( 'rs_load_script_styles' ) ) ? true : false ;
			add_action( 'wp_enqueue_scripts' , array( __CLASS__ , 'frontend_enqueue_script' ) ) ;
			add_action( 'wp_enqueue_scripts' , array( __CLASS__ , 'common_enqueue_script' ) ) ;
		}

		public static function common_enqueue_script() {
			wp_enqueue_script( 'jquery' ) ;
			wp_enqueue_script( 'jquery-ui-datepicker' ) ;
						
						wp_enqueue_style( 'jquery-ui-style' , WC()->plugin_url() . '/assets/css/jquery-ui/jquery-ui.min.css' , array() , WC_VERSION ) ;
						wp_enqueue_style( 'fp-srp-style' , SRP_PLUGIN_DIR_URL . 'assets/css/style.css' , array() , SRP_VERSION , self::$in_footer) ;
			wp_enqueue_style( 'wp_reward_footable_css' , SRP_PLUGIN_DIR_URL . 'assets/css/footable.core.css' , array() , SRP_VERSION , self::$in_footer) ;

			if ( '1'  == get_option( 'rs_enable_reward_point_bootstrap' )) {
				wp_enqueue_style( 'wp_reward_bootstrap_css' , SRP_PLUGIN_DIR_URL . 'assets/css/bootstrap.css' , array() , SRP_VERSION , self::$in_footer) ;
			}

			if ('no' == get_option('rs_reward_point_dequeue_select2_css', 'no')) {
				wp_enqueue_script( 'select2' ) ;
				$assets_path = str_replace( array( 'http:' , 'https:' ) , '' , WC()->plugin_url() ) . '/assets/' ;
				wp_enqueue_style( 'select2' , $assets_path . 'css/select2.css', array(), SRP_VERSION , self::$in_footer) ;
								
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' ;
				wp_enqueue_script( 'wc-enhanced-select' , WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . $suffix . '.js' , array( 'jquery' , 'select2' ) , WC_VERSION , self::$in_footer ) ;
				wp_localize_script( 'wc-enhanced-select' , 'wc_enhanced_select_params' , array(
					'ajax_url'               => SRP_ADMIN_AJAX_URL ,
					'search_customers_nonce' => wp_create_nonce( 'search-customers' )
				) ) ;
			}

			if ( 'yes' == get_option( 'rs_reward_point_dequeue_select2' ) ) {
				wp_dequeue_script( 'edgt_select2' ) ;
			}

			if ( 'yes' == get_option( 'rs_reward_point_dequeue_recaptcha' ) ) {
				wp_dequeue_script( 'wp_google_recaptcha' ) ;
			}

			// Enqueue Datepicker CSS for my reward table date filter.
			if ( '1' == get_option( 'rs_show_or_hide_date_filter' ) ) {
				wp_register_style( 'wp_reward_jquery_ui_css' , SRP_PLUGIN_DIR_URL . 'assets/css/jquery-ui.css' , array(), SRP_VERSION) ;
				wp_enqueue_style( 'wp_reward_jquery_ui_css' ) ;
			}

			/* Enqueue Footable JS */
			if ( '1' == get_option( 'rs_enable_footable_js' , '1' )  ) {
				wp_enqueue_script( 'wp_reward_footable' , SRP_PLUGIN_DIR_URL . 'assets/js/footable.js' , array() , SRP_VERSION , self::$in_footer ) ;
				wp_enqueue_script( 'wp_reward_footable_sort' , SRP_PLUGIN_DIR_URL . 'assets/js/footable.sort.js' , array() , SRP_VERSION , self::$in_footer ) ;
				wp_enqueue_script( 'wp_reward_footable_paging' , SRP_PLUGIN_DIR_URL . 'assets/js/footable.paginate.js' , array() , SRP_VERSION , self::$in_footer ) ;
				wp_enqueue_script( 'wp_reward_footable_filter' , SRP_PLUGIN_DIR_URL . 'assets/js/footable.filter.js' , array() , SRP_VERSION , self::$in_footer ) ;
			}
			
			/* Enqueue JSColor */
			if ('1' == get_option('rs_enable_jscolor_js', 1)) {
				wp_enqueue_script( 'wp_jscolor_rewards' , SRP_PLUGIN_DIR_URL . 'assets/js/jscolor/jscolor.js' , array( 'jquery' ) , SRP_VERSION , self::$in_footer ) ;
			}
			
			wp_enqueue_script( 'frontendscripts' , SRP_PLUGIN_DIR_URL . 'includes/frontend/js/frontendscripts.js' , array( 'jquery' ) , SRP_VERSION , self::$in_footer ) ;
			wp_localize_script( 'frontendscripts' , 'frontendscripts_params' , array(
				'ajaxurl'                             => SRP_ADMIN_AJAX_URL ,
				'generate_referral'                   => wp_create_nonce( 'generate-referral' ) ,
				'unset_referral'                      => wp_create_nonce( 'unset-referral' ) ,
				'unset_product'                       => wp_create_nonce( 'unset-product' ) ,
				'booking_msg'                         => wp_create_nonce( 'booking-msg' ) ,
				'variation_msg'                       => wp_create_nonce( 'variation-msg' ) ,
				'enable_option_nonce'                 => wp_create_nonce( 'earn-reward-points' ) ,
				'loggedinuser'                        => is_user_logged_in() ? 'yes' : 'no' ,
				'buttonlanguage'                      => get_option( 'rs_language_selection_for_button' ) ,
				'wplanguage'                          => get_option( 'WPLANG' ) ,
				'fbappid'                             => get_option( 'rs_facebook_application_id' ) ,
				'url'                                 => ( get_option( 'rs_global_social_ok_url' ) == '1' ) ? get_permalink() : get_option( 'rs_global_social_ok_url_custom' ) ,
				'showreferralmsg'                     => get_option( 'rs_show_hide_message_for_variable_product_referral' ) ,
				'showearnmsg'                         => get_option( 'rs_show_hide_message_for_variable_in_single_product_page' ) ,
				'showearnmsg_guest'                   => get_option( 'rs_show_hide_message_for_variable_in_single_product_page_guest' ) ,
				'showpurchasemsg'                     => get_option( 'rs_show_hide_message_for_variable_product' ) ,
				'showbuyingmsg'                       => get_option( 'rs_show_hide_buy_points_message_for_variable_in_product' ) ,
				'productpurchasecheckbox'             => get_option( 'rs_product_purchase_activated' ) ,
				'buyingpointscheckbox'                => get_option( 'rs_buyingpoints_activated' ) ,
				'buyingmsg'                           => get_option( 'rs_show_hide_buy_point_message_for_variable_product' ) ,
				'variable_product_earnmessage'        => get_option( 'rs_enable_display_earn_message_for_variation_single_product' , 'no' ) ,
				'enqueue_footable'                    => get_option( 'rs_enable_footable_js' , '1' ) ,
				'check_purchase_notice_for_variation' => rs_check_product_purchase_notice_for_variation() ,
				'check_referral_notice_for_variation' => rs_check_referral_notice_variation() ,
				'check_buying_notice_for_variation'   => rs_check_buying_points_notice_for_variation() ,
				'is_product_page'                     => is_product() ,
				'is_date_filter_enabled'              => get_option( 'rs_show_or_hide_date_filter' ) ,
				'custom_date_error_message'           => esc_html__( 'From Date and To Date is mandatory' , 'rewardsystem' ) ,
				'default_selection_error_message'     => esc_html__( 'Please select any option' , 'rewardsystem' ) ,
								'is_user_logged_in'                   => is_user_logged_in(),
								'user_id'                             => get_current_user_id(),
								'unsub_link_error'                    => esc_html__( 'Unsubscribe link is invalid' , 'rewardsystem' ), 
								'unsub_link_success'                  => esc_html__( 'Successfully Unsubscribed' , 'rewardsystem' ),
								'site_url'                            => site_url(),
			) ) ;
						
						/* Enhanced JS */
			wp_enqueue_script( 'srp_enhanced' , SRP_PLUGIN_URL . '/assets/js/srp-enhanced.js' , array( 'jquery' , 'select2' ) , SRP_VERSION ) ;
			wp_localize_script( 'srp_enhanced' , 'srp_enhanced_params' , array(
				'srp_wc_version'                  => WC_VERSION ,
				'ajax_url'                        => SRP_ADMIN_AJAX_URL ,
				'fp_pages_and_posts_search_nonce' => wp_create_nonce( 'fp-pages-and-posts-search-nonce' ) ,
								'search_nonce'                    => wp_create_nonce( 'search-nonce' ) ,
				'search_customers'                => wp_create_nonce( 'search-customers' ) ,
				'search_products'                 => wp_create_nonce( 'search-products' ) ,
				'i18n_no_matches'                 => esc_html_x( 'No matches found' , 'enhanced select' , 'rewardsystem' ) ,
				'i18n_ajax_error'                 => esc_html_x( 'Loading failed' , 'enhanced select' , 'rewardsystem' ) ,
				'i18n_input_too_short_1'          => esc_html_x( 'Please enter 1 or more characters' , 'enhanced select' , 'rewardsystem' ) ,
				'i18n_input_too_short_n'          => esc_html_x( 'Please enter %qty% or more characters' , 'enhanced select' , 'rewardsystem' ) ,
				'i18n_input_too_long_1'           => esc_html_x( 'Please delete 1 character' , 'enhanced select' , 'rewardsystem' ) ,
				'i18n_input_too_long_n'           => esc_html_x( 'Please delete %qty% characters' , 'enhanced select' , 'rewardsystem' ) ,
				'i18n_selection_too_long_1'       => esc_html_x( 'You can only select 1 item' , 'enhanced select' , 'rewardsystem' ) ,
				'i18n_selection_too_long_n'       => esc_html_x( 'You can only select %qty% items' , 'enhanced select' , 'rewardsystem' ) ,
				'i18n_load_more'                  => esc_html_x( 'Loading more results&hellip;' , 'enhanced select' , 'rewardsystem' ) ,
				'i18n_searching'                  => esc_html_x( 'Searching&hellip;' , 'enhanced select' , 'rewardsystem' ) ,
			) ) ;
						
			wp_register_style( 'fp-srp-inline-style' , false , array() , SRP_VERSION ) ; // phpcs:ignore
			wp_enqueue_style( 'fp-srp-inline-style' ) ;
						
			//add inline style
			self::add_inline_style() ;
		}

		public static function frontend_enqueue_script() {
			$enqueue_array = array(
				'srp-productpurchase-modules' => array(
					'callable' => array( 'RSFrontendEnqueues' , 'enqueue_for_productpurchase_module' ) ,
					'restrict' => ( ( is_cart() || is_checkout() ) && ( 'yes' == get_option( 'rs_product_purchase_activated' ) ) ) ,
				) ,
				'srp-redeem-modules'          => array(
					'callable' => array( 'RSFrontendEnqueues' , 'enqueue_for_redeem_module' ) ,
					'restrict' => ( ( is_cart() || is_checkout() ) && ( 'yes' ==  get_option( 'rs_redeeming_activated' ) ) ) ,
				) ,
				'srp-action-modules'          => array(
					'callable' => array( 'RSFrontendEnqueues' , 'enqueue_for_action_module' ) ,
					'restrict' => ( is_checkout() && ( 'yes' == get_option( 'rs_reward_action_activated' ) ) || 'yes' == get_option( 'rs_product_purchase_activated' ) ) ,
				) ,
				'srp-social-buttons'          => array(
					'callable' => array( 'RSFrontendEnqueues' , 'enqueue_for_social_buttons' ) ,
					'restrict' => 'yes' == get_option( 'rs_social_reward_activated' ) ,
				) ,
				'srp-cashback-module'         => array(
					'callable' => array( 'RSFrontendEnqueues' , 'enqueue_for_cashback_module' ) ,
					'restrict' => 'yes' == get_option( 'rs_cashback_activated' ),
				) ,
				'srp-giftvocuher-module'      => array(
					'callable' => array( 'RSFrontendEnqueues' , 'enqueue_for_giftvoucher' ) ,
					'restrict' => 'yes' == get_option( 'rs_gift_voucher_activated' ) ,
				) ,
				'srp-email-module'            => array(
					'callable' => array( 'RSFrontendEnqueues' , 'enqueue_for_email' ) ,
					'restrict' => 'yes' == get_option( 'rs_email_activated' ) ,
				) ,
				'srp-send-points-module'      => array(
					'callable' => array( 'RSFrontendEnqueues' , 'enqueue_for_send_points' ) ,
					'restrict' => 'yes' == get_option( 'rs_send_points_activated' ),
				) ,
				'srp-nominee-module'          => array(
					'callable' => array( 'RSFrontendEnqueues' , 'enqueue_for_nominee' ) ,
					'restrict' => ( 'yes' == get_option( 'rs_nominee_activated' ) ) ,
				) ,
					) ;
			$enqueue_array = apply_filters( 'fp_srp_frontend_enqueue_scripts' , $enqueue_array ) ;
			if ( srp_check_is_array( $enqueue_array ) ) {
				foreach ( $enqueue_array as $key => $enqueue ) {
					if ( srp_check_is_array( $enqueue ) ) {
						if ( $enqueue[ 'restrict' ] ) {
							call_user_func_array( $enqueue[ 'callable' ] , array() ) ;
						}
					}
				}
			}
		}

		public static function enqueue_for_productpurchase_module() {
			$LocalizedScript = array(
				'ajaxurl'             => SRP_ADMIN_AJAX_URL ,
				'availablepointsmsgp' => is_cart() ? get_option( 'rs_available_pts_before_after_redeemed_pts_cart' ) : get_option( 'rs_available_pts_before_after_redeemed_pts_checkout' ) ,
				'page'                => is_cart() ? 'cart' : 'checkout' ,
					) ;
			wp_enqueue_script( 'fp_productpurchase_frontend' , SRP_PLUGIN_DIR_URL . 'includes/frontend/js/modules/fp-productpurchase-frontend.js' , array( 'jquery' ) , SRP_VERSION , self::$in_footer ) ;
			wp_localize_script( 'fp_productpurchase_frontend' , 'fp_productpurchase_frontend_params' , $LocalizedScript ) ;
		}

		public static function enqueue_for_redeem_module() {
			$PointsData      = new RS_Points_Data( get_current_user_id() ) ;
			$AvailablePoints = $PointsData->total_available_points() ;
			$FieldType       = is_cart() ? get_option( 'rs_redeem_field_type_option' ) : get_option( 'rs_redeem_field_type_option_checkout' ) ;
			$user                  = get_user_by( 'id', get_current_user_id() ) ;
			$username              = is_object( $user ) ? $user->user_login : '' ;
			$redeeming_coupon      = 'sumo_' . strtolower( "$username" ) ;
			$auto_redeeming_coupon = 'auto_redeem_' . strtolower( "$username" ) ;					
			
			$LocalizedScript = array(
				'ajaxurl'             => SRP_ADMIN_AJAX_URL ,
				'available_points'    => $AvailablePoints ,
				'minredeempoint'      => get_option( 'rs_minimum_redeeming_points' ) ,
				'maxredeempoint'      => get_option( 'rs_maximum_redeeming_points' ) ,
				'redeemingfieldtype'  => $FieldType ,
				'emptyerr'            => get_option( 'rs_redeem_empty_error_message' ) ,
				'numericerr'          => get_option( 'rs_redeem_character_error_message' ) ,
				'maxredeemederr'      => get_option( 'rs_redeem_max_error_message' ) ,
				'minmaxerr'           => ( 1 == $FieldType ) ? do_shortcode( get_option( 'rs_minimum_and_maximum_redeem_point_error_message' ) ) : do_shortcode( get_option( 'rs_minimum_and_maximum_redeem_point_error_message_for_buttontype' ) ) ,
				'minerr'              => ( 1 == $FieldType ) ? do_shortcode( get_option( 'rs_minimum_redeem_point_error_message' ) ) : do_shortcode( get_option( 'rs_minimum_redeem_point_error_message_for_button_type' ) ) ,
				'maxerr'              => ( 1 == $FieldType ) ? do_shortcode( get_option( 'rs_maximum_redeem_point_error_message' ) ) : do_shortcode( get_option( 'rs_maximum_redeem_point_error_message_for_button_type' ) ) ,
				'checkoutredeemfield' => get_option( 'rs_show_hide_redeem_it_field_checkout' ) ,
				'page'                => is_cart() ? 'cart' : 'checkout' ,
				'redeeming_coupon'      => $redeeming_coupon,
				'auto_redeeming_coupon' => $auto_redeeming_coupon		
						) ;
			wp_enqueue_script( 'fp_redeem_frontend' , SRP_PLUGIN_DIR_URL . 'includes/frontend/js/modules/fp-redeem-frontend.js' , array( 'jquery' ) , SRP_VERSION , self::$in_footer ) ;
			wp_localize_script( 'fp_redeem_frontend' , 'fp_redeem_frontend_params' , $LocalizedScript ) ;
		}

		public static function enqueue_for_action_module() {
			$LocalizedScript = array(
				'ajaxurl'        => SRP_ADMIN_AJAX_URL ,
				'fp_gateway_msg' => wp_create_nonce( 'fp-gateway-msg' ) ,
				'user_id'        => get_current_user_id() ,
					) ;
			wp_enqueue_script( 'fp_action_frontend' , SRP_PLUGIN_DIR_URL . 'includes/frontend/js/modules/fp-action-frontend.js' , array( 'jquery' ) , SRP_VERSION , self::$in_footer ) ;
			wp_localize_script( 'fp_action_frontend' , 'fp_action_frontend_params' , $LocalizedScript ) ;
		}

		public static function enqueue_for_social_buttons() {
			if ( '1' == get_option( 'rs_reward_point_enable_tipsy_social_rewards' ) ) {
				wp_enqueue_script( 'wp_reward_tooltip' , SRP_PLUGIN_DIR_URL . 'assets/js/jquery.tipsy.js' , array( 'jquery' ) , SRP_VERSION , self::$in_footer ) ;
				wp_enqueue_style( 'wp_reward_tooltip_style' , SRP_PLUGIN_DIR_URL . 'assets/css/tipsy.css' , array() , SRP_VERSION ) ;
			}
		}

		public static function enqueue_for_cashback_module() {
			$PointsData      = new RS_Points_Data( get_current_user_id() ) ;
			$AvailablePoints = $PointsData->total_available_points() ;
			$user            = wp_get_current_user() ;
			$roles           = is_object( $user ) ? $user->roles : '' ;
			$role            = isset( $roles[ 0 ] ) ? $roles[ 0 ] : '' ;
			$LocalizedScript = array(
				'ajaxurl'              => SRP_ADMIN_AJAX_URL ,
				'fp_cashback_request'  => wp_create_nonce( 'fp-cashback-request' ) ,
				'fp_cancel_request'    => wp_create_nonce( 'fp-cancel-request' ) ,
				'available_points'     => $AvailablePoints ,
				'minpointstoreq'       => 1 != get_option('rs_select_type_for_min_max_cashback', 1) || '' == get_option( 'rs_minimum_points_encashing_request' ) ? 0 : get_option( 'rs_minimum_points_encashing_request' ) ,
				'maxpointstoreq'       => 1 != get_option('rs_select_type_for_min_max_cashback', 1) || '' == get_option( 'rs_maximum_points_encashing_request' ) ? $AvailablePoints : get_option( 'rs_maximum_points_encashing_request' ) ,
				'paymentmethod'        => get_option( 'rs_select_payment_method' ) ,
				'conversionrate'       => get_option( 'rs_redeem_point_for_cash_back' ) ,
				'conversionvalue'      => get_option( 'rs_redeem_point_value_for_cash_back' ) ,
				'redirection_type'     => get_option( 'rs_select_type_to_redirect' ) ,
				'redirection_url'      => get_option( 'rs_custom_page_url_after_submit' ) ,
				'enable_recaptcha'     => get_option( 'rs_enable_recaptcha_to_display' ) ,
				'user_role_percentage' => get_option( 'rs_cashback_' . $role . '_for_redeem_percentage' , 100 ) ,
				'cash_back_reason'     => get_option( 'rs_reason_mandatory_for_cashback_form' , 'yes' ) ,
					) ;
			wp_enqueue_script( 'fp_cashback_action' , SRP_PLUGIN_DIR_URL . 'includes/frontend/js/modules/fp-cashback-frontend.js' , array( 'jquery' ) , SRP_VERSION , self::$in_footer ) ;
			wp_localize_script( 'fp_cashback_action' , 'fp_cashback_action_params' , $LocalizedScript ) ;
		}

		public static function enqueue_for_giftvoucher() {
			$LocalizedScript = array(
				'ajaxurl'           => SRP_ADMIN_AJAX_URL ,
				'error'             => addslashes( get_option( 'rs_voucher_redeem_empty_error' ) ) ,
				'fp_redeem_vocuher' => wp_create_nonce( 'fp-redeem-voucher' ) ,
					) ;
			wp_enqueue_script( 'fp_giftvoucher_frontend' , SRP_PLUGIN_DIR_URL . 'includes/frontend/js/modules/fp-giftvoucher-frontend.js' , array( 'jquery' ) , SRP_VERSION , self::$in_footer ) ;
			wp_localize_script( 'fp_giftvoucher_frontend' , 'fp_giftvoucher_frontend_params' , $LocalizedScript ) ;
		}

		public static function enqueue_for_email() {
			$LocalizedScript = array(
				'ajaxurl'           => SRP_ADMIN_AJAX_URL ,
				'fp_subscribe_mail' => wp_create_nonce( 'fp-subscribe-mail' ) ,
					) ;
			wp_enqueue_script( 'fp_email_frontend' , SRP_PLUGIN_DIR_URL . 'includes/frontend/js/modules/fp-email-frontend.js' , array( 'jquery' ) , SRP_VERSION , self::$in_footer ) ;
			wp_localize_script( 'fp_email_frontend' , 'fp_email_frontend_params' , $LocalizedScript ) ;
		}

		public static function enqueue_for_send_points() {
			$PointsData      = new RS_Points_Data( get_current_user_id() ) ;
			$sendpointslimit = ! empty( get_option( 'rs_limit_send_points_request' ) ) ? get_option( 'rs_limit_send_points_request' ) : 0 ;

			$limit_err       = str_replace( '{limitpoints}' , get_option( 'rs_limit_send_points_request' ) , get_option( 'rs_err_when_point_greater_than_limit' ) ) ;
			$success_info    = ( '1' == get_option( 'rs_request_approval_type' ) ) ? get_option( 'rs_message_send_point_request_submitted' ) : get_option( 'rs_message_send_point_request_submitted_for_auto' ) ;
			$LocalizedScript = array(
				'wp_ajax_url'                  => SRP_ADMIN_AJAX_URL ,
				'success_info'                 => $success_info ,
				'point_emp_err'                => get_option( 'rs_err_when_point_field_empty' ) ,
				'point_not_num'                => get_option( 'rs_err_when_point_is_not_number' ) ,
				'user_emty_err'                => '1' == get_option( 'rs_send_points_user_selection_field' , 1 ) ? get_option( 'rs_err_for_empty_user' ) : get_option( 'rs_username_empty_error_message' , 'Please enter the username/email id' ) ,
				'error_for_reason_field_empty' => get_option( 'rs_err_for_empty_reason_user' ) ,
				'send_points_reason'           => get_option( 'rs_reason_for_send_points_user' ) ,
				'limit_err'                    => $limit_err ,
				'user_id'                      => get_current_user_id() ,
				'currentuserpoint'             => round_off_type( $PointsData->total_available_points() ) ,
				'limittosendreq'               => $sendpointslimit ,
				'sendpointlimit'               => get_option( 'rs_limit_for_send_point' ) ,
				'username'                     => is_user_logged_in() ? get_user_by( 'id' , get_current_user_id() )->user_login : 'Guest' ,
				'selecttype'                   => get_option( 'rs_select_send_points_user_type' ) ,
				'user_selection_fieldtype'     => get_option( 'rs_send_points_user_selection_field' , 1 ) ,
				'errorforgreaterpoints'        => get_option( 'rs_error_msg_when_points_is_more' ) ,
				'errorforlesserpoints'         => get_option( 'rs_error_msg_when_points_is_less' ) ,
				'invalid_username_error'       => get_option( 'rs_invalid_username_error_message' , 'Please enter the valid username/email id' ) ,
				'restricted_username_error'    => get_option( 'rs_restricted_username_error_message' , 'This user has been restricted to receive points' ) ,
				'fp_user_search'               => wp_create_nonce( 'fp-user-search' ) ,
				'fp_send_points_data'          => wp_create_nonce( 'fp-send-points-data' ) ,
					) ;
			wp_enqueue_script( 'fp_sendpoint_frontend' , SRP_PLUGIN_DIR_URL . 'includes/frontend/js/modules/fp-sendpoints-frontend.js' , array( 'jquery' ) , SRP_VERSION , self::$in_footer ) ;
			wp_localize_script( 'fp_sendpoint_frontend' , 'fp_sendpoint_frontend_params' , $LocalizedScript ) ;
		}

		public static function enqueue_for_nominee() {
			$LocalizedScript = array(
				'ajaxurl'         => SRP_ADMIN_AJAX_URL ,
				'fp_wc_version'   => WC_VERSION ,
				'fp_save_nominee' => wp_create_nonce( 'fp-save-nominee' ) ,
					) ;
			wp_enqueue_script( 'fp_nominee_frontend' , SRP_PLUGIN_DIR_URL . 'includes/frontend/js/modules/fp-nominee-frontend.js' , array( 'jquery' ) , SRP_VERSION , self::$in_footer ) ;
			wp_localize_script( 'fp_nominee_frontend' , 'fp_nominee_frontend_params' , $LocalizedScript ) ;
		}
				
		/**
		 * Add Inline Style.
		 * */
		public static function add_inline_style() {
					
			$contents = '';
			$contents .= get_option( 'rs_myaccount_custom_css' );
			$contents .= get_option( 'rs_social_custom_css' );
			$contents .= get_option( 'rs_myaccount_custom_css' );
			$contents .= get_option( 'rs_refer_a_friend_custom_css' );
			$contents .= '1' == get_option( 'rs_encash_form_inbuilt_design' ) ? get_option( 'rs_encash_form_default_css' ) : get_option( 'rs_encash_form_custom_css' );
			$contents .= get_option( 'rs_general_custom_css' );
			$contents .= is_shop() ? get_option( 'rs_shop_page_custom_css' ):$contents;
			$contents .= is_product_category() ? get_option( 'rs_category_page_custom_css' ):$contents;
			$contents .= is_product() ? get_option( 'rs_single_product_page_custom_css' ):$contents;
			$contents .= is_cart() ? get_option( 'rs_cart_page_custom_css' ):$contents;
			$contents .= is_checkout() ? get_option( 'rs_checkout_page_custom_css' ):$contents;
			$contents .= '.fp_rs_display_free_product h3 {
                                            display:block;
					}
                                        .fb_edge_widget_with_comment span.fb_edge_comment_widget iframe.fb_ltr {
                                            display: none !important;
                                        }
                                        .fb-like{
                                            height: 20px !important;
                                            overflow: hidden !important;
                                        }
                                        .tipsy-inner {
                                            background-color: ' . get_option( 'rs_social_tooltip_bg_color' ) . ';
                                            color: ' . get_option( 'rs_social_tooltip_text_color' ) . ';
                                        }
                                        .tipsy-arrow-s {
                                            border-top-color: ' . get_option( 'rs_social_tooltip_bg_color' ) . ';
                                        }
                                        .points_empty_error, 
                                        .points_number_error, 
                                        .points_greater_than_earnpoints_error,
                                        .points_lesser_than_minpoints_error,
                                        .reason_empty_error,
                                        .paypal_email_empty_error,
                                        .paypal_email_format_error,
                                        .recaptcha_empty_error,
                                        .encash_form_success_info{
                                            display:none;
                                        }
                                        .referral_field{
                                            margin-top:40px;
                                        }
                                        .referral_field_title{
                                            text-align:center;
                                        }
                                        .rs_social_sharing_buttons {
                                            display: ' . get_option( 'rs_social_button_position_troubleshoot' ) . ';
                                        }
                                        .twitter-share-button,
                                        .vk-like{
                                            width:88px;
                                        }
                                        .ok-share-button{
                                            width:30px;
                                        }
                                        .fp-srp-point-price-label{
                                            margin-left:10px;
                                        }
                                        .referral_field1{
                                            margin-top:10px;
                                        }
                                        .rs_alert_div_for_copy{
                                            display:none;
                                        }
                                        .rs_warning_message{
                                            display:inline-block;
                                            color:red;
                                        }
                                        .rs_gift_voucher_submit_button{
                                            margin-left:10px;
                                        }
                                        .rs_redeem_voucher_error{
                                            color:red;
                                        }
                                        .rs_redeem_voucher_success{
                                            color:green;
                                        }
                                        .gifticon{
                                            width:16px;height:16px;
                                            display:inline;
                                        }
                                        .rs_variable_earn_messages{
                                            display:none;
                                        }
                                        .simpleshopmessage{
                                            width:16px;height:16px;
                                            display:inline;
                                        }
                                        .gift_icon{
                                            width:16px;height:16px;
                                            display:inline;
                                        }
                                        .variationrewardpoints,
                                        .variationreferralpoints,
                                        .variationpoint_price,
                                        .variationrewardpointsamount,
                                        .variationreferralpointsamount{
                                            display:inline-block;
                                        }
                                        .iagreeerror{
                                            display:none;
                                        }
                                        .fp-srp-send-point{
                                            border:none;
                                            padding: 6px 10px 6px 10px;
                                        }
                                        .fp-srp-send-point-value{
                                            min-width:250px !important;
                                            height:30px !important;
                                        }
                                        .fp-srp-point-price {
                                            margin-left: ' . get_option( 'rs_pixel_val' ) . 'px;
                                        }
                                        .fp-srp-email-content{
                                            border: 1px solid #000;
                                            border-collapse: collapse;
                                        }
                                        .fp-srp-email-content-title{
                                            background: black;
                                            color:#fff;
                                        }';
					
			if ( ! $contents ) {
				return ;
			}

			//Add custom css as inline style.
			wp_add_inline_style( 'fp-srp-inline-style' , $contents ) ;
		}

	}
}
