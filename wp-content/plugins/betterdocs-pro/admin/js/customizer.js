/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {

    // Multiple KB start

    wp.customize( 'betterdocs_mkb_background_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wraper.betterdocs-mkb-wraper' ).css( 'background-color', to );
        } );
    });

    wp.customize( 'betterdocs_mkb_background_image', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wraper.betterdocs-mkb-wraper' ).css( 'background-image', 'url('+to+')');
        } );
    });
    
    wp.customize( 'betterdocs_mkb_background_size', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wraper.betterdocs-mkb-wraper' ).css( 'background-size', to);
        } );
    });
    
    wp.customize( 'betterdocs_mkb_background_repeat', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wraper.betterdocs-mkb-wraper' ).css( 'background-repeat', to);
        } );
    });
    
    wp.customize( 'betterdocs_mkb_background_attachment', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wraper.betterdocs-mkb-wraper' ).css( 'background-attachment', to);
        } );
    });
    
    wp.customize( 'betterdocs_mkb_background_position', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wraper.betterdocs-mkb-wraper' ).css( 'background-position', to);
        } );
    });

    wp.customize( 'betterdocs_mkb_content_padding_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-archive-wrap.betterdocs-archive-mkb' ).css( 'padding-top', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_content_padding_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-archive-wrap.betterdocs-archive-mkb' ).css( 'padding-right', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_content_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-archive-wrap.betterdocs-archive-mkb' ).css( 'padding-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_content_padding_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-archive-wrap.betterdocs-archive-mkb' ).css( 'padding-left', to + 'px' );
        } );
    });
    
    wp.customize( 'betterdocs_mkb_content_max_width', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-categories-wrap.multiple-kb' ).css( 'max-width', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_content_width', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-categories-wrap.multiple-kb' ).css( 'width', to + '%' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_space', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-categories-wrap.betterdocs-archive-mkb .docs-single-cat-wrap' ).css( 'margin', to + 'px' );
            $( '.betterdocs-categories-wrap.betterdocs-archive-mkb.layout-flex .docs-single-cat-wrap' ).css( 'margin', to + 'px' );
            $( '.tabs-content .betterdocs-tab-content .betterdocs-tab-categories').css('grid-gap', to + 'px');
        } );
    });

    wp.customize( 'betterdocs_mkb_column_padding_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-categories-wrap.multiple-kb .docs-single-cat-wrap .docs-cat-title-wrap,.betterdocs-categories-wrap.betterdocs-category-box .docs-single-cat-wrap,.betterdocs-categories-wrap .docs-single-cat-wrap.docs-cat-list-2-box, .tabs-content .betterdocs-tab-content .betterdocs-tab-categories' ).css( 'padding-top', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_padding_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-categories-wrap.multiple-kb .docs-single-cat-wrap .docs-cat-title-wrap,.docs-item-container,.betterdocs-categories-wrap.betterdocs-category-box .docs-single-cat-wrap,.betterdocs-categories-wrap .docs-single-cat-wrap.docs-cat-list-2-box, .tabs-content .betterdocs-tab-content .betterdocs-tab-categories' ).css( 'padding-right', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.docs-item-container,.betterdocs-categories-wrap.multiple-kb.betterdocs-category-box .docs-single-cat-wrap,.betterdocs-categories-wrap .docs-single-cat-wrap.docs-cat-list-2-box, .tabs-content .betterdocs-tab-content .betterdocs-tab-categories' ).css( 'padding-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_padding_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-categories-wrap.multiple-kb .docs-single-cat-wrap .docs-cat-title-wrap,.docs-item-container,.betterdocs-categories-wrap.betterdocs-category-box .docs-single-cat-wrap,.betterdocs-categories-wrap .docs-single-cat-wrap.docs-cat-list-2-box, .tabs-content .betterdocs-tab-content .betterdocs-tab-categories' ).css( 'padding-left', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_bg_color2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-box.multiple-kb.ash-bg .docs-single-cat-wrap' ).css( 'background-color', to );
            $( '.tabs-content .betterdocs-tab-categories .docs-single-cat-wrap' ).css( 'background-color', to );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_borderr_topleft', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-categories-wrap.multiple-kb .docs-single-cat-wrap, .betterdocs-categories-wrap.multiple-kb .docs-single-cat-wrap .docs-cat-title-wrap' ).css( 'border-top-left-radius', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_borderr_topright', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-categories-wrap.multiple-kb .docs-single-cat-wrap, .betterdocs-categories-wrap.multiple-kb .docs-single-cat-wrap .docs-cat-title-wrap' ).css( 'border-top-right-radius', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_borderr_bottomright', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-categories-wrap.multiple-kb .docs-single-cat-wrap, .betterdocs-categories-wrap.multiple-kb .docs-single-cat-wrap .docs-item-container' ).css( 'border-bottom-right-radius', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_borderr_bottomleft', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-categories-wrap.multiple-kb .docs-single-cat-wrap, .betterdocs-categories-wrap.multiple-kb .docs-single-cat-wrap .docs-item-container' ).css( 'border-bottom-left-radius', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_cat_icon_size', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-box.multiple-kb .docs-single-cat-wrap img, .betterdocs-categories-wrap.betterdocs-category-box-pro.multiple-kb .docs-single-cat-wrap img, .betterdocs-list-view.betterdocs-category-box.multiple-kb .docs-single-cat-wrap img, .tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-cat-title-inner .docs-cat-title .docs-cat-icon' ).css( 'height', to + 'px' );
        } );
    });
    
    wp.customize( 'betterdocs_mkb_column_content_space_image', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-box.multiple-kb .docs-single-cat-wrap img' ).css( 'margin-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_content_space_title', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-box.multiple-kb .docs-single-cat-wrap .docs-cat-title' ).css( 'margin-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_content_space_desc', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-box.multiple-kb .docs-single-cat-wrap p' ).css( 'margin-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_content_space_counter', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-box.multiple-kb .docs-single-cat-wrap span' ).css( 'margin-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_cat_title_font_size', function( value ) {
        value.bind( function( to ) {
            $( '.multiple-kb .docs-cat-title-inner .docs-cat-heading, .betterdocs-category-box.multiple-kb .docs-single-cat-wrap .docs-cat-title' ).css( 'font-size', to + 'px' );
            $( '.tabs-content .betterdocs-tab-content .docs-single-cat-wrap .docs-cat-title-inner .docs-cat-title .docs-cat-heading').css( 'font-size', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_cat_title_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-box.multiple-kb .docs-single-cat-wrap .docs-cat-title, .betterdocs-archive-mkb .docs-cat-list-2 .docs-cat-title, .tabs-content .betterdocs-tab-content .docs-single-cat-wrap .docs-cat-title .docs-cat-heading' ).css( 'color', to );
        } );
    });

    wp.customize( 'betterdocs_mkb_desc_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-box.multiple-kb .docs-single-cat-wrap p' ).css( 'color', to );
        } );
    });

    wp.customize( 'betterdocs_mkb_item_count_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-categories-wrap.multiple-kb.betterdocs-category-box .docs-single-cat-wrap span, .betterdocs-archive-mkb .docs-cat-list-2-box .title-count span' ).css( 'color', to );
        } );
    });
    
    wp.customize( 'betterdocs_mkb_item_count_font_size', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-categories-wrap.multiple-kb.betterdocs-category-box .docs-single-cat-wrap span, .betterdocs-archive-mkb .docs-cat-list-2-box .title-count span' ).css( 'font-size', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_popular_list_bg_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-list-popular .betterdocs-archive-popular .betterdocs-categories-wrap.betterdocs-popular-list.multiple-kb' ).css( 'background-color', to );
        } );
    });

    wp.customize( 'betterdocs_mkb_popular_list_color', function( value ) {
        value.bind( function( to ) {
            console.log(to);
            $( '.betterdocs-categories-wrap.multiple-kb .docs-item-container li a, .betterdocs-popular-list.multiple-kb ul li a' ).css( 'color', to );
        } );
    });

    wp.customize( 'betterdocs_mkb_popular_list_font_size', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-popular-list.multiple-kb ul li a' ).css( 'font-size', to + 'px' );

        } );
    });

    wp.customize( 'betterdocs_mkb_popular_list_icon_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-popular-list.multiple-kb ul li svg path' ).css( 'fill', to );
        } );
    });

    wp.customize( 'betterdocs_mkb_popular_list_icon_font_size', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-popular-list.multiple-kb ul li svg' ).css( 'font-size', to + 'px' );
            $( '.betterdocs-popular-list.multiple-kb ul li svg' ).css( 'min-width', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_popular_list_margin_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-list-popular .betterdocs-archive-popular .betterdocs-categories-wrap.betterdocs-popular-list.multiple-kb ul' ).css( 'margin-top', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_popular_list_margin_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-list-popular .betterdocs-archive-popular .betterdocs-categories-wrap.betterdocs-popular-list.multiple-kb ul' ).css( 'margin-right', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_popular_list_margin_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-list-popular .betterdocs-archive-popular .betterdocs-categories-wrap.betterdocs-popular-list.multiple-kb ul' ).css( 'margin-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_popular_list_margin_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-list-popular .betterdocs-archive-popular .betterdocs-categories-wrap.betterdocs-popular-list.multiple-kb ul' ).css( 'margin-left', to + 'px' );
        } );
    });

    // Multiple KB end

    wp.customize( 'betterdocs_doc_page_cat_title_font_size2', function( value ) {
        value.bind( function( to ) {
            $( '.docs-cat-list-2-items .docs-cat-title' ).css( 'font-size', to + 'px' );
        } );
    });
    wp.customize( 'betterdocs_live_search_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wraper.betterdocs-category-list-2 .betterdocs-search-form-wrap' ).css( 'padding-bottom', parseInt(to)+ parseInt(80) + 'px' );
        } );
    });
    wp.customize( 'betterdocs_doc_page_cat_icon_size_l_3_4', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-box.pro-layout-3 .docs-single-cat-wrap img,.docs-cat-list-2-box img' ).css( 'height', to + 'px' );
            $( '.betterdocs-category-box.pro-layout-3 .docs-single-cat-wrap img,.docs-cat-list-2-box img' ).css( 'width', 'auto' );
        } );
    });
    wp.customize( 'betterdocs_doc_page_column_content_space_image', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-box.pro-layout-3 .docs-single-cat-wrap img,.docs-cat-list-2-box img' ).css( 'margin-bottom','0px' );
            $( '.betterdocs-category-box.pro-layout-3 .docs-single-cat-wrap img,.docs-cat-list-2-box img' ).css( 'margin-right', to + 'px' );
        } );
    });
    wp.customize( 'betterdocs_doc_page_content_overlap', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-archive-wrap.cat-layout-4' ).css( 'marrgin-top', '-' + to + 'px' );
        } );
    });
    wp.customize( 'betterdocs_doc_single_content_area_bg_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-single-bg .betterdocs-content-full' ).css( 'background-color', to );
        } );
    });
    wp.customize( 'betterdocs_doc_single_content_area_padding_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-single-wraper .betterdocs-content-full' ).css( 'padding-right', to + 'px' );
        } );
    });
    wp.customize( 'betterdocs_doc_single_content_area_padding_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-single-wraper .betterdocs-content-full' ).css( 'padding-left', to + 'px' );
        } );
    });
    wp.customize( 'betterdocs_sidebar_title_bg_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-single-layout2 .betterdocs-full-sidebar-left .betterdocs-sidebar-content.betterdocs-category-sidebar .betterdocs-categories-wrap .docs-cat-title-inner' ).css( 'background-color', to );
        } );
    });
    wp.customize( 'betterdocs_sidebar_title_padding_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-single-layout2 .betterdocs-full-sidebar-left .betterdocs-sidebar-content.betterdocs-category-sidebar .betterdocs-categories-wrap .docs-cat-title-inner' ).css( 'padding-top', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_sidebar_title_padding_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-single-layout2 .betterdocs-full-sidebar-left .betterdocs-sidebar-content.betterdocs-category-sidebar .betterdocs-categories-wrap .docs-cat-title-inner' ).css( 'padding-right', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_sidebar_title_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-single-layout2 .betterdocs-full-sidebar-left .betterdocs-sidebar-content.betterdocs-category-sidebar .betterdocs-categories-wrap .docs-cat-title-inner' ).css( 'padding-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_sidebar_title_padding_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-single-layout2 .betterdocs-full-sidebar-left .betterdocs-sidebar-content.betterdocs-category-sidebar .betterdocs-categories-wrap .docs-cat-title-inner' ).css( 'padding-left', to + 'px' );
        } );
    });

    wp.customize('betterdocs_mkb_list_bg_color', function(value){
        value.bind( function( to ){
            $( '.betterdocs-categories-wrap .betterdocs-tab-list a').css('background-color', to);
        } );
    });

    wp.customize('betterdocs_mkb_list_font_size', function(value){
        value.bind( function( to ){
            $('.betterdocs-categories-wrap .betterdocs-tab-list a').css('font-size', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_font_color', function(value){
        value.bind( function( to ) {
            $('.betterdocs-categories-wrap .betterdocs-tab-list a').css('color', to);
        });
    });

    wp.customize('betterdocs_mkb_list_column_padding_top', function(value){
        value.bind( function( to ){
            $('.betterdocs-categories-wrap .betterdocs-tab-list a').css('padding-top', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_list_column_padding_right', function(value){
        value.bind( function( to ){
            $('.betterdocs-categories-wrap .betterdocs-tab-list a').css('padding-right', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_list_column_padding_bottom', function(value){
        value.bind( function( to ){
            $('.betterdocs-categories-wrap .betterdocs-tab-list a').css('padding-bottom', to + 'px');
        });
    })

    wp.customize('betterdocs_mkb_list_column_padding_left', function(value){
        value.bind( function( to ){
            $('.betterdocs-categories-wrap .betterdocs-tab-list a').css('padding-left', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_margin_top', function(value){
        value.bind( function( to ){
            $('.betterdocs-categories-wrap .betterdocs-tab-list a').css('margin-top', to + 'px');
        });
    });


    wp.customize('betterdocs_mkb_tab_list_margin_right', function(value){
        value.bind( function( to ){
            $('.betterdocs-categories-wrap .betterdocs-tab-list a').css('margin-right', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_margin_bottom', function(value){
        value.bind( function( to ){
            $('.betterdocs-categories-wrap .betterdocs-tab-list a').css('margin-bottom', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_margin_left', function(value){
        value.bind( function( to ){
            $('.betterdocs-categories-wrap .betterdocs-tab-list a').css('margin-left', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_border_topleft', function(value){
        value.bind( function( to ){
            $('.betterdocs-categories-wrap .betterdocs-tab-list a').css('border-top-left-radius', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_border_topright', function(value){
        value.bind( function( to ){
            $('.betterdocs-categories-wrap .betterdocs-tab-list a').css('border-top-right-radius', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_border_bottomright', function(value){
        value.bind( function( to ){
            $('.betterdocs-categories-wrap .betterdocs-tab-list a').css('border-bottom-right-radius', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_border_bottomleft', function(value){
        value.bind( function( to ){
            $('.betterdocs-categories-wrap .betterdocs-tab-list a').css('border-bottom-left-radius', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_box_content_width', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories').css('width', to + '%');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_box_gap', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories').css('grid-gap', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_box_content_padding_top', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap').css('padding-top', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_box_content_padding_right', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap').css('padding-right', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_box_content_padding_bottom', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap').css('padding-bottom', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_box_content_padding_left', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap').css('padding-left', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_box_content_margin_top', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap').css('margin-top', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_box_content_margin_right', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap').css('margin-right', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_box_content_margin_bottom', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap').css('margin-bottom', to + 'px');
        });
    });
    
    wp.customize('betterdocs_mkb_tab_list_box_content_margin_left', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap').css('margin-left', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_font_color_active', function(value){
        value.bind( function( to ){
            $('.betterdocs-categories-wrap .betterdocs-tab-list a.active').css('color', to);
        });
    });

    wp.customize('betterdocs_mkb_tab_list_back_color_active', function(value){
        value.bind( function( to ){
            $('.betterdocs-categories-wrap .betterdocs-tab-list a.active').css('background-color', to);
        });
    });

    /** Knowledge Base Column List **/

    wp.customize('betterdocs_mkb_column_list_color', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container ul li a').css('color', to);
        });
    });

    wp.customize('betterdocs_mkb_column_list_font_size', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container ul li a').css('font-size', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_column_list_margin_top', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container ul li').css('margin-top', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_column_list_margin_right', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container ul li').css('margin-right', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_list_margin_bottom', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container ul li').css('margin-bottom', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_list_margin_left', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container ul li').css('margin-left', to + 'px');
        });
    });

    // MKB Explore More Button
    wp.customize('betterdocs_mkb_tab_list_explore_btn_bg_color', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn').css('background-color', to);
        });
    });

    wp.customize('betterdocs_mkb_tab_list_explore_btn_color', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn').css('color', to);
        });
    });

    wp.customize('betterdocs_mkb_tab_list_explore_btn_border_color', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn').css('border-color', to);
        });
    });


    wp.customize('betterdocs_mkb_tab_list_explore_btn_font_size', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn').css('font-size', to + 'px');
        });
    });
    
    wp.customize('betterdocs_mkb_tab_list_explore_btn_padding_top', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn').css('padding-top', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_explore_btn_padding_right', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn').css('padding-right', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_explore_btn_padding_bottom', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn').css('padding-bottom', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_explore_btn_padding_left', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn').css('padding-left', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_explore_btn_borderr_topleft', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn').css('border-top-left-radius', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_explore_btn_borderr_topright', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn').css('border-top-right-radius', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_explore_btn_borderr_bottomright', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn').css('border-bottom-right-radius', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_tab_list_explore_btn_borderr_bottomleft', function(value){
        value.bind( function( to ){
            $('.tabs-content .betterdocs-tab-content .betterdocs-tab-categories .docs-single-cat-wrap .docs-item-container .docs-cat-link-btn').css('border-bottom-left-radius', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_popular_title_font_size', function(value){
        value.bind( function( to ){
            $('.betterdocs-popular-list.multiple-kb .popular-title').css('font-size', to + 'px');
        });
    });

    //popular docs padding
    wp.customize('betterdocs_mkb_popular_docs_padding_top', function(value){
        value.bind( function( to ){
            $('.betterdocs-list-popular .betterdocs-archive-popular .betterdocs-categories-wrap.betterdocs-popular-list.multiple-kb ul').css('padding-top', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_popular_docs_padding_right', function(value){
        value.bind( function( to ){
            $('.betterdocs-list-popular .betterdocs-archive-popular .betterdocs-categories-wrap.betterdocs-popular-list.multiple-kb ul').css('padding-right', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_popular_docs_padding_bottom', function(value){
        value.bind( function( to ){
            $('.betterdocs-list-popular .betterdocs-archive-popular .betterdocs-categories-wrap.betterdocs-popular-list.multiple-kb ul').css('padding-bottom', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_popular_docs_padding_left', function(value){
        value.bind( function( to ){
            $('.betterdocs-list-popular .betterdocs-archive-popular .betterdocs-categories-wrap.betterdocs-popular-list.multiple-kb ul').css('padding-left', to + 'px');
        });
    });

    wp.customize('betterdocs_mkb_popular_title_color', function(value){
        value.bind( function( to ){
            $('.betterdocs-popular-list.multiple-kb .popular-title').css('color', to);
        });
    });



    //Search Button Background Color
    wp.customize( 'betterdocs_search_button_background_color', function( value ) {
        value.bind( function( to ) {
            console.log(to);
            $( '.betterdocs-searchform .search-submit' ).css( 'background-color', to );
        } );
    });

    //Search Button Text Color
    wp.customize( 'betterdocs_search_button_text_color', function( value ) {
        value.bind( function( to ) {
            console.log(to);
            $( '.betterdocs-searchform .search-submit' ).css( 'color', to );
        } );
    });

    //Search Button Top Left Border Radius
    wp.customize( 'betterdocs_search_button_borderr_left_top', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-searchform .search-submit').css('border-top-left-radius', to + 'px');
        });
    });

    //Search Button Top Right Border Radius
    wp.customize( 'betterdocs_search_button_borderr_right_top', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-searchform .search-submit').css('border-top-right-radius', to + 'px');
        });
    });

    //Search Button Bottom Left Border Radius
    wp.customize( 'betterdocs_search_button_borderr_left_bottom', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-searchform .search-submit').css('border-bottom-left-radius', to + 'px');
        });
    });

    //Search Button Bottom Right Border Radius
    wp.customize( 'betterdocs_search_button_borderr_right_bottom', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-searchform .search-submit').css('border-bottom-right-radius', to + 'px');
        });
    });

    //Search Button Padding Top
    wp.customize( 'betterdocs_search_button_padding_top', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-searchform .search-submit').css('padding-top', to + 'px');
        });
    });

    //Search Button Padding Left
    wp.customize( 'betterdocs_search_button_padding_left', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-searchform .search-submit').css('padding-left', to + 'px');
        });
    });

    //Search Button Padding Bottom
    wp.customize( 'betterdocs_search_button_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-searchform .search-submit').css('padding-bottom', to + 'px');
        });
    });

    //Search Button Padding Right
    wp.customize( 'betterdocs_search_button_padding_right', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-searchform .search-submit').css('padding-right', to + 'px');
        });
    });

    //Search Button Text Font Size
    wp.customize( 'betterdocs_new_search_button_font_size', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-searchform .search-submit').css('font-size', to + 'px');
        });
    });

    //Search Button Font Weight
    wp.customize( 'betterdocs_new_search_button_font_weight', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-searchform .search-submit').css('font-weight', to );
        });
    });

    //Search Button Text Transform
    wp.customize( 'betterdocs_new_search_button_text_transform', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-searchform .search-submit').css('text-transform', to );
        });
    });

    //Search Button Letter Spacing
    wp.customize( 'betterdocs_new_search_button_letter_spacing', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-searchform .search-submit').css('letter-spacing', to + 'px');
        });
    });

    //Popular Search Background Color
    wp.customize( 'betterdocs_popular_search_background_color', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword .popular-keyword').css('background-color', to);
        });
    });

    //Popular Title Text Color
    wp.customize( 'betterdocs_popular_search_title_text_color', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword .popular-search-title').css('color', to);
        });
    });

    //Popular Title Font Size
    wp.customize( 'betterdocs_popular_search_title_font_size', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword .popular-search-title').css('font-size', to + 'px');
        });
    });


    //Popular Search Keyword Text Color
    wp.customize( 'betterdocs_popular_keyword_text_color', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword .popular-keyword').css('color', to);
        });
    });

    //Popular Search Margin Top
    wp.customize( 'betterdocs_popular_search_margin_top', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword').css('margin-top', to + 'px');
        });
    });

    //Popular Search Margin Right
    wp.customize( 'betterdocs_popular_search_margin_right', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword').css('margin-right', to + 'px');
        });
    });

    //Popular Search Margin Bottom
    wp.customize( 'betterdocs_popular_search_margin_bottom', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword').css('margin-bottom', to + 'px');
        });
    });

    //Popular Search Margin Left
    wp.customize( 'betterdocs_popular_search_margin_left', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword').css('margin-left', to + 'px');
        });
    });

    //Popular Search Keyword Padding Top
    wp.customize( 'betterdocs_popular_search_padding_top', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword .popular-keyword').css('padding-top', to + 'px');
        });
    });

    //Popular Search Keyword Padding Right
    wp.customize( 'betterdocs_popular_search_padding_right', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword .popular-keyword').css('padding-right', to + 'px');
        });
    });

    //Popular Search Keyword Padding Bottom
    wp.customize( 'betterdocs_popular_search_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword .popular-keyword').css('padding-bottom', to + 'px');
        });
    });

    //Popular Search Keyword Padding Left
    wp.customize( 'betterdocs_popular_search_padding_left', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword .popular-keyword').css('padding-left', to + 'px');
        });
    });

    //Popular Search Keyword Margin Top
    wp.customize( 'betterdocs_popular_search_keyword_margin_top', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword .popular-keyword').css('margin-top', to + 'px');
        });
    });

    //Popular Search Keyword Margin Right
    wp.customize( 'betterdocs_popular_search_keyword_margin_right', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword .popular-keyword').css('margin-right', to + 'px');
        });
    });

    //Popular Search Keyword Margin Bottom
    wp.customize( 'betterdocs_popular_search_keyword_margin_bottom', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword .popular-keyword').css('margin-bottom', to + 'px');
        });
    });

    //Popular Search Keyword Margin Left
    wp.customize( 'betterdocs_popular_search_keyword_margin_left', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword .popular-keyword').css('margin-left', to + 'px');
        });
    });

    //Popular Search Keyword Border Type
    wp.customize( 'betterdocs_popular_search_keyword_border', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword .popular-keyword').css('border-style', to);
        });
    });

    //Popular Search Keyword Border Color
    wp.customize( 'betterdocs_popular_search_keyword_border_color', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword .popular-keyword').css('border-color', to);
        });
    });

    //Popular Search Keyword Border Width Top
    wp.customize( 'betterdocs_popular_search_keyword_border_width_top', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword .popular-keyword').css('border-top-width', to + 'px');
        });
    });

    //Popular Search Keyword Border Width Right
    wp.customize( 'betterdocs_popular_search_keyword_border_width_right', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword .popular-keyword').css('border-right-width', to + 'px');
        });
    });

    //Popular Search Keyword Border Width Bottom
    wp.customize( 'betterdocs_popular_search_keyword_border_width_bottom', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword .popular-keyword').css('border-bottom-width', to + 'px');
        });
    });

    //Popular Search Keyword Border Width Left
    wp.customize( 'betterdocs_popular_search_keyword_border_width_left', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword .popular-keyword').css('border-left-width', to + 'px');
        });
    });

    //Popular Search Keyword Border Radius Left Top
    wp.customize( 'betterdocs_popular_keyword_border_radius_left_top', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword .popular-keyword').css('border-top-left-radius', to + 'px');
        });
    });

    //Popular Search Keyword Border Radius Right Top
    wp.customize( 'betterdocs_popular_keyword_border_radius_right_top', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword .popular-keyword').css('border-top-right-radius', to + 'px');
        });
    });

    //Popular Search Keyword Border Radius Left Bottom
    wp.customize( 'betterdocs_popular_keyword_border_radius_left_bottom', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword .popular-keyword').css('border-bottom-left-radius', to + 'px');
        });
    });

    //Popular Search Keyword Border Radius Right Bottom
    wp.customize( 'betterdocs_popular_keyword_border_radius_right_bottom', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword .popular-keyword').css('border-bottom-right-radius', to + 'px');
        });
    });

    //Popular Search Font Size
    wp.customize( 'betterdocs_popular_search_font_size', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-popular-search-keyword .popular-keyword').css('font-size', to + 'px');
        });
    });

	// Sidebar Background Color Layout 6(For Single Doc Layout 6)

    wp.customize( 'betterdocs_sidebar_bg_color_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6').css('background-color', to);
        });
    });

    // Sidebar Active Background Color Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_active_bg_color_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .doc-list.current-doc-list').css('background-color', to);
        });
    });

    // Sidebar Active Background Color Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_active_bg_border_color_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .doc-list.current-doc-list, .betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count.current-term').css('border-color', to);
        });
    });


    // Sidebar Padding Top Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_padding_top_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6').css('padding-top', to + 'px');
        });
    });

    // Sidebar Padding Right Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_padding_top_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6').css('padding-right', to + 'px');
        });
    });

    // Sidebar Padding Bottom Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_padding_top_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6').css('padding-bottom', to + 'px');
        });
    });

    // Sidebar Padding Left Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_padding_top_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6').css('padding-left', to + 'px');
        });
    });

    // Sidebar Margin Top Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_margin_top_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6').css('margin-top', to + 'px');
        });
    });

    // Sidebar Margin Right Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_margin_right_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6').css('margin-right', to + 'px');
        });
    });

    // Sidebar Margin Bottom Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_margin_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6').css('margin-bottom', to + 'px');
        });
    });

    // Sidebar Margin Left Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_margin_left_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6').css('margin-left', to + 'px');
        });
    });

    // Sidebar Border Radius Top Left Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_border_radius_top_left_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6').css('border-top-left-radius', to + 'px');
        });
    });

    // Sidebar Border Radius Top Right Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_border_radius_top_right_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6').css('border-top-right-radius', to + 'px');
        });
    });

    // Sidebar Border Radius Bottom Right Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_border_radius_bottom_right_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6').css('border-bottom-right-radius', to + 'px');
        });
    });

    // Sidebar Border Radius Bottom Left Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_border_radius_bottom_left_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6').css('border-bottom-left-radius', to + 'px');
        });
    });

    // Sidebar Title Background Color Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_bg_color_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 .betterdocs-sidebar-category-title-count').css('background-color', to);
        });
    });

    //Sidebar Term List Item Color Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_list_item_color_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .doc-list ul li a').css('color', to);
        });
    });

    //Sidebar Term List Item Font Size Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_list_item_font_size_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .doc-list ul li a').css('font-size', to + 'px');
        });
    });

    //Sidebar Term List Item Icon Color Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_list_item_icon_color_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .doc-list ul li svg').css('fill', to);
        });
    });

    //Sidebar Term List Item Icon Size Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_list_item_icon_size_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .doc-list ul li svg').css('height', to + 'px');
        });
    });

    wp.customize( 'betterdocs_sidebar_term_list_item_icon_size_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .doc-list ul li svg').css('width', to + 'px');
        });
    });

    //Sidebar Term List Item Padding Top(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_list_item_padding_top_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .doc-list ul li').css('padding-top', to + 'px');
        });
    });

    //Sidebar Term List Item Padding Right(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_list_item_padding_right_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .doc-list ul li').css('padding-right', to + 'px');
        });
    });

    //Sidebar Term List Item Padding Bottom(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_list_item_padding_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .doc-list ul li').css('padding-bottom', to + 'px');
        });
    });

    //Sidebar Term List Item Padding Left(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_list_item_padding_left_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .doc-list ul li').css('padding-left', to + 'px');
        });
    });

    //Sidebar Term List Active Item Color(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_list_active_item_color_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .doc-list.current-doc-list ul li a.active-doc, .betterdocs-sidebar-layout-6 li .doc-list .nested-docs-sub-cat.current-sub-cat li a.active-doc').css('color', to);
        });
    });

    // Sidebar Term List Item Counter Border Type
    wp.customize( 'betterdocs_sidebar_term_item_counter_border_type_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count span.betterdocs-sidebar-term-count').css('border-style', to);
        });
    });

    // Sidebar Term List Item Counter Border Width
    wp.customize( 'betterdocs_sidebar_term_item_counter_border_width_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count span.betterdocs-sidebar-term-count').css('border-width', to + 'px');
        });
    });

    // Sidebar Active Title Background Color Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_active_title_bg_color_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count.current-term').css('background-color', to);
        });
    });

    // Sidebar Title Color Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_color_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count a .betterdocs-sidebar-term-heading').css('color', to);
        });
    });

    // Sidebar Title Font Size Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_font_size_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count a .betterdocs-sidebar-term-heading').css('font-size', to + 'px');
        });
    });

    // Sidebar Title Font Line Height Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_font_line_height_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count a .betterdocs-sidebar-term-heading').css('line-height', to + 'px');
        });
    });

    // Sidebar Title Font Font Weight Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_font_weight_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count a .betterdocs-sidebar-term-heading').css('font-weight', to);
        });
    });

    // Sidebar Title Padding Top Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_padding_top_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count a .betterdocs-sidebar-term-heading').css('padding-top', to + 'px');
        });
    });

    // Sidebar Title Padding Right Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_padding_right_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count a .betterdocs-sidebar-term-heading').css('padding-right', to + 'px');
        });
    });

	// Sidebar Title Padding Bottom Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_padding_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count a .betterdocs-sidebar-term-heading').css('padding-bottom', to + 'px');
        });
    });

	// Sidebar Title Padding Left Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_padding_left_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count a .betterdocs-sidebar-term-heading').css('padding-left', to + 'px');
        });
    });

    // Sidebar Title Margin Top Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_margin_top_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count a .betterdocs-sidebar-term-heading').css('margin-top', to + 'px');
        });
    });

    // Sidebar Title Margin Right Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_margin_right_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count a .betterdocs-sidebar-term-heading').css('margin-right', to + 'px');
        });
    });

    // Sidebar Title Margin Bottom Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_margin_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count a .betterdocs-sidebar-term-heading').css('margin-bottom', to + 'px');
        });
    });

    // Sidebar Title Margin Bottom Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_margin_left_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count a .betterdocs-sidebar-term-heading').css('margin-left', to + 'px');
        });
    });

    // Sidebar Term List Border Type Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_list_border_type_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li').css('border-type', to);
        });
    });

    // Sidebar Term List Border Top Width Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_border_top_width_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li').css('border-top-width', to + 'px');
        });
    });

    // Sidebar Term List Border Right Width Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_border_right_width_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li').css('border-right-width', to + 'px');
        });
    });


    // Sidebar Term List Border Bottom Width Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_border_bottom_width_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li').css('border-bottom-width', to + 'px');
        });
    });

    // Sidebar Term List Border Left Width Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_border_left_width_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li').css('border-left-width', to + 'px');
        });
    });

    // Sidebar Term List Border Width Color Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_border_width_color_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li').css('border-color', to);
        });
    });

    // Sidebar Term List Item Counter Font Size Layout 6(For Single Doc Layout 6)

    wp.customize( 'betterdocs_sidebar_term_item_counter_font_size_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count span.betterdocs-sidebar-term-count').css('font-size', to + 'px');
        });
    });

    // Sidebar Term List Item Counter Font Weight Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_font_weight_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count span.betterdocs-sidebar-term-count').css('font-weight', to );
        });
    });

    // Sidebar Term List Item Counter Font Line Height Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_font_line_height_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count span.betterdocs-sidebar-term-count').css('line-height', to + 'px' );
        });
    });

    // Sidebar Term List Item Counter Color Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_color_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count span.betterdocs-sidebar-term-count').css('color', to );
        });
    });

    // Sidebar Term List Item Counter Background Color Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_back_color_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count span.betterdocs-sidebar-term-count').css('background-color', to );
        });
    });

    // Sidebar Term List Item Counter Border Radius Top Left Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_border_radius_top_left_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count span.betterdocs-sidebar-term-count').css('border-top-left-radius', to + 'px');
        });
    });

	// Sidebar Term List Item Counter Border Radius Top Right Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_border_radius_top_right_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count span.betterdocs-sidebar-term-count').css('border-top-right-radius', to + 'px');
        });
    });

    // Sidebar Term List Item Counter Border Radius Bottom Right Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_border_radius_bottom_right_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count span.betterdocs-sidebar-term-count').css('border-bottom-right-radius', to + 'px');
        });
    });

    // Sidebar Term List Item Counter Border Radius Bottom Left Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_border_radius_bottom_left_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count span.betterdocs-sidebar-term-count').css('border-bottom-left-radius', to + 'px');
        });
    });

    // Sidebar Term List Item Counter Padding Top Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_padding_top_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count span.betterdocs-sidebar-term-count').css('padding-top', to + 'px');
        });
    });

    // Sidebar Term List Item Counter Padding Right Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_padding_right_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count span.betterdocs-sidebar-term-count').css('padding-right', to + 'px');
        });
    });

    // Sidebar Term List Item Counter Padding Bottom Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_padding_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count span.betterdocs-sidebar-term-count').css('padding-bottom', to + 'px');
        });
    });

    // Sidebar Term List Item Counter Padding Left Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_padding_left_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count span.betterdocs-sidebar-term-count').css('padding-left', to + 'px');
        });
    });

    // Sidebar Term List Item Counter Margin Top Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_margin_top_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count span.betterdocs-sidebar-term-count').css('margin-top', to + 'px');
        });
    });

    // Sidebar Term List Item Counter Margin Right Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_margin_right_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count span.betterdocs-sidebar-term-count').css('margin-right', to + 'px');
        });
    });

    // Sidebar Term List Item Counter Margin Bottom Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_margin_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count span.betterdocs-sidebar-term-count').css('margin-bottom', to + 'px');
        });
    });

    // Sidebar Term List Item Counter Margin Left Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_margin_left_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar-layout-6 li .betterdocs-sidebar-category-title-count span.betterdocs-sidebar-term-count').css('margin-left', to + 'px');
        });
    });

    // Archive Page Title Color (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_title_color_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-title').css('color', to);
        });
    });

    // Archive Page Category Inner Content Background Color(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_inner_content_back_color_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2').css('background-color', to);
        });
    });

    // Archive Page Category Inner Content Image Size(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_inner_content_image_size_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-term-img').css('width', to + '%');
        });
    });

    // Archive Page Category Other Categories Image Size(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_image_size', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-category img').css('width', to + '%');
        });
    });
    
    // Archive Page Category Inner Content Image Padding Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_inner_content_image_padding_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-img img').css('padding-top', to + 'px');
        });
    });

    // Archive Page Category Inner Content Image Padding Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_inner_content_image_padding_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-img img').css('padding-right', to + 'px');
        });
    });

    // Archive Page Category Inner Content Image Padding Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_inner_content_image_padding_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-img img').css('padding-bottom', to + 'px');
        });
    });

    // Archive Page Category Inner Content Image Padding Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_inner_content_image_padding_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-img img').css('padding-left', to + 'px');
        });
    });

    // Archive Page Category Inner Content Image Margin Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_inner_content_image_margin_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-img img').css('margin-top', to + 'px');
        });
    });

    // Archive Page Category Inner Content Image Margin Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_inner_content_image_margin_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-img img').css('margin-right', to + 'px');
        });
    });

    // Archive Page Category Inner Content Image Margin Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_inner_content_image_margin_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-img img').css('margin-bottom', to + 'px');
        });
    });

    // Archive Page Category Inner Content Image Margin Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_inner_content_image_margin_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-img img').css('margin-left', to + 'px');
        });
    });

    // Archive Page Title Font Size (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_title_font_size_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-title').css('font-size', to + 'px');
        });
    });

    // Archive Page Title Margin Top (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_title_margin_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-title').css('margin-top', to + 'px');
        });
    });

    // Archive Page Title Margin Right (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_title_margin_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-title').css('margin-right', to + 'px');
        });
    });

    // Archive Page Title Margin Bottom (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_title_margin_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-title').css('margin-bottom', to + 'px');
        });
    });

     // Archive Page Title Margin Left (Archive Page Layout 2)
     wp.customize( 'betterdocs_archive_title_margin_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-title').css('margin-left', to + 'px');
        });
    });

	// Archive Page Description Color (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_description_color_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-term-description p').css('color', to);
        });
    });

    // Archive Page Description Font Size (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_description_font_size_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-term-description p').css('font-size', to + 'px');
        });
    });

    // Archive Page Description Margin Top (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_description_margin_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-term-description p').css('margin-top', to + 'px');
        });
    });

    // Archive Page Description Margin Right (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_description_margin_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-term-description p').css('margin-right', to + 'px');
        });
    });

    // Archive Page Description Margin Bottom (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_description_margin_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-term-description p').css('margin-bottom', to + 'px');
        });
    });

    // Archive Page Description Margin Left (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_description_margin_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-term-description p').css('margin-left', to + 'px');
        });
    });

    // Archive Page List Item Color (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_item_color_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li a.betterdocs-article p').css('color', to);
        });
    });

    // Archive Page List Item Color (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_item_font_size_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li a.betterdocs-article p').css( 'font-size', to + 'px' );
        });
    });

    // Archive Page Docs List Margin Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_margin_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li a.betterdocs-article p').css( 'margin-top', to + 'px' );
        });
    });

    // Archive Page Docs List Margin Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_margin_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li a.betterdocs-article p').css( 'margin-right', to + 'px' );
        });
    });

    // Archive Page Docs List Margin Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_margin_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li a.betterdocs-article p').css( 'margin-bottom', to + 'px' );
        });
    });

    // Archive Page Docs List Margin Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_margin_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li a.betterdocs-article p').css( 'margin-left', to + 'px' );
        });
    });

    // Archive Page Docs List Font Weight(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_font_weight_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li a.betterdocs-article p').css( 'font-weight', to );
        });
    });

    // Archive Page Docs List Line Height(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_item_line_height_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li a.betterdocs-article p').css( 'line-height', to + 'px' );
        });
    });

    // Archive Page List Item Arrow Height(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_item_arrow_height_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li a .archive-doc-arrow').css( 'height', to + 'px' );
        });
    });

    // Archive Page List Item Border Style(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_border_style_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li').css( 'border-style', to );
        });
    });

    // Archive Page List Item Border Width Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_border_width_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li').css( 'border-top-width', to + 'px');
        });
    });

    // Archive Page List Item Border Width Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_border_width_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li').css( 'border-right-width', to + 'px');
        });
    });

	// Archive Page List Item Border Width Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_border_width_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li').css( 'border-bottom-width', to + 'px');
        });
    });

	// Archive Page List Item Border Width Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_border_width_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li').css( 'border-left-width', to + 'px');
        });
    });

    // Archive Page List Item Border Color Top(Archive Page Layout 2) 
    wp.customize( 'betterdocs_archive_list_border_width_color_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li').css( 'border-top-color', to );
        });
    });

    // Archive Page List Item Border Color Right(Archive Page Layout 2) 
    wp.customize( 'betterdocs_archive_list_border_width_color_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li').css( 'border-right-color', to );
        });
    });

    // Archive Page List Item Border Color Bottom(Archive Page Layout 2) 
    wp.customize( 'betterdocs_archive_list_border_width_color_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li').css( 'border-bottom-color', to );
        });
    });

    // Archive Page List Item Border Color Left(Archive Page Layout 2) 
    wp.customize( 'betterdocs_archive_list_border_width_color_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li').css( 'border-left-color', to );
        });
    });


    // Archive Page List Item Arrow Width(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_item_arrow_width_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li a .archive-doc-arrow').css( 'width', to + 'px' );
        });
    });


    // Archive Page List Item Arrow Color(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_item_arrow_color_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li a .archive-doc-arrow path').css( 'fill', to );
        });
    });

    // Archive Page Docs List Arrow Margin Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_arrow_margin_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li a .archive-doc-arrow').css( 'margin-top', to + 'px' );
        });
    });

    // Archive Page Docs List Arrow Margin Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_arrow_margin_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li a .archive-doc-arrow').css( 'margin-right', to + 'px' );
        });
    });

    // Archive Page Docs List Arrow Margin Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_arrow_margin_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li a .archive-doc-arrow').css( 'margin-bottom', to + 'px' );
        });
    });

    // Archive Page Docs List Arrow Margin Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_arrow_margin_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li a .archive-doc-arrow').css( 'margin-left', to + 'px' );
        });
    });

    // Archive Page Excerpt Font Weight(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_font_weight_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li p.betterdocs-excerpt').css( 'font-weight', to );
        });
    });

    // Archive Page Excerpt Font Size(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_font_size_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li p.betterdocs-excerpt').css( 'font-size', to + 'px' );
        });
    });

    // Archive Page Excerpt Font Line Height(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_font_line_height_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li p.betterdocs-excerpt').css( 'line-height', to + 'px' );
        });
    });

    // Archive Page Excerpt Font Color(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_font_color_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li p.betterdocs-excerpt').css( 'color', to );
        });
    });

    // Archive Page Excerpt Margin Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_margin_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li p.betterdocs-excerpt').css( 'margin-top', to + 'px');
        });
    });

    // Archive Page Excerpt Margin Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_margin_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li p.betterdocs-excerpt').css( 'margin-right', to + 'px' );
        });
    });


    // Archive Page Excerpt Margin Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_margin_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li p.betterdocs-excerpt').css( 'margin-left', to + 'px');
        });
    });

    // Archive Page Excerpt Margin Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_margin_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li p.betterdocs-excerpt').css( 'margin-bottom', to + 'px');
        });
    });

    // Archive Page Excerpt Padding Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_padding_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li p.betterdocs-excerpt').css( 'padding-top', to + 'px');
        });
    });

    // Archive Page Excerpt Padding Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_padding_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li p.betterdocs-excerpt').css( 'padding-right', to + 'px');
        });
    });

    // Archive Page Excerpt Padding Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_padding_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li p.betterdocs-excerpt').css( 'padding-bottom', to + 'px');
        });
    });
    
    // Archive Page Excerpt Padding Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_padding_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2-list li p.betterdocs-excerpt').css( 'padding-left', to + 'px');
        });
    });

    // Archive Page Excerpt Category Item Counter Font Weight(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_font_weight_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-count').css( 'font-weight', to );
        });
    });

    // Archive Page Excerpt Category Item Counter Font Size(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_font_size_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-count').css( 'font-size', to + 'px');
        });
    });

    // Archive Page Excerpt Category Item Counter Font Line Height(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_font_line_height_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-count').css( 'line-height', to + 'px');
        });
    });

    // Archive Page Excerpt Category Item Counter Font Color(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_font_color_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-count').css( 'color', to );
        });
    });

    //Archive Page Category Item Count Border Radius Top Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_border_radius_top_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-count').css( 'border-top-left-radius', to + 'px');
        });
    });

    //Archive Page Category Item Count Border Radius Top Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_border_radius_top_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-count').css( 'border-top-right-radius', to + 'px');
        });
    });

    //Archive Page Category Item Count Border Radius Bottom Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_border_radius_bottom_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-count').css( 'border-bottom-right-radius', to + 'px');
        });
    });

    //Archive Page Category Item Count Border Radius Bottom Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_border_radius_bottom_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-count').css( 'border-bottom-left-radius', to + 'px');
        });
    });

    //Archive Page Category Item Count Border Margin Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_margin_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-count').css( 'margin-top', to + 'px');
        });
    });

    //Archive Page Category Item Count Border Margin Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_margin_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-count').css( 'margin-right', to + 'px');
        });
    });

    //Archive Page Category Item Count Border Margin Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_margin_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-count').css( 'margin-bottom', to + 'px');
        });
    });

    //Archive Page Category Item Count Border Margin Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_margin_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-count').css( 'margin-left', to + 'px');
        });
    });

    //Archive Page Category Item Count Padding Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_padding_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-count').css( 'padding-top', to + 'px');
        });
    });

    //Archive Page Category Item Count Padding Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_padding_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-count').css( 'padding-right', to + 'px');
        });
    });

	//Archive Page Category Item Count Padding Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_padding_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-count').css( 'padding-bottom', to + 'px');
        });
    });

	//Archive Page Category Item Count Padding Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_padding_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-count').css( 'padding-left', to + 'px');
        });
    });

    // Archive Page Category Item Count Border Color(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_border_color_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-count').css( 'border-color', to );
        });
    });

    // Archive Page Category Item Count Background Color(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_back_color_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-doc-category-layout-2 .betterdocs-doc-category-term-info .betterdocs-doc-category-term-title-count .betterdocs-doc-category-term-count').css( 'background-color', to );
        });
    });

    // Archive Page Category Other Categories Title Color(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_color', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-text').css( 'color', to );
        });
    });

    // Archive Page Category Other Categories Title Font Weight(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_font_weight', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-text').css( 'font-weight', to );
        });
    });

    // Archive Page Category Other Categories Title Font Size(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_font_size', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-text').css( 'font-size', to + 'px' );
        });
    });


    // Archive Page Category Other Categories Title Line Height(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_line_height', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-text').css( 'line-height', to + 'px' );
        });
    });

    // Archive Page Category Other Categories Title Padding Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_padding_top', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-text').css( 'padding-top', to + 'px' );
        });
    });

    // Archive Page Category Other Categories Title Padding Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_padding_right', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-text').css( 'padding-right', to + 'px' );
        });
    });

    // Archive Page Category Other Categories Title Padding Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-text').css( 'padding-bottom', to + 'px' );
        });
    });

    // Archive Page Category Other Categories Title Padding Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_padding_left', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-text').css( 'padding-left', to + 'px' );
        });
    });

    // Archive Page Category Other Categories Title Margin Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_margin_top', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-text').css( 'margin-top', to + 'px' );
        });
    });

    // Archive Page Category Other Categories Title Margin Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_margin_right', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-text').css( 'margin-right', to + 'px' );
        });
    });

    // Archive Page Category Other Categories Title Margin Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_margin_bottom', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-text').css( 'margin-bottom', to + 'px' );
        });
    });

    // Archive Page Category Other Categories Title Margin Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_margin_left', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-text').css( 'margin-left', to + 'px' );
        });
    });

    // Archive Page Category Other Categories Count(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_color', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-count').css( 'color', to );
        });
    });

    // Archive Page Category Other Categories Count Background Color(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_back_color', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-count').css( 'background-color', to );
        });
    });

    // Archive Page Category Other Categories Count Line Height(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_line_height', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-count').css( 'line-height', to + 'px' );
        });
    });
    
    // Archive Page Category Other Categories Count Font Weight (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_font_weight', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-count').css( 'font-weight', to  );
        });
    });
    
    // Archive Page Category Other Categories Count Font Size(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_font_size', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-count').css( 'font-size', to + 'px'  );
        });
    });

    // Archive Page Category Other Categories Count Border Radius Top Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_border_radius_topleft', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-count').css( 'border-top-left-radius', to + 'px'  );
        });
    });
    
	// Archive Page Category Other Categories Count Border Radius Top Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_border_radius_topright', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-count').css( 'border-top-right-radius', to + 'px'  );
        });
    });
    
	// Archive Page Category Other Categories Count Border Radius Bottom Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_border_radius_bottomright', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-count').css( 'border-bottom-right-radius', to + 'px'  );
        });
    });
    
	// Archive Page Category Other Categories Count Border Radius Bottom Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_border_radius_bottomleft', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-count').css( 'border-bottom-left-radius', to + 'px'  );
        });
    });

    // Archive Page Category Other Categories Count Padding Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_padding_top', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-count').css( 'padding-top', to + 'px'  );
        });
    });

    // Archive Page Category Other Categories Count Padding Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_padding_right', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-count').css( 'padding-right', to + 'px'  );
        });
    });

    // Archive Page Category Other Categories Count Padding Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-count').css( 'padding-bottom', to + 'px'  );
        });
    });

    // Archive Page Category Other Categories Count Padding Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_padding_left', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-count').css( 'padding-left', to + 'px'  );
        });
    });

    // Archive Page Category Other Categories Count Margin Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_margin_top', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-count').css( 'margin-top', to + 'px'  );
        });
    });

    // Archive Page Category Other Categories Count Margin Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_margin_right', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-count').css( 'margin-right', to + 'px'  );
        });
    });

    // Archive Page Category Other Categories Count Margin Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_margin_bottom', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-count').css( 'margin-bottom', to + 'px'  );
        });
    });

    // Archive Page Category Other Categories Count Margin Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_margin_left', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-info .betterdocs-related-term-count').css( 'margin-left', to + 'px'  );
        });
    });

    // Archive Page Category Other Categories Description Color(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_description_color', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-category .betterdocs-related-term-desc').css( 'color', to );
        });
    });
    
    // Archive Page Category Other Categories Description Font Weight(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_description_font_weight', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-category .betterdocs-related-term-desc').css( 'font-weight', to );
        });
    });
    
    // Archive Page Category Other Categories Description Font Size(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_description_font_size', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-category .betterdocs-related-term-desc').css( 'font-size', to + 'px');
        });
    });

    // Archive Page Category Other Categories Description Line Height(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_description_line_height', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-category .betterdocs-related-term-desc').css( 'line-height', to + 'px');
        });
    });

    // Archive Page Category Other Categories Description Padding Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_description_padding_top', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-category .betterdocs-related-term-desc').css( 'padding-top', to + 'px');
        });
    });

    // Archive Page Category Other Categories Description Padding Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_description_padding_right', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-category .betterdocs-related-term-desc').css( 'padding-right', to + 'px');
        });
    });

    // Archive Page Category Other Categories Description Padding Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_description_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-category .betterdocs-related-term-desc').css( 'padding-bottom', to + 'px');
        });
    });

    // Archive Page Category Other Categories Description Padding Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_description_padding_left', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-related-category .betterdocs-related-term-desc').css( 'padding-left', to + 'px');
        });
    });

    // Archive Page Category Other Categories Button Color(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_color', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-show-more-terms .betterdocs-load-more-button').css( 'color', to );
        });
    });

    // Archive Page Category Other Categories Button Background Color(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_back_color', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-show-more-terms .betterdocs-load-more-button').css( 'background-color', to );
        });
    });

	// Archive Page Category Other Categories Button Font Weight(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_font_weight', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-show-more-terms .betterdocs-load-more-button').css( 'font-weight', to );
        });
    });

	// Archive Page Category Other Categories Button Font Size(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_font_size', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-show-more-terms .betterdocs-load-more-button').css( 'font-size', to + 'px' );
        });
    });

	// Archive Page Category Other Categories Button Font Line Height(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_font_line_height', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-show-more-terms .betterdocs-load-more-button').css( 'line-height', to + 'px' );
        });
    });

	// Archive Page Category Other Categories Button Border Radius Top Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_border_radius_top_left', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-show-more-terms .betterdocs-load-more-button').css( 'border-top-left-radius', to + 'px' );
        });
    });

	// Archive Page Category Other Categories Button Border Radius Top Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_border_radius_top_right', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-show-more-terms .betterdocs-load-more-button').css( 'border-top-right-radius', to + 'px' );
        });
    });

	// Archive Page Category Other Categories Button Border Radius Bottom Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_border_radius_bottom_right', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-show-more-terms .betterdocs-load-more-button').css( 'border-bottom-right-radius', to + 'px' );
        });
    });

	// Archive Page Category Other Categories Button Border Radius Bottom Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_border_radius_bottom_left', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-show-more-terms .betterdocs-load-more-button').css( 'border-bottom-left-radius', to + 'px' );
        });
    });

	// Archive Page Category Other Categories Button Padding Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_padding_top', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-show-more-terms .betterdocs-load-more-button').css( 'padding-top', to + 'px' );
        });
    });

	// Archive Page Category Other Categories Button Padding Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_padding_right', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-show-more-terms .betterdocs-load-more-button').css( 'padding-right', to + 'px' );
        });
    });

	// Archive Page Category Other Categories Button Padding Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-show-more-terms .betterdocs-load-more-button').css( 'padding-bottom', to + 'px' );
        });
    });

	// Archive Page Category Other Categories Button Padding Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_padding_left', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-show-more-terms .betterdocs-load-more-button').css( 'padding-left', to + 'px' );
        });
    });

    // Category Title Font Size (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_cat_title_font_size_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-title' ).css( 'font-size', to + 'px' );
        } );
    });

    // Category Image Width(Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_img_width_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-term-img' ).css( 'width', to + '%' );
        } );
    });


    // Category Title Padding Bottom (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_cat_title_padding_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-title-count' ).css( 'padding-bottom', to + 'px' );
        } );
    });

    // Item Count Font Size (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_item_count_font_size_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'font-size', to + 'px');
        } );
    });

    // Item Count Color (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_item_count_color_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'color', to );
        } );
    });

    // Item Count Background Color (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_item_count_back_color_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'background-color', to );
        } );
    });

    // Item Count Border Style (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_item_count_border_type_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'border-style', to );
        } );
    });

    // Item Count Border Color (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_item_count_border_color_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'border-color', to );
        } );
    });

    // Item Count Border Width Top (Doc Page Layout 6)
     wp.customize( 'betterdocs_doc_page_item_count_border_width_top_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'border-top-width', to + 'px');
        } );
    });

    // Item Count Border Width Right (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_item_count_border_width_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'border-right-width', to + 'px');
        } );
    });

    // Item Count Border Width Bottom (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_item_count_border_width_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'border-bottom-width', to + 'px');
        } );
    });

    // Item Count Border Width Left (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_item_count_border_width_left_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'border-left-width', to + 'px');
        } );
    });

    // Item Count Border Radius Top Left (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_item_count_border_radius_top_left_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'border-top-left-radius', to + 'px' );
        } );
    });

    // Item Count Border Radius Top Right (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_item_count_border_radius_top_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'border-top-right-radius', to + 'px');
        } );
    });

    // Item Count Border Radius Bottom Right(Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_item_count_border_radius_bottom_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'border-bottom-right-radius', to + 'px');
        } );
    });

    // Item Count Border Radius Bottom Left(Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_item_count_border_radius_bottom_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'border-bottom-left-radius', to + 'px');
        } );
    });

    // Item Count Margin Top(Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_item_count_margin_top_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'margin-top', to + 'px');
        } );
    });    

    // Item Count Margin Right(Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_item_count_margin_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'margin-right', to + 'px');
        } );
    });  
    
    // Item Count Margin Bottom(Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_item_count_margin_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'margin-bottom', to + 'px');
        } );
    });   

    // Item Count Margin Left(Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_item_count_margin_left_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'margin-left', to + 'px');
        } );
    });   

    // Item Count Padding Top(Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_item_count_padding_top_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'padding-top', to + 'px');
        } );
    });   

    // Item Count Padding Right(Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_item_count_padding_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'padding-right', to + 'px');
        } );
    }); 

    // Item Count Padding Bottom(Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_item_count_padding_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'padding-bottom', to + 'px');
        } );
    }); 

    // Item Count Padding Left(Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_item_count_padding_left_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'padding-left', to + 'px');
        } );
    }); 

    // Doc List Font Size(Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_font_size_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li .betterdocs-article p' ).css( 'font-size', to + 'px');
        } );
    }); 

    // Doc List Font Weight(Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_font_weight_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li .betterdocs-article p' ).css( 'font-weight', to);
        } );
    }); 

    // Doc List Font Line Height(Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_font_line_height_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li .betterdocs-article p' ).css( 'line-height', to + 'px' );
        } );
    }); 

    // Doc List Category Description Color(Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_desc_color_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-description' ).css( 'color', to );
        } );
    }); 

	// Doc List Category Font Size (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_desc_font_size_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-description' ).css( 'font-size', to + 'px' );
        } );
    }); 

    // Doc List Category Font Weight (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_desc_font_weight_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-description' ).css( 'font-weight', to );
        } );
    }); 

    // Doc List Category Line Height (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_desc_line_height_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-description' ).css( 'line-height', to + 'px' );
        } );
    }); 

    // Doc List Description Margin Top (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_desc_margin_top_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-description' ).css( 'margin-top', to + 'px' );
        } );
    }); 


    // Doc List Description Margin Right (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_desc_margin_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-description' ).css( 'margin-right', to + 'px' );
        } );
    }); 

    // Doc List Description Margin Bottom (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_desc_margin_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-description' ).css( 'margin-bottom', to + 'px' );
        } );
    }); 

    // Doc List Description Margin Left (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_desc_margin_left_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-description' ).css( 'margin-left', to + 'px' );
        } );
    }); 

    // Doc List Font Color (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_font_color_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li .betterdocs-article p' ).css( 'color', to );
        } );
    }); 

    // Doc List Margin Top (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_margin_top_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li .betterdocs-article p' ).css( 'margin-top', to + 'px' );
        } );
    }); 

    // Doc List Margin Right (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_margin_right_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li .betterdocs-article p' ).css( 'margin-right', to + 'px' );
        } );
    }); 

    // Doc List Margin Bottom (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_margin_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li .betterdocs-article p' ).css( 'margin-bottom', to + 'px' );
        } );
    }); 

    // Doc List Margin Left (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_margin_left_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li .betterdocs-article p' ).css( 'margin-left', to + 'px' );
        } );
    }); 
    
    // Doc List Padding Top (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_padding_top_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li .betterdocs-article p' ).css( 'padding-top', to + 'px' );
        } );
    }); 

    // Doc List Padding Right (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_padding_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li .betterdocs-article p' ).css( 'padding-right', to + 'px' );
        } );
    }); 

	// Doc List Padding Bottom (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_padding_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li .betterdocs-article p' ).css( 'padding-bottom', to + 'px' );
        } );
    }); 

	// Doc List Padding Left (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_padding_left_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li .betterdocs-article p' ).css( 'padding-left', to + 'px' );
        } );
    }); 

	// Doc List Border Width Top (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_border_top_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list .betterdocs-article' ).css( 'border-top-width', to + 'px' );
        } );
    }); 

    // Doc List Border Style (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_border_style_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list .betterdocs-article' ).css( 'border-style', to );
        } );
    }); 

    // Doc List Border Width Right (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_border_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list .betterdocs-article' ).css( 'border-right-width', to + 'px' );
        } );
    }); 

    // Doc List Border Width Bottom (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_border_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list .betterdocs-article' ).css( 'border-bottom-width', to + 'px' );
        } );
    }); 

    // Doc List Border Width Left (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_border_left_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list .betterdocs-article' ).css( 'border-left-width', to + 'px' );
        } );
    }); 


    // Doc List Border Top Color (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_border_color_top_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list .betterdocs-article' ).css( 'border-top-color', to );
        } );
    }); 

    //Doc List Border Color Right(Doc Page Layout 6) 
    wp.customize( 'betterdocs_doc_list_border_color_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list .betterdocs-article' ).css( 'border-right-color', to );
        } );
    }); 

    //Doc List Border Color Bottom(Doc Page Layout 6) 
    wp.customize( 'betterdocs_doc_list_border_color_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list .betterdocs-article' ).css( 'border-bottom-color', to );
        } );
    }); 

    //Doc List Border Color Left(Doc Page Layout 6) 
    wp.customize( 'betterdocs_doc_list_border_color_left_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list .betterdocs-article' ).css( 'border-left-color', to );
        } );
    }); 


    //Doc List Arrow Height (Doc Page Layout 6) 
    wp.customize( 'betterdocs_doc_list_arrow_height_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li a .doc-list-arrow' ).css( 'height', to + 'px' );
        } );
    }); 

    //Doc List Arrow Width (Doc Page Layout 6) 
    wp.customize( 'betterdocs_doc_list_arrow_width_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li a .doc-list-arrow' ).css( 'width', to + 'px' );
        } );
    }); 

    // Doc List Arrow Color (Doc Page Layout 6) 
    wp.customize( 'betterdocs_doc_list_arrow_color_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li a .doc-list-arrow path' ).css( 'fill', to );
        } );
    }); 

    //Doc List Explore More Font Size(Doc Page Layout 6) 
    wp.customize( 'betterdocs_doc_list_explore_more_font_size_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li a.betterdocs-term-explore-more p' ).css( 'font-size', to + 'px' );
        } );
    }); 

     //Doc List Explore More Font Line Height(Doc Page Layout 6) 
     wp.customize( 'betterdocs_doc_list_explore_more_font_line_height_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li a.betterdocs-term-explore-more p' ).css( 'line-height', to + 'px' );
        } );
    }); 

    //Doc List Explore More Font Color(Doc Page Layout 6) 
    wp.customize( 'betterdocs_doc_list_explore_more_font_color_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li a.betterdocs-term-explore-more p' ).css( 'color', to );
        } );
    }); 

    //Doc List Explore More Font Weight(Doc Page Layout 6) 
    wp.customize( 'betterdocs_doc_list_explore_more_font_weight_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li a.betterdocs-term-explore-more p' ).css( 'font-weight', to );
        } );
    }); 

    //Explore More Top Padding (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_explore_more_padding_top_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li a.betterdocs-term-explore-more p' ).css( 'padding-top', to + 'px' );
        } );
    }); 

    //Explore More Right Padding (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_explore_more_padding_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li a.betterdocs-term-explore-more p' ).css( 'padding-right', to + 'px' );
        } );
    }); 

    //Explore More Bottom Padding (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_explore_more_padding_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li a.betterdocs-term-explore-more p' ).css( 'padding-bottom', to + 'px' );
        } );
    }); 

    //Explore More Left Padding (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_explore_more_padding_left_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li a.betterdocs-term-explore-more p' ).css( 'padding-left', to + 'px' );
        } );
    }); 

    //Explore More Top Margin (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_explore_more_margin_top_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li a.betterdocs-term-explore-more p' ).css( 'margin-top', to + 'px' );
        } );
    }); 

    //Explore More Right Margin (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_explore_more_margin_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li a.betterdocs-term-explore-more p' ).css( 'margin-right', to + 'px' );
        } );
    }); 

    //Explore More Bottom Margin (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_explore_more_margin_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li a.betterdocs-term-explore-more p' ).css( 'margin-bottom', to + 'px' );
        } );
    }); 

    //Explore More Left Margin (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_explore_more_margin_left_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li a.betterdocs-term-explore-more p' ).css( 'margin-left', to + 'px' );
        } );
    }); 

    //Explore More Arrow Height(Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_explore_more_arrow_height_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li.betterdocs-explore-more .doc-explore-more' ).css( 'height', to + 'px' );
        } );
    }); 


    //Explore More Arrow Width(Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_explore_more_arrow_width_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li.betterdocs-explore-more .doc-explore-more' ).css( 'width', to + 'px' );
        } );
    }); 

    //Explore More Arrow Color(Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_list_explore_more_arrow_color_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-doc-list li.betterdocs-explore-more .doc-explore-more path' ).css( 'fill', to );
        } );
    }); 

    //Category Select Font Size
    wp.customize( 'betterdocs_category_select_font_size', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-searchform .betterdocs-search-category').css('font-size', to + 'px');
        });
    });

    //Category Select Font Weight
    wp.customize( 'betterdocs_category_select_font_weight', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-searchform .betterdocs-search-category').css('font-weight', to);
        });
    });

    //Category Select Text Transform
    wp.customize( 'betterdocs_category_select_text_transform', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-searchform .betterdocs-search-category').css('text-transform', to);
        });
    });

    //Category Select Font Color
    wp.customize( 'betterdocs_category_select_text_color', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-searchform .betterdocs-search-category').css('color', to);
        });
    });

    //Doc List Background Color(Docs Layout-5)
    wp.customize( 'betterdocs_doc_page_article_list_bg_color_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-list-popular .betterdocs-archive-popular .betterdocs-categories-wrap.betterdocs-popular-list.single-kb').css( 'background-color', to );
        } );
    });

    //Doc List Color(Docs Layout-5)
    wp.customize( 'betterdocs_doc_page_article_list_color_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-list-popular .betterdocs-archive-popular .single-kb ul li a').css( 'color', to );
        } );
    });

    //Doc List Font Size(Layout-5)
    wp.customize( 'betterdocs_doc_page_article_list_font_size_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-list-popular .betterdocs-archive-popular .single-kb ul li a').css( 'font-size', to + 'px' );
        } );
    });

    //Doc List Title Font Size(Layout-5)
    wp.customize( 'betterdocs_doc_page_article_title_font_size_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-list-popular .betterdocs-archive-popular .single-kb .popular-title').css( 'font-size', to + 'px' );
        } );
    });    

    //Doc List Title Color(Layout-5)
    wp.customize( 'betterdocs_doc_page_article_title_color_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-list-popular .betterdocs-archive-popular .single-kb .popular-title').css( 'color', to );
        } );
    });  

    //Doc List Icon Color(Layout-5)
    wp.customize( 'betterdocs_doc_page_article_list_icon_color_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-popular-list.single-kb ul li svg path').css( 'fill', to );
        } );
    });  
    
    //Doc List Icon Font Size
    wp.customize( 'betterdocs_doc_page_article_list_icon_font_size_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-popular-list.single-kb ul li svg ').css( 'min-width', to + 'px' );
        } );
    });  

    //Doc List Margin Top(Layout-5)
    wp.customize( 'betterdocs_doc_page_article_list_margin_top_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-list-popular .betterdocs-archive-popular .betterdocs-categories-wrap.betterdocs-popular-list.single-kb ul').css( 'margin-top', to + 'px' );
        } );
    });  

    //Doc List Margin Right(Layout-5)
    wp.customize( 'betterdocs_doc_page_article_list_margin_right_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-list-popular .betterdocs-archive-popular .betterdocs-categories-wrap.betterdocs-popular-list.single-kb ul').css( 'margin-right', to + 'px' );
        } );
    }); 
    
    //Doc List Margin Bottom(Layout-5)
    wp.customize( 'betterdocs_doc_page_article_list_margin_bottom_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-list-popular .betterdocs-archive-popular .betterdocs-categories-wrap.betterdocs-popular-list.single-kb ul').css( 'margin-bottom', to + 'px' );
        } );
    });  

    //Doc List Margin Left(Layout-5)
    wp.customize( 'betterdocs_doc_page_article_list_margin_left_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-list-popular .betterdocs-archive-popular .betterdocs-categories-wrap.betterdocs-popular-list.single-kb ul').css( 'margin-left', to + 'px' );
        } );
    });  

    //Doc List Padding Top(Layout-5)
    wp.customize( 'betterdocs_doc_page_popular_docs_padding_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-list-popular .betterdocs-archive-popular .betterdocs-categories-wrap.betterdocs-popular-list.single-kb ul').css( 'padding-top', to + 'px');
        } );
    });

    //Doc List Padding Right(Layout-5)
    wp.customize( 'betterdocs_doc_page_popular_docs_padding_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-list-popular .betterdocs-archive-popular .betterdocs-categories-wrap.betterdocs-popular-list.single-kb ul').css( 'padding-right', to + 'px');
        } );
    });

    //Doc List Padding Bottom(Layout-5)
    wp.customize( 'betterdocs_doc_page_popular_docs_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-list-popular .betterdocs-archive-popular .betterdocs-categories-wrap.betterdocs-popular-list.single-kb ul').css( 'padding-bottom', to + 'px');
        } );
    });

    //Doc List Padding Left(Layout-5)
    wp.customize( 'betterdocs_doc_page_popular_docs_padding_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-list-popular .betterdocs-archive-popular .betterdocs-categories-wrap.betterdocs-popular-list.single-kb ul').css( 'padding-left', to + 'px');
        } );
    });

    //Popular Title Margin Top(Docs)
    wp.customize( 'betterdocs_doc_page_popular_title_margin_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-list-popular .betterdocs-archive-popular .single-kb .popular-title').css( 'margin-top', to + 'px');
        } );
    });

    //Popular Title Margin Right(Docs)
    wp.customize( 'betterdocs_doc_page_popular_title_margin_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-list-popular .betterdocs-archive-popular .single-kb .popular-title').css( 'margin-right', to + 'px');
        } );
    });

    //Popular Title Margin Bottom(Docs)
    wp.customize( 'betterdocs_doc_page_popular_title_margin_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-list-popular .betterdocs-archive-popular .single-kb .popular-title').css( 'margin-bottom', to + 'px');
        } );
    });

    //Popular Title Margin Left(Docs)
    wp.customize( 'betterdocs_doc_page_popular_title_margin_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-list-popular .betterdocs-archive-popular .single-kb .popular-title').css( 'margin-left', to + 'px');
        } );
    });


    //Popular Title Margin Top(MKB)
    wp.customize( 'betterdocs_mkb_popular_title_margin_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-popular-list.multiple-kb .popular-title').css( 'margin-top', to + 'px');
        } );
    });

    //Popular Title Margin Right(MKB)
    wp.customize( 'betterdocs_mkb_popular_title_margin_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-popular-list.multiple-kb .popular-title').css( 'margin-right', to + 'px');
        } );
    });

    //Popular Title Margin Bottom(MKB)
    wp.customize( 'betterdocs_mkb_popular_title_margin_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-popular-list.multiple-kb .popular-title').css( 'margin-bottom', to + 'px');
        } );
    });

    //Popular Title Margin Bottom(MKB)
    wp.customize( 'betterdocs_mkb_popular_title_margin_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-popular-list.multiple-kb .popular-title').css( 'margin-left', to + 'px');
        } );
    });

	// FAQ Section Title Margin Layout 1

    wp.customize( 'betterdocs_faq_title_margin_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-faq-section-title.faq-mkb').css('margin', formatData( JSON.parse( to ) ) );
        } );
    });

    // FAQ Section Title Color Layout 1
    wp.customize( 'betterdocs_faq_title_color_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-faq-section-title.faq-mkb').css( 'color', to );
        } );
    });

    // FAQ Section Font Size Layout 1
    wp.customize( 'betterdocs_faq_title_font_size_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-faq-section-title.faq-mkb').css( 'font-size', to + 'px' );
        } );
    });

    // FAQ Category Title Color Layout 1
    wp.customize( 'betterdocs_faq_category_title_color_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-faq-main-wrapper-layout-1.faq-mkb .betterdocs-faq-title h2').css( 'color', to );
        } );
    });

    // FAQ Category Title Font Size
    wp.customize( 'betterdocs_faq_category_name_font_size_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-faq-main-wrapper-layout-1.faq-mkb .betterdocs-faq-title h2').css( 'font-size', to + 'px' );
        } );
    });

    // FAQ Category Title Padding
    wp.customize( 'betterdocs_faq_category_name_padding_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-faq-main-wrapper-layout-1.faq-mkb .betterdocs-faq-title h2').css('padding', formatData( JSON.parse( to ) ) );
        } );
    });

    // FAQ List Color
    wp.customize( 'betterdocs_faq_list_color_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-faq-main-wrapper-layout-1.faq-mkb .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post .betterdocs-faq-post-name').css( 'color', to );
        } );
    });

    // FAQ List Background Color
    wp.customize( 'betterdocs_faq_list_background_color_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-faq-main-wrapper-layout-1.faq-mkb .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post').css( 'background-color', to );
        } );
    });

    // FAQ List Content Background Color
    wp.customize( 'betterdocs_faq_list_content_background_color_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-faq-main-wrapper-layout-1.faq-mkb .betterdocs-faq-group .betterdocs-faq-main-content').css( 'background-color', to );
        } );
    });

    // FAQ List Content Color
    wp.customize( 'betterdocs_faq_list_content_color_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-faq-main-wrapper-layout-1.faq-mkb .betterdocs-faq-group .betterdocs-faq-main-content').css( 'color', to );
        } );
    });

    // FAQ List Content Font Size
    wp.customize( 'betterdocs_faq_list_content_font_size_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-faq-main-wrapper-layout-1.faq-mkb .betterdocs-faq-group .betterdocs-faq-main-content').css( 'font-size', to + 'px' );
        } );
    });

    // FAQ List Font Size
    wp.customize( 'betterdocs_faq_list_font_size_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-faq-main-wrapper-layout-1.faq-mkb .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post .betterdocs-faq-post-name').css( 'font-size', to + 'px' );
        } );
    });

    // FAQ List Padding
    wp.customize( 'betterdocs_faq_list_padding_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-faq-main-wrapper-layout-1.faq-mkb .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post').css( 'padding', formatData( JSON.parse(to) ) );
        } );
    });

    /** MKB FAQ CONTROLLERS LAYOUT 2 **/

    // FAQ Category Title Color

    wp.customize( 'betterdocs_faq_category_title_color_mkb_layout_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-faq-main-wrapper-layout-2.faq-mkb .betterdocs-faq-title h2').css( 'color', to );
        } );
    });

    // FAQ Category Title Font Size

    wp.customize( 'betterdocs_faq_category_name_font_size_mkb_layout_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-faq-main-wrapper-layout-2.faq-mkb .betterdocs-faq-title h2').css( 'font-size', to + 'px' );
        } );
    });

    // FAQ Category Title Padding

    wp.customize( 'betterdocs_faq_category_name_padding_mkb_layout_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-faq-main-wrapper-layout-2.faq-mkb .betterdocs-faq-title h2').css( 'padding', formatData( JSON.parse( to ) ) );
        } );
    });

    // FAQ List Color

    wp.customize( 'betterdocs_faq_list_color_mkb_layout_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-faq-main-wrapper-layout-2.faq-mkb .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post-layout-2 .betterdocs-faq-post-name').css( 'color', to );
        } );
    });

    // FAQ List Background Color

    wp.customize( 'betterdocs_faq_list_background_color_mkb_layout_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-faq-main-wrapper-layout-2.faq-mkb .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2 .betterdocs-faq-post-layout-2').css( 'background-color', to );
        } );
    });

    // FAQ List Content Background Color

    wp.customize( 'betterdocs_faq_list_content_background_color_mkb_layout_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-faq-main-wrapper-layout-2.faq-mkb .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2 .betterdocs-faq-main-content-layout-2').css( 'background-color', to );
        } );
    });

    // FAQ List Content Color

    wp.customize( 'betterdocs_faq_list_content_color_mkb_layout_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-faq-main-wrapper-layout-2.faq-mkb .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2 .betterdocs-faq-main-content-layout-2').css( 'color', to );
        } );
    });

    // FAQ List Content Background Color

    wp.customize( 'betterdocs_faq_list_content_font_size_mkb_layout_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-faq-main-wrapper-layout-2.faq-mkb .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2 .betterdocs-faq-main-content-layout-2').css( 'font-size', to + 'px' );
        } );
    });

    // FAQ List Font Size

    wp.customize( 'betterdocs_faq_list_font_size_mkb_layout_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-faq-main-wrapper-layout-2.faq-mkb .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post-layout-2 .betterdocs-faq-post-name').css( 'font-size', to + 'px' );
        } );
    });

    // FAQ List Padding

    wp.customize( 'betterdocs_faq_list_padding_mkb_layout_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-faq-main-wrapper-layout-2.faq-mkb .betterdocs-faq-list-layout-2 > li .betterdocs-faq-group-layout-2 .betterdocs-faq-post-layout-2').css( 'padding', formatData( JSON.parse( to ) ) );
        } );
    });

} )( jQuery );


function formatData( data ) {

    var dimensions = '';

    for( let key in data ) {
        if( data[key] != '' ) {
            dimensions += data[key] + 'px ';
        } else {
            dimensions += 0 + 'px ';
        }
    }

    return dimensions;
}