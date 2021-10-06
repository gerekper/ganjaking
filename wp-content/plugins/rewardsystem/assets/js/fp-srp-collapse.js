jQuery( function ( $ ) {
    'use strict' ;
    jQuery( '.rs_section_wrapper h2' ).nextUntil( 'h2' ).hide() ;
    jQuery( '.rs_membership_compatible_wrapper h2' ).nextUntil( 'h2' ).hide() ;
    jQuery( '.rs_bsn_compatible_wrapper h2' ).nextUntil( 'h2' ).hide() ;
    jQuery( '.rs_affs_compatible_wrapper h2' ).nextUntil( 'h2' ).hide() ;
    jQuery( '.rs_fpwcrs_compatible_wrapper h2' ).nextUntil( 'h2' ).hide() ;
    jQuery( '.rs_subscription_compatible_wrapper h2' ).nextUntil( 'h2' ).hide() ;
    jQuery( '.rs_coupon_compatible_wrapper h2' ).nextUntil( 'h2' ).hide() ;
    jQuery( '.rs_adminstrator_wrapper h2' ).nextUntil( 'h2' ).hide() ;
    jQuery( '.rs_exp_col input[type="checkbox"]' ).click( function () {
        if ( jQuery( this ).is( ":checked" ) ) {
            jQuery( '.rs_section_wrapper h2' ).nextUntil( 'h2' ).show() ;
            jQuery( '.rs_membership_compatible_wrapper h2' ).nextUntil( 'h2' ).show() ;
            jQuery( '.rs_bsn_compatible_wrapper h2' ).nextUntil( 'h2' ).show() ;
            jQuery( '.rs_affs_compatible_wrapper h2' ).nextUntil( 'h2' ).show() ;
            jQuery( '.rs_fpwcrs_compatible_wrapper h2' ).nextUntil( 'h2' ).show() ;
            jQuery( '.rs_subscription_compatible_wrapper h2' ).nextUntil( 'h2' ).show() ;
            jQuery( '.rs_coupon_compatible_wrapper h2' ).nextUntil( 'h2' ).show() ;
            jQuery( '.rs_adminstrator_wrapper h2' ).nextUntil( 'h2' ).show() ;
        } else if ( jQuery( this ).is( ":not(:checked)" ) ) {
            jQuery( '.rs_section_wrapper h2' ).nextUntil( 'h2' ).hide() ;
            jQuery( '.rs_membership_compatible_wrapper h2' ).nextUntil( 'h2' ).hide() ;
            jQuery( '.rs_bsn_compatible_wrapper h2' ).nextUntil( 'h2' ).hide() ;
            jQuery( '.rs_affs_compatible_wrapper h2' ).nextUntil( 'h2' ).hide() ;
            jQuery( '.rs_fpwcrs_compatible_wrapper h2' ).nextUntil( 'h2' ).hide() ;
            jQuery( '.rs_subscription_compatible_wrapper h2' ).nextUntil( 'h2' ).hide() ;
            jQuery( '.rs_coupon_compatible_wrapper h2' ).nextUntil( 'h2' ).hide() ;
            jQuery( '.rs_adminstrator_wrapper h2' ).nextUntil( 'h2' ).hide() ;
        }
    } ) ;

} ) ;
