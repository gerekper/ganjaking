<?php

namespace WPDeveloper\BetterDocsPro\Shortcodes;
use WPDeveloper\BetterDocs\Core\Query;
use WPDeveloper\BetterDocs\Utils\Helper;
use WPDeveloper\BetterDocs\Core\Settings;
use WPDeveloper\BetterDocs\Core\Shortcode;
use WPDeveloper\BetterDocs\Admin\Customizer\Defaults;

class RelatedCategories extends Shortcode {
    protected $is_pro = true;

    /**
     * A list of deprecated attributes.
     * @var array<string, string>
     */
    protected $deprecated_attributes = [
        'multiple_kb' => 'multiple_knowledge_base'
    ];

    protected $map_view_vars = [
        'terms_title_tag' => 'title_tag'
    ];

    public function __construct( Settings $settings, Query $query, Helper $helper, Defaults $defaults ) {
        parent::__construct( $settings, $query, $helper, $defaults );

        add_action( 'wp_ajax_nopriv_load_more_terms', [$this, 'load_more_terms'] );
        add_action( 'wp_ajax_load_more_terms', [$this, 'load_more_terms'] );
    }

    /**
     * Summary of get_id
     * @return array|string
     */
    public function get_name() {
        return 'betterdocs_related_categories';
    }

    public function get_style_depends(){
        return ['betterdocs-related-categories'];
    }

    /**
     * Summary of default_attributes
     * @return array
     */
    public function default_attributes() {
        return [
            'terms_order'             => $this->settings->get( 'alphabetically_order_term' ) ? 'ASC' : $this->settings->get( 'terms_order' ),
            'terms_orderby'           => $this->settings->get( 'alphabetically_order_term' ) ? 'name' : $this->settings->get( 'terms_orderby' ),
            'multiple_knowledge_base' => $this->settings->get( 'multiple_kb' ),
            'nested_subcategory'      => $this->settings->get( 'nested_subcategory' ),
            'heading'                 => __( 'Other Categories', 'betterdocs-pro' ),
            'load_more_text'          => __( 'Load More', 'betterdocs-pro' ),
            'terms_title_tag'         => 'h2'
        ];
    }

    public function load_more_button( $terms ) {
        if( empty( $terms ) || count( $terms ) < 4 ) {
            return;
        }
        $this->views( 'layouts/related-categories/load-more' );
    }

    public function heading( $terms ) {
        if( empty( $terms ) ) {
            return;
        }

        $this->views( 'layouts/related-categories/heading' );
    }

    public function get_script_depends() {
        return ['betterdocs-related-categories'];
    }

    public function load_more_terms() {
        if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'show-more-catergories' ) ) {
            die( 'Cheating&huh?' );
        }

        $current_term_id = isset( $_GET['current_term_id'] ) ? $_GET['current_term_id'] : '';
        $kb_slug         = isset( $_GET['kb_slug'] ) ? $_GET['kb_slug'] : '';

        $page               = isset( $_GET['page'] ) ? $_GET['page'] : 2;
        $title_tag          = isset( $_GET['title_tag'] ) ? $_GET['title_tag'] : 'h2';
        $multiple_kb        = $this->settings->get( 'multiple_kb' );
        $terms_order        = $this->settings->get( 'alphabetically_order_term' ) != 'off' ? 'ASC' : $this->settings->get( 'terms_order' );
        $terms_orderby      = $this->settings->get( 'alphabetically_order_term' ) != 'off' ? 'name' : $this->settings->get( 'terms_orderby' );
        $nested_subcategory = $this->settings->get( 'nested_subcategory' );
        $per_page           = 4;
        $offset             = $per_page * ( $page - 1 );

        $_term_query_args = $this->query->terms_query( [
            'taxonomy'           => 'doc_category',
            'hide_empty'         => true,
            'multiple_kb'        => $multiple_kb,
            'kb_slug'            => $kb_slug,
            'order'              => $terms_order,
            'orderby'            => $terms_orderby,
            'nested_subcategory' => $nested_subcategory,
            'number'             => $per_page,
            'offset'             => $offset,
            'exclude'            => [$current_term_id]
        ] );
        $terms = get_terms( $_term_query_args );

        $_term_query_args['offset'] = $per_page * (  ( $page + 1 ) - 1 );
        $_has_term                  = count( get_terms( $_term_query_args ) );

        $output = '';

        if ( ! empty( $terms ) && is_array( $terms ) ) {
            foreach ( $terms as $term ) {
                ob_start();

                $_counts = betterdocs()->query->get_docs_count( $term, $nested_subcategory, [
                    'multiple_knowledge_base' => isset( $multiple_kb ) ? $multiple_kb : false,
                    'kb_slug'                 => isset( $kb_slug ) ? $kb_slug : ''
                ] );

                if ( $_counts <= 0 ) {
                    continue;
                }

                $permalink = apply_filters(
                    'betterdocs_term_permalink',
                    get_term_link( $term->term_id, $term->taxonomy ), $term, 'doc_category'
                );

                betterdocs()->views->get( 'layouts/related-categories/default', [
                    'term'          => $term,
                    'title_tag'     => $title_tag,
                    'show_term_image' => true,
                    'permalink'     => $permalink,
                    'widget_type'   => 'related-categories',
                    'counts' => $_counts
                ] );

                $output .= ob_get_clean();
            }
        }

        wp_send_json_success( [
            'html'          => $output,
            'has_more_term' => $_has_term > 0
        ] );
    }

    /**
     * Summary of render
     *
     * @param mixed $atts
     * @param mixed $content
     * @return mixed
     */
    public function render( $atts, $content = null ) {
        betterdocs_pro()->assets->localize( 'betterdocs-related-categories', 'betterdocsRelatedTerms', [
            'ajax_url'        => admin_url( 'admin-ajax.php' ),
            'nonce'           => wp_create_nonce( 'show-more-catergories' ),
            'title_tag'       => $this->attributes['terms_title_tag'],
            'current_term_id' => get_queried_object_id(),
            'kb_slug'         => betterdocs_pro()->multiple_kb->get_kb_slug()
        ] );

        add_action( 'betterdocs_base_layout_inner_wrapper_before', [$this, 'heading'] );
        add_action( 'betterdocs_base_layout_inner_wrapper_after', [$this, 'load_more_button'] );

        $this->views( 'layouts/base' );

        remove_action( 'betterdocs_base_layout_inner_wrapper_before', [$this, 'heading'] );
        remove_action( 'betterdocs_base_layout_inner_wrapper_after', [$this, 'load_more_button'] );
    }

    public function view_params() {
        $terms_query = $this->query->terms_query( [
            'multiple_kb'        => $this->attributes['multiple_knowledge_base'],
            'order'              => $this->attributes['terms_order'],
            'orderby'            => $this->attributes['terms_orderby'],
            'nested_subcategory' => $this->attributes['nested_subcategory'],
            'number'             => 4,
            'kb_slug'            => betterdocs_pro()->multiple_kb->get_kb_slug(),
            'exclude'            => [get_queried_object_id()]
        ] );

        $_view_params = [
            'wrapper_attr'       => [
                'class' => ['betterdocs-related-terms-wrapper']
            ],
            'inner_wrapper_attr' => [
                'class' => ['betterdocs-related-terms-inner-wrapper']
            ],
            'layout'             => 'default',
            'terms_query_args'   => $terms_query,
            'widget_type'        => 'related-categories'
        ];

        return $_view_params;
    }
}
