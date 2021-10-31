/**
 * Header Builder Menu
 * 
 * @since 6.1.0
 */
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash ) {
    "use strict";

    var __ = wpI18n.__,
        registerBlockType = wpBlocks.registerBlockType,
        PanelBody = wpComponents.PanelBody,
        InspectorControls = wpBlockEditor.InspectorControls,
        PanelColorSettings = wpBlockEditor.PanelColorSettings,
        Disabled = wpComponents.Disabled,
        TextControl = wpComponents.TextControl,
        SelectControl = wpComponents.SelectControl,
        ServerSideRender = wp.serverSideRender,
        Placeholder = wpComponents.Placeholder;

    const PortoTypographyControl = window.portoTypographyControl,
        EmptyPlaceholder = () => (
        <Placeholder
            icon='porto'
            label={ __( 'Menu', 'porto-functionality' ) }
        >
            { __(
                'Please select a menu location.',
                'porto-functionality'
            ) }
        </Placeholder>
    );

    const PortoHBMenu = function ( { attributes, setAttributes, name } ) {

        const menuColorSettings = [];
        if ( 'main-toggle-menu' === attributes.location || 'nav-top' === attributes.location ) {
            menuColorSettings.push( {
                label: __( 'Top Level Hover Color', 'porto-functionality' ),
                value: attributes.hover_color,
                onChange: function onChange( value ) {
                    return setAttributes( { hover_color: value } );
                }
            } );
            if ( 'main-toggle-menu' === attributes.location ) {
                menuColorSettings.push( {
                    label: __( 'Top Level Hover Background Color', 'porto-functionality' ),
                    value: attributes.hover_bgcolor,
                    onChange: function onChange( value ) {
                        return setAttributes( { hover_bgcolor: value } );
                    }
                } );
            }
        }

        let internalStyle = '';
        if ( 'nav-top' === attributes.location ) {
            if ( attributes.font_size || attributes.font_weight || attributes.text_transform || attributes.line_height || attributes.letter_spacing || attributes.padding || attributes.color ) {
                internalStyle += '#header .top-links > li.menu-item > a {';
                if ( attributes.font_size ) {
                    let unitVal = attributes.font_size;
                    const unit = unitVal.trim().replace( /[0-9.]/g, '' );
                    if ( ! unit ) {
                        unitVal += 'px';
                    }
                    internalStyle += 'font-size:' + unitVal + ';';
                }
                if ( attributes.font_weight ) {
                    internalStyle += 'font-weight:' + attributes.font_weight + ';';
                }
                if ( attributes.text_transform ) {
                    internalStyle += 'text-transform:' + attributes.text_transform + ';';
                }
                if ( attributes.line_height ) {
                    let unitVal = attributes.line_height;
                    const unit = unitVal.trim().replace( /[0-9.]/g, '' );
                    if ( ! unit && Number( unitVal ) > 3 ) {
                        unitVal += 'px';
                    }
                    internalStyle += 'line-height:' + unitVal + ';';
                }
                if ( attributes.letter_spacing ) {
                    let unitVal = attributes.letter_spacing;
                    const unit = unitVal.trim().replace( /[0-9.-]/g, '' );
                    if ( ! unit ) {
                        unitVal += 'px';
                    }
                    internalStyle += 'letter-spacing:' + unitVal + ';';
                }
                if ( attributes.padding ) {
                    let unitVal = attributes.padding;
                    const unit = unitVal.trim().replace( /[0-9.]/g, '' );
                    if ( ! unit ) {
                        unitVal += 'px';
                    }
                    internalStyle += 'padding-left:' + unitVal + ';';
                    internalStyle += 'padding-right:' + unitVal + ';';
                }
                if ( attributes.color ) {
                    internalStyle += 'color:' + attributes.color;
                }
                internalStyle += '}';
            }

            if ( attributes.hover_color ) {
                internalStyle += '#header .top-links > li.menu-item:hover > a {color:' + attributes.hover_color + '}';
            }
        } else if ( 'main-toggle-menu' === attributes.location ) {
            if ( attributes.font_size || attributes.font_weight || attributes.text_transform || attributes.line_height || attributes.letter_spacing || attributes.padding || attributes.color || attributes.bgcolor ) {
                internalStyle += '#main-toggle-menu .menu-title {';
                if ( attributes.font_size ) {
                    let unitVal = attributes.font_size;
                    const unit = unitVal.trim().replace( /[0-9.]/g, '' );
                    if ( ! unit ) {
                        unitVal += 'px';
                    }
                    internalStyle += 'font-size:' + unitVal + ';';
                }
                if ( attributes.font_weight ) {
                    internalStyle += 'font-weight:' + attributes.font_weight + ';';
                }
                if ( attributes.text_transform ) {
                    internalStyle += 'text-transform:' + attributes.text_transform + ';';
                }
                if ( attributes.line_height ) {
                    let unitVal = attributes.line_height;
                    const unit = unitVal.trim().replace( /[0-9.]/g, '' );
                    if ( ! unit && parseInt( unitVal, 10 ) > 3 ) {
                        unitVal += 'px';
                    }
                    internalStyle += 'line-height:' + unitVal + ';';
                }
                if ( attributes.letter_spacing ) {
                    let unitVal = attributes.letter_spacing;
                    const unit = unitVal.trim().replace( /[0-9.-]/g, '' );
                    if ( ! unit ) {
                        unitVal += 'px';
                    }
                    internalStyle += 'letter-spacing:' + unitVal + ';';
                }
                if ( attributes.padding || 0 === parseInt( attributes.padding, 10 ) ) {
                    let unitVal = attributes.padding;
                    const unit = unitVal.trim().replace( /[0-9.]/g, '' );
                    if ( ! unit ) {
                        unitVal += 'px';
                    }
                    internalStyle += 'padding-left:' + unitVal + ';';
                    internalStyle += 'padding-right:' + unitVal + ';';
                }
                if ( attributes.color ) {
                    internalStyle += 'color:' + attributes.color + ';';
                }
                if ( attributes.bgcolor ) {
                    internalStyle += 'background-color:' + attributes.bgcolor;
                }
                internalStyle += '}';
            }

            if ( attributes.popup_width ) {
                let unitVal = attributes.popup_width;
                const unit = unitVal.trim().replace( /[0-9.]/g, '' );
                if ( ! unit ) {
                    unitVal += 'px';
                }
                internalStyle += '#main-toggle-menu .toggle-menu-wrap {width:' + unitVal + '}';
            }

            if ( attributes.hover_color || attributes.hover_bgcolor ) {
                internalStyle += '#main-toggle-menu .menu-title:hover{';
                if ( attributes.hover_color ) {
                    internalStyle += 'color:' + attributes.hover_color + ';';
                }
                if ( attributes.hover_bgcolor ) {
                    internalStyle += 'background-color:' + attributes.hover_bgcolor + ';';
                }
                internalStyle += '}';
            }
        }

        return (
            <>
                <InspectorControls key="inspector">
                    <SelectControl
                        label={ __( 'Location', 'porto-functionality' ) }
                        value={ attributes.location }
                        options={ [ { 'label': __( 'Select a location', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Main Menu', 'porto-functionality' ), 'value': 'main-menu' }, { 'label': __( 'Secondary Menu', 'porto-functionality' ), 'value': 'secondary-menu' }, { 'label': __( 'Main Toggle Menu', 'porto-functionality' ), 'value': 'main-toggle-menu' }, { 'label': __( 'Top Navigation', 'porto-functionality' ), 'value': 'nav-top' } ] }
                        onChange={ ( value ) => { setAttributes( { location: value } ); } }
                    />
                    { 'main-toggle-menu' === attributes.location && (
                        <TextControl
                            label={ __( 'Menu Title', 'porto-functionality' ) }
                            value={ attributes.title }
                            onChange={ ( value ) => { setAttributes( { title: value } ); } }
                        />
                    ) }
                    { ( 'main-toggle-menu' === attributes.location || 'nav-top' === attributes.location ) && (
                        <PortoTypographyControl
                            label={ __( 'Top Level Font', 'porto-functionality' ) }
                            value={ { fontSize: attributes.font_size, fontWeight: attributes.font_weight, textTransform: attributes.text_transform, lineHeight: attributes.line_height, letterSpacing: attributes.letter_spacing, color: attributes.color } }
                            options={ { fontFamily: false } }
                            onChange={ ( value ) => {
                                if ( typeof value.fontSize != 'undefined' ) {
                                    setAttributes( { font_size: value.fontSize } );
                                }
                                if ( typeof value.fontWeight != 'undefined' ) {
                                    setAttributes( { font_weight: value.fontWeight } );
                                }
                                if ( typeof value.textTransform != 'undefined' ) {
                                    setAttributes( { text_transform: value.textTransform } );
                                }
                                if ( typeof value.lineHeight != 'undefined' ) {
                                    setAttributes( { line_height: value.lineHeight } );
                                }
                                if ( typeof value.letterSpacing != 'undefined' ) {
                                    setAttributes( { letter_spacing: value.letterSpacing } );
                                }
                                if ( typeof value.color != 'undefined' ) {
                                    setAttributes( { color: value.color } );
                                } else {
                                    setAttributes( { color: '' } );
                                }
                            } }
                        />
                    ) }
                    { ( 'main-toggle-menu' === attributes.location || 'nav-top' === attributes.location ) && (
                        <TextControl
                            label={ __( 'Top Level Left/Right Padding', 'porto-functionality' ) }
                            value={ attributes.padding }
                            help={ __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ) }
                            onChange={ ( value ) => { setAttributes( { padding: value } ); } }
                        />
                    ) }
                    { ( 'main-toggle-menu' === attributes.location || 'nav-top' === attributes.location ) && (
                        <PanelColorSettings
                            title={ __( 'Color Settings', 'porto-functionality' ) }
                            initialOpen={ false }
                            colorSettings={ menuColorSettings }
                        />
                    ) }
                    { ( 'main-toggle-menu' === attributes.location || 'nav-top' === attributes.location ) && (
                        <TextControl
                            label={ __( 'Popup Width', 'porto-functionality' ) }
                            value={ attributes.popup_width }
                            help={ __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ) }
                            onChange={ ( value ) => { setAttributes( { popup_width: value } ); } }
                        />
                    ) }
                </InspectorControls>
                <Disabled>
                    { internalStyle && (
                        <style>
                            { internalStyle }
                        </style>
                    ) }
                    <ServerSideRender
                        block={ name }
                        attributes={ { location: attributes.location, title: attributes.title, className: attributes.className } }
                        EmptyResponsePlaceholder={ EmptyPlaceholder }
                    />
                </Disabled>
            </>
        )
    }
    registerBlockType( 'porto-hb/porto-menu', {
        title: __( 'Porto Menu', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-hb',
        description: __(
            'Display navigation in header',
            'porto-functionality'
        ),
        supports: {
            customClassName: false,
        },
        attributes: {
            location: {
                type: 'string',
                default: '',
            },
            title: {
                type: 'string',
            },
            font_size: {
                type: 'string',
            },
            font_weight: {
                type: 'int',
            },
            text_transform: {
                type: 'string',
            },
            line_height: {
                type: 'string',
            },
            letter_spacing: {
                type: 'string',
            },
            color: {
                type: 'string',
            },
            padding: {
                type: 'string',
            },
            bgcolor: {
                type: 'string',
            },
            hover_color: {
                type: 'string',
            },
            hover_bgcolor: {
                type: 'string',
            },
            popup_width: {
                type: 'string',
            },
        },
        edit: PortoHBMenu,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );