/*
 * Social Reward Points - Module
 */
jQuery( function ( $ ) {
    var SocialActionScripts = {
        init : function ( ) {
            this.tooltip_for_social_icons() ;
            this.initialize_fb_buttons( ) ;
            this.initialize_twitter_buttons( ) ;
            this.initialize_vk_button() ;
            this.initialize_gplus_lang() ;
            this.initialize_ok_share_button() ;
            $( window ).on( 'load' , this.fb_like_button ) ;
            $( window ).on( 'load' , this.twitter_tweet_button ) ;
            $( window ).on( 'load' , this.twitter_follow_button ) ;
            $( window ).on( 'load' , this.instagram_follow_button ) ;
            $( window ).on( 'load' , this.vk_like_button ) ;
            $( window ).on( 'load' , this.ok_share_button ) ;
            $( window ).on( 'load' , this.custom_fblike_button ) ;
            $( document ).on( 'click' , '.share_wrapper1' , this.fb_share_button ) ;
            $( document ).on( 'click' , '.share_wrapper11' , this.fb_share_button ) ;
            $( document ).on( 'click' , '.fp_gplus_share' , this.gplus_share_button ) ;
            $( document ).on( 'click' , '.rs_custom_fbshare_button' , this.custom_fbshare_button ) ;
            $( document ).on( 'click' , '.rs_custom_tweet_button' , this.custom_tweet_button ) ;
            $( document ).on( 'click' , '.rs_custom_tweetfollow_button' , this.custom_tweetfollow_button ) ;
            $( document ).on( 'click' , '.rs_custom_gplus_button' , this.custom_gplus_button ) ;
            $( document ).on( 'click' , '.rs_custom_vklike_button' , this.custom_vklike_button ) ;
            $( document ).on( 'click' , '.rs_custom_instagram_button' , this.custom_instagram_button ) ;
            $( document ).on( 'click' , '.rs_custom_ok_button' , this.custom_ok_button ) ;
        } ,
        custom_fblike_button : function () {
            $( '.rs_custom_fblike_button' ).click( function () {
                SocialActionScripts.award_social_points( 'fblikecallback' , fp_social_action_params.fb_like , 'on' , fp_social_action_params.allowfblike , 'post_or_page_unlike' ) ;
            } ) ;
        } ,
        initialize_fb_buttons : function ( ) {
            if ( ( fp_social_action_params.showfblike == '1' || fp_social_action_params.showfbshare == '1' ) ) {
                window.fbAsyncInit = function ( ) {
                    FB.init( {
                        appId : fp_social_action_params.fbappid ,
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
                    if ( fp_social_action_params.buttonlanguage == 1 ) {
                        js.src = "https://connect.facebook.net/en_US/sdk.js" ;
                    } else {
                        if ( fp_social_action_params.wplanguage == '' ) {
                            js.src = "https://connect.facebook.net/en_US/sdk.js" ;
                        } else {
                            js.src = "https://connect.facebook.net/" + fp_social_action_params.wplanguage + "/sdk.js" ;
                        }
                    }

                    fjs.parentNode.insertBefore( js , fjs ) ;
                }( document , 'script' , 'facebook-jssdk' ) ) ;
            }
        } ,
        initialize_twitter_buttons : function ( ) {
            if ( fp_social_action_params.showtweet == '1' || fp_social_action_params.showtwitterfollow == '1' ) {
                ! function ( d , s , id ) {
                    var js , fjs = d.getElementsByTagName( s )[0] , p = /^http:/.test( d.location ) ? 'http' : 'https' ;
                    if ( ! d.getElementById( id ) ) {
                        js = d.createElement( s ) ;
                        js.id = id ;
                        js.src = p + '://platform.twitter.com/widgets.js' ;
                        fjs.parentNode.insertBefore( js , fjs ) ;
                    }
                }( document , 'script' , 'twitter-wjs' ) ;
            }
        } ,
        initialize_vk_button : function ( ) {
            if ( fp_social_action_params.showvk == '1' ) {
                ! function ( d , s , id ) {
                    var js , fjs = d.getElementsByTagName( s )[0] , p = /^http:/.test( d.location ) ? 'http' : 'https' ;
                    if ( ! d.getElementById( id ) ) {
                        js = d.createElement( s ) ;
                        js.id = id ;
                        js.src = p + '://vk.com/js/api/openapi.js?116' ;
                        fjs.parentNode.insertBefore( js , fjs ) ;
                    }
                }( document , 'script' , 'vk-jssdk' ) ;
            }
        } ,
        initialize_gplus_lang : function ( ) {
            if ( fp_social_action_params.showgplus == '1' ) {
                window.___gcfg = {
                    lang : fp_social_action_params.wplanguage ,
                    parsetags : 'onload'
                }
            }
        } ,
        initialize_ok_share_button : function () {
            if ( fp_social_action_params.showok == '1' ) {
                ! function ( d , id , did , st , title , description , image ) {
                    var js = d.createElement( "script" ) ;
                    js.src = "https://connect.ok.ru/connect.js" ;
                    js.onload = js.onreadystatechange = function () {
                        if ( ! this.readyState || this.readyState == "loaded" || this.readyState == "complete" ) {
                            if ( ! this.executed ) {
                                this.executed = true ;
                                setTimeout( function () {
                                    OK.CONNECT.insertShareWidget( id , did , st , title , description , image ) ;
                                } , 0 ) ;
                            }
                        }
                    } ;
                    d.documentElement.appendChild( js ) ;
                }( document , "ok_shareWidget" , fp_social_action_params.url , '{"sz":30,"st":"oval","nc":1,"nt":1}' , "" , "" , "" ) ;
                ! function ( d , id , did , st ) {
                    var js = d.createElement( "script" ) ;
                    js.src = "https://connect.ok.ru/connect.js" ;
                    js.onload = js.onreadystatechange = function () {
                        if ( ! this.readyState || this.readyState == "loaded" || this.readyState == "complete" ) {
                            if ( ! this.executed ) {
                                this.executed = true ;
                                setTimeout( function () {
                                    onOkConnectReady()
                                } , 0 ) ;
                            }
                        }
                    }
                    d.documentElement.appendChild( js ) ;
                }( document ) ;
                function onOkConnectReady() {
                    OK.CONNECT.insertGroupWidget( "mineGroupWidgetDivId" , "50582132228315" , "{width:250,height:335}" ) ;
                    OK.CONNECT.insertShareWidget( "mineShareWidgetDivId" , "https://apiok.ru" , "{width:125,height:25,st:'oval',sz:12,ck:1}" ) ;
                }
            }
        } ,
        award_social_points : function ( actionname , security , state , allowaction , responsedata ) {
            var dataparam = ( {
                action : actionname ,
                state : state ,
                postid : fp_social_action_params.post_id ,
                type : fp_social_action_params.type ,
                sumo_security : security
            } ) ;
            $.post( fp_social_action_params.ajaxurl , dataparam ,
                    function ( response ) {
                        if ( true === response.success ) {
                            if ( allowaction ) {
                                if ( response.data.content == responsedata ) {
                                    $( '<p>' + response.data.unsuccess_msg + '</p>' ).appendTo( '.social_promotion_success_message' ).fadeIn( ).delay( 5000 ).fadeOut( ) ;
                                } else {
                                    $( '<p>' + response.data.success_msg + '</p>' ).appendTo( '.social_promotion_success_message' ).fadeIn( ).delay( 5000 ).fadeOut( ) ;
                                }
                            } else {
                                $( '<p>' + response.data.restrictmsg + '</p>' ).appendTo( '.social_promotion_success_message' ).fadeIn( ).delay( 5000 ).fadeOut( ) ;
                            }
                        } else {
                            window.alert( response.data.error ) ;
                        }
                    } ) ;
        } ,
        fb_like_button : function ( ) {
            if ( fp_social_action_params.showfblike == '1' ) {
                var page_like_callback = function ( url , html_element ) {
                    SocialActionScripts.award_social_points( 'fblikecallback' , fp_social_action_params.fb_like , 'on' , fp_social_action_params.allowfblike , 'post_or_page_unlike' ) ;
                } ;
                var page_unlike_callback = function ( url , html_element ) {
                    SocialActionScripts.award_social_points( 'fblikecallback' , fp_social_action_params.fb_like , 'off' , fp_social_action_params.allowfblike , 'page_unlike' ) ;
                } ;
                FB.Event.subscribe( 'edge.create' , page_like_callback ) ;
                FB.Event.subscribe( 'edge.remove' , page_unlike_callback ) ;
            }
        } ,
        fb_share_button : function ( evt ) {
            evt.preventDefault( ) ;
            if ( fp_social_action_params.showfbshare == '1' ) {
                var post_title = fp_social_action_params.post_title ;
                var post_desc = fp_social_action_params.post_desc ;
                var post_image = fp_social_action_params.post_image ;
                var post_url = fp_social_action_params.post_url ;
                var post_caption = fp_social_action_params.post_caption ;
                var obj = {
                    method : 'feed' ,
                    name : post_title ,
                    link : post_url ,
                    picture : post_image ,
                    caption : post_caption ,
                    description : post_desc ,
                } ;
                function callback( response ) {
                    if ( response != null ) {
                        SocialActionScripts.award_social_points( 'fbsharecallback' , fp_social_action_params.fb_share , 'on' , fp_social_action_params.allowfbshare , 'post_or_page_unshare' ) ;
                    } else {
                        alert( 'Cancel' ) ;
                    }

                }
                FB.ui( obj , callback ) ;
            }
            return false ;
        } ,
        custom_fbshare_button : function () {
            SocialActionScripts.award_social_points( 'fbsharecallback' , fp_social_action_params.fb_share , 'on' , fp_social_action_params.allowfbshare , 'post_or_page_unshare' ) ;
        } ,
        twitter_tweet_button : function ( ) {
            if ( fp_social_action_params.showtweet == '1' ) {
                twttr.events.bind( 'tweet' , function ( event ) {
                    SocialActionScripts.award_social_points( 'twittertweetcallback' , fp_social_action_params.twitter_tweet , 'on' , fp_social_action_params.allowtweet , 'tweeted' ) ;
                } ) ;
            }
        } ,
        custom_tweet_button : function ( ) {
            SocialActionScripts.award_social_points( 'twittertweetcallback' , fp_social_action_params.twitter_tweet , 'on' , fp_social_action_params.allowtweet , 'tweeted' ) ;
        } ,
        twitter_follow_button : function ( ) {
            if ( fp_social_action_params.showtwitterfollow == '1' ) {
                twttr.events.bind( 'follow' , function ( event ) {
                    SocialActionScripts.award_social_points( 'twitterfollowcallback' , fp_social_action_params.twitter_follow , 'on' , fp_social_action_params.allowfollow , 'followed' ) ;
                } ) ;
            }
        } ,
        custom_tweetfollow_button : function ( ) {
            SocialActionScripts.award_social_points( 'twitterfollowcallback' , fp_social_action_params.twitter_follow , 'on' , fp_social_action_params.allowfollow , 'followed' ) ;
        } ,
        instagram_follow_button : function ( ) {
            if ( fp_social_action_params.showinstagram == '1' && fp_social_action_params.instagram_button_type == '1' ) {

                if ( fp_social_action_params.instagram_profile_name !== '' ) {

                    if ( fp_social_action_params.type == 'product' ) {
                        var instagram_button = document.querySelector( '.instagram_button' ) ;
                    } else {
                        var instagram_button = document.querySelector( '.instagram_button_post' ) ;
                    }

                    if ( instagram_button !== null ) {
                        instagram_button.addEventListener( 'click' , function ( e ) {
                            SocialActionScripts.award_social_points( 'instagramcallback' , fp_social_action_params.instagram_follow , 'on' , fp_social_action_params.allowinstagramfollow , 'instagramfollowed' ) ;
                        } ) ;
                    }
                }
            }
        } ,
        custom_instagram_button : function () {
            SocialActionScripts.award_social_points( 'instagramcallback' , fp_social_action_params.instagram_follow , 'on' , fp_social_action_params.allowinstagramfollow , 'instagramfollowed' ) ;
        } ,
        vk_like_button : function () {
            if ( fp_social_action_params.showvk == '1' && fp_social_action_params.vkappid !== '' ) {
                VK.init( {
                    apiId : fp_social_action_params.vkappid ,
                    onlyWidgets : true
                } ) ;
                VK.Widgets.Like( "vk_like" , { type : "button" } ) ;
                VK.Observer.subscribe( "widgets.like.liked" , function f() {
                    SocialActionScripts.award_social_points( 'vklikecallback' , fp_social_action_params.vk_like , 'on' , fp_social_action_params.allowvklike , 'vkliked' ) ;
                } ) ;
                VK.Observer.subscribe( "widgets.like.unliked" , function f1() {
                    SocialActionScripts.award_social_points( 'vklikecallback' , fp_social_action_params.vk_like , 'off' , fp_social_action_params.allowvklike , 'vkliked' ) ;
                } ) ;
            }
        } ,
        custom_vklike_button : function () {
            SocialActionScripts.award_social_points( 'vklikecallback' , fp_social_action_params.vk_like , 'on' , fp_social_action_params.allowvklike , 'vkliked' ) ;
        } ,
        gplus_share_button : function () {
            if ( fp_social_action_params.showgplus == '1' ) {
                SocialActionScripts.award_social_points( 'gpluscallback' , fp_social_action_params.gplus_share , 'on' , fp_social_action_params.allowgplus , 'gplusshared' ) ;
            }
        } ,
        custom_gplus_button : function ( ) {
            SocialActionScripts.award_social_points( 'gpluscallback' , fp_social_action_params.gplus_share , 'on' , fp_social_action_params.allowgplus , 'gplusshared' ) ;
        } ,
        ok_share_button : function ( ) {
            if ( fp_social_action_params.showok == '1' ) {
                if ( window.addEventListener ) {
                    window.addEventListener( 'message' , onShare , false ) ;
                } else {
                    window.attachEvent( 'onmessage' , onShare ) ;
                }
                function onShare( e ) {
                    var args = e.data.split( "$" ) ;
                    if ( args[0] == "ok_shared" ) {
                        SocialActionScripts.award_social_points( 'okrucallback' , fp_social_action_params.okru_share , 'on' , fp_social_action_params.allowokru , 'okrushared' ) ;
                    }
                }
            }
        } ,
        custom_ok_button : function () {
            SocialActionScripts.award_social_points( 'okrucallback' , fp_social_action_params.okru_share , 'on' , fp_social_action_params.allowokru , 'okrushared' ) ;
        } ,
        tooltip_for_social_icons : function () {
            if ( fp_social_action_params.fbliketooltip == '1' ) {
                $( '.' + fp_social_action_params.fbliketooltipclassname ).tipsy( { gravity : 's' , live : 'true' , fallback : fp_social_action_params.fbliketooltipmsg } ) ;
            }
            if ( fp_social_action_params.fbsharetooltip == '1' ) {
                $( '.' + fp_social_action_params.fbsharetooltipclassname ).tipsy( { gravity : 's' , live : 'true' , fallback : fp_social_action_params.fbsharetooltipmsg } ) ;
            }
            if ( fp_social_action_params.tweettooltip == '1' ) {
                $( '.' + fp_social_action_params.tweettooltipclassname ).tipsy( { gravity : 's' , live : 'true' , fallback : fp_social_action_params.tweettooltipmsg } ) ;
            }
            if ( fp_social_action_params.followtooltip == '1' ) {
                $( '.' + fp_social_action_params.followtooltipclassname ).tipsy( { gravity : 's' , live : 'true' , fallback : fp_social_action_params.followtooltipmsg } ) ;
            }
            if ( fp_social_action_params.oktooltip == '1' ) {
                $( '.' + fp_social_action_params.oktooltipclassname ).tipsy( { gravity : 's' , live : 'true' , fallback : fp_social_action_params.oktooltipmsg } ) ;
            }
            if ( fp_social_action_params.gplustooltip == '1' ) {
                $( '.' + fp_social_action_params.gplustooltipclassname ).tipsy( { gravity : 's' , live : 'true' , fallback : fp_social_action_params.gplustooltipmsg } ) ;
            }
            if ( fp_social_action_params.vktooltip == '1' ) {
                $( '.' + fp_social_action_params.vktooltipclassname ).tipsy( { gravity : 's' , live : 'true' , fallback : fp_social_action_params.vktooltipmsg } ) ;
            }
            if ( fp_social_action_params.instagramtooltip == '1' ) {
                $( '.' + fp_social_action_params.instatooltipclassname ).tipsy( { gravity : 's' , live : 'true' , fallback : fp_social_action_params.instagramtooltipmsg } ) ;
            }
        }
    } ;
    SocialActionScripts.init( ) ;
} ) ;