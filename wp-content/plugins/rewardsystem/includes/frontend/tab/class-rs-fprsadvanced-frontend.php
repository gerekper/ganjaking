<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'RSFunctionForAdvanced' ) ) {

    class RSFunctionForAdvanced {

        public static function init() {
            if ( get_option( 'rs_load_script_styles' ) == 'wp_head' ) {
                add_action( 'wp_head' , array( __CLASS__ , 'load_script_in_header_or_footer' ) ) ;
            } else {
                add_action( 'wp_footer' , array( __CLASS__ , 'load_script_in_header_or_footer' ) ) ;
            }
            if ( get_option( 'rs_reward_content_menu_page' ) == 'yes' ) {
                $TitleURL = get_option( 'rs_my_reward_url_title' ) != '' ? get_option( 'rs_my_reward_url_title' ) : 'sumo-rewardpoints' ;
                add_filter( 'woocommerce_account_menu_items' , array( __CLASS__ , 'add_reward_menu_in_myaccount' ) ) ;
                add_action( 'woocommerce_account_' . $TitleURL . '_endpoint' , array( __CLASS__ , 'reward_contents_in_menu_page' ) ) ;
            }
        }

        public static function load_script_in_header_or_footer() {
            wp_enqueue_style( 'fp_style_for_rewardsystem' , SRP_PLUGIN_DIR_URL . "assets/css/style.css" , array() , SRP_VERSION ) ;
            wp_enqueue_style( 'wp_reward_footable_css' , SRP_PLUGIN_DIR_URL . "assets/css/footable.core.css" , array() , SRP_VERSION ) ;

            if ( get_option( 'rs_enable_reward_point_bootstrap' ) == '1' )
                wp_enqueue_style( 'wp_reward_bootstrap_css' , SRP_PLUGIN_DIR_URL . "assets/css/bootstrap.css" , array() , SRP_VERSION ) ;

            $assets_path = str_replace( array( 'http:' , 'https:' ) , '' , WC()->plugin_url() ) . '/assets/' ;
            wp_enqueue_style( 'select2' , $assets_path . 'css/select2.css' ) ;

            if ( get_option( 'rs_reward_point_dequeue_select2' ) == 'yes' )
                wp_dequeue_script( 'edgt_select2' ) ;

            if ( get_option( 'rs_reward_point_dequeue_recaptcha' ) == 'yes' )
                wp_dequeue_script( 'wp_google_recaptcha' ) ;
        }

        public static function add_reward_menu_in_myaccount( $items ) {
            $BanType = check_banning_type( get_current_user_id() ) ;
            if ( $BanType == 'earningonly' || $BanType == 'both' )
                return $items ;

            $TitleURL   = get_option( 'rs_my_reward_url_title' ) != '' ? get_option( 'rs_my_reward_url_title' ) : 'sumo-rewardpoints' ;
            $RewardMenu = array( $TitleURL => get_option( 'rs_my_reward_content_title' ) ) ;
            $items      = array_slice( $items , 0 , 2 ) + $RewardMenu + array_slice( $items , 2 , count( $items ) - 1 ) ;
            return $items ;
        }

        public static function reward_contents_in_menu_page() {
            $BanType = check_banning_type( get_current_user_id() ) ;
            if ( $BanType == 'earningonly' || $BanType == 'both' ) {
                wp_safe_redirect( get_permalink() ) ;
                return ;
            }

            if ( get_option( 'rs_my_reward_table_menu_page' ) == '1' )
                RSFunctionForMessage::reward_log() ;

            if ( get_option( 'rs_cashback_activated' ) == 'yes' && get_option( 'rs_my_cashback_table_menu_page' ) == '1' )
                RSCashBackFrontend::cash_back_log() ;

            if ( get_option( 'rs_nominee_activated' ) == 'yes' && get_option( 'rs_show_hide_nominee_field_menu_page' ) == '1' )
                RSFunctionForNominee::display_nominee_field_in_my_account() ;

            if ( get_option( 'rs_gift_voucher_activated' ) == 'yes' && get_option( 'rs_show_hide_redeem_voucher_menu_page' ) == '1' )
                RSGiftVoucherFrontend::giftvoucherfield() ;

            if ( get_option( 'rs_referral_activated' ) == 'yes' ) {
                if ( get_option( 'rs_show_hide_referal_table_menu_page' ) == '1' ) {
                    if ( check_if_referral_is_restricted_based_on_history() )
                        RSFunctionForReferralSystem::referral_list_table_in_menu() ;
                }
                if ( get_option( 'rs_show_hide_generate_referral_menu_page' ) == '1' ) {
                    if ( get_option( 'rs_show_hide_generate_referral_link_type' ) == '2' ) {
                        if ( check_if_referral_is_restricted_based_on_history() )
                            echo RSFunctionForReferralSystem::static_referral_link() ;
                    } else {
                        if ( check_if_referral_is_restricted_based_on_history() )
                            echo RSFunctionForReferralSystem::list_of_generated_link_and_field() ;
                    }
                }

                if ( '1' == get_option( 'rs_show_hide_refer_a_friend_menu_page' , '1' ) && '1' == get_option( 'rs_enable_message_for_friend_form' ) ) {
                    echo sprintf( '<h3>%s</h3>' , esc_html__( 'Refer a Friend Form' , SRP_LOCALE ) ) ;
                    echo RS_Rewardsystem_Shortcodes::display_refer_a_friend_form() ;
                }
            }
            if ( get_option( 'rs_email_activated' ) == 'yes' && get_option( 'rs_show_hide_your_subscribe_link_menu_page' ) == 1 )
                RSFunctionForEmailTemplate::field_for_subcribe(true) ;
        }

    }

    RSFunctionForAdvanced::init() ;
}