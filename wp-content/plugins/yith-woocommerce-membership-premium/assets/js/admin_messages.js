jQuery( function ( $ ) {
    var message                           = $( '#yith-wcmbs-message-to-send' ),
        send_button                       = $( '#yith-wcmbs-send-button' ),
        message_editor                    = $( '#yith-wcmbs-send-message-editor' ),
        thread_id                         = ( message_editor.length > 0 ) ? message_editor.data( 'thread-id' ) : 0,
        user_id                           = ( message_editor.length > 0 ) ? message_editor.data( 'user-id' ) : 0,
        spinner                           = message_editor.find( '.spinner' ),
        messages_list                     = $( '#yith-wcmbs-messages-list' ),
        message_count                     = messages_list.data( 'messages-count' ),
        get_older_btn                     = $( '#yith-wcmbs-get-older-messages' ),
        get_older_spinner                 = $( '#get-older-spinner' ),
        messages_list_wrapper             = $( '#yith-wcmbs-messages' ).find( '.inside' ),
        control_if_all_messages_displayed = function () {
            var displayed_messages = messages_list.find( 'li' ).length;

            if ( displayed_messages >= message_count ) {
                get_older_btn.hide();
            }
        },
        list_go_to_bottom                 = function () {
            if ( messages_list.length ) {
                messages_list_wrapper.animate( {
                    scrollTop: messages_list.outerHeight()
                }, 1000, 'swing' );
            }
        };


    send_button.on( 'click', function () {
        if ( thread_id && message.val().length > 0 ) {
            var post_data = {
                thread_id: thread_id,
                user_id: user_id,
                message: message.val(),
                action: 'yith_wcmbs_send_message'
            };

            spinner.addClass( 'is-active' );

            $.ajax( {
                type: "POST",
                data: post_data,
                url: ajaxurl,
                success: function ( response ) {
                    messages_list.append( response );
                    message.val( '' );
                    spinner.removeClass( 'is-active' );
                    list_go_to_bottom();
                }
            } );
        }
    } );

    get_older_btn.on( 'click', function () {
        var message_number = messages_list.find( 'li' ).length;
        var post_data      = {
            thread_id: thread_id,
            offset: message_number,
            action: 'yith_wcmbs_get_older_messages'
        };

        get_older_spinner.addClass( 'is-active' );

        $.ajax( {
            type: "POST",
            data: post_data,
            url: ajaxurl,
            success: function ( response ) {
                messages_list.prepend( response );
                get_older_spinner.removeClass( 'is-active' );
                control_if_all_messages_displayed();
            }
        } );

    } );

    control_if_all_messages_displayed();
    list_go_to_bottom();
} );