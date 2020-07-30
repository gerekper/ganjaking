/*
 * Referral - Module
 */
jQuery( function ( $ ) {
    var RSReferralFrontend = {
        init : function ( ) {
            if ( '1' == fp_referral_frontend_params.enqueue_footable) {
              this.table_as_footable() ;
            }
            
            this.initialize_fb_buttons( ) ;
            this.initialize_twitter_buttons( ) ;
            this.initialize_gplus_lang() ;
            $( document ).on( 'click' , '.referralclick' , this.unset_generated_referral_link ) ;
            $( document ).on( 'click' , '.share_wrapper_default_url' , this.fb_default_share_button ) ;
            $( document ).on( 'click' , '.share_wrapper_static_url' , this.fb_static_share_button ) ;
        } ,
        table_as_footable : function ( ) {
            jQuery( '.referral_log_table' ).footable( ).bind( 'footable_filtering' , function ( e ) {
                var selected = jQuery( '.filter-status' ).find( ':selected' ).text( ) ;
                if ( selected && selected.length > 0 ) {
                    e.filter += ( e.filter && e.filter.length > 0 ) ? ' ' + selected : selected ;
                    e.clear = ! e.filter ;
                }
            } ) ;
            jQuery( '.referral_link' ).footable( ).bind( {
                'footable_row_expanded' : function ( e ) {
                    RSReferralFrontend.initialize_fb_buttons( ) ;
                    RSReferralFrontend.initialize_twitter_buttons( ) ;
                    RSReferralFrontend.initialize_gplus_lang() ;
                } ,
            } ) ;
        } ,
        unset_generated_referral_link : function ( ) {
            var getarraykey = jQuery( this ).attr( 'data-array' ) ;
            var data = ( {
                action : 'unset_referral' ,
                unsetarray : getarraykey ,
                sumo_security : frontendscripts_params.unset_referral
            } ) ;
            $.post( frontendscripts_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {
                    window.location.reload( ) ;
                } else {
                    window.alert( response.data.error ) ;
                }
            } ) ;
        } ,
        initialize_fb_buttons : function ( ) {
            window.fbAsyncInit = function ( ) {
                FB.init( {
                    appId : fp_referral_frontend_params.fbappid ,
                    autoLogAppEvents : true ,
                    xfbml : true ,
                    version : 'v3.0'
                } ) ;
            } ;
            ( function ( d , s , id ) {
                var js , fjs = d.getElementsByTagName( s )[0] ;
                if ( d.getElementById( id ) ) {
                    return ;
                }
                js = d.createElement( s ) ;
                js.id = id ;
                if ( fp_referral_frontend_params.buttonlanguage == 1 ) {
                    js.src = "https://connect.facebook.net/en_US/sdk.js" ;
                } else {
                    if ( fp_referral_frontend_params.wplanguage == '' ) {
                        js.src = "https://connect.facebook.net/en_US/sdk.js" ;
                    } else {
                        js.src = "https://connect.facebook.net/" + fp_referral_frontend_params.wplanguage + "/sdk.js" ;
                    }
                }
                fjs.parentNode.insertBefore( js , fjs ) ;
            }( document , 'script' , 'facebook-jssdk' ) ) ;
        } ,
        initialize_twitter_buttons : function ( ) {
            ! function ( d , s , id ) {
                var js , fjs = d.getElementsByTagName( s )[0] , p = /^http:/.test( d.location ) ? 'http' : 'https' ;
                if ( ! d.getElementById( id ) ) {
                    js = d.createElement( s ) ;
                    js.id = id ;
                    js.src = p + '://platform.twitter.com/widgets.js' ;
                    fjs.parentNode.insertBefore( js , fjs ) ;
                }
            }( document , 'script' , 'twitter-wjs' ) ;
        } ,
        initialize_gplus_lang : function ( ) {
            window.___gcfg = {
                lang : fp_referral_frontend_params.wplanguage ,
                parsetags : 'onload'
            }
        } ,
        fb_default_share_button : function ( evt ) {
            RSReferralFrontend.fb_share_button( evt , 'share_wrapper_default_url' ) ;
        } ,
        fb_static_share_button : function ( evt ) {
            RSReferralFrontend.fb_share_button( evt , 'share_wrapper_static_url' ) ;
        } ,
        fb_share_button : function ( evt , id ) {
            evt.preventDefault( ) ;
            var a = document.getElementById( id ) ;
            var post_title = a.getAttribute( 'data-title' ) ;
            var post_desc = a.getAttribute( 'data-description' ) ;
            var post_image = a.getAttribute( 'data-image' ) ;
            var post_url = a.getAttribute( 'href' ) ;
            if ( post_image == '' ) {
                var obj = {
                    method : 'feed' ,
                    name : post_title ,
                    link : post_url ,
                    description : post_desc ,
                } ;
            } else {
                var obj = {
                    method : 'feed' ,
                    name : post_title ,
                    link : post_url ,
                    picture : post_image ,
                    description : post_desc ,
                } ;
            }
            function callback( response ) {
                if ( response != null ) {
                    alert( 'Sucessfully Posted' ) ;
                } else {
                    alert( 'Cancel' ) ;
                }
            }
            FB.ui( obj , callback ) ;
            return false ;
        } ,
    } ;
    RSReferralFrontend.init( ) ;
} ) ;