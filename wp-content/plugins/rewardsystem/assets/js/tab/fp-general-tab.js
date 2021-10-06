/*
 * General tab
 */
jQuery( function ( $ ) {
    'use strict' ;
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
            this.toggle_admin_email_for_free_product() ;
            this.toggle_admin_email_for_bouns_points( ) ;
            this.toggle_user_email_for_bouns_points( ) ; 
            $( document ).on( 'change' , '#rs_round_off_type' , this.roundoff_type ) ;
            $( document ).on( 'change' , '#rs_enable_disable_max_earning_points_for_user' , this.maximum_earn_points_for_user ) ;
            $( document ).on( 'change' , '#rs_enable_user_role_based_reward_points' , this.enable_user_role ) ;
            $( document ).on( 'change' , '#rs_enable_membership_plan_based_reward_points' , this.enable_membership_plan ) ;
            $( document ).on( 'change' , '#rs_enable_earned_level_based_reward_points' , this.enable_earning_level ) ;
            $( document ).on( 'change' , '#rs_enable_user_purchase_history_based_reward_points' , this.enable_user_purchase_history ) ;
            $( document ).on( 'change' , '#rs_enable_banning_users_earning_points' , this.ban_user_for_earning ) ;
            $( document ).on( 'change' , '#rs_enable_banning_users_redeeming_points' , this.ban_user_for_redeeming ) ;
            $( document ).on( 'click' , '.rs_refresh_button' , this.refresh_expired_points ) ;
            $( document ).on( 'change' , '#rs_enable_admin_email_for_free_product' , this.toggle_admin_email_for_free_product ) ;
            $( document ).on( 'change' , '#rs_enable_admin_email_for_bonus_points' , this.toggle_admin_email_for_bouns_points ) ;
            $( document ).on( 'change' , '#rs_enable_user_email_for_bonus_points' , this.toggle_user_email_for_bouns_points ) ; 
            // User Purchase History Rule.
            $( document ).on( 'click' , '.rs-add-new-purchase-history-rule' , this.add_rule_for_purchase_history ) ;
            $( document ).on( 'click' , '.rs-remove-purchase-history-rule' , this.remove_rule_for_purchase_history ) ;

            // Earning Percentage Rule.
            $( document ).on( 'click' , '.rs-add-earning-percentage-rule' , this.add_rule_for_earning_percentage ) ;
            $( document ).on( 'click' , '.rs-remove-earning-percentage-rule' , this.remove_rule_for_earning_percentage ) ;
            $( document ).on( 'change' , '.rs-member-level-earning-type' , this.show_or_hide_member_level_earning_type ) ;
            
            $( '.rs-member-level-earning-type' ).change();
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
                jQuery( '#rs_maximum_threshold_error_message' ).closest( 'tr' ).hide() ;
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
                jQuery( '#rs_maximum_threshold_error_message' ).closest( 'tr' ).show() ;
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
                $( '#rs_enable_admin_email_for_free_product' ).closest( 'tr' ).show( ) ;
                $( '#rs_enable_admin_email_for_bonus_points' ).closest( 'tr' ).show( ) ;
                $( '#rs_enable_user_email_for_bonus_points' ).closest( 'tr' ).show( ) ;
                GeneralTabScripts.toggle_admin_email_for_free_product( ) ;
                GeneralTabScripts.toggle_admin_email_for_bouns_points( ) ;
                GeneralTabScripts.toggle_user_email_for_bouns_points( ) ;
            } else {
                jQuery( '.rsdynamicrulecreation' ).parent( ).hide( ) ;
                jQuery( '#rs_select_earn_points_based_on' ).parent( ).parent( ).hide( ) ;
                jQuery( '#rs_free_product_range' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_free_product_add_by_user_or_admin' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_subject_for_free_product_mail' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_content_for_free_product_mail' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_order_status_control_to_automatic_order' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_free_product_add_quantity' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_free_product_quantity' ).parent( ).parent( ).hide( ) ;
                $( '#rs_enable_admin_email_for_free_product' ).closest( 'tr' ).hide( ) ;
                $( '#rs_subject_for_free_product_mail_send_admin' ).closest( 'tr' ).hide( ) ;
                $( '#rs_content_for_free_product_mail_send_admin' ).closest( 'tr' ).hide( ) ;
                $( '#rs_enable_admin_email_for_bonus_points' ).closest( 'tr' ).hide( ) ;
                $( '#rs_subject_for_bonus_points_admin_email' ).closest( 'tr' ).hide( ) ;
                $( '#rs_message_for_bonus_points_admin_email' ).closest( 'tr' ).hide( ) ;
                $( '#rs_enable_user_email_for_bonus_points' ).closest( 'tr' ).hide( ) ;
                $( '#rs_subject_for_bonus_points_customer_email' ).closest( 'tr' ).hide( ) ;
                $( '#rs_message_for_bonus_points_customer_email' ).closest( 'tr' ).hide( ) ;
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
        show_or_hide_member_level_earning_type : function( event ) {
            event.preventDefault( ) ;
            var $this = $( event.currentTarget ) ;           
            GeneralTabScripts.member_level_earning_type( $this ) ;
        } ,
        member_level_earning_type : function( $this ) {         
            if( '1' == $this.val() ) {
                $( $this ).closest( 'tr' ).find( '.rs-free-product' ).closest( '.rs-free-product-data' ).show() ;
                $( $this ).closest( 'tr' ).find( '.rs-bonus-points' ).closest( '.rs-bouns-point-data' ).hide() ;
            } else {
                $( $this ).closest( 'tr' ).find( '.rs-bonus-points' ).closest( '.rs-bouns-point-data' ).show() ;
                $( $this ).closest( 'tr' ).find( '.rs-free-product' ).closest( '.rs-free-product-data' ).hide() ;
            }
        } ,
        refresh_expired_points : function( ) {
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
        } ,
        toggle_admin_email_for_free_product : function ( ) {
            if ( $( '#rs_enable_admin_email_for_free_product' ).is( ':checked' ) ) {
                $( '#rs_subject_for_free_product_mail_send_admin' ).closest( 'tr' ).show() ;
                $( '#rs_content_for_free_product_mail_send_admin' ).closest( 'tr' ).show() ;
            } else {
                $( '#rs_subject_for_free_product_mail_send_admin' ).closest( 'tr' ).hide() ;
                $( '#rs_content_for_free_product_mail_send_admin' ).closest( 'tr' ).hide() ;
            }
        } ,
        toggle_admin_email_for_bouns_points : function( ) {
            if( $( '#rs_enable_admin_email_for_bonus_points' ).is( ':checked' ) ) {
                $( '#rs_subject_for_bonus_points_admin_email' ).closest( 'tr' ).show( ) ;
                $( '#rs_message_for_bonus_points_admin_email' ).closest( 'tr' ).show( ) ;
            } else {
                $( '#rs_subject_for_bonus_points_admin_email' ).closest( 'tr' ).hide( ) ;
                $( '#rs_message_for_bonus_points_admin_email' ).closest( 'tr' ).hide( ) ;
            }
        } ,
        toggle_user_email_for_bouns_points : function( ) {
            if( $( '#rs_enable_user_email_for_bonus_points' ).is( ':checked' ) ) {
                $( '#rs_subject_for_bonus_points_customer_email' ).closest( 'tr' ).show( ) ;
                $( '#rs_message_for_bonus_points_customer_email' ).closest( 'tr' ).show( ) ;
            } else {
                $( '#rs_subject_for_bonus_points_customer_email' ).closest( 'tr' ).hide( ) ;
                $( '#rs_message_for_bonus_points_customer_email' ).closest( 'tr' ).hide( ) ;
            }
        } ,             
        add_rule_for_purchase_history : function( event ) {
            event.preventDefault( ) ;
            var $this = $( event.currentTarget ) ;
            var random_value = Math.round( new Date( ).getTime( ) + ( Math.random( ) * 100 ) ) ;
            var data = {
                action : 'add_user_purchase_history_rule' ,
                random_value : random_value ,
                sumo_security : fp_general_tab_params.add_user_purchase_history_nonce
            } ;
            $.post( fp_general_tab_params.ajaxurl , data , function( response ) {
                if( true == response.success && response.data.html ) {
                    $( $this ).closest( '.rs-user-purchase-history-rules' ).find( 'tbody' ).append( response.data.html ) ;
                } else {
                    alert( response.data.error ) ;
                }
            } ) ;
        } ,
        remove_rule_for_purchase_history : function( event ) {
            event.preventDefault() ;
            $( event.currentTarget ).closest( "tr" ).remove() ;
        } ,
        add_rule_for_earning_percentage : function( event ) {
            event.preventDefault( ) ;
            var $this = $( event.currentTarget ) ;
            var random_value = Math.round( new Date( ).getTime( ) + ( Math.random( ) * 100 ) ) ;
            var data = {
                action : 'add_earning_percentage_rule' ,
                random_value : random_value ,
                sumo_security : fp_general_tab_params.add_earning_percentage_nonce
            } ;
            $.post( fp_general_tab_params.ajaxurl , data , function( response ) {
                if( true == response.success && response.data.html ) {
                    $( $this ).closest( '.rs-earning-percentage-rule' ).find( 'tbody' ).append( response.data.html ) ;
                    $( $this ).closest( 'table.rs-earning-percentage-rule' ).find( 'tbody tr:last div.rs-bouns-point-data' ).hide() ;
                    $( 'body' ).trigger( 'wc-enhanced-select-init' ) ;
                    $( '#rs_enable_user_role_based_reward_points' ).addClass( 'rs_enable_user_role_based_reward_points' ) ;
                    $( '#rs_enable_earned_level_based_reward_points' ).addClass( 'rs_enable_user_role_based_reward_points' ) ;
                } else {
                    alert( response.data.error ) ;
                }
            } ) ;
        } ,
        remove_rule_for_earning_percentage : function( event ) {
            event.preventDefault() ;
            $( event.currentTarget ).closest( "tr" ).remove() ;
        } ,
    } ;
    GeneralTabScripts.init() ;

} ) ;