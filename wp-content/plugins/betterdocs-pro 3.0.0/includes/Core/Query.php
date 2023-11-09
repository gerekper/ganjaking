<?php

namespace WPDeveloper\BetterDocsPro\Core;

use WP_Term;
use WPDeveloper\BetterDocs\Core\Query as FreeQuery;

class Query extends FreeQuery {

    public function init() {
        parent::init();

        add_action( 'betterdocs_navigation_docs_query', [$this, 'navigation_docs_query'], 11, 1 );
        add_filter( 'betterdocs_adjacent_docs_order', [$this, 'adjacent_docs_order'], 11, 2 );
    }

    public function navigation_docs_query( &$_args ) {
        global $wp_query;

        if ( isset( $wp_query->query_vars['knowledge_base'] ) ) {
            $_args['tax_query'][] = [
                'taxonomy'         => 'knowledge_base',
                'field'            => 'slug',
                'terms'            => $wp_query->query_vars['knowledge_base'],
                'operator'         => 'AND',
                'include_children' => false
            ];
        }

        if ( isset( $_args['tax_query'] ) && count( $_args['tax_query'] ) > 1 ) {
            $_args['tax_query']['relation'] = 'AND';
        }

        $post__in = $this->combine_doc_ids();

        if ( ! empty( $post__in ) ) {
            $_args['post__in'] = $post__in;
        }
    }

    public function combine_doc_ids() {
        global $wp_query;
        $_ids = [];
        if ( isset( $wp_query->query_vars['doc_category'], $wp_query->query_vars['knowledge_base'] ) ) {
            $_term             = get_term_by( 'slug', $wp_query->query_vars['doc_category'], 'doc_category' );
            $_kb_term          = get_term_by( 'slug', $wp_query->query_vars['knowledge_base'], 'knowledge_base' );


            $_term_docs_ids    = $_term instanceof WP_Term ? get_objects_in_term( $_term->term_id, 'doc_category' ) : [];
            $_kb_term_docs_ids = $_kb_term instanceof WP_Term ? get_objects_in_term( $_kb_term->term_id, 'knowledge_base' ) : [];

            $_ids = array_intersect( $_term_docs_ids, $_kb_term_docs_ids );
        }

        return $_ids;
    }

    public function adjacent_docs_order( $docs_order, $terms ) {
        global $wp_query;

        if ( isset( $wp_query->query_vars['doc_category'], $wp_query->query_vars['knowledge_base'] ) ) {
            $docs = $this->combine_doc_ids();

            $docs_order = array_filter( $docs_order, function ( $doc_id ) use ( $docs ) {
                return in_array( $doc_id, $docs );
            } );
        }

        return $docs_order;
    }

    public function parse_term_query( $term_query ) {
        if ( empty( $term_query->query_vars['taxonomy'] ) ) {
            return;
        }

        global $current_screen;

        if ( $current_screen == null ) {
            return;
        }

        parent::parse_term_query( $term_query );

        if ( ! in_array( 'knowledge_base', $term_query->query_vars['taxonomy'], true ) ) {
            return;
        }

        if ( $current_screen->taxonomy !== 'knowledge_base' || $current_screen->id != 'edit-knowledge_base' ) {
            return;
        }

        $term_query->query_vars['meta_query'] = [[
            'key'  => 'kb_order',
            'type' => 'NUMERIC'
        ]];

        $term_query->query_vars['orderby'] = 'meta_value_num';
    }

    public function popular_search_keyword() {
        $keywords              = [];
        $search_table          = get_option( 'betterdocs_db_version' );
        $popular_keyword_limit = $this->settings->get( 'popular_keyword_limit' );

        if ( $search_table == true ) {
            global $wpdb;
            $select = "SELECT search_keyword.keyword, SUM(search_log.count) as count";
            $join   = "FROM {$wpdb->prefix}betterdocs_search_keyword as search_keyword
                    JOIN {$wpdb->prefix}betterdocs_search_log as search_log on search_keyword.id = search_log.keyword_id";
            $get_search_keyword = $wpdb->get_results(
                $wpdb->prepare( "
                        {$select}
                        {$join}
                        GROUP BY search_log.keyword_id
                        ORDER BY count DESC
                        LIMIT %d
                    ", $popular_keyword_limit )
            );

            if ( $get_search_keyword ) {
                foreach ( $get_search_keyword as $key => $value ) {
                    if ( $value->count > $popular_keyword_limit ) {
                        array_push( $keywords, $value->keyword );
                    }
                }
            }
        }

        return $keywords;
    }

    public function get_doc_term_from_kb( $terms, $kb_slug ) {
        if ( is_array( $terms ) && ! empty( $terms ) ) {
            return $terms[0]->slug;
        }

        return '';
    }
}
