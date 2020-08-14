jQuery( function ( $ ) {
    "use strict";

    var product_type = $( 'select#product-type' ),
        post_id      = woocommerce_admin_meta_boxes.post_id,
        block_params = {
            message   : null,
            overlayCSS: {
                background: '#fff',
                opacity   : 0.7
            }
        },
        tiptip_args  = {
            'attribute': 'data-tip',
            'fadeIn'   : 50,
            'fadeOut'  : 50,
            'delay'    : 200
        },
        isBundle     = function () {
            return 'yith_bundle' === product_type.val();
        };


    var yith_wcbp_metabox = {
        el      : {
            add_item_btn        : $( '#yith-wcpb-add-bundled-product' ),
            bundled_items       : $( '#yith_bundled_product_data .yith-wcpb-bundled-items' ),
            items_count         : $( '#yith_bundled_product_data .yith-wcpb-bundled-items .yith-wcpb-bundled-item' ).size() + 1,
            ajax_filter_products: null
        },
        init    : function () {
            this.el.add_item_btn.on( 'click', this.select_products );
            $( document ).on( 'click', '.yith-wcpb-add-product', this.add_item );
            $( document ).on( 'keyup', 'input.yith-wcpb-select-product-box__filter__search', this.search_filter );

            $( document ).on( 'click', '.yith-wcpb-remove-bundled-product-item', this.remove_current_item );
            $( document ).on( 'click', '.yith-wcpb-bundled-item h3 a', this.stop_event_propagation );

            $( document ).on( 'click', '.yith-wcpb-select-product-box__products__pagination .first:not(.disabled)', this.paginate );
            $( document ).on( 'click', '.yith-wcpb-select-product-box__products__pagination .prev:not(.disabled)', this.paginate );
            $( document ).on( 'click', '.yith-wcpb-select-product-box__products__pagination .next:not(.disabled)', this.paginate );
            $( document ).on( 'click', '.yith-wcpb-select-product-box__products__pagination .last:not(.disabled)', this.paginate );

            this.sorting();
        },
        add_item: function () {
            var row        = $( this ).closest( 'tr' ),
                added      = row.find( '.yith-wcpb-product-added' ),
                product_id = $( this ).data( 'id' ),
                products   = $( this ).closest( '.yith-wcpb-select-product-box__products' );

            if ( product_id ) {
                products.block( block_params );

                var data = {
                    action     : 'yith_wcpb_add_product_in_bundle',
                    open_closed: 'open',
                    post_id    : post_id,
                    id         : yith_wcbp_metabox.el.items_count,
                    product_id : product_id
                };

                $.ajax( {
                            type    : 'POST',
                            url     : ajaxurl,
                            data    : data,
                            success : function ( response ) {
                                if ( response.error ) {
                                    alert( response.error );
                                } else if ( response.html ) {
                                    yith_wcbp_metabox.el.bundled_items.append( response.html );
                                    yith_wcbp_metabox.el.bundled_items.find( '.help_tip, .woocommerce-help-tip' ).tipTip( tiptip_args );
                                    $( 'body' ).trigger( 'wc-enhanced-select-init' );
                                    yith_wcbp_metabox.el.items_count++;
                                }

                            },
                            complete: function () {
                                products.unblock();
                                added.fadeIn().delay( 1000 ).fadeOut();
                            }
                        } );
            }
        },

        remove_current_item: function () {
            $( this ).parent().parent().remove();
        },

        filter_products: function ( data ) {
            if ( data.s !== undefined && data.s.length < 3 ) {
                data.s = '';
            }

            data = $.extend( data, { action: 'yith_wcpb_select_product_box_filtered' } );

            var products = $( '.yith-wcpb-select-product-box__products' );
            products.block( block_params );

            if ( yith_wcbp_metabox.el.ajax_filter_products ) {
                yith_wcbp_metabox.el.ajax_filter_products.abort();
            }

            yith_wcbp_metabox.el.ajax_filter_products =
                $.ajax( {
                            type    : 'POST',
                            url     : ajaxurl,
                            data    : data,
                            success : function ( response ) {
                                products.html( response );
                            },
                            complete: function ( jqXHR, textStatus ) {
                                if ( textStatus !== 'abort' ) {
                                    products.unblock( block_params );
                                }
                            }
                        } );
        },

        paginate: function () {
            var page = $( this ).data( 'page' );
            if ( page !== undefined ) {
                var search_filter_value = $( 'input.yith-wcpb-select-product-box__filter__search' ).val();
                yith_wcbp_metabox.filter_products( { s: search_filter_value, page: page } );
            }
        },

        search_filter: function () {
            var value = $( this ).val();
            if ( !value || value.length >= 3 ) {
                yith_wcbp_metabox.filter_products( { s: value } );
            }
        },

        select_products       : function () {
            $.fn.yith_wcpb_popup( {
                                      ajax        : true,
                                      url         : ajaxurl,
                                      ajax_data   : {
                                          action: 'yith_wcpb_select_product_box'
                                      },
                                      ajax_success: function () {
                                          $( '.yith-wcpb-select-product-box__filter__search' ).focus();
                                      }
                                  } );
        },
        sorting               : function () {
            var bundled_items = this.el.bundled_items.find( '.yith-wcpb-bundled-item' ).get();

            bundled_items.sort( function ( a, b ) {
                var compA = parseInt( $( a ).attr( 'rel' ) );
                var compB = parseInt( $( b ).attr( 'rel' ) );
                return ( compA < compB ) ? -1 : ( compA > compB ) ? 1 : 0;
            } );

            $( bundled_items ).each( function ( idx, itm ) {
                yith_wcbp_metabox.el.bundled_items.append( itm );
            } );

            this.el.bundled_items.sortable( {
                                                items               : '.yith-wcpb-bundled-item',
                                                cursor              : 'move',
                                                axis                : 'y',
                                                handle              : 'h3',
                                                scrollSensitivity   : 40,
                                                forcePlaceholderSize: true,
                                                helper              : 'clone',
                                                opacity             : 0.65,
                                                placeholder         : 'wc-metabox-sortable-placeholder',
                                                start               : function ( event, ui ) {
                                                    ui.item.css( 'background-color', '#f6f6f6' );
                                                },
                                                stop                : function ( event, ui ) {
                                                    ui.item.removeAttr( 'style' );
                                                }
                                            } );
        },
        stop_event_propagation: function ( event ) {
            event.stopPropagation();
        }
    };

    yith_wcbp_metabox.init();


    $( 'body' ).on( 'woocommerce-product-type-change', function ( event, select_val, select ) {

        if ( select_val === 'yith_bundle' ) {
            $( '.pricing' ).show();
            $( '.product_data_tabs' ).find( 'li.general_options' ).show();

            $( '.show_if_external' ).hide();
            $( '.show_if_simple' ).show();
            $( '.show_if_bundle' ).show();

            $( 'input#_downloadable' ).prop( 'checked', false ).closest( '.show_if_simple' ).hide();
            $( 'input#_virtual' ).removeAttr( 'checked' ).closest( '.show_if_simple' ).hide();

            $( 'input#_manage_stock' ).change();

            $( '.hide_if_bundle' ).hide();

            $( 'a[href="#yith_bundled_product_data"]' ).click();
        } else {
            $( '.show_if_bundle' ).hide();
            $( '.hide_if_bundle' ).show();
        }

    } );

    product_type.change();

} );