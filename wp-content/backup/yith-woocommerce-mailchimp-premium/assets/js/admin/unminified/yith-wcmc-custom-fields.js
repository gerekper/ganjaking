jQuery( document ).ready( function( $ ){
    var body = $( 'body'),
        fields_content = $( '.fields-content'),
        id = fields_content.parents('.form-table').data( 'id' );

    $('#add_field').on( 'click', function(ev){
        var t = $(this),
            item = t.parents( '.advanced-panel-item'),
            item_id = item.data('id');

        ev.preventDefault();

        $.ajax({
            beforeSend: function(){
                t.block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });
            },
            complete: function(){
                t.unblock();
            },
            data: {
                action: yith_wcmc_custom_fields.actions.add_custom_field_action,
                item_id: yith_wcmc_custom_fields.item_id,
                list_id: t.parents( '#plugin-fw-wc').find( '.list-select').val(),
                id: id
            },
            dataType: 'html',
            error: function(){

            },
            method: 'POST',
            success: function( data ){
                if( data.length != 0 ){
                    fields_content.prepend( data );
                    $( fields_content.find('.fields-item').get(0) ).hide().slideDown();
                    yith_wcmc_custom_fields.item_id ++;

                    handle_fields_button();
                    body.trigger( 'wc-enhanced-select-init' );
                }
            },
            url: ajaxurl
        });
    });

    $('#yith_wcmc_shortcode_style_enable').on( 'change', function(){
        var t = $(this),
            round_corners = $('#yith_wcmc_shortcode_subscribe_button_round_corners'),
            background_color = $('#yith_wcmc_shortcode_subscribe_button_background_color'),
            text_color = $('#yith_wcmc_shortcode_subscribe_button_color'),
            border_color = $('#yith_wcmc_shortcode_subscribe_button_border_color'),
            background_hover_color = $('#yith_wcmc_shortcode_subscribe_button_background_hover_color'),
            text_hover_color = $('#yith_wcmc_shortcode_subscribe_button_hover_color'),
            border_hover_color = $('#yith_wcmc_shortcode_subscribe_button_border_hover_color'),
            custom_css = $('#yith_wcmc_shortcode_custom_css');

        if( t.is( ':checked' ) ){
            round_corners.parents( 'tr').show();
            background_color.parents( 'tr').show();
            text_color.parents( 'tr').show();
            border_color.parents( 'tr').show();
            background_hover_color.parents( 'tr').show();
            text_hover_color.parents( 'tr').show();
            border_hover_color.parents( 'tr').show();
            custom_css.parents( 'tr').show();
        }
        else{
            round_corners.parents( 'tr').hide();
            background_color.parents( 'tr').hide();
            text_color.parents( 'tr').hide();
            border_color.parents( 'tr').hide();
            background_hover_color.parents( 'tr').hide();
            text_hover_color.parents( 'tr').hide();
            border_hover_color.parents( 'tr').hide();
            custom_css.parents( 'tr').hide();
        }
    }).change();

    $('#yith_wcmc_widget_style_enable').on( 'change', function(){
        var t = $(this),
            round_corners = $('#yith_wcmc_widget_subscribe_button_round_corners'),
            background_color = $('#yith_wcmc_widget_subscribe_button_background_color'),
            text_color = $('#yith_wcmc_widget_subscribe_button_color'),
            border_color = $('#yith_wcmc_widget_subscribe_button_border_color'),
            background_hover_color = $('#yith_wcmc_widget_subscribe_button_background_hover_color'),
            text_hover_color = $('#yith_wcmc_widget_subscribe_button_hover_color'),
            border_hover_color = $('#yith_wcmc_widget_subscribe_button_border_hover_color'),
            custom_css = $('#yith_wcmc_widget_custom_css');

        if( t.is( ':checked' ) ){
            round_corners.parents( 'tr').show();
            background_color.parents( 'tr').show();
            text_color.parents( 'tr').show();
            border_color.parents( 'tr').show();
            background_hover_color.parents( 'tr').show();
            text_hover_color.parents( 'tr').show();
            border_hover_color.parents( 'tr').show();
            custom_css.parents( 'tr').show();
        }
        else{
            round_corners.parents( 'tr').hide();
            background_color.parents( 'tr').hide();
            text_color.parents( 'tr').hide();
            border_color.parents( 'tr').hide();
            background_hover_color.parents( 'tr').hide();
            text_hover_color.parents( 'tr').hide();
            border_hover_color.parents( 'tr').hide();
            custom_css.parents( 'tr').hide();
        }
    }).change();

    var handle_fields_button = function(){
        $('.fields-item a.remove-button').off( 'click' ).on( 'click', function(ev){
            var t = $(this),
                tab = t.parents('.fields-item');

            ev.preventDefault();

            tab.slideUp(300, function(){
                $(this).remove();
            });
        } );

        $('.fields-item a.update-fields').on( 'click', function(ev){
            var t = $(this),
                select = t.parent().find('select'),
                selected_option = select.val(),
                list_id = t.parents( '#plugin-fw-wc').find( '.list-select').val();

            ev.preventDefault();

            $.ajax({
                beforeSend: function(){
                    t.block({
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    });


                },
                complete: function(){
                    t.unblock();
                },
                data: {
                    force_update: true,
                    action: yith_wcmc.actions.retrieve_fields_via_ajax_action,
                    list: list_id,
                    yith_wcmc_ajax_request_nonce: yith_wcmc.ajax_request_nonce
                },
                dataType: 'json',
                method: 'POST',
                success: function( fields ){
                    var new_options = '',
                        i = 0;

                    if( typeof( selected_option ) == 'undefined' ){
                        selected_option = 'EMAIL';
                    }

                    if( fields.length != 0 ){
                        for( i in fields ){
                            new_options += '<option value="' + i + '" ' + ( ( selected_option == i ) ? 'selected="selected"' : '' ) + ' >' + fields[i]+ '</option>';
                        }
                    }

                    select.html( new_options ).select2();

                },
                url: ajaxurl
            });
        });
    };

    handle_fields_button();

    fields_content.sortable();
} );