/**
 * Header Builder Logo
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
        ServerSideRender = wp.serverSideRender;

    const PortoHBLogo = function ( { attributes, setAttributes, name } ) {
        return (
            <>
                <InspectorControls key="inspector">
                </InspectorControls>
                <Disabled>
                    <ServerSideRender
                        block={ name }
                        attributes={ attributes }
                    />
                </Disabled>
            </>
        )
    }
    registerBlockType( 'porto-hb/porto-logo', {
        title: __( 'Porto Header Logo', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-hb',
        description: __(
            'Display logo in header',
            'porto-functionality'
        ),
        attributes: {
            className: {
                type: 'string',
            }
        },
        edit: PortoHBLogo,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );