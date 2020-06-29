jQuery( document ).ready( function( $ ){

    // define form initialization process
    $.fn.yith_wcmc_subscription_form = function() {
        $(this).off('submit').on('submit', 'form', submit_form );
    };

    // hook initialization process to yith_wcmc_subscription_form trigger
    var init_forms = function() {
            var forms = $( '.yith-wcmc-subscription-form' );

            forms.yith_wcmc_subscription_form();
        },
        submit_form = function(ev){
            var form = $(this),
                hide_after = form.data('hide'),
                data,
                xhr = null;

            if( xhr ) {
                xhr.abort();
            }

            ev.preventDefault();
            ev.stopPropagation();

            if( $(this).triggerHandler( 'yith_wcmc_subscription_form_validation' ) !== false ){
                data = form.serialize();
                data += '&action=' + yith_wcmc.actions.yith_wcmc_subscribe_action;

                xhr = $.ajax({
                    beforeSend: function () {
                        form.block({
                            message: null,
                            overlayCSS: {
                                background: '#fff',
                                opacity: 0.6
                            }
                        });
                    },
                    complete: function () {
                        form.unblock();
                    },
                    data: data,
                    dataType: 'json',
                    error: function () {

                    },
                    method: 'POST',
                    success: function (data, status, xhr) {
                        if (data.length == 0) {
                            return;
                        }

                        var status = data.status,
                            message = data.message,
                            subscription_notice_container = form.prev('.subscription-notice');

                        subscription_notice_container.fadeOut(300, function () {
                            var message_html = '';

                            if (status) {
                                message_html = '<div class="woocommerce-message">' + message + '</div>';
                            }
                            else {
                                message_html = '<div class="woocommerce-error">' + message + '</div>';
                            }

                            $('html, body').animate({
                                scrollTop: form.parent().offset().top
                            }, 1000);

                            if (status && hide_after == 'yes') {
                                form.fadeOut(300, function () {
                                    subscription_notice_container
                                        .html(message_html)
                                        .fadeIn(300);
                                });
                            }
                            else {
                                subscription_notice_container
                                    .html(message_html)
                                    .fadeIn(300);

                                if( status ){
                                    // reset input fields
                                    form.find('input').each( function(){
                                        var field = $(this);

                                        switch( field.attr('type') ){
                                            case 'submit':
                                            case 'hidden':
                                                break;
                                            case 'radio':
                                            case 'checkbox':
                                                field.removeProp( 'checked' );
                                                break;
                                            default:
                                                field.val( '' );
                                                break;
                                        }
                                    } );

                                    // reset select fields
                                    form.find('select').each( function(){
                                        var select = $(this),
                                            options = select.find('option');

                                        options.removeProp('selected');
                                    } );
                                }
                            }

                            $(document).trigger( 'yith_wcmc_form_subscription_result', [ data, hide_after ] );
                        });
                    },
                    url: yith_wcmc.ajax_url
                });
            }

            return false;
        };

    $(document)
        .on( 'yith_wcmc_subscription_form', init_forms )
        .trigger( 'yith_wcmc_subscription_form' );

} );