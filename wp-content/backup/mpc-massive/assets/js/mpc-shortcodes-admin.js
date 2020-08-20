

/*----------------------------------------------------------------------------*\
	BUTTON SHORTCODE - Panel
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.on( 'mpc.render', function() {
		if ( $popup.attr( 'data-vc-shortcode' ) != 'mpc_button' ) {
			return;
		}

		if ( vc.shortcodes.findWhere( { id: vc.active_panel.model.attributes.parent_id } ).get( 'shortcode' ) == 'mpc_button_set' ) {
			$popup.find( '.vc_shortcode-param[data-vc-shortcode-param-name="block"]' ).hide();
		}
	} );
} )( jQuery );





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












/*----------------------------------------------------------------------------*\
	GRID POSTS SHORTCODE - Panel
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

			if ( _value === true ) {
				$siblings.addClass( _hide_class );
				$section.addClass( _hide_class );
			} else {
				$siblings.removeClass( _hide_class );
				$section.removeClass( _hide_class );
			}
		} );
	}

	function readmore_tab_toggle() {
		var _params     = $popup.find( '[data-vc-shortcode-param-name="mpc_button__disable"]' ).data( 'param_settings' ),
			_group_name = _params.group;

		$.each( $popup.find( '[data-vc-ui-element="panel-tabs-controls"] li' ), function() {
			var $this = $( this );

			if ( $this.find( 'button' ).text() == _group_name ) {
				if ( _readmore === true ) {
					$this.addClass( _hide_class );
				} else {
					$this.removeClass( _hide_class );
				}
			}
		} );
	}

	function overlay_tab_toggle() {
		var _params     = $popup.find( '[data-vc-shortcode-param-name="overlay_section_divider"]' ).data( 'param_settings' ),
			_group_name = _params.group;

		$.each( $popup.find( '[data-vc-ui-element="panel-tabs-controls"] li' ), function() {
			var $this = $( this );

			if ( $this.find( 'button' ).text() == _group_name ) {
				if ( _overlay === true ) {
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
		var _layout               = $popup.find( '[name="layout"]' ).val(),
			_dependencies         = [ 'title_margin_divider' ],
			_overlay_dependencies = [ 'overlay_title_section_divider', 'overlay_title_margin_divider' ];

		if ( _layout == 'style_8' && _overlay_value ) {
			section_dependency( _dependencies, false );
		} else {
			section_dependency( _dependencies, true );
		}

		if ( $.inArray( _layout, [ 'style_1', 'style_4', 'style_6', 'style_7', 'style_8' ] ) > -1 && !_overlay_value ) {
			section_dependency( _overlay_dependencies, false );
		} else {
			section_dependency( _overlay_dependencies, true );
		}
	}

	function description_dependency( _overlay_value ) {
		var _layout               = $popup.find( '[name="layout"]' ).val(),
			_dependencies_base    = [ 'description_section_divider' ],
			_dependencies         = [ 'description_font_divider', 'description_padding_divider', 'description_margin_divider' ],
			_overlay_dependencies = [ 'overlay_description_section_divider', 'overlay_description_padding_divider', 'overlay_description_margin_divider' ];

		if ( $.inArray( _layout, [ 'style_1', 'style_4', 'style_7', 'style_8' ] ) > -1 || _overlay_value ) {
			section_dependency( _dependencies, true );
		} else {
			section_dependency( _dependencies, false );
		}

		if ( $.inArray( _layout, [ 'style_1', 'style_4', 'style_7', 'style_8' ] ) > -1 ) {
			section_dependency( _dependencies_base, true );
		} else {
			section_dependency( _dependencies_base, false );
		}

		if ( _layout == 'style_6' || _overlay_value ) {
			section_dependency( _overlay_dependencies, true );
		} else {
			section_dependency( _overlay_dependencies, true );
		}
	}

	function check_date_dependency() {
		// Based on layout, thumbnail for style 5, meta data enable
		var _layout             = $popup.find( '[name="layout"]' ).val(),
			_enabled            = $popup.find( '[name="meta_layout-option_date"]' ).is( ':checked' ),
			_thumbnail          = $popup.find( '[name="disable_thumbnail"]' ).is( ':checked' ),
			_disable            = true,
			_disable_at_overlay = _layout == 'style_6' && _enabled ? false : true;

		// Disable if date not selected
		if ( !_enabled ) {
			date_dependency( _disable, _disable_at_overlay );
			return false;
		}

		// Date is enabled, check if layout needs date settings
		if ( $.inArray( _layout, [ 'style_3', 'style_5', 'style_6' ] ) > -1 ) {
			// Check if layout has overlay enabled
			_disable = _thumbnail && _layout == 'style_5';
		}

		date_dependency( _disable, _disable_at_overlay );
	}

	function date_dependency( _value, _overlay_value ) {
		var _layout               = $popup.find( '[name="layout"]' ).val(),
			_dependencies         = [ 'date_font_divider', 'date_border_divider', 'date_padding_divider', 'date_margin_divider' ],
			_overlay_dependencies = [ 'overlay_date_section_divider', 'overlay_date_padding_divider', 'overlay_date_margin_divider' ];

		_overlay_value = _layout == 'style_6' ? _overlay_value : true;

		section_dependency( _dependencies, _value );

		section_dependency( _overlay_dependencies, _overlay_value );
	}

	function meta_dependency( _value ) {
		var _layout               = $popup.find( '[name="layout"]' ).val(),
			_dependencies         = [ 'meta_font_divider', 'meta_margin_divider' ],
			_overlay_dependencies = [ 'overlay_meta_section_divider', 'overlay_meta_margin_divider' ];

		section_dependency( _dependencies, _value );

		if ( $.inArray( _layout, [ 'style_1', 'style_4', 'style_6', 'style_7', 'style_8' ] ) > -1 && !_value ) {
			section_dependency( _overlay_dependencies, false );
		} else {
			section_dependency( _overlay_dependencies, true );
		}
	}

	function layout_dependency( _layout, _thumbnail ) {
		/* Trigger Thumbnail dependency */
		if ( $.inArray( _layout, [ 'style_2', 'style_3', 'style_5' ] ) > -1 ) {
			thumbnail_dependency( _thumbnail );
		} else {
			thumbnail_dependency( false );
		}

		if ( _layout == 'style_9' || ( $.inArray( _layout, [ 'style_2', 'style_3', 'style_5' ] ) > -1 && _thumbnail ) ) {
			_overlay = true;
			overlay_tab_toggle();
		} else {
			_overlay = false;
			overlay_tab_toggle( false );
		}

		/* Read More */
		if ( $.inArray( _layout, [ 'style_2', 'style_3', 'style_5', 'style_9' ] ) == -1 ) {
			_readmore = true;
			readmore_tab_toggle();
		} else {
			_readmore = false;
			readmore_tab_toggle();
		}
	}

	$popup.on( 'mpc.render', function() {
		if ( $popup.attr( 'data-vc-shortcode' ) != 'mpc_grid_posts' ) {
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
			if ( $.inArray( $layout.val(), [ 'style_2', 'style_3', 'style_5' ] ) > -1 ) {
				var _thumbnail = $thumbnail.is( ':checked' );

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
})( jQuery );

/*----------------------------------------------------------------------------*\
	HOTSPOT SHORTCODE - Panel
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_frame( $position_field, _frame, _background_id, _cache ) {

		$position_field.parent().append( _frame );

		var $frame       = $position_field.siblings( '.mpc-coords' ),
			$overlay     = $frame.find( '.mpc-coords__overlay' ),
			$point       = $frame.find( '.mpc-coords__point' ),
			_position    = $position_field.val().split( '||' ),
			_image_width = $frame.find( '.mpc-coords__image' )[ 0 ].width;

		$frame.css( 'max-width', _image_width );

		if ( _cache ) {
			$frame.attr( 'data-id', _background_id );
			$images_cache.append( $frame.clone() );
		}

		if ( _position.length == 2 ) {
			_position[ 0 ] = isNaN( parseFloat( _position[ 0 ] ) ) ? 50 : parseFloat( _position[ 0 ] );
			_position[ 1 ] = isNaN( parseFloat( _position[ 1 ] ) ) ? 50 : parseFloat( _position[ 1 ] );

			$point.css( {
				left: _position[ 0 ] + '%',
				top: _position[ 1 ] + '%'
			} );
		}

		frame_behavior( $frame, $overlay, $point, $position_field );
	}

	function frame_behavior( $frame, $overlay, $point, $position_field ) {
		var _is_dragging = false,
			_release_timer;

		$overlay.on( 'mousedown', function( event ) {
			_is_dragging = true;

			event.preventDefault();
		} ).on( 'mouseup', function() {
			_is_dragging = false;
		} ).on( 'mouseleave', function() {
			_release_timer = setTimeout( function() {
				$overlay.trigger( 'mouseup' );
			}, 500 );
		} ).on( 'mouseenter', function() {
			clearTimeout( _release_timer );
		} ).on( 'mousemove', function( event ) {
			if ( ! _is_dragging ) {
				return;
			}

			set_position( $frame, $point, $position_field, event );
		} ).on( 'click', function( event ) {
			set_position( $frame, $point, $position_field, event );
		} ).on( 'dragstart', function( event ) {
			event.preventDefault();
		} );
	}

	function set_position( $frame, $point, $position_field, event ) {
		var _offsetX = typeof event.offsetX != 'undefined' ? event.offsetX : event.originalEvent.layerX,
			_offsetY = typeof event.offsetY != 'undefined' ? event.offsetY : event.originalEvent.layerY,
			_position = {
				x: ( _offsetX / $frame.width() * 100 ).toFixed( 3 ),
				y: ( _offsetY / $frame.height() * 100 ).toFixed( 3 )
			};

		$point.css( {
			left: _position.x + '%',
			top: _position.y + '%'
		} );

		$position_field.val( _position.x + '||' + _position.y );
	}

	var $popup = $( '#vc_ui-panel-edit-element' ),
		$images_cache = $( '<div id="mpc_hotspot_images_cache" class="mpc-hotspot-images-cache" />' );

	$images_cache.appendTo( 'body' );

	$popup.on( 'mpc.render', function() {
		if ( $popup.attr( 'data-vc-shortcode' ) != 'mpc_hotspot' ) {
			return;
		}

		var $position_field = $( '.wpb_vc_param_value.position' ),
			$load_image = $( '<button class="mpc-vc-button button mpc-default">' + _mpc_lang.mpc_hotspot.set_position + '</button>' ),
			_background_id = '';

		_background_id = vc.shortcodes.findWhere( { id: vc.active_panel.model.attributes.parent_id } ).attributes.params.background_image;
		if ( typeof _background_id == 'undefined' ) {
			_background_id = '';
		}

		if ( _background_id == '' ) {
			$position_field.parent().append( '<p class="mpc-error">' + _mpc_lang.mpc_hotspot.no_background + '</p>' );
			return;
		}

		$position_field.parent().append( $load_image );

		$load_image.one( 'click', function() {
			$load_image.remove();

			if ( $images_cache.find( '.mpc-coords[data-id="' + _background_id + '"]' ).length ) {
				init_frame( $position_field, $images_cache.find( '.mpc-coords[data-id="' + _background_id + '"]' ).clone(), _background_id, false );
			} else {
				$.post( ajaxurl, {
					action: 'mpc_hotspot_get_image',
					image_id: _background_id
				}, function( response ) {
					init_frame( $position_field, response, _background_id, true );
				} );
			}
		} );
	} );
} )( jQuery );


/*----------------------------------------------------------------------------*\
 ICON LIST SHORTCODE - Panel
 \*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.on( 'mpc.render', function() {
		if( $popup.attr( 'data-vc-shortcode' ) != 'mpc_icon_list' ) {
			return '';
		}

		var $icon_type = $popup.find( '[name="mpc_icon__icon_type"]' ),
			$list_group = $popup.find( '[data-vc-shortcode-param-name="list"]' ),
			$group_toggle = $list_group.find( '.column_toggle' ),
			$group_add = $list_group.find( '.vc_param_group-add_content' ),
			$group_duplicate = $list_group.find( '.column_clone' );

		function icon_dependency( $this ) {
			var _type = $this.val();

			$list_group.find( '[name="list_icon_type"]' ).val( _type ).trigger( 'change' );
		}

		$icon_type.on( 'change', function() {
			icon_dependency( $( this ) );
		} );

		$group_add.on( 'click', function() {
			setTimeout( function(){
				icon_dependency( $icon_type );
			}, 250 );
		} );
		$group_duplicate.on( 'click', function() {
			icon_dependency( $icon_type );
		} );

		// Triggers
		setTimeout( function() {
			icon_dependency( $icon_type );
			$group_toggle.first().trigger( 'click' );
		}, 250 );
	} );
})( jQuery );

/*----------------------------------------------------------------------------*\
	ICON COLUMN SHORTCODE - Panel
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.on( 'mpc.render', function() {
		if ( $popup.attr( 'data-vc-shortcode' ) != 'mpc_icon_column' ) {
			return;
		}

		if ( vc.shortcodes.findWhere( { id: vc.active_panel.model.attributes.parent_id } ).attributes.shortcode == 'mpc_circle_icons' ) {
			$popup.find( '.vc_shortcode-param[data-vc-shortcode-param-name="layout"], .vc_shortcode-param[data-vc-shortcode-param-name="border_radius"]' ).hide( 0 );

			$popup.find( '.vc_shortcode-param[data-vc-shortcode-param-name="margin_divider"]' ).closest( '.mpc-vc-wrapper' ).hide( 0 );
		}
	} );
} )( jQuery );


/*----------------------------------------------------------------------------*\
	IHOVER ITEM SHORTCODE - Panel
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function get_styles( _mpc_shape, _mpc_effect ) {
		if ( _mpc_shape == 'circle' ) {
			switch( _mpc_effect ) {
				case 'effect1':
				case 'effect5':
				case 'effect15':
				case 'effect17':
				case 'effect19':
					return _styles[ 'style1' ];
				case 'effect2':
				case 'effect3':
				case 'effect4':
				case 'effect7':
				case 'effect8':
				case 'effect9':
				case 'effect11':
				case 'effect12':
				case 'effect14':
				case 'effect18':
					return _styles[ 'style2' ];
				case 'effect6':
					return _styles[ 'style3' ];
				case 'effect10':
				case 'effect20':
					return _styles[ 'style4' ];
				case 'effect13':
					return _styles[ 'style5' ];
				case 'effect16':
					return _styles[ 'style6' ];
				default:
					return '';
			}
		} else {
			switch( _mpc_effect ) {
				case 'effect2':
				case 'effect4':
				case 'effect7':
					return _styles[ 'style1' ];
				case 'effect9':
				case 'effect10':
				case 'effect11':
				case 'effect12':
				case 'effect13':
				case 'effect14':
				case 'effect15':
					return _styles[ 'style2' ];
				case 'effect3':
					return _styles[ 'style4' ];
				case 'effect5':
					return _styles[ 'style6' ];
				case 'effect1':
					return _styles[ 'style7' ];
				case 'effect6':
					return _styles[ 'style8' ];
				case 'effect8':
					return _styles[ 'style9' ];
				default:
					return '';
			}
		}
	}

	var _styles = {
		'style1': '.none',
		'style2': '.left_to_right, .right_to_left, .top_to_bottom, .bottom_to_top',
		'style3': '.scale_up, .scale_down, .scale_down_up',
		'style4': '.top_to_bottom, .bottom_to_top',
		'style5': '.from_left_and_right, .top_to_bottom, .bottom_to_top',
		'style6': '.left_to_right, .right_to_left',
		'style7': '.left_and_right, .top_to_bottom, .bottom_to_top',
		'style8': '.from_top_and_bottom, .from_left_and_right, .top_to_bottom, .bottom_to_top',
		'style9': '.scale_up, .scale_down'
	};

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.on( 'mpc.render', function() {
		if ( $popup.attr( 'data-vc-shortcode' ) != 'mpc_ihover' ) {
			return;
		}

		var $mpc_shape  = $popup.find( '.mpc-ihover-shape select.shape' ),
			$mpc_effect = $popup.find( '.mpc-ihover-effect select.effect' ),
			$mpc_style  = $popup.find( '.mpc-ihover-style select.style' );

		$mpc_shape.on( 'change', function() {
			if ( $mpc_shape.val() == 'circle' ) {
				$mpc_effect.children().prop( 'disabled', false );
			} else {
				$mpc_effect.children( '.effect16, .effect17, .effect18, .effect19, .effect20' ).prop( 'disabled', true );
			}

			if ( $mpc_effect.val() == null ) {
				$mpc_effect.val( $mpc_effect.children( ':not(:disabled)' ).first().attr( 'value' ) );
			}

			$mpc_effect.trigger( 'change' );
		} );
		$mpc_shape.trigger( 'change' );

		$mpc_effect.on( 'change', function() {
			$mpc_style.children().prop( 'disabled', true );

			$mpc_style.find( get_styles( $mpc_shape.val(), $mpc_effect.val() ) ).prop( 'disabled', false );

			if ( $mpc_style.val() == null ) {
				$mpc_style.val( $mpc_style.children( ':not(:disabled)' ).first().attr( 'value' ) );
			}
		} );
		$mpc_effect.trigger( 'change' );
	});

	$popup.on( 'mpc.render', function() {
		if ( $popup.attr( 'data-vc-shortcode' ) != 'mpc_ihover_item' ) {
			return;
		}

		var $mpc_style  = $popup.find( '.mpc-ihover-style select.style' ),
			_params = vc.shortcodes.findWhere( { id: vc.active_panel.model.attributes.parent_id } ).attributes.params,
			_shape = _params.shape,
			_effect = _params.effect;

		$mpc_style.children().prop( 'disabled', true );

		$mpc_style.find( get_styles( _shape, _effect ) + ', .default' ).prop( 'disabled', false );

		if ( $mpc_style.val() == null ) {
			$mpc_style.val( $mpc_style.children( ':not(:disabled)' ).first().attr( 'value' ) );
		}
	});
} )( jQuery );

/*----------------------------------------------------------------------------*\
	INTERACTIVE IMAGE SHORTCODE - Panel
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.on( 'mpc.render', function() {
		if ( $popup.attr( 'data-vc-shortcode' ) != 'mpc_interactive_image' ) {
			return;
		}

		var $divider = $( '.vc_shortcode-param[data-vc-shortcode-param-name="preview_divider"] .edit_form_line' ),
			$load_preview = $( '<button class="mpc-vc-button button mpc-default mpc-preview">' + _mpc_lang.mpc_interactive_image.preview + '</button>' ),
			$preview = $( '<div class="mpc-coords__preview" />' ),
			_hotspots = [];

		$divider.append( $load_preview );
		$load_preview.after( $preview ).after( '<br>' );

		_hotspots = vc.shortcodes.where( { parent_id: vc.active_panel.model.attributes.id } );

		$load_preview.on( 'click', function() {
			var _background_id = $popup.find( '.wpb_vc_param_value.background_image' ).val();

			$preview.html( '' );

			if ( _background_id == '' ) {
				$preview.append( '<p class="mpc-error">' + _mpc_lang.mpc_interactive_image.no_background + '</p>' );
			} else if ( _hotspots.length == 0 ) {
				$preview.append( '<p class="mpc-error">' + _mpc_lang.mpc_interactive_image.no_hotspots + '</p>' );
			} else {
				$.post( ajaxurl, {
					action: 'mpc_interactive_image_get_image',
					image_id: _background_id
				}, function( response ) {
					if ( response == 'error' ) {
						$preview.append( '<p class="mpc-error">' + _mpc_lang.mpc_interactive_image.no_background + '</p>' );
						return;
					}

					$preview
						.append( response )
						.addClass( 'mpc-loaded' );

					var _image_width = $preview.find( '.mpc-coords__image' )[ 0 ].width;

					$preview.css( 'max-width', _image_width );

					for ( var _index = 0; _index < _hotspots.length; _index++ ) {
						var $point = $( '<div class="mpc-coords__point" />' ),
							_position = _hotspots[ _index ].attributes.params.position.split( '||' );

						if ( _position.length == 2 ) {
							_position[ 0 ] = isNaN( parseFloat( _position[ 0 ] ) ) ? 50 : parseFloat( _position[ 0 ] );
							_position[ 1 ] = isNaN( parseFloat( _position[ 1 ] ) ) ? 50 : parseFloat( _position[ 1 ] );

							$point.css( {
								left: _position[ 0 ] + '%',
								top: _position[ 1 ] + '%'
							} );

							$preview.append( $point );
						}
					}
				} );
			}
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
 LIGHTBOX SHORTCODE - Panel
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";
} )( jQuery );



/*----------------------------------------------------------------------------*\
 MODAL BOX SHORTCODE - Panel
\*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	// vc.shortcodes.findWhere( { id: $popup.find( '[name="button_link"]' ).val() } )

	// If we find a way to save shortcode...
	// var $popup = $( '#vc_ui-panel-edit-element' );
	//
	// $popup.on( 'mpc.render', function() {
	// 	if ( ! $popup.is( '[data-vc-shortcode="mpc_modal"]' ) ) {
	// 		return;
	// 	}
	//
	// 	var _modal = vc.active_panel.model,
	// 		_modals = vc.shortcodes.where( { shortcode: 'mpc_modal' } ),
	// 		_id = _modal.get( 'params' )[ 'onclick_id' ];
	//
	// 	if ( _modal.get( 'cloned' ) ) {
	// 		console.log('cloned');
	// 		_modals.forEach( function( modal ) {
	// 			console.log(modal.get( 'params' )[ 'onclick_id' ], _id);
	// 			if ( modal.get( 'params' )[ 'onclick_id' ] == _id ) {
	// 				$( '.wpb_vc_param_value.onclick_id' ).val( 'modal_id_' + Date.now().toString(16) );
	// 			}
	// 		} );
	// 	}
	// } );

	//var shortcode = vc.shortcodes.findWhere( { id: 'e1d25581-9699' } );
	//shortcode.save()
	//function retrieve_buttons() {
	//	var $button_linker = $popup.find( '[name="button_link"]' ),
	//		_buttons = [],
	//		_options = '';
	//
	//	if( $button_linker.html() != '' ) {
	//		return;
	//	}
	//
	//	vc.shortcodes.models.forEach( function( _shortcode ) {
	//		if( _shortcode.attributes.shortcode == 'mpc_button' ) {
	//			var _title = '';
	//			_title += typeof _shortcode.attributes.params.title !== 'undefined' ? _shortcode.attributes.params.title + ' ' : '';
	//			_title += typeof _shortcode.attributes.params.url !== 'undefined' ? _shortcode.attributes.params.url : '';
	//			_title = _title == '' ? _shortcode.attributes.id : urldecode( _title );
	//			_buttons.push( { 'value' : _shortcode.attributes.id, 'title' : _title } );
	//		}
	//	});
	//
	//	_buttons.forEach( function( _button ) {
	//		_options += '<option value="' + _button.value + '">' + _button.title + '</option>';
	//	});
	//	$button_linker.html( _options );
	//}
	//
	//function link_button( $popup, _modal_id ) {
	//	var $frequency = $popup.find( '[name="frequency"]' );
	//
	//	if( $frequency.val() != 'onclick' ) {
	//		return false;
	//	}
	//
	//	var _button = vc.shortcodes.findWhere( { id: $popup.find( '[name="button_link"]' ).val() } );
	//
	//	_button.attributes.params.modal_id = _modal_id; console.log( _modal_id );
	//	_button.save();
	//}
	//
	//var $popup = $( '#vc_ui-panel-edit-element' );
	//
	//$popup.on( 'mpc.render', function() {
	//	if( $popup.attr( 'data-vc-shortcode' ) != 'mpc_modal' ) {
	//		return '';
	//	}
	//
	//	var $frequency = $popup.find( '[name="frequency"]' ),
	//		$modal_id  =$popup.find( '[name="modal_id"]' ),
	//		_modal_id = Math.random().toString( 36 ).substr( 2, 5 );
	//
	//	if( $frequency.val() == 'onclick' ) {
	//		retrieve_buttons();
	//		$modal_id.val( _modal_id );
	//	} else {
	//		$frequency.on( 'change', function() {
	//			if( $frequency.val() == 'onclick' ) {
	//				retrieve_buttons();
	//				$modal_id.val( _modal_id );
	//			} else {
	//				$modal_id.val( '' );
	//			}
	//		});
	//	}
	//
	//	vc.edit_element_block_view.on( 'save', function() { link_button( $popup, _modal_id ); } );
	//} );
})( jQuery );




/*----------------------------------------------------------------------------*\
	PRICING COLUMN - Panel
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.on( 'mpc.render', function() {
		if ( $popup.attr( 'data-vc-shortcode' ) != 'mpc_pricing_column' ) {
			return;
		}

		var _params         = vc.shortcodes.findWhere( { id: vc.active_panel.model.attributes.parent_id } ).attributes.params,
			_title_disable  = _params.title_disable,
			_price_disable  = _params.price_disable,
            _button_disable = _params.button_disable;

		$popup.find( '[data-vc-shortcode-param-name="title_disable"] input' ).val( _title_disable ).trigger( 'change' );
		$popup.find( '[data-vc-shortcode-param-name="price_disable"] input' ).val( _price_disable ).trigger( 'change' );
		$popup.find( '[data-vc-shortcode-param-name="button_disable"] input' ).val( _button_disable ).trigger( 'change' );
    });
} )( jQuery );







/*----------------------------------------------------------------------------*\
	SINGLE POST SHORTCODE - Panel
\*----------------------------------------------------------------------------*/
(function( $ ) {
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
		var _params     = $popup.find( '[data-vc-shortcode-param-name="overlay_section_divider"]' ).data( 'param_settings' ),
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
		var _layout               = $popup.find( '[name="layout"]' ).val(),
		    _dependencies         = [ 'title_margin_divider' ],
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
		var _layout               = $popup.find( '[name="layout"]' ).val(),
		    _dependencies_base    = [ 'description_section_divider' ],
		    _dependencies         = [ 'description_font_divider', 'description_padding_divider', 'description_margin_divider' ],
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
		var _layout             = $popup.find( '[name="layout"]' ).val(),
		    _enabled            = $popup.find( '[name="meta_layout-option_date"]' ).is( ':checked' ),
		    _thumbnail          = $popup.find( '[name="disable_thumbnail"]' ).is( ':checked' ),
		    _disable            = true,
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
		var _layout               = $popup.find( '[name="layout"]' ).val(),
		    _dependencies         = [ 'date_font_divider', 'date_border_divider', 'date_padding_divider', 'date_margin_divider' ],
		    _overlay_dependencies = [ 'overlay_date_section_divider', 'overlay_date_padding_divider', 'overlay_date_margin_divider' ];

		_overlay_value = _layout == 'style_6' ? _overlay_value : true;

		section_dependency( _dependencies, _value );

		section_dependency( _overlay_dependencies, _overlay_value );
	}

	function meta_dependency( _value ) {
		var _layout               = $popup.find( '[name="layout"]' ).val(),
		    _dependencies         = [ 'meta_font_divider', 'meta_margin_divider' ],
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

		/* Overlay ( Style 9 + Thumbnail & Overlay disable ) */
		if( _layout == 'style_9' || ( $.inArray( _layout, [ 'style_2', 'style_3', 'style_5' ] ) > -1 && _thumbnail ) ) {
			_overlay = true;
			overlay_tab_toggle();
		} else {
			_overlay = false;
			overlay_tab_toggle();
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
		if( $popup.attr( 'data-vc-shortcode' ) != 'mpc_single_post' ) {
			return '';
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
				var _thumbnail = $thumbnail.is( ':checked' );

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
})( jQuery );

/*----------------------------------------------------------------------------*\
	COLUMN SHORTCODE - Panel
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function add_separator_class() {
		vc.shortcodes
			.where( { shortcode: 'vc_column' } )
			.filter( function( shortcode ) { return shortcode.getParam( 'divider_enable' ) == 'true'; } )
			.forEach( function( shortcode ){ shortcode.view.$el.addClass( 'mpc-backend--divider' ); } );
	}

	var $popup      = $( '#vc_ui-panel-edit-element' ),
		$save_panel = $popup.find( '.vc_ui-button[data-vc-ui-element="button-save"]' );

	$( window ).on( 'load', function() {
		if ( typeof vc !== 'undefined' ) {
			add_separator_class();
		}
	} );

	$popup.on( 'mpc.render', function() {
		if ( $popup.attr( 'data-vc-shortcode' ) != 'vc_column' ) {
			return;
		}

		$save_panel.one( 'click', function() {
			add_separator_class();

			setTimeout( function() {
				add_separator_class();
			}, 2000 );
		} );
	} );
} )( jQuery );








/*----------------------------------------------------------------------------*\
	TIMELINE ITEM SHORTCODE - Panel
\*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	var $popup      = $( '#vc_ui-panel-edit-element' ),
		_hide_class = 'vc_dependent-hidden',
		_tabs       = [];

	_tabs[ 'title' ]   = 1;
	_tabs[ 'content' ] = 2;
	_tabs[ 'icon' ]    = 3;
	_tabs[ 'divider' ] = 4;

	function layout_dependency( _layout ) {
		var _parts = _layout.split( ',' ),
			$tabs = $popup.find( '[data-vc-ui-element="panel-tab-control"]:gt(0):lt(4)' );

		$tabs.addClass( _hide_class );

		_parts.forEach( function( _part ) {
			var $tab = $popup.find( '[data-vc-ui-element-target="#vc_edit-form-tab-' + _tabs[ _part ] + '"]' );

			$tab.removeClass( _hide_class );
		} );
	}

	$popup.on( 'mpc.render', function() {
		if( $popup.attr( 'data-vc-shortcode' ) != 'mpc_timeline_item' ) {
			return '';
		}

		var $layout = $popup.find( '[name="layout"]' );

		$layout.on( 'change', function() {
			layout_dependency( $layout.val() );
		} );

		// Triggers
		setTimeout( function() {
			$layout.trigger( 'change' );
		}, 350 );
	} );
})( jQuery );


/*----------------------------------------------------------------------------*\
 ADD TO CART SHORTCODE - Panel
 \*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.on( 'mpc.render', function() {
		if ( $popup.attr( 'data-vc-shortcode' ) != 'mpc_wc_add_to_cart' ) {
			return;
		}

		if ( vc.shortcodes.findWhere( { id: vc.active_panel.model.attributes.parent_id } ).get( 'shortcode' ) == 'mpc_button_set' ) {
			$popup.find( '.vc_shortcode-param[data-vc-shortcode-param-name="block"]' ).hide();
		}
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	WC PRODUCT SHORTCODE - Panel
\*----------------------------------------------------------------------------*/
// ToDo: Dependency
(function( $ ) {
	"use strict";

	var $popup      = $( '#vc_ui-panel-edit-element' ),
	    _hide_class = 'vc_dependent-hidden';

	function section_dependency( _dependencies, _value ) {
		if( typeof _dependencies === 'undefined' ) {
			return;
		}

		_dependencies.forEach( function( _el ) {
			var $section  = $popup.find( '[data-vc-shortcode-param-name="' + _el + '"]' ),
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

	function tab_dependency( _group_name, _hide ) {
		$.each( $popup.find( '[data-vc-ui-element="panel-tabs-controls"] li' ), function() {
			var $this = $( this );

			if( $this.find( 'button' ).text().indexOf( _group_name ) > -1 ) {
				if( _hide === true ) {
					$this.addClass( _hide_class );
				} else {
					$this.removeClass( _hide_class );
				}
			}
		} );
	}

	function buttons_dependency( _value ) {
		var  _dependencies = {
			    'wishlist': [ 'buttons_wcwl_icon_divider' ],
			    'lightbox': [ 'buttons_lb_icon_divider' ],
			    'url': [ 'buttons_url_icon_divider' ]
			};

		for ( var _el in _dependencies ) {
			if ( _dependencies.hasOwnProperty( _el ) ) {
				section_dependency( _dependencies[ _el ], true );
			}
		}

		_value = _value.split( ',' );

		_value.forEach( function( _el ) {
			section_dependency( _dependencies[ _el ], false );
		} );
	}

	function layout_dependency( _el_main, _el_hover, _el_thumb, _el_thumb_hover ) {
		var	_enabled_elements = [],
			_dependencies = {
				'title' : [ 'title_font_divider', 'title_margin_divider' ],
				'price' : [ 'price_font_divider', 'price_margin_divider' ],
				'categories' : [ 'tax_font_divider', 'tax_margin_divider' ],
				'rating' : [ 'rating_section_divider', 'rating_value_section_divider', 'rating_margin_divider' ],
				'atc_button' : []
			},
			_atc_tab = $popup.find( '[data-vc-shortcode-param-name="mpc_wc_add_to_cart__preset"]' ).data( 'param_settings' ).group;

		tab_dependency( _atc_tab, true );
		for ( var _el in _dependencies ) {
			if ( _dependencies.hasOwnProperty( _el ) ) {
				section_dependency( _dependencies[ _el ], true );
			}
		}

		_el_main = _el_main.split( ',' );
		_el_hover = _el_hover.split( ',' );
		_el_thumb = _el_thumb.split( ',' );
		_el_thumb_hover = _el_thumb_hover.split( ',' );

		if( _el_main.length > 0 ) {
			_el_main.forEach( function( _el ) {
				if( _el != '' && _enabled_elements.indexOf( _el ) == -1 ) {
					_enabled_elements.push( _el );
				}
			} );
		}
		if( _el_hover.length > 0 ) {
			_el_hover.forEach( function( _el ) {
				if ( _el != '' && _enabled_elements.indexOf( _el ) == -1 ) {
					_enabled_elements.push( _el );
				}
			} );
		}
		if( _el_thumb.length > 0 ) {
			_el_thumb.forEach( function( _el ) {
				if ( _el != '' && _enabled_elements.indexOf( _el ) == -1 ) {
					_enabled_elements.push( _el );
				}
			} );
		}
		if( _el_thumb_hover.length > 0 ) {
			_el_thumb_hover.forEach( function( _el ) {
				if( _el != '' && _enabled_elements.indexOf( _el ) == -1 ) {
					_enabled_elements.push( _el );
				}
			} );
		}

		_enabled_elements.forEach( function( _el ) {
			if( _el != 'atc_button' ) {
				section_dependency( _dependencies[ _el ], false );
			} else {
				tab_dependency( _atc_tab, false );
			}
		});
	}

	$popup.on( 'mpc.render', function() {
		if( $popup.attr( 'data-vc-shortcode' ) != 'mpc_wc_product' ) {
			return '';
		}

		var $el_main        = $popup.find( '[name="main_elements"]' ),
			$el_hover       = $popup.find( '[name="hover_elements"]' ),
			$el_thumb       = $popup.find( '[name="thumb_elements"]' ),
			$el_thumb_hover = $popup.find( '[name="thumb_hover_elements"]' ),
		    $buttons        = $popup.find( '[name="buttons_list"]' );

		$buttons.on( 'change', function() {
			buttons_dependency( $buttons.val() );
			layout_dependency( $el_main.val(), $el_hover.val(), $el_thumb.val(), $el_thumb_hover.val() );
		} );

		$el_main.on( 'change', function() {
			layout_dependency( $el_main.val(), $el_hover.val(), $el_thumb.val(), $el_thumb_hover.val() );
		});
		$el_hover.on( 'change', function() {
			layout_dependency( $el_main.val(), $el_hover.val(), $el_thumb.val(), $el_thumb_hover.val() );
		});
		$el_thumb.on( 'change', function() {
			layout_dependency( $el_main.val(), $el_hover.val(), $el_thumb.val(), $el_thumb_hover.val() );
		});
		$el_thumb_hover.on( 'change', function() {
			layout_dependency( $el_main.val(), $el_hover.val(), $el_thumb.val(), $el_thumb_hover.val() );
		});

		// Triggers
		setTimeout( function() {
			$buttons.trigger( 'change' );
		}, 500 );
	} );
})( jQuery );

/*----------------------------------------------------------------------------*\
 PRODUCTS CATEGORY SHORTCODE - Panel
\*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	var $popup      = $( '#vc_ui-panel-edit-element' ),
		_hide_class = 'vc_dependent-hidden';

	function layout_dependency( _layout, _state, _animation ) {
		var _parts = _layout.split( ',' ),
			_prefix = _state == 'hover' ? 'hover_' : '',
			_title_fields = [ 'title_font_preset', 'title_font_align', 'title_font_transform', 'title_overflow' ],
			_count_fields = [ 'count_font_preset', 'count_font_align', 'count_font_transform' ],
			$title_elements = $popup.find( '[data-vc-shortcode-param-name^="' + _prefix + 'title_"]' ),
			$count_elements = $popup.find( '[data-vc-shortcode-param-name^="' + _prefix + 'count_"]' );

		$title_elements.addClass( _hide_class );
		$count_elements.addClass( _hide_class );

		_parts.forEach( function( _part ) {
			if( _state == 'regular' || _animation == 'replace' ) {
				if( _part == 'title' ) {
					$title_elements.removeClass( _hide_class );
				} else if( _part == 'count' ) {
					$count_elements.removeClass( _hide_class );
				}
			} else {
				if( _part == 'title' ) {
					$title_elements.removeClass( _hide_class );

					_title_fields.forEach( function( _field ) {
						$( '[data-vc-shortcode-param-name="hover_' + _field + '"]' ).addClass( _hide_class );
					} );
				} else if( _part == 'count' ) {
					$count_elements.removeClass( _hide_class );

					_count_fields.forEach( function( _field ) {
						$( '[data-vc-shortcode-param-name="hover_' + _field + '"]' ).addClass( _hide_class );
					} );
				}
			}
		} );
	}

	$popup.on( 'mpc.render', function() {
		if( $popup.attr( 'data-vc-shortcode' ) != 'mpc_wc_category' ) {
			return '';
		}

		var $regular   = $popup.find( '[name="layout"]' ),
			$hover     = $popup.find( '[name="hover_layout"]' ),
			$animation = $popup.find( '[name="animation_type"]' );

		$animation.on( 'change', function() {
			if( $( this ).val() == 'move' ) {
				$hover.val( $regular.val() );
			}

			$hover.trigger( 'change' );
		} );

		$regular.on( 'change', function() {
			var _animation = $animation.val();

			if( _animation == 'move' ) {
				$hover.val( $regular.val() );
			}

			layout_dependency( $regular.val(), 'regular', _animation );
			layout_dependency( $hover.val(), 'hover', _animation );
		} );

		$hover.on( 'change', function() {
			var _animation = $animation.val();

			layout_dependency( $regular.val(), 'regular', _animation );
			layout_dependency( $hover.val(), 'hover', _animation );
		} );

		// Triggers
		setTimeout( function() {
			$regular.trigger( 'change' );
		}, 350 );
	} );
})( jQuery );

/*----------------------------------------------------------------------------*\
  CAROUSEL POSTS SHORTCODE - Panel
 \*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	var $popup      = $( '#vc_ui-panel-edit-element' ),
		_hide_class = 'vc_dependent-hidden';

	function layout_dependency( _layout, _state, _animation ) {
		var _parts = _layout.split( ',' ),
			_prefix = _state == 'hover' ? 'hover_' : '',
			_title_fields = [ 'title_font_preset', 'title_font_align', 'title_font_transform', 'title_overflow' ],
			_count_fields = [ 'count_font_preset', 'count_font_align', 'count_font_transform' ],
			$title_elements = $popup.find( '[data-vc-shortcode-param-name^="' + _prefix + 'title_"]' ),
			$count_elements = $popup.find( '[data-vc-shortcode-param-name^="' + _prefix + 'count_"]' );

		$title_elements.addClass( _hide_class );
		$count_elements.addClass( _hide_class );

		_parts.forEach( function( _part ) {
			if( _state == 'regular' || _animation == 'replace' ) {
				if( _part == 'title' ) {
					$title_elements.removeClass( _hide_class );
				} else if( _part == 'count' ) {
					$count_elements.removeClass( _hide_class );
				}
			} else {
				if( _part == 'title' ) {
					$title_elements.removeClass( _hide_class );

					_title_fields.forEach( function( _field ) {
						$( '[data-vc-shortcode-param-name="hover_' + _field + '"]' ).addClass( _hide_class );
					} );
				} else if( _part == 'count' ) {
					$count_elements.removeClass( _hide_class );

					_count_fields.forEach( function( _field ) {
						$( '[data-vc-shortcode-param-name="hover_' + _field + '"]' ).addClass( _hide_class );
					} );
				}
			}
		} );
	}

	$popup.on( 'mpc.render', function() {
		if( $popup.attr( 'data-vc-shortcode' ) != 'mpc_wc_carousel_categories' ) {
			return '';
		}

		var $regular   = $popup.find( '[name="layout"]' ),
			$hover     = $popup.find( '[name="hover_layout"]' ),
			$animation = $popup.find( '[name="animation_type"]' );

		$animation.on( 'change', function() {
			if( $( this ).val() == 'move' ) {
				$hover.val( $regular.val() );
			}

			$hover.trigger( 'change' );
		} );

		$regular.on( 'change', function() {
			var _animation = $animation.val();

			if( _animation == 'move' ) {
				$hover.val( $regular.val() );
			}

			layout_dependency( $regular.val(), 'regular', _animation );
			layout_dependency( $hover.val(), 'hover', _animation );
		} );

		$hover.on( 'change', function() {
			var _animation = $animation.val();

			layout_dependency( $regular.val(), 'regular', _animation );
			layout_dependency( $hover.val(), 'hover', _animation );
		} );

		// Triggers
		setTimeout( function() {
			$regular.trigger( 'change' );
		}, 350 );
	} );
})( jQuery );

/*----------------------------------------------------------------------------*\
	GRID PRODUCTS CATEGORIES SHORTCODE - Panel
\*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	var $popup      = $( '#vc_ui-panel-edit-element' ),
		_hide_class = 'vc_dependent-hidden';

	function layout_dependency( _layout, _state, _animation ) {
		var _parts = _layout.split( ',' ),
			_prefix = _state == 'hover' ? 'hover_' : '',
			_title_fields = [ 'title_font_preset', 'title_font_align', 'title_font_transform', 'title_overflow' ],
			_count_fields = [ 'count_font_preset', 'count_font_align', 'count_font_transform' ],
			$title_elements = $popup.find( '[data-vc-shortcode-param-name^="' + _prefix + 'title_"]' ),
			$count_elements = $popup.find( '[data-vc-shortcode-param-name^="' + _prefix + 'count_"]' );

		$title_elements.addClass( _hide_class );
		$count_elements.addClass( _hide_class );

		_parts.forEach( function( _part ) {
			if( _state == 'regular' || _animation == 'replace' ) {
				if( _part == 'title' ) {
					$title_elements.removeClass( _hide_class );
				} else if( _part == 'count' ) {
					$count_elements.removeClass( _hide_class );
				}
			} else {
				if( _part == 'title' ) {
					$title_elements.removeClass( _hide_class );

					_title_fields.forEach( function( _field ) {
						$( '[data-vc-shortcode-param-name="hover_' + _field + '"]' ).addClass( _hide_class );
					} );
				} else if( _part == 'count' ) {
					$count_elements.removeClass( _hide_class );

					_count_fields.forEach( function( _field ) {
						$( '[data-vc-shortcode-param-name="hover_' + _field + '"]' ).addClass( _hide_class );
					} );
				}
			}
		} );
	}

	$popup.on( 'mpc.render', function() {
		if( $popup.attr( 'data-vc-shortcode' ) != 'mpc_wc_grid_categories' ) {
			return '';
		}

		var $regular   = $popup.find( '[name="layout"]' ),
			$hover     = $popup.find( '[name="hover_layout"]' ),
			$animation = $popup.find( '[name="animation_type"]' );

		$animation.on( 'change', function() {
			if( $( this ).val() == 'move' ) {
				$hover.val( $regular.val() );
			}

			$hover.trigger( 'change' );
		} );

		$regular.on( 'change', function() {
			var _animation = $animation.val();

			if( _animation == 'move' ) {
				$hover.val( $regular.val() );
			}

			layout_dependency( $regular.val(), 'regular', _animation );
			layout_dependency( $hover.val(), 'hover', _animation );
		} );

		$hover.on( 'change', function() {
			var _animation = $animation.val();

			layout_dependency( $regular.val(), 'regular', _animation );
			layout_dependency( $hover.val(), 'hover', _animation );
		} );

		// Triggers
		setTimeout( function() {
			$regular.trigger( 'change' );
		}, 350 );
	} );
})( jQuery );

/*----------------------------------------------------------------------------*\
 WC CAROUSEL PRODUCTS SHORTCODE - Panel
\*----------------------------------------------------------------------------*/

(function( $ ) {
	"use strict";

	var $popup      = $( '#vc_ui-panel-edit-element' ),
		_hide_class = 'vc_dependent-hidden';

	function section_dependency( _dependencies, _value ) {
		if( typeof _dependencies === 'undefined' ) {
			return;
		}

		_dependencies.forEach( function( _el ) {
			var $section  = $popup.find( '[data-vc-shortcode-param-name="' + _el + '"]' ),
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

	function buttons_dependency( _value ) {
		var  _dependencies = {
			'wishlist': [ 'buttons_wcwl_icon_divider' ],
			'lightbox': [ 'buttons_lb_icon_divider' ],
			'url': [ 'buttons_url_icon_divider' ]
		};

		for ( var _el in _dependencies ) {
			if ( _dependencies.hasOwnProperty( _el ) ) {
				section_dependency( _dependencies[ _el ], true );
			}
		}

		_value = _value.split( ',' );

		_value.forEach( function( _el ) {
			section_dependency( _dependencies[ _el ], false );
		} );
	}

	function tab_dependency( _group_name, _hide ) {
		$.each( $popup.find( '[data-vc-ui-element="panel-tabs-controls"] li' ), function() {
			var $this = $( this );

			if( $this.find( 'button' ).text().indexOf( _group_name ) > -1 ) {
				if( _hide === true ) {
					$this.addClass( _hide_class );
				} else {
					$this.removeClass( _hide_class );
				}
			}
		} );
	}

	function layout_dependency( _el_main, _el_hover, _el_thumb, _el_thumb_hover ) {
		var	_enabled_elements = [],
			   _dependencies = {
				   'title' : [ 'title_font_divider', 'title_margin_divider' ],
				   'price' : [ 'price_font_divider', 'price_margin_divider' ],
				   'categories' : [ 'tax_font_divider', 'tax_margin_divider' ],
				   'rating' : [ 'rating_section_divider', 'rating_value_section_divider', 'rating_margin_divider' ],
				   'atc_button' : []
			   },
			   _atc_tab = $popup.find( '[data-vc-shortcode-param-name="mpc_wc_add_to_cart__preset"]' ).data( 'param_settings' ).group;

		tab_dependency( _atc_tab, true );
		for ( var _el in _dependencies ) {
			if ( _dependencies.hasOwnProperty( _el ) ) {
				section_dependency( _dependencies[ _el ], true );
			}
		}

		_el_main = _el_main.split( ',' );
		_el_hover = _el_hover.split( ',' );
		_el_thumb = _el_thumb.split( ',' );
		_el_thumb_hover = _el_thumb_hover.split( ',' );

		if( _el_main.length > 0 ) {
			_el_main.forEach( function( _el ) {
				if( _el != '' && _enabled_elements.indexOf( _el ) == -1 ) {
					_enabled_elements.push( _el );
				}
			} );
		}
		if( _el_hover.length > 0 ) {
			_el_hover.forEach( function( _el ) {
				if ( _el != '' && _enabled_elements.indexOf( _el ) == -1 ) {
					_enabled_elements.push( _el );
				}
			} );
		}
		if( _el_thumb.length > 0 ) {
			_el_thumb.forEach( function( _el ) {
				if ( _el != '' && _enabled_elements.indexOf( _el ) == -1 ) {
					_enabled_elements.push( _el );
				}
			} );
		}
		if( _el_thumb_hover.length > 0 ) {
			_el_thumb_hover.forEach( function( _el ) {
				if( _el != '' && _enabled_elements.indexOf( _el ) == -1 ) {
					_enabled_elements.push( _el );
				}
			} );
		}

		_enabled_elements.forEach( function( _el ) {
			if( _el != 'atc_button' ) {
				section_dependency( _dependencies[ _el ], false );
			} else {
				tab_dependency( _atc_tab, false );
			}
		});
	}

	$popup.on( 'mpc.render', function() {
		if( $popup.attr( 'data-vc-shortcode' ) != 'mpc_wc_carousel_products' ) {
			return '';
		}

		var $el_main        = $popup.find( '[name="main_elements"]' ),
			$el_hover       = $popup.find( '[name="hover_elements"]' ),
			$el_thumb       = $popup.find( '[name="thumb_elements"]' ),
			$el_thumb_hover = $popup.find( '[name="thumb_hover_elements"]' ),
			$buttons        = $popup.find( '[name="buttons_list"]' );

		$buttons.on( 'change', function() {
			buttons_dependency( $buttons.val() );
			layout_dependency( $el_main.val(), $el_hover.val(), $el_thumb.val(), $el_thumb_hover.val() );
		} );

		$el_main.on( 'change', function() {
			layout_dependency( $el_main.val(), $el_hover.val(), $el_thumb.val(), $el_thumb_hover.val() );
		});
		$el_hover.on( 'change', function() {
			layout_dependency( $el_main.val(), $el_hover.val(), $el_thumb.val(), $el_thumb_hover.val() );
		});
		$el_thumb.on( 'change', function() {
			layout_dependency( $el_main.val(), $el_hover.val(), $el_thumb.val(), $el_thumb_hover.val() );
		});
		$el_thumb_hover.on( 'change', function() {
			layout_dependency( $el_main.val(), $el_hover.val(), $el_thumb.val(), $el_thumb_hover.val() );
		});

		// Triggers
		setTimeout( function() {
			$buttons.trigger( 'change' );
		}, 500 );
	} );
})( jQuery );

/*----------------------------------------------------------------------------*\
 WC GRID PRODUCTS SHORTCODE - Panel
 \*----------------------------------------------------------------------------*/

(function( $ ) {
	"use strict";

	var $popup      = $( '#vc_ui-panel-edit-element' ),
		_hide_class = 'vc_dependent-hidden';

	function section_dependency( _dependencies, _value ) {
		if( typeof _dependencies === 'undefined' ) {
			return;
		}

		_dependencies.forEach( function( _el ) {
			var $section  = $popup.find( '[data-vc-shortcode-param-name="' + _el + '"]' ),
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

	function buttons_dependency( _value ) {
		var  _dependencies = {
			'wishlist': [ 'buttons_wcwl_icon_divider' ],
			'lightbox': [ 'buttons_lb_icon_divider' ],
			'url': [ 'buttons_url_icon_divider' ]
		};

		for ( var _el in _dependencies ) {
			if ( _dependencies.hasOwnProperty( _el ) ) {
				section_dependency( _dependencies[ _el ], true );
			}
		}

		_value = _value.split( ',' );

		_value.forEach( function( _el ) {
			section_dependency( _dependencies[ _el ], false );
		} );
	}

	function tab_dependency( _group_name, _hide ) {
		$.each( $popup.find( '[data-vc-ui-element="panel-tabs-controls"] li' ), function() {
			var $this = $( this );

			if( $this.find( 'button' ).text().indexOf( _group_name ) > -1 ) {
				if( _hide === true ) {
					$this.addClass( _hide_class );
				} else {
					$this.removeClass( _hide_class );
				}
			}
		} );
	}

	function layout_dependency( _el_main, _el_hover, _el_thumb, _el_thumb_hover ) {
		var	_enabled_elements = [],
			   _dependencies = {
				   'title' : [ 'title_font_divider', 'title_margin_divider' ],
				   'price' : [ 'price_font_divider', 'price_margin_divider' ],
				   'categories' : [ 'tax_font_divider', 'tax_margin_divider' ],
				   'rating' : [ 'rating_section_divider', 'rating_value_section_divider', 'rating_margin_divider' ],
				   'atc_button' : []
			   },
			   _atc_tab = $popup.find( '[data-vc-shortcode-param-name="mpc_wc_add_to_cart__preset"]' ).data( 'param_settings' ).group;

		tab_dependency( _atc_tab, true );
		for ( var _el in _dependencies ) {
			if ( _dependencies.hasOwnProperty( _el ) ) {
				section_dependency( _dependencies[ _el ], true );
			}
		}

		_el_main = _el_main.split( ',' );
		_el_hover = _el_hover.split( ',' );
		_el_thumb = _el_thumb.split( ',' );
		_el_thumb_hover = _el_thumb_hover.split( ',' );

		if( _el_main.length > 0 ) {
			_el_main.forEach( function( _el ) {
				if( _el != '' && _enabled_elements.indexOf( _el ) == -1 ) {
					_enabled_elements.push( _el );
				}
			} );
		}
		if( _el_hover.length > 0 ) {
			_el_hover.forEach( function( _el ) {
				if ( _el != '' && _enabled_elements.indexOf( _el ) == -1 ) {
					_enabled_elements.push( _el );
				}
			} );
		}
		if( _el_thumb.length > 0 ) {
			_el_thumb.forEach( function( _el ) {
				if ( _el != '' && _enabled_elements.indexOf( _el ) == -1 ) {
					_enabled_elements.push( _el );
				}
			} );
		}
		if( _el_thumb_hover.length > 0 ) {
			_el_thumb_hover.forEach( function( _el ) {
				if( _el != '' && _enabled_elements.indexOf( _el ) == -1 ) {
					_enabled_elements.push( _el );
				}
			} );
		}

		_enabled_elements.forEach( function( _el ) {
			if( _el != 'atc_button' ) {
				section_dependency( _dependencies[ _el ], false );
			} else {
				tab_dependency( _atc_tab, false );
			}
		});
	}

	$popup.on( 'mpc.render', function() {
		if( $popup.attr( 'data-vc-shortcode' ) != 'mpc_wc_grid_products' ) {
			return '';
		}

		var $el_main        = $popup.find( '[name="main_elements"]' ),
			$el_hover       = $popup.find( '[name="hover_elements"]' ),
			$el_thumb       = $popup.find( '[name="thumb_elements"]' ),
			$el_thumb_hover = $popup.find( '[name="thumb_hover_elements"]' ),
			$buttons        = $popup.find( '[name="buttons_list"]' );

		$buttons.on( 'change', function() {
			buttons_dependency( $buttons.val() );
			layout_dependency( $el_main.val(), $el_hover.val(), $el_thumb.val(), $el_thumb_hover.val() );
		} );

		$el_main.on( 'change', function() {
			layout_dependency( $el_main.val(), $el_hover.val(), $el_thumb.val(), $el_thumb_hover.val() );
		});
		$el_hover.on( 'change', function() {
			layout_dependency( $el_main.val(), $el_hover.val(), $el_thumb.val(), $el_thumb_hover.val() );
		});
		$el_thumb.on( 'change', function() {
			layout_dependency( $el_main.val(), $el_hover.val(), $el_thumb.val(), $el_thumb_hover.val() );
		});
		$el_thumb_hover.on( 'change', function() {
			layout_dependency( $el_main.val(), $el_hover.val(), $el_thumb.val(), $el_thumb_hover.val() );
		});

		// Triggers
		setTimeout( function() {
			$buttons.trigger( 'change' );
		}, 500 );
	} );
})( jQuery );
