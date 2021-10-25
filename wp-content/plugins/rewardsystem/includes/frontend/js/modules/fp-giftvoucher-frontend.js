/*
 * GiftVocuher - Module
 */
jQuery( function ( $ ) {
    var RSGiftVoucherFrontend = {
        init : function () {
            $( document ).on( 'click' , '.rs_gift_voucher_submit_button' , this.redeemgiftvoucher ) ;
        } ,
        redeemgiftvoucher : function ( evt ) {
            evt.preventDefault() ;
            var vouchercode = jQuery( '#rs_redeem_voucher_code' ).val() ;
            var pregmatchedcode = vouchercode.replace( /\s/g , '' ) ;
            if ( pregmatchedcode === '' ) {
                jQuery( '.rs_redeem_voucher_error' ).html( fp_giftvoucher_frontend_params.error ).fadeIn().delay( 5000 ).fadeOut() ;
                return false ;
            } else {
                RSGiftVoucherFrontend.block( '.rs_giftvoucher_field' ) ;
                var data = {
                    action : 'redeemvouchercode' ,
                    vouchercode : pregmatchedcode ,
                    sumo_security : fp_giftvoucher_frontend_params.fp_redeem_vocuher ,
                } ;
                $.post( fp_giftvoucher_frontend_params.ajaxurl , data , function ( response ) {
                    if ( true === response.success ) {
                        jQuery( '.rs_redeem_voucher_success' ).html( jQuery.parseHTML( response.data.content ) ).fadeIn().delay( 5000 ).fadeOut() ;
                        jQuery( '#rs_redeem_voucher_code' ).val( '' ) ;
                    } else {
                        jQuery( '.rs_redeem_voucher_error' ).html( jQuery.parseHTML( response.data.error ) ).fadeIn().delay( 5000 ).fadeOut() ;
                    }
                    RSGiftVoucherFrontend.unblock( '.rs_giftvoucher_field' ) ;
                } ) ;
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
    RSGiftVoucherFrontend.init() ;
} ) ;




