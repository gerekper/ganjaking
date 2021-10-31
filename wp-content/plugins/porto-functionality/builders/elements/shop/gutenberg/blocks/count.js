/**
 * Shop Builder - Count Per Page
 * 
 * @since 6.1.0
 */
( function ( wpI18n, wpBlocks, wpBlockEditor ) {
    "use strict";

    const __ = wpI18n.__,
        registerBlockType = wpBlocks.registerBlockType,
        InspectorControls = wpBlockEditor.InspectorControls;

    const PortoSBCount = function ( { attributes, setAttributes, name } ) {

        return (
            <>
                <InspectorControls key="inspector">
                </InspectorControls>
                <div className={ 'woocommerce-viewing' + ( attributes.className ? ' ' + attributes.className : '' ) }>
                    <label>
                        { __( 'Show:', 'porto-functionality' ) }
                    </label>
                    <select className="count">
                        <option>12</option>
                    </select>
                </div>
            </>
        )
    }
    registerBlockType( 'porto-sb/porto-count', {
        title: __( 'Count Per Page', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-sb',
        attributes: {
        },
        parent: ['porto-sb/porto-toolbox'],
        edit: PortoSBCount,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.blockEditor );