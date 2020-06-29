/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {	
    wp.customize( 'betterdocs_doc_page_cat_title_font_size2', function( value ) {
        value.bind( function( to ) {
            $( '.docs-cat-list-2-items .docs-cat-title' ).css( 'font-size', to + 'px' );
        } );
    });
    wp.customize( 'betterdocs_live_search_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wraper .betterdocs-search-form-wrap.cat-layout-4' ).css( 'padding-bottom', parseInt(to)+ parseInt(80) + 'px' );
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
    wp.customize( 'betterdocs_sidebar_title_bg_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-single-layout2 .betterdocs-full-sidebar-left .betterdocs-sidebar-content .betterdocs-categories-wrap .docs-cat-title-inner' ).css( 'background-color', to );
        } );
    });
    wp.customize( 'betterdocs_sidebar_title_padding_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-single-layout2 .betterdocs-full-sidebar-left .betterdocs-sidebar-content .betterdocs-categories-wrap .docs-cat-title-inner' ).css( 'padding-top', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_sidebar_title_padding_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-single-layout2 .betterdocs-full-sidebar-left .betterdocs-sidebar-content .betterdocs-categories-wrap .docs-cat-title-inner' ).css( 'padding-right', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_sidebar_title_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-single-layout2 .betterdocs-full-sidebar-left .betterdocs-sidebar-content .betterdocs-categories-wrap .docs-cat-title-inner' ).css( 'padding-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_sidebar_title_padding_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-single-layout2 .betterdocs-full-sidebar-left .betterdocs-sidebar-content .betterdocs-categories-wrap .docs-cat-title-inner' ).css( 'padding-left', to + 'px' );
        } );
    });
} )( jQuery );
