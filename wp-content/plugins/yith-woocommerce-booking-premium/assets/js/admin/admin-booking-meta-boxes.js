/* global wcbk_admin_booking_meta_boxes */
jQuery( function ( $ ) {
    "use strict";

    var block_params = {
        message   : null,
        overlayCSS: {
            background: '#fff',
            opacity   : 0.6
        }
    };

    /** ------------------------------------------------------------------------
     *  Booking Notes Metabox
     * ------------------------------------------------------------------------- */
    var wcbk_meta_boxes_booking_notes = {
        init: function () {
            $( '#yith-booking-notes' )
                .on( 'click', 'button.add-booking-note', this.add_booking_note )
                .on( 'click', '.delete-booking-note', this.delete_booking_note );

        },

        add_booking_note: function () {
            var note_textarea = $( 'textarea#booking-note' ),
                note          = note_textarea.val();

            if ( note.length > 0 ) {

                $( '#yith-booking-notes' ).block( block_params );

                var data = {
                    action   : 'yith_wcbk_add_booking_note',
                    post_id  : wcbk_admin_booking_meta_boxes.post_id,
                    note     : note,
                    note_type: $( 'select#booking-note-type' ).val(),
                    security : wcbk_admin_booking_meta_boxes.add_booking_note_nonce
                };

                $.post( ajaxurl, data, function ( response ) {
                    $( 'ul.booking-notes' ).prepend( response );
                    $( '#yith-booking-notes' ).unblock();
                    note_textarea.val( '' );
                } );
            }
        },

        delete_booking_note: function () {
            if ( window.confirm( wcbk_admin_booking_meta_boxes.i18n_delete_note ) ) {
                var $note = $( this ).closest( 'li.note' );

                $note.block( block_params );

                var data = {
                    action  : 'yith_wcbk_delete_booking_note',
                    note_id : $note.attr( 'rel' ),
                    security: wcbk_admin_booking_meta_boxes.delete_booking_note_nonce
                };

                $.post( ajaxurl, data, function () {
                    $note.remove();
                } );
            }
        }
    };


    wcbk_meta_boxes_booking_notes.init();
} );