/**
 * Shop Builder - Filter Toggle on mobile
 * 
 * @since 6.1.0
 */
( function ( wpI18n, wpBlocks, wpBlockEditor ) {
    "use strict";

    const __ = wpI18n.__,
        registerBlockType = wpBlocks.registerBlockType,
        InspectorControls = wpBlockEditor.InspectorControls;

    const PortoSBFilter = function ( { attributes, setAttributes, name } ) {

        return (
            <>
                <InspectorControls key="inspector">
                </InspectorControls>
                <a href="#" className="porto-product-filters-toggle sidebar-toggle d-inline-flex">
                    <svg data-name="Layer 3" id="Layer_3" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                        <line className="cls-1" x1="15" x2="26" y1="9" y2="9"></line>
                        <line className="cls-1" x1="6" x2="9" y1="9" y2="9"></line>
                        <line className="cls-1" x1="23" x2="26" y1="16" y2="16"></line>
                        <line className="cls-1" x1="6" x2="17" y1="16" y2="16"></line>
                        <line className="cls-1" x1="17" x2="26" y1="23" y2="23"></line>
                        <line className="cls-1" x1="6" x2="11" y1="23" y2="23"></line>
                        <path className="cls-2" d="M14.5,8.92A2.6,2.6,0,0,1,12,11.5,2.6,2.6,0,0,1,9.5,8.92a2.5,2.5,0,0,1,5,0Z"></path>
                        <path className="cls-2" d="M22.5,15.92a2.5,2.5,0,1,1-5,0,2.5,2.5,0,0,1,5,0Z"></path>
                        <path className="cls-3" d="M21,16a1,1,0,1,1-2,0,1,1,0,0,1,2,0Z"></path>
                        <path className="cls-2" d="M16.5,22.92A2.6,2.6,0,0,1,14,25.5a2.6,2.6,0,0,1-2.5-2.58,2.5,2.5,0,0,1,5,0Z"></path>
                    </svg>
                    <span>
                        { __( 'Filter', 'porto-functionality' ) }
                    </span>
                </a>
            </>
        )
    }
    registerBlockType( 'porto-sb/porto-filter', {
        title: __( 'Filter Toggle on mobile', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-sb',
        attributes: {
        },
        parent: ['porto-sb/porto-toolbox'],
        supports: {
            customClassName: false,
        },
        edit: PortoSBFilter,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.blockEditor );