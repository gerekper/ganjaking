/**
 * Single Product Image
 * 
 * @since 6.1.0
 */
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash ) {
    "use strict";

    var __ = wpI18n.__,
        registerBlockType = wpBlocks.registerBlockType,
        SelectControl = wpComponents.SelectControl,
        PanelBody = wpComponents.PanelBody,
        InspectorControls = wpBlockEditor.InspectorControls,
        Disabled = wpComponents.Disabled,
        Placeholder = wpComponents.Placeholder,
        ServerSideRender = wp.serverSideRender;

    const EmptyPlaceholder = () => (
        <Placeholder
            icon='porto'
            label={ __( 'Single Product Image', 'porto-functionality' ) }
        >
            { __(
                'This block shows single product image. There are currently no discounted products in your store.',
                'porto-functionality'
            ) }
        </Placeholder>
    );

    const PortoSpImage = function ( { attributes, setAttributes, name } ) {
        return (
            <>
                <InspectorControls key="inspector">
                    <PanelBody
                        title={ __( 'Product Image', 'porto-functionality' ) }
                        initialOpen={ true }
                    >
                        <SelectControl
                            label={ __( 'Style', 'porto-functionality' ) }
                            value={ attributes.style }
                            options={ [
                                {
                                    label: __( 'Default', 'porto-functionality' ),
                                    value: ''
                                },
                                {
                                    label: __( 'Extended', 'porto-functionality' ),
                                    value: 'extended'
                                },
                                {
                                    label: __( 'Grid Images', 'porto-functionality' ),
                                    value: 'grid'
                                },
                                {
                                    label: __( 'Thumbs on Image', 'porto-functionality' ),
                                    value: 'full_width'
                                },
                                {
                                    label: __( 'List Images', 'porto-functionality' ),
                                    value: 'sticky_info'
                                },
                                {
                                    label: __( 'Left Thumbs 1', 'porto-functionality' ),
                                    value: 'transparent'
                                },
                                {
                                    label: __( 'Left Thumbs 2', 'porto-functionality' ),
                                    value: 'centered_vertical_zoom'
                                }
                            ] }
                            onChange={ ( value ) => {
                                setAttributes( { style: value } );
                            } } />
                    </PanelBody>
                </InspectorControls>
                <Disabled>
                    <ServerSideRender
                        block={ name }
                        attributes={ attributes }
                        EmptyResponsePlaceholder={ EmptyPlaceholder }
                    />
                </Disabled>
            </>
        )
    }
    registerBlockType( 'porto-single-product/porto-sp-image', {
        title: __( 'Porto Single Product Image', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-single-product',
        description: __(
            'Display a single product image. It supports you to customize single product page as you mind.',
            'porto-functionality'
        ),
        supports: {
            customClassName: false
        },
        attributes: {
            style: { type: 'string' }
        },
        edit: PortoSpImage,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );