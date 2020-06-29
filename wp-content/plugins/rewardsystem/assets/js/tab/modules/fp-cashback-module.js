/*
 * Cashback - Module
 */
jQuery( function ( $ ) {
    var CashbackModule = {
        init : function () {
            this.show_or_hide_for_enable_cashback_reward_points() ;
            this.show_or_hide_for_enable_recaptcha_reward_points() ;
            this.email_notification_for_cashback_admin() ;
            this.show_or_hide_for_cashback_table() ;
            this.show_or_hide_for_cashback_table_shortcode() ;
            $( document ).on( 'change' , '#rs_enable_disable_encashing' , this.enable_cashback_reward_points ) ;
            $( document ).on( 'change' , '#rs_enable_recaptcha_to_display' , this.enable_recaptcha_reward_points ) ;
            $( document ).on( 'change' , '#rs_my_cashback_table' , this.show_or_hide_for_cashback_table ) ;
            $( document ).on( 'change' , '#rs_my_cashback_table_shortcode' , this.show_or_hide_for_cashback_table_shortcode ) ;
            $( document ).on( 'change' , '#rs_email_notification_for_Admin_cashback' , this.email_notification_for_cashback ) ;
        } ,
        enable_recaptcha_reward_points : function () {
            CashbackModule.show_or_hide_for_enable_recaptcha_reward_points() ;
        } ,
        show_or_hide_for_enable_recaptcha_reward_points : function () {
            if ( jQuery( '#rs_enable_recaptcha_to_display' ).is( ':checked' ) == true ) {
                jQuery( '#rs_google_recaptcha_label' ).closest( 'tr' ).show() ;
                jQuery( '#rs_google_recaptcha_site_key' ).closest( 'tr' ).show() ;
                jQuery( '#rs_google_recaptcha_secret_key' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_google_recaptcha_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_google_recaptcha_site_key' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_google_recaptcha_secret_key' ).closest( 'tr' ).hide() ;
            }
        } ,
        enable_cashback_reward_points : function () {
            CashbackModule.show_or_hide_for_enable_cashback_reward_points() ;
        } ,
        show_or_hide_for_enable_cashback_reward_points : function () {
            if ( jQuery( '#rs_enable_disable_encashing' ).val() == '1' ) {
                jQuery( '#rs_minimum_points_encashing_request' ).closest( 'tr' ).show() ;
                jQuery( '#rs_maximum_points_encashing_request' ).closest( 'tr' ).show() ;
                jQuery( '#rs_allow_user_to_request_cashback' ).closest( 'tr' ).show() ;
                jQuery( '#rs_total_points_for_cashback_request' ).closest( 'tr' ).show() ;
                jQuery( '#rs_encashing_points_label' ).closest( 'tr' ).show() ;
                jQuery( '#rs_encashing_reason_label' ).closest( 'tr' ).show() ;
                jQuery( '#rs_encashing_payment_method_label' ).closest( 'tr' ).show() ;
                jQuery( '#rs_encashing_submit_button_label' ).closest( 'tr' ).show() ;
                jQuery( '#rs_select_payment_method' ).closest( 'tr' ).show() ;
                jQuery( '#rs_select_type_to_redirect' ).closest( 'tr' ).show() ;
                jQuery( '#rs_allow_admin_to_save_previous_payment_method' ).closest( 'tr' ).show() ;

                if ( jQuery( '#rs_select_type_to_redirect' ).val() == '1' ) {
                    jQuery( '#rs_custom_page_url_after_submit' ).closest( 'tr' ).hide() ;
                } else {
                    jQuery( '#rs_custom_page_url_after_submit' ).closest( 'tr' ).show() ;
                }

                jQuery( '#rs_select_type_to_redirect' ).change( function () {
                    if ( jQuery( '#rs_select_type_to_redirect' ).val() == '1' ) {
                        jQuery( '#rs_custom_page_url_after_submit' ).closest( 'tr' ).hide() ;
                    } else {
                        jQuery( '#rs_custom_page_url_after_submit' ).closest( 'tr' ).show() ;
                    }
                } ) ;

                /*Show or hide Settings for Payment Method Display - Start*/
                if ( jQuery( '#rs_select_payment_method' ).val() == '1' ) {
                    jQuery( '#rs_encashing_payment_paypal_label' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_encashing_payment_custom_label' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_encashing_wallet_label' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_encashing_wallet_menu_label' ).closest( 'tr' ).hide() ;
                } else if ( jQuery( '#rs_select_payment_method' ).val() == '2' ) {
                    jQuery( '#rs_encashing_payment_custom_label' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_encashing_payment_paypal_label' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_encashing_wallet_label' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_encashing_wallet_menu_label' ).closest( 'tr' ).hide() ;
                } else if ( jQuery( '#rs_select_payment_method' ).val() == '4' ) {
                    jQuery( '#rs_encashing_payment_paypal_label' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_encashing_payment_custom_label' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_encashing_wallet_label' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_encashing_wallet_menu_label' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_encashing_payment_paypal_label' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_encashing_payment_custom_label' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_encashing_wallet_label' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_encashing_wallet_menu_label' ).closest( 'tr' ).show() ;
                }

                jQuery( '#rs_select_payment_method' ).change( function () {
                    if ( jQuery( '#rs_select_payment_method' ).val() == '1' ) {
                        jQuery( '#rs_encashing_payment_paypal_label' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_encashing_payment_custom_label' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_encashing_wallet_label' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_encashing_wallet_menu_label' ).closest( 'tr' ).hide() ;
                    } else if ( jQuery( '#rs_select_payment_method' ).val() == '2' ) {
                        jQuery( '#rs_encashing_payment_custom_label' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_encashing_payment_paypal_label' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_encashing_wallet_label' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_encashing_wallet_menu_label' ).closest( 'tr' ).hide() ;
                    } else if ( jQuery( '#rs_select_payment_method' ).val() == '4' ) {
                        jQuery( '#rs_encashing_payment_paypal_label' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_encashing_payment_custom_label' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_encashing_wallet_label' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_encashing_wallet_menu_label' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_encashing_payment_paypal_label' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_encashing_payment_custom_label' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_encashing_wallet_label' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_encashing_wallet_menu_label' ).closest( 'tr' ).show() ;
                    }
                } ) ;

                jQuery( '#rs_user_selection_type_for_cashback' ).closest( 'tr' ).show() ;
                if ( jQuery( '#rs_user_selection_type_for_cashback' ).val() == '1' ) {
                    jQuery( '#rs_select_inc_user_search' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_select_exc_user_search' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_select_inc_userrole' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_select_exc_userrole' ).closest( 'tr' ).hide() ;
                } else if ( jQuery( '#rs_user_selection_type_for_cashback' ).val() == '2' ) {
                    jQuery( '#rs_select_inc_user_search' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_select_exc_user_search' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_select_inc_userrole' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_select_exc_userrole' ).closest( 'tr' ).hide() ;
                } else if ( jQuery( '#rs_user_selection_type_for_cashback' ).val() == '3' ) {
                    jQuery( '#rs_select_inc_user_search' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_select_exc_user_search' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_select_inc_userrole' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_select_exc_userrole' ).closest( 'tr' ).hide() ;
                } else if ( jQuery( '#rs_user_selection_type_for_cashback' ).val() == '4' ) {
                    jQuery( '#rs_select_inc_user_search' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_select_exc_user_search' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_select_inc_userrole' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_select_exc_userrole' ).closest( 'tr' ).hide() ;
                } else if ( jQuery( '#rs_user_selection_type_for_cashback' ).val() == '5' ) {
                    jQuery( '#rs_select_inc_user_search' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_select_exc_user_search' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_select_inc_userrole' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_select_exc_userrole' ).closest( 'tr' ).hide() ;
                } else {
                    jQuery( '#rs_select_inc_user_search' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_select_exc_user_search' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_select_inc_userrole' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_select_exc_userrole' ).closest( 'tr' ).show() ;
                }
                jQuery( '#rs_user_selection_type_for_cashback' ).change( function () {
                    if ( jQuery( '#rs_user_selection_type_for_cashback' ).val() == '1' ) {
                        jQuery( '#rs_select_inc_user_search' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_select_exc_user_search' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_select_inc_userrole' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_select_exc_userrole' ).closest( 'tr' ).hide() ;
                    } else if ( jQuery( '#rs_user_selection_type_for_cashback' ).val() == '2' ) {
                        jQuery( '#rs_select_inc_user_search' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_select_exc_user_search' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_select_inc_userrole' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_select_exc_userrole' ).closest( 'tr' ).hide() ;
                    } else if ( jQuery( '#rs_user_selection_type_for_cashback' ).val() == '3' ) {
                        jQuery( '#rs_select_inc_user_search' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_select_exc_user_search' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_select_inc_userrole' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_select_exc_userrole' ).closest( 'tr' ).hide() ;
                    } else if ( jQuery( '#rs_user_selection_type_for_cashback' ).val() == '4' ) {
                        jQuery( '#rs_select_inc_user_search' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_select_exc_user_search' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_select_inc_userrole' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_select_exc_userrole' ).closest( 'tr' ).hide() ;
                    } else if ( jQuery( '#rs_user_selection_type_for_cashback' ).val() == '5' ) {
                        jQuery( '#rs_select_inc_user_search' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_select_exc_user_search' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_select_inc_userrole' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_select_exc_userrole' ).closest( 'tr' ).hide() ;
                    } else {
                        jQuery( '#rs_select_inc_user_search' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_select_exc_user_search' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_select_inc_userrole' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_select_exc_userrole' ).closest( 'tr' ).show() ;
                    }
                } ) ;
                /*Show or hide Settings for Payment Method Display - End*/
            } else {
                jQuery( '#rs_minimum_points_encashing_request' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_maximum_points_encashing_request' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_encashing_points_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_allow_user_to_request_cashback' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_total_points_for_cashback_request' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_encashing_reason_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_encashing_payment_method_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_encashing_submit_button_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_select_payment_method' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_encashing_payment_paypal_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_encashing_payment_custom_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_encashing_wallet_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_encashing_wallet_menu_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_select_type_to_redirect' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_custom_page_url_after_submit' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_allow_admin_to_save_previous_payment_method' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_select_inc_user_search' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_select_exc_user_search' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_select_inc_userrole' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_select_exc_userrole' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_user_selection_type_for_cashback' ).closest( 'tr' ).hide() ;
            }
        } ,
        email_notification_for_cashback : function () {
            CashbackModule.email_notification_for_cashback_admin() ;
        } ,
        email_notification_for_cashback_admin : function () {
            if ( jQuery( '#rs_email_notification_for_Admin_cashback' ).is( ':checked' ) == true ) {
                jQuery( '#rs_email_subject_message_for_cashback' ).closest( 'tr' ).show() ;
                jQuery( '#rs_email_message_for_cashback' ).closest( 'tr' ).show() ;
                jQuery( "input[type='radio'][name='rs_mail_sender_for_admin_for_cashback']" ).closest( 'tr' ).show() ;
                if ( jQuery( 'input[name=rs_mail_sender_for_admin_for_cashback]:checked' ).val() == 'local' ) {
                    jQuery( '#rs_from_name_for_admin_cashback' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_from_email_for_admin_cashback' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_from_name_for_admin_cashback' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_from_email_for_admin_cashback' ).closest( 'tr' ).hide() ;
                }
            } else {
                jQuery( '#rs_email_subject_message_for_cashback' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_message_for_cashback' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_from_name_for_admin_cashback' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_from_email_for_admin_cashback' ).closest( 'tr' ).hide() ;
                jQuery( "input[type='radio'][name='rs_mail_sender_for_admin_for_cashback']" ).closest( 'tr' ).hide() ;
            }
            jQuery( '#rs_email_notification_for_Admin_cashback' ).change( function () {
                if ( jQuery( '#rs_email_notification_for_Admin_cashback' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_message_for_cashback' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_email_message_for_cashback' ).closest( 'tr' ).show() ;
                    jQuery( "input[type='radio'][name='rs_mail_sender_for_admin_for_cashback']" ).closest( 'tr' ).show() ;
                    if ( jQuery( 'input[name=rs_mail_sender_for_admin_for_cashback]:checked' ).val() == 'local' ) {
                        jQuery( '#rs_from_name_for_admin_cashback' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_from_email_for_admin_cashback' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_from_name_for_admin_cashback' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_from_email_for_admin_cashback' ).closest( 'tr' ).hide() ;
                    }
                } else {
                    jQuery( '#rs_email_subject_message_for_cashback' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_email_message_for_cashback' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_from_name_for_admin_cashback' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_from_email_for_admin_cashback' ).closest( 'tr' ).hide() ;
                    jQuery( "input[type='radio'][name='rs_mail_sender_for_admin_for_cashback']" ).closest( 'tr' ).hide() ;
                }
            } ) ;
            jQuery( 'input[name=rs_mail_sender_for_admin_for_cashback]:radio' ).click( function () {
                if ( jQuery( 'input[name=rs_mail_sender_for_admin_for_cashback]:checked' ).val() == 'local' ) {
                    jQuery( '#rs_from_name_for_admin_cashback' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_from_email_for_admin_cashback' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_from_name_for_admin_cashback' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_from_email_for_admin_cashback' ).closest( 'tr' ).hide() ;
                }
            } ) ;
        } ,
        show_or_hide_for_cashback_table : function () {
            if ( jQuery( '#rs_my_cashback_table' ).val() == '1' ) {
                jQuery( '#rs_my_cashback_title' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_cashback_sno_label' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_cashback_userid_label' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_cashback_requested_label' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_cashback_status_label' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_my_cashback_title' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_cashback_sno_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_cashback_userid_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_cashback_requested_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_cashback_status_label' ).closest( 'tr' ).hide() ;
            }
        } ,
        show_or_hide_for_cashback_table_shortcode : function () {
            if ( jQuery( '#rs_my_cashback_table_shortcode' ).val() == '1' ) {
                jQuery( '#rs_my_cashback_title_shortcode' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_cashback_sno_label_shortcode' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_cashback_userid_label_shortcode' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_cashback_requested_label_shortcode' ).closest( 'tr' ).show() ;
                jQuery( '#rs_my_cashback_status_label_shortcode' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_my_cashback_title_shortcode' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_cashback_sno_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_cashback_userid_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_cashback_requested_label' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_my_cashback_status_label' ).closest( 'tr' ).hide() ;
            }
        } ,
    } ;
    CashbackModule.init() ;
} ) ;