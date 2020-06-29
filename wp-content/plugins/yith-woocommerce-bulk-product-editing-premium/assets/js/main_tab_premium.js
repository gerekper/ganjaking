jQuery( function ( $ ) {
    var is_debug                             = false,
        page_wrapper                         = $( '#yith-wcbep-my-page-wrapper' ),
        is_vendor                            = page_wrapper.data( 'is-vendor' ) == 'yes',
        wpml_current_lang                    = page_wrapper.data( 'wpml-current-language' ),
        attr_filter_select                   = $( '.yith_webep_attr_chosen' ),
        resize_table                         = $( '#yith-wcbep-resize-table' ),
        yith_wcbep_chosen                    = $( '.yith-wcbep-chosen' ),
        custom_input_categories              = $( '#yith-wcbep-custom-input-categories' ),
        custom_input                         = $( '#yith-wcbep-custom-input' ),
        custom_input_textarea                = $( '#yith-wcbep-custom-input-textarea' ),
        custom_input_textarea_save           = $( '#yith-wcbep-custom-input-textarea-button-save' ),
        custom_input_textarea_cancel         = $( '#yith-wcbep-custom-input-textarea-button-cancel' ),
        custom_input_date                    = $( '#yith-wcbep-custom-input-date' ),
        custom_input_image                   = $( '#yith-wcbep-custom-input-image' ),
        custom_input_image_save              = $( '#yith-wcbep-custom-input-image-button-save' ),
        custom_input_image_cancel            = $( '#yith-wcbep-custom-input-image-button-cancel' ),
        custom_input_image_remove            = $( '#yith-wcbep-custom-input-image-button-remove' ),
        custom_input_gallery                 = $( '#yith-wcbep-custom-input-gallery' ),
        custom_input_gallery_cancel          = $( '#yith-wcbep-custom-input-gallery-button-cancel' ),
        custom_input_gallery_save            = $( '#yith-wcbep-custom-input-gallery-button-save' ),
        custom_input_gallery_add             = $( '#yith-wcbep-custom-input-gallery-button-add' ),
        custom_input_down_files              = $( '#yith-wcbep-custom-input-downloadable-files' ),
        custom_input_down_files_cancel       = $( '#yith-wcbep-custom-input-downloadable-files-button-cancel' ),
        custom_input_down_files_save         = $( '#yith-wcbep-custom-input-downloadable-files-button-save' ),
        custom_input_down_files_add          = $( '#yith-wcbep-custom-input-downloadable-files-button-add' ),
        custom_input_down_files_def_row      = $( '#yith-wcbep-custom-input-downloadable-files-default-row tbody' ),
        custom_input_down_files_table        = $( '#yith-wcbep-custom-input-downloadable-files-table' ),
        message_not_editable                 = $( '#yith-wcbep-message-not-editable' ),
        selected                             = null,
        current_cell                         = null,
        matrix                               = [],
        current_matrix                       = [],
        current_matrix_keys                  = [],
        cell_matrix                          = [],
        get_products_btn                     = $( '#yith-wcbep-get-products' ),
        filter_form                          = $( '#yith-wcbep-filter-form' ),
        f_title_select                       = $( '#yith-wcbep-title-filter-select' ),
        f_title_value                        = $( '#yith-wcbep-title-filter-value' ),
        f_sku_select                         = $( '#yith-wcbep-sku-filter-select' ),
        f_sku_value                          = $( '#yith-wcbep-sku-filter-value' ),
        f_description_select                 = $( '#yith-wcbep-description-filter-select' ),
        f_description_value                  = $( '#yith-wcbep-description-filter-value' ),
        f_categories                         = $( '#yith-wcbep-categories-filter' ),
        f_brands                             = $( '#yith-wcbep-brands-filter' ),
        f_custom_taxonomies                  = $( '.yith-wcbep-custom-taxonomy-filter' ),
        f_tags                               = $( '#yith-wcbep-tags-filter' ),
        f_reg_price_select                   = $( '#yith-wcbep-regular-price-filter-select' ),
        f_reg_price_value                    = $( '#yith-wcbep-regular-price-filter-value' ),
        f_sale_price_select                  = $( '#yith-wcbep-sale-price-filter-select' ),
        f_sale_price_value                   = $( '#yith-wcbep-sale-price-filter-value' ),
        f_weight_select                      = $( '#yith-wcbep-weight-filter-select' ),
        f_weight_value                       = $( '#yith-wcbep-weight-filter-value' ),
        f_stock_qty_select                   = $( '#yith-wcbep-stock-qty-filter-select' ),
        f_stock_qty_value                    = $( '#yith-wcbep-stock-qty-filter-value' ),
        f_stock_status                       = $( '#yith-wcbep-stock-status-filter-select' ),
        f_status                             = $( '#yith-wcbep-status-filter-select' ),
        f_visibility                         = $( '#yith-wcbep-visibility-filter-select' ),
        f_allow_backorders                   = $( '#yith-wcbep-allow_backorders-filter-select' ),
        f_shipping_class                     = $( '#yith-wcbep-shipping-class-filter-select' ),
        f_per_page                           = $( '#yith-wcbep-per-page-filter' ),
        f_product_type                       = $( '#yith-wcbep-product-type-filter-select' ),
        f_show_variations                    = $( '#yith-wcbep-show-variations-filter' ),
        f_reset_btn                          = $( '#yith-wcbep-reset-filters' ),
        f_check_by_filters                   = $( '#yith-wcbep-check-by-filters' ),
        table_wrap                           = $( '#yith-wcbep-table-wrap' ),
        table                                = table_wrap.find( '.wp-list-table' ).first(),
        bulk_edit_btn                        = $( '#yith-wcbep-bulk-edit-btn' ),
        bulk_editor                          = $( '#yith-wcbep-bulk-editor' ),
        bulk_apply_btn                       = $( '#yith-wcbep-bulk-apply' ),
        b_categories_sel                     = $( '#yith-wcbep-categories-bulk-select' ),
        b_categories_val                     = $( '#yith-wcbep-categories-bulk-chosen' ),
        b_attributes_val                     = $( '.yith-wcbep-attributes-bulk-chosen' ),
        b_attributes_visible_val             = $( '.yith-wcbep-attributes-visible-bulk-select' ),
        b_attributes_variation_val           = $( '.yith-wcbep-attributes-used-for-variation-bulk-select' ),
        b_tags_sel                           = $( '#yith-wcbep-tags-bulk-select' ),
        b_tags_val                           = $( '#yith-wcbep-tags-bulk-value' ),
        b_tags_rep                           = $( '#yith-wcbep-tags-bulk-replace' ),
        save_btn                             = $( '#yith-wcbep-save' ),
        cols_settings_btn                    = $( '#yith-wcbep-cols-settings-btn' ),
        cols_settings                        = $( '#yith-wcbep-columns-settings-wrapper' ),
        cols_settings_select_all_btn         = $( '#yith-wcbep-columns-settings-select-all' ),
        cols_settings_unselect_all_btn       = $( '#yith-wcbep-columns-settings-unselect-all' ),
        my_checked_rows                      = [],
        modified_rows                        = [],
        undo_btn                             = $( '#yith-wcbep-undo' ),
        redo_btn                             = $( '#yith-wcbep-redo' ),
        new_product_btn                      = $( '#yith-wcbep-new' ),
        delete_product_btn                   = $( '#yith-wcbep-delete' ),
        export_btn                           = $( '#yith-wcbep-export' ),
        export_form                          = $( '#yith-wcbep-export-form' ),
        export_form_btn                      = $( '#yith-wcbep-export-form-btn' ),
        export_ids                           = $( '#yith-wcbep-export-form__selected-products' ),
        message                              = $( '#yith-wcbep-message' ),
        initial_table_width                  = table.width(),
        close_btn                            = $( '.yith-wcbep-close' ),
        products_wrap                        = $( '.yith-wcbep-products-wrap' ).first(),
        new_width                            = initial_table_width * resize_table.width() / 100,
        block_params                         = {
            message        : null,
            overlayCSS     : {
                background: '#000 url()',
                opacity   : 0.6
            },
            ignoreIfBlocked: true
        },
        block_params2                        = {
            message   : null,
            overlayCSS: {
                background: '#000 url()',
                opacity   : 0.6,
                cursor    : 'default'
            }
        },
        set_html_value                       = function ( a, b ) {
            if ( b == custom_input_down_files ) {
                var rows       = custom_input_down_files_table.find( 'tr' ),
                    files_html = '';

                custom_input_down_files_table.find( 'tr' ).each( function () {
                    var name = $( this ).find( 'td.file_name input' ).val(),
                        url  = $( this ).find( 'td.file_url input' ).val();

                    files_html += '<input type="hidden" class="yith-wcbep-hidden-downloadable-file" data-file-name="' + name + '" data-file-url="' + url + '" />';
                } );

                if ( rows.length > 0 ) {
                    if ( rows.length > 1 ) {
                        files_html += rows.length + ' ' + ajax_object.files;
                    } else {
                        files_html += rows.length + ' ' + ajax_object.file;
                    }
                }

                a.html( files_html );
                return;
            }

            if ( b == custom_input_gallery ) {
                var gallery_html = '';
                b.find( 'li.image img' ).each( function () {
                    gallery_html += '<img data-image-id="' + $( this ).data( 'image-id' ) + '" src="' + $( this ).attr( 'src' ) + '" />';
                } );
                a.find( '.yith-wcbep-table-image-gallery' ).html( gallery_html );
                return;
            }

            if ( b == custom_input_categories || b == custom_attr_input || b == custom_chosen ) {
                var val = b.find( 'select.chosen' ).val() != null ? b.find( 'select.chosen' ).val() : '';
                a.find( '.yith-wcbep-select-selected' ).val( '[' + val + ']' );
                //a.find('.yith-wcbep-select-values').html('');
                var select = b.find( 'select' );
                var txt    = '';
                if ( val != '' && typeof val != 'object' ) {
                    val = [val];
                }
                if ( val != '' ) {
                    for ( var i in val ) {
                        txt += select.find( 'option[value="' + val[ i ] + '"]' ).html();
                        if ( i < ( val.length - 1 ) ) {
                            txt += ', ';
                        }
                    }
                }
                a.find( '.yith-wcbep-select-values' ).html( txt );

                if ( b == custom_attr_input ) {
                    if ( a.find( '.yith-wcbep-attr-is-visible' ).val() == -1 || a.find( '.yith-wcbep-attr-is-variation' ).val() == -1 ) {

                    } else {
                        var is_visible   = b.find( '.yith-wcbep-custom-input-attributes-visible' ).is( ':checked' ) ? '1' : '0';
                        var is_variation = b.find( '.yith-wcbep-custom-input-attributes-variations' ).is( ':checked' ) ? '1' : '0';
                        a.find( '.yith-wcbep-attr-is-visible' ).val( is_visible );
                        a.find( '.yith-wcbep-attr-is-variation' ).val( is_variation );
                    }
                }

                return;
            } else if ( b == custom_input_textarea ) {
                a.text( b.find( 'textarea' ).val() );
                return;
            } else if ( b == custom_input_date ) {
                a.html( b.find( 'input' ).val() );
                return;
            } else if ( b == custom_input_image ) {
                var new_img_url = b.find( 'img' ).attr( 'src' ) ? b.find( 'img' ).attr( 'src' ) : '';
                var new_img_id  = b.find( '.yith-wcbep-hidden-image-value' ).val();
                a.find( 'img' ).attr( 'src', new_img_url );
                a.find( '.yith-wcbep-hidden-image-value' ).val( new_img_id );
                return;
            }
            var b_input = b.find( 'input' );
            if ( a.hasClasses( ['regular_price', 'sale_price', 'weight', 'height', 'width', 'length', 'stock_quantity', 'download_limit', 'download_expiry',
                                'menu_order', 'low_stock_amount'], 'OR' ) ) {
                // FOR NUMERIC TABLE CELLS
                var new_value = b_input.val();
                if ( !isNaN( new_value ) ) {
                    a.html( new_value );
                } else if ( new_value == '' ) {
                    a.html( '' );
                }
            } else {
                var has_checkbox = !!a.find( '.yith-wcbep-editable-checkbox' ).length;
                if ( has_checkbox ) {
                    return;
                }

                // FOR TEXT FIELDS
                a.html( b_input.val() );
            }
        },
        custom_input_hide                    = function ( hide ) {
            //last_selected = selected;
            var hided = false;
            if ( custom_input.is( ':visible' ) ) {
                hided = true;
                if ( hide ) {
                    custom_input.hide();
                }
                if ( selected ) {
                    set_html_value( selected, custom_input );
                    custom_input.val( '' );
                    selected = null;
                }
            }
            if ( custom_input_categories.is( ':visible' ) ) {
                hided = true;
                if ( hide ) {
                    custom_input_categories.hide();
                }
                if ( selected ) {
                    set_html_value( selected, custom_input_categories );
                    selected = null;
                }
            }
            if ( custom_input_textarea.is( ':visible' ) ) {
                hided = true;
                if ( hide ) {
                    custom_input_textarea.hide();
                }
                if ( selected ) {
                    selected = null;
                }
            }
            if ( custom_input_date.is( ':visible' ) ) {
                hided = true;
                if ( hide ) {
                    custom_input_date.hide();
                }
                if ( selected ) {
                    set_html_value( selected, custom_input_date );
                    selected = null;
                }
            }
            if ( custom_attr_input && custom_attr_input.is( ':visible' ) ) {
                hided = true;
                if ( hide ) {
                    custom_attr_input.hide();
                }
                if ( selected ) {
                    set_html_value( selected, custom_attr_input );
                    selected = null;
                }
            }
            if ( custom_input_image.is( ':visible' ) ) {
                hided = true;
                if ( hide ) {
                    custom_input_image.hide();
                }
                if ( selected ) {
                    selected = null;
                }
            }
            if ( custom_input_gallery.is( ':visible' ) ) {
                hided = true;
                if ( hide ) {
                    custom_input_gallery.hide();
                }
                if ( selected ) {
                    selected = null;
                }
            }
            if ( custom_input_down_files.is( ':visible' ) ) {
                hided = true;
                if ( hide ) {
                    custom_input_down_files.hide();
                }
                if ( selected ) {
                    selected = null;
                }
            }

            if ( custom_chosen && custom_chosen.is( ':visible' ) ) {
                hided = true;
                if ( hide ) {
                    custom_chosen.hide();
                }
                if ( selected ) {
                    set_html_value( selected, custom_chosen );
                    selected = null;
                }
            }

            if ( message_not_editable.is( ':visible' ) ) {
                if ( hide ) {
                    message_not_editable.hide();
                    return;
                }
            }

            if ( last_selected && hided ) {
                single_controller();
                last_selected = null;
            }
        },
        last_selected,
        single_controller                    = function () {
            var row      = last_selected.data( 'y' ),
                col      = last_selected.data( 'x' ) + 1,
                val      = last_selected.html(),
                modified = false;

            if ( val != matrix[ row ][ col ] ) {
                last_selected.addClass( 'yith-wcbep-table-modified-td' );
                modified = true;
            } else {
                last_selected.removeClass( 'yith-wcbep-table-modified-td' );
            }
            create_current_html_matrix();
        },
        edited_matrix                        = [],
        controller_test                      = function ( create_matrix ) {

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
        custom_attr_input,
        custom_input_gallery_init_actions    = function () {
            custom_input_gallery.find( '.yith-wcbep-image-gallery' ).sortable( {
                                                                                   items               : 'li.image',
                                                                                   cursor              : 'move',
                                                                                   scrollSensitivity   : 40,
                                                                                   forcePlaceholderSize: true,
                                                                                   forceHelperSize     : false,
                                                                                   helper              : 'clone',
                                                                                   opacity             : 0.65,
                                                                                   placeholder         : 'wc-metabox-sortable-placeholder',
                                                                                   start               : function ( event, ui ) {
                                                                                       ui.item.css( 'background-color', '#aaa' );
                                                                                   },
                                                                                   stop                : function ( event, ui ) {
                                                                                       ui.item.removeAttr( 'style' );
                                                                                   },
                                                                                   update              : function () {
                                                                                       $( '#yith-wcbep-custom-input-gallery-container ul li.image' ).css( 'cursor', 'default' );
                                                                                   }
                                                                               } );

            custom_input_gallery.find( 'span.delete' ).on( 'click', function () {
                $( this ).closest( 'li.image' ).remove();
            } );
        },
        custom_input_down_files_init_actions = function () {
            custom_input_down_files.find( '.delete' ).on( 'click', function () {
                $( this ).closest( 'tr' ).remove();
            } );

            custom_input_down_files.find( '.yith-wcbep-custom-input-downloadable-files-choose-file' ).on( 'click', function () {
                var my_row = $( this ).closest( 'tr' );
                var file   = wp.media( {
                                           title   : 'Choose file',
                                           multiple: false
                                       } ).open()
                    .on( 'select', function () {
                        var uploaded_file = file.state().get( 'selection' ).first(),
                            file_url      = uploaded_file.toJSON().url;

                        my_row.find( 'td.file_url input' ).val( file_url );
                    } );
            } );
            custom_input_down_files_table.sortable( {
                                                        items               : 'tr',
                                                        cursor              : 'move',
                                                        axis                : 'y',
                                                        handle              : 'td.sort',
                                                        scrollSensitivity   : 40,
                                                        forcePlaceholderSize: true,
                                                        helper              : 'clone',
                                                        opacity             : 0.65
                                                    } );
        },
        first_resize_table_by_num_columns    = function () {
            var number_of_colums = table.children( 'thead' ).find( 'th:visible' ).length,
                minimum_width    = number_of_colums * 150;

            minimum_width = initial_table_width > minimum_width ? initial_table_width : minimum_width;

            var resize_table_width = minimum_width * 100 / initial_table_width;
            resize_table.css( { width: resize_table_width + 'px' } );
            resize_table.trigger( 'resize' );
            table.css( { height: 'auto', width: minimum_width } );
        },
        custom_chosen,
        table_init                           = function () {
            first_resize_table_by_num_columns();
            matrix_array    = [];
            index_of_matrix = 0;
            table           = $( '#yith-wcbep-table-wrap .wp-list-table' );
            //carico i dati iniziali in una matrice
            matrix_init();

            last_selected = null;

            // actions for select/deselect all checkbox
            table.on( 'click', 'th#cb input', function () {
                var all_checkbox = table.find( 'tbody#the-list th.check-column > input' );
                if ( $( this ).is( ':checked' ) ) {
                    all_checkbox.attr( 'checked', true );
                } else {
                    all_checkbox.attr( 'checked', false );
                }
            } );

            // actions for checkbox in table
            table.on( 'click', 'td.manage_stock, td.sold_individually, td.featured, td.virtual, td.downloadable, td.enable_reviews', function () {
                var checkbox              = $( this ).find( 'input.yith-wcbep-editable-checkbox' );
                var hidden_checkbox_value = $( this ).find( 'input.yith-wcbep-hidden-checkbox-value' );
                if ( checkbox.is( ':checked' ) ) {
                    hidden_checkbox_value.val( '1' );
                } else {
                    hidden_checkbox_value.val( '0' );
                }
                selected      = $( this ).closest( 'td' );
                last_selected = selected;
                single_controller();
            } );

            var extra_class_select = extra_class.select.length > 0 ? ', ' + extra_class.select : '';
            // actions for select in table
            //table.on( 'change', 'td.tax_status, td.tax_class, td.allow_backorders, td.shipping_class, td.status, td.visibility, td.download_type, td.prod_type' + extra_class_select , function () {
            table.on( 'change', 'td .yith-wcbep-editable-select', function () {
                //var select       = $( this ).find( '.yith-wcbep-editable-select' );
                //var hidden_value = $( this ).find( 'input.yith-wcbep-hidden-select-value' );

                var select       = $( this );
                var hidden_value = $( this ).closest( 'td' ).find( 'input.yith-wcbep-hidden-select-value' );
                hidden_value.val( select.val() );
                selected      = $( this ).closest( 'td' );
                last_selected = selected;
                single_controller();
            } );

            var extra_class_text = extra_class.text,
                text_classes     = ['title', 'regular_price', 'sale_price', 'sku', 'tags', 'weight', 'height', 'width', 'length', 'stock_quantity', 'download_limit',
                                    'download_expiry', 'menu_order', 'button_text', 'product_url', 'slug', 'up_sells', 'cross_sells', 'low_stock_amount'];

            text_classes = text_classes.concat( extra_class_text );
            // Add actions on cells of table
            table
                .on( 'click', 'td', function ( event ) {
                    event.stopPropagation();
                    custom_input_hide( true );
                    selected      = $( event.target );
                    last_selected = $( event.target ).closest( 'td' );
                    current_cell  = $( event.target ); //used for fo_to_next_cell

                    if ( !selected.is( 'td' ) ) {
                        selected = selected.closest( 'td' );
                    }

                    if ( selected.closest( 'td' ).find( '.not_editable' ).length > 0 || selected.closest( 'td' ).find( '.not_editable_for_new' ).length > 0 ) {
                        if ( selected.closest( 'td' ).find( '.not_editable' ).length > 0 ) {
                            message_not_editable.html( ajax_object.not_editable_variations );
                        } else if ( selected.closest( 'td' ).find( '.not_editable_for_new' ).length > 0 ) {
                            message_not_editable.html( ajax_object.not_editable_new_product );
                        }


                        message_not_editable.show();
                        var top  = event.pageY - 55 - 10;
                        var left = event.pageX - 215 / 2;
                        message_not_editable.offset( { top: top, left: left } );
                        return;
                    }

                    if ( selected.hasClasses( text_classes, 'OR' ) ) {
                        // Custom Input for input

                        var my_input = custom_input.find( 'input' );
                        custom_input.width( selected.width() );
                        custom_input.height( selected.height() );
                        custom_input.show();
                        custom_input.offset( selected.offset() );
                        my_input.width( selected.width() );
                        my_input.height( selected.height() );
                        my_input.val( selected.html() );
                        my_input.trigger( 'focus' ).select();
                    } else if ( $( event.target ).closest( 'td' ).hasClasses( ['categories'], 'OR' ) ) {
                        selected     = $( event.target ).closest( 'td' );
                        current_cell = $( event.target ).closest( 'td' ); //used for fo_to_next_cell
                        custom_input.prop( 'contenteditable', 'false' );
                        custom_input_categories.width( selected.width() );
                        custom_input_categories.height( selected.height() );
                        custom_input_categories.find( '.chosen' ).val( $.parseJSON( selected.find( '.yith-wcbep-select-selected' ).val() ) );
                        custom_input_categories.find( '.chosen' ).trigger( "change" );
                        custom_input_categories.show();
                        custom_input_categories.offset( selected.offset() );
                        custom_input_categories.find( '.chosen' ).select2( 'open' );
                    } else if ( selected.hasClasses( ['description', 'shortdesc', 'purchase_note'], 'OR' ) ) {
                        custom_input_textarea.find( 'textarea' ).val( selected.text() );
                        custom_input_textarea.show();
                        custom_input_textarea.offset( selected.offset() );
                    } else if ( selected.hasClasses( ['image'], 'OR' ) || $( event.target ).closest( 'td' ).hasClasses( ['image'], 'OR' ) ) {
                        selected    = $( event.target ).closest( 'td' );
                        var img_src = selected.find( 'img' ).attr( 'src' ) ? selected.find( 'img' ).attr( 'src' ) : '';
                        var img_id  = selected.find( '.yith-wcbep-hidden-image-value' ).val();
                        custom_input_image.find( 'img' ).attr( 'src', img_src );
                        custom_input_image.find( '.yith-wcbep-hidden-image-value' ).val( img_id );
                        custom_input_image.show();
                        custom_input_image.offset( selected.offset() );
                    } else if ( selected.hasClasses( ['image_gallery'], 'OR' ) || $( event.target ).closest( 'td' ).hasClasses( ['image_gallery'], 'OR' ) ) {
                        selected         = $( event.target ).closest( 'td' );
                        var images       = selected.find( 'img' );
                        var gallery_html = '<ul class="yith-wcbep-image-gallery">';
                        if ( images.length > 0 ) {
                            images.each( function () {
                                gallery_html += '<li class="image"><span class="delete">x</span><img data-image-id="' + $( this ).data( 'image-id' ) + '" src="' + $( this ).attr( 'src' ) + '"></li>';
                            } );
                        }
                        gallery_html += '</ul>';
                        custom_input_gallery.find( '#yith-wcbep-custom-input-gallery-container' ).html( gallery_html );
                        custom_input_gallery_init_actions();
                        custom_input_gallery.show();
                        custom_input_gallery.offset( selected.offset() );
                    } else if ( selected.hasClasses( ['downloadable_files'], 'OR' ) || $( event.target ).closest( 'td' ).hasClasses( ['downloadable_files'], 'OR' ) ) {
                        selected = $( event.target ).closest( 'td' );
                        custom_input_down_files_table.html( '' );
                        var files           = selected.find( '.yith-wcbep-hidden-downloadable-file' ),
                            default_row_obj = $( custom_input_down_files_def_row.html() );

                        if ( files.length > 0 ) {
                            files.each( function () {
                                var file_row = default_row_obj.clone(),
                                    file     = $( this );
                                file_row.find( 'td.file_name input' ).val( file.data( 'file-name' ) );
                                file_row.find( 'td.file_url input' ).val( file.data( 'file-url' ) );
                                custom_input_down_files_table.append( file_row );
                            } );
                            custom_input_down_files_init_actions();
                        }
                        custom_input_down_files.show();
                        custom_input_down_files.offset( {
                                                            top : selected.offset().top,
                                                            left: ( selected.offset().left - custom_input_down_files.width() / 2 )
                                                        } );
                    } else if ( selected.hasClasses( ['date', 'sale_price_from', 'sale_price_to'], 'OR' ) ) {
                        // Custom Input for input DATE
                        var my_input = custom_input_date.find( 'input' );
                        custom_input_date.width( selected.width() );
                        custom_input_date.height( selected.height() );
                        my_input.width( selected.width() );
                        my_input.height( selected.height() );
                        my_input.val( selected.html() );
                        custom_input_date.show();
                        custom_input_date.offset( selected.offset() );
                        my_input.datepicker( { dateFormat: "yy-mm-dd" } );
                        my_input.trigger( 'focus' );
                    } else if ( selected.hasClasses( extra_obj.chosen, 'OR' ) ) {
                        // CUSTOM CHOSEN
                        var chosen_id = "yith-wcbep-custom-input-" + selected.prop( 'class' ).split( ' ' )[ 0 ];
                        custom_chosen = $( '#' + chosen_id );

                        custom_chosen.width( selected.width() );
                        custom_chosen.height( selected.height() );
                        custom_chosen.find( '.chosen' ).val( $.parseJSON( selected.find( '.yith-wcbep-select-selected' ).val() ) );
                        custom_chosen.find( '.chosen' ).trigger( "change" );
                        custom_chosen.show();
                        custom_chosen.offset( selected.offset() );
                        custom_chosen.find( '.chosen' ).select2( 'open' );

                    } else if ( $( event.target ).closest( 'td' ).is( '[class^="attr_"]' ) ) {
                        selected          = $( event.target ).closest( 'td' );
                        var attr          = selected.prop( 'class' ).split( ' ' )[ 0 ].replace( 'attr_', '' );
                        custom_attr_input = $( '#yith-wcbep-custom-input-attributes-' + attr );
                        current_cell      = $( event.target ).closest( 'td' ); //used for fo_to_next_cell
                        custom_attr_input.width( "240px" );

                        if ( selected.find( '.yith-wcbep-attr-is-visible' ).val() == -1 || selected.find( '.yith-wcbep-attr-is-variation' ).val() == -1 ) {
                            custom_attr_input.find( '.yith-wcbep-custom-input-attributes-checkbox-wrap' ).hide();
                            var my_chosen = custom_attr_input.find( '.chosen' );
                            if ( my_chosen.prop( 'multiple' ) ) {
                                my_chosen.val( '' ).trigger( 'change' );
                                my_chosen.removeAttr( 'multiple' ).select2( { width: '100%' } )
                                    .append( $( "<option></option>" )
                                                 .attr( "value", '' )
                                                 .text( ajax_object.leave_empty ) );
                            }
                            my_chosen.val( $.parseJSON( selected.find( '.yith-wcbep-select-selected' ).val() ) );
                            my_chosen.trigger( "change" );
                        } else {
                            custom_attr_input.find( '.yith-wcbep-custom-input-attributes-checkbox-wrap' ).show();
                            var my_chosen = custom_attr_input.find( '.chosen' );
                            if ( !my_chosen.prop( 'multiple' ) ) {
                                my_chosen.val( '' ).trigger( 'change' );
                                my_chosen.attr( 'multiple', true ).select2( { width: '100%' } );
                                my_chosen.find( 'option[value=""]' ).remove();
                            }

                            if ( selected.find( '.yith-wcbep-attr-is-visible' ).val() == 1 ) {
                                custom_attr_input.find( '.yith-wcbep-custom-input-attributes-visible' ).prop( 'checked', true );
                            } else {
                                custom_attr_input.find( '.yith-wcbep-custom-input-attributes-visible' ).prop( 'checked', false );
                            }
                            if ( selected.find( '.yith-wcbep-attr-is-variation' ).val() == 1 ) {
                                custom_attr_input.find( '.yith-wcbep-custom-input-attributes-variations' ).prop( 'checked', true );
                            } else {
                                custom_attr_input.find( '.yith-wcbep-custom-input-attributes-variations' ).prop( 'checked', false );
                            }
                            my_chosen.val( $.parseJSON( selected.find( '.yith-wcbep-select-selected' ).val() ) );
                            my_chosen.trigger( "change" );
                        }


                        custom_attr_input.show();
                        custom_attr_input.offset( selected.offset() );
                    }
                } );
            create_current_html_matrix();
        },
        matrix_init                          = function () {
            //carico i dati iniziali in una matrice
            matrix      = [];
            cell_matrix = [];
            var loop_y  = 0;
            table.find( 'tbody#the-list > tr' ).each( function () {
                var item      = $( this ).children( 'td' );
                var cell_cols = [$( this ).children( 'th' )];
                if ( item.length > 0 ) {
                    var cols   = [false];
                    var loop_x = 0;
                    item.each( function () {
                        $( this ).data( 'y', loop_y );
                        $( this ).data( 'x', loop_x );
                        cols.push( $( this ).html() );
                        cell_cols.push( $( this ) );
                        loop_x++;
                    } );
                    matrix.push( cols );
                    cell_matrix.push( cell_cols );
                }
                loop_y++;
            } );
            current_matrix_keys = create_current_matrix_keys();
        },
        create_current_matrix                = function () {
            var new_matrix = [];
            table.find( 'tbody#the-list > tr' ).each( function () {
                var item = $( this ).children( 'td' );
                if ( item.length > 0 ) {
                    var cols = [false];
                    item.each( function () {
                        var selected_hidden           = $( this ).find( '.yith-wcbep-select-selected' );
                        var hidden_checkbox_value     = $( this ).find( 'input.yith-wcbep-hidden-checkbox-value' );
                        var hidden_select_value       = $( this ).find( 'input.yith-wcbep-hidden-select-value' );
                        var hidden_image_value        = $( this ).find( 'input.yith-wcbep-hidden-image-value' );
                        var table_image_gallery       = $( this ).find( '.yith-wcbep-table-image-gallery' );
                        var hidden_downloadable_files = $( this ).find( '.yith-wcbep-hidden-downloadable-file' );
                        var not_editable_div          = $( this ).find( '.not_editable' );
                        if ( selected_hidden.length > 0 ) {
                            // FOR CHOSEN FIELDS [es. categories, attributes]
                            var my_array            = [];
                            //for attributes ONLY
                            var hidden_is_visible   = $( this ).find( 'input.yith-wcbep-attr-is-visible' );
                            var hidden_is_variation = $( this ).find( 'input.yith-wcbep-attr-is-variation' );

                            if ( hidden_is_visible.length > 0 && hidden_is_variation.length > 0 ) {
                                my_array.push( hidden_is_visible.val() );
                                my_array.push( hidden_is_variation.val() );
                                my_array.push( $.parseJSON( selected_hidden.val() ) );
                                cols.push( my_array );
                            } else {
                                cols.push( selected_hidden.val() );
                            }
                        } else if ( hidden_checkbox_value.length > 0 ) {
                            // FOR CHECKBOX FIELDS [es. stock status]
                            cols.push( hidden_checkbox_value.val() );
                        } else if ( hidden_select_value.length > 0 ) {
                            // FOR SELECT FIELDS [es. tax status]
                            cols.push( hidden_select_value.val() );
                        } else if ( hidden_image_value.length > 0 ) {
                            // FOR IMAGE
                            cols.push( hidden_image_value.val() );
                        } else if ( table_image_gallery.length > 0 ) {
                            // FOR IMAGE GALLERY
                            var gallery_array = [];
                            $( this ).find( 'img' ).each( function () {
                                gallery_array.push( $( this ).data( 'image-id' ) );
                            } );
                            gallery_array = ( gallery_array.length > 0 ) ? gallery_array : '';
                            cols.push( gallery_array );
                        } else if ( hidden_downloadable_files.length > 0 ) {
                            // FOR DOWNLOADABLE FILES
                            var files_array = [];
                            hidden_downloadable_files.each( function () {
                                files_array.push( [$( this ).data( 'file-name' ), $( this ).data( 'file-url' )] );
                            } );
                            files_array = ( files_array.length > 0 ) ? files_array : '';
                            cols.push( files_array );
                        } else if ( not_editable_div.length > 0 ) {
                            // FOR NOT EDITABLE
                            if ( $( this ).is( '.prod_type' ) ) {
                                cols.push( 'variation' );
                            } else {
                                cols.push( not_editable_div.html() );
                            }
                        } else if ( $( this ).hasClasses( ['description', 'shortdesc', 'purchase_note'], 'OR' ) ) {
                            cols.push( $( this ).text() );
                        } else {
                            cols.push( $( this ).html() );
                        }
                    } );
                    new_matrix.push( cols );
                }
            } );

            return new_matrix;
        },
        create_current_ids_array             = function () {
            var ids_array = [];
            table.find( 'tbody#the-list > tr' ).each( function () {
                var item = $( this ).children( 'td.ID' );
                if ( item.length > 0 ) {
                    item.each( function () {
                        ids_array.push( parseInt( $( this ).html() ) );
                    } );
                }
            } );

            return ids_array;
        },
        index_of_matrix                      = 0,
        matrix_array                         = [],
        create_current_html_matrix           = function () {
            var new_matrix = [];
            table.find( 'tbody#the-list > tr' ).each( function () {
                var item = $( this ).children( 'td' );
                if ( item.length > 0 ) {
                    var cols = [];
                    item.each( function () {
                        cols.push( $( this ).html() );
                    } );
                    new_matrix.push( cols );
                }
            } );

            if ( matrix_array[ index_of_matrix - 1 ] ) {
                if ( matrix_array[ index_of_matrix - 1 ].toString() != new_matrix.toString() ) {
                    matrix_array[ index_of_matrix ] = new_matrix;
                    index_of_matrix++;

                    matrix_array = matrix_array.slice( 0, index_of_matrix );
                }
            } else {
                matrix_array[ index_of_matrix ] = new_matrix;
                index_of_matrix++;
            }
            return new_matrix;
        },
        apply_html_matrix_to_table           = function ( html_matrix ) {
            var row = 0;
            table.find( 'tbody#the-list > tr' ).each( function () {
                var item = $( this ).children( 'td' );
                if ( item.length > 0 ) {
                    var col = 0;
                    item.each( function () {
                        $( this ).html( html_matrix[ row ][ col ] );
                        var select   = $( this ).find( '.yith-wcbep-editable-select' ),
                            checkbox = $( this ).find( '.yith-wcbep-editable-checkbox' ),
                            value    = '';

                        if ( select.length > 0 ) {
                            value = $( this ).find( '.yith-wcbep-hidden-select-value' ).val();
                            select.val( value );
                        } else if ( checkbox.length > 0 ) {
                            value = $( this ).find( '.yith-wcbep-hidden-checkbox-value' ).val();
                            checkbox.prop( 'checked', value == 1 );
                        }
                        col++;
                    } );
                }
                row++;
            } );
            controller_test();
        },
        undo                                 = function () {
            if ( index_of_matrix > 1 ) {
                index_of_matrix--;
                apply_html_matrix_to_table( matrix_array[ index_of_matrix - 1 ] );
            }
        },
        redo                                 = function () {
            if ( matrix_array[ index_of_matrix ] ) {
                apply_html_matrix_to_table( matrix_array[ index_of_matrix ] );
                index_of_matrix++;
            }
        },
        create_current_matrix_keys           = function () {
            var new_matrix = [];
            table.children( 'thead' ).children( 'tr' ).children( 'th' ).each( function () {
                new_matrix.push( $( this ).attr( 'id' ) );
            } );
            return new_matrix;
        },
        checked_rows                         = function () {
            var row    = 0;
            var result = [];
            table.find( 'tbody#the-list > tr' ).each( function () {
                var item = $( this ).find( 'th.check-column input:checked' );
                if ( item.length > 0 ) {
                    result.push( row );
                }
                row++;
            } );
            return result;
        },
        reset_bulk_editor                    = function () {
            bulk_editor.find( 'input.is_resetable, textarea.is_resetable' ).each( function () {
                $( this ).val( '' );
            } );
            bulk_editor.find( 'select.is_resetable' ).each( function () {
                if ( $( this ).hasClass( 'chosen' ) ) {

                } else {
                    $( this ).prop( 'selectedIndex', 0 );
                    $( this ).trigger( 'change' );
                }
            } );
            bulk_editor.find( 'select.is_resetable' ).each( function () {
                $( this ).prop( 'selectedIndex', 0 );
                $( this ).trigger( 'change' );
            } );

            bulk_editor.find( '.yith-wcbep-image-bulk-reset' ).trigger( 'click' );

            var extra_bulk_chosen = extra_bulk.chosen;
            for ( i in extra_bulk_chosen ) {
                var this_chosen = $( '#yith-wcbep-' + extra_bulk_chosen[ i ] + '-bulk-chosen' );

                this_chosen.prop( 'selectedIndex', -1 );
                this_chosen.select2();
            }


            b_categories_val.prop( 'selectedIndex', -1 );
            b_categories_val.select2();

            b_attributes_val.each( function () {

                $( this ).prop( 'selectedIndex', -1 );
                $( this ).select2();
            } );


        },
        reset_filters                        = function () {
            filter_form.find( 'input.is_resetable' ).each( function () {
                $( this ).val( '' );
            } );
            filter_form.find( 'select.is_resetable' ).each( function () {
                $( this ).prop( 'selectedIndex', 0 );
            } );

            filter_form.find( '.chosen.is_resetable' ).each( function () {
                $( this ).val( '' ).trigger( 'change' );
            } );


        },
        go_to_next_cell                      = function () {
            if ( current_cell ) {
                for ( var index in cell_matrix ) {
                    var row = cell_matrix[ index ];
                    for ( var index_col in row ) {
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
        },
        control_visible_columns              = function () {
            var hidden_cols = [];
            cols_settings.find( 'input[type=checkbox]' ).each( function () {
                var id = $( this ).data( 'cols-id' );
                if ( $( this ).is( ':checked' ) ) {
                    table.find( '.column-' + id ).show();
                } else {
                    table.find( '.column-' + id ).hide();
                    hidden_cols.push( id );
                }
            } );
            // SAVE DEFAULT HIDDEN COLS if current user is not a vendor
            if ( !is_vendor ) {
                var post_data = {
                    hidden_cols: hidden_cols,
                    action     : 'yith_wcbep_save_default_hidden_cols'
                };

                $.ajax( {
                            type   : "POST",
                            data   : post_data,
                            url    : ajaxurl,
                            success: function ( response ) {

                            }
                        } );
            }

            first_resize_table_by_num_columns();
        },
        scrollTo                             = function ( _element ) {
            var _offset = _element.offset();

            if ( _offset && _offset.top ) {
                $( 'html, body' ).animate( { scrollTop: _offset.top - 32 - 20 } );
            }
        };


    /*
     hasClasses function:
     check if the object hasClasses with 2 types of logic: AND, OR
     */
    $.fn.extend( {
                     hasClasses: function ( selectors, logic ) {
                         var self = this;
                         if ( logic == 'OR' ) {
                             for ( var i in selectors ) {
                                 if ( $( self ).hasClass( selectors[ i ] ) ) {
                                     return true;
                                 }
                             }
                             return false;
                         } else {
                             // AND
                             var result = true;
                             for ( var i in selectors ) {
                                 if ( !$( self ).hasClass( selectors[ i ] ) ) {
                                     result = false;
                                 }
                             }
                             return result;
                         }
                     }
                 } );

    $.fn.selectText = function () {
        var doc     = document;
        var element = this[ 0 ];
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

    close_btn.on( 'click', function () {
        $( this ).parent().fadeOut();
    } );

    // I N I T
    $( '.yith-wcbep-datepicker' ).datepicker( { dateFormat: "yy-mm-dd" } );

    bulk_editor.draggable( {
                               stop: function () {
                                   $( this ).css( { height: 'auto' } );
                               }
                           } );
    cols_settings.draggable();

    table.css( { height: 'auto', width: new_width } );
    resize_table.resizable( {
                                //alsoResize: '#yith-wcbep-table-wrap',
                                minHeight: 20,
                                maxHeight: 20,
                                minWidth : 100,
                                handles  : 'e',
                                resize   : function () {
                                    var new_width = initial_table_width * $( this ).width() / 100;
                                    table.css( { height: 'auto', width: new_width } );
                                }
                            }
    );
    $( '#yith-wcbep-bulk-editor-container' ).tabs();

    filter_form.find( '.chosen' ).select2();

    yith_wcbep_chosen.select2( { width: '100%' } );
    b_attributes_val.select2();
    custom_input.offset( [0, 0] );

    //table_init();

    new_product_btn.on( 'click', function () {
        var new_empty_row = '<tr class="yith-wcbep-table-new-product">';
        for ( var i in current_matrix_keys ) {
            var classes = current_matrix_keys[ i ] + ' column-' + current_matrix_keys[ i ],
                content = '';

            switch ( current_matrix_keys[ i ] ) {
                case 'cb':
                    new_empty_row += '<th scope="row" class="check-column"></th>';
                    break;
                case 'ID':
                    content = 'NEW';
                    break;
                case 'title':
                    content = 'New Product';
                    break;
                case 'image':
                    content = '<img src=""><input class="yith-wcbep-hidden-image-value" value="" type="hidden">';
                    break;
                case 'image_gallery':
                    content = '<div class="yith-wcbep-table-image-gallery"></div>';
                    break;
                case 'categories':
                    content = '<div class="yith-wcbep-select-values"></div> <input class="yith-wcbep-select-selected" value="[]" type="hidden">';
                    break;
                case 'manage_stock':
                case 'sold_individually':
                case 'featured':
                case 'virtual':
                case 'downloadable':
                case 'enable_reviews':
                    content = '<input class="yith-wcbep-editable-checkbox" type="checkbox"> <input class="yith-wcbep-hidden-checkbox-value" value="0" type="hidden">';
                    break;
                case 'stock_status':
                case 'tax_status':
                case 'tax_class':
                case 'allow_backorders':
                case 'shipping_class':
                case 'status':
                case 'visibility':
                case 'download_type':
                case 'prod_type':
                    content = '<div class="not_editable_for_new"></div><input type="hidden" class="yith-wcbep-hidden-checkbox-value" value="">';
                    break;
            }

            if ( current_matrix_keys[ i ].substring( 0, 5 ) == 'attr_' ) {
                content = '<div class="not_editable_for_new"></div>';
            }

            if ( current_matrix_keys[ i ] != 'cb' ) {
                new_empty_row += '<td class="' + classes + '">' + content + '</td>';
            }
        }
        new_empty_row += '</tr>';

        table.find( 'tbody' ).prepend( new_empty_row );

        control_visible_columns();
        table_init();
    } );

    delete_product_btn.on( 'click', function ( e ) {
        e.stopPropagation();
        my_checked_rows = checked_rows();
        if ( my_checked_rows.length < 1 ) {
            return;
        }

        if ( $( this ).data( 'confirm' ) != 'yes' ) {
            $( this ).val( ajax_object.delete_confirm_txt.replace( '%s', my_checked_rows.length ) );
            $( this ).data( 'confirm', 'yes' );
            $( this ).addClass( 'yith-wcbep-confirm-btn' );
            return;
        }


        $( this ).val( ajax_object.delete_txt );
        $( this ).data( 'confirm', 'no' );
        $( this ).removeClass( 'yith-wcbep-confirm-btn' );

        //BLOCK
        table.block( block_params );
        save_btn.prop( 'disabled', true );
        bulk_edit_btn.prop( 'disabled', true );

        var current_ids        = create_current_ids_array(),
            products_to_delete = [];

        for ( var i in my_checked_rows ) {
            var tmp_id = current_ids[ my_checked_rows[ i ] ];
            products_to_delete.push( tmp_id );
        }

        var post_data = {
            products_to_delete: products_to_delete,
            action            : 'yith_wcbep_bulk_delete_products'
        };

        $.ajax( {
                    type   : "POST",
                    data   : post_data,
                    url    : ajaxurl,
                    success: function ( response ) {
                        message.html( '<p>' + response + '</p>' );
                        var dismiss_btn = $( '<button type="button" class="notice-dismiss" />' );
                        dismiss_btn.appendTo( message );
                        message.fadeIn();
                        dismiss_btn.on( 'click', function () {
                            message.fadeOut();
                        } );
                        get_products_btn.trigger( 'click' );
                    }
                } );

    } );

    export_btn.on( 'click', function () {
        my_checked_rows = checked_rows();
        if ( my_checked_rows.length < 1 ) {
            return;
        }

        var tmp_current_html_matrix = create_current_matrix(),
            matrix_to_export        = [];
        for ( var i in my_checked_rows ) {
            matrix_to_export.push( tmp_current_html_matrix[ my_checked_rows[ i ] ] );
        }

        var complete_matrix = [current_matrix_keys, matrix_to_export],
            json_export     = JSON.stringify( complete_matrix );

        var element = document.createElement( 'a' );
        element.setAttribute( 'href', 'data:text/plain;charset=utf-8,' + encodeURIComponent( json_export ) );
        element.setAttribute( 'download', 'yith_exported_products.txt' );
        element.style.display = 'none';
        document.body.appendChild( element );
        element.click();
        document.body.removeChild( element );
    } );

    export_form_btn.on( 'click', function () {
        my_checked_rows = checked_rows();
        if ( my_checked_rows.length < 1 ) {
            return;
        }

        var tmp_current_html_matrix = create_current_matrix(),
            ids_to_export           = [];
        for ( var i in my_checked_rows ) {
            ids_to_export.push( parseInt( tmp_current_html_matrix[ my_checked_rows[ i ] ][ current_matrix_keys.indexOf( 'ID' ) ] ) );
        }

        var json_ids = JSON.stringify( ids_to_export );

        export_ids.val( json_ids );
        export_form.submit();
    } );

    undo_btn.on( 'click', function () {
        undo();
    } );

    redo_btn.on( 'click', function () {
        redo();
    } );

    //custom input textarea init ----------------------------
    custom_input_textarea_save.on( 'click', function () {
        set_html_value( selected, custom_input_textarea );
        custom_input_hide( true );
    } );

    custom_input_textarea_cancel.on( 'click', function () {
        custom_input_hide( true );
    } );

    //custom input IMAGE GALLERY init ----------------------------
    custom_input_gallery_save.on( 'click', function () {
        set_html_value( selected, custom_input_gallery );
        custom_input_hide( true );
    } );

    custom_input_gallery_cancel.on( 'click', function () {
        custom_input_hide( true );
    } );

    custom_input_gallery_add.on( 'click', function () {
        var image = wp.media( {
                                  title   : 'Upload Image',
                                  multiple: true
                              } ).open()
            .on( 'select', function ( e ) {
                var uploaded_image = image.state().get( 'selection' ),
                    gallery_html   = '';

                uploaded_image.map( function ( single_image ) {
                    var image_url = single_image.toJSON().url;
                    gallery_html += '<li class="image"><span class="delete">x</span><img data-image-id="' + single_image.id + '" src="' + image_url + '"></li>';
                } );

                custom_input_gallery.find( '#yith-wcbep-custom-input-gallery-container ul' ).append( gallery_html );
                custom_input_gallery_init_actions();
            } );
    } );

    //custom input IMAGE  init ----------------------------
    custom_input_image_save.on( 'click', function () {
        set_html_value( selected, custom_input_image );
        custom_input_hide( true );
    } );

    custom_input_image_remove.on( 'click', function () {
        custom_input_image.find( 'img' ).attr( 'src', '' );
        custom_input_image.find( '.yith-wcbep-hidden-image-value' ).val( '' );
    } );

    custom_input_image_cancel.on( 'click', function () {
        custom_input_hide( true );
    } );

    custom_input_image.on( 'click', '.yith-wcbep-custom-input-image-container', function ( e ) {
        e.preventDefault();
        var this_image = $( this ).find( 'img' ).first();
        var image      = wp.media( {
                                       title   : 'Upload Image',
                                       multiple: false
                                   } ).open()
            .on( 'select', function ( e ) {
                var uploaded_image = image.state().get( 'selection' ).first(),
                    image_url      = uploaded_image.toJSON().url;
                this_image.attr( 'src', image_url );
                custom_input_image.find( '.yith-wcbep-hidden-image-value' ).val( uploaded_image.id );
            } );
    } );


    $( '#yith-wcbep-image-bulk-choose-image' ).on( 'click', function ( e ) {
        var target  = $( this ),
            parent  = target.closest( '.yith-wcbep-bulk-form-content-col' ),
            preview = parent.find( '#yith-wcbep-image-bulk-preview' ),
            value   = parent.find( '#yith-wcbep-image-bulk-value' ),
            src     = parent.find( '#yith-wcbep-image-bulk-src' );
        var image   = wp.media( {
                                    title   : 'Upload Image',
                                    multiple: false
                                } ).open()
            .on( 'select', function ( e ) {
                var uploaded_image = image.state().get( 'selection' ).first(),
                    image_url      = uploaded_image.toJSON().url,
                    img            = $( '<img />' ).attr( 'src', image_url ),
                    del            = $( '<span class="yith-wcbep-image-bulk-reset dashicons dashicons-no-alt"></span>' );
                preview.html( '' ).append( img ).append( del );
                value.val( uploaded_image.id );
                src.val( image_url );
            } );
    } );

    $( document ).on( 'click', '.yith-wcbep-image-bulk-reset', function () {
        var target  = $( this ),
            parent  = target.closest( '.yith-wcbep-bulk-form-content-col' ),
            preview = parent.find( '#yith-wcbep-image-bulk-preview' ),
            value   = parent.find( '#yith-wcbep-image-bulk-value' ),
            src     = parent.find( '#yith-wcbep-image-bulk-src' );

        value.val( '' );
        preview.html( '' );
        src.val( '' );
    } );


    //custom input DOWNLOADABLE FILES  init ----------------------------
    custom_input_down_files_add.on( 'click', function () {
        custom_input_down_files_table.append( custom_input_down_files_def_row.html() );
        custom_input_down_files_init_actions();
    } );

    custom_input_down_files_save.on( 'click', function () {
        set_html_value( selected, custom_input_down_files );
        custom_input_hide( true );
    } );

    custom_input_down_files_cancel.on( 'click', function () {
        custom_input_hide( true );
    } );


    bulk_editor.find( 'select' ).on( 'change', function () {
        var obj = $( this );
        if ( obj.val() == 'rep' ) {
            obj.parent().find( '.yith_wcbep_no_display' ).show();
        } else {
            obj.parent().find( '.yith_wcbep_no_display' ).hide();
        }
    } );


    page_wrapper.on( 'click', function () {
        custom_input_hide( true );
        cols_settings.fadeOut();

        delete_product_btn
            .val( ajax_object.delete_txt )
            .data( 'confirm', 'no' )
            .removeClass( 'yith-wcbep-confirm-btn' );
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
        var f_attributes = [];
        attr_filter_select.each( function () {
            var current_value = $( this ).val();
            if ( typeof current_value != 'undefined' && !!current_value ) {
                f_attributes.push( [$( this ).data( 'taxonomy-name' ), $( this ).val()] );
            }
        } );

        var data = {
            paged               : '1',
            order               : 'desc',
            orderby             : 'ID',
            f_title_select      : f_title_select.val(),
            f_title_value       : f_title_value.val(),
            f_description_select: f_description_select.val(),
            f_description_value : f_description_value.val(),
            f_sku_select        : f_sku_select.val(),
            f_sku_value         : f_sku_value.val(),
            f_categories        : f_categories.val(),
            f_tags              : f_tags.val(),
            f_attributes        : f_attributes,
            f_reg_price_select  : f_reg_price_select.val(),
            f_reg_price_value   : f_reg_price_value.val(),
            f_sale_price_select : f_sale_price_select.val(),
            f_sale_price_value  : f_sale_price_value.val(),
            f_weight_select     : f_weight_select.val(),
            f_weight_value      : f_weight_value.val(),
            f_stock_qty_select  : f_stock_qty_select.val(),
            f_stock_qty_value   : f_stock_qty_value.val(),
            f_per_page          : f_per_page.val(),
            f_product_type      : f_product_type.val(),
            f_stock_status      : f_stock_status.val(),
            f_visibility        : f_visibility.val(),
            f_allow_backorders  : f_allow_backorders.val(),
            f_status            : f_status.val(),
            f_shipping_class    : f_shipping_class.val(),
            f_show_variations   : f_show_variations[ 0 ].checked ? 'yes' : 'no'
        };
        list.update( data );
    } );

    bulk_edit_btn.on( 'click', function () {
        custom_input_hide( true );
        // get selected ID
        var checked_array = table.find( 'tbody#the-list th.check-column > input:checked' );
        var checked_ids   = [];
        checked_array.each( function () {
            checked_ids.push( $( this ).val() );
        } );

        if ( checked_ids.length < 1 ) {
            alert( ajax_object.no_product_selected );
            return;
        }
        // open bulk editor
        var top = $( window ).scrollTop() + 50;
        bulk_editor.css( {
                             top: top
                         } );
        bulk_editor.fadeIn();
        $( '#wpwrap' ).block( block_params2 );
        my_checked_rows = checked_rows();
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

    bulk_editor.find( 'textarea' ).keypress( function ( e ) {
        if ( e.which == 13 ) {
            e.stopPropagation();
        }
    } );

    bulk_apply_btn.on( 'click', function () {
        var categories_s      = b_categories_sel.val(),
            categories_v      = ( b_categories_val.val() != null ) ? b_categories_val.val() : '',
            categories_keys   = [],
            categories_labels = [],
            tags_s            = b_tags_sel.val(),
            tags_v            = b_tags_val.val(),
            tags_r            = b_tags_rep.val();


        b_categories_val.find( 'option' ).each( function () {
            categories_keys.push( $( this ).val() );
            categories_labels.push( $( this ).text() );
        } );

        for ( var index in my_checked_rows ) {
            var ckd = my_checked_rows[ index ];
            if ( categories_v.length > 0 && current_matrix_keys.indexOf( 'categories' ) > -1 ) {
                var categories_cell      = $( cell_matrix[ ckd ][ current_matrix_keys.indexOf( 'categories' ) ] ),
                    categories_old_value = $.parseJSON( categories_cell.find( '.yith-wcbep-select-selected' ).val() ),
                    categories_new       = '';

                var cat_old_str = [];
                for ( var i in categories_old_value ) {
                    cat_old_str.push( categories_old_value[ i ].toString() );
                }
                categories_old_value = cat_old_str.slice();

                // CATEGORIES -----------------------------------
                switch ( categories_s ) {
                    case 'new':
                        categories_new = categories_v;
                        break;
                    case 'add':
                        categories_new = categories_old_value.slice();
                        for ( var i in categories_v ) {
                            if ( categories_new.indexOf( categories_v[ i ] ) == -1 ) { // not in array
                                categories_new.push( categories_v[ i ] );
                            }
                        }
                        break;
                    case 'rem':
                        categories_new = categories_old_value.slice();
                        for ( var i in categories_v ) {
                            var index = categories_new.indexOf( categories_v[ i ] );
                            if ( index > -1 ) { // is in array
                                categories_new.splice( index, 1 );
                            }
                        }
                        break;
                }
                var val = categories_new;
                categories_cell.find( '.yith-wcbep-select-selected' ).val( '[' + val + ']' );
                var txt = '';
                for ( var i in val ) {
                    txt += categories_labels[ categories_keys.indexOf( val[ i ] ) ];
                    if ( i < ( val.length - 1 ) ) {
                        txt += ', ';
                    }
                }
                categories_cell.find( '.yith-wcbep-select-values' ).html( txt );
            }
            // -----------------------------------

            // for all choosen
            var extra_bulk_chosen = extra_bulk.chosen;
            for ( var i in extra_bulk_chosen ) {
                if ( current_matrix_keys.indexOf( extra_bulk_chosen[ i ] ) > -1 ) {
                    var cell             = $( cell_matrix[ ckd ][ current_matrix_keys.indexOf( extra_bulk_chosen[ i ] ) ] ),
                        chosen_old_value = $.parseJSON( cell.find( '.yith-wcbep-select-selected' ).val() ),
                        chosen_new       = '',
                        chosen_old_str   = [],
                        chosen_s         = $( '#yith-wcbep-' + extra_bulk_chosen[ i ] + '-bulk-select' ).val(),
                        this_chosen      = $( '#yith-wcbep-' + extra_bulk_chosen[ i ] + '-bulk-chosen' ),
                        chosen_v         = this_chosen.val();

                    for ( var i in chosen_old_value ) {
                        if ( chosen_old_value[ i ] ) {
                            chosen_old_str.push( chosen_old_value[ i ].toString() );
                        }
                    }
                    chosen_old_value = chosen_old_str.slice();

                    switch ( chosen_s ) {
                        case 'new':
                            chosen_new = chosen_v;
                            break;
                        case 'add':
                            chosen_new = chosen_old_value.slice();
                            for ( var i in chosen_v ) {
                                if ( chosen_new.indexOf( chosen_v[ i ] ) == -1 ) { // not in array
                                    chosen_new.push( chosen_v[ i ] );
                                }
                            }
                            break;
                        case 'rem':
                            chosen_new = chosen_old_value.slice();
                            for ( var i in chosen_v ) {
                                var index = chosen_new.indexOf( chosen_v[ i ] );
                                if ( index > -1 ) { // is in array
                                    chosen_new.splice( index, 1 );
                                }
                            }
                            break;
                    }
                    var val = chosen_new;
                    cell.find( '.yith-wcbep-select-selected' ).val( '[' + val + ']' );
                    var txt = '';
                    for ( var i in val ) {
                        var name = this_chosen.find( 'option[value="' + val[ i ] + '"]' ).text();
                        txt += name;
                        if ( i < ( val.length - 1 ) ) {
                            txt += ', ';
                        }
                    }
                    cell.find( '.yith-wcbep-select-values' ).html( txt );
                }
            }

            // TAGS
            if ( current_matrix_keys.indexOf( 'tags' ) > -1 ) {
                var tags_cell      = $( cell_matrix[ ckd ][ current_matrix_keys.indexOf( 'tags' ) ] ),
                    tags_old_value = matrix[ ckd ][ current_matrix_keys.indexOf( 'tags' ) ],
                    tags_new       = '';
                if ( ( tags_v != '' || tags_s == 'del' ) && tags_cell.find( '.not_editable' ).length < 1 ) {
                    switch ( tags_s ) {
                        case 'new':
                            tags_new = tags_v;
                            break;
                        case 'pre':
                            tags_new = tags_v + tags_old_value;
                            break;
                        case 'app':
                            tags_new = tags_old_value + tags_v;
                            break;
                        case 'rep':
                            var to_search = 'yes' === ajax_object.use_regex ? new RegExp( tags_v, "g" ) : tags_v;
                            tags_new      = tags_old_value.replace( to_search, tags_r );
                            break;
                        case 'del':
                            tags_new = '';
                            break;
                    }
                    tags_cell.html( tags_new );
                }
            }

            // Numbers
            var number_array = ['weight', 'height', 'width', 'length', 'stock_quantity', 'download_limit', 'download_expiry', 'regular_price', 'sale_price', 'menu_order', 'low_stock_amount'];
            for ( var i in number_array ) {
                if ( current_matrix_keys.indexOf( number_array[ i ] ) > -1 ) {
                    var cell      = $( cell_matrix[ ckd ][ current_matrix_keys.indexOf( number_array[ i ] ) ] ),
                        //old_value              = parseFloat(matrix[ckd][current_matrix_keys.indexOf( number_array[i] )]),
                        old_value = parseFloat( cell.html() ),
                        new_value = '';

                    var s = $( '#yith-wcbep-' + number_array[ i ] + '-bulk-select' ).val();
                    var v = $( '#yith-wcbep-' + number_array[ i ] + '-bulk-value' ).val();

                    if ( ( ( !isNaN( v ) && v != '' ) || s == 'del' ) && cell.find( '.not_editable' ).length < 1 ) {
                        switch ( s ) {
                            case 'new':
                                new_value = parseFloat( v );
                                break;
                            case 'inc':
                                old_value = !isNaN( old_value ) ? old_value : 0;
                                new_value = old_value + parseFloat( v );
                                break;
                            case 'dec':
                                if ( 'menu_order' === number_array[ i ] ) {
                                    // menu_order can be negative
                                    new_value = old_value - parseFloat( v );
                                } else {
                                    new_value = old_value > 0 ? old_value - parseFloat( v ) : '';
                                }
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
                                if ( !isNaN( old_value ) && old_value != '' ) {
                                    new_value = old_value - parseFloat( v );
                                }
                                break;
                            case 'decpfr':
                                var cell_regular = $( cell_matrix[ ckd ][ current_matrix_keys.indexOf( 'regular_price' ) ] );
                                old_value        = parseFloat( cell_regular.html() );
                                if ( !isNaN( old_value ) && old_value != '' ) {
                                    new_value = old_value - old_value * parseFloat( v ) / 100;
                                }
                                break;
                            case 'del':
                                new_value = '';
                                break;
                        }
                        if ( 'menu_order' === number_array[ i ] ) {
                            // menu_order can be negative
                            if ( new_value !== '' && isNaN( new_value ) ) {
                                new_value = 0;
                            }
                        } else {
                            if ( new_value !== '' && ( new_value < 0 || isNaN( new_value ) ) ) {
                                new_value = 0;
                            }
                        }

                        if ( 'yes' === ajax_object.round_prices && ajax_object.woocommerce_price_num_decimals ) {
                            var _isPriceField = 'regular_price' === number_array[ i ] || 'sale_price' === number_array[ i ],
                                _isValidPrice = new_value !== '' && 'yes' === ajax_object.round_prices && new_value > 0 && !isNaN( new_value );
                            if ( _isPriceField && _isValidPrice ) {
                                var _roundFactor = Math.pow( 10, ajax_object.woocommerce_price_num_decimals );
                                new_value        = Math.round( new_value * _roundFactor ) / _roundFactor;
                            }
                        }

                        cell.html( new_value );
                    }
                }
            }

            // for all textarea
            var textarea_array = ['purchase_note', 'description', 'shortdesc'];
            for ( var i in textarea_array ) {
                if ( current_matrix_keys.indexOf( textarea_array[ i ] ) > -1 ) {
                    var cell      = $( cell_matrix[ ckd ][ current_matrix_keys.indexOf( textarea_array[ i ] ) ] ),
                        old_value = cell.text(),
                        new_value = '';

                    var s = $( '#yith-wcbep-' + textarea_array[ i ] + '-bulk-select' ).val();
                    var v = $( '#yith-wcbep-' + textarea_array[ i ] + '-bulk-value' ).val();
                    var r = $( '#yith-wcbep-' + textarea_array[ i ] + '-bulk-replace' ).val();

                    if ( ( v != '' || s == 'del' ) && cell.find( '.not_editable' ).length < 1 ) {
                        switch ( s ) {
                            case 'new':
                                new_value = v;
                                break;
                            case 'pre':
                                new_value = v + old_value;
                                break;
                            case 'app':
                                new_value = old_value + v;
                                break;
                            case 'rep':
                                var to_search = 'yes' === ajax_object.use_regex ? new RegExp( v, "g" ) : v;
                                new_value     = old_value.replace( to_search, r );
                                break;
                            case 'del':
                                new_value = '';
                                break;
                        }
                        cell.text( new_value );
                    }
                }
            }

            // for all checkbox
            var checkbox_array = ['manage_stock', 'sold_individually', 'featured', 'virtual', 'downloadable', 'enable_reviews'];
            for ( var i in checkbox_array ) {
                if ( current_matrix_keys.indexOf( checkbox_array[ i ] ) > -1 ) {
                    var cell = $( cell_matrix[ ckd ][ current_matrix_keys.indexOf( checkbox_array[ i ] ) ] );
                    var s    = $( '#yith-wcbep-' + checkbox_array[ i ] + '-bulk-select' ).val();

                    if ( cell.find( '.not_editable' ).length < 1 ) {
                        switch ( s ) {
                            case 'skip':
                                break;
                            case 'yes':
                                cell.find( '.yith-wcbep-editable-checkbox' ).prop( 'checked', false ).trigger( 'click' );
                                break;
                            case 'no':
                                cell.find( '.yith-wcbep-editable-checkbox' ).prop( 'checked', true ).trigger( 'click' );
                                break;
                        }
                    }
                }
            }

            // for all selects
            var select_array = ['stock_status', 'tax_status', 'tax_class', 'allow_backorders', 'shipping_class', 'status', 'visibility', 'download_type', 'prod_type'];
            select_array     = select_array.concat( extra_bulk.select );
            for ( var i in select_array ) {
                if ( current_matrix_keys.indexOf( select_array[ i ] ) > -1 ) {
                    var cell = $( cell_matrix[ ckd ][ current_matrix_keys.indexOf( select_array[ i ] ) ] );
                    var s    = $( '#yith-wcbep-' + select_array[ i ] + '-bulk-select' ).val();

                    if ( s != 'skip' && cell.find( '.not_editable' ).length < 1 ) {
                        cell.find( '.yith-wcbep-editable-select' ).val( s ).trigger( 'change' );
                    }
                }
            }

            // for all texts
            var text_array = ['button_text', 'product_url', 'slug', 'up_sells', 'cross_sells', 'title', 'sku', 'sale_price_from', 'sale_price_to', 'date'];
            text_array     = text_array.concat( extra_bulk.text );
            for ( var i in text_array ) {
                if ( current_matrix_keys.indexOf( text_array[ i ] ) > -1 ) {
                    var cell      = $( cell_matrix[ ckd ][ current_matrix_keys.indexOf( text_array[ i ] ) ] ),
                        old_value = cell.html(),
                        new_value = '';

                    var s = $( '#yith-wcbep-' + text_array[ i ] + '-bulk-select' ).val();
                    var v = $( '#yith-wcbep-' + text_array[ i ] + '-bulk-value' ).val();
                    var r = $( '#yith-wcbep-' + text_array[ i ] + '-bulk-replace' ).val();

                    if ( ( v != '' || s == 'del' ) && cell.find( '.not_editable' ).length < 1 ) {
                        switch ( s ) {
                            case 'new':
                                new_value = v;
                                break;
                            case 'pre':
                                new_value = v + old_value;
                                break;
                            case 'app':
                                new_value = old_value + v;
                                break;
                            case 'rep':
                                var to_search = 'yes' === ajax_object.use_regex ? new RegExp( v, "g" ) : v;
                                new_value     = old_value.replace( to_search, r );
                                break;
                            case 'del':
                                new_value = '';
                                break;
                        }
                        cell.html( new_value );
                    }
                }
            }

            // IMAGE
            if ( current_matrix_keys.indexOf( 'image' ) > -1 ) {
                var cell      = $( cell_matrix[ ckd ][ current_matrix_keys.indexOf( 'image' ) ] ),
                    old_value = cell.html(),
                    new_value = '';

                var s = $( '#yith-wcbep-image-bulk-select' ).val();
                var v = $( '#yith-wcbep-image-bulk-value' ).val();
                var r = $( '#yith-wcbep-image-bulk-src' ).val();

                if ( ( v != '' || s == 'del' ) && cell.find( '.not_editable' ).length < 1 ) {
                    switch ( s ) {
                        case 'new':
                            new_value = v;
                            break;
                        case 'del':
                            new_value = '';
                            r         = '';
                            break;
                    }
                    cell.find( '.yith-wcbep-hidden-image-value' ).val( new_value );
                    cell.find( 'img' ).attr( 'src', r );
                }
            }

            // for ATTRIBUTES
            b_attributes_val.each( function () {
                var current_attr = $( this );
                if ( current_matrix_keys.indexOf( 'attr_' + current_attr.data( 'taxonomy-name' ) ) > -1 ) {
                    var cell      = $( cell_matrix[ ckd ][ current_matrix_keys.indexOf( 'attr_' + current_attr.data( 'taxonomy-name' ) ) ] ),
                        s         = current_attr.parent().parent().find( '.yith-wcbep-attributes-bulk-select' ).val(),
                        new_value = '',
                        old_value = $.parseJSON( cell.find( '.yith-wcbep-select-selected' ).val() ),
                        v         = current_attr.val(),
                        labels    = [],
                        keys      = [];

                    for ( var i in old_value ) {
                        old_value[ i ] = '' + old_value[ i ];
                    }

                    current_attr.find( 'option' ).each( function () {
                        keys.push( $( this ).val() );
                        labels.push( $( this ).text() );
                    } );

                    switch ( s ) {
                        case 'new':
                            new_value = v;
                            break;
                        case 'add':
                            new_value = old_value.slice();
                            for ( var i in v ) {
                                if ( new_value.indexOf( v[ i ] ) == -1 ) { // not in array
                                    new_value.push( v[ i ] );
                                }
                            }
                            break;
                        case 'rem':
                            new_value = old_value.slice();
                            for ( var i in v ) {
                                var index = new_value.indexOf( v[ i ] )
                                if ( index > -1 ) { // is in array
                                    new_value.splice( index, 1 );
                                }
                            }
                            break;
                    }
                    var val = new_value;
                    if ( cell.find( '.yith-wcbep-attr-is-visible' ).val() < 0 ) {
                        if ( val.length > 0 ) {
                            val = [val[ 0 ]];
                        }
                    }
                    cell.find( '.yith-wcbep-select-selected' ).val( '[' + val + ']' );
                    var txt = '';
                    for ( var i in val ) {
                        txt += labels[ keys.indexOf( val[ i ] ) ];
                        if ( i < ( val.length - 1 ) ) {
                            txt += ', ';
                        }
                    }
                    cell.find( '.yith-wcbep-select-values' ).html( txt );
                }
            } );

            // for ATTRIBUTES - is visible
            b_attributes_visible_val.each( function () {
                var self = $( this );
                if ( current_matrix_keys.indexOf( 'attr_' + self.data( 'taxonomy-name' ) ) > -1 ) {
                    var cell = $( cell_matrix[ ckd ][ current_matrix_keys.indexOf( 'attr_' + self.data( 'taxonomy-name' ) ) ] ),
                        s    = self.val();

                    switch ( s ) {
                        case 'yes':
                            cell.find( '.yith-wcbep-attr-is-visible' ).val( '1' );
                            break;
                        case 'no':
                            cell.find( '.yith-wcbep-attr-is-visible' ).val( '0' );
                            break;
                    }
                }
            } );

            // for ATTRIBUTES - used for variation
            b_attributes_variation_val.each( function () {
                var self = $( this );
                if ( current_matrix_keys.indexOf( 'attr_' + self.data( 'taxonomy-name' ) ) > -1 ) {
                    var cell = $( cell_matrix[ ckd ][ current_matrix_keys.indexOf( 'attr_' + self.data( 'taxonomy-name' ) ) ] ),
                        s    = self.val();

                    switch ( s ) {
                        case 'yes':
                            cell.find( '.yith-wcbep-attr-is-variation' ).val( '1' );
                            break;
                        case 'no':
                            cell.find( '.yith-wcbep-attr-is-variation' ).val( '0' );
                            break;
                    }
                }
            } );

        }
        bulk_editor.fadeOut();
        reset_bulk_editor();
        controller_test( true );
        $( '#wpwrap' ).unblock();
    } );

    f_reset_btn.on( 'click', function () {
        reset_filters();
    } );

    f_check_by_filters.on( 'click', function () {
        var f_attributes       = [],
            checked_by_filters = [];
        attr_filter_select.each( function ( index, element ) {
            var my_attr_filter = $( element );
            var attr_choosed   = ( my_attr_filter.val() != null ) ? my_attr_filter.val() : null;
            for ( var idx in attr_choosed ) {
                attr_choosed[ idx ] = parseInt( attr_choosed[ idx ] );
            }
            f_attributes.push( [my_attr_filter.data( 'taxonomy-name' ), attr_choosed] );
        } );

        current_matrix         = create_current_matrix();
        var categories_choosed = ( f_categories.val() != null ) ? f_categories.val().map( parseInt ) : null,
            tags_choosed       = [];

        for ( var k in f_tags.val() ) {
            var tag_name = f_tags.find( 'option[value="' + f_tags.val()[ k ] + '"]' ).html();
            tags_choosed.push( tag_name );
        }

        for ( var i in current_matrix ) {
            var t_row  = current_matrix[ i ],
                finded = true;

            // TITLE
            if ( f_title_value.val().length > 0 ) {
                var this_title = t_row[ current_matrix_keys.indexOf( 'title' ) ];

                if ( this_title.length < 1 ) {
                    finded = false;
                }

                switch ( f_title_select.val() ) {
                    case 'cont':
                        if ( this_title.indexOf( f_title_value.val() ) < 0 ) {
                            finded = false;
                        }
                        break;
                    case 'notcont':
                        if ( this_title.indexOf( f_title_value.val() ) > -1 ) {
                            finded = false;
                        }
                        break;
                    case 'starts':
                        if ( this_title.indexOf( f_title_value.val() ) != 0 ) {
                            finded = false;
                        }
                        break;
                    case 'ends':
                        if ( this_title.indexOf( f_title_value.val() ) != ( this_title.length - f_title_value.val().length ) ) {
                            finded = false;
                        }
                        break;
                }
            }

            // SKU
            if ( f_sku_value.val().length > 0 ) {
                var this_sku = t_row[ current_matrix_keys.indexOf( 'sku' ) ];

                if ( this_sku.length < 1 ) {
                    finded = false;
                }

                switch ( f_sku_select.val() ) {
                    case 'cont':
                        if ( this_sku.indexOf( f_sku_value.val() ) < 0 ) {
                            finded = false;
                        }
                        break;
                    case 'notcont':
                        if ( this_sku.indexOf( f_sku_value.val() ) > -1 ) {
                            finded = false;
                        }
                        break;
                    case 'starts':
                        if ( this_sku.indexOf( f_sku_value.val() ) !== 0 ) {
                            finded = false;
                        }
                        break;
                    case 'ends':
                        if ( this_sku.indexOf( f_sku_value.val() ) !== ( this_sku.length - f_sku_value.val().length ) ) {
                            finded = false;
                        }
                        break;
                }
            }

            // DESCRIPTION
            if ( f_description_value.val().length > 0 ) {
                var this_description = t_row[ current_matrix_keys.indexOf( 'description' ) ];

                if ( this_description.length < 1 ) {
                    finded = false;
                }

                switch ( f_description_select.val() ) {
                    case 'cont':
                        if ( this_description.indexOf( f_description_value.val() ) < 0 ) {
                            finded = false;
                        }
                        break;
                    case 'notcont':
                        if ( this_description.indexOf( f_description_value.val() ) > -1 ) {
                            finded = false;
                        }
                        break;
                    case 'starts':
                        if ( this_description.indexOf( f_description_value.val() ) != 0 ) {
                            finded = false;
                        }
                        break;
                    case 'ends':
                        if ( this_description.indexOf( f_description_value.val() ) != ( this_description.length - f_description_value.val().length ) ) {
                            finded = false;
                        }
                        break;
                }
            }

            // CATEGORIES
            if ( categories_choosed != null && categories_choosed.length > 0 ) {
                var this_categories = $.parseJSON( t_row[ current_matrix_keys.indexOf( 'categories' ) ] );
                if ( !this_categories || this_categories.length < 1 ) {
                    finded = false;
                }

                if ( this_categories ) {
                    for ( var j in categories_choosed ) {
                        if ( this_categories.indexOf( categories_choosed[ j ] ) < 0 ) {
                            finded = false;
                        }
                    }
                }

            }

            // TAGS
            if ( tags_choosed != null && tags_choosed.length > 0 ) {
                var this_tags = t_row[ current_matrix_keys.indexOf( 'tags' ) ].split( ', ' );
                if ( this_tags.length < 1 ) {
                    finded = false;
                }

                for ( var j in tags_choosed ) {
                    if ( this_tags.indexOf( tags_choosed[ j ] ) < 0 ) {
                        finded = false;
                    }
                }
            }

            // ATTIBUTES
            for ( var j in f_attributes ) {
                if ( f_attributes[ j ][ 1 ] != null && f_attributes[ j ][ 1 ].length > 0 ) {
                    var this_attrs = t_row[ current_matrix_keys.indexOf( 'attr_' + f_attributes[ j ][ 0 ] ) ][ 2 ];
                    if ( this_attrs.length < 1 ) {
                        finded = false;
                    }

                    var attrs_choosed = f_attributes[ j ][ 1 ];
                    for ( var k in attrs_choosed ) {
                        if ( this_attrs.indexOf( attrs_choosed[ k ] ) < 0 ) {
                            finded = false;
                        }
                    }
                }
            }

            // REGULAR PRICE
            if ( f_reg_price_value.val().length > 0 ) {
                var this_reg_price = parseFloat( t_row[ current_matrix_keys.indexOf( 'regular_price' ) ] ),
                    filter_price   = parseFloat( f_reg_price_value.val() );

                switch ( f_reg_price_select.val() ) {
                    case 'mag':
                        if ( !( this_reg_price > filter_price ) ) {
                            finded = false;
                        }
                        break;
                    case 'min':
                        if ( !( this_reg_price < filter_price ) ) {
                            finded = false;
                        }
                        break;
                    case 'ug':
                        if ( !( this_reg_price == filter_price ) ) {
                            finded = false;
                        }
                        break;
                    case 'magug':
                        if ( !( this_reg_price >= filter_price ) ) {
                            finded = false;
                        }
                        break;
                    case 'minug':
                        if ( !( this_reg_price <= filter_price ) ) {
                            finded = false;
                        }
                        break;
                }
            }

            // SALE PRICE
            if ( f_sale_price_value.val().length > 0 ) {
                var this_sale_price = parseFloat( t_row[ current_matrix_keys.indexOf( 'sale_price' ) ] ),
                    filter_price    = parseFloat( f_sale_price_value.val() );

                switch ( f_sale_price_select.val() ) {
                    case 'mag':
                        if ( !( this_sale_price > filter_price ) ) {
                            finded = false;
                        }
                        break;
                    case 'min':
                        if ( !( this_sale_price < filter_price ) ) {
                            finded = false;
                        }
                        break;
                    case 'ug':
                        if ( !( this_sale_price == filter_price ) ) {
                            finded = false;
                        }
                        break;
                    case 'magug':
                        if ( !( this_sale_price >= filter_price ) ) {
                            finded = false;
                        }
                        break;
                    case 'minug':
                        if ( !( this_sale_price <= filter_price ) ) {
                            finded = false;
                        }
                        break;
                }
            }

            // WEIGHT
            if ( f_weight_value.val().length > 0 ) {
                var this_weight   = parseFloat( t_row[ current_matrix_keys.indexOf( 'weight' ) ] ),
                    filter_weight = parseFloat( f_weight_value.val() );

                switch ( f_weight_select.val() ) {
                    case 'mag':
                        if ( !( this_weight > filter_weight ) ) {
                            finded = false;
                        }
                        break;
                    case 'min':
                        if ( !( this_weight < filter_weight ) ) {
                            finded = false;
                        }
                        break;
                    case 'ug':
                        if ( !( this_weight == filter_weight ) ) {
                            finded = false;
                        }
                        break;
                    case 'magug':
                        if ( !( this_weight >= filter_weight ) ) {
                            finded = false;
                        }
                        break;
                    case 'minug':
                        if ( !( this_weight <= filter_weight ) ) {
                            finded = false;
                        }
                        break;
                }
            }

            // STOCK QTY
            if ( f_stock_qty_value.val().length > 0 ) {
                var this_stock_qty   = parseFloat( t_row[ current_matrix_keys.indexOf( 'stock_quantity' ) ] ),
                    filter_stock_qty = parseFloat( f_stock_qty_value.val() );

                switch ( f_stock_qty_select.val() ) {
                    case 'mag':
                        if ( !( this_stock_qty > filter_stock_qty ) ) {
                            finded = false;
                        }
                        break;
                    case 'min':
                        if ( !( this_stock_qty < filter_stock_qty ) ) {
                            finded = false;
                        }
                        break;
                    case 'ug':
                        if ( !( this_stock_qty == filter_stock_qty ) ) {
                            finded = false;
                        }
                        break;
                    case 'magug':
                        if ( !( this_stock_qty >= filter_stock_qty ) ) {
                            finded = false;
                        }
                        break;
                    case 'minug':
                        if ( !( this_stock_qty <= filter_stock_qty ) ) {
                            finded = false;
                        }
                        break;
                }
            }

            // STOCK Status
            if ( f_stock_status.val() ) {
                var this_stock_status   = t_row[ current_matrix_keys.indexOf( 'stock_status' ) ],
                    filter_stock_status = f_stock_status.val();

                if ( filter_stock_status ) {
                    finded = this_stock_status === filter_stock_status;
                }
            }

            // Visibility
            if ( f_visibility.val().length > 0 ) {
                var this_visibility   = t_row[ current_matrix_keys.indexOf( 'visibility' ) ],
                    filter_visibility = f_visibility.val();
                if ( filter_visibility ) {
                    finded = this_visibility === filter_visibility;
                }
            }

            // Allow Backorders
            if ( f_allow_backorders.val().length > 0 ) {
                var this_allow_backorders   = t_row[ current_matrix_keys.indexOf( 'allow_backorders' ) ],
                    filter_allow_backorders = f_allow_backorders.val();
                if ( filter_allow_backorders ) {
                    finded = this_allow_backorders === filter_allow_backorders;
                }
            }

            // Status
            if ( f_status.val().length > 0 ) {
                var this_status   = t_row[ current_matrix_keys.indexOf( 'status' ) ],
                    filter_status = f_status.val();
                if ( filter_status ) {
                    finded = this_status === filter_status;
                }
            }

            // Shipping class
            if ( f_shipping_class.val().length > 0 && f_shipping_class.prop( 'selectedIndex' ) ) {
                var this_shipping_class   = t_row[ current_matrix_keys.indexOf( 'shipping_class' ) ],
                    filter_shipping_class = f_shipping_class.val();
                if ( filter_shipping_class ) {
                    finded = this_shipping_class === filter_shipping_class;
                }
            }

            //INCLUDE VARIATIONS
            var include_variation_in_cheched = f_show_variations[ 0 ].checked ? 'yes' : 'no';
            if ( include_variation_in_cheched == 'no' ) {
                var tmp = $( t_row[ current_matrix_keys.indexOf( 'prod_type' ) ] );
                if ( tmp.is( '.not_editable' ) ) {
                    finded = false;
                }
            }

            if ( finded ) {
                checked_by_filters.push( parseInt( i ) );
            }
        }

        var all_checkbox = table.find( 'tbody#the-list th.check-column > input' ),
            loop         = 0;

        all_checkbox.each( function () {
            if ( checked_by_filters.indexOf( loop ) > -1 ) {
                $( this ).attr( 'checked', true );
            } else {
                $( this ).attr( 'checked', false );
            }
            loop++;
        } );

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

            scrollTo( products_wrap );

            var bulk_edit_length     = matrix_modify.length,
                bulk_edit_processing = function ( bulk_edit_index ) {
                    if ( bulk_edit_index < bulk_edit_length ) {
                        var post_data = {
                            keys  : current_matrix_keys,
                            row   : matrix_modify[ bulk_edit_index ],
                            edited: current_edited_matrix[ bulk_edit_index ],
                            action: 'yith_wcbep_bulk_edit_products'
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
                                        percentual = parseInt( 100 * ( edit_count - to_edit ) / edit_count );

                                        percentual_span.html( percentual + '%' );
                                        percentual_span.animate( {
                                                                     width: percentual + '%'
                                                                 } );
                                        bulk_edit_processing( ++bulk_edit_index );
                                    }
                                } );
                    } else {
                        list.update();
                        percentual_container.delay( 1500 ).fadeOut();
                    }
                };
            // call the first processing
            if ( bulk_edit_length > 0 ) {
                bulk_edit_processing( 0 );
            }

        }
    } );

    // COLUMNS SETTINGS
    cols_settings_btn.on( 'click', function ( e ) {
        e.stopPropagation();
        custom_input_hide( true );

        var top = $( window ).scrollTop() + 100;
        cols_settings.css( {
                               top: top
                           } );

        cols_settings.fadeIn();
    } );

    cols_settings.on( 'click', 'input[type=checkbox]', function ( e ) {
        control_visible_columns();
    } );

    cols_settings_select_all_btn.on( 'click', function () {
        cols_settings.find( 'input' ).attr( 'checked', true );
        cols_settings.find( 'input' ).first().attr( 'checked', false ).trigger( 'click' );
    } );

    cols_settings_unselect_all_btn.on( 'click', function () {
        cols_settings.find( 'input' ).attr( 'checked', false );
        cols_settings.find( 'input' ).first().attr( 'checked', true ).trigger( 'click' );
    } );

    // AJAX WP_TABLE_LIST
    list = {

        f_data: {
            paged            : '1',
            order            : 'desc',
            orderby          : 'ID',
            f_show_variations: 'no',
            lang             : wpml_current_lang
        },

        init: function () {
            var timer;
            var delay = 500;

            table_init();

            $( '.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a' ).on( 'click', function ( e ) {
                e.preventDefault();
                if ( index_of_matrix > 1 ) {
                    return;
                }

                var query = this.search.substring( 1 );

                var f_attributes = [];
                attr_filter_select.each( function () {
                    f_attributes.push( [$( this ).data( 'taxonomy-name' ), $( this ).val()] );
                } );

                $.extend(
                    list.f_data,
                    {
                        paged  : list.__query( query, 'paged' ) || 1,
                        order  : list.__query( query, 'order' ) || list.f_data.order,
                        orderby: list.__query( query, 'orderby' ) || list.f_data.orderby
                    }
                );


                list.update( list.f_data );
            } );

            $( 'input[name=paged]' ).on( 'change', function ( e ) {

                if ( 13 == e.which ) {
                    e.preventDefault();
                }

                var query       = $( '.tablenav-pages a' )[ 0 ].search.substring( 1 ),
                    paged       = parseInt( $( this ).val() ),
                    total_pages = parseInt( $( '#total_pages' ).val() );

                paged = Math.min( paged, total_pages );
                paged = Math.max( paged, 1 );

                $.extend(
                    list.f_data,
                    {
                        paged  : paged,
                        order  : list.__query( query, 'order' ) || list.f_data.order,
                        orderby: list.__query( query, 'orderby' ) || list.f_data.orderby
                    }
                );

                window.clearTimeout( timer );
                timer = window.setTimeout( function () {
                    list.update( list.f_data );
                }, delay );
            } );
        },

        /** AJAX call
         *
         * Send the call and replace table parts with updated version!
         *
         */
        update: function ( data ) {
            table.block( block_params );
            save_btn.prop( 'disabled', true );
            bulk_edit_btn.prop( 'disabled', true );

            if ( typeof data != 'undefined' ) {
                $.extend( list.f_data, data );
            }

            if ( f_brands.length ) {
                list.f_data = $.extend(
                    {
                        f_brands: f_brands.val()
                    },
                    data
                );
            }

            if ( f_custom_taxonomies.length ) {
                var custom_taxonomies_data = [];
                f_custom_taxonomies.each( function () {
                    var single_taxonomy_values = $( this ).val();
                    if ( single_taxonomy_values ) {
                        var single_taxonomy_data = {
                            taxonomy: $( this ).data( 'taxonomy' ),
                            values  : single_taxonomy_values
                        };

                        custom_taxonomies_data.push( single_taxonomy_data );
                    }
                } );
                list.f_data = $.extend(
                    list.f_data,
                    {
                        f_custom_taxonomies: custom_taxonomies_data
                    }
                );
            }

            $.ajax( {
                        url    : ajaxurl,
                        data   : $.extend(
                            {
                                _ajax_yith_wcbep_list_nonce: $( '#_ajax_yith_wcbep_list_nonce' ).val(),
                                action                     : '_ajax_fetch_yith_wcbep_list'
                            },
                            list.f_data
                        ),
                        success: function ( resp ) {

                            var response = $.parseJSON( resp );

                            // Add the requested rows
                            if ( response.rows.length ) {
                                $( '#the-list' ).html( response.rows );
                            }
                            // Update column headers for sorting
                            if ( response.column_headers.length ) {
                                $( 'thead tr, tfoot tr' ).html( response.column_headers );
                            }
                            // Update pagination for navigation
                            if ( response.pagination.bottom.length ) {
                                $( '.tablenav.top .tablenav-pages' ).html( $( response.pagination.top ).html() );
                            }
                            if ( response.pagination.top.length ) {
                                $( '.tablenav.bottom .tablenav-pages' ).html( $( response.pagination.bottom ).html() );
                            }

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
         * @param {string} query The URL query part containing the variables
         * @param {string} variable Name of the variable we want to get
         *
         * @return string|boolean The variable value if available, false else.
         */
        __query: function ( query, variable ) {

            var vars = query.split( "&" );
            for ( var i = 0; i < vars.length; i++ ) {
                var pair = vars[ i ].split( "=" );
                if ( pair[ 0 ] == variable ) {
                    return pair[ 1 ];
                }
            }
            return false;
        }
    };

    // And now Show it!
    list.init();


    /**
     * Filter Visibility
     */
    var filterRowCategories = $( '.yith-wcbep-filter-row__categories' ),
        filterRowTags       = $( '.yith-wcbep-filter-row__tags' );

    f_product_type.on( 'change', function () {
        if ( $( this ).val() !== 'variation' ) {
            filterRowCategories.show();
            filterRowTags.show();
        } else {
            f_categories.val( '' ).change();
            f_tags.val( '' ).change();

            filterRowCategories.hide();
            filterRowTags.hide();
        }
    } );
} );