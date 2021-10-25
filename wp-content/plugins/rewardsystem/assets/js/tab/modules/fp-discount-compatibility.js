/*
 * Discount Compatibility- Module
 */
jQuery( function( $ ) {
    var DiscountCompatibility = {
        init : function( ) {
            this.show_restriction_message_for_redeeming() ;
            this.show_restriction_message_for_coupon() ;
            $( document ).on( 'change' , '#rs_show_redeeming_field' , this.show_r_hide_redeeming_restriction_message ) ;
            $( document ).on( 'click' , '#_rs_show_hide_coupon_if_sumo_discount' , this.show_r_hide_coupon_restriction_message ) ;
        } ,
        show_r_hide_redeeming_restriction_message : function() {
            DiscountCompatibility.show_restriction_message_for_redeeming() ;
        } ,
        show_restriction_message_for_redeeming : function() {
            if( $( '#rs_show_redeeming_field' ).val() === '1' ) {
                $( '#rs_redeeming_usage_restriction_for_discount' ).closest( 'tr' ).hide() ;
            } else {
                $( '#rs_redeeming_usage_restriction_for_discount' ).closest( 'tr' ).show() ;
            }
        } ,
        show_r_hide_coupon_restriction_message : function() {
            DiscountCompatibility.show_restriction_message_for_coupon() ;
        } ,
        show_restriction_message_for_coupon : function() {
            if( $( '#_rs_show_hide_coupon_if_sumo_discount' ).is( ':checked' ) === true ) {
                $( '#rs_message_in_cart_and_checkout_for_discount' ).closest( 'tr' ).show() ;
            } else {
                $( '#rs_message_in_cart_and_checkout_for_discount' ).closest( 'tr' ).hide() ;
            }
        } ,
    }
    DiscountCompatibility.init() ;
} ) ;