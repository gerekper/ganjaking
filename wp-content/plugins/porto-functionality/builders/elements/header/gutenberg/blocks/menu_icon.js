/**
 * Header Builder Mobile Menu Toggle
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
        Disabled = wpComponents.Disabled,
        TextControl = wpComponents.TextControl,
        SelectControl = wpComponents.SelectControl,
        ServerSideRender = wp.serverSideRender;

    const PortoTypographyControl = window.portoTypographyControl;

    const PortoHBMenuIcon = function ( { attributes, setAttributes, name } ) {

        let internalStyle = '';

        if ( attributes.size || attributes.bg_color || attributes.color ) {
            internalStyle += '#header .mobile-toggle {';
            if ( attributes.size ) {
                let unitVal = attributes.size;
                const unit = unitVal.trim().replace( /[0-9.]/g, '' );
                if ( ! unit ) {
                    unitVal += 'px';
                }
                internalStyle += 'font-size:' + unitVal + ';';
            }
            if ( attributes.bg_color ) {
                internalStyle += 'background-color:' + attributes.bg_color + ';';
            }
            if ( attributes.color ) {
                internalStyle += 'color:' + attributes.color;
            }
            internalStyle += '}';
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
                                label: __( 'Background Color', 'porto-functionality' ),
                                value: attributes.bg_color,
                                onChange: function onChange( value ) {
                                    return setAttributes( { bg_color: value } );
                                }
                            },
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
                <Disabled>
                    { internalStyle && (
                        <style>
                            { internalStyle }
                        </style>
                    ) }
                    <div class="d-none d-lg-block">
                        {
                            __( 'Mobile Menu Toggle', 'porto-functionality' )
                        }
                    </div>
                    <ServerSideRender
                        block={ name }
                        attributes={ { icon_cl: attributes.icon_cl, className: attributes.className } }
                    />
                </Disabled>
            </>
        )
    }
    registerBlockType( 'porto-hb/porto-menu-icon', {
        title: __( 'Porto Mobile Menu Icon', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-hb',
        description: __(
            'Display mobile menu toggle in header',
            'porto-functionality'
        ),
        attributes: {
            icon_cl: {
                type: 'string',
            },
            size: {
                type: 'string',
            },
            bg_color: {
                type: 'string',
                default: '',
            },
            color: {
                type: 'string',
                default: '',
            },
        },
        edit: PortoHBMenuIcon,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );