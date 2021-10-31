/**
 * Header Builder Search Form
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
        RangeControl = wpComponents.RangeControl,
        SelectControl = wpComponents.SelectControl,
        ServerSideRender = wp.serverSideRender,
        Placeholder = wpComponents.Placeholder;

    const EmptyPlaceholder = () => (
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

    const PortoHBSearchForm = function ( { attributes, setAttributes, name } ) {

        let internalStyle = '';
        if ( attributes.toggle_size || attributes.toggle_color ) {
            internalStyle += '#header .searchform button, #header .searchform-popup .search-toggle {';
            if ( attributes.toggle_size ) {
                let unitVal = attributes.toggle_size;
                const unit = unitVal.trim().replace( /[0-9.]/g, '' );
                if ( ! unit ) {
                    unitVal += 'px';
                }
                internalStyle += 'font-size:'+ unitVal + ';';
            }
            if ( attributes.toggle_color ) {
                internalStyle += 'color:' + attributes.toggle_color;
            }
            internalStyle += '}';
        }

        if ( attributes.input_size ) {
            let unitVal = attributes.input_size;
            const unit = unitVal.trim().replace( /[0-9.]/g, '' );
            if ( ! unit ) {
                unitVal += 'px';
            }
            internalStyle += '#header .searchform input, #header .searchform.searchform-cats input{width:' + unitVal + '}';
        }

        if ( attributes.height ) {
            let unitVal = attributes.height;
            const unit = unitVal.trim().replace( /[0-9.]/g, '' );
            if ( ! unit ) {
                unitVal += 'px';
            }
            internalStyle += '#header .searchform input, #header .searchform select, #header .searchform .selectric .label, #header .searchform button{height:' + unitVal + '; line-height:' + unitVal + '}';
        }

        if ( typeof attributes.border_width != 'undefined' || attributes.border_color ) {
            internalStyle += '#header .searchform {';
            if ( typeof attributes.border_width != 'undefined' ) {
                internalStyle += 'border-width:' + attributes.border_width + 'px;';
            }
            if ( attributes.border_color ) {
                internalStyle += 'border-color:' + attributes.border_color;
            }
            internalStyle += '}';
            if ( attributes.border_color ) {
                internalStyle += '#header .searchform-popup .search-toggle:after{border-bottom-color:' + attributes.border_color + '}';
            }
        }

        if ( typeof attributes.border_radius != 'undefined' && attributes.border_radius.length ) {
            let unitVal = attributes.border_radius;
            const unit = unitVal.trim().replace( /[0-9.]/g, '' );
            if ( ! unit ) {
                unitVal += 'px';
            }
            let border_radius_selectors = '#header .searchform { border-radius: %s }';
            if ( porto_block_vars.is_rtl ) {
                border_radius_selectors += '#header .searchform input { border-radius: 0 %s %s 0 }';
                border_radius_selectors += '#header .searchform button { border-radius: %s 0 0 %s }';
            } else {
                border_radius_selectors += '#header .searchform input { border-radius: %s 0 0 %s }';
                border_radius_selectors += '#header .searchform button { border-radius: 0 %s %s 0 }';
            }
            internalStyle += border_radius_selectors.replace( /%s/g, unitVal );
        }

        if ( attributes.divider_color ) {
            internalStyle += '#header .searchform input, #header .searchform select, #header .searchform .selectric, #header .searchform .selectric-hover .selectric, #header .searchform .selectric-open .selectric, #header .searchform .autocomplete-suggestions, #header .searchform .selectric-items{border-color:' + attributes.divider_color + '}';
        }

        return (
            <>
                <InspectorControls key="inspector">
                    <TextControl
                        label={ __( 'Placeholder Text', 'porto-functionality' ) }
                        value={ attributes.placeholder_text }
                        onChange={ ( value ) => { setAttributes( { placeholder_text: value } ); } }
                    />
                    <SelectControl
                        label={ __( 'Show category filter', 'porto-functionality' ) }
                        value={ attributes.category_filter }
                        options={ [ { 'label': __( 'Theme Options', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Yes', 'porto-functionality' ), 'value': 'yes' }, { 'label': __( 'No', 'porto-functionality' ), 'value': 'no' } ] }
                        onChange={ ( value ) => { setAttributes( { category_filter: value } ); } }
                    />
                    { 'yes' === attributes.category_filter && (
                        <SelectControl
                            label={ __( 'Show category filter on Mobile', 'porto-functionality' ) }
                            value={ attributes.category_filter_mobile }
                            options={ [ { 'label': __( 'Theme Options', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Yes', 'porto-functionality' ), 'value': 'yes' }, { 'label': __( 'No', 'porto-functionality' ), 'value': 'no' } ] }
                            onChange={ ( value ) => { setAttributes( { category_filter_mobile: value } ); } }
                        />
                    ) }
                    <SelectControl
                        label={ __( 'Popup Position', 'porto-functionality' ) }
                        value={ attributes.popup_pos }
                        options={ [ { 'label': __( 'Default', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Left', 'porto-functionality' ), 'value': 'left' }, { 'label': __( 'Center', 'porto-functionality' ), 'value': 'center' }, { 'label': __( 'Right', 'porto-functionality' ), 'value': 'right' } ] }
                        onChange={ ( value ) => { setAttributes( { popup_pos: value } ); } }
                        help={ __( 'This works for only "Popup 1" and "Popup 2" and "Form" search layout on mobile. You can change search layout using Porto -> Theme Options -> Header -> Search Form -> Search Layout.', 'porto-functionality' ) }
                    />
                    <TextControl
                        label={ __( 'Search Icon Size', 'porto-functionality' ) }
                        value={ attributes.toggle_size }
                        onChange={ ( value ) => { setAttributes( { toggle_size: value } ); } }
                        help={ __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ) }
                    />
                    <TextControl
                        label={ __( 'Input Box Width', 'porto-functionality' ) }
                        value={ attributes.input_size }
                        onChange={ ( value ) => { setAttributes( { input_size: value } ); } }
                        help={ __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ) }
                    />
                    <TextControl
                        label={ __( 'Height', 'porto-functionality' ) }
                        value={ attributes.height }
                        onChange={ ( value ) => { setAttributes( { height: value } ); } }
                        help={ __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ) }
                    />
                    <RangeControl
                        label={ __( 'Border Width (px)', 'porto-functionality' ) }
                        value={ attributes.border_width }
                        min="0"
                        max="40"
                        onChange={ ( value ) => { setAttributes( { border_width: value } ); } }
                    />
                    <TextControl
                        label={ __( 'Border Radius', 'porto-functionality' ) }
                        value={ attributes.border_radius }
                        onChange={ ( value ) => { setAttributes( { border_radius: value } ); } }
                        help={ __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto-functionality' ) }
                    />
                    <PanelColorSettings
                        title={ __( 'Color Settings', 'porto-functionality' ) }
                        initialOpen={ false }
                        colorSettings={ [
                            {
                                label: __( 'Search Icon Color', 'porto-functionality' ),
                                value: attributes.toggle_color,
                                onChange: function onChange( value ) {
                                    return setAttributes( { toggle_color: value } );
                                }
                            },
                            {
                                label: __( 'Border Color', 'porto-functionality' ),
                                value: attributes.border_color,
                                onChange: function onChange( value ) {
                                    return setAttributes( { border_color: value } );
                                }
                            },
                            {
                                label: __( 'Separator Color', 'porto-functionality' ),
                                value: attributes.divider_color,
                                onChange: function onChange( value ) {
                                    return setAttributes( { divider_color: value } );
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
                        attributes={ { placeholder_text: attributes.placeholder_text, category_filter: attributes.category_filter, category_filter_mobile: attributes.category_filter_mobile, popup_pos: attributes.popup_pos, className: attributes.className } }
                        EmptyResponsePlaceholder={ EmptyPlaceholder }
                    />
                </Disabled>
            </>
        )
    }
    registerBlockType( 'porto-hb/porto-search-form', {
        title: __( 'Porto Header Search Form', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-hb',
        description: __(
            'Display search form in header',
            'porto-functionality'
        ),
        attributes: {
            placeholder_text: {
                type: 'string',
            },
            category_filter: {
                type: 'string',
                default: '',
            },
            category_filter_mobile: {
                type: 'string',
                default: '',
            },
            popup_pos: {
                type: 'string',
                default: '',
            },
            toggle_size: {
                type: 'string',
            },
            toggle_color: {
                type: 'string',
            },
            input_size: {
                type: 'string',
            },
            height: {
                type: 'string',
            },
            border_width: {
                type: 'int',
            },
            border_color: {
                type: 'string',
            },
            border_radius: {
                type: 'string',
            },
        },
        edit: PortoHBSearchForm,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );