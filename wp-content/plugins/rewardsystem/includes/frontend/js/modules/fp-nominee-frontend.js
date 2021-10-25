/*
 * Nominee - Module
 */
jQuery( function ( $ ) {
    var RSNomineeModule = {
        init : function () {
            this.trigger_on_page_load() ;
            $( document ).on( 'click' , '.rs_add_nominee' , this.save_nominee ) ;
        } ,
        trigger_on_page_load : function () {
            if ( fp_nominee_frontend_params.fp_wc_version <= parseFloat( '2.2.0' ) ) {
                $( '.rs_select_nominee' ).chosen() ;
                $( '.rs_select_nominee_in_checkout' ).chosen() ;
            } else {
                $( '.rs_select_nominee' ).select2() ;
                $( '.rs_select_nominee_in_checkout' ).select2() ;
            }
        } ,
        save_nominee : function () {
            var data = {
                action : "savenominee" ,
                selectedvalue : $( '.rs_select_nominee' ).val() ,
                sumo_security : fp_nominee_frontend_params.fp_save_nominee
            } ;
            $.post( fp_nominee_frontend_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {
                    window.alert( response.data.content ) ;
                } else {
                    window.alert( response.data.error ) ;
                }
            } ) ;
        } ,
    } ;
    RSNomineeModule.init() ;
} ) ;