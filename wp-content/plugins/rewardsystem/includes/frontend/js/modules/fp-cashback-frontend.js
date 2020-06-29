/*
 * Cashback - Module
 */
jQuery( function ( $ ) {
    var RSCashbackFrontend = {
        init : function ( ) {
            this.show_or_hide_for_payment_method() ;
            this.hide_err_msgs() ;
            $( document ).on( 'change' , '#rs_encash_payment_method' , this.show_or_hide_for_payment_method ) ;
            $( document.body ).on( 'click' , '.cancelbutton' , this.cancel_cashback_request ) ;
            $( document.body ).on( 'click' , '#submit_cashback' , this.submit_cashback_request ) ;
        } ,
        hide_err_msgs : function () {
            $( "#points_empty_error" ).hide() ;
            $( "#points_number_error" ).hide() ;
            $( "#points_greater_than_earnpoints_error" ).hide() ;
            $( "#points_lesser_than_minpoints_error" ).hide() ;
            $( "#reason_empty_error" ).hide() ;
            $( "#paypal_email_empty_error" ).hide() ;
            $( "#paypal_email_format_error" ).hide() ;
            $( "#paypal_custom_option_empty_error" ).hide() ;
            $( "#recaptcha_empty_error" ).hide() ;
            $( '#encash_form_success_info' ).hide() ;
        } ,
        show_or_hide_for_payment_method : function () {
            if ( fp_cashback_action_params.paymentmethod == 3 ) {
                if ( $( '#rs_encash_payment_method' ).val() == 'encash_through_paypal_method' ) {
                    $( ".rs_encash_paypal_address" ).show() ;
                    $( '.rs_encash_custom_payment_option_value' ).hide() ;
                } else {
                    $( ".rs_encash_paypal_address" ).hide() ;
                    $( '.rs_encash_custom_payment_option_value' ).show() ;
                }
            } else if ( fp_cashback_action_params.paymentmethod == 2 ) {
                $( ".rs_encash_paypal_address" ).hide() ;
                $( '.rs_encash_custom_payment_option_value' ).show() ;
            } else if ( fp_cashback_action_params.paymentmethod == 1 ) {
                $( ".rs_encash_paypal_address" ).show() ;
                $( '.rs_encash_custom_payment_option_value' ).hide() ;
            }
        } ,
        cancel_cashback_request : function () {
            var status = jQuery( this ).attr( 'data-status' ) ;
            var id = jQuery( this ).attr( 'data-id' ) ;
            var data = {
                action : "cancelcashback" ,
                status : status ,
                id : id ,
                sumo_security : fp_cashback_action_params.fp_cancel_request
            } ;
            $.post( fp_cashback_action_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {
                    window.location.reload( ) ;
                } else {
                    window.alert( response.data.error ) ;
                }
            } ) ;
        } ,
        submit_cashback_request : function ( evt ) {
            evt.preventDefault() ;
            var available_points = fp_cashback_action_params.available_points ;
            var paymentmethod = fp_cashback_action_params.paymentmethod ;
            var pointsascash = jQuery( "#rs_encash_points_value" ).val() ;
            var validatepoints = /^[0-9\b]+$/.test( pointsascash ) ;
            var points_value = fp_cashback_action_params.conversionrate ;
            var amount_value = fp_cashback_action_params.conversionvalue ;
            var conversionrate = pointsascash / points_value ;
            var currency_converted_value = conversionrate * amount_value ;
            var enable_recaptcha = fp_cashback_action_params.enable_recaptcha ;
            if ( pointsascash == "" ) {
                jQuery( "#points_empty_error" ).fadeIn().delay( 5000 ).fadeOut() ;
                return false ;
            } else {
                jQuery( "#points_empty_error" ).hide() ;
                if ( validatepoints == false ) {
                    jQuery( "#points_number_error" ).fadeIn().delay( 5000 ).fadeOut() ;
                    return false ;
                } else {
                    jQuery( "#points_number_error" ).hide() ;
                    if ( Number( pointsascash ) > Number( available_points ) ) {
                        jQuery( "#points_greater_than_earnpoints_error" ).fadeIn().delay( 5000 ).fadeOut() ;
                        return false ;
                    } else {
                        if ( ( Number( pointsascash ) >= Number( fp_cashback_action_params.minpointstoreq ) ) && ( Number( pointsascash ) <= Number( fp_cashback_action_params.maxpointstoreq ) ) ) {
                            jQuery( "#points_greater_than_earnpoints_error" ).hide() ;
                            jQuery( "#points_lesser_than_minpoints_error" ).hide() ;
                            jQuery( "#rs_error_message_points_lesser_than_minimum_points" ).hide() ;
                            jQuery( "#points_greater_than_maxpoints_error" ).hide() ;
                        } else {
                            jQuery( "#points_lesser_than_minpoints_error" ).fadeIn().delay( 5000 ).fadeOut() ;
                            return false ;
                        }
                    }
                }
            }
            var reason_to_encash = jQuery( "#rs_encash_points_reason" ).val() ;
            if ( reason_to_encash == "" ) {
                jQuery( "#reason_empty_error" ).fadeIn().delay( 5000 ).fadeOut() ;
                return false ;
            } else {
                jQuery( "#reason_empty_error" ).hide() ;
            }

            if ( paymentmethod === '1' ) {
                jQuery( ".rs_encash_paypal_address" ).show() ;
                var selectedpaymentmethod = 'encash_through_paypal_method' ;
                var paypal_email = jQuery( "#rs_encash_paypal_address" ).val() ;
                if ( paypal_email == "" ) {
                    jQuery( "#paypal_email_empty_error" ).fadeIn().delay( 5000 ).fadeOut() ;
                    return false ;
                } else {
                    var validatepaypalemail = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test( paypal_email ) ;
                    jQuery( "#paypal_email_empty_error" ).hide() ;
                    if ( validatepaypalemail == false ) {
                        jQuery( "#paypal_email_format_error" ).fadeIn().delay( 5000 ).fadeOut() ;
                        return false ;
                    } else {
                        jQuery( "#paypal_email_format_error" ).hide() ;
                    }
                }
            } else if ( paymentmethod === '2' ) {
                jQuery( ".rs_encash_custom_payment_option_value" ).show() ;
                var selectedpaymentmethod = 'encash_through_custom_payment' ;
                var custom_payment_details = jQuery( "#rs_encash_custom_payment_option_value" ).val() ;
                if ( custom_payment_details == "" ) {
                    jQuery( "#paypal_custom_option_empty_error" ).fadeIn().delay( 5000 ).fadeOut() ;
                    return false ;
                } else {
                    jQuery( "#paypal_custom_option_empty_error" ).hide() ;
                }
            } else {
                var selectedpaymentmethod = jQuery( "#rs_encash_payment_method" ).val() ;
                if ( selectedpaymentmethod == 'encash_through_paypal_method' ) {
                    jQuery( ".rs_encash_paypal_address" ).show() ;
                    var paypal_email = jQuery( "#rs_encash_paypal_address" ).val() ;
                    if ( paypal_email == "" ) {
                        jQuery( "#paypal_email_empty_error" ).fadeIn().delay( 5000 ).fadeOut() ;
                        return false ;
                    } else {
                        var validatepaypalemail = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test( paypal_email ) ;
                        jQuery( "#paypal_email_empty_error" ).hide() ;
                        if ( validatepaypalemail == false ) {
                            jQuery( "#paypal_email_format_error" ).fadeIn().delay( 5000 ).fadeOut() ;
                            return false ;
                        } else {
                            jQuery( "#paypal_email_format_error" ).hide() ;
                        }
                    }
                } else if ( selectedpaymentmethod == 'encash_through_custom_payment' ) {
                    var custom_payment_details = jQuery( "#rs_encash_custom_payment_option_value" ).val() ;
                    jQuery( ".rs_encash_custom_payment_option_value" ).show() ;
                    if ( custom_payment_details == "" ) {
                        jQuery( "#paypal_custom_option_empty_error" ).fadeIn().delay( 5000 ).fadeOut() ;
                        return false ;
                    } else {
                        jQuery( "#paypal_custom_option_empty_error" ).hide() ;
                    }
                }
            }
            if ( enable_recaptcha == "yes" ) {
                if ( grecaptcha.getResponse() == "" ) {
                    jQuery( "#recaptcha_empty_error" ).fadeIn().delay( 5000 ).fadeOut() ;
                    return false ;
                } else {
                    jQuery( "#recaptcha_empty_error" ).hide() ;
                }
            }
            jQuery( ".rs_encash_paypal_address" ).hide() ;
            jQuery( ".rs_encash_custom_payment_option_value" ).hide() ;
            jQuery( ".rs_encash_wallet" ).hide() ;

            var wallet = jQuery( "#is_walletia_selected" ).val() ;
            var redirection_type = fp_cashback_action_params.redirection_type ;
            var redirection_url = fp_cashback_action_params.redirection_url ;
            var data = ( {
                action : "cashbackrequest" ,
                points : pointsascash ,
                reason : reason_to_encash ,
                wallet : wallet ,
                payment_method : selectedpaymentmethod ,
                paypal_email : paypal_email ,
                custom_payment_details : custom_payment_details ,
                available_points : available_points ,
                converted_value : currency_converted_value ,
                status : "Due" ,
                sumo_security : fp_cashback_action_params.fp_cashback_request
            } ) ;
            $.post( fp_cashback_action_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {
                    if ( redirection_type == '2' && redirection_url != '' ) {
                        window.location.href = redirection_url ;
                    } else {
                        location.reload() ;
                    }
                    jQuery( ".success_info" ).show() ;
                    jQuery( ".success_info" ).fadeOut( 3000 ) ;
                    jQuery( "#encashing_form" )[0].reset() ;
                } else {
                    window.alert( response.data.error ) ;
                }
            } ) ;
        } ,
    } ;
    RSCashbackFrontend.init( ) ;
} ) ;