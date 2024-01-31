<?php

namespace WPDeveloper\BetterDocsPro\Core;

use WP_Term;
use WPDeveloper\BetterDocs\Utils\Base;

class ContentRestrictions extends Base {
    private $settings;
    private $current_user;

    private $is_user_logged_in;

    public function __construct( Settings $settings ) {
        $this->settings          = $settings;
        $this->current_user      = wp_get_current_user();
        $this->is_user_logged_in = is_user_logged_in();

        if ( ! $this->settings->get( 'enable_content_restriction', false ) ) {
            return;
        }

        if ( $this->settings->get( 'enable_disable', false ) ) {
            add_filter( 'betterdocs_ia_query_string_array', [$this, 'ia_query_string_array'], 10, 4 );
        }

        add_filter( 'betterdocs_terms_query_args', [$this, 'exclude_terms'], 11, 1 );
        add_filter( 'betterdocs_tag_tax_query', [$this, 'tag_template_tax_query'], 11, 1 );
        add_filter( 'betterdocs_docs_tax_query_args', [$this, 'live_search_tax_query'], 20, 5 );
        add_action( 'template_redirect', [$this, 'template_redirect'], 99 );

        //Filter Search Results Based IKB
        add_filter( 'rest_docs_query', [$this, 'filter_ia_search_results'], 10, 2 );

        //Filter Doc Category Terms Based On IKB
        add_filter( 'rest_doc_category_query', [$this, 'filter_ia_doc_categories'], 10, 2 );
    }

    public function filter_ia_search_results( $query_args, $request ) {
        if ( ! $this->is_visible_by_role_ia( $this->current_user ) ) {
            $restricted_categories = $this->get_restricted_categories();
            $_restricted_kb_terms  = $this->get_restricted_categories( 'knowledge_base', true );
            $search_keyword        = isset( $query_args['s'] ) ? $query_args['s'] : '';
            if ( strlen( $search_keyword ) > 0 && count( $restricted_categories ) > 0 ) {
                $query_args['tax_query'][] = [
                    'taxonomy'         => 'doc_category',
                    'field'            => 'term_id',
                    'operator'         => 'NOT IN',
                    'terms'            => $restricted_categories,
                    'include_children' => true
                ];
            }
            if ( $this->settings->get( 'multiple_kb', false ) && count( $_restricted_kb_terms ) > 0 && strlen( $search_keyword ) > 0 ) {
                $query_args['tax_query'][] = [
                    'taxonomy'         => 'knowledge_base',
                    'field'            => 'term_id',
                    'terms'            => $_restricted_kb_terms,
                    'operator'         => 'NOT IN',
                    'include_children' => true
                ];
            }
            return $query_args;
        }
        return $query_args;
    }

    public function filter_ia_doc_categories( $query_args, $request ) {
        if ( ! $this->is_visible_by_role_ia( $this->current_user ) ) {
            $restricted_categories = $this->get_restricted_categories();
            $_restricted_kb_terms  = $this->get_restricted_categories( 'knowledge_base', true );
            if ( $this->settings->get( 'multiple_kb', false ) && count( $_restricted_kb_terms ) > 0 ) {
                $merged_ids = [];
                foreach ( $_restricted_kb_terms as $term_id ) {
                    $query = [
                        'taxonomy'   => 'doc_category',
                        'fields'     => 'ids',
                        'meta_query' => [
                            'relation' => 'OR',
                            [
                                'key'     => 'doc_category_knowledge_base',
                                'value'   => get_term_field( 'slug', $term_id, 'knowledge_base' ),
                                'compare' => 'LIKE'
                            ]
                        ]
                    ];
                    $term_ids   = get_terms( $query );
                    $merged_ids = array_merge( $term_ids, $merged_ids );
                }
                $query_args['exclude'] = $merged_ids;
            } else if ( count( $restricted_categories ) > 0 ) {
                $query_args['exclude'] = $restricted_categories;
            }
        }
        return $query_args;
    }

    public function template_redirect() {
        if ( ! $this->is_visible_by_role() && ( is_post_type_archive( 'docs' ) || is_singular( 'docs' ) || is_tax( 'knowledge_base' ) || is_tax( 'doc_category' ) ) ) {
            global $wp_query;
            $_is_restricted          = false;
            $_current_queried_object = get_queried_object();
            $_restricted_docs_page   = $this->settings->get( 'restrict_template', ['all'] );
            $_taxonomy               = $_current_queried_object instanceof WP_Term ? $_current_queried_object->taxonomy : null;
            $_settings_key           = is_tax( 'knowledge_base' ) ? 'restrict_kb' : 'restrict_category';
            $_restricted_terms       = (array) $this->settings->get( $_settings_key, ['all'] );

            $_docs_terms = $_docs_kbs = [];

            if ( is_singular( 'docs' ) ) {
                $_docs_terms = get_the_terms( get_the_ID(), 'doc_category' );
                $_docs_kbs   = get_the_terms( get_the_ID(), 'knowledge_base' );

                $_docs_terms = ! is_array( $_docs_terms ) ? [] : $_docs_terms;
                $_docs_kbs   = ! is_array( $_docs_kbs ) ? [] : $_docs_kbs;

                $_docs_terms = array_map( function ( $term ) {return $term->slug;}, $_docs_terms );
                $_docs_kbs = array_map( function ( $term ) {return $term->slug;}, $_docs_kbs );
            }

            switch ( true ) {
                case in_array( 'all', $_restricted_docs_page ):
                case in_array( 'docs', $_restricted_docs_page ):
                    $_is_restricted = true;
                    break;
                case $_taxonomy != null && is_tax( $_taxonomy ) && in_array( $_taxonomy, $_restricted_docs_page ):
                    if ( in_array( 'all', $_restricted_terms ) || in_array( $wp_query->query[$_taxonomy], $_restricted_terms ) ) {
                        $_is_restricted = true;
                    }
                    break;
                case is_singular( 'docs' ) && ( in_array( 'doc_category', $_restricted_docs_page ) || in_array( 'knowledge_base', $_restricted_docs_page ) ):
                    $_is_doc_terms = in_array( 'doc_category', $_restricted_docs_page );
                    if ( $_is_doc_terms ) {
                        $_restricted_terms = (array) $this->settings->get( 'restrict_category', ['all'] );
                        if ( in_array( 'all', $_restricted_terms ) || array_intersect( $_docs_terms, $_restricted_terms ) ) {
                            $_is_restricted = true;
                        }
                    }

                    $_is_kb_terms = in_array( 'knowledge_base', $_restricted_docs_page );
                    if ( $_is_kb_terms ) {
                        $_restricted_terms = (array) $this->settings->get( 'restrict_kb', ['all'] );
                        if ( in_array( 'all', $_restricted_terms ) || array_intersect( $_docs_kbs, $_restricted_terms ) ) {
                            $_is_restricted = true;
                        }
                    }
                    break;
                // case is_singular( 'docs' ) && in_array( 'doc_category', $_restricted_docs_page ):
                //     $_restricted_terms = (array) $this->settings->get( 'restrict_category', ['all'] );
                //     if ( in_array( 'all', $_restricted_terms ) || array_intersect( $_docs_terms, $_restricted_terms ) ) {
                //         $_is_restricted = true;
                //     }
                //     break;
                // case is_singular( 'docs' ) && in_array( 'knowledge_base', $_restricted_docs_page ):
                //     $_restricted_terms = (array) $this->settings->get( 'restrict_kb', ['all'] );
                //     if ( in_array( 'all', $_restricted_terms ) || array_intersect( $_docs_kbs, $_restricted_terms ) ) {
                //         $_is_restricted = true;
                //     }
                //     break;
                default:
                    $_is_restricted = false;
                    break;
            }

            if ( $_is_restricted ) {
                $this->redirect_restricted_users();
            }
        }
    }

    public function redirect_restricted_users() {
        $restricted_redirect_url = $this->settings->get( 'restricted_redirect_url', '' );
        if ( $restricted_redirect_url ) {
            wp_safe_redirect( $restricted_redirect_url );
            exit;
        } else {
            global $wp_query;
            $wp_query->set_404();

            /**
             * This is commented because we are setting 404 in template_redirect action.
             * where we don't need to decide template part.
             */
            // status_header( 404 );
            // get_template_part( 404 );
            // exit();
        }
    }

    public function exclude_terms( $query_args ) {
        $_taxonomy = $query_args['taxonomy'];
        $_taxonomy = empty( $_taxonomy ) ? 'doc_category' : $_taxonomy;
        $_is_kb    = $_taxonomy === 'knowledge_base' ? true : false;

        $_restricted_terms = $this->get_restricted_categories( $_taxonomy, $_is_kb );
        if ( ! $this->is_visible_by_role() && ! empty( $_restricted_terms ) ) {
            $query_args['exclude'] = $_restricted_terms;
        }

        return $query_args;
    }

    /**
     * This
     *
     * @param mixed $tax_query
     * @return mixed
     */
    public function tag_template_tax_query( $tax_query ) {
        if ( ! $this->is_visible_by_role() ) {
            $_restricted_terms = $this->get_restricted_categories();
            $_terms_query      = [];
            if ( ! empty( $_restricted_terms ) ) {
                $_terms_query = [
                    'taxonomy'         => 'doc_category',
                    'field'            => 'term_id',
                    'operator'         => 'NOT IN',
                    'terms'            => $_restricted_terms,
                    'include_children' => true
                ];
            }

            $_restricted_kb_terms = $this->get_restricted_categories( 'knowledge_base', true );
            if ( $this->settings->get( 'multiple_kb', false ) && ! empty( $_restricted_kb_terms ) ) {
                $_terms_query = [
                    'taxonomy'         => 'knowledge_base',
                    'field'            => 'term_id',
                    'terms'            => $_restricted_kb_terms,
                    'operator'         => 'NOT IN',
                    'include_children' => true
                ];
            }

            if ( ! empty( $_terms_query ) ) {
                $tax_query[] = $_terms_query;
            }

            if ( count( $tax_query ) > 1 ) {
                $tax_query['relation'] = 'AND';
            }
        }
        return $tax_query;
    }

    /**
     * This method is responsible for Live Search Restriction Query Modification.
     *
     * @since 2.5.0
     *
     * @param array $tax_query
     * @param bool $_multiple_kb
     * @param string $_term_slug
     * @param string $_kb_slug
     * @param array $_origin_args
     *
     * @return array
     */
    public function live_search_tax_query( $tax_query, $_multiple_kb, $_term_slug, $_kb_slug, $_origin_args ) {
        if ( ! $this->is_visible_by_role() && isset( $_origin_args['s'] ) ) {
            $tax_query = $this->tag_template_tax_query( $tax_query );
        }

        return $tax_query;
    }

    public function is_visible_by_role() {
        global $current_user;

        if ( ! is_user_logged_in() ) {
            return false;
        }

        $content_visibility = $this->settings->get( 'content_visibility', ['all'] );
        if ( in_array( 'all', $content_visibility, true ) ) {
            return true;
        }

        // If The User Has Multiple Roles Assigned
        $roles         = $current_user->roles;
        $_user_can_see = count( array_intersect( $roles, $content_visibility ) ) >= 1;

        return $_user_can_see;
    }

    public function is_visible_by_role_ia( $user ) {
        $current_user = $user;

        if ( ! $this->is_user_logged_in ) {
            return false;
        }

        $content_visibility = $this->settings->get( 'content_visibility', ['all'] );
        if ( in_array( 'all', $content_visibility, true ) ) {
            return true;
        }

        // If The User Has Multiple Roles Assigned
        $roles         = $current_user->roles;
        $_user_can_see = count( array_intersect( $roles, $content_visibility ) ) >= 1;

        return $_user_can_see;
    }

    public function get_restricted_categories( $taxonomy = 'doc_category', $is_kb = false ) {
        $_restricted_docs_page       = (array) $this->settings->get( 'restrict_template', ['all'] ); // $restrict_template
        $_restricted_docs_categories = (array) $this->settings->get( 'restrict_category', ['all'] ); // $restrict_category

        $_term_ids  = [];
        $_restriced = ( in_array( 'all', $_restricted_docs_page ) || in_array( $taxonomy, $_restricted_docs_page ) );

        if ( $is_kb ) {
            $_restricted_docs_categories = (array) $this->settings->get( 'restrict_kb', ['all'] ); // $restrict_kb
            $_restriced                  = $_restriced && $this->settings->get( 'multiple_kb', false );
        }

        if ( $_restriced && in_array( 'all', $_restricted_docs_categories ) ) {
            $_term_ids = get_terms( [
                'taxonomy' => $taxonomy,
                'fields'   => 'ids'
            ] );
        } elseif ( $_restriced && ! in_array( 'all', $_restricted_docs_categories ) ) {
            foreach ( $_restricted_docs_categories as $category ) {
                $term = get_term_by( 'slug', $category, $taxonomy );
                if ( $term != false ) {
                    $_term_ids[] = $term->term_id;
                }
            }
        }

        return $_term_ids;
    }

    /**
     * This method is responsible for Instant Answer Query String generation.
     *
     * @param mixed $query_strings_array
     * @param mixed $content_type
     * @param mixed $is_search
     * @param mixed $content_list
     * @return array
     */
    public function ia_query_string_array( $query_strings_array, $content_type, $is_search, $content_list ) {
        if ( ! $this->is_visible_by_role() ) {
            $_restricted_categories = $this->get_restricted_categories();
            switch ( $content_type ) {
                case 'docs':
                    if ( ! empty( $_restricted_categories ) ) {
                        $query_strings_array['doc_category_exclude'] = implode( ',', $_restricted_categories );
                    }
                    if ( ! empty( $_restricted_kbs = $this->get_restricted_categories( 'knowledge_base', true ) ) ) {
                        $query_strings_array['knowledge_base_exclude'] = implode( ',', $_restricted_kbs );
                    }
                    break;
                case 'docs_categories':
                    if ( empty( $content_list ) && ! empty( $_restricted_categories ) ) {
                        $_term_ids = get_terms( [
                            'taxonomy' => 'doc_category',
                            'fields'   => 'ids'
                        ] );

                        $query_strings_array[$is_search ? 'doc_category' : 'include'] = implode( ',', array_diff( $_term_ids, $_restricted_categories ) );
                    }
                    break;
            }
        }

        return $query_strings_array;
    }
}
