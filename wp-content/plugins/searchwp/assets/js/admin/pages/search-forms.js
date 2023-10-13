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
         * Page events.
         *
         * @since 4.3.2
         */
        events: () => {

            app.initExistingPageEmbedSelect();

            $( '#swp-form-save' ).on( 'click', app.saveSettings );

            $('.swp-sf--edit-header--icon').on( 'click', (e) => {
                const $title = $(e.currentTarget).parent();
                $title.siblings('input').show();
                $title.hide();
            } );

            $( '[name="swp-sfe-embed"]' ).on( 'change', (e) => {
                $('.swp-sfe-embed--desc').hide();
                $( `#${e.target.value}` ).show();
            } );

            $( '.swp-search-form-embed-modal-go-btn' ).on( 'click', app.embedPageRedirect )

            app.UIEvents();
        },

        /**
         * Save form settings.
         *
         * @since 4.3.2
         */
        saveSettings: () => {

            const settings = {
                'title': $( 'input[name=title]' ).val(),
                'swp-layout-theme': $( 'input[name=swp-layout-theme]:checked' ).val(),
                'category-search': $( 'input[name=category-search]' ).is( ':checked' ),
                'quick-search': $( 'input[name=quick-search]' ).is( ':checked' ),
                'advanced-search': $( 'input[name=advanced-search]' ).is( ':checked' ),
                'engine': $( 'select[name=engine]' ).val(),
                'input_name': $( 'select[name=input_name]' ).val(),
                'post-type': $( 'select[name=post-type]' ).val(),
                'category': $( 'select[name=category]' ).val(),
                'field-label': $( 'input[name=field-label]' ).val(),
                'search-button': $( 'input[name=search-button]' ).is( ':checked' ),
                'quick-search-items': $( 'select[name=quick-search-items]' ).val(),
                'advanced-search-filters': $( 'select[name=advanced-search-filters]' ).val(),
                'swp-sfinput-shape': $( 'input[name=swp-sfinput-shape]:checked' ).val(),
                'swp-sfbutton-filled': $( 'input[name=swp-sfbutton-filled]:checked' ).val(),
                'search-form-color': $( 'input[name=search-form-color]' ).val(),
                'search-form-font-size': $( 'input[name=search-form-font-size]' ).val(),
                'button-background-color': $( 'input[name=button-background-color]' ).val(),
                'button-label': $( 'input[name=button-label]' ).val(),
                'button-font-color': $( 'input[name=button-font-color]' ).val(),
                'button-font-size': $( 'input[name=button-font-size]' ).val(),
            };

            const $saveButton = $( '#swp-form-save' );

            const data = {
                _ajax_nonce: _SEARCHWP.nonce,
                action: _SEARCHWP.prefix + 'save_form_settings',
                form_id: $saveButton.data( 'form-id' ),
                settings: JSON.stringify( settings ),
            };

            const $enabledInputs = $( '.swp-content-container button:not([disabled]), .swp-content-container input:not([disabled])' );

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
         * @since 4.3.2
         */
        UIEvents: () => {

            $('[name="swp-layout-theme"]').on('change', (e) => {

                const theme = e.target.value;

                const $category = $( 'input[name=category-search]' );
                const $quick = $( 'input[name=quick-search]' );
                const $advanced = $( 'input[name=advanced-search]' );

                const $categoryPreview = $( '#swp-sf--theme-preview-category' );
                const $quickPreview = $( '#swp-sf--theme-preview-quick' );
                const $advancedPreview = $( '#swp-sf--theme-preview-advanced' );

                if ( theme === 'basic' ) {
                    $category.prop( 'checked', false );
                    $quick.prop( 'checked', false );
                    $advanced.prop( 'checked', false );

                    $categoryPreview.hide();
                    $quickPreview.hide();
                    $advancedPreview.hide();
                }

                if ( theme === 'category' ) {
                    $category.prop( 'checked', true );
                    $quick.prop( 'checked', false );
                    $advanced.prop( 'checked', false );

                    $categoryPreview.show();
                    $quickPreview.hide();
                    $advancedPreview.hide();
                }

                if ( theme === 'quick' ) {
                    $category.prop( 'checked', false );
                    $quick.prop( 'checked', true );
                    $advanced.prop( 'checked', false );

                    $categoryPreview.hide();
                    $quickPreview.show();
                    $advancedPreview.hide();
                }

                if ( theme === 'advanced' ) {
                    $category.prop( 'checked', false );
                    $quick.prop( 'checked', false );
                    $advanced.prop( 'checked', true );

                    $categoryPreview.hide();
                    $quickPreview.hide();
                    $advancedPreview.show();
                }

                if ( theme === 'combined' ) {
                    $category.prop( 'checked', true );
                    $quick.prop( 'checked', true );
                    $advanced.prop( 'checked', true );

                    $categoryPreview.show();
                    $quickPreview.show();
                    $advancedPreview.show();
                }
            });

            $('input[name=category-search]').on('change', (e) => {

                const categoryEnabled = e.target.checked;
                const quickEnabled = $( 'input[name=quick-search]' ).is(':checked');
                const advancedEnabled = $( 'input[name=advanced-search]' ).is(':checked');

                const $categoryPreview = $( '#swp-sf--theme-preview-category' );

                if ( categoryEnabled ) {
                    $categoryPreview.show();
                } else {
                    $categoryPreview.hide();
                }

                if ( categoryEnabled ) {
                    if ( quickEnabled || advancedEnabled ) {
                        $('[name="swp-layout-theme"][value="combined"]').prop( 'checked', true );
                    } else {
                        $('[name="swp-layout-theme"][value="category"]').prop( 'checked', true );
                    }
                } else {
                    if ( quickEnabled || advancedEnabled ) {
                        if ( quickEnabled && ! advancedEnabled ) {
                            $('[name="swp-layout-theme"][value="quick"]').prop( 'checked', true );
                        }
                        if ( ! quickEnabled && advancedEnabled ) {
                            $('[name="swp-layout-theme"][value="advanced"]').prop( 'checked', true );
                        }
                    } else {
                        $('[name="swp-layout-theme"][value="basic"]').prop( 'checked', true );
                    }
                }
            });

            $('input[name=quick-search]').on('change', (e) => {

                const categoryEnabled = $( 'input[name=category-search]' ).is(':checked');
                const quickEnabled = e.target.checked;
                const advancedEnabled = $( 'input[name=advanced-search]' ).is(':checked');

                const $quickPreview = $( '#swp-sf--theme-preview-quick' );

                if ( quickEnabled ) {
                    $quickPreview.show();
                } else {
                    $quickPreview.hide();
                }

                if ( quickEnabled ) {
                    if ( categoryEnabled || advancedEnabled ) {
                        $('[name="swp-layout-theme"][value="combined"]').prop( 'checked', true );
                    } else {
                        $('[name="swp-layout-theme"][value="quick"]').prop( 'checked', true );
                    }
                } else {
                    if ( categoryEnabled || advancedEnabled ) {
                        if ( categoryEnabled && ! advancedEnabled ) {
                            $('[name="swp-layout-theme"][value="category"]').prop( 'checked', true );
                        }
                        if ( ! categoryEnabled && advancedEnabled ) {
                            $('[name="swp-layout-theme"][value="advanced"]').prop( 'checked', true );
                        }
                    } else {
                        $('[name="swp-layout-theme"][value="basic"]').prop( 'checked', true );
                    }
                }
            });

            $('input[name=advanced-search]').on('change', (e) => {

                const categoryEnabled = $( 'input[name=category-search]' ).is(':checked');
                const quickEnabled = $( 'input[name=quick-search]' ).is(':checked');
                const advancedEnabled = e.target.checked;

                const $advancedPreview = $( '#swp-sf--theme-preview-advanced' );

                if ( advancedEnabled ) {
                    $advancedPreview.show();
                } else {
                    $advancedPreview.hide();
                }

                if ( advancedEnabled ) {
                    if ( categoryEnabled || quickEnabled ) {
                        $('[name="swp-layout-theme"][value="combined"]').prop( 'checked', true );
                    } else {
                        $('[name="swp-layout-theme"][value="advanced"]').prop( 'checked', true );
                    }
                } else {
                    if ( categoryEnabled || quickEnabled ) {
                        if ( categoryEnabled && ! quickEnabled ) {
                            $('[name="swp-layout-theme"][value="category"]').prop( 'checked', true );
                        }
                        if ( ! categoryEnabled && quickEnabled ) {
                            $('[name="swp-layout-theme"][value="quick"]').prop( 'checked', true );
                        }
                    } else {
                        $('[name="swp-layout-theme"][value="basic"]').prop( 'checked', true );
                    }
                }
            });

            $('input[name=search-button]').on('change', (e) => {

                const buttonEnabled = e.target.checked;

                const $buttonPreview = $( '#swp-sf--theme-preview-button' );

                if ( buttonEnabled ) {
                    $buttonPreview.show();
                } else {
                    $buttonPreview.hide();
                }
            });
        },

        /**
         * Display "Loading" in ChoicesJS instance.
         *
         * @since 4.3.2
         *
         * @param {Choices} choicesJS ChoicesJS instance.
         */
        displayLoading: function( choicesJS ) {

            const loadingText = 'Loading';

            choicesJS.setChoices(
                [
                    { value: '', label: `${loadingText}...`, disabled: true },
                ],
                'value',
                'label',
                true
            );
        },

        /**
         * Perform AJAX search request.
         *
         * @since 4.3.2
         *
         * @param {string} action     Action to be used when doing ajax request for search.
         * @param {string} searchTerm Search term.
         * @param {string} nonce      Nonce to be used when doing ajax request.
         *
         * @returns {Promise} jQuery ajax call promise.
         */
        ajaxSearchPages: function( action, searchTerm, nonce ) {

            return $.get(
                ajaxurl,
                {
                    action: action,
                    search: searchTerm,
                    _wpnonce: nonce,
                }
            ).fail(
                function( err ) {
                    console.error( err );
                }
            );
        },

        /**
         * Perform search in ChoicesJS instance.
         *
         * @since 4.3.2
         *
         * @param {Choices} choicesJS  ChoicesJS instance.
         * @param {string}  searchTerm Search term.
         * @param {object}  ajaxArgs   Object containing `action` and `nonce` to perform AJAX search.
         */
        performSearch: function( choicesJS, searchTerm, ajaxArgs ) {

            if ( ! ajaxArgs.action || ! ajaxArgs.nonce ) {
                return;
            }

            app.displayLoading( choicesJS );

            const requestSearchPages = app.ajaxSearchPages( ajaxArgs.action, searchTerm, ajaxArgs.nonce );

            requestSearchPages.done( function( response ) {
                choicesJS.setChoices( response.data, 'value', 'label', true );
            } );
        },

        /**
         * Init "Existing Page" select inside the Embed modal.
         *
         * @since 4.3.2
         */
        initExistingPageEmbedSelect: () => {

            const el = document.getElementById('swp-search-form-embed-existing-page-modal-select');

            if ( ! el ) {
                return;
            }

            const choices = new Choices( el );

            if ( ! el.dataset.useAjax ) {
                return;
            }

            const ajaxArgs = {
                action: 'searchwp_admin_form_embed_wizard_search_pages_choicesjs',
                nonce: _SEARCHWP.nonce,
            };

            /*
             * ChoicesJS doesn't handle empty string search with it's `search` event handler,
             * so we work around it by detecting empty string search with `keyup` event.
             */
            choices.input.element.addEventListener( 'keyup', function( e ) {

                // Only capture backspace and delete keypress that results to empty string.
                if (
                    ( e.which !== 8 && e.which !== 46 ) ||
                    e.target.value.length > 0
                ) {
                    return;
                }

                app.performSearch( choices, '', ajaxArgs );
            } );

            choices.passedElement.element.addEventListener( 'search', _.debounce( function( e ) {

                // Make sure that the search term is actually changed.
                if ( choices.input.element.value.length === 0 ) {
                    return;
                }

                app.performSearch( choices, e.detail.value, ajaxArgs );
            }, 800 ) );
        },

        /**
         * Redirect to form embed page.
         *
         * @since 4.3.2
         */
        embedPageRedirect: function(e) {

            const $button = $( e.target );
            const $allInputs = $( '.swp-content-container button:not(.swp-sf--theme-preview button), .swp-content-container input:not(.swp-sf--theme-preview input)' );

            $allInputs.attr('disabled','disabled');
            $button.addClass('swp-button--processing');

            e.target.disabled = true;

            const data = {
                action  : 'searchwp_admin_form_embed_wizard_embed_page_url',
                _wpnonce: _SEARCHWP.nonce,
                formId: $( '#swp-form-save' ).data( 'form-id' ),
                pageId: 0,
                pageTitle: '',
            };

            if ( $button.data( 'action' ) === 'select-page' ) {
                data.pageId = $( '#swp-search-form-embed-existing-page-modal-select' ).val();
            }

            if ( $button.data( 'action' ) === 'create-page' ) {
                data.pageTitle = $( '#swp-search-form-embed-new-page-modal-page-title' ).val()
            }

            $.post( ajaxurl, data, function( response ) {
                if ( response.success ) {
                    window.location = response.data;
                } else {
                    console.error(response);
                    $allInputs.removeAttr('disabled');
                    $button.removeClass('swp-button--processing');
                    $button.after('<span class="swp-error-msg swp-text-red swp-b ">Error</span>');
                    setTimeout(
                        function () {
                            $button.siblings('.swp-error-msg').remove();
                        },
                        1500
                    );
                }
            } );
        },
    };

    app.init();

    window.searchwp = window.searchwp || {};

    window.searchwp.AdminSearchFormsPage = app;

}( jQuery ) );
