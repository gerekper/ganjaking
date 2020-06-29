/**
 * Created by Your Inspiration on 08/10/2015.
 */
jQuery(document).ready(function ($) {

    //collapse fields
    var collapse = $('.ywcwat_product_collapse'),
        image_file_frame,
        dialog =  $( "#ywcwat_preview_image" ).dialog({
            resizable: false,
            autoOpen: false,
            height: "auto",
            width: 400,
            modal: true,
            buttons: {
                Ok: function() {
                    $( this ).dialog( "close" );

                }
            }
        }),
        get_row_index = function (element) {

            var id = element.parents('.ywcwat_product_watermark_row').attr('id');

            return id;
        },
        show_watermark_position = function ($current_table_cell) {
            var table = $current_table_cell.parent().parent(),
                this_row = table.parents('.position_table'),
                text_position = this_row.parent().find('.position_text'),
                hidden_field_pos = this_row.find('.product_ywcwat_pos_value:eq(0)');

            table.find('td').removeClass('position_select');
            $current_table_cell.addClass('position_select');


            if ($current_table_cell.hasClass("ywcwat_top_left")) {

                hidden_field_pos.val('top_left');
                text_position.html(ywcwat_product_param.label_position.top_left);

            } else if ($current_table_cell.hasClass("ywcwat_top_center")) {

                hidden_field_pos.val('top_center');
                text_position.html(ywcwat_product_param.label_position.top_center);
            } else if ($current_table_cell.hasClass("ywcwat_top_right")) {

                hidden_field_pos.val('top_right');
                text_position.html(ywcwat_product_param.label_position.top_right);

            } else if ($current_table_cell.hasClass("ywcwat_middle_left")) {


                hidden_field_pos.val('middle_left');
                text_position.html(ywcwat_product_param.label_position.middle_left);

            } else if ($current_table_cell.hasClass("ywcwat_middle_center")) {


                hidden_field_pos.val('middle_center');
                text_position.html(ywcwat_product_param.label_position.middle_center);

            } else if ($current_table_cell.hasClass("ywcwat_middle_right")) {


                hidden_field_pos.val('middle_right');
                text_position.html(ywcwat_product_param.label_position.middle_right);


            } else if ($current_table_cell.hasClass("ywcwat_bottom_left")) {


                hidden_field_pos.val('bottom_left');
                text_position.html(ywcwat_product_param.label_position.bottom_left);

            } else if ($current_table_cell.hasClass("ywcwat_bottom_center")) {


                hidden_field_pos.val('bottom_center');
                text_position.html(ywcwat_product_param.label_position.bottom_center);

            } else {
                //bottom right default

                hidden_field_pos.val('bottom_right');
                text_position.html(ywcwat_product_param.label_position.bottom_right);
            }
        },
        init_watermark_admin_field = function () {
            //show/hide current fields
            $('.ywcwat_select_type_wat').on('change', function (e) {

                var t = $(this),
                    value_select = t.val(),
                    this_row = $('#' + get_row_index(t)),
                    text_field_container = this_row.find('.ywcwat_custom_wat_text_fields'),
                    img_field_container = this_row.find('.ywcwat_custom_wat_img_fields'),
                    general_field_container = this_row.find('.ywcwat_custom_general_fields'),
                    current_position = this_row.find('.position_select');


                switch (value_select) {

                    case 'type_text' :
                        img_field_container.hide();
                        text_field_container.show();
                        general_field_container.show();

                        break;
                    case 'type_img' :
                        img_field_container.show();
                        text_field_container.hide();
                        general_field_container.show();
                        break;
                    case 'no':
                        img_field_container.hide();
                        text_field_container.hide();
                        general_field_container.hide();
                        break;

                }

            }).change();

            $(document).on('click', '.ywcwat_product_image', function (e) {

                e.preventDefault();
                var t = $(this),
                    this_row = $('#' + get_row_index(t));
                watermark_url = this_row.find('.ywcwat_product_wat_url');
                watermark_id = this_row.find('input:hidden[id^="ywcwat_product_image_hidden-"]');
                this_preview = this_row.find('.ywcwat_preview_watermark img');
                table_position = this_row.find('.ywcwat_bottom_right');

                // If the media frame already exists, reopen it.
                if (image_file_frame) {
                    image_file_frame.open();
                    return;
                }

                var downloadable_file_states = [
                    // Main states.
                    new wp.media.controller.Library({
                        library: wp.media.query({type: 'image'}),
                        multiple: false,
                        title: t.data('choose'),
                        priority: 20,
                        filterable: 'all'
                    })
                ];

                // Create the media frame.
                image_file_frame = wp.media.frames.downloadable_file = wp.media({
                    // Set the title of the modal.
                    title: t.data('choose'),
                    library: {
                        type: 'image'
                    },
                    button: {
                        text: t.data('choose')
                    },
                    multiple: false,
                    states: downloadable_file_states
                });

                // When an image is selected, run a callback.
                image_file_frame.on('select', function () {


                    var selection = image_file_frame.state().get('selection');

                    selection.map(function (attachment) {

                        attachment = attachment.toJSON();

                        if (attachment.url) {

                            watermark_id.val(attachment.id);
                            this_preview.attr('src', attachment.url);
                            watermark_url.val(attachment.url);
                            table_position.click();
                        }

                    });
                });

                // Finally, open the modal.
                image_file_frame.open();

            });

            var lock = false;
            $(document).on('click', '.ywcwat_remove_product_watermark', function (e) {
                e.preventDefault();
                if( !lock ) {

                    var t = $(this),
                        table_to_remove_id = t.data('element_id');

                    var answer = window.confirm(ywcwat_product_param.delete_single_watermark.confirm_delete_watermark);
                    if (answer) {

                        $('#ywcwat_product_watermark_row-' + table_to_remove_id).remove();
                        init_watermark_admin_field();
                    }
                    lock = true;
                    return false;
                }
            });

            $(document).on('click', '.product_ywcwat_container_position tr td', function (e) {

                var t = $(this);

                show_watermark_position(t);

            });

            collapse.each(function () {
                $(this).toggleClass('expand').nextUntil('.ywcwat_product_collapse').slideToggle('slow');
            });

            $(document).on('click', '.ywcwat_product_collapse', function () {
                $(this).toggleClass('expand').nextUntil('.ywcwat_product_collapse').slideToggle('slow');
            });

            if ($('#_ywcwat_product_enabled_watermark').is(':checked')) {
                $('.show_if_custom_watermark_enabled').show();
            }
            else {
                $('.show_if_custom_watermark_enabled').hide();
            }
            $(document).on('change', '#_ywcwat_product_enabled_watermark', function () {

                var t = $(this);

                if (t.is(':checked'))
                    $('.show_if_custom_watermark_enabled').show();
                else
                    $('.show_if_custom_watermark_enabled').hide();


            });

            $(document).on( 'click', '.ywcwat_preview', function(e){
                e.preventDefault();

                var t = $(this),

                    row = t.parents('.ywcwat_product_watermark_row'),
                    field = row.find( 'input, select, checkbox' ).serialize(),
                    data = {
                        action:ywcwat_product_param.actions.preview_watermark,
                        ywcwat_args: field,
                        context:'product'
                    };


                $.ajax({

                    type: 'POST',
                    url: ywcwat_product_param.ajax_url,
                    data: data,
                    dataType: 'json',
                    success: function (response) {


                        if( response.result ) {
                            $('#ywcwat_preview_image').find('img').attr('src', response.message ).show();
                        }else{
                            $('#ywcwat_preview_image').html( '<p>'+response.message+'</p>' );
                        }
                        dialog.dialog("open");

                    }

                });



            });

            $('#ywcwat_preview_image').on('dialogclose', function(event) {
                $(this).find('img').attr('src', '' );
            });


        };



    $(document).on('click', '#ywcwat_apply_product', function (e) {
        e.preventDefault();

        var t = $(this),
            product_id = $(this).data('product_id'),
            data = {
                product_id: product_id,
                action: ywcwat_product_param.actions.save_watermark_on_single_product
            };

        $.ajax({
            type: 'POST',
            url: ywcwat_product_param.ajax_url,
            data: data,
            dataType: 'json',
            beforeSend: function () {
                t.siblings('.ajax-loading').css('visibility', 'visible');
            },
            complete: function () {
                t.siblings('.ajax-loading').css('visibility', 'hidden');
            },
            success: function (response) {

            }
        });
    });

    $('body').on('ywcwat-product-init-fields', function () {

        init_watermark_admin_field();
        $(document).find('.product_ywcwat_container_position tr td.position_select').click();
        $(document.body).trigger('wc-enhanced-select-init');
        $('.product_colorpicker').wpColorPicker();
    }).trigger('ywcwat-product-init-fields');

});