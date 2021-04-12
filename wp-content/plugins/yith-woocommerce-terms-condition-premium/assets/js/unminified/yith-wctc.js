jQuery( function ($) {
    var $body = $( 'body'),
        $document = $( document),
        defaults = {
            framewidth : '400px',
            frameheight: '300px',
            border     : '10px',
            bgcolor    : '#5dff5e',
            titleattr  : 'data-title',
            numeratio  : true,
            infinigall : true
        },
        lightbox_terms,
        lightbox_privacy,
        read = [],
        scrollbar,
        register_poup = function () {
            var params = $.extend(defaults, yith_wctc_params.popup_init);
            lightbox_terms = $('a.wctc-terms-and-conditions').venobox(params);
            lightbox_privacy = $('a.wctc-privacy').venobox(params);
        },
        create_popup_scrollbar = function(){
            var popup_content = $( '.yith-wctc-popup .popup-content' ),
                terms_content = popup_content.find( '.terms-content' ),
                container_height = popup_content.innerHeight(),
                content_height = terms_content.innerHeight(),
                is_scrollable = content_height > container_height * 0.95,
                agree_button = $( '.agree-button'),
                type = $('.yith-wctc-popup .popup-content').parents( "div[data-type]").data('type');

            if( ! terms_content.length ){
                return;
            }

            scrollbar = new PerfectScrollbar( '.yith-wctc-popup .popup-content', {
                suppressScrollX: true
            } );

            popup_content
                .on( 'ps-y-reach-end', function(){

                    read[type] = true;
                    agree_button.removeClass( 'disabled' );
                } );

            if( yith_wctc_params.force_to_read && is_scrollable ){
                agree_button.addClass( 'disabled' );
            }

            read[type] = ! is_scrollable;

        },
        handle_privacy_agree_button = function () {
            $('#agree_privacy_button').on('click', function (ev) {
                if( $(this).hasClass( 'disabled' ) && yith_wctc_params.force_to_read && ! read.privacy ){
                    alert( yith_wctc_params.force_to_read_message );
                }
                else {
                    var privacy_checkbox = $('#privacy_checkbox'),
                        terms_checkbox = $('#terms');
                    ev.preventDefault();

                    if (yith_wctc_params.terms == 'both' && yith_wctc_params.fields == 'together') {
                        if( ( read.privacy && read.terms ) || ! yith_wctc_params.force_to_read ){
                            terms_checkbox.prop('checked', true ).change();
                        }
                    }
                    else {
                        if( ( read.privacy ) || ! yith_wctc_params.force_to_read ) {
                            privacy_checkbox.prop('checked', true).change();
                        }
                    }

                    $('.vbox-close').trigger('click');
                }
            });
        },
        handle_terms_agree_button = function () {
            $('#agree_terms_button').on('click', function (ev) {
                if( $(this).hasClass( 'disabled' ) && yith_wctc_params.force_to_read && ! read.terms ){
                    alert( yith_wctc_params.force_to_read_message );
                }
                else {
                    var privacy_checkbox = $('#privacy_checkbox'),
                        terms_checkbox = $('#terms');
                    ev.preventDefault();

                    if (yith_wctc_params.terms == 'both' && yith_wctc_params.fields == 'together') {
                        if( ( read.privacy && read.terms ) || ! yith_wctc_params.force_to_read ){
                            terms_checkbox.prop('checked', true ).change();
                        }
                    }
                    else{
                        if( ( read.terms ) || ! yith_wctc_params.force_to_read ) {
                            terms_checkbox.prop('checked', true).change();
                        }
                    }

                    $('.vbox-close').trigger('click');
                }
            });
        },
        handle_checkbox_change = function (ev) {
            $('#terms').add('#privacy_checkbox').on('change', function(){
                var t = $(this),
                    privacy_checkbox = $('#privacy_checkbox'),
                    terms_checkbox = $('#terms');

                if( ! t.is(':checked') ){
                    return;
                }

                if( yith_wctc_params.force_to_read ){
                    if( yith_wctc_params.terms == 'both' && yith_wctc_params.fields == 'together' ){
                        if( ! read.privacy || ! read.terms ){
                            t.prop( 'checked', false ).change();
                            alert( yith_wctc_params.force_to_read_message );
                        }
                    }
                    else if( this.id == 'privacy_checkbox' ){
                        if( ! read.privacy ){
                            t.prop( 'checked', false ).change();
                            alert( yith_wctc_params.force_to_read_message );
                        }
                    }
                    else if( this.id == 'terms' ){
                        if( ! read.terms ){
                            t.prop( 'checked', false ).change();
                            alert( yith_wctc_params.force_to_read_message );
                        }
                    }
                }
            });
        };

    $document.ajaxComplete( handle_privacy_agree_button );
    $document.ajaxComplete( handle_terms_agree_button );
    $document.ajaxComplete( create_popup_scrollbar );

    $body.on('updated_checkout', register_poup);
    $body.on('updated_checkout', handle_checkbox_change);
    register_poup();
    handle_checkbox_change();
} );