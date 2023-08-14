/* global _SEARCHWP */

( function($) {

    'use strict';

    const app = {

        /**
         * Init.
         *
         * @since 4.3.2
         */
        init: () => {

            $( app.ready );
        },

        /**
         * Document ready
         *
         * @since 4.3.2
         */
        ready: () => {

            app.events();
        },

        /**
         * Extension page events.
         *
         * @since 4.3.2
         */
        events: () => {

            $( document ).on( 'click', '[data-swp-copy-from]', app.copyInputText );
        },

        /**
         * Copy text input content to the clipboard.
         *
         * @since 4.3.2
         */
        copyInputText: function(e) {

            e.preventDefault();
            const textInput = document.querySelector( e.target.dataset.swpCopyFrom );

            if ( ! textInput ) {
                return;
            }

            // Select the text input content.
            textInput.select();
            textInput.setSelectionRange( 0, 99999 ); // For mobile devices.

            // Fallback to execCommand() if Clipboard API can't be used.
            if ( typeof navigator.clipboard === 'undefined' || ! window.isSecureContext ) {
                document.execCommand('copy');
                $(e.target).addClass('swp-button--completed');
                setTimeout(
                    () => {
                        $(e.target).removeClass('swp-button--completed');
                    },
                    1500
                );
                return;
            }

            navigator.clipboard.writeText(textInput.value).then(function () {
                $(e.target).addClass('swp-button--completed');
                setTimeout(
                    () => {
                        $(e.target).removeClass('swp-button--completed');
                    },
                    1500
                );
            }, function () {
                $(e.target).after('<span class="swp-error-msg swp-text-red swp-b ">Error</span>');
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

    window.searchwp.CopyInputText = app;

}( jQuery ) );
