( function ( api, wp, $ ) {
    'use strict';
    $( document ).ready( function () {
        function generateElementsArray( $parent ) {
            var result = [];
            $parent.children( 'span' ).each( function () {
                var $this = $( this );
                if ( $this.hasClass( 'element-cont' ) ) {
                    var subResult = generateElementsArray( $( this ) );
                    if ( Array.isArray( subResult ) && subResult.length > 0 ) {
                        result.push( subResult );
                    }
                } else {
                    var obj = {}, meta = '';
                    if ( $this.data( 'html' ) || $this.data( 'el_class' ) ) {
                        meta = {};
                    }
                    if ( $this.data( 'html' ) ) {
                        meta.html = $this.data( 'html' );
                    }
                    if ( typeof $this.data( 'el_class' ) != 'undefined' ) {
                        meta.el_class = $this.data( 'el_class' );
                    }
                    obj[ $this.data( 'id' ) ] = meta;
                    result.push( obj );
                }
            } );
            return result;
        }
        function generateElementsFromArray( itemArr, itemId, $parent, screen ) {
            if ( typeof $parent == 'string' ) {
                $parent = '.header-wrapper-' + $parent + ' [data-id="' + itemId + '"]';
            }
            $.each( itemArr, function ( index, value ) {
                if ( Array.isArray( value ) ) { // row element
                    var parentObj = $( '.porto-' + screen + '-visible span[data-id="row"]' ).clone().addClass( 'porto-drop-item' + ( 'sm' == screen ? '-mobile' : '' ) );
                    parentObj.children( 'b' ).remove();
                    parentObj.appendTo( $parent );
                    generateElementsFromArray( value, itemId, parentObj, screen );
                } else {
                    $.each( value, function ( key, html ) {
                        var $obj;
                        if ( $( '.porto-' + screen + '-visible span[data-id="' + key + '"]' ).hasClass( 'element-infinite' ) ) {
                            $obj = $( '.porto-' + screen + '-visible span[data-id="' + key + '"]' ).clone().appendTo( $parent );
                        } else {
                            $obj = $( '.porto-' + screen + '-visible span[data-id="' + key + '"]' ).appendTo( $parent );
                        }
                        if ( html ) {
                            if ( typeof html == 'string' ) {
                                $obj.data( 'html', html );
                            } else {
                                if ( html.html ) {
                                    $obj.data( 'html', html.html );
                                }
                                if ( typeof html.el_class != 'undefined' ) {
                                    $obj.data( 'el_class', html.el_class );
                                }
                            }
                        }
                    } );
                }
            } );
        }
        function setItem( item, connectWith ) {
            if ( item ) {
                var arr = generateElementsArray( $( '.' + connectWith + '[data-id="' + item + '"]' ) );
                wp.customize.instance( item ).set( JSON.stringify( arr ) );
            }
        }
        function itemSortable( $element, connectWithClass ) {
            $element.sortable( {
                connectWith: '.' + connectWithClass,
                update: function ( event, ui ) {
                    if ( $( ui.item ).hasClass( 'element-cont' ) ) {
                        $( ui.item ).contents().filter( function () {
                            return this.nodeType === 3;
                        } ).remove();
                        $( ui.item ).addClass( connectWithClass ).children( 'b' ).remove();
                        itemSortable( $( ui.item ), connectWithClass );
                    }
                    setItem( $( ui.item ).closest( 'div' ).data( 'id' ), connectWithClass );
                    if ( !$( ui.sender ).hasClass( 'element-cont' ) ) {
                        setItem( $( ui.sender ).data( 'id' ), connectWithClass );
                    }
                },
                start: function ( event, ui ) {
                    if ( $( ui.item ).hasClass( 'element-cont' ) ) {
                        var obj = $( ui.item ).closest( '.' + connectWithClass ),
                            objData = obj.data( 'ui-sortable' ),
                            objContainers = objData.containers,
                            flag = false;

                        $.each( objContainers, function ( index, cont ) {
                            if ( typeof cont != 'undefined' && $( cont.element ).is( $( ui.item ) ) ) {
                                objContainers.splice( index, 1 );
                                flag = true;
                            }
                        } );
                        if ( flag ) {
                            objData.containers = objContainers;
                            obj.data( 'ui-sortable', objData );
                        }
                    }
                    $( ui.item ).hasClass( 'element-infinite' ) && $( ui.item ).closest( '.porto-header-builder-list' ).length && $( ui.item ).clone().removeAttr( 'style' ).insertAfter( $( ui.item ) );
                },
                stop: function ( event, ui ) {
                    $( ui.item ).hasClass( 'element-infinite' ) && $( ui.item ).closest( '.porto-header-builder-list' ).length && $( ui.item ).remove();
                }
            } ).disableSelection();
        }
        function initHeaderLayout() {
            $( '.porto-drop-item' ).each( function () {
                var itemId = $( this ).data( 'id' );
                if ( itemId && wp.customize.instance( itemId ) && wp.customize.instance( itemId ).get() ) {
                    var itemArr = JSON.parse( wp.customize.instance( itemId ).get() );
                    generateElementsFromArray( itemArr, itemId, 'desktop', 'lg' );
                }
            } );
            $( '.porto-drop-item-mobile' ).each( function () {
                var itemId = $( this ).data( 'id' );
                if ( itemId && wp.customize.instance( itemId ) && wp.customize.instance( itemId ).get() ) {
                    var itemArr = JSON.parse( wp.customize.instance( itemId ).get() );
                    generateElementsFromArray( itemArr, itemId, 'mobile', 'sm' );
                }
            } );
            itemSortable( $( '.porto-drop-item' ), 'porto-drop-item' );
            itemSortable( $( '.porto-drop-item-mobile' ), 'porto-drop-item-mobile' );
        }

        function resetHeaderBuilderElements( response ) {
            $.each( wp.customize.section( 'porto_header_builder' ).controls(), function ( key, control ) {
                wp.customize.instance( control.settings.default.id ).set( '' );
            } );
            $( '.porto-header-builder .header-builder-wrapper .porto-drop-item span:not(.element-infinite)' ).insertBefore( $( '.header-wrapper-desktop .porto-header-builder-list span.element-infinite' ).first() );
            $( '.porto-header-builder .header-builder-wrapper .porto-drop-item-mobile span:not(.element-infinite)' ).insertBefore( $( '.header-wrapper-mobile .porto-header-builder-list span.element-infinite' ).first() );
            $( '.porto-header-builder .header-builder-wrapper .porto-drop-item, .porto-header-builder .header-builder-wrapper .porto-drop-item-mobile' ).html( '' );
            if ( response.elements ) {
                $.each( response.elements, function ( key, value ) {
                    value && wp.customize.instance( 'porto_header_builder_elements[' + key + ']' ).set( value );
                } );
            }
            initHeaderLayout();
            if ( response.custom_css ) {
                wp.customize.instance( 'porto_header_builder[custom_css]' ).set( response.custom_css );
            } else {
                wp.customize.instance( 'porto_header_builder[custom_css]' ).set( '' );
            }
            if ( response.type ) {
                wp.customize.instance( 'porto_header_builder[type]' ).set( response.type );
            } else {
                wp.customize.instance( 'porto_header_builder[type]' ).set( '' );
            }
            wp.customize.control( 'porto_header_layouts_type' ).container.find( 'select' ).trigger( 'change' );
            if ( response.side_header_toggle ) {
                wp.customize.instance( 'porto_header_builder[side_header_toggle]' ).set( response.side_header_toggle );
            } else {
                wp.customize.instance( 'porto_header_builder[side_header_toggle]' ).set( '' );
            }
            wp.customize.control( 'porto_header_layouts_side_header_toggle' ).container.find( 'select' ).trigger( 'change' );
            if ( response.side_header_toggle_logo ) {
                wp.customize.instance( 'porto_header_builder[side_header_toggle_logo]' ).set( response.side_header_toggle_logo );
            } else {
                wp.customize.instance( 'porto_header_builder[side_header_toggle_logo]' ).set( '' );
            }
            if ( response.side_header_toggle_desc ) {
                wp.customize.instance( 'porto_header_builder[side_header_toggle_desc]' ).set( response.side_header_toggle_desc );
            } else {
                wp.customize.instance( 'porto_header_builder[side_header_toggle_desc]' ).set( '' );
            }
            if ( response.side_header_width ) {
                wp.customize.instance( 'porto_header_builder[side_header_width]' ).set( response.side_header_width );
            } else {
                wp.customize.instance( 'porto_header_builder[side_header_width]' ).set( '' );
            }
        }

        initHeaderLayout();
        var porto_header_builder_first_load = true;
        var header_builder_section = wp.customize.instance( 'porto_header_builder[on_section]' );
        header_builder_section && header_builder_section.set( '' );
        wp.customize.section( 'porto_header_layouts' ) && wp.customize.section( 'porto_header_layouts' ).expanded.bind( function ( t ) {
            if ( !t ) {
                $( '.porto-header-builder' ).removeClass( 'active' );
                wp.customize.instance( 'porto_header_builder[on_section]' ).set( '' );
            } else if ( $( '#customize-control-porto_header_layouts_select select' ).val() ) {
                $( '.porto-header-builder' ).addClass( 'active' );
                wp.customize.instance( 'porto_header_builder[on_section]' ).set( '1' );
                if ( porto_header_builder_first_load && ( typeof wp.customize.instance( 'porto_settings[header-type-select]' ) == 'undefined' || wp.customize.instance( 'porto_settings[header-type-select]' ).get() != 'header_builder' ) ) {
                    $( '#customize-control-porto_header_layouts_select select' ).trigger( 'change' );
                }
                porto_header_builder_first_load = false;
            }
        } );

        $( '#customize-control-porto_header_layouts_select select' ).on( 'change', function () {
            $( '#customize-control-porto_header_layouts_block_element, #customize-control-porto_header_layouts_html_element, #customize-control-porto_header_layouts_el_class, #customize-control-porto_header_layouts_save_html_button' ).hide();
            activeSettingObject = null;
            if ( $( this ).val() ) {
                var $this = $( this );
                $( '.porto-header-builder' ).addClass( 'active' );
                $( '#customize-control-porto_header_layouts_custom_css' ).show();
                $( '.porto_delete_header_layout_link' ).removeClass( 'disabled' );
                $this.attr( 'disabled', 'disabled' );
                $.ajax( {
                    url: customizer_admin_vars.ajax_url,
                    data: { wp_customize: 'on', action: 'porto_load_header_elements', nonce: customizer_admin_vars.nonce, header_layout: $this.val() },
                    type: 'post',
                    dataType: 'json',
                    success: function ( response ) {
                        if ( !response ) {
                            response = {};
                        }
                        resetHeaderBuilderElements( response );
                        wp.customize.instance( 'porto_header_builder[preset]' ).set( '' );
                    },
                    complete: function () {
                        $this.removeAttr( 'disabled' );
                    }
                } );
            } else {
                $( '.porto-header-builder' ).removeClass( 'active' );
                $( '#customize-control-porto_header_layouts_custom_css' ).hide();
                $( '.porto_delete_header_layout_link' ).addClass( 'disabled' );
            }
        } );
        var activeSettingObject = null;
        $( document.body ).on( 'click', '.porto-header-builder .header-builder-wrapper [data-id="html"], .porto-header-builder .header-builder-wrapper [data-id="porto_block"]', function ( e ) {
            var sameObject = activeSettingObject == null || !activeSettingObject.is( $( this ) ) ? false : true,
                $this = $( this );
            if ( $this.data( 'id' ) == 'html' ) {
                if ( !sameObject || $( '#customize-control-porto_header_layouts_html_element' ).is( ':hidden' ) ) {
                    $( '#customize-control-porto_header_layouts_block_element' ).hide();
                    $( '#customize-control-porto_header_layouts_html_element, #customize-control-porto_header_layouts_el_class, #customize-control-porto_header_layouts_save_html_button' ).show();
                    $( '#customize-control-porto_header_layouts_html_element textarea' ).focus();
                    if ( $this.data( 'html' ) ) {
                        $( '#customize-control-porto_header_layouts_html_element textarea' ).val( $this.data( 'html' ) );
                    } else {
                        $( '#customize-control-porto_header_layouts_html_element textarea' ).val( '' );
                    }
                } else if ( $( '#customize-control-porto_header_layouts_html_element' ).is( ':visible' ) ) {
                    $( '#customize-control-porto_header_layouts_block_element, #customize-control-porto_header_layouts_html_element, #customize-control-porto_header_layouts_el_class, #customize-control-porto_header_layouts_save_html_button' ).hide();
                }
            } else if ( $this.data( 'id' ) == 'porto_block' ) {
                if ( !sameObject || $( '#customize-control-porto_header_layouts_block_element' ).is( ':hidden' ) ) {
                    $( '#customize-control-porto_header_layouts_html_element' ).hide();
                    $( '#customize-control-porto_header_layouts_block_element, #customize-control-porto_header_layouts_el_class, #customize-control-porto_header_layouts_save_html_button' ).show();
                    if ( $this.data( 'html' ) ) {
                        $( '#customize-control-porto_header_layouts_block_element select :selected' ).removeAttr( 'selected' );
                        $( '#customize-control-porto_header_layouts_block_element select option[value="' + escape( $this.data( 'html' ) ) + '"]' ).attr( 'selected', 'selected' );
                    }
                } else if ( $( '#customize-control-porto_header_layouts_block_element' ).is( ':visible' ) ) {
                    $( '#customize-control-porto_header_layouts_block_element, #customize-control-porto_header_layouts_html_element, #customize-control-porto_header_layouts_el_class, #customize-control-porto_header_layouts_save_html_button' ).hide();
                }
            }
            if ( $this.data( 'el_class' ) ) {
                $( '#customize-control-porto_header_layouts_el_class input' ).val( $this.data( 'el_class' ) );
            } else {
                $( '#customize-control-porto_header_layouts_el_class input' ).val( '' );
            }
            activeSettingObject = $( this );
        } );
        $( document.body ).on( 'click', '.goto-header-builder', function ( e ) {
            e.preventDefault();
            wp.customize.section( 'porto_header_layouts' ).focus();
        } );
        $( '.porto-header-builder-tooltip' ).on( 'click', function ( e ) {
            e.preventDefault();
            if ( $( this ).data( 'id' ) ) {
                var type = $( this ).data( 'type' ) ? $( this ).data( 'type' ) : 'section';
                if ( typeof wp.customize[ type ]( $( this ).data( 'id' ) ) == 'undefined' ) {
                    return;
                }
                if ( wp.customize[ type ]( $( this ).data( 'id' ) ).contentContainer ) {
                    $.redux.initFields( wp.customize[ type ]( $( this ).data( 'id' ) ).contentContainer );
                }
                wp.customize[ type ]( $( this ).data( 'id' ) ).focus();
            }
        } );
        $( document.body ).on( 'click', '.porto_header_builder_save_html', function () {
            if ( activeSettingObject == null ) {
                return false;
            }
            if ( activeSettingObject.data( 'id' ) == 'html' ) {
                activeSettingObject.data( 'html', $( '#customize-control-porto_header_layouts_html_element textarea' ).val() );
                $( '#customize-control-porto_header_layouts_html_element textarea' ).val( '' );
            } else if ( activeSettingObject.data( 'id' ) == 'porto_block' ) {
                activeSettingObject.data( 'html', $( '#customize-control-porto_header_layouts_block_element select' ).val() );
            }
            activeSettingObject.data( 'el_class', $( '#customize-control-porto_header_layouts_el_class input' ).val() );
            setItem( activeSettingObject.closest( 'div' ).data( 'id' ), 'porto-drop-item' + ( activeSettingObject.closest( '.porto-drop-item-mobile' ).length ? '-mobile' : '' ) );
            $( '#customize-control-porto_header_layouts_block_element, #customize-control-porto_header_layouts_html_element, #customize-control-porto_header_layouts_el_class, #customize-control-porto_header_layouts_save_html_button' ).fadeOut();
        } );

        $( document.body ).on( 'initReduxFields', function ( e, parentObj ) {
            $.redux.initFields( parentObj );
        } );

        if ( typeof $.redux != 'undefined' ) {
            var reduxInitializedFields = [ 'background', 'color_gradient', 'color' ];
            $.redux.initFields = function ( parentObj ) {
                if ( !parentObj ) {
                    parentObj = $( '.redux-section.open' );
                }
                parentObj.find( ".redux-group-tab:visible .redux-field-init:visible" ).each(
                    function () {
                        var type = $( this ).attr( 'data-type' );
                        if ( /*$.inArray(type, reduxInitializedFields) == -1 &&*/ type in redux.field_objects && typeof redux.field_objects[ type ].init == 'function' ) {
                            reduxInitializedFields.push( type );
                            redux.field_objects[ type ].init( $( this ) );
                        }
                    }
                );
                parentObj.find( '.redux-image-select [data-original]' ).each( function ( index, element ) {
                    if ( $.fn.waypoint ) {
                        $( element ).waypoint( function ( direction ) {
                            portoAdminLazyLoadImages( element );
                        }, { offset: '140%' } );
                    } else {
                        portoAdminLazyLoadImages( element );
                    }
                } );
            };
        }

        // lazy load images
        function portoAdminLazyLoadImages( element ) {
            var $element = $( element );
            if ( $element.hasClass( 'lazy-load-active' ) ) return;
            var src = $element.data( 'original' );
            if ( src ) $element.attr( 'src', src );
            $element.addClass( 'lazy-load-active' );
        }

        // reset options
        $( '.porto_reset_all_options' ).on( 'click', function ( e ) {
            e.preventDefault();
            if ( confirm( 'Attention! This will remove all customizations made to the theme.' ) ) {
                $.ajax( {
                    url: customizer_admin_vars.ajax_url,
                    data: { wp_customize: 'on', action: 'porto_customizer_reset_options', nonce: customizer_admin_vars.nonce },
                    type: 'post',
                    dataType: 'json',
                    success: function ( response ) {
                        wp.customize.state( 'saved' ).set( true );
                        window.location.reload();
                    }
                } );
            }
        } );

        // goto section
        $( '.redux-info-field .goto-section' ).on( 'click', function ( e ) {
            e.preventDefault();
            var section_id = $( this ).attr( 'href' );
            if ( $( this ).hasClass( 'field-control' ) ) {
                if ( typeof wp.customize.control( 'porto_settings[' + section_id + ']' ).contentContainer != 'undefined' ) {
                    $.redux.initFields( wp.customize.control( section_id ).container.closest( '.control-section' ) );
                }
                wp.customize.control( 'porto_settings[' + section_id + ']' ).focus();
            } else {
                if ( typeof wp.customize.section( section_id ).contentContainer != 'undefined' ) {
                    $.redux.initFields( wp.customize.section( section_id ).contentContainer );
                }
                wp.customize.section( section_id ).focus();
            }
        } );

        $( '.header-builder-header .preview-desktop' ).on( 'click', function ( e ) {
            e.preventDefault();
            $( '.devices .preview-desktop' ).click();
            $( this ).siblings().removeClass( 'active' );
            $( this ).addClass( 'active' );
        } );
        $( '.header-builder-header .preview-mobile' ).on( 'click', function ( e ) {
            e.preventDefault();
            $( '.devices .preview-tablet' ).click();
            $( this ).siblings().removeClass( 'active' );
            $( this ).addClass( 'active' );
        } );
        $( '.header-builder-header .button-close' ).on( 'click', function ( e ) {
            e.preventDefault();
            $( '#sub-accordion-section-porto_header_layouts .customize-section-back' ).click();
        } );
        $( '.header-builder-header .button-clear' ).on( 'click', function ( e ) {
            e.preventDefault();
            $( '.header-builder-wrapper .header-builder' ).each( function () {
                var $this = $( this );
                $this.find( 'span:not(.element-infinite)' ).appendTo( $this.parent().prev().find( '.porto-header-builder-list' ) );
                $this.find( '.element-infinite' ).remove();
            } );
            $.each( wp.customize.section( 'porto_header_builder' ).controls(), function ( key, control ) {
                wp.customize.instance( control.settings.default.id ).set( '' );
            } );
        } );

        if ( typeof $.redux != 'undefined' ) {
            // add go to old theme options button
            $( '#customize-header-actions' ).append( '<a href="#" class="button button-secondary switch-live-option-panel">Old Panel</a>' );
        }

        // set default options for header presets
        $( '.porto_header_presets > h3' ).on( 'click', function ( e ) {
            $( this ).parent().toggleClass( 'opened' );
        } );
        if ( typeof js_porto_hb_vars != 'undefined' ) {
            js_porto_hb_vars.header_builder_presets = JSON.parse( js_porto_hb_vars.header_builder_presets );
        }
        $( '.porto_header_presets > img' ).on( 'click', function () {
            if ( $( this ).hasClass( 'active' ) ) return;
            $( this ).siblings( 'img' ).removeClass( 'active' );
            $( this ).addClass( 'active' );
            var selected_header = $( this ).data( 'preset' );
            wp.customize.instance( 'porto_header_builder[preset]' ).set( selected_header );
            if ( js_porto_hb_vars.header_builder_presets[ selected_header ] ) {
                var default_settings = js_porto_hb_vars.header_builder_presets[ selected_header ];
                resetHeaderBuilderElements( default_settings );

                $.each( default_settings.options, function ( name, value ) {
                    if ( $( '#porto_settings-' + name ).length ) {
                        if ( value ) {
                            $( '#porto_settings-' + name ).find( 'input[value="' + value + '"]' ).trigger( 'click' );
                        } else {
                            $( '#porto_settings-' + name ).find( 'input[value]:first-child' ).trigger( 'click' );
                        }
                    }
                } );
            }
        } );

        $( '#porto_settings-show-icon-menus-mobile label' ).on( 'click', function ( e ) {
            if ( $( '#customize-preview' ).width() != 320 ) {
                $( '#customize-footer-actions .preview-mobile' ).trigger( 'click' );
            }
        } );
    } );
} )( wp.customize, wp, jQuery );