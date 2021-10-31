/**
 * Header Builder Language/Currency Switcher
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
        ServerSideRender = wp.serverSideRender,
        Placeholder = wpComponents.Placeholder;

    const PortoTypographyControl = window.portoTypographyControl,
        EmptyPlaceholder = () => (
        <Placeholder
            icon='porto'
            label={ __( 'View Switcher', 'porto-functionality' ) }
        >
            { __(
                'Please select style.',
                'porto-functionality'
            ) }
        </Placeholder>
    );

    const PortoHBSwitcher = function ( { attributes, setAttributes, name } ) {

        let internalStyle = '',
            selector = 'currency-switcher' == attributes.type ? '#header .currency-switcher > li.menu-item > a' : '#header .view-switcher > li.menu-item > a';
        if ( attributes.font_size || attributes.font_weight || attributes.text_transform || attributes.line_height || attributes.letter_spacing || attributes.color ) {
            internalStyle += selector + '{';
            if ( attributes.font_size ) {
                let unitVal = attributes.font_size;
                const unit = unitVal.trim().replace( /[0-9.]/g, '' );
                if ( ! unit ) {
                    unitVal += 'px';
                }
                internalStyle += 'font-size:' + unitVal + ';';
            }
            if ( attributes.font_weight ) {
                internalStyle += 'font-weight:' + attributes.font_weight + ';';
            }
            if ( attributes.text_transform ) {
                internalStyle += 'text-transform:' + attributes.text_transform + ';';
            }
            if ( attributes.line_height ) {
                let unitVal = attributes.line_height;
                const unit = unitVal.trim().replace( /[0-9.]/g, '' );
                if ( ! unit && Number( unitVal ) > 3 ) {
                    unitVal += 'px';
                }
                internalStyle += 'line-height:' + unitVal + ';';
            }
            if ( attributes.letter_spacing ) {
                let unitVal = attributes.letter_spacing;
                const unit = unitVal.trim().replace( /[0-9.-]/g, '' );
                if ( ! unit ) {
                    unitVal += 'px';
                }
                internalStyle += 'letter-spacing:' + unitVal + ';';
            }
            if ( attributes.color ) {
                internalStyle += 'color:' + attributes.color;
            }
            internalStyle += '}';
        }

        if ( attributes.hover_color ) {
            selector = 'currency-switcher' == attributes.type ? '#header .currency-switcher > li.menu-item:hover > a' : '#header .view-switcher > li.menu-item:hover > a';
            internalStyle += ( selector + '{color:' + attributes.hover_color + '}' );
        }


        return (
            <>
                <InspectorControls key="inspector">
                    <SelectControl
                        label={ __( 'Type', 'porto-functionality' ) }
                        value={ attributes.type }
                        options={ [ { 'label': __( 'Select...', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Language Switcher', 'porto-functionality' ), 'value': 'language-switcher' }, { 'label': __( 'Currency Switcher', 'porto-functionality' ), 'value': 'currency-switcher' } ] }
                        onChange={ ( value ) => { setAttributes( { type: value } ); } }
                    />
                    <PortoTypographyControl
                        label={ __( 'Top Level Font', 'porto-functionality' ) }
                        value={ { fontSize: attributes.font_size, fontWeight: attributes.font_weight, textTransform: attributes.text_transform, lineHeight: attributes.line_height, letterSpacing: attributes.letter_spacing, color: attributes.color } }
                        options={ { fontFamily: false } }
                        onChange={ ( value ) => {
                            if ( typeof value.fontSize != 'undefined' ) {
                                setAttributes( { font_size: value.fontSize } );
                            }
                            if ( typeof value.fontWeight != 'undefined' ) {
                                setAttributes( { font_weight: value.fontWeight } );
                            }
                            if ( typeof value.textTransform != 'undefined' ) {
                                setAttributes( { text_transform: value.textTransform } );
                            }
                            if ( typeof value.lineHeight != 'undefined' ) {
                                setAttributes( { line_height: value.lineHeight } );
                            }
                            if ( typeof value.letterSpacing != 'undefined' ) {
                                setAttributes( { letter_spacing: value.letterSpacing } );
                            }
                            if ( typeof value.color != 'undefined' ) {
                                setAttributes( { color: value.color } );
                            } else {
                                setAttributes( { color: '' } );
                            }
                        } }
                    />
                    <PanelColorSettings
                        title={ __( 'Top Level Hover Color', 'porto-functionality' ) }
                        initialOpen={ false }
                        colorSettings={ [
                            {
                                label: __( 'Top Level Hover Color', 'porto-functionality' ),
                                value: attributes.hover_color,
                                onChange: function onChange( value ) {
                                    return setAttributes( { hover_color: value } );
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
                    <ServerSideRender
                        block={ name }
                        attributes={ { type: attributes.type, className: attributes.className } }
                        EmptyResponsePlaceholder={ EmptyPlaceholder }
                    />
                </Disabled>
            </>
        )
    }
    registerBlockType( 'porto-hb/porto-switcher', {
        title: __( 'Porto View Switcher', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-hb',
        description: __(
            'Display language or currency switcher in header',
            'porto-functionality'
        ),
        attributes: {
            type: {
                type: 'string',
                default: '',
            },
            font_size: {
                type: 'string',
            },
            font_weight: {
                type: 'int',
            },
            text_transform: {
                type: 'string',
            },
            line_height: {
                type: 'string',
            },
            letter_spacing: {
                type: 'string',
            },
            color: {
                type: 'string',
            },
            hover_color: {
                type: 'string',
            },
        },
        edit: PortoHBSwitcher,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );