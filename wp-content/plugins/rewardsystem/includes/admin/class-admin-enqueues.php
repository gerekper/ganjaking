<?php

/*
 * Admin Side Enqueues
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSAdminEnqueues' ) ) {

    class RSAdminEnqueues {

        public static function init() {
            add_action( 'admin_enqueue_scripts' , array( __CLASS__ , 'admin_enqueue_script' ) ) ;

            add_action( 'admin_enqueue_scripts' , array( __CLASS__ , 'common_enqueue_script' ) ) ;
        }

        public static function common_enqueue_script() {
            wp_enqueue_script( 'jquery' ) ;

            if ( isset( $_GET[ 'page' ] ) && ($_GET[ 'page' ] == 'rewardsystem_callback') )
                wp_enqueue_script( 'jquery-ui-datepicker' ) ;

            wp_enqueue_script( 'rewardsystem_admin' , SRP_PLUGIN_DIR_URL . "assets/js/tab/admin.js" , array() , SRP_VERSION ) ;
            wp_enqueue_script( 'fp_edit_product_page' , SRP_PLUGIN_DIR_URL . "assets/js/tab/fp-edit-product-page.js" , array() , SRP_VERSION ) ;

            /* Enqueue Footable JS */
            if ( get_option( 'rs_enable_footable_js' , '1' ) == '1' ) {
                wp_enqueue_script( 'wp_reward_footable' , SRP_PLUGIN_DIR_URL . "assets/js/footable.js" , array() , SRP_VERSION ) ;
                wp_enqueue_script( 'wp_reward_footable_sort' , SRP_PLUGIN_DIR_URL . "assets/js/footable.sort.js" , array() , SRP_VERSION ) ;
                wp_enqueue_script( 'wp_reward_footable_paging' , SRP_PLUGIN_DIR_URL . "assets/js/footable.paginate.js" , array() , SRP_VERSION ) ;
                wp_enqueue_script( 'wp_reward_footable_filter' , SRP_PLUGIN_DIR_URL . "assets/js/footable.filter.js" , array() , SRP_VERSION ) ;
            }

            /* Enhanced JS */
            wp_enqueue_script( 'srp_enhanced' , SRP_PLUGIN_DIR_URL . "assets/js/srp-enhanced.js" , array( 'jquery' , 'select2' ) , SRP_VERSION ) ;
            wp_localize_script( 'srp_enhanced' , 'srp_enhanced_params' , array(
                'srp_wc_version' => WC_VERSION ,
            ) ) ;

            wp_enqueue_style( 'wp_reward_facebook' , SRP_PLUGIN_DIR_URL . "assets/css/style.css" , array() , SRP_VERSION ) ;
            wp_enqueue_style( 'wp_reward_footable_css' , SRP_PLUGIN_DIR_URL . "assets/css/footable.core.css" , array() , SRP_VERSION ) ;
            wp_enqueue_style( 'wp_reward_bootstrap_css' , SRP_PLUGIN_DIR_URL . "assets/css/bootstrap.css" , array() , SRP_VERSION ) ;
        }

        public static function admin_enqueue_script() {
            $page          = (isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'rewardsystem_callback') ;
            $enqueue_array = array(
                'srp-general-tab'                        => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_general_tab' ) ,
                    'restrict' => $page || (isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'fprsgeneral') ,
                ) ,
                'srp-modules-tab'                        => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_modules_tab' ) ,
                    'restrict' => $page && (isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'fprsmodules') ,
                ) ,
                'srp-addremovepoints-tab'                => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_addremovepoints_tab' ) ,
                    'restrict' => $page && (isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'fprsaddremovepoints') ,
                ) ,
                'srp-message-tab'                        => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_message_tab' ) ,
                    'restrict' => $page && (isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'fprsmessage') ,
                ) ,
                'srp-userrewardpoints-tab'               => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_userrewardpoints_tab' ) ,
                    'restrict' => $page && (isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'fprsuserrewardpoints') ,
                ) ,
                'srp-masterlog-tab'                      => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_master_log_tab' ) ,
                    'restrict' => $page && (isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'fprsmasterlog') ,
                ) ,
                'srp-advance-tab'                        => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_advance_tab' ) ,
                    'restrict' => $page && (isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'fprsadvanced') ,
                ) ,
                'srp-product-purchase-modules-tab'       => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_product_purchase_modules_tab' ) ,
                    'restrict' => isset( $_GET[ 'section' ] ) && ($_GET[ 'section' ] == 'fpproductpurchase' || $_GET[ 'section' ] == 'fpreferralsystem') ,
                ) ,
                'srp-buying-points-modules-tab'          => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_buying_points_modules_tab' ) ,
                    'restrict' => isset( $_GET[ 'section' ] ) && ($_GET[ 'section' ] == 'fpbuyingpoints') ,
                ) ,
                'srp-referral-modules-tab'               => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_referral_modules_tab' ) ,
                    'restrict' => isset( $_GET[ 'section' ] ) && $_GET[ 'section' ] == 'fpreferralsystem' ,
                ) ,
                'srp-social-modules-tab'                 => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_social_modules_tab' ) ,
                    'restrict' => isset( $_GET[ 'section' ] ) && $_GET[ 'section' ] == 'fpsocialreward' ,
                ) ,
                'srp-action-modules-tab'                 => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_action_modules_tab' ) ,
                    'restrict' => isset( $_GET[ 'section' ] ) && $_GET[ 'section' ] == 'fpactionreward' ,
                ) ,
                'srp-redeem-modules-tab'                 => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_redeem_modules_tab' ) ,
                    'restrict' => isset( $_GET[ 'section' ] ) && $_GET[ 'section' ] == 'fpredeeming' ,
                ) ,
                'srp-pointprice-modules-tab'             => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_pointprice_modules_tab' ) ,
                    'restrict' => isset( $_GET[ 'section' ] ) && $_GET[ 'section' ] == 'fppointprice' ,
                ) ,
                'srp-email-modules-tab'                  => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_email_modules_tab' ) ,
                    'restrict' => isset( $_GET[ 'section' ] ) && $_GET[ 'section' ] == 'fpmail' ,
                ) ,
                'srp-emailexpired-modules-tab'           => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_emailexpired_modules_tab' ) ,
                    'restrict' => isset( $_GET[ 'section' ] ) && $_GET[ 'section' ] == 'fpemailexpiredpoints' ,
                ) ,
                'srp-giftvoucher-modules-tab'            => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_giftvoucher_modules_tab' ) ,
                    'restrict' => isset( $_GET[ 'section' ] ) && $_GET[ 'section' ] == 'fpgiftvoucher' ,
                ) ,
                'srp-sms-modules-tab'                    => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_sms_modules_tab' ) ,
                    'restrict' => isset( $_GET[ 'section' ] ) && $_GET[ 'section' ] == 'fpsms' ,
                ) ,
                'srp-coupon-modules-tab'                 => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_coupon_modules_tab' ) ,
                    'restrict' => isset( $_GET[ 'section' ] ) && $_GET[ 'section' ] == 'fpcoupon' ,
                ) ,
                'srp-pointurl-modules-tab'               => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_pointurl_modules_tab' ) ,
                    'restrict' => isset( $_GET[ 'section' ] ) && $_GET[ 'section' ] == 'fppointurl' ,
                ) ,
                'srp-impexp-modules-tab'                 => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_impexp_modules_tab' ) ,
                    'restrict' => isset( $_GET[ 'section' ] ) && $_GET[ 'section' ] == 'fpimportexport' ,
                ) ,
                'srp-cashback-modules-tab'               => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_cashback_modules_tab' ) ,
                    'restrict' => isset( $_GET[ 'section' ] ) && $_GET[ 'section' ] == 'fpcashback' ,
                ) ,
                'srp-rewardgateway-modules-tab'          => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_reward_gateway_modules_tab' ) ,
                    'restrict' => isset( $_GET[ 'section' ] ) && $_GET[ 'section' ] == 'fprewardgateway' ,
                ) ,
                'srp-nominee-modules-tab'                => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_nominee_modules_tab' ) ,
                    'restrict' => isset( $_GET[ 'section' ] ) && $_GET[ 'section' ] == 'fpnominee' ,
                ) ,
                'srp-sendpoints-modules-tab'             => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_sendpoints_modules_tab' ) ,
                    'restrict' => isset( $_GET[ 'section' ] ) && $_GET[ 'section' ] == 'fpsendpoints' ,
                ) ,
                'srp-reportsinscv-modules-tab'           => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_reportsincsv_modules_tab' ) ,
                    'restrict' => isset( $_GET[ 'section' ] ) && $_GET[ 'section' ] == 'fpreportsincsv' ,
                ) ,
                'srp-reset-modules-tab'                  => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_reset_modules_tab' ) ,
                    'restrict' => isset( $_GET[ 'section' ] ) && $_GET[ 'section' ] == 'fpreset' ,
                ) ,
                'srp-discount-compatibility-modules-tab' => array(
                    'callable' => array( 'RSAdminEnqueues' , 'enqueue_for_discount_compatibility_modules_tab' ) ,
                    'restrict' => isset( $_GET[ 'section' ] ) && $_GET[ 'section' ] == 'fpdiscounts' ,
                ) ,
                    ) ;
            if ( isset( $_GET[ 'page' ] ) && ($_GET[ 'page' ] == 'rewardsystem_callback') ) {
                $enqueue_array = apply_filters( 'fp_srp_admin_enqueue_scripts' , $enqueue_array ) ;
                if ( srp_check_is_array( $enqueue_array ) ) {
                    foreach ( $enqueue_array as $key => $enqueue ) {
                        if ( srp_check_is_array( $enqueue ) ) {
                            if ( $enqueue[ 'restrict' ] )
                                call_user_func_array( $enqueue[ 'callable' ] , array() ) ;
                        }
                    }
                }
            }
        }

        public static function enqueue_for_general_tab() {
            $redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bg_process_to_refresh_points' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            $isadmin      = is_admin() ? 'yes' : 'no' ;
            wp_enqueue_script( 'fp_general_tab' , SRP_PLUGIN_DIR_URL . "assets/js/tab/fp-general-tab.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_localize_script( 'fp_general_tab' , 'fp_general_tab_params' , array(
                'ajaxurl'           => SRP_ADMIN_AJAX_URL ,
                'fp_refresh_points' => wp_create_nonce( 'fp-refresh-points' ) ,
                'fp_wc_version'     => WC_VERSION ,
                'isadmin'           => $isadmin ,
                'redirect'          => $redirect_url
            ) ) ;
        }

        public static function enqueue_for_modules_tab() {
            wp_enqueue_script( 'wp_jscolor_rewards' , SRP_PLUGIN_DIR_URL . "assets/js/jscolor/jscolor.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_enqueue_script( 'fp_module_tab' , SRP_PLUGIN_DIR_URL . "assets/js/tab/fp-module-tab.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_localize_script( 'fp_module_tab' , 'fp_module_tab_params' , array(
                'ajaxurl'            => SRP_ADMIN_AJAX_URL ,
                'fp_activate_module' => wp_create_nonce( 'fp-activate-module' ) ,
                'fp_wc_version'      => WC_VERSION ,
                'redirecturl'        => admin_url( 'admin.php?page=rewardsystem_callback&tab=fprsmodules' ) ,
                'activeclass'        => 'active_rs_box' ,
                'inactiveclass'      => 'inactive_rs_box' ,
                'section'            => isset( $_GET[ 'section' ] ) ? true : false ,
            ) ) ;
        }

        public static function enqueue_for_addremovepoints_tab() {
            $redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bg_process_to_add_points' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            $isadmin      = is_admin() ? 'yes' : 'no' ;
            wp_enqueue_script( 'fp_addremovepoints_tab' , SRP_PLUGIN_DIR_URL . "assets/js/tab/fp-addremovepoints-tab.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_localize_script( 'fp_addremovepoints_tab' , 'fp_addremovepoints_tab_params' , array(
                'ajaxurl'            => SRP_ADMIN_AJAX_URL ,
                'pointerrormsg'      => esc_html__( 'Please Enter Points' , SRP_LOCALE ) ,
                'reasomerrormsg'     => esc_html__( 'Please Enter Reason' , SRP_LOCALE ) ,
                'expirydateerrormsg' => esc_html__( 'Please enter the valid expiry date' , SRP_LOCALE ) ,
                'current_date'       => date( 'Y-m-d' ) ,
                'fp_add_points'      => wp_create_nonce( 'fp-add-points' ) ,
                'fp_remove_points'   => wp_create_nonce( 'fp-remove-points' ) ,
                'isadmin'            => $isadmin ,
                'redirect'           => $redirect_url
            ) ) ;
        }

        public static function enqueue_for_message_tab() {
            if ( function_exists( 'wp_enqueue_media' ) ) {
                wp_enqueue_media() ;
            } else {
                wp_enqueue_style( 'thickbox' ) ;
                wp_enqueue_script( 'media-upload' ) ;
                wp_enqueue_script( 'thickbox' ) ;
            }
            wp_enqueue_script( 'fp_msg_tab' , SRP_PLUGIN_DIR_URL . "assets/js/tab/fp-msg-tab.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_enqueue_script( 'wp_reward_jquery_ui' , SRP_PLUGIN_DIR_URL . "assets/js/jquery-ui.js" , array( 'jquery' ) , SRP_VERSION ) ;
        }

        public static function enqueue_for_userrewardpoints_tab() {
            $UserId          = isset( $_GET[ 'edit' ] ) ? $_GET[ 'edit' ] : 0 ;
            $PointsData      = new RS_Points_Data( $UserId ) ;
            $Points          = $PointsData->total_available_points() ;
            $localize_script = array(
                'ajaxurl'                              => SRP_ADMIN_AJAX_URL ,
                'fp_wc_version'                        => WC_VERSION ,
                'available_points'                     => $Points ,
                'restrict_user'                        => ('yes' == get_option( 'rs_enable_reward_program' ) ) ? get_user_meta( $UserId , 'allow_user_to_earn_reward_points' , true ) : '' ,
                'restrict_add_points_error_message'    => esc_html__( 'As of now, this user is not involved in the reward program. Hence, you cannot add points to this user account.' , SRP_LOCALE ) ,
                'restrict_remove_points_error_message' => esc_html__( 'As of now, this user is not involved in the reward program. Hence, you cannot remove points from this user account.' , SRP_LOCALE ) ,
                'restrict_user_points_nonce'           => wp_create_nonce( 'srp-restrict-user-points-nonce' ) ,
                'hide_filter'                          => (isset( $_GET[ 'view' ] ) || isset( $_GET[ 'edit' ] ))
                    ) ;
            wp_enqueue_script( 'fp_userrewardpoints_tab' , SRP_PLUGIN_DIR_URL . "assets/js/tab/fp-userrewardpoints-tab.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_localize_script( 'fp_userrewardpoints_tab' , 'fp_userrewardpoints_tab_params' , $localize_script ) ;
        }

        public static function enqueue_for_master_log_tab() {
            $redirect_url    = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bg_process_to_export_log' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            $localize_script = array(
                'ajaxurl'       => SRP_ADMIN_AJAX_URL ,
                'redirecturl'   => $redirect_url ,
                'fp_wc_version' => WC_VERSION ,
                'fp_export_log' => wp_create_nonce( 'fp-export-log' ) ,
                    ) ;
            wp_enqueue_script( 'fp_masterlog_tab' , SRP_PLUGIN_DIR_URL . "assets/js/tab/fp-masterlog-tab.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_localize_script( 'fp_masterlog_tab' , 'fp_masterlog_params' , $localize_script ) ;
        }

        public static function enqueue_for_advance_tab() {
            $redirect_url    = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bg_process_to_apply_points' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            $localize_script = array(
                'ajaxurl'         => SRP_ADMIN_AJAX_URL ,
                'redirecturl'     => $redirect_url ,
                'fp_wc_version'   => WC_VERSION ,
                'fp_apply_points' => wp_create_nonce( 'fp-apply-points' ) ,
                'fp_old_points'   => wp_create_nonce( 'fp-old-points' ) ,
                    ) ;
            wp_enqueue_script( 'fp_advance_tab' , SRP_PLUGIN_DIR_URL . "assets/js/tab/fp-advance-tab.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_localize_script( 'fp_advance_tab' , 'fp_advance_params' , $localize_script ) ;
        }

        public static function enqueue_for_product_purchase_modules_tab() {
            $redirect_url    = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bg_process_to_bulk_update' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            $localize_script = array(
                'ajaxurl'                      => SRP_ADMIN_AJAX_URL ,
                'redirecturl'                  => $redirect_url ,
                'fp_wc_version'                => WC_VERSION ,
                'fp_bulk_update'               => wp_create_nonce( 'fp-bulk-update' ) ,
                'product_purchase_bulk_update' => wp_create_nonce( 'product-purchase-bulk-update' ) ,
                    ) ;
            wp_enqueue_script( 'fp_product_purchase_module' , SRP_PLUGIN_DIR_URL . "assets/js/tab/modules/fp-productpurchase-module.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_localize_script( 'fp_product_purchase_module' , 'fp_product_purchase_module_param' , $localize_script ) ;
        }

        public static function enqueue_for_buying_points_modules_tab() {
            $buyingredirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bg_process_to_buying_points_bulk_update' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            $localize_script    = array(
                'ajaxurl'                   => SRP_ADMIN_AJAX_URL ,
                'buyingredirecturl'         => $buyingredirect_url ,
                'fp_wc_version'             => WC_VERSION ,
                'buying_reward_bulk_update' => wp_create_nonce( 'buying-reward-bulk-update' ) ,
                    ) ;
            wp_enqueue_script( 'fp_buyingpoints_module' , SRP_PLUGIN_DIR_URL . "assets/js/tab/modules/fp-buyingpoints-module.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_localize_script( 'fp_buyingpoints_module' , 'fp_buyingpoints_module_param' , $localize_script ) ;
        }

        public static function enqueue_for_referral_modules_tab() {
            if ( function_exists( 'wp_enqueue_media' ) ) {
                wp_enqueue_media() ;
            } else {
                wp_enqueue_style( 'thickbox' ) ;
                wp_enqueue_script( 'media-upload' ) ;
                wp_enqueue_script( 'thickbox' ) ;
            }
            wp_enqueue_script( 'fp_referral_module' , SRP_PLUGIN_DIR_URL . "assets/js/tab/modules/fp-referral-module.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_localize_script( 'fp_referral_module' , 'fp_referral_module_params' , array(
                'ajaxurl'       => SRP_ADMIN_AJAX_URL ,
                'fp_wc_version' => WC_VERSION ,
            ) ) ;
        }

        public static function enqueue_for_social_modules_tab() {
            $redirect_url    = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bulk_update_for_social_reward' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            $localize_script = array(
                'ajaxurl'                   => SRP_ADMIN_AJAX_URL ,
                'redirecturl'               => $redirect_url ,
                'social_reward_bulk_update' => wp_create_nonce( 'social-reward-bulk-update' ) ,
                'fp_wc_version'             => WC_VERSION ,
                    ) ;
            wp_enqueue_script( 'fp_social_module' , SRP_PLUGIN_DIR_URL . "assets/js/tab/modules/fp-social-module.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_localize_script( 'fp_social_module' , 'fp_social_params' , $localize_script ) ;
        }

        public static function enqueue_for_action_modules_tab() {
            $localize_script = array(
                'ajaxurl'                     => SRP_ADMIN_AJAX_URL ,
                'cus_reg_fields_nonce'        => wp_create_nonce( 'srp-cus-reg-fields-nonce' ) ,
                'add_coupon_usage_rule_nonce' => wp_create_nonce( 'srp-add-coupon-usage-rule-nonce' ) ,
                    ) ;
            wp_enqueue_script( 'fp_action_module' , SRP_PLUGIN_DIR_URL . "assets/js/tab/modules/fp-action-module.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_localize_script( 'fp_action_module' , 'fp_action_params' , $localize_script ) ;
        }

        public static function enqueue_for_redeem_modules_tab() {
            $localize_script = array(
                'ajaxurl'       => SRP_ADMIN_AJAX_URL ,
                'fp_wc_version' => WC_VERSION ,
                    ) ;
            wp_enqueue_script( 'fp_redeem_module' , SRP_PLUGIN_DIR_URL . "assets/js/tab/modules/fp-redeem-module.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_localize_script( 'fp_redeem_module' , 'fp_redeem_module_params' , $localize_script ) ;
        }

        public static function enqueue_for_pointprice_modules_tab() {
            $redirect_url    = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bg_process_to_bulk_update_point_price' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            $localize_script = array(
                'ajaxurl'                 => SRP_ADMIN_AJAX_URL ,
                'redirecturl'             => $redirect_url ,
                'fp_wc_version'           => WC_VERSION ,
                'point_price_bulk_update' => wp_create_nonce( 'points-price-bulk-update' ) ,
                    ) ;
            wp_enqueue_script( 'fp_pointprice_module' , SRP_PLUGIN_DIR_URL . "assets/js/tab/modules/fp-pointprice-module.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_localize_script( 'fp_pointprice_module' , 'fp_pointprice_module_param' , $localize_script ) ;
        }

        public static function enqueue_for_email_modules_tab() {
            $localize_script = array(
                'ajaxurl'              => SRP_ADMIN_AJAX_URL ,
                'fp_wc_version'        => WC_VERSION ,
                'fp_unsubscribe_email' => wp_create_nonce( 'fp-unsubscribe-email' ) ,
                'fp_update_status'     => wp_create_nonce( 'fp-update-status' ) ,
                'fp_delete_template'   => wp_create_nonce( 'fp-delete-template' ) ,
                'fp_new_template'      => wp_create_nonce( 'fp-new-template' ) ,
                'fp_edit_template'     => wp_create_nonce( 'fp-edit-template' ) ,
                'template_id'          => isset( $_GET[ 'rs_edit_email' ] ) ? $_GET[ 'rs_edit_email' ] : 0 ,
                'admin_email'          => get_option( 'admin_email' ) ,
                'fp_send_mail'         => wp_create_nonce( 'fp-send-mail' ) ,
                'save_new_template'    => isset( $_GET[ 'rs_new_email' ] ) ,
                'save_edited_template' => isset( $_GET[ 'rs_edit_email' ] )
                    ) ;
            wp_enqueue_script( 'fp_email_module' , SRP_PLUGIN_DIR_URL . "assets/js/tab/modules/fp-email-module.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_localize_script( 'fp_email_module' , 'fp_email_params' , $localize_script ) ;
        }

        public static function enqueue_for_giftvoucher_modules_tab() {
            $redirect_url    = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bg_process_to_generate_voucher_code' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            $localize_script = array(
                'ajaxurl'        => SRP_ADMIN_AJAX_URL ,
                'redirecturl'    => $redirect_url ,
                'fp_wc_version'  => WC_VERSION ,
                'date'           => date( 'Y - m - d' ) ,
                'prefix'         => __( 'Prefix value Should not be Empty' , SRP_LOCALE ) ,
                'suffix'         => __( 'Suffix value Should not be Empty' , SRP_LOCALE ) ,
                'character'      => __( 'Number of Characters for Voucher Code Should not be Empty' , SRP_LOCALE ) ,
                'points'         => __( 'Reward Points Value per Voucher Code Generated Should not be Empty' , SRP_LOCALE ) ,
                'noofcodes'      => __( 'Number of Voucher Codes to be Generated Should not be Empty' , SRP_LOCALE ) ,
                'fp_create_code' => wp_create_nonce( 'fp-create-code' ) ,
                    ) ;
            wp_enqueue_script( 'fp_giftvoucher_module' , SRP_PLUGIN_DIR_URL . "assets/js/tab/modules/fp-giftvoucher-module.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_localize_script( 'fp_giftvoucher_module' , 'fp_giftvoucher_module_param' , $localize_script ) ;
            wp_enqueue_script( 'wp_reward_jquery_ui' , SRP_PLUGIN_DIR_URL . "assets/js/jquery-ui.js" , array() , SRP_VERSION ) ;
        }

        public static function enqueue_for_sms_modules_tab() {
            wp_enqueue_script( 'fp_sms_module' , SRP_PLUGIN_DIR_URL . "assets/js/tab/modules/fp-sms-module.js" , array( 'jquery' ) , SRP_VERSION ) ;
        }

        public static function enqueue_for_coupon_modules_tab() {
            wp_enqueue_script( 'fp_coupon_module' , SRP_PLUGIN_DIR_URL . "assets/js/tab/modules/fp-coupon-module.js" , array( 'jquery' ) , SRP_VERSION ) ;
        }

        public static function enqueue_for_impexp_modules_tab() {
            $redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bg_process_to_export_points' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            wp_enqueue_script( 'fp_impexp_module' , SRP_PLUGIN_DIR_URL . "assets/js/tab/modules/fp-importexport-module.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_localize_script( 'fp_impexp_module' , 'fp_impexp_module_params' , array(
                'ajaxurl'           => SRP_ADMIN_AJAX_URL ,
                'fp_export_points'  => wp_create_nonce( 'fp-export-points' ) ,
                'fp_start_date'     => wp_create_nonce( 'fp-start-date' ) ,
                'fp_end_date'       => wp_create_nonce( 'fp-end-date' ) ,
                'fp_user_selection' => wp_create_nonce( 'fp-user-selection' ) ,
                'fp_date_type'      => wp_create_nonce( 'fp-date-type' ) ,
                'fp_wc_version'     => WC_VERSION ,
                'redirect'          => $redirect_url
            ) ) ;
        }

        public static function enqueue_for_cashback_modules_tab() {
            wp_enqueue_script( 'fp_cashback_module' , SRP_PLUGIN_DIR_URL . "assets/js/tab/modules/fp-cashback-module.js" , array( 'jquery' ) , SRP_VERSION ) ;
        }

        public static function enqueue_for_reportsincsv_modules_tab() {
            $redirect_url    = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'fp_bg_process_to_export_report' => 'yes' ) , SRP_ADMIN_URL ) ) ;
            $localize_script = array(
                'ajaxurl'          => SRP_ADMIN_AJAX_URL ,
                'fp_wc_version'    => WC_VERSION ,
                'redirecturl'      => $redirect_url ,
                'fp_start_date'    => wp_create_nonce( 'fp-start-date' ) ,
                'fp_end_date'      => wp_create_nonce( 'fp-end-date' ) ,
                'fp_export_report' => wp_create_nonce( 'fp-export-report' ) ,
                'fp_user_type'     => wp_create_nonce( 'fp-user-type' ) ,
                'fp_selected_user' => wp_create_nonce( 'fp-selected-user' ) ,
                'fp_date_type'     => wp_create_nonce( 'fp-date-type' ) ,
                'fp_points_type'   => wp_create_nonce( 'fp-points-type' ) ,
                    ) ;
            wp_enqueue_script( 'fp_reports_in_csv_module' , SRP_PLUGIN_DIR_URL . "assets/js/tab/modules/fp-reportsincsv-module.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_localize_script( 'fp_reports_in_csv_module' , 'fp_reports_in_csv_module_params' , $localize_script ) ;
        }

        public static function enqueue_for_reset_modules_tab() {
            $localize_script = array(
                'ajaxurl'                      => SRP_ADMIN_AJAX_URL ,
                'fp_wc_version'                => WC_VERSION ,
                'rs_reset_tab'                 => wp_create_nonce( 'rs-reset-tab' ) ,
                'rs_reset_data_for_user'       => wp_create_nonce( 'reset-data-for-user' ) ,
                'rs_reset_previous_order_meta' => wp_create_nonce( 'reset-previous-order-meta' ) ,
                    ) ;
            wp_enqueue_script( 'fp_reset_module' , SRP_PLUGIN_DIR_URL . "assets/js/tab/modules/fp-reset-module.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_localize_script( 'fp_reset_module' , 'fp_reset_module_params' , $localize_script ) ;
        }

        public static function enqueue_for_reward_gateway_modules_tab() {
            $localize_script = array(
                'ajaxurl'       => SRP_ADMIN_AJAX_URL ,
                'fp_wc_version' => WC_VERSION ,
                    ) ;
            wp_enqueue_script( 'fp_reward_gateway_module' , SRP_PLUGIN_DIR_URL . "assets/js/tab/modules/fp-rewardgateway-module.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_localize_script( 'fp_reward_gateway_module' , 'fp_reward_gateway_module_params' , $localize_script ) ;
        }

        public static function enqueue_for_nominee_modules_tab() {
            $localize_script = array(
                'ajaxurl'       => SRP_ADMIN_AJAX_URL ,
                'fp_wc_version' => WC_VERSION ,
                    ) ;
            wp_enqueue_script( 'fp_nominee_module' , SRP_PLUGIN_DIR_URL . "assets/js/tab/modules/fp-nominee-module.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_localize_script( 'fp_nominee_module' , 'fp_nominee_module_params' , $localize_script ) ;
        }

        public static function enqueue_for_sendpoints_modules_tab() {
            $localize_script = array(
                'ajaxurl'       => SRP_ADMIN_AJAX_URL ,
                'fp_wc_version' => WC_VERSION ,
                    ) ;
            wp_enqueue_script( 'fp_sendpoints_module' , SRP_PLUGIN_DIR_URL . "assets/js/tab/modules/fp-sendpoints-module.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_localize_script( 'fp_sendpoints_module' , 'fp_sendpoints_module_params' , $localize_script ) ;
        }

        public static function enqueue_for_pointurl_modules_tab() {
            $localize_script = array(
                'ajaxurl'         => SRP_ADMIN_AJAX_URL ,
                'fp_wc_version'   => WC_VERSION ,
                'fp_generate_url' => wp_create_nonce( 'fp-generate-url' ) ,
                'fp_remove_url'   => wp_create_nonce( 'fp-remove-url' ) ,
                'date'            => date( 'Y-m-d' )
                    ) ;
            wp_enqueue_script( 'fp_pointurl_module' , SRP_PLUGIN_DIR_URL . "assets/js/tab/modules/fp-pointurl-module.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_localize_script( 'fp_pointurl_module' , 'fp_pointurl_module_params' , $localize_script ) ;
        }

        public static function enqueue_for_emailexpired_modules_tab() {
            $localize_script = array(
                'ajaxurl'              => SRP_ADMIN_AJAX_URL ,
                'fp_update_status'     => wp_create_nonce( 'fp-update-status' ) ,
                'fp_delete_template'   => wp_create_nonce( 'fp-delete-template' ) ,
                'fp_new_template'      => wp_create_nonce( 'fp-new-template' ) ,
                'fp_edit_template'     => wp_create_nonce( 'fp-edit-template' ) ,
                'template_id'          => isset( $_GET[ 'rs_edit_email_expired' ] ) ? $_GET[ 'rs_edit_email_expired' ] : 0 ,
                'admin_email'          => get_option( 'admin_email' ) ,
                'save_new_template'    => isset( $_GET[ 'rs_new_email_expired' ] ) ,
                'save_edited_template' => isset( $_GET[ 'rs_edit_email_expired' ] )
                    ) ;
            wp_enqueue_script( 'fp_emailexpired_module' , SRP_PLUGIN_DIR_URL . "assets/js/tab/modules/fp-emailexpired-module.js" , array( 'jquery' ) , SRP_VERSION ) ;
            wp_localize_script( 'fp_emailexpired_module' , 'fp_emailexpired_params' , $localize_script ) ;
        }

        public static function enqueue_for_discount_compatibility_modules_tab() {
            wp_enqueue_script( 'fp_discount_compatibility_module' , SRP_PLUGIN_DIR_URL . "assets/js/tab/modules/fp-discount-compatibility.js" , array( 'jquery' ) , SRP_VERSION ) ;
        }

    }

    RSAdminEnqueues::init() ;
}