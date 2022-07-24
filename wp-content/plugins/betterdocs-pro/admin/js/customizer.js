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
    wp.customize( 'betterdocs_post_reactions_text_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-article-reactions .betterdocs-article-reactions-heading h5' ).css( 'color', to );
        } );
    });
    
    wp.customize( 'betterdocs_post_reactions_icon_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-article-reaction-links li a' ).css( 'background-color', to );
        } );
    });
    wp.customize( 'betterdocs_post_reactions_icon_hover_bg_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-article-reaction-links li a:hover' ).css( 'background-color', to );
        } );
    });
    wp.customize( 'betterdocs_post_reactions_icon_svg_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-article-reaction-links li a svg path' ).css( 'fill', to );
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
} )( jQuery );
