/*
 * Module Tab
 */
jQuery( function ( $ ) {
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
                        if ( enable == 'yes' ) {
                            divclass.removeClass( fp_module_tab_params.inactiveclass ).addClass( fp_module_tab_params.activeclass ) ;
                            closest.find( '.rs_settings_link' ).show() ;
                        } else {
                            divclass.removeClass( fp_module_tab_params.activeclass ).addClass( fp_module_tab_params.inactiveclass ) ;
                            closest.find( '.rs_settings_link' ).hide() ;
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