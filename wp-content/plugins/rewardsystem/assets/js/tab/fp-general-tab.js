/*
 * General tab
 */
jQuery( function ( $ ) {
    var GeneralTabScripts = {
        init : function () {
            this.trigger_on_page_load() ;
            this.show_or_hide_for_roundoff_type() ;
            this.show_or_hide_for_maximum_earn_points_for_user() ;
            this.show_or_hide_for_enable_user_role() ;
            this.show_or_hide_for_enable_membership_plan() ;
            this.show_or_hide_for_enable_earning_level() ;
            this.show_or_hide_for_enable_user_purchase_history() ;
            this.show_or_hide_to_ban_user_for_earning() ;
            this.show_or_hide_to_ban_user_for_redeeming() ;
            $( document ).on( 'change' , '#rs_round_off_type' , this.roundoff_type ) ;
            $( document ).on( 'change' , '#rs_enable_disable_max_earning_points_for_user' , this.maximum_earn_points_for_user ) ;
            $( document ).on( 'change' , '#rs_enable_user_role_based_reward_points' , this.enable_user_role ) ;
            $( document ).on( 'change' , '#rs_enable_membership_plan_based_reward_points' , this.enable_membership_plan ) ;
            $( document ).on( 'change' , '#rs_enable_earned_level_based_reward_points' , this.enable_earning_level ) ;
            $( document ).on( 'change' , '#rs_enable_user_purchase_history_based_reward_points' , this.enable_user_purchase_history ) ;
            $( document ).on( 'change' , '#rs_enable_banning_users_earning_points' , this.ban_user_for_earning ) ;
            $( document ).on( 'change' , '#rs_enable_banning_users_redeeming_points' , this.ban_user_for_redeeming ) ;
            $( document ).on( 'click' , '.rs_refresh_button' , this.refresh_expired_points ) ;
        } ,
        trigger_on_page_load : function () {
            if ( fp_general_tab_params.fp_wc_version <= parseFloat( '2.2.0' ) ) {
                $( '#rs_banning_user_role_for_earning' ).chosen() ;
                $( '#rs_banning_user_role_for_redeeming' ).chosen() ;
                $( '#rs_select_inc_userrole' ).chosen() ;
                $( '#rs_select_exc_userrole' ).chosen() ;
                $( '#rs_order_status_control' ).chosen() ;
                $( '#rs_order_status_control_to_automatic_order' ).chosen() ;
                $( '#rs_earning_percentage_order_status_control' ).chosen() ;
            } else {
                $( '#rs_banning_user_role_for_earning' ).select2() ;
                $( '#rs_banning_user_role_for_redeeming' ).select2() ;
                $( '#rs_select_inc_userrole' ).select2() ;
                $( '#rs_select_exc_userrole' ).select2() ;
                $( '#rs_order_status_control' ).select2() ;
                $( '#rs_order_status_control_to_automatic_order' ).select2() ;
                $( '#rs_earning_percentage_order_status_control' ).select2() ;
            }
        } ,
        roundoff_type : function () {
            GeneralTabScripts.show_or_hide_for_roundoff_type() ;
        } ,
        show_or_hide_for_roundoff_type : function () {
            if ( jQuery( '#rs_round_off_type' ).val() == 1 ) {
                jQuery( '#rs_round_up_down' ).parent().parent().hide() ;
                jQuery( '#rs_decimal_seperator_check' ).closest( 'tr' ).show() ;
                jQuery( '#rs_roundoff_type_for_currency' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_decimal_seperator_check_for_currency' ).closest( 'tr' ).hide() ;
            } else {
                jQuery( '#rs_round_up_down' ).parent().parent().show() ;
                jQuery( '#rs_decimal_seperator_check' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_roundoff_type_for_currency' ).closest( 'tr' ).show() ;
                if ( jQuery( '#rs_roundoff_type_for_currency' ).val() == '1' ) {
                    jQuery( '#rs_decimal_seperator_check_for_currency' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_decimal_seperator_check_for_currency' ).closest( 'tr' ).hide() ;
                }
                jQuery( '#rs_roundoff_type_for_currency' ).change( function () {
                    if ( jQuery( '#rs_roundoff_type_for_currency' ).val() == '1' ) {
                        jQuery( '#rs_decimal_seperator_check_for_currency' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_decimal_seperator_check_for_currency' ).closest( 'tr' ).hide() ;
                    }
                } ) ;
            }
        } ,
        maximum_earn_points_for_user : function () {
            GeneralTabScripts.show_or_hide_for_maximum_earn_points_for_user() ;
        } ,
        show_or_hide_for_maximum_earn_points_for_user : function () {
            if ( jQuery( '#rs_enable_disable_max_earning_points_for_user' ).is( ":checked" ) == false ) {
                jQuery( '#rs_max_earning_points_for_user' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_mail_for_reaching_maximum_threshold' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_mail_subject_for_reaching_maximum_threshold' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_mail_message_for_reaching_maximum_threshold' ).closest( 'tr' ).hide() ;
            } else {
                jQuery( '#rs_max_earning_points_for_user' ).closest( 'tr' ).show() ;
                jQuery( '#rs_mail_for_reaching_maximum_threshold' ).closest( 'tr' ).show() ;
                if ( jQuery( '#rs_mail_for_reaching_maximum_threshold' ).is( ":checked" ) == false ) {
                    jQuery( '#rs_mail_subject_for_reaching_maximum_threshold' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_mail_message_for_reaching_maximum_threshold' ).closest( 'tr' ).hide() ;
                } else {
                    jQuery( '#rs_mail_subject_for_reaching_maximum_threshold' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_mail_message_for_reaching_maximum_threshold' ).closest( 'tr' ).show() ;
                }

                jQuery( '#rs_mail_for_reaching_maximum_threshold' ).change( function () {
                    if ( jQuery( '#rs_mail_for_reaching_maximum_threshold' ).is( ":checked" ) == false ) {
                        jQuery( '#rs_mail_subject_for_reaching_maximum_threshold' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_mail_message_for_reaching_maximum_threshold' ).closest( 'tr' ).hide() ;
                    } else {
                        jQuery( '#rs_mail_subject_for_reaching_maximum_threshold' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_mail_message_for_reaching_maximum_threshold' ).closest( 'tr' ).show() ;
                    }
                } ) ;
            }
        } ,
        enable_user_role : function () {
            GeneralTabScripts.show_or_hide_for_enable_user_role() ;
        } ,
        show_or_hide_for_enable_user_role : function () {
            if ( jQuery( '#rs_enable_user_role_based_reward_points' ).is( ':checked' ) == true ) {
                jQuery( '.rewardpoints_userrole' ).parent().parent().show() ;
            } else {
                jQuery( '.rewardpoints_userrole' ).parent().parent().hide() ;
            }
        } ,
        enable_membership_plan : function () {
            GeneralTabScripts.show_or_hide_for_enable_membership_plan() ;
        } ,
        show_or_hide_for_enable_membership_plan : function () {
            if ( jQuery( '#rs_enable_membership_plan_based_reward_points' ).is( ':checked' ) == true ) {
                jQuery( '.rewardpoints_membership_plan' ).parent().parent().show() ;
            } else {
                jQuery( '.rewardpoints_membership_plan' ).parent().parent().hide() ;
            }
        } ,
        enable_earning_level : function () {
            GeneralTabScripts.show_or_hide_for_enable_earning_level() ;
        } ,
        show_or_hide_for_enable_earning_level : function () {
            if ( jQuery( '#rs_enable_earned_level_based_reward_points' ).is( ':checked' ) == true ) {
                jQuery( '.rsdynamicrulecreation' ).parent().show() ;
                jQuery( '#rs_select_earn_points_based_on' ).parent().parent().show() ;
                jQuery( '#rs_free_product_range' ).closest( 'tr' ).show() ;
                jQuery( '#rs_free_product_add_by_user_or_admin' ).closest( 'tr' ).show() ;
                if ( jQuery( '#rs_free_product_add_by_user_or_admin' ).val() == '1' ) {
                    jQuery( '#rs_order_status_control_to_automatic_order' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_subject_for_free_product_mail' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_content_for_free_product_mail' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_free_product_add_quantity' ).closest( 'tr' ).show() ;
                    if ( jQuery( '#rs_free_product_add_quantity' ).val() == '2' ) {
                        jQuery( '#rs_free_product_quantity' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_free_product_quantity' ).parent().parent().hide() ;
                    }
                    jQuery( '#rs_free_product_add_quantity' ).change( function () {
                        if ( jQuery( '#rs_free_product_add_quantity' ).val() == '2' ) {
                            jQuery( '#rs_free_product_quantity' ).parent().parent().show() ;
                        } else {
                            jQuery( '#rs_free_product_quantity' ).parent().parent().hide() ;
                        }
                    } ) ;
                } else {
                    jQuery( '#rs_order_status_control_to_automatic_order' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_subject_for_free_product_mail' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_content_for_free_product_mail' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_free_product_add_quantity' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_free_product_quantity' ).parent().parent().hide() ;
                }

                jQuery( '#rs_free_product_add_by_user_or_admin' ).change( function () {
                    if ( jQuery( '#rs_free_product_add_by_user_or_admin' ).val() == '1' ) {
                        jQuery( '#rs_order_status_control_to_automatic_order' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_subject_for_free_product_mail' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_content_for_free_product_mail' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_free_product_add_quantity' ).closest( 'tr' ).show() ;
                        if ( jQuery( '#rs_free_product_add_quantity' ).val() == '2' ) {
                            jQuery( '#rs_free_product_quantity' ).parent().parent().show() ;
                        } else {
                            jQuery( '#rs_free_product_quantity' ).parent().parent().hide() ;
                        }
                        jQuery( '#rs_free_product_add_quantity' ).change( function () {
                            if ( jQuery( '#rs_free_product_add_quantity' ).val() == '2' ) {
                                jQuery( '#rs_free_product_quantity' ).parent().parent().show() ;
                            } else {
                                jQuery( '#rs_free_product_quantity' ).parent().parent().hide() ;
                            }
                        } ) ;
                    } else {
                        jQuery( '#rs_order_status_control_to_automatic_order' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_subject_for_free_product_mail' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_content_for_free_product_mail' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_free_product_add_quantity' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_free_product_quantity' ).closest( 'tr' ).hide() ;
                    }
                } ) ;
            } else {
                jQuery( '.rsdynamicrulecreation' ).parent().hide() ;
                jQuery( '#rs_select_earn_points_based_on' ).parent().parent().hide() ;
                jQuery( '#rs_free_product_range' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_free_product_add_by_user_or_admin' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_subject_for_free_product_mail' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_content_for_free_product_mail' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_order_status_control_to_automatic_order' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_free_product_add_quantity' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_free_product_quantity' ).parent().parent().hide() ;
            }
        } ,
        enable_user_purchase_history : function () {
            GeneralTabScripts.show_or_hide_for_enable_user_purchase_history() ;
        } ,
        show_or_hide_for_enable_user_purchase_history : function () {
            if ( jQuery( '#rs_enable_user_purchase_history_based_reward_points' ).is( ':checked' ) == true ) {
                jQuery( '.rsdynamicrulecreationsforuserpurchasehistory' ).show() ;
                jQuery( '#rs_earning_percentage_order_status_control' ).closest( 'tr' ).show() ;
                jQuery( '#rs_product_purchase_history_range' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '.rsdynamicrulecreationsforuserpurchasehistory' ).hide() ;
                jQuery( '#rs_earning_percentage_order_status_control' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_product_purchase_history_range' ).closest( 'tr' ).hide() ;
            }
        } ,
        ban_user_for_earning : function () {
            GeneralTabScripts.show_or_hide_to_ban_user_for_earning() ;
        } ,
        show_or_hide_to_ban_user_for_earning : function () {
            if ( jQuery( '#rs_enable_banning_users_earning_points' ).is( ':checked' ) == true ) {
                jQuery( '#rs_banned_users_list_for_earning' ).closest( 'tr' ).show() ;
                jQuery( '#rs_banning_user_role_for_earning' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_banned_users_list_for_earning' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_banning_user_role_for_earning' ).closest( 'tr' ).hide() ;
            }
        } ,
        ban_user_for_redeeming : function () {
            GeneralTabScripts.show_or_hide_to_ban_user_for_redeeming() ;
        } ,
        show_or_hide_to_ban_user_for_redeeming : function () {
            if ( jQuery( '#rs_enable_banning_users_redeeming_points' ).is( ':checked' ) == true ) {
                jQuery( '#rs_banned_users_list_for_redeeming' ).closest( 'tr' ).show() ;
                jQuery( '#rs_banning_user_role_for_redeeming' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_banned_users_list_for_redeeming' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_banning_user_role_for_redeeming' ).closest( 'tr' ).hide() ;
            }
        } ,
        refresh_expired_points : function () {
            var rsconfirm = confirm( "It is strongly recommended that you do not reload or refresh page. Are you sure you wish to update expired points for all user now?" ) ;
            if ( rsconfirm === true ) {
                var dataparam = ( {
                    action : 'refreshexpiredpoints' ,
                    sumo_security : fp_general_tab_params.fp_refresh_points
                } ) ;
                $.post( fp_general_tab_params.ajaxurl , dataparam , function ( response ) {
                    if ( true === response.success ) {
                        console.log( 'Ajax Done Successfully' ) ;
                        window.location.href = fp_general_tab_params.redirect ;
                    } else {
                        window.alert( response.data.error ) ;
                    }
                } ) ;
            }
            return false ;
        }
    } ;
    GeneralTabScripts.init() ;

} ) ;