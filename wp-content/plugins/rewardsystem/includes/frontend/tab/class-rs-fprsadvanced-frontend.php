<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'RSFunctionForAdvanced' ) ) {

	class RSFunctionForAdvanced {

		public static function init() {
			if ( 'yes' == get_option( 'rs_reward_content_menu_page' ) ) {
				$TitleURL = '' != get_option( 'rs_my_reward_url_title' ) ? get_option( 'rs_my_reward_url_title' ) : 'sumo-rewardpoints' ;
				add_filter( 'woocommerce_account_menu_items' , array( __CLASS__ , 'add_reward_menu_in_myaccount' ) ) ;
				add_action( 'woocommerce_account_' . sanitize_title($TitleURL) . '_endpoint' , array( __CLASS__ , 'myreward_page_contents_sorting' ) ) ;
			}
		}

		public static function add_reward_menu_in_myaccount( $items ) {
			$BanType = check_banning_type( get_current_user_id() ) ;
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return $items ;
			}

			$TitleURL   = '' != get_option( 'rs_my_reward_url_title' ) ? get_option( 'rs_my_reward_url_title' ) : 'sumo-rewardpoints' ;
			$RewardMenu = array( $TitleURL => get_option( 'rs_my_reward_content_title' ) ) ;
			$items      = array_slice( $items , 0 , 2 ) + $RewardMenu + array_slice( $items , 2 , count( $items ) - 1 ) ;
			return $items ;
		}

		public static function myreward_page_contents_sorting() {

			$BanType = check_banning_type( get_current_user_id() ) ;
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				wp_safe_redirect( get_permalink() ) ;
				return ;
			}

			$columnvalues = array(
				'rs_myrewards_table',
				'rs_nominee_field',
				'rs_gift_voucher_field',
				'rs_referral_table',
				'rs_generate_referral_link',
				'rs_refer_a_friend_form',
				'rs_my_cashback_form',
				'rs_my_cashback_table',
				'rs_email_subscribe_link',
						) ;

			$sortedcolumn = srp_check_is_array( get_option( 'rs_sorted_menu_settings_list' ) ) ? get_option( 'rs_sorted_menu_settings_list' ) : $columnvalues ;
			if ( ! isset( $sortedcolumn[ 'rs_my_cashback_form'] ) ) {
				$sortedcolumn = array_slice( $sortedcolumn , 0 , 4 , true ) +
						array( 'rs_my_cashback_form' ) +
						array_slice( $sortedcolumn , 3 , count( $sortedcolumn ) - 4 , true ) ;
			}

			foreach ( $sortedcolumn as $column_key => $column_value ) {
				$function_name = str_replace('rs_', '', $column_key);
				if (!method_exists('RSFunctionForAdvanced', $function_name)) {
					continue;
				}
								
				self::$function_name();
			}
		}

		public static function my_cashback_form() {

			if ( 'yes' != get_option( 'rs_cashback_activated' ) || '1' != get_option( 'rs_my_cashback_form_menu_page' , 1 ) ) {
				return ;
			}
												
			if ('1'!= get_option('rs_enable_disable_encashing')) {
				return ;
			}
			
			RS_Rewardsystem_Shortcodes::shortcode_rsencashform() ;                        
		}

		public static function my_cashback_table() {

			if ( 'yes' != get_option( 'rs_cashback_activated' ) || '1' != get_option( 'rs_my_cashback_table_menu_page' ) ) {
				return '' ;
			}
						
			RSCashBackFrontend::cash_back_log() ;                       
		}

		public static function myrewards_table() {
					
			if ( '1' != get_option( 'rs_my_reward_table_menu_page' ) ) {
				return '' ;
			}
			
						RSFunctionForMessage::reward_log( true ) ;                        
		}

		public static function nominee_field() {

			if ( 'yes' != get_option( 'rs_nominee_activated' ) || '1' != get_option( 'rs_show_hide_nominee_field_menu_page' ) ) {
				return '' ;
			}
			
						RSFunctionForNominee::display_nominee_field_in_my_account() ;                        
		}

		public static function gift_voucher_field() {

			if ( 'yes' != get_option( 'rs_gift_voucher_activated' ) || '1' != get_option( 'rs_show_hide_redeem_voucher_menu_page' ) ) {
				return '' ;
			}
			
						RSGiftVoucherFrontend::giftvoucherfield() ;                        
		}

		public static function referral_table() {

			if ( 'yes' != get_option( 'rs_referral_activated' ) || '1' != get_option( 'rs_show_hide_referal_table_menu_page' ) ) {
				return '' ;
			}

			if ( ! check_if_referral_is_restricted_based_on_history() ) {
				return '' ;
			}
			
						RSFunctionForReferralSystem::referral_list_table_in_menu() ;                        
		}

		public static function refer_a_friend_form() {

			if ( 'yes' != get_option( 'rs_referral_activated' ) || '1' != get_option( 'rs_show_hide_refer_a_friend_menu_page' , '1' ) || '1' != get_option( 'rs_enable_message_for_friend_form' ) ) {
				return '' ;
			}		
					
						echo wp_kses_post(sprintf( '<h3 class="rs_refer_a_friend_title">%s</h3>' , esc_html__( 'Refer a Friend Form' , 'rewardsystem' ) )) ;
						RS_Rewardsystem_Shortcodes::display_refer_a_friend_form() ;                        
		}

		public static function email_subscribe_link() {

			if ( 'yes' != get_option( 'rs_email_activated' ) || 1 != get_option( 'rs_show_hide_your_subscribe_link_menu_page' ) ) {
				return '' ;
			}
						
						echo wp_kses_post(sprintf( '<h3 class="rs_email_subscribe_link_title">%s</h3>' , esc_html__( 'Email - Subscribe Link' , 'rewardsystem' ) ) );
						RSFunctionForEmailTemplate::field_for_subcribe( true ) ;                        					
		}

		public static function generate_referral_link() {

			if ( 'yes' != get_option( 'rs_referral_activated' ) || '1' != get_option( 'rs_show_hide_generate_referral_menu_page' ) ) {
				return '' ;
			}
						
			if ( '2' == get_option( 'rs_show_hide_generate_referral_link_type' ) ) {
				if ( check_if_referral_is_restricted_based_on_history() ) {
					RSFunctionForReferralSystem::static_referral_link() ;
				}
			} else {
				if ( check_if_referral_is_restricted_based_on_history() ) {
					RSFunctionForReferralSystem::list_of_generated_link_and_field();
				}
			}                        
		}
	}

	RSFunctionForAdvanced::init() ;
}
