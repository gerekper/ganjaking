jQuery( function ( $ ) {
    var is_debug                   = false,
        categories_filter_select   = $( '#yith-wcbep-categories-filter' ),
        table                      = $( '#yith-wcbep-table-wrap .wp-list-table' ),
        custom_input               = $( '#yith-wcbep-custom-input' ),
        selected                   = null,
        current_cell               = null,
        matrix                     = new Array(),
        current_matrix             = new Array(),
        current_matrix_keys        = new Array(),
        cell_matrix                = new Array(),
        get_products_btn           = $( '#yith-wcbep-get-products' ),
        filter_form                = $( '#yith-wcbep-filter-form' ),
        f_categories               = $( '#yith-wcbep-categories-filter' ),
        f_reg_price_select         = $( '#yith-wcbep-regular-price-filter-select' ),
        f_reg_price_value          = $( '#yith-wcbep-regular-price-filter-value' ),
        f_sale_price_select        = $( '#yith-wcbep-sale-price-filter-select' ),
        f_sale_price_value         = $( '#yith-wcbep-sale-price-filter-value' ),
        f_per_page                 = $( '#yith-wcbep-per-page-filter' ),
        f_reset_btn                = $( '#yith-wcbep-reset-filters' ),
        table_wrap                 = $( '#yith-wcbep-table-wrap' ),
        bulk_edit_btn              = $( '#yith-wcbep-bulk-edit-btn' ),
        bulk_editor                = $( '#yith-wcbep-bulk-editor' ),
        bulk_apply_btn             = $( '#yith-wcbep-bulk-apply' ),
        b_reg_price_sel            = $( '#yith-wcbep-regular-price-bulk-select' ),
        b_reg_price_val            = $( '#yith-wcbep-regular-price-bulk-value' ),
        b_sale_price_sel           = $( '#yith-wcbep-sale-price-bulk-select' ),
        b_sale_price_val           = $( '#yith-wcbep-sale-price-bulk-value' ),
        save_btn                   = $( '#yith-wcbep-save' ),
        my_checked_rows            = new Array(),
        modified_rows              = new Array(),
        message                    = $( '#yith-wcbep-message' ),
        block_params               = {
            message        : null,
            overlayCSS     : {
                background: '#000',
                opacity   : 0.6
            },
            ignoreIfBlocked: true
        },
        block_params2              = {
            message   : null,
            overlayCSS: {
                background: '#000 url()',
                opacity   : 0.6,
                cursor    : 'default'
            }
        },

        custom_input_hide          = function ( hide ) {
            hide = hide || false;
            if ( hide ) {
                custom_input.hide();
            }
            if ( selected ) {
                selected.html( custom_input.html() );
                custom_input.html( '' );
                selected = null;
            }
            controller_test();
        },
        edited_matrix              = [],
        controller_test            = function ( create_matrix ) {

            var row       = 0;
            edited_matrix = [];
            modified_rows = [];
            table.find( 'tbody#the-list > tr' ).each( function () {
                var item     = $( this ).children( 'td' ),
                    modified = false;

                if ( item.length > 0 ) {
                    var col               = 1,
                        edited_matrix_row = [];
                    edited_matrix_row.push( 0 );
                    item.each( function () {
                        var val = $( this ).html();

                        if ( val != matrix[ row ][ col ] ) {
                            $( this ).addClass( 'yith-wcbep-table-modified-td' );
                            modified = true;
                            edited_matrix_row.push( 1 );
                        } else {
                            $( this ).removeClass( 'yith-wcbep-table-modified-td' );
                            edited_matrix_row.push( 0 );
                        }
                        col++;
                    } );
                    edited_matrix.push( edited_matrix_row );

                    if ( modified ) {
                        modified_rows.push( row );
                    }
                    row++;
                }
            } );
            if ( create_matrix ) {
                create_current_html_matrix();
            }

        },
        table_init                 = function () {
            table = $( '#yith-wcbep-table-wrap .wp-list-table' );
            //carico i dati iniziali in una matrice
            matrix_init();

            // actions for select/deselect all checkbox
            table.find( 'th#cb input' ).on( 'click', function () {
                var all_checkbox = table.find( 'tbody#the-list th.check-column > input' );
                if ( $( this ).is( ':checked' ) ) {
                    all_checkbox.attr( 'checked', true );
                } else {
                    all_checkbox.attr( 'checked', false );
                }
            } );

            table
                .on( 'click', 'td.regular_price, td.sale_price', function ( event ) {
                    event.stopPropagation();
                    custom_input_hide( false );

                    selected     = $( event.target );
                    current_cell = $( event.target );

                    custom_input.width( selected.width() );
                    custom_input.height( selected.height() );

                    custom_input.show();
                    custom_input.offset( selected.offset() );
                    custom_input.html( selected.html() );
                } );
        },
        matrix_init                = function () {
            //carico i dati iniziali in una matrice
            matrix      = new Array();
            cell_matrix = new Array();
            table.find( 'tbody tr' ).each( function () {
                var item      = $( this ).find( 'td' );
                var cell_cols = [ $( this ).find( 'th' ) ];
                if ( item.length > 0 ) {
                    var cols = [ false ];
                    item.each( function () {
                        cols.push( $( this ).html() );
                        cell_cols.push( $( this ) );
                    } );
                    matrix.push( cols );
                    cell_matrix.push( cell_cols );
                }
            } );
        },
        create_current_matrix      = function () {
            var new_matrix = new Array();
            table.find( 'tbody tr' ).each( function () {
                var item = $( this ).find( 'td' );
                if ( item.length > 0 ) {
                    var cols = [ false ];
                    item.each( function () {
                        cols.push( $( this ).html() );
                    } );
                    new_matrix.push( cols );
                }
            } );
            return new_matrix;
        },
        create_current_matrix_keys = function () {
            var new_matrix = new Array();
            table.find( 'thead tr th' ).each( function () {
                new_matrix.push( $( this ).attr( 'id' ) );
            } );
            return new_matrix;
        },
        checked_rows               = function () {
            var row    = 0;
            var result = new Array();
            table.find( 'tbody tr' ).each( function () {
                var item = $( this ).find( 'th input:checked' );
                if ( item.length > 0 ) {
                    result.push( row );
                }
                row++;
            } );
            return result;
        },
        reset_bulk_editor          = function () {
            bulk_editor.find( 'input.is_resetable' ).each( function () {
                $( this ).val( '' );
            } );
            bulk_editor.find( 'select.is_resetable' ).each( function () {
                $( this ).prop( 'selectedIndex', 0 );
            } );
        },
        reset_filters              = function () {
            filter_form.find( 'input.is_resetable' ).each( function () {
                $( this ).val( '' );
            } );
            filter_form.find( 'select.is_resetable' ).each( function () {
                $( this ).prop( 'selectedIndex', 0 );
            } );
            categories_filter_select.val( '' ).trigger( 'change' )
        },
        go_to_next_cell            = function () {
            if ( current_cell ) {
                for ( index in cell_matrix ) {
                    var row = cell_matrix[ index ];
                    for ( index_col in row ) {
                        if ( $( row[ index_col ] )[ 0 ] == current_cell[ 0 ] ) {
                            if ( typeof cell_matrix[ parseInt( index ) + 1 ] != 'undefined' ) {
                                $( cell_matrix[ parseInt( index ) + 1 ][ index_col ] ).trigger( 'click' );
                                custom_input.selectText();
                                return;
                            }
                        }
                    }
                }
            }
        };

    $.fn.selectText = function () {
        var doc     = document;
        var element = this[ 0 ];
        //console.log(this, element);
        if ( doc.body.createTextRange ) {
            var range = document.body.createTextRange();
            range.moveToElementText( element );
            range.select();
        } else if ( window.getSelection ) {
            var selection = window.getSelection();
            var range     = document.createRange();
            range.selectNodeContents( element );
            selection.removeAllRanges();
            selection.addRange( range );
        }
    };


    // I N I T
    categories_filter_select.select2( { width: '95%' } );
    custom_input.offset( new Array( 0, 0 ) );
    current_matrix_keys = create_current_matrix_keys();

    bulk_editor.draggable();

    $( 'html' ).on( 'click', function () {
        custom_input_hide( true );
    } );

    custom_input
        .on( 'click', function ( event ) {
            event.stopPropagation();
        } )

        .keypress( function ( e ) {
            if ( e.which == 13 ) {
                custom_input_hide( true );
                e.stopPropagation();
                setTimeout( go_to_next_cell, 0 );
            }
        } );

    get_products_btn.on( 'click', function () {
        var data = {
            f_categories       : f_categories.val(),
            f_reg_price_select : f_reg_price_select.val(),
            f_reg_price_value  : f_reg_price_value.val(),
            f_sale_price_select: f_sale_price_select.val(),
            f_sale_price_value : f_sale_price_value.val(),
            f_per_page         : f_per_page.val()
        };
        list.update( data );
    } );

    bulk_edit_btn.on( 'click', function () {
        // get selected ID
        var checked_array = table.find( 'tbody th input:checked' );
        var checked_ids   = new Array();
        checked_array.each( function () {
            checked_ids.push( $( this ).val() );
        } );

        if ( checked_ids.length < 1 ) {
            alert( ajax_object.no_product_selected );
            return;
        }

        // open bulk editor
        bulk_editor.fadeIn();
        $( '#wpwrap' ).block( block_params2 );
        my_checked_rows = checked_rows();
        //console.log(checked_rows());
    } );

    $( '#yith-wcbep-bulk-cancel' ).add( '.yith-wcbep-close-bulk-editor' ).on( 'click', function () {
        bulk_editor.fadeOut();
        reset_bulk_editor();
        $( '#wpwrap' ).unblock();
    } );

    bulk_editor.keypress( function ( e ) {
        if ( e.which == 13 ) {
            bulk_apply_btn.trigger( 'click' );
        }
    } );

    bulk_apply_btn.on( 'click', function () {
        for ( var index in my_checked_rows ) {
            var ckd = my_checked_rows[ index ];

            // Numbers
            var number_array = [ 'regular_price', 'sale_price' ];
            for ( var i in number_array ) {
                if ( current_matrix_keys.indexOf( number_array[ i ] ) > -1 ) {
                    var cell      = $( cell_matrix[ ckd ][ current_matrix_keys.indexOf( number_array[ i ] ) ] ),
                        old_value = parseFloat( cell.html() ),
                        new_value = '';

                    var s = $( '#yith-wcbep-' + number_array[ i ] + '-bulk-select' ).val();
                    var v = $( '#yith-wcbep-' + number_array[ i ] + '-bulk-value' ).val();

                    if ( ( (!isNaN( v ) && v != '') || s == 'del') && cell.find( '.not_editable' ).length < 1 ) {
                        switch ( s ) {
                            case 'new':
                                new_value = parseFloat( v );
                                break;
                            case 'inc':
                                old_value = !isNaN( old_value ) ? old_value : 0;
                                new_value = old_value + parseFloat( v );
                                break;
                            case 'dec':
                                new_value = old_value > 0 ? old_value - parseFloat( v ) : '';
                                break;
                            case 'incp':
                                new_value = old_value > 0 ? old_value + old_value * parseFloat( v ) / 100 : '';
                                break;
                            case 'decp':
                                new_value = old_value > 0 ? old_value - old_value * parseFloat( v ) / 100 : '';
                                break;
                            case 'decfr':
                                var cell_regular = $( cell_matrix[ ckd ][ current_matrix_keys.indexOf( 'regular_price' ) ] );
                                old_value        = parseFloat( cell_regular.html() );
                                if ( !isNaN( old_value ) && old_value != '' )
                                    new_value = old_value - parseFloat( v );
                                break;
                            case 'decpfr':
                                var cell_regular = $( cell_matrix[ ckd ][ current_matrix_keys.indexOf( 'regular_price' ) ] );
                                old_value        = parseFloat( cell_regular.html() );
                                if ( !isNaN( old_value ) && old_value != '' )
                                    new_value = old_value - old_value * parseFloat( v ) / 100;
                                break;
                            case 'del':
                                new_value = '';
                                break;
                        }
                        if ( new_value != '' && ( new_value < 0 || isNaN( new_value ) ) ) {
                            new_value = 0;
                        }
                        cell.html( new_value );
                    }
                }
            }


        }

        bulk_editor.fadeOut();
        reset_bulk_editor();
        $( '#wpwrap' ).unblock();
    } );

    f_reset_btn.on( 'click', function () {
        reset_filters();
    } );

    save_btn.on( 'click', function () {
        controller_test();
        if ( modified_rows.length > 0 ) {
            //BLOCK
            table.block( block_params );
            save_btn.prop( 'disabled', true );
            bulk_edit_btn.prop( 'disabled', true );

            current_matrix = create_current_matrix();

            var matrix_modify         = [],
                current_edited_matrix = [];
            for ( var mod_row in modified_rows ) {
                var index = modified_rows[ mod_row ];
                matrix_modify.push( current_matrix[ index ] );
                current_edited_matrix.push( edited_matrix[ index ] );
            }

            for ( var ir in current_edited_matrix ) {
                var row = current_edited_matrix[ ir ];

                for ( ic in row ) {
                    var col = row[ ic ];
                    if ( col == 0 && ic != 2 ) {
                        matrix_modify[ ir ][ ic ] = null;
                    }
                }
            }

            var to_edit              = matrix_modify.length,
                edit_count           = to_edit,
                percentual           = 0,
                width                = 40,
                percentual_container = $( '#yith-wcbep-percentual-container' );
            percentual_container.html( '<span class="yith-wcbep-percentual" style="width:' + percentual + '%;">' + percentual + '%</span>' );
            var percentual_span = percentual_container.find( 'span.yith-wcbep-percentual' );
            percentual_container.fadeIn();

            var bulk_edit_length     = matrix_modify.length,
                bulk_edit_processing = function ( bulk_edit_index ) {
                    if ( bulk_edit_index < bulk_edit_length ) {
                        var post_data = {
                            matrix_keys  : current_matrix_keys,
                            matrix_modify: [ matrix_modify[ bulk_edit_index ] ],
                            edited_matrix: [ current_edited_matrix[ bulk_edit_index ] ],
                            action       : 'yith_wcbep_bulk_edit_products'
                        };

                        $.ajax( {
                                    type    : "POST",
                                    data    : post_data,
                                    url     : ajaxurl,
                                    success : function ( response ) {
                                        if ( is_debug ) {
                                            console.log( response );
                                        }
                                    },
                                    complete: function ( response ) {
                                        if ( is_debug ) {
                                            console.log( response );
                                        }
                                        to_edit--;
                                        percentual = parseInt( 100 * (edit_count - to_edit) / edit_count );

                                        percentual_span.html( percentual + '%' );
                                        percentual_span.animate( {
                                                                     width: percentual + '%'
                                                                 }, 200 );
                                        bulk_edit_processing( ++bulk_edit_index );
                                    }
                                } );
                    } else {
                        get_products_btn.trigger( 'click' );
                        percentual_container.delay( 1500 ).fadeOut();
                    }
                };
            // call the first processing
            if ( bulk_edit_length > 0 ) {
                bulk_edit_processing( 0 );
            }

        }
    } );

    // AJAX WP_TABLE_LIST
    list = {

        init: function () {

            // This will have its utility when dealing with the page number input
            var timer;
            var delay = 500;

            table_init();

            // Pagination links, sortable link
            $( '.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a' ).on( 'click', function ( e ) {
                // We don't want to actually follow these links
                e.preventDefault();
                // Simple way: use the URL to extract our needed variables
                var query = this.search.substring( 1 );

                var data = {
                    paged              : list.__query( query, 'paged' ) || '1',
                    order              : list.__query( query, 'order' ) || 'asc',
                    orderby            : list.__query( query, 'orderby' ) || 'date',
                    f_categories       : f_categories.val(),
                    f_reg_price_select : f_reg_price_select.val(),
                    f_reg_price_value  : f_reg_price_value.val(),
                    f_sale_price_select: f_sale_price_select.val(),
                    f_sale_price_value : f_sale_price_value.val(),
                    f_per_page         : f_per_page.val()
                };
                list.update( data );
            } );

            // Page number input
            $( 'input[name=paged]' ).on( 'keyup', function ( e ) {

                // If user hit enter, we don't want to submit the form
                // We don't preventDefault() for all keys because it would
                // also prevent to get the page number!
                if ( 13 == e.which )
                    e.preventDefault();

                // This time we fetch the variables in inputs
                var data = {
                    paged  : parseInt( $( 'input[name=paged]' ).val() ) || '1',
                    order  : $( 'input[name=order]' ).val() || 'asc',
                    orderby: $( 'input[name=orderby]' ).val() || 'date'
                };

                // Now the timer comes to use: we wait half a second after
                // the user stopped typing to actually send the call. If
                // we don't, the keyup event will trigger instantly and
                // thus may cause duplicate calls before sending the intended
                // value
                window.clearTimeout( timer );
                timer = window.setTimeout( function () {
                    list.update( data );
                }, delay );
            } );
        },

        /** AJAX call
         *
         * Send the call and replace table parts with updated version!
         *
         * @param    object    data The data to pass through AJAX
         */
        update: function ( data ) {
            table.block( block_params );
            save_btn.prop( 'disabled', true );
            bulk_edit_btn.prop( 'disabled', true );

            $.ajax( {
                        // /wp-admin/admin-ajax.php
                        url    : ajaxurl,
                        // Add action and nonce to our collected data
                        data   : $.extend(
                            {
                                _ajax_yith_wcbep_list_nonce: $( '#_ajax_yith_wcbep_list_nonce' ).val(),
                                action                     : '_ajax_fetch_yith_wcbep_list',
                            },
                            data
                        ),
                        // Handle the successful result
                        success: function ( response ) {

                            // WP_List_Table::ajax_response() returns json
                            var response = $.parseJSON( response );

                            //console.log(response);

                            // Add the requested rows
                            if ( response.rows.length )
                                $( '#the-list' ).html( response.rows );
                            // Update column headers for sorting
                            if ( response.column_headers.length )
                                $( 'thead tr, tfoot tr' ).html( response.column_headers );
                            // Update pagination for navigation
                            if ( response.pagination.bottom.length )
                                $( '.tablenav.top .tablenav-pages' ).html( $( response.pagination.top ).html() );
                            if ( response.pagination.top.length )
                                $( '.tablenav.bottom .tablenav-pages' ).html( $( response.pagination.bottom ).html() );

                            // Init back our event handlers
                            list.init();
                            // UNBLOCK
                            table.unblock();
                            save_btn.prop( 'disabled', false );
                            bulk_edit_btn.prop( 'disabled', false );
                            matrix_init();
                        }
                    } );
        },

        /**
         * Filter the URL Query to extract variables
         *
         * @see http://css-tricks.com/snippets/javascript/get-url-variables/
         *
         * @param    string    query The URL query part containing the variables
         * @param    string    variable Name of the variable we want to get
         *
         * @return   string|boolean The variable value if available, false else.
         */
        __query: function ( query, variable ) {

            var vars = query.split( "&" );
            for ( var i = 0; i < vars.length; i++ ) {
                var pair = vars[ i ].split( "=" );
                if ( pair[ 0 ] == variable )
                    return pair[ 1 ];
            }
            return false;
        },
    }

// Show time!
    list.init();

} );