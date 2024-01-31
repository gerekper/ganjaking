/* global _SEARCHWP */

( function() {

    'use strict';

    const app = {

        /**
         * Init.
         *
         * @since 4.3.6
         */
        init: () => {

            if ( document.readyState === 'loading' ) {
                document.addEventListener( 'DOMContentLoaded', app.ready );
                return;
            }

            app.ready();
        },

        /**
         * Document ready
         *
         * @since 4.3.6
         */
        ready: () => {

            app.events();
        },

        /**
         * Events.
         *
         * @since 4.3.6
         */
        events: () => {

            document.querySelectorAll( 'select.swp-choicesjs-single' ).forEach( app.initChoicesSingle );
            document.querySelectorAll( 'select.swp-choicesjs-multiple' ).forEach( app.initChoicesMultiple );
            document.querySelectorAll( 'select.swp-choicesjs-hybrid' ).forEach( app.initChoicesHybrid );
        },

        /**
         * Init single choice select.
         *
         * @since 4.3.6
         */
        initChoicesSingle: ( el ) => {

            if ( typeof Choices === 'undefined' ) {
                return;
            }

            const args = {
                searchEnabled: false,
                shouldSort: false,
            };

            // Attach the Choices object to an element for easy access.
            el.data = el.data || {};
            el.data.choicesjs = new Choices( el, args );
        },

        /**
         * Init searchable multiple choice select.
         *
         * @since 4.3.6
         */
        initChoicesMultiple: ( el ) => {

            if ( typeof Choices === 'undefined' ) {
                return;
            }

            const args = {
                removeItemButton: true,
                duplicateItemsAllowed: false,
            };

            const choices = new Choices( el, args );

            // This makes the input element take as little space as possible.
            choices.clearInput();

            // Attach the Choices object to an element for easy access.
            el.data = el.data || {};
            el.data.choicesjs = choices;
        },

        /**
         * Init hybrid searchable multiple choice select with an ability to add new items by pressing Enter.
         *
         * @since 4.3.6
         */
        initChoicesHybrid: ( el ) => {

            if ( typeof Choices === 'undefined' ) {
                return;
            }

            const args = {
                removeItemButton: true,
                duplicateItemsAllowed: false,
                noResultsText: 'Press Enter to add item',
                noChoicesText: 'Type to add new items',
                fuseOptions: {
                    threshold: 0,
                },
            };

            const choices = new Choices( el, args );

            // This makes the input element take as little space as possible.
            choices.clearInput();

            const keyupChoiceHybridCallback = ( e ) => {
                if ( e.keyCode === 13 ) {
                    app.addChoiceToHybrid( choices );
                }
            };

            choices.input.element.addEventListener( 'keyup', keyupChoiceHybridCallback, false );

            // Attach the Choices object to an element for easy access.
            el.data = el.data || {};
            el.data.choicesjs = choices;
        },

        /**
         * Add new item to a hybrid select.
         *
         * @since 4.3.6
         *
         * @param choices Choices.js object.
         */
        addChoiceToHybrid: ( choices ) => {

            if ( ! choices.input.value ) {
                return;
            }

            const canAddItem = choices._canAddItem( choices._store.activeItems, choices.input.value );

            if ( ! canAddItem.response ) {
                const notice = choices._getTemplate( 'notice', canAddItem.notice );
                choices.choiceList.clear();
                choices.choiceList.append( notice );
                return;
            }

            const choice = {
                "value": choices.input.value,
                "isSelected": true,
            }

            choices._addChoice( choice );

            choices.clearInput();
        },
    };

    app.init();

    window.searchwp = window.searchwp || {};

    window.searchwp.ChoicesJs = window.searchwp.ChoicesJs || app;

})();
