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

            app.synonymsEvents();
            app.stopwordsEvents();
            app.draggableEvents();
            app.actionMenuEvents();
        },

        /**
         * Synonyms events.
         *
         * @since 4.3.0
         */
        synonymsEvents: () => {

            // General buttons.
            $( '#swp-synonyms-save' ).on( 'click', app.synonymsSave );
            $( '#swp-synonyms-add-new-btn' ).on( 'click', app.synonymsAddNew );

            // Individual synonyms.
            $( document ).on( 'click', '.swp-synonym-delete', app.synonymDelete );

            // Menu actions.
            $( '#swp-synonyms-sort-asc' ).on( 'click', app.synonymsSortAsc );
            $( '#swp-synonyms-sort-desc' ).on( 'click', app.synonymsSortDesc );
            $( '#swp-synonyms-remove-all' ).on( 'click', app.synonymsRemoveAll );
        },

        /**
         * Callback for clicking "Save Synonyms" button.
         *
         * @since 4.3.0
         */
        synonymsSave: (e) => {

            e.preventDefault();

            const data = $( '#swp-synonyms .swp-synonym' ).map( ( el, v ) => {
                return {
                    sources: v.querySelector( '.swp-synonym-sources-input' ).value,
                    synonyms: v.querySelector( '.swp-synonym-synonyms-input' ).value,
                    replace: v.querySelector( '.swp-synonym-replace-checkbox' ).checked,
                };
            } ).get();

            $( '.swp-content-container button' ).attr( 'disabled','disabled' );
            $( '#swp-synonyms-save' ).addClass( 'swp-button--processing' );

            $.post(
                ajaxurl,
                {
                    _ajax_nonce: _SEARCHWP.nonce,
                    action: _SEARCHWP.prefix + 'synonyms_update',
                    synonyms: JSON.stringify( data )
                },
                app.synonymsSaveProcessResponse
            );
        },

        /**
         * Process response for the "Save Synonyms" button callback.
         *
         * @since 4.3.0
         */
        synonymsSaveProcessResponse: (response) => {

            $( '.swp-content-container button' ).removeAttr( 'disabled' );
            $( '#swp-synonyms-save' ).removeClass( 'swp-button--processing' );

            if ( response.success ) {
                $( '#swp-synonyms-save' ).addClass( 'swp-button--completed' );
                setTimeout( () => { $( '#swp-synonyms-save' ).removeClass( 'swp-button--completed' ) }, 1500 );
            }
        },

        /**
         * Callback for clicking "Add New Synonym" button.
         *
         * @since 4.3.0
         */
        synonymsAddNew: (e) => {

            e.preventDefault();

            $( '#swp-synonyms' ).append( $( '#swp-synonym-template' ).html() );
        },

        /**
         * Callback for clicking "Delete Synonym" button.
         *
         * @since 4.3.0
         */
        synonymDelete: (e) => {

            e.preventDefault();

            $( e.currentTarget ).closest( '.swp-synonym' ).remove();
        },

        /**
         * Callback for clicking "Sort ASC" synonyms actions menu item.
         *
         * @since 4.3.0
         */
        synonymsSortAsc: (e) => {

            const $el = $( '#swp-synonyms .swp-synonym' );

            $el.sort( function( a, b ) {
                const aVal = $( a ).find( '.swp-synonym-sources-input' ).val();
                const bVal = $( b ).find( '.swp-synonym-sources-input' ).val();
                if (aVal < bVal) {
                    return -1;
                }
                if ( aVal > bVal) {
                    return 1;
                }
                return 0;
            } ).appendTo( $el.parent() );
        },

        /**
         * Callback for clicking "Sort DESC" synonyms actions menu item.
         *
         * @since 4.3.0
         */
        synonymsSortDesc: (e) => {

            const $el = $( '#swp-synonyms .swp-synonym' );

            $el.sort( function( a, b ) {
                const aVal = $( a ).find( '.swp-synonym-sources-input' ).val();
                const bVal = $( b ).find( '.swp-synonym-sources-input' ).val();
                if (aVal > bVal) {
                    return -1;
                }
                if ( aVal < bVal) {
                    return 1;
                }
                return 0;
            } ).appendTo( $el.parent() );
        },

        /**
         * Callback for clicking "Remove All" synonyms actions menu item.
         *
         * @since 4.3.0
         */
        synonymsRemoveAll: (e) => {

            $( '#swp-synonyms .swp-synonym' ).remove();
        },

        /**
         * Stopwords events.
         *
         * @since 4.3.0
         */
        stopwordsEvents: () => {

            // General buttons.
            $( '#swp-stopwords-save' ).on( 'click', app.stopwordsSave );
            $( '#swp-stopwords-add-new-btn' ).on( 'click', app.stopwordsAddNew );

            // Menu actions.
            $( '#swp-stopwords-sort-asc' ).on( 'click', app.stopwordsSortAsc );
            $( '#swp-stopwords-restore-defaults' ).on( 'click', app.stopwordsRestoreDefaults );
            $( '#swp-stopwords-clear' ).on( 'click', app.stopwordsClear );

            // "Suggested stopwords" modal buttons.
            $('#swp-suggested-stopwords .swp-suggested-stopword-add').on( 'click', (e) => {
                e.preventDefault();
                const $stopword = $(e.target).closest('.swp-suggested-stopword');

                window.searchwp.Pills.add('#swp-stopwords', $stopword.find('.swp-suggested-stopword-name').text());
                $stopword.remove();
            });
        },

        /**
         * Callback for clicking "Save Stopwords" button.
         *
         * @since 4.3.0
         */
        stopwordsSave: (e) => {

            e.preventDefault();

            const stopwords = window.searchwp.Pills.get( '#swp-stopwords' );

            $( '.swp-content-container button' ).attr( 'disabled', 'disabled' );
            $( '#swp-stopwords-save' ).addClass( 'swp-button--processing' );

            $.post(
                ajaxurl,
                {
                    _ajax_nonce: _SEARCHWP.nonce,
                    action: _SEARCHWP.prefix + 'stopwords_update',
                    stopwords: JSON.stringify( stopwords ),
                },
                app.stopwordsSaveProcessResponse
            );
        },

        /**
         * Process response for the "Save Stopwords" button callback.
         *
         * @since 4.3.0
         */
        stopwordsSaveProcessResponse: (response) => {

            $( '.swp-content-container button' ).removeAttr( 'disabled' );
            $( '#swp-stopwords-save' ).removeClass( 'swp-button--processing' );

            if ( response.success ) {
                $( '#swp-stopwords-save' ).addClass( 'swp-button--completed' );
                setTimeout( () => { $( '#swp-stopwords-save' ).removeClass( 'swp-button--completed' ) }, 1500 );
            }
        },

        /**
         * Callback for clicking "Add New Stopword" button.
         *
         * @since 4.3.0
         */
        stopwordsAddNew: (e) => {

            e.preventDefault();

            $( '#swp-stopwords .swp-pills-input' ).focus();
        },

        /**
         * Callback for clicking "Sort Alphabetically" stopwords actions menu item.
         *
         * @since 4.3.0
         */
        stopwordsSortAsc: (e) => {

            window.searchwp.Pills.sort( '#swp-stopwords' );
        },

        /**
         * Callback for clicking "Restore Defaults" stopwords actions menu item.
         *
         * @since 4.3.0
         */
        stopwordsRestoreDefaults: (e) => {

            window.searchwp.Pills.clear( '#swp-stopwords' );
            window.searchwp.Pills.add( '#swp-stopwords', _SEARCHWP.stopwords.defaults );
        },

        /**
         * Callback for clicking "Clear Stopwords" stopwords actions menu item.
         *
         * @since 4.3.0
         */
        stopwordsClear: (e) => {

            window.searchwp.Pills.clear( '#swp-stopwords' );
        },

        /**
         * Draggable events.
         *
         * @since 4.3.0
         */
        draggableEvents: () => {

            function handleMouseDown(e) {
                $( e.target ).closest( '.swp-synonym' ).attr( 'draggable', true );
            }
            function handleMouseUp(e) {
                $( e.target ).closest( '.swp-synonym' ).removeAttr( 'draggable' );
            }

            function handleDragStart(e) {

                const $synonym = $( e.target ).closest( '.swp-synonym' );
                $synonym.css( 'opacity', '0.4' );

                $synonym.find( 'input' ).each( ( i, input ) => {
                    const $input = $( input );
                    if ( $input.is( ':checkbox' ) ) {
                        $input.attr( 'checked', $input.is( ':checked' ) );
                    } else {
                        $input.attr( 'value', $input.val() );
                    }
                } );

                $srcSynonym = $synonym;

                e.originalEvent.dataTransfer.dropEffect = 'move';
            }

            function handleDragOver(e) {
                // Prevent default to allow drop.
                e.preventDefault();
            }

            function handleDragEnter(e) {

                const $synonym = $( e.target ).closest( '.swp-synonym' );

                counter++;

                if ( $synonym.length && ! $synonym.hasClass( 'over' ) ) {
                    $synonym.addClass( 'over' );

                    if ( $srcSynonym && ! $srcSynonym.is( $synonym ) ) {
                        const median = $synonym.offset().top + ( $synonym.height() / 2 );
                        if (e.originalEvent.pageY < median ) {
                            $synonym.after( $srcSynonym );
                        } else {
                            $synonym.before( $srcSynonym );
                        }
                        $synonym.removeClass( 'over' );
                    }
                }
            }


            function handleDragLeave(e) {
                const $synonym = $( e.target ).closest( '.swp-synonym' );
                counter--;
                if ($synonym.length && counter === 0) {
                    $synonym.removeClass( 'over' );
                }
            }

            function handleDrop(e) {
                // Prevent default action (open as link for some elements).
                e.preventDefault();
            }

            function handleDragEnd(e) {
                $( '#swp-synonyms .swp-synonym' ).removeClass( 'over' ).css( 'opacity', '1' ).removeAttr( 'draggable' );
            }

            let $srcSynonym;
            let counter = 0;

            $( document ).on( 'mousedown', '#swp-synonyms .swp-dragsort-handle', handleMouseDown );
            $( document ).on( 'mouseup', '#swp-synonyms .swp-dragsort-handle', handleMouseUp );

            $( document ).on( 'dragstart', '#swp-synonyms .swp-synonym', handleDragStart );
            $( document ).on( 'dragover', '#swp-synonyms .swp-synonym', handleDragOver );
            $( document ).on( 'dragenter', '#swp-synonyms .swp-synonym', handleDragEnter );
            $( document ).on( 'dragleave', '#swp-synonyms .swp-synonym', handleDragLeave );
            $( document ).on( 'dragend', '#swp-synonyms .swp-synonym', handleDragEnd );
            $( document ).on( 'drop', '#swp-synonyms .swp-synonym', handleDrop );
        },

        /**
         * Synonyms/stopwords action menu events.
         *
         * @since 4.3.0
         */
        actionMenuEvents: () => {

            $( '.swp-action-menu--button' ).on( 'click', (e) => {
                e.stopPropagation();
                const $menu = $( e.currentTarget ).siblings( '.swp-swp-action-menu--list' );
                if ( $menu.hasClass( 'swp-display-none' ) ) {
                    $menu.removeClass( 'swp-display-none' ).addClass( 'swp-display-block' );
                    return;
                }
                if ( $menu.hasClass( 'swp-display-block' ) ) {
                    $menu.removeClass( 'swp-display-block' ).addClass( 'swp-display-none' );
                    return;
                }
            } );

            $( 'body' ).on( 'click', (e) => {
                const $menu = $( '.swp-swp-action-menu--list.swp-display-block' );
                if ( $menu.length ) {
                    $menu.removeClass( 'swp-display-block' ).addClass( 'swp-display-none' );
                }
            } );

            $( '.swp-action-menu--item' ).on( 'click', (e) => {
                $( e.currentTarget ).closest( '.swp-swp-action-menu--list' ).removeClass( 'swp-display-block' ).addClass( 'swp-display-none' );
            } );
        },
    };

    app.init();

    window.searchwp = window.searchwp || {};

    window.searchwp.AdminGlobalRulesPage = app;

}( jQuery ) );
