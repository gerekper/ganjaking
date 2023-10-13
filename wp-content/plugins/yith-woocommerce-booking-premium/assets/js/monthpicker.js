jQuery( function ( $ ) {
    "use strict";

    $.fn.yith_wcbk_monthpicker = function () {
        return $( this ).each( function () {
            $( this ).on( 'click', '.month.enabled', function ( e ) {
                var month_picker       = $( this ).closest( '.yith-wcbk-month-picker-wrapper' ),
                    months             = month_picker.find( '.selected' ),
                    month              = $( e.target ),
                    selected_value     = month.data( 'value' ),
                    month_picker_value = month_picker.find( '.yith-wcbk-month-picker-value' );

                if ( !month.is( '.selected' ) ) {
                    months.removeClass( 'selected' );
                    month.addClass( 'selected' );
                    month_picker_value.val( selected_value );
                    month_picker_value.trigger( 'change' );
                }
            } )

                .on( 'click', '.prev, .next', function ( e ) {
                    console.log('click');
                    var month_picker = $( this ).closest( '.yith-wcbk-month-picker-wrapper' ),
                        target       = $( e.target ),
                        current_year = parseInt( month_picker.data( 'current-year' ) ),
                        year_to_show = target.is( '.prev' ) ? ( current_year - 1) : ( current_year + 1),
                        to_show      = month_picker.find( '.year-' + year_to_show );

                    if ( target.is( '.enabled' ) && to_show.length > 0 ) {
                        month_picker.find( '.year' ).hide();
                        to_show.show();
                        month_picker.data( 'current-year', year_to_show );
                    }
                } );
        } );
    };

} );
