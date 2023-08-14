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
         * Plugin events.
         *
         * @since 4.3.2
         */
        events: () => {

            $( '.swp-toggle-checkbox' ).removeAttr( 'disabled' );

            $( '.swp-toggle-checkbox' ).on( 'change', (e) => {
                const $filters = e.target.closest('form').querySelector('.searchwp-form-advanced-filters');
                const $selects = $filters.querySelectorAll('select');
                if ( e.target.checked ) {
                    $filters.style.display = 'flex';
                    $selects.forEach(($item) => {
                        $item.disabled = false;
                    });
                } else {
                    $filters.style.display = 'none';
                    $selects.forEach(($item) => {
                        $item.disabled = true;
                    });
                }
            } );
        },
    };

    app.init();

    window.searchwp = window.searchwp || {};

    window.searchwp.searchForms = app;

}( jQuery ) );
