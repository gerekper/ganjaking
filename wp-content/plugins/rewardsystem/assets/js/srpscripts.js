
jQuery( function ( $ ) {

    var SRPScripts = {
        init : function () {
            $( document ).on( 'click' , '#rs_enable_earn_points_for_user' , this.allow_user_to_earn_points ) ;
        } ,
        allow_user_to_earn_points : function ( event ) {
            event.preventDefault() ;
            var checkbox_value = jQuery( '#rs_enable_earn_points_for_user' ).is( ':checked' ) == true ? 'yes' : 'no' ;
            var con = checkbox_value == 'yes' ? confirm( srpscripts_params.checked_alert_msg ) : confirm( srpscripts_params.unchecked_alert_msg ) ;
            if ( con ) {
                SRPScripts.block( '.enable_reward_points' ) ;
                var data = {
                    action : 'enable_reward_program' ,
                    enable_reward_points : checkbox_value ,
                    sumo_security : srpscripts_params.enable_option_nonce
                } ;
                $.post( srpscripts_params.ajaxurl , data , function ( response ) {
                    if ( true === response.success ) {
                        SRPScripts.unblock( '.enable_reward_points' ) ;
                        window.location.reload( true ) ;
                    } else {
                        window.alert( response.data.error ) ;
                        SRPScripts.unblock( '.enable_reward_points' ) ;
                    }
                } ) ;
            }
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
    SRPScripts.init() ;
} ) ;