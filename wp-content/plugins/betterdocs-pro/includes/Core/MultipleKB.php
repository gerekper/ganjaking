<?php

namespace WPDeveloper\BetterDocsPro\Core;

use WPDeveloper\BetterDocs\Utils\Base;
use WPDeveloper\BetterDocs\Core\PostType;
use WPDeveloper\BetterDocs\Core\Settings;
use WPDeveloper\BetterDocs\Admin\Builder\Rules;

class MultipleKB extends Base {
    /**
     * Summary of post_type
     * @var PostType
     */
    private $post_type;
    /**
     * Summary of settings
     * @var Settings
     */
    private $settings;

    public $is_enable = false;

    public function __construct( PostType $type, Settings $settings ) {
        $this->post_type = $type;
        $this->settings  = $settings;
        $this->is_enable = $this->settings->get( 'multiple_kb', false );

        add_filter( 'betterdocs_internal_kb_fields', [$this, 'internal_kb_settings'], 11, 1 );
        add_filter( 'betterdocs_docs_type_rewrite_permalink', [$this, 'type_rewrite_permalink'], 11, 3 );

        /**
         * Return if KB is disabled.
         */
        if ( ! $this->is_enable ) {
            return;
        }

        $this->init();
        $this->admin_init();
    }

    public function init() {
        add_action( 'init', [$this, 'register_taxonomy'] );
        add_filter( 'betterdocs_category_rewrite', [$this, 'category_rewrite'], 10, 2 );

        add_filter( 'betterdocs_nested_terms_args', [$this, 'nested_terms_args'], 11 );
        add_filter( 'betterdocs_post_type_link', [$this, 'post_type_link'], 1, 3 );
        add_filter( 'betterdocs_docs_count', [$this, 'counts'], 11, 4 );
        add_filter( 'betterdocs_enable_multiple_knowledge_base', [$this, 'enable'], 9 );
        add_filter( 'betterdocs_docs_tax_query_args', [$this, 'docs_tax_query_args'], 20, 5 );
        add_filter( 'betterdocs_shortcodes_default_atts', [$this, 'shortcodes_default_atts'], 20, 2 );
        add_filter( 'betterdocs_sidebar_template_shortcode_params', [$this, 'archive_template_shortcode_params'], 11, 3 );
        add_filter( 'betterdocs_archive_template_shortcode_params', [$this, 'archive_template_shortcode_params'], 11, 3 );
        add_filter( 'betterdocs_terms_meta_query_args', [$this, 'terms_meta_query'], 10, 4 );
        add_filter( 'betterdocs_breadcrumb_before_archives', [$this, 'breadcrumbs'], 20, 1 );
    }

    public function type_rewrite_permalink( $permalink, $slug, $permalink_structure ) {
        if( ! $this->is_enable && strpos( $permalink, '%knowledge_base%' ) >= 0 )  {
            $permalink = trim( str_replace('%knowledge_base%', '', $permalink), '/' );
            $this->settings->save_settings( [ 'permalink_structure' => $permalink ] );
        }

        return $permalink;
    }

    public function nested_terms_args( $args ) {
        global $wp_query;

        return $args;
    }

    public function post_type_link( $url, $post = null, $leavename = false ) {
        global $wp_query;
        $_kb_slug = isset( $wp_query->query['knowledge_base'] ) ? $wp_query->query['knowledge_base'] : null;

        if( $_kb_slug === null ) {
            $knowledgebase_terms = wp_get_object_terms($post->ID, 'knowledge_base');
            $_kb_slug = is_array( $knowledgebase_terms ) && count( $knowledgebase_terms ) > 0 ? $knowledgebase_terms[0]->slug : 'non-knowledgebase';
        }

        return str_replace( '%knowledge_base%', $_kb_slug, $url );
    }

    public function counts( $counts, $term, $nested_subcategory, $args ) {
        if ( $nested_subcategory == false && $counts == 0 ) {
            return $counts;
        }

        $kb_slug = ! empty( $args['kb_slug'] ) ? trim( $args['kb_slug'] ) : '';
        if ( empty( $kb_slug ) ) {
            if ( is_singular( 'docs' ) ) {
                $kb_terms = $this->single_kb_terms();
                $kb_slug  = ( $kb_terms ) ? $kb_terms[0]->slug : '';
            } else {
                global $wp_query;
                $kb_slug = isset( $wp_query->query['knowledge_base'] ) ? $wp_query->query['knowledge_base'] : '';
            }
        }

        $_kb = get_term_by( 'slug', $kb_slug, 'knowledge_base' );
        if ( isset( $args['multiple_knowledge_base'] ) && $args['multiple_knowledge_base'] && $_kb ) {
            $_child_terms_docs_ids = betterdocs()->query->get_doc_ids_by_term( $term, $_kb, $nested_subcategory );
            if ( is_array( $_child_terms_docs_ids ) ) {
                $counts = count( $_child_terms_docs_ids );
            }
        }

        return $counts;
    }

    public function enable( $enable ) {
        return $this->is_enable;
    }

    public function add_fields() {
        $terms = get_terms( 'knowledge_base', ['hide_empty' => false] );
        betterdocs()->views->get( 'admin/taxonomy/doc_category/add', ['terms' => $terms] );
    }

    public function update_fields( $term ) {
        $terms          = get_terms( 'knowledge_base', ['hide_empty' => false] );
        $knowledge_base = get_term_meta( $term->term_id, 'doc_category_knowledge_base', true );

        betterdocs()->views->get( 'admin/taxonomy/doc_category/edit', [
            'terms'          => $terms,
            'term'           => $term,
            'knowledge_base' => $knowledge_base
        ] );
    }

    public function admin_init() {
        /**
         * Return if its not admin.
         */
        if ( ! is_admin() ) {
            return;
        }

        add_action( 'admin_enqueue_scripts', [$this, 'enqueue'] );

        $this->ajax();

        add_filter( 'betterdocs_highlight_admin_menu', [$this, 'highlight_admin_menu'], 10, 2 );
        add_filter( 'betterdocs_highlight_admin_submenu', [$this, 'highlight_admin_submenu'], 10, 3 );

        add_action( 'betterdocs_doc_category_add_form_before', [$this, 'add_fields'] );
        add_action( 'betterdocs_doc_category_update_form_before', [$this, 'update_fields'], 11, 1 );

        add_filter( 'doc_category_row_actions', [$this, 'disable_category_view'], 10, 2 );

        add_action( 'knowledge_base_add_form_fields', [$this, 'add_kb_icon'] );
        add_action( 'knowledge_base_edit_form_fields', [$this, 'edit_kb_icon'], 10, 2 );

        add_action( 'created_knowledge_base', [$this, 'save_kb_meta'], 10, 2 );
        add_action( 'edited_knowledge_base', [$this, 'update_kb_meta'], 10, 2 );

        add_action( 'admin_head', [$this, 'admin_order_terms'] );
    }

    public function ajax() {
        /**
         * All kind of ajax related to post type: docs
         * for admin side.
         */
        add_action( 'wp_ajax_update_knowledge_base_order', [$this, 'update_knowledge_base_order'] );
    }

    public function enqueue( $hook ) {
        $current_screen = get_current_screen();
        if ( ! isset( $current_screen->id ) || $current_screen->id !== 'edit-knowledge_base' ) {
            return;
        }

        wp_enqueue_media();
        betterdocs()->assets->enqueue( 'betterdocs-category-edit', 'admin/js/category-edit.js' );
        betterdocs_pro()->assets->localize(
            'betterdocs-category-edit',
            'betterdocsCategorySorting',
            [
                'action'      => 'update_knowledge_base_order',
                'selector'    => '.taxonomy-knowledge_base',
                'ajaxurl'     => admin_url( 'admin-ajax.php' ),
                'nonce'       => wp_create_nonce( 'knowledge_base_order_nonce' ),
                'paged'       => isset( $_GET['paged'] ) ? absint( wp_unslash( $_GET['paged'] ) ) : 0,
                'per_page_id' => "edit_{$current_screen->taxonomy}_per_page"
            ]
        );
    }

    public static function disable_category_view( $actions, $tag ) {
        unset( $actions['view'] );
        return $actions;
    }

    public function category_rewrite( $rewrite, $slug ) {
        $_docs_slug = $this->post_type->docs_archive;

        // FIXME: Need to remove this later.
        // if ( $this->settings->get( 'disable_root_slug_archive', false ) ) {
        //     $_docs_slug = '/';
        // }

        return ['slug' => trim( $_docs_slug, '/' ) . '/%knowledge_base%', 'with_front' => false];
    }

    public function breadcrumbs( $breadcrumbs ) {
        if ( is_post_type_archive( 'docs' ) ) {
            return $breadcrumbs;
        }

        $_term_slug = $this->get_kb_slug();
        if ( $_term_slug != 'non-knowledgebase' ) {
            $term = get_term_by( 'slug', $_term_slug, 'knowledge_base' );

            if ( isset( $term->term_id ) ) {
                $term_parents = betterdocs()->query->get_term_parents( $term->term_id, 'knowledge_base' );
                $breadcrumbs  = array_merge( $breadcrumbs, $term_parents );
            }
        }

        return $breadcrumbs;
    }

    public function get_kb_slug( $_kb_slug = '' ) {
        global $wp_query;

        if ( empty( $_kb_slug ) && ! empty( $wp_query->query_vars['knowledge_base'] ) ) {
            $_kb_slug = $wp_query->query_vars['knowledge_base'];
            return $_kb_slug;
        }

        if ( empty( $_kb_slug ) && ! empty( $_COOKIE['last_knowledge_base'] ) ) {
            $_kb_slug = $_COOKIE['last_knowledge_base'];
        }

        return $_kb_slug;
    }

    public function docs_tax_query_args( $tax_query, $_multiple_kb, $_term_slug, $_kb_slug, $_origin_args ) {
        global $wp_query;
        if ( isset( $_origin_args['s'] ) && $this->settings->get( 'kb_based_search', false ) ) {
            $tax_query[] = [
                'taxonomy'         => 'knowledge_base',
                'field'            => 'slug',
                'terms'            => $this->get_kb_slug(),
                'operator'         => 'AND',
                'include_children' => false
            ];

            if ( count( $tax_query ) > 1 ) {
                $tax_query['relation'] = 'AND';
            }

            return $tax_query;
        }

        if ( is_singular( 'docs' ) ) {
            $kb_terms       = $this->single_kb_terms();
            $knowledge_base = ( $kb_terms ) ? $kb_terms[0]->slug : '';
        } elseif ( $_kb_slug ) {
            $knowledge_base = $_kb_slug;
        } else {
            $knowledge_base = isset( $wp_query->query['knowledge_base'] ) ? $wp_query->query['knowledge_base'] : '';
        }

        if ( $_multiple_kb == true && $knowledge_base != 'non-knowledgebase' ) {
            $taxes   = ['knowledge_base', 'doc_category'];
            $tax_map = [];

            foreach ( $taxes as $tax ) {
                $terms = get_terms( [
                    'taxonomy'   => $tax,
                    'hide_empty' => false
                ] );

                foreach ( $terms as $term ) {
                    $tax_map[$tax][$term->slug] = $term->term_taxonomy_id;
                }
            }

            $tax_query = [];

            if ( array_key_exists( 'knowledge_base', $tax_map ) && ! empty( $tax_map['knowledge_base'][$knowledge_base] ) ) {
                $tax_query[] = [
                    'taxonomy'         => 'knowledge_base',
                    'field'            => 'term_taxonomy_id',
                    'terms'            => [$tax_map['knowledge_base'][$knowledge_base]],
                    'include_children'  => false,
                ];
            }

            if ( array_key_exists( 'doc_category', $tax_map ) && ! empty( $tax_map['doc_category'][$_term_slug] ) ) {
                $tax_query[] = [
                    'taxonomy'         => 'doc_category',
                    'field'            => 'term_taxonomy_id',
                    'terms'            => [$tax_map['doc_category'][$_term_slug]],
                    'include_children'  => false,
                ];
            }

            if ( count( $tax_query ) > 1 ) {
                $tax_query['relation'] = 'AND';
            }

            if( empty( $tax_query ) && isset( $_origin_args['tax_query'] ) && ! empty( $_origin_args['tax_query'] ) ) {
                $tax_query = $_origin_args['tax_query'];
            }
        }

        return $tax_query;
    }

    public function single_kb_terms() {
        global $post;

        $kb_terms = [];
        $term     = wp_get_post_terms( $post->ID, 'knowledge_base' );
        if ( ! is_wp_error( $term ) && ! empty( $term ) ) {
            $kb_terms[] = $term[0];
            if ( isset( $_COOKIE['last_knowledge_base'] ) && has_term( $_COOKIE['last_knowledge_base'], 'knowledge_base' ) ) {
                $kb_terms[0] = get_term_by( 'slug', $_COOKIE['last_knowledge_base'], 'knowledge_base' );
            }
        }
        return $kb_terms;
    }

    public function shortcodes_default_atts( $default_atts, $shortdode ) {
        if ( $this->is_enable ) {
            // if ( isset( $default_atts['multiple_knowledge_base'] ) ) {
            //     $default_atts['multiple_knowledge_base'] = $this->is_enable;
            // }

            // if ( isset( $default_atts['kb_slug'] ) ) {
            //     $default_atts['kb_slug'] = $this->get_kb_slug( $default_atts['kb_slug'] );
            // }
        }

        return $default_atts;
    }

    public function archive_template_shortcode_params( $atts, $shortcode_name, $layout ){
        if ( $this->is_enable ) {
            $atts['multiple_knowledge_base'] = $this->is_enable;
            if ( ! isset( $atts['kb_slug'] ) ) {
                $atts['kb_slug'] = $this->get_kb_slug();
            }
        }

        return $atts;
    }

    public function terms_meta_query( $meta_query, $_multiple_kb, $_kb_slug, $_origin_args ) {
        if ( ! $_multiple_kb ) {
            return $meta_query;
        }

        if ( ! empty( $_kb_slug ) ) {
            $meta_query = [
                'relation' => 'OR',
                [
                    'key'     => 'doc_category_knowledge_base',
                    'value'   => $this->get_kb_slug( $_kb_slug ),
                    'compare' => 'LIKE'
                ]
            ];
        }

        return $meta_query;
    }

    public function highlight_admin_menu( $parent_file, $current_screen ) {
        if ( in_array( $current_screen->id, ['edit-knowledge_base'] ) ) {
            $parent_file = 'betterdocs-admin';
        }

        return $parent_file;
    }

    public function highlight_admin_submenu( $submenu_file, $current_screen, $pagenow ) {
        if ( $current_screen->post_type == 'docs' ) {
            if ( $current_screen->id === 'edit-knowledge_base' ) {
                $submenu_file = 'edit-tags.php?taxonomy=knowledge_base&post_type=docs';
            }
        }

        return $submenu_file;
    }

    public function internal_kb_settings( $settings ) {
        $settings['restrict_kb'] = [
            'name'        => 'restrict_kb',
            'type'        => 'select',
            'label'       => __( 'Restriction on Knowledge Bases', 'betterdocs-pro' ),
            'help'        => __( '<strong>Note:</strong> Selected Knowledge Bases will be restricted  ', 'betterdocs-pro' ),
            'priority'    => 4,
            'is_pro'      => true,
            'multiple'    => true,
            'default'     => 'all',
            'placeholder' => __( 'Select any', 'betterdocs' ),
            'filterValue' => 'all',
            'options'     => $this->settings->get_terms( 'knowledge_base' ),
            'rules'       => Rules::logicalRule( [
                Rules::is( 'multiple_kb', true ),
                Rules::is( 'enable_content_restriction', true )
            ] )
        ];
        return $settings;
    }

    /**
     * Register Knowledge Base Taxonomy
     */
    public function register_taxonomy() {
        $disable_root_slug_mkb = $this->settings->get( 'disable_root_slug_mkb' );
        $docs_archive          = $this->post_type->docs_archive;
        $permalink             = get_option( 'permalink_structure' );

        if ( $disable_root_slug_mkb == 1 && $permalink == "/%postname%/" ) {
            $docs_archive = '/';
        }

        /**
         * Register knowledge base taxonomy
         */
        $manage_labels = [
            'name'                => __( 'Knowledge Base', 'betterdocs-pro' ),
            'singular_name'       => __( 'Knowledge Base', 'betterdocs-pro' ),
            'search_items'        => __( 'Search Knowledge Base', 'betterdocs-pro' ),
            'all_items'           => __( 'All Knowledge Base', 'betterdocs-pro' ),
            'parent_item'         => null,
            'parent_item_colon'   => null,
            'edit_item'           => __( 'Edit Knowledge Base', 'betterdocs-pro' ),
            'update_item'         => __( 'Update Knowledge Base', 'betterdocs-pro' ),
            'not_found'           => __( 'No Knowledge Base found.', 'betterdocs-pro' ),
            'add_new_item'        => __( 'Add New Knowledge Base', 'betterdocs-pro' ),
            'new_item_name'       => __( 'New Knowledge Base Name', 'betterdocs-pro' ),
            'add_or_remove_items' => __( 'Add or reomve Knowledge Base', 'betterdocs-pro' ),
            'menu_name'           => __( 'Knowledge Base', 'betterdocs-pro' )
        ];

        $manage_args = [
            'hierarchical'          => true,
            'labels'                => $manage_labels,
            'show_ui'               => true,
            'update_count_callback' => '_update_post_term_count',
            'show_admin_column'     => true,
            'query_var'             => true,
            'show_in_rest'          => true,
            'has_archive'           => true,
            'rewrite'               => ['slug' => $docs_archive, 'with_front' => false],
            'capabilities'          => [
                'manage_terms' => 'manage_knowledge_base_terms',
                'edit_terms'   => 'edit_knowledge_base_terms',
                'delete_terms' => 'delete_knowledge_base_terms',
                'assign_terms' => 'edit_docs'
            ]
        ];

        register_taxonomy( 'knowledge_base', $this->post_type->post_type, $manage_args );
    }

    /**
     * Add a form field in the new category page
     *
     * old: add_knowledge_base_meta
     *
     * @since 1.3.1
     */
    public function add_kb_icon() {
        betterdocs()->views->get( 'admin/taxonomy/kb/add-icon' );
    }

    public function edit_kb_icon( $term, $taxonomy ) {
        $icon_id = get_term_meta( $term->term_id, 'knowledge_base_image-id', true );
        betterdocs()->views->get( 'admin/taxonomy/kb/edit-icon', [
            'icon_id' => $icon_id,
            'term'    => $term
        ] );
    }

    /**
     * Save the form field
     *
     * old: save_knowledge_base_meta
     *
     * @since 2.5.0
     */
    public function save_kb_meta( $term_id ) {
        if ( isset( $_POST['term_meta'] ) ) {
            $cat_keys = array_keys( $_POST['term_meta'] );
            foreach ( $cat_keys as $key ) {
                if ( isset( $_POST['term_meta'][$key] ) ) {
                    add_term_meta( $term_id, "knowledge_base_$key", $_POST['term_meta'][$key] );
                }
            }
        }

        $order = $this->get_max_taxonomy_order( 'knowledge_base' );
        update_term_meta( $term_id, 'kb_order', $order++ );
    }

    /*
     * Update the form field value
     *
     * @since 1.3.1
     */
    public function update_kb_meta( $term_id ) {
        if ( isset( $_POST['term_meta'] ) ) {
            $cat_keys = array_keys( $_POST['term_meta'] );
            foreach ( $cat_keys as $key ) {
                if ( isset( $_POST['term_meta'][$key] ) ) {
                    update_term_meta( $term_id, "knowledge_base_$key", $_POST['term_meta'][$key] );
                }
            }
        }
    }

    /**
     * Order the terms on the admin side.
     */
    public function admin_order_terms() {
        global $current_screen;
        $screen_id = isset( $current_screen->id ) ? $current_screen->id : '';

        if ( in_array( $screen_id, ['toplevel_page_betterdocs-admin', 'betterdocs_page_betterdocs-settings'] ) ) {
            $this->default_term_order( 'knowledge_base' );
        }

        if ( ! isset( $_GET['orderby'] ) && ! empty( $current_screen->base ) && $current_screen->base === 'edit-tags' && $current_screen->taxonomy === 'knowledge_base' ) {
            $this->default_term_order( $current_screen->taxonomy );
            add_filter( 'terms_clauses', [$this, 'set_tax_order'], 10, 3 );
        }
    }

    /**
     *
     * Default the taxonomy's terms' order if it's not set.
     *
     * @param string $tax_slug The taxonomy's slug.
     */
    public function default_term_order( $tax_slug ) {
        $terms = get_terms( $tax_slug, ['hide_empty' => false] );
        $order = $this->get_max_taxonomy_order( $tax_slug );

        foreach ( $terms as $term ) {
            if ( ! get_term_meta( $term->term_id, 'kb_order', true ) ) {
                update_term_meta( $term->term_id, 'kb_order', $order );
                $order++;
            }
        }
    }

    /**
     *
     * Get the maximum kb_order for this taxonomy.
     * This will be applied to terms that don't have a tax position.
     *
     */

    private function get_max_taxonomy_order( $tax_slug ) {
        global $wpdb;
        $max_term_order = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT MAX( CAST( tm.meta_value AS UNSIGNED ) )
				FROM $wpdb->terms t
				JOIN $wpdb->term_taxonomy tt ON t.term_id = tt.term_id AND tt.taxonomy = '%s'
				JOIN $wpdb->termmeta tm ON tm.term_id = t.term_id WHERE tm.meta_key = 'kb_order'",
                $tax_slug
            )
        );
        $max_term_order = is_array( $max_term_order ) ? current( $max_term_order ) : 0;
        return (int) $max_term_order === 0 || empty( $max_term_order ) ? 1 : (int) $max_term_order + 1;
    }

    /**
     * Re-Order the taxonomies based on the kb_order value.
     *
     * @param array $pieces     Array of SQL query clauses.
     * @param array $taxonomies Array of taxonomy names.
     * @param array $args       Array of term query args.
     */
    public function set_tax_order( $pieces, $taxonomies, $args ) {
        foreach ( $taxonomies as $taxonomy ) {
            global $wpdb;
            if ( $taxonomy === 'knowledge_base' ) {
                $join_statement = " LEFT JOIN $wpdb->termmeta AS kb_term_meta ON t.term_id = kb_term_meta.term_id AND kb_term_meta.meta_key = 'kb_order'";

                if ( ! $this->does_substring_exist( $pieces['join'], $join_statement ) ) {
                    $pieces['join'] .= $join_statement;
                }

                $pieces['orderby'] = 'ORDER BY CAST( kb_term_meta.meta_value AS UNSIGNED )';
            }
        }
        return $pieces;
    }

    /**
     * Check if a substring exists inside a string.
     *
     * @param string $string    The main string (haystack) we're searching in.
     * @param string $substring The substring we're searching for.
     *
     * @return bool True if substring exists, else false.
     */
    public function does_substring_exist( $string, $substring ) {
        return strstr( $string, $substring ) !== false;
    }

    public function update_knowledge_base_order() {
        if ( ! check_ajax_referer( 'knowledge_base_order_nonce', 'nonce', false ) ) {
            wp_send_json_error();
        }

        $kb_ordering_data = filter_var_array( wp_unslash( $_POST['data'] ), FILTER_SANITIZE_NUMBER_INT );
        $kb_index         = filter_var( wp_unslash( $_POST['base_index'] ), FILTER_SANITIZE_NUMBER_INT );

        foreach ( $kb_ordering_data as $order_data ) {
            if ( $kb_index > 0 ) {
                $current_position = get_term_meta( $order_data['term_id'], 'kb_order', true );

                if ( (int) $current_position < (int) $kb_index ) {
                    continue;
                }
            }

            update_term_meta( $order_data['term_id'], 'kb_order', ( (int) $order_data['order'] + (int) $kb_index ) );
        }
        wp_send_json_success();
    }
}
