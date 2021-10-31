/**
 * Header Builder Mini Cart
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

    const PortoTypographyControl = window.portoTypographyControl;

    const PortoHBMinicart = function ( { attributes, setAttributes, name } ) {

        let internalStyle = '';

        if ( attributes.icon_size ) {
            let unitVal = attributes.icon_size;
            const unit = unitVal.trim().replace( /[0-9.]/g, '' );
            if ( ! unit ) {
                unitVal += 'px';
            }
            internalStyle += '#mini-cart .minicart-icon{font-size:' + unitVal + '}';
        }
        if ( attributes.icon_color ) {
            internalStyle += '#mini-cart .cart-subtotal, #mini-cart .minicart-icon{color:' + attributes.icon_color + '}';
        }
        if ( attributes.icon_mr || attributes.icon_ml ) {
            internalStyle += '#mini-cart .cart-icon{';
            if ( attributes.icon_mr ) {
                let unitVal = attributes.icon_mr;
                const unit = unitVal.trim().replace( /[0-9.]/g, '' );
                if ( ! unit ) {
                   unitVal += 'px';
                }
                internalStyle += 'margin-right:' + unitVal + ';';
            }
            if ( attributes.icon_ml ) {
                let unitVal = attributes.icon_ml;
                const unit = unitVal.trim().replace( /[0-9.]/g, '' );
                if ( ! unit ) {
                    unitVal += 'px';
                }
                internalStyle += 'margin-left:' + unitVal;
            }
            internalStyle += '}';
        }

        if ( 'minicart-inline' === attributes.type || 'minicart-text' === attributes.type ) {
            let text_style_escaped = '';
            if ( attributes.text_font_size ) {
                let unitVal = attributes.text_font_size;
                const unit = unitVal.trim().replace( /[0-9.]/g, '' );
                if ( ! unit ) {
                    unitVal += 'px';
                }
                text_style_escaped += 'font-size:' + unitVal + ';';
            }
            if ( attributes.text_font_weight ) {
                text_style_escaped += 'font-weight:' + attributes.text_font_weight + ';';
            }
            if ( attributes.text_transform ) {
                text_style_escaped += 'text-transform:' + attributes.text_transform + ';';
            }
            if ( attributes.text_line_height ) {
                let unitVal = attributes.text_line_height;
                const unit = unitVal.trim().replace( /[0-9.]/g, '' );
                if ( ! unit && unitVal > 3 ) {
                    unitVal += 'px';
                }
                text_style_escaped += 'line-height:' + unitVal + ';';
            }
            if ( typeof attributes.text_ls != 'undefined' && '' !== attributes.text_ls ) {
                let unitVal = attributes.text_ls;
                const unit = unitVal.trim().replace( /[0-9.]/g, '' );
                if ( ! unit ) {
                    unitVal += 'px';
                }
                text_style_escaped += 'letter-spacing:' + unitVal + ';';
            }
            if ( attributes.text_color ) {
                text_style_escaped += 'color:' + attributes.text_color;
            }
            if ( text_style_escaped ) {
                internalStyle += '#mini-cart .cart-subtotal {' + text_style_escaped + '}';
            }

            let price_style_escaped = '';
            if ( attributes.price_font_size ) {
                let unitVal = attributes.price_font_size;
                const unit = unitVal.trim().replace( /[0-9.]/g, '' );
                if ( ! unit ) {
                    unitVal += 'px';
                }
                price_style_escaped += 'font-size:' + unitVal + ';';
            }
            if ( attributes.price_font_weight ) {
                price_style_escaped += 'font-weight:' + attributes.price_font_weight + ';';
            }
            if ( attributes.price_line_height ) {
                let unitVal = attributes.price_line_height;
                const unit = unitVal.trim().replace( /[0-9.]/g, '' );
                if ( ! unit && unitVal > 3 ) {
                    unitVal += 'px';
                }
                price_style_escaped += 'line-height:' + unitVal + ';';
            }
            if ( typeof attributes.price_ls != 'undefined' && '' !== attributes.price_ls ) {
                let unitVal = attributes.price_ls;
                const unit = unitVal.trim().replace( /[0-9.]/g, '' );
                if ( ! unit ) {
                    unitVal += 'px';
                }
                price_style_escaped += 'letter-spacing:' + unitVal + ';';
            }
            if ( attributes.price_color ) {
                price_style_escaped += 'color:' + attributes.price_color;
            }
            if ( price_style_escaped ) {
                internalStyle += '#mini-cart .cart-price {' + price_style_escaped + '}';
            }
        }

        return (
            <>
                <InspectorControls key="inspector">
                    <SelectControl
                        label={ __( 'Type', 'porto-functionality' ) }
                        help={ __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ) }
                        value={ attributes.type }
                        options={ [ { 'label': __( 'Theme Options', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Simple', 'porto-functionality' ), 'value': 'simple' }, { 'label': __( 'Arrow Alt', 'porto-functionality' ), 'value': 'minicart-arrow-alt' }, { 'label': __( 'Text', 'porto-functionality' ), 'value': 'minicart-inline' }, { 'label': __( 'Icon & Text', 'porto-functionality' ), 'value': 'minicart-text' } ] }
                        onChange={ ( value ) => { setAttributes( { type: value } ); } }
                    />
                    <SelectControl
                        label={ __( 'Content Type', 'porto-functionality' ) }
                        value={ attributes.content_type }
                        options={ [ { 'label': __( 'Default', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Off Canvas', 'porto-functionality' ), 'value': 'offcanvas' } ] }
                        onChange={ ( value ) => { setAttributes( { content_type: value } ); } }
                    />
                    <TextControl
                        label={ __( 'Icon Class (ex: fas fa-pencil-alt)', 'porto-functionality' ) }
                        value={ attributes.icon_cl }
                        onChange={ ( value ) => { setAttributes( { icon_cl: value } ); } }
                    />
                    { ( 'minicart-inline' === attributes.type || 'minicart-text' === attributes.type ) && (
                        <TextControl
                            label={ __( 'Mini Cart Text', 'porto-functionality' ) }
                            help={ __( 'If you have any trouble with this setting, please use Porto -> Theme Options -> Header -> Mini Cart Text instead.', 'porto-functionality' ) }
                            value={ attributes.cart_text }
                            onChange={ ( value ) => { setAttributes( { cart_text: value } ); } }
                        />
                    ) }
                    <TextControl
                        label={ __( 'Icon Size', 'porto-functionality' ) }
                        value={ attributes.icon_size }
                        help={ __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ) }
                        onChange={ ( value ) => { setAttributes( { icon_size: value } ); } }
                    />
                    <PanelColorSettings
                        title={ __( 'Color Settings', 'porto-functionality' ) }
                        initialOpen={ false }
                        colorSettings={ [
                            {
                                label: __( 'Icon Color', 'porto-functionality' ),
                                value: attributes.icon_color,
                                onChange: function onChange( value ) {
                                    return setAttributes( { icon_color: value } );
                                }
                            }
                        ] }
                    />
                    <TextControl
                        label={ __( 'Icon Margin Left', 'porto-functionality' ) }
                        value={ attributes.icon_ml }
                        help={ __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ) }
                        onChange={ ( value ) => { setAttributes( { icon_ml: value } ); } }
                    />
                    <TextControl
                        label={ __( 'Icon Margin Right', 'porto-functionality' ) }
                        value={ attributes.icon_mr }
                        help={ __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ) }
                        onChange={ ( value ) => { setAttributes( { icon_mr: value } ); } }
                    />
                    { ( 'minicart-inline' === attributes.type || 'minicart-text' === attributes.type ) && (
                        <PortoTypographyControl
                            label={ __( 'Text Font', 'porto-functionality' ) }
                            value={ { fontSize: attributes.text_font_size, fontWeight: attributes.text_font_weight, textTransform: attributes.text_transform, lineHeight: attributes.text_line_height, letterSpacing: attributes.text_ls, color: attributes.text_color } }
                            options={ { fontFamily: false } }
                            onChange={ ( value ) => {
                                if ( typeof value.fontSize != 'undefined' ) {
                                    setAttributes( { text_font_size: value.fontSize } );
                                }
                                if ( typeof value.fontWeight != 'undefined' ) {
                                    setAttributes( { text_font_weight: value.fontWeight } );
                                }
                                if ( typeof value.textTransform != 'undefined' ) {
                                    setAttributes( { text_transform: value.textTransform } );
                                }
                                if ( typeof value.lineHeight != 'undefined' ) {
                                    setAttributes( { text_line_height: value.lineHeight } );
                                }
                                if ( typeof value.letterSpacing != 'undefined' ) {
                                    setAttributes( { text_ls: value.letterSpacing } );
                                }
                                if ( typeof value.color != 'undefined' ) {
                                    setAttributes( { text_color: value.color } );
                                } else {
                                    setAttributes( { text_color: '' } );
                                }
                            } }
                        />
                    ) }
                    { ( 'minicart-inline' === attributes.type || 'minicart-text' === attributes.type ) && (
                        <PortoTypographyControl
                            label={ __( 'Price Font', 'porto-functionality' ) }
                            value={ { fontSize: attributes.price_font_size, fontWeight: attributes.price_font_weight, lineHeight: attributes.price_line_height, letterSpacing: attributes.price_ls, color: attributes.price_color } }
                            options={ { fontFamily: false, textTransform: false } }
                            onChange={ ( value ) => {
                                if ( typeof value.fontSize != 'undefined' ) {
                                    setAttributes( { price_font_size: value.fontSize } );
                                }
                                if ( typeof value.fontWeight != 'undefined' ) {
                                    setAttributes( { price_font_weight: value.fontWeight } );
                                }
                                if ( typeof value.lineHeight != 'undefined' ) {
                                    setAttributes( { price_line_height: value.lineHeight } );
                                }
                                if ( typeof value.letterSpacing != 'undefined' ) {
                                    setAttributes( { price_ls: value.letterSpacing } );
                                }
                                if ( typeof value.color != 'undefined' ) {
                                    setAttributes( { price_color: value.color } );
                                } else {
                                    setAttributes( { price_color: '' } );
                                }
                            } }
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
                        attributes={ { type: attributes.type, content_type: attributes.content_type, icon_cl: attributes.icon_cl, cart_text: attributes.cart_text, className: attributes.className } }
                    />
                </Disabled>
            </>
        )
    }
    registerBlockType( 'porto-hb/porto-mini-cart', {
        title: __( 'Porto Mini Cart', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-hb',
        description: __(
            'Display mini cart in header',
            'porto-functionality'
        ),
        attributes: {
            type: {
                type: 'string',
                default: 'minicart-arrow-alt',
            },
            content_type: {
                type: 'string',
                default: '',
            },
            icon_cl: {
                type: 'string',
                default: '',
            },
            cart_text: {
                type: 'string',
            },
            icon_size: {
                type: 'string',
            },
            icon_color: {
                type: 'string',
            },
            icon_margin_left: {
                type: 'string',
            },
            icon_margin_right: {
                type: 'string',
            },
            text_font_size: {
                type: 'string',
            },
            text_font_weight: {
                type: 'int',
            },
            text_transform: {
                type: 'string',
            },
            text_line_height: {
                type: 'string',
            },
            text_ls: {
                type: 'string',
            },
            text_color: {
                type: 'string',
                default: '',
            },
            price_font_size: {
                type: 'string',
            },
            price_font_weight: {
                type: 'int',
            },
            price_line_height: {
                type: 'string',
            },
            price_ls: {
                type: 'string',
            },
            price_color: {
                type: 'string',
            },
        },
        edit: PortoHBMinicart,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );