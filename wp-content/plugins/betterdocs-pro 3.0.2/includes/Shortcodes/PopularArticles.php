<?php

namespace WPDeveloper\BetterDocsPro\Shortcodes;
use WPDeveloper\BetterDocs\Core\Shortcode;

class PopularArticles extends Shortcode {
    protected $is_pro = true;

    /**
     * Summary of get_id
     * @return array|string
     */
    public function get_name() {
        return 'betterdocs_popular_articles';
    }

    public function get_style_depends(){
        return ['betterdocs-popular-articles'];
    }

    /**
     * Summary of default_attributes
     * @return array
     */
    public function default_attributes() {
        return [
            'post_per_page'            => 10,
            'title'                    => __( 'Popular Docs', 'betterdocs-pro' ),
            'title_tag'                => 'h2',
            'multiple_knowledge_base'  => false,
            'disable_customizer_style' => false
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
        $this->views( 'layouts/popular-articles/default' );
    }

    public function view_params() {
        $_view_params = [
            'wrapper_attr' => [
                'class' => ['betterdocs-popular-articles-wrapper']
            ],
            'query_args'   => $this->query->docs_query_args( [
                'post_type'      => 'docs',
                'posts_per_page' => $this->attributes['post_per_page'],
                'meta_key'       => '_betterdocs_meta_views',
                'orderby'        => 'meta_value_num',
                'order'          => 'DESC'
            ], [ 'tax_query' ] )
        ];

        $_view_params['nested_subcategory'] = false;

        if ( ! $this->attributes['disable_customizer_style'] ) {
            if ( $this->attributes['multiple_knowledge_base'] ) {
                $_view_params['wrapper_attr']['class'][] = 'multiple-kb';
            } else {
                $_view_params['wrapper_attr']['class'][] = 'single-kb';
            }
        }

        return $_view_params;
    }
}
