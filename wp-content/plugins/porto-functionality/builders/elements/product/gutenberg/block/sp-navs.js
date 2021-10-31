/**
 * Single Product Navs
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
            label={ __( 'Single Product Navigation', 'porto-functionality' ) }
        >
            { __(
                'This block shows single product navigation. There are currently no discounted products in your store. Please refer to preview page.',
                'porto-functionality'
            ) }
        </Placeholder>
    );

    const PortoSpNavs = function ( { attributes, setAttributes, name } ) {
        return (
            <>
                <InspectorControls key="inspector">
                    <PanelBody
                        title={ __( 'General', 'porto-functionality' ) }
                        initialOpen={ true }
                    >
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
    registerBlockType( 'porto-single-product/porto-sp-next-prev-nav', {
        title: __( 'Porto Single Product Navigation', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-single-product',
        description: __(
            'Display a single product navigation. It supports you to customize single product page as you mind.',
            'porto-functionality'
        ),
        supports: {
            customClassName: false
        },
        edit: PortoSpNavs,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );