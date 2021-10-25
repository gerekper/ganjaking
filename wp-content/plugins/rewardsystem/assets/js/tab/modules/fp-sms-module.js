/*
 * SMS - Module
 */
jQuery( function ( $ ) {
    var SMSModule = {
        init : function () {
            this.show_or_hide_for_api_option() ;
            this.show_or_hide_for_earning_msg() ;
            this.show_or_hide_for_redeeming_msg() ;
            this.show_or_hide_for_phone_number_field() ;
            this.show_or_hide_earning_msg_for_actions() ;
            $( document ).on( 'change' , '#rs_sms_sending_api_option' , this.api_option ) ;
            $( document ).on( 'change' , '#rs_send_sms_earning_points' , this.earning_msg ) ;
            $( document ).on( 'change' , '#rs_send_sms_redeeming_points' , this.redeeming_msg ) ;
            $( document ).on( 'change' , '#rs_ph_no_field_registration_page' , this.phone_number_field ) ;
            $( document ).on( 'change' , '#rs_send_sms_earning_points_for_actions' , this.earning_msg_for_actions ) ;
        } ,
        phone_number_field : function () {
            SMSModule.show_or_hide_for_phone_number_field() ;
        } ,
        show_or_hide_for_phone_number_field : function () {
            if ( jQuery( '#rs_ph_no_field_registration_page' ).is( ':checked' ) == true ) {
                jQuery( '#rs_ph_no_field_label_registration' ).closest( 'tr' ).show() ;
                jQuery( '#rs_ph_no_validationerror_emptyfield' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_ph_no_field_label_registration' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_ph_no_validationerror_emptyfield' ).closest( 'tr' ).hide() ;
            }
        } ,
        earning_msg_for_actions : function () {
            SMSModule.show_or_hide_earning_msg_for_actions() ;
        } ,
        show_or_hide_earning_msg_for_actions : function () {
            if ( jQuery( '#rs_send_sms_earning_points_for_actions' ).is( ':checked' ) == true ) {
                jQuery( '#rs_send_sms_earning_points_content_for_actions' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_send_sms_earning_points_content_for_actions' ).closest( 'tr' ).hide() ;
            }
        } ,
        api_option : function () {
            SMSModule.show_or_hide_for_api_option() ;
        } ,
        show_or_hide_for_api_option : function () {
            if ( ( jQuery( '#rs_sms_sending_api_option' ).val() ) === '1' ) {
                jQuery( '#rs_nexmo_key' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_nexmo_secret' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_twilio_secret_account_id' ).closest( 'tr' ).show() ;
                jQuery( '#rs_twilio_auth_token_id' ).closest( 'tr' ).show() ;
                jQuery( '#rs_twilio_from_number' ).closest( 'tr' ).show() ;
            } else if ( ( jQuery( '#rs_sms_sending_api_option' ).val() ) === '2' ) {
                jQuery( '#rs_nexmo_key' ).closest( 'tr' ).show() ;
                jQuery( '#rs_nexmo_secret' ).closest( 'tr' ).show() ;
                jQuery( '#rs_twilio_secret_account_id' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_twilio_auth_token_id' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_twilio_from_number' ).closest( 'tr' ).hide() ;
            } else {
                jQuery( '#rs_nexmo_key' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_nexmo_secret' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_twilio_secret_account_id' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_twilio_auth_token_id' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_twilio_from_number' ).closest( 'tr' ).hide() ;
            }
        } ,
        earning_msg : function () {
            SMSModule.show_or_hide_for_earning_msg() ;
        } ,
        show_or_hide_for_earning_msg : function () {
            if ( jQuery( '#rs_send_sms_earning_points' ).is( ':checked' ) == true ) {
                jQuery( '#rs_points_sms_content_for_earning' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_points_sms_content_for_earning' ).closest( 'tr' ).hide() ;
            }
        } ,
        redeeming_msg : function () {
            SMSModule.show_or_hide_for_redeeming_msg() ;
        } ,
        show_or_hide_for_redeeming_msg : function () {
            if ( jQuery( '#rs_send_sms_redeeming_points' ).is( ':checked' ) == true ) {
                jQuery( '#rs_points_sms_content_for_redeeming' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_points_sms_content_for_redeeming' ).closest( 'tr' ).hide() ;
            }
        } ,
    } ;
    SMSModule.init() ;
} ) ;