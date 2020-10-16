/*
 * Message Tab
 */
jQuery( function ( $ ) {
    var MessageTabScripts = {
        init : function () {
            this.show_or_hide_for_position_for_var() ;
            this.show_or_hide_for_reward_shortcode() ;
            this.show_or_hide_for_single_product_page() ;
            this.show_or_hide_for_single_product_page_msg_for_referral_to_simple() ;
            this.show_or_hide_for_single_product_page_msg_for_referral_to_variable() ;
            this.show_or_hide_for_simple_product_msg_in_shop_page() ;
            this.show_or_hide_for_simple_product_buying_points_msg_in_shop_page() ;
            this.show_or_hide_for_variable_product_buying_points_msg_in_shop_page() ;
            this.show_or_hide_for_simple_product_msg_in_custom_page() ;
            this.show_or_hide_for_simple_product_buying_points_msg_in_custom_page() ;
            this.enable_show_or_hide_for_variable_product_buying_points_msg_in_custom_page() ;
            this.show_or_hide_for_variable_product_buying_points_msg_in_custom_page() ;
            this.show_or_hide_for_simple_product_buying_points_msg_in_product_page() ;
            this.show_or_hide_for_variable_product_buying_points_msg_in_product_page() ;
            this.show_or_hide_for_booking_product_msg_in_product_page() ;
            this.show_or_hide_for_product_review_msg_in_product_page() ;
            this.show_or_hide_for_subscribe_product_msg_in_product_page() ;
            this.show_or_hide_for_related_simple_product_msg_in_product_page() ;
            this.show_or_hide_for_related_variable_product_msg_in_product_page() ;
            this.show_or_hide_for_earn_points_msg_for_simple_product_in_product_page() ;
            this.show_or_hide_for_earn_points_msg_for_variable_product_in_product_page() ;
            this.show_or_hide_for_earn_points_msg_for_each_variant_in_product_page() ;
            this.show_or_hide_for_purchase_message_in_cart_page() ;
            this.show_or_hide_for_buy_points_message_in_cart_page() ;
            this.page_size_for_my_reward_table() ;
            this.show_or_hide_for_first_purchase_message_in_cart_page() ;
            this.show_or_hide_for_total_points_message_in_cart_page() ;
            this.show_or_hide_for_my_reward_message_in_cart_page() ;
            this.show_or_hide_for_redeemed_points_message_in_cart_page() ;
            this.show_or_hide_for_account_creation_checkbox_in_checkout() ;
            this.show_or_hide_for_account_signup_msg() ;
            this.show_or_hide_for_daily_login_msg() ;
            this.show_or_hide_for_blog_creation_msg() ;
            this.show_or_hide_for_post_comment_msg() ;
            this.show_or_hide_for_page_comment_msg() ;
            this.show_or_hide_for_each_product_msg_in_checkout() ;
            this.show_or_hide_for_each_product_buying_point_msg_in_checkout() ;
            this.show_or_hide_for_total_point_msg_in_checkout() ;
            this.show_or_hide_for_my_reward_msg_in_checkout() ;
            this.show_or_hide_for_redeemed_points_msg_in_checkout() ;
            this.show_or_hide_for_payment_gateway_msg_in_checkout() ;
            this.show_or_hide_for_point_price_error_msg_for_redeem() ;
            this.show_or_hide_for_tax_notification() ;
            this.show_or_hide_for_sumo_payment_plan_msg() ;
            this.show_or_hide_for_sumo_payment_plan_buy_points_msg() ;
            this.show_or_hide_for_sumo_total_payment_plan_points_for_referral() ;
            this.show_or_hide_for_sumo_total_payment_plan_points_in_cart() ;
            this.show_or_hide_for_sumo_each_payment_plan_points_in_checkout() ;
            this.show_or_hide_for_sumo_each_payment_plan_buying_points_in_checkout() ;
            this.show_or_hide_for_sumo_total_payment_plan_points_for_referral_in_checkout() ;
            this.show_or_hide_for_sumo_total_payment_plan_points_in_checkout() ;
            this.show_or_hide_for_cart_based_point_message_in_cart_page() ;
            this.show_or_hide_for_cart_based_point_message_in_checkout_page() ;
            this.show_or_hide_for_msg_for_fixed_cart_based_points_in_product_page() ;
            this.show_or_hide_for_msg_for_percent_cart_based_points_in_product_page() ;
            this.show_or_hide_for_msg_for_fixed_cart_based_points_in_shop_page() ;
            this.show_or_hide_for_msg_for_percent_cart_based_points_in_shop_page() ;
            this.show_or_hide_for_msg_for_fixed_cart_based_points_in_custom_page() ;
            this.show_or_hide_for_msg_for_percent_cart_based_points_in_custom_page() ;
            this.show_or_hide_for_msg_for_page_size_my_reward_table_shortcode() ;
            $( document ).on( 'change' , '#rs_my_reward_table_shortcode' , this.show_or_hide_for_reward_shortcode ) ;
            $( document ).on( 'change' , '#rs_enable_display_earn_message_for_variation' , this.show_or_hide_for_position_for_var ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_single_product' , this.single_product_page ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_single_product_referral' , this.single_product_page_msg_for_referral_to_simple ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_variable_product_referral' , this.single_product_page_msg_for_referral_to_variable ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_simple_in_shop' , this.simple_product_msg_in_shop_page ) ;
            $( document ).on( 'change' , '#rs_show_hide_buy_points_message_for_simple_in_shop' , this.simple_product_buying_points_msg_in_shop_page ) ;
            $( document ).on( 'change' , '#rs_show_hide_buy_points_message_for_variable_in_shop' , this.variable_product_buying_points_msg_in_shop_page ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_simple_in_custom_shop' , this.simple_product_msg_in_custom_page ) ;
            $( document ).on( 'change' , '#rs_show_hide_buy_points_message_for_simple_in_custom' , this.simple_product_buying_points_msg_in_custom_page ) ;
            $( document ).on( 'change' , '#rs_enable_display_earn_message_for_variation_custom_shop' , this.enable_variable_product_buying_points_msg_in_custom_page ) ;
            $( document ).on( 'change' , '#rs_show_hide_buy_points_message_for_variable_in_custom_shop' , this.variable_product_buying_points_msg_in_custom_page ) ;
            $( document ).on( 'change' , '#rs_show_hide_buy_points_message_for_simple_in_product' , this.simple_product_buying_points_msg_in_product_page ) ;
            $( document ).on( 'change' , '#rs_show_hide_buy_points_message_for_variable_in_product' , this.variable_product_buying_points_msg_in_product_page ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_booking_product' , this.booking_product_msg_in_product_page ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_product_review' , this.product_review_msg_in_product_page ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_waitlist' , this.subscribe_product_msg_in_product_page ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_shop_archive_single_related_products' , this.related_simple_product_msg_in_product_page ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_shop_archive_variable_related_products' , this.related_variable_product_msg_in_product_page ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_shop_archive_single' , this.earn_points_msg_for_simple_product_in_product_page ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_variable_in_single_product_page' , this.earn_points_msg_for_variable_product_in_product_page ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_variable_product' , this.earn_points_msg_for_each_variant_in_product_page ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_each_products' , this.purchase_message_in_cart_page ) ;
            $( document ).on( 'change' , '#rs_show_hide_buy_point_message_for_each_products' , this.buy_points_message_in_cart_page ) ;
            $( document ).on( 'change' , '#rs_show_hide_page_size_my_rewards' , this.my_reward_table_page_size ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_first_purchase_points' , this.first_purchase_message_in_cart_page ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_total_points' , this.total_points_message_in_cart_page ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_my_rewards' , this.my_reward_message_in_cart_page ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_redeem_points' , this.redeemed_points_message_in_cart_page ) ;
            $( document ).on( 'change' , '#rs_enable_msg_for_cart_total_based_points' , this.cart_based_point_message_in_cart_page ) ;
            $( document ).on( 'change' , '#rs_enable_msg_for_cart_total_based_points_in_checkout' , this.cart_based_point_message_in_checkout_page ) ;
            $( document ).on( 'change' , '#rs_enable_acc_creation_for_guest_checkout_page' , this.account_creation_checkbox_in_checkout ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_sign_up' , this.account_signup_msg ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_daily_login' , this.daily_login_msg ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_blog_create' , this.blog_creation_msg ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_post_comment' , this.post_comment_msg ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_page_comment' , this.page_comment_msg ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_each_products_checkout_page' , this.each_product_msg_in_checkout ) ;
            $( document ).on( 'change' , '#rs_show_hide_buy_point_message_for_each_products_checkout_page' , this.each_product_buying_point_msg_in_checkout ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_total_points_checkout_page' , this.total_point_msg_in_checkout ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_my_rewards_checkout_page' , this.my_reward_msg_in_checkout ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_redeem_points_checkout_page' , this.redeemed_points_msg_in_checkout ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_payment_gateway_reward_points' , this.payment_gateway_msg_in_checkout ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_errmsg_for_point_price_coupon' , this.point_price_error_msg_for_redeem ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_notice_for_redeeming' , this.tax_notification ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_each_payment_plan_products' , this.sumo_payment_plan_msg ) ;
            $( document ).on( 'change' , '#rs_show_hide_buy_point_message_for_each_payment_plan_products' , this.sumo_payment_plan_buy_points_msg ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_total_payment_plan_points_referral' , this.sumo_total_payment_plan_points_for_referral ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_total_payment_plan_points' , this.sumo_total_payment_plan_points_in_cart ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_each_payment_plan_products_checkout_page' , this.sumo_each_payment_plan_points_in_checkout ) ;
            $( document ).on( 'change' , '#rs_show_hide_buy_point_message_for_each_payment_plan_products_checkout_page' , this.sumo_each_payment_plan_buying_points_in_checkout ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_total_payment_plan_points_referrel_checkout' , this.sumo_total_payment_plan_points_for_referral_in_checkout ) ;
            $( document ).on( 'change' , '#rs_show_hide_message_for_total_payment_plan_points_checkout_page' , this.sumo_total_payment_plan_points_in_checkout ) ;
            $( document ).on( 'change' , '#rs_enable_msg_for_fixed_cart_total_based_product_purchase' , this.msg_for_fixed_cart_based_points_in_product_page ) ;
            $( document ).on( 'change' , '#rs_enable_msg_for_percent_cart_total_based_product_purchase' , this.msg_for_percent_cart_based_points_in_product_page ) ;
            $( document ).on( 'change' , '#rs_enable_msg_for_fixed_cart_total_based_product_purchase_in_shop' , this.msg_for_fixed_cart_based_points_in_shop_page ) ;
            $( document ).on( 'change' , '#rs_enable_msg_for_percent_cart_total_based_product_purchase_in_shop' , this.msg_for_percent_cart_based_points_in_shop_page ) ;
            $( document ).on( 'change' , '#rs_enable_msg_for_fixed_cart_total_based_product_purchase_in_custom' , this.msg_for_fixed_cart_based_points_in_custom_page ) ;
            $( document ).on( 'change' , '#rs_enable_msg_for_percent_cart_total_based_product_purchase_in_custom' , this.msg_for_percent_cart_based_points_in_custom_page ) ;
            $( document ).on( 'change' , '#rs_show_hide_page_size_my_rewards_shortcode' , this.msg_for_page_size_my_reward_table_shortcode ) ;
        } ,
        msg_for_fixed_cart_based_points_in_product_page : function () {
            MessageTabScripts.show_or_hide_for_msg_for_fixed_cart_based_points_in_product_page() ;
        } ,
        show_or_hide_for_msg_for_fixed_cart_based_points_in_product_page : function () {
            if ( jQuery( '#rs_enable_msg_for_fixed_cart_total_based_product_purchase' ).val() == '1' ) {
                jQuery( '#rs_msg_for_fixed_cart_total_based_product_purchase' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_msg_for_fixed_cart_total_based_product_purchase' ).parent().parent().hide() ;
            }
        } ,
        msg_for_percent_cart_based_points_in_product_page : function () {
            MessageTabScripts.show_or_hide_for_msg_for_percent_cart_based_points_in_product_page() ;
        } ,
        show_or_hide_for_msg_for_percent_cart_based_points_in_product_page : function () {
            if ( jQuery( '#rs_enable_msg_for_percent_cart_total_based_product_purchase' ).val() == '1' ) {
                jQuery( '#rs_msg_for_percent_cart_total_based_product_purchase' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_msg_for_percent_cart_total_based_product_purchase' ).parent().parent().hide() ;
            }
        } ,
        msg_for_fixed_cart_based_points_in_shop_page : function () {
            MessageTabScripts.show_or_hide_for_msg_for_fixed_cart_based_points_in_shop_page() ;
        } ,
        show_or_hide_for_msg_for_fixed_cart_based_points_in_shop_page : function () {
            if ( jQuery( '#rs_enable_msg_for_fixed_cart_total_based_product_purchase_in_shop' ).val() == '1' ) {
                jQuery( '#rs_msg_for_fixed_cart_total_based_product_purchase_in_shop' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_msg_for_fixed_cart_total_based_product_purchase_in_shop' ).parent().parent().hide() ;
            }
        } ,
        msg_for_percent_cart_based_points_in_shop_page : function () {
            MessageTabScripts.show_or_hide_for_msg_for_percent_cart_based_points_in_shop_page() ;
        } ,
        show_or_hide_for_msg_for_percent_cart_based_points_in_shop_page : function () {
            if ( jQuery( '#rs_enable_msg_for_percent_cart_total_based_product_purchase_in_shop' ).val() == '1' ) {
                jQuery( '#rs_msg_for_percent_cart_total_based_product_purchase_in_shop' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_msg_for_percent_cart_total_based_product_purchase_in_shop' ).parent().parent().hide() ;
            }
        } ,
        msg_for_fixed_cart_based_points_in_custom_page : function () {
            MessageTabScripts.show_or_hide_for_msg_for_fixed_cart_based_points_in_custom_page() ;
        } ,
        show_or_hide_for_msg_for_fixed_cart_based_points_in_custom_page : function () {
            if ( jQuery( '#rs_enable_msg_for_fixed_cart_total_based_product_purchase_in_custom' ).val() == '1' ) {
                jQuery( '#rs_msg_for_fixed_cart_total_based_product_purchase_in_custom' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_msg_for_fixed_cart_total_based_product_purchase_in_custom' ).parent().parent().hide() ;
            }
        } ,
        msg_for_percent_cart_based_points_in_custom_page : function () {
            MessageTabScripts.show_or_hide_for_msg_for_percent_cart_based_points_in_custom_page() ;
        } ,
        show_or_hide_for_msg_for_percent_cart_based_points_in_custom_page : function () {
            if ( jQuery( '#rs_enable_msg_for_percent_cart_total_based_product_purchase_in_custom' ).val() == '1' ) {
                jQuery( '#rs_msg_for_percent_cart_total_based_product_purchase_in_custom' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_msg_for_percent_cart_total_based_product_purchase_in_custom' ).parent().parent().hide() ;
            }
        } ,
        msg_for_page_size_my_reward_table_shortcode : function () {
            MessageTabScripts.show_or_hide_for_msg_for_page_size_my_reward_table_shortcode() ;
        } ,
        show_or_hide_for_msg_for_page_size_my_reward_table_shortcode : function () {
            if ( jQuery( '#rs_show_hide_page_size_my_rewards_shortcode' ).val() == '1' ) {
                jQuery( '#rs_number_of_page_size_in_myrewards_shortcode' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_number_of_page_size_in_myrewards_shortcode' ).parent().parent().show() ;
            }
        } ,
        single_product_page : function () {
            MessageTabScripts.show_or_hide_for_single_product_page() ;
        } ,
        show_or_hide_for_single_product_page : function () {
            if ( jQuery( '#rs_show_hide_message_for_single_product' ).val() == '1' ) {
                jQuery( '#rs_message_for_single_product_point_rule' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_for_single_product_point_rule' ).parent().parent().hide() ;
            }
        } ,
        single_product_page_msg_for_referral_to_simple : function () {
            MessageTabScripts.show_or_hide_for_single_product_page_msg_for_referral_to_simple() ;
        } ,
        show_or_hide_for_single_product_page_msg_for_referral_to_simple : function () {
            if ( jQuery( '#rs_show_hide_message_for_single_product_referral' ).val() == '1' ) {
                jQuery( '#rs_message_for_single_product_point_rule_referral' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_for_single_product_point_rule_referral' ).parent().parent().hide() ;
            }
        } ,
        single_product_page_msg_for_referral_to_variable : function () {
            MessageTabScripts.show_or_hide_for_single_product_page_msg_for_referral_to_variable() ;
        } ,
        show_or_hide_for_single_product_page_msg_for_referral_to_variable : function () {
            if ( jQuery( '#rs_show_hide_message_for_variable_product_referral' ).val() == '1' ) {
                jQuery( '#rs_message_for_variation_products_referral' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_for_variation_products_referral' ).parent().parent().hide() ;
            }
        } ,
        simple_product_msg_in_shop_page : function () {
            MessageTabScripts.show_or_hide_for_simple_product_msg_in_shop_page() ;
        } ,
        show_or_hide_for_simple_product_msg_in_shop_page : function () {
            if ( jQuery( '#rs_show_hide_message_for_simple_in_shop' ).val() == '1' ) {
                jQuery( '#rs_message_in_shop_page_for_simple' ).parent().parent().show() ;
                jQuery( '#rs_message_position_for_simple_products_in_shop_page' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_in_shop_page_for_simple' ).parent().parent().hide() ;
                jQuery( '#rs_message_position_for_simple_products_in_shop_page' ).parent().parent().hide() ;
            }
        } ,
        simple_product_buying_points_msg_in_shop_page : function () {
            MessageTabScripts.show_or_hide_for_simple_product_buying_points_msg_in_shop_page() ;
        } ,
        show_or_hide_for_simple_product_buying_points_msg_in_shop_page : function () {
            if ( jQuery( '#rs_show_hide_buy_points_message_for_simple_in_shop' ).val() == '1' ) {
                jQuery( '#rs_buy_point_message_in_shop_page_for_simple' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_buy_point_message_in_shop_page_for_simple' ).parent().parent().hide() ;
            }
        } ,
        variable_product_buying_points_msg_in_shop_page : function () {
            MessageTabScripts.show_or_hide_for_variable_product_buying_points_msg_in_shop_page() ;
        } ,
        show_or_hide_for_variable_product_buying_points_msg_in_shop_page : function () {
            if ( jQuery( '#rs_show_hide_buy_points_message_for_variable_in_shop' ).val() == '1' ) {
                jQuery( '#rs_buy_point_message_in_shop_page_for_variable' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_buy_point_message_in_shop_page_for_variable' ).parent().parent().hide() ;
            }
        } ,
        simple_product_msg_in_custom_page : function () {
            MessageTabScripts.show_or_hide_for_simple_product_msg_in_custom_page() ;
        } ,
        show_or_hide_for_simple_product_msg_in_custom_page : function () {
            if ( jQuery( '#rs_show_hide_message_for_simple_in_custom_shop' ).val() == '1' ) {
                jQuery( '#rs_message_in_custom_shop_page_for_simple' ).parent().parent().show() ;
                jQuery( '#rs_message_position_for_simple_products_in_custom_shop_page' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_in_custom_shop_page_for_simple' ).parent().parent().hide() ;
                jQuery( '#rs_message_position_for_simple_products_in_custom_shop_page' ).parent().parent().hide() ;
            }
        } ,
        simple_product_buying_points_msg_in_custom_page : function () {
            MessageTabScripts.show_or_hide_for_simple_product_buying_points_msg_in_custom_page() ;
        } ,
        show_or_hide_for_simple_product_buying_points_msg_in_custom_page : function () {
            if ( jQuery( '#rs_show_hide_buy_points_message_for_simple_in_custom' ).val() == '1' ) {
                jQuery( '#rs_buy_point_message_in_custom_shop_page_for_simple' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_buy_point_message_in_custom_shop_page_for_simple' ).parent().parent().hide() ;
            }
        } ,
        enable_variable_product_buying_points_msg_in_custom_page : function () {
            MessageTabScripts.enable_show_or_hide_for_variable_product_buying_points_msg_in_custom_page() ;
        } ,
        enable_show_or_hide_for_variable_product_buying_points_msg_in_custom_page : function () {
            if ( jQuery( '#rs_enable_display_earn_message_for_variation_custom_shop' ).val() == '1' ) {
                jQuery( '#rs_message_for_custom_shop_variation' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_for_custom_shop_variation' ).parent().parent().hide() ;
            }
        } ,
        variable_product_buying_points_msg_in_custom_page : function () {
            MessageTabScripts.show_or_hide_for_variable_product_buying_points_msg_in_custom_page() ;
        } ,
        show_or_hide_for_variable_product_buying_points_msg_in_custom_page : function () {
            if ( jQuery( '#rs_show_hide_buy_points_message_for_variable_in_custom_shop' ).val() == '1' ) {
                jQuery( '#rs_buy_point_message_in_custom_shop_page_for_variable' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_buy_point_message_in_custom_shop_page_for_variable' ).parent().parent().hide() ;
            }
        } ,
        simple_product_buying_points_msg_in_product_page : function () {
            MessageTabScripts.show_or_hide_for_simple_product_buying_points_msg_in_product_page() ;
        } ,
        show_or_hide_for_simple_product_buying_points_msg_in_product_page : function () {
            if ( jQuery( '#rs_show_hide_buy_points_message_for_simple_in_product' ).val() == '1' ) {
                jQuery( '#rs_buy_point_message_in_product_page_for_simple' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_buy_point_message_in_product_page_for_simple' ).parent().parent().hide() ;
            }
        } ,
        variable_product_buying_points_msg_in_product_page : function () {
            MessageTabScripts.show_or_hide_for_variable_product_buying_points_msg_in_product_page() ;
        } ,
        show_or_hide_for_variable_product_buying_points_msg_in_product_page : function () {
            if ( jQuery( '#rs_show_hide_buy_points_message_for_variable_in_product' ).val() == '1' ) {
                jQuery( '#rs_buy_point_message_in_product_page_for_variable' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_buy_point_message_in_product_page_for_variable' ).parent().parent().hide() ;
            }
        } ,
        booking_product_msg_in_product_page : function () {
            MessageTabScripts.show_or_hide_for_booking_product_msg_in_product_page() ;
        } ,
        show_or_hide_for_booking_product_msg_in_product_page : function () {
            if ( jQuery( '#rs_show_hide_message_for_booking_product' ).val() == '1' ) {
                jQuery( '#rs_message_for_booking_product' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_for_booking_product' ).parent().parent().hide() ;
            }
        } ,
        product_review_msg_in_product_page : function () {
            MessageTabScripts.show_or_hide_for_product_review_msg_in_product_page() ;
        } ,
        show_or_hide_for_product_review_msg_in_product_page : function () {
            if ( jQuery( '#rs_show_hide_message_for_product_review' ).val() == '1' ) {
                jQuery( '#rs_message_for_product_review' ).parent().parent().show() ;
                jQuery( '#rs_show_hide_message_for_product_review_for_guest_user' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_for_product_review' ).parent().parent().hide() ;
                jQuery( '#rs_show_hide_message_for_product_review_for_guest_user' ).parent().parent().hide() ;
            }
        } ,
        subscribe_product_msg_in_product_page : function () {
            MessageTabScripts.show_or_hide_for_subscribe_product_msg_in_product_page() ;
        } ,
        show_or_hide_for_subscribe_product_msg_in_product_page : function () {
            if ( jQuery( '#rs_show_hide_message_for_waitlist' ).val() == '1' ) {
                jQuery( '#rs_message_for_subscribing_product' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_for_subscribing_product' ).parent().parent().hide() ;
            }
        } ,
        related_simple_product_msg_in_product_page : function () {
            MessageTabScripts.show_or_hide_for_related_simple_product_msg_in_product_page() ;
        } ,
        show_or_hide_for_related_simple_product_msg_in_product_page : function () {
            if ( jQuery( '#rs_show_hide_message_for_shop_archive_single_related_products' ).val() == '1' ) {
                jQuery( '#rs_message_in_single_product_page_related_products' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_in_single_product_page_related_products' ).parent().parent().hide() ;
            }
        } ,
        related_variable_product_msg_in_product_page : function () {
            MessageTabScripts.show_or_hide_for_related_variable_product_msg_in_product_page() ;
        } ,
        show_or_hide_for_related_variable_product_msg_in_product_page : function () {
            if ( jQuery( '#rs_show_hide_message_for_shop_archive_variable_related_products' ).val() == '1' ) {
                jQuery( '#rs_message_in_variable_related_products' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_in_variable_related_products' ).parent().parent().hide() ;
            }
        } ,
        earn_points_msg_for_simple_product_in_product_page : function () {
            MessageTabScripts.show_or_hide_for_earn_points_msg_for_simple_product_in_product_page() ;
        } ,
        show_or_hide_for_earn_points_msg_for_simple_product_in_product_page : function () {
            if ( jQuery( '#rs_show_hide_message_for_shop_archive_single' ).val() == '1' ) {
                jQuery( '#rs_message_in_single_product_page' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_in_single_product_page' ).parent().parent().hide() ;
            }
        } ,
        earn_points_msg_for_variable_product_in_product_page : function () {
            MessageTabScripts.show_or_hide_for_earn_points_msg_for_variable_product_in_product_page() ;
        } ,
        show_or_hide_for_earn_points_msg_for_variable_product_in_product_page : function () {
            if ( jQuery( '#rs_show_hide_message_for_variable_in_single_product_page' ).val() == '1' ) {
                jQuery( '#rs_message_for_single_product_variation' ).parent().parent().show() ;
                jQuery( '#rs_message_position_in_single_product_page_for_simple_products' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_for_single_product_variation' ).parent().parent().hide() ;
                jQuery( '#rs_message_position_in_single_product_page_for_simple_products' ).parent().parent().hide() ;
            }
        } ,
        earn_points_msg_for_each_variant_in_product_page : function () {
            MessageTabScripts.show_or_hide_for_earn_points_msg_for_each_variant_in_product_page() ;
        } ,
        show_or_hide_for_earn_points_msg_for_each_variant_in_product_page : function () {
            if ( jQuery( '#rs_show_hide_message_for_variable_product' ).val() == '1' ) {
                jQuery( '#rs_message_for_variation_products' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_for_variation_products' ).parent().parent().hide() ;
            }
        } ,
        purchase_message_in_cart_page : function () {
            MessageTabScripts.show_or_hide_for_purchase_message_in_cart_page() ;
        } ,
        show_or_hide_for_purchase_message_in_cart_page : function () {
            if ( jQuery( '#rs_show_hide_message_for_each_products' ).val() == '1' ) {
                jQuery( '#rs_message_product_in_cart' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_product_in_cart' ).parent().parent().hide() ;
            }
        } ,
        buy_points_message_in_cart_page : function () {
            MessageTabScripts.show_or_hide_for_buy_points_message_in_cart_page() ;
        } ,
        show_or_hide_for_buy_points_message_in_cart_page : function () {
            if ( jQuery( '#rs_show_hide_buy_point_message_for_each_products' ).val() == '1' ) {
                jQuery( '#rs_buy_point_message_product_in_cart' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_buy_point_message_product_in_cart' ).parent().parent().hide() ;
            }
        } ,
        first_purchase_message_in_cart_page : function () {
            MessageTabScripts.show_or_hide_for_first_purchase_message_in_cart_page() ;
        } ,
        show_or_hide_for_first_purchase_message_in_cart_page : function () {
            if ( jQuery( '#rs_show_hide_message_for_first_purchase_points' ).val() == '1' ) {
                jQuery( '#rs_message_for_first_purchase' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_for_first_purchase' ).parent().parent().hide() ;
            }
        } ,
        my_reward_table_page_size : function () {
            MessageTabScripts.page_size_for_my_reward_table() ;
        } ,
        page_size_for_my_reward_table : function () {
            if ( jQuery( '#rs_show_hide_page_size_my_rewards' ).val() == '2' ) {
                jQuery( '#rs_number_of_page_size_in_myaccount' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_number_of_page_size_in_myaccount' ).parent().parent().hide() ;
            }
        } ,
        total_points_message_in_cart_page : function () {
            MessageTabScripts.show_or_hide_for_total_points_message_in_cart_page() ;
        } ,
        show_or_hide_for_total_points_message_in_cart_page : function () {
            if ( jQuery( '#rs_show_hide_message_for_total_points' ).val() == '1' ) {
                jQuery( '#rs_message_total_price_in_cart' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_total_price_in_cart' ).parent().parent().hide() ;
            }
        } ,
        my_reward_message_in_cart_page : function () {
            MessageTabScripts.show_or_hide_for_my_reward_message_in_cart_page() ;
        } ,
        show_or_hide_for_my_reward_message_in_cart_page : function () {
            if ( jQuery( '#rs_show_hide_message_for_my_rewards' ).val() == '1' ) {
                jQuery( '#rs_message_user_points_in_cart' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_user_points_in_cart' ).parent().parent().hide() ;
            }
        } ,
        redeemed_points_message_in_cart_page : function () {
            MessageTabScripts.show_or_hide_for_redeemed_points_message_in_cart_page() ;
        } ,
        show_or_hide_for_redeemed_points_message_in_cart_page : function () {
            if ( jQuery( '#rs_show_hide_message_for_redeem_points' ).val() == '1' ) {
                jQuery( '#rs_message_user_points_redeemed_in_cart' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_user_points_redeemed_in_cart' ).parent().parent().hide() ;
            }
        } ,
        cart_based_point_message_in_cart_page : function () {
            MessageTabScripts.show_or_hide_for_cart_based_point_message_in_cart_page() ;
        } ,
        show_or_hide_for_cart_based_point_message_in_cart_page : function () {
            if ( jQuery( '#rs_enable_msg_for_cart_total_based_points' ).val() == '1' ) {
                jQuery( '#rs_msg_for_cart_total_based_points' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_msg_for_cart_total_based_points' ).parent().parent().hide() ;
            }
        } ,
        cart_based_point_message_in_checkout_page : function () {
            MessageTabScripts.show_or_hide_for_cart_based_point_message_in_checkout_page() ;
        } ,
        show_or_hide_for_cart_based_point_message_in_checkout_page : function () {
            if ( jQuery( '#rs_enable_msg_for_cart_total_based_points_in_checkout' ).val() == '1' ) {
                jQuery( '#rs_msg_for_cart_total_based_points_in_checkout' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_msg_for_cart_total_based_points_in_checkout' ).parent().parent().hide() ;
            }
        } ,
        account_creation_checkbox_in_checkout : function () {
            MessageTabScripts.show_or_hide_for_account_creation_checkbox_in_checkout() ;
        } ,
        show_or_hide_for_account_creation_checkbox_in_checkout : function () {
            if ( jQuery( '#rs_enable_acc_creation_for_guest_checkout_page' ).is( ':checked' ) == true ) {
                jQuery( '#rs_message_for_guest_in_checkout' ).parent().parent().hide() ;
                jQuery( '#rs_show_hide_message_for_guest_checkout_page' ).parent().parent().hide() ;
                jQuery( '#rs_show_hide_message_for_guest' ).parent().parent().hide() ;
                jQuery( '#rs_message_for_guest_in_cart' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_show_hide_message_for_guest_checkout_page' ).parent().parent().show() ;
                if ( jQuery( '#rs_show_hide_message_for_guest_checkout_page' ).val() == '1' ) {
                    jQuery( '#rs_message_for_guest_in_checkout' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_message_for_guest_in_checkout' ).parent().parent().hide() ;
                }

                jQuery( '#rs_show_hide_message_for_guest_checkout_page' ).change( function () {
                    if ( jQuery( '#rs_show_hide_message_for_guest_checkout_page' ).val() == '1' ) {
                        jQuery( '#rs_message_for_guest_in_checkout' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_message_for_guest_in_checkout' ).parent().parent().hide() ;
                    }
                } ) ;

                //Show or Hide Message for Guest in Cart Page
                jQuery( '#rs_show_hide_message_for_guest' ).parent().parent().show() ;
                if ( jQuery( '#rs_show_hide_message_for_guest' ).val() == '1' ) {
                    jQuery( '#rs_message_for_guest_in_cart' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_message_for_guest_in_cart' ).parent().parent().hide() ;
                }

                jQuery( '#rs_show_hide_message_for_guest' ).change( function () {
                    if ( jQuery( '#rs_show_hide_message_for_guest' ).val() == '1' ) {
                        jQuery( '#rs_message_for_guest_in_cart' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_message_for_guest_in_cart' ).parent().parent().hide() ;
                    }
                } ) ;
            }
        } ,
        account_signup_msg : function () {
            MessageTabScripts.show_or_hide_for_account_signup_msg() ;
        } ,
        show_or_hide_for_account_signup_msg : function () {
            if ( jQuery( '#rs_show_hide_message_for_sign_up' ).val() == '1' ) {
                jQuery( '#rs_message_user_points_for_sign_up' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_user_points_for_sign_up' ).parent().parent().hide() ;
            }
        } ,
        daily_login_msg : function () {
            MessageTabScripts.show_or_hide_for_daily_login_msg() ;
        } ,
        show_or_hide_for_daily_login_msg : function () {
            if ( jQuery( '#rs_show_hide_message_for_daily_login' ).val() == '1' ) {
                jQuery( '#rs_message_user_points_for_daily_login' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_user_points_for_daily_login' ).parent().parent().hide() ;
            }
        } ,
        blog_creation_msg : function () {
            MessageTabScripts.show_or_hide_for_blog_creation_msg() ;
        } ,
        show_or_hide_for_blog_creation_msg : function () {
            if ( jQuery( '#rs_show_hide_message_for_blog_create' ).val() == '1' ) {
                jQuery( '#rs_message_user_points_for_blog_creation' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_user_points_for_blog_creation' ).parent().parent().hide() ;
            }
        } ,
        post_comment_msg : function () {
            MessageTabScripts.show_or_hide_for_post_comment_msg() ;
        } ,
        show_or_hide_for_post_comment_msg : function () {
            if ( jQuery( '#rs_show_hide_message_for_post_comment' ).val() == '1' ) {
                jQuery( '#rs_message_user_points_for_blog_comment' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_user_points_for_blog_comment' ).parent().parent().hide() ;
            }
        } ,
        page_comment_msg : function () {
            MessageTabScripts.show_or_hide_for_page_comment_msg() ;
        } ,
        show_or_hide_for_page_comment_msg : function () {
            if ( jQuery( '#rs_show_hide_message_for_page_comment' ).val() == '1' ) {
                jQuery( '#rs_message_user_points_for_page_comment' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_user_points_for_page_comment' ).parent().parent().hide() ;
            }
        } ,
        each_product_msg_in_checkout : function () {
            MessageTabScripts.show_or_hide_for_each_product_msg_in_checkout() ;
        } ,
        show_or_hide_for_each_product_msg_in_checkout : function () {
            if ( jQuery( '#rs_show_hide_message_for_each_products_checkout_page' ).val() == '1' ) {
                jQuery( '#rs_message_product_in_checkout' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_product_in_checkout' ).parent().parent().hide() ;
            }
        } ,
        each_product_buying_point_msg_in_checkout : function () {
            MessageTabScripts.show_or_hide_for_each_product_buying_point_msg_in_checkout() ;
        } ,
        show_or_hide_for_each_product_buying_point_msg_in_checkout : function () {
            if ( jQuery( '#rs_show_hide_buy_point_message_for_each_products_checkout_page' ).val() == '1' ) {
                jQuery( '#rs_buy_point_message_product_in_checkout' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_buy_point_message_product_in_checkout' ).parent().parent().hide() ;
            }
        } ,
        total_point_msg_in_checkout : function () {
            MessageTabScripts.show_or_hide_for_total_point_msg_in_checkout() ;
        } ,
        show_or_hide_for_total_point_msg_in_checkout : function () {
            if ( jQuery( '#rs_show_hide_message_for_total_points_checkout_page' ).val() == '1' ) {
                jQuery( '#rs_message_total_price_in_checkout' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_total_price_in_checkout' ).parent().parent().hide() ;
            }
        } ,
        my_reward_msg_in_checkout : function () {
            MessageTabScripts.show_or_hide_for_my_reward_msg_in_checkout() ;
        } ,
        show_or_hide_for_my_reward_msg_in_checkout : function () {
            if ( jQuery( '#rs_show_hide_message_for_my_rewards_checkout_page' ).val() == '1' ) {
                jQuery( '#rs_message_user_points_in_checkout' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_user_points_in_checkout' ).parent().parent().hide() ;
            }
        } ,
        redeemed_points_msg_in_checkout : function () {
            MessageTabScripts.show_or_hide_for_redeemed_points_msg_in_checkout() ;
        } ,
        show_or_hide_for_redeemed_points_msg_in_checkout : function () {
            if ( jQuery( '#rs_show_hide_message_for_redeem_points_checkout_page' ).val() == '1' ) {
                jQuery( '#rs_message_user_points_redeemed_in_checkout' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_user_points_redeemed_in_checkout' ).parent().parent().hide() ;
            }
        } ,
        payment_gateway_msg_in_checkout : function () {
            MessageTabScripts.show_or_hide_for_payment_gateway_msg_in_checkout() ;
        } ,
        show_or_hide_for_payment_gateway_msg_in_checkout : function () {
            if ( jQuery( '#rs_show_hide_message_payment_gateway_reward_points' ).val() == '1' ) {
                jQuery( '#rs_message_payment_gateway_reward_points' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_payment_gateway_reward_points' ).parent().parent().hide() ;
            }
        } ,
        point_price_error_msg_for_redeem : function () {
            MessageTabScripts.show_or_hide_for_point_price_error_msg_for_redeem() ;
        } ,
        show_or_hide_for_point_price_error_msg_for_redeem : function () {
            if ( jQuery( '#rs_show_hide_message_errmsg_for_point_price_coupon' ).val() == '1' ) {
                jQuery( '#rs_errmsg_for_redeem_in_point_price_prt' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_errmsg_for_redeem_in_point_price_prt' ).parent().parent().hide() ;
            }
        } ,
        tax_notification : function () {
            MessageTabScripts.show_or_hide_for_tax_notification() ;
        } ,
        show_or_hide_for_tax_notification : function () {
            if ( jQuery( '#rs_show_hide_message_notice_for_redeeming' ).val() == '1' ) {
                jQuery( '#rs_msg_for_redeem_when_tax_enabled' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_msg_for_redeem_when_tax_enabled' ).parent().parent().hide() ;
            }
        } ,
        sumo_payment_plan_msg : function () {
            MessageTabScripts.show_or_hide_for_sumo_payment_plan_msg() ;
        } ,
        show_or_hide_for_sumo_payment_plan_msg : function () {
            if ( jQuery( '#rs_show_hide_message_for_each_payment_plan_products' ).val() == '1' ) {
                jQuery( '#rs_message_payment_plan_product_in_cart' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_payment_plan_product_in_cart' ).parent().parent().hide() ;
            }
        } ,
        sumo_payment_plan_buy_points_msg : function () {
            MessageTabScripts.show_or_hide_for_sumo_payment_plan_buy_points_msg() ;
        } ,
        show_or_hide_for_sumo_payment_plan_buy_points_msg : function () {
            if ( jQuery( '#rs_show_hide_buy_point_message_for_each_payment_plan_products' ).val() == '1' ) {
                jQuery( '#rs_buy_point_message_payment_plan_product_in_cart' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_buy_point_message_payment_plan_product_in_cart' ).parent().parent().hide() ;
            }
        } ,
        sumo_total_payment_plan_points_for_referral : function () {
            MessageTabScripts.show_or_hide_for_sumo_total_payment_plan_points_for_referral() ;
        } ,
        show_or_hide_for_sumo_total_payment_plan_points_for_referral : function () {
            if ( jQuery( '#rs_show_hide_message_for_total_payment_plan_points_referral' ).val() == '1' ) {
                jQuery( '#rs_referral_point_message_payment_plan_product_in_cart' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_referral_point_message_payment_plan_product_in_cart' ).parent().parent().hide() ;
            }
        } ,
        sumo_total_payment_plan_points_in_cart : function () {
            MessageTabScripts.show_or_hide_for_sumo_total_payment_plan_points_in_cart() ;
        } ,
        show_or_hide_for_sumo_total_payment_plan_points_in_cart : function () {
            if ( jQuery( '#rs_show_hide_message_for_total_payment_plan_points' ).val() == '1' ) {
                jQuery( '#rs_message_payment_plan_total_price_in_cart' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_payment_plan_total_price_in_cart' ).parent().parent().hide() ;
            }
        } ,
        sumo_each_payment_plan_points_in_checkout : function () {
            MessageTabScripts.show_or_hide_for_sumo_each_payment_plan_points_in_checkout() ;
        } ,
        show_or_hide_for_sumo_each_payment_plan_points_in_checkout : function () {
            if ( jQuery( '#rs_show_hide_message_for_each_payment_plan_products_checkout_page' ).val() == '1' ) {
                jQuery( '#rs_message_payment_plan_product_in_checkout' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_payment_plan_product_in_checkout' ).parent().parent().hide() ;
            }
        } ,
        sumo_each_payment_plan_buying_points_in_checkout : function () {
            MessageTabScripts.show_or_hide_for_sumo_each_payment_plan_buying_points_in_checkout() ;
        } ,
        show_or_hide_for_sumo_each_payment_plan_buying_points_in_checkout : function () {
            if ( jQuery( '#rs_show_hide_buy_point_message_for_each_payment_plan_products_checkout_page' ).val() == '1' ) {
                jQuery( '#rs_buy_point_message_payment_plan_product_in_checkout' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_buy_point_message_payment_plan_product_in_checkout' ).parent().parent().hide() ;
            }
        } ,
        sumo_total_payment_plan_points_for_referral_in_checkout : function () {
            MessageTabScripts.show_or_hide_for_sumo_total_payment_plan_points_for_referral_in_checkout() ;
        } ,
        show_or_hide_for_sumo_total_payment_plan_points_for_referral_in_checkout : function () {
            if ( jQuery( '#rs_show_hide_message_for_total_payment_plan_points_referrel_checkout' ).val() == '1' ) {
                jQuery( '#rs_referral_point_message_payment_plan_product_in_checkout' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_referral_point_message_payment_plan_product_in_checkout' ).parent().parent().hide() ;
            }
        } ,
        sumo_total_payment_plan_points_in_checkout : function () {
            MessageTabScripts.show_or_hide_for_sumo_total_payment_plan_points_in_checkout() ;
        } ,
        show_or_hide_for_sumo_total_payment_plan_points_in_checkout : function () {
            if ( jQuery( '#rs_show_hide_message_for_total_payment_plan_points_checkout_page' ).val() == '1' ) {
                jQuery( '#rs_message_payment_plan_total_price_in_checkout' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_message_payment_plan_total_price_in_checkout' ).parent().parent().hide() ;
            }
        } ,
        show_or_hide_for_position_for_var : function () {
            if ( jQuery( '#rs_enable_display_earn_message_for_variation' ).is( ':checked' ) == true ) {
                $( '#rs_msg_position_for_var_products_in_shop_page' ).closest( 'tr' ).show() ;
            } else {
                $( '#rs_msg_position_for_var_products_in_shop_page' ).closest( 'tr' ).hide() ;
            }
        } ,
        show_or_hide_for_reward_shortcode : function () {
            if ( jQuery( '#rs_my_reward_table_shortcode' ).val() == '1' ) {
                jQuery( '#rs_points_log_sorting_shortcode' ).closest( 'tr' ).show() ;
                jQuery( '#rs_show_hide_search_box_in_my_rewards_table_shortcode' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_reward_points_expire_shortcode' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_reward_points_s_no_shortcode' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_reward_points_user_name_hide_shortcode' ).closest( 'tr' ).show() ;
                jQuery( '#rs_show_hide_page_size_my_rewards_shortcode' ).closest( 'tr' ).show() ;
                MessageTabScripts.show_or_hide_for_msg_for_page_size_my_reward_table_shortcode() ;
                jQuery( '#rs_reward_table_position_shortcode' ).closest( 'tr' ).show() ;
                jQuery( '#rs_reward_point_label_position_shortcode' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_rewards_total_shortcode' ).closest( 'tr' ).show() ;
                jQuery( '#rs_reward_currency_value_shortcode' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_rewards_title_shortcode' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_rewards_sno_label_shortcode' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_rewards_userid_label_shortcode' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_rewards_rewarder_label_shortcode' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_rewards_points_earned_label_shortcode' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_rewards_redeem_points_label_shortcode' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_rewards_total_points_label_shortcode' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_rewards_date_label_shortcode' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_rewards_points_expired_label_shortcode' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_points_log_sorting' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_show_hide_search_box_in_my_rewards_table_shortcode' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_reward_points_expire_shortcode' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_reward_points_s_no_shortcode' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_reward_points_user_name_hide_shortcode' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_show_hide_page_size_my_rewards_shortcode' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_number_of_page_size_in_myrewards_shortcode' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_reward_table_position_shortcode' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_reward_point_label_position_shortcode' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_rewards_total_shortcode' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_reward_currency_value_shortcode' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_rewards_title_shortcode' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_rewards_sno_label_shortcode' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_rewards_userid_label_shortcode' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_rewards_rewarder_label_shortcode' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_rewards_points_earned_label_shortcode' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_rewards_redeem_points_label_shortcode' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_rewards_total_points_label_shortcode' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_rewards_date_label_shortcode' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_rewards_points_expired_label_shortcode' ).closest( 'tr' ).hide() ;
            }
        }
    } ;
    MessageTabScripts.init() ;
} ) ;