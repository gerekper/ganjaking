/**
 * Single Product Excerpt
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
            label={ __( 'Single Product Excerpt', 'porto-functionality' ) }
        >
            { __(
                'This block shows single product excerpt. There are currently no discounted products in your store.',
                'porto-functionality'
            ) }
        </Placeholder>
    );

    const PortoSpExcerpt = function ( { attributes, setAttributes, name, className } ) {
        let style_inline = '<style>';
        let shortcode_css = ''
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
        if ( attributes.line_height ) {
            const unit = ( '' + attributes.line_height ).replace( /[0-9.]/g, '' );
            if ( !unit && attributes.line_height > 3 ) {
                attributes.line_height += 'px';
            }
            shortcode_css += 'line-height:' + attributes.line_height + ';';
        }
        if ( attributes.ls ) {
            const unit = attributes.ls.replace( /[0-9.]/g, '' );
            if ( !unit ) {
                attributes.ls += 'px';
            }
            shortcode_css += 'letter-spacing:' + attributes.ls + ';';
        }
        if ( attributes.color ) {
            shortcode_css += 'color:' + attributes.color + ';';
        }
        if ( shortcode_css ) {
            style_inline += '.block-editor .woocommerce-product-details__short-description p {' + shortcode_css + '}'
        }
        style_inline += '</style>';
        return (
            <>
                <InspectorControls key="inspector">
                    <PanelBody
                        title={ __( 'General', 'porto-functionality' ) }
                        initialOpen={ true }
                    >
                        <TextControl
                            label={ __( 'Font Size', 'porto-functionality' ) }
                            value={ attributes.font_size }
                            onChange={ ( value ) => { setAttributes( { font_size: value } ) } }
                        />
                        <RangeControl
                            label={ __( 'Font Weight', 'porto-functionality' ) }
                            value={ attributes.font_weight }
                            onChange={ ( value ) => { setAttributes( { font_weight: value } ) } }
                            min={ 100 }
                            max={ 900 }
                            step={ 100 }
                        />
                        <TextControl
                            label={ __( 'Line Height', 'porto-functionality' ) }
                            value={ attributes.line_height }
                            onChange={ ( value ) => { setAttributes( { line_height: value } ) } }
                        />
                        <TextControl
                            label={ __( 'Letter Spacing', 'porto-functionality' ) }
                            value={ attributes.ls }
                            onChange={ ( value ) => { setAttributes( { ls: value } ) } }
                        />
                        <PanelColorSettings
                            title={ __( 'Color Settings', 'porto-functionality' ) }
                            initialOpen={ false }
                            colorSettings={ [ {
                                label: __( 'Font Color', 'porto-functionality' ),
                                value: attributes.color,
                                onChange: ( value ) => { setAttributes( { color: value } ) }
                            } ] }
                        />
                    </PanelBody>
                </InspectorControls>
                <Disabled>
                    <div dangerouslySetInnerHTML={ { __html: style_inline } }>
                    </div>
                    <ServerSideRender
                        block={ name }
                        attributes={ {} }
                        EmptyResponsePlaceholder={ EmptyPlaceholder }
                    />
                </Disabled>
            </>
        )
    }
    registerBlockType( 'porto-single-product/porto-sp-excerpt', {
        title: __( 'Porto Single Product Excerpt', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-single-product',
        description: __(
            'Display a single product excerpt. It supports you to customize single product page as you mind.',
            'porto-functionality'
        ),
        attributes: {
            font_size: {
                type: 'string',
            },
            font_weight: {
                type: 'int',
            },
            line_height: {
                type: 'string',
            },
            ls: {
                type: 'string',
            },
            color: {
                type: 'string',
            }
        },
        supports: {
            customClassName: false
        },
        edit: PortoSpExcerpt,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );