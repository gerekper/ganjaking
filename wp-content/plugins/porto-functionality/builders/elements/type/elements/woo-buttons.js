/**
 * Post Type Builder - WooCommerce Links
 * 
 * @since 2.3.0
 */
import PortoStyleOptionsControl, { portoGenerateStyleOptionsCSS } from '../../../../shortcodes/assets/blocks/controls/style-options';
import PortoTypographyControl, { portoGenerateTypographyCSS } from '../../../../shortcodes/assets/blocks/controls/typography';
import { portoAddHelperClasses } from '../../../../shortcodes/assets/blocks/controls/editor-extra-classes';

( function( wpI18n, wpBlocks, wpBlockEditor, wpComponents ) {
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

    const PortoTBWooButtons = function( { attributes, setAttributes, name, clientId } ) {

        useEffect(
            () => {
                if ( !attributes.el_class || -1 !== porto_tb_ids.indexOf( attributes.el_class ) ) { // new or just cloned
                    const new_cls = 'porto-tb-woo-buttons-' + Math.ceil( Math.random() * 10000 );
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

        let attrs = { link_source: attributes.link_source, show_quantity_input: attributes.show_quantity_input, hide_title: attributes.hide_title, icon_cls: attributes.icon_cls, icon_pos: attributes.icon_pos, el_class: attributes.el_class, className: attributes.className };
        if ( porto_content_type ) {
            attrs.content_type = porto_content_type;
            if ( porto_content_type_value ) {
                attrs.content_type_value = porto_content_type_value;
            }
        }

        let internalStyle = '',
            font_settings = Object.assign( {}, attributes.font_settings );

        const style_options = Object.assign( {}, attributes.style_options );
        let selectorCls;
        if ( attributes.el_class ) {
            selectorCls = 'editor-styles-wrapper .' + attributes.el_class;
        }
        if ( attributes.show_quantity_input && attributes.spacing && attributes.el_class ) {
            internalStyle += '.' + attributes.el_class + '{margin-' + ( porto_block_vars.is_rtl ? 'right:' : 'left:' ) + attributes.spacing + '}';
        }

        if ( attributes.font_settings ) {
            let fontAtts = attributes.font_settings;

            internalStyle += portoGenerateTypographyCSS( fontAtts, selectorCls );
        }

        // add helper classes to parent block element
        if ( attributes.className ) {
            portoAddHelperClasses( attributes.className, clientId );
        }

        let icon_cls_ex = 'porto-icon-shopping-cart';
        if ( 'compare' === attributes.link_source ) {
            icon_cls_ex = 'porto-icon-compare';
        } else if ( 'quickview' === attributes.link_source ) {
            icon_cls_ex = 'porto-icon-search';
        } else if ( 'wishlist' == attributes.link_source ) {
            icon_cls_ex = 'porto-icon-wishlist';
        }

        return (
            <>
                <InspectorControls key="inspector">
                    <SelectControl
                        label={ __( 'Link Source', 'porto-functionality' ) }
                        value={ attributes.link_source }
                        options={ [{ 'label': __( 'Select...', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Add to cart', 'porto-functionality' ), 'value': 'cart' }, { 'label': __( 'Add to wishlist', 'porto-functionality' ), 'value': 'wishlist' }, { 'label': __( 'Compare', 'porto-functionality' ), 'value': 'compare' }, { 'label': __( 'Quick View', 'porto-functionality' ), 'value': 'quickview' }, { 'label': __( 'Image / Color Swatch', 'porto-functionality' ), 'value': 'swatch' }] }
                        onChange={ ( value ) => { setAttributes( { link_source: value } ); } }
                    />
                    { 'cart' == attributes.link_source && (
                        <ToggleControl
                            label={ __( 'Show Quantity Input', 'porto-functionality' ) }
                            checked={ attributes.show_quantity_input }
                            onChange={ ( value ) => { setAttributes( { show_quantity_input: value } ); } }
                        />
                    ) }
                    { 'wishlist' !== attributes.link_source && 'swatch' !== attributes.link_source && (
                        <ToggleControl
                            label={ __( 'Hide Title', 'porto-functionality' ) }
                            checked={ attributes.hide_title }
                            onChange={ ( value ) => { setAttributes( { hide_title: value } ); } }
                        />
                    ) }
                    { 'wishlist' != attributes.link_source && (
                        <TextControl
                            label={ __( 'Icon Class (ex: %s)', 'porto-functionality' ).replace( '%s', icon_cls_ex ) }
                            value={ attributes.icon_cls }
                            onChange={ ( value ) => { setAttributes( { icon_cls: value } ); } }
                        />
                    ) }
                    { 'cart' === attributes.link_source && (
                        <TextControl
                            label={ __( 'Icon Class for variable product (ex: %s)', 'porto-functionality' ).replace( '%s', 'fas fa-arrow-right' ) }
                            value={ attributes.icon_cls_variable }
                            onChange={ ( value ) => { setAttributes( { icon_cls_variable: value } ); } }
                        />
                    ) }
                    { 'compare' === attributes.link_source && (
                        <TextControl
                            label={ __( 'Icon Class for Added status (ex: %s)', 'porto-functionality' ).replace( '%s', 'fas fa-check' ) }
                            value={ attributes.icon_cls_added }
                            onChange={ ( value ) => { setAttributes( { icon_cls_added: value } ); } }
                        />
                    ) }
                    { attrs.icon_cls && 'wishlist' != attributes.link_source && (
                        <SelectControl
                            label={ __( 'Icon Position', 'porto-functionality' ) }
                            value={ attributes.icon_pos }
                            options={ [{ label: __( 'Left', 'porto-functionality' ), value: 'left' }, { label: __( 'Right', 'porto-functionality' ), value: 'right' }] }
                            onChange={ ( value ) => { setAttributes( { icon_pos: value } ); } }
                        />
                    ) }
                    { attributes.show_quantity_input && (
                        <TextControl
                            label={ __( 'Spacing between quantity input and link', 'porto-functionality' ) }
                            value={ attrs.spacing }
                            help={ __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ) }
                            onChange={ ( value ) => { setAttributes( { spacing: value } ); } }
                        />
                    ) }
                    <PanelBody title={ __( 'Font Settings', 'porto-functionality' ) } initialOpen={ true }>
                        <PortoTypographyControl
                            label={ __( 'Typography', 'porto-functionality' ) }
                            value={ font_settings }
                            options={ {} }
                            onChange={ ( value ) => {
                                setAttributes( { font_settings: value } );
                            } }
                        />
                    </PanelBody>
                    <PortoStyleOptionsControl
                        label={ __( 'Style Options', 'porto-functionality' ) }
                        value={ style_options }
                        options={ { hoverOptions: true } }
                        onChange={ ( value ) => { setAttributes( { style_options: value } ); } }
                    />
                </InspectorControls>
                <Disabled>
                    <style>
                        { internalStyle }
                        { portoGenerateStyleOptionsCSS( style_options, selectorCls, clientId ) }
                    </style>
                    <ServerSideRender
                        block={ name }
                        attributes={ attrs }
                    />
                </Disabled>
            </>
        )
    };

    registerBlockType( 'porto-tb/porto-woo-buttons', {
        title: __( 'Woo Link', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-tb',
        keywords: ['type builder', 'mini', 'card', 'post', 'woocommerce', 'add to cart', 'quick view', 'compare', 'wishlist', 'yith', 'button', 'product', 'swatch', 'variation'],
        attributes: {
            content_type: {
                type: 'string',
            },
            content_type_value: {
                type: 'string',
            },
            link_source: {
                type: 'string',
            },
            show_quantity_input: {
                type: 'boolean',
            },
            hide_title: {
                type: 'boolean',
            },
            icon_cls: {
                type: 'string',
            },
            icon_cls_variable: {
                type: 'string',
            },
            icon_cls_added: {
                type: 'string',
            },
            icon_pos: {
                type: 'string',
            },
            spacing: {
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
        edit: PortoTBWooButtons,
        save: function() {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.blockEditor, wp.components );