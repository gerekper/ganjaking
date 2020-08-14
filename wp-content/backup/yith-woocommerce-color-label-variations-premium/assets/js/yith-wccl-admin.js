/**
 * Admin
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Colors and Labels Variations
 * @version 1.1.0
 */
jQuery(document).ready(function($) {
    "use strict";

    var colorpicker = $( '.ywccl[data-type="colorpicker"]' ),
        image       = $( '.ywccl[data-type="image"]'),
        // apply colorpicker
        yith_wccl_colorpicker = function( colorpicker ) {
            colorpicker.each( function() {

                $(this).wpColorPicker();

                if( $(this).hasClass('hidden_empty') && ! $(this).val() ) {
                    $(this).closest('.wp-picker-container').hide();
                }
            });
        },
        // apply upload image
        yith_wccl_upload = function( image ) {

            image.each(function(){

                var button = $("<input type='button' name='' id='term_value_button' class='button' value='Upload' />");
                button.insertAfter(this);

                //image uploader
                button.on('click', function(e) {

                    e.preventDefault();

                    var t = $(this),
                        custom_uploader,
                        id = t.attr('id').replace('_button', '');

                    //If the uploader object has already been created, reopen the dialog
                    if (custom_uploader) {
                        custom_uploader.open();
                        return;
                    }

                    var custom_uploader_states = [
                        // Main states.
                        new wp.media.controller.Library({
                            library:   wp.media.query(),
                            multiple:  false,
                            title:     'Choose Image',
                            priority:  20,
                            filterable: 'uploaded'
                        })
                    ];
                    // Create the media frame.
                    custom_uploader = wp.media.frames.downloadable_file = wp.media({
                        // Set the title of the modal.
                        title: 'Choose Image',
                        library: {
                            type: ''
                        },
                        button: {
                            text: 'Choose Image'
                        },
                        multiple: false,
                        states: custom_uploader_states
                    });
                    //When a file is selected, grab the URL and set it as the text field's value
                    custom_uploader.on( 'select' , function() {
                        var attachment = custom_uploader.state().get( 'selection' ).first().toJSON();

                        $("#" + id).val( attachment.url );
                    });

                    //Open the uploader dialog
                    custom_uploader.open();
                });
            });
        };

    yith_wccl_colorpicker( colorpicker );
    yith_wccl_upload( image );


    // ADD DESCRIPTION TO ATTRIBUTE FORM

    var form_attr = $( '.product_page_product_attributes .woocommerce form' );

    if( typeof yith_wccl_admin != 'undefined' && yith_wccl_admin.html )
        form_attr.find('.form-field').last().after( yith_wccl_admin.html );


    // FORM DIALOG

    var container           = $('.product_attributes'),
        dialog_wrap         = $( '#yith_wccl_dialog_form' ),
        dialog_error        = dialog_wrap.find( '.dialog_error' ),
        // save original form
        dialog_form_o       = dialog_wrap.find( 'form').clone(),
        reset_form          = function() {
            // clone original form and change with current
            var clone = dialog_form_o.clone();
            dialog_wrap.find( 'form' ).replaceWith( clone );
        };

    // Add a new attribute (via ajax)
    container.on( 'click', 'button.yith_wccl_add_new_attribute', function(e) {
        e.preventDefault();

        var wrapper     = $(this).closest('.woocommerce_attribute'),
            attribute   = wrapper.data( 'taxonomy' ),
            type        = $(this).data( 'type_input' ),
            form        = dialog_wrap.find( 'form' ),
            term_value  = form.find( '#term_value, #term_value_2' );

        // replace standard term value
        term_value.attr( 'data-type', type );

        // check type
        if( type == 'colorpicker' ) {
            yith_wccl_colorpicker( term_value );
            double_color( form.find( '.ywccl_add_color_icon' ) );
        }
        else{
            // remove not used input
            form.find( '#term_value_2, .ywccl_add_color_icon, br').remove();
            if( type == 'image' ) {
                yith_wccl_upload(term_value);
            }
        }

        // init dialog
        dialog_wrap.dialog({
            width: 350,
            modal: true,
            dialogClass: 'yith_wccl_dialog_modal',
            buttons: {
                'Add': function(){
                    $(document).find( '#yith_wccl_dialog_form form' ).trigger( "submit", [ wrapper, attribute ] );
                },
                Cancel: function() {
                    dialog_wrap.dialog( "close" );
                }
            },
            close: function() {
                reset_form();
            }
        });

        return false;
    });

    $(document).on("submit", '#yith_wccl_dialog_form form', function (e, wrapper, attribute) {
        e.preventDefault();

        var t       = $(this),
            form = t.serializeArray(),
            data;

        // add action and taxonomy
        form.push({ name: "action", value: 'yith_wccl_add_new_attribute' }, { name: "taxonomy", value: attribute } );
        data = $.param( form );

        t.block({message: null, overlayCSS: {background: '#fff', opacity: 0.6}});

        $.post( yith_wccl_admin.ajaxurl, data, function (response) {

            // unblock form
            t.unblock();

            if ( response.error ) {
                // Error
                dialog_error.html( response.error );
            }
            else if ( response.value ) {
                // Remove error
                dialog_error.html('');
                // Success
                wrapper.find('select.attribute_values').append('<option value="' + response.value + '" selected="selected">' + response.name + '</option>');
                wrapper.find('select.attribute_values').change();

                // close dialog
                dialog_wrap.dialog("close");
            }

        });

        return false;

    });

    var double_color = function( plus ){

        plus.off('click').on( 'click', function(){
            var t = $(this),
                tdata = t.data('content'),
                input_container = $(this).nextAll( '.wp-picker-container' ),
                input_clear = input_container.find( '.wp-picker-clear' );


            // change button content
            t.data( 'content', t.html() );
            t.html( tdata );

            input_clear.click();
            input_container.toggle();
        });
    };

    double_color( $( '.ywccl_add_color_icon' ) );

    // HANDLE PRODUCT VARIATION IMAGE GALLERY

    var updateGalleryInput = function( gallery ){
        var input   = gallery.find( '.yith_wccl_variation_gallery_values' ),
            images  = gallery.find( "li.image:not('.add')" ),
            value   = [];

        $.each( images, function(){
            value.push( $(this).data('value') );
        });

        input.attr( 'value', value.join(',') ).change();
    }

    $(document).on( 'woocommerce_variations_loaded woocommerce_variations_added', function(){
        $( ".woocommerce_variation:not('.initialized')" ).each( function( index, el ) {
            var gallery = $(el).find( '.yith-wccl-variation-gallery-wrapper' ),
                options = $(el).find( '.form-row-full.options' ).first();

            gallery.insertBefore( options );
            // Image ordering.
            gallery.find( '.yith-wccl-variation-gallery-images' ).sortable({
                items: "li.image:not('.add')",
                cursor: 'move',
                scrollSensitivity: 40,
                forcePlaceholderSize: true,
                forceHelperSize: false,
                helper: 'clone',
                opacity: 0.65,
                placeholder: 'yith-wccl-sortable-placeholder',
                start: function( event, ui ) {
                    ui.item.css( 'background-color', '#f6f6f6' );
                },
                stop: function( event, ui ) {
                    ui.item.removeAttr( 'style' );
                },
                update: function() {
                    updateGalleryInput( gallery );
                }
            });

            $(this).addClass( 'initialized' );
        });
    });

    $(document).on( 'click', '.yith-wccl-variation-gallery-images .remove', function (event) {
        event.preventDefault();
        var gallery = $(this).closest( '.yith-wccl-variation-gallery-wrapper' );
        $(this).closest( '.image' ).remove();
        updateGalleryInput( gallery );
    });

    $(document).on( 'click', '.add-variation-gallery-image', function( event ){
        event.preventDefault();

        if( typeof wp == 'undefined' )
            return;

        var button  = $(this),
            html    = '',
            gallery = button.closest( '.yith-wccl-variation-gallery-wrapper' ),
            images  = gallery.find( '.yith-wccl-variation-gallery-images' ),
            index   = button.attr( 'data-index' ),
            media   = wp.media({
                title: woocommerce_admin_meta_boxes_variations.i18n_choose_image,
                button: {
                    text: woocommerce_admin_meta_boxes_variations.i18n_set_image
                },
                library: { type: 'image' },
                multiple : true
            });

        media.on( 'select', function () {
            var attachment = media.state().get( 'selection' ).toJSON(),
                html = attachment.map( function ( image ) {
                    if( image.type !== 'image' || images.find( '[data-value="'+ image.id + '"]' ).length ) {
                        return '';
                    }

                    var id          = image.id,
                        url         = ( image.sizes && image.sizes.thumbnail ) ? image.sizes.thumbnail.url : image.url,
                        template    = wp.template('yith-wccl-variation-gallery-image');

                    return template({ id: id, url: url });
                }).join('');

            images.find( '.image.add' ).before( html );
            updateGalleryInput( gallery );
        });

        media.open();
    });
});