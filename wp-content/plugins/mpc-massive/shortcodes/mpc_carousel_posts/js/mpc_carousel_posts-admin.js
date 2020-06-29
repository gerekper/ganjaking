/*----------------------------------------------------------------------------*\
	CAROUSEL POSTS SHORTCODE - Panel
\*----------------------------------------------------------------------------*/
( function( $ ) {
    "use strict";

    var $popup      = $( '#vc_ui-panel-edit-element' ),
        _hide_class = 'vc_dependent-hidden',
        _overlay    = false,
        _readmore   = false;

    function section_dependency( _dependencies, _value ) {
        $.each( _dependencies, function() {
            var $section  = $popup.find( '[data-vc-shortcode-param-name="' + this + '"]' ),
                $siblings = $section.siblings( '.mpc-vc-indent' );

            if( _value === true ) {
                $siblings.addClass( _hide_class );
                $section.addClass( _hide_class );
            } else {
                $siblings.removeClass( _hide_class );
                $section.removeClass( _hide_class );
            }
        } );
    }

    function overlay_tab_toggle() {
        var _params = $popup.find( '[data-vc-shortcode-param-name="overlay_section_divider"]' ).data( 'param_settings' ),
            _group_name = _params.group;

        $.each( $popup.find( '[data-vc-ui-element="panel-tabs-controls"] li' ), function() {
            var $this = $( this );

            if( $this.find( 'button' ).text() == _group_name ) {
                if( _overlay === true ) {
                    $this.addClass( _hide_class );
                } else {
                    $this.removeClass( _hide_class );
                }
            }
        } );
    }

    function readmore_tab_toggle() {
        var _params     = $popup.find( '[data-vc-shortcode-param-name="mpc_button__disable"]' ).data( 'param_settings' ),
            _group_name = _params.group;

        $.each( $popup.find( '[data-vc-ui-element="panel-tabs-controls"] li' ), function() {
            var $this = $( this );

            if( $this.find( 'button' ).text() == _group_name ) {
                if( _readmore === true ) {
                    $this.addClass( _hide_class );
                } else {
                    $this.removeClass( _hide_class );
                }
            }
        } );
    }

    function thumbnail_dependency( _value ) {
        var _dependencies = [ 'items_section_divider' ];
        section_dependency( _dependencies, _value );
    }

    function title_dependency( _overlay_value ) {
        var _layout = $popup.find( '[name="layout"]' ).val(),
            _dependencies = [ 'title_margin_divider' ],
            _overlay_dependencies = [ 'overlay_title_section_divider', 'overlay_title_margin_divider' ];

        if( _layout == 'style_8' && _overlay_value ) {
            section_dependency( _dependencies, false );
        } else {
            section_dependency( _dependencies, true );
        }

        if( $.inArray( _layout, [ 'style_1', 'style_4', 'style_6', 'style_7', 'style_8' ] ) > -1 && !_overlay_value ) {
            section_dependency( _overlay_dependencies, false );
        } else {
            section_dependency( _overlay_dependencies, true );
        }
    }

    function description_dependency( _overlay_value ) {
        var _layout = $popup.find( '[name="layout"]' ).val(),
            _dependencies_base = [ 'description_section_divider' ],
            _dependencies = [ 'description_font_divider', 'description_padding_divider', 'description_margin_divider' ],
            _overlay_dependencies = [ 'overlay_description_section_divider', 'overlay_description_padding_divider', 'overlay_description_margin_divider' ];

        if( $.inArray( _layout, [ 'style_1', 'style_4', 'style_7', 'style_8' ] ) > -1 || _overlay_value ) {
            section_dependency( _dependencies, true );
        } else {
            section_dependency( _dependencies, false );
        }

        if( $.inArray( _layout, [ 'style_1', 'style_4', 'style_7', 'style_8' ] ) > -1 ) {
            section_dependency( _dependencies_base, true );
        } else {
            section_dependency( _dependencies_base, false );
        }

        if( _layout == 'style_6' || _overlay_value ) {
            section_dependency( _overlay_dependencies, true );
        } else {
            section_dependency( _overlay_dependencies, true );
        }
    }

    function check_date_dependency() {
        // Based on layout, thumbnail for style 5, meta data enable
        var _layout    = $popup.find( '[name="layout"]' ).val(),
            _enabled   = $popup.find( '[name="meta_layout-option_date"]' ).is( ':checked' ),
            _thumbnail = $popup.find( '[name="disable_thumbnail"]' ).is( ':checked' ),
            _disable   = true,
            _disable_at_overlay = _layout == 'style_6' && _enabled ? false : true;

        // Disable if date not selected
        if( !_enabled ) {
            date_dependency( _disable, _disable_at_overlay );
            return false;
        }

        // Date is enabled, check if layout needs date settings
        if( $.inArray( _layout, [ 'style_3', 'style_5', 'style_6' ] ) > -1 ) {
            // Check if layout has overlay enabled
            _disable = _thumbnail && _layout == 'style_5';
        }

        date_dependency( _disable, _disable_at_overlay );
    }

    function date_dependency( _value, _overlay_value ) {
        var _layout = $popup.find( '[name="layout"]' ).val(),
            _dependencies = [ 'date_font_divider', 'date_border_divider', 'date_padding_divider', 'date_margin_divider'],
            _overlay_dependencies = [ 'overlay_date_section_divider', 'overlay_date_padding_divider', 'overlay_date_margin_divider' ];

        _overlay_value = _layout == 'style_6' ? _overlay_value : true;

        section_dependency( _dependencies, _value );

        section_dependency( _overlay_dependencies, _overlay_value );
    }

    function meta_dependency( _value ) {
        var _layout = $popup.find( '[name="layout"]' ).val(),
            _dependencies = [ 'meta_font_divider', 'meta_margin_divider'],
            _overlay_dependencies = [ 'overlay_meta_section_divider', 'overlay_meta_margin_divider' ];

        section_dependency( _dependencies, _value );

        if( $.inArray( _layout, [ 'style_1', 'style_4', 'style_6', 'style_7', 'style_8' ] ) > -1 && !_value ) {
            section_dependency( _overlay_dependencies, false );
        } else {
            section_dependency( _overlay_dependencies, true );
        }
    }

    function layout_dependency( _layout, _thumbnail ) {
        /* Trigger Thumbnail dependency */
        if( $.inArray( _layout, [ 'style_2', 'style_3', 'style_5' ] ) > -1 ) {
            thumbnail_dependency( _thumbnail );
        } else {
            thumbnail_dependency( false );
        }

        if( _layout == 'style_9' || ( $.inArray( _layout, [ 'style_2', 'style_3', 'style_5' ] ) > -1 && _thumbnail ) ) {
            _overlay = true;
            overlay_tab_toggle();
        } else {
            _overlay = false;
            overlay_tab_toggle( false );
        }

        /* Read More */
        if( $.inArray( _layout, [ 'style_2', 'style_3', 'style_5', 'style_9' ] ) == -1 ) {
            _readmore = true;
            readmore_tab_toggle();
        } else {
            _readmore = false;
            readmore_tab_toggle();
        }
    }

    $popup.on( 'mpc.render', function() {
        if ( $popup.attr( 'data-vc-shortcode' ) != 'mpc_carousel_posts' ) {
            return;
        }

        var $layout      = $popup.find( '[name="layout"]' ),
            $metas       = $popup.find( '[name="meta_layout"]' ),
            $title       = $popup.find( '[name="title_disable"]' ),
            $description = $popup.find( '[name="description_disable"]' ),
            $thumbnail   = $popup.find( '[name="disable_thumbnail"]' );

        $layout.on( 'change', function() {
            layout_dependency( $layout.val(), $thumbnail.is( ':checked' ) );

            $metas.trigger( 'change' );
            $title.trigger( 'change' );
            $description.trigger( 'change' );

            overlay_tab_toggle();
            readmore_tab_toggle();
        } );

        $title.on( 'change', function() {
            title_dependency( $title.is( ':checked' ) );

            overlay_tab_toggle();
        } );

        $description.on( 'change', function() {
            description_dependency( $description.is( ':checked' ) );

            overlay_tab_toggle();
        } );

        $metas.on( 'change', function() {
            var _value = $metas.val() == ''; // true if empty

            meta_dependency( _value );
            check_date_dependency();

            overlay_tab_toggle();
        } );

        $thumbnail.on( 'change', function() {
            if( $.inArray( $layout.val(), [ 'style_2', 'style_3', 'style_5' ] ) > -1 ) {
                var _thumbnail = $thumbnail.is( ':checked');

                _overlay = _thumbnail;

                overlay_tab_toggle();
                thumbnail_dependency( _thumbnail );

                $metas.trigger( 'change' );
            }
        } );

        // Triggers
        setTimeout( function() {
            $layout.trigger( 'change' );
        }, 350 );
    } );
} )( jQuery );
