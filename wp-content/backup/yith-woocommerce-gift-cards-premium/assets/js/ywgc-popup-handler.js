/**
 * Frontend
 *
 * @author YITH
 * @package YITH Easy Login Register Popup for WooCommerce
 * @version 1.0.0
 */

;(function( $, window, document ){

    if( typeof ywgc_popup_data == 'undefined' ) {
        return;
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


    /**
     * @param $popup
     * @param attr
     * @constructor
     */
    var YITHLoginRegisterPopup = function( item ) {
        if( ! item.length ) {
            return;
        }

        this.self               = item;
        this.wrap               = item.find( '.yith-ywgc-popup-wrapper' );
        this.popup              = item.find( '.yith-ywgc-popup' );
        this.content            = item.find( '.yith-ywgc-popup-content-wrapper' );
        this.overlay            = item.find( '.yith-ywgc-overlay' );
        this.blocked            = false;
        this.opened             = false;
        this.additional         = false;
        this.currentSection     = null;
        this.previousSection    = null;
        this.animationIn        = this.popup.attr( 'data-animation-in' );
        this.animationOut       = this.popup.attr( 'data-animation-out' );

        // position first
        this.position( null );


        // prevent propagation on popup click
        $( this.popup ).on( 'click', function(ev){
            ev.stopPropagation();
        })

        // attach event
        $( window ).on( 'resize', { obj: this }, this.position );
        // open
        $( document ).on( 'click', ywgc_popup_data.mainSelector, { obj: this, additional: false }, this.open );


        //close the popup on overlay click
        $(document).on( 'click', '.yith-ywgc-overlay.close-on-click', function (e) {
            e.preventDefault();
            $('.yith-ywgc-popup-wrapper .yith-ywgc-popup-close').click();
        });

        this.popup.on('click', '.qq-upload-drop-area-selector', function (e) {
          e.preventDefault();
          $('.qq-upload-button-selector input').click();
        });

        //close the popup on X button click
        this.popup.on( 'click', '.yith-ywgc-popup-close', { obj: this }, this.close);
    };

    /** UTILS **/
    YITHLoginRegisterPopup.prototype.position           = function( event ) {
        let popup    = event == null ? this.popup : event.data.obj.popup,
            window_w = $(window).width(),
            window_h = $(window).height(),
            margin   = ( ( window_w - 40 ) > ywgc_popup_data.popupWidth ) ? window_h/10 + 'px' : '0',
            width    = ( ( window_w - 40 ) > ywgc_popup_data.popupWidth ) ? ywgc_popup_data.popupWidth + 'px' : 'auto';

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
                    background: '#fff url(' + ywgc_popup_data.loader + ') no-repeat center',
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


    /** EVENT **/
    YITHLoginRegisterPopup.prototype.open               = function( event ) {
        event.preventDefault();

        let object = event.data.obj;
        // if already opened, return
        if( object.opened ) {
            return;
        }

        object.opened = true;

        // add template
        object.loadTemplate( 'gift-card-presets', {
            title: 'Test title'
        } );
        // animate
        object.self.fadeIn("slow");
        animateInElem( object.overlay, 'fadeIn' );
        animateInElem( object.popup, object.animationIn );
        // add html and body class
        $('html, body').addClass( 'yith_ywgc_opened' );

        object.wrap.css('position', 'fixed');
        object.overlay.css('position', 'fixed');
        object.overlay.css('z-index', '1');

        // trigger event
        $(document).trigger( 'yith_ywgc_popup_opened', [ object.popup, object ] );
    }

    YITHLoginRegisterPopup.prototype.loadTemplate       = function( id, data ) {
        var template            = wp.template( id );
        this.showTemplate( template( data ) );
    }

    YITHLoginRegisterPopup.prototype.showTemplate       = function( section ) {
        this.content.hide().html( section ).fadeIn("slow");
        $(document).trigger( 'yith_ywgc_popup_template_loaded', [ this.popup, this ] );
    }

    YITHLoginRegisterPopup.prototype.close              = function( event ) {
        event.preventDefault();

        var object = event.data.obj;

        object.additional    = false;
        object.opened        = false;
        object.self.fadeOut("slow");

        // remove body class
        $('html, body').removeClass( 'yith_ywgc_opened' );
        // trigger event
        $(document).trigger( 'yith_ywgc_popup_closed', [ object.popup, object ] );
    }


    // START
    $( function(){
        new YITHLoginRegisterPopup( $( document ).find( '#yith-ywgc' ) );
    });

})( jQuery, window, document );
