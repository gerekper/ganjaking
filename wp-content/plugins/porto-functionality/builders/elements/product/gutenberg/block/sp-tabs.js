/**
 * Single Product Tabs
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
            label={ __( 'Single Product Tabs', 'porto-functionality' ) }
        >
            { __(
                'This block shows single product tabs. There are currently no discounted products in your store.',
                'porto-functionality'
            ) }
        </Placeholder>
    );

    const PortoSpTabs = function ( { attributes, setAttributes, name } ) {
        return (
            <>
                <InspectorControls key="inspector">
                    <PanelBody
                        title={ __( 'General', 'porto-functionality' ) }
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
                                    label: __( 'Vetical', 'porto-functionality' ),
                                    value: 'vertical'
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
    registerBlockType( 'porto-single-product/porto-sp-tabs', {
        title: __( 'Porto Single Product Tabs', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-single-product',
        description: __(
            'Display a single product tab. It supports you to customize single product page as you mind.',
            'porto-functionality'
        ),
        supports: {
            customClassName: false
        },
        attributes: {
            style: { type: 'string' }
        },
        edit: PortoSpTabs,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );