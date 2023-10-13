/* global wcbk_admin */
jQuery( function ( $ ) {
    "use strict";

    var changes = {
        status : false,
        enable : function () {
            changes.status = true;
        },
        disable: function () {
            changes.status = false;
        }
    };

    $( document ).on( 'change', 'input, textarea, select, checkbox', changes.enable );
    $( document ).on( 'click', '.yith-wcbk-admin-button, .yith-wcbk-admin-action-link', changes.enable );

    // this allows to save the form without confirmation
    $( document ).on( 'click', '#yith-wcbk-settings-tab-actions-save', changes.disable );

    window.addEventListener( "beforeunload", function ( e ) {
        if ( changes.status ) {
            e.returnValue = wcbk_admin.i18n_leave_page_confirmation;
            return wcbk_admin.i18n_leave_page_confirmation;
        }
    } );
} );