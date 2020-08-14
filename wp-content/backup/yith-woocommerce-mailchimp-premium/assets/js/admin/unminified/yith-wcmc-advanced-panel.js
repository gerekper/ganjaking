jQuery( document ).ready( function( $ ){
    var body = $( 'body'),
        advanced_integration_content = $( '.advanced-integration-content'),
        add_set_button = $( '#add_options_set'),
        field_ids = [],
        condition_ids = [];

    // add dependencies handler
    $( '#yith_wcmc_mailchimp_integration_mode').on( 'change', function(){
        var t = $(this);

        if( t.val() == 'simple' ){
            $( '#yith_wcmc_mailchimp_list').parents( 'tr').show();
            $( '#yith_wcmc_mailchimp_groups').parents( 'tr').show();
            add_set_button.parents( 'tr').hide();
        }
        else{
            $( '#yith_wcmc_mailchimp_list').parents( 'tr').hide();
            $( '#yith_wcmc_mailchimp_groups').parents( 'tr').hide();
            add_set_button.parents( 'tr').show();
        }
    }).change();

    // add set handler
    $(document).on( 'click', '#add_options_set', function(ev){
        var t = $(this);

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
                action: yith_wcmc_advanced_panel.actions.add_advanced_panel_item_action,
                item_id: yith_wcmc_advanced_panel.item_id
            },
            dataType: 'html',
            error: function(){

            },
            method: 'POST',
            success: function( data ){
                if( data.length != 0 ){
                    advanced_integration_content.prepend( data );
                    $( advanced_integration_content.find('.advanced-panel-item').get(0) ).hide().slideDown();
                    yith_wcmc_advanced_panel.item_id ++;

                    handle_items_button();
                    body.trigger( 'wc-enhanced-select-init' );
                    body.trigger( 'add_updater_handler' );
                }
            },
            url: ajaxurl
        });
    } );

    // accordions handler
    var handle_items_button = function() {
            $('.advanced-panel-item .panel-item-handle a.collapse-button').off( 'click' ).on('click', function (ev) {
                var t = $(this),
                    tab = t.parents('.advanced-panel-item'),
                    tab_content = tab.find('.panel-item-content');

                ev.preventDefault();

                tab_content.slideToggle( 300, function(){
                    tab.toggleClass('opened');
                });
            });

            $('.advanced-panel-item .panel-item-handle a.remove-button').off( 'click' ).on('click', function (ev) {
                var t = $(this),
                    tab = t.parents('.advanced-panel-item');

                ev.preventDefault();

                tab.slideUp(300, function(){
                    $(this).remove();
                });
            });

            $('.advanced-panel-item a.add-field').off( 'click' ).on( 'click', function(ev){
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

                        if( 'undefined' == typeof( field_ids[ item_id ] ) ){
                            field_ids[ item_id ] = item.find( '.field-item' ).length + 1;
                        }
                    },
                    complete: function(){
                        t.unblock();
                        field_ids[ item_id ] = field_ids[ item_id ] + 1;
                    },
                    data: {
                        action: yith_wcmc_advanced_panel.actions.add_advanced_panel_field_action,
                        item_id: item_id,
                        field_id: ( 'undefined' == typeof( field_ids[ item_id ] ) ) ? item.find( '.field-item' ).length + 1 : field_ids[ item_id ],
                        list_id: t.parents('.advanced-panel-item').find( 'select.list-select').val()
                    },
                    dataType: 'html',
                    error: function(){

                    },
                    method: 'POST',
                    success: function( data ){
                        if( data.length != 0 ){
                            t.parents('.section').find('.fields-content').prepend( data );
                            $( t.parents('.section').find('.fields-content .field-item').get(0) ).hide().slideDown();

                            body.trigger( 'wc-enhanced-select-init' );
                            handle_subitems_button();
                        }
                    },
                    url: ajaxurl
                });
            });

            $('.advanced-panel-item a.add-condition').off( 'click' ).on( 'click', function(ev){
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

                        if( 'undefined' == typeof( condition_ids[ item_id ] ) ){
                            condition_ids[ item_id ] = item.find( '.condition-item' ).length + 1;
                        }
                    },
                    complete: function(){
                        t.unblock();
                        condition_ids[ item_id ] = condition_ids[ item_id ] + 1;
                    },
                    data: {
                        action: yith_wcmc_advanced_panel.actions.add_advanced_panel_condition_action,
                        item_id: item_id,
                        condition_id: ( 'undefined' == typeof( condition_ids[ item_id ] ) ) ? item.find( '.condition-item' ).length + 1 : condition_ids[ item_id ]
                    },
                    dataType: 'html',
                    error: function(){

                    },
                    method: 'POST',
                    success: function( data ){
                        if( data.length != 0 ){
                            t.parents('.section').find('.conditions-content').prepend( data );
                            $( t.parents('.section').find('.conditions-content .condition-item').get(0) ).hide().slideDown();

                            body.trigger( 'wc-enhanced-select-init' );
                            handle_subitems_button();
                        }
                    },
                    url: ajaxurl
                });
            });
        },
        handle_conditions_select = function(ev){
            var t = $(this),
                condition = t.val(),
                fields_column = t.parent().next(),
                op_mixed_select = fields_column.find( '.condition_op_mixed'),
                op_set_select = fields_column.find( '.condition_op_set'),
                op_number_select = fields_column.find( '.condition_op_number'),
                products_select = fields_column.find( '.condition_products.enhanced' ).add( fields_column.find( '.condition_products.enhanced' ).next() ),
                cats_select = fields_column.find( '.condition_cats.enhanced').add( fields_column.find( '.condition_cats.enhanced').next() ),
                total_input = fields_column.find( '.condition_total'),
                key_input = fields_column.find( '.condition_key'),
                value_input = fields_column.find( '.condition_value');

            fields_column.fadeOut(300, function(){
                switch( condition ){
                    case 'product_in_cart':
                        op_mixed_select.hide();
                        op_set_select.show();
                        op_number_select.hide();
                        products_select.removeClass( 'select-hidden' );
                        cats_select.addClass( 'select-hidden' );
                        total_input.hide();
                        key_input.hide();
                        value_input.hide();
                        break;
                    case 'product_cat_in_cart':
                        op_mixed_select.hide();
                        op_set_select.show();
                        op_number_select.hide();
                        products_select.addClass( 'select-hidden' );
                        cats_select.removeClass( 'select-hidden' );
                        total_input.hide();
                        key_input.hide();
                        value_input.hide();
                        break;
                    case 'order_total':
                        op_mixed_select.hide();
                        op_set_select.hide();
                        op_number_select.show();
                        products_select.addClass( 'select-hidden' );
                        cats_select.addClass( 'select-hidden' );
                        total_input.show();
                        key_input.hide();
                        value_input.hide();
                        break;
                    case 'custom':
                        op_mixed_select.show();
                        op_set_select.hide();
                        op_number_select.hide();
                        products_select.addClass( 'select-hidden' );
                        cats_select.addClass( 'select-hidden' );
                        total_input.hide();
                        key_input.show();
                        value_input.show();
                        break;
                }
                fields_column.fadeIn();
            });
        },
        handle_subitems_button = function(){
            $('.advanced-panel-item .panel-item-content .field-item a.remove-button').on( 'click', function(ev) {
                var t = $(this),
                    item = t.parents( '.field-item' );

                ev.preventDefault();

                item.slideUp(300, function(){
                    $(this).remove();
                });
            });

            $('.advanced-panel-item .panel-item-content .field-item a.update-fields').on( 'click', function(ev){
                var t = $(this),
                    select = t.parent().find('select'),
                    selected_option = select.val(),
                    list_id = t.parents( '.panel-item-content').find( 'select.list-select').val();

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
                        list: list_id,
                        force_update: true,
                        action: yith_wcmc.actions.retrieve_fields_via_ajax_action,
                        yith_wcmc_ajax_request_nonce: yith_wcmc.ajax_request_nonce
                    },
                    dataType: 'json',
                    method: 'POST',
                    success: function( fields ){
                        var new_options = '',
                            i = 0;

                        if( fields.length != 0 ){
                            for( i in fields ){
                                new_options += '<option value="' + i + '" ' + ( ( i == selected_option ) ? 'selected="selected"' : '' ) + ' >' + fields[i]+ '</option>';
                            }
                        }

                        select.html( new_options );

                        if( new_options.length == 0 ){
                            select.prop( 'disabled' );
                        }
                        else{
                            select.removeProp( 'disabled' );
                        }

                    },
                    url: ajaxurl
                });
            });

            $('.advanced-panel-item .panel-item-content .condition-item a.remove-button').on( 'click', function(ev) {
                var t = $(this),
                    item = t.parents( '.condition-item' );

                ev.preventDefault();

                item.slideUp(300, function(){
                    $(this).remove();
                });
            });

            $('.advanced-panel-item .panel-item-content .condition-item select.condition_type').on( 'change', handle_conditions_select ).change();
        };

    handle_items_button();
    handle_subitems_button();
} );