/**
 * Single Product Related
 * 
 * @since 6.1.0
 */
( function ( wpI18n, wpBlocks, wpElement, wpEditor, wpBlockEditor, wpComponents, wpData, lodash ) {
    "use strict";

    var __ = wpI18n.__,
        registerBlockType = wpBlocks.registerBlockType,
        SelectControl = wpComponents.SelectControl,
        TextControl = wpComponents.TextControl,
        PanelBody = wpComponents.PanelBody,
        InspectorControls = wpBlockEditor.InspectorControls,
        Disabled = wpComponents.Disabled,
        Placeholder = wpComponents.Placeholder,
        RangeControl = wpComponents.RangeControl,
        ToggleControl = wpComponents.ToggleControl,
        BlockControls = wpBlockEditor.BlockControls,
        Toolbar = wpComponents.Toolbar,
        ServerSideRender = wp.serverSideRender,
        el = wpElement.createElement;

    const EmptyPlaceholder = () => (
        <Placeholder
            icon='porto'
            label={ __( 'Single Product Related Products', 'porto-functionality' ) }
        >
            { __(
                'This block shows single product related products. There are currently no discounted products in your store. Please refer to preview page.',
                'porto-functionality'
            ) }
        </Placeholder>
    );
    const PortoImageChoose = window.portoImageControl;

    const PortoSpRelated = function ( { attributes, setAttributes, name } ) {

        let attrs = attributes;

        var viewControls = [ {
            icon: 'grid-view',
            title: __( 'Grid', 'porto-functionality' ),
            onClick: function onClick() {
                return setAttributes( { view: 'grid' } );
            },
            isActive: attrs.view === 'grid'
        }, {
            icon: 'slides',
            title: __( 'Slider', 'porto-functionality' ),
            onClick: function onClick() {
                return setAttributes( { view: 'products-slider' } );
            },
            isActive: attrs.view === 'products-slider'
        }, {
            icon: 'media-spreadsheet',
            title: __( 'Creative Grid', 'porto-functionality' ),
            onClick: function onClick() {
                return setAttributes( { view: 'creative' } );
            },
            isActive: attrs.view === 'creative'
        } ];

        const grid_layouts = [];
        for ( var i = 1; i <= 14; i++ ) {
            grid_layouts.push( { alt: i, src: porto_block_vars.shortcodes_url + 'assets/images/cg/' + i + '.jpg' } );
        }

        return (
            <>
                {
                    el(
                        BlockControls,
                        null,
                        el( Toolbar, { controls: viewControls } )
                    )
                }
                {

                    el( InspectorControls, null,

                        el(
                            PanelBody,
                            { title: __( 'Selector', 'porto-functionality' ), initialOpen: true },
                            el( TextControl, {
                                label: __( 'Title', 'porto-functionality' ),
                                value: attrs.title,
                                onChange: function ( value ) { setAttributes( { title: value } ); },
                            } ),
                            attrs.title && el( SelectControl, {
                                label: __( 'Title Border Style', 'porto-functionality' ),
                                value: attrs.title_border_style,
                                options: [ { label: __( 'No Border', 'porto-functionality' ), value: '' }, { label: __( 'Bottom Border', 'porto-functionality' ), value: 'border-bottom' }, { label: __( 'Middle Border', 'porto-functionality' ), value: 'border-middle' } ],
                                onChange: ( value ) => { setAttributes( { title_border_style: value } ); },
                            } ),
                            el( SelectControl, {
                                label: __( 'Product Status', 'porto-functionality' ),
                                value: attrs.status,
                                options: porto_block_vars.status_values,
                                onChange: function onChange( value ) {
                                    return setAttributes( { status: value } );
                                }
                            } ),
                            el( RangeControl, {
                                label: __( 'Per page', 'porto-functionality' ),
                                value: attrs.count,
                                min: 1,
                                max: 100,
                                onChange: ( value ) => { setAttributes( { count: value } ); },
                            } ),
                            el( SelectControl, {
                                label: __( 'Order by', 'porto-functionality' ),
                                value: attrs.orderby,
                                options: porto_block_vars.orderby_values,
                                onChange: ( value ) => { setAttributes( { orderby: value } ); },
                            } ),
                            attrs.orderby != 'rating' && el( SelectControl, {
                                label: __( 'Order', 'porto-functionality' ),
                                value: attrs.order,
                                options: [ { label: __( 'Descending', 'porto-functionality' ), value: 'desc' }, { label: __( 'Ascending', 'porto-functionality' ), value: 'asc' } ],
                                onChange: ( value ) => { setAttributes( { order: value } ); },
                            } ),
                        ),
                        el(
                            PanelBody,
                            { title: __( 'Layout', 'porto-functionality' ), initialOpen: false },
                            el( ToggleControl, {
                                label: __( 'Show category filter', 'porto-functionality' ),
                                checked: attrs.category_filter,
                                onChange: ( value ) => { setAttributes( { category_filter: value } ); },
                            } ),
                            attrs.view != 'products-slider' && el( SelectControl, {
                                label: __( 'Pagination Style', 'porto-functionality' ),
                                value: attrs.pagination_style,
                                options: [ { label: __( 'No pagination', 'porto-functionality' ), value: '' }, { label: __( 'Default', 'porto-functionality' ), value: 'default' }, { label: __( 'Load more', 'porto-functionality' ), value: 'load_more' } ],
                                onChange: ( value ) => { setAttributes( { pagination_style: value } ); },
                            } ),
                            ( 'grid' == attrs.view || 'products-slider' == attrs.view ) && el( RangeControl, {
                                label: __( 'Columns', 'porto-functionality' ),
                                value: attrs.columns,
                                min: 1,
                                max: 8,
                                onChange: ( value ) => { setAttributes( { columns: value } ); },
                            } ),
                            ( 'grid' == attrs.view || 'products-slider' == attrs.view ) && el( SelectControl, {
                                label: __( 'Columns on mobile ( <= 575px )', 'porto-functionality' ),
                                value: attrs.columns_mobile,
                                options: [ { label: __( 'Default', 'porto-functionality' ), value: '' }, { label: '1', value: '1' }, { label: '2', value: '2' }, { label: '3', value: '3' } ],
                                onChange: ( value ) => { setAttributes( { columns_mobile: value } ); },
                            } ),
                            ( 'grid' == attrs.view || 'products-slider' == attrs.view ) && el( SelectControl, {
                                label: __( 'Column Width', 'porto-functionality' ),
                                value: attrs.column_width,
                                options: [ { label: __( 'Default', 'porto-functionality' ), value: '' }, { label: __( '1/1 of content width', 'porto-functionality' ), value: '1' }, { label: __( '1/2 of content width', 'porto-functionality' ), value: '2' }, { label: __( '1/3 of content width', 'porto-functionality' ), value: '3' }, { label: __( '1/4 of content width', 'porto-functionality' ), value: '4' }, { label: __( '1/5 of content width', 'porto-functionality' ), value: '5' }, { label: __( '1/6 of content width', 'porto-functionality' ), value: '6' }, { label: __( '1/7 of content width', 'porto-functionality' ), value: '7' }, { label: __( '1/8 of content width', 'porto-functionality' ), value: '8' } ],
                                onChange: ( value ) => { setAttributes( { column_width: value } ); },
                            } ),
                            'creative' == attrs.view && el(
                                PortoImageChoose, {
                                label: __( 'Creative Grid Layout', 'porto-functionality' ),
                                options: grid_layouts,
                                value: attrs.grid_layout,
                                onChange: ( value ) => {
                                    setAttributes( { grid_layout: value } );
                                }
                            }
                            ),
                            'creative' == attrs.view && el( TextControl, {
                                label: __( 'Grid Height', 'porto-functionality' ),
                                value: attrs.grid_height,
                                onChange: ( value ) => { setAttributes( { grid_height: value } ); },
                            } ),
                            ( 'creative' == attrs.view || 'grid' == attrs.view || 'products-slider' == attrs.view ) && el( RangeControl, {
                                label: __( 'Column Spacing (px)', 'porto-functionality' ),
                                value: attrs.spacing,
                                min: 0,
                                max: 100,
                                onChange: ( value ) => { setAttributes( { spacing: value } ); },
                            } ),
                            'list' != attrs.view && el( SelectControl, {
                                label: __( 'Add Links Position', 'porto-functionality' ),
                                value: 'creative' == attrs.view && 'onimage' != attrs.addlinks_pos && 'onimage2' != attrs.addlinks_pos && 'onimage3' != attrs.addlinks_pos ? 'onimage' : attrs.addlinks_pos,
                                options: 'creative' == attrs.view ? [ { label: __( 'On Image', 'porto-functionality' ), value: 'onimage' }, { label: __( 'On Image with Overlay 1', 'porto-functionality' ), value: 'onimage2' }, { label: __( 'On Image with Overlay 2', 'porto-functionality' ), value: 'onimage3' } ] : porto_block_vars.product_layouts,
                                onChange: ( value ) => { setAttributes( { addlinks_pos: value } ); },
                            } ),
                            'products-slider' == attrs.view && el( ToggleControl, {
                                label: __( 'Show Slider Navigation', 'porto-functionality' ),
                                checked: attrs.navigation,
                                onChange: ( value ) => { setAttributes( { navigation: value } ); },
                            } ),
                            'products-slider' == attrs.view && attrs.navigation && el( SelectControl, {
                                label: __( 'Nav Position', 'porto-functionality' ),
                                value: attrs.nav_pos,
                                options: [ { label: __( 'Middle', 'porto-functionality' ), value: '' }, { label: __( 'Middle of Images', 'porto-functionality' ), value: 'nav-center-images-only' }, { label: __( 'Top', 'porto-functionality' ), value: 'show-nav-title' }, { label: __( 'Bottom', 'porto-functionality' ), value: 'nav-bottom' } ],
                                onChange: ( value ) => { setAttributes( { nav_pos: value } ); },
                            } ),
                            'products-slider' == attrs.view && attrs.navigation && el( SelectControl, {
                                label: __( 'Nav Inside?', 'porto-functionality' ),
                                value: attrs.nav_pos2,
                                options: [ { label: __( 'Default', 'porto-functionality' ), value: '' }, { label: __( 'Inside', 'porto-functionality' ), value: 'nav-pos-inside' }, { label: __( 'Outside', 'porto-functionality' ), value: 'nav-pos-outside' } ],
                                onChange: ( value ) => { setAttributes( { nav_pos2: value } ); },
                            } ),
                            'products-slider' == attrs.view && attrs.navigation && ( '' == attrs.nav_pos || 'nav-bottom' == attrs.nav_pos || 'nav-center-images-only' == attrs.nav_pos ) && el( SelectControl, {
                                label: __( 'Nav Type', 'porto-functionality' ),
                                value: attrs.nav_type,
                                options: porto_block_vars.carousel_nav_types,
                                onChange: ( value ) => { setAttributes( { nav_type: value } ); },
                            } ),
                            'products-slider' == attrs.view && attrs.navigation && el( ToggleControl, {
                                label: __( 'Show Nav on Hover', 'porto-functionality' ),
                                checked: attrs.show_nav_hover,
                                onChange: ( value ) => { setAttributes( { show_nav_hover: value } ); },
                            } ),
                            'products-slider' == attrs.view && el( ToggleControl, {
                                label: __( 'Show Slider Pagination', 'porto-functionality' ),
                                checked: attrs.pagination,
                                onChange: ( value ) => { setAttributes( { pagination: value } ); },
                            } ),
                            'products-slider' == attrs.view && attrs.pagination && el( SelectControl, {
                                label: __( 'Dots Position', 'porto-functionality' ),
                                value: attrs.dots_pos,
                                options: [ { label: __( 'Bottom', 'porto-functionality' ), value: '' }, { label: __( 'Top right', 'porto-functionality' ), value: 'show-dots-title-right' } ],
                                onChange: ( value ) => { setAttributes( { dots_pos: value } ); },
                            } )
                        )
                    )
                }
                <Disabled>
                    <ServerSideRender
                        block={ name }
                        attributes={ attributes }
                        EmptyResponsePlaceholder={ EmptyPlaceholder }
                    />
                </Disabled>
            </>
        )
    }
    registerBlockType( 'porto-single-product/porto-sp-related', {
        title: __( 'Porto Single Product Related Products', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-single-product',
        description: __(
            'Display a single product related products. It supports you to customize single product page as you mind.',
            'porto-functionality'
        ),
        attributes: {
            title: { type: 'string' },
            title_border_style: { type: 'string' },
            view: { type: 'string', default: 'grid' },
            status: { type: 'string' },
            count: { type: 'int' },
            orderby: { type: 'string', default: 'title' },
            order: { type: 'string', default: 'asc' },
            columns: { type: 'int', default: 4 },
            columns_mobile: { type: 'string' },
            column_width: { type: 'string' },
            grid_layout: { type: 'int', default: 1 },
            grid_height: { type: 'string', default: '600px' },
            spacing: { type: 'int' },
            addlinks_pos: { type: 'string' },
            navigation: { type: 'boolean', default: true },
            show_nav_hover: { type: 'boolean', default: false },
            pagination: { type: 'boolean', default: false },
            nav_pos: { type: 'string', default: '' },
            nav_pos2: { type: 'string' },
            nav_type: { type: 'string' },
            dots_pos: { type: 'string' },
            category_filter: { type: 'boolean' },
            pagination_style: { type: 'string' },
        },
        supports: {
            customClassName: false
        },
        edit: PortoSpRelated,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.element, wp.editor, wp.blockEditor, wp.components, wp.data, lodash );