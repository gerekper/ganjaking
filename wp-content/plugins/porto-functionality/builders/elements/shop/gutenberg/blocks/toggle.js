/**
 * Shop Builder - Grid / List Toggle
 * 
 * @since 6.1.0
 */
( function ( wpI18n, wpBlocks, wpBlockEditor ) {
    "use strict";

    const __ = wpI18n.__,
        registerBlockType = wpBlocks.registerBlockType,
        InspectorControls = wpBlockEditor.InspectorControls;

    const PortoSBToggle = function ( { attributes, setAttributes, name } ) {

        return (
            <>
                <InspectorControls key="inspector">
                </InspectorControls>
                <div className={ 'gridlist-toggle' + ( attributes.className ? ' ' + attributes.className : '' ) }>
                    <a href="#" id="grid" title="Grid View" className="active"></a>
                    <a href="#" id="list" title="List View"></a>
                </div>
            </>
        )
    }
    registerBlockType( 'porto-sb/porto-toggle', {
        title: __( 'Grid / List Toggle', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-sb',
        attributes: {
        },
        parent: ['porto-sb/porto-toolbox'],
        edit: PortoSBToggle,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.blockEditor );