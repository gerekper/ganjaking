<?php
/**
 * This class responsible for database work
 * using wordpress functionality 
 * get_option and update_option.
 */
class BetterDocs_DB {
    /**
     * Get all default settings value.
     *
     * @param string $name
     * @return array
     */
    public static function default_settings(){
                
        $option_default = apply_filters('betterdocs_option_default_settings', array(
            'builtin_doc_page' => 1,
            'docs_slug' => 'docs',
            'doc_page' => '',
            'category_slug' => 'docs-category',
            'tag_slug' => 'docs-tag',
            'live_search' => 1,
            'search_placeholder' => 'Search..',
            'search_result_image' => 1,
            'masonry_layout' => 1,
            'alphabetically_order_post' => '',
            'alphabetically_order_term' => '',
            'nested_subcategory' => '',
            'column_number' => 3,
            'posts_number' => 10,
            'post_count' => 1,
            'exploremore_btn' => 1,
            'exploremore_btn_txt' => 'Explore More',
            'doc_single' => 1,
            'enable_toc' => 1,
            'toc_hierarchy' => 1,
            'enable_sticky_toc' => 1,
            'sticky_toc_offset' => 100,
            'collapsible_toc_mobile' => '',
            'enable_post_title' => 1,
            'title_link_ctc' => 1,
            'enable_breadcrumb' => 1,
            'breadcrumb_doc_title' => 'Docs',
            'enable_breadcrumb_category' => 1,
            'enable_breadcrumb_title' => 1,
            'enable_sidebar_cat_list' => 1,
            'enable_print_icon' => 1,
            'enable_tags' => 1,
            'email_feedback' => 1,
            'email_address' => get_option('admin_email'),
            'enable_navigation' => 1,
            'show_last_update_time' => 1,
            'enable_comment' => 1,
            'enable_credit' => 1,
            'customizer_link' => '',
            'category_grid' => '[betterdocs_category_grid]',
            'category_box' => '[betterdocs_category_box]',
            'search_form' => '[betterdocs_search_form]',
            'feedback_form' => '[betterdocs_feedback_form]',
            'supported_heading_tag' => array( 1,2,3,4,5,6 ),
            'display_ia_pages' => array('all'),
            'display_ia_archives' => array('all'),
            'display_ia_texonomy' => array('all'),
            'display_ia_single' => array('all'),
        ));

        return $option_default;
    }
    /**
     * Get all settings value from options table.
     *
     * @param string $name
     * @return array
     */
    public static function get_settings( $name = '' ){
        $settings = get_option( 'betterdocs_settings', true );
        $default = self::default_settings();
        if( ! empty( $name ) && isset( $settings[ $name ] ) ) {
            return $settings[ $name ];
        }
        
        if( ! empty( $name ) && ! isset( $settings[ $name ] ) && isset( $default[ $name ] ) ) {
            return $default[ $name ];
        }
        
        if( ! empty( $name ) && ! isset( $settings[ $name ] )  && ! isset( $default[ $name ] ) ) {
            return '';
        }

        return is_array( $settings ) ? $settings : [];
    }
    /**
     * Update settings 
     * @param array $value
     * @return boolean
     */
    public static function update_settings( $value, $key = '' ){
        if( ! empty( $key ) ) {
            return update_option( $key, $value );
        }
        return update_option( 'betterdocs_settings', $value );
    }
}