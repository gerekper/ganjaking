<?php

namespace WPDeveloper\BetterDocsPro\Shortcodes;
use WPDeveloper\BetterDocs\Core\Shortcode;

class MultipleKBTabGrid extends Shortcode {
    protected $is_pro = true;
    /**
     * A list of deprecated attributes.
     * @var array<string, string>
     */
    protected $deprecated_attributes = [
        'posts_per_grid' => 'posts_per_page',
        'icon'           => 'show_icon'
    ];

    /**
     * Summary of get_id
     * @return array|string
     */
    public function get_name() {
        return 'betterdocs_multiple_kb_tab_grid';
    }

    /**
     * Summary of default_attributes
     * @return array
     */
    public function default_attributes() {
        return [
            'column'                   => '',
            'terms'                    => '',
            'terms_order'              => $this->settings->get( 'terms_order', 'ASC' ),
            'terms_orderby'            => $this->settings->get( 'terms_orderby', 'betterdocs_order' ),
            'disable_customizer_style' => 'false',
            'title_tag'                => 'h2',
            'orderby'                  => $this->settings->get( 'alphabetically_order_post', 'betterdocs_order' ),
            'order'                    => $this->settings->get( 'docs_order' ),
            'posts_per_page'           => '',
            'show_icon'                => true,
            'nested_subcategory'       => $this->settings->get( 'nested_subcategory', false )
        ];
    }

    public function get_style_depends(){
        return ['betterdocs-category-tab-grid'];
    }

    public function get_script_depends(){
        return ['betterdocs-pro-mkb-tab-grid'];
    }

    public function term_permalink( $permalink, $term, $taxonomy, $params ) {
        $_current_kb = isset( $params['kb_slug'] ) ? $params['kb_slug'] : '';
        return str_replace( betterdocs_pro()->multiple_kb->get_kb_slug(), $_current_kb, $permalink );
    }

    /**
     * Summary of render
     *
     * @param mixed $atts
     * @param mixed $content
     * @return mixed
     */
    public function render( $atts, $content = null ) {
        add_filter( 'betterdocs_term_permalink', [$this, 'term_permalink'], 10, 4 );

        $this->views( 'layouts/tab-grid/default' );

        remove_filter( 'betterdocs_term_permalink', [$this, 'term_permalink'], 10 );
    }

    public function view_params() {
        $posts_number    = $this->settings->get( 'posts_number' );
        $exploremore_btn = $this->settings->get( 'exploremore_btn' );
        $button_text     = $this->settings->get( 'exploremore_btn_txt' );

        $show_count = false;

        $show_button = false;
        if ( $this->attributes['posts_per_page'] == -1 || $posts_number == -1 ) {
            $show_button = false;
        } elseif ( $exploremore_btn ) {
            $show_button = true;
        }

        $kb_terms_query = $this->query->terms_query( [
            'taxonomy'           => 'knowledge_base',
            'hide_empty'         => true,
            'parent'             => 0,
            'terms'              => $this->attributes['terms'],
            'nested_subcategory' => $this->attributes['nested_subcategory'],
            'order'              => $this->attributes['terms_order'],
            'orderby'            => $this->attributes['terms_orderby'],
            'meta_key'           => 'kb_order'
        ] );

        $_view_params = [
            'wrapper_attr' => [
                'class' => ['betterdocs-category-tab-grid-wrapper']
            ],
            'kb_terms'     => get_terms( $kb_terms_query ),

            'show_count'   => $show_count,
            'show_header'  => true,
            'show_list'    => true,
            'show_title'   => true,
            'show_button'  => $show_button,
            'button_text'  => $button_text
        ];

        return $this->merge( parent::view_params(), $_view_params );
    }
}
