/*
 * Action Reward Points - Module
 */
jQuery( function ( $ ) {
    'use strict' ;
    var BirthdayModule = {
        init : function () {
            this.show_or_hide_for_enable_birthday() ;
            this.show_or_hide_for_email() ;
            $( document ).on( 'change' , '#rs_enable_bday_points' , this.enable_birthday ) ;
            $( document ).on( 'change' , '#rs_send_mail_for_bday_points' , this.enable_email ) ;
        } ,
        enable_birthday : function () {
            BirthdayModule.show_or_hide_for_enable_birthday() ;
        } ,
        show_or_hide_for_enable_birthday : function () {
            if ( jQuery( '#rs_enable_bday_points' ).is( ':checked' ) == true ) {
                jQuery( '#rs_bday_points' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_bday_points' ).closest( 'tr' ).hide() ;
            }
        } ,
        enable_email : function () {
            BirthdayModule.show_or_hide_for_email() ;
        } ,
        show_or_hide_for_email : function () {
            if ( jQuery( '#rs_send_mail_for_bday_points' ).is( ':checked' ) == true ) {
                jQuery( '#rs_email_subject_for_bday_points' ).closest( 'tr' ).show() ;
                jQuery( '#rs_email_message_for_bday_points' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_email_subject_for_bday_points' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_message_for_bday_points' ).closest( 'tr' ).hide() ;
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
            $( id ).unblock() ;
        } ,
    } ;
    BirthdayModule.init() ;
} ) ;