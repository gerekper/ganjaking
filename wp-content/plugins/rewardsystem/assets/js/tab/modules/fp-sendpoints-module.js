/*
 * Send Points - Module
 */
jQuery( function ( $ ) {
    var SendPointsScripts = {
        init : function ( ) {
            this.show_or_hide_for_confirmation_mail( ) ;
            this.show_or_hide_for_user_mail( ) ;
            this.show_or_hide_for_admin_mail( ) ;
            this.toggle_user_selection_field() ;
            $( document ).on( 'change' , '#rs_mail_for_send_points_confirmation_mail_for_user' , this.show_or_hide_for_confirmation_mail ) ;
            $( document ).on( 'change' , '#rs_mail_for_send_points_for_user' , this.show_or_hide_for_user_mail ) ;
            $( document ).on( 'change' , '#rs_mail_for_send_points_notification_admin' , this.show_or_hide_for_admin_mail ) ;
            $( document ).on( 'change' , '#rs_send_points_user_selection_field' , this.toggle_user_selection_field ) ;
        } ,
        show_or_hide_for_confirmation_mail : function ( ) {
            if ( jQuery( '#rs_mail_for_send_points_confirmation_mail_for_user' ).is( ':checked' ) == true ) {
                jQuery( '#rs_email_subject_for_send_points_confirmation' ).closest( 'tr' ).show( ) ;
                jQuery( '#rs_email_message_for_send_points_confirmation' ).closest( 'tr' ).show( ) ;
            } else {
                jQuery( '#rs_email_subject_for_send_points_confirmation' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_email_message_for_send_points_confirmation' ).closest( 'tr' ).hide( ) ;
            }
        } ,
        show_or_hide_for_user_mail : function ( ) {
            if ( jQuery( '#rs_mail_for_send_points_for_user' ).is( ':checked' ) == true ) {
                jQuery( '#rs_email_subject_for_send_points' ).closest( 'tr' ).show( ) ;
                jQuery( '#rs_email_message_for_send_points' ).closest( 'tr' ).show( ) ;
            } else {
                jQuery( '#rs_email_subject_for_send_points' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_email_message_for_send_points' ).closest( 'tr' ).hide( ) ;
            }
        } ,
        show_or_hide_for_admin_mail : function ( ) {
            if ( jQuery( '#rs_mail_for_send_points_notification_admin' ).is( ':checked' ) == true ) {
                jQuery( '#rs_email_subject_for_send_points_notification_admin' ).closest( 'tr' ).show( ) ;
                jQuery( '#rs_email_message_for_send_points_notification_admin' ).closest( 'tr' ).show( ) ;
                jQuery( "input[type='radio'][name='rs_mail_sender_for_admin']" ).closest( 'tr' ).show( )
                if ( jQuery( 'input[name=rs_mail_sender_for_admin]:checked' ).val( ) == 'local' ) {
                    jQuery( '#rs_from_name_for_sendpoints_for_admin' ).closest( 'tr' ).show( ) ;
                    jQuery( '#rs_from_email_for_sendpoints_for_admin' ).closest( 'tr' ).show( ) ;
                } else {
                    jQuery( '#rs_from_name_for_sendpoints_for_admin' ).closest( 'tr' ).hide( ) ;
                    jQuery( '#rs_from_email_for_sendpoints_for_admin' ).closest( 'tr' ).hide( ) ;
                }
                jQuery( 'input[name=rs_mail_sender_for_admin]:radio' ).click( function ( ) {
                    if ( jQuery( 'input[name=rs_mail_sender_for_admin]:checked' ).val( ) == 'local' ) {
                        jQuery( '#rs_from_name_for_sendpoints_for_admin' ).closest( 'tr' ).show( ) ;
                        jQuery( '#rs_from_email_for_sendpoints_for_admin' ).closest( 'tr' ).show( ) ;
                    } else {
                        jQuery( '#rs_from_name_for_sendpoints_for_admin' ).closest( 'tr' ).hide( ) ;
                        jQuery( '#rs_from_email_for_sendpoints_for_admin' ).closest( 'tr' ).hide( ) ;
                    }
                } ) ;
            } else {
                jQuery( '#rs_email_subject_for_send_points_notification_admin' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_email_message_for_send_points_notification_admin' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_from_name_for_sendpoints_for_admin' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_from_email_for_sendpoints_for_admin' ).closest( 'tr' ).hide( ) ;
                jQuery( "input[type='radio'][name='rs_mail_sender_for_admin']" ).closest( 'tr' ).hide( )
            }
        } ,
        toggle_user_selection_field : function ( ) {
            if ( '1' == jQuery( '#rs_send_points_user_selection_field' ).val() ) {
                jQuery( '#rs_select_user_label' ).closest( 'tr' ).show( ) ;
                jQuery( '#rs_select_user_placeholder' ).closest( 'tr' ).show( ) ;
                jQuery( '#rs_send_points_username_field_label' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_send_points_username_placeholder' ).closest( 'tr' ).hide( ) ;
            } else {
                jQuery( '#rs_select_user_label' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_select_user_placeholder' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_send_points_username_field_label' ).closest( 'tr' ).show( ) ;
                jQuery( '#rs_send_points_username_placeholder' ).closest( 'tr' ).show( ) ;
            }
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
            $( id ).unblock( ) ;
        } ,
    } ;
    SendPointsScripts.init() ;
} ) ;