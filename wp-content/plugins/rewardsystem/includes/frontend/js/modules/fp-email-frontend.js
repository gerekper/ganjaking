/*
 * Email - Module
 */
jQuery( function ( $ ) {
    var RSEmailFrontend = {
        init : function ( ) {
            $( document ).on( 'click' , '#subscribeoption' , this.subscribe_or_unsubscribe_mail ) ;
        } ,
        subscribe_or_unsubscribe_mail : function () {
            var subscribe = $( '#subscribeoption' ).is( ':checked' ) ? 'yes' : 'no' ;
            var data = {
                action : "subscribemail" ,
                subscribe : subscribe ,
                sumo_security : fp_email_frontend_params.fp_subscribe_mail
            } ;
            $.post( fp_email_frontend_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {
                    window.alert( response.data.content ) ;
                } else {
                    window.alert( response.data.error ) ;
                }
            } ) ;
        } ,
    } ;
    RSEmailFrontend.init( ) ;
} ) ;