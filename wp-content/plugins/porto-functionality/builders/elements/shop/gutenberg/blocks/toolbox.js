/**
 * Shop Builder - Tool Box
 * 
 * @since 6.1.0
 */
( function ( wpI18n, wpBlocks, wpBlockEditor ) {
    "use strict";

    var __ = wpI18n.__,
        registerBlockType = wpBlocks.registerBlockType,
        InspectorControls = wpBlockEditor.InspectorControls,
        InnerBlocks = wpBlockEditor.InnerBlocks;

    const PortoSBToolbox = function ( { attributes, setAttributes, name } ) {

        return (
            <>
                <InspectorControls key="inspector">
                </InspectorControls>
                <div className={ 'shop-loop-before shop-builder' + ( attributes.className ? ' '  + attributes.className : '' ) }>
                    <InnerBlocks allowedBlocks={ [ 'porto-sb/porto-sort', 'porto-sb/porto-count', 'porto-sb/porto-result', 'porto-sb/porto-toggle', 'porto-sb/porto-filter' ] } />
                </div>
            </>
        )
    }
    registerBlockType( 'porto-sb/porto-toolbox', {
        title: __( 'Tool Box', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-sb',
        description: __(
            'Tools box is a container which contains "Sort By", "Display Count", "Grid/List Toggle", etc.',
            'porto-functionality'
        ),
        attributes: {
        },
        edit: PortoSBToolbox,
        save: function () {
            return <InnerBlocks.Content />;
        }
    } );
} )( wp.i18n, wp.blocks, wp.blockEditor );