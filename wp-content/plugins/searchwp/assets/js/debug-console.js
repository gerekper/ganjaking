( function($) {

    'use strict';

    const app = {

        $el: {},

        settings: {
            startY: 0,
            resizerHeight: 0,
            minHeight: 100,
            pinnedPanelKey: 'swp-debug-panel-current',
            consoleHeightKey: 'swp-debug-console-height'
        },

        /**
         * Init.
         *
         * @since 4.2.9
         */
        init: () => {

            $( app.ready );
        },

        /**
         * Document ready
         *
         * @since 4.2.9
         */
        ready: () => {

            app.initElements();
            app.initSettings();
            app.registerEvents();
            app.runActions();
        },

        /**
         * Init dynamic settings.
         *
         * @since 4.2.9
         */
        initSettings: () => {

            const toolbarHeight     = $( '#wpadminbar' ).length ? $( '#wpadminbar' ).outerHeight() : 0;
            app.settings.maxHeight  = ( $( window ).height() - toolbarHeight );
            app.settings.bodyMargin = app.$el.body.css( 'margin-bottom' );
        },

        /**
         * Init elements.
         *
         * @since 4.2.9
         */
        initElements: () => {

            app.$el.body         = $( 'body' );
            app.$el.debugConsole = $( '#searchwp-debug-console-main' );
        },

        /**
         * Register events.
         *
         * @since 4.2.9
         */
        registerEvents: () => {

            $( '#wp-admin-bar-searchwp-debug' ).on( 'click', app.openConsoleLinkClicked );
            $( '.searchwp-console-close' ).on( 'click', app.closeConsoleLinkClicked );

            $( document ).on( 'mousedown touchstart', '#searchwp-console-header', app.resizeConsoleStart );

            $( '#searchwp-console-panels-nav button' ).on( 'click', app.navItemClicked );
            $( '#searchwp-console-panels-content a.swp-panel-link' ).on( 'click', app.navItemClicked );
        },

        /**
         * Run actions.
         *
         * @since 4.2.9
         */
        runActions: () => {

            app.showPinnedPanel();
        },

        /**
         * Callback for a WP Admin Bar menu link click.
         * Opens the console and highlights the first menu item.
         *
         * @since 4.2.9
         */
        openConsoleLinkClicked: ( event ) => {

            event.preventDefault();
            app.showPanel();
        },

        /**
         * Callback for a "close console" link (icon) click.
         *
         * @since 4.2.9
         */
        closeConsoleLinkClicked: () => {

            app.$el.debugConsole.removeClass( 'swp-show' ).height( '' ).width( '' );
            app.$el.body.css( 'margin-bottom', '' );

            localStorage.removeItem( app.settings.pinnedPanelKey );
        },

        /**
         * Callback for the navigation item click.
         *
         * @since 4.2.9
         */
        navItemClicked: (event ) => {

            event.preventDefault();

            const href = $( event.target ).attr( 'href' ) || $( event.target ).attr( 'data-swp-href' );
            app.showPanel( href );
        },

        /**
         * Start the console resizing (mousedown).
         *
         * @since 4.2.9
         */
        resizeConsoleStart: ( event ) => {

            // Prevent resizing on right button click.
            if ( event.button === 2 ) {
                return;
            }

            app.settings.resizerHeight = $( event.target ).outerHeight() - 1;
            app.settings.startY        = app.$el.debugConsole.outerHeight() + ( event.clientY || event.originalEvent.targetTouches[0].pageY );

            $( document ).on( 'mousemove touchmove', app.resizeConsole );
            $( document ).on( 'mouseup touchend', app.resizeConsoleStop );
        },

        /**
         * Resize the console (mousemove).
         *
         * @since 4.2.9
         */
        resizeConsole: ( event ) => {

            const h = ( app.settings.startY - event.clientY );

            if ( h >= app.settings.resizerHeight && h <= app.settings.maxHeight ) {
                app.$el.debugConsole.height( h );
                app.$el.body.css( 'margin-bottom', 'calc( ' + app.settings.bodyMargin + ' + ' + h + 'px )' );
            }
        },

        /**
         * Stop the console resizing (mouseup).
         *
         * @since 4.2.9
         */
        resizeConsoleStop: () => {

            $( document ).off( 'mousemove touchmove', app.resizeConsole );
            $( document ).off( 'mouseup touchend', app.resizeConsoleStop );

            localStorage.setItem( app.settings.consoleHeightKey, app.$el.debugConsole.height() );
        },

        /**
         * Set the console height (check local storage for the saved value).
         *
         * @since 4.2.9
         */
        setConsoleHeight: () => {

            let consoleHeight = localStorage.getItem( app.settings.consoleHeightKey );

            if ( ! consoleHeight ) {
                return; // Let CSS handle console height.
            }

            if ( consoleHeight < app.settings.minHeight ) {
                consoleHeight = app.settings.minHeight;
            }

            app.$el.debugConsole.height( consoleHeight );
        },

        /**
         * Select (highlight) a menu item and save its selector into local storage.
         *
         * @since 4.2.9
         */
        selectMenuItem: ( panelSelector ) => {

            const $consoleMenu = $( '#searchwp-console-panels-nav' );

            $consoleMenu.find( 'button' ).removeAttr( 'aria-selected' );
            $consoleMenu.find( 'li' ).removeClass( 'searchwp-panel-nav-current' );

            const $selectedMenu = $consoleMenu.find( '[data-swp-href="' + panelSelector + '"]' ).attr( 'aria-selected',true );

            if ( ! $selectedMenu.length ) {
                return;
            }

            const selectedMenuTop = $selectedMenu.position().top - 27;
            const menuHeight      = $consoleMenu.height();

            $selectedMenu.closest( '#searchwp-console-panels-nav > ul > li' ).addClass( 'searchwp-panel-nav-current' );

            const selectedMenuOffBottom = ( selectedMenuTop > ( menuHeight ) );
            const selectedMenuOffTop    = ( selectedMenuTop < 0 );
            const menuScroll            = $consoleMenu.scrollTop();

            if ( selectedMenuOffBottom || selectedMenuOffTop ) {
                $consoleMenu.scrollTop( selectedMenuTop + menuScroll - ( menuHeight / 2 ) + ( $selectedMenu.outerHeight() / 2 ) );
            }

            localStorage.setItem( app.settings.pinnedPanelKey, $selectedMenu.attr( 'data-swp-slug' ) );
        },

        /**
         * Show a specific console panel (open the console if needed).
         *
         * @since 4.2.9
         */
        showPanel: ( panelSelector ) => {

            app.setConsoleHeight();
            app.$el.body.css( 'margin-bottom', 'calc( ' + app.settings.bodyMargin + ' + ' + app.$el.debugConsole.height() + 'px )' );
            app.$el.debugConsole.addClass( 'swp-show' );

            $( '.searchwp-panel-content' ).removeClass( 'searchwp-panel-content-show' );
            $( '#searchwp-console-panels-content' ).scrollTop( 0 );

            let $panel = $( panelSelector );
            if ( ! $panel.length ) {
                $panel        = $( '#searchwp-console-panels-content .searchwp-panel-content' ).first();
                panelSelector = '#' + $panel.attr( 'id' );
            }
            $panel.addClass( 'searchwp-panel-content-show' ).focus();

            app.selectMenuItem( panelSelector );
        },

        /**
         * Show the pinned (saved) panel on page reload.
         *
         * @since 4.2.9
         */
        showPinnedPanel: () => {

            const pinnedPanel = localStorage.getItem( app.settings.pinnedPanelKey );

            if ( ! pinnedPanel ) {
                return;
            }

            const $menuItem = $( '[data-swp-slug="' + pinnedPanel + '"]' );

            if ( $menuItem.length ) {
                app.showPanel( $menuItem.attr( 'data-swp-href' ) );
            } else {
                app.showPanel();
            }
        },
    };

    app.init();

    window.searchwp = window.searchwp || {};

    window.searchwp.debugConsole = app;

}( jQuery ) );
