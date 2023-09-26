<?php

namespace WPDeveloper\BetterDocsPro\Shortcodes;
use WPDeveloper\BetterDocs\Core\Shortcode;

class SidebarList extends Shortcode {
    protected $is_pro = true;

    /**
     * A list of deprecated attributes.
     * @var array<string, string>
     */
    protected $deprecated_attributes = [
        'terms_title_tag' => 'title_tag'
    ];

    protected $map_view_vars = [
        'terms_title_tag' => 'title_tag'
    ];

    /**
     * Summary of get_id
     * @return array|string
     */
    public function get_name() {
        return 'betterdocs_sidebar_list';
    }

    /**
     * Summary of default_attributes
     * @return array
     */
    public function default_attributes() {
        return [
            'nested_subcategory'      => $this->settings->get( 'nested_subcategory' ),
            'multiple_knowledge_base' => $this->settings->get( 'multiple_kb' ),
            'terms_order'             => $this->settings->get( 'alphabetically_order_term' ) ? 'ASC' : $this->settings->get( 'terms_order' ),
            'terms_orderby'           => $this->settings->get( 'alphabetically_order_term' ) ? 'name' : $this->settings->get( 'terms_orderby' ),
            'orderby'                 => $this->settings->get( 'alphabetically_order_post' ),
            'posts_per_page'          => $this->settings->get( 'posts_number', 0 ),
            'order'                   => $this->settings->get( 'docs_order' ),
            'title_tag'               => 'h2',
            'terms'                   => '',
            'kb_slug'                 => ''
        ];
    }

    /**
     * Summary of render
     *
     * @param mixed $atts
     * @param mixed $content
     * @return mixed
     */
    public function render( $atts, $content = null ) {
        $this->views( 'layouts/base' );
    }

    public function view_params() {
        $terms_query = $this->query->terms_query( [
            'multiple_kb'        => $this->attributes['multiple_knowledge_base'],
            'kb_slug'            => isset( $this->attributes['kb_slug'] ) ? $this->attributes['kb_slug'] : '',
            'terms'              => $this->attributes['terms'],
            'order'              => $this->attributes['terms_order'],
            'orderby'            => $this->attributes['terms_orderby'],
            'nested_subcategory' => $this->attributes['nested_subcategory']
        ] );

        return [
            'wrapper_attr'       => [
                'class' => ['betterdocs-sidebar-list-wrapper']
            ],
            'inner_wrapper_attr' => [
                'class' => ['betterdocs-sidebar-list-inner']
            ],

            'layout'             => 'default',

            'terms_query_args'   => $terms_query,
            'widget_type'        => 'sidebar-list',

            'show_header'        => true,
            'show_title'         => true,
            'show_count'         => true,
            'show_list'          => true
        ];
    }
}
