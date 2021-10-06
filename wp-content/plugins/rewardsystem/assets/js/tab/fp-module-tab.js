/*
 * Module Tab
 */
jQuery( function ( $ ) {
    'use strict' ;
    var RSModuleTab = {
        init : function () {
            $( document ).on( 'change' , '.rs_enable_module' , this.activate_module ) ;
        } ,
        activate_module : function ( event ) {
            event.preventDefault( ) ;
            var $this = $( event.currentTarget ) ;
            var enable = $( $this ).is( ':checked' ) ? 'yes' : 'no' ;
            var metakey = $( $this ).attr( 'data-metakey' ) ;
            var closest = $( $this ).closest( 'div.rs_grid' ) ;
            var divclass = closest.find( '.rs_inner_grid' ) ;
            var dataparam = ( {
                action : 'activatemodule' ,
                enable : enable ,
                metakey : metakey ,
                sumo_security : fp_module_tab_params.fp_activate_module
            } ) ;
            $.post( fp_module_tab_params.ajaxurl , dataparam , function ( response ) {
                if ( true === response.success ) {
                    if ( fp_module_tab_params.section ) {
                        window.location.href = fp_module_tab_params.redirecturl ;
                    } else {
                        var tabname = closest.find( '.fp-srp-tab-name' ).val();
                        if ( 'yes' == enable ) {
                            divclass.removeClass( fp_module_tab_params.inactiveclass ).addClass( fp_module_tab_params.activeclass ) ;
                            closest.find( '.rs_settings_link' ).addClass( 'fp-srp-show' ) ;
                            closest.find( '.rs_settings_link' ).removeClass( 'fp-srp-hide' ) ;
                            $('#'+tabname).addClass( 'fp-srp-show' );
                            $('#'+tabname).removeClass( 'fp-srp-hide' );
                        } else {
                            divclass.removeClass( fp_module_tab_params.activeclass ).addClass( fp_module_tab_params.inactiveclass ) ;
                            closest.find( '.rs_settings_link' ).addClass( 'fp-srp-hide' ) ;
                            closest.find( '.rs_settings_link' ).removeClass( 'fp-srp-show' ) ;
                            $('#'+tabname).addClass( 'fp-srp-hide' );
                            $('#'+tabname).removeClass( 'fp-srp-show' );
                        }
                    }
                } else {
                    window.alert( response.data.error ) ;
                }
            } ) ;
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
    RSModuleTab.init() ;
} ) ;