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
        } );
    });

    wp.customize( 'betterdocs_mkb_column_padding_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-categories-wrap.multiple-kb .docs-single-cat-wrap .docs-cat-title-wrap,.betterdocs-categories-wrap.betterdocs-category-box .docs-single-cat-wrap,.betterdocs-categories-wrap .docs-single-cat-wrap.docs-cat-list-2-box' ).css( 'padding-top', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_padding_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-categories-wrap.multiple-kb .docs-single-cat-wrap .docs-cat-title-wrap,.docs-item-container,.betterdocs-categories-wrap.betterdocs-category-box .docs-single-cat-wrap,.betterdocs-categories-wrap .docs-single-cat-wrap.docs-cat-list-2-box' ).css( 'padding-right', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.docs-item-container,.betterdocs-categories-wrap.multiple-kb.betterdocs-category-box .docs-single-cat-wrap,.betterdocs-categories-wrap .docs-single-cat-wrap.docs-cat-list-2-box' ).css( 'padding-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_padding_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-categories-wrap.multiple-kb .docs-single-cat-wrap .docs-cat-title-wrap,.docs-item-container,.betterdocs-categories-wrap.betterdocs-category-box .docs-single-cat-wrap,.betterdocs-categories-wrap .docs-single-cat-wrap.docs-cat-list-2-box' ).css( 'padding-left', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_bg_color2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-box.multiple-kb.ash-bg .docs-single-cat-wrap' ).css( 'background-color', to );
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
            $( '.betterdocs-category-box.multiple-kb .docs-single-cat-wrap img' ).css( 'height', to + 'px' );
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
        } );
    });

    wp.customize( 'betterdocs_mkb_cat_title_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-category-box.multiple-kb .docs-single-cat-wrap .docs-cat-title, .betterdocs-archive-mkb .docs-cat-list-2 .docs-cat-title' ).css( 'color', to );
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

    // Multiple KB end

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
} )( jQuery );
