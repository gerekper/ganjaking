jQuery( function( $ ) {

    $( document ).ready( function ( $ ) {

        // SUBMIT MESSAGE
        $( '#ywcars_form_my_account_new_message' ).on( 'submit', function ( e ) {
            e.preventDefault();
            $( 'div.ywcars_alert' ).hide(); // Hide all messages when pressing the submit button

            var $view_request = $( 'div.ywcars_view_request' );
            var $message = $( 'textarea#ywcars_new_message' );
            var request_id = $( 'input#ywcars_request_id' ).val();

            if ( $message.val() && request_id ) {

                form_data = new FormData( this );
                fields_filled = true;
                form_data.append( 'action', 'ywcars_submit_message' );
                form_data.append( 'security', localize_js_ywcars_frontend.ywcars_submit_message );
                form_data.append( 'request_id', request_id );

                var settings = {
                    type: 'POST',
                    url: localize_js_ywcars_frontend.ajax_url,
                    data: form_data,
                    success: function ( data, textStatus, jqXHR ) {
                        if ( data.success && data.data == 'ywcars_message_submitted_correctly' ) {
                            $( 'div.ywcars_success_alert' ).show().find( 'span.ywcars_alert_content' ).text( localize_js_ywcars_frontend.success_message );
                            $message.val( '' );
                        } else {
                            $( 'div.ywcars_error_alert' ).show().find( 'span.ywcars_alert_content' ).text( data.data );
                        }
                    },
                    error: function ( jqXHR, text_status, error_thrown ) {
                        $( 'div.ywcars_error_alert' ).show().find( 'span.ywcars_alert_content' ).text( error_thrown );
                    },
                    complete: function () {
                        $view_request.unblock();
                        // Update messages list
                        $( 'span.ywcars_update_messages' ).trigger( 'click' );
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                };

                $view_request.block( { message: null, overlayCSS:{ background: "#fff", opacity: .6 } } );
                $.ajax( settings );
            } else {
                $( 'div.ywcars_error_alert' ).show().find( 'span.ywcars_alert_content' ).text( localize_js_ywcars_frontend.fill_fields );
            }
        } );

        // ALERTS

        $( '.ywcars_close_alert' ).on( 'click', function () {
            $( this ).closest( 'div.ywcars_alert' ).hide();
        } );

        // UPDATE MESSAGES
        $( 'span.ywcars_update_messages' ).on( 'click', function ( e ) {
            e.preventDefault();
            var request_id = $( 'input#ywcars_request_id' ).val();
            var $ywcars_messages_history_frame = $( 'div.ywcars_messages_history_frame' );
            var $ywcars_message_history = $( 'div#ywcars_message_history' );
            if ( request_id ) {
                var data = {
                    action: 'ywcars_update_messages',
                    security: localize_js_ywcars_frontend.ywcars_update_messages,
                    request_id: request_id
                };
                $ywcars_message_history.block( { message: localize_js_ywcars_frontend.reloading, overlayCSS:{ background: "#fff", opacity: .6 } } );
                $ywcars_messages_history_frame.block( { message: null, overlayCSS:{ background: "#fff", opacity: .6 } } );
                $.post( localize_js_ywcars_frontend.ajax_url, data, function ( resp ) {
                    $ywcars_messages_history_frame.empty();
                    $ywcars_messages_history_frame.prepend( resp );
                    $ywcars_message_history.unblock();
                    $ywcars_messages_history_frame.unblock();
                } );
            }
        } );



        // SUBMIT REQUEST
        var $ywcars_button_refund = $( '.ywcars_button_refund' );

        $ywcars_button_refund.each( function ( index, button ) {
            $( button ).prettyPhoto( {
                hook: 'data-rel',
                social_tools: false,
                theme: 'pp_woocommerce',
                horizontal_padding: 20,
                opacity: 0.8,
                deeplinking: false,
                modal: true,
                keyboard_shortcuts: false,
                changepicturecallback: function() {
                    $( document.body ).trigger( 'ywcars_request_window_created' );
                }
            } );
        } );


        $( document.body ).on( 'ywcars_request_window_created', function () {
            $( document.body ).trigger( 'scroll.prettyphoto' );
            request_window_content = $( 'div.pp_content_container' );
            request_window_close_button = $( 'a.pp_close' );
            submit_button = $( '#ywcars_submit_button' );

            $( '.ywcars_close_alert' ).on( 'click', function () {
                $( this ).closest( 'div.ywcars_alert' ).hide();
            } );

            $( '#ywcars_form' ).on( 'submit', function ( e ) {
                e.preventDefault();
                $( 'div.ywcars_message' ).hide(); // Hide all messages when pressing the submit button

                form_data = new FormData( this );
                fields_filled = true;
                form_data.append( 'action', 'ywcars_submit_request' );
                form_data.append( 'security', localize_js_ywcars_frontend.ywcars_submit_request );
                whole_order = $( "input[name='ywcars_form_whole_order']" ).val();

                var settings = {
                    type: 'POST',
                    url: localize_js_ywcars_frontend.ajax_url,
                    data: form_data,
                    success: function ( data ) {
                        if ( data.success && data['data'] == 'ywcars_request_created' ) {
                            submit_button.prop( 'disabled', true );
                            if ( 'current-url' == localize_js_ywcars_frontend.redirect_url ) {
                                window.location.href = window.location.href;
                            } else {
                                window.location.href = localize_js_ywcars_frontend.redirect_url;
                            }
                        } else {
                            $( 'div.ywcars_error_alert' ).show().find( 'span.ywcars_alert_content' ).text( data.data );
                        }
                    },
                    error: function ( jqXHR, text_status, error_thrown ) {
                        $( 'div.ywcars_error_alert' ).show().find( 'span.ywcars_alert_content' ).text( error_thrown );
                    },
                    complete: function () {
                        request_window_content.unblock();
                        request_window_close_button.show();
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                };

                reason = $( "textarea[name='ywcars_form_reason']" ).val();
                fields_filled = !! reason;

                if ( ! whole_order ) {
                    qty = $( "input[name='ywcars_form_qty']" ).val();
                    fields_filled = !! ( qty && reason );
                }

                if ( fields_filled ) {
                    request_window_content.block( { message: null, overlayCSS:{ background: "#f1f1f1", opacity: .7 } } );
                    request_window_close_button.hide();
                    ////////////////////////////// FIX FOR SAFARI BROWSER ///////////////////////////////
                    // There are issues when the attachment is empty, so the solution is to delete the field from the form.
                    var attachment = form_data.get( 'ywcars_form_attachment[]' );
                    if ( attachment['name'] === "" && attachment['type'] === "" ) {
                        form_data.delete( 'ywcars_form_attachment[]' );
                    }
                    ////////////////////////////// FIX FOR SAFARI BROWSER ///////////////////////////////

                    $.ajax( settings );
                } else {
                    $( 'div.ywcars_error_alert' ).show().find( 'span.ywcars_alert_content' ).text( localize_js_ywcars_frontend.fill_fields );
                }

            } );

        } );

    } );

} );