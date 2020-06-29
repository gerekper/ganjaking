/* global yith_wcpb_params, ajax_obj */

jQuery( function ( $ ) {
    $.fn.yith_bundle_form = function () {
        $( this ).each( function () {
            var $form                              = $( this ),
                product_id                         = $form.data( 'product-id' ),
                per_item_pricing                   = $form.data( 'per-item-pricing' ),
                ajax_update_price_enabled          = $form.data( 'ajax-update-price' ),
                $bundled_items_prices              = $form.find( '.yith-wcpb-product-bundled-items .price' ),
                $price_handler                     = $form.closest( yith_wcpb_params.price_handler_parent ).find( yith_wcpb_params.price_handler ) || $form.closest( yith_wcpb_params.price_handler_parent_alt ).find( yith_wcpb_params.price_handler_alt ),
                $price                             = yith_wcpb_params.price_handler_only_first == 1 ? $price_handler.not( $bundled_items_prices ).first() : $price_handler.not( $bundled_items_prices ),
                $add_to_cart                       = $form.closest( 'form' ).find( 'button[type=submit]' ),
                $add_to_quote                      = $form.closest( '.product' ).find( '.add-request-quote-button' ),
                $variation_forms                   = $form.find( '.bundled_item_cart_content' ),
                $qty_fields                        = $form.find( 'input.yith-wcpb-bundled-quantity' ),
                $opt_fields                        = $form.find( '.yith-wcpb-bundled-optional' ),
                $variations                        = $form.find( '.variation_id' ),
                $currency                          = $form.find( 'input[name=yith_wcpb_wpml_client_currency]' ),
                ajax_call                          = null,
                check_disable_btn                  = function () {
                    var isDisabled               = false,
                        variationSelectionNeeded = false,
                        outOfStockItemSelected   = false,
                        classes                  = 'disabled',
                        allClasses               = ['disabled', 'yith-wcpb-variation-selection-needed', 'yith-wcpb-out-of-stock-item-selected'].join( ' ' );

                    $variation_forms.each( function () {
                        var $_variationForm  = $( this ),
                            $_optional       = $_variationForm.find( '.yith-wcpb-bundled-optional' ),
                            $_quantity       = $_variationForm.find( '.yith-wcpb-bundled-quantity' ),
                            $_variableSelect = $_variationForm.find( 'select.yith-wcpb-select-for-variables' ),
                            isOptional       = $_optional.length,
                            optionalChecked  = isOptional && $_optional.is( ':checked' ),
                            hasZeroQuantity  = $_quantity.length && parseInt( $_quantity.val() ) === 0;

                        if ( ( !isOptional || optionalChecked ) && !hasZeroQuantity ) {
                            $_variableSelect.each( function () {
                                if ( !$( this ).val() ) {
                                    isDisabled               = true;
                                    variationSelectionNeeded = true;
                                }
                            } );
                            $_variationForm.find( '.variations' ).slideDown( 'fast' );
                            $_variationForm.find( '.single_variation_wrap' ).slideDown( 'fast' );

                            if ( $_variationForm.find( '.out-of-stock' ).length ) {
                                isDisabled             = true;
                                outOfStockItemSelected = true;
                            }
                        } else {
                            if ( isOptional ) {
                                $_variationForm.find( '.quantity input.qty' ).removeAttr( 'max' );
                                $_variationForm.find( '.single_variation_wrap' ).slideUp( 'fast' );
                                $_variationForm.find( '.variations' ).slideUp( 'fast' );
                            }
                        }
                    } );

                    if ( variationSelectionNeeded ) {
                        classes += ' yith-wcpb-variation-selection-needed';
                    }

                    if ( outOfStockItemSelected ) {
                        classes += ' yith-wcpb-out-of-stock-item-selected';
                    }

                    if ( isDisabled ) {
                        $add_to_cart.addClass( classes );
                    } else {
                        $add_to_cart.removeClass( allClasses );
                    }

                    // integration with Request a quote
                    if ( $add_to_quote.length ) {
                        if ( isDisabled ) {
                            $add_to_quote.addClass( classes );
                        } else {
                            $add_to_quote.removeClass( allClasses );
                        }
                    }
                },
                block_params                       = {
                    message        : null,
                    overlayCSS     : {
                        background: '#fff',
                        opacity   : 0.6
                    },
                    ignoreIfBlocked: true
                },
                update_price                       = function () {
                    if ( ajax_call ) {
                        ajax_call.abort();
                    }

                    if ( ajax_update_price_enabled != 1 ) {
                        return;
                    }

                    $price.block( block_params );

                    var array_qty = [];
                    var array_opt = [];
                    var array_var = [];

                    $qty_fields.each( function () {
                        array_qty[ $( this ).data( 'item-id' ) - 1 ] = $( this ).val();
                    } );

                    $opt_fields.each( function () {
                        array_opt[ $( this ).data( 'item-id' ) - 1 ] = $( this ).is( ':checked' ) ? 1 : 0;
                    } );

                    $variations.each( function () {
                        array_var[ $( this ).data( 'item-id' ) - 1 ] = $( this ).val();
                    } );

                    /* WPML - Multi Currency */
                    var client_currency = $currency.length > 0 ? $currency.val() : '';

                    var post_data = {
                        bundle_id                     : product_id,
                        array_qty                     : array_qty,
                        array_opt                     : array_opt,
                        array_var                     : array_var,
                        yith_wcpb_wpml_client_currency: client_currency,
                        action                        : 'yith_wcpb_get_bundle_total_price'
                    };

                    ajax_call = $.ajax( {
                                            type   : "POST",
                                            data   : post_data,
                                            url    : ajax_obj.ajaxurl,
                                            success: function ( response ) {
                                                var price_to_upload = $price.find( 'ins .amount' );
                                                if ( price_to_upload.length < 1 ) {
                                                    price_to_upload = $price.find( '.amount' );
                                                }

                                                if ( price_to_upload.length < 1 ) {
                                                    price_to_upload = $price;
                                                }

                                                price_to_upload = price_to_upload.first();
                                                price_to_upload.html( response.price_html );
                                                $price.html( response.price_html );

                                                $( document ).trigger( 'yith_wcpb_ajax_update_price_request', response );

                                                $price.unblock();
                                            }
                                        } );
                },
                show_alert_for_invalid_add_to_cart = function ( event ) {
                    if ( $( this ).is( '.disabled' ) ) {
                        event.preventDefault();

                        if ( $( this ).is( '.yith-wcpb-out-of-stock-item-selected' ) ) {
                            window.alert( yith_wcpb_params.i18n.out_of_stock_item_selected );
                        } else if ( $( this ).is( '.yith-wcpb-variation-selection-needed' ) ) {
                            window.alert( yith_wcpb_params.i18n.variation_selection_needed );
                        }
                    }
                }
            ;

            $add_to_cart.on( 'click', show_alert_for_invalid_add_to_cart );
            $add_to_quote.on( 'click', show_alert_for_invalid_add_to_cart );

            $form.on( 'yith_wcpb_update_price', function () {
                check_disable_btn();
                update_price();
            } );

            if ( yith_wcpb_params.update_price_on_load === 'yes' ) {
                $form.trigger( 'yith_wcpb_update_price' );
            }

            $qty_fields.on( 'change', function () {
                if ( !$( this ).parents( '.bundled_item_cart_content' ).length ) {
                    $form.trigger( 'yith_wcpb_update_price' );
                }
            } );

            $opt_fields.on( 'click', function () {
                if ( !$( this ).parents( '.bundled_item_cart_content' ).length ) {
                    $form.trigger( 'yith_wcpb_update_price' );
                }

            } );

            $variation_forms.on( 'change', function () {
                $form.trigger( 'yith_wcpb_update_price' );
            } );

            $variation_forms.on( 'found_variation', function ( event, variation ) {
                var $current_product = $( this ).closest( '.product' ),
                    $prices          = $current_product.find( '.yith-wcpb-product-bundled-item-image .price' ).first(),
                    $price           = $prices.find( 'ins' ),
                    $real_price      = $prices.find( 'del' ),
                    $image           = $current_product.find( '.yith-wcpb-product-bundled-item-image-wrapper img' ).first(),
                    min_quantity     = $current_product.data( 'min-quantity' ),
                    max_quantity     = $current_product.data( 'max-quantity' ),
                    $qty             = $current_product.find( '.yith-wcpb-bundled-quantity' );

                if ( $qty.length ) {
                    $qty.attr( 'min', min_quantity );
                    $qty.attr( 'max', max_quantity );
                }

                if ( typeof variation.image !== 'undefined' ) {
                    if ( typeof variation.image.srcset !== 'undefined' && variation.image.srcset ) {
                        $image.attr( 'srcset', variation.image.srcset );
                    } else {
                        // prevent issues id srcset is false
                        if ( typeof variation.image.thumb_src !== 'undefined' && variation.image.thumb_src ) {
                            $image.removeAttr( 'srcset' );
                            $image.attr( 'src', variation.image.thumb_src );
                        }
                    }

                    if ( typeof variation.image.full_src !== 'undefined' && typeof variation.image.full_src_h !== 'undefined' && typeof variation.image.full_src_w !== 'undefined' ) {
                        $image.attr( 'data-large_image', variation.image.full_src );
                        $image.attr( 'data-large_image_width', variation.image.full_src_w );
                        $image.attr( 'data-large_image_height', variation.image.full_src_h );
                    }
                }

                $price.html( variation.price_html.replace( 'price', 'amount' ) );
                $real_price.html( variation.display_regular_price_html );
            } )
                .on( 'reset_data', function () {
                    var $current_product   = $( this ).closest( '.product' ),
                        $prices            = $current_product.find( '.yith-wcpb-product-bundled-item-image .price' ).first(),
                        $price             = $prices.find( 'ins' ),
                        default_price      = $price.data( 'default-ins' ),
                        $real_price        = $prices.find( 'del' ),
                        default_real_price = $real_price.data( 'default-ins' ),
                        $image             = $current_product.find( '.yith-wcpb-product-bundled-item-image-wrapper img' ).first(),
                        new_image_src      = $image.attr( 'src' );

                    if ( 'undefined' !== typeof new_image_src && new_image_src.length > 0 ) {
                        $image.attr( 'srcset', new_image_src );
                    }

                    $price.html( default_price );
                    $real_price.html( default_real_price );
                } );

            // trigger the check_variation to show the variation prices if a variation is selected by default
            $variation_forms.trigger( 'check_variations' );
            // display only available variations
            $variation_forms.trigger( 'update_variation_values' );
        } );
    };


    $( document ).on( 'yith_wcpb_add_to_cart_init', function () {
        $( '.yith-wcpb-bundle-form' ).yith_bundle_form();

    } ).trigger( 'yith_wcpb_add_to_cart_init' );

    // compatibility with YITH WooCommerce Quick View
    $( document ).on( 'qv_loader_stop', function () {
        $( document ).trigger( 'yith_wcpb_add_to_cart_init' );
    } );

    /**
     * Flatsome Quick View integration
     *
     * Flatsome uses "Magnific Popup" jQuery plugin
     *
     * @see https://dimsemenov.com/plugins/magnific-popup/documentation.html
     */
    $( document ).on( 'mfpOpen', function () {
        $( '.variations_form' ).each( function () {
            $( this ).wc_variation_form();
        } );
        $( document ).trigger( 'yith_wcpb_add_to_cart_init' );
    } );


    var yith_wcpb_bundled_items_photoswipe = {
        enabled        : typeof PhotoSwipe !== 'undefined' && typeof PhotoSwipeUI_Default !== 'undefined' && typeof wc_single_product_params !== 'undefined' && 'yes' === yith_wcpb_params.photoswipe_enabled,
        init           : function () {
            $( document ).on( 'click', '.yith-wcpb-product-bundled-item-image-wrapper .woocommerce-product-gallery__image a', this.openPhotoswipe );
            this.indexGallery();
        },
        getImages      : function () {
            return $( '.yith-wcpb-product-bundled-item-image-wrapper .woocommerce-product-gallery__image' );
        },
        indexGallery   : function () {
            var $images = this.getImages(),
                loop    = 0;

            $images.each( function () {
                $( this ).data( 'bundled-image-index', loop );
                loop++;
            } );
        },
        getGalleryItems: function () {
            var $images = this.getImages(),
                items   = [];

            if ( $images.length > 0 ) {
                $images.each( function ( i, el ) {
                    var img = $( el ).find( 'img' );

                    if ( img.length ) {
                        var large_image_src = img.attr( 'data-large_image' ),
                            large_image_w   = img.attr( 'data-large_image_width' ),
                            large_image_h   = img.attr( 'data-large_image_height' ),
                            item            = {
                                src  : large_image_src,
                                w    : large_image_w,
                                h    : large_image_h,
                                title: img.attr( 'data-caption' ) ? img.attr( 'data-caption' ) : img.attr( 'title' )
                            };
                        items.push( item );
                    }
                } );
            }

            return items;
        },
        openPhotoswipe : function ( e ) {
            e.preventDefault();
            if ( !yith_wcpb_bundled_items_photoswipe.enabled ) {
                return;
            }

            var pswpElement = $( '.pswp' )[ 0 ],
                items       = yith_wcpb_bundled_items_photoswipe.getGalleryItems(),
                eventTarget = $( e.target ),
                clicked     = eventTarget.closest( '.woocommerce-product-gallery__image' );

            var options = $.extend( {
                                        index: $( clicked ).data( 'bundled-image-index' )
                                    }, wc_single_product_params.photoswipe_options );

            // Initializes and opens PhotoSwipe.
            var photoswipe = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options );
            photoswipe.init();
        }
    };

    yith_wcpb_bundled_items_photoswipe.init();
} );