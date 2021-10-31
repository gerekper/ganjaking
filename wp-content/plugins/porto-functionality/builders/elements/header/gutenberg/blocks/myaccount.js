/**
 * Header Builder My Account Icon
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

    const PortoHBMyaccount = function ( { attributes, setAttributes, name } ) {
        const inlineStyle = {};
        if ( attributes.size ) {
            let unitVal = attributes.size;
            const unit = unitVal.trim().replace( /[0-9.]/g, '' );
            if ( ! unit ) {
                unitVal += 'px';
            }
            inlineStyle.fontSize = unitVal;
        }
        if ( attributes.color ) {
            inlineStyle.color = attributes.color;
        }
        let cls = 'my-account';
        if ( attributes.className ) {
            cls += ' ' + attributes.className.trim();
        }

        return (
            <>
                <InspectorControls key="inspector">
                    <TextControl
                        label={ __( 'Icon Class (ex: fas fa-pencil-alt)', 'porto-functionality' ) }
                        value={ attributes.icon_cl }
                        onChange={ ( value ) => { setAttributes( { icon_cl: value } ); } }
                    />
                    <TextControl
                        label={ __( 'Font Size', 'porto-functionality' ) }
                        value={ attributes.size }
                        help={ __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ) }
                        onChange={ ( value ) => { setAttributes( { size: value } ); } }
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
                <a href="#" className={ cls } style={ inlineStyle }>
                    <i className={ attributes.icon_cl ? attributes.icon_cl : 'porto-icon-user-2' }>
                    </i>
                </a>
            </>
        )
    }
    registerBlockType( 'porto-hb/porto-myaccount', {
        title: __( 'Porto My Account Icon', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-hb',
        description: __(
            'Display myaccount icon in header',
            'porto-functionality'
        ),
        attributes: {
            icon_cl: {
                type: 'string',
            },
            size: {
                type: 'string',
            },
            color: {
                type: 'string',
                default: '',
            },
        },
        edit: PortoHBMyaccount,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );