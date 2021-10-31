/**
 * Header Builder Social Icons
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
        RangeControl = wpComponents.RangeControl,
        ServerSideRender = wp.serverSideRender,
        Placeholder = wpComponents.Placeholder;

    const PortoTypographyControl = window.portoTypographyControl,
    EmptyPlaceholder = () => (
        <Placeholder
            icon='porto'
            label={ __( 'Social Icons', 'porto-functionality' ) }
        >
            { __(
                'Please add social links in Theme Options -> Header -> Social Links.',
                'porto-functionality'
            ) }
        </Placeholder>
    );

    const PortoHBSocial = function ( { attributes, setAttributes, name } ) {

        let internalStyle = '';

        internalStyle += '#header .share-links a {';
        if ( attributes.icon_size ) {
            let unitVal = attributes.icon_size;
            const unit = unitVal.trim().replace( /[0-9.]/g, '' );
            if ( ! unit ) {
                unitVal += 'px';
            }
            internalStyle += 'font-size:' + unitVal + ';';
        }
        if ( attributes.icon_border_style ) {
            internalStyle += 'border-style:' + attributes.icon_border_style + ';';
        }
        if ( attributes.icon_color_border ) {
            internalStyle += 'border-color:' + attributes.icon_color_border + ';';
        }
        if ( attributes.icon_border_size || 0 === attributes.icon_border_size ) {
            let unitVal = attributes.icon_border_size;
            const unit = unitVal.trim().replace( /[0-9.]/g, '' );
            if ( ! unit ) {
                unitVal += 'px';
            }
            internalStyle += 'border-width:' + unitVal + ';';
        }
        if ( attributes.icon_border_radius ) {
            let unitVal = attributes.icon_border_radius;
            const unit = unitVal.trim().replace( /[0-9.]/g, '' );
            if ( ! unit ) {
                unitVal += 'px';
            }
            internalStyle += 'border-radius:' + unitVal + ';';
        }
        if ( attributes.icon_border_spacing ) {
            let unitVal = attributes.icon_border_spacing;
            const unit = unitVal.trim().replace( /[0-9.]/g, '' );
            if ( ! unit ) {
                unitVal += 'px';
            }
            internalStyle += 'width:' + unitVal + ';height:' + unitVal + ';';
        }
        if ( attributes.spacing ) {
            let unitVal = attributes.spacing;
            const unit = unitVal.trim().replace( /[0-9.]/g, '' );
            if ( ! unit ) {
                unitVal = unitVal / 2;
                unitVal += 'px';
            } else {
                unitVal = parseFloat( unitVal.replace( unit, '' ) ) / 2 + unit;
            }
            internalStyle += 'margin-left:' + unitVal + ';margin-right:' + unitVal + ';';
        }

        internalStyle += '}';

        if ( attributes.icon_color || attributes.icon_color_bg ) {
            internalStyle += '#header .share-links a:not(:hover){';
            if ( attributes.icon_color ) {
                internalStyle += 'color:' + attributes.icon_color + ';';
            }
            if ( attributes.icon_color_bg ) {
                internalStyle += 'background-color:' + attributes.icon_color_bg;
            }
            internalStyle += '}';
        }

        if ( attributes.icon_hover_color || attributes.icon_hover_color_bg ) {
            internalStyle += '#header .share-links a:hover {';
            if ( attributes.icon_hover_color ) {
                internalStyle += 'color:' + attributes.icon_hover_color + ';';
            }
            if ( attributes.icon_hover_color_bg ) {
                internalStyle += 'background-color:' + attributes.icon_hover_color_bg + ';';
            }
            internalStyle += '}';
        }

        return (
            <>
                <InspectorControls key="inspector">
                    <TextControl
                        label={ __( 'Icon Font Size', 'porto-functionality' ) }
                        value={ attributes.icon_size }
                        help={ __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ) }
                        onChange={ ( value ) => { setAttributes( { icon_size: value } ); } }
                    />
                    <PanelColorSettings
                        title={ __( 'Color Settings', 'porto-functionality' ) }
                        initialOpen={ false }
                        colorSettings={ [
                            {
                                label: __( 'Color', 'porto-functionality' ),
                                value: attributes.icon_color,
                                onChange: function onChange( value ) {
                                    return setAttributes( { icon_color: value } );
                                }
                            },
                            {
                                label: __( 'Hover Color', 'porto-functionality' ),
                                value: attributes.icon_color,
                                onChange: function onChange( value ) {
                                    return setAttributes( { icon_color: value } );
                                }
                            },
                            {
                                label: __( 'Icon Background Color', 'porto-functionality' ),
                                value: attributes.icon_color_bg,
                                onChange: function onChange( value ) {
                                    return setAttributes( { icon_color_bg: value } );
                                }
                            },
                            {
                                label: __( 'Icon Hover Background Color', 'porto-functionality' ),
                                value: attributes.icon_hover_color_bg,
                                onChange: function onChange( value ) {
                                    return setAttributes( { icon_hover_color_bg: value } );
                                }
                            },
                            {
                                label: __( 'Icon Border Color', 'porto-functionality' ),
                                value: attributes.icon_color_border,
                                onChange: function onChange( value ) {
                                    return setAttributes( { icon_color_border: value } );
                                }
                            }
                        ] }
                    />
                    <SelectControl
                        label={ __( 'Icon Border Style', 'porto-functionality' ) }
                        value={ attributes.icon_border_style }
                        options={ [ { 'label': __( 'None', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Solid', 'porto-functionality' ), 'value': 'solid' }, { 'label': __( 'Dashed', 'porto-functionality' ), 'value': 'dashed' }, { 'label': __( 'Dotted', 'porto-functionality' ), 'value': 'dotted' }, { 'label': __( 'Double', 'porto-functionality' ), 'value': 'double' }, { 'label': __( 'Inset', 'porto-functionality' ), 'value': 'inset' }, { 'label': __( 'Outset', 'porto-functionality' ), 'value': 'outset' } ] }
                        onChange={ ( value ) => { setAttributes( { icon_border_style: value } ); } }
                    />
                    <RangeControl
                        label={ __( 'Icon Border Width', 'porto-functionality' ) }
                        value={ attributes.icon_border_size }
                        min="0"
                        max="20"
                        onChange={ ( value ) => { setAttributes( { icon_border_size: value } ); } }
                    />
                    <TextControl
                        label={ __( 'Icon Border Radius', 'porto-functionality' ) }
                        value={ attributes.icon_border_radius }
                        help={ __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ) }
                        onChange={ ( value ) => { setAttributes( { icon_border_radius: value } ); } }
                    />
                    <TextControl
                        label={ __( 'Icon Size', 'porto-functionality' ) }
                        value={ attributes.icon_border_spacing }
                        help={ __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ) }
                        onChange={ ( value ) => { setAttributes( { icon_border_spacing: value } ); } }
                    />
                    <TextControl
                        label={ __( 'Spacing', 'porto-functionality' ) }
                        value={ attributes.spacing }
                        help={ __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ) }
                        onChange={ ( value ) => { setAttributes( { spacing: value } ); } }
                    />
                </InspectorControls>
                <Disabled>
                    { internalStyle && (
                        <style>
                            { internalStyle }
                        </style>
                    ) }
                    <ServerSideRender
                        block={ name }
                        attributes={ { className: attributes.className } }
                        EmptyResponsePlaceholder={ EmptyPlaceholder }
                    />
                </Disabled>
            </>
        )
    }
    registerBlockType( 'porto-hb/porto-social', {
        title: __( 'Porto Social Icons', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-hb',
        description: __(
            'Display social icons in header',
            'porto-functionality'
        ),
        attributes: {
            icon_size: {
                type: 'string',
            },
            icon_color: {
                type: 'string',
                default: '',
            },
            icon_hover_color: {
                type: 'string',
                default: '',
            },
            icon_color_bg: {
                type: 'string',
                default: '',
            },
            icon_hover_color_bg: {
                type: 'string',
                default: '',
            },
            icon_border_style: {
                type: 'string',
                default: '',
            },
            icon_color_border: {
                type: 'string',
                default: '',
            },
            icon_border_size: {
                type: 'int',
            },
            icon_border_radius: {
                type: 'string',
            },
            icon_border_spacing: {
                type: 'string',
            },
            spacing: {
                type: 'string',
            },
        },
        edit: PortoHBSocial,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );