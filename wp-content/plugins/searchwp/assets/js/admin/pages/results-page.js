/* global _SEARCHWP */

( function($) {

    'use strict';

    const app = {

        /**
         * Init.
         *
         * @since 4.3.6
         */
        init: () => {

            $( app.ready );
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
         * Page events.
         *
         * @since 4.3.6
         */
        events: () => {

            app.UIEvents();

            $( '#swp-results-page-save' ).on( 'click', app.saveSettings );
        },

        /**
         * Save page settings.
         *
         * @since 4.3.6
         */
        saveSettings: () => {

            const settings = {
                'swp-layout-theme': $( 'input[name=swp-layout-theme]:checked' ).val(),
                'swp-layout-style': $( 'input[name=swp-layout-style]:checked' ).val(),
                'swp-results-per-row': $( 'select[name=swp-results-per-row]' ).val(),
                'swp-image-size': $( 'select[name=swp-image-size]' ).val(),
                'swp-title-color': $( 'input[name=swp-title-color]' ).val(),
                'swp-title-font-size': $( 'input[name=swp-title-font-size]' ).val(),
                'swp-price-color': $( 'input[name=swp-price-color]' ).val(),
                'swp-price-font-size': $( 'input[name=swp-price-font-size]' ).val(),
                'swp-description-enabled': $( 'input[name=swp-description-enabled]' ).is( ':checked' ),
                'swp-button-enabled': $( 'input[name=swp-button-enabled]' ).is( ':checked' ),
                'swp-button-label': $( 'input[name=swp-button-label]' ).val(),
                'swp-button-bg-color': $( 'input[name=swp-button-bg-color]' ).val(),
                'swp-button-font-color': $( 'input[name=swp-button-font-color]' ).val(),
                'swp-button-font-size': $( 'input[name=swp-button-font-size]' ).val(),
                'swp-results-per-page': $( 'input[name=swp-results-per-page]' ).val(),
                'swp-pagination-style': $( 'input[name=swp-pagination-style]:checked' ).val(),
            };

            const data = {
                _ajax_nonce: _SEARCHWP.nonce,
                action: _SEARCHWP.prefix + 'save_results_page_settings',
                settings: JSON.stringify( settings ),
            };

            const $enabledInputs = $( '.swp-content-container button:not([disabled]), .swp-content-container input:not([disabled])' );
            const $saveButton    = $( '#swp-results-page-save' );

            $enabledInputs.attr( 'disabled','disabled' );
            $saveButton.addClass( 'swp-button--processing' );

            $.post( ajaxurl, data, ( response ) => {
                $enabledInputs.removeAttr( 'disabled' );
                $saveButton.removeClass( 'swp-button--processing' );

                if ( response.success ) {
                    $saveButton.addClass( 'swp-button--completed' );
                    setTimeout( () => { $saveButton.removeClass( 'swp-button--completed' ) }, 1500 );
                }
            } );
        },

        /**
         * Page UI events.
         *
         * @since 4.3.6
         */
        UIEvents: () => {

            $('[name="swp-layout-theme"]').on('change', (e) => {

                const theme = e.target.value;

                const $layout = $( 'input[name=swp-layout-style]' );
                const imageSizeChoicesJs = document.querySelector( 'select[name=swp-image-size]' ).data.choicesjs;
                const perRowChoicesJs = document.querySelector( 'select[name=swp-results-per-row]' ).data.choicesjs;

                const $preview = $('.swp-rp-theme-preview > div');
                const $perRowBlock = $('#swp-results-per-row-block');

                console.log( );

                if ( theme === 'alpha' ) {
                    $layout.filter( '[value=list]' ).prop( 'checked',true );
                    imageSizeChoicesJs.setChoiceByValue( '' );
                    $preview
                        .removeClass('swp-grid')
                        .addClass('swp-flex');
                    $preview
                        .removeClass('swp-rp--img-sm swp-rp--img-m swp-rp--img-l')
                        .addClass('swp-result-item--img--off');
                    $perRowBlock.hide();
                }

                if ( theme === 'beta' ) {
                    $layout.filter( '[value=list]' ).prop( 'checked',true );
                    imageSizeChoicesJs.setChoiceByValue( 'small' );
                    $preview
                        .removeClass('swp-grid')
                        .addClass('swp-flex');
                    $preview
                        .removeClass('swp-result-item--img--off swp-rp--img-sm swp-rp--img-m swp-rp--img-l')
                        .addClass('swp-rp--img-sm');
                    $perRowBlock.hide();
                }

                if ( theme === 'gamma' ) {
                    $layout.filter( '[value=grid]' ).prop( 'checked',true );
                    imageSizeChoicesJs.setChoiceByValue( 'small' );
                    perRowChoicesJs.setChoiceByValue( '3' );
                    $preview
                        .removeClass('swp-flex')
                        .addClass('swp-grid');
                    $preview
                        .removeClass('swp-grid--cols-2 swp-grid--cols-4 swp-grid--cols-5 swp-grid--cols-6 swp-grid--cols-7')
                        .addClass('swp-grid--cols-3');
                    $preview
                        .removeClass('swp-result-item--img--off swp-rp--img-sm swp-rp--img-m swp-rp--img-l')
                        .addClass('swp-rp--img-sm');
                    $perRowBlock.show();
                }

                if ( theme === 'epsilon' ) {
                    $layout.filter( '[value=list]' ).prop( 'checked',true );
                    imageSizeChoicesJs.setChoiceByValue( 'medium' );
                    $preview
                        .removeClass('swp-grid')
                        .addClass('swp-flex');
                    $preview
                        .removeClass('swp-result-item--img--off swp-rp--img-sm swp-rp--img-m swp-rp--img-l')
                        .addClass('swp-rp--img-m');
                    $perRowBlock.hide();
                }

                if ( theme === 'zeta' ) {
                    $layout.filter( '[value=grid]' ).prop( 'checked',true );
                    imageSizeChoicesJs.setChoiceByValue( 'large' );
                    perRowChoicesJs.setChoiceByValue( '3' );
                    $preview
                        .removeClass('swp-flex')
                        .addClass('swp-grid');
                    $preview
                        .removeClass('swp-grid--cols-2 swp-grid--cols-4 swp-grid--cols-5 swp-grid--cols-6 swp-grid--cols-7')
                        .addClass('swp-grid--cols-3');
                    $preview
                        .removeClass('swp-result-item--img--off swp-rp--img-sm swp-rp--img-m')
                        .addClass('swp-rp--img-l');
                    $perRowBlock.show();
                }

                if ( theme === 'combined' ) {
                    $layout.filter( '[value=list]' ).prop( 'checked',true );
                    imageSizeChoicesJs.setChoiceByValue( 'large' );
                    $preview
                        .removeClass('swp-grid')
                        .addClass('swp-flex');
                    $preview
                        .removeClass('swp-result-item--img--off swp-rp--img-sm swp-rp--img-m swp-rp--img-l')
                        .addClass('swp-rp--img-l');
                    $perRowBlock.show();
                }
            });

            $('[name=swp-layout-style]').on('change', (e) => {

                const layout = e.target.value;
                const $preview = $('.swp-rp-theme-preview > div');
                const $perRowBlock = $('#swp-results-per-row-block');

                $( '[name="swp-layout-theme"][value="combined"]' ).prop( 'checked', true );

                if ( layout === 'list' ) {
                    $preview
                        .removeClass('swp-grid')
                        .addClass('swp-flex');
                    $perRowBlock.hide();
                }

                if ( layout === 'grid' ) {
                    $preview
                        .removeClass('swp-flex')
                        .addClass('swp-grid');
                    $perRowBlock.show();
                }
            });

            $('[name=swp-results-per-row]').on('change', (e) => {

                const $preview = $('.swp-rp-theme-preview > div');
                let perRow = parseInt( e.target.value, 10 );
                if ( ! perRow ) {
                    perRow = 3;
                }

                $( '[name="swp-layout-theme"][value="combined"]' ).prop( 'checked', true );

                $preview
                    .removeClass('swp-grid--cols-2 swp-grid--cols-3 swp-grid--cols-4 swp-grid--cols-5 swp-grid--cols-6 swp-grid--cols-7')
                    .addClass(`swp-grid--cols-${perRow}`);
            });

            $('[name=swp-image-size]').on('change', (e) => {

                const imageSize = e.target.value;
                const $preview = $('.swp-rp-theme-preview > div');

                $( '[name="swp-layout-theme"][value="combined"]' ).prop( 'checked', true );

                if ( ! imageSize ) {
                    $preview
                        .removeClass('swp-rp--img-sm swp-rp--img-m swp-rp--img-l')
                        .addClass('swp-result-item--img--off');
                }

                if ( imageSize === 'small' ) {
                    $preview
                        .removeClass('swp-result-item--img--off swp-rp--img-m swp-rp--img-l')
                        .addClass('swp-rp--img-sm');
                }

                if ( imageSize === 'medium' ) {
                    $preview
                        .removeClass('swp-result-item--img--off swp-rp--img-sm swp-rp--img-l')
                        .addClass('swp-rp--img-m');
                }

                if ( imageSize === 'large' ) {
                    $preview
                        .removeClass('swp-result-item--img--off swp-rp--img-sm swp-rp--img-m')
                        .addClass('swp-rp--img-l');
                }
            });

            $('[name="swp-description-enabled"]').on('change', (e) => {
                const descriptionEnabled = e.target.checked;
                const descriptionPreview = $('.swp-result-item--desc');

                if ( descriptionEnabled ) {
                    descriptionPreview.show();
                } else {
                    descriptionPreview.hide();
                }
            });

            $('[name="swp-button-enabled"]').on('change', (e) => {
                const buttonEnabled = e.target.checked;
                const buttonPreview = $('.swp-result-item--button');

                if ( buttonEnabled ) {
                    buttonPreview.show();
                } else {
                    buttonPreview.hide();
                }
            });

            $('[name="swp-pagination-style"]').on('change', (e) => {
                const style = e.target.value;
                const container = $( '.swp-results-pagination > ul' );

                if ( style === 'circular' ) {
                    container
                        .removeClass('swp-results-pagination--boxed swp-results-pagination--noboxed')
                        .addClass('swp-results-pagination--circular');
                }

                if ( style === 'boxed' ) {
                    container
                        .removeClass('swp-results-pagination--circular swp-results-pagination--noboxed')
                        .addClass('swp-results-pagination--boxed');
                }

                if ( style === 'noboxed' ) {
                    container
                        .removeClass('swp-results-pagination--boxed swp-results-pagination--circular')
                        .addClass('swp-results-pagination--noboxed');
                }
            });
        },
    };

    app.init();

    window.searchwp = window.searchwp || {};

    window.searchwp.AdminSearchResultsPage = app;

}( jQuery ) );
