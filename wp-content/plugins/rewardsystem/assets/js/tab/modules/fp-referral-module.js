/*
 * Referral - Module
 */
jQuery( function ( $ ) {
    var ReferralModuleScripts = {
        init : function () {
            this.trigger_on_page_load() ;
            this.show_or_hide_for_referral_cookie_expiry_settings() ;
            this.show_or_hide_for_delete_referral_cookie_expiry() ;
            this.show_or_hide_for_enable_referral_link_limit() ;
            this.show_or_hide_for_global_level_setup() ;
            this.show_or_hide_for_product_filter() ;
            this.show_or_hide_for_enable_referral_in_global() ;
            this.show_or_hide_for_enable_referral_signup() ;
            this.show_or_hide_for_generate_referral_link() ;
            this.show_or_hide_for_user_selection() ;
            this.show_or_hide_for_title_and_descrition_for_fbshare() ;
            this.show_or_hide_for_referree_time_slection() ;
            this.show_or_hide_for_enable_revoke_function_for_referral() ;
            this.show_or_hide_for_generate_referral_msg() ;
            this.show_or_hide_for_referral_table() ;
            this.show_or_hide_for_referral_table_shortcode() ;
            this.show_or_hide_for_refer_friend_form() ;
            this.show_or_hide_for_product_category_selection() ;
            $( document ).on( 'change' , '#rs_referral_cookies_expiry' , this.referral_cookie_expiry_settings ) ;
            $( document ).on( 'change' , '#rs_enable_delete_referral_cookie_after_first_purchase' , this.delete_referral_cookie_expiry ) ;
            $( document ).on( 'change' , '#rs_enable_referral_link_limit' , this.enable_referral_link_limit ) ;
            $( document ).on( 'change' , '.rs_enable_product_category_level_for_referral_product_purchase' , this.global_level_setup ) ;
            $( document ).on( 'change' , '#rs_referral_product_purchase_global_level_applicable_for' , this.product_filter ) ;
            $( document ).on( 'change' , '#rs_global_enable_disable_sumo_referral_reward' , this.enable_referral_in_global ) ;
            $( document ).on( 'change' , '#_rs_referral_enable_signups' , this.enable_referral_signup ) ;
            $( document ).on( 'change' , '#rs_show_hide_generate_referral' , this.generate_referral_link ) ;
            $( document ).on( 'change' , '#rs_enable_referral_link_generate_after_first_order' , this.restrict_referral_system_based_on_purchase_history ) ;
            $( document ).on( 'change' , '#rs_select_type_of_user_for_referral' , this.user_selection ) ;
            $( document ).on( 'change' , '#rs_account_show_hide_facebook_share_button' , this.title_and_descrition_for_fbshare ) ;
            $( document ).on( 'change' , '#_rs_select_referral_points_referee_time' , this.referree_time_slection ) ;
            $( document ).on( 'change' , '#_rs_reward_referal_point_user_deleted' , this.enable_revoke_function_for_referral ) ;
            $( document ).on( 'change' , '#rs_show_hide_generate_referral_message' , this.generate_referral_msg ) ;
            $( document ).on( 'change' , '#rs_show_hide_referal_table' , this.referral_table ) ;
            $( document ).on( 'change' , '#rs_show_hide_referal_table_shortcode' , this.referral_table_shortcode ) ;
            $( document ).on( 'change' , '#rs_enable_message_for_friend_form' , this.refer_friend_form ) ;
            $( document ).on( 'change' , '.rs_which_product_selection' , this.product_category_selection ) ;
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
        global_level_setup : function () {
            ReferralModuleScripts.show_or_hide_for_global_level_setup() ;
        } ,
        show_or_hide_for_global_level_setup : function () {
            if ( jQuery( 'input[name=rs_enable_product_category_level_for_referral_product_purchase]:checked' ).val() == 'no' ) {
                jQuery( '#rs_referral_product_purchase_global_level_applicable_for' ).closest( 'tr' ).show() ;
                jQuery( '.rs_hide_bulk_update_for_referral_product_purchase_start' ).hide() ;
            } else {
                jQuery( '#rs_referral_product_purchase_global_level_applicable_for' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_include_products_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_products_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_include_particular_categories_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_particular_categories_for_referral_product_purchase' ).closest( 'tr' ).hide() ;
                jQuery( '.rs_hide_bulk_update_for_referral_product_purchase_start' ).show() ;
            }
        } ,
        product_filter : function () {
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
        enable_referral_in_global : function () {
            ReferralModuleScripts.show_or_hide_for_enable_referral_in_global() ;
        } ,
        show_or_hide_for_enable_referral_in_global : function () {
            if ( jQuery( '#rs_global_enable_disable_sumo_referral_reward' ).val() == '2' ) {
                jQuery( '.show_if_enable_in_referral' ).closest( 'tr' ).hide() ;
            } else {
                jQuery( '.show_if_enable_in_referral' ).parent().parent().show() ;

                //To Show or hide Referral Points or Percentage for SUMO Reward.
                if ( jQuery( '#rs_global_referral_reward_type' ).val() == '1' ) {
                    jQuery( '#rs_global_referral_reward_point' ).parent().parent().show() ;
                    jQuery( '#rs_global_referral_reward_percent' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_global_referral_reward_point' ).parent().parent().hide() ;
                    jQuery( '#rs_global_referral_reward_percent' ).parent().parent().show() ;
                }

                jQuery( '#rs_global_referral_reward_type' ).change( function () {
                    if ( jQuery( '#rs_global_referral_reward_type' ).val() == '1' ) {
                        jQuery( '#rs_global_referral_reward_point' ).parent().parent().show() ;
                        jQuery( '#rs_global_referral_reward_percent' ).parent().parent().hide() ;
                    } else {
                        jQuery( '#rs_global_referral_reward_point' ).parent().parent().hide() ;
                        jQuery( '#rs_global_referral_reward_percent' ).parent().parent().show() ;
                    }
                } ) ;

                if ( jQuery( '#rs_global_referral_reward_type_refer' ).val() == '1' ) {
                    jQuery( '#rs_global_referral_reward_point_get_refer' ).parent().parent().show() ;
                    jQuery( '#rs_global_referral_reward_percent_get_refer' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_global_referral_reward_percent_get_refer' ).parent().parent().show() ;
                    jQuery( '#rs_global_referral_reward_point_get_refer' ).parent().parent().hide() ;
                }

                jQuery( '#rs_global_referral_reward_type_refer' ).change( function () {
                    if ( jQuery( '#rs_global_referral_reward_type_refer' ).val() == '1' ) {
                        jQuery( '#rs_global_referral_reward_point_get_refer' ).parent().parent().show() ;
                        jQuery( '#rs_global_referral_reward_percent_get_refer' ).parent().parent().hide() ;
                    } else {
                        jQuery( '#rs_global_referral_reward_point_get_refer' ).parent().parent().hide() ;
                        jQuery( '#rs_global_referral_reward_percent_get_refer' ).parent().parent().show() ;
                    }
                } ) ;
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
    } ;
    ReferralModuleScripts.init() ;
} ) ;