/**
 * Single Product Action
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
            label={ __( 'Single Product Action', 'porto-functionality' ) }
        >
            { __(
                'This block shows single product action. There are currently no discounted products in your store. Please refer to preview page.',
                'porto-functionality'
            ) }
        </Placeholder>
    );

    const PortoSpAction = function ( { attributes, setAttributes, name } ) {
        return (
            <>
                <InspectorControls key="inspector">
                    <PanelBody
                        title={ __( 'General', 'porto-functionality' ) }
                        initialOpen={ true }
                    >
                        <SelectControl
                            label={ __( 'Action', 'porto-functionality' ) }
                            value={ attributes.action }
                            options={ [
                                {
                                    label: 'woocommerce_before_single_product_summary',
                                    value: 'woocommerce_before_single_product_summary'
                                },
                                {
                                    label: 'woocommerce_single_product_summary',
                                    value: 'woocommerce_single_product_summary'
                                },
                                {
                                    label: 'woocommerce_after_single_product_summary',
                                    value: 'woocommerce_after_single_product_summary'
                                },
                                {
                                    label: 'porto_woocommerce_before_single_product_summary',
                                    value: 'porto_woocommerce_before_single_product_summary'
                                },
                                {
                                    label: 'porto_woocommerce_single_product_summary2',
                                    value: 'porto_woocommerce_single_product_summary2'
                                },
                                {
                                    label: 'woocommerce_share',
                                    value: 'woocommerce_share'
                                }
                            ] }
                            onChange={ ( value ) => {
                                setAttributes( { action: value } );
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
    registerBlockType( 'porto-single-product/porto-sp-actions', {
        title: __( 'Porto Single Product Action', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-single-product',
        description: __(
            'Display a single product action. It supports you to customize single product page as you mind.',
            'porto-functionality'
        ),
        attributes: {
            action: { type: 'string' }
        },
        supports: {
            customClassName: false
        },
        edit: PortoSpAction,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );