/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */


( function ( $ ) {

    /* === Licence API === */

    var $body               = $( 'body' ),
        $document           = $(document),
        form                = $('#yith-license-activation'),
        message_email       = $(form).find('.error-message.email'),
        message_license_key = $(form).find('.error-message.license-key'),
        email               = form.find('.user-email'),
        licence_key         = form.find('.licence-key'),
        is_mail             = function( val ){
            /* https://stackoverflow.com/questions/2855865/jquery-validate-e-mail-address-regex */
            var re = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
            return re.test( val );
        },
        is_license_key     = function( val ){
            var re = new RegExp(/^[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}$/g);
            return re.test( val );
        };

    $.yith_license_animation = function( elem, spinner, action ){
        var table_wrapper           = $( '#licence-check-section-wrapper' ),
            no_license_activated    = $( '#no-license-enabled-message' ),
            no_license_to_enable    = $( '#yith-no-license-to-enabled-message' ),
            plugins_form            = $('#yith-license-from-wrapper');

        if( action == 'beforeSend' ){
            elem.animate({opacity: 0.4}, 100);
            spinner.addClass( 'show' );
        }

        else if( action == 'afterSend' ){
            elem.animate({opacity: 1}, 100);
            spinner.removeClass( 'show' );
        }

        else if( action == 'removeLine' ){
            var tbody   = elem.parent( 'tbody' ),
                tr      = tbody.find( 'tr' );

            if( $( '#yith-products-list' ).find( 'option' ).length === 0 ){
                plugins_form.fadeIn();
                no_license_to_enable.fadeOut();
            }

            if( tr.length == 1 ){
                no_license_activated.fadeIn();
                table_wrapper.fadeOut();
                elem.remove();
            }
            else {
                elem.fadeOut();
            }
            spinner.removeClass( 'show' );
        }

        else if( action == 'addLine' ){
            var tbody   = elem.parent( 'tbody' ),
                tr      = tbody.find( 'tr' );

            if( $( '#yith-products-list' ).find( 'option' ).length === 1 ){
                plugins_form.fadeOut();
                no_license_to_enable.fadeIn();
            }

            if( tr.length == 1 ){
                elem.show();
                elem = table_wrapper;
                no_license_activated.fadeOut();
            }
            elem.fadeIn();
            spinner.removeClass( 'show' );
        }
    };

    var add_option_to_product_list = function ( elem, select ) {
        var textdomain = elem.data('textdomain'),
            init = elem.data('product-init'),
            marketplace = elem.data('marketplace'),
            display_name = elem.data('displayname'),
            option = $( '<option data-textdomain="' + textdomain + '" data-init="' + init + '" data-marketplace="' + marketplace + '">' + display_name + '</option>' );

        select.append( option );
    };

    var licence_activation = function ( button ) {
        button.off('click').on( 'click', function ( e, button ) {
            e.preventDefault();

            var t                       = $( this ),
                form                    = $('#yith-license-activation'),
                products_list           = $( '#yith-products-list' ),
                current_product         = products_list.find( 'option:selected' ),
                product_init            = current_product.data('init'),
                marketplace             = current_product.data('marketplace'),
                message_wrapper         = $( '#yith-licence-notice-wrapper' ),
                message                 = $( '#yith-licence-notice' ),
                data                    = null,
                spinner                 = $( '#products-to-active' ).find( '.spinner' ),
                error                   = false,
                scroll_anchor           = $( '#licence-check-update' );


            $( '#product_init' ).val( product_init );
            $( '#marketplace' ).val( marketplace );
            data = form.serialize();

            /* Init Input Fields */
            message_email.add( message_license_key ).empty();
            message_email.add( message_license_key ).removeClass( 'visible' );

            email_check         = $body.find( 'input.licence-key' ).trigger( 'focusout' );
            license_key_check   = $body.find( 'input.user-email' ).trigger( 'focusout' );

            if( email_check.hasClass( 'error' ) || license_key_check.hasClass( 'error' ) )  {
                error = true;
            }

            if ( error === false ) {
                jQuery.ajax({
                    type: 'POST',
                    url: typeof ajaxurl != 'undefined' ? ajaxurl : yith_ajax.url,
                    data: data,
                    beforeSend: function () {
                        $.yith_license_animation( form, spinner, 'beforeSend' );
                    },
                    success: function (response) {
                        $.yith_license_animation( form, spinner, 'afterSend' );

                        if (true === response.activated) {
                            var table = $('#yith-enabled-license'),
                            new_elem = $( response.template );
                            table.find( 'tbody' ).prepend( new_elem );
                            new_elem.hide();

                            $('html, body').animate({
                                scrollTop: scroll_anchor.offset().top
                            }, 500 ).promise().done(function(){
                                email.add( licence_key ).attr( 'value', '' );
                                current_product.remove();
                                product_count = form.data( 'count' );
                                console.log( product_count );
                                form.removeClass( 'count-' + product_count );
                                product_count = ( product_count * 1 ) - 1;
                                form.data( 'count', product_count ).addClass( 'count-' + product_count );

                                if( product_count == 1 || product_count == 0 ){
                                    products_list.attr( 'disabled', true );
                                    $('.yith-select-plugin').text( licence_message.choose_the_plugin_singular );
                                }

                                else {
                                    products_list.attr( 'disabled', false );
                                    $('.yith-select-plugin').text( licence_message.choose_the_plugin_plural );
                                }
                            });

                            $.yith_license_animation( new_elem, spinner, 'addLine' );
                            message.find('p.yith-licence-notice-message').html('#' + response.code + ': ' + response.activation_message);
                            message_wrapper.fadeIn();
                            licence_api();
                        } else if (false !== response) {
                            message_license_key.text('#' + response.code + ': ' + response.error);
                            message_email.add(message_license_key).addClass('visible');
                            form.addClass('error');
                        } else {
                            message_license_key.text(licence_message.server);
                            message_license_key.addClass('visible');
                            form.addClass('error');
                        }

                        if (typeof response.debug !== 'undefined') {
                            console.log(response.debug);
                        }
                    }
                });
            } else {
                $.yith_license_animation( form, spinner, 'afterSend' );
            }
        } );
    };

    var licence_update = function (button) {
        button.off('click').on('click', function (e) {
            e.preventDefault();

            var t = $(this),
                form = $('#licence-check-update'),
                license_table = $('#yith-enabled-license'),
                data = form.serialize();

            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: data,
                beforeSend: function () {
                    $.yith_license_animation( license_table, form.find('div.spinner'), 'beforeSend' );
                },
                success: function (response) {
                    $('.product-licence-activation').empty().replaceWith(response.template);
                },
                complete: function(){
                    licence_api();
                }
            });
        });
    };

    var licence_deactivate = function ( button ) {
        button.off('click').on( 'click', function ( e ) {
            e.preventDefault();

            var check = script_info.is_debug == true ? true : confirm( licence_message.are_you_sure );

            if ( check == true ) {
                var t               = $( this ),
                    tr_to_remove    = t.parent( 'td' ).parent( 'tr' ),
                    product_init    = t.data( 'product-init' ),
                    product_id      = t.data( 'product-id' ),
                    action          = t.data( 'action' ),
                    message_wrapper = $( '#yith-licence-notice-wrapper' ),
                    message         = $( '#yith-licence-notice' ),
                    spinner         = $( '#activated-products' ).find( '.spinner' );

                jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: action,
                        product_init: product_init,
                        product_id: product_id
                    },
                    beforeSend: function () {
                        $.yith_license_animation( tr_to_remove, spinner, 'beforeSend' );
                    },
                    success: function (response) {


                        if (false == response) {
                            message.find('p.yith-licence-notice-message').html(licence_message.server);
                            message_wrapper.fadeIn();
                        }

                        else {
                            if (false == response.activated) {
                                $.yith_license_animation( tr_to_remove, spinner, 'removeLine' );
                                add_option_to_product_list(t, $( '#yith-products-list' ) );
                                product_count = form.data( 'count' );
                                form.removeClass( 'count-' + product_count );
                                product_count = ( product_count * 1 ) + 1;
                                form.data( 'count', product_count ).addClass( 'count-' + product_count );

                                if( product_count == 1 || product_count == 0 ){
                                    products_list.attr( 'disabled', true );
                                    $('.yith-select-plugin').text( licence_message.choose_the_plugin_singular );
                                }

                                else {
                                    products_list.attr( 'disabled', false );
                                    $('.yith-select-plugin').text( licence_message.choose_the_plugin_plural );
                                }
                                licence_api();
                            }

                            if (typeof response.error != 'undefined') {
                                message.find('p.yith-licence-notice-message').html(response.error);
                                message_wrapper.fadeIn();
                            }
                        }
                    }
                });
            }
        });
    };

    var remove_license_expired = function ( button ){
        button.off('click').on( 'click', function ( e ) {
            e.preventDefault();

            var check = script_info.is_debug == true ? true : confirm( licence_message.are_you_sure );

            if( check ){
                var t            = $(this),
                    bubble       = $('#yith-expired-license-count'),
                    count        = bubble.data('count') * 1,
                    tr_to_remove = t.parent( 'td' ).parent( 'tr' ),
                    new_count    = count-1,
                    product_init = t.data( 'product-init' ),
                    product_id   = t.data( 'product-id' ),
                    action       = t.data( 'action' ),
                    spinner      = $( '#activated-products' ).find( '.spinner' );

                jQuery.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: {
                            action: action,
                            product_init: product_init,
                            product_id: product_id,
                            force_delete: true,
                        },
                        beforeSend: function () {
                            $.yith_license_animation( tr_to_remove, spinner, 'beforeSend' );
                        },
                        success: function ( response ) {
                            $.yith_license_animation( tr_to_remove, spinner, response.code == 999 ? 'removeLine' : 'afterSend' );
                            bubble.removeClass( 'count-' + count ).addClass( 'count-' + new_count );
                            bubble.data( 'count', new_count );
                            bubble.find( '.expired-count' ).empty().text( new_count );
                        }
                    }
                );
            }
        });
    };

    $body
        .on('focusout', 'input.user-email', function () {
            var email_field = form.find('.user-email'),
                email_val = email_field.val().trim();

            //Rewrite the value after trim
            email_field.val(email_val);

            if ('' === email_val || !is_mail(email_val)) {
                var to_replace = licence_message.email;
                message_email.text(licence_message.error.replace('%field%', to_replace));
                message_email.addClass('visible');
                email.addClass('require error');
            }
        })

        .on('focusout', 'input.licence-key', function () {
            var licence_field = form.find('.licence-key'),
                licence_key_val = licence_field.val().trim(),
                error_fields = new Array();

            //Rewrite the value after trim
            licence_field.val(licence_key_val);

            if ('' === licence_key_val || !is_license_key(licence_key_val)) {
                error_fields[error_fields.length] = licence_message.license_key;
                var to_replace = licence_message.license_key;
                message_license_key.text(licence_message.error.replace('%field%', to_replace));
                message_license_key.addClass('visible');
                licence_key.addClass('require error');
            }
        });

    var licence_api = function () {
        /**
         * Re-init global variable
         */
        var button = $('.licence-activation'),
            check = $('.licence-check'),
            deactivated = $('.licence-deactive'),
            remove_expired = $('.remove-expired-plugin');


        $body = $('body');
        $document = $(document);
        form = $('#yith-license-activation');
        message_email = form.find('.error-message.email');
        message_license_key = form.find('.error-message.license-key');
        email = form.find('.user-email');
        licence_key = form.find('.licence-key');
        products_list = $( '#yith-products-list' );
        product_count = form.data( 'count' );

        licence_activation(button);
        licence_update(check);
        licence_deactivate(deactivated);
        remove_license_expired(remove_expired);

        $body.trigger('wc-enhanced-select-init');
    };

    licence_api();

    $body.on( 'click', '.yit-changelog-button', function ( e ) {
        $( '#TB_window' ).remove();
    } );

    $body.on( 'focusin', 'input.user-email, input.licence-key', function(){
        var t = $(this);

        t.removeClass( 'require' ).removeClass( 'error' );

        if( t.hasClass( 'user-email' ) ){
            message_email.text('');
        }

        if( t.hasClass( 'licence-key' ) ){
            message_license_key.text('');
        }
    } );

    $( function(){
        var input_user_email = $body.find( 'input.user-email' ),
          input_licence_key = $body.find( 'input.licence-key' );

        if( input_user_email.val() != '' ){
          input_user_email.trigger( 'focusout' );
        }

        if( input_licence_key.val() != '' ){
          input_licence_key.trigger( 'focusout' );
        }
      } );


} )( jQuery );
