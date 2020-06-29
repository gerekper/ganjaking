/*
 * Redeem - Module
 */
jQuery( function ( $ ) {
    var RSRedeemFrontend = {
        init : function () {
            $( document ).on( 'click' , '#mainsubmi' , this.validation_in_cart_and_checkout ) ;
            $( document ).on( 'click' , '.woocommerce-info a.redeemit' , this.toggle_redeem_field ) ;
            $( '.checkout_redeeming' ).css( "display" , "none" ) ;
        } ,
        toggle_redeem_field : function () {
            $( '.checkout_redeeming' ).toggle() ;
        } ,
        validation_in_cart_and_checkout : function () {
            var availablepoints = parseFloat( fp_redeem_frontend_params.available_points ) ;
            availablepoints = Math.round( availablepoints * 100 ) / 100 ;
            var minredeempoint = parseFloat( fp_redeem_frontend_params.minredeempoint ) ;
            minredeempoint = Math.round( minredeempoint * 100 ) / 100 ;
            var maxredeempoint = parseFloat( fp_redeem_frontend_params.maxredeempoint ) ;
            maxredeempoint = Math.round( maxredeempoint * 100 ) / 100 ;
            var getvalue = jQuery( '#rs_apply_coupon_code_field' ).val() ;
            if ( getvalue === '' ) {
                jQuery( '.rs_warning_message' ).html( fp_redeem_frontend_params.emptyerr ) ;
                return false ;
            } else if ( jQuery.isNumeric( getvalue ) == false ) {
                jQuery( '.rs_warning_message' ).html( fp_redeem_frontend_params.numericerr ) ;
                return false ;
            } else if ( getvalue > availablepoints ) {
                jQuery( '.rs_warning_message' ).html( fp_redeem_frontend_params.maxredeemederr ) ;
                return false ;
            } else if ( jQuery.isNumeric( getvalue ) == true ) {
                if ( getvalue < 0 ) {
                    jQuery( '.rs_warning_message' ).html( fp_redeem_frontend_params.numericerr ) ;
                    return false ;
                }
            }
            if ( fp_redeem_frontend_params.minredeempoint == fp_redeem_frontend_params.maxredeempoint ) {
                if ( getvalue < minredeempoint ) {
                    jQuery( '.rs_warning_message' ).html( fp_redeem_frontend_params.minmaxerr ) ;
                    return false ;
                } else if ( getvalue > maxredeempoint ) {
                    jQuery( '.rs_warning_message' ).html( fp_redeem_frontend_params.minmaxerr ) ;
                    return false ;
                }
            }
            if ( fp_redeem_frontend_params.minredeempoint != '' ) {
                if ( getvalue < minredeempoint ) {
                    jQuery( '.rs_warning_message' ).html( fp_redeem_frontend_params.minerr ) ;
                    return false ;
                }
            }
            if ( fp_redeem_frontend_params.maxredeempoint != '' ) {
                if ( getvalue > maxredeempoint ) {
                    jQuery( '.rs_warning_message' ).html( fp_redeem_frontend_params.maxerr ) ;
                    return false ;
                }
            }
        } ,
    } ;
    RSRedeemFrontend.init() ;
} ) ;