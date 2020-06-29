<?php

/*
 * Advanced Tab
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSShortcode' ) ) {

    class RSShortcode {

        public static function init() {

            add_action( 'woocommerce_rs_settings_tabs_fprsshortcodes' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action( 'woocommerce_update_options_fprsshortcodes' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               
        }

        public static function reward_system_admin_fields() {
            return apply_filters( 'woocommerce_fprsshortcodes_settings' , array(
                array(
                    'type' => 'rs_modulecheck_start' ,
                ) ,
                array(
                    'name' => __( 'Shortcodes' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_shortcode' ,
                ) ,
                array(
                    'type' => 'title' ,
                    'desc' => '<h3>User Reward Points Info Shortcodes</h3><br><br>'
                    . '<b>[rs_my_reward_points]</b> - Use this Shortcode to display Reward Points of Current User<br><br>'
                    . '<b>[rs_user_total_earned_points]</b> - Use this Shortcode to display Total Points Earned by a User<br><br>'
                    . '<b>[rs_user_total_points_in_value]</b> - Use this Shortcode to display Total Points in Currency Value<br><br>'
                    . '<b>[rs_user_total_redeemed_points]</b> - Use this Shortcode to display Total Points Redeemed by a User<br><br>'
                    . '<b>[rs_user_total_expired_points]</b> - Use this Shortcode to display Total Points Expired for a User<br><br>'
                    . '<b>[rs_my_rewards_log]</b> - Use this Shortcode to display Log of Reward Points <br><br>'
                    . '<h3>Referral System Shortcodes</h3><br><br>'
                    . '<b>[rs_generate_referral referralbutton="show" referraltable="show"]</b> - Use this Shortcode to display Referral Link Generation and its Table.Shortcode Parameters are referralbutton and referraltable, make it as Show/Hide.<br><br>'
                    . '<b>[rs_generate_static_referral]</b> - Use this Shortcode to display Static URL Link<br><br>'
                    . '<b>[rs_view_referral_table]</b> - Use this Shortcode to display Referral Table<br><br>'
                    . '<b>[rs_refer_a_friend]</b> - Use this Shortcode to display Refer a Friend Form on any Page/Post<br><br>'
                    . '<h3>Cashback Shortcodes</h3><br><br>'
                    . '<b>[rs_my_cashback_log]</b> - Use this Shortcode to display My Cashback Table<br><br>'
                    . '<b>[rsencashform]</b> - Use this Shortcode to display Cashback Form<br><br>'
                    . '<h3>Member Level Shortcodes</h3><br><br>'
                    . '<b>[rs_my_current_earning_level_name]</b> - Use this Shortcode to display current Member Level of the User in Earning<br><br>'
                    . '<b>[rs_next_earning_level_points]</b> - Use this Shortcode to display points needed to reach the next Member Level in Earning<br><br>'
                    . '<b>[rs_my_current_redeem_level_name]</b> - Use this Shortcode to display current Member Level of the User in Redeeming<br><br>'
                    . '<b>[rs_rank_based_current_reward_points]</b> - Use this Shortcode to display Current Earned Points of all User<br><br>'
                    . '<b>[rs_rank_based_total_earned_points]</b> - Use this Shortcode to display Total Earned Points of all User<br><br>'
                    . '<b>[rs_total_earned_points_by_all_users]</b> - Use this shortcode to display total earned points by all your users<br><br>'
                    . '<b>[rs_total_available_points_of_all_users]</b> - Use this Shortcode to display total available points of all your users<br><br> '
                    . '<h3>Unsubscribe Email Shortcode</h3><br><br>'
                    . '<b>[rs_unsubscribe_email]</b> - Use this Shortcode to display Unsubscribe Email Checkbox<br><br>'
                    . '<h3>First Name and Last Name Shortcode</h3><br><br>'
                    . '<b>[rsfirstname]</b> - Use this Shortcode to display First name <br><br>'
                    . '<b>[rslastname]</b> - Use this Shortcode to display Last name <br><br>'
                    . '<h3>Gift Voucher Shortcode</h3><br><br>'
                    . '<b>[rs_redeem_vouchercode]</b> - Use this Shortcode to display Redeeming Voucher Field <br><br>'
                    . '<h3>Nominee Table Shortcode</h3><br><br>'
                    . '<b>[rs_nominee_table]</b> - Use this Shortcode to display Nominee Table<br><br>'
                    . '<h3>Send Points Shortcode</h3><br><br>'
                    . '<b>[rssendpoints]</b> - Use this Shortcode to display Send Points Form'
                    . '<h3>Action Points     Message Shortcode</h3><br><br>'
                    . '<b>[rs_list_enable_options]</b> - Use this Shortcode to display action messages<br><br>'
                    . '<b>[rs_list_of_orders_with_pending_points]</b> - Use this shortcode to display the orders which has the points to award<br><br>' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_shortcode' ) ,
                array(
                    'type' => 'rs_modulecheck_end' ,
                ) ,
                    )
                    ) ;
        }

        public static function reward_system_register_admin_settings() {
            woocommerce_admin_fields( RSShortcode::reward_system_admin_fields() ) ;
        }

        public static function reward_system_update_settings() {
            woocommerce_update_options( RSShortcode::reward_system_admin_fields() ) ;
        }

    }

    RSShortcode::init() ;
}