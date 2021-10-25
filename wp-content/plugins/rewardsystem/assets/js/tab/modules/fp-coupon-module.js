/*
 * SUMO Coupon - Module
 */
jQuery( function ( $ ) {
    var CouponModule = {
        init : function () {
            this.show_hide_message_field() ;
            $( document ).on( 'click' , '#_rs_show_hide_coupon_if_sumo_discount' , this.show_hide_message_field ) ;
        } ,
        show_hide_message_field : function () {
            if ( jQuery( '#_rs_show_hide_coupon_if_sumo_discount' ).is( ':checked' ) == true ) {
                jQuery( '#rs_message_in_cart_and_checkout_for_discount' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_message_in_cart_and_checkout_for_discount' ).closest( 'tr' ).hide() ;
            }
        } ,
    } ;
    CouponModule.init() ;
} ) ;