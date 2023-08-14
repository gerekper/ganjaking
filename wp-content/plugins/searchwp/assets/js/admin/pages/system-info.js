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

            $( '#swp-tools-system-info-copy' ).on( 'click', app.copySystemInfo );
        },

        /**
         * Copy System Info textarea content to the clipboard.
         *
         * @since 4.3.2
         */
        copySystemInfo: function(e) {
            e.preventDefault();
            const textarea = document.getElementById( 'swp-tools-system-info' );

            // Select the text field.
            textarea.select();
            textarea.setSelectionRange( 0, 99999 ); // For mobile devices.

            // Fallback to execCommand() if Clipboard API can't be used.
            if ( typeof navigator.clipboard === 'undefined' || ! window.isSecureContext ) {
                document.execCommand('copy');
                $('#swp-tools-system-info-copy').addClass('swp-button--completed');
                setTimeout(
                    () => {
                        $('#swp-tools-system-info-copy').removeClass('swp-button--completed');
                    },
                    1500
                );
                return;
            }

            navigator.clipboard.writeText(textarea.value).then(function () {
                $('#swp-tools-system-info-copy').addClass('swp-button--completed');
                setTimeout(
                    () => {
                        $('#swp-tools-system-info-copy').removeClass('swp-button--completed');
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
        }
    };

    app.init();

    window.searchwp = window.searchwp || {};

    window.searchwp.AdminSystemInfoPage = app;

}( jQuery ) );
