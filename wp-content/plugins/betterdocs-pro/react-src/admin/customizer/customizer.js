/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {

    //Doc Layout 6 Category Image Width
    wp.customize( 'betterdocs_doc_list_img_width_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-term-img' ).css( 'flex-basis', to + '%'  );
        } );
    });

    //Doc Layout 6 Category Title Padding Bottom
    wp.customize( 'betterdocs_doc_page_cat_title_padding_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-dynamic-wrapper' ).css( 'padding-bottom', to + 'px'  );
        } );
    });

    //Doc Category Title Font Size for Layout 6
    wp.customize( 'betterdocs_doc_page_cat_title_font_size_layout6', function( value ) {
        value.bind( function( to ) {
            console.log(to);
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-wrapper .betterdocs-category-grid-list-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-single-category-inner .betterdocs-category-title a' ).css( 'font-size', to + 'px' );
        } );
    });

    // Item Count Font Size (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_item_count_font_size_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-items-counts' ).css( 'font-size', to + 'px');
        } );
    });

    // Item Count Color (Doc Page Layout 6)
    wp.customize( 'betterdocs_doc_page_item_count_color_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-items-counts' ).css( 'color', to );
        } );
    });

    //Doc Layout 6 Item Count Background
    wp.customize( 'betterdocs_doc_page_item_count_back_color_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-items-counts' ).css( 'background-color', to);
        } );
    });

    //Doc Layout 6 Item Count Border Style
    wp.customize( 'betterdocs_doc_page_item_count_border_type_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-items-counts' ).css( 'border-style', to);
        } );
    });

    //Doc Layout 6 Item Count Border Color
    wp.customize( 'betterdocs_doc_page_item_count_border_color_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-items-counts' ).css( 'border-color', to);
        } );
    });

    //Doc Layout 6 Item Count Border Top Width
    wp.customize( 'betterdocs_doc_page_item_count_border_width_top_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-items-counts' ).css( 'border-top-width', to + 'px');
        } );
    });

    //Doc Layout 6 Item Count Border Right Width
    wp.customize( 'betterdocs_doc_page_item_count_border_width_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-items-counts' ).css( 'border-right-width', to + 'px');
        } );
    });

    //Doc Layout 6 Item Count Border Bottom Width
    wp.customize( 'betterdocs_doc_page_item_count_border_width_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-items-counts' ).css( 'border-bottom-width', to + 'px');
        } );
    });

    //Doc Layout 6 Item Count Border Left Width
    wp.customize( 'betterdocs_doc_page_item_count_border_width_left_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-items-counts' ).css( 'border-left-width', to + 'px');
        } );
    });

    //Doc Layout 6 Item Count Border Top-Left Radius
    wp.customize( 'betterdocs_doc_page_item_count_border_radius_top_left_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-items-counts' ).css( 'border-top-left-radius', to + 'px');
        } );
    });

    //Doc Layout 6 Item Count Border Top-Right Radius
    wp.customize( 'betterdocs_doc_page_item_count_border_radius_top_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-items-counts' ).css( 'border-top-right-radius', to + 'px');
        } );
    });

    //Doc Layout 6 Item Count Border Bottom-Right Radius
    wp.customize( 'betterdocs_doc_page_item_count_border_radius_bottom_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-items-counts' ).css( 'border-bottom-right-radius', to + 'px');
        } );
    });

    //Doc Layout 6 Item Count Border Bottom-Left Radius
    wp.customize( 'betterdocs_doc_page_item_count_border_radius_bottom_left_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-items-counts' ).css( 'border-bottom-left-radius', to + 'px');
        } );
    });

    //Doc Layout 6 Item Count Margin Top
    wp.customize( 'betterdocs_doc_page_item_count_margin_top_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-items-counts' ).css( 'margin-top', to + 'px');
        } );
    });

    //Doc Layout 6 Item Count Margin Right
    wp.customize( 'betterdocs_doc_page_item_count_margin_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-items-counts' ).css( 'margin-right', to + 'px');
        } );
    });

    //Doc Layout 6 Item Count Margin Bottom
    wp.customize( 'betterdocs_doc_page_item_count_margin_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-items-counts' ).css( 'margin-bottom', to + 'px');
        } );
    });

    //Doc Layout 6 Item Count Margin Left
    wp.customize( 'betterdocs_doc_page_item_count_margin_left_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-items-counts' ).css( 'margin-left', to + 'px');
        } );
    });

    //Doc Layout 6 Item Count Padding Top
    wp.customize( 'betterdocs_doc_page_item_count_padding_top_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-items-counts' ).css( 'padding-top', to + 'px');
        } );
    });

    //Doc Layout 6 Item Count Padding Right
    wp.customize( 'betterdocs_doc_page_item_count_padding_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-items-counts' ).css( 'padding-right', to + 'px');
        } );
    });

    //Doc Layout 6 Item Count Padding Bottom
    wp.customize( 'betterdocs_doc_page_item_count_padding_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-items-counts' ).css( 'padding-bottom', to + 'px');
        } );
    });

    //Doc Layout 6 Item Count Padding Left
    wp.customize( 'betterdocs_doc_page_item_count_padding_left_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-items-counts' ).css( 'padding-left', to + 'px');
        } );
    });

    // Doc Layout 4 Content Overlap
    wp.customize( 'betterdocs_doc_page_content_overlap', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-content-wrapper' ).css( 'margin-top', '-' + to + 'px' );
        } );
    });

    //Box Category Icon for Layout 4
    wp.customize( 'betterdocs_doc_page_cat_icon_size_l_3_4', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-3 .betterdocs-category-box-wrapper .betterdocs-single-category-wrapper .betterdocs-category-header .betterdocs-category-icon .betterdocs-category-icon-img' ).css( 'height',  to + 'px'  );
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-category-box-wrapper .betterdocs-single-category-wrapper .betterdocs-category-header .betterdocs-category-icon .betterdocs-category-icon-img' ).css( 'height',  to + 'px'  );
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-category-box-wrapper .betterdocs-single-category-wrapper .betterdocs-category-header .betterdocs-category-icon .betterdocs-category-icon-img' ).css( 'height',  to + 'px'  );
        } );
    });

    //Doc Category List Title Font Size for Layout 4
    wp.customize( 'betterdocs_doc_page_cat_title_font_size2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper > .betterdocs-single-category-wrapper .betterdocs-single-category-inner .betterdocs-category-title' ).css( 'font-size', to + 'px' );
        } );
    });

    //Doc Layout 6 List Font Size
    wp.customize( 'betterdocs_doc_list_font_size_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a' ).css( 'font-size', to + 'px');
        } );
    });

    //Doc Layout 6 List Font Line Height
    wp.customize( 'betterdocs_doc_list_font_line_height_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a' ).css( 'line-height', to + 'px');
        } );
    });

    //Doc layout 6 List Font Weight
    wp.customize( 'betterdocs_doc_list_font_weight_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a' ).css( 'font-weight', to );
        } );
    });

    //Doc Layout 6 Description Color
    wp.customize( 'betterdocs_doc_list_desc_color_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-description' ).css( 'color', to );
        } );
    });

    //Doc Layout 6 Description Font Size
    wp.customize( 'betterdocs_doc_list_desc_font_size_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-description' ).css( 'font-size', to + 'px' );
        } );
    });

    //Doc Layout 6 Description Font Weight
    wp.customize( 'betterdocs_doc_list_desc_font_weight_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-description' ).css( 'font-weight', to );
        } );
    });

    //Doc Layout 6 Description Font Line Height
    wp.customize( 'betterdocs_doc_list_desc_line_height_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-description' ).css( 'line-height', to + 'px' );
        } );
    });

    //Doc Layout 6 Description Margin Top | Right | Bottom | Left
    wp.customize( 'betterdocs_doc_list_desc_margin_top_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-description' ).css( 'margin-top', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_doc_list_desc_margin_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-description' ).css( 'margin-right', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_doc_list_desc_margin_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-description' ).css( 'margin-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_doc_list_desc_margin_left_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-category-description' ).css( 'margin-left', to + 'px' );
        } );
    });

    //Doc Layout 6 List Font Color
    wp.customize( 'betterdocs_doc_list_font_color_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a' ).css( 'color', to  );
        } );
    });

    //Doc Layout 6 List Margin Top
    wp.customize( 'betterdocs_doc_list_margin_top_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a' ).css( 'margin-top', to + 'px'  );
        } );
    });

    //Doc Layout 6 List Margin Right
    wp.customize( 'betterdocs_doc_list_margin_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a' ).css( 'margin-right', to + 'px'  );
        } );
    });

    //Doc Layout 6 List Margin Bottom
    wp.customize( 'betterdocs_doc_list_margin_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a' ).css( 'margin-bottom', to + 'px'  );
        } );
    });

    //Doc Layout 6 List Margin Left
    wp.customize( 'betterdocs_doc_list_margin_left_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a' ).css( 'margin-left', to + 'px'  );
        } );
    });

    //Doc Layout 6 List Padding Top
    wp.customize( 'betterdocs_doc_list_padding_top_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a' ).css( 'padding-top', to + 'px'  );
        } );
    });

    //Doc Layout 6 List Padding Right
    wp.customize( 'betterdocs_doc_list_padding_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a' ).css( 'padding-right', to + 'px'  );
        } );
    });

    //Doc Layout 6 List Padding Bottom
    wp.customize( 'betterdocs_doc_list_padding_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a' ).css( 'padding-bottom', to + 'px'  );
        } );
    });

    //Doc Layout 6 List Padding Left
    wp.customize( 'betterdocs_doc_list_padding_left_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a' ).css( 'padding-left', to + 'px'  );
        } );
    });

    //Doc Layout 6 List Border Style
    wp.customize( 'betterdocs_doc_list_border_style_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a' ).css( 'border-style', to   );
        } );
    });

    //Doc Layout 6 List Border Width Top
    wp.customize( 'betterdocs_doc_list_border_top_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a' ).css( 'border-top-width', to + 'px' );
        } );
    });

    //Doc Layout 6 List Border Width Right
    wp.customize( 'betterdocs_doc_list_border_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a' ).css( 'border-right-width', to + 'px' );
        } );
    });

    //Doc Layout 6 List Border Width Bottom
    wp.customize( 'betterdocs_doc_list_border_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a' ).css( 'border-bottom-width', to + 'px' );
        } );
    });

    //Doc Layout 6 List Border Width Left
    wp.customize( 'betterdocs_doc_list_border_left_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a' ).css( 'border-left-width', to + 'px' );
        } );
    });

    //Doc Layout 6 List Border Color Top
    wp.customize( 'betterdocs_doc_list_border_color_top_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a' ).css( 'border-top-color', to );
        } );
    });

    //Doc Layout 6 List Border Color Right
    wp.customize( 'betterdocs_doc_list_border_color_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a' ).css( 'border-right-color', to );
        } );
    });

    //Doc Layout 6 List Border Color Bottom
    wp.customize( 'betterdocs_doc_list_border_color_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a' ).css( 'border-bottom-color', to );
        } );
    });

    //Doc Layout 6 List Border Color Left
    wp.customize( 'betterdocs_doc_list_border_color_left_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a' ).css( 'border-left-color', to );
        } );
    });

    //Doc Layout 6 List Arrow Height
    wp.customize( 'betterdocs_doc_list_arrow_height_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a svg' ).css( 'height', to + 'px' );
        } );
    });

    //Doc Layout 6 List Arrow Width
    wp.customize( 'betterdocs_doc_list_arrow_width_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a svg' ).css( 'width', to + 'px' );
        } );
    });

    //Doc Layout 6 List Arrow Color
    wp.customize( 'betterdocs_doc_list_arrow_color_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-body .betterdocs-articles-list li a svg' ).css( 'fill', to );
        } );
    });

    //Doc Layout 6 Explore More Font Size
    wp.customize( 'betterdocs_doc_list_explore_more_font_size_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer button, .betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer a' ).css( 'font-size', to + 'px');
        } );
    });

    //Doc Layout 6 Explore More Line Height
    wp.customize( 'betterdocs_doc_list_explore_more_font_line_height_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer button, .betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer a' ).css( 'line-height', to + 'px');
        } );
    });

    //Doc Layout 6 Explore More Font Color
    wp.customize( 'betterdocs_doc_list_explore_more_font_color_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer button, .betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer a' ).css( 'color', to);
        } );
    });

    //Doc Layout 6 Explore More Font Weight
    wp.customize( 'betterdocs_doc_list_explore_more_font_weight_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer button, .betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer a' ).css( 'font-weight', to);
        } );
    });

    //Doc Layout 6 Explore More Padding
    wp.customize( 'betterdocs_doc_list_explore_more_padding_top_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer button, .betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer a' ).css( 'padding-top', to + 'px');
        } );
    });

    wp.customize( 'betterdocs_doc_list_explore_more_padding_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer button, .betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer a' ).css( 'padding-right', to + 'px');
        } );
    });

    wp.customize( 'betterdocs_doc_list_explore_more_padding_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer button, .betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer a' ).css( 'padding-bottom', to + 'px');
        } );
    });

    wp.customize( 'betterdocs_doc_list_explore_more_padding_left_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer button, .betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer a' ).css( 'padding-left', to + 'px');
        } );
    });

    //Doc Layout 6 Explore More Margin
    wp.customize( 'betterdocs_doc_list_explore_more_margin_top_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer button, .betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer a' ).css( 'margin-top', to + 'px');
        } );
    });

    wp.customize( 'betterdocs_doc_list_explore_more_margin_right_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer button, .betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer a' ).css( 'margin-right', to + 'px');
        } );
    });

    wp.customize( 'betterdocs_doc_list_explore_more_margin_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer button, .betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer a' ).css( 'margin-bottom', to + 'px');
        } );
    });

    wp.customize( 'betterdocs_doc_list_explore_more_margin_left_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer button, .betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer a' ).css( 'margin-left', to + 'px');
        } );
    });

    //Doc Layout 6 Explore More Arrow Height
    wp.customize( 'betterdocs_doc_list_explore_more_arrow_height_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer button svg, .betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer a svg' ).css( 'height', to + 'px');
        } );
    });

    //Doc Layout 6 Explore More Arrow Width
    wp.customize( 'betterdocs_doc_list_explore_more_arrow_width_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer button svg, .betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer a svg' ).css( 'width', to + 'px');
        } );
    });

    //Doc Layout 6 Explore More Arrow Color
    wp.customize( 'betterdocs_doc_list_explore_more_arrow_color_layout6', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer button svg, .betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .betterdocs-category-grid-list-inner-wrapper .betterdocs-footer a svg' ).css( 'fill', to );
        } );
    });

    //Doc Layout 5 Popular Docs Background Color
    wp.customize( 'betterdocs_doc_page_article_list_bg_color_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-popular-article-list-wrapper' ).css( 'background-color', to );
        } );
    });

    //Doc Layout 5 Popular Docs List Color
    wp.customize( 'betterdocs_doc_page_article_list_color_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-popular-article-list-wrapper .betterdocs-articles-list li a' ).css( 'color', to  );
        } );
    });

    //Doc Layout 5 Popular Docs List Font Size
    wp.customize( 'betterdocs_doc_page_article_list_font_size_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-popular-article-list-wrapper .betterdocs-articles-list li a' ).css( 'font-size', to + 'px'  );
        } );
    });

    //Doc Layout 5 Popular Title Font Size
    wp.customize( 'betterdocs_doc_page_article_title_font_size_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-popular-article-list-wrapper .betterdocs-popular-articles-heading' ).css( 'font-size', to + 'px'  );
        } );
    });

    //Doc Layout 5 Popular Title Color
    wp.customize( 'betterdocs_doc_page_article_title_color_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-popular-article-list-wrapper .betterdocs-popular-articles-heading' ).css( 'color', to );
        } );
    });

    //Doc Layout 5 Popular List Icon Color
    wp.customize( 'betterdocs_doc_page_article_list_icon_color_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-popular-article-list-wrapper .betterdocs-articles-list li svg' ).css( 'fill', to );
        } );
    });

    //Doc Layout 5 Popular List Icon Font Size
    wp.customize( 'betterdocs_doc_page_article_list_icon_font_size_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-popular-article-list-wrapper .betterdocs-articles-list li svg' ).css( 'min-width', to + 'px'  );
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-popular-article-list-wrapper .betterdocs-articles-list li svg' ).css( 'width', to + 'px'  );
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-popular-article-list-wrapper .betterdocs-articles-list li svg' ).css( 'font-size', to + 'px'  );
        } );
    });

    //Doc Layout 5 Popular Docs Title Margin Top
    wp.customize( 'betterdocs_doc_page_popular_title_margin_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-popular-article-list-wrapper .betterdocs-popular-articles-heading' ).css( 'margin-top', to + 'px'  );
        } );
    });

    //Doc Layout 5 Popular Docs Title Margin Right
    wp.customize( 'betterdocs_doc_page_popular_title_margin_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-popular-article-list-wrapper .betterdocs-popular-articles-heading' ).css( 'margin-right', to + 'px'  );
        } );
    });

    //Doc Layout 5 Popular Docs Title Margin Bottom
    wp.customize( 'betterdocs_doc_page_popular_title_margin_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-popular-article-list-wrapper .betterdocs-popular-articles-heading' ).css( 'margin-bottom', to + 'px'  );
        } );
    });

    //Doc Layout 5 Popular Docs Title Margin Left
    wp.customize( 'betterdocs_doc_page_popular_title_margin_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-popular-article-list-wrapper .betterdocs-popular-articles-heading' ).css( 'margin-left', to + 'px'  );
        } );
    });

    //Doc Layout 5 Popular Docs List Margin Top
    wp.customize( 'betterdocs_doc_page_article_list_margin_top_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-popular-article-list-wrapper .betterdocs-articles-list' ).css( 'margin-top', to + 'px'  );
        } );
    });

    //Doc Layout 5 Popular Docs List Margin Right
    wp.customize( 'betterdocs_doc_page_article_list_margin_right_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-popular-article-list-wrapper .betterdocs-articles-list' ).css( 'margin-right', to + 'px'  );
        } );
    });

    //Doc Layout 5 Popular Docs List Margin Bottom
    wp.customize( 'betterdocs_doc_page_article_list_margin_bottom_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-popular-article-list-wrapper .betterdocs-articles-list' ).css( 'margin-bottom', to + 'px'  );
        } );
    });

    //Doc Layout 5 Popular Docs List Margin Left
    wp.customize( 'betterdocs_doc_page_article_list_margin_left_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-popular-article-list-wrapper .betterdocs-articles-list' ).css( 'margin-left', to + 'px'  );
        } );
    });

    //Doc Layout 5 Popular Docs Padding Top
    wp.customize( 'betterdocs_doc_page_popular_docs_padding_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-popular-article-list-wrapper .betterdocs-articles-list' ).css( 'padding-top', to + 'px'  );
        } );
    });

    //Doc Layout 5 Popular Docs Padding Right
    wp.customize( 'betterdocs_doc_page_popular_docs_padding_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-popular-article-list-wrapper .betterdocs-articles-list' ).css( 'padding-right', to + 'px'  );
        } );
    });

    //Doc Layout 5 Popular Docs Padding Bottom
    wp.customize( 'betterdocs_doc_page_popular_docs_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-popular-article-list-wrapper .betterdocs-articles-list' ).css( 'padding-bottom', to + 'px'  );
        } );
    });

    //Doc Layout 5 Popular Docs Padding Left
    wp.customize( 'betterdocs_doc_page_popular_docs_padding_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-popular-article-list-wrapper .betterdocs-articles-list' ).css( 'padding-left', to + 'px'  );
        } );
    });

    // TODO:
    // FIXME:

    //MKB Content Area Background Color (Common Controls For All MKB Layouts)
    wp.customize( 'betterdocs_mkb_background_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-mkb-wrapper' ).css( 'background', to );
        } );
    });

    //MKB Content Area Background Size (Common Controls For All MKB Layouts)
    wp.customize( 'betterdocs_mkb_background_size', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-mkb-wrapper' ).css( 'background-size', to);
        } );
    });

    //MKB Content Area Background Repeat (Common Controls For All MKB Layouts)
    wp.customize( 'betterdocs_mkb_background_repeat', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-mkb-wrapper' ).css( 'background-repeat', to);
        } );
    });

    //MKB Content Area Background Attachment (Common Controls For All MKB Layouts)
    wp.customize( 'betterdocs_mkb_background_attachment', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-mkb-wrapper' ).css( 'background-attachment', to);
        } );
    });

    //MKB Content Area Background Position (Common Controls For All MKB Layouts)
    wp.customize( 'betterdocs_mkb_background_position', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-mkb-wrapper' ).css( 'background-position', to);
        } );
    });

    //MKB Content Area Padding (Common Controls For All MKB Layouts)
    wp.customize( 'betterdocs_mkb_content_padding_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-mkb-wrapper' ).css( 'padding-top', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_content_padding_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-mkb-wrapper' ).css( 'padding-right', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_content_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-mkb-wrapper' ).css( 'padding-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_content_padding_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-mkb-wrapper' ).css( 'padding-left', to + 'px' );
        } );
    });

    //MKB Content Area Width (Common Controls For All MKB Layouts)
    wp.customize( 'betterdocs_mkb_content_width', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-mkb-wrapper .betterdocs-content-wrapper' ).css( 'width', to + '%' );
        } );
    });

    //MKB Content Area Max Width (Common Controls For All MKB Layouts)
    wp.customize( 'betterdocs_mkb_content_max_width', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-mkb-wrapper .betterdocs-content-wrapper' ).css( 'max-width', to + 'px' );
        } );
    });

    //MKB Content Area Background Image (Common Controls For All MKB Layouts)
    wp.customize( 'betterdocs_mkb_background_image', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wrapper.betterdocs-mkb-wrapper' ).css( 'background-image', 'url('+to+')');
        } );
    });

    //MKB Layout 1 Space Between Columns
    wp.customize( 'betterdocs_mkb_column_space', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-1 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'margin', to + 'px');
        } );
    });

    //MKB Layout 1 Column Background Color
    wp.customize( 'betterdocs_mkb_column_bg_color2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-1 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'background-color', to );
        } );
    });

    //MKB Layout 1 Column Padding Top | Right | Bottom | Left
    wp.customize( 'betterdocs_mkb_column_padding_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-1 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'padding-top', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_padding_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-1 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'padding-right', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-1 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'padding-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_padding_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-1 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'padding-left', to + 'px' );
        } );
    });

    //MKB Layout 1 Icon Size
    wp.customize( 'betterdocs_mkb_cat_icon_size', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-1 .betterdocs-category-icon .betterdocs-category-icon-img' ).css( 'max-height', to + 'px' );
        } );
    });

    //MKB Layout 1 Column Border Radius Top Left | Top Right | Bottom Right | Bottom Left
    wp.customize( 'betterdocs_mkb_column_borderr_topleft', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-1 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'border-top-left-radius', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_borderr_topright', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-1 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'border-top-right-radius', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_borderr_bottomright', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-1 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'border-bottom-right-radius', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_borderr_bottomleft', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-1 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'border-bottom-left-radius', to + 'px' );
        } );
    });

    //MKB Layout 1 Title Font Size
    wp.customize( 'betterdocs_mkb_cat_title_font_size', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-1 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-title' ).css( 'font-size', to + 'px' );
        } );
    });

    //MKB Layout 1 Title Color
    wp.customize( 'betterdocs_mkb_cat_title_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-1 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-title' ).css( 'color', to );
        } );
    });

    //MKB Layout 1 KB Description Color
    wp.customize( 'betterdocs_mkb_desc_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-1 .betterdocs-category-box-wrapper .betterdocs-single-category-wrapper .betterdocs-category-description' ).css( 'color', to );
        } );
    });

    //MKB Layout 1 Item Count Color
    wp.customize( 'betterdocs_mkb_item_count_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-1 .betterdocs-category-box-wrapper .betterdocs-category-items-counts span' ).css( 'color', to );
        } );
    });

    //MKB Layout 1 Font Size
    wp.customize( 'betterdocs_mkb_item_count_font_size', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-1 .betterdocs-category-box-wrapper .betterdocs-category-items-counts span' ).css( 'font-size', to + 'px' );
        } );
    });

    //MKB Layout 1 Content Space Between Icon | Title | Description | Counter
    wp.customize( 'betterdocs_mkb_column_content_space_image', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-1 .betterdocs-category-icon .betterdocs-category-icon-img' ).css( 'margin-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_content_space_title', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-1 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-title' ).css( 'margin-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_content_space_desc', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-1 .betterdocs-category-box-wrapper .betterdocs-single-category-wrapper .betterdocs-category-description' ).css( 'margin-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_content_space_counter', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-1 .betterdocs-category-box-wrapper .betterdocs-category-items-counts' ).css( 'margin-bottom', to + 'px' );
        } );
    });

    //MKB Layout 2 Space Between Columns
    wp.customize( 'betterdocs_mkb_column_space', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-2 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'margin', to + 'px');
        } );
    });

    //MKB Layout 2 Column Background Color
    wp.customize( 'betterdocs_mkb_column_bg_color2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-2 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'background-color', to );
        } );
    });

    //MKB Layout 2 Column Padding Top | Right | Bottom | Left
    wp.customize( 'betterdocs_mkb_column_padding_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-2 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'padding-top', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_padding_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-2 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'padding-right', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-2 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'padding-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_padding_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-2 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'padding-left', to + 'px' );
        } );
    });

    //MKB Layout 2 Icon Size
    wp.customize( 'betterdocs_mkb_cat_icon_size', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-2 .betterdocs-category-icon .betterdocs-category-icon-img' ).css( 'max-height', to + 'px' );
        } );
    });

    //MKB Layout 2 Column Border Radius Top Left | Top Right | Bottom Right | Bottom Left
    wp.customize( 'betterdocs_mkb_column_borderr_topleft', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-2 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'border-top-left-radius', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_borderr_topright', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-2 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'border-top-right-radius', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_borderr_bottomright', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-2 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'border-bottom-right-radius', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_borderr_bottomleft', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-2 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'border-bottom-left-radius', to + 'px' );
        } );
    });

    //MKB Layout 2 Title Font Size
    wp.customize( 'betterdocs_mkb_cat_title_font_size', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-2 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-title' ).css( 'font-size', to + 'px' );
        } );
    });

    //MKB Layout 2 Title Color
    wp.customize( 'betterdocs_mkb_cat_title_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-2 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-title' ).css( 'color', to );
        } );
    });

    //MKB Layout 2 KB Description Color
    wp.customize( 'betterdocs_mkb_desc_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-2 .betterdocs-category-description' ).css( 'color', to );
        } );
    });

    //MKB Layout 2 Item Count Color
    wp.customize( 'betterdocs_mkb_item_count_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-2 .betterdocs-category-box-wrapper .betterdocs-category-items-counts span' ).css( 'color', to );
        } );
    });

    //MKB Layout 2 Font Size
    wp.customize( 'betterdocs_mkb_item_count_font_size', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-2 .betterdocs-category-box-wrapper .betterdocs-category-items-counts span' ).css( 'font-size', to + 'px' );
        } );
    });

    //MKB Layout 2 Content Space Between Icon | Title | Description | Counter
    wp.customize( 'betterdocs_mkb_column_content_space_image', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-2 .betterdocs-category-icon' ).css( 'margin-right', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_content_space_title', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-2 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-title' ).css( 'margin-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_content_space_desc', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-2 .betterdocs-category-description' ).css( 'margin-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_content_space_counter', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-2 .betterdocs-category-box-wrapper .betterdocs-category-items-counts' ).css( 'margin-bottom', to + 'px' );
        } );
    });

     //MKB Layout 3 Space Between Columns
     wp.customize( 'betterdocs_mkb_column_space', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'margin', to + 'px');
        } );
    });

    //MKB Layout 3 Column Background Color
    wp.customize( 'betterdocs_mkb_column_bg_color2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'background-color', to );
        } );
    });

    //MKB Layout 3 Column Padding Top | Right | Bottom | Left
    wp.customize( 'betterdocs_mkb_column_padding_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'padding-top', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_padding_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'padding-right', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'padding-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_padding_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'padding-left', to + 'px' );
        } );
    });

    //MKB Layout 3 Icon Size
    wp.customize( 'betterdocs_mkb_cat_icon_size', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-category-icon .betterdocs-category-icon-img' ).css( 'max-height', to + 'px' );
        } );
    });

    //MKB Layout 3 Column Border Radius Top Left | Top Right | Bottom Right | Bottom Left
    wp.customize( 'betterdocs_mkb_column_borderr_topleft', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'border-top-left-radius', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_borderr_topright', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'border-top-right-radius', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_borderr_bottomright', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'border-bottom-right-radius', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_borderr_bottomleft', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'border-bottom-left-radius', to + 'px' );
        } );
    });

    //MKB Layout 3 Title Font Size
    wp.customize( 'betterdocs_mkb_cat_title_font_size', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-title' ).css( 'font-size', to + 'px' );
        } );
    });

    //MKB Layout 3 Title Color
    wp.customize( 'betterdocs_mkb_cat_title_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-title' ).css( 'color', to );
        } );
    });

    //MKB Layout 3 KB Description Color
    wp.customize( 'betterdocs_mkb_desc_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-category-description' ).css( 'color', to );
        } );
    });

    //MKB Layout 3 Item Count Color
    wp.customize( 'betterdocs_mkb_item_count_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-category-box-wrapper .betterdocs-category-items-counts span' ).css( 'color', to );
        } );
    });

    //MKB Layout 3 Font Size
    wp.customize( 'betterdocs_mkb_item_count_font_size', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-category-box-wrapper .betterdocs-category-items-counts span' ).css( 'font-size', to + 'px' );
        } );
    });

    //MKB Layout 3 Content Space Between Icon | Title | Description | Counter
    wp.customize( 'betterdocs_mkb_column_content_space_image', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-category-icon' ).css( 'margin-right', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_content_space_title', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-title' ).css( 'margin-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_content_space_desc', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-category-description' ).css( 'margin-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_content_space_counter', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-category-box-wrapper .betterdocs-category-items-counts' ).css( 'margin-bottom', to + 'px' );
        } );
    });

    //MKB Layout 4 Space Between Columns
    wp.customize( 'betterdocs_mkb_column_space', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper' ).css( 'gap', to + 'px');
        } );
    });

    //MKB Layout 4 Column Background Color
    wp.customize( 'betterdocs_mkb_column_bg_color2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper > .betterdocs-single-category-wrapper .betterdocs-single-category-inner' ).css( 'background-color', to );
        } );
    });

    //MKB Layout 4 Column Padding Top | Right | Bottom | Left (Header & Body & Footer)
    wp.customize( 'betterdocs_mkb_column_padding_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-header' ).css( 'padding-top', to + 'px' );
        } );
    });

    //MKB Layout 4 Icon Size
    wp.customize( 'betterdocs_mkb_cat_icon_size', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-category-icon .betterdocs-category-icon-img' ).css( 'max-height', to + 'px' );
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-category-icon .betterdocs-category-icon-img' ).css( 'height', to + 'px' );
        } );
    });

    //MKB Layout 4 Tab List Background Color
    wp.customize( 'betterdocs_mkb_list_bg_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-tab-list a' ).css( 'background-color', to );
        } );
    });

    //MKB Layout 4 Tab List Font Color
    wp.customize( 'betterdocs_mkb_tab_list_font_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-tab-list a' ).css( 'color', to );
        } );
    });

    //MKB Layout 4 Active Tab List Font Color
    wp.customize( 'betterdocs_mkb_tab_list_font_color_active', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-tab-list a.active' ).css( 'color', to );
        } );
    });

    //MKB Layout 4 Active Tab List Background Color
    wp.customize( 'betterdocs_mkb_tab_list_back_color_active', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-tab-list a.active' ).css( 'background-color', to );
        } );
    });

    //MKB Layout 4 Tab List Font Size
    wp.customize( 'betterdocs_mkb_list_font_size', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-tab-list a' ).css( 'font-size', to + 'px' );
        } );
    });

    //MKB Layout 4 Tab List Padding Top | Right | Bottom | Left
    wp.customize( 'betterdocs_mkb_list_column_padding_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-tab-list a' ).css( 'padding-top', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_list_column_padding_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-tab-list a' ).css( 'padding-right', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_list_column_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-tab-list a' ).css( 'padding-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_list_column_padding_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-tab-list a' ).css( 'padding-left', to + 'px' );
        } );
    });

    //MKB Layout 4 Tab List Margin Top | Right | Bottom | Left
    wp.customize( 'betterdocs_mkb_tab_list_margin_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-tab-list a' ).css( 'margin-top', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_tab_list_margin_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-tab-list a' ).css( 'margin-right', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_tab_list_margin_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-tab-list a' ).css( 'margin-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_tab_list_margin_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-tab-list a' ).css( 'margin-left', to + 'px' );
        } );
    });

    //MKB Layout 4 Tab List Border Radius Top Left | Top Right | Bottom Right | Bottom Left
    wp.customize( 'betterdocs_mkb_tab_list_border_topleft', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-tab-list a' ).css( 'border-top-left-radius', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_tab_list_border_topright', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-tab-list a' ).css( 'border-top-right-radius', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_tab_list_border_bottomright', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-tab-list a' ).css( 'border-bottom-right-radius', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_tab_list_border_bottomleft', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-tab-list a' ).css( 'border-bottom-left-radius', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_padding_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-header, .betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-body, .betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-footer' ).css( 'padding-right', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-footer' ).css( 'padding-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_padding_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-header, .betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-body, .betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-footer' ).css( 'padding-left', to + 'px' );
        } );
    });

    //MKB Layout 4 Column Border Radius Top Left | Top Right | Bottom Right | Bottom Left
    wp.customize( 'betterdocs_mkb_column_borderr_topleft', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper > .betterdocs-single-category-wrapper .betterdocs-single-category-inner' ).css( 'border-top-left-radius', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_borderr_topright', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper > .betterdocs-single-category-wrapper .betterdocs-single-category-inner' ).css( 'border-top-right-radius', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_borderr_bottomright', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper > .betterdocs-single-category-wrapper .betterdocs-single-category-inner' ).css( 'border-bottom-right-radius', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_borderr_bottomleft', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper > .betterdocs-single-category-wrapper .betterdocs-single-category-inner' ).css( 'border-bottom-left-radius', to + 'px' );
        } );
    });

    //MKB Layout 4 Title Font Size
    wp.customize( 'betterdocs_mkb_cat_title_font_size', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-category-grid-wrapper .betterdocs-category-title:not(a)' ).css( 'font-size', to + 'px' );
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-category-grid-wrapper .betterdocs-category-title a' ).css( 'font-size', to + 'px' );
        } );
    });

    //MKB Layout 4 Title Color
    wp.customize( 'betterdocs_mkb_cat_title_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-category-grid-wrapper .betterdocs-category-title:not(a)' ).css( 'color', to );
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-category-grid-wrapper .betterdocs-category-title a' ).css( 'color', to );
        } );
    });

    //MKB Layout 4 KB Description Color
    wp.customize( 'betterdocs_mkb_desc_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-category-description' ).css( 'color', to );
        } );
    });

    //MKB Layout 4 Content Space Between Icon | Title | Description | Counter
    wp.customize( 'betterdocs_mkb_column_content_space_image', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-category-icon' ).css( 'margin-right', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_content_space_title', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-title' ).css( 'margin-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_content_space_desc', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-category-description' ).css( 'margin-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_content_space_counter', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-category-items-counts' ).css( 'margin-bottom', to + 'px' );
        } );
    });

    //MKB Layout 4 Docs List Color
    wp.customize( 'betterdocs_mkb_column_list_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-single-category-wrapper .betterdocs-single-category-inner .betterdocs-articles-list li a' ).css( 'color', to );
        } );
    });

    //MKB Layout 4 Docs List Font Size
    wp.customize( 'betterdocs_mkb_column_list_font_size', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-single-category-wrapper .betterdocs-single-category-inner .betterdocs-articles-list li a' ).css( 'font-size', to + 'px' );
        } );
    });

    //MKB Layout 4 Docs List Margin
    wp.customize( 'betterdocs_mkb_column_list_margin_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-single-category-wrapper .betterdocs-single-category-inner .betterdocs-articles-list li:not(.betterdocs-nested-category-wrapper)' ).css( 'margin-top', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_column_list_margin_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-single-category-wrapper .betterdocs-single-category-inner .betterdocs-articles-list li:not(.betterdocs-nested-category-wrapper)' ).css( 'margin-right', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_list_margin_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-single-category-wrapper .betterdocs-single-category-inner .betterdocs-articles-list li:not(.betterdocs-nested-category-wrapper)' ).css( 'margin-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_list_margin_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-single-category-wrapper .betterdocs-single-category-inner .betterdocs-articles-list li:not(.betterdocs-nested-category-wrapper)' ).css( 'margin-left', to + 'px' );
        } );
    });

    //MKB Doc Layout 4 Explore More Button Background Color
    wp.customize( 'betterdocs_mkb_tab_list_explore_btn_bg_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-footer a' ).css( 'background-color', to );
        } );
    });

    //MKB Doc Layout 4 Explore More Button Text Color
    wp.customize( 'betterdocs_mkb_tab_list_explore_btn_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-footer a' ).css( 'color', to );
        } );
    });

    //MKB Doc Layout 4 Explore More Button Border Color
    wp.customize( 'betterdocs_mkb_tab_list_explore_btn_border_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-footer a' ).css( 'border-color', to );
        } );
    });

    //MKB Doc Layout 4 Explore More Button Font Size
    wp.customize( 'betterdocs_mkb_tab_list_explore_btn_font_size', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-footer a' ).css( 'font-size', to + 'px' );
        } );
    });

    //MKB Doc Layout 4 Explore More Button Padding Top | Right | Bottom | Left
    wp.customize( 'betterdocs_mkb_tab_list_explore_btn_padding_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-footer a' ).css( 'padding-top', to + 'px');
        } );
    });
    wp.customize( 'betterdocs_mkb_tab_list_explore_btn_padding_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-footer a' ).css( 'padding-right', to + 'px');
        } );
    });
    wp.customize( 'betterdocs_mkb_tab_list_explore_btn_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-footer a' ).css( 'padding-bottom', to + 'px');
        } );
    });
    wp.customize( 'betterdocs_mkb_tab_list_explore_btn_padding_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-footer a' ).css( 'padding-left', to + 'px' );
        } );
    });

    //MKB Doc Layout 4 Explore More Button Border Radius Top Left | Top Right | Bottom right | Bottom left
    wp.customize( 'betterdocs_mkb_tab_list_explore_btn_borderr_topleft', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-footer a' ).css( 'border-top-left-radius', to + 'px');
        } );
    });

    wp.customize( 'betterdocs_mkb_tab_list_explore_btn_borderr_topright', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-footer a' ).css( 'border-top-right-radius', to + 'px');
        } );
    });

    wp.customize( 'betterdocs_mkb_tab_list_explore_btn_borderr_bottomright', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-footer a' ).css( 'border-bottom-right-radius', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_tab_list_explore_btn_borderr_bottomleft', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-4 .betterdocs-footer a' ).css( 'border-bottom-left-radius', to + 'px');
        } );
    });

    //MKB Layout 3 Popular Docs Background Color
    wp.customize( 'betterdocs_mkb_popular_list_bg_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-content-wrapper .betterdocs-popular-article-list-wrapper' ).css( 'background-color', to );
        } );
    });

    //MKB Layout 3 Popular Docs List Color
    wp.customize( 'betterdocs_mkb_popular_list_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-popular-articles-wrapper .betterdocs-articles-list li a' ).css( 'color', to );
        } );
    });

    //MKB Layout 3 Popular Docs List Font Size
    wp.customize( 'betterdocs_mkb_popular_list_font_size', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-popular-articles-wrapper .betterdocs-articles-list li a' ).css( 'font-size', to + 'px' );
        } );
    });

    //MKB Layout 3 Popular Title Font Size
    wp.customize( 'betterdocs_mkb_popular_title_font_size', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-content-wrapper .betterdocs-popular-articles-wrapper .betterdocs-popular-articles-heading' ).css( 'font-size', to + 'px' );
        } );
    });

    //MKB LAYOUT 3 Popular Title Color
    wp.customize( 'betterdocs_mkb_popular_title_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-content-wrapper .betterdocs-popular-articles-wrapper .betterdocs-popular-articles-heading' ).css( 'color', to );
        } );
    });

    //MKB LAYOUT 3 Popular List Icon Color
    wp.customize( 'betterdocs_mkb_popular_list_icon_color', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-popular-articles-wrapper .betterdocs-articles-list li svg path' ).css( 'fill', to );
        } );
    });

    //MKB LAYOUT 3 Popular List Icon Font Size
    wp.customize( 'betterdocs_mkb_popular_list_icon_font_size', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-popular-articles-wrapper .betterdocs-articles-list li svg' ).css( 'min-width', to + 'px' );
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-popular-articles-wrapper .betterdocs-articles-list li svg' ).css( 'min-height', to + 'px' );
        } );
    });

    //MKB LAYOUT 3 Popular Docs Title Margin Top | Right | Bottom | Left
    wp.customize( 'betterdocs_mkb_popular_title_margin_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-content-wrapper .betterdocs-popular-articles-wrapper .betterdocs-popular-articles-heading' ).css( 'margin-top', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_popular_title_margin_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-content-wrapper .betterdocs-popular-articles-wrapper .betterdocs-popular-articles-heading' ).css( 'margin-right', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_popular_title_margin_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-content-wrapper .betterdocs-popular-articles-wrapper .betterdocs-popular-articles-heading' ).css( 'margin-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_popular_title_margin_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-content-wrapper .betterdocs-popular-articles-wrapper .betterdocs-popular-articles-heading' ).css( 'margin-left', to + 'px' );
        } );
    });

    //MKB LAYOUT 3 Popular Docs List Margin Top | Right | Bottom | Left
    wp.customize( 'betterdocs_mkb_popular_list_margin_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-popular-articles-wrapper .betterdocs-articles-list' ).css( 'margin-top', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_popular_list_margin_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-popular-articles-wrapper .betterdocs-articles-list' ).css( 'margin-right', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_popular_list_margin_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-popular-articles-wrapper .betterdocs-articles-list' ).css( 'margin-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_popular_list_margin_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-popular-articles-wrapper .betterdocs-articles-list' ).css( 'margin-left', to + 'px' );
        } );
    });

    //MKB LAYOUT 3 Popular Docs Padding Top | Right | Bottom | Left
    wp.customize( 'betterdocs_mkb_popular_docs_padding_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-popular-articles-wrapper .betterdocs-articles-list' ).css( 'padding-top', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_popular_docs_padding_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-popular-articles-wrapper .betterdocs-articles-list' ).css( 'padding-right', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_popular_docs_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-popular-articles-wrapper .betterdocs-articles-list' ).css( 'padding-bottom', to + 'px' );
        } );
    });

    wp.customize( 'betterdocs_mkb_popular_docs_padding_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper.betterdocs-mkb-layout-3 .betterdocs-popular-articles-wrapper .betterdocs-articles-list' ).css( 'padding-left', to + 'px' );
        } );
    });

    /**********************MKB-END**************************/

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
    wp.customize( 'betterdocs_live_search_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-wraper.betterdocs-category-list-2 .betterdocs-search-form-wrap' ).css( 'padding-bottom', parseInt(to)+ parseInt(80) + 'px' );
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

    // Doc Layout 2 (Column Background Color)
    wp.customize( 'betterdocs_doc_page_column_bg_color2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-3 .betterdocs-content-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper' ).css( 'background-color', to );
        } );
    });

    //Doc Layout 3 Column Padding Top
    wp.customize( 'betterdocs_doc_page_column_padding_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-3 .betterdocs-content-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper' ).css( 'padding-top', to + 'px');
        } );
    });

    //Doc Layout 3 Column Padding Right
    wp.customize( 'betterdocs_doc_page_column_padding_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-3 .betterdocs-content-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper' ).css( 'padding-right', to + 'px');
        } );
    });

    //Doc Layout 3 Column Padding Bottom
    wp.customize( 'betterdocs_doc_page_column_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-3 .betterdocs-content-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper' ).css( 'padding-bottom', to + 'px');
        } );
    });

    //Doc Layout 3 Column Padding Left
    wp.customize( 'betterdocs_doc_page_column_padding_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-3 .betterdocs-content-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper' ).css( 'padding-left', to + 'px');
        } );
    });

    //Doc Layout 3 Column Border Radius Top Left
    wp.customize( 'betterdocs_doc_page_column_borderr_topleft', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-3 .betterdocs-content-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper' ).css( 'border-top-left-radius', to + 'px');
        } );
    });

    //Doc Layout 3 Column Border Radius Top Right
    wp.customize( 'betterdocs_doc_page_column_borderr_topright', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-3 .betterdocs-content-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper' ).css( 'border-top-right-radius', to + 'px');
        } );
    });

    //Doc Layout 3 Column Border Radius Bottom Right
    wp.customize( 'betterdocs_doc_page_column_borderr_bottomright', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-3 .betterdocs-content-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper' ).css( 'border-bottom-right-radius', to + 'px');
        } );
    });

    //Doc Layout 3 Column Border Radius Bottom Left
    wp.customize( 'betterdocs_doc_page_column_borderr_bottomleft', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-3 .betterdocs-content-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper' ).css( 'border-bottom-left-radius', to + 'px');
        } );
    });

    // //Doc Layout 3 Column Category Title Font Size
    // wp.customize( 'betterdocs_doc_page_cat_title_font_size', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-3 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-title' ).css( 'font-size', to + 'px');
    //     } );
    // });

    // //Doc Layout 3 Category Title Color
    // wp.customize( 'betterdocs_doc_page_cat_title_color2', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-3 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-title' ).css( 'color', to );
    //     } );
    // });

    //Doc Layout 3 Category Title Description Color
    // wp.customize( 'betterdocs_doc_page_cat_desc_color', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-3 .betterdocs-category-description' ).css( 'color', to );
    //     } );
    // });

    // //Doc Layout 3 Item Count Color
    // wp.customize( 'betterdocs_doc_page_item_count_color_layout2', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-3 .betterdocs-category-items-counts' ).css( 'color', to );
    //     } );
    // });

    // //Doc Layout 3 Item Font Size
    // wp.customize( 'betterdocs_doc_page_item_count_font_size', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-3 .betterdocs-category-items-counts' ).css( 'font-size', to + 'px');
    //     } );
    // });

    // //Doc Layout 4 Column Padding Top
    // wp.customize( 'betterdocs_doc_page_column_padding_top', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-content-wrapper .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper .betterdocs-grid-top-row-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper, .betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-content-wrapper .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-single-category-inner .betterdocs-category-header' ).css( 'padding-top',  to + 'px'  );
    //     } );
    // });

    // //Doc Layout 4 Column Padding Right
    // wp.customize( 'betterdocs_doc_page_column_padding_right', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-content-wrapper .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper .betterdocs-grid-top-row-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper, .betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-content-wrapper .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-single-category-inner .betterdocs-category-header, .betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-body' ).css( 'padding-right',  to + 'px'  );
    //     } );
    // });

    // //Doc Layout 4 Column Padding Bottom
    // wp.customize( 'betterdocs_doc_page_column_padding_bottom', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-content-wrapper .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper .betterdocs-grid-top-row-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper, .betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-body' ).css( 'padding-bottom',  to + 'px'  );
    //     } );
    // });

    // //Doc Layout 4 Column Padding Left
    // wp.customize( 'betterdocs_doc_page_column_padding_left', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-content-wrapper .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper .betterdocs-grid-top-row-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper, .betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-content-wrapper .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-single-category-inner .betterdocs-category-header, .betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-body' ).css( 'padding-left',  to + 'px'  );
    //     } );
    // });

    // //Doc Layout 4 Column Border Top Left
    // wp.customize( 'betterdocs_doc_page_column_borderr_topleft', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-content-wrapper .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper .betterdocs-grid-top-row-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper, .betterdocs-wrapper.betterdocs-category-layout-4 .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper > .betterdocs-single-category-wrapper .betterdocs-single-category-inner' ).css( 'border-top-left-radius',  to + 'px'  );
    //     } );
    // });

    // //Doc Layout 4 Column Border Top Right
    // wp.customize( 'betterdocs_doc_page_column_borderr_topright', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-content-wrapper .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper .betterdocs-grid-top-row-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper, .betterdocs-wrapper.betterdocs-category-layout-4 .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper > .betterdocs-single-category-wrapper .betterdocs-single-category-inner' ).css( 'border-top-right-radius',  to + 'px'  );
    //     } );
    // });

    // //Doc Layout 4 Column Border Bottom Right
    // wp.customize( 'betterdocs_doc_page_column_borderr_bottomright', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-content-wrapper .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper .betterdocs-grid-top-row-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper, .betterdocs-wrapper.betterdocs-category-layout-4 .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper > .betterdocs-single-category-wrapper .betterdocs-single-category-inner' ).css( 'border-bottom-right-radius',  to + 'px'  );
    //     } );
    // });

    // //Doc Layout 4 Column Border Bottom Left
    // wp.customize( 'betterdocs_doc_page_column_borderr_bottomleft', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-content-wrapper .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper .betterdocs-grid-top-row-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper, .betterdocs-wrapper.betterdocs-category-layout-4 .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper > .betterdocs-single-category-wrapper .betterdocs-single-category-inner' ).css( 'border-bottom-left-radius',  to + 'px'  );
    //     } );
    // });

    // //Doc Layout 4 Category Title Font Size
    // wp.customize( 'betterdocs_doc_page_cat_title_font_size', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-title, .betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .category-grid .betterdocs-category-title' ).css( 'font-size',  to + 'px'  );
    //     } );
    // });

    // //Doc Layout 4 Category Title Color
    // wp.customize( 'betterdocs_doc_page_cat_title_color2', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-title, .betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .category-grid .betterdocs-category-title' ).css( 'color',  to );
    //     } );
    // });

    // //Doc Layout 4 Column Background Color
    // wp.customize( 'betterdocs_doc_page_column_bg_color', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-content-wrapper .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper .betterdocs-grid-top-row-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper, .betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-content-wrapper .betterdocs-category-grid-wrapper .betterdocs-category-grid-inner-wrapper.layout-flex .betterdocs-single-category-wrapper' ).css( 'background-color', to );
    //     } );
    // });

    // //Doc Layout 4 Category Content Background Color
    // wp.customize( 'betterdocs_doc_page_article_list_bg_color', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-body, .betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-footer' ).css( 'background-color',  to );
    //     } );
    // });

    // //Doc Layout 4 Item Count Color
    // wp.customize( 'betterdocs_doc_page_item_count_color_layout2', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-category-items-counts' ).css( 'color',  to );
    //     } );
    // });

    // //Doc Layout 4 Font Size
    // wp.customize( 'betterdocs_doc_page_item_count_font_size', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-4 .betterdocs-category-items-counts' ).css( 'font-size',  to + 'px' );
    //     } );
    // });

    //Doc Layout 5 Button BG Color
    wp.customize( 'betterdocs_doc_page_column_bg_color2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-content-wrapper .betterdocs-display-flex .betterdocs-article-list-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper' ).css( 'background-color', to );
        } );
    });

    //Doc Layout 5 Column Padding Top
    wp.customize( 'betterdocs_doc_page_column_padding_top', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-content-wrapper .betterdocs-display-flex .betterdocs-article-list-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper' ).css( 'padding-top', to + 'px' );
        } );
    });

    //Doc Layout 5 Column Padding Right
    wp.customize( 'betterdocs_doc_page_column_padding_right', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-content-wrapper .betterdocs-display-flex .betterdocs-article-list-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper' ).css( 'padding-right', to + 'px' );
        } );
    });

    //Doc Layout 5 Column Padding Bottom
    wp.customize( 'betterdocs_doc_page_column_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-content-wrapper .betterdocs-display-flex .betterdocs-article-list-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper' ).css( 'padding-bottom', to + 'px' );
        } );
    });

    //Doc Layout 5 Column Padding Left
    wp.customize( 'betterdocs_doc_page_column_padding_left', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-content-wrapper .betterdocs-display-flex .betterdocs-article-list-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper' ).css( 'padding-left', to + 'px' );
        } );
    });

    //Doc Layout 5 Column Border Radius Top Left
    wp.customize( 'betterdocs_doc_page_column_borderr_topleft', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-content-wrapper .betterdocs-display-flex .betterdocs-article-list-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper' ).css( 'border-top-left-radius', to + 'px' );
        } );
    });

    //Doc Layout 5 Column Border Radius Top Right
    wp.customize( 'betterdocs_doc_page_column_borderr_topright', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-content-wrapper .betterdocs-display-flex .betterdocs-article-list-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper' ).css( 'border-top-right-radius', to + 'px' );
        } );
    });

    //Doc Layout 5 Column Border Radius Bottom Right
    wp.customize( 'betterdocs_doc_page_column_borderr_bottomright', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-content-wrapper .betterdocs-display-flex .betterdocs-article-list-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper' ).css( 'border-bottom-right-radius', to + 'px' );
        } );
    });

    //Doc Layout 5 Column Border Radius Bottom Left
    wp.customize( 'betterdocs_doc_page_column_borderr_bottomleft', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-content-wrapper .betterdocs-display-flex .betterdocs-article-list-wrapper .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper' ).css( 'border-bottom-left-radius', to + 'px' );
        } );
    });

    // //Doc Layout 5 Category Title Font Size
    // wp.customize( 'betterdocs_doc_page_cat_title_font_size', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-title' ).css( 'font-size', to + 'px' );
    //     } );
    // });

    //Doc Layout 5 Category Title Color
    wp.customize( 'betterdocs_doc_page_cat_title_color2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-category-box-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-title' ).css( 'color', to );
        } );
    });

    //Doc Layout 5 Category Description Color
    // wp.customize( 'betterdocs_doc_page_cat_desc_color', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-category-description' ).css( 'color', to );
    //     } );
    // });

    // //Doc Layout 5 Item Count Color
    // wp.customize( 'betterdocs_doc_page_item_count_color_layout2', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-category-items-counts' ).css( 'color', to );
    //     } );
    // });

    // //Doc Layout 5 Font Size
    // wp.customize( 'betterdocs_doc_page_item_count_font_size', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-5 .betterdocs-category-items-counts' ).css( 'font-size', to + 'px' );
    //     } );
    // });


    //Doc Layout 6 Category Title Color
    wp.customize( 'betterdocs_doc_page_cat_title_color2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .layout-6.betterdocs-category-grid-list-inner-wrapper .betterdocs-category-title' ).css( 'color', to );
        } );
    });

    // //Doc Layout 6 Item Count Color
    // wp.customize( 'betterdocs_doc_page_item_count_color_layout6', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-docs-archive-wrapper.betterdocs-category-layout-6 .layout-6.betterdocs-category-grid-list-inner-wrapper .betterdocs-category-items-counts' ).css( 'color', to);
    //     } );
    // });

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
            $('.betterdocs-search-form-wrapper .betterdocs-popular-search-keyword .popular-search-title').css('color', to);
        });
    });

    //Popular Title Font Size
    wp.customize( 'betterdocs_popular_search_title_font_size', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-search-form-wrapper .betterdocs-popular-search-keyword .popular-search-title').css('font-size', to + 'px');
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
            $('.betterdocs-single-layout-6 .betterdocs-sidebar-list-wrapper').css('background-color', to);
        });
    });

    // Sidebar Active Background Color Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_active_bg_color_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6.active .betterdocs-sidebar-list-inner .betterdocs-body').css('background-color', to);
        });
    });

    // Sidebar Active Background Color Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_active_bg_border_color_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6.active .betterdocs-sidebar-list-inner .betterdocs-category-header, .betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6.active .betterdocs-sidebar-list-inner .betterdocs-body').css('border-color', to);
        });
    });


    // Sidebar Padding Top Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_padding_top_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-single-layout-6 .betterdocs-sidebar-list-wrapper').css('padding-top', to + 'px');
        });
    });

    // Sidebar Padding Right Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_padding_right_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-single-layout-6 .betterdocs-sidebar-list-wrapper').css('padding-right', to + 'px');
        });
    });

    // Sidebar Padding Bottom Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_padding_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-single-layout-6 .betterdocs-sidebar-list-wrapper').css('padding-bottom', to + 'px');
        });
    });

    // Sidebar Padding Left Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_padding_left_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-single-layout-6 .betterdocs-sidebar-list-wrapper').css('padding-left', to + 'px');
        });
    });

    // Sidebar Margin Top Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_margin_top_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-single-layout-6 .betterdocs-sidebar-list-wrapper').css('margin-top', to + 'px');
        });
    });

    // Sidebar Margin Right Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_margin_right_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-single-layout-6 .betterdocs-sidebar-list-wrapper').css('margin-right', to + 'px');
        });
    });

    // Sidebar Margin Bottom Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_margin_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-single-layout-6 .betterdocs-sidebar-list-wrapper').css('margin-bottom', to + 'px');
        });
    });

    // Sidebar Margin Left Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_margin_left_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-single-layout-6 .betterdocs-sidebar-list-wrapper').css('margin-left', to + 'px');
        });
    });

    // Sidebar Border Radius Top Left Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_border_radius_top_left_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-single-layout-6 .betterdocs-sidebar-list-wrapper').css('border-top-left-radius', to + 'px');
        });
    });

    // Sidebar Border Radius Top Right Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_border_radius_top_right_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-single-layout-6 .betterdocs-sidebar-list-wrapper').css('border-top-right-radius', to + 'px');
        });
    });

    // Sidebar Border Radius Bottom Right Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_border_radius_bottom_right_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-single-layout-6 .betterdocs-sidebar-list-wrapper').css('border-bottom-right-radius', to + 'px');
        });
    });

    // Sidebar Border Radius Bottom Left Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_border_radius_bottom_left_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-single-layout-6 .betterdocs-sidebar-list-wrapper').css('border-bottom-left-radius', to + 'px');
        });
    });

    // Sidebar Title Background Color Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_bg_color_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header').css('background-color', to);
        });
    });

    //Sidebar Term List Item Color Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_list_item_color_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-single-layout-6 .betterdocs-articles-list li a').css('color', to);
        });
    });

    //Sidebar Term List Item Font Size Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_list_item_font_size_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-single-layout-6 .betterdocs-articles-list li a').css('font-size', to + 'px');
        });
    });

    //Sidebar Term List Item Icon Color Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_list_item_icon_color_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-single-layout-6 .betterdocs-articles-list li svg').css('fill', to);
        });
    });

    //Sidebar Term List Item Icon Size Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_list_item_icon_size_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-single-layout-6 .betterdocs-articles-list li svg').css('height', to + 'px');
            $('.betterdocs-single-layout-6 .betterdocs-articles-list li svg').css('width', to + 'px');
        });
    });

    //Sidebar Term List Item Padding Top(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_list_item_padding_top_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-single-layout-6 .betterdocs-articles-list li').css('padding-top', to + 'px');
        });
    });

    //Sidebar Term List Item Padding Right(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_list_item_padding_right_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-single-layout-6 .betterdocs-articles-list li').css('padding-right', to + 'px');
        });
    });

    //Sidebar Term List Item Padding Bottom(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_list_item_padding_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-single-layout-6 .betterdocs-articles-list li').css('padding-bottom', to + 'px');
        });
    });

    //Sidebar Term List Item Padding Left(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_list_item_padding_left_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-single-layout-6 .betterdocs-articles-list li').css('padding-left', to + 'px');
        });
    });

    //Sidebar Term List Active Item Color(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_list_active_item_color_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-single-layout-6 .betterdocs-articles-list li a.active').css('color', to);
        });
    });

    // Sidebar Term List Item Counter Border Type
    wp.customize( 'betterdocs_sidebar_term_item_counter_border_type_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-items-counts').css('border-style', to);
        });
    });

    // Sidebar Term List Item Counter Border Width
    wp.customize( 'betterdocs_sidebar_term_item_counter_border_width_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-items-counts').css('border-width', to + 'px');
        });
    });

    // Sidebar Active Title Background Color Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_active_title_bg_color_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6.active .betterdocs-sidebar-list-inner .betterdocs-category-header').css('background-color', to);
        });
    });

    // Sidebar Title Color Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_color_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-title a').css('color', to);
        });
    });

    // Sidebar Title Font Size Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_font_size_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-title a').css('font-size', to + 'px');
        });
    });

    // Sidebar Title Font Line Height Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_font_line_height_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-title a').css('line-height', to + 'px');
        });
    });

    // Sidebar Title Font Weight Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_font_weight_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-title a').css('font-weight', to);
        });
    });

    // Sidebar Title Padding Top Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_padding_top_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-title a').css('padding-top', to + 'px');
        });
    });

    // Sidebar Title Padding Right Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_padding_right_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-title a').css('padding-right', to + 'px');
        });
    });

	// Sidebar Title Padding Bottom Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_padding_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-title a').css('padding-bottom', to + 'px');
        });
    });

	// Sidebar Title Padding Left Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_padding_left_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-title a').css('padding-left', to + 'px');
        });
    });

    // Sidebar Title Margin Top Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_margin_top_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-title a').css('margin-top', to + 'px');
        });
    });

    // Sidebar Title Margin Right Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_margin_right_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-title a').css('margin-right', to + 'px');
        });
    });

    // Sidebar Title Margin Bottom Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_margin_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-title a').css('margin-bottom', to + 'px');
        });
    });

    // Sidebar Title Margin Left Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_title_margin_left_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-title a').css('margin-left', to + 'px');
        });
    });

    // Sidebar Term List Border Type Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_list_border_type_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6').css('border-style', to);
        });
    });

    // Sidebar Term List Border Top Width Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_border_top_width_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6').css('border-top-width', to + 'px');
        });
    });

    // Sidebar Term List Border Right Width Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_border_right_width_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6').css('border-right-width', to + 'px');
        });
    });

    // Sidebar Term List Border Bottom Width Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_border_bottom_width_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6').css('border-bottom-width', to + 'px');
        });
    });

    // Sidebar Term List Border Left Width Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_border_left_width_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6').css('border-left-width', to + 'px');
        });
    });

    // Sidebar Term List Border Width Color Layout 6(For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_border_width_color_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6').css('border-color', to);
        });
    });

    // Sidebar Term List Item Counter Font Size Layout 6(For Single Doc Layout 6)

    wp.customize( 'betterdocs_sidebar_term_item_counter_font_size_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-items-counts').css('font-size', to + 'px');
        });
    });

    // Sidebar Term List Item Counter Font Weight Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_font_weight_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-items-counts').css('font-weight', to );
        });
    });

    // Sidebar Term List Item Counter Font Line Height Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_font_line_height_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-items-counts').css('line-height', to + 'px' );
        });
    });

    // Sidebar Term List Item Counter Color Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_color_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-layout-6 .betterdocs-category-items-counts span').css('color', to );
        });
    });

    // Sidebar Term List Item Counter Background Color Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_back_color_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-items-counts').css('background-color', to );
        });
    });

    // Sidebar Term List Item Counter Border Radius Top Left Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_border_radius_top_left_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-items-counts').css('border-top-left-radius', to + 'px');
        });
    });

	// Sidebar Term List Item Counter Border Radius Top Right Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_border_radius_top_right_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-items-counts').css('border-top-right-radius', to + 'px');
        });
    });

    // Sidebar Term List Item Counter Border Radius Bottom Right Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_border_radius_bottom_right_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-items-counts').css('border-bottom-right-radius', to + 'px');
        });
    });

    // Sidebar Term List Item Counter Border Radius Bottom Left Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_border_radius_bottom_left_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-items-counts').css('border-bottom-left-radius', to + 'px');
        });
    });

    // Sidebar Term List Item Counter Padding Top Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_padding_top_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-items-counts').css('padding-top', to + 'px');
        });
    });

    // Sidebar Term List Item Counter Padding Right Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_padding_right_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-items-counts').css('padding-right', to + 'px');
        });
    });

    // Sidebar Term List Item Counter Padding Bottom Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_padding_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-items-counts').css('padding-bottom', to + 'px');
        });
    });

    // Sidebar Term List Item Counter Padding Left Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_padding_left_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-items-counts').css('padding-left', to + 'px');
        });
    });

    // Sidebar Term List Item Counter Margin Top Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_margin_top_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-items-counts').css('margin-top', to + 'px');
        });
    });

    // Sidebar Term List Item Counter Margin Right Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_margin_right_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-items-counts').css('margin-right', to + 'px');
        });
    });

    // Sidebar Term List Item Counter Margin Bottom Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_margin_bottom_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-items-counts').css('margin-bottom', to + 'px');
        });
    });

    // Sidebar Term List Item Counter Margin Left Layout 6 (For Single Doc Layout 6)
    wp.customize( 'betterdocs_sidebar_term_item_counter_margin_left_layout6', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-sidebar .betterdocs-sidebar-content .betterdocs-sidebar-list-wrapper .betterdocs-sidebar-list-inner .betterdocs-sidebar-list.betterdocs-sidebar-layout-6 .betterdocs-sidebar-list-inner .betterdocs-category-header .betterdocs-category-header-inner .betterdocs-category-items-counts').css('margin-left', to + 'px');
        });
    });

    //Content Area Background Color (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_content_background_color', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area').css('background-color', to);
        });
    });

    //Content Area Margin (Archive Page Layout 2) Top | Right | Bottom | Left
    wp.customize( 'betterdocs_archive_content_margin_top', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area').css('margin-top', to + 'px');
        });
    });

    wp.customize( 'betterdocs_archive_content_margin_right', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area').css('margin-right', to + 'px');
        });
    });

    wp.customize( 'betterdocs_archive_content_margin_bottom', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area').css('margin-bottom', to + 'px');
        });
    });

    wp.customize( 'betterdocs_archive_content_margin_left', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area').css('margin-left', to + 'px');
        });
    });

    //Content Area Padding (Archive Page Layout 2) Top | Right | Bottom | Left
    wp.customize( 'betterdocs_archive_content_padding_top', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area').css('padding-top', to + 'px');
        });
    });

    wp.customize( 'betterdocs_archive_content_padding_right', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area').css('padding-right', to + 'px');
        });
    });

    wp.customize( 'betterdocs_archive_content_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area').css('padding-bottom', to + 'px');
        });
    });

    wp.customize( 'betterdocs_archive_content_padding_left', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area').css('padding-left', to + 'px');
        });
    });

    //Archive Content Border Radius (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_content_border_radius', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area').css('border-radius', to + 'px');
        });
    });

    // Archive Page Title Color (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_title_color_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-entry-heading').css('color', to);
        });
    });

    // Archive Page Category Inner Content Background Color(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_inner_content_back_color_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info').css('background-color', to);
        });
    });

    // Archive Page Category Inner Content Image Size(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_inner_content_image_size_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-category-image').css('width', to + '%');
        });
    });

    // Archive Page Category Other Categories Image Size(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_image_size', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-thumb-image').css('width', to + '%');
        });
    });

    // Archive Page Category Inner Content Image Padding Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_inner_content_image_padding_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-category-image img').css('padding-top', to + 'px');
        });
    });

    // Archive Page Category Inner Content Image Padding Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_inner_content_image_padding_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-category-image img').css('padding-right', to + 'px');
        });
    });

    // Archive Page Category Inner Content Image Padding Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_inner_content_image_padding_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-category-image img').css('padding-bottom', to + 'px');
        });
    });

    // Archive Page Category Inner Content Image Padding Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_inner_content_image_padding_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-category-image img').css('padding-left', to + 'px');
        });
    });

    // Archive Page Category Inner Content Image Margin Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_inner_content_image_margin_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-category-image img').css('margin-top', to + 'px');
        });
    });

    // Archive Page Category Inner Content Image Margin Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_inner_content_image_margin_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-category-image img').css('margin-right', to + 'px');
        });
    });

    // Archive Page Category Inner Content Image Margin Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_inner_content_image_margin_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-category-image img').css('margin-bottom', to + 'px');
        });
    });

    // Archive Page Category Inner Content Image Margin Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_inner_content_image_margin_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-category-image img').css('margin-left', to + 'px');
        });
    });

    // Archive Page Title Font Size (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_title_font_size_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-entry-heading').css('font-size', to + 'px');
        });
    });

    // Archive Page Title Margin Top (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_title_margin_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-entry-heading').css('margin-top', to + 'px');
        });
    });

    // Archive Page Title Margin Right (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_title_margin_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-entry-heading').css('margin-right', to + 'px');
        });
    });

    // Archive Page Title Margin Bottom (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_title_margin_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-entry-heading').css('margin-bottom', to + 'px');
        });
    });

     // Archive Page Title Margin Left (Archive Page Layout 2)
     wp.customize( 'betterdocs_archive_title_margin_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-entry-heading').css('margin-left', to + 'px');
        });
    });

	// Archive Page Description Color (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_description_color_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-description').css('color', to);
        });
    });

    // Archive Page Description Font Size (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_description_font_size_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-description').css('font-size', to + 'px');
        });
    });

    // Archive Page Description Margin Top (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_description_margin_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-description').css('margin-top', to + 'px');
        });
    });

    // Archive Page Description Margin Right (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_description_margin_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-description').css('margin-right', to + 'px');
        });
    });

    // Archive Page Description Margin Bottom (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_description_margin_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-description').css('margin-bottom', to + 'px');
        });
    });

    // Archive Page Description Margin Left (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_description_margin_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-description').css('margin-left', to + 'px');
        });
    });

    // Archive Page List Item Color (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_item_color_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > a p').css('color', to);
        });
    });

    // Archive Page List Item Color (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_item_font_size_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > a p').css( 'font-size', to + 'px' );
        });
    });

    // Archive Page Docs List Margin Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_margin_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > a p').css( 'margin-top', to + 'px' );
        });
    });

    // Archive Page Docs List Margin Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_margin_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > a p').css( 'margin-right', to + 'px' );
        });
    });

    // Archive Page Docs List Margin Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_margin_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > a p').css( 'margin-bottom', to + 'px' );
        });
    });

    // Archive Page Docs List Margin Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_margin_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > a p').css( 'margin-left', to + 'px' );
        });
    });

    // Archive Page Docs List Font Weight(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_font_weight_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > a p').css( 'font-weight', to );
        });
    });

    // Archive Page Docs List Line Height(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_item_line_height_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > a p').css( 'line-height', to + 'px' );
        });
    });

    // Archive Page List Item Arrow Height(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_item_arrow_height_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > a .toggle-arrow').css( 'height', to + 'px' );
        });
    });

    // Archive Page List Item Border Style(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_border_style_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li').css( 'border-style', to );
        });
    });

    // Archive Page List Item Border Width Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_border_width_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li').css( 'border-top-width', to + 'px');
        });
    });

    // Archive Page List Item Border Width Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_border_width_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li').css( 'border-right-width', to + 'px');
        });
    });

	// Archive Page List Item Border Width Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_border_width_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li').css( 'border-bottom-width', to + 'px');
        });
    });

	// Archive Page List Item Border Width Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_border_width_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li').css( 'border-left-width', to + 'px');
        });
    });

    // Archive Page List Item Border Color Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_border_width_color_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li').css( 'border-top-color', to );
        });
    });

    // Archive Page List Item Border Color Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_border_width_color_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li').css( 'border-right-color', to );
        });
    });

    // Archive Page List Item Border Color Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_border_width_color_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li').css( 'border-bottom-color', to );
        });
    });

    // Archive Page List Item Border Color Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_border_width_color_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li').css( 'border-left-color', to );
        });
    });


    // Archive Page List Item Arrow Width(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_item_arrow_width_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > a .toggle-arrow').css( 'width', to + 'px' );
        });
    });


    // Archive Page List Item Arrow Color(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_list_item_arrow_color_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > a .toggle-arrow').css( 'fill', to );
        });
    });

    // Archive Page Docs List Arrow Margin Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_arrow_margin_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > a .toggle-arrow').css( 'margin-top', to + 'px' );
        });
    });

    // Archive Page Docs List Arrow Margin Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_arrow_margin_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > a .toggle-arrow').css( 'margin-right', to + 'px' );
        });
    });

    // Archive Page Docs List Arrow Margin Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_arrow_margin_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > a .toggle-arrow').css( 'margin-bottom', to + 'px' );
        });
    });

    // Archive Page Docs List Arrow Margin Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_arrow_margin_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > a .toggle-arrow').css( 'margin-left', to + 'px' );
        });
    });

    // Archive Page Excerpt Font Weight(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_font_weight_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > p').css( 'font-weight', to );
        });
    });

    // Archive Page Excerpt Font Size(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_font_size_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > p').css( 'font-size', to + 'px' );
        });
    });

    // Archive Page Excerpt Font Line Height(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_font_line_height_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > p').css( 'line-height', to + 'px' );
        });
    });

    // Archive Page Excerpt Font Color(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_font_color_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > p').css( 'color', to );
        });
    });

    // Archive Page Excerpt Margin Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_margin_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > p').css( 'margin-top', to + 'px');
        });
    });

    // Archive Page Excerpt Margin Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_margin_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > p').css( 'margin-right', to + 'px' );
        });
    });


    // Archive Page Excerpt Margin Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_margin_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > p').css( 'margin-left', to + 'px');
        });
    });

    // Archive Page Excerpt Margin Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_margin_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > p').css( 'margin-bottom', to + 'px');
        });
    });

    // Archive Page Excerpt Padding Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_padding_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > p').css( 'padding-top', to + 'px');
        });
    });

    // Archive Page Excerpt Padding Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_padding_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > p').css( 'padding-right', to + 'px');
        });
    });

    // Archive Page Excerpt Padding Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_padding_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > p').css( 'padding-bottom', to + 'px');
        });
    });

    // Archive Page Excerpt Padding Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_excerpt_padding_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-taxonomy-doc-category .betterdocs-body ul li > p').css( 'padding-left', to + 'px');
        });
    });

    // Archive Page Excerpt Category Item Counter Font Weight(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_font_weight_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-category-items-counts').css( 'font-weight', to );
        });
    });

    // Archive Page Excerpt Category Item Counter Font Size(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_font_size_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-category-items-counts').css( 'font-size', to + 'px');
        });
    });

    // Archive Page Excerpt Category Item Counter Font Line Height(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_font_line_height_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-category-items-counts').css( 'line-height', to + 'px');
        });
    });

    // Archive Page Excerpt Category Item Counter Font Color(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_font_color_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-category-items-counts').css( 'color', to );
        });
    });

    //Archive Page Category Item Count Border Radius Top Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_border_radius_top_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-category-items-counts').css( 'border-top-left-radius', to + 'px');
        });
    });

    //Archive Page Category Item Count Border Radius Top Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_border_radius_top_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-category-items-counts').css( 'border-top-right-radius', to + 'px');
        });
    });

    //Archive Page Category Item Count Border Radius Bottom Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_border_radius_bottom_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-category-items-counts').css( 'border-bottom-right-radius', to + 'px');
        });
    });

    //Archive Page Category Item Count Border Radius Bottom Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_border_radius_bottom_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-category-items-counts').css( 'border-bottom-left-radius', to + 'px');
        });
    });

    //Archive Page Category Item Count Border Margin Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_margin_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-category-items-counts').css( 'margin-top', to + 'px');
        });
    });

    //Archive Page Category Item Count Border Margin Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_margin_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-category-items-counts').css( 'margin-right', to + 'px');
        });
    });

    //Archive Page Category Item Count Border Margin Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_margin_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-category-items-counts').css( 'margin-bottom', to + 'px');
        });
    });

    //Archive Page Category Item Count Border Margin Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_margin_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-category-items-counts').css( 'margin-left', to + 'px');
        });
    });

    //Archive Page Category Item Count Padding Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_padding_top_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-category-items-counts').css( 'padding-top', to + 'px');
        });
    });

    //Archive Page Category Item Count Padding Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_padding_right_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-category-items-counts').css( 'padding-right', to + 'px');
        });
    });

	//Archive Page Category Item Count Padding Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_padding_bottom_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-category-items-counts').css( 'padding-bottom', to + 'px');
        });
    });

	//Archive Page Category Item Count Padding Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_padding_left_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-category-items-counts').css( 'padding-left', to + 'px');
        });
    });

    // Archive Page Category Item Count Border Color(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_border_color_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-category-items-counts').css( 'border-color', to );
        });
    });

    // Archive Page Category Item Count Background Color(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_article_list_counter_back_color_layout2', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-content-wrapper.doc-category-layout-6 .betterdocs-content-area .betterdocs-category-heading .betterdocs-category-info .betterdocs-entry-title .betterdocs-category-title-counts .betterdocs-category-items-counts').css( 'background-color', to );
        });
    });

    // Archive Page Category Other Categories Title Color(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_color', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-title').css( 'color', to );
        });
    });

    // Archive Page Category Other Categories Title Font Weight(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_font_weight', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-title').css( 'font-weight', to );
        });
    });

    // Archive Page Category Other Categories Title Font Size(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_font_size', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-title').css( 'font-size', to + 'px' );
        });
    });


    // Archive Page Category Other Categories Title Line Height(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_line_height', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-title').css( 'line-height', to + 'px' );
        });
    });

    // Archive Page Category Other Categories Title Padding Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_padding_top', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-title').css( 'padding-top', to + 'px' );
        });
    });

    // Archive Page Category Other Categories Title Padding Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_padding_right', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-title').css( 'padding-right', to + 'px' );
        });
    });

    // Archive Page Category Other Categories Title Padding Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-title').css( 'padding-bottom', to + 'px' );
        });
    });

    // Archive Page Category Other Categories Title Padding Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_padding_left', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-title').css( 'padding-left', to + 'px' );
        });
    });

    // Archive Page Category Other Categories Title Margin Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_margin_top', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-title').css( 'margin-top', to + 'px' );
        });
    });

    // Archive Page Category Other Categories Title Margin Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_margin_right', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-title').css( 'margin-right', to + 'px' );
        });
    });

    // Archive Page Category Other Categories Title Margin Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_margin_bottom', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-title').css( 'margin-bottom', to + 'px' );
        });
    });

    // Archive Page Category Other Categories Title Margin Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_title_margin_left', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-title').css( 'margin-left', to + 'px' );
        });
    });

    // Archive Page Category Other Categories Count(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_color', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-items-counts span').css( 'color', to );
        });
    });

    // Archive Page Category Other Categories Count Background Color(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_back_color', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-items-counts').css( 'background-color', to );
        });
    });

    // Archive Page Category Other Categories Count Line Height(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_line_height', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-items-counts').css( 'line-height', to + 'px' );
        });
    });

    // Archive Page Category Other Categories Count Font Weight (Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_font_weight', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-items-counts').css( 'font-weight', to  );
        });
    });

    // Archive Page Category Other Categories Count Font Size(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_font_size', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-items-counts span').css( 'font-size', to + 'px'  );
        });
    });

    // Archive Page Category Other Categories Count Border Radius Top Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_border_radius_topleft', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-items-counts').css( 'border-top-left-radius', to + 'px'  );
        });
    });

	// Archive Page Category Other Categories Count Border Radius Top Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_border_radius_topright', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-items-counts').css( 'border-top-right-radius', to + 'px'  );
        });
    });

	// Archive Page Category Other Categories Count Border Radius Bottom Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_border_radius_bottomright', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-items-counts').css( 'border-bottom-right-radius', to + 'px'  );
        });
    });

	// Archive Page Category Other Categories Count Border Radius Bottom Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_border_radius_bottomleft', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-items-counts').css( 'border-bottom-left-radius', to + 'px'  );
        });
    });

    // Archive Page Category Other Categories Count Padding Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_padding_top', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-items-counts').css( 'padding-top', to + 'px'  );
        });
    });

    // Archive Page Category Other Categories Count Padding Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_padding_right', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-items-counts').css( 'padding-right', to + 'px'  );
        });
    });

    // Archive Page Category Other Categories Count Padding Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-items-counts').css( 'padding-bottom', to + 'px'  );
        });
    });

    // Archive Page Category Other Categories Count Padding Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_padding_left', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-items-counts').css( 'padding-left', to + 'px'  );
        });
    });

    // Archive Page Category Other Categories Count Margin Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_margin_top', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-items-counts').css( 'margin-top', to + 'px'  );
        });
    });

    // Archive Page Category Other Categories Count Margin Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_margin_right', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-items-counts').css( 'margin-right', to + 'px'  );
        });
    });

    // Archive Page Category Other Categories Count Margin Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_margin_bottom', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-items-counts').css( 'margin-bottom', to + 'px'  );
        });
    });

    // Archive Page Category Other Categories Count Margin Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_count_margin_left', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-single-related-category-inner .betterdocs-category-header .betterdocs-category-items-counts').css( 'margin-left', to + 'px'  );
        });
    });

    // Archive Page Category Other Categories Description Color(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_description_color', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-category-grid-list-inner-wrapper.layout-6 .betterdocs-single-related-category .betterdocs-category-description').css( 'color', to );
        });
    });

    // Archive Page Category Other Categories Description Font Weight(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_description_font_weight', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-category-grid-list-inner-wrapper.layout-6 .betterdocs-single-related-category .betterdocs-category-description').css( 'font-weight', to );
        });
    });

    // Archive Page Category Other Categories Description Font Size(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_description_font_size', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-category-grid-list-inner-wrapper.layout-6 .betterdocs-single-related-category .betterdocs-category-description').css( 'font-size', to + 'px');
        });
    });

    // Archive Page Category Other Categories Description Line Height(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_description_line_height', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-category-grid-list-inner-wrapper.layout-6 .betterdocs-single-related-category .betterdocs-category-description').css( 'line-height', to + 'px');
        });
    });

    // Archive Page Category Other Categories Description Padding Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_description_padding_top', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-category-grid-list-inner-wrapper.layout-6 .betterdocs-single-related-category .betterdocs-category-description').css( 'padding-top', to + 'px');
        });
    });

    // Archive Page Category Other Categories Description Padding Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_description_padding_right', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-category-grid-list-inner-wrapper.layout-6 .betterdocs-single-related-category .betterdocs-category-description').css( 'padding-right', to + 'px');
        });
    });

    // Archive Page Category Other Categories Description Padding Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_description_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-category-grid-list-inner-wrapper.layout-6 .betterdocs-single-related-category .betterdocs-category-description').css( 'padding-bottom', to + 'px');
        });
    });

    // Archive Page Category Other Categories Description Padding Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_description_padding_left', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-category-grid-list-inner-wrapper.layout-6 .betterdocs-single-related-category .betterdocs-category-description').css( 'padding-left', to + 'px');
        });
    });

    // Archive Page Category Other Categories Button Color(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_color', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-load-more-wrapper .betterdocs-load-more-button').css( 'color', to );
        });
    });

    // Archive Page Category Other Categories Button Background Color(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_back_color', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-load-more-wrapper .betterdocs-load-more-button').css( 'background-color', to );
        });
    });

	// Archive Page Category Other Categories Button Font Weight(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_font_weight', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-load-more-wrapper .betterdocs-load-more-button').css( 'font-weight', to );
        });
    });

	// Archive Page Category Other Categories Button Font Size(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_font_size', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-load-more-wrapper .betterdocs-load-more-button').css( 'font-size', to + 'px' );
        });
    });

	// Archive Page Category Other Categories Button Font Line Height(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_font_line_height', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-load-more-wrapper .betterdocs-load-more-button').css( 'line-height', to + 'px' );
        });
    });

	// Archive Page Category Other Categories Button Border Radius Top Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_border_radius_top_left', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-load-more-wrapper .betterdocs-load-more-button').css( 'border-top-left-radius', to + 'px' );
        });
    });

	// Archive Page Category Other Categories Button Border Radius Top Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_border_radius_top_right', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-load-more-wrapper .betterdocs-load-more-button').css( 'border-top-right-radius', to + 'px' );
        });
    });

	// Archive Page Category Other Categories Button Border Radius Bottom Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_border_radius_bottom_right', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-load-more-wrapper .betterdocs-load-more-button').css( 'border-bottom-right-radius', to + 'px' );
        });
    });

	// Archive Page Category Other Categories Button Border Radius Bottom Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_border_radius_bottom_left', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-load-more-wrapper .betterdocs-load-more-button').css( 'border-bottom-left-radius', to + 'px' );
        });
    });

	// Archive Page Category Other Categories Button Padding Top(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_padding_top', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-load-more-wrapper').css( 'padding-top', to + 'px' );
        });
    });

	// Archive Page Category Other Categories Button Padding Right(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_padding_right', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-load-more-wrapper').css( 'padding-right', to + 'px' );
        });
    });

	// Archive Page Category Other Categories Button Padding Bottom(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_padding_bottom', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-load-more-wrapper').css( 'padding-bottom', to + 'px' );
        });
    });

	// Archive Page Category Other Categories Button Padding Left(Archive Page Layout 2)
    wp.customize( 'betterdocs_archive_other_categories_button_padding_left', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-wrapper.betterdocs-taxonomy-wrapper .betterdocs-load-more-wrapper').css( 'padding-left', to + 'px' );
        });
    });

    // // Category Title Font Size (Doc Page Layout 6)
    // wp.customize( 'betterdocs_doc_page_cat_title_font_size_layout6', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-title' ).css( 'font-size', to + 'px' );
    //     } );
    // });

    // // Category Image Width(Doc Page Layout 6)
    // wp.customize( 'betterdocs_doc_list_img_width_layout6', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-term-img' ).css( 'width', to + '%' );
    //     } );
    // });


    // // Category Title Padding Bottom (Doc Page Layout 6)
    // wp.customize( 'betterdocs_doc_page_cat_title_padding_bottom_layout6', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-category-grid-layout-6 .betterdocs-term-title-count' ).css( 'padding-bottom', to + 'px' );
    //     } );
    // });


    // // Item Count Background Color (Doc Page Layout 6)
    // wp.customize( 'betterdocs_doc_page_item_count_back_color_layout6', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'background-color', to );
    //     } );
    // });

    // // Item Count Border Style (Doc Page Layout 6)
    // wp.customize( 'betterdocs_doc_page_item_count_border_type_layout6', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'border-style', to );
    //     } );
    // });

    // // Item Count Border Color (Doc Page Layout 6)
    // wp.customize( 'betterdocs_doc_page_item_count_border_color_layout6', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'border-color', to );
    //     } );
    // });

    // // Item Count Border Width Top (Doc Page Layout 6)
    //  wp.customize( 'betterdocs_doc_page_item_count_border_width_top_layout6', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'border-top-width', to + 'px');
    //     } );
    // });

    // // Item Count Border Width Right (Doc Page Layout 6)
    // wp.customize( 'betterdocs_doc_page_item_count_border_width_right_layout6', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'border-right-width', to + 'px');
    //     } );
    // });

    // // Item Count Border Width Bottom (Doc Page Layout 6)
    // wp.customize( 'betterdocs_doc_page_item_count_border_width_bottom_layout6', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'border-bottom-width', to + 'px');
    //     } );
    // });

    // // Item Count Border Width Left (Doc Page Layout 6)
    // wp.customize( 'betterdocs_doc_page_item_count_border_width_left_layout6', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'border-left-width', to + 'px');
    //     } );
    // });

    // // // Item Count Border Radius Top Left (Doc Page Layout 6)
    // // wp.customize( 'betterdocs_doc_page_item_count_border_radius_top_left_layout6', function( value ) {
    // //     value.bind( function( to ) {
    // //         $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'border-top-left-radius', to + 'px' );
    // //     } );
    // // });

    // // Item Count Border Radius Top Right (Doc Page Layout 6)
    // wp.customize( 'betterdocs_doc_page_item_count_border_radius_top_right_layout6', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'border-top-right-radius', to + 'px');
    //     } );
    // });

    // // Item Count Border Radius Bottom Right(Doc Page Layout 6)
    // wp.customize( 'betterdocs_doc_page_item_count_border_radius_bottom_right_layout6', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'border-bottom-right-radius', to + 'px');
    //     } );
    // });

    // // Item Count Border Radius Bottom Left(Doc Page Layout 6)
    // wp.customize( 'betterdocs_doc_page_item_count_border_radius_bottom_right_layout6', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'border-bottom-left-radius', to + 'px');
    //     } );
    // });

    // // Item Count Margin Top(Doc Page Layout 6)
    // wp.customize( 'betterdocs_doc_page_item_count_margin_top_layout6', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'margin-top', to + 'px');
    //     } );
    // });

    // // Item Count Margin Right(Doc Page Layout 6)
    // wp.customize( 'betterdocs_doc_page_item_count_margin_right_layout6', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'margin-right', to + 'px');
    //     } );
    // });

    // // Item Count Margin Bottom(Doc Page Layout 6)
    // wp.customize( 'betterdocs_doc_page_item_count_margin_bottom_layout6', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'margin-bottom', to + 'px');
    //     } );
    // });

    // // Item Count Margin Left(Doc Page Layout 6)
    // wp.customize( 'betterdocs_doc_page_item_count_margin_left_layout6', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'margin-left', to + 'px');
    //     } );
    // });

    // // Item Count Padding Top(Doc Page Layout 6)
    // wp.customize( 'betterdocs_doc_page_item_count_padding_top_layout6', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'padding-top', to + 'px');
    //     } );
    // });

    // // Item Count Padding Right(Doc Page Layout 6)
    // wp.customize( 'betterdocs_doc_page_item_count_padding_right_layout6', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'padding-right', to + 'px');
    //     } );
    // });

    // // Item Count Padding Bottom(Doc Page Layout 6)
    // wp.customize( 'betterdocs_doc_page_item_count_padding_bottom_layout6', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'padding-bottom', to + 'px');
    //     } );
    // });

    // // Item Count Padding Left(Doc Page Layout 6)
    // wp.customize( 'betterdocs_doc_page_item_count_padding_left_layout6', function( value ) {
    //     value.bind( function( to ) {
    //         $( '.betterdocs-category-grid-layout-6 .betterdocs-term-info .betterdocs-term-count' ).css( 'padding-left', to + 'px');
    //     } );
    // });


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
            $('.betterdocs-searchform select').css('font-size', to + 'px');
        });
    });

    //Category Select Font Weight
    wp.customize( 'betterdocs_category_select_font_weight', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-searchform select').css('font-weight', to);
        });
    });

    //Category Select Text Transform
    wp.customize( 'betterdocs_category_select_text_transform', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-searchform select').css('text-transform', to);
        });
    });

    //Category Select Font Color
    wp.customize( 'betterdocs_category_select_text_color', function( value ) {
        value.bind( function( to ) {
            $('.betterdocs-searchform select').css('color', to);
        });
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

	// FAQ Section Title Margin (Common MKB Layout Controls FAQ)

    wp.customize( 'betterdocs_faq_title_margin_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper .betterdocs-faq-wrapper .betterdocs-faq-section-title').css('margin', formatData( JSON.parse( to ) ) );
        } );
    });

    // FAQ Section Title Color (Common MKB Layout Controls FAQ)
    wp.customize( 'betterdocs_faq_title_color_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper .betterdocs-faq-wrapper .betterdocs-faq-section-title').css( 'color', to );
        } );
    });

    // FAQ Section Font Size (Common MKB Layout Controls FAQ)
    wp.customize( 'betterdocs_faq_title_font_size_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper .betterdocs-faq-wrapper .betterdocs-faq-section-title').css( 'font-size', to + 'px' );
        } );
    });

    // FAQ Category Title Color Layout 1
    wp.customize( 'betterdocs_faq_category_title_color_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper .betterdocs-faq-wrapper.betterdocs-faq-layout-1 .betterdocs-faq-inner-wrapper .betterdocs-faq-title h2').css( 'color', to );
        } );
    });

    // FAQ Category Title Font Size Layout 1
    wp.customize( 'betterdocs_faq_category_name_font_size_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper .betterdocs-faq-wrapper.betterdocs-faq-layout-1 .betterdocs-faq-inner-wrapper .betterdocs-faq-title h2').css( 'font-size', to + 'px' );
        } );
    });

    // FAQ Category Title Padding Layout 1
    wp.customize( 'betterdocs_faq_category_name_padding_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper .betterdocs-faq-wrapper.betterdocs-faq-layout-1 .betterdocs-faq-inner-wrapper .betterdocs-faq-title h2').css('padding', formatData( JSON.parse( to ) ) );
        } );
    });

    // FAQ List Color Layout 1
    wp.customize( 'betterdocs_faq_list_color_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper .betterdocs-faq-wrapper.betterdocs-faq-layout-1 .betterdocs-faq-inner-wrapper .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post .betterdocs-faq-post-name').css( 'color', to );
        } );
    });

    // FAQ List Background Color Layout 1
    wp.customize( 'betterdocs_faq_list_background_color_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper .betterdocs-faq-wrapper.betterdocs-faq-layout-1 .betterdocs-faq-inner-wrapper .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post').css( 'background-color', to );
        } );
    });

    // FAQ List Content Background Color Layout 1
    wp.customize( 'betterdocs_faq_list_content_background_color_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper .betterdocs-faq-wrapper.betterdocs-faq-layout-1 .betterdocs-faq-inner-wrapper .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-main-content').css( 'background-color', to );
        } );
    });

    // FAQ List Content Color Layout 1
    wp.customize( 'betterdocs_faq_list_content_color_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper .betterdocs-faq-wrapper.betterdocs-faq-layout-1 .betterdocs-faq-inner-wrapper .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-main-content').css( 'color', to );
        } );
    });

    // FAQ List Content Font Size Layout 1
    wp.customize( 'betterdocs_faq_list_content_font_size_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper .betterdocs-faq-wrapper.betterdocs-faq-layout-1 .betterdocs-faq-inner-wrapper .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-main-content').css( 'font-size', to + 'px' );
        } );
    });

    // FAQ List Font Size Layout 1
    wp.customize( 'betterdocs_faq_list_font_size_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper .betterdocs-faq-wrapper.betterdocs-faq-layout-1 .betterdocs-faq-inner-wrapper .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post .betterdocs-faq-post-name').css( 'font-size', to + 'px' );
        } );
    });

    // FAQ List Padding Layout 1
    wp.customize( 'betterdocs_faq_list_padding_mkb_layout_1', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper .betterdocs-faq-wrapper.betterdocs-faq-layout-1 .betterdocs-faq-inner-wrapper .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post').css( 'padding', formatData( JSON.parse(to) ) );
        } );
    });

    /** MKB FAQ CONTROLLERS LAYOUT 2 **/

    // FAQ Category Title Color Layout 2

    wp.customize( 'betterdocs_faq_category_title_color_mkb_layout_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper .betterdocs-faq-wrapper.betterdocs-faq-layout-2 .betterdocs-faq-inner-wrapper .betterdocs-faq-title h2').css( 'color', to );
        } );
    });

    // FAQ Category Title Font Size Layout 2

    wp.customize( 'betterdocs_faq_category_name_font_size_mkb_layout_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper .betterdocs-faq-wrapper.betterdocs-faq-layout-2 .betterdocs-faq-inner-wrapper .betterdocs-faq-title h2').css( 'font-size', to + 'px' );
        } );
    });

    // FAQ Category Title Padding Layout 2

    wp.customize( 'betterdocs_faq_category_name_padding_mkb_layout_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper .betterdocs-faq-wrapper.betterdocs-faq-layout-2 .betterdocs-faq-inner-wrapper .betterdocs-faq-title h2').css( 'padding', formatData( JSON.parse( to ) ) );
        } );
    });

    // FAQ List Color Layout 2

    wp.customize( 'betterdocs_faq_list_color_mkb_layout_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper .betterdocs-faq-wrapper.betterdocs-faq-layout-2 .betterdocs-faq-inner-wrapper .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post .betterdocs-faq-post-name').css( 'color', to );
        } );
    });

    // FAQ List Background Color Layout 2

    wp.customize( 'betterdocs_faq_list_background_color_mkb_layout_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper .betterdocs-faq-wrapper.betterdocs-faq-layout-2 .betterdocs-faq-inner-wrapper .betterdocs-faq-list > li .betterdocs-faq-group').css( 'background-color', to );
        } );
    });

    // FAQ List Content Background Color Layout 2

    wp.customize( 'betterdocs_faq_list_content_background_color_mkb_layout_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper .betterdocs-faq-wrapper.betterdocs-faq-layout-2 .betterdocs-faq-inner-wrapper .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-main-content').css( 'background-color', to );
        } );
    });

    // FAQ List Content Color Layout 2

    wp.customize( 'betterdocs_faq_list_content_color_mkb_layout_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper .betterdocs-faq-wrapper.betterdocs-faq-layout-2 .betterdocs-faq-inner-wrapper .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-main-content').css( 'color', to );
        } );
    });

    // FAQ List Content Background Color Layout 2

    wp.customize( 'betterdocs_faq_list_content_font_size_mkb_layout_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper .betterdocs-faq-wrapper.betterdocs-faq-layout-2 .betterdocs-faq-inner-wrapper .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-main-content').css( 'font-size', to + 'px' );
        } );
    });

    // FAQ List Font Size Layout 2

    wp.customize( 'betterdocs_faq_list_font_size_mkb_layout_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper .betterdocs-faq-wrapper.betterdocs-faq-layout-2 .betterdocs-faq-inner-wrapper .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post .betterdocs-faq-post-name').css( 'font-size', to + 'px' );
        } );
    });

    // FAQ List Padding Layout 2

    wp.customize( 'betterdocs_faq_list_padding_mkb_layout_2', function( value ) {
        value.bind( function( to ) {
            $( '.betterdocs-mkb-wrapper .betterdocs-faq-wrapper.betterdocs-faq-layout-2 .betterdocs-faq-inner-wrapper .betterdocs-faq-list > li .betterdocs-faq-group .betterdocs-faq-post').css( 'padding', formatData( JSON.parse( to ) ) );
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
