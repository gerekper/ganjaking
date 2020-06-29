/**
 * Frontend
 *
 * @author YITH
 * @package YITH Easy Login Register Popup for WooCommerce
 * @version 1.0.0
 */

;(function( $, window, document ){

    if( typeof yith_welrp == 'undefined' ) {
        return;
    }

    var perfEntries = performance.getEntriesByType("navigation"),
        entry       = perfEntries[0],
        json        = typeof entry != 'undefined' ? entry.toJSON() : false,
        logged_in   = false

    function cachedScript ( url, options ) {
        options = $.extend( options || {}, {
            dataType: "script",
            cache: true,
            url: url
        });
        return jQuery.ajax( options );
    };

    function doRequest ( action, data ) {
        return $.ajax({
            url: yith_welrp.ajaxUrl.toString().replace( '%%endpoint%%', action ),
            data: data,
            dataType: 'json',
            type: 'POST'
        })
    }

    function animateInElem( elem, animation, callback ) {
        elem.show().addClass( 'animated ' + animation );
        elem.one( 'animationend', function() {
            elem.removeClass( 'animated ' + animation );
            if( typeof callback != 'undefined' ) {
                callback();
            }
        });
    }

    function animateOutElem( elem, animation, callback ) {
        elem.addClass( 'animated ' + animation );
        elem.one( 'animationend', function() {
            elem.hide().removeClass( 'animated ' + animation );
            if( typeof callback != 'undefined' ) {
                callback();
            }
        });
    }

    if( json && json.type == 'back_forward' ) {
        // check user logged in on back_forward navigation type to avoid cache issues
        doRequest(yith_welrp.checkLogin, {_ajax_nonce: yith_welrp.checkNonce, context: "frontend"})
            .done((response) => {
                logged_in = response.logged;
            });
    }

    /**
     * @param $popup
     * @param attr
     * @constructor
     */
    var YITHLoginRegisterPopup                          = function( item ) {
        if( ! item.length ) {
            return;
        }

        this.self               = item;
        this.wrap               = item.find( '.yith-welrp-popup-wrapper' );
        this.popup              = item.find( '.yith-welrp-popup' );
        this.content            = item.find( '.yith-welrp-popup-content-wrapper' );
        this.overlay            = item.find( '.yith-welrp-overlay' );
        this.blocked            = false;
        this.opened             = false;
        this.animated           = false;
        this.additional         = item.hasClass( 'fixed' );
        this.currentSection     = null;
        this.previousSection    = null;
        this.animationIn        = this.popup.attr( 'data-animation-in' );
        this.animationOut       = this.popup.attr( 'data-animation-out' );

        // position first
        this.position( null );
        // handle social if needed
        this.handleFacebook();
        this.handleGoogle();
        this.handleGoogleReCaptcha();

        // prevent propagation on popup click
        $( this.popup ).on( 'click', function(ev){
            ev.stopPropagation();
        })

        // attach event
        $( window ).on( 'resize', { obj: this }, this.position );
        // open
        $( document ).on( 'click', yith_welrp.mainSelector, { obj: this, additional: false }, this.open );
        if( yith_welrp.additionalSelector ) {
            $( document ).on( 'click', yith_welrp.additionalSelector, { obj: this, additional: true }, this.open );
        }
        // close
        if( this.wrap.hasClass( 'close-on-click' ) ) {
            this.wrap.on( 'click', { obj: this }, this.close );
        }
        this.popup.on( 'click', '.yith-welrp-popup-close', { obj: this }, this.close );
        this.popup.on( 'click', 'a.yith-welrp-go-back', { obj: this }, this.goBack );
        this.popup.on( 'click', 'a.yith-welrp-lost-password', { obj: this }, this.goLostPassword );
        this.popup.on( 'click', 'a.yith-welrp-send-auth, a.yith-welrp-send-email', { obj: this }, this.sendResetEmail );
        this.popup.on( 'click', '.yith-welrp-password-eye', { obj: this }, this.passwordEye );
        this.popup.on( 'submit', 'form.yith-welrp-form', { obj: this }, this.formSubmit );
        this.popup.on( 'keyup', '#user_login, #reg_email', { obj: this }, this.emailSuggestions );
        this.popup.on( 'click', '.yith-welrp-email-suggestion-item', { obj: this }, this.useEmailSuggestion );
        this.popup.on( 'click', '*', this.closeEmailSuggestion );
    };

    /** UTILS **/
    YITHLoginRegisterPopup.prototype.position           = function( event ) {
        let popup    = event == null ? this.popup : event.data.obj.popup,
            window_w = $(window).width(),
            window_h = $(window).height(),
            margin   = ( ( window_w - 40 ) > yith_welrp.popupWidth ) ? window_h/10 + 'px' : '0',
            width    = ( ( window_w - 40 ) > yith_welrp.popupWidth ) ? yith_welrp.popupWidth + 'px' : 'auto';

        popup.css({
            'margin-top'    : margin,
            'margin-bottom' : margin,
            'width'         : width,
        });
    },
    YITHLoginRegisterPopup.prototype.block              = function() {
        if( ! this.blocked ) {
            this.popup.block({
                message   : null,
                overlayCSS: {
                    background: '#fff url(' + yith_welrp.loader + ') no-repeat center',
                    opacity   : 0.5,
                    cursor    : 'none'
                }
            });
            this.blocked = true;
        }
    }
    YITHLoginRegisterPopup.prototype.unblock            = function() {
        if( this.blocked ) {
            this.popup.unblock();
            this.blocked = false;
        }
    }
    YITHLoginRegisterPopup.prototype.reset              = function( message ) {
        this.loadTemplate( 'email-section', {
            title: this.additional ? yith_welrp.fsAdditionalTitle : yith_welrp.fsTitle
        });
        this.previousSection = null;
        if( message ) {
            this.addMessage( message, 'error' );
        }
        this.unblock();
    }
    YITHLoginRegisterPopup.prototype.loadTemplate       = function( id, data ) {
        var template            = wp.template( id );
        // save section and show new template
        this.content.find('.yith-welrp-message').remove();
        this.previousSection    = this.content.html();
        this.showTemplate( template( data ) );
    }
    YITHLoginRegisterPopup.prototype.showTemplate       = function( section ) {
        this.content.hide().html( section ).fadeIn();
        $(document).trigger( 'yith_welrp_popup_template_loaded', [ this.popup, this ] );
    }
    YITHLoginRegisterPopup.prototype.addMessage         = function( message, type ) {
        if( message ) {
            if( typeof type == 'undefined' ) {
                type = 'success';
            }

            let elem = $('<div/>', {
                'class': 'yith-welrp-message ' + type,
            }).html( message );

            if( $('.yith-welrp-message').length ) {
                $('.yith-welrp-message').replaceWith( elem );
            }
            else {
                this.content.find( '.yith-welrp-submit-button' ).before( elem );
            }
        }
    }
    YITHLoginRegisterPopup.prototype.passwordEye        = function( event ) {
        let input = $(this).closest('.yith-welrp-password-container').find('input'),
            type  = input.attr('type');

        $(this).toggleClass('opened');
        input.attr( 'type', type == 'password' ? 'text' : 'password' );
    }

    /** EVENT **/
    YITHLoginRegisterPopup.prototype.open               = function( event ) {
        if( logged_in ) {
            return;
        }
        event.preventDefault();

        let object = event.data.obj;
        // if already opened or animated, return
        if( object.opened || object.animated ) {
            return;
        }

        object.opened = true;
        // check if additional
        if( event.data.additional ){
            object.additional   = true;
            object.popup.addClass( 'additional' );
        }
        // add template
        object.loadTemplate( 'email-section', {
            title: object.additional ? yith_welrp.fsAdditionalTitle : yith_welrp.fsTitle
        } );
        // animate
        object.animated = true;
        object.self.show();
        animateInElem( object.overlay, 'fadeIn' );
        animateInElem( object.popup, object.animationIn, function(){
            object.animated = false;
        });
        // add html and body class
        $('html, body').addClass( 'yith_welrp_opened' );
        // trigger event
        $(document).trigger( 'yith_welrp_popup_opened', [ object.popup, object ] );
    }
    YITHLoginRegisterPopup.prototype.close              = function( event ) {
        event.preventDefault();
        var object = event.data.obj;
        // if animated, block close
        if( object.animated ) {
            return;
        }
        // animate. Remove body class after overlay close
        object.animated = true;
        animateOutElem( object.popup, object.animationOut );
        animateOutElem( object.overlay, 'fadeOut slow', function(){
            object.opened           = false;
            object.additional       = false;
            object.animated         = false;
            object.popup.removeClass( 'additional' );
            object.self.hide();
        });
        // remove body class
        $('html, body').removeClass( 'yith_welrp_opened' );
        // trigger event
        $(document).trigger( 'yith_welrp_popup_closed', [ object.popup, object ] );
    }
    YITHLoginRegisterPopup.prototype.goBack             = function( event ) {
        event.preventDefault();
        let object = event.data.obj;

        if( object.previousSection != null ) {
            object.showTemplate( object.previousSection );
            object.previousSection = null;
        } else {
            object.reset();
        }
    }
    YITHLoginRegisterPopup.prototype.goLostPassword     = function( event ) {
        event.preventDefault();

        var object  = event.data.obj,
            user    = object.content.find("input[name='user_login']"),
            data    = {
                title           : yith_welrp.lostPasswordTitle,
                message         : yith_welrp.lostPasswordMessage,
                button_label    : yith_welrp.lostPasswordButton,
                user_login      : user.length ? user.val() : '',
                action          : 'lost-password'
            };

        object.loadTemplate( 'lost-password-section', data );
    }
    YITHLoginRegisterPopup.prototype.sendResetEmail     = function( event ) {
        event.preventDefault();
        var object  = event.data.obj,
            user    = $(this).data('user-login'),
            data    = [
                { name: "action", value: 'lost-password' },
                { name: "resend", value: true },
                { name: "user_login", value: user }
            ];

        object.formAction( data );
    }
    YITHLoginRegisterPopup.prototype.emailSuggestions   = function( event ) {
        let field       = $(this),
            val         = field.val().split('@'),
            base        = val[0],
            domain      = val.length > 1 ? val[1] : '',
            suggestions = '';

        // create suggestions
        $.each( yith_welrp.emailSuggestions, function( x, j ) {
            if( domain && ( j.indexOf( domain ) !== 0 || domain === j ) ) {
                return;
            }
            suggestions = suggestions + '<li class="yith-welrp-email-suggestion-item">' + base + '@' + j + '</li>';
        });

        if( base.length < 3 || ! suggestions ){
            field.closest( '.yith-welrp-email-suggestion' ).replaceWith( field ).end().focus();
            return;
        }

        if( ! field.closest( '.yith-welrp-email-suggestion' ).length ) {
            field.wrap( '<div class="yith-welrp-email-suggestion"></div>' )
                .after( '<ul class="yith-welrp-email-suggestion-list"></ul>' ).focus();
        }

        field.next( '.yith-welrp-email-suggestion-list' ).html( suggestions );
    }
    YITHLoginRegisterPopup.prototype.useEmailSuggestion = function( event ) {
        event.stopPropagation();
        $(this).closest('.yith-welrp-email-suggestion').find('input').val( $(this).text() ).keyup();
    }
    YITHLoginRegisterPopup.prototype.closeEmailSuggestion = function( event ) {
        event.stopPropagation();
        if( $(this).closest( '.yith-welrp-email-suggestion' ).length || $(this).hasClass( 'yith-welrp-email-suggestion' ) ) {
            return;
        }

        let field = $( '.yith-welrp-email-suggestion' ).find('input');
        if( field.length ) {
            $( '.yith-welrp-email-suggestion' ).replaceWith( field );
        }

    }
    YITHLoginRegisterPopup.prototype.formSubmit         = function( event ) {
        event.preventDefault();

        var object  = event.data.obj,
            form    = $(this),
            data    = form.serializeArray();

        object.formAction( data );
    }
    YITHLoginRegisterPopup.prototype.formAction         = function( data ) {

        var object = this;

        data.push(
            { name: "origin", value: window.location.pathname },
            { name: "additional", value: object.additional ? 1 : 0 },
            { name: "_ajax_nonce", value: yith_welrp.formNonce },
            { name: "context", value: "frontend" }
        );

        object.block();

        doRequest( yith_welrp.formAction, data )
            .done( ( response ) => {

                if( response.success ){
                    if( typeof response.data.action != 'undefined' ) { // handle action if any
                        if( typeof response.data.action.redirectTo != 'undefined' ){
                            window.location.href = response.data.action.redirectTo;
                            return;
                        }
                        else if( typeof response.data.action.nextSection != 'undefined' ){
                            object.loadTemplate( response.data.action.nextSection, response.data.popup );
                        }
                    }
                }

                object.addMessage( response.data.message, response.success ? 'success' : 'error' );

                $(document).trigger( 'yith_welrp_popup_form_action_handled', [ response.success, response, this ] );

                object.unblock();
            })
            .fail( ( response ) => {
                object.reset( yith_welrp.errorMsg );
            });
    }

    /** Google reCatpcha **/
    YITHLoginRegisterPopup.prototype.handleGoogleReCaptcha  = function() {
        if( typeof yith_welrp.googleReCaptcha == 'undefined' ) {
            return;
        }
        // get script
        cachedScript( 'https://www.google.com/recaptcha/api.js' )
            .done( ( script, textStatus ) => {

                var reCaptcha = null;

                $( document ).on( 'yith_welrp_popup_template_loaded', function(){
                    var captchaContainer = document.getElementById( 'g-recaptcha' );
                    if( captchaContainer ) {
                        reCaptcha = grecaptcha.render( captchaContainer, {
                            'sitekey' : yith_welrp.googleReCaptcha,
                            'theme' : 'light'
                        });
                    }
                });

                $( document ).on( 'yith_welrp_popup_form_action_handled', function( event, response ){
                    if( ! response && reCaptcha !== null ) {
                        grecaptcha.reset( reCaptcha );
                    }
                });
            })
            .fail( ( jqxhr, settings, exception ) => {
                console.log('Error retreiving Google reCaptcha');
            });
    }

    /** SOCIALS **/
    YITHLoginRegisterPopup.prototype.socialAction       = function( token, social ) {

        var object  = this,
            data    = [
                { name: "origin", value: window.location.pathname },
                { name: "_ajax_nonce", value: yith_welrp.socialNonce },
                { name: "context", value: "frontend" },
                { name: "token", value: token },
                { name: "social", value: social }
            ];

        doRequest( yith_welrp.socialAction, data )
            .done( ( response ) => {
                if( response.success ){
                    if( typeof response.data.redirectTo != 'undefined' ){
                        window.location.href = response.data.redirectTo
                    }
                } else {
                    object.reset( response.data.message, 'error' );
                }

                $(document).trigger( 'yith_welrp_popup_form_social_handled', [ response.success, response, social, this ] );
            })
            .fail( ( response ) => {
                object.reset( yith_welrp.message );
            })
    }
    YITHLoginRegisterPopup.prototype.handleFacebook     = function() {
        var object   = this;

        if( typeof yith_welrp.facebookAppID == 'undefined' ) {
            return;
        }

        // get script
        cachedScript( 'https://connect.facebook.net/en_US/sdk.js' )
            .done( ( script, textStatus ) => {
                FB.init({
                    appId   : yith_welrp.facebookAppID,
                    xfbml   : true,     // Parse social plugins on this webpage.
                    version : 'v4.0'
                });

                object.popup.on( 'click', '#yith-welrp-facebook-button', ( event ) => {
                    object.block();

                    FB.getLoginStatus( ( response ) => {
                        if( response.status == 'connected' ) {
                            object.socialAction( response.authResponse.accessToken, 'facebook' );
                        } else {
                            FB.login( ( response ) => {
                                if( response.status === 'connected' ) {
                                    object.socialAction( response.authResponse.accessToken, 'facebook' );
                                } else {
                                    object.reset( yith_welrp.errorMsg );
                                }
                            }, {scope: 'public_profile,email'});
                        }
                    });
                });
            })
            .fail( ( jqxhr, settings, exception ) => {
                console.log('Error retreiving FB SDK');
            });
    }
    YITHLoginRegisterPopup.prototype.handleGoogle       = function() {
        var object   = this;

        if( typeof yith_welrp.googleAppID == 'undefined' ) {
            return;
        }

        // get script
        cachedScript( 'https://apis.google.com/js/api:client.js' )
            .done( ( script, textStatus ) => {
                gapi.load('auth2', function(){
                    // Retrieve the singleton for the GoogleAuth library and set up the client.
                    auth2 = gapi.auth2.init({
                        client_id: yith_welrp.googleAppID,
                        cookiepolicy: 'single_host_origin'
                    });

                    $( document ).on( 'yith_welrp_popup_opened', function(){
                        var button = document.getElementById( 'yith-welrp-google-button' );

                        $(button).on( 'click', function(){
                            object.block();
                        })
                        auth2.attachClickHandler( button, {},
                            ( googleUser ) => {
                                object.socialAction( googleUser.getAuthResponse().id_token, 'google' );
                            },
                            ( error ) => {
                                console.log( JSON.stringify( error, undefined, 2 ) );
                                object.reset( yith_welrp.errorMsg );
                            });
                    });
                });
            })
            .fail( ( jqxhr, settings, exception ) => {
                console.log('Error retreiving Google SDK');
            });
    }

    // START
    $(document).on( 'ready', function(){
        new YITHLoginRegisterPopup( $( document ).find( '#yith-welrp' ) );
    });

})( jQuery, window, document );
