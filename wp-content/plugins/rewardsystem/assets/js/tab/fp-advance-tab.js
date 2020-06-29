/*
 * Advance Tab
 */
jQuery( function ( $ ) {
    var AdvanceTabScript = {
        init : function () {
            this.trigger_on_page_load() ;
            this.show_or_hide_for_my_account_menu_page() ;
            this.show_or_hide_for_apply_previous_order_range() ;
            this.show_or_hide_for_menu_restriction_based_on_userrole() ;
            this.show_or_hide_for_pagination_for_total_earned_points() ;
            this.show_or_hide_for_pagination_for_total_available_points() ;
            this.show_or_hide_for_enable_msg_to_participate_in_reward_prgm() ;
            this.show_or_hide_for_coupon_restriction() ;
            $( document ).on( 'change' , '#rs_reward_content_menu_page' , this.my_account_menu_page ) ;
            $( document ).on( 'change' , '#rs_sumo_select_order_range' , this.apply_previous_order_range ) ;
            $( document ).on( 'change' , '#rs_menu_restriction_based_on_user_role' , this.menu_restriction_based_on_userrole ) ;
            $( document ).on( 'change' , '#rs_select_pagination_for_total_earned_points' , this.pagination_for_total_earned_points ) ;
            $( document ).on( 'change' , '#rs_select_pagination_for_available_points' , this.pagination_for_total_available_points ) ;
            $( document ).on( 'change' , '#rs_enable_reward_program' , this.enable_msg_to_participate_in_reward_prgm ) ;
            $( document ).on( 'click' , '.rs_sumo_rewards_for_previous_order' , this.apply_points_for_previous_order ) ;
            $( document ).on( 'click' , '#rs_add_old_points' , this.add_old_points_for_user ) ;
            $( document ).on( 'click' , '#_rs_enable_coupon_restriction' , this.show_or_hide_for_coupon_restriction ) ;
        } ,
        trigger_on_page_load : function () {
            if ( fp_advance_params.fp_wc_version <= parseFloat( '2.2.0' ) ) {
                $( '.rewardpoints_userrole_menu_restriction' ).chosen() ;
            } else {
                $( '.rewardpoints_userrole_menu_restriction' ).select2() ;
            }
        } ,
        my_account_menu_page : function () {
            AdvanceTabScript.show_or_hide_for_my_account_menu_page() ;
        } ,
        show_or_hide_for_my_account_menu_page : function () {
            if ( jQuery( '#rs_reward_content_menu_page' ).is( ':checked' ) == true ) {
                jQuery( '#rs_my_reward_table_menu_page' ).parent().parent().show() ;
                jQuery( '#rs_show_hide_generate_referral_menu_page' ).parent().parent().show() ;
                jQuery( '#rs_show_hide_referal_table_menu_page' ).parent().parent().show() ;
                jQuery( '#rs_my_cashback_table_menu_page' ).parent().parent().show() ;
                jQuery( '#rs_show_hide_nominee_field_menu_page' ).parent().parent().show() ;
                jQuery( '#rs_show_hide_redeem_voucher_menu_page' ).parent().parent().show() ;
                jQuery( '#rs_show_hide_your_subscribe_link_menu_page' ).parent().parent().show() ;
                jQuery( '#rs_my_reward_content_title' ).parent().parent().show() ;
                jQuery( '#rs_my_reward_url_title' ).parent().parent().show() ;
                jQuery( '#rs_show_hide_refer_a_friend_menu_page' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_my_reward_table_menu_page' ).parent().parent().hide() ;
                jQuery( '#rs_show_hide_generate_referral_menu_page' ).parent().parent().hide() ;
                jQuery( '#rs_show_hide_referal_table_menu_page' ).parent().parent().hide() ;
                jQuery( '#rs_my_cashback_table_menu_page' ).parent().parent().hide() ;
                jQuery( '#rs_show_hide_nominee_field_menu_page' ).parent().parent().hide() ;
                jQuery( '#rs_show_hide_redeem_voucher_menu_page' ).parent().parent().hide() ;
                jQuery( '#rs_show_hide_your_subscribe_link_menu_page' ).parent().parent().hide() ;
                jQuery( '#rs_my_reward_content_title' ).parent().parent().hide() ;
                jQuery( '#rs_my_reward_url_title' ).parent().parent().hide() ;
                jQuery( '#rs_show_hide_refer_a_friend_menu_page' ).parent().parent().hide() ;
            }
        } ,
        apply_previous_order_range : function () {
            AdvanceTabScript.show_or_hide_for_apply_previous_order_range() ;
        } ,
        show_or_hide_for_apply_previous_order_range : function () {
            if ( jQuery( '#rs_sumo_select_order_range' ).val() === '1' ) {
                jQuery( '#rs_from_date' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_from_date' ).parent().parent().show() ;
            }
        } ,
        menu_restriction_based_on_userrole : function () {
            AdvanceTabScript.show_or_hide_for_menu_restriction_based_on_userrole() ;
        } ,
        show_or_hide_for_menu_restriction_based_on_userrole : function () {
            if ( jQuery( '#rs_menu_restriction_based_on_user_role' ).is( ':checked' ) == true ) {
                jQuery( '.rewardpoints_userrole_menu_restriction' ).parent().parent().show() ;
            } else {
                jQuery( '.rewardpoints_userrole_menu_restriction' ).parent().parent().hide() ;
            }
        } ,
        pagination_for_total_earned_points : function () {
            AdvanceTabScript.show_or_hide_for_pagination_for_total_earned_points() ;
        } ,
        show_or_hide_for_pagination_for_total_earned_points : function () {
            if ( jQuery( '#rs_select_pagination_for_total_earned_points' ).val() == '1' ) {
                jQuery( '#rs_value_without_pagination_for_total_earned_points' ).closest( 'tr' ).hide() ;
            } else {
                jQuery( '#rs_value_without_pagination_for_total_earned_points' ).closest( 'tr' ).show() ;
            }
        } ,
        pagination_for_total_available_points : function () {
            AdvanceTabScript.show_or_hide_for_pagination_for_total_available_points() ;
        } ,
        show_or_hide_for_pagination_for_total_available_points : function () {
            if ( jQuery( '#rs_select_pagination_for_available_points' ).val() == '1' ) {
                jQuery( '#rs_value_without_pagination_for_available_points' ).closest( 'tr' ).hide() ;
            } else {
                jQuery( '#rs_value_without_pagination_for_available_points' ).closest( 'tr' ).show() ;
            }
        } ,
        enable_msg_to_participate_in_reward_prgm : function () {
            AdvanceTabScript.show_or_hide_for_enable_msg_to_participate_in_reward_prgm() ;
        } ,
        show_or_hide_for_enable_msg_to_participate_in_reward_prgm : function () {
            if ( jQuery( '#rs_enable_reward_program' ).is( ':checked' ) == true ) {
                jQuery( '#rs_msg_in_reg_page' ).closest( 'tr' ).show() ;
                jQuery( '#rs_msg_in_acc_page_when_checked' ).closest( 'tr' ).show() ;
                jQuery( '#rs_msg_in_acc_page_when_unchecked' ).closest( 'tr' ).show() ;
                jQuery('#rs_alert_msg_in_acc_page_when_checked').closest( 'tr' ).show() ;
                jQuery('#rs_alert_msg_in_acc_page_when_unchecked').closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_msg_in_reg_page' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_msg_in_acc_page_when_checked' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_msg_in_acc_page_when_unchecked' ).closest( 'tr' ).hide() ;
                jQuery('#rs_alert_msg_in_acc_page_when_checked').closest( 'tr' ).hide() ;
                jQuery('#rs_alert_msg_in_acc_page_when_unchecked').closest( 'tr' ).hide() ;
            }
        } ,
        show_or_hide_for_coupon_restriction : function () {
            if ( jQuery( '#_rs_enable_coupon_restriction' ).is( ':checked' ) == true ) {
                jQuery( '#_rs_restrict_coupon' ).closest( 'tr' ).show() ;
                if ( jQuery( '#_rs_restrict_coupon' ).val() == '2' ) {
                    jQuery( '#rs_delete_coupon_by_cron_time' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_delete_coupon_specific_time' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_delete_coupon_by_cron_time' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_delete_coupon_specific_time' ).closest( 'tr' ).hide() ;
                }
                jQuery( '#_rs_restrict_coupon' ).change( function () {
                    if ( jQuery( '#_rs_restrict_coupon' ).val() == '2' ) {
                        jQuery( '#rs_delete_coupon_by_cron_time' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_delete_coupon_specific_time' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_delete_coupon_by_cron_time' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_delete_coupon_specific_time' ).closest( 'tr' ).hide() ;
                    }
                } ) ;
            } else {
                jQuery( '#_rs_restrict_coupon' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_delete_coupon_by_cron_time' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_delete_coupon_specific_time' ).closest( 'tr' ).hide() ;
            }
        } ,
        apply_points_for_previous_order : function () {
            var rsconfirm = confirm( "It is strongly recommended that you do not reload or refresh page. Are you sure you wish to update now?" ) ;
            if ( rsconfirm === true ) {
                var fromdate = jQuery( '#rs_from_date' ).val() ;
                var todate = jQuery( '#rs_to_date' ).val() ;
                var previous_order_points_for = jQuery( '#rs_award_previous_order_points' ).val() ;
                var award_points_on = jQuery( '#rs_sumo_select_order_range' ).val() ;
                var dataparam = ( {
                    action : 'applypointsforpreviousorder' ,
                    fromdate : fromdate ,
                    todate : todate ,
                    awardpointson : award_points_on ,
                    previousorderpointsfor : previous_order_points_for ,
                    sumo_security : fp_advance_params.fp_apply_points
                } ) ;
                $.post( fp_advance_params.ajaxurl , dataparam , function ( response ) {
                    if ( true === response.success ) {
                        console.log( 'Ajax Done Successfully' ) ;
                        window.location.href = fp_advance_params.redirecturl ;
                    } else {
                        window.alert( response.data.error ) ;
                    }
                } ) ;
            }
            return false ;
        } ,
        add_old_points_for_user : function () {
            var rsconfirm = confirm( "It is strongly recommended that you do not reload or refresh page. Are you sure you wish to Add Existing Points for User(s)?" ) ;
            if ( rsconfirm === true ) {
                var dataparam = ( {
                    action : 'addoldpoints' ,
                    sumo_security : fp_advance_params.fp_old_points
                } ) ;
                $.post( fp_advance_params.ajaxurl , dataparam , function ( response ) {
                    if ( true === response.success ) {
                        console.log( 'Ajax Done Successfully' ) ;
                        window.location.href = fp_advance_params.redirecturl ;
                    } else {
                        window.alert( response.data.error ) ;
                    }
                } ) ;
            }
            return false ;
        }
    } ;
    AdvanceTabScript.init() ;
} ) ;