/**
 * Single Product Rating
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
            label={ __( 'Single Product Rating', 'porto-functionality' ) }
        >
            { __(
                'This block shows single product rating. There are currently no discounted products in your store.',
                'porto-functionality'
            ) }
        </Placeholder>
    );

    const PortoSpRating = function ( { attributes, setAttributes, name, className } ) {
        let style_inline = '<style>';
        if ( attributes.font_size ) {
            let unit = attributes.font_size.replace( /[0-9.]/g, '' );
            let size = attributes.font_size;
            if ( !unit ) {
                size += 'px';
            }
            style_inline += '.block-editor .woocommerce-product-rating .star-rating{font-size:' + size + '}';
        }
        if ( attributes.bgcolor ) {
            style_inline += '.block-editor .woocommerce-product-rating .star-rating:before{color:' + attributes.bgcolor + '}';
        }
        if ( attributes.color ) {
            style_inline += '.block-editor .woocommerce-product-rating .star-rating span:before{color:' + attributes.color + '}';
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
                        <PanelColorSettings
                            title={ __( 'Background Star Color', 'porto-functionality' ) }
                            initialOpen={ false }
                            colorSettings={ [ {
                                label: __( 'Color', 'porto-functionality' ),
                                value: attributes.bgcolor,
                                onChange: ( value ) => { setAttributes( { bgcolor: value } ) }
                            } ] }
                        />
                        <PanelColorSettings
                            title={ __( 'Active Color', 'porto-functionality' ) }
                            initialOpen={ false }
                            colorSettings={ [ {
                                label: __( 'Color', 'porto-functionality' ),
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
    registerBlockType( 'porto-single-product/porto-sp-rating', {
        title: __( 'Porto Single Product Rating', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-single-product',
        description: __(
            'Display a single product rating. It supports you to customize single product page as you mind.',
            'porto-functionality'
        ),
        attributes: {
            font_size: {
                type: 'string',
            },
            bgcolor: {
                type: 'string',
            },
            color: {
                type: 'string',
            }
        },
        supports: {
            customClassName: false
        },
        edit: PortoSpRating,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );