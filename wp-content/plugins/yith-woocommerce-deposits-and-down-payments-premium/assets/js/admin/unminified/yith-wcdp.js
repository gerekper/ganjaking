jQuery( document ).ready( function($){
    var general_deposit_type = $( '#yith_wcdp_general_deposit_type' ),
        general_deposit_amount = $( '#yith_wcdp_general_deposit_amount' ),
        general_deposit_rate = $( '#yith_wcdp_general_deposit_rate'),
        general_deposit_virtual = $( '#yith_wcdp_general_deposit_virtual'),
        general_deposit_shipping = $( '#yith_wcdp_general_deposit_shipping'),
        general_deposit_shipping_admin_selection = $( '#yith_wcdp_general_deposit_shipping_admin_selection'),
        general_create_balance_orders = $( '#yith_wcdp_general_create_balance_orders' ),
        deposit_expiration_enable = $( '#yith_wcdp_deposit_expiration_enable' ),
        deposits_expiration_type = $( '#yith_wcdp_deposits_expiration_type'),
        deposits_expiration_duration = $( '#yith_wcdp_deposits_expiration_duration'),
        deposits_expiration_date = $( '#yith_wcdp_deposits_expiration_date'),
        deposit_expiration_product_fallback = $( '#yith_wcdp_deposits_expiration_product_fallback'),
        deposit_expiration_fallback = $( '#yith_wcdp_deposit_expiration_fallback'),
        notify_customer_deposit_expiring = $( '#yith_wcdp_notify_customer_deposit_expiring' ),
        notify_customer_deposit_expiring_days_limit = $( '#yith_wcdp_notify_customer_deposit_expiring_days_limit'),
        balance_type = $( '#yith_wcdp_balance_type' ),
        labels_product_note = $( '#yith_wcdp_deposit_labels_product_note'),
        labels_product_note_position = $( '#yith_wcdp_deposit_labels_product_note_position');

    // fields dependencies
    general_deposit_type.on( 'change', function(){
        var t = $(this),
            val = t.val();

        if( val == 'amount' ){
            general_deposit_amount.parents( 'tr' ).show();
            general_deposit_rate.parents( 'tr' ).hide();
        }
        else if( val == 'rate' ){
            general_deposit_amount.parents( 'tr' ).hide();
            general_deposit_rate.parents( 'tr' ).show();
        }
    }).change();

    general_deposit_virtual.on( 'change', function(){
        var t = $(this);

        if( t.is( ':checked' ) ){
            general_deposit_shipping.parents('tr').show().end().change();
        }
        else{
            general_deposit_shipping.parents('tr').hide().end().change();
        }
    }).change();

    general_deposit_shipping.on( 'change', function(){
        var t = $(this),
            val = t.val();

        if( val === 'admin_choose' && t.is( ':visible' ) ){
            general_deposit_shipping_admin_selection.parents('tr').show();
        }
        else{
            general_deposit_shipping_admin_selection.parents('tr').hide();
        }
    }).change();

    deposit_expiration_enable.on( 'change', function(){
        var t = $(this);

        if( t.is( ':checked' ) ){
            deposits_expiration_type.parents('tr').show().end().change();
            notify_customer_deposit_expiring.change().parents('tr').show();
            deposit_expiration_fallback.change().parents('tr').show();
        }
        else{
            deposits_expiration_type.parents('tr').hide();
            deposits_expiration_duration.parents('tr').hide();
            deposits_expiration_date.parents('tr').hide();
            notify_customer_deposit_expiring.parents('tr').hide();
            notify_customer_deposit_expiring_days_limit.parents('tr').hide();
            deposit_expiration_product_fallback.parents('tr').hide();
            deposit_expiration_fallback.change().parents('tr').hide();
        }
    }).change();

    balance_type.on( 'change', function(){
        var t = $(this);

        if( t.val() === 'none' ){
            general_create_balance_orders.parents('tr').hide();
        }
        else{
            general_create_balance_orders.parents('tr').show();
        }
    } ).change();

    deposits_expiration_type.on( 'change', function(){
        var t = $(this);

        if( ! t.is(':visible') ){
            return;
        }

        if( t.val() === 'num_of_days' ){
            deposits_expiration_duration.parents('tr').show();
            deposits_expiration_date.parents('tr').hide();
            deposit_expiration_product_fallback.parents('tr').hide();
        }
        else{
            deposits_expiration_duration.parents('tr').hide();
            deposits_expiration_date.parents('tr').show();
            deposit_expiration_product_fallback.parents('tr').show();
        }
    }).change();

    notify_customer_deposit_expiring.on( 'change', function(){
        var t = $(this);

        if( t.is( ':checked' ) ){
            notify_customer_deposit_expiring_days_limit.parents('tr').show();
        }
        else{
            notify_customer_deposit_expiring_days_limit.parents('tr').hide();
        }
    }).change();

    labels_product_note_position.on( 'change', function(){
        var t = $(this);

        if( t.val() === 'none' ){
            labels_product_note.parents('tr').hide();
        }
        else{
            labels_product_note.parents('tr').show();
        }
    }).change();

    // rate handling
    $('#yith_wcdp_panel_deposits').find( 'td.column-type select' ).on( 'change', function(){
        var t = $(this),
            row = t.closest('tr'),
            value = row.find('.column-value').find('input');

        if( t.val() === 'rate' ){
            value.attr( 'max', 100 );
        }
        else{
            value.attr( 'max', 9999999 );
        }
    }).change();

    // deposits actions
    $('.yith-roles-update-deposit').on( 'click', function(ev){
        var t = $(this),
            row = t.parents( 'tr' ),
            role_slug = t.data('role_slug'),
            type = row.find( '.column-type select').val(),
            value = row.find( '.column-value input').val();

        ev.preventDefault();

        if( type === 'rate' && value > 100 ){
            alert( yith_wcdp.labels.max_rate_notice );
            return;
        }

        $.ajax( {
            beforeSend: function(){
                t.block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });
            },
            data: {
                action: 'yith_wcdp_update_roles_deposit',
                role_slug: role_slug,
                type: type,
                value: value
            },
            dataType: 'json',
            method: 'POST',
            complete: function(){
                t.unblock();
            },
            success: function( data ){

            },
            url: ajaxurl
        } );
    } );

    $('.yith-roles-delete-deposit').on( 'click', function(ev){
        var t = $(this),
            row = t.parents( 'tr' ),
            table = row.parents('tbody'),
            role_slug = t.data('role_slug');

        ev.preventDefault();

        $.ajax( {
            beforeSend: function(){
                t.block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });
            },
            data: {
                action: 'yith_wcdp_delete_roles_deposit',
                role_slug: role_slug
            },
            dataType: 'json',
            method: 'POST',
            complete: function(){
                t.unblock();
            },
            success: function( data ){
                row.remove();

                if( table.find('tr').length == 0 ){
                    table.html( yith_wcdp.empty_row )
                }
            },
            url: ajaxurl
        } );
    } );

    $('.yith-products-update-deposit').on( 'click', function(ev){
        var t = $(this),
            row = t.parents( 'tr' ),
            product = t.data('product_id'),
            type = row.find( '.column-type select').val(),
            value = row.find( '.column-value input').val();

        ev.preventDefault();

        if( type === 'rate' && value > 100 ){
            alert( yith_wcdp.labels.max_rate_notice );
            return;
        }

        $.ajax( {
            beforeSend: function(){
                t.block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });
            },
            data: {
                action: 'yith_wcdp_update_products_deposit',
                product_id: product,
                type: type,
                value: value
            },
            dataType: 'json',
            method: 'POST',
            complete: function(){
                t.unblock();
            },
            success: function( data ){

            },
            url: ajaxurl
        } );
    } );

    $('.yith-products-delete-deposit').on( 'click', function(ev){
        var t = $(this),
            row = t.parents( 'tr' ),
            table = row.parents('tbody'),
            product = t.data('product_id');

        ev.preventDefault();

        $.ajax( {
            beforeSend: function(){
                t.block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });
            },
            data: {
                action: 'yith_wcdp_delete_products_deposit',
                product_id: product
            },
            dataType: 'json',
            method: 'POST',
            complete: function(){
                t.unblock();
            },
            success: function( data ){
                row.remove();

                if( table.find('tr').length == 0 ){
                    table.html( yith_wcdp.empty_row )
                }
            },
            url: ajaxurl
        } );
    } );

    $('.yith-categories-update-deposit').on( 'click', function(ev){
        var t = $(this),
            row = t.parents( 'tr' ),
            term = t.data('term_id'),
            type = row.find( '.column-type select').val(),
            value = row.find( '.column-value input').val();

        ev.preventDefault();

        if( type === 'rate' && value > 100 ){
            alert( yith_wcdp.labels.max_rate_notice );
            return;
        }

        $.ajax( {
            beforeSend: function(){
                t.block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });
            },
            data: {
                action: 'yith_wcdp_update_categories_deposit',
                term_id: term,
                type: type,
                value: value
            },
            dataType: 'json',
            method: 'POST',
            complete: function(){
                t.unblock();
            },
            success: function( data ){

            },
            url: ajaxurl
        } );
    } );

    $('.yith-categories-delete-deposit').on( 'click', function(ev){
        var t = $(this),
            row = t.parents( 'tr' ),
            table = row.parents('tbody'),
            term = t.data('term_id');

        ev.preventDefault();

        $.ajax( {
            beforeSend: function(){
                t.block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });
            },
            data: {
                action: 'yith_wcdp_delete_categories_deposit',
                term_id: term
            },
            dataType: 'json',
            method: 'POST',
            complete: function(){
                t.unblock();
            },
            success: function( data ){
                row.remove();

                if( table.find('tr').length === 0 ){
                    table.html( yith_wcdp.empty_row )
                }
            },
            url: ajaxurl
        } );
    } );

    // Date pickers
    $( document.body ).on( 'wc-init-datepickers woocommerce_variations_loaded', function() {
        $( '.date-picker-field, .date-picker' ).datepicker({
            dateFormat: 'yy-mm-dd',
            numberOfMonths: 1,
            showButtonPanel: true
        });
    }).trigger( 'wc-init-datepickers' );
} );