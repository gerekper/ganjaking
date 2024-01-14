/*
 * Buying Points - Module
 */
jQuery( function ( $ ) {
    'use strict' ;
    var BuyingPointsModule = {
        init : function () {
            this.show_or_hide_for_bulk_update_for_buying_point_is_applicable_for() ;
            this.show_or_hide_for_enable_buying_points() ;
            $( document ).on( 'change' , '#rs_buying_points_is_applicable' , this.bulk_update_for_buying_point_is_applicable_for ) ;
            $( document ).on( 'change' , '#rs_enable_buying_points' , this.enable_buying_points ) ;

            $( document ).on( 'click' , '.rs_bulk_update_button_for_buying_points' , this.bulk_update_points_for_buying_reward ) ;
        } ,
        bulk_update_for_buying_point_is_applicable_for : function () {
            BuyingPointsModule.show_or_hide_for_bulk_update_for_buying_point_is_applicable_for() ;
        } ,
        show_or_hide_for_bulk_update_for_buying_point_is_applicable_for : function () {
            if ( $( '#rs_buying_points_is_applicable' ).val() == '1' ) {
                $( '#rs_include_products_for_buying_points' ).closest( 'tr' ).hide() ;
                $( '#rs_exclude_products_for_buying_points' ).closest( 'tr' ).hide() ;
            } else if ( $( '#rs_buying_points_is_applicable' ).val() == '2' ) {
                $( '#rs_include_products_for_buying_points' ).closest( 'tr' ).show() ;
                $( '#rs_exclude_products_for_buying_points' ).closest( 'tr' ).hide() ;
            } else {
                $( '#rs_include_products_for_buying_points' ).closest( 'tr' ).hide() ;
                $( '#rs_exclude_products_for_buying_points' ).closest( 'tr' ).show() ;
            }
        } ,
        enable_buying_points : function () {
            BuyingPointsModule.show_or_hide_for_enable_buying_points() ;
        } ,
        show_or_hide_for_enable_buying_points : function () {
            if ( $( '#rs_enable_buying_points' ).val() == 'no' ) {
                $( '#rs_points_for_buying_points' ).closest( 'tr' ).hide() ;
            } else {
                $( '#rs_points_for_buying_points' ).closest( 'tr' ).show() ;
            }
        } ,
        bulk_update_points_for_buying_reward : function ( ) {
                var div = $( this ).closest( 'div.rs_section_wrapper' ) ;
                BuyingPointsModule.block( div ) ;
                var dataparam = {
                    action : 'buying_points_bulk_update_action' ,
                    sumo_security : fp_buyingpoints_module_param.buying_reward_bulk_update ,
                    applicable_product : $( '#rs_buying_points_is_applicable' ).val() ,
                    enable_buying_point : $( '#rs_enable_buying_points' ).val() ,
                    buying_point : $( '#rs_points_for_buying_points' ).val() ,
                    include_products : $( '#rs_include_products_for_buying_points' ).val() ,
                    exclude_products : $( '#rs_exclude_products_for_buying_points' ).val() ,
                } ;
                $.post( fp_buyingpoints_module_param.ajaxurl , dataparam , function ( response ) {
                    if ( true === response.success ) {
                        window.location.href = response.data.redirect_url ;
                    } else {
                        window.alert( response.data.error ) ;
                    }
                    BuyingPointsModule.unblock( div ) ;
                } ) ;
            return false ;
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
    BuyingPointsModule.init() ;
} ) ;