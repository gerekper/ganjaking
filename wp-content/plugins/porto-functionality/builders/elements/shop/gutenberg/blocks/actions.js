/**
 * Shop Builder - Hooks
 * 
 * @since 6.1.0
 */
( function ( wpI18n, wpBlocks, wpBlockEditor, wpComponents ) {
    "use strict";

    const __ = wpI18n.__,
        registerBlockType = wpBlocks.registerBlockType,
        InspectorControls = wpBlockEditor.InspectorControls,
        SelectControl = wpComponents.SelectControl;

    const PortoSBActions = function ( { attributes, setAttributes, name } ) {

        return (
            <>
                <InspectorControls key="inspector">
                    <SelectControl
                        label={ __( 'action', 'porto-functionality' ) }
                        value={ attributes.action }
                        options={ [ { 'label': 'woocommerce_before_shop_loop', 'value': 'woocommerce_before_shop_loop' }, { 'label': 'woocommerce_after_shop_loop', 'value': 'woocommerce_after_shop_loop' } ] }
                        onChange={ ( value ) => { setAttributes( { action: value } ); } }
                    />
                </InspectorControls>
                <div>
                    { attributes.action }
                </div>
            </>
        )
    }
    registerBlockType( 'porto-sb/porto-actions', {
        title: __( 'Shop Hooks', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-sb',
        attributes: {
            action: {
                type: 'string',
                default: 'woocommerce_before_shop_loop',
            },
        },
        edit: PortoSBActions,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.blockEditor, wp.components );