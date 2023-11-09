/**
 * UnGrabber
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         3.0.4
 * @copyright       (C) 2018 - 2023 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

( function ( $ ) {

    "use strict";

    $( document ).ready( function () {

        let mdpUngrabber = window.mdpUngrabberUnity;

        /** Logo click - smooth scroll. */
        $( '.mdc-drawer__header > a.mdp-plugin-title' ).on( 'click', function ( e ) {
            e.preventDefault();

            $( 'html, body' ).animate( {
                scrollTop: 0
            }, 500 );

        } );

        /** Subscribe form. */
        let $subscribeBtn = $('#mdp-subscribe');
        $subscribeBtn.on( 'click', function (e) {

            e.preventDefault();

            let $mail = $('#mdp-subscribe-mail');
            let $name = $('#mdp-subscribe-name');
            let mailIndex = $mail.parent().data('mdc-index');

            if ( $mail.val().length > 0 && window.MerkulovMaterial[mailIndex].valid) {

                const noticeArea = document.querySelector( '.mdp-subscribe-form-message' );
                $name.prop("disabled", true);
                $mail.prop("disabled", true);
                $('#mdp-subscribe').prop("disabled", true);

                $.ajax({
                    type: "GET",
                    url: `${ mdpUngrabber.restBase }ungrabber/v2/subscribe`,
                    crossDomain: true,
                    data: `name=${ $name.val() }&mail=${ $mail.val() }`,
                    success: function (data) {

                        data = JSON.parse( data );

                        if ( data.status ) {

                            noticeArea.classList.add( 'mdp-subscribe-form-message-success' );
                            noticeArea.innerHTML = noticeArea.dataset.success;

                            setTimeout( function () { noticeArea.style.display = 'none' }, 7500 );

                        } else {

                            noticeArea.classList.add( 'mdp-subscribe-form-message-error' );
                            noticeArea.innerHTML = noticeArea.dataset.error;

                        }

                        noticeArea.style.display = 'block';
                        setTimeout( function () { noticeArea.style.display = 'none' }, 7500 );

                    },
                    error: function (err) {

                        noticeArea.style.display = 'block';
                        noticeArea.classList.add( 'mdp-subscribe-form-message-error' );
                        noticeArea.innerHTML = noticeArea.dataset.warn;

                        $('#mdp-subscribe-name').prop( "disabled", false );
                        $('#mdp-subscribe-mail').prop( "disabled", false );
                        $('#mdp-subscribe').prop( "disabled", false );

                        setTimeout( function () { noticeArea.style.display = 'none' }, 7500 );

                    }
                });

            } else {
                window.MerkulovMaterial[mailIndex].valid = false;
            }

        });

        /** Check for Updates. */
        let $checkUpdatesBtn = $( '#mdp-updates-btn' );
        $checkUpdatesBtn.on( 'click', function ( e ) {

            e.preventDefault();

            /** Disable button and show process. */
            $checkUpdatesBtn.attr( 'disabled', true ).addClass( 'mdp-spin' ).find( '.material-icons' ).text( 'refresh' );

            /** Prepare data for AJAX request. */
            let data = {
                action: 'check_updates_ungrabber',
                nonce: mdpUngrabber.nonce,
                checkUpdates: true
            };

            /** Do AJAX request. */
            $.post( mdpUngrabber.ajaxURL, data, function( response ) {

                if ( response ) {
                    console.info( 'Latest version information updated.' );
                    location.reload();
                } else {
                    console.warn( response );
                }

            }, 'json' ).fail( function( response ) {

                /** Show Error message if returned some data. */
                console.error( response );
                alert( 'Looks like an Error has occurred. Please try again later.' );

            } ).always( function() {

                /** Enable button again. */
                $checkUpdatesBtn.attr( 'disabled', false ).removeClass( 'mdp-spin' ).find( '.material-icons' ).text( 'autorenew' );

            } );

        } );

        /** Custom CSS */
        function custom_css_init() {

            let $custom_css_fld = $('#mdp_custom_css_fld');

            if (!$custom_css_fld.length) {
                return;
            }

            if ( ! wp.codeEditor ) { return; }

            let editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
            editorSettings.codemirror = _.extend(
                {},
                editorSettings.codemirror, {
                    indentUnit: 2,
                    tabSize: 2,
                    mode: 'css'
                }
            );

            let css_editor;
            css_editor = wp.codeEditor.initialize( 'mdp_custom_css_fld', editorSettings );

            css_editor.codemirror.on( 'change', function( cMirror ) {
                css_editor.codemirror.save(); // Save data from CodeEditor to textarea.
                $custom_css_fld.change();
            } );

        }
        custom_css_init();

        /** Layout Select */
        $( '.mdp-layout .mdp-nav-dropdown a' ).on( 'click', function( e ) {

            e.preventDefault();

            let layoutImg = e.target;
            let layoutA = e.target.parentElement;

            if ( e.target.tagName !== 'IMG' ) {

                layoutImg = e.target.querySelector( 'img' );
                layoutA = e.target;

            }

            const val = layoutA.getAttribute( 'data-val' );
            const name = layoutImg.alt;
            const img = $( '.mdp-layout button img' );

            /** Change image on thumb */
            img.attr( 'src', img.attr( 'src' ).replace(/(.*)\/.*(\.svg$)/i, '$1/' + val + '$2') );
            img.attr( 'alt', name );

            /** Change setting value in the input */
            $( this ).closest( 'td' ).find( 'input' ).val( val ).change();

            /** Close dropdown */
            setTimeout( function() {
                $( '.mdp-layout' ).removeClass( 'mdp-open' );
            }, 100 );

            /** Select new item as active */
            $( '.mdp-layout .mdp-nav-dropdown a' ).removeClass( 'mdp-active' );
            $( this ).addClass( 'mdp-active' );

        } );

        /**
         * Media Library control
         */
        function media_library_init() {

            jQuery( '.mdc-media-library-field' ).each( function() {

                const uid = jQuery( this ).data( 'uid' );

                // Add Media button
                jQuery( `#${ uid }-add` ).click( function( e ) {

                    e.preventDefault();

                    // Media Library Frame
                    let mlFrame;
                    if ( mlFrame ) {
                        mlFrame.open();
                    }

                    // Define mlFrame as wp.media object
                    mlFrame = wp.media( {
                        multiple : false,
                        library : {
                            type : 'image',
                        }
                    } );

                    // On open, get the id from the hidden input and select the appropriate images in the media manager
                    mlFrame.on( 'open', () => {

                        let selection =  mlFrame.state().get('selection');

                        let ids = jQuery( `input#${ uid }` ).val().split(',' );

                        ids.forEach( function( id){
                            let attachment = wp.media.attachment( id );
                            attachment.fetch();
                            selection.add( attachment ? [ attachment ] : [] );
                        } );

                    } );

                    // On close, get selections and save to the hidden input plus other AJAX stuff to refresh the image preview
                    mlFrame.on( 'close', () => {

                        let selection =  mlFrame.state().get('selection');

                        let gallery_ids = [];
                        let my_index = 0;

                        selection.each( function(attachment) {

                            gallery_ids[ my_index ] = attachment['id'];
                            my_index++;

                        } );

                        let ids = gallery_ids.join(",");
                        if( ids.length === 0 ) return true;//if closed without selecting an image

                        jQuery( `input#${ uid }` ).val( ids );
                        refreshImage( ids, uid );

                    } );

                    mlFrame.open();

                } );

                // Remove button
                jQuery( `#${ uid }-remove` ).click( function ( e ) {

                    e.preventDefault();

                    console.log( 'remove' );

                    jQuery( `input#${ uid }` ).val( '' );
                    jQuery( `#${ uid }-preview-image` ).replaceWith( '' );

                } );

            } );

        }

        /**
         * Refresh image preview
         * @param the_id
         * @param uid
         */
        function refreshImage( the_id, uid ){

            let data = {
                action: 'unity_media_library',
                id: the_id
            };

            jQuery.get(ajaxurl, data, function(response) {

                if( response.success === true ) {
                    jQuery( `#${ uid }-preview-image` ).replaceWith( response.data.image );
                }

            } );

        }

        media_library_init();

        /**
         * Init sides group of controls
         */
        function initSidesControl() {

            const $topInput = $( this ).find( '.mdp-controls-sides-top input' );
            const $rightInput = $( this ).find( '.mdp-controls-sides-right input' );
            const $bottomInput = $( this ).find( '.mdp-controls-sides-bottom input' );
            const $leftInput = $( this ).find( '.mdp-controls-sides-left input' );
            const $lock = $( this ).find( '.mdp-controls-sides-lock' );
            const $lockInput = $( this ).find( '.mdp-controls-sides-lock input' );

            let locked = JSON.parse( $lockInput.val() );

            /**
             * Set same values if link enabled
             * @param $sides - array of controls
             */
            function lockedValues( $sides ) {

                $sides.each( function () {

                    if ( locked ) {

                        $lockInput.val( 'true' );

                        // Update values on change
                        $( this ).on( 'change', function () {

                            $topInput.val( this.value );
                            $rightInput.val( this.value );
                            $bottomInput.val( this.value );
                            $leftInput.val( this.value );

                        } );

                        // Update values in live-time
                        $( this ).on( 'focus', lockedValuesLive );

                    } else {

                        $lockInput.val( 'false' );
                        $( this ).off( 'change' );
                        $( this ).off( 'focus' );

                    }

                } );

            }

            /**
             * Live updating fo locked sides values
             */
            function lockedValuesLive () {

                const focusInput = this;
                const focusInterval = setInterval( () => {

                    $topInput.val( focusInput.value );
                    $rightInput.val( focusInput.value );
                    $bottomInput.val( focusInput.value );
                    $leftInput.val( focusInput.value );

                }, 100 );

                $( this ).on( 'blur', function (  ) {

                    clearInterval( focusInterval );

                } );

            }

            // Set text input to number type
            $topInput.attr( 'type', 'number' );
            $rightInput.attr( 'type', 'number' );
            $bottomInput.attr( 'type', 'number' );
            $leftInput.attr( 'type', 'number' );

            // Initial values lock
            lockedValues( $( this ).find( '.mdp-controls-sides-single input' ) );

            // Lock values after click on the link
            if ( locked ) {
                $lock.addClass( 'active-lock' );
            }
            $lock.on( 'click',  function () {

                $lock.toggleClass( 'active-lock' );
                locked = $lock.hasClass( 'active-lock' );

                lockedValues( $( this ).parent().find( '.mdp-controls-sides-single input' ) );

            } );

        }
        $( '.mdp-controls-sides' ).each( initSidesControl );

        /**
         * Purchase code validation
         */
        const $pidInput = document.querySelector( '#mdp_envato_purchase_code' );
        const $pidHelper = document.querySelector( '.mdp-activation-form .mdc-text-field-helper-text' );
        const $submitBtn = document.querySelector( '#submit' );
        if ( $pidInput && $pidHelper ) {

            if ( ! sessionStorage.getItem( 'activationHelper' ) ) {
                sessionStorage.setItem( 'activationHelper', $pidHelper.innerText );
            }

            // Run on page load
            validateAction( $pidInput.value.trim() );

            // Run on input change
            $pidInput.addEventListener( 'change', ( e ) => {

                validateAction( e.target.value.trim() );

            } );

            // Run on input focus
            let pidFocusInterval;
            $pidInput.addEventListener( 'focus', ( e ) => {

                pidFocusInterval = setInterval( () => {

                    validateAction( e.target.value.trim() )

                }, 500 );

            } );

            // Stop interval on input blur
            $pidInput.addEventListener( 'blur', ( e ) => {

                clearInterval( pidFocusInterval );

            } );

        }

        /**
         * Purchase code validate action
         * @param pid
         */
        function validateAction( pid ) {

            pidValidator( $pidHelper, pid ) ?
                $submitBtn.removeAttribute( 'disabled' ):
                $submitBtn.setAttribute( 'disabled', 'true' );

        }

        /**
         * Purchase code validator
         * @param $helper
         * @param pid
         *
         * @param window.mdpUngrabberUnity.translation.pidErrorElements
         * @param window.mdpUngrabberUnity.translation.pidErrorInvoice
         * @param window.mdpUngrabberUnity.translation.pidErrorOrder
         * @param window.mdpUngrabberUnity.translation.pidError
         * @returns {boolean}
         */
        function pidValidator( $helper, pid ) {

            pid = pid.trim();

            const envatoRegex = new RegExp('((\\d||\\w)\\w+){7}-(((\\d||\\w)\\w+){4}-){3}((\\d||\\w)\\w+){12}', 'g');
            if ( ( envatoRegex.test( pid ) && pid.length === 36 ) || pid.length === 0 ) {

                if ( sessionStorage.getItem( 'activationHelper' ) ) {
                    $helper.innerText = sessionStorage.getItem( 'activationHelper' );
                }
                $helper.removeAttribute( 'style' );
                return true;

            }

            const elementsRegex = new RegExp('((\\d||[A-Z])\\w+){10}', 'g');
            const invoiceRegex = new RegExp('[A-Z]{4}(\\d){8}', 'g');
            const orderRegex = new RegExp('(\\d){9}', 'g');
            const { translation } = window.mdpUngrabberUnity;

            if ( elementsRegex.test( pid ) && pid.length === 10 ) {

                if ( $helper.innerText !== translation.pidErrorElements ) {
                    showPIDError( $helper, translation.pidErrorElements );
                }
                return false;

            } else if ( invoiceRegex.test( pid ) && pid.length === 12 ) {

                if ( $helper.innerText !== translation.pidErrorInvoice ) {
                    showPIDError( $helper, translation.pidErrorInvoice );
                }
                return false;

            } else if ( orderRegex.test( pid ) && pid.length === 9 ) {

                if ( $helper.innerText !== translation.pidErrorOrder ) {
                    showPIDError( $helper, translation.pidErrorOrder );
                }
                return false;

            } else {

                if ( $helper.innerText !== translation.pidError ) {
                    showPIDError( $helper, translation.pidError );
                }
                return false;

            }

        }

        /**
         * Change purchase code helper text
         * @param $helper
         * @param text
         */
        function showPIDError( $helper, text ) {

            $helper.innerText = text;
            $helper.style.color = '#ff495c';
            $helper.style.fontSize = '14px';

        }

    } )

} ( jQuery ) );
