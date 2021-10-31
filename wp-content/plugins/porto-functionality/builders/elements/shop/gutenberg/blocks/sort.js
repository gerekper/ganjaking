/**
 * Shop Builder - Sort By
 * 
 * @since 6.1.0
 */
( function ( wpI18n, wpBlocks, wpBlockEditor ) {
    "use strict";

    const __ = wpI18n.__,
        registerBlockType = wpBlocks.registerBlockType,
        InspectorControls = wpBlockEditor.InspectorControls;

    const PortoSBSort = function ( { attributes, setAttributes, name } ) {

        return (
            <>
                <InspectorControls key="inspector">
                </InspectorControls>
                <div className={ 'woocommerce-ordering' + ( attributes.className ? ' ' + attributes.className : '' ) }>
                    <label>
                        { __( 'Sort By:', 'porto-functionality' ) }
                    </label>
                    <select className="orderby">
                        <option>
                            { __( 'Default sorting', 'woocommerce' ) }
                        </option>
                    </select>
                </div>
            </>
        )
    }
    registerBlockType( 'porto-sb/porto-sort', {
        title: __( 'Porto Shop Sort By', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-sb',
        attributes: {
        },
        parent: ['porto-sb/porto-toolbox'],
        edit: PortoSBSort,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.blockEditor );