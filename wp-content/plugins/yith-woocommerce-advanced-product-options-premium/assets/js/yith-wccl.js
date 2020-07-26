/**
 * Frontend
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Colors and Labels Variations Premium
 * @version 1.0.0
 */

;(function( $, window, document ){

    if ( typeof yith_wccl_general === 'undefined' )
        return false;

    /**
     * Matches inline variation objects to chosen attributes and return variation
     * @type {Object}
     */
    var variations_match = function( form, value, current_attribute_name ) {
        var match = false,
            product_variations = form.data( 'product_variations' ),
            all_select = form.find( '.variations select' ),
            settings = [];

        // current selected values
        $.each( all_select, function(){
            var attribute_name = $( this ).data( 'attribute_name' ) || $( this ).attr( 'name' );
            if( current_attribute_name == attribute_name ) {
                settings[attribute_name] = value;
            }
            else {
                settings[attribute_name] = $(this).val();
            }
        });

        for ( var i = 0; i < product_variations.length; i++ ) {
            var variation    = product_variations[i];

            // if found matching variation exit
            if( match ) {
                break;
            }

            match = variation;
            for ( var attr_name in variation.attributes ) {
                if ( variation.attributes.hasOwnProperty( attr_name ) ) {
                    var val1 = variation.attributes[ attr_name ],
                        val2 = settings[ attr_name ];

                    if ( val1 != val2 && val1 != '' ) {
                        match = false;
                    }
                }
            }
        }

        return match;
    };

    /**
     * Add to cart variation loop
     * @param event
     */
    var yith_wccl_add_cart = function( event ){

        event.preventDefault();

        var b          = $( this ),
            product_id = b.data( 'product_id' ),
            quantity   = b.data( 'quantity' ),
            attr = [],
            data = {};

        $.each( b.data(), function( key, value ) {
            data[ key ] = value;
        });

        // get select value
        event.data.select.each( function(index){
            attr[ index ] = this.name + '=' + this.value;
        });

        // Trigger event.
        $( document.body ).trigger( 'adding_to_cart', [ b, data ] );

        $.ajax({
            url: yith_wccl_general.ajaxurl.toString().replace( '%%endpoint%%', yith_wccl_general.actionAddCart ),
            type: 'POST',
            data: {
                action: yith_wccl_general.actionAddCart,
                product_id : product_id,
                variation_id : event.data.variation,
                attr: attr.join('&'),
                quantity: quantity,
                context: 'frontend'
            },
            beforeSend: function(){
                b.addClass( 'loading').removeClass( 'added' );
            },
            success: function( res ){

                // redirect to product page if some error occurred
                if ( res.error && res.product_url ) {
                    window.location = res.product_url;
                    return;
                }
                // redirect to cart
                if ( yith_wccl_general.cart_redirect ) {
                    window.location = yith_wccl_general.cart_url;
                    return;
                }

                // change button
                b.removeClass('loading').addClass('added');

                if( ! b.next('.added_to_cart').length ) {
                    b.after(' <a href="' + yith_wccl_general.cart_url + '" class="added_to_cart wc-forward" title="' + yith_wccl_general.view_cart + '">' + yith_wccl_general.view_cart + '</a>');
                }

                $( document.body ).trigger( 'wc_fragment_refresh' );
                // trigger refresh also cart page
                $( document ).trigger( 'wc_update_cart' );

                // added to cart
                $( document.body ).trigger( 'added_to_cart', [ res.fragments, res.cart_hash, b ] );
            }
        });
    }

    /**
     *
     * @param $form
     * @param attr
     * @constructor
     */
    var WCCL = function( $form, attr ) {

        this.$form              = $form;
        this.$attr              = ( typeof yith_wccl != 'undefined' ) ? JSON.parse( yith_wccl.attributes ) : attr;
        this.$select            = this.$form.find( '.variations select' );
        this.$use_ajax          = this.$form.data( 'product_variations' ) === false;
        // variables for loop
        this.$is_loop           = this.$form.hasClass('in_loop');
        this.$wrapper           = this.$form.closest( yith_wccl_general.wrapper_container_shop ).length ? this.$form.closest( yith_wccl_general.wrapper_container_shop ) : this.$form.closest('.product-add-to-cart' );
        this.$image             = this.$wrapper.find( yith_wccl_general.image_selector );
        this.$def_image_src     = ( this.$image.data('lazy-src') ) ? this.$image.data('lazy-src') : this.$image.attr( 'src' );
        this.$def_image_srcset  = ( this.$image.data('lazy-srcset') ) ? this.$image.data('lazy-srcset') : this.$image.attr( 'srcset' );
        this.$price_html        = this.$wrapper.find( 'span.price' ).clone().wrap('<p>').parent().html();
        this.$button            = this.$wrapper.find( 'a.product_type_variable' );
        this.$button_html       = this.$button.html();
        this.$input_qty         = this.$wrapper.find('input.thumbnail-quantity');
        this.$xhr               = null;

        // prevent undefined attr error
        if( typeof this.$attr == 'undefined' ) {
            this.$attr = [];
        }

        $form.on( 'yith_wccl_form_initialized', { obj: this }, this.init );

        // get default value
        this.$select.each( function() {
            this.setAttribute( 'data-default_value', this.value );
        });

        // reset form and select
        this.resetForm( this );

        if( this.$is_loop ) {
            $form.parent().on( 'change', function(e) { e.stopPropagation(); });
        }

        // hide input qty if present
        if( this.$input_qty.length )
            this.$input_qty.hide();

        if( ! this.$form.hasClass( 'initialized' ) ) {
            this.$form.addClass('initialized').fadeIn().trigger( 'yith_wccl_form_initialized' );
        }
    };

    WCCL.prototype.styleOption = function( obj, option, type, value ) {
        if( type == 'colorpicker' ) {

            value = value.split(',');

            if( value.length == 1 ) {
                option.append($('<span/>', {
                    'class': 'yith_wccl_value',
                    'css': {
                        'background': value
                    }
                }));
            } else {
                option.append($('<span class="yith_wccl_value"><span class="yith-wccl-bicolor"/></span>'));
                option.find('.yith-wccl-bicolor').css({
                    'border-bottom-color': value[0],
                    'border-left-color': value[1]
                });
            }
        } else if( type == 'image' ) {
            option.append($('<img/>', {
                'class': 'yith_wccl_value',
                'src': value
            }));
        } else if( type == 'label' ) {
            option.append($('<span/>', {
                'class': 'yith_wccl_value',
                'text': value
            }));
        }
    };

    WCCL.prototype.addTooltip = function( obj, tooltip, option, type, value ) {

        var tooltip_wrapper = $('<span class="yith_wccl_tooltip"></span>'),
            classes         = yith_wccl_general.tooltip_pos + ' ' + yith_wccl_general.tooltip_ani;

        if( ! yith_wccl_general.tooltip || typeof tooltip == 'undefined' || ! tooltip || option.find( '.yith_wccl_tooltip' ).length ) {
            return;
        }

        if( type == 'image' ) {
            tooltip = tooltip.replace('{show_image}', '<img src="' + value + '" />');
        }

        tooltip_wrapper.addClass( classes );
        option.append( tooltip_wrapper.html( '<span>' + tooltip + '</span>' ) );
    };

    WCCL.prototype.handleSelect = function( event ) {

        var obj = event.data.obj;

        obj.$select.each( function() {
            var t               = $(this),
                current_attr    = obj.$attr[ this.name ],
                decoded_name    = decodeURIComponent( this.name ),
                select_box      = t.parent().find( '.select_box' ),
                current_option  = [];

            if( typeof current_attr == 'undefined' ) {
                current_attr = obj.$attr[ decoded_name ];
            }

            // Add description
            if ( yith_wccl_general.description && ! obj.$is_loop && ! obj.$wrapper.length && ! obj.$form.find( '.description_' + decoded_name ).length
                 && typeof current_attr != 'undefined' && current_attr.descr ) {
                if( $(this).closest('tr').length ) {
                    $(this).closest('tr').after( '<tr class="description_' + decoded_name + '"><td colspan="2">' + current_attr.descr + '</td></tr>' );
                } else {
                    $(this).parent().append( '<p class="description_' + decoded_name + '">' + current_attr.descr + '</p>' );
                }
            }

            var type    = ( typeof current_attr != 'undefined' ) ? current_attr.type : t.data('type'),
                opt     = ( typeof current_attr != 'undefined' ) ? current_attr.terms : false;

            // exit if is not a custom attr
            if ( ( ! obj.$is_loop && ( typeof current_attr == 'undefined' || ! current_attr.terms ) ) || typeof type == 'undefined' || ! type ) {
                return;
            }

            t.addClass('yith_wccl_custom').hide().end().closest('.select-wrapper').addClass('yith_wccl_is_custom');

            if( ! select_box.length || ! yith_wccl_general.grey_out ) {
                select_box.remove();
                select_box = $('<div />', {
                    'class': 'select_box_' + type + ' select_box ' + t.attr('name')
                }).insertAfter(t);
            }

            t.find('option').each(function () {

                var option_val = $(this).val();

                if( ( opt && typeof opt[option_val] != 'undefined') || $(this).data('value') ) {

                    current_option.push( option_val );

                    var o           = $(this),
                        classes     = 'select_option_' + type + ' select_option',
                        value       = opt && typeof opt[option_val] != 'undefined' ? opt[option_val].value : $(this).data('value'),
                        tooltip     = opt && typeof opt[option_val] != 'undefined' ? opt[option_val].tooltip : $(this).data('tooltip'),
                        option      = select_box.find('[data-value="' + option_val + '"]');

                    // add options if missing
                    if( ! option.length ) {

                        // add selected class if is default
                        if( option_val == t.val() || option_val == t.attr( 'data-default_value' ) ) {
                            classes += ' selected';
                        }

                        option = $('<div/>', {
                            'class': classes,
                            'data-value': option_val
                        }).appendTo(select_box);

                        // event
                        option.off('click').on('click', function (e) {

                            var inactive = $(this).hasClass('inactive'),
                                selected = $(this).hasClass('selected');

                            if( inactive ) {

                                var current_attribute_name = t.data('attribute_name') || t.attr('name');

                                if( variations_match( obj.$form, $(this).data('value'), current_attribute_name ) ) {
                                    t.val('').change();
                                } else {
                                    obj.resetForm( obj );
                                }
                            }

                            if( selected ) {
                                t.val('').change();
                            } else {
                                t.val( o.val() ).change();
                            }

                            $(this).toggleClass( 'selected' );
                            $(this).siblings().removeClass( 'selected' );
                        });

                        // style option
                        obj.styleOption( obj, option, type, value );
                        // add tooltip if any
                        obj.addTooltip( obj, tooltip, option, type, value );
                    }
                }
            });

            select_box.children().each(function () {
                var val = $(this).data('value') + '';

                if ( $.inArray( val, current_option ) == '-1' ) {
                    $(this).addClass('inactive');
                }
                else {
                    $(this).removeClass('inactive');
                }
            });

            obj.$form.trigger( 'yith_wccl_select_initialized', [ t, current_attr ] );
        });
    };

    WCCL.prototype.setDefaultValue = function( event ) {
        var obj = event.data.obj;

        obj.$select.each( function () {
            $(this).val( $(this).attr( 'data-default_value' ) );
        });

        obj.$select.first().change();
    }

    WCCL.prototype.changeLoopImage = function( obj, variation ){
        if( ! variation ) {
            obj.$image.attr( 'src', obj.$def_image_src );
            if( obj.$def_image_srcset ) {
                obj.$image.attr( 'srcset', obj.$def_image_srcset ); // restore srcset if any
            }
        } else {

            var var_image           = ( typeof variation.image != 'undefined' && variation.image.thumb_src ) ? variation.image.thumb_src : '',
                var_image_srcset    = ( typeof variation.image != 'undefined' && variation.image.srcset ) ? variation.image.srcset : '';

            if( var_image && var_image.length ) {
                obj.$image.attr('src', var_image );
                obj.$image.attr('data-lazy-src', var_image );
            }
            if( var_image_srcset && var_image_srcset.length && obj.$def_image_srcset ) {
                obj.$image.attr( 'srcset', var_image_srcset );
                obj.$image.attr( 'data-lazy-srcset', var_image_srcset );
            }
        }
    };

    WCCL.prototype.changeSingleImage = function( obj, variation ) {
        var $product_gallery  = obj.$form.closest( '.product' ).find( '.images' ),
            $product_img_wrap = $product_gallery.find( '.woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder' ).eq( 0 ),
            $product_img      = $product_img_wrap.find( '.wp-post-image' ),
            $product_link     = $product_img_wrap.find( 'a' ).eq( 0 );

        $product_img.wc_set_variation_attr( 'src', variation.image.src );
        $product_img.wc_set_variation_attr( 'height', variation.image.src_h );
        $product_img.wc_set_variation_attr( 'width', variation.image.src_w );
        $product_img.wc_set_variation_attr( 'srcset', variation.image.srcset );
        $product_img.wc_set_variation_attr( 'sizes', variation.image.sizes );
        $product_img.wc_set_variation_attr( 'title', variation.image.title );
        $product_img.wc_set_variation_attr( 'alt', variation.image.alt );
        $product_img.wc_set_variation_attr( 'data-src', variation.image.full_src );
        $product_img.wc_set_variation_attr( 'data-large_image', variation.image.full_src );
        $product_img.wc_set_variation_attr( 'data-large_image_width', variation.image.full_src_w );
        $product_img.wc_set_variation_attr( 'data-large_image_height', variation.image.full_src_h );
        $product_img_wrap.wc_set_variation_attr( 'data-thumb', variation.image.src );
        $product_link.wc_set_variation_attr( 'href', variation.image.full_src );
    };

    WCCL.prototype.changeImageOnHover = function( event ) {

        var obj = event.data.obj;

        if( obj.$select.length != 1 || ! yith_wccl_general.image_hover ) {
            return;
        }

        obj.$form.on('mouseenter', '.select_option', function() {
            var value       = $(this).attr("data-value"),
                attr_name   = obj.$select.attr('name'),
                variation   = variations_match( obj.$form, value, attr_name ); // find variation

            if( $(this).hasClass('selected') || $(this).siblings().hasClass('selected') ){
                return;
            }

            if( variation && ( ( variation.image && variation.image.src ) || variation.image_src ) ) {
                if( obj.$form.hasClass('in_loop') ) {
                    obj.changeLoopImage( obj, variation );
                } else {
                    obj.changeSingleImage( obj, variation );
                }
            }
        }).on('mouseleave', '.select_option', function() {
            if( $(this).hasClass('selected') || $(this).siblings().hasClass('selected') ){
                return;
            }

            if( obj.$form.hasClass('in_loop') ) {
                obj.changeLoopImage( obj, false );
            } else {
                obj.$form.wc_variations_image_update( false );
            }
        });

    };

    WCCL.prototype.handleCheckVariations = function( event, data, focus ) {
        var obj = event.data.obj;
        if ( ! focus ) {
            if( obj.$found ) {
                event.data.obj.$found = false;
                if( ! obj.$use_ajax ) return;
            }
            if( obj.$changed ) {
                event.data.obj.$changed = false;
                // reset
                obj.resetLoopForm( obj );
            }
        }
    }

    WCCL.prototype.handleFoundVariation = function( event, variation ) {
        var obj = event.data.obj;

        if( obj.$use_ajax ) {
            obj.handleSelect( event );
        } else {
            obj.$select.last().trigger('focusin');
        }

        if ( obj.$is_loop ) {

            if( obj.$changed ) {
                obj.resetLoopForm( obj );
            }

            // found it!
            event.data.obj.$changed = true;
            event.data.obj.$found = true;

            // change image
            obj.changeLoopImage( obj, variation );

            if( variation.is_purchasable ){
                // change price
                if( variation.price_html ) {
                    obj.$wrapper.find('span.price').replaceWith( variation.price_html );
                }

                // show qty input
                if( obj.$input_qty.length ) {
                    obj.$input_qty.show();
                }
                // change button and add event add to cart

                if( variation.is_in_stock ) {

                    // $(document).trigger('yith_wccl_variation_found', [obj.$button,yith_wccl_general.add_cart]);

                    obj.$button.html( yith_wccl_general.add_cart );

                    obj.$button.off( 'click' ).on( 'click', { variation: variation.variation_id, select: obj.$select }, yith_wccl_add_cart );
                }
            }

            // add availability
            obj.$wrapper.find('span.price').after( $( variation.availability_html).addClass('ywccl_stock') );

            // set active variation
            obj.$form.data('active_variation', variation.variation_id );

            $(document).trigger('ywccl_found_variation_in_loop', [variation,obj.$button,yith_wccl_general.add_cart]);
        }
    };

    WCCL.prototype.handleVariationGallery = function( event, variation ) {

        var obj             = event.data.obj,
            gallery_wrap    = $( yith_wccl_general.single_gallery_selector ),
            id;

        if( obj.$is_loop || ! gallery_wrap.length ) {
            return;
        }

        if( obj.$xhr !== null ) {
            obj.$xhr.abort();
        }

        id = typeof variation != 'undefined' ? variation.variation_id : obj.$form.find( 'input[name="product_id"]' ).val();
        if( ! id || typeof id == 'undefined' ) {
            return;
        }

        obj.$xhr = $.ajax({
            url: yith_wccl_general.ajaxurl.toString().replace( '%%endpoint%%', yith_wccl_general.actionVariationGallery ),
            data: {
                action: yith_wccl_general.actionVariationGallery,
                id : id,
                context: 'frontend'
            },
            type: 'POST',
            dataType: 'html',
            beforeSend: function(){
                gallery_wrap.addClass( 'loading-gallery' );
            },
            success: function( html ){
                if( html ) {
                    gallery_wrap.replaceWith( html );
                    if ( typeof wc_single_product_params !== 'undefined' ) {
                        // reload gallery
                        $( yith_wccl_general.single_gallery_selector ).wc_product_gallery( wc_single_product_params );
                    }

                    obj.$form.wc_variations_image_update( variation );
                }

                gallery_wrap.removeClass( 'loading-gallery' );
                obj.$xhr = null;

                $(document).trigger( 'yith_wccl_product_gallery_loaded' );
            }
        });
    }

    WCCL.prototype.resetLoopForm = function( obj ){
        // reset image
        obj.changeLoopImage( obj, false );
        obj.$wrapper.find( 'span.price' ).replaceWith( obj.$price_html );
        obj.$wrapper.find('.ywccl_stock').remove();

        if( obj.$input_qty.length ){
            obj.$input_qty.hide();
        }

        obj.$button.html( obj.$button_html )
            .off( 'click', yith_wccl_add_cart )
            .removeClass( 'added' )
            .next('.added_to_cart').remove();

        // set active variation
        obj.$form.data('active_variation', '' );

        $(document).trigger( 'yith_wccl_reset_loop_form',[obj.$button] );
    }

    WCCL.prototype.resetForm = function( obj ) {
        obj.$form.find( 'div.select_option' ).removeClass( 'selected inactive' );
        obj.$select.val('').change();
        obj.$form.trigger( 'reset_data' );
    };

    WCCL.prototype.onReset = function( event ) {
        event.data.obj.$form.find('.select_option.selected').removeClass('selected inactive');
    }

    WCCL.prototype.init = function( event ) {

        var obj = event.data.obj;

        obj.$form.on( 'click.wc-variation-form', '.reset_variations', { obj: obj }, obj.onReset );
        obj.$form.on( 'woocommerce_update_variation_values', { obj: obj }, obj.handleSelect );
        obj.$form.one( 'yith_wccl_select_initialized', { obj: obj }, obj.changeImageOnHover );
        obj.$form.on( 'check_variations', { obj: obj }, obj.handleCheckVariations );
        obj.$form.on( 'found_variation', { obj: obj }, obj.handleFoundVariation );
        if( yith_wccl_general.enable_handle_variation_gallery ){
            obj.$form.on( 'found_variation', {obj: obj}, obj.handleVariationGallery );
            obj.$form.on( 'reset_image', {obj: obj}, obj.handleVariationGallery );
        }


        // force start select
        obj.handleSelect( event );

        obj.$select.each( function(){
            var val = $(this).attr( 'data-default_value' );
            $(this).removeAttr( 'data-default_value' );
            $(this).val( val );
        });


    }

    // retrocompatibility
    $.yith_wccl = function( attr ) {
        forms = $( '.variations_form.cart:not(.initialized), .owl-item.cloned .variations_form, form.cart.ywcp_form_loaded' );
        // prevent undefined attr error
        if( typeof attr == 'undefined' ) {
            attr = [];
        }

        forms.each(function (){
            new WCCL( $(this), attr );
        });
    };

    // plugin compatibility
    $(document).on( yith_wccl_general.plugin_compatibility_selectors, function() {

        if( typeof $.yith_wccl != 'undefined' && typeof $.fn.wc_variation_form != 'undefined' ) {
            // not initialized
            $(document).find( '.variations_form:not(.initialized), .owl-item.cloned .variations_form' ).each( function() {
                $(this).wc_variation_form();
            });

            // prevent undefined attr error
            if( typeof attr == 'undefined' ) {
                attr = [];
            }

            $.yith_wccl(attr);
        }
    });

    // reinit for woocommerce quick view
    $( 'body' ).on( 'quick-view-displayed', function() {
        var attr_qv = $('.pp_woocommerce_quick_view').find('.yith-wccl-data').data('attr');
        if( attr_qv ) {
            $.yith_wccl(attr_qv);
        }
    });

    // Fix for Flatsome Infinite Scrolling
    $('.shop-container > .products').on('append.infiniteScroll',function(){
        $(document).find( '.variations_form:not(.initialized), .owl-item.cloned .variations_form' ).each( function() {
            $(this).wc_variation_form();
        });

        // prevent undefined attr error
        if( typeof attr == 'undefined' ) {
            attr = [];
        }

        $.yith_wccl(attr);

    });

    // Re-init scripts on gallery loaded
    $(document).on( 'yith_wccl_product_gallery_loaded', function(){
        if( typeof mkdf != 'undefined' && typeof mkdf.modules.common.mkdfPrettyPhoto === "function" ) {
            var item = $('.mkdf-woo-single-page.mkdf-woo-single-has-pretty-photo .images .woocommerce-product-gallery__image');
            if( item.length ) {
                item.children('a').attr('data-rel', 'prettyPhoto[woo_single_pretty_photo]');
                mkdf.modules.common.mkdfPrettyPhoto();
            }
        }

        if( typeof Flatsome != 'undefined' ){
            Flatsome.attach( $( '.product-gallery' ) );
            // foce zoom button to work
            $( '.zoom-button' ).click( function ( ev ) {
                ev.preventDefault();
                $( '.product-gallery-slider' ) .find( '.is-selected a' ).click();
            });
        }
    });

    // START
    $(document).ready( function(){
        $.yith_wccl();
    });


})( jQuery, window, document );