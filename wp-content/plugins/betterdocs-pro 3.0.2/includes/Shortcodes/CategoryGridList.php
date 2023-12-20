<?php

namespace WPDeveloper\BetterDocsPro\Shortcodes;
use WPDeveloper\BetterDocs\Core\Shortcode;

class CategoryGridList extends Shortcode {
    protected $is_pro = true;

    /**
     * Summary of get_id
     * @return array|string
     */
    public function get_name() {
        return 'betterdocs_category_grid_list';
    }

    public function get_style_depends() {
        return ['betterdocs-category-grid-list'];
    }

    protected $deprecated_attributes = [
        'multiple_kb'           => 'multiple_knowledge_base',
        'terms_title_tag'       => 'title_tag',
        'posts_per_grid'        => 'posts_per_page',
        'show_term_description' => 'show_description',
        'post_counter'          => 'show_count'
    ];

    protected $map_view_vars = [
        'show_term_description' => 'show_description'
    ];

    /**
     * Summary of default_attributes
     * @return array
     */
    public function default_attributes() {
        return [
            'posts_per_page'          => 5,
            'nested_subcategory'      => false,
            'multiple_knowledge_base' => $this->settings->get( 'multiple_kb', false ),
            'terms'                   => '',
            'terms_order'             => $this->settings->get( 'alphabetically_order_term' ) ? 'ASC' : $this->settings->get( 'terms_order' ),
            'terms_orderby'           => $this->settings->get( 'alphabetically_order_term' ) ? 'name' : $this->settings->get( 'terms_orderby' ),
            'kb_slug'                 => '',
            'orderby'                 => $this->settings->get( 'alphabetically_order_post' ),
            'order'                   => $this->settings->get( 'docs_order' ),
            'explore_more_text'       => $this->settings->get( 'exploremore_btn_txt', __( 'Explore More', 'betterdocs-pro' ) ),
            'show_count_icon'         => true,
            'show_term_image'         => true,
            'show_description'        => true,
            'title_tag'               => 'h2',
            'show_count'              => $this->settings->get( 'post_count' )
        ];
    }

    public function header_sequence( $_layout_sequence, $layout, $widget_type, $_defined_vars ) {
        $_new_layout_sequence = [
            [
                'class'    => 'betterdocs-category-title-counts',
                'sequence' => ['category_title', 'category_counts']
            ],
            'category_description'
        ];

        return $_new_layout_sequence;
    }

    /**
     * Summary of render
     *
     * @param mixed $atts
     * @param mixed $content
     * @return mixed
     */
    public function render( $atts, $content = null ) {
        add_filter( 'betterdocs_header_layout_sequence', [$this, 'header_sequence'], 10, 4 );
        $this->views( 'layouts/base' );
        remove_filter( 'betterdocs_header_layout_sequence', [$this, 'header_sequence'], 10 );
    }

    public function view_params() {
        $terms_query = $this->query->terms_query( [
            'multiple_kb'        => $this->attributes['multiple_knowledge_base'],
            'kb_slug'            => $this->attributes['kb_slug'],
            'terms'              => $this->attributes['terms'],
            'order'              => $this->attributes['terms_order'],
            'orderby'            => $this->attributes['terms_orderby'],
            'nested_subcategory' => $this->attributes['nested_subcategory']
        ] );

        return [
            'wrapper_attr'         => ['class' => ['betterdocs-category-grid-list-wrapper']],
            'inner_wrapper_attr'   => ['class' => 'layout-6 betterdocs-category-grid-list-inner-wrapper'],
            'layout'               => 'default',
            'widget_type'          => 'category-grid-list',

            'terms_query_args'     => $terms_query,

            'image_size'           => 'full',
            'show_header'          => true,
            'show_title'           => true,

            'show_list'            => true,
            'list_icon_position'   => 'right',
            'list_icon_name'       => 'doc-list-arrow',

            'show_button'          => true,
            'show_button_icon'     => true,
            'button_icon_position' => 'right',
            'button_icon'          => 'explore-more',
            'button_text'          => $this->attributes['explore_more_text'],
            'show_term_image'      => $this->attributes['show_term_image']
        ];
    }
}
