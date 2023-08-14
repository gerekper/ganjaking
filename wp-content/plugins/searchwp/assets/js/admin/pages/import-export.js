/* global _SEARCHWP */

( function($) {

    'use strict';

    const app = {

        /**
         * Init.
         *
         * @since 4.3.0
         */
        init: () => {

            $( app.ready );
        },

        /**
         * Document ready
         *
         * @since 4.3.0
         */
        ready: () => {

            app.events();
        },

        /**
         * Page events.
         *
         * @since 4.3.0
         */
        events: () => {

            const val = {
                engines:   _SEARCHWP.engines,
                settings:  _SEARCHWP.settings,
                stopwords: _SEARCHWP.stopwords,
                synonyms:  _SEARCHWP.synonyms,
            };

            $( '#swp-tools-export' ).val( JSON.stringify( val ) );

            $( '#swp-tools-export-items input' ).on( 'change', ( e ) => {
                const exportItem  = e.currentTarget.dataset.exportItem;
                val[ exportItem ] = e.currentTarget.checked ? _SEARCHWP[ exportItem ] : null;
                $( '#swp-tools-export' ).val( JSON.stringify( val ) );
            });

            $( '#swp-tools-export-copy' ).on( 'click', ( e ) => {
                e.preventDefault();
                const textarea = document.getElementById( 'swp-tools-export' );

                // Select the text field.
                textarea.select();
                textarea.setSelectionRange( 0, 99999 ); // For mobile devices.

                // Fallback to execCommand() if Clipboard API can't be used.
                if ( typeof navigator.clipboard === 'undefined' || ! window.isSecureContext ) {
                    document.execCommand('copy');
                    $('#swp-tools-export-copy').addClass('swp-button--completed');
                    setTimeout(
                        () => {
                            $('#swp-tools-export-copy').removeClass('swp-button--completed');
                        },
                        1500
                    );
                    return;
                }

                navigator.clipboard.writeText(textarea.value).then(function () {
                    $('#swp-tools-export-copy').addClass('swp-button--completed');
                    setTimeout(
                        () => {
                            $('#swp-tools-export-copy').removeClass('swp-button--completed');
                        },
                        1500
                    );
                }, function () {
                    $(e.currentTarget).after('<span class="swp-error-msg swp-text-red swp-b ">Error</span>');
                    setTimeout(
                        () => {
                            $(e.target).siblings('.swp-error-msg').remove();
                        },
                        1500
                    );
                });
            } );

            $( '#swp-tools-import-continue-btn' ).on( 'click', ( e ) => {
                e.preventDefault();

                $(e.target).closest('.swp-modal').find('.swp-modal--close').click();
                let json = '';
                try {
                    json = JSON.parse($( '#swp-tools-import' ).val());
                } catch(error) {
                    $( '#swp-tools-import-btn' ).after('<span class="swp-error-msg swp-text-red swp-b ">Error parsing JSON</span>');
                    setTimeout(
                        function () {
                            $( '#swp-tools-import-btn' ).siblings('.swp-error-msg').remove();
                        },
                        1500
                    );
                    return;
                }

                $('.swp-content-container button').attr('disabled','disabled');
                $('#swp-tools-import-btn').addClass('swp-button--processing');

                $.post(ajaxurl, {
                    _ajax_nonce: _SEARCHWP.nonce,
                    action: _SEARCHWP.prefix + 'import_settings',
                    settings: JSON.stringify( json )
                }, function(response) {
                    $('.swp-content-container button').removeAttr('disabled');
                    $('#swp-tools-import-btn').removeClass('swp-button--processing');

                    if (response.success) {
                        $('#swp-tools-import-btn').addClass('swp-button--completed');
                        setTimeout(
                            () => {
                                $('#swp-tools-import-btn').removeClass('swp-button--completed');
                            },
                            1500
                        );
                    } else {
                        console.error(response);
                        $( '#swp-tools-import-btn' ).after('<span class="swp-error-msg swp-text-red swp-b ">Import Error</span>');
                        setTimeout(
                            function () {
                                $( '#swp-tools-import-btn' ).siblings('.swp-error-msg').remove();
                            },
                            1500
                        );
                    }
                });
            } );
        },
    };

    app.init();

    window.searchwp = window.searchwp || {};

    window.searchwp.AdminImportExportPage = app;

}( jQuery ) );
