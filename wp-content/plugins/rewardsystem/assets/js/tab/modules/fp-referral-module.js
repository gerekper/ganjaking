/*
 * Referral - Module
 */
jQuery( function ( $ ) {
    'use strict' ;
    var rs_custom_uploader ;
    var ReferralModuleScripts = {
        init : function () {
            this.trigger_on_page_load() ;
            this.show_or_hide_for_referral_cookie_expiry_settings() ;
            this.show_or_hide_for_delete_referral_cookie_expiry() ;
            this.show_or_hide_for_enable_referral_link_limit() ;
            this.show_or_hide_for_enable_referral_signup() ;
            this.show_or_hide_social_share_button();
            this.show_or_hide_for_generate_referral_link() ;
            this.show_or_hide_for_user_selection() ;
            this.show_or_hide_for_title_and_descrition_for_fbshare() ;
            this.show_or_hide_twitter_share_field();
            this.show_or_hide_for_referree_time_slection() ;
            this.show_or_hide_for_enable_revoke_function_for_referral() ;
            this.show_or_hide_for_generate_referral_msg() ;
            this.show_or_hide_for_referral_table() ;
            this.show_or_hide_for_referral_table_shortcode() ;
            this.show_or_hide_for_refer_friend_form() ;
            this.show_or_hide_for_product_category_selection() ;        
            this.enable_referral_reward_signup_bonus();
            $( document ).on( 'change' , '#rs_global_referral_reward_type' , this.toggle_referral_reward_type_for_product_total ) ;
            $( document ).on( 'change' , '#rs_global_referral_reward_type_refer' , this.toggle_referred_reward_type_for_product_total ) ;
            $( document ).on( 'change' , '#rs_global_referral_reward_type_for_cart_total' , this.toggle_referral_reward_type_for_cart_total ) ;
            $( document ).on( 'change' , '#rs_global_referral_reward_type_refer_for_cart_total' , this.toggle_referred_reward_type_for_cart_total ) ;
            $( document ).on( 'change' , '#rs_send_mail_pdt_purchase_referral' , this.toggle_send_mail_pdt_purchase_referral ) ;
            $( document ).on( 'change' , '#rs_send_mail_pdt_purchase_referrer' , this.toggle_send_mail_pdt_purchase_referred ) ;
            $( document ).on( 'change' , '#rs_referral_product_purchase_global_level_applicable_for' , this.toggle_product_filter ) ;
            $( document ).on( 'change' , '#rs_global_enable_disable_sumo_referral_reward' , this.toggle_enable_disable_sumo_referral_reward ) ;
            $( document ).on( 'change' , '#rs_award_points_for_cart_or_product_total_for_refferal_system' , this.toggle_cart_or_product_total_for_refferal_system ) ;
            $( '.rs_enable_product_category_level_for_referral_product_purchase' ).change( this.toggle_enable_product_category_level_for_referral_product_purchase ).change() ;

            $( document ).on( 'change' , '#rs_referral_cookies_expiry' , this.referral_cookie_expiry_settings ) ;
            $( document ).on( 'change' , '#rs_enable_delete_referral_cookie_after_first_purchase' , this.delete_referral_cookie_expiry ) ;
            $( document ).on( 'change' , '#rs_enable_referral_link_limit' , this.enable_referral_link_limit ) ;
            $( document ).on( 'change' , '#_rs_referral_enable_signups' , this.enable_referral_signup ) ;
            $( document ).on( 'change' , '#rs_show_hide_generate_referral' , this.generate_referral_link ) ;
            $( document ).on( 'change' , '#rs_enable_referral_link_generate_after_first_order' , this.restrict_referral_system_based_on_purchase_history ) ;
            $( document ).on( 'change' , '#rs_select_type_of_user_for_referral' , this.user_selection ) ;
            $( document ).on( 'change' , '#rs_account_show_hide_facebook_share_button' , this.title_and_descrition_for_fbshare ) ;
            $( document ).on( 'change' , '#rs_account_show_hide_twitter_tweet_button' , this.toggle_twitter_share_field ) ;
            $( document ).on( 'change' , '#_rs_select_referral_points_referee_time' , this.referree_time_slection ) ;
            $( document ).on( 'change' , '#_rs_reward_referal_point_user_deleted' , this.enable_revoke_function_for_referral ) ;
            $( document ).on( 'change' , '#rs_show_hide_generate_referral_message' , this.generate_referral_msg ) ;
            $( document ).on( 'change' , '#rs_show_hide_referal_table' , this.referral_table ) ;
            $( document ).on( 'change' , '#rs_show_hide_referal_table_shortcode' , this.referral_table_shortcode ) ;
            $( document ).on( 'change' , '#rs_enable_message_for_friend_form' , this.refer_friend_form ) ;
            $( document ).on( 'change' , '.rs_which_product_selection' , this.product_category_selection ) ;
            $( document ).on( 'click' , '.add' , this.add_manual_referral_link_rule ) ;
            $( document ).on( 'click' , '.rs-remove-manual-referral-link-rule' , this.remove_manual_referral_link_rule ) ;

            // Upload gift voucher.
            $( document ).on( 'click' , '#rs_fbimage_upload_button' , this.upload_gift_voucher ) ;            
            $( document ).on( 'change' , '#rs_account_show_hide_social_share_button' , this.toggle_social_share_button ) ;
            $( document ).on( 'change' , '#rs_enable_referral_bonus_reward_signup' , this.enable_referral_reward_signup_bonus ) ;
        } ,
        trigger_on_page_load : function () {
            if ( fp_referral_module_params.fp_wc_version <= parseFloat( '2.2.0' ) ) {
                $( '#rs_select_users_role_for_show_referral_link' ).chosen() ;
                $( '#rs_select_exclude_users_role_for_show_referral_link' ).chosen() ;
                $( '#rs_include_particular_categories_for_referral_product_purchase' ).chosen() ;
                $( '#rs_exclude_particular_categories_for_referral_product_purchase' ).chosen() ;
                $( '.rs_select_particular_categories' ).chosen() ;
            } else {
                $( '#rs_select_users_role_for_show_referral_link' ).select2() ;
                $( '#rs_select_exclude_users_role_for_show_referral_link' ).select2() ;
                $( '#rs_include_particular_categories_for_referral_product_purchase' ).select2() ;
                $( '#rs_exclude_particular_categories_for_referral_product_purchase' ).select2() ;
                $( '.rs_select_particular_categories' ).select2() ;
            }

            ReferralModuleScripts.referral_reward_type_for_product_total() ;
            ReferralModuleScripts.referred_reward_type_for_product_total() ;
            ReferralModuleScripts.referral_reward_type_for_cart_total() ;
            ReferralModuleScripts.referred_reward_type_for_cart_total() ;
            ReferralModuleScripts.send_mail_pdt_purchase_referral() ;
            ReferralModuleScripts.send_mail_pdt_purchase_referred() ;
            ReferralModuleScripts.show_or_hide_for_product_filter() ;
            ReferralModuleScripts.enable_disable_sumo_referral_reward() ;
            ReferralModuleScripts.cart_or_product_total_for_refferal_system() ;
        } ,

        toggle_referral_reward_type_for_product_total : function () {
            ReferralModuleScripts.referral_reward_type_for_product_total() ;
        } ,
        referral_reward_type_for_product_total : function () {
            if ( '1' == $( '#rs_global_referral_reward_type' ).val() ) {
                $( '#rs_global_referral_reward_point' ).parent().parent().show() ;
                $( '#rs_global_referral_reward_percent' ).parent().parent().hide() ;
            } else {
                $( '#rs_global_referral_reward_point' ).parent().parent().hide() ;
                $( '#rs_global_referral_reward_percent' ).parent().parent().show() ;
            }
        } ,
        toggle_referred_reward_type_for_product_total : function () {
            ReferralModuleScripts.referred_reward_type_for_product_total() ;
        } ,
        referred_reward_type_for_product_total : function () {
            if ( '1' == $( '#rs_global_referral_reward_type_refer' ).val() ) {
                $( '#rs_global_referral_reward_point_get_refer' ).parent().parent().show() ;
                $( '#rs_global_referral_reward_percent_get_refer' ).parent().parent().hide() ;
            } else {
                $( '#rs_global_referral_reward_point_get_refer' ).parent().parent().hide() ;
                $( '#rs_global_referral_reward_percent_get_refer' ).parent().parent().show() ;
            }
        } ,
        toggle_referral_reward_type_for_cart_total : function () {
            ReferralModuleScripts.referral_reward_type_for_cart_total() ;
        } ,
        referral_reward_type_for_cart_total : function () {
            if ( '1' == $( '#rs_global_referral_reward_type_for_cart_total' ).val() ) {
                $( '#rs_global_referral_reward_point_for_cart_total' ).parent().parent().show() ;
                $( '#rs_global_referral_reward_percent_for_cart_total' ).parent().parent().hide() ;
            } else {
                $( '#rs_global_referral_reward_point_for_cart_total' ).parent().parent().hide() ;
                $( '#rs_global_referral_reward_percent_for_cart_total' ).parent().parent().show() ;
            }
        } ,
        toggle_referred_reward_type_for_cart_total : function () {
            ReferralModuleScripts.referred_reward_type_for_cart_total() ;
        } ,
        referred_reward_type_for_cart_total : function () {
            if ( '1' == $( '#rs_global_referral_reward_type_refer_for_cart_total' ).val() ) {
                $( '#rs_global_referral_reward_point_get_refer_for_cart_total' ).parent().parent().show() ;
                $( '#rs_global_referral_reward_percent_get_refer_for_cart_total' ).parent().parent().hide() ;
            } else {
                $( '#rs_global_referral_reward_point_get_refer_for_cart_total' ).parent().parent().hide() ;
                $( '#rs_global_referral_reward_percent_get_refer_for_cart_total' ).parent().parent().show() ;
            }
        } ,
        toggle_send_mail_pdt_purchase_referral : function () {
            ReferralModuleScripts.send_mail_pdt_purchase_referral() ;
        } ,
        send_mail_pdt_purchase_referral : function () {
            if ( $( '#rs_send_mail_pdt_purchase_referral' ).is( ':checked' ) ) {
                $( '#rs_email_subject_pdt_purchase_referral' ).closest( 'tr' ).show() ;
                $( '#rs_email_message_pdt_purchase_referral' ).closest( 'tr' ).show() ;
            } else {
                $( '#rs_email_subject_pdt_purchase_referral' ).closest( 'tr' ).hide() ;
                $( '#rs_email_message_pdt_purchase_referral' ).closest( 'tr' ).hide() ;
            }
        } ,
        toggle_send_mail_pdt_purchase_referred : function () {
            ReferralModuleScripts.send_mail_pdt_purchase_referred() ;
        } ,
        send_mail_pdt_purchase_referred : function () {
            if ( $( '#rs_send_mail_pdt_purchase_referrer' ).is( ':checked' ) ) {
                $( '#rs_email_subject_pdt_purchase_referrer' ).closest( 'tr' ).show() ;
                $( '#rs_email_message_pdt_purchase_referrer' ).closest( 'tr' ).show() ;
            } else {
                $( '#rs_email_subject_pdt_purchase_referrer' ).closest( 'tr' ).hide() ;
                $( '#rs_email_message_pdt_purchase_referrer' ).closest( 'tr' ).hide() ;
            }
        } ,
        product_category_selection : function () {
            ReferralModuleScripts.show_or_hide_for_product_category_selection() ;
        } ,
        show_or_hide_for_product_category_selection : function () {
            if ( ( $( '.rs_which_product_selection' ).val() === '1' ) ) {
                $( '#rs_select_particular_products' ).parent().parent().hide() ;
                $( '#rs_select_particular_categories' ).parent().parent().hide() ;
            } else if ( $( '.rs_which_product_selection' ).val() === '2' ) {
                $( '#rs_select_particular_products' ).parent().parent().show() ;
                $( '#rs_select_particular_categories' ).parent().parent().hide() ;
            } else if ( $( '.rs_which_product_selection' ).val() === '3' ) {
                $( '#rs_select_particular_products' ).parent().parent().hide() ;
                $( '#rs_select_particular_categories' ).parent().parent().hide() ;
            } else {
                $( '#rs_select_particular_categories' ).parent().parent().show() ;
                $( '#rs_select_particular_products' ).parent().parent().hide() ;
            }
        } ,
        referral_cookie_expiry_settings : function () {
            ReferralModuleScripts.show_or_hide_for_referral_cookie_expiry_settings() ;
        } ,
        show_or_hide_for_referral_cookie_expiry_settings : function () {
            if ( jQuery( '#rs_referral_cookies_expiry' ).val() == '1' ) {
                jQuery( '#rs_referral_cookies_expiry_in_min' ).parent().parent().show() ;
                jQuery( '#rs_referral_cookies_expiry_in_hours' ).parent().parent().hide() ;
                jQuery( '#rs_referral_cookies_expiry_in_days' ).parent().parent().hide() ;
            } else if ( jQuery( '#rs_referral_cookies_expiry' ).val() == '2' ) {
                jQuery( '#rs_referral_cookies_expiry_in_min' ).parent().parent().hide() ;
                jQuery( '#rs_referral_cookies_expiry_in_hours' ).parent().parent().show() ;
                jQuery( '#rs_referral_cookies_expiry_in_days' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_referral_cookies_expiry_in_min' ).parent().parent().hide() ;
                jQuery( '#rs_referral_cookies_expiry_in_hours' ).parent().parent().hide() ;
                jQuery( '#rs_referral_cookies_expiry_in_days' ).parent().parent().show() ;
            }
        } ,
        delete_referral_cookie_expiry : function () {
            ReferralModuleScripts.show_or_hide_for_delete_referral_cookie_expiry() ;
        } ,
        show_or_hide_for_delete_referral_cookie_expiry : function () {
            if ( jQuery( '#rs_enable_delete_referral_cookie_after_first_purchase' ).is( ":checked" ) == false ) {
                jQuery( '#rs_no_of_purchase' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_no_of_purchase' ).parent().parent().show() ;
            }
        } ,
        enable_referral_link_limit : function () {
            ReferralModuleScripts.show_or_hide_for_enable_referral_link_limit() ;
        } ,
        show_or_hide_for_enable_referral_link_limit : function () {
            if ( jQuery( '#rs_enable_referral_link_limit' ).is( ':checked' ) == true ) {
                jQuery( '#rs_referral_link_limit' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_referral_link_limit' ).closest( 'tr' ).hide() ;
            }
        } ,
        toggle_product_filter : function () {
            ReferralModuleScripts.show_or_hide_for_product_filter() ;
        } ,
        show_or_hide_for_product_filter : function () {
            if ( jQuery( '#rs_referral_product_purchase_global_level_applicable_for' ).val() == '1' ) {
                jQuery( '#rs_include_products_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_products_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_include_particular_categories_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_particular_categories_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
            } else if ( jQuery( '#rs_referral_product_purchase_global_level_applicable_for' ).val() == '2' ) {
                jQuery( '#rs_include_products_for_referral_product_purchase' ).closest( 'tr' ).show() ;
                jQuery( '#rs_exclude_products_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_include_particular_categories_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_particular_categories_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
            } else if ( jQuery( '#rs_referral_product_purchase_global_level_applicable_for' ).val() == '3' ) {
                jQuery( '#rs_include_products_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_products_for_referral_product_purchase' ).closest( 'tr' ).show() ;
                jQuery( '#rs_include_particular_categories_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_particular_categories_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
            } else if ( jQuery( '#rs_referral_product_purchase_global_level_applicable_for' ).val() == '4' ) {
                jQuery( '#rs_include_products_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_products_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_include_particular_categories_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_particular_categories_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
            } else if ( jQuery( '#rs_referral_product_purchase_global_level_applicable_for' ).val() == '5' ) {
                jQuery( '#rs_include_products_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_products_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_include_particular_categories_for_referral_product_purchase' ).closest( 'tr' ).show() ;
                jQuery( '#rs_exclude_particular_categories_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
            } else {
                jQuery( '#rs_include_products_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_products_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_include_particular_categories_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_particular_categories_for_referral_product_purchase' ).closest( 'tr' ).show() ;
            }
        } ,
        toggle_enable_disable_sumo_referral_reward : function () {
            ReferralModuleScripts.enable_disable_sumo_referral_reward() ;
        } ,
        enable_disable_sumo_referral_reward : function () {
            ReferralModuleScripts.enable_product_category_level_for_referral_product_purchase() ;
        } ,
        toggle_cart_or_product_total_for_refferal_system : function () {
            ReferralModuleScripts.cart_or_product_total_for_refferal_system() ;
        } ,
        cart_or_product_total_for_refferal_system : function () {
            ReferralModuleScripts.enable_product_category_level_for_referral_product_purchase() ;
        } ,
        toggle_enable_product_category_level_for_referral_product_purchase : function () {
            ReferralModuleScripts.enable_product_category_level_for_referral_product_purchase() ;
        } ,
        enable_product_category_level_for_referral_product_purchase : function () {

            if ( 'no' == $( 'input[name=rs_enable_product_category_level_for_referral_product_purchase]:checked' ).val() ) {
                $( '#rs_award_points_for_cart_or_product_total_for_refferal_system' ).closest( 'tr' ).show() ;
                if ( '1' == $( '#rs_award_points_for_cart_or_product_total_for_refferal_system' ).val() ) {
                    $( '#rs_referral_product_purchase_global_level_applicable_for' ).closest( 'tr' ).show() ;
                    ReferralModuleScripts.show_or_hide_for_product_filter() ;
                    if ( '2' == $( '#rs_global_enable_disable_sumo_referral_reward' ).val() ) {
                        $( '.show_if_enable_in_referral' ).closest( 'tr' ).hide() ;
                    } else {
                        $( '.show_if_enable_in_referral' ).closest( 'tr' ).show() ;
                        $( '.show_referral_based_on_product_total' ).closest( 'tr' ).show() ;
                        ReferralModuleScripts.referral_reward_type_for_product_total() ;
                        ReferralModuleScripts.referred_reward_type_for_product_total() ;
                        $( '.show_referral_based_on_cart_total' ).closest( 'tr' ).hide() ;
                    }
                    
                    $( '#rs_restrict_sale_price_product_points_referral_system' ).closest( 'tr' ).show() ;
                    $( '#rs_restrict_referral_reward' ).closest( 'tr' ).show() ;
                    $( '#rs_referral_points_after_discounts' ).closest( 'tr' ).show() ;
                    $( '#rs_exclude_shipping_cost_based_on_cart_total_for_referral_module' ).closest( 'tr' ).hide() ;
                } else {

                    $( '#rs_referral_product_purchase_global_level_applicable_for' ).closest( 'tr' ).hide() ;
                    $( '#rs_include_products_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                    $( '#rs_exclude_products_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                    $( '#rs_include_particular_categories_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                    $( '#rs_exclude_particular_categories_for_referral_product_purchase' ).closest( 'tr' ).hide() ;

                    if ( '2' == $( '#rs_global_enable_disable_sumo_referral_reward' ).val() ) {
                        $( '.show_if_enable_in_referral' ).closest( 'tr' ).hide() ;
                    } else {
                        $( '.show_if_enable_in_referral' ).closest( 'tr' ).show() ;
                        $( '.show_referral_based_on_product_total' ).closest( 'tr' ).hide() ;
                        $( '.show_referral_based_on_cart_total' ).closest( 'tr' ).show() ;
                        ReferralModuleScripts.referral_reward_type_for_cart_total() ;
                        ReferralModuleScripts.referred_reward_type_for_cart_total() ;
                        ReferralModuleScripts.send_mail_pdt_purchase_referral() ;
                        ReferralModuleScripts.send_mail_pdt_purchase_referred() ;

                    }

                    $( '#rs_restrict_sale_price_product_points_referral_system' ).closest( 'tr' ).hide() ;
                    $( '#rs_restrict_referral_reward' ).closest( 'tr' ).hide() ;
                    $( '#rs_referral_points_after_discounts' ).closest( 'tr' ).hide() ;
                    $( '#rs_exclude_shipping_cost_based_on_cart_total_for_referral_module' ).closest( 'tr' ).show() ;
                }
            } else {
                $( '#rs_award_points_for_cart_or_product_total_for_refferal_system' ).closest( 'tr' ).hide() ;
                $( '#rs_referral_product_purchase_global_level_applicable_for' ).closest( 'tr' ).hide() ;
                $( '#rs_referral_product_purchase_global_level_applicable_for' ).closest( 'tr' ).hide() ;
                $( '#rs_include_products_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_exclude_products_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_include_particular_categories_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_exclude_particular_categories_for_referral_product_purchase' ).closest( 'tr' ).hide() ;

                if ( '2' == $( '#rs_global_enable_disable_sumo_referral_reward' ).val() ) {
                    $( '.show_if_enable_in_referral' ).closest( 'tr' ).hide() ;
                } else {
                    $( '.show_if_enable_in_referral' ).closest( 'tr' ).show() ;
                    $( '.show_referral_based_on_product_total' ).closest( 'tr' ).show() ;
                    ReferralModuleScripts.referral_reward_type_for_product_total() ;
                    ReferralModuleScripts.referred_reward_type_for_product_total() ;
                    $( '.show_referral_based_on_cart_total' ).closest( 'tr' ).hide() ;

                    ReferralModuleScripts.send_mail_pdt_purchase_referral() ;
                    ReferralModuleScripts.send_mail_pdt_purchase_referred() ;
                }

                $( '#rs_restrict_sale_price_product_points_referral_system' ).closest( 'tr' ).show() ;
                $( '#rs_restrict_referral_reward' ).closest( 'tr' ).show() ;
                $( '#rs_referral_points_after_discounts' ).closest( 'tr' ).show() ;
                $( '#rs_exclude_shipping_cost_based_on_cart_total_for_referral_module' ).closest( 'tr' ).hide() ;
            }
        } ,
        enable_referral_signup : function () {
            ReferralModuleScripts.show_or_hide_for_enable_referral_signup() ;
        } ,
        show_or_hide_for_enable_referral_signup : function () {
            if ( jQuery( '#_rs_referral_enable_signups' ).is( ':checked' ) == true ) {
                jQuery( '#rs_select_referral_points_award' ).closest( 'tr' ).show() ;
                jQuery( '#rs_referral_reward_signup' ).closest( 'tr' ).show() ;
                if ( jQuery( '#rs_select_referral_points_award' ).val() == '1' ) {
                    jQuery( '#rs_number_of_order_for_referral_points' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_amount_of_order_for_referral_points' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_referral_reward_signup_after_first_purchase' ).closest( 'tr' ).show() ;
                } else if ( jQuery( '#rs_select_referral_points_award' ).val() == '2' ) {
                    jQuery( '#rs_amount_of_order_for_referral_points' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_referral_reward_signup_after_first_purchase' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_number_of_order_for_referral_points' ).closest( 'tr' ).show() ;
                } else if ( jQuery( '#rs_select_referral_points_award' ).val() == '3' ) {
                    jQuery( '#rs_number_of_order_for_referral_points' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_referral_reward_signup_after_first_purchase' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_amount_of_order_for_referral_points' ).closest( 'tr' ).show() ;
                }

                jQuery( '#rs_select_referral_points_award' ).change( function () {
                    if ( jQuery( '#rs_select_referral_points_award' ).val() == '1' ) {
                        jQuery( '#rs_number_of_order_for_referral_points' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_amount_of_order_for_referral_points' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_referral_reward_signup_after_first_purchase' ).closest( 'tr' ).show() ;
                    } else if ( jQuery( '#rs_select_referral_points_award' ).val() == '2' ) {
                        jQuery( '#rs_amount_of_order_for_referral_points' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_referral_reward_signup_after_first_purchase' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_number_of_order_for_referral_points' ).closest( 'tr' ).show() ;
                    } else if ( jQuery( '#rs_select_referral_points_award' ).val() == '3' ) {
                        jQuery( '#rs_number_of_order_for_referral_points' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_referral_reward_signup_after_first_purchase' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_amount_of_order_for_referral_points' ).closest( 'tr' ).show() ;
                    }
                } ) ;

                jQuery( '#rs_referral_reward_signup_getting_refer' ).closest( 'tr' ).show() ;
                if ( jQuery( '#rs_referral_reward_signup_getting_refer' ).val() == '1' ) {
                    jQuery( '#rs_referral_reward_getting_refer' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_referral_reward_getting_refer_after_first_purchase' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_send_mail_getting_referred' ).closest( 'tr' ).show() ;
                    if ( jQuery( '#rs_send_mail_getting_referred' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_getting_referred' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_email_message_getting_referred' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_email_subject_getting_referred' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_email_message_getting_referred' ).closest( 'tr' ).hide() ;
                    }

                    jQuery( '#rs_send_mail_getting_referred' ).change( function () {
                        if ( jQuery( '#rs_send_mail_getting_referred' ).is( ':checked' ) == true ) {
                            jQuery( '#rs_email_subject_getting_referred' ).closest( 'tr' ).show() ;
                            jQuery( '#rs_email_message_getting_referred' ).closest( 'tr' ).show() ;
                        } else {
                            jQuery( '#rs_email_subject_getting_referred' ).closest( 'tr' ).hide() ;
                            jQuery( '#rs_email_message_getting_referred' ).closest( 'tr' ).hide() ;
                        }
                    } ) ;
                } else if ( jQuery( '#rs_referral_reward_signup_getting_refer' ).val() == '2' ) {
                    jQuery( '#rs_referral_reward_getting_refer' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_referral_reward_getting_refer_after_first_purchase' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_send_mail_getting_referred' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_email_subject_getting_referred' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_email_message_getting_referred' ).closest( 'tr' ).hide() ;
                }

                jQuery( '#rs_referral_reward_signup_getting_refer' ).change( function () {
                    if ( jQuery( '#rs_referral_reward_signup_getting_refer' ).val() == '1' ) {
                        jQuery( '#rs_referral_reward_getting_refer' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_referral_reward_getting_refer_after_first_purchase' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_send_mail_getting_referred' ).closest( 'tr' ).show() ;
                        if ( jQuery( '#rs_send_mail_getting_referred' ).is( ':checked' ) == true ) {
                            jQuery( '#rs_email_subject_getting_referred' ).closest( 'tr' ).show() ;
                            jQuery( '#rs_email_message_getting_referred' ).closest( 'tr' ).show() ;
                        } else {
                            jQuery( '#rs_email_subject_getting_referred' ).closest( 'tr' ).hide() ;
                            jQuery( '#rs_email_message_getting_referred' ).closest( 'tr' ).hide() ;
                        }

                        jQuery( '#rs_send_mail_getting_referred' ).change( function () {
                            if ( jQuery( '#rs_send_mail_getting_referred' ).is( ':checked' ) == true ) {
                                jQuery( '#rs_email_subject_getting_referred' ).closest( 'tr' ).show() ;
                                jQuery( '#rs_email_message_getting_referred' ).closest( 'tr' ).show() ;
                            } else {
                                jQuery( '#rs_email_subject_getting_referred' ).closest( 'tr' ).hide() ;
                                jQuery( '#rs_email_message_getting_referred' ).closest( 'tr' ).hide() ;
                            }
                        } ) ;
                    } else if ( jQuery( '#rs_referral_reward_signup_getting_refer' ).val() == '2' ) {
                        jQuery( '#rs_referral_reward_getting_refer' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_referral_reward_getting_refer_after_first_purchase' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_send_mail_getting_referred' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_email_subject_getting_referred' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_email_message_getting_referred' ).closest( 'tr' ).hide() ;
                    }
                } ) ;
                jQuery( '#rs_send_mail_referral_signup' ).closest( 'tr' ).show() ;
                if ( jQuery( '#rs_send_mail_referral_signup' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_referral_signup' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_email_message_referral_signup' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_email_subject_referral_signup' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_email_message_referral_signup' ).closest( 'tr' ).hide() ;
                }

                jQuery( '#rs_send_mail_referral_signup' ).change( function () {
                    if ( jQuery( '#rs_send_mail_referral_signup' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_referral_signup' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_email_message_referral_signup' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_email_subject_referral_signup' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_email_message_referral_signup' ).closest( 'tr' ).hide() ;
                    }
                } ) ;
            } else {
                jQuery( '#rs_select_referral_points_award' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_number_of_order_for_referral_points' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_amount_of_order_for_referral_points' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_referral_reward_signup_after_first_purchase' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_referral_reward_getting_refer' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_referral_reward_getting_refer_after_first_purchase' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_send_mail_getting_referred' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_subject_getting_referred' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_message_getting_referred' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_referral_reward_signup_getting_refer' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_referral_reward_signup' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_subject_referral_signup' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_message_referral_signup' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_send_mail_referral_signup' ).closest( 'tr' ).hide() ;
            }
        } ,
        generate_referral_link : function () {
            ReferralModuleScripts.show_or_hide_for_generate_referral_link() ;
        } ,
        show_or_hide_for_generate_referral_link : function () {
            if ( jQuery( '#rs_show_hide_generate_referral' ).val() == '1' ) {
                jQuery( '#rs_show_hide_generate_referral_link_type' ).parent().parent().show() ;
                jQuery( '#rs_generate_referral_link_based_on_user' ).parent().parent().show() ;
                jQuery( '#rs_enable_copy_to_clipboard' ).closest( 'tr' ).show() ;
                jQuery( '#rs_generate_link_label' ).closest( 'tr' ).show() ;
                jQuery( '#rs_generate_link_sno_label' ).closest( 'tr' ).show() ;
                jQuery( '#rs_generate_link_date_label' ).closest( 'tr' ).show() ;
                jQuery( '#rs_generate_link_referrallink_label' ).closest( 'tr' ).show() ;
                jQuery( '#rs_generate_link_social_label' ).closest( 'tr' ).show() ;
                jQuery( '#rs_generate_link_action_label' ).closest( 'tr' ).show() ;
                jQuery( '#rs_generate_link_button_label' ).closest( 'tr' ).show() ;
                jQuery( '#rs_display_generate_referral' ).closest( 'tr' ).show() ;
                jQuery( '#rs_extra_class_name_generate_referral_link' ).closest( 'tr' ).show() ;

                if ( jQuery( '#rs_show_hide_generate_referral_link_type' ).val() == '1' ) {
                    jQuery( '#rs_prefill_generate_link' ).parent().parent().show() ;
                    jQuery( '#rs_static_generate_link' ).parent().parent().hide() ;
                    jQuery( '#rs_my_referral_link_button_label' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_prefill_generate_link' ).parent().parent().hide() ;
                    jQuery( '#rs_static_generate_link' ).parent().parent().show() ;
                    jQuery( '#rs_my_referral_link_button_label' ).parent().parent().show() ;
                }

                jQuery( '#rs_show_hide_generate_referral_link_type' ).change( function () {
                    if ( jQuery( '#rs_show_hide_generate_referral_link_type' ).val() == '1' ) {
                        jQuery( '#rs_prefill_generate_link' ).parent().parent().show() ;
                        jQuery( '#rs_static_generate_link' ).parent().parent().hide() ;
                        jQuery( '#rs_my_referral_link_button_label' ).parent().parent().hide() ;
                    } else {
                        jQuery( '#rs_prefill_generate_link' ).parent().parent().hide() ;
                        jQuery( '#rs_static_generate_link' ).parent().parent().show() ;
                        jQuery( '#rs_my_referral_link_button_label' ).parent().parent().show() ;
                    }
                } ) ;

                jQuery( '#rs_select_type_of_user_for_referral' ).closest( 'tr' ).show() ;

                jQuery( '#rs_display_msg_when_access_is_prevented' ).closest( 'tr' ).show() ;
                if ( jQuery( '#rs_display_msg_when_access_is_prevented' ).val() == '1' ) {
                    jQuery( '#rs_msg_for_restricted_user' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_msg_for_restricted_user' ).closest( 'tr' ).hide() ;
                }

                jQuery( '#rs_display_msg_when_access_is_prevented' ).change( function () {
                    if ( jQuery( '#rs_display_msg_when_access_is_prevented' ).val() == '1' ) {
                        jQuery( '#rs_msg_for_restricted_user' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_msg_for_restricted_user' ).closest( 'tr' ).hide() ;
                    }
                } ) ;
                jQuery( '#rs_enable_referral_link_generate_after_first_order' ).closest( 'tr' ).show() ;
                ReferralModuleScripts.show_or_hide_for_restrict_referral_system_based_on_purchase_history() ;
                
                jQuery('#rs_generate_link_hover_label').closest('tr').show();
                jQuery( '#rs_account_show_hide_social_share_button' ).closest( 'tr' ).show() ;
                ReferralModuleScripts.show_or_hide_social_share_button() ;
            } else {
                jQuery( '#rs_show_hide_generate_referral_link_type' ).parent().parent().hide() ;
                jQuery( '#rs_prefill_generate_link' ).parent().parent().hide() ;
                jQuery( '#rs_static_generate_link' ).parent().parent().hide() ;
                jQuery( '#rs_my_referral_link_button_label' ).parent().parent().hide() ;
                jQuery( '#rs_generate_referral_link_based_on_user' ).parent().parent().hide() ;
                jQuery( '#rs_enable_copy_to_clipboard' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_generate_link_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_generate_link_sno_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_generate_link_date_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_generate_link_referrallink_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_generate_link_social_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_generate_link_action_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_generate_link_button_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_select_type_of_user_for_referral' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_select_exclude_users_list_for_show_referral_link' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_select_include_users_for_show_referral_link' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_select_users_role_for_show_referral_link' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_select_exclude_users_role_for_show_referral_link' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_display_msg_when_access_is_prevented' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_msg_for_restricted_user' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_display_generate_referral' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_extra_class_name_generate_referral_link' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_set_order_status_for_generate_link' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_referral_link_generated_settings' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_getting_number_of_orders' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_number_of_amount_spent' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_enable_referral_link_generate_after_first_order' ).closest( 'tr' ).hide() ;
                
                jQuery('#rs_generate_link_hover_label').closest('tr').hide();
                jQuery( '#rs_account_show_hide_social_share_button' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_account_show_hide_facebook_share_button' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_facebook_description' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_facebook_title' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_fbshare_image_url_upload' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_account_show_hide_twitter_tweet_button' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_twitter_share_text' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_acount_show_hide_google_plus_button' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_acount_show_hide_whatsapp_button' ).closest( 'tr' ).hide( ) ;
            }
        } ,
        restrict_referral_system_based_on_purchase_history : function () {
            ReferralModuleScripts.show_or_hide_for_restrict_referral_system_based_on_purchase_history() ;
        } ,
        show_or_hide_for_restrict_referral_system_based_on_purchase_history : function () {
            if ( jQuery( '#rs_enable_referral_link_generate_after_first_order' ).is( ':checked' ) == true ) {
                jQuery( '#rs_set_order_status_for_generate_link' ).closest( 'tr' ).show() ;
                jQuery( '#rs_referral_link_generated_settings' ).closest( 'tr' ).show() ;
                if ( jQuery( '#rs_referral_link_generated_settings' ).val() == '1' ) {
                    jQuery( '#rs_getting_number_of_orders' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_number_of_amount_spent' ).closest( 'tr' ).hide() ;
                } else {
                    jQuery( '#rs_getting_number_of_orders' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_number_of_amount_spent' ).closest( 'tr' ).show() ;
                }
                jQuery( '#rs_referral_link_generated_settings' ).change( function () {
                    if ( jQuery( '#rs_referral_link_generated_settings' ).val() == '1' ) {
                        jQuery( '#rs_getting_number_of_orders' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_number_of_amount_spent' ).closest( 'tr' ).hide() ;
                    } else {
                        jQuery( '#rs_getting_number_of_orders' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_number_of_amount_spent' ).closest( 'tr' ).show() ;
                    }
                } ) ;
            } else {
                jQuery( '#rs_set_order_status_for_generate_link' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_referral_link_generated_settings' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_getting_number_of_orders' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_number_of_amount_spent' ).closest( 'tr' ).hide() ;
            }
        } ,
        user_selection : function () {
            ReferralModuleScripts.show_or_hide_for_user_selection() ;
        } ,
        show_or_hide_for_user_selection : function () {
            if ( jQuery( '#rs_select_type_of_user_for_referral' ).val() === '1' ) {
                jQuery( '#rs_select_exclude_users_list_for_show_referral_link' ).parent().parent().hide() ;
                jQuery( '#rs_select_users_role_for_show_referral_link' ).parent().parent().hide() ;
                jQuery( '#rs_select_exclude_users_role_for_show_referral_link' ).parent().parent().hide() ;
                jQuery( '#rs_select_include_users_for_show_referral_link' ).parent().parent().hide() ;
            } else if ( jQuery( '#rs_select_type_of_user_for_referral' ).val() === '2' ) {
                jQuery( '#rs_select_exclude_users_list_for_show_referral_link' ).parent().parent().hide() ;
                jQuery( '#rs_select_users_role_for_show_referral_link' ).parent().parent().hide() ;
                jQuery( '#rs_select_exclude_users_role_for_show_referral_link' ).parent().parent().hide() ;
                jQuery( '#rs_select_include_users_for_show_referral_link' ).parent().parent().show() ;
            } else if ( jQuery( '#rs_select_type_of_user_for_referral' ).val() === '3' ) {
                jQuery( '#rs_select_include_users_for_show_referral_link' ).parent().parent().hide() ;
                jQuery( '#rs_select_exclude_users_list_for_show_referral_link' ).parent().parent().show() ;
                jQuery( '#rs_select_users_role_for_show_referral_link' ).parent().parent().hide() ;
                jQuery( '#rs_select_exclude_users_role_for_show_referral_link' ).parent().parent().hide() ;
            } else if ( jQuery( '#rs_select_type_of_user_for_referral' ).val() === '4' ) {
                jQuery( '#rs_select_exclude_users_list_for_show_referral_link' ).parent().parent().hide() ;
                jQuery( '#rs_select_users_role_for_show_referral_link' ).parent().parent().show() ;
                jQuery( '#rs_select_exclude_users_role_for_show_referral_link' ).parent().parent().hide() ;
                jQuery( '#rs_select_include_users_for_show_referral_link' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_select_exclude_users_list_for_show_referral_link' ).parent().parent().hide() ;
                jQuery( '#rs_select_users_role_for_show_referral_link' ).parent().parent().hide() ;
                jQuery( '#rs_select_exclude_users_role_for_show_referral_link' ).parent().parent().show() ;
                jQuery( '#rs_select_include_users_for_show_referral_link' ).parent().parent().hide() ;
            }
        } ,
        title_and_descrition_for_fbshare : function () {
            ReferralModuleScripts.show_or_hide_for_title_and_descrition_for_fbshare() ;
        } ,
        show_or_hide_for_title_and_descrition_for_fbshare : function () {
            if ( jQuery( '#rs_account_show_hide_facebook_share_button' ).val() == '1' ) {
                jQuery( '#rs_facebook_title' ).closest( 'tr' ).show() ;
                jQuery( '#rs_facebook_description' ).closest( 'tr' ).show() ;
                jQuery( '#rs_fbshare_image_url_upload' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_facebook_title' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_facebook_description' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_fbshare_image_url_upload' ).closest( 'tr' ).hide() ;
            }
        } ,
        referree_time_slection : function () {
            ReferralModuleScripts.show_or_hide_for_referree_time_slection() ;
        } ,
        show_or_hide_for_referree_time_slection : function () {
            if ( jQuery( '#_rs_select_referral_points_referee_time' ).val() == '2' ) {
                jQuery( '#_rs_select_referral_points_referee_time_content' ).parent().parent().show() ;
            } else {
                jQuery( '#_rs_select_referral_points_referee_time_content' ).parent().parent().hide() ;
            }
        } ,
        enable_revoke_function_for_referral : function () {
            ReferralModuleScripts.show_or_hide_for_enable_revoke_function_for_referral() ;
        } ,
        show_or_hide_for_enable_revoke_function_for_referral : function () {
            if ( jQuery( '#_rs_reward_referal_point_user_deleted' ).val() == '1' ) {
                jQuery( '#_rs_time_validity_to_redeem' ).closest( 'tr' ).show() ;
                if ( jQuery( '#_rs_time_validity_to_redeem' ).val() == '2' ) {
                    jQuery( '#_rs_days_for_redeeming_points' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#_rs_days_for_redeeming_points' ).closest( 'tr' ).hide() ;
                }

                jQuery( '#_rs_time_validity_to_redeem' ).change( function () {
                    if ( jQuery( '#_rs_time_validity_to_redeem' ).val() == '2' ) {
                        jQuery( '#_rs_days_for_redeeming_points' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#_rs_days_for_redeeming_points' ).closest( 'tr' ).hide() ;
                    }
                } ) ;
            } else {
                jQuery( '#_rs_time_validity_to_redeem' ).closest( 'tr' ).hide() ;
                jQuery( '#_rs_days_for_redeeming_points' ).closest( 'tr' ).hide() ;
            }
        } ,
        generate_referral_msg : function () {
            ReferralModuleScripts.show_or_hide_for_generate_referral_msg() ;
        } ,
        show_or_hide_for_generate_referral_msg : function () {
            if ( jQuery( '#rs_show_hide_generate_referral_message' ).val() == '1' ) {
                jQuery( '#rs_show_hide_generate_referral_message_text' ).closest( 'tr' ).show() ;
                jQuery( '#rs_send_message_by_referrer' ).closest( 'tr' ).show( ) ;
            } else {
                jQuery( '#rs_show_hide_generate_referral_message_text' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_send_message_by_referrer' ).closest( 'tr' ).hide( ) ;
            }
        } ,
        referral_table : function () {
            ReferralModuleScripts.show_or_hide_for_referral_table() ;
        } ,
        show_or_hide_for_referral_table : function () {
            if ( jQuery( '#rs_show_hide_referal_table' ).val() == '1' ) {
                jQuery( '#rs_referal_table_title' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_referal_sno_label' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_total_referal_points_label' ).closest( 'tr' ).show() ;
                jQuery( '#rs_select_option_for_referral' ).closest( 'tr' ).show( ) ;
                if ( jQuery( '#rs_select_option_for_referral' ).val() == '1' ) {
                    jQuery( '#rs_my_referal_userid_label' ).closest( 'tr' ).show( ) ;
                    jQuery( '#rs_referral_email_ids' ).closest( 'tr' ).hide( ) ;
                } else {
                    jQuery( '#rs_my_referal_userid_label' ).closest( 'tr' ).hide( ) ;
                    jQuery( '#rs_referral_email_ids' ).closest( 'tr' ).show( ) ;
                }
                jQuery( '#rs_select_option_for_referral' ).change( function () {
                    if ( jQuery( '#rs_select_option_for_referral' ).val() == '1' ) {
                        jQuery( '#rs_my_referal_userid_label' ).closest( 'tr' ).show( ) ;
                        jQuery( '#rs_referral_email_ids' ).closest( 'tr' ).hide( ) ;
                    } else {
                        jQuery( '#rs_my_referal_userid_label' ).closest( 'tr' ).hide( ) ;
                        jQuery( '#rs_referral_email_ids' ).closest( 'tr' ).show( ) ;
                    }
                } ) ;
            } else {
                jQuery( '#rs_referal_table_title' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_my_referal_sno_label' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_my_referal_userid_label' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_referral_email_ids' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_select_option_for_referral' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_my_total_referal_points_label' ).closest( 'tr' ).hide( ) ;
            }
        } ,
        referral_table_shortcode : function () {
            ReferralModuleScripts.show_or_hide_for_referral_table_shortcode() ;
        } ,
        show_or_hide_for_referral_table_shortcode : function ( ) {
            if ( jQuery( '#rs_show_hide_referal_table_shortcode' ).val( ) == '1' ) {
                jQuery( '#rs_referal_table_title_shortcode' ).closest( 'tr' ).show( ) ;
                jQuery( '#rs_my_referal_sno_label_shortcode' ).closest( 'tr' ).show( ) ;
                jQuery( '#rs_my_total_referal_points_label_shortcode' ).closest( 'tr' ).show( ) ;
                jQuery( '#rs_select_option_for_referral_shortcode' ).closest( 'tr' ).show( ) ;
                if ( jQuery( '#rs_select_option_for_referral_shortcode' ).val() == '1' ) {
                    jQuery( '#rs_my_referal_userid_label_shortcode' ).closest( 'tr' ).show( ) ;
                    jQuery( '#rs_referral_email_ids_shortcode' ).closest( 'tr' ).hide( ) ;
                } else {
                    jQuery( '#rs_my_referal_userid_label_shortcode' ).closest( 'tr' ).hide( ) ;
                    jQuery( '#rs_referral_email_ids_shortcode' ).closest( 'tr' ).show( ) ;
                }
                jQuery( '#rs_select_option_for_referral_shortcode' ).change( function () {
                    if ( jQuery( '#rs_select_option_for_referral_shortcode' ).val() == '1' ) {
                        jQuery( '#rs_my_referal_userid_label_shortcode' ).closest( 'tr' ).show( ) ;
                        jQuery( '#rs_referral_email_ids_shortcode' ).closest( 'tr' ).hide( ) ;
                    } else {
                        jQuery( '#rs_my_referal_userid_label_shortcode' ).closest( 'tr' ).hide( ) ;
                        jQuery( '#rs_referral_email_ids_shortcode' ).closest( 'tr' ).show( ) ;
                    }
                } ) ;
            } else {
                jQuery( '#rs_referal_table_title_shortcode' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_select_option_for_referral_shortcode' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_my_referal_sno_label_shortcode' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_my_referal_userid_label_shortcode' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_referral_email_ids_shortcode' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_my_total_referal_points_label_shortcode' ).closest( 'tr' ).hide( ) ;
            }
        } ,
        refer_friend_form : function () {
            ReferralModuleScripts.show_or_hide_for_refer_friend_form() ;
        } ,
        show_or_hide_for_refer_friend_form : function () {
            if ( jQuery( '#rs_enable_message_for_friend_form' ).val() == '1' ) {
                jQuery( '#rs_my_rewards_friend_name_label' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_rewards_friend_name_placeholder' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_rewards_friend_email_label' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_rewards_friend_email_placeholder' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_rewards_friend_subject_label' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_rewards_friend_email_subject_placeholder' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_rewards_friend_message_label' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_rewards_friend_email_message_placeholder' ).closest( 'tr' ).show() ;
                jQuery( '#rs_allow_user_to_request_prefilled_message' ).closest( 'tr' ).show() ;
                jQuery( '#rs_friend_referral_link' ).closest( 'tr' ).show() ;
                jQuery( '#rs_referral_link_refer_a_friend_form' ).closest( 'tr' ).show() ;
                jQuery( '#rs_show_hide_iagree_termsandcondition_field' ).closest( 'tr' ).show() ;
                if ( jQuery( '#rs_show_hide_iagree_termsandcondition_field' ).val() == '2' ) {
                    jQuery( '#rs_refer_friend_iagreecaption_link' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_refer_friend_termscondition_caption' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_refer_friend_termscondition_url' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_refer_friend_iagreecaption_link' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_refer_friend_termscondition_caption' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_refer_friend_termscondition_url' ).closest( 'tr' ).hide() ;
                }
                jQuery( '#rs_show_hide_iagree_termsandcondition_field' ).change( function () {
                    if ( jQuery( '#rs_show_hide_iagree_termsandcondition_field' ).val() == '2' ) {
                        jQuery( '#rs_refer_friend_iagreecaption_link' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_refer_friend_termscondition_caption' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_refer_friend_termscondition_url' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_refer_friend_iagreecaption_link' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_refer_friend_termscondition_caption' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_refer_friend_termscondition_url' ).closest( 'tr' ).hide() ;
                    }
                } ) ;
                jQuery( '#rs_allow_user_to_request_prefilled_subject' ).closest( 'tr' ).show() ;
                jQuery( '#rs_subject_field' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_my_rewards_friend_name_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_rewards_friend_name_placeholder' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_rewards_friend_email_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_rewards_friend_email_placeholder' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_rewards_friend_subject_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_rewards_friend_email_subject_placeholder' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_rewards_friend_message_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_rewards_friend_email_message_placeholder' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_allow_user_to_request_prefilled_message' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_friend_referral_link' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_show_hide_iagree_termsandcondition_field' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_refer_friend_iagreecaption_link' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_refer_friend_termscondition_caption' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_refer_friend_termscondition_url' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_referral_link_refer_a_friend_form' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_allow_user_to_request_prefilled_subject' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_subject_field' ).closest( 'tr' ).hide() ;
            }
        } ,
        toggle_social_share_button:function(){
            ReferralModuleScripts.show_or_hide_social_share_button() ;
        },
        show_or_hide_social_share_button : function () {
            if ( '1' == $( '#rs_account_show_hide_social_share_button' ).val() ) {
                $( '#rs_account_show_hide_facebook_share_button' ).closest( 'tr' ).show() ;
                $( '#rs_facebook_description' ).closest( 'tr' ).show( ) ;
                ReferralModuleScripts.show_or_hide_for_title_and_descrition_for_fbshare();
                $( '#rs_account_show_hide_twitter_tweet_button' ).closest( 'tr' ).show( ) ;
                ReferralModuleScripts.show_or_hide_twitter_share_field();
                $( '#rs_acount_show_hide_google_plus_button' ).closest( 'tr' ).show( ) ;
                $( '#rs_acount_show_hide_whatsapp_button' ).closest( 'tr' ).show( ) ;
            } else {
                $( '#rs_account_show_hide_facebook_share_button' ).closest( 'tr' ).hide() ;
                $( '#rs_facebook_description' ).closest( 'tr' ).hide( ) ;
                $( '#rs_facebook_title' ).closest( 'tr' ).hide( ) ;
                $( '#rs_fbshare_image_url_upload' ).closest( 'tr' ).hide( ) ;
                $( '#rs_account_show_hide_twitter_tweet_button' ).closest( 'tr' ).hide( ) ;
                $( '#rs_twitter_share_text' ).closest( 'tr' ).hide( ) ;
                $( '#rs_acount_show_hide_google_plus_button' ).closest( 'tr' ).hide( ) ;
                $( '#rs_acount_show_hide_whatsapp_button' ).closest( 'tr' ).hide( ) ;
            }
        } ,
        toggle_twitter_share_field:function(){
            ReferralModuleScripts.show_or_hide_twitter_share_field() ;
        },
        show_or_hide_twitter_share_field:function(){
            if ( '1' == $( '#rs_account_show_hide_twitter_tweet_button' ).val() ) {
                $( '#rs_twitter_share_text' ).closest( 'tr' ).show( ) ;
            } else {
                $( '#rs_twitter_share_text' ).closest( 'tr' ).hide( ) ;
            }
        },
        add_manual_referral_link_rule : function ( event ) {
            event.preventDefault( ) ;
            var $this = $( event.currentTarget ) ;
            var count = fp_referral_module_params.rule_count ;
            count = count + 1 ;
            var data = {
                action : 'add_manual_referral_link_rule' ,
                rule_count : count ,
                sumo_security : fp_referral_module_params.manual_referral_link_nonce
            } ;
            $.post( fp_referral_module_params.ajaxurl , data , function ( response ) {
                if ( true == response.success && response.data.html ) {
                    $( $this ).closest( '.rsdynamicrulecreation_manual' ).find( 'tbody' ).append( response.data.html ) ;
                    $( 'body' ).trigger( 'wc-enhanced-select-init' ) ;
                } else {
                    alert( response.data.error ) ;
                }
            } ) ;
        } ,
        remove_manual_referral_link_rule : function ( event ) {
            event.preventDefault( ) ;
            var $this = $( event.currentTarget ) ;
            $this.closest( 'tr' ).hide( ) ;
            var $row = $this.closest( 'tr' ).data( 'row' ) ;
            $this.closest( 'tr' ).find( 'span.rs_removed_rule' ).append( '<input type="hidden" name="rs_removed_link_rule[' + $row + ']" value="yes">' ) ;
        } ,
        upload_gift_voucher : function ( e ) {
            e.preventDefault() ;
            if ( rs_custom_uploader ) {
                rs_custom_uploader.open() ;
                return ;
            }
            rs_custom_uploader = wp.media.frames.file_frame = wp.media( {
                title : 'Choose Image' ,
                button : { text : 'Choose Image'
                } ,
                multiple : false
            } ) ;
            //When a file is selected, grab the URL and set it as the text field's value
            rs_custom_uploader.on( 'select' , function () {
                attachment = rs_custom_uploader.state().get( 'selection' ).first().toJSON() ;
                jQuery( '#rs_fbshare_image_url_upload' ).val( attachment.url ) ;
            } ) ;
            //Open the uploader dialog
            rs_custom_uploader.open() ;
        } ,

        enable_referral_reward_signup_bonus : function ( ) {
            if ( true ===  $( '#rs_enable_referral_bonus_reward_signup' ).is(':checked') ){
                $( '#rs_referral_reward_signup_bonus' ).closest( 'tr' ).show();
                $( '#rs_no_of_users_referral_to_get_reward_signup_bonus' ).closest( 'tr' ).show();
                $( '#rs_referral_reward_signup_bonus_points' ).closest( 'tr' ).show();
            } else {
                $( '#rs_referral_reward_signup_bonus' ).closest( 'tr' ).hide();
                $( '#rs_no_of_users_referral_to_get_reward_signup_bonus' ).closest( 'tr' ).hide();
                $( '#rs_referral_reward_signup_bonus_points' ).closest( 'tr' ).hide();
            }
        } ,

    } ;
    ReferralModuleScripts.init() ;
} ) ;
