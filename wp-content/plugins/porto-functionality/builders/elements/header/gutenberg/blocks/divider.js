/**
 * Header Builder Divider
 * 
 * @since 6.1.0
 */
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash ) {
    "use strict";

    var __ = wpI18n.__,
        registerBlockType = wpBlocks.registerBlockType,
        PanelBody = wpComponents.PanelBody,
        InspectorControls = wpBlockEditor.InspectorControls,
        PanelColorSettings = wpBlockEditor.PanelColorSettings,
        TextControl = wpComponents.TextControl;

    const PortoHBDivider = function ( { attributes, setAttributes, name } ) {
        const inlineStyle = {};
        if ( attributes.width ) {
            let unitVal = attributes.width;
            const unit = unitVal.trim().replace( /[0-9.]/g, '' );
            if ( ! unit ) {
                unitVal += 'px';
            }
            inlineStyle.borderLeftWidth = unitVal;
        }
        if ( attributes.height ) {
            let unitVal = attributes.height;
            const unit = unitVal.trim().replace( /[0-9.]/g, '' );
            if ( ! unit ) {
                unitVal += 'px';
            }
            inlineStyle.height = unitVal;
        }
        if ( attributes.color ) {
            inlineStyle.borderLeftColor = attributes.color;
        }
        let cls = 'separator';
        if ( attributes.className ) {
            cls += ' ' + attributes.className.trim();
        }
        return (
            <>
                <InspectorControls key="inspector">
                    <TextControl
                        label={ __( 'Width', 'porto-functionality' ) }
                        value={ attributes.width }
                        help={ __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ) }
                        onChange={ ( value ) => { setAttributes( { width: value } ); } }
                    />
                    <TextControl
                        label={ __( 'Height', 'porto-functionality' ) }
                        value={ attributes.height }
                        help={ __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ) }
                        onChange={ ( value ) => { setAttributes( { height: value } ); } }
                    />
                    <PanelColorSettings
                        title={ __( 'Color Settings', 'porto-functionality' ) }
                        initialOpen={ false }
                        colorSettings={ [
                            {
                                label: __( 'Icon Color', 'porto-functionality' ),
                                value: attributes.color,
                                onChange: function onChange( value ) {
                                    return setAttributes( { color: value } );
                                }
                            }
                        ] }
                    />
                </InspectorControls>
                <span className={ cls } style={ inlineStyle }>
                </span>
            </>
        )
    }
    registerBlockType( 'porto-hb/porto-divider', {
        title: __( 'Porto Vertical Divider', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-hb',
        description: __(
            'Display vertical divider in header',
            'porto-functionality'
        ),
        attributes: {
            width: {
                type: 'string',
            },
            height: {
                type: 'string',
            },
            color: {
                type: 'string',
                default: '',
            },
        },
        edit: PortoHBDivider,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );