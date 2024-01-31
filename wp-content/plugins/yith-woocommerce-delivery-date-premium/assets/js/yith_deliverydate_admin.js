jQuery(function($){

    $.fn.checkRequiredFields = function(){
        var req_fields = $(this).find(':input.yith-required-field'),
            is_valid = 'yes';

        $.each( req_fields, function(i, field ) {

            var value = $(field).val();

            if( value === '' || value === null ){

                if( $(field).hasClass('wc-enhanced-select') || $(field).hasClass('yith-post-search') || $(field).hasClass('yith-term-search') ){
                    $(field).parent().find('.select2.select2-container').addClass('ywcdd_required_field');
                }else {
                    $(field).addClass('ywcdd_required_field');
                }
                is_valid = 'no';
            }else{
                if( $(field).hasClass('wc-enhanced-select') || $(field).hasClass('yith-post-search') || $(field).hasClass('yith-term-search') ){
                    $(field).parent().find('.select2.select2-container').removeClass('ywcdd_required_field');
                }else {
                    $(field).removeClass('ywcdd_required_field');
                }
            }
        });

        return is_valid;
    };
    $('.ywcdd_processing_type input[type="radio"]').on('change', function(e){

        var element = $(this),
            element_to_disable = ['ywcdd_delivery_mode','ywcdd_fee_is_taxable','yith_delivery_date_format'],
            message_to_hide =[ 'ywcdd_delivery_settings_title-description', 'ywcdd_timeslot_settings_title-description'];


        $.each( element_to_disable,function( i, single_element ){

            var row = $('#'+single_element).parents('tr');

            'product' === element.val() && element.is(':checked') ? row.addClass( 'yith-disabled') : row.removeClass('yith-disabled');

        } );


        if( 'product' === element.val() && element.is(':checked') ){

            $('#ywcdd_fee_tax_class').parents('tr').addClass('yith-disabled');
        }else{
           if( !$('#ywcdd_fee_is_taxable').is(':checked') ){
               $('#ywcdd_fee_tax_class').parents('tr').addClass('yith-disabled');
           }else{
               $('#ywcdd_fee_tax_class').parents('tr').removeClass('yith-disabled');
           }
        }


        $.each( message_to_hide, function( i, single_message ){

            'product' === element.val() && element.is(':checked') ? $('#'+single_message).show() : $('#'+single_message).hide();
        });


    }).trigger('change');

    $('#ywcdd_ddm_enable_shipping_message').on('change',function(e){

        var other_check_field = $('#ywcdd_ddm_enable_delivery_message'),
            field_to_disable = $('#ywcdd_ddm_where_show_delivery_message');

        if( 'no' === $(this).val() && 'no' === other_check_field.val() ){
            field_to_disable.parents('tr').addClass('yith-disabled');
        }else{
            field_to_disable.parents('tr').removeClass('yith-disabled');
        }

    });
    $('#ywcdd_ddm_enable_delivery_message').on('change',function(e){

        var other_check_field = $('#ywcdd_ddm_enable_shipping_message'),
            field_to_disable = $('#ywcdd_ddm_where_show_delivery_message');

        if( 'no' === $(this).val() && 'no' === other_check_field.val() ){
            field_to_disable.parents('tr').addClass('yith-disabled');
        }else{
            field_to_disable.parents('tr').removeClass('yith-disabled');
        }

    });

    if( $('#yith_delivery_date_panel_processing-method').length ){

        $(document).on('yith-toggle-change-counter',function ( e, hidden_obj, add_box ) {

            var id = add_box.attr('id');

            if( 'yith_new_shipping_day_prod_add_box' === id){

                var product_selected = add_box.find('#new_product').val();
                hidden_obj.val( product_selected);
            }else if( 'yith_new_shipping_day_cat_add_box' === id ){

                var category_selected = add_box.find('#new_category').val();

                hidden_obj.val( category_selected );
            }
        });


        $(document).on('click','.ywcdd_add_range',function(e){
            e.preventDefault();

            var parent = $(this).parent().parent(),
                range_list = parent.find('.ywcdd_quantity_row'),
                i = range_list.find('.ywcdd_quantity_item').size(),
                data_row  = parent.data('row');

            data_row = $(data_row);
            var html = data_row.html().replace(/index/g,i);

            data_row.html( html );
            data_row.appendTo( range_list);

        });
        $(document).on( 'click','.ywcdd_delete_range', function(e){

            e.preventDefault();
            var btn = $(this),
                row = btn.parents('.ywcdd_quantity_item');

            row.remove();
        });


        $(document).on('yith-toggle-element-item-before-add', function(e,add_box, toggle_el, form_is_valid){

            var is_valid = add_box.checkRequiredFields(),
                id = add_box.attr('id');

            form_is_valid.val( is_valid );

            if( 'yes' == is_valid ) {
                var quantity_items = add_box.find('.ywcdd_quantity_row .ywcdd_quantity_item'),
                    field_name = '',
                    i = 0,
                    field_selected = '',
                    custom_type_title = '';


                if ('yith_new_shipping_day_cat_add_box' === id) {
                    field_name = 'yith_new_shipping_day_cat';
                    field_selected = add_box.find('#new_category').val();
                    custom_type_title = 'category';
                } else if ('yith_new_shipping_day_prod_add_box' === id) {
                    field_name = 'yith_new_shipping_day_prod';
                    field_selected = add_box.find('#new_product').val();
                    custom_type_title = 'product';
                }

                var form_field_type = ['from', 'to', 'day'];
                $.each(quantity_items, function (index, item) {

                    var new_field_name = field_name + '[' + field_selected + '][need_process_day][' + i + ']',
                        new_qty_items = $(quantity_items[0]).clone();

                    $.each(form_field_type, function (c, form_type) {

                        var new_field_id = form_type + '_' + i + '_need_process_day_' + field_selected,
                            single_field_name = new_field_name + '[' + form_type + ']';

                        if (!toggle_el.find('#' + new_field_id).length) {

                            var new_value = '';

                            if ('from' == form_type) {
                                new_qty_items.find('.ywcdd_from').attr('id', new_field_id).attr('name', single_field_name);
                                new_value = add_box.find('#from_' + i + '_new_need_process_day').val();
                                new_qty_items.find('.ywcdd_from').val(new_value);

                            } else if ('to' == form_type) {
                                new_qty_items.find('.ywcdd_to').attr('id', new_field_id).attr('name', single_field_name);
                                new_value = add_box.find('#to_' + i + '_new_need_process_day').val();
                                new_qty_items.find('.ywcdd_to').val(new_value);
                            } else {
                                new_qty_items.find('.ywcdd_day').attr('id', new_field_id).attr('name', single_field_name);
                                new_value = add_box.find('#day_' + i + '_new_need_process_day').val();
                                new_qty_items.find('.ywcdd_day').val(new_value);
                            }


                            new_qty_items.appendTo(toggle_el.find('.ywcdd_quantity_row'));

                        }

                    });

                    i++;

                });


                var data = {
                    'field_selected' : field_selected,
                    'action' : yith_delivery_parmas.actions.update_custom_processing_title,
                    'custom_type_title' : custom_type_title,
                };

                $.ajax({
                    type: 'POST',
                    url: yith_delivery_parmas.ajax_url,
                    data: data,
                    dataType: 'json',
                    success:function (response) {

                       if( typeof  response.title !== "undefined" ){

                           toggle_el.find('h3 .title').html( response.title );
                       }
                    }
                });

            }


        });

        $(document).on('yith-toggle-element-item-before-update', function( e, toggle, form_is_valid ){

            var is_valid = toggle.checkRequiredFields();

            form_is_valid.val( is_valid );
        });
    }



});
