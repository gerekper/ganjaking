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
                add_action( 'woocommerce_account_' . $TitleURL . '_endpoint' , array( __CLASS__ , 'myreward_page_contents_sorting' ) ) ;
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

        public static function myreward_page_contents_sorting() {

            $BanType = check_banning_type( get_current_user_id() ) ;
            if ( $BanType == 'earningonly' || $BanType == 'both' ) {
                wp_safe_redirect( get_permalink() ) ;
                return ;
            }

            $columnvalues = array(
                'rs_myrewards_table'        => self::reward_log() ,
                'rs_nominee_field'          => self::display_nominee_field_in_my_account() ,
                'rs_gift_voucher_field'     => self::giftvoucherfield() ,
                'rs_referral_table'         => self::referral_list_table_in_menu() ,
                'rs_generate_referral_link' => self::generate_referral_link() ,
                'rs_refer_a_friend_form'    => self::display_refer_a_friend_form() ,
                'rs_my_cashback_table'      => self::cash_back_log() ,
                'rs_email_subscribe_link'   => self::field_for_subcribe() ,
                    ) ;

            $sortedcolumn = srp_check_is_array( get_option( 'rs_sorted_menu_settings_list' ) ) ? get_option( 'rs_sorted_menu_settings_list' ) : $columnvalues ;
            foreach ( $sortedcolumn as $column_key => $column_value ) {
                echo isset( $columnvalues[ $column_key ] ) ? $columnvalues[ $column_key ] : '' ;
            }
        }

        public static function cash_back_log() {

            if ( 'yes' != get_option( 'rs_cashback_activated' ) || '1' != get_option( 'rs_my_cashback_table_menu_page' ) ) {
                return '' ;
            }

            ob_start() ;
            RSCashBackFrontend::cash_back_log() ;
            $content = ob_get_contents() ;
            ob_end_clean() ;

            return $content ;
        }

        public static function reward_log() {

            if ( '1' != get_option( 'rs_my_reward_table_menu_page' ) )
                return '' ;

            ob_start() ;
            RSFunctionForMessage::reward_log() ;
            $content = ob_get_contents() ;
            ob_end_clean() ;

            return $content ;
        }

        public static function display_nominee_field_in_my_account() {

            if ( 'yes' != get_option( 'rs_nominee_activated' ) || '1' != get_option( 'rs_show_hide_nominee_field_menu_page' ) )
                return '' ;

            ob_start() ;
            RSFunctionForNominee::display_nominee_field_in_my_account() ;
            $content = ob_get_contents() ;
            ob_end_clean() ;

            return $content ;
        }

        public static function giftvoucherfield() {

            if ( 'yes' != get_option( 'rs_gift_voucher_activated' ) || '1' != get_option( 'rs_show_hide_redeem_voucher_menu_page' ) )
                return '' ;

            ob_start() ;
            RSGiftVoucherFrontend::giftvoucherfield() ;
            $content = ob_get_contents() ;
            ob_end_clean() ;

            return $content ;
        }

        public static function referral_list_table_in_menu() {

            if ( 'yes' != get_option( 'rs_referral_activated' ) || '1' != get_option( 'rs_show_hide_referal_table_menu_page' ) ) {
               return '' ;
            }
            
            if ( ! check_if_referral_is_restricted_based_on_history() ){
               return '' ;
            }
            
            ob_start() ;
            RSFunctionForReferralSystem::referral_list_table_in_menu() ;
            $content = ob_get_contents() ;
            ob_end_clean() ;

            return $content ;
        }

        public static function display_refer_a_friend_form() {

            if ( 'yes' != get_option( 'rs_referral_activated' ) || '1' != get_option( 'rs_show_hide_refer_a_friend_menu_page' , '1' ) || '1' != get_option( 'rs_enable_message_for_friend_form' ) )
                return '' ;

            ob_start() ;
            echo sprintf( '<h3>%s</h3>' , esc_html__( 'Refer a Friend Form' , SRP_LOCALE ) ) ;
            echo RS_Rewardsystem_Shortcodes::display_refer_a_friend_form() ;
            $content = ob_get_contents() ;
            ob_end_clean() ;

            return $content ;
        }

        public static function field_for_subcribe() {

            if ( 'yes' != get_option( 'rs_email_activated' ) || 1 != get_option( 'rs_show_hide_your_subscribe_link_menu_page' ) )
                return '' ;

            ob_start() ;
            echo sprintf( '<h3>%s</h3>' , esc_html__( 'Email - Subscribe Link' , SRP_LOCALE ) ) ;
            RSFunctionForEmailTemplate::field_for_subcribe( true ) ;
            $content = ob_get_contents() ;
            ob_end_clean() ;

            return $content ;
        }

        public static function generate_referral_link() {
            
            if ( 'yes' != get_option( 'rs_referral_activated' ) || '1' != get_option( 'rs_show_hide_generate_referral_menu_page' ) ) {
               return '' ;
            }

            ob_start() ;
            if ( '2' == get_option( 'rs_show_hide_generate_referral_link_type' ) ) {
                if ( check_if_referral_is_restricted_based_on_history() )
                    echo RSFunctionForReferralSystem::static_referral_link() ;
            } else {
                if ( check_if_referral_is_restricted_based_on_history() )
                    echo RSFunctionForReferralSystem::list_of_generated_link_and_field() ;
            }

            $content = ob_get_contents() ;
            ob_end_clean() ;

            return $content ;
        }

    }

    RSFunctionForAdvanced::init() ;
}