/**
 * Shop Builder - Products
 * 
 * @since 6.1.0
 */
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents ) {
    "use strict";

    var __ = wpI18n.__,
        registerBlockType = wpBlocks.registerBlockType,
        PanelBody = wpComponents.PanelBody,
        InspectorControls = wpBlockEditor.InspectorControls,
        Disabled = wpComponents.Disabled,
        TextControl = wpComponents.TextControl,
        SelectControl = wpComponents.SelectControl,
        RangeControl = wpComponents.RangeControl,
        ToggleControl = wpComponents.ToggleControl,
        ServerSideRender = wp.serverSideRender,
        Placeholder = wpComponents.Placeholder;

    const PortoTypographyControl = window.portoTypographyControl,
        PortoImageChoose = window.portoImageControl,
        EmptyPlaceholder = () => (
            <Placeholder
                icon='porto'
                label={ __( 'Product Archive Products', 'porto-functionality' ) }
            >
                { __(
                    'This block shows products in shop pages.',
                    'porto-functionality'
                ) }
            </Placeholder>
        );

    const PortoSBProducts = function ( { attributes, setAttributes, name } ) {

        let internalStyle = '', shortcode_css = '';
        if ( attributes.title_google_font ) {
            shortcode_css += 'font-family:' + attributes.title_google_font + ';';
        }
        if ( attributes.title_font_size ) {
            let unitVal = attributes.title_font_size;
            const unit = unitVal.trim().replace( /[0-9.]/g, '' );
            if ( ! unit ) {
                unitVal += 'px';
            }
            shortcode_css += 'font-size:' + unitVal + ';';
        }
        if ( attributes.title_font_weight ) {
            shortcode_css += 'font-weight:' + Number( attributes.title_font_weight ) + ';';
        }
        if ( attributes.title_text_transform ) {
            shortcode_css += 'text-transform:' + attributes.title_text_transform + ';';
        }
        if ( attributes.title_line_height ) {
            let unitVal = attributes.title_line_height;
            const unit = unitVal.trim().replace( /[0-9.]/g, '' );
            if ( ! unit && unitVal > 3 ) {
                unitVal += 'px';
            }
            shortcode_css += 'line-height:' + unitVal + ';';
        }
        if ( attributes.title_ls ) {
            let unitVal = attributes.title_ls;
            const unit = unitVal.trim().replace( /[0-9.]/g, '' );
            if ( ! unit ) {
                unitVal += 'px';
            }
            shortcode_css += 'letter-spacing:' + unitVal + ';';
        }
        if ( attributes.title_color ) {
            shortcode_css += 'color:' + attributes.title_color;
        }
        if ( shortcode_css ) {
            internalStyle += 'div.archive-products li.product-col h3 {' + shortcode_css + '}';
        }

        shortcode_css = '';
        if ( attributes.price_font_size ) {
            let unitVal = attributes.price_font_size;
            const unit = unitVal.trim().replace( /[0-9.]/g, '' );
            if ( ! unit ) {
                unitVal += 'px';
            }
            shortcode_css += 'font-size:' + unitVal + ';';
        }
        if ( attributes.price_font_weight ) {
            shortcode_css += 'font-weight:' + attributes.price_font_weight + ';';
        }
        if ( attributes.price_line_height ) {
            let unitVal = attributes.price_line_height;
            const unit = unitVal.trim().replace( /[0-9.]/g, '' );
            if ( ! unit && unitVal > 3 ) {
                unitVal += 'px';
            }
            shortcode_css += 'line-height:' + unitVal + ';';
        }
        if ( attributes.price_ls ) {
            let unitVal = attributes.price_ls;
            const unit = unitVal.trim().replace( /[0-9.]/g, '' );
            if ( ! unit ) {
                unitVal += 'px';
            }
            shortcode_css += 'letter-spacing:' + unitVal + ';';
        }
        if ( attributes.price_color ) {
            shortcode_css += 'color:' + attributes.price_color;
        }
        if ( shortcode_css ) {
            internalStyle += 'div.archive-products li.product-col .price {' + shortcode_css + '}';
        }

        const grid_layouts = [];
        for ( var i = 1; i <= 14; i++ ) {
            grid_layouts.push( { alt: i, src: porto_block_vars.shortcodes_url + 'assets/images/cg/' + i + '.jpg' } );
        }

        return (
            <>
                <InspectorControls key="inspector">
                    <PanelBody title={ __( 'Layout', 'porto-functionality' ) } initialOpen={ true }>
                        <SelectControl
                            label={ __( 'View', 'porto-functionality' ) }
                            value={ attributes.view }
                            options={ [ { 'label': __( 'Grid', 'porto-functionality' ), 'value': 'grid' }, { 'label': __( 'List', 'porto-functionality' ), 'value': 'list' }, { 'label': __( 'Slider', 'porto-functionality' ), 'value': 'products-slider' }, { 'label': __( 'Creative Grid', 'porto-functionality' ), 'value': 'creative' }, { 'label': __( 'Grid - Divider Line', 'porto-functionality' ), 'value': 'divider' } ] }
                            onChange={ ( value ) => { setAttributes( { view: value } ); } }
                        />
                        { 'creative' === attributes.view && (
                            <PortoImageChoose
                                label={ __( 'Grid Layout', 'porto-functionality' ) }
                                options={ grid_layouts }
                                value={ attributes.grid_layout }
                                onChange={ ( value ) => {
                                    setAttributes( { grid_layout: value } );
                                } }
                            />
                        ) }
                        { 'creative' === attributes.view && (
                            <TextControl
                                label={ __( 'Grid Height', 'porto-functionality' ) }
                                value={ attributes.grid_height }
                                onChange={ ( value ) => { setAttributes( { grid_height: value } ); } }
                            />
                        ) }
                        { ( 'grid' === attributes.view || 'creative' === attributes.view || 'products-slider' === attributes.view ) && (
                            <RangeControl
                                label={ __( 'Column Spacing (px)', 'porto-functionality' ) }
                                help={ __( 'Leave blank if you use theme default value.', 'porto-functionality' ) }
                                value={ attributes.spacing }
                                min="0"
                                max="100"
                                onChange={ ( value ) => { setAttributes( { spacing: value } ); } }
                            />
                        ) }
                        { ( 'grid' == attributes.view || 'divider' == attributes.view || 'products-slider' == attributes.view ) && (
                            <RangeControl
                                label={ __( 'Columns', 'porto-functionality' ) }
                                value={ attributes.columns }
                                min="1"
                                max="8"
                                onChange={ ( value ) => { setAttributes( { columns: value } ); } }
                            />
                        ) }
                        { ( 'grid' == attributes.view || 'list' == attributes.view || 'divider' == attributes.view || 'products-slider' == attributes.view ) && (
                            <SelectControl
                                label={ __( 'Columns on mobile ( <= 575px )', 'porto-functionality' ) }
                                value={ attributes.columns_mobile }
                                options={ [ { label: __( 'Default', 'porto-functionality' ), value: '' }, { label: '1', value: '1' }, { label: '2', value: '2' }, { label: '3', value: '3' } ] }
                                onChange={ ( value ) => { setAttributes( { columns_mobile: value } ); } }
                            />
                        ) }
                        <SelectControl
                            label={ __( 'Add Links Position', 'porto-functionality' ) }
                            value={ attributes.addlinks_pos }
                            options={ porto_block_vars.product_layouts }
                            onChange={ ( value ) => { setAttributes( { addlinks_pos: value } ); } }
                        />
                        { ( 'onimage2' === attributes.addlinks_pos || 'onimage3' === attributes.addlinks_pos ) && (
                            <RangeControl
                                label={ __( 'Overlay Background Opacity (%)', 'porto-functionality' ) }
                                value={ attributes.overlay_bg_opacity }
                                min="0"
                                max="100"
                                onChange={ ( value ) => { setAttributes( { overlay_bg_opacity: value } ); } }
                            />
                        ) }
                        { 'list' !== attributes.view && (
                            <SelectControl
                                label={ __( 'Image Size', 'porto-functionality' ) }
                                value={ attributes.image_size }
                                options={ porto_block_vars.image_sizes }
                                onChange={ ( value ) => { setAttributes( { image_size: value } ); } }
                            />
                        ) }
                    </PanelBody>
                    { 'products-slider' === attributes.view && (
                        <PanelBody title={ __( 'Slider Options', 'porto-functionality' ) } initialOpen={ false }>
                            { attributes.slider_config && (
                                <ToggleControl
                                    label={ __( 'Show Slider Navigation', 'porto-functionality' ) }
                                    checked={ attributes.navigation }
                                    onChange={ ( value ) => { setAttributes( { navigation: value } ); } }
                                />
                            ) }
                            { attributes.navigation && (
                                <SelectControl
                                    label={ __( 'Nav Position', 'porto-functionality' ) }
                                    value={ attributes.nav_pos }
                                    options={ [ { 'label': __( 'Middle', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Middle of Images', 'porto-functionality' ), 'value': 'nav-center-images-only' }, { 'label': __( 'Top', 'porto-functionality' ), 'value': 'show-nav-title' }, { 'label': __( 'Bottom', 'porto-functionality' ), 'value': 'nav-bottom' } ] }
                                    onChange={ ( value ) => { setAttributes( { nav_pos: value } ); } }
                                />
                            ) }
                            { attributes.navigation && ( attributes.nav_pos === '' || attributes.nav_pos === 'nav-center-images-only' ) && (
                                <SelectControl
                                    label={ __( 'Nav Inside/Outside?', 'porto-functionality' ) }
                                    value={ attributes.nav_pos2 }
                                    options={ [ { 'label': __( 'Default', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Inside', 'porto-functionality' ), 'value': 'nav-pos-inside' }, { 'label': __( 'Outside', 'porto-functionality' ), 'value': 'nav-pos-outside' } ] }
                                    onChange={ ( value ) => { setAttributes( { nav_pos2: value } ); } }
                                />
                            ) }
                            { attributes.navigation && ( attributes.nav_pos === '' || attributes.nav_pos === 'nav-bottom' || attributes.nav_pos === 'nav-center-images-only' ) && (
                                <SelectControl
                                    label={ __( 'Nav Type', 'porto-functionality' ) }
                                    value={ attributes.nav_type }
                                    options={ porto_block_vars.carousel_nav_types }
                                    onChange={ ( value ) => { setAttributes( { nav_type: value } ); } }
                                />
                            ) }
                            { attributes.navigation && (
                                <ToggleControl
                                    label={ __( 'Show Nav on Hover', 'porto-functionality' ) }
                                    checked={ attributes.show_nav_hover }
                                    onChange={ ( value ) => { setAttributes( { show_nav_hover: value } ); } }
                                />
                            ) }
                            { (
                                <ToggleControl
                                    label={ __( 'Show Slider Pagination', 'porto-functionality' ) }
                                    checked={ attributes.pagination }
                                    onChange={ ( value ) => { setAttributes( { pagination: value } ); } }
                                />
                            ) }
                            { attributes.pagination && (
                                <SelectControl
                                    label={ __( 'Dots Position', 'porto-functionality' ) }
                                    value={ attributes.dots_pos }
                                    options={ [ { 'label': __( 'Bottom', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Top right', 'porto-functionality' ), 'value': 'show-dots-title-right' } ] }
                                    onChange={ ( value ) => { setAttributes( { dots_pos: value } ); } }
                                />
                            ) }
                            { attributes.pagination && (
                                <SelectControl
                                    label={ __( 'Dots Style', 'porto-functionality' ) }
                                    value={ attributes.dots_style }
                                    options={ [ { 'label': __( 'Default', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Circle inner dot', 'porto-functionality' ), 'value': 'dots-style-1' } ] }
                                    onChange={ ( value ) => { setAttributes( { dots_style: value } ); } }
                                />
                            ) }
                            <SelectControl
                                label={ __( 'Auto Play', 'porto-functionality' ) }
                                value={ attributes.autoplay }
                                options={ [ { 'label': __( 'Theme Options', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Yes', 'porto-functionality' ), 'value': 'yes' }, { 'label': __( 'No', 'porto-functionality' ), 'value': 'no' } ] }
                                onChange={ ( value ) => { setAttributes( { autoplay: value } ); } }
                            />
                            <RangeControl
                                label={ __( 'Auto Play Timeout (ms)', 'porto-functionality' ) }
                                value={ attributes.autoplay_timeout }
                                min="1000"
                                max="20000"
                                step="500"
                                onChange={ ( value ) => { setAttributes( { autoplay_timeout: value } ); } }
                            />
                        </PanelBody>
                    ) }
                    <PanelBody title={ __( 'Style', 'porto-functionality' ) } initialOpen={ false }>
                        <PortoTypographyControl
                            label={ __( 'Title Font', 'porto-functionality' ) }
                            value={ { fontFamily: attributes.title_google_font, fontSize: attributes.title_font_size, fontWeight: attributes.title_font_weight, textTransform: attributes.title_text_transform, lineHeight: attributes.title_line_height, letterSpacing: attributes.title_ls, color: attributes.title_color } }
                            onChange={ ( value ) => {
                                if ( typeof value.fontFamily != 'undefined' ) {
                                    setAttributes( { title_google_font: value.fontFamily } );
                                }
                                if ( typeof value.fontSize != 'undefined' ) {
                                    setAttributes( { title_font_size: value.fontSize } );
                                }
                                if ( typeof value.fontWeight != 'undefined' ) {
                                    setAttributes( { title_font_weight: value.fontWeight } );
                                }
                                if ( typeof value.textTransform != 'undefined' ) {
                                    setAttributes( { title_text_transform: value.textTransform } );
                                }
                                if ( typeof value.lineHeight != 'undefined' ) {
                                    setAttributes( { title_line_height: value.lineHeight } );
                                }
                                if ( typeof value.letterSpacing != 'undefined' ) {
                                    setAttributes( { title_ls: value.letterSpacing } );
                                }
                                if ( typeof value.color != 'undefined' ) {
                                    setAttributes( { title_color: value.color } );
                                } else {
                                    setAttributes( { title_color: '' } );
                                }
                            } }
                        />
                        <PortoTypographyControl
                            label={ __( 'Price Font', 'porto-functionality' ) }
                            value={ { fontSize: attributes.price_font_size, fontWeight: attributes.price_font_weight, lineHeight: attributes.price_line_height, letterSpacing: attributes.price_ls, color: attributes.price_color } }
                            options={ { fontFamily: false, textTransform: false } }
                            onChange={ ( value ) => {
                                if ( typeof value.fontSize != 'undefined' ) {
                                    setAttributes( { price_font_size: value.fontSize } );
                                }
                                if ( typeof value.fontWeight != 'undefined' ) {
                                    setAttributes( { price_font_weight: value.fontWeight } );
                                }
                                if ( typeof value.lineHeight != 'undefined' ) {
                                    setAttributes( { price_line_height: value.lineHeight } );
                                }
                                if ( typeof value.letterSpacing != 'undefined' ) {
                                    setAttributes( { price_ls: value.letterSpacing } );
                                }
                                if ( typeof value.color != 'undefined' ) {
                                    setAttributes( { price_color: value.color } );
                                } else {
                                    setAttributes( { price_color: '' } );
                                }
                            } }
                        />
                    </PanelBody>
                </InspectorControls>
                <Disabled>
                    { internalStyle && (
                        <style>
                            { internalStyle }
                        </style>
                    ) }
                    <ServerSideRender
                        block={ name }
                        attributes={ { view: attributes.view, grid_layout: attributes.grid_layout, grid_height: attributes.grid_height, spacing: attributes.spacing, columns: attributes.columns, columns_mobile: attributes.columns_mobile, addlinks_pos: attributes.addlinks_pos, overlay_bg_opacity: attributes.overlay_bg_opacity, image_size: attributes.image_size, className: attributes.className } }
                        EmptyResponsePlaceholder={ EmptyPlaceholder }
                    />
                </Disabled>
            </>
        )
    }
    registerBlockType( 'porto-sb/porto-products', {
        title: __( 'Archive Products', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-sb',
        description: __(
            'Display products in product archives',
            'porto-functionality'
        ),
        attributes: {
            view: {
                type: 'string',
                default: 'grid',
            },
            grid_layout: {
                type: 'int',
                default: 1,
            },
            grid_height: {
                type: 'string',
                default: '600px',
            },
            spacing: {
                type: 'int',
            },
            columns: {
                type: 'int',
                default: 4,
            },
            columns_mobile: {
                type: 'int',
            },
            addlinks_pos: {
                type: 'string',
            },
            overlay_bg_opacity: {
                type: 'int',
                default: 30,
            },
            image_size: {
                type: 'string',
                default: '',
            },
            navigation: {
                type: 'boolean',
                default: true
            },
            nav_pos: {
                type: 'string',
                default: ''
            },
            nav_pos2: {
                type: 'string'
            },
            nav_type: {
                type: 'string'
            },
            show_nav_hover: {
                type: 'boolean',
                default: false,
            },
            pagination: {
                type: 'boolean',
                default: false,
            },
            dots_pos: {
                type: 'string'
            },
            dots_style: {
                type: 'string'
            },
            autoplay: {
                type: 'boolean',
                default: false,
            },
            autoplay_timeout: {
                type: 'int',
                default: 5000,
            },
            title_google_font: {
                type: 'string',
            },
            title_font_size: {
                type: 'string',
            },
            title_font_weight: {
                type: 'int',
            },
            title_text_transform: {
                type: 'string',
            },
            title_line_height: {
                type: 'string',
            },
            title_ls: {
                type: 'string',
            },
            title_color: {
                type: 'string',
                default: '',
            },
            price_font_size: {
                type: 'string',
            },
            price_font_weight: {
                type: 'int',
            },
            price_line_height: {
                type: 'string',
            },
            price_ls: {
                type: 'string',
            },
            price_color: {
                type: 'string',
            },
        },
        edit: PortoSBProducts,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components );