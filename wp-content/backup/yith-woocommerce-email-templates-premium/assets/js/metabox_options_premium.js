jQuery( function ( $ ) {
    $( '.yith-wcet-color-picker' ).wpColorPicker();

    var logo_input                    = $( '#yith-wcet-logo-url' ),
        logo_up_image                 = $( '#yith-wcet-logo-image' ),
        logo_and_del_container        = $( '#yith-wcet-logo-and-del-container' ),
        custom_logo_url               = $( '#yith-wcet-custom-logo-url' ).val(),
        remove_logo_btn               = $( '#yith-wcet-remove-logo-btn' ),
        footer_logo_input             = $( '#yith-wcet-logo-url-footer' ),
        footer_logo_up_image          = $( '#yith-wcet-logo-image-footer' ),
        footer_logo_and_del_container = $( '#yith-wcet-logo-and-del-container-footer' ),
        footer_remove_logo_btn        = $( '#yith-wcet-remove-logo-btn-footer' ),
        footer_add_himg               = $( '#yith-wcet-custom-logo-btn-footer-add-himg' ),
        remove_custom_link_btn        = $( '.yith-wcet-custom-links-remove-btn' ),
        custom_links_count            = parseInt( $( '#yith-wcet-custom-links-count' ).data( 'custom-links-count' ) ),
        custom_links_table            = $( '#yith-wcet-custom-links-table' ),
        custom_links_add_btn          = $( '#yith-wcet-custom-links-add-btn' ),
        default_custom_link           = $( '#yith-wcet-custom-link-row-default' ).html(),
        remove_btn_add_action         = function () {
            remove_custom_link_btn.on( 'click', function () {
                var cl_id = $( this ).attr( 'custom-link-index' );
                $( '#yith-wcet-custom-links-text' + cl_id ).val( '' );
                $( '#yith-wcet-custom-links-url' + cl_id ).val( '' );
                $( '#yith-wcet-custom-link-row' + cl_id ).hide();
            } );
        },
        // PREMIUM MAIL STYLE
        premium_style_1               = [ 0, 0, '#769ed2', '#e0e7f0', 'transparent', '#656565', 1, '#e0e7f0', 1 ],
        premium_style_2               = [ 2, 17, '#6dcbbb', '#65707a', '#6dcbbb', '#656565', 2, '#6dcbbb', 0 ],
        premium_style_3               = [ 2, 0, '#ee6563', '#ffffff', 'transparent', '#ffffff', 1, '#e0e7f0', 1 ],
        premium_styles                = [ premium_style_1, premium_style_2, premium_style_3 ],
        header_padding_0              = [ 36, 48, 36, 48 ],
        header_padding_1              = [ 36, 48, 36, 48 ],
        header_padding_2              = [ 25, 20, 25, 20 ],
        header_padding_3              = [ 25, 25, 25, 25 ],
        header_padding                = [ header_padding_0, header_padding_1, header_padding_2, header_padding_3 ],
        price_title_bg_color_styles   = [ '#ffffff', '#ffffff', '#444444', '#444444' ],
        price_title_bg_color          = $( '#yith-wcet-price-title-bg-color' ),
        premium_mail_style            = $( '#yith-wcet-premium-mail-style' ),
        header_position               = $( '#yith-wcet-header-position' ),
        page_width                    = $( '#yith-wcet-page-width' ),
        page_border_radius            = $( '#yith-wcet-page-border-radius' ),
        base_color                    = $( '#yith-wcet-base-color' ),
        body_color                    = $( '#yith-wcet-body-color' ),
        bg_color                      = $( '#yith-wcet-bg-color' ),
        text_color                    = $( '#yith-wcet-txt-color' ),
        header_color                  = $( '#yith-wcet-header-color' )
    footer_text_color = $( '#yith-wcet-footer-text-color' ),
        h1_size = $( '#yith-wcet-h1-size' ),
        h2_size = $( '#yith-wcet-h2-size' ),
        h3_size = $( '#yith-wcet-h3-size' ),
        body_size = $( '#yith-wcet-body-size' ),
        table_border_width = $( '#yith-wcet-table-border-width' ),
        table_border_color = $( '#yith-wcet-table-border-color' ),
        table_bg_color = $( '#yith-wcet-table-bg-color' ),
        socials_on_header = $( '#yith-wcet-socials-on-header' ),
        socials_on_footer = $( '#yith-wcet-socials-on-footer' ),
        socials_color = $( '#yith-wcet-socials-color' ),
        image_preview_template = $( '#yith-wcet-image-preview-template' );

    //upload button action
    $( '#yith-wcet-upload-btn' ).on( 'click', function ( e ) {
        e.preventDefault();
        var image = wp.media( {
                                  title   : 'Upload Image',
                                  multiple: false
                              } ).open()
                      .on( 'select', function ( e ) {
                          var uploaded_image = image.state().get( 'selection' ).first(),
                              logo_url       = uploaded_image.toJSON().url;

                          logo_input.val( logo_url );
                          logo_up_image.attr( 'src', logo_url );
                          logo_up_image.show();
                      } );
    } );

    //custom logo button action
    $( '#yith-wcet-custom-logo-btn' ).on( 'click', function ( e ) {
        logo_input.val( custom_logo_url );
        if ( custom_logo_url.length > 0 ) {
            logo_up_image.attr( 'src', custom_logo_url );
            logo_up_image.show();
        }
    } );

    logo_and_del_container.on( 'mouseover', function ( e ) {
        remove_logo_btn.show();
    } );

    logo_and_del_container.on( 'mouseout', function ( e ) {
        remove_logo_btn.hide();
    } );

    remove_logo_btn.on( 'click', function () {
        logo_input.val( '' );
        logo_up_image.attr( 'src', '' );
        logo_up_image.hide();
    } );

    /*
     UPLOAD FOOTER
     */
    $( '#yith-wcet-upload-btn-footer' ).on( 'click', function ( e ) {
        e.preventDefault();
        var image = wp.media( {
                                  title   : 'Upload Image',
                                  multiple: false
                              } ).open()
                      .on( 'select', function ( e ) {
                          var uploaded_image = image.state().get( 'selection' ).first(),
                              logo_url       = uploaded_image.toJSON().url;

                          footer_logo_input.val( logo_url );
                          footer_logo_up_image.attr( 'src', logo_url );
                          footer_logo_up_image.show();
                      } );
    } );

    //custom logo button action
    $( '#yith-wcet-custom-logo-btn-footer' ).on( 'click', function ( e ) {
        footer_logo_input.val( custom_logo_url );
        if ( custom_logo_url.length > 0 ) {
            footer_logo_up_image.attr( 'src', custom_logo_url );
            footer_logo_up_image.show();
        }
    } );

    footer_logo_and_del_container.on( 'mouseover', function ( e ) {
        footer_remove_logo_btn.show();
    } );

    footer_logo_and_del_container.on( 'mouseout', function ( e ) {
        footer_remove_logo_btn.hide();
    } );

    footer_remove_logo_btn.on( 'click', function () {
        footer_logo_input.val( '' );
        footer_logo_up_image.attr( 'src', '' );
        footer_logo_up_image.hide();
    } );

    footer_add_himg.on( 'click', function () {
        footer_logo_input.val( logo_input.val() );
        if ( logo_input.val().length > 0 ) {
            footer_logo_up_image.attr( 'src', logo_input.val() );
            footer_logo_up_image.show();
        } else {
            footer_logo_up_image.attr( 'src', '' );
            footer_logo_up_image.hide();
        }
    } );


    /*
     CUSTOM LINKS ACTIONS
     */
    remove_btn_add_action();

    custom_links_add_btn.on( 'click', function () {
        custom_links_count++;
        var cl_content = '<tr id="yith-wcet-custom-link-row' + custom_links_count + '">';
        cl_content += default_custom_link.replace( /INDEX/g, custom_links_count );
        cl_content += '</tr>';
        custom_links_table.append( cl_content );
        remove_custom_link_btn = $( '.yith-wcet-custom-links-remove-btn' );
        remove_btn_add_action();
    } );


    /*
     PREMIUM MAIL STYLE
     */
    premium_mail_style.on( 'change', function () {

        var style_id = ( $( this ).val() == 'default' ) ? 1 : parseInt( $( this ).val() ) + 1;
        var image_preview_template_url = ajax_object.assets_url + '/images/preview-emails/template-' + style_id + '.png';
        image_preview_template.attr( 'src', image_preview_template_url );

        if ( $( this ).val() != 'default' ) {
            var id = parseInt( $( this ).val() - 1 );
            header_position.prop( 'selectedIndex', premium_styles[ id ][ 0 ] );
            page_width.val( 600 );
            page_border_radius.val( premium_styles[ id ][ 1 ] );
            base_color.val( premium_styles[ id ][ 2 ] );
            base_color.trigger( 'change' );
            body_color.val( '#ffffff' );
            body_color.trigger( 'change' );
            bg_color.val( premium_styles[ id ][ 3 ] );
            bg_color.trigger( 'change' );
            text_color.val( '#656565' );
            text_color.trigger( 'change' );
            header_color.val( premium_styles[ id ][ 4 ] );
            header_color.trigger( 'change' );
            footer_text_color.val( premium_styles[ id ][ 5 ] );
            footer_text_color.trigger( 'change' );
            h1_size.val( 25 );
            h2_size.val( 14 );
            h3_size.val( 13 );
            body_size.val( 12 );
            table_border_width.val( premium_styles[ id ][ 6 ] );
            table_border_color.val( premium_styles[ id ][ 7 ] );
            table_border_color.trigger( 'change' );
            table_bg_color.val( '#ffffff' );
            table_bg_color.trigger( 'change' );
            table_bg_color.val( 'transparent' );
            socials_on_header.prop( 'checked', 'checked' );
            socials_on_footer.prop( 'checked', 'checked' );
            socials_color.prop( 'selectedIndex', premium_styles[ id ][ 8 ] );

            // Header Padding
            $( '#yith-wcet-header-padding-top' ).val( header_padding[ id + 1 ][ 0 ] );
            $( '#yith-wcet-header-padding-right' ).val( header_padding[ id + 1 ][ 1 ] );
            $( '#yith-wcet-header-padding-bottom' ).val( header_padding[ id + 1 ][ 2 ] );
            $( '#yith-wcet-header-padding-left' ).val( header_padding[ id + 1 ][ 3 ] );

            price_title_bg_color.val( price_title_bg_color_styles[ id + 1 ] );
            price_title_bg_color.trigger( 'change' );
        } else {
            // Header Padding
            $( '#yith-wcet-header-padding-top' ).val( header_padding[ 0 ][ 0 ] );
            $( '#yith-wcet-header-padding-right' ).val( header_padding[ 0 ][ 1 ] );
            $( '#yith-wcet-header-padding-bottom' ).val( header_padding[ 0 ][ 2 ] );
            $( '#yith-wcet-header-padding-left' ).val( header_padding[ 0 ][ 3 ] );

            price_title_bg_color.val( price_title_bg_color_styles[ 0 ] );
            price_title_bg_color.trigger( 'change' );
        }
    } );

    // Test Email
    var block_params = {
        message        : null,
        overlayCSS     : {
            background: '#000',
            opacity   : 0.6
        },
        ignoreIfBlocked: true
    };
    $( '.yith-wcet-test-email-wrapper' ).on( 'click', '.yith-wcet-test-email-send', function ( event ) {
        var $target     = $( event.target ),
            $send_btn   = $target.closest( '.yith-wcet-test-email-send' ),
            $wrapper    = $send_btn.closest( '.yith-wcet-test-email-wrapper' ),
            $recipient  = $wrapper.find( '.yith-wcet-test-email-recipient' ).first(),
            $message    = $wrapper.find( '.yith-wcet-test-email-message' ).first(),
            send_to     = $recipient.val(),
            template_id = $wrapper.data( 'template_id' );

        if ( send_to.length > 0 && template_id ) {
            $send_btn.block( block_params );
            var previewData = {
                preview    : 1,
                template_id: template_id,
                send_to    : send_to,
                action     : 'yith_wcet_send_test_email'
            };

            $.ajax( {
                        type    : "POST",
                        data    : previewData,
                        url     : ajaxurl,
                        success : function ( response ) {
                            if ( typeof response.error != 'undefined' ) {
                                $message.html( '<span class="error">' + response.error + '</span>' );
                            } else {
                                if ( typeof response.message != 'undefined' )
                                    $message.html( '<span class="message">' + response.message + '</span>' );
                            }

                            $message.find( 'span' ).delay( 3000 ).fadeOut();
                        },
                        complete: function () {
                            $send_btn.unblock();
                        }
                    } );
        }
    } );


    // Additional CSS
    var additional_css          = wp.codeEditor.initialize( "yith-wcet-additional-css", { type: 'text/css' } ),
        additional_css_textarea = $( '#yith-wcet-additional-css' );

    // Preview Email Template
    var serializeFormValuesJSON = function () {
        var o = {},
            a = $( '[name^="_template_meta"]:not(#yith-wcet-custom-links-textINDEX):not(#yith-wcet-custom-links-urlINDEX)' ).serializeArray();
        $.each( a, function () {
            if ( o[ this.name ] ) {
                if ( !o[ this.name ].push ) {
                    o[ this.name ] = [ o[ this.name ] ];
                }
                o[ this.name ].push( this.value || '' );
            } else {
                o[ this.name ] = this.value || '';
            }
        } );
        return o;
    };
    $( '#yith-wcet-preview-email-btn' ).on( 'click', function () {
        additional_css_textarea.val( additional_css.codemirror.getValue() ).trigger( 'change' );
        var popup_params = {
            ajax               : true,
            url                : ajaxurl,
            responsive_controls: true,
            ajax_data          : $.extend( {
                                               action                : 'yith_wcet_email_preview',
                                               yith_wcet_preview_mail: '1',
                                               template_id           : 'ajax_preview'
                                           }, serializeFormValuesJSON() )
        };
        $.fn.yith_wcet_popup( popup_params );
    } );
} );