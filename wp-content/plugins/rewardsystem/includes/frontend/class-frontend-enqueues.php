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
            self::$in_footer = (get_option( 'rs_load_script_styles' ) == 'wp_footer') ? true : false ;
            add_action( 'wp_enqueue_scripts' , array( __CLASS__ , 'frontend_enqueue_script' ) ) ;
            add_action( 'wp_enqueue_scripts' , array( __CLASS__ , 'common_enqueue_script' ) ) ;
        }

        public static function common_enqueue_script() {
            wp_enqueue_script( 'jquery' ) ;
            wp_enqueue_script( 'jquery-ui-datepicker' ) ;
            wp_enqueue_script( 'select2' ) ;

            // Enqueue Datepicker CSS for my reward table date filter.
            if ( '1' == get_option( 'rs_show_or_hide_date_filter' ) ) {
                wp_register_style( 'wp_reward_jquery_ui_css' , SRP_PLUGIN_DIR_URL . "assets/css/jquery-ui.css" ) ;
                wp_enqueue_style( 'wp_reward_jquery_ui_css' ) ;
            }

            /* Enqueue Footable JS */
            if ( get_option( 'rs_enable_footable_js' , '1' ) == '1' ) {
                wp_enqueue_script( 'wp_reward_footable' , SRP_PLUGIN_DIR_URL . "assets/js/footable.js" , array() , SRP_VERSION , self::$in_footer ) ;
                wp_enqueue_script( 'wp_reward_footable_sort' , SRP_PLUGIN_DIR_URL . "assets/js/footable.sort.js" , array() , SRP_VERSION , self::$in_footer ) ;
                wp_enqueue_script( 'wp_reward_footable_paging' , SRP_PLUGIN_DIR_URL . "assets/js/footable.paginate.js" , array() , SRP_VERSION , self::$in_footer ) ;
                wp_enqueue_script( 'wp_reward_footable_filter' , SRP_PLUGIN_DIR_URL . "assets/js/footable.filter.js" , array() , SRP_VERSION , self::$in_footer ) ;
            }

            wp_enqueue_script( 'wp_jscolor_rewards' , SRP_PLUGIN_DIR_URL . "assets/js/jscolor/jscolor.js" , array( 'jquery' ) , SRP_VERSION , self::$in_footer ) ;
            wp_enqueue_script( 'frontendscripts' , SRP_PLUGIN_DIR_URL . "includes/frontend/js/frontendscripts.js" , array( 'jquery' ) , SRP_VERSION , self::$in_footer ) ;
            wp_localize_script( 'frontendscripts' , 'frontendscripts_params' , array(
                'ajaxurl'                             => SRP_ADMIN_AJAX_URL ,
                'generate_referral'                   => wp_create_nonce( 'generate-referral' ) ,
                'unset_referral'                      => wp_create_nonce( 'unset-referral' ) ,
                'unset_product'                       => wp_create_nonce( 'unset-product' ) ,
                'booking_msg'                         => wp_create_nonce( 'booking-msg' ) ,
                'variation_msg'                       => wp_create_nonce( 'variation-msg' ) ,
                'enable_option_nonce'                 => wp_create_nonce( 'earn-reward-points' ) ,
                'loggedinuser'                        => is_user_logged_in() ? "yes" : "no" ,
                'buttonlanguage'                      => get_option( 'rs_language_selection_for_button' ) ,
                'wplanguage'                          => get_option( 'WPLANG' ) ,
                'fbappid'                             => get_option( 'rs_facebook_application_id' ) ,
                'url'                                 => (get_option( 'rs_global_social_ok_url' ) == '1') ? get_permalink() : get_option( 'rs_global_social_ok_url_custom' ) ,
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
                'custom_date_error_message'           => esc_html__( 'From Date and To Date is mandatory' , SRP_LOCALE ) ,
                'default_selection_error_message'     => esc_html__( 'Please select any option' , SRP_LOCALE ) ,
            ) ) ;

            $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' ;
            wp_enqueue_script( 'wc-enhanced-select' , WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . $suffix . '.js' , array( 'jquery' , 'select2' ) , WC_VERSION , self::$in_footer ) ;
            wp_localize_script( 'wc-enhanced-select' , 'wc_enhanced_select_params' , array(
                'ajax_url'               => SRP_ADMIN_AJAX_URL ,
                'search_customers_nonce' => wp_create_nonce( 'search-customers' )
            ) ) ;
        }

        public static function frontend_enqueue_script() {
            $enqueue_array = array(
                'srp-productpurchase-modules' => array(
                    'callable' => array( 'RSFrontendEnqueues' , 'enqueue_for_productpurchase_module' ) ,
                    'restrict' => ((is_cart() || is_checkout()) && (get_option( 'rs_product_purchase_activated' ) == 'yes')) ,
                ) ,
                'srp-redeem-modules'          => array(
                    'callable' => array( 'RSFrontendEnqueues' , 'enqueue_for_redeem_module' ) ,
                    'restrict' => ((is_cart() || is_checkout()) && (get_option( 'rs_redeeming_activated' ) == 'yes')) ,
                ) ,
                'srp-action-modules'          => array(
                    'callable' => array( 'RSFrontendEnqueues' , 'enqueue_for_action_module' ) ,
                    'restrict' => (is_checkout() && (get_option( 'rs_reward_action_activated' ) == 'yes') || get_option( 'rs_product_purchase_activated' ) == 'yes') ,
                ) ,
                'srp-social-buttons'          => array(
                    'callable' => array( 'RSFrontendEnqueues' , 'enqueue_for_social_buttons' ) ,
                    'restrict' => get_option( 'rs_social_reward_activated' ) == 'yes' ,
                ) ,
                'srp-cashback-module'         => array(
                    'callable' => array( 'RSFrontendEnqueues' , 'enqueue_for_cashback_module' ) ,
                    'restrict' => get_option( 'rs_cashback_activated' ) == 'yes' ,
                ) ,
                'srp-giftvocuher-module'      => array(
                    'callable' => array( 'RSFrontendEnqueues' , 'enqueue_for_giftvoucher' ) ,
                    'restrict' => get_option( 'rs_gift_voucher_activated' ) == 'yes' ,
                ) ,
                'srp-email-module'            => array(
                    'callable' => array( 'RSFrontendEnqueues' , 'enqueue_for_email' ) ,
                    'restrict' => get_option( 'rs_email_activated' ) == 'yes' ,
                ) ,
                'srp-send-points-module'      => array(
                    'callable' => array( 'RSFrontendEnqueues' , 'enqueue_for_send_points' ) ,
                    'restrict' => get_option( 'rs_send_points_activated' ) == 'yes' ,
                ) ,
                'srp-nominee-module'          => array(
                    'callable' => array( 'RSFrontendEnqueues' , 'enqueue_for_nominee' ) ,
                    'restrict' => (get_option( 'rs_nominee_activated' ) == 'yes') ,
                ) ,
                    ) ;
            $enqueue_array = apply_filters( 'fp_srp_frontend_enqueue_scripts' , $enqueue_array ) ;
            if ( srp_check_is_array( $enqueue_array ) ) {
                foreach ( $enqueue_array as $key => $enqueue ) {
                    if ( srp_check_is_array( $enqueue ) ) {
                        if ( $enqueue[ 'restrict' ] )
                            call_user_func_array( $enqueue[ 'callable' ] , array() ) ;
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
            wp_enqueue_script( 'fp_productpurchase_frontend' , SRP_PLUGIN_DIR_URL . "includes/frontend/js/modules/fp-productpurchase-frontend.js" , array( 'jquery' ) , SRP_VERSION , self::$in_footer ) ;
            wp_localize_script( 'fp_productpurchase_frontend' , 'fp_productpurchase_frontend_params' , $LocalizedScript ) ;
        }

        public static function enqueue_for_redeem_module() {
            $PointsData      = new RS_Points_Data( get_current_user_id() ) ;
            $AvailablePoints = $PointsData->total_available_points() ;
            $FieldType       = is_cart() ? get_option( 'rs_redeem_field_type_option' ) : get_option( 'rs_redeem_field_type_option_checkout' ) ;
            $LocalizedScript = array(
                'ajaxurl'             => SRP_ADMIN_AJAX_URL ,
                'available_points'    => $AvailablePoints ,
                'minredeempoint'      => get_option( "rs_minimum_redeeming_points" ) ,
                'maxredeempoint'      => get_option( "rs_maximum_redeeming_points" ) ,
                'redeemingfieldtype'  => $FieldType ,
                'emptyerr'            => get_option( 'rs_redeem_empty_error_message' ) ,
                'numericerr'          => get_option( 'rs_redeem_character_error_message' ) ,
                'maxredeemederr'      => get_option( 'rs_redeem_max_error_message' ) ,
                'minmaxerr'           => ($FieldType == 1) ? do_shortcode( get_option( "rs_minimum_and_maximum_redeem_point_error_message" ) ) : do_shortcode( get_option( "rs_minimum_and_maximum_redeem_point_error_message_for_buttontype" ) ) ,
                'minerr'              => ($FieldType == 1) ? do_shortcode( get_option( "rs_minimum_redeem_point_error_message" ) ) : do_shortcode( get_option( "rs_minimum_redeem_point_error_message_for_button_type" ) ) ,
                'maxerr'              => ($FieldType == 1) ? do_shortcode( get_option( "rs_maximum_redeem_point_error_message" ) ) : do_shortcode( get_option( "rs_maximum_redeem_point_error_message_for_button_type" ) ) ,
                'checkoutredeemfield' => get_option( 'rs_show_hide_redeem_it_field_checkout' ) ,
                'page'                => is_cart() ? 'cart' : 'checkout' ,
                    ) ;
            wp_enqueue_script( 'fp_redeem_frontend' , SRP_PLUGIN_DIR_URL . "includes/frontend/js/modules/fp-redeem-frontend.js" , array( 'jquery' ) , SRP_VERSION , self::$in_footer ) ;
            wp_localize_script( 'fp_redeem_frontend' , 'fp_redeem_frontend_params' , $LocalizedScript ) ;
        }

        public static function enqueue_for_action_module() {
            $LocalizedScript = array(
                'ajaxurl'        => SRP_ADMIN_AJAX_URL ,
                'fp_gateway_msg' => wp_create_nonce( 'fp-gateway-msg' ) ,
                'user_id'        => get_current_user_id() ,
                    ) ;
            wp_enqueue_script( 'fp_action_frontend' , SRP_PLUGIN_DIR_URL . "includes/frontend/js/modules/fp-action-frontend.js" , array( 'jquery' ) , SRP_VERSION , self::$in_footer ) ;
            wp_localize_script( 'fp_action_frontend' , 'fp_action_frontend_params' , $LocalizedScript ) ;
        }

        public static function enqueue_for_social_buttons() {
            if ( get_option( 'rs_reward_point_enable_tipsy_social_rewards' ) == '1' ) {
                wp_enqueue_script( 'wp_reward_tooltip' , SRP_PLUGIN_DIR_URL . "assets/js/jquery.tipsy.js" , array( 'jquery' ) , SRP_VERSION , self::$in_footer ) ;
                wp_enqueue_style( 'wp_reward_tooltip_style' , SRP_PLUGIN_DIR_URL . "assets/css/tipsy.css" , array() , SRP_VERSION ) ;
            }
        }

        public static function enqueue_for_cashback_module() {
            $PointsData      = new RS_Points_Data( get_current_user_id() ) ;
            $AvailablePoints = $PointsData->total_available_points() ;
            $LocalizedScript = array(
                'ajaxurl'             => SRP_ADMIN_AJAX_URL ,
                'fp_cashback_request' => wp_create_nonce( 'fp-cashback-request' ) ,
                'fp_cancel_request'   => wp_create_nonce( 'fp-cancel-request' ) ,
                'available_points'    => $AvailablePoints ,
                'minpointstoreq'      => get_option( 'rs_minimum_points_encashing_request' ) == '' ? 0 : get_option( 'rs_minimum_points_encashing_request' ) ,
                'maxpointstoreq'      => get_option( 'rs_maximum_points_encashing_request' ) == '' ? $AvailablePoints : get_option( 'rs_maximum_points_encashing_request' ) ,
                'paymentmethod'       => get_option( 'rs_select_payment_method' ) ,
                'conversionrate'      => get_option( 'rs_redeem_point_for_cash_back' ) ,
                'conversionvalue'     => get_option( 'rs_redeem_point_value_for_cash_back' ) ,
                'redirection_type'    => get_option( 'rs_select_type_to_redirect' ) ,
                'redirection_url'     => get_option( 'rs_custom_page_url_after_submit' ) ,
                'enable_recaptcha'    => get_option( 'rs_enable_recaptcha_to_display' ) ,
                    ) ;
            wp_enqueue_script( 'fp_cashback_action' , SRP_PLUGIN_DIR_URL . "includes/frontend/js/modules/fp-cashback-frontend.js" , array( 'jquery' ) , SRP_VERSION , self::$in_footer ) ;
            wp_localize_script( 'fp_cashback_action' , 'fp_cashback_action_params' , $LocalizedScript ) ;
        }

        public static function enqueue_for_giftvoucher() {
            $LocalizedScript = array(
                'ajaxurl'           => SRP_ADMIN_AJAX_URL ,
                'error'             => addslashes( get_option( 'rs_voucher_redeem_empty_error' ) ) ,
                'fp_redeem_vocuher' => wp_create_nonce( 'fp-redeem-voucher' ) ,
                    ) ;
            wp_enqueue_script( 'fp_giftvoucher_frontend' , SRP_PLUGIN_DIR_URL . "includes/frontend/js/modules/fp-giftvoucher-frontend.js" , array( 'jquery' ) , SRP_VERSION , self::$in_footer ) ;
            wp_localize_script( 'fp_giftvoucher_frontend' , 'fp_giftvoucher_frontend_params' , $LocalizedScript ) ;
        }

        public static function enqueue_for_email() {
            $LocalizedScript = array(
                'ajaxurl'           => SRP_ADMIN_AJAX_URL ,
                'fp_subscribe_mail' => wp_create_nonce( 'fp-subscribe-mail' ) ,
                    ) ;
            wp_enqueue_script( 'fp_email_frontend' , SRP_PLUGIN_DIR_URL . "includes/frontend/js/modules/fp-email-frontend.js" , array( 'jquery' ) , SRP_VERSION , self::$in_footer ) ;
            wp_localize_script( 'fp_email_frontend' , 'fp_email_frontend_params' , $LocalizedScript ) ;
        }

        public static function enqueue_for_send_points() {
            $PointsData      = new RS_Points_Data( get_current_user_id() ) ;
            $sendpointslimit = ! empty( get_option( 'rs_limit_send_points_request' ) ) ? get_option( 'rs_limit_send_points_request' ) : 0 ;

            $limit_err       = str_replace( '{limitpoints}' , get_option( 'rs_limit_send_points_request' ) , get_option( "rs_err_when_point_greater_than_limit" ) ) ;
            $success_info    = (get_option( 'rs_request_approval_type' ) == '1') ? get_option( 'rs_message_send_point_request_submitted' ) : get_option( 'rs_message_send_point_request_submitted_for_auto' ) ;
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
            wp_enqueue_script( 'fp_sendpoint_frontend' , SRP_PLUGIN_DIR_URL . "includes/frontend/js/modules/fp-sendpoints-frontend.js" , array( 'jquery' ) , SRP_VERSION , self::$in_footer ) ;
            wp_localize_script( 'fp_sendpoint_frontend' , 'fp_sendpoint_frontend_params' , $LocalizedScript ) ;
        }

        public static function enqueue_for_nominee() {
            $LocalizedScript = array(
                'ajaxurl'         => SRP_ADMIN_AJAX_URL ,
                'fp_wc_version'   => WC_VERSION ,
                'fp_save_nominee' => wp_create_nonce( 'fp-save-nominee' ) ,
                    ) ;
            wp_enqueue_script( 'fp_nominee_frontend' , SRP_PLUGIN_DIR_URL . "includes/frontend/js/modules/fp-nominee-frontend.js" , array( 'jquery' ) , SRP_VERSION , self::$in_footer ) ;
            wp_localize_script( 'fp_nominee_frontend' , 'fp_nominee_frontend_params' , $LocalizedScript ) ;
        }

    }
}
