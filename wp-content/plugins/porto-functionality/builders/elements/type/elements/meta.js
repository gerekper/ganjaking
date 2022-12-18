/**
 * Post Type Builder - Meta element
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

    const tmp_options = document.getElementById( 'content_type_term' ).options, porto_all_terms = [];
    for (var i = 0; i < tmp_options.length; i++) {
        var option = tmp_options[i];
        if ( option.value ) {
            porto_all_terms.push( { label: option.innerText.trim(), value: option.value } );
        }
    }

    const PortoTBMeta = function ( { attributes, setAttributes, name, clientId } ) {

        let selectorCls = 'porto-tb-meta';
        useEffect(
            () => {
                if ( ! attributes.el_class || -1 !== porto_tb_ids.indexOf( attributes.el_class ) ) { // new or just cloned
                    const new_cls = 'porto-tb-meta-' + Math.ceil( Math.random() * 10000 );
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

        let attrs = { field: attributes.field, date_format: attributes.date_format, icon_cls: attributes.icon_cls, icon_pos: attributes.icon_pos, el_class: attributes.el_class, className: attributes.className };
        if ( porto_content_type ) {
            attrs.content_type = porto_content_type;
            if ( porto_content_type_value ) {
                attrs.content_type_value = porto_content_type_value;
            }
        }

        if ( attributes.font_settings && typeof attributes.font_settings.alignment != 'undefined' ) {
            delete attributes.font_settings.alignment;
        }

        let internalStyle = '',
            font_settings = Object.assign( {}, attributes.font_settings );

        const style_options = Object.assign( {}, attributes.style_options );

        if ( attributes.font_settings ) {
            let fontAtts = attributes.font_settings;

            internalStyle += portoGenerateTypographyCSS( fontAtts, selectorCls );
        }

        if ( attributes.spacing || 0 === attributes.spacing ) {
            if ( 'right' === attributes.icon_pos ) {
                internalStyle += '.' + selectorCls + ' .porto-tb-icon{margin-left:' + Number( attributes.spacing ) + 'px}';
            } else {
                internalStyle += '.' + selectorCls + ' .porto-tb-icon{margin-right:' + Number( attributes.spacing ) + 'px}';
            }
        }

         // add helper classes to parent block element
        if ( attributes.className ) {
            portoAddHelperClasses( attributes.className, clientId );
        }

        return (
            <>
                <InspectorControls key="inspector">
                    <SelectControl
                        label={ __( 'Field', 'porto-functionality' ) }
                        value={ attributes.field }
                        options={
                            [ { 'label': __( 'Author', 'porto-functionality' ), 'value': 'author' }, { 'label': __( 'Published Date', 'porto-functionality' ), 'value': 'published_date' }, { 'label': __( 'Modified Date', 'porto-functionality' ), 'value': 'modified_date' }, { 'label': __( 'Comments', 'porto-functionality' ), 'value': 'comments' }, { 'label': __( 'Comments Number', 'porto-functionality' ), 'value': 'comments_number' }, { 'label': __( 'Product SKU', 'porto-functionality' ), 'value': 'sku' } ].concat( porto_all_terms )
                        }
                        onChange={ ( value ) => { setAttributes( { field: value } ); } }
                    />
                    { ( 'published_date' === attrs.field || 'modified_date' === attrs.field ) && (
                        <TextControl
                            label={ __( 'Date Format', 'porto-functionality' ) }
                            help={ __( 'j = 1-31, F = January-December, M = Jan-Dec, m = 01-12, n = 1-12', 'porto-functionality' ) }
                            value={ attributes.date_format }
                            onChange={ ( value ) => { setAttributes( { date_format: value } ); } }
                        />
                    ) }
                    <TextControl
                        label={ __( 'Icon Class (ex: fas fa-pencil-alt)', 'porto-functionality' ) }
                        value={ attributes.icon_cls }
                        onChange={ ( value ) => { setAttributes( { icon_cls: value } ); } }
                    />
                    { attrs.icon_cls && (
                        <SelectControl
                            label={ __( 'Icon Position', 'porto-functionality' ) }
                            value={ attributes.icon_pos }
                            options={ [ { label: __( 'Left', 'porto-functionality' ), value: '' }, { label: __( 'Right', 'porto-functionality' ), value: 'right' } ] }
                            onChange={ ( value ) => { setAttributes( { icon_pos: value } ); } }
                        />
                    ) }
                    { attrs.icon_cls && (
                        <RangeControl
                            label={ __( 'Spacing (px)', 'porto-functionality' ) }
                            help={ __( 'Spacing between icon and meta', 'porto-functionality' ) }
                            value={ attributes.spacing }
                            min="0"
                            max="100"
                            allowReset={ true }
                            onChange={ ( value ) => { setAttributes( { spacing: value } ); } }
                        />
                    ) }
                    <PanelBody title={ __( 'Font Settings', 'porto-functionality' ) } initialOpen={ true }>
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
                    <style>
                        { internalStyle }
                        { portoGenerateStyleOptionsCSS( style_options, selectorCls ) }
                    </style>
                    <ServerSideRender
                        block={ name }
                        attributes={ attrs }
                    />
                </Disabled>
            </>
        )
    }
    registerBlockType( 'porto-tb/porto-meta', {
        title: __( 'Meta', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-tb',
        keywords: [ 'type builder', 'mini', 'card', 'post', 'author', 'categories', 'date' ],
        attributes: {
            content_type: {
                type: 'string',
            },
            content_type_value: {
                type: 'string',
            },
            field: {
                type: 'string',
            },
            date_format: {
                type: 'string',
            },
            icon_cls: {
                type: 'string',
            },
            icon_pos: {
                type: 'string',
            },
            spacing: {
                type: 'int',
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
        edit: PortoTBMeta,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.blockEditor, wp.components );