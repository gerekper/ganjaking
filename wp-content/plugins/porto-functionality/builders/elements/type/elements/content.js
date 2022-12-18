/**
 * Post Type Builder - Content
 * 
 * @since 2.3.0
 */

import PortoStyleOptionsControl, {portoGenerateStyleOptionsCSS} from '../../../../shortcodes/assets/blocks/controls/style-options';
import PortoTypographyControl, {portoGenerateTypographyCSS} from '../../../../shortcodes/assets/blocks/controls/typography';
import {portoAddHelperClasses} from '../../../../shortcodes/assets/blocks/controls/editor-extra-classes';

( function ( wpI18n, wpBlocks, wpBlockEditor, wpComponents ) {
    "use strict";

    const __ = wpI18n.__,
        registerBlockType = wpBlocks.registerBlockType,
        InspectorControls = wpBlockEditor.InspectorControls,
        SelectControl = wpComponents.SelectControl,
        TextControl = wpComponents.TextControl,
        RangeControl = wpComponents.RangeControl,
        ToggleControl = wpComponents.ToggleControl,
        Disabled = wpComponents.Disabled,
        PanelBody = wpComponents.PanelBody,
        ServerSideRender = wp.serverSideRender,
        useEffect = wp.element.useEffect;

    const PortoTBContent = function ( { attributes, setAttributes, name, clientId } ) {

        let selectorCls = 'tb-content';
        useEffect(
            () => {
                if ( ! attributes.el_class || -1 !== porto_tb_ids.indexOf( attributes.el_class ) ) { // new or just cloned
                    const new_cls = 'porto-tb-content-' + Math.ceil( Math.random() * 10000 );
                    attributes.el_class = new_cls;
                    setAttributes( { el_class: new_cls } );
                }
                porto_tb_ids.push( attributes.el_class );

                return () => {
                    const arr_index = porto_tb_ids.indexOf( attributes.el_class );
                    if ( -1 !== arr_index ) {
                        porto_tb_ids.splice( arr_index, 1 );
                    }
                }
            },
            [],
        );

        if ( attributes.el_class ) {
            selectorCls = attributes.el_class;
        }

        let attrs = { content_display: attributes.content_display, excerpt_length: attributes.excerpt_length, strip_html: attributes.strip_html, el_class: attributes.el_class, className: attributes.className };
        if ( porto_content_type ) {
            attrs.content_type = porto_content_type;
            if ( porto_content_type_value ) {
                attrs.content_type_value = porto_content_type_value;
            }
        }

        let internalStyle = '';
        const font_settings = Object.assign( {}, attributes.font_settings ),
            style_options = Object.assign( {}, attributes.style_options );

        if ( attributes.alignment || attributes.font_settings ) {
            let fontAtts = attributes.font_settings;
            fontAtts.alignment = attributes.alignment;
            internalStyle += portoGenerateTypographyCSS( fontAtts, selectorCls );
        }
        if ( style_options ) {
            internalStyle += portoGenerateStyleOptionsCSS( style_options, selectorCls );
        }

        // add helper classes to parent block element
        if ( attributes.className ) {
            portoAddHelperClasses( attributes.className, clientId );
        }

        return (
            <>
                <InspectorControls key="inspector">
                    <PanelBody title={ __( 'Layout', 'porto-functionality' ) } initialOpen={ true }>
                        <SelectControl
                            label={ __( 'Content Display', 'porto-functionality' ) }
                            value={ attributes.content_display }
                            options={ [ { 'label': __( 'Excerpt', 'porto-functionality' ), 'value': 'excerpt' }, { 'label': __( 'Content', 'porto-functionality' ), 'value': 'content' } ] }
                            onChange={ ( value ) => { setAttributes( { content_display: value } ); } }
                        />
                        { 'content' !== attributes.content_display && (
                            <RangeControl
                                label={ __( 'Excerpt Length', 'porto-functionality' ) }
                                value={ attributes.excerpt_length }
                                min="1"
                                max="100"
                                onChange={ ( value ) => { setAttributes( { excerpt_length: value } ); } }
                            />
                        ) }
                    </PanelBody>
                    <PanelBody title={ __( 'Font Settings', 'porto-functionality' ) } initialOpen={ false }>
                        <SelectControl
                            label={ __( 'Alignment', 'porto-functionality' ) }
                            value={ attributes.alignment }
                            options={ [ { 'label': __( 'Inherit', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Left', 'porto-functionality' ), 'value': 'left' }, { 'label': __( 'Center', 'porto-functionality' ), 'value': 'center' }, { 'label': __( 'Right', 'porto-functionality' ), 'value': 'right' }, { 'label': __( 'Justify', 'porto-functionality' ), 'value': 'justify' } ] }
                            onChange={ ( value ) => { setAttributes( { alignment: value } ); } }
                        />
                        <PortoTypographyControl
                            label={ __( 'Typography', 'porto-functionality' ) }
                            value={ font_settings }
                            options={ { } }
                            onChange={ ( value ) => {
                                setAttributes( { font_settings: value } );
                            } }
                        />
                    </PanelBody>
                    <PortoStyleOptionsControl
                        label={ __( 'Style Options', 'porto-functionality' ) }
                        value={ style_options }
                        options={ {} }
                        onChange={ ( value ) => { setAttributes( { style_options: value } ); } }
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
                        attributes={ attrs }
                    />
                </Disabled>
            </>
        )
    }
    registerBlockType( 'porto-tb/porto-content', {
        title: __( 'Content', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-tb',
        keywords: [ 'type builder', 'mini', 'card', 'post', 'text', 'excerpt', 'description', 'short' ],
        attributes: {
            content_display: {
                type: 'string',
            },
            excerpt_length: {
                type: 'int',
            },
            content_type: {
                type: 'string',
            },
            content_type_value: {
                type: 'string',
            },
            alignment: {
                type: 'string',
            },
            font_settings: {
                type: 'object',
                default: {},
            },
            style_options: {
                type: 'object',
            },
            el_class: {
                type: 'string',
            }
        },
        edit: PortoTBContent,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.blockEditor, wp.components );