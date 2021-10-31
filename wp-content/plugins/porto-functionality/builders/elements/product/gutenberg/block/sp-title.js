/**
 * Single Product Title
 * 
 * @since 6.1.0
 */


( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash ) {
    "use strict";

    var __ = wpI18n.__,
        registerBlockType = wpBlocks.registerBlockType,
        PanelBody = wpComponents.PanelBody,
        InspectorControls = wpBlockEditor.InspectorControls,
        Disabled = wpComponents.Disabled,
        Placeholder = wpComponents.Placeholder,
        ServerSideRender = wp.serverSideRender,
        TextControl = wpComponents.TextControl,
        RangeControl = wpComponents.RangeControl,
        PanelColorSettings = wpBlockEditor.PanelColorSettings;

    const EmptyPlaceholder = () => (
        <Placeholder
            icon='porto'
            label={ __( 'Single Product Title', 'porto-functionality' ) }
        >
            { __(
                'This block shows single product title. There are currently no discounted products in your store.',
                'porto-functionality'
            ) }
        </Placeholder>
    );

    const PortoSpTitle = function ( { attributes, setAttributes, name, className } ) {
        let style_inline = '<style>';
        let shortcode_css = ''
        if ( attributes.font_family ) {
            shortcode_css += 'font-family:' + attributes.font_family + ';';            
        }
        if ( attributes.font_size ) {
            let unit = attributes.font_size.replace( /[0-9.]/g, '' );
            let size = attributes.font_size;
            if ( !unit ) {
                size += 'px';
            }
            shortcode_css += 'font-size:' + size + ';';
        }
        if ( attributes.font_weight ) {
            shortcode_css += 'font-weight:' + Number( attributes.font_weight ) + ';';
        }
        if ( attributes.text_transform) {
            shortcode_css += 'text-transform:' + attributes.text_transform + ';';   
        }
        if ( attributes.line_height ) {
            let unit = attributes.line_height.replace( /[0-9.]/g, '' );
            let lh = attributes.line_height;
            if ( !unit && attributes.line_height > 3 ) {
                lh += 'px';
            }
            shortcode_css += 'line-height:' + lh + ';';
        }
        if ( attributes.letter_spacing ) {
            shortcode_css += 'letter-spacing:' + attributes.letter_spacing + ';';
        }
        if ( attributes.color ) {
            shortcode_css += 'color:' + attributes.color + ';';
        }
        if ( shortcode_css ) {
            style_inline += '.block-editor .product_title, .single-product .product_title {' + shortcode_css + '}'
        }
        style_inline += '</style>';

        const PortoTypographyControl = window.portoTypographyControl;
        return (
            <>
                <InspectorControls key="inspector">
                    <PanelBody
                        title={ __( 'General', 'porto-functionality' ) }
                        initialOpen={ true }
                    >
                        <PortoTypographyControl
                            label={ __( 'Typography', 'porto-functionality' ) }
                            value= { { fontFamily: attributes.font_family, fontSize: attributes.font_size, fontWeight: attributes.font_weight, textTransform: attributes.text_transform, lineHeight: attributes.line_height, letterSpacing: attributes.letter_spacing, color: attributes.color } }
                            options= {{}}
                            onChange= { ( value ) => {
                                if ( typeof value.fontFamily != 'undefined' )
                                    setAttributes( { font_family: value.fontFamily } );
                                if ( typeof value.fontSize != 'undefined' )
                                    setAttributes( { font_size: value.fontSize } );
                                if ( typeof value.fontWeight != 'undefined' )
                                    setAttributes( { font_weight: value.fontWeight } );
                                if ( typeof value.textTransform != 'undefined' )
                                    setAttributes( { text_transform: value.textTransform } );
                                if ( typeof value.lineHeight != 'undefined' )
                                    setAttributes( { line_height: value.lineHeight } );
                                if ( typeof value.letterSpacing != 'undefined' )
                                    setAttributes( { letter_spacing: value.letterSpacing } );
                                if ( typeof value.color != 'undefined' )
                                    setAttributes( { color: value.color } );
                                else
                                    setAttributes( { color: '' } );
                                }
                            }
                        />
                    </PanelBody>
                </InspectorControls>
                <Disabled>
                    <div dangerouslySetInnerHTML={ { __html: style_inline } }>
                    </div>
                    <ServerSideRender
                        block={ name }
                        attributes={ { el_class: className ? className : '' } }
                        EmptyResponsePlaceholder={ EmptyPlaceholder }
                    />
                </Disabled>
            </>
        )
    }
    registerBlockType( 'porto-single-product/porto-sp-title', {
        title: __( 'Porto Single Product Title', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-single-product',
        description: __(
            'Display a single product title. It supports you to customize single product page as you mind.',
            'porto-functionality'
        ),
        attributes: {
            font_family: {
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
            }
        },
        edit: PortoSpTitle,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );