jQuery( document ).ready( function($){
    // panel dependencies handling
    var $body = $( document.body ),
        general_referral_cod = $( '#yith_wcaf_general_referral_cod' ),
        referral_var_name = $( '#yith_wcaf_referral_var_name' ),
        history_cookie_enabled = $( '#yith_wcaf_history_cookie_enable' ),
        history_cookie_name = $( '#yith_wcaf_history_cookie_name'),
        history_make_cookie_expire = $( '#yith_wcaf_history_make_cookie_expire'),
        history_cookie_expire = $( '#yith_wcaf_history_cookie_expire'),
        commission_persistent_calculation = $( '#yith_wcaf_commission_persistent_calculation'),
        persistent_rate = $( '#yith_wcaf_persistent_rate'),
        avoid_referral_change = $( '#yith_wcaf_avoid_referral_change'),
        referral_make_cookie_expire = $( '#yith_wcaf_referral_make_cookie_expire'),
        referral_cookie_expire = $( '#yith_wcaf_referral_cookie_expire'),
        referral_registration_show_terms_field = $( '#yith_wcaf_referral_registration_show_terms_field'),
        referral_registration_terms_label = $( '#yith_wcaf_referral_registration_terms_label'),
        referral_registration_terms_anchor_url = $( '#yith_wcaf_referral_registration_terms_anchor_url'),
        referral_registration_terms_anchor_text = $( '#yith_wcaf_referral_registration_terms_anchor_text'),
        coupon_enable = $( '#yith_wcaf_coupon_enable' ),
        coupon_show_section = $( '#yith_wcaf_coupon_show_section' ),
        coupon_limit_section = $( '#yith_wcaf_coupon_limit_section' ),
        payment_pending_notify_admin = $( '#yith_wcaf_payment_pending_notify_admin' ),
        payment_type = $('#yith_wcaf_payment_type'),
        payment_date = $('#yith_wcaf_payment_date'),
        payment_pay_only_old_commissions = $('#yith_wcaf_payment_pay_only_old_commissions'),
        payment_commission_age = $('#yith_wcaf_payment_commission_age'),
        payment_default_gateway = $('#yith_wcaf_payment_default_gateway'),
        payment_threshold = $('#yith_wcaf_payment_threshold'),
        payment_require_invoice = $('#yith_wcaf_payment_require_invoice'),
        payment_invoice_mode = $('#yith_wcaf_payment_invoice_mode'),
        payment_invoice_example = $('#yith_wcaf_payment_invoice_example'),
        payment_invoice_company_section = $('#yith_wcaf_payment_invoice_company_section'),
        payment_invoice_fields = $('#yith_wcaf_payment_invoice_fields'),
        payment_invoice_template = $('#yith_wcaf_payment_invoice_template'),
        payment_invoice_show_terms_field = $('#yith_wcaf_payment_invoice_show_terms_field'),
        payment_invoice_terms_label = $('#yith_wcaf_payment_invoice_terms_label'),
        payment_invoice_terms_anchor_url = $('#yith_wcaf_payment_invoice_terms_anchor_url'),
        payment_invoice_terms_anchor_text = $('#yith_wcaf_payment_invoice_terms_anchor_text'),
        click_enabled = $('#yith_wcaf_click_enabled'),
        click_resolution = $('#yith_wcaf_click_resolution'),
        click_auto_delete = $('#yith_wcaf_click_auto_delete'),
        click_auto_delete_expiration = $('#yith_wcaf_click_auto_delete_expiration');

    general_referral_cod.on( 'change', function(){
        var t = $(this);

        if( t.val() == 'query_string' ){
            referral_var_name.parents( 'tr' ).show();
        }
        else{
            referral_var_name.parents( 'tr' ).hide();
        }
    }).change();

    history_cookie_enabled.on( 'change', function(){
        var t = $(this);

        if( t.is( ':checked' ) ){
            history_cookie_name.parents( 'tr' ).show();
            history_make_cookie_expire.parents( 'tr' ).show();

            if( history_make_cookie_expire.is( ':checked' ) ) {
                history_cookie_expire.parents('tr').show();
            }
        }
        else{
            history_cookie_name.parents( 'tr' ).hide();
            history_cookie_expire.parents( 'tr' ).hide();
            history_make_cookie_expire.parents( 'tr' ).hide();
        }
    }).change();

    history_make_cookie_expire.on( 'change', function(){
        var t = $(this);

        if( t.is( ':checked' ) ){
            history_cookie_expire.parents( 'tr' ).show();
        }
        else{
            history_cookie_expire.parents( 'tr' ).hide();
        }
    }).change();

    commission_persistent_calculation.on( 'change', function(){
        var t = $(this);

        if( t.is( ':checked' ) ){
            persistent_rate.parents( 'tr' ).show();
            avoid_referral_change.parents( 'tr' ).show();
        }
        else{
            persistent_rate.parents( 'tr' ).hide();
            avoid_referral_change.parents( 'tr' ).hide();
        }
    }).change();

    referral_make_cookie_expire.on( 'change', function(){
        var t = $(this);

        if( t.is( ':checked' ) ){
            referral_cookie_expire.parents( 'tr' ).show();
        }
        else{
            referral_cookie_expire.parents( 'tr' ).hide();
        }
    }).change();

    referral_registration_show_terms_field.on( 'change', function(){
        var t = $(this);

        if( t.is( ':checked' ) ){
            referral_registration_terms_label.parents( 'tr' ).show();
            referral_registration_terms_anchor_url.parents( 'tr' ).show();
            referral_registration_terms_anchor_text.parents( 'tr' ).show();
        }
        else{
            referral_registration_terms_label.parents( 'tr' ).hide();
            referral_registration_terms_anchor_url.parents( 'tr' ).hide();
            referral_registration_terms_anchor_text.parents( 'tr' ).hide();
        }
    } ).change();

    coupon_enable.on( 'change', function(){
        var t = $(this);

        if( t.is( ':checked' ) ){
            coupon_show_section.closest( 'tr' ).show().end().change();
            payment_pending_notify_admin.closest( 'tr' ).show();
        }
        else{
            coupon_show_section.closest( 'tr' ).hide().end().change();
            payment_pending_notify_admin.closest( 'tr' ).hide();
        }
    } ).change();

    coupon_show_section.on( 'change', function(){
        var t = $(this);

        if( t.is( ':checked' ) && t.is( ':visible' ) ){
            coupon_limit_section.closest( 'tr' ).show();
        }
        else{
            coupon_limit_section.closest( 'tr' ).hide();
        }
    } ).change();

    payment_type.on( 'change', function(){
        var t = $(this),
            val = t.val();

        if( val == 'manually' ){
            payment_default_gateway.parents('tr').hide();
            payment_date.parents('tr').hide();
            payment_pay_only_old_commissions.parents('tr').hide().end().change();
            payment_commission_age.parents('tr').hide();
            payment_threshold.parents('tr').hide();
            payment_require_invoice.parents('tr').hide().end().change();
        }
        else if( val == 'automatically_on_threshold' ){
            payment_default_gateway.parents('tr').show();
            payment_date.parents('tr').hide();
            payment_pay_only_old_commissions.parents('tr').show().end().change();
            payment_threshold.parents('tr').show();
            payment_require_invoice.parents('tr').hide().end().change();
        }
        else if( val == 'automatically_on_date' ){
            payment_default_gateway.parents('tr').show();
            payment_date.parents('tr').show();
            payment_pay_only_old_commissions.parents('tr').show().end().change();
            payment_threshold.parents('tr').hide();
            payment_require_invoice.parents('tr').hide().end().change();
        }
        else if( val == 'automatically_on_both' ){
            payment_default_gateway.parents('tr').show();
            payment_date.parents('tr').show();
            payment_pay_only_old_commissions.parents('tr').show().end().change();
            payment_threshold.parents('tr').show();
            payment_require_invoice.parents('tr').hide().end().change();
        }
        else if( val == 'automatically_every_day' ){
            payment_default_gateway.parents('tr').show();
            payment_date.parents('tr').hide();
            payment_pay_only_old_commissions.parents('tr').show().end().change();
            payment_threshold.parents('tr').hide();
            payment_require_invoice.parents('tr').hide().end().change();
        }
        else if( val == 'let_user_request' ){
            payment_default_gateway.parents('tr').hide();
            payment_date.parents('tr').hide();
            payment_pay_only_old_commissions.parents('tr').hide();
            payment_threshold.parents('tr').show();
            payment_require_invoice.parents('tr').show().end().change();
        }
    }).change();

    payment_require_invoice.on( 'change', function(){
        var t = $(this),
            checked = t.is( ':checked' ),
            visible = t.is( ':visible' );

        if( checked && visible ){
            payment_invoice_mode.parents('tr').show().end().change();
        }
        else{
            payment_invoice_mode.parents('tr').hide().end().change();
        }
    } ).change();

    payment_invoice_mode.on( 'change', function(){
        var t = $(this),
            v = t.val(),
            visible = t.is( ':visible' );

        if( ! visible ){
            payment_invoice_example.parents('tr').hide();
            payment_invoice_company_section.parents('tr').hide();
            payment_invoice_fields.parents('tr').hide();
            payment_invoice_template.parents('tr').hide();
            payment_invoice_show_terms_field.parents('tr').hide().end().change();
        }
        else if( v == 'upload' ){
            payment_invoice_example.parents('tr').show();
            payment_invoice_company_section.parents('tr').hide();
            payment_invoice_fields.parents('tr').hide();
            payment_invoice_template.parents('tr').hide();
            payment_invoice_show_terms_field.parents('tr').hide().end().change();
        }
        else if( v == 'generate' ){
            payment_invoice_example.parents('tr').hide();
            payment_invoice_company_section.parents('tr').show();
            payment_invoice_fields.parents('tr').show();
            payment_invoice_template.parents('tr').show();
            payment_invoice_show_terms_field.parents('tr').show().end().change();
        }
        else if( v == 'both' ){
            payment_invoice_example.parents('tr').show();
            payment_invoice_company_section.parents('tr').show();
            payment_invoice_fields.parents('tr').show();
            payment_invoice_template.parents('tr').show();
            payment_invoice_show_terms_field.parents('tr').show().end().change();
        }
    } ).change();

    payment_invoice_show_terms_field.on( 'change', function(){
        var t = $(this),
            visible = t.is( ':visible' );

        if( t.is( ':checked' ) && visible ){
            payment_invoice_terms_label.parents( 'tr' ).show();
            payment_invoice_terms_anchor_url.parents( 'tr' ).show();
            payment_invoice_terms_anchor_text.parents( 'tr' ).show();
        }
        else{
            payment_invoice_terms_label.parents( 'tr' ).hide();
            payment_invoice_terms_anchor_url.parents( 'tr' ).hide();
            payment_invoice_terms_anchor_text.parents( 'tr' ).hide();
        }
    } ).change();

    payment_pay_only_old_commissions.on( 'change', function(){
        var t = $(this);

        if( t.is( ':checked' ) && t.is( ':visible' ) ){
            payment_commission_age.parents( 'tr').show();
        }
        else{
            payment_commission_age.parents( 'tr').hide();
        }
    }).change();

    click_enabled.on( 'change', function(){
        var t = $(this);

        if( t.is( ':checked' ) ){
            click_resolution.parents('tr').show();
            click_auto_delete.change().parents('tr').show();
        }
        else{
            click_resolution.parents('tr').hide();
            click_auto_delete.parents('tr').hide();
            click_auto_delete_expiration.parents('tr').hide();
        }
    } ).change();

    click_auto_delete.on( 'change', function(){
        var t = $(this);

        if( t.is( ':checked' ) ){
            click_auto_delete_expiration.parents( 'tr').show();
        }
        else{
            click_auto_delete_expiration.parents( 'tr').hide();
        }
    }).change();

    $body.on( 'wc_backbone_modal_response', function( e, target, data ) {
        if ( 'yith-wcaf-message' === target ) {
            if( typeof data.url === 'undefined' ){
                return;
            }

            var final_url = data.url;

            // action performed from anchor
            if( final_url.length ) {
                if (typeof data.message !== 'undefined' && data.message) {
                    final_url = final_url + '&message=' + encodeURI(data.message);
                }

                window.location = final_url;
            }
            // action performed from bulk actions
            else{
                var form = $('#yith_wcaf_panel_affiliate').children('form');

                if( form.length ){
                    form.append( $( '<input/>', {
                        type: 'hidden',
                        name: 'message',
                        value: data.message
                    } ) );

                    form.submit();
                }
            }
        }
    } );

    // field description handling
    $( '.variable-description' ).on( 'change', function(){
        var t = $(this),
            conditional_descriptions = t.next('.description').find( '[data-value]' ),
            active_description = conditional_descriptions.filter('[data-value="' + t.val() + '"]');

        conditional_descriptions.hide();

        if( active_description.length ){
            active_description.show();
        }
        else{
            conditional_descriptions.first().show();
        }
    } ).change();

    // template field handling

    var view = yith_wcaf.labels.view_template;
    var hide = yith_wcaf.labels.hide_template;

    $( 'a.toggle_editor' ).text( view ).toggle( function() {
        $( this ).text( hide ).closest(' .template' ).find( '.editor' ).slideToggle();
        return false;
    }, function() {
        $( this ).text( view ).closest( '.template' ).find( '.editor' ).slideToggle();
        return false;
    } );

    $( 'a.delete_template' ).click( function() {
        if ( window.confirm( yith_wcaf.labels.confirm_template_delete ) ) {
            return true;
        }

        return false;
    });

    $( '.editor textarea' ).change( function() {
        var name = $( this ).attr( 'data-name' );

        if ( name ) {
            $( this ).attr( 'name', name );
        }
    });

    // rates actions
    $('.yith-affiliates-update-commission').on( 'click', function(ev){
        var t = $(this),
            row = t.parents( 'tr' ),
            affiliate_id = t.data('affiliate_id'),
            rate = row.find( '.column-rate input').val();

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
                action: 'yith_wcaf_update_affiliate_commission',
                affiliate_id: affiliate_id,
                rate: rate
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

    $('.yith-affiliates-delete-commission').on( 'click', function(ev){
        var t = $(this),
            row = t.parents( 'tr' ),
            table = row.parents('tbody'),
            affiliate_id = t.data('affiliate_id');

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
                action: 'yith_wcaf_delete_affiliate_commission',
                affiliate_id: affiliate_id
            },
            dataType: 'json',
            method: 'POST',
            complete: function(){
                t.unblock();
            },
            success: function( data ){
                row.remove();

                if( table.find('tr').length == 0 ){
                    table.html( yith_wcaf.empty_row )
                }
            },
            url: ajaxurl
        } );
    } );

    $('.yith-products-update-commission').on( 'click', function(ev){
        var t = $(this),
            row = t.parents( 'tr' ),
            product_id = t.data('product_id'),
            rate = row.find( '.column-rate input').val();

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
                action: 'yith_wcaf_update_product_commission',
                product_id: product_id,
                rate: rate
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

    $('.yith-products-delete-commission').on( 'click', function(ev){
        var t = $(this),
            row = t.parents( 'tr' ),
            table = row.parents('tbody'),
            product_id = t.data('product_id');

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
                action: 'yith_wcaf_delete_product_commission',
                product_id: product_id
            },
            dataType: 'json',
            method: 'POST',
            complete: function(){
                t.unblock();
            },
            success: function( data ){
                row.remove();

                if( table.find('tr').length == 0 ){
                    table.html( yith_wcaf.empty_row )
                }
            },
            url: ajaxurl
        } );
    } );

    // commissions actions
    $('#yith_wcaf_commission_notes')
        .on( 'click', 'a.add_note', function(ev){
            var t = $(this),
                sidebar = t.parents( '#yith_wcaf_commission_notes'),
                list = sidebar.find( 'ul'),
                textarea = sidebar.find( 'textarea'),
                note_content = textarea.val(),
                commission_id = $( '#commission_id' ).val();

            ev.preventDefault();

            if( ! note_content ){
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
                complete: function(){
                    t.unblock();
                },
                data: {
                    commission_id: commission_id,
                    note_content: note_content,
                    action: 'yith_wcaf_add_commission_note'
                },
                method: 'POST',
                success: function(response){
                    if( response.template ) {
                        list.prepend(response.template);
                    }

                    if( list.find('li').length > 0 ){
                        list.find('li.no_notes').hide();
                    }

                    textarea.val( '' );
                },
                url: ajaxurl
            } );
        } )
        .on( 'click', 'a.delete_note', function(ev){
            var t = $(this),
                sidebar = t.parents( '#yith_wcaf_commission_notes'),
                list = sidebar.find( 'ul'),
                li = t.parents( 'li'),
                note_id = li.attr( 'rel' );

            ev.preventDefault();

            if( ! note_id ){
                return;
            }

            $.ajax( {
                beforeSend: function(){
                    li.block({
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    });
                },
                complete: function(){
                    li.unblock();
                },
                data: {
                    note_id: note_id,
                    action: 'yith_wcaf_delete_commission_note'
                },
                method: 'POST',
                success: function(response){
                    li.remove();

                    if( list.find('li').not('.no_notes').length == 0 ){
                        list.find('li.no_notes').show();
                    }
                },
                url: ajaxurl
            } );
        } );

    // payments actions
    $('#yith_wcaf_payment_notes')
        .on( 'click', 'a.add_note', function(ev){
            var t = $(this),
                sidebar = t.parents( '#yith_wcaf_payment_notes'),
                list = sidebar.find( 'ul'),
                textarea = sidebar.find( 'textarea'),
                note_content = textarea.val(),
                commission_id = $( '#payment_id' ).val();

            ev.preventDefault();

            if( ! note_content ){
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
                complete: function(){
                    t.unblock();
                },
                data: {
                    payment_id: commission_id,
                    note_content: note_content,
                    action: 'yith_wcaf_add_payment_note'
                },
                method: 'POST',
                success: function(response){
                    if( response.template ) {
                        list.prepend(response.template);
                    }

                    if( list.find('li').length > 0 ){
                        list.find('li.no_notes').hide();
                    }

                    textarea.val( '' );
                },
                url: ajaxurl
            } );
        } )
        .on( 'click', 'a.delete_note', function(ev){
            var t = $(this),
                sidebar = t.parents( '#yith_wcaf_payment_notes'),
                list = sidebar.find( 'ul'),
                li = t.parents( 'li'),
                note_id = li.attr( 'rel' );

            ev.preventDefault();

            if( ! note_id ){
                return;
            }

            $.ajax( {
                beforeSend: function(){
                    li.block({
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    });
                },
                complete: function(){
                    li.unblock();
                },
                data: {
                    note_id: note_id,
                    action: 'yith_wcaf_delete_payment_note'
                },
                method: 'POST',
                success: function(response){
                    li.remove();

                    if( list.find('li').not('.no_notes').length == 0 ){
                        list.find('li.no_notes').show();
                    }
                },
                url: ajaxurl
            } );
        } );

    // affiliates actions
    $('#yith_wcaf_panel_affiliate')
        .on( 'click', 'a.button.ban, a.button.disable', function(ev) {
            var t = $(this),
                title;

            ev.preventDefault();

            if( t.hasClass( 'ban' ) ){
                title = yith_wcaf.labels.ban_message;
            }
            else if( t.hasClass( 'disable' ) ){
                title = yith_wcaf.labels.rejected_message;
            }

            t.WCBackboneModal({
                template: 'yith-wcaf-message',
                variable: {
                    title: title,
                    url: t.attr( 'href' )
                }
            });

            return false;
        } )
        .on( 'click', '#doaction, #doaction2', function(ev){
            var t = $(this),
                select = t.prev(),
                openMessageModal = false,
                title,
                name;

            if( select.val() === 'ban' ){
                openMessageModal = true;
                title = yith_wcaf.labels.ban_message;
                name = 'ban_message';
            }

            if( select.val() === 'disable' ){
                openMessageModal = true;
                title = yith_wcaf.labels.rejected_message;
                name = 'reject_message';
            }

            if( openMessageModal ) {
                ev.preventDefault();

                t.WCBackboneModal({
                    template: 'yith-wcaf-message',
                    variable: {
                        title: title,
                        name : name,
                        url  : ''
                    }
                });
            }
        } );

    // add payment detail behaviour
    $( '.edit_address_button').on( 'click', function(ev){
        var t = $(this);

        ev.preventDefault();

        t.parent().nextAll( '.address').toggle();
        t.parent().nextAll( '.edit_address').toggle();
    } );

    // commissions filter
    $( ".date-picker-field, .date-picker" ).datepicker({
        dateFormat: "yy-mm-dd",
        numberOfMonths: 1,
        showButtonPanel: true
    });

    // user edit actions
    $('.user-edit-php')
        .find( '#enabled' ).on( 'change', function(){
            var t = $(this);

            if( t.val() === '-1' ){
                t.closest( 'table' ).find('#reject_message').closest('tr').show();
            }
            else{
                t.closest( 'table' ).find('#reject_message').closest('tr').hide();
            }
        } ).change()
        .end()
        .find( '#banned' ).on( 'change', function(){
            var t = $(this);

            if( t.is( ':checked' ) ){
                t.closest( 'table' ).find('#ban_message').closest('tr').show();
            }
            else{
                t.closest( 'table' ).find('#ban_message').closest('tr').hide();
            }
        } ).change();

    // add badges to tab headings
    if( yith_wcaf.tabs_badges.commissions != 0 ){
        $( '.nav-tab-wrapper').find( '[href*="tab=commissions"]' ).append( ' <span class="pending-count">' + yith_wcaf.tabs_badges.commissions + '</span>' );
    }
    if( yith_wcaf.tabs_badges.affiliates != 0 ){
        $( '.nav-tab-wrapper').find( '[href*="tab=affiliates"]' ).append( ' <span class="pending-count">' + yith_wcaf.tabs_badges.affiliates + '</span>' );
    }
    if( yith_wcaf.tabs_badges.payments != 0 ){
        $( '.nav-tab-wrapper').find( '[href*="tab=payments"]' ).append( ' <span class="pending-count">' + yith_wcaf.tabs_badges.payments + '</span>' );
    }
} );
