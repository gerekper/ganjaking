jQuery( function ( $ ) {
    var type                         = $( '#yith-wcbm-badge-type' ).data( 'type' ),
        advanced_bg_color            = $( '#yith-wcbm-advanced-bg-color' ),
        advanced_text_color          = $( '#yith-wcbm-advanced-text-color' ),
        preview_style_badge          = $( '#preview-style-badge' ),
        advanced_badge               = $( '#yith-wcbm-advanced-badge' ),
        css_badge                    = $( '#yith-wcbm-css-badge' ),
        css_bg_color                 = $( '#yith-wcbm-css-bg-color' ),
        css_text_color               = $( '#yith-wcbm-css-text-color' ),
        css_text                     = $( '#yith-wcbm-css-text' ),
        first_open                   = 1,
        advanced_no_change_color     = 0,
        css_no_change_color          = 0,
        preview_bg                   = $( "#preview-bg" ),
        preview_bg_top               = preview_bg.offset().top - 60,
        input_type                   = $( "#yith-wcbm-badge-type" ),
        input_image_url              = $( "#yith-wcbm-image-url" ),
        btn_text                     = $( '#btn-text' ),
        btn_css                      = $( '#btn-css' ),
        btn_image                    = $( '#btn-image' ),
        btn_advanced                 = $( '#btn-advanced' ),
        button_select_image          = $( ".button-select-image" ),
        button_select_advanced       = $( ".button-select-advanced" ),
        button_select_css            = $( ".button-select-css" ),
        preview_badge                = $( '#preview-badge' ),
        anchor_point                 = $( "#yith-wcbm-position" ),
        half_left                    = $( ".half-left" ),
        half_right                   = $( ".half-right" ),
        tab_container                = $( ".tab-container" ),
        input_txt_color              = $( "#yith-wcbm-txt-color" ),
        input_text                   = $( "#yith-wcbm-text" ),
        input_bg_color               = $( "#yith-wcbm-bg-color" ),
        input_width                  = $( "#yith-wcbm-width" ),
        input_height                 = $( "#yith-wcbm-height" ),
        input_border_tl              = $( "#yith-wcbm-border-top-left-radius" ),
        input_border_tr              = $( "#yith-wcbm-border-top-right-radius" ),
        input_border_br              = $( "#yith-wcbm-border-bottom-right-radius" ),
        input_border_bl              = $( "#yith-wcbm-border-bottom-left-radius" ),
        input_padding_t              = $( "#yith-wcbm-padding-top" ),
        input_padding_l              = $( "#yith-wcbm-padding-left" ),
        input_padding_b              = $( "#yith-wcbm-padding-bottom" ),
        input_padding_r              = $( "#yith-wcbm-padding-right" ),
        input_font_size              = $( "#yith-wcbm-font-size" ),
        input_line_height            = $( "#yith-wcbm-line-height" ),
        input_opacity                = $( "#yith-wcbm-opacity" ),
        input_rotation_x             = $( "#yith-wcbm-rotation-x" ),
        input_rotation_y             = $( "#yith-wcbm-rotation-y" ),
        input_rotation_z             = $( "#yith-wcbm-rotation-z" ),
        pos_top                      = $( "#yith-wcbm-pos-top" ),
        pos_bottom                   = $( "#yith-wcbm-pos-bottom" ),
        pos_left                     = $( "#yith-wcbm-pos-left" ),
        pos_right                    = $( "#yith-wcbm-pos-right" ),
        pos_top_center               = $( '#yith-wcbm-pos-top-center' ),
        pos_bottom_center            = $( '#yith-wcbm-pos-bottom-center' ),
        pos_left_center              = $( '#yith-wcbm-pos-left-center' ),
        pos_right_center             = $( '#yith-wcbm-pos-right-center' ),
        pos_center                   = $( '#yith-wcbm-pos-center' ),
        flip_text_horizontally       = $( '#yith-wcbm-flip-text-horizontally' ),
        flip_text_vertically         = $( '#yith-wcbm-flip-text-vertically' ),
        output_opacity               = $( "#output-opacity" ),
        preview_badge                = $( '#preview-badge' ),
        custom_image_badge           = $( '#custom-image-badges' ),
        advanced_options             = $( '#yith-wcbm-advanced-options' ),
        advanced_message_not_conf    = $( '#yith-wcbm-advanced-message-not-config' ),
        preview_loader               = $( '#preview-loader' ),
        ad_colors                    = $( '.yith-wcbm-advanced-colors' ),
        ad_display                   = $( '#yith-wcbm-advanced-display' ),
        ad_list_configurable_colors  = new Array( 1, 1, 1, 1, 0, 1, 1, 1, 1, 0 ),
        ad_list_configurable_display = new Array( 0, 1, 1, 1, 1, 0, 1, 0, 1, 1 ),
        ad_list_bg_color_def         = new Array( '#66B909', '#FF6621', '#FF6621', '#00DDBF', '', '#A527F0', '#7428F9', '#4090FF', '#FF6621', '' ),
        ad_list_txt_color_def        = new Array( '#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF', '', '#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF', '' ),
        ad_list                      = new Array( ad_list_configurable_colors, ad_list_bg_color_def, ad_list_txt_color_def, ad_list_configurable_display ),
        css_list_text                = new Array( 'NEW!', 'BEST', 'ON SALE', 'New!', 'SALE!', 'SALE!', 'SALE!', 'NEW' ),
        css_list_bg_color_def        = new Array( '#3986C6', '#4AC393', '#FFFFFF', '#F78E35', '#45D0EB', '#F66600', '#F66600', '#3E93FF' ),
        css_list_txt_color_def       = new Array( '#FFFFFF', '#FFFFFF', '#61C300', '#FFFFFF', '#FFFFFF', '#000000', '#FFFFFF', '#FFFFFF' ),
        css_list                     = new Array( css_list_text, css_list_bg_color_def, css_list_txt_color_def ),
        correct_height               = function () {
            half_left.removeAttr( "style" );
            half_right.removeAttr( "style" );
            if ( half_right.height() > half_left.height() ) {
                half_left.height( half_right.height() );
            } else {
                half_right.height( half_left.height() );
            }
        },
        my_timeout,
        positioning_render           = function () {
            var p_top    = ( $.isNumeric( pos_top.val() ) ) ? pos_top.val() + 'px' : pos_top.val(),
                p_bottom = ( $.isNumeric( pos_bottom.val() ) ) ? pos_bottom.val() + 'px' : pos_bottom.val(),
                p_left   = ( $.isNumeric( pos_left.val() ) ) ? pos_left.val() + 'px' : pos_left.val(),
                p_right  = ( $.isNumeric( pos_right.val() ) ) ? pos_right.val() + 'px' : pos_right.val();

            if ( p_top.substr( 0, 8 ) == 'calc(50%' ) {
                p_top = 'calc(50% - ' + preview_badge.outerHeight() / 2 + 'px)';
                pos_top.val( p_top );
            }

            if ( p_left.substr( 0, 8 ) == 'calc(50%' ) {
                p_left = 'calc(50% - ' + preview_badge.outerWidth() / 2 + 'px)';
                pos_left.val( p_left );
            }
            preview_badge.css( { 'top': p_top, 'bottom': p_bottom, 'left': p_left, 'right': p_right, 'opacity': input_opacity.val() / 100 } );

            var rotation = 'rotateX(' + parseInt( input_rotation_x.val() ) + 'deg) ' + 'rotateY(' + parseInt( input_rotation_y.val() ) + 'deg) ' + 'rotateZ(' + parseInt( input_rotation_z.val() ) + 'deg)';
            preview_badge.css( { 'transform': rotation, '-ms-transform': rotation, '-webkit-transform': rotation } );

            var flip_text = '';
            flip_text += flip_text_horizontally.is( ':checked' ) ? ' scaleX(-1)' : '';
            flip_text += flip_text_vertically.is( ':checked' ) ? ' scaleY(-1)' : '';

            preview_badge.find( '.yith-wcbm-badge-text' ).css( { 'transform': flip_text, '-ms-transform': flip_text, '-webkit-transform': flip_text } );
        },
        preview_render               = function () {
            switch ( type ) {
                case 'image':
                    preview_badge.removeAttr( "style" );
                    preview_style_badge.html( '' );
                    preview_badge.html( '<img src="' + input_image_url.val() + '" />' );
                    positioning_render();
                    break;
                case 'css':
                    preview_loader.fadeIn( 400 );
                    preview_badge.removeAttr( "style" );
                    preview_badge.removeClass( 'yith-wcbm-on-sale-badge-advanced' );
                    preview_badge.addClass( 'yith-wcbm-css-badge' );
                    preview_badge.html( '<div class="yith-wcbm-css-s1"></div><div class="yith-wcbm-css-s2"></div><div class="yith-wcbm-css-text"><div class="yith-wcbm-badge-text">' + css_text.val() + '</div></div>' );
                    positioning_render();
                    // my_timeout prevents too many ajax requests
                    window.clearTimeout( my_timeout );
                    my_timeout = setTimeout( function () {
                        $.ajax( {
                                    type   : "POST",
                                    data   : {
                                        id_badge_style: css_badge.val(),
                                        color         : css_bg_color.val().slice( 1 ),
                                        text_color    : css_text_color.val().slice( 1 ),
                                        action        : 'yith_get_css_badge_style'
                                    },
                                    url    : ajaxurl,
                                    success: function ( data ) {
                                        preview_style_badge.html( $( "<style></style>" ).html( data ) );
                                        preview_loader.fadeOut( 200 );
                                        preview_badge.height( preview_badge.height() ).width( preview_badge.width() + 1 );
                                        positioning_render();
                                    }
                                } );
                    }, 500 );
                    break;
                case 'advanced':
                    preview_loader.fadeIn( 400 );
                    preview_badge.removeAttr( "style" );
                    preview_badge.removeClass( 'yith-wcbm-css-badge' );
                    preview_badge.addClass( 'yith-wcbm-on-sale-badge-advanced' );
                    preview_badge.html( '<div class="yith-wcbm-shape1"></div><div class="yith-wcbm-shape2"></div><div class="yith-wcbm-badge-text-advanced"><div class="yith-wcbm-simbol-sale">On Sale</div><div class="yith-wcbm yith-wcbm-simbol-sale-exclamation">Sale!</div><div class="yith-wcbm-simbol-percent">%</div><div class="yith-wcbm-simbol-off">OFF</div><div class="yith-wcbm-sale-percent">50</div><div class="yith-wcbm-save">Save $15</div><div class="yith-wcbm-saved-money">15$</div><div class="yith-wcbm-saved-money-value">15</div><div class="yith-wcbm-saved-money-currency">$</div></div>' );
                    positioning_render();

                    if ( first_open == 1 ) {
                        preview_badge.hide();
                        first_open = 0;
                    }
                    // my_timeout prevents too many ajax requests
                    window.clearTimeout( my_timeout );
                    my_timeout = setTimeout( function () {
                        $.ajax( {
                                    type   : "POST",
                                    data   : {
                                        id_badge_style        : advanced_badge.val(),
                                        color                 : advanced_bg_color.val().slice( 1 ),
                                        text_color            : advanced_text_color.val().slice( 1 ),
                                        flip_text_horizontally: flip_text_horizontally.is( ':checked' ) ? 1 : 0,
                                        flip_text_vertically  : flip_text_vertically.is( ':checked' ) ? 1 : 0,
                                        action                : 'yith_get_advanced_badge_style'
                                    },
                                    url    : ajaxurl,
                                    success: function ( data ) {
                                        preview_style_badge.html( $( "<style></style>" ).html( data ) );
                                        preview_badge.fadeIn( 'fast' );
                                        preview_loader.fadeOut( 200 );
                                        positioning_render();
                                    }
                                } );
                    }, 500 );
                    break;
                default:
                    // TEXT
                    preview_badge.html( '<div class="yith-wcbm-badge-text">' + input_text.val() + '</div>' );
                    preview_style_badge.html( '' );
                    positioning_render();
                    var _width  = input_width.val() != 'auto' ? input_width.val() + 'px' : 'auto';
                    var _height = input_height.val() != 'auto' ? input_height.val() + 'px' : 'auto';
                    preview_badge.css( {
                                           "color"                     : input_txt_color.val(),
                                           "background-color"          : input_bg_color.val(),
                                           "width"                     : _width,
                                           "height"                    : _height,
                                           "border-top-left-radius"    : input_border_tl.val() + "px",
                                           "border-top-right-radius"   : input_border_tr.val() + "px",
                                           "border-bottom-right-radius": input_border_br.val() + "px",
                                           "border-bottom-left-radius" : input_border_bl.val() + "px",
                                           "padding-top"               : input_padding_t.val() + "px",
                                           "padding-left"              : input_padding_l.val() + "px",
                                           "padding-bottom"            : input_padding_b.val() + "px",
                                           "padding-right"             : input_padding_r.val() + "px",
                                           "font-size"                 : input_font_size.val() + "px"
                                       } );
                    var line_height = input_line_height.val();
                    if ( line_height != -1 ) {
                        preview_badge.css( "line-height", line_height + "px" );
                    } else {
                        if ( 'auto' === _height ) {
                            preview_badge.css( "line-height", input_font_size.val() + "px" );
                        } else {
                            preview_badge.css( "line-height", _height );
                        }
                    }
            }
        },
        add_button_action            = function () {
            button_select_image.click( function () {
                var badge_image_url = $( this ).attr( 'badge_image_url' );
                input_image_url.val( badge_image_url );
                preview_render();
                button_select_image.removeClass( "yith-wcbm-select-image-btn-selected" );
                $( this ).addClass( "yith-wcbm-select-image-btn-selected" );
            } );

            //add selected css class to the selected image button
            var flag = 0;
            button_select_image.each( function () {
                if ( $( this ).attr( 'badge_image_url' ) == input_image_url.val() ) {
                    $( this ).addClass( "yith-wcbm-select-image-btn-selected" );
                    flag = 1;
                }
            } );

            if ( flag == 0 && input_image_url.val().length > 0 ) {
                var custom_image = '<div class="yith-wcbm-select-image-btn button-select-image" badge_image_url="' + input_image_url.val() + '" style="background-image:url(' + input_image_url.val() + ')"></div>';
                $( '#custom-image-badges' ).append( custom_image );
                button_select_image = $( ".button-select-image" );
                add_button_action();
            }

            if ( flag == 0 && input_image_url.val().length == 0 ) {
                button_select_image.first().trigger( 'click' );
            }

            correct_height();
        };

    advanced_message_not_conf.hide();
    preview_render();

    $( document ).on( 'change paste keyup input focus', '#yith-wcbm-rotation-x, #yith-wcbm-rotation-y, #yith-wcbm-rotation-z', function () {
        positioning_render();
    } );

    $( "input.update-preview" ).on( "change paste keyup input focus", function () {
        preview_render();
    } );
    anchor_point.on( "change", function () {
        switch ( anchor_point.val() ) {
            case 'top-left':
                pos_top.val( 0 );
                pos_bottom.val( 'auto' );
                pos_left.val( 0 );
                pos_right.val( 'auto' );
                break;
            case 'top-right':
                pos_top.val( 0 );
                pos_bottom.val( 'auto' );
                pos_left.val( 'auto' );
                pos_right.val( 0 );
                break;
            case 'bottom-left':
                pos_top.val( 'auto' );
                pos_bottom.val( 0 );
                pos_left.val( 0 );
                pos_right.val( 'auto' );
                break;
            case 'bottom-right':
                pos_top.val( 'auto' );
                pos_bottom.val( 0 );
                pos_left.val( 'auto' );
                pos_right.val( 0 );
                break;
            default:
        }
        preview_render();
    } );

    $( '.yith-wcbm-color-picker' ).wpColorPicker( {
                                                      change: preview_render
                                                  } );

    $( '.iris-palette' ).on( 'click', function () {
        setTimeout( preview_render, 1 );
    } );

    tab_container.tabs();
    switch ( type ) {
        case 'css':
            tab_container.tabs( 'option', 'active', 1 );
            break;
        case 'image':
            tab_container.tabs( 'option', 'active', 2 );
            break;
        case 'advanced':
            tab_container.tabs( 'option', 'active', 3 );
            break;
        default:
    }

    correct_height();

    $( 'input.yith-wcbm-range-input' ).on( 'input', function () {
        var output = $( this ).closest( 'div' ).find( '.yith-wcbm-range-output' );
        output.html( $( this ).val() );
    } );

    //scrolling Preview
    $( window ).scroll( function () {
        if ( $( window ).scrollTop() > preview_bg_top ) {
            var top_bg = $( window ).scrollTop() - preview_bg_top;
            preview_bg.stop().animate( {
                                           top: top_bg + 'px'
                                       }, 0 );
        } else {
            preview_bg.stop().animate( {
                                           top: '0'
                                       }, 0 );
        }
    } );

    preview_badge.draggable( {
                                 containment: ".half-right",
                                 stop       : function () {
                                     var offset = $( this ).position(),
                                         _x     = parseInt( offset.left ),
                                         _y     = parseInt( offset.top ),
                                         width  = preview_badge.outerWidth(),
                                         height = preview_badge.outerHeight();

                                     if ( anchor_point.val() == 'top-left' ) {
                                         pos_top.val( _y );
                                         pos_bottom.val( 'auto' );
                                         pos_left.val( _x );
                                         pos_right.val( 'auto' );
                                     } else if ( anchor_point.val() == 'top-right' ) {
                                         pos_top.val( _y );
                                         pos_bottom.val( 'auto' );
                                         pos_left.val( 'auto' );
                                         pos_right.val( 150 - _x - width );
                                     } else if ( anchor_point.val() == 'bottom-left' ) {
                                         pos_top.val( 'auto' );
                                         pos_bottom.val( 150 - _y - height );
                                         pos_left.val( _x );
                                         pos_right.val( 'auto' );
                                     } else if ( anchor_point.val() == 'bottom-right' ) {
                                         pos_top.val( 'auto' );
                                         pos_bottom.val( 150 - _y - height );
                                         pos_left.val( 'auto' );
                                         pos_right.val( 150 - _x - width );
                                     }
                                 },
                                 grid       : [ 1, 1 ],

                                 drag: function () {
                                     var offset = $( this ).position(),
                                         _x     = parseInt( offset.left ),
                                         _y     = parseInt( offset.top ),
                                         width  = preview_badge.outerWidth(),
                                         height = preview_badge.outerHeight();
                                     if ( anchor_point.val() == 'top-left' ) {
                                         pos_top.val( _y );
                                         pos_bottom.val( 'auto' );
                                         pos_left.val( _x );
                                         pos_right.val( 'auto' );
                                     } else if ( anchor_point.val() == 'top-right' ) {
                                         pos_top.val( _y );
                                         pos_bottom.val( 'auto' );
                                         pos_left.val( 'auto' );
                                         pos_right.val( 150 - _x - width );
                                     } else if ( anchor_point.val() == 'bottom-left' ) {
                                         pos_top.val( 'auto' );
                                         pos_bottom.val( 150 - _y - height );
                                         pos_left.val( _x );
                                         pos_right.val( 'auto' );
                                     } else if ( anchor_point.val() == 'bottom-right' ) {
                                         pos_top.val( 'auto' );
                                         pos_bottom.val( 150 - _y - height );
                                         pos_left.val( 'auto' );
                                         pos_right.val( 150 - _x - width );
                                     }

                                 }
                             } );

    btn_text.on( 'click', function () {
        input_type.val( 'text' );
        type = 'text';
        correct_height();
        preview_render();
    } );
    btn_css.on( 'click', function () {
        input_type.val( 'css' );
        type = 'css';
        correct_height();
        preview_render();
    } );
    btn_image.on( 'click', function () {
        input_type.val( 'image' );
        type = 'image';
        correct_height();
        preview_render();
    } );
    btn_advanced.on( 'click', function () {
        input_type.val( 'advanced' );
        type = 'advanced';
        correct_height();
        first_open = 0;
        preview_render();
    } );
    add_button_action();


    /*
     ADVANCED BADGE BUTTON ACTION
     */
    button_select_advanced.click( function () {
        var badge_advanced_index = $( this ).attr( 'badge_advanced_index' );
        advanced_badge.val( badge_advanced_index );
        var ad_index = parseInt( advanced_badge.val() ) - 1;
        if ( ad_list[ 0 ][ ad_index ] == 1 ) {
            //configurable
            if ( !advanced_no_change_color ) {
                advanced_bg_color.val( ad_list[ 1 ][ ad_index ] );
                advanced_text_color.val( ad_list[ 2 ][ ad_index ] );
                advanced_bg_color.trigger( 'change' );
                advanced_text_color.trigger( 'change' );
            }
            advanced_no_change_color = 0;
            ad_colors.fadeIn( 'fast' );
        } else {
            ad_colors.fadeOut( 'fast' );
        }

        // configurable display
        if ( ad_list[ 3 ][ ad_index ] == 1 ) {
            ad_display.fadeIn( 'fast' );
        } else {
            ad_display.fadeOut( 'fast' );
        }

        preview_render();

        button_select_advanced.removeClass( "yith-wcbm-select-image-btn-selected" );
        $( this ).addClass( "yith-wcbm-select-image-btn-selected" );
    } );
    //add selected css class to the selected advanced button
    button_select_advanced.each( function () {
        if ( $( this ).attr( 'badge_advanced_index' ) == advanced_badge.val() ) {
            $( this ).addClass( "yith-wcbm-select-image-btn-selected" );
            if ( advanced_bg_color.val() == '' && advanced_text_color.val() == '' ) {
                advanced_no_change_color = 0;
            } else {
                advanced_no_change_color = 1;
            }
            $( this ).trigger( 'click' );
        }
    } );
    /* ----------------------------- */


    /*
     CSS BADGE BUTTON ACTION
     */
    button_select_css.click( function () {
        var badge_css_index = $( this ).attr( 'badge_css_index' );
        css_badge.val( badge_css_index );

        var css_index = parseInt( css_badge.val() ) - 1;
        if ( !css_no_change_color ) {
            css_text.val( css_list[ 0 ][ css_index ] );
            css_bg_color.val( css_list[ 1 ][ css_index ] );
            css_text_color.val( css_list[ 2 ][ css_index ] );

            css_bg_color.trigger( 'change' );
            css_text_color.trigger( 'change' );
        }
        css_no_change_color = 0;
        preview_render();

        button_select_css.removeClass( "yith-wcbm-select-image-btn-selected" );
        $( this ).addClass( "yith-wcbm-select-image-btn-selected" );
    } );
    //add selected css class to the selected css button
    button_select_css.each( function () {
        if ( $( this ).attr( 'badge_css_index' ) == css_badge.val() ) {
            $( this ).addClass( "yith-wcbm-select-image-btn-selected" );
            if ( css_bg_color.val() == '' && css_text_color.val() == '' && css_text.val() == '' ) {
                css_no_change_color = 0;
            } else {
                css_no_change_color = 1;
            }
            $( this ).trigger( 'click' );
        }
    } );
    /* ----------------------------- */


    //upload button action
    $( '#upload-btn' ).on( 'click', function ( e ) {
        e.preventDefault();
        var image = wp.media( {
                                  title   : 'Upload Image',
                                  multiple: false
                              } ).open()
            .on( 'select', function ( e ) {
                var uploaded_image     = image.state().get( 'selection' ).first(),
                    image_url          = uploaded_image.toJSON().url,
                    image_badge_to_add = '<div class="yith-wcbm-select-image-btn button-select-image" badge_image_url="' + image_url + '" style="background-image:url(' + image_url + ')">' + '</div>';
                custom_image_badge.append( image_badge_to_add );
                button_select_image = $( ".button-select-image" );
                add_button_action();
            } );
    } );

    // Hide the "view badge" button
    $( '#view-post-btn' ).hide();

    // Center Positioning Buttons Actions
    pos_top_center.on( 'click', function () {
        pos_top.val( '0' );
        pos_bottom.val( 'auto' );
        pos_left.val( 'calc(50% - ' + preview_badge.outerWidth() / 2 + 'px)' );
        pos_right.val( 'auto' );
        preview_render();
    } );

    pos_bottom_center.on( 'click', function () {
        pos_top.val( 'auto' );
        pos_bottom.val( '0' );
        pos_left.val( 'calc(50% - ' + preview_badge.outerWidth() / 2 + 'px)' );
        pos_right.val( 'auto' );
        preview_render();
    } );

    pos_left_center.on( 'click', function () {
        pos_top.val( 'calc(50% - ' + preview_badge.outerHeight() / 2 + 'px)' );
        pos_bottom.val( 'auto' );
        pos_left.val( '0' );
        pos_right.val( 'auto' );
        preview_render();
    } );

    pos_right_center.on( 'click', function () {
        pos_top.val( 'calc(50% - ' + preview_badge.outerHeight() / 2 + 'px)' );
        pos_bottom.val( 'auto' );
        pos_left.val( 'auto' );
        pos_right.val( '0' );
        preview_render();
    } );

    pos_center.on( 'click', function () {
        pos_top.val( 'calc(50% - ' + preview_badge.outerHeight() / 2 + 'px)' );
        pos_bottom.val( 'auto' );
        pos_left.val( 'calc(50% - ' + preview_badge.outerWidth() / 2 + 'px)' );
        pos_right.val( 'auto' );
        preview_render();
    } );

    ad_display.find( 'input[type=radio]' ).on( 'change', function () {
        if ( ad_display.find( 'input[type=radio]:checked' ).val() === 'amount' ) {
            preview_badge.removeClass( 'yith-wcbm-advanced-display-percentage' );
            preview_badge.addClass( 'yith-wcbm-advanced-display-amount' );
        } else {
            preview_badge.removeClass( 'yith-wcbm-advanced-display-amount' );
            preview_badge.addClass( 'yith-wcbm-advanced-display-percentage' );
        }
    } ).trigger( 'change' );

    var $rotation_table              = $( '#yith-wcbm-3d-rotation-table' ),
        $rotation_table_range_inputs = $rotation_table.find( '.yith-wcbm-range-input' ),
        $rotation_table_range_values = $rotation_table.find( '.yith-wcbm-range-output' );

    $( '#yith-wcbm-rotation-mode-change' ).on( 'click', function ( event ) {
        var current_value = $( this ).data( 'value' );

        if ( current_value == 'slider' ) {
            $rotation_table_range_inputs.attr( 'type', 'number' );
            $rotation_table_range_values.hide();
            $( this ).data( 'value', 'number' );
            $( this ).html( yith_wcbm_language.slider );
        } else {
            $rotation_table_range_inputs.attr( 'type', 'range' );
            $rotation_table_range_values.show();
            $( this ).data( 'value', 'slider' );
            $( this ).html( yith_wcbm_language.number );

        }
    } );

} );