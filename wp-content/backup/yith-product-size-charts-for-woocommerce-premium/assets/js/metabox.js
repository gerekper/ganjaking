jQuery( function ( $ ) {
    var table                    = $( '#yith-wcpsc-metabox-table' ),
        num_rows                 = table.find( 'tr' ).length - 1,
        num_cols                 = table.find( 'th' ).length - 1,
        h_table_input            = $( '#yith-wcpsc-table-hidden' ),
        build_row                = function () {
            var tmp_row = '<tr>';
            for ( var i = 0; i < num_cols; i++ ) {
                tmp_row += '<td><input class="yith-wcpsc-input-table" type="text" /></td>';
            }
            tmp_row += '<td class="yith-wcpsc-table-button-container"><input type="button" class="yith-wcpsc-add-row yith-wcpsc-table-button yith-wcpsc-table-button-add" value="+" /><input type="button" class="yith-wcpsc-del-row yith-wcpsc-table-button yith-wcpsc-table-button-del" value="-" /></td>';
            tmp_row += '</tr>';
            return tmp_row;
        },
        build_col                = function ( cell_id ) {
            var tmp_col_btn = '<th><input type="button" class="yith-wcpsc-add-col yith-wcpsc-table-button yith-wcpsc-table-button-add" value="+" /><input type="button" class="yith-wcpsc-del-col yith-wcpsc-table-button yith-wcpsc-table-button-del" value="-" /></th>',
                tmp_col     = '<td><input class="yith-wcpsc-input-table" type="text" /></td>';

            table.find( 'thead tr' ).find( 'th:eq(' + cell_id + ')' ).after( tmp_col_btn );
            table.find( 'tbody tr' ).each( function () {
                $( this ).find( 'td:eq(' + cell_id + ')' ).after( tmp_col );
            } );
        },
        remove_col               = function ( cell_id ) {
            table.find( 'thead tr' ).find( 'th:eq(' + cell_id + ')' ).remove();
            table.find( 'tbody tr' ).each( function () {
                $( this ).find( 'td:eq(' + cell_id + ')' ).remove();
            } );
        },
        create_matrix_from_table = function () {
            var tmp_matrix = [];

            table.find( 'tbody tr' ).each( function () {
                var cols   = [],
                    all_td = $( this ).find( 'td' );

                all_td.each( function () {
                    if ( !$( this ).is( '.yith-wcpsc-table-button-container' ) ) {
                        var tmp_value = $( this ).find( 'input' ).val();
                        cols.push( tmp_value );
                    }
                } );

                tmp_matrix.push( cols );
            } );

            //h_table_input.val( JSON.stringify( tmp_matrix ).replace(/"/g, '\'') );
            h_table_input.val( JSON.stringify( tmp_matrix ) );
            //h_table_input.val( encodeURIComponent( JSON.stringify( tmp_matrix ) ) );
        };


    table
        .on( 'click', '.yith-wcpsc-add-row', function () {
            var this_cell = $( this ).closest( 'td' ),
                this_row  = this_cell.closest( 'tr' );

            num_rows++;
            this_row.after( build_row() );
            create_matrix_from_table();
        } )

        .on( 'click', '.yith-wcpsc-del-row', function () {
            if ( num_rows < 2 )
                return;

            var this_cell = $( this ).closest( 'td' ),
                this_row  = this_cell.closest( 'tr' );

            num_rows--;
            this_row.remove();
            create_matrix_from_table();
        } )

        .on( 'click', '.yith-wcpsc-add-col', function () {
            var this_cell = $( this ).closest( 'th' ),
                cell_id   = this_cell.index();

            num_cols++;
            build_col( cell_id );
            create_matrix_from_table();
        } )

        .on( 'click', '.yith-wcpsc-del-col', function () {
            if ( num_cols < 2 )
                return;
            var this_cell = $( this ).closest( 'th' ),
                cell_id   = this_cell.index();

            num_cols--;
            remove_col( cell_id );
            create_matrix_from_table();
        } )

        .on( 'keyup', 'input', function ( event ) {
            var this_input = $( event.target ),
                value      = this_input.val();
            // remove html tags and wrong apics
            if ( value.search( /<[^>]+>/ig ) >= 0 || value.search( '<>' ) >= 0 || value.search( '“' ) >= 0 ) {
                this_input.val( value.replace( /<[^>]+>/ig, '' ).replace( '<>', '' ).replace( '“', '"' ) );
            }

            create_matrix_from_table();
        } );


    // Hide-Show Fields
    var display_as   = $( '#display_as' ),
        title_of_tab = $( '#title_of_desc_tab-container' ).parent(),
        button_text  = $( '#button_text-container' ).parent(),
        tab_priority = $( '#tab_priority-container' ).parent(),
        tab_title    = $( '#tab_title-container' ).parent();

    display_as.on( 'change', function () {
        var selected = $( this ).find( ':selected' ).val();
        if ( selected == 'tabbed_popup' ) {
            title_of_tab.show();
        } else {
            title_of_tab.hide();
        }

        if ( selected == 'tab' ) {
            tab_priority.show();
            button_text.hide();
        } else {
            tab_priority.hide();
            button_text.show();
        }

        if ( 'tab' === selected || 'tabbed_popup' === selected ) {
            tab_title.show();
        } else {
            tab_title.hide();
        }

    } );
    display_as.trigger( 'change' );
} );