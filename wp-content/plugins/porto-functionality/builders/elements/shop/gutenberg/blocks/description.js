/**
 * Shop Builder - Archive description
 * 
 * @since 6.1.0
 */
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents ) {
    "use strict";

    var __ = wpI18n.__,
        registerBlockType = wpBlocks.registerBlockType,
        PanelBody = wpComponents.PanelBody,
        InspectorControls = wpBlockEditor.InspectorControls;

    const PortoTypographyControl = window.portoTypographyControl;

    const PortoSBDescription = function ( { attributes, setAttributes, name } ) {

        let inlineStyle = {};
        if ( attributes.font_size ) {
            let unitVal = attributes.font_size;
            const unit = unitVal.trim().replace( /[0-9.]/g, '' );
            if ( ! unit ) {
                unitVal += 'px';
            }
            inlineStyle.fontSize = unitVal;
        }
        if ( attributes.font_weight ) {
            inlineStyle.fontWeight = Number( attributes.font_weight );
        }
        if ( attributes.line_height ) {
            let unitVal = attributes.line_height;
            const unit = unitVal.trim().replace( /[0-9.]/g, '' );
            if ( ! unit && unitVal > 3 ) {
                unitVal += 'px';
            }
            inlineStyle.lineHeight = unitVal;
        }
        if ( attributes.ls ) {
            let unitVal = attributes.ls;
            const unit = unitVal.trim().replace( /[0-9.]/g, '' );
            if ( ! unit ) {
                unitVal += 'px';
            }
            inlineStyle.letterSpacing = unitVal;
        }
        if ( attributes.color ) {
            inlineStyle.color = attributes.color;
        }

        return (
            <>
                <InspectorControls key="inspector">
                    <PortoTypographyControl
                        label={ __( 'Typography', 'porto-functionality' ) }
                        value={ { fontSize: attributes.font_size, fontWeight: attributes.font_weight, lineHeight: attributes.line_height, letterSpacing: attributes.ls, color: attributes.color } }
                        options={ { fontFamily: false, textTransform: false } }
                        onChange={ ( value ) => {
                            if ( typeof value.fontSize != 'undefined' ) {
                                setAttributes( { font_size: value.fontSize } );
                            }
                            if ( typeof value.fontWeight != 'undefined' ) {
                                setAttributes( { font_weight: value.fontWeight } );
                            }
                            if ( typeof value.lineHeight != 'undefined' ) {
                                setAttributes( { line_height: value.lineHeight } );
                            }
                            if ( typeof value.letterSpacing != 'undefined' ) {
                                setAttributes( { ls: value.letterSpacing } );
                            }
                            if ( typeof value.color != 'undefined' ) {
                                setAttributes( { color: value.color } );
                            } else {
                                setAttributes( { color: '' } );
                            }
                        } }
                    />
                </InspectorControls>
                <div className={ 'entry-description' + ( attributes.className ? ' ' + attributes.className : '' ) } style={ inlineStyle }>
                    { __( 'Archive description', 'porto-functionality' ) }
                </div>
            </>
        )
    }
    registerBlockType( 'porto-sb/porto-description', {
        title: __( 'Archive description', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-sb',
        description: __(
            'Display description in product archives',
            'porto-functionality'
        ),
        attributes: {
            font_size: {
                type: 'string',
            },
            font_weight: {
                type: 'int',
            },
            line_height: {
                type: 'string',
            },
            ls: {
                type: 'string',
            },
            color: {
                type: 'string',
                default: '',
            },
        },
        edit: PortoSBDescription,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components );