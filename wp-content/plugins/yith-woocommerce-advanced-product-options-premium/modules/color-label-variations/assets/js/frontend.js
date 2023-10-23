/**
 * Frontend
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 3.0.0
 */

;(function( $, window, document ){

    if ( typeof yith_wapo_color_label_attr === 'undefined' ) {
        return false;
    }

    /**
     * Matches inline variation objects to chosen attributes and return variation
     * @type {Object}
     */
    var variations_match = function( form, value, current_attribute_name ) {
        var match = false,
            product_variations = form.data( 'product_variations' ),
            all_select = form.find( '.variations select' ),
            settings = [];

        // Current selected values.
        $.each( all_select, function(){
            var attribute_name = $( this ).data( 'attribute_name' ) || $( this ).attr( 'name' );
            if( current_attribute_name === attribute_name ) {
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

                    if ( val1 !== val2 && val1 !== '' ) {
                        match = false;
                    }
                }
            }
        }

        return match;
    };

    /**
     *
     * @param $form
     * @param attr
     * @constructor
     */
    var WCCL = function( $form, attr ) {

        this.$form              = $form;
        this.$attr              = JSON.parse( yith_wapo_color_label_attr.attributes );
        this.$select            = this.$form.find( '.variations select' );
        this.$use_ajax          = this.$form.data( 'product_variations' ) === false;
        this.$xhr               = null;
        this.variations_gallery = []; // Store variations gallery to improve performance.

        // prevent undefined attr error
        if( typeof this.$attr == 'undefined' ) {
            this.$attr = [];
        }

        $form.on( 'yith_wccl_form_initialized', { obj: this }, this.init );

        // Get default value.
        this.$select.each( function() {
            this.setAttribute( 'data-default_value', this.value );
        });

        // Reset form and select.
        this.resetForm( this );
        if( ! this.$form.hasClass( 'initialized' ) ) {
            this.$form.addClass('initialized').fadeIn().trigger( 'yith_wccl_form_initialized' );
        }
    };

    WCCL.prototype.styleOption = function( obj, option, type, value ) {
        if( type === 'colorpicker' ) {
            value = Array.isArray( value ) ? value : value.split(',');

            if( value.length === 1 ) {
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
        } else if( type === 'image' ) {
            option.append($('<img/>', {
                'class': 'yith_wccl_value',
                'src': value,
                'alt':type
            }));
        } else if( type === 'label' ) {
            option.append($('<span/>', {
                'class': 'yith_wccl_value',
                'text': value
            }));
        }
    };

    WCCL.prototype.addTooltip = function( obj, tooltip, option, type, value ) {

        if( ! yith_wapo_color_label_attr.tooltip || typeof tooltip == 'undefined' || ! tooltip || option.find( '.yith_wccl_tooltip' ).length ) {
            return;
        }

        var tooltip_wrapper = $('<span class="yith_wccl_tooltip"></span>'),
            classes         = yith_wapo_color_label_attr.tooltip_pos + ' ' + yith_wapo_color_label_attr.tooltip_ani;

        if( type === 'image' ) {
            tooltip = tooltip.toString().replace('{show_image}', '<img src="' + value + '" />');
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
                current_option  = [],
                parent;

            // Set select parent.
            parent = t.closest('.select-wrapper');
            if ( ! parent.length ) {
                parent = t.closest('td');
                if ( ! parent.length ) {
                    parent = t.parent();
                }
            }

            if( typeof current_attr == 'undefined' ) {
                current_attr = obj.$attr[ decoded_name ];
            }

            // Add description
            if ( yith_wapo_color_label_attr.description && ! obj.$form.find( '.description_' + decoded_name ).length && typeof current_attr != 'undefined' && current_attr.descr ) {
                if( t.closest('tr').length ) {
                    t.closest('tr').after( '<tr class="description_' + decoded_name + '"><td colspan="2">' + current_attr.descr + '</td></tr>' );
                } else {
                    parent.append( '<p class="description_' + decoded_name + '">' + current_attr.descr + '</p>' );
                }
            }

            var type    = ( typeof current_attr != 'undefined' ) ? current_attr.type : t.data('type'),
                opt     = ( typeof current_attr != 'undefined' ) ? current_attr.terms : false;

            // exit if is not a custom attr
            if ( typeof current_attr == 'undefined' || ! current_attr.terms || typeof type == 'undefined' || ! type ) {
                return;
            }

            t.addClass('yith_wccl_custom').hide();
            parent.addClass('yith_wccl_is_custom');

            if( ! select_box.length || ! yith_wapo_color_label_attr.grey_out ) {
                select_box.remove();
                select_box = $('<div />', {
                    'class': 'select_box_' + type + ' select_box ' + t.attr('name')
                }).insertAfter(t);
            }

            t.find('option').each(function () {

                var option_val = $(this).val();

                if( ( opt && typeof opt[option_val] != 'undefined') || ( typeof $(this).data('value') !== 'undefined' && $(this).data('value') !== '' ) ) {

                    current_option.push( option_val );

                    var o           = $(this),
                        classes     = 'select_option_' + type + ' select_option',
                        value       = opt && typeof opt[option_val] != 'undefined' ? opt[option_val].value : $(this).data('value'),
                        tooltip     = opt && typeof opt[option_val] != 'undefined' ? opt[option_val].tooltip : $(this).data('tooltip'),
                        option      = select_box.find('[data-value="' + option_val + '"]');

                    // add options if missing
                    if( ! option.length ) {

                        // add selected class if is default
                        if( option_val === t.val() || option_val === t.attr( 'data-default_value' ) ) {
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
                if ( $.inArray( val, current_option ) === -1 ) {
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

    WCCL.prototype.handleCheckVariations = function( event, data, focus ) {
        var obj = event.data.obj;
        if ( ! focus ) {
            if( obj.$found ) {
                event.data.obj.$found = false;
                if( ! obj.$use_ajax ) {
                    return;
                }
            }
            if( obj.$changed ) {
                event.data.obj.$changed = false;
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
    };

    WCCL.prototype.handleVariationGallery = function( event, variation ) {

        var obj             = event.data.obj,
            gallery_wrap    = $( yith_wapo_color_label_attr.single_gallery_selector ),
            id;

        if( ! gallery_wrap.length ) {
            return;
        }

        if( obj.$xhr !== null ) {
            obj.$xhr.abort();
        }

        id = typeof variation != 'undefined' ? variation.variation_id : obj.$form.find( 'input[name="product_id"]' ).val();
        if( ! id || typeof id == 'undefined' ) {
            return;
        }

        if ( undefined !== obj.variations_gallery[ id ] ) {
            obj.loadVariationGallery( obj.variations_gallery[ id ], gallery_wrap, variation );
        }
        else {
            obj.$xhr = $.ajax({
                url: yith_wapo_color_label_attr.ajaxurl.toString().replace( '%%endpoint%%', yith_wapo_color_label_attr.actionVariationGallery ),
                data: {
                    action: yith_wapo_color_label_attr.actionVariationGallery,
                    id : id,
                    context: 'frontend'
                },
                type: 'POST',
                dataType: 'html',
                beforeSend: function(){
                    gallery_wrap.addClass( 'loading-gallery' );
                },
                success: function( html ){
                    gallery_wrap.removeClass( 'loading-gallery' );
                    obj.$xhr = null;
                    if( html ) {
                        // store variation gallery to improve performance
                        obj.variations_gallery[ id ] = html;
                        obj.loadVariationGallery( html, gallery_wrap, variation );
                    }
                }
            });
        }
    }

    WCCL.prototype.loadVariationGallery = function( html, gallery_wrap, variation ) {
        gallery_wrap.replaceWith( html ).fadeIn();
        if ( typeof wc_single_product_params !== 'undefined' ) {
            // reload gallery
            $( yith_wapo_color_label_attr.single_gallery_selector ).wc_product_gallery( wc_single_product_params );
        }

        this.$form.wc_variations_image_update( variation );

        $(document).trigger( 'yith_wccl_product_gallery_loaded' );
    }

    WCCL.prototype.resetForm = function( obj ) {
        obj.$form.find( 'div.select_option' ).removeClass( 'selected inactive' );
        obj.$select.val('').change();
        obj.$form.trigger( 'reset_data', 'yith_wccl' );
    };

    WCCL.prototype.onReset = function( event ) {
        event.data.obj.$form.find('.select_option.selected').removeClass('selected inactive');
    }

    WCCL.prototype.init = function( event ) {

        var obj = event.data.obj;

        obj.$form.on( 'click.wc-variation-form', '.reset_variations', { obj: obj }, obj.onReset );
        obj.$form.on( 'woocommerce_update_variation_values', { obj: obj }, obj.handleSelect );
        obj.$form.on( 'check_variations', { obj: obj }, obj.handleCheckVariations );
        obj.$form.on( 'found_variation', { obj: obj }, obj.handleFoundVariation );
        if( yith_wapo_color_label_attr.enable_handle_variation_gallery ){
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
