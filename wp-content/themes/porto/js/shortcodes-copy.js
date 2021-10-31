/**
 * Porto Shortcodes Copy JavaScript file
 */

'use strict';
jQuery( document ).ready( function ( $ ) {

    if ( $( 'textarea.wpb-content' ) ) {
        $( 'textarea.wpb-content' ).val( '' );
    }

    if ( $( '.elements-copy-list .copy' ) ) {

        $( '.elements-copy-list' ).on( 'click', '.copy', function ( e ) {
            e.preventDefault();
            var $this = $( this ),
                pb_id = $this.data( 'id' ),
                pb_content,
                initial = {};

            if ( pb_id == 'wpb' ) { // Wpbakery
                var $textarea = $this.parent().find( 'textarea.wpb-content' ),
                    textareaValue;
                if ( $textarea && 0 == $textarea.val().length ) {
                    textareaValue = $textarea.attr( 'placeholder' );
                    $textarea.val( textareaValue );
                }

                $textarea.trigger( 'select' );
                document.execCommand( 'copy' );

            } else if ( pb_id == 'el' ) { // Elementor

                pb_content = $this.data( 'content' );

                if ( window.localStorage.getItem( 'elementor' ) && pb_content ) {
                    initial = JSON.parse( window.localStorage.getItem( 'elementor' ) );
                    initial.clipboard = pb_content;
                    window.localStorage.setItem( 'elementor', JSON.stringify( initial ) );
                } else if ( pb_content ) {
                    initial.__expiration = {};
                    initial.clipboard = pb_content;
                    window.localStorage.setItem( 'elementor', JSON.stringify( initial ) );
                } else {
                    initial.__expiration = {};
                    initial.clipboard = {};
                    window.localStorage.setItem( 'elementor', JSON.stringify( initial ) );
                }

            } else if ( pb_id == 'gu' ) { // Gutenberg
                var $textarea2 = $this.parent().find( 'textarea.gu-content' );
                $textarea2.trigger( 'select' );
                document.execCommand( 'copy' );
            }

            $( '.elements-copy-list .copy' ).each( function ( e ) {
                var $el = $( this );
                if ( $el.hasClass( 'copied' ) || $el.html( 'Copied' ) ) {
                    $el.removeClass( 'copied' );
                    if ( $el.data( 'id' ) == 'wpb' ) {
                        $el.html( '<i class="fas fa-download mr-2"></i>WPBakery' );
                    } else if ( $el.data( 'id' ) == 'el' ) {
                        $el.html( '<i class="fas fa-download mr-2"></i>Elementor' );
                    } else {
                        $el.html( '<i class="fas fa-download mr-2"></i>Gutenberg' );
                    }
                }
            } );

            $this.addClass( 'copied' );
            $this.html( 'Copied' );
        } );

    }

    if ( $( '.elements-copy-list textarea' ) ) {
        $( '.elements-copy-list textarea' ).css( {
            "opacity": "0",
            "width": "0",
            "height": "0"
        } );
    }

} );