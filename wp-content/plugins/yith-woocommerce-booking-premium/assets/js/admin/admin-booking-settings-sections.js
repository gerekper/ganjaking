/** global wcbk_admin */
jQuery( function ( $ ) {
    "use strict";

    /** ------------------------------------------------------------------------
     *  Settings Section - Toggle
     * ------------------------------------------------------------------------- */
    $( document ).on( 'click', '.yith-wcbk-settings-section__toggle', function ( event ) {
        var _toggle  = $( event.target ),
            _section = _toggle.closest( '.yith-wcbk-settings-section' ),
            _content = _section.find( '.yith-wcbk-settings-section__content' );

        if ( _section.is( '.yith-wcbk-settings-section--closed' ) ) {
            _content.slideDown( 400 );
        } else {
            _content.slideUp( 400 );
        }

        _section.toggleClass( 'yith-wcbk-settings-section--closed' );
    } );

    /** ------------------------------------------------------------------------
     *  Settings Section Box - Sortable
     * ------------------------------------------------------------------------- */
    function yith_wcbk_settings_section_box_sortable_row_indexes( element ) {
        var _container = element.closest( '.yith-wcbk-settings-section-box__sortable-container' );
        _container.find( '.yith-wcbk-settings-section-box' ).each( function ( index, el ) {
            $( '.yith-wcbk-settings-section-box__sortable-position', el ).val( parseInt( $( el ).index( '.yith-wcbk-settings-section-box__sortable-container .yith-wcbk-settings-section-box' ), 10 ) );
        } );
    }

    var sortable_container = $( '.yith-wcbk-settings-section-box__sortable-container' );
    sortable_container.sortable( {
                                     items               : '.yith-wcbk-settings-section-box',
                                     cursor              : 'move',
                                     handle              : '.yith-wcbk-settings-section-box__sortable-anchor',
                                     axis                : 'y',
                                     scrollSensitivity   : 40,
                                     forcePlaceholderSize: true,
                                     opacity             : 0.65,
                                     stop                : function ( event, ui ) {
                                         yith_wcbk_settings_section_box_sortable_row_indexes( ui.item );
                                     }
                                 } );

    if ( typeof MutationObserver !== 'undefined' ) {
        var setSortableStatus = function ( container ) {
            var enabled = container.find( '.yith-wcbk-settings-section-box' ).length > 1;
            container.sortable( "option", "disabled", !enabled );

            enabled && container.removeClass( 'yith-wcbk-settings-section-box__sortable--disabled' ) || container.addClass( 'yith-wcbk-settings-section-box__sortable--disabled' );

        };
        sortable_container.each( function ( index, element ) {
            var current         = $( this ),
                observer_config = { childList: true, subtree: true },
                observ_cb       = function () {
                    setSortableStatus( current );
                },
                observer        = new MutationObserver( observ_cb );
            observer.observe( element, observer_config );
            setSortableStatus( current );
        } );

    }

    /** ------------------------------------------------------------------------
     *  Settings Section Box - Toggle
     * ------------------------------------------------------------------------- */
    $( document ).on( 'click', '.yith-wcbk-settings-section-box__toggle', function ( event ) {
        var _toggle  = $( event.target ),
            _section = _toggle.closest( '.yith-wcbk-settings-section-box' ),
            _content = _section.find( '.yith-wcbk-settings-section-box__content' );
        if ( _section.is( '.yith-wcbk-settings-section-box--closed' ) ) {
            _content.slideDown( 400 );
        } else {
            _content.css( { display: 'block' } );
            _content.slideUp( 400 );
        }

        _section.toggleClass( 'yith-wcbk-settings-section-box--closed' );
    } );

    /** ------------------------------------------------------------------------
     *  Settings Section Box - Edit Title
     * ------------------------------------------------------------------------- */
    $( document ).on( 'change keyup', '.yith-wcbk-settings-section-box__edit-title', function ( event ) {
        var _edit_input = $( event.target ),
            _section    = _edit_input.closest( '.yith-wcbk-settings-section-box' ),
            _title      = _section.find( '.yith-wcbk-settings-section-box__title h3' ).first();

        if ( _title.length ) {
            _title.html( _edit_input.val() || wcbk_admin.i18n_untitled );
        }
    } );
} );