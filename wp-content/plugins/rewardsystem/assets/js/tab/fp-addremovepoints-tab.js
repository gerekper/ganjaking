/*
 * Add/Remove Reward Points Tab
 */
jQuery( function ( $ ) {
    var AddRemovePointsTabScript = {
        init : function () {
            this.trigger_on_page_load() ;
            this.show_or_hide_for_reward_type_selection() ;
            this.show_or_hide_for_user_type_selection() ;
            $( '#rs_expired_date' ).datepicker( { dateFormat : 'yy-mm-dd' } ) ;
            $( '.gif_rs_sumo_reward_button_for_remove' ).css( 'display' , 'none' ) ;
            jQuery( '.gif_rs_sumo_reward_button_for_add' ).css( 'display' , 'none' ) ;
            $( document ).on( 'change' , '#rs_select_user_type' , this.user_type_selection ) ;
            $( document ).on( 'change' , '#rs_reward_select_type' , this.reward_type_selection ) ;
            $( document ).on( 'click' , '#rs_remove_points' , this.remove_points_from_user ) ;
            $( document ).on( 'click' , '#rs_add_points' , this.add_points_to_user ) ;
        } ,
        trigger_on_page_load : function () {
            if ( fp_addremovepoints_tab_params.fp_wc_version <= parseFloat( '2.2.0' ) ) {
                $( '#rs_select_to_include_customers_role' ).chosen() ;
                $( '#rs_select_to_exclude_customers_role' ).chosen() ;
            } else {
                $( '#rs_select_to_include_customers_role' ).select2() ;
                $( '#rs_select_to_exclude_customers_role' ).select2() ;
            }
        } ,
        user_type_selection : function () {
            AddRemovePointsTabScript.show_or_hide_for_user_type_selection() ;
        } ,
        show_or_hide_for_user_type_selection : function () {
            if ( jQuery( '#rs_select_user_type' ).val() == '1' ) {
                jQuery( '#rs_select_to_include_customers' ).parent().parent().hide() ;
                jQuery( '#rs_select_to_exclude_customers' ).parent().parent().hide() ;
                jQuery( '#rs_select_to_include_customers_role' ).parent().parent().hide() ;
                jQuery( '#rs_select_to_exclude_customers_role' ).parent().parent().hide() ;
            } else if ( jQuery( '#rs_select_user_type' ).val() == '2' ) {
                jQuery( '#rs_select_to_include_customers' ).parent().parent().show() ;
                jQuery( '#rs_select_to_exclude_customers' ).parent().parent().hide() ;
                jQuery( '#rs_select_to_include_customers_role' ).parent().parent().hide() ;
                jQuery( '#rs_select_to_exclude_customers_role' ).parent().parent().hide() ;
            } else if ( jQuery( '#rs_select_user_type' ).val() == '3' ) {
                jQuery( '#rs_select_to_include_customers' ).parent().parent().hide() ;
                jQuery( '#rs_select_to_exclude_customers' ).parent().parent().show() ;
                jQuery( '#rs_select_to_include_customers_role' ).parent().parent().hide() ;
                jQuery( '#rs_select_to_exclude_customers_role' ).parent().parent().hide() ;
            } else if ( jQuery( '#rs_select_user_type' ).val() == '4' ) {
                jQuery( '#rs_select_to_include_customers' ).parent().parent().hide() ;
                jQuery( '#rs_select_to_exclude_customers' ).parent().parent().hide() ;
                jQuery( '#rs_select_to_include_customers_role' ).parent().parent().show() ;
                jQuery( '#rs_select_to_exclude_customers_role' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_select_to_include_customers' ).parent().parent().hide() ;
                jQuery( '#rs_select_to_exclude_customers' ).parent().parent().hide() ;
                jQuery( '#rs_select_to_include_customers_role' ).parent().parent().hide() ;
                jQuery( '#rs_select_to_exclude_customers_role' ).parent().parent().show() ;
            }
        } ,
        reward_type_selection : function () {
            AddRemovePointsTabScript.show_or_hide_for_reward_type_selection() ;
        } ,
        show_or_hide_for_reward_type_selection : function () {
            if ( jQuery( '#rs_reward_select_type' ).val() == '1' ) {
                jQuery( '#rs_remove_points' ).hide() ;
                jQuery( '#rs_add_points' ).show() ;
                jQuery( '#rs_expired_date' ).parent().parent().show() ;
                jQuery( '#send_mail_add_remove_settings' ).closest( 'tr' ).show() ;
                jQuery( '#send_mail_settings' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_subject_for_remove' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_message_for_remove' ).closest( 'tr' ).hide() ;
                if ( jQuery( '#send_mail_add_remove_settings' ).is( ':checked' ) ) {
                    jQuery( '#rs_email_subject_message' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_email_message' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_email_subject_message' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_email_message' ).closest( 'tr' ).hide() ;
                }
                jQuery( '#send_mail_add_remove_settings' ).click( function () {
                    if ( jQuery( '#send_mail_add_remove_settings' ).is( ':checked' ) ) {
                        jQuery( '#rs_email_subject_message' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_email_message' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_email_subject_message' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_email_message' ).closest( 'tr' ).hide() ;
                    }
                } ) ;
            } else {
                jQuery( '#rs_remove_points' ).show() ;
                jQuery( '#rs_add_points' ).hide() ;
                jQuery( '#rs_expired_date' ).parent().parent().hide() ;
                jQuery( '#send_mail_add_remove_settings' ).closest( 'tr' ).hide() ;
                jQuery( '#send_mail_settings' ).closest( 'tr' ).show() ;
                jQuery( '#rs_email_subject_message' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_message' ).closest( 'tr' ).hide() ;
                if ( jQuery( '#send_mail_settings' ).is( ':checked' ) ) {
                    jQuery( '#rs_email_subject_for_remove' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_email_message_for_remove' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_email_subject_for_remove' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_email_message_for_remove' ).closest( 'tr' ).hide() ;
                }
                jQuery( '#send_mail_settings' ).click( function () {
                    if ( jQuery( '#send_mail_settings' ).is( ':checked' ) ) {
                        jQuery( '#rs_email_subject_for_remove' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_email_message_for_remove' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_email_subject_for_remove' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_email_message_for_remove' ).closest( 'tr' ).hide() ;
                    }
                } ) ;
            }
        } ,
        validation_for_add_or_remove_points : function ( actionname ) {
            AddRemovePointsTabScript.block( '.form-table' ) ;
            var enteredpoints = jQuery( '#rs_reward_addremove_points' ).val() ;
            var reason = jQuery( '#rs_reward_addremove_reason' ).val() ;
            var usertype = jQuery( '#rs_select_user_type' ).val() ;
            var includeuser = jQuery( '#rs_select_to_include_customers' ).val() ;
            var excludeuser = jQuery( '#rs_select_to_exclude_customers' ).val() ;
            var includeuserrole = jQuery( '#rs_select_to_include_customers_role' ).val() ;
            var excludeuserrole = jQuery( '#rs_select_to_exclude_customers_role' ).val() ;
            var sendmail_to_add_points = jQuery( '#send_mail_add_remove_settings' ).is( ':checked' ) ;
            var sendmail_to_remove_points = jQuery( '#send_mail_settings' ).is( ':checked' ) ;
            var expireddate = jQuery( '#rs_expired_date' ).val() ;
            var email_subject_to_add_points = jQuery( '#rs_email_subject_message' ).val() ;
            var email_message_to_add_points = jQuery( '#rs_email_message' ).val() ;
            var email_subject_to_remove_points = jQuery( '#rs_email_subject_for_remove' ).val() ;
            var email_message_to_remove_points = jQuery( '#rs_email_message_for_remove' ).val() ;
            var points_error = '<div class="rs_add_remove_points_error" style="color: red;font-size:14px;"></div>' ;
            var reason_error = '<div class="rs_add_remove_points_reason_error" style="color: red;font-size:14px;"></div>' ;
            var expiry_date_error = '<div class="rs_add_remove_points_expirydate_error" style="color: red;font-size:14px;"></div>' ;
            if ( enteredpoints == '' && reason == '' ) {
                jQuery( '#rs_reward_addremove_points' ).closest( 'td' ).append( points_error ) ;
                jQuery( '.rs_add_remove_points_error' ).fadeIn() ;
                jQuery( '.rs_add_remove_points_error' ).html( fp_addremovepoints_tab_params.pointerrormsg ) ;
                jQuery( '.rs_add_remove_points_error' ).fadeOut( 5000 , function () {
                    $( this ).remove() ;
                } ) ;
                jQuery( '#rs_reward_addremove_reason' ).closest( 'td' ).append( reason_error ) ;
                jQuery( '.rs_add_remove_points_reason_error' ).fadeIn() ;
                jQuery( '.rs_add_remove_points_reason_error' ).html( fp_addremovepoints_tab_params.reasomerrormsg ) ;
                jQuery( '.rs_add_remove_points_reason_error' ).fadeOut( 5000 , function () {
                    $( this ).remove() ;
                } ) ;
                AddRemovePointsTabScript.unblock( '.form-table' ) ;
                return false ;
            } else if ( enteredpoints == '' ) {
                jQuery( '#rs_reward_addremove_points' ).closest( 'td' ).append( points_error ) ;
                jQuery( '.rs_add_remove_points_error' ).fadeIn() ;
                jQuery( '.rs_add_remove_points_error' ).html( fp_addremovepoints_tab_params.pointerrormsg ) ;
                jQuery( '.rs_add_remove_points_error' ).fadeOut( 5000 , function () {
                    $( this ).remove() ;
                } ) ;
                AddRemovePointsTabScript.unblock( '.form-table' ) ;
                return false ;
            } else if ( reason == '' ) {
                jQuery( '#rs_reward_addremove_reason' ).closest( 'td' ).append( reason_error ) ;
                jQuery( '.rs_add_remove_points_reason_error' ).fadeIn() ;
                jQuery( '.rs_add_remove_points_reason_error' ).html( fp_addremovepoints_tab_params.reasomerrormsg ) ;
                jQuery( '.rs_add_remove_points_reason_error' ).fadeOut( 5000 , function () {
                    $( this ).remove() ;
                } ) ;
                AddRemovePointsTabScript.unblock( '.form-table' ) ;
                return false ;
            } else if ( '' != expireddate ) {
                var expired_time = new Date( expireddate ).getTime() ;
                var current_time = new Date( fp_addremovepoints_tab_params.current_date ).getTime() ;
                if ( parseInt( expired_time ) < parseInt( current_time ) ) {
                    jQuery( '#rs_expired_date' ).closest( 'td' ).append( expiry_date_error ) ;
                    jQuery( '.rs_add_remove_points_expirydate_error' ).fadeIn() ;
                    jQuery( '.rs_add_remove_points_expirydate_error' ).html( fp_addremovepoints_tab_params.expirydateerrormsg ) ;
                    jQuery( '.rs_add_remove_points_expirydate_error' ).fadeOut( 5000 , function () {
                        $( this ).remove() ;
                    } ) ;
                    AddRemovePointsTabScript.unblock( '.form-table' ) ;
                    return false ;
                }
            }

            AddRemovePointsTabScript.unblock( '.form-table' ) ;
            if ( actionname == 'rsremovepointforuser' ) {
                var rsconfirm = confirm( "It is strongly recommended that you do not reload or refresh page. Are you sure you wish to Remove Points for User(s)?" ) ;
                var sumo_security = fp_addremovepoints_tab_params.fp_remove_points ;
            } else {
                var rsconfirm = confirm( "It is strongly recommended that you do not reload or refresh page. Are you sure you wish to Add Points for User(s)?" ) ;
                var sumo_security = fp_addremovepoints_tab_params.fp_add_points ;
            }
            if ( rsconfirm === true ) {
                var data = ( {
                    action : actionname ,
                    usertype : usertype ,
                    includeuser : includeuser ,
                    excludeuser : excludeuser ,
                    includeuserrole : includeuserrole ,
                    excludeuserrole : excludeuserrole ,
                    sendmail_to_add_points : sendmail_to_add_points ,
                    sendmail_to_remove_points : sendmail_to_remove_points ,
                    expireddate : expireddate ,
                    email_subject_to_add_points : email_subject_to_add_points ,
                    email_message_to_add_points : email_message_to_add_points ,
                    email_subject_to_remove_points : email_subject_to_remove_points ,
                    email_message_to_remove_points : email_message_to_remove_points ,
                    points : enteredpoints ,
                    reason : reason ,
                    sumo_security : sumo_security ,
                    state : fp_addremovepoints_tab_params.isadmin
                } ) ;
                $.post( fp_addremovepoints_tab_params.ajaxurl , data , function ( response ) {
                    if ( true === response.success ) {
                        window.location.href = fp_addremovepoints_tab_params.redirect ;
                    } else {
                        window.alert( response.data.error ) ;
                    }
                } ) ;
            }
            return false ;
        } ,
        remove_points_from_user : function () {
            AddRemovePointsTabScript.validation_for_add_or_remove_points( 'rsremovepointforuser' ) ;
        } ,
        add_points_to_user : function () {
            AddRemovePointsTabScript.validation_for_add_or_remove_points( 'rsaddpointforuser' ) ;
        } ,
        block : function ( id ) {
            $( id ).block( {
                message : null ,
                overlayCSS : {
                    background : '#fff' ,
                    opacity : 0.6
                }
            } ) ;
        } ,
        unblock : function ( id ) {
            $( id ).unblock() ;
        } ,
    } ;
    AddRemovePointsTabScript.init() ;
} ) ;