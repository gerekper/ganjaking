jQuery( function ( $ ) {
    var tabs      = $( '.yith-wcmbs-tabs' ),
        accordion = $( '.yith-wcmbs-my-account-accordion' );

    tabs.tabs();

    accordion.accordion( {
        collapsible: true,
        heightStyle: 'content'
    } );


    $( '.yith-wcmbs-tooltip' ).tooltip({
        track: true,
        tooltipClass: "yith-wcmbs-tooltip-container",
        template: '<div class="yith-wcmbs-tooltip-container" role=""><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
        viewport: {
            selector: '.yith-wcmbs-plan-item',
            padding: 0
        },
        position: {
            my: "center bottom-20",
            at: "center top",
            using: function( position, feedback ) {
                $( this ).css( position );
            }
        },
        content: function() {
            var element = $( this );
            if ( element.is( "[data-locked]" ) ) {
                var title = element.attr( "title" ),
                    r = title,
                    locked_text = element.data('locked');

                if (title.length > 0 && locked_text.length > 0){
                    r += '<br / >';
                }

                r += '<strong>'+ locked_text + '</strong>';

                return r;
            }else{
                return element.attr( "title" );
            }
        }
    });

    /*
     MESSAGES
     */

    var user_id                           = my_ajax_obj.user_id,
        ajax_url                          = my_ajax_obj.ajax_url,
        message                           = $( '#yith-wcmbs-message-to-send' ),
        send_button                       = $( '#yith-wcmbs-send-button' ),
        message_editor                    = $( '#yith-wcmbs-send-message-editor' ),
        messages_list                     = $( '#yith-wcmbs-widget-messages-list' ),
        messages_list_wrapper             = $( '#yith-wcmbs-widget-messages-list-wrapper' ),
        message_count                     = messages_list.data( 'messages-count' ),
        get_older_btn                     = $( '#yith-wcmbs-get-older-messages' ),
        control_if_all_messages_displayed = function () {
            var displayed_messages = messages_list.find( 'li' ).length;

            if ( displayed_messages >= displayed_messages ) {
                //get_older_btn.hide();
                get_older_btn.addClass( 'yith-wcmbs-get-older-messages-disabled' );
            } else {
                get_older_btn.removeClass( 'yith-wcmbs-get-older-messages-disabled' );
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
        if ( user_id && message.val().length > 0 ) {
            var post_data = {
                user_id: user_id,
                message: message.val(),
                action: 'yith_wcmbs_user_send_message'
            };

            $.ajax( {
                type: "POST",
                data: post_data,
                url: ajax_url,
                success: function ( response ) {
                    messages_list.append( response );
                    message.val( '' );
                    list_go_to_bottom();
                }
            } );
        }
    } );

    get_older_btn.on( 'click', function () {
        var message_number = messages_list.find( 'li' ).length;
        var post_data      = {
            user_id: user_id,
            offset: message_number,
            action: 'yith_wcmbs_user_get_older_messages'
        };

        $.ajax( {
            type: "POST",
            data: post_data,
            url: ajax_url,
            success: function ( response ) {
                messages_list.prepend( response );
                control_if_all_messages_displayed();
            }
        } );

    } );

    control_if_all_messages_displayed();
    list_go_to_bottom();
} );
